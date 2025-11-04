<template>
  <div class="wysiwyg-container">
    <!-- Barre d'outils -->
    <div class="toolbar">
      <button 
        type="button" 
        @click="toggleFormat('bold')"
        :class="{ active: isFormatActive('bold') }"
        class="toolbar-btn"
        title="Gras"
      >
        <strong>B</strong>
      </button>
      <button 
        type="button" 
        @click="toggleFormat('italic')"
        :class="{ active: isFormatActive('italic') }"
        class="toolbar-btn"
        title="Italique"
      >
        <em>I</em>
      </button>
      <button 
        type="button" 
        @click="toggleFormat('underline')"
        :class="{ active: isFormatActive('underline') }"
        class="toolbar-btn"
        title="Souligné"
      >
        <u>U</u>
      </button>
      <div class="separator"></div>
      <button 
        type="button" 
        @click="insertList('unordered')"
        class="toolbar-btn"
        title="Liste à puces"
      >
        • Liste
      </button>
      <button 
        type="button" 
        @click="insertList('ordered')"
        class="toolbar-btn"
        title="Liste numérotée"
      >
        1. Liste
      </button>
    </div>
    
    <!-- Zone d'édition -->
    <div 
      ref="editor"
      contenteditable="true"
      @input="updateContent"
      @keydown="handleKeyDown"
      @mouseup="updateFormatState"
      @focus="onFocus"
      @blur="onBlur"
      class="editor"
      :style="{ minHeight: minHeight }"
      :data-placeholder="placeholder"
    ></div>
  </div>
</template>

<script setup>
import { ref, watch, nextTick, onMounted, onBeforeUnmount } from 'vue'

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: 'Tapez votre texte...'
  },
  minHeight: {
    type: String,
    default: '200px'
  }
})

const emit = defineEmits(['update:modelValue', 'focus', 'blur'])

const editor = ref(null)
const isFocused = ref(false)
const formatState = ref(0) // Variable pour forcer la réactivité

// S'assurer que la ref est correctement initialisée
onMounted(() => {
  if (editor.value) {
    editor.value.innerHTML = props.modelValue
    
    // Ajouter un gestionnaire pour les changements de sélection
    document.addEventListener('selectionchange', updateFormatState)
  }
})

// Watcher pour synchroniser le contenu externe
watch(() => props.modelValue, (newValue) => {
  if (editor.value && editor.value.innerHTML !== newValue) {
    editor.value.innerHTML = newValue
  }
})

const updateContent = () => {
  if (editor.value) {
    const content = editor.value.innerHTML
    emit('update:modelValue', content)
  }
}

const toggleFormat = (format) => {
  if (!editor.value) return
  
  // S'assurer que l'éditeur a le focus
  editor.value.focus()
  
  // Appliquer le formatage
  try {
    document.execCommand(format, false, null)
  } catch (e) {
    console.warn('Commande non supportée:', format)
  }
  
  // Mettre à jour le contenu et l'état
  updateContent()
  updateFormatState()
}

const isFormatActive = (format) => {
  if (!editor.value) return false
  try {
    // Utiliser formatState pour forcer la réactivité
    formatState.value
    return document.queryCommandState(format)
  } catch (e) {
    return false
  }
}

const updateFormatState = () => {
  // Force la réactivité en incrémentant formatState
  formatState.value++
}

const insertList = (type) => {
  if (!editor.value) return
  
  // S'assurer que l'éditeur a le focus
  editor.value.focus()
  
  // Appliquer la liste
  try {
    if (type === 'unordered') {
      document.execCommand('insertUnorderedList', false, null)
    } else {
      document.execCommand('insertOrderedList', false, null)
    }
  } catch (e) {
    console.warn('Commande de liste non supportée:', type)
  }
  
  updateContent()
}

    const handleKeyDown = (event) => {
      // Gérer le backspace pour empêcher la suppression des médias
      if (event.key === 'Backspace') {
        const selection = window.getSelection()
        if (selection.rangeCount > 0) {
          const range = selection.getRangeAt(0)
          const container = range.startContainer
          
          // Vérifier si on est dans une légende de média
          const mediaCaption = container.nodeType === Node.TEXT_NODE ? 
            container.parentElement?.closest('.media-caption') : 
            container.closest?.('.media-caption')
          if (mediaCaption) {
            // Si on est au début de la légende et qu'elle est vide, empêcher la suppression
            if (range.startOffset === 0 && mediaCaption.textContent.trim() === '') {
              event.preventDefault()
              event.stopPropagation()
              return false
            }
          }
          
          // Vérifier si on est juste avant un conteneur de média
          const mediaContainer = container.nodeType === Node.TEXT_NODE ? 
            container.parentElement?.closest('.media-container') : 
            container.closest?.('.media-container')
          if (mediaContainer) {
            // Si on est au début du conteneur et qu'il est vide, empêcher la suppression
            if (range.startOffset === 0 && mediaContainer.textContent.trim() === '') {
              event.preventDefault()
              event.stopPropagation()
              return false
            }
          }
          
          // Vérifier si on est juste avant un média
          const insertedMedia = container.parentElement?.querySelector('.inserted-media')
          if (insertedMedia && range.startOffset === 0) {
            // Vérifier si le conteneur parent contient seulement le média
            const parent = insertedMedia.parentElement
            if (parent && parent.textContent.trim() === insertedMedia.textContent.trim()) {
              event.preventDefault()
              event.stopPropagation()
              return false
            }
          }
        }
      }
  
  // Gérer Ctrl+B, Ctrl+I, Ctrl+U
  if (event.ctrlKey) {
    switch (event.key.toLowerCase()) {
      case 'b':
        event.preventDefault()
        toggleFormat('bold')
        break
      case 'i':
        event.preventDefault()
        toggleFormat('italic')
        break
      case 'u':
        event.preventDefault()
        toggleFormat('underline')
        break
    }
  }
}

const onFocus = () => {
  isFocused.value = true
  emit('focus')
}

const onBlur = () => {
  isFocused.value = false
  emit('blur')
}

// Nettoyer l'événement lors de la destruction du composant
onBeforeUnmount(() => {
  document.removeEventListener('selectionchange', updateFormatState)
})
</script>

<style scoped>
.wysiwyg-container {
  border: 2px solid #e5e7eb;
  border-radius: 0.75rem;
  overflow: hidden;
  background: white;
  box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
  transition: all 0.2s ease;
}

.wysiwyg-container:focus-within {
  border-color: #8b5cf6;
  box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
}

.toolbar {
  display: flex;
  gap: 0.5rem;
  padding: 1rem;
  background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
  border-bottom: 1px solid #e2e8f0;
  flex-wrap: wrap;
  align-items: center;
}

.toolbar-btn {
  padding: 0.5rem 0.75rem;
  font-size: 0.875rem;
  font-weight: 500;
  background: white;
  border: 1px solid #d1d5db;
  border-radius: 0.5rem;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  min-width: 2.5rem;
  color: #374151;
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}

.toolbar-btn:hover {
  background: #f8fafc;
  border-color: #8b5cf6;
  color: #7c3aed;
  transform: translateY(-1px);
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.toolbar-btn.active {
  background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
  border-color: #7c3aed;
  color: white;
  box-shadow: 0 4px 6px -1px rgba(139, 92, 246, 0.3), 0 2px 4px -1px rgba(139, 92, 246, 0.2);
}

.toolbar-btn.active:hover {
  background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
  transform: translateY(-1px);
}

.separator {
  width: 1px;
  height: 2rem;
  background: linear-gradient(to bottom, transparent, #d1d5db, transparent);
  margin: 0 0.5rem;
}

.editor {
  padding: 1.25rem;
  min-height: 200px;
  outline: none;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
  line-height: 1.6;
  direction: ltr;
  text-align: left;
  transition: all 0.2s ease;
  background: white;
  color: #1f2937;
  font-size: 0.95rem;
}

.editor:focus {
  outline: none;
}

.editor:empty:before {
  content: attr(data-placeholder);
  color: #9ca3af;
  pointer-events: none;
  direction: ltr;
  text-align: left;
  font-style: italic;
}

/* Styles pour le contenu formaté */
.editor strong {
  font-weight: bold;
}

.editor em {
  font-style: italic;
}

.editor u {
  text-decoration: underline;
}

.editor ul {
  list-style-type: disc;
  margin-left: 1.25rem;
  margin-top: 0.5rem;
  margin-bottom: 0.5rem;
}

.editor ol {
  list-style-type: decimal;
  margin-left: 1.25rem;
  margin-top: 0.5rem;
  margin-bottom: 0.5rem;
}

.editor li {
  margin: 0.25rem 0;
}

/* Forcer la direction LTR */
.editor * {
  direction: ltr !important;
  text-align: left !important;
}
</style>