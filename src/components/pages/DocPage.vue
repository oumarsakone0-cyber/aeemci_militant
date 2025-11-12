<template>
  <div class="min-h-screen bg-gradient-to-br from-emerald-50 via-white to-teal-50 relative overflow-hidden pb-24 md:pb-8">
    <!-- Décorations améliorées -->
    <div class="absolute top-0 left-0 w-[500px] h-[500px] bg-gradient-to-br from-emerald-400/20 to-teal-400/20 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2 animate-pulse"></div>
    <div class="absolute bottom-0 right-0 w-[600px] h-[600px] bg-gradient-to-tl from-emerald-400/15 to-cyan-400/15 rounded-full blur-3xl translate-x-1/2 translate-y-1/2"></div>
    <div class="absolute top-1/3 right-1/4 w-[400px] h-[400px] bg-teal-400/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 2s;"></div>

    <!-- HEADER -->
    <header class="relative backdrop-blur-md bg-white/70 shadow-xl border-b border-emerald-200/50 sticky top-0 z-40">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 md:py-7 flex justify-between items-center flex-wrap gap-4">
        <div class="flex items-center space-x-4">
          <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 via-emerald-600 to-teal-600 rounded-2xl flex items-center justify-center shadow-2xl shadow-emerald-500/40 transform hover:scale-105 transition-transform duration-300">
            <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 4a8 8 0 00-8 8v5a1 1 0 001 1h2v-4H6v-2a6 6 0 0112 0v2h-1v4h2a1 1 0 001-1v-5a8 8 0 00-8-8z" />
              <path d="M9 19h6v-6H9v6z" />
            </svg>
          </div>
          <div>
            <h1 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-emerald-700 via-emerald-600 to-teal-600 bg-clip-text text-transparent tracking-tight">
              Documents Officiels AEEMCI
            </h1>
            <p class="text-xs md:text-sm text-gray-600 font-medium mt-0.5">PAF, Règlements, Statuts, Décisions…</p>
          </div>
        </div>
        <button
        v-if="userStore.user.je_suis === 'CE_CE'"
          @click="showAddModal = true"
          class="group px-5 md:px-6 py-2.5 bg-gradient-to-r from-emerald-600 via-emerald-500 to-teal-500 text-white rounded-xl hover:shadow-2xl hover:shadow-emerald-500/50 transition-all duration-300 flex items-center space-x-2 text-sm md:text-base font-semibold hover:scale-105 active:scale-95"
        >
          <svg class="w-5 h-5 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
          </svg>
          <span>Ajouter</span>
        </button>
      </div>
    </header>

    <!-- CONTENU PRINCIPAL -->
    <main class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-10">
      <!-- Barre de recherche améliorée -->
      <div class="bg-white/80 backdrop-blur-md rounded-3xl shadow-2xl border border-emerald-100/50 p-5 md:p-7 mb-8 md:mb-10 hover:shadow-emerald-100 transition-shadow duration-300">
        <div class="flex flex-col lg:flex-row gap-3 md:gap-4">
          <div class="flex-1 relative group">
            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-emerald-500 group-focus-within:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input
              v-model="searchQuery"
              @input="filterDocs"
              type="text"
              placeholder="Rechercher un document..."
              class="w-full pl-12 pr-5 py-3.5 border-2 border-emerald-100 rounded-2xl focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all bg-white/70 backdrop-blur-sm font-medium placeholder:text-gray-400 text-gray-900"
            />
          </div>
        </div>
      </div>

            
       <!-- Grille des documents améliorée -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <div
                v-for="doc in filteredDocs"
                :key="doc.id"
                class="group bg-white/90 backdrop-blur-sm rounded-3xl border border-emerald-100/50 shadow-xl hover:-translate-y-2 hover:shadow-2xl hover:shadow-emerald-200/50 transition-all duration-500 cursor-pointer overflow-hidden flex hover:border-emerald-300"
                @click="openDoc(doc)"
            >
                <!-- Image à gauche améliorée -->
                <div class="w-28 h-30 flex-shrink-0 overflow-hidden rounded-l-3xl bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center p-2 group-hover:scale-105 transition-transform duration-500">
                  <img
                    :src="doc.image_url || 'https://upload.wikimedia.org/wikipedia/fr/4/42/Logo_AEEMCI.jpeg'"
                    alt="Image document"
                    class="max-w-full max-h-full object-contain rounded-xl"
                  />
                </div>

                <!-- Contenu à droite amélioré -->
                <div class="p-5 flex flex-col justify-between flex-1" style="text-align: left;">
                  <div>
                    <h3 class="font-bold text-gray-900 line-clamp-2 group-hover:text-emerald-600 transition-colors text-base leading-snug">
                      {{ doc.titre }}
                    </h3>
                    <p class="text-sm text-gray-600 mt-2 line-clamp-3 leading-relaxed">{{ doc.description  }}</p>
                  </div>

                  <div class="flex flex-col text-xs text-gray-500 space-y-1 mt-3 pt-3 border-t border-gray-100">
                    <div class="flex items-center space-x-1.5">
                      <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                      </svg>
                      <span class="font-medium">{{ formatDate(doc.date) }}</span>
                    </div>
                  </div>
                </div>
            </div>
        </div>

      <!-- État vide amélioré -->
      <div v-if="filteredDocs.length === 0" class="text-center py-24">
        <div class="bg-white/90 backdrop-blur-md p-10 rounded-3xl shadow-2xl max-w-md mx-auto border border-emerald-100">
          <div class="w-20 h-20 bg-gradient-to-br from-emerald-100 to-teal-100 rounded-full flex items-center justify-center mx-auto mb-5">
            <svg class="w-10 h-10 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
          </div>
          <h3 class="text-xl font-bold text-gray-900 mb-2">Aucun document trouvé</h3>
          <p class="text-gray-500 text-sm leading-relaxed">Essayez une autre recherche ou ajoutez un nouveau document.</p>
        </div>
      </div>
    </main>

    <!-- MODALE AJOUT améliorée -->
    <div
      v-if="showAddModal"
      class="fixed inset-0 bg-black/70 backdrop-blur-md flex items-center justify-center p-4 z-50 animate-fade-in"
      @click="closeModal"
    >
      <div
        class="bg-white/95 backdrop-blur-xl rounded-3xl shadow-2xl max-w-lg w-full border border-emerald-200/50 flex flex-col max-h-[80vh] animate-scale-in"
        @click.stop
      >
        <!-- Header amélioré -->
        <div class="flex items-center justify-between p-7 border-b border-emerald-100 sticky top-0 bg-white/95 backdrop-blur-xl z-10 rounded-t-3xl">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-500 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/30">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
              </svg>
            </div>
            <h2 class="text-2xl font-bold bg-gradient-to-r from-emerald-700 to-teal-600 bg-clip-text text-transparent">
              Ajouter un document
            </h2>
          </div>
          <button @click="closeModal" class="w-10 h-10 flex items-center justify-center rounded-xl text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-all duration-200 font-bold text-xl">✕</button>
        </div>

        <!-- Contenu scrollable amélioré -->
        <div class="p-7 overflow-y-auto space-y-5">
          <!-- Champs utilisateur -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-bold text-gray-700 mb-2">Nom</label>
              <input type="text" :value="addDocForm.ajout_par_nom" disabled
                class="w-full px-4 py-3 border-2 border-emerald-100 rounded-xl bg-gradient-to-br from-gray-50 to-gray-100 text-gray-600 font-medium" />
            </div>

            <div>
              <label class="block text-sm font-bold text-gray-700 mb-2">Prénom</label>
              <input type="text" :value="addDocForm.ajout_par_prenom" disabled
                class="w-full px-4 py-3 border-2 border-emerald-100 rounded-xl bg-gradient-to-br from-gray-50 to-gray-100 text-gray-600 font-medium" />
            </div>
          </div>

          <!-- Titre -->
          <div>
            <label class="block text-sm font-bold text-gray-700 mb-2 flex items-center space-x-2">
              <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
              </svg>
              <span>Titre du document</span>
            </label>
            <input v-model="addDocForm.titre" type="text" required
              class="w-full px-4 py-3 border-2 border-emerald-100 rounded-xl focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all font-medium" />
          </div>

          <!-- Image -->
          <div>
            <label class="block text-sm font-bold text-gray-700 mb-2 flex items-center space-x-2">
              <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
              <span>Image</span>
            </label>
            <input @change="handleImageUpload" type="file" accept="image/*"
              class="w-full px-4 py-3 border-2 border-emerald-100 rounded-xl file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-emerald-50 file:text-emerald-700 file:font-semibold hover:file:bg-emerald-100 transition-all cursor-pointer" />
            <div v-if="imagePreview" class="mt-3 p-3 bg-emerald-50 rounded-xl border border-emerald-100 text-sm text-emerald-700 font-medium flex items-center space-x-2">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
              </svg>
              <span>{{ imagePreview.name }} ({{ imagePreview.size }})</span>
            </div>
            <div v-if="isUploadingImage" class="mt-2 text-emerald-600 text-sm font-semibold flex items-center space-x-2">
              <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>Upload en cours...</span>
            </div>
          </div>

          <!-- Fichier PDF -->
          <div>
            <label class="block text-sm font-bold text-gray-700 mb-2 flex items-center space-x-2">
              <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"/>
              </svg>
              <span>Fichier PDF</span>
            </label>
            <input @change="handlePdfUpload" type="file" accept=".pdf" required
              class="w-full px-4 py-3 border-2 border-emerald-100 rounded-xl file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-red-50 file:text-red-700 file:font-semibold hover:file:bg-red-100 transition-all cursor-pointer" />
            <div v-if="pdfPreview" class="mt-3 p-3 bg-red-50 rounded-xl border border-red-100 text-sm text-red-700 font-medium flex items-center space-x-2">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
              </svg>
              <span>{{ pdfPreview.name }} ({{ pdfPreview.size }})</span>
            </div>
            <div v-if="isUploadingPdf" class="mt-2 text-emerald-600 text-sm font-semibold flex items-center space-x-2">
              <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>Upload en cours...</span>
            </div>
          </div>

          <!-- Bouton amélioré -->
          <div class="flex justify-end pt-6">
            <button :disabled="isSubmitting" type="button" @click="submitDoc"
              class="group px-6 py-3 bg-gradient-to-r from-emerald-600 via-emerald-500 to-teal-500 text-white rounded-xl font-bold hover:shadow-2xl hover:shadow-emerald-500/50 active:scale-95 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-2">
              <svg v-if="isSubmitting" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <svg v-else class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
              </svg>
              <span>{{ isSubmitting ? 'Enregistrement...' : 'Enregistrer' }}</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de confirmation après ajout réussi -->
    <div
      v-if="showSuccessModal"
      class="fixed inset-0 bg-black/70 backdrop-blur-md flex items-center justify-center p-4 z-50 animate-fade-in"
      @click="closeSuccessModal"
    >
      <div
        class="bg-white/95 backdrop-blur-xl rounded-3xl shadow-2xl max-w-md w-full border border-emerald-200/50 animate-scale-in overflow-hidden"
        @click.stop
      >
        <!-- Header avec icône de succès -->
        <div class="relative bg-gradient-to-br from-emerald-500 via-emerald-600 to-teal-600 p-8 text-center">
          <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto shadow-2xl animate-bounce" style="animation-duration: 0.6s; animation-iteration-count: 2;">
            <svg class="w-10 h-10 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
            </svg>
          </div>
        </div>

        <!-- Contenu -->
        <div class="p-8 space-y-4">
          <h3 class="text-2xl font-black text-center bg-gradient-to-r from-emerald-700 to-teal-600 bg-clip-text text-transparent">
            Document ajouté avec succès !
          </h3>
          
          <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl p-5 border border-emerald-100 space-y-3">
            <div class="flex items-start space-x-3">
              <svg class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
              <div class="flex-1">
                <p class="text-sm font-semibold text-gray-500">Titre du document</p>
                <p class="text-base font-bold text-gray-900 mt-0.5">{{ addedDocInfo.titre }}</p>
              </div>
            </div>

            <div class="flex items-start space-x-3">
              <svg class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
              </svg>
              <div class="flex-1">
                <p class="text-sm font-semibold text-gray-500">Ajouté par</p>
                <p class="text-base font-bold text-gray-900 mt-0.5">{{ addedDocInfo.ajout_par }}</p>
              </div>
            </div>

            <div class="flex items-start space-x-3">
              <svg class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
              <div class="flex-1">
                <p class="text-sm font-semibold text-gray-500">Date d'ajout</p>
                <p class="text-base font-bold text-gray-900 mt-0.5">{{ addedDocInfo.date }}</p>
              </div>
            </div>
          </div>

          <p class="text-center text-sm text-gray-600 leading-relaxed">
            Votre document est maintenant disponible dans la liste des documents officiels AEEMCI.
          </p>

          <!-- Bouton de fermeture -->
          <button
            @click="closeSuccessModal"
            class="w-full group px-6 py-3 bg-gradient-to-r from-emerald-600 via-emerald-500 to-teal-500 text-white rounded-xl font-bold hover:shadow-2xl hover:shadow-emerald-500/50 active:scale-95 transition-all duration-300 flex items-center justify-center space-x-2"
          >
            <span>Continuer</span>
            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
            </svg>
          </button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue"
import { useUserStore } from "../../stores/user"
import { uploadToCloudinary } from '../../utils/cloudinary.js'

const API_BASE_URL = "https://sogetrag.com/api/documents.php"
const userStore = useUserStore()

const docs = ref([])
const searchQuery = ref("")
const showAddModal = ref(false)
const showSuccessModal = ref(false)
const addedDocInfo = ref({
  titre: "",
  ajout_par: "",
  date: ""
})
const pdfPreview = ref(null)
const imagePreview = ref(null)
const isUploadingPdf = ref(false)
const isUploadingImage = ref(false)
const isSubmitting = ref(false)

const addDocForm = ref({
  titre: "",
  description: `Document ajouté par ${userStore.user?.nom || ''} ${userStore.user?.prenom || ''}`,
  image_url: "",
  fichier: null,
  fichier_url: "",
  date: "",
  ajout_par_nom: userStore.user?.nom || '',
  ajout_par_prenom: userStore.user?.prenom || "Prénom Inconnu",
  ajout_par_matricule: userStore.user?.matricule_gen || ''
});

const loadDocs = async () => {
  try {
    const randomId = Math.floor(Math.random() * 1000000)
    console.log("ID aléatoire :", randomId)

    const res = await fetch(`${API_BASE_URL}?action=list&rand=${randomId}`)
    const data = await res.json()
    if (data.success) {
      docs.value = data.data.map(d => ({
        ...d,
        randomId,
        fichier_url: d.fichier_url || "https://upload.wikimedia.org/wikipedia/fr/4/42/Logo_AEEMCI.jpeg",
        image_url: d.image_url || "https://upload.wikimedia.org/wikipedia/fr/4/42/Logo_AEEMCI.jpeg"
      }))
    }
  } catch (e) {
    console.error(e)
  }
}

const filteredDocs = computed(() => {
  return docs.value.filter(d =>
    (!searchQuery.value || d.titre.toLowerCase().includes(searchQuery.value.toLowerCase())) 
  )
})

const filterDocs = () => {}

const handlePdfUpload = async (event) => {
  const file = event.target.files[0]
  if (!file) return
  isUploadingPdf.value = true
  pdfPreview.value = { name: file.name, size: (file.size / 1024 / 1024).toFixed(2) + " MB" }

  try {
    const result = await uploadToCloudinary(file, 'pdf')
    if (result.success) {
      addDocForm.value.fichier = file
      addDocForm.value.fichier_url = result.url
    } else {
      alert(result.error)
      addDocForm.value.fichier = null
      addDocForm.value.fichier_url = ""
      pdfPreview.value = null
    }
  } catch (e) { console.error(e) }
  finally { isUploadingPdf.value = false }
}

const handleImageUpload = async (event) => {
  const file = event.target.files[0]
  if (!file) return
  isUploadingImage.value = true
  imagePreview.value = { name: file.name, size: (file.size / 1024 / 1024).toFixed(2) + " MB" }

  try {
    const result = await uploadToCloudinary(file, 'image')
    if (result.success) addDocForm.value.image_url = result.url
    else {
      alert(result.error)
      addDocForm.value.image_url = ""
      imagePreview.value = null
    }
  } catch (e) { console.error(e) }
  finally { isUploadingImage.value = false }
}

const submitDoc = async () => {
  if (!addDocForm.value.titre || !addDocForm.value.fichier_url) return;

  const payload = {
    titre: addDocForm.value.titre,
    description: addDocForm.value.description,
    image_url: addDocForm.value.image_url,
    fichier_url: addDocForm.value.fichier_url,
    ajout_par_nom: `${userStore.user?.nom || ''} ${userStore.user?.prenom || ''}`,
    ajout_par_matricule: userStore.user?.matricule_gen || ''
  };

  isSubmitting.value = true;

  try {
    const res = await fetch(`${API_BASE_URL}?action=add`, {
      method: 'POST',
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload)
    });

    const data = await res.json();
    if (data.success) {
      addedDocInfo.value = {
        titre: addDocForm.value.titre,
        ajout_par: `${userStore.user?.nom || ''} ${userStore.user?.prenom || ''}`,
        date: new Date().toLocaleDateString("fr-FR", { 
          day: 'numeric', 
          month: 'long', 
          year: 'numeric' 
        })
      };
      
      showAddModal.value = false;
      showSuccessModal.value = true;
      
      addDocForm.value = { 
        titre: "", 
        description: `Document ajouté par ${userStore.user?.nom || ''} ${userStore.user?.prenom || ''}`,
        image_url: "",
        fichier: null, 
        fichier_url: "",
        date: "",
        ajout_par_matricule: userStore.user?.matricule_gen || '',
        ajout_par_nom: userStore.user?.nom || '',
        ajout_par_prenom: userStore.user?.prenom || "Prénom Inconnu"
      };
      pdfPreview.value = null;
      imagePreview.value = null;
      await loadDocs();
    } else {
      alert("Erreur: " + data.error);
    }
  } catch (e) {
    console.error(e);
    alert("Erreur lors de l'ajout du document");
  } finally {
    isSubmitting.value = false;
  }
};

const deleteDoc = async (id) => {
  if (!confirm("Voulez-vous vraiment supprimer ce document ?")) return
  try {
    const res = await fetch(`${API_BASE_URL}?action=delete&id=${id}`, { method: 'DELETE' })
    const data = await res.json()
    if (data.success) loadDocs()
  } catch (e) { console.error(e) }
}

const openDoc = (doc) => {
  window.open(doc.fichier_url, "_blank")
}

const closeModal = () => {
  showAddModal.value = false
  addDocForm.value = {
    titre: "",
    description: `Document ajouté par ${userStore.user?.nom || ''} ${userStore.user?.prenom || ''}`,
    image_url: "",
    fichier: null,
    fichier_url: "",
    date: "",
    ajout_par_matricule: userStore.user?.matricule_gen || '',
    ajout_par_nom: userStore.user?.nom || '',
    ajout_par_prenom: userStore.user?.prenom || "Prénom Inconnu"
  }
  pdfPreview.value = null
  imagePreview.value = null
}

const closeSuccessModal = () => {
  showSuccessModal.value = false
}

const formatDate = (d) => new Date(d).toLocaleDateString("fr-FR")

onMounted(loadDocs)
</script>

<style scoped>
@keyframes scale-in {
  0% { transform: scale(0.9); opacity: 0; }
  100% { transform: scale(1); opacity: 1; }
}

@keyframes fade-in {
  0% { opacity: 0; }
  100% { opacity: 1; }
}

.animate-scale-in { 
  animation: scale-in 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}

.animate-fade-in { 
  animation: fade-in 0.3s ease-in-out;
}
</style>
