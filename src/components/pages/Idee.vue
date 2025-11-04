<template>
  <div class="app">
    <!-- Header -->


    <div class="main-container">
      <div v-if="showMobileMenu" class="mobile-overlay" @click="closeMobileMenu"></div>

      <!-- Main Content -->
      <main class="main-content">
        <div class="content-wrapper">
          <!-- Ideas Section -->
          <div class="ideas-section">
            <!-- Header with Add Button -->
            <div class="section-header">
              <h2 class="section-title">Boîte à Idées</h2>
              <button class="add-idea-btn" @click="showAddIdeaModal = true">
                <svg class="add-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <line x1="12" y1="5" x2="12" y2="19"></line>
                  <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Proposer une idée
              </button>
            </div>

            <!-- Search Section -->
            <div class="filters-container">
              <div class="search-section">
                <div class="search-container">
                  <input
                    type="text"
                    placeholder="Rechercher par nom ou idée..."
                    class="search-input"
                    v-model="searchQuery"
                    @input="searchIdeas"
                  >
                  <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.35-4.35"></path>
                  </svg>
                </div>
              </div>
            </div>

            <!-- Ideas Grid -->
            <div class="ideas-grid-container" v-if="filteredIdeas.length > 0">
              <div
                v-for="idea in filteredIdeas"
                :key="idea.id"
                class="idea-card"
                @click="openIdeaModal(idea)"
              >
                <div class="idea-header">
                  <div class="idea-author">
                    <svg class="author-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                      <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    {{ idea.name }}
                  </div>
                  <div class="idea-date">
                    {{ formatDate(idea.created_at) }}
                  </div>
                </div>
                <div class="idea-content">
                  <h3 class="idea-title">{{ idea.idea }}</h3>
                  <p class="idea-preview">{{ truncateText(idea.details, 120) }}</p>
                </div>
                <div class="idea-actions">
                  <button class="view-btn" @click.stop="openIdeaModal(idea)">
                    <svg class="view-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                      <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    Voir détails
                  </button>
                </div>
              </div>
            </div>

            <!-- Empty State -->
            <div v-else class="empty-state">
              <svg class="empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M9 12l2 2 4-4"></path>
                <path d="M21 12c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1z"></path>
                <path d="M3 12c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1z"></path>
                <path d="M12 21c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1z"></path>
                <path d="M12 3c.552 0 1-.448 1-1s-.448-1-1-1-1 .448-1 1 .448 1 1 1z"></path>
              </svg>
              <h3 class="empty-title">Aucune idée trouvée</h3>
              <p class="empty-description">Soyez le premier à proposer une idée innovante</p>
            </div>
          </div>
        </div>
      </main>
    </div>

    <!-- Add Idea Modal -->
    <div v-if="showAddIdeaModal" class="modal-overlay" @click="closeAddIdeaModal">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3 class="modal-title">Proposer une nouvelle idée</h3>
          <button class="modal-close" @click="closeAddIdeaModal">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <line x1="18" y1="6" x2="6" y2="18"></line>
              <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
          </button>
        </div>

        <form @submit.prevent="submitAddIdea" class="modal-form">
          <div class="form-group">
            <label class="form-label">Votre nom *</label>
            <input
              type="text"
              v-model="addIdeaForm.name"
              class="form-input"
              placeholder="Ex: Ahmed Hassan"
              required
            >
          </div>

          <div class="form-group">
            <label class="form-label">Votre idée *</label>
            <input
              type="text"
              v-model="addIdeaForm.idea"
              class="form-input"
              placeholder="Ex: Organiser des ateliers de formation"
              required
            >
          </div>

          <div class="form-group">
            <label class="form-label">Détails de l'idée *</label>
            <textarea
              v-model="addIdeaForm.details"
              class="form-textarea"
              placeholder="Décrivez votre idée en détail, son objectif, comment la mettre en œuvre..."
              rows="6"
              required
            ></textarea>
          </div>

          <div class="modal-actions">
            <button type="button" class="cancel-btn" @click="closeAddIdeaModal">
              Annuler
            </button>
            <button type="submit" class="submit-btn" :disabled="isAddingIdea">
              <span v-if="isAddingIdea">Envoi en cours...</span>
              <span v-else>Proposer l'idée</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Idea Detail Modal -->
    <div v-if="showModal && selectedIdea" class="modal-overlay" @click="closeModal">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3 class="modal-title">{{ selectedIdea.idea }}</h3>
          <button class="modal-close" @click="closeModal">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <line x1="18" y1="6" x2="6" y2="18"></line>
              <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
          </button>
        </div>

        <div class="idea-details-modal">
          <div class="detail-row">
            <strong>Proposé par:</strong> {{ selectedIdea.name }}
          </div>
          <div class="detail-row">
            <strong>Date:</strong> {{ formatDate(selectedIdea.created_at) }}
          </div>
          <div class="detail-row detail-full">
            <strong>Détails:</strong>
            <div class="idea-full-details">{{ selectedIdea.details }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

// API Configuration
const API_BASE_URL = 'https://sogetrag.com/api/idees.php'

// Reactive state
const activeSection = ref('idees')
const showMobileMenu = ref(false)
const showModal = ref(false)
const showAddIdeaModal = ref(false)
const selectedIdea = ref(null)
const isAddingIdea = ref(false)
const searchQuery = ref('')

const ideas = ref([])

const addIdeaForm = ref({
  name: '',
  idea: '',
  details: ''
})

// Computed properties
const filteredIdeas = computed(() => {
  if (!searchQuery.value) {
    return ideas.value
  }

  const query = searchQuery.value.toLowerCase()
  return ideas.value.filter(idea =>
    idea.name.toLowerCase().includes(query) ||
    idea.idea.toLowerCase().includes(query) ||
    idea.details.toLowerCase().includes(query)
  )
})

// Methods
const toggleMobileMenu = () => {
  showMobileMenu.value = !showMobileMenu.value
}

const closeMobileMenu = () => {
  showMobileMenu.value = false
}

const openIdeaModal = (idea) => {
  selectedIdea.value = idea
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  selectedIdea.value = null
}

const closeAddIdeaModal = () => {
  showAddIdeaModal.value = false
  resetAddIdeaForm()
}

const resetAddIdeaForm = () => {
  addIdeaForm.value = {
    name: '',
    idea: '',
    details: ''
  }
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const truncateText = (text, maxLength) => {
  if (text.length <= maxLength) return text
  return text.substring(0, maxLength) + '...'
}

const searchIdeas = async () => {
  if (!searchQuery.value.trim()) {
    await loadIdeas()
    return
  }

  try {
    const response = await fetch(`${API_BASE_URL}?action=search&query=${encodeURIComponent(searchQuery.value)}`)
    const data = await response.json()
    
    if (data.success) {
      ideas.value = data.ideas || []
    }
  } catch (error) {
    console.error('Erreur lors de la recherche:', error)
  }
}

const loadIdeas = async () => {
  try {
    const response = await fetch(`${API_BASE_URL}?action=list`)
    const data = await response.json()
    
    if (data.success) {
      ideas.value = data.ideas || []
    }
  } catch (error) {
    console.error('Erreur lors du chargement des idées:', error)
    // Fallback data for development
    ideas.value = [
      {
        id: 1,
        name: 'Ahmed Hassan',
        idea: 'Ateliers de formation numérique',
        details: 'Organiser des ateliers mensuels pour former les membres de la communauté aux outils numériques modernes. Cela inclurait des formations sur les réseaux sociaux, la création de contenu, et les outils de productivité. L\'objectif est de réduire la fracture numérique et d\'autonomiser notre communauté.',
        created_at: '2024-01-15'
      },
      {
        id: 2,
        name: 'Fatima Al-Zahra',
        idea: 'Jardin communautaire',
        details: 'Créer un espace vert partagé où les familles peuvent cultiver leurs propres légumes et herbes. Ce projet favoriserait les liens sociaux, l\'apprentissage du jardinage écologique, et fournirait des produits frais à la communauté. Nous pourrions également organiser des ateliers sur l\'agriculture urbaine.',
        created_at: '2024-01-20'
      },
      {
        id: 3,
        name: 'Omar Benali',
        idea: 'Programme de mentorat jeunesse',
        details: 'Mettre en place un système où les adultes expérimentés accompagnent les jeunes dans leur développement personnel et professionnel. Le programme inclurait des séances de coaching, des ateliers sur les compétences de vie, et des opportunités de networking. L\'objectif est de guider la nouvelle génération vers la réussite.',
        created_at: '2024-01-25'
      }
    ]
  }
}

const submitAddIdea = async () => {
  if (isAddingIdea.value) return

  isAddingIdea.value = true

  try {
    const formData = new FormData()
    formData.append('action', 'add')
    formData.append('name', addIdeaForm.value.name)
    formData.append('idea', addIdeaForm.value.idea)
    formData.append('details', addIdeaForm.value.details)

    const response = await fetch(API_BASE_URL, {
      method: 'POST',
      body: formData
    })

    const data = await response.json()

    if (data.success) {
      alert('Idée proposée avec succès!')
      closeAddIdeaModal()
      await loadIdeas()
    } else {
      alert('Erreur lors de l\'envoi: ' + (data.message || 'Erreur inconnue'))
    }
  } catch (error) {
    console.error('Erreur lors de l\'envoi:', error)
    alert('Erreur lors de l\'envoi de l\'idée')
  } finally {
    isAddingIdea.value = false
  }
}

// Lifecycle
onMounted(() => {
  loadIdeas()
})
</script>

<style scoped>
/* Base Styles */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

.app {
  min-height: 100vh;
  background: #f8fafc;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* Header */
.header {
  background: #ffffff;
  border-bottom: 1px solid #e5e7eb;
  padding: 0 20px;
  height: 60px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  position: sticky;
  top: 0;
  z-index: 100;
}

.header-left {
  display: flex;
  align-items: center;
  gap: 20px;
  flex: 1;
}

.mobile-menu-btn {
  display: none;
  background: none;
  border: none;
  cursor: pointer;
  padding: 8px;
  border-radius: 6px;
  color: #374151;
}

.mobile-menu-btn:hover {
  background: #f3f4f6;
}

.mobile-menu-btn svg {
  width: 20px;
  height: 20px;
}

.logo {
  display: flex;
  align-items: center;
  gap: 12px;
}

.logo-text {
  font-size: 20px;
  font-weight: 700;
  color: #1f2937;
}

.search-container {
  position: relative;
  max-width: 400px;
  flex: 1;
}

.search-input {
  width: 100%;
  padding: 12px 12px 12px 40px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 14px;
  color: #1f2937;
  background: white;
}

.search-input::placeholder {
  color: #9ca3af;
}

.search-icon {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  width: 16px;
  height: 16px;
  color: #9ca3af;
}

.header-right {
  display: flex;
  align-items: center;
  gap: 16px;
}

.profile-section {
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  padding: 8px 12px;
  border-radius: 8px;
  transition: background 0.2s;
}

.profile-section:hover {
  background: #f3f4f6;
}

.profile-avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  object-fit: cover;
}

.profile-name {
  font-size: 14px;
  font-weight: 500;
  color: #1f2937;
}

.dropdown-icon {
  width: 16px;
  height: 16px;
  color: #6b7280;
}

/* Main Container */
.main-container {
  display: flex;
  min-height: calc(100vh - 60px);
  position: relative;
}

/* Sidebar */
.sidebar {
  width: 280px;
  background: #ffffff;
  border-right: 1px solid #e5e7eb;
  padding: 20px 0;
  overflow-y: auto;
  position: sticky;
  top: 60px;
  height: calc(100vh - 60px);
}

.mobile-close-btn {
  display: none;
  position: absolute;
  top: 16px;
  right: 16px;
  background: none;
  border: none;
  cursor: pointer;
  padding: 8px;
  border-radius: 6px;
  color: #374151;
}

.mobile-close-btn:hover {
  background: #f3f4f6;
}

.mobile-close-btn svg {
  width: 20px;
  height: 20px;
}

.sidebar-nav {
  padding: 0 20px;
}

.nav-section {
  margin-bottom: 32px;
}

.profile-card {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 16px;
  background: #f8fafc;
  border-radius: 12px;
  margin-bottom: 24px;
}

.profile-card-avatar {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  object-fit: cover;
}

.profile-info {
  flex: 1;
}

.profile-card-name {
  font-size: 16px;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 4px;
}

.profile-card-status {
  font-size: 14px;
  color: #10b981;
}

.nav-title {
  font-size: 12px;
  font-weight: 600;
  color: #6b7280;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  margin-bottom: 12px;
}

.nav-list {
  list-style: none;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 16px;
  border-radius: 8px;
  cursor: pointer;
  font-size: 14px;
  font-weight: 500;
  color: #374151;
  transition: all 0.2s;
  margin-bottom: 4px;
}

.nav-item:hover {
  background: #f3f4f6;
  color: #1f2937;
}

.nav-item.active {
  background: #8b5cf6;
  color: #ffffff;
}

.nav-icon {
  width: 20px;
  height: 20px;
}

/* Main Content */
.main-content {
  flex: 1;
  padding: 20px;
  overflow-y: auto;
}

.content-wrapper {
  max-width: 1200px;
  margin: 0 auto;
}

/* Ideas Section */
.ideas-section {
  background: #ffffff;
  border-radius: 12px;
  border: 1px solid #e5e7eb;
  overflow: hidden;
}

.section-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 24px;
  border-bottom: 1px solid #e5e7eb;
}

.section-title {
  font-size: 24px;
  font-weight: 700;
  color: #1f2937;
}

.add-idea-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  background: #8b5cf6;
  color: #ffffff;
  border: none;
  padding: 12px 20px;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  font-size: 14px;
  transition: all 0.2s;
}

.add-idea-btn:hover {
  background: #7c3aed;
}

.add-icon {
  width: 16px;
  height: 16px;
}

/* Filters */
.filters-container {
  display: flex;
  gap: 16px;
  padding: 20px 24px;
  border-bottom: 1px solid #e5e7eb;
  background: #f8fafc;
}

.search-section {
  flex: 1;
}

/* Ideas Grid */
.ideas-grid-container {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
  gap: 20px;
  padding: 24px;
}

.idea-card {
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  overflow: hidden;
  cursor: pointer;
  transition: all 0.2s;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.idea-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  border-color: #8b5cf6;
}

.idea-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px;
  background: #f8fafc;
  border-bottom: 1px solid #e5e7eb;
}

.idea-author {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 14px;
  font-weight: 500;
  color: #374151;
}

.author-icon {
  width: 16px;
  height: 16px;
  color: #8b5cf6;
}

.idea-date {
  font-size: 12px;
  color: #6b7280;
}

.idea-content {
  padding: 16px;
}

.idea-title {
  font-size: 18px;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 12px;
  line-height: 1.4;
}

.idea-preview {
  font-size: 14px;
  color: #6b7280;
  line-height: 1.5;
  margin-bottom: 16px;
}

.idea-actions {
  display: flex;
  gap: 8px;
  padding: 0 16px 16px;
}

.view-btn {
  display: flex;
  align-items: center;
  gap: 6px;
  background: #f3f4f6;
  color: #374151;
  border: 1px solid #d1d5db;
  padding: 8px 12px;
  border-radius: 6px;
  cursor: pointer;
  font-size: 12px;
  font-weight: 500;
  transition: all 0.2s;
}

.view-btn:hover {
  background: #e5e7eb;
}

.view-icon {
  width: 14px;
  height: 14px;
}

/* Empty State */
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 60px 20px;
  text-align: center;
}

.empty-icon {
  width: 64px;
  height: 64px;
  color: #d1d5db;
  margin-bottom: 16px;
}

.empty-title {
  font-size: 20px;
  font-weight: 600;
  color: #374151;
  margin-bottom: 8px;
}

.empty-description {
  font-size: 16px;
  color: #6b7280;
}

/* Modal Styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 20px;
}

.modal-content {
  background: #ffffff;
  border-radius: 12px;
  max-width: 600px;
  width: 100%;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 24px;
  border-bottom: 1px solid #e5e7eb;
}

.modal-title {
  font-size: 20px;
  font-weight: 600;
  color: #1f2937;
}

.modal-close {
  background: none;
  border: none;
  cursor: pointer;
  padding: 8px;
  border-radius: 6px;
  color: #6b7280;
  transition: all 0.2s;
}

.modal-close:hover {
  background: #f3f4f6;
  color: #374151;
}

.modal-close svg {
  width: 20px;
  height: 20px;
}

/* Form Styles */
.modal-form {
  padding: 24px;
}

.form-group {
  margin-bottom: 20px;
}

.form-label {
  display: block;
  font-size: 14px;
  font-weight: 500;
  color: #374151;
  margin-bottom: 6px;
}

.form-input,
.form-textarea {
  width: 100%;
  padding: 12px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 14px;
  color: #1f2937;
  background: white;
  transition: border-color 0.2s;
  font-family: inherit;
}

.form-input:focus,
.form-textarea:focus {
  outline: none;
  border-color: #8b5cf6;
  box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
}

.form-textarea {
  resize: vertical;
  min-height: 120px;
}

/* Modal Actions */
.modal-actions {
  display: flex;
  gap: 12px;
  justify-content: flex-end;
  padding-top: 20px;
  border-top: 1px solid #e5e7eb;
}

.cancel-btn {
  background: #ffffff;
  color: #374151;
  border: 1px solid #d1d5db;
  padding: 12px 20px;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 500;
  font-size: 14px;
  transition: all 0.2s;
}

.cancel-btn:hover {
  background: #f3f4f6;
}

.submit-btn {
  background: #8b5cf6;
  color: #ffffff;
  border: none;
  padding: 12px 20px;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  font-size: 14px;
  transition: all 0.2s;
}

.submit-btn:hover:not(:disabled) {
  background: #7c3aed;
}

.submit-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Idea Details Modal */
.idea-details-modal {
  padding: 24px;
}

.detail-row {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  padding: 12px 0;
  border-bottom: 1px solid #f3f4f6;
}

.detail-row:last-child {
  border-bottom: none;
}

.detail-row.detail-full {
  flex-direction: column;
  align-items: stretch;
  gap: 8px;
}

.detail-row strong {
  min-width: 120px;
  color: #374151;
  font-weight: 600;
  flex-shrink: 0;
}

.idea-full-details {
  background: #f8fafc;
  padding: 16px;
  border-radius: 8px;
  line-height: 1.6;
  color: #374151;
  white-space: pre-wrap;
}

/* Mobile Styles */
@media (max-width: 768px) {
  .mobile-menu-btn {
    display: block;
  }

  .sidebar {
    position: fixed;
    top: 0;
    left: -280px;
    width: 280px;
    height: 100vh;
    z-index: 200;
    transition: left 0.3s;
    padding-top: 60px;
  }

  .sidebar.mobile-open {
    left: 0;
  }

  .mobile-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 150;
  }

  .mobile-close-btn {
    display: block;
  }

  .main-content {
    padding: 16px;
  }

  .ideas-grid-container {
    grid-template-columns: 1fr;
    gap: 16px;
    padding: 16px;
  }

  .modal-content {
    margin: 20px;
    max-height: calc(100vh - 40px);
  }

  .profile-name {
    display: none;
  }

  .search-container {
    max-width: none;
  }
}

@media (max-width: 480px) {
  .header {
    padding: 0 16px;
  }

  .section-header {
    flex-direction: column;
    gap: 16px;
    align-items: stretch;
  }

  .add-idea-btn {
    justify-content: center;
  }
}
</style>
