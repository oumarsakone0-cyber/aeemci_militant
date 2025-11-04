<template>
  <div class="min-h-screen bg-gradient-to-br from-emerald-50 to-amber-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-emerald-100">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-emerald-600 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
              </svg>
            </div>
            <div>
              <h1 class="text-2xl font-bold text-gray-900">Mes Secrétariats</h1>
              <p class="text-sm text-gray-600">Gérez vos secrétariats</p>
            </div>
          </div>
          <div class="flex items-center space-x-4" style="display: grid;">
            <button
              @click="openAddModal"
              class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors flex items-center space-x-2"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
              </svg>
              <span>Ajouter un secrétariat</span>
            </button>
            <div class="text-right">
              <p class="text-sm text-gray-600">{{ secretariats.length }} secrétariats</p>
            </div>
          </div>
        </div>
      </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Search and Filters -->
      <div class="bg-white rounded-xl shadow-sm border border-emerald-100 p-6 mb-8">
        <div class="flex flex-col lg:flex-row gap-4">
          <!-- Search Bar -->
          <div class="flex-1">
            <div class="relative">
              <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
              </svg>
              <input
                v-model="searchQuery"
                @input="searchSecretariats"
                type="text"
                placeholder="Rechercher un secrétariat..."
                class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
              />
            </div>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="isLoading" class="flex flex-col items-center justify-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-emerald-600"></div>
        <p class="mt-4 text-gray-600">Chargement des secrétariats...</p>
      </div>

      <!-- Secrétariats Grid -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">

        <div
          v-for="sec in filteredSecretariats"
          :key="sec.id"
          class="bg-white rounded-xl shadow-sm border border-emerald-100 overflow-hidden hover:shadow-lg hover:border-emerald-200 transition-all duration-300"
        >
          <div class="p-6">
            <div class="flex items-start justify-between mb-4">
              <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                  <span class="text-emerald-600 font-bold text-sm">SR</span>
                </div>
                <div>
                  <h3 class="font-semibold text-gray-900">{{ sec.nom_secretariat }}</h3>
                  <p class="text-sm text-gray-500">Secrétariat Régional</p>
                </div>
              </div>
              <div class="flex space-x-2">
                <button
                  @click="editSecretariat(sec)"
                  class="p-2 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors"
                  title="Modifier"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                  </svg>
                </button>
                <button
                  @click="confirmDelete(sec.id, sec.nom_secretariat)"
                  class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                  title="Supprimer"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                  </svg>
                </button>
              </div>
            </div>
            
            <div class="space-y-3">
              <div class="flex items-center space-x-3">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span class="text-sm text-gray-600">{{ sec.sr }}</span>
              </div>
              
              <div class="flex items-center space-x-3">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                <span class="text-sm text-gray-600">{{ sec.contact_sr }}</span>
              </div>
              
              <div class="flex items-center space-x-3">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span class="text-sm text-gray-600">{{ sec.region_secretariat }}</span>
              </div>
            </div>
            
            <div class="mt-4 pt-4 border-t border-gray-100">
              <p class="text-xs text-gray-500">Créé le {{ formatDate(sec.created_at) }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="!isLoading && filteredSecretariats.length === 0" class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun secrétariat trouvé</h3>
        <p class="mt-1 text-sm text-gray-500">Commencez par ajouter votre premier secrétariat.</p>
      </div>
    </main>

  <!-- Add/Edit Modal -->
      <div
    v-if="showAddModal || showEditModal"
    style="z-index: 1111"
    class="modal-overlay"
    @click="closeModal"
  >
    <div
      class="modal-content"
      @click.stop
    >
      <div class="modal-body">
        <div class="modal-header">
          <h2 class="modal-title">
            {{ showEditModal ? 'Modifier le secrétariat' : (currentStep === 1 ? 'Vérification du secrétaire' : 'Ajouter un secrétariat') }}
          </h2>
          <button
            @click="closeModal"
            class="modal-close"
          >
            <svg class="close-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
          </div>

        <!-- Indicateur d'étapes -->
        <div class="step-indicator">
          <div class="step" :class="{ active: currentStep === 1, completed: currentStep > 1 }">
            <div class="step-number">1</div>
            <div class="step-label">Vérification</div>
          </div>
          <div class="step-line" :class="{ completed: currentStep > 1 }"></div>
          <div class="step" :class="{ active: currentStep === 2 }">
            <div class="step-number">2</div>
            <div class="step-label">Formulaire</div>
        </div>
      </div>

        <!-- Étape 1: Vérification du matricule -->
        <div v-if="currentStep === 1 && !showEditModal" class="verification-step">
          <div class="verification-content">
            <div class="verification-icon">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
    </div>
            <h3 class="verification-title">Vérification du secrétaire</h3>
            <p class="verification-description">
              Saisissez le matricule du secrétaire pour récupérer automatiquement ses informations.
            </p>
            
            <div class="form-group">
              <label class="form-label">Matricule du secrétaire *</label>
              <input
                v-model="matriculeInput"
                type="text"
                required
                class="form-input"
                placeholder="Ex: ABO-SOU-24-002"
                :disabled="isVerifying"
              />
              <div v-if="verificationError" class="error-message">
                {{ verificationError }}
              </div>
    </div>

            <div class="verification-actions">
              <button
                @click="verifyMatricule"
                :disabled="isVerifying || !matriculeInput.trim()"
                class="verify-button"
              >
                <svg v-if="isVerifying" class="spinner" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <svg v-else class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ isVerifying ? 'Vérification...' : 'Vérifier le matricule' }}
              </button>
        </div>
          </div>
            </div>

        <!-- Étape 2: Formulaire de création -->
        <div v-if="currentStep === 2 || showEditModal" class="form-step">
          <!-- Informations récupérées (si ajout) -->
          <div v-if="foundUser && !showEditModal" class="retrieved-info">
            <div class="info-header">
              <svg class="info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <h4>Informations récupérées avec succès</h4>
            </div>
            </div>

          <form @submit.prevent="submitForm" class="form">
          <div class="form-group">
            <label class="form-label">Nom du secrétariat *</label>
            <input
              v-model="form.nom_secretariat"
              type="text"
              required
              class="form-input"
              placeholder="Ex: Abidjan"
            />
            </div>

              <div class="form-group">
            <label class="form-label">Secrétaire régional *</label>
            <input
              v-model="form.secretaire_regional"
              type="text"
              required
              class="form-input"
              placeholder="Ex: Mohamed Traoré"
            />
              </div>

              <div class="form-group">
            <label class="form-label">Contact SR *</label>
            <input
              v-model="form.contact_sr"
              type="tel"
              required
              class="form-input"
              placeholder="Ex: 0714157323"
            />
              </div>

              <div class="form-group">
            <label class="form-label">Région secrétariat *</label>
            <input
              v-model="form.region_secretariat"
              type="text"
              required
              class="form-input"
              placeholder="Ex: Abidjan"
            />
              </div>

              <div class="form-group">
            <label class="form-label">Matricule SR *</label>
            <input
              v-model="form.matricule_sr"
              type="text"
              required
              class="form-input"
              placeholder="Ex: ABO-SOU-24-002"
            />
              </div>

              <div class="form-actions">
            <button
              v-if="currentStep === 2 && !showEditModal"
              type="button"
              @click="backToVerification"
              class="back-button"
            >
              <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
              </svg>
              Retour à la vérification
                </button>
            <button
              type="button"
              @click="closeModal"
              class="cancel-button"
            >
              Annuler
            </button>
            <button
              type="submit"
              :disabled="isSubmitting"
              class="submit-button"
            >
              {{ isSubmitting ? 'Enregistrement...' : (showEditModal ? 'Modifier' : 'Ajouter') }}
            </button>
              </div>
        </form>
            </div>
          </div>
        </div>
      </div>

<!-- Modal de confirmation de suppression -->
    <div v-if="showDeleteModal" class="modal-overlay" @click="showDeleteModal = false">
      <div class="modal-content delete-modal" @click.stop>
            <div class="modal-header">
          <h3>Confirmer la suppression</h3>
          <button @click="showDeleteModal = false" class="close-button">×</button>
            </div>
            <div class="modal-body">
          <div class="warning-icon">⚠</div>
          <p>Êtes-vous sûr de vouloir supprimer le secrétariat <strong>"{{ deleteName }}"</strong> ?</p>
          <p class="warning-text">Cette action est irréversible !</p>
              </div>
        <div class="modal-footer">
          <button @click="showDeleteModal = false" class="cancel-button">
            Annuler
          </button>
          <button @click="deleteSecretariat(deleteId)" class="confirm-delete-button">
            Supprimer définitivement
          </button>
            </div>
          </div>
        </div>

<!-- Notifications -->
    <div class="notifications-container">
      <div
        v-for="notification in notifications"
        :key="notification.id"
    :class="['notification', `notification-${notification.type}`]"

        @click="removeNotification(notification.id)"
      >
        <div class="notification-icon">
          <span v-if="notification.type === 'success'">✅</span>
          <span v-else-if="notification.type === 'error'">❌</span>
          <span v-else-if="notification.type === 'warning'">⚠</span>
          <span v-else>ℹ</span>
          </div>
        <div class="notification-content">
          <p class="notification-message">{{ notification.message }}</p>
          <p class="notification-time">{{ formatTime(notification.timestamp) }}</p>
        </div>
        <button @click="removeNotification(notification.id)" class="notification-close">×</button>
      </div>
  
    </div>

     <div class="fixed bottom-0 left-0 right-0 bg-white border-t px-4 py-2 md:hidden">
    <div class="flex justify-around items-center max-w-md mx-auto">
      
      <router-link to="/" class="flex flex-col items-center">
        <svg class="w-6 h-6 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
          <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
        </svg>
        <span class="text-xs text-gray-600 mt-1 font-medium">Accueil</span>
      </router-link>

      <router-link to="/" class="flex flex-col items-center">
        <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
          <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
        </svg>
        <span class="text-xs text-blue-600 mt-1">Explorer</span>
      </router-link>

      <router-link to="/" class="flex flex-col items-center">
        <svg class="w-6 h-6 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
          <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>
        </svg>
        <span class="text-xs text-gray-600 mt-1">Fichiers</span>
      </router-link>

      <router-link to="/" class="flex flex-col items-center">
        <svg class="w-6 h-6 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
          <path d="M3 3h18v2H3V3zm0 4h18v2H3V7zm0 4h18v2H3v-2z"/>
        </svg>
        <span class="text-xs text-gray-600 mt-1">Onglets</span>
      </router-link>

      <router-link to="/" class="flex flex-col items-center relative">
        <svg class="w-6 h-6 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
          <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
        </svg>
        <span class="text-xs text-gray-600 mt-1">Profile</span>
        <div class="absolute -top-1 -right-1 bg-red-500 w-3 h-3 rounded-full"></div>
      </router-link>

    </div>
  </div>
        
  </div>

</template>

<script setup>
import { ref, onMounted, computed } from 'vue'

// Data
const secretariats = ref([])
const isLoading = ref(false)
const searchQuery = ref('')
const showAddModal = ref(false)
const showEditModal = ref(false)
const showDeleteModal = ref(false)
const deleteId = ref(null)
const deleteName = ref('')
const isSubmitting = ref(false)

// Form data
const form = ref({
nom_secretariat: '',
secretaire_regional: '',
contact_sr: '',
region_secretariat: '',
matricule_sr: ''
})

// Two-step form
const currentStep = ref(1)
const matriculeInput = ref('')
const isVerifying = ref(false)
const verificationError = ref('')
const foundUser = ref(null)

// Notifications
const notifications = ref([])

// API URLs - Utilisation de l'API des organes qui fonctionne
const API_BASE_URL = 'https://sogetrag.com/apistage/mes_secretariats_api.php'
const ORGANES_API_URL = 'https://sogetrag.com/api/organes-api.php'

// Current user
const currentUser = ref({
id: 123,
nom: 'DOGRO',
email: 'test@example.com',
secretariat_id: 'USER123'
})

// Load current user
const loadCurrentUser = async () => {
try {
  // Simuler le chargement de l'utilisateur connecté
  currentUser.value = {
    id: 123,
    nom: 'DOGRO',
    email: 'test@example.com',
    secretariat_id: 'USER123'
  }
  console.log('👤 Utilisateur connecté:', currentUser.value)
} catch (error) {
  console.error('❌ Erreur lors du chargement de l\'utilisateur:', error)
}
}

// Load secretariats
const loadSecretariats = async () => {
isLoading.value = true
try {
  console.log('🔍 Tentative de chargement des secrétariats...')

  const url = `${API_BASE_URL}?action=list&t=${Date.now()}`;

  const response = await fetch(url, {
    method: 'GET',
    headers: {
      'Accept': 'application/json'
    }
  })

  console.log('📊 Status de la réponse:', response.status)

  if (!response.ok) {
    throw new Error(`Erreur HTTP: ${response.status}`);

  }
  
  const data = await response.json()
  console.log('📦 Données brutes reçues:', data)

  if (Array.isArray(data)) {
    // Vider puis remplir
    secretariats.value = data.map(sec => ({
      id: sec.id,
      nom_secretariat: sec.nom_secretariat,
      sr: sec.sr || 'Non défini',
      contact_sr: sec.contact_sr || 'Non défini',
      region_secretariat: sec.region_secretariat || 'Non défini',
      created_at: sec.created_at || new Date().toISOString()
    }))

    console.log('✅ Secrétariats chargés:', secretariats.value.length)
    secretariats.value.forEach((sec, index) => {
    })

    addNotification(`${secretariats.value.length} secrétariat(s) chargé(s)`, 'success')
  } else {
    console.error('❌ Les données ne sont pas dans le format attendu:', data)
    addNotification('Erreur: Format de données invalide', 'error')
  }
} catch (error) {
  console.error('❌ Erreur lors du chargement des secrétariats:', error)
  addNotification('Erreur lors du chargement des secrétariats', 'error')
} finally {
  isLoading.value = false
  console.log('🏁 Chargement terminé, isLoading:', isLoading.value)
}
}

// Format date
const formatDate = (dateString) => {
if (!dateString) return 'Non défini'

const date = new Date(dateString)
const now = new Date()
const diffInSeconds = Math.floor((now - date) / 1000)

if (diffInSeconds < 60) return "À l'instant"
if (diffInSeconds < 3600) 
  return `Il y a ${Math.floor(diffInSeconds / 60)} min`;


return date.toLocaleString('fr-FR')
}

// Format time
const formatTime = (timestamp) => {
const date = new Date(timestamp)
return date.toLocaleTimeString('fr-FR', { 
  hour: '2-digit', 
  minute: '2-digit' 
})
}

// Add notification
const addNotification = (message, type = 'success') => {
const id = Date.now() + Math.random()
notifications.value.push({
  id,
  message,
  type,
  timestamp: Date.now()
})

// Auto remove after 5 seconds
setTimeout(() => {
  removeNotification(id)
}, 5000)
}

// Remove notification
const removeNotification = (id) => {
const index = notifications.value.findIndex(n => n.id === id)
if (index > -1) {
  notifications.value.splice(index, 1)
}
}

// Search secretariats
const searchSecretariats = () => {
// La recherche se fait automatiquement via le computed filteredSecretariats
}

// Filtered secretariats
const filteredSecretariats = computed(() => {
if (!searchQuery.value.trim()) {
  return secretariats.value
}

const query = searchQuery.value.toLowerCase()
return secretariats.value.filter(sec =>
  sec.nom_secretariat?.toLowerCase().includes(query) ||
  sec.sr?.toLowerCase().includes(query) ||
  sec.contact_sr?.toLowerCase().includes(query) ||
  sec.region_secretariat?.toLowerCase().includes(query)
)
})

// Open add modal
const openAddModal = () => {
console.log('🔓 Ouverture du modal d\'ajout')
currentStep.value = 1
matriculeInput.value = ''
isVerifying.value = false
verificationError.value = ''
foundUser.value = null
form.value = {
  nom_secretariat: '',
  secretaire_regional: '',
  contact_sr: '',
  region_secretariat: '',
  matricule_sr: ''
}
showAddModal.value = true
}

// Close modal
const closeModal = () => {
showAddModal.value = false
showEditModal.value = false
currentStep.value = 1
matriculeInput.value = ''
isVerifying.value = false
verificationError.value = ''
foundUser.value = null
form.value = {
  nom_secretariat: '',
  secretaire_regional: '',
  contact_sr: '',
  region_secretariat: '',
  matricule_sr: ''
}
}

// Verify matricule
const verifyMatricule = async () => {
if (!matriculeInput.value.trim()) {
  verificationError.value = 'Veuillez saisir un matricule'
  return
}

isVerifying.value = true
verificationError.value = ''

try {
  console.log('🔍 Vérification du matricule:', matriculeInput.value)
  
  // Utiliser la même API que le login mais sans mot de passe
  const response = await fetch(`${ORGANES_API_URL}?action=get_user_by_matricule2`, {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    matricule: matriculeInput.value.trim()
  })
});

  console.log('📊 Status de la réponse:', response.status)
  console.log('📊 Status OK:', response.ok)

  if (!response.ok) {
    throw new Error(`Erreur HTTP: ${response.status}`);
  }

  const data = await response.json()
  console.log('📦 Réponse de vérification:', data)

  if (data.success && data.user) {
    console.log('✅ Utilisateur trouvé:', data.user)
    foundUser.value = data.user

    // Remplir automatiquement le formulaire
    const nomComplet = data.user.nom + (data.user.prenom ? ' ' + data.user.prenom : '')

    // Chercher la ville dans différentes propriétés possibles
    const ville = data.user.ville || data.user.region || data.user.lieu || data.user.ville_naissance || data.user.ville_residence || ''
    console.log('🏙 Ville détectée:', ville)

const nomSecretariat = ville ? `SR ${ville.toUpperCase()}` : '';
    console.log('📝 Nom du secrétariat généré:', nomSecretariat)

    form.value = {
      nom_secretariat: nomSecretariat,
      secretaire_regional: nomComplet,
      contact_sr: data.user.telephone || data.user.contact || '',
      region_secretariat: ville || '',
      matricule_sr: data.user.matricule || matriculeInput.value.trim()
    }

    console.log('📋 Formulaire auto-rempli:', form.value)

    // Passer à l'étape 2
    currentStep.value = 2
    addNotification('Informations récupérées avec succès', 'success')

  } else {
    console.log('❌ Aucun utilisateur trouvé')
    verificationError.value = data.message || data.error || 'Aucun utilisateur trouvé avec ce matricule'
  }

} catch (error) {
  console.error('❌ Erreur de vérification:', error)
  verificationError.value = 'Erreur lors de la vérification du matricule'

} finally {
  isVerifying.value = false
}
}


// Back to verification
const backToVerification = () => {
currentStep.value = 1
foundUser.value = null
form.value = {
  nom_secretariat: '',
  secretaire_regional: '',
  contact_sr: '',
  region_secretariat: '',
  matricule_sr: ''
}
}

// Submit form
const submitForm = async () => {
if (!form.value.nom_secretariat || !form.value.secretaire_regional || !form.value.contact_sr || !form.value.region_secretariat || !form.value.matricule_sr) {
  addNotification('Veuillez remplir tous les champs obligatoires', 'error')
  return
}

isSubmitting.value = true

try {
  const action = showEditModal.value ? 'update' : 'create';
  const url = showEditModal.value
    ? `${API_BASE_URL}?action=${action}&id=${form.value.id}`
    : `${API_BASE_URL}?action=${action}`;

  const payload = { ...form.value, user_id: currentUser.value.id };
  if (showEditModal.value) delete payload.id;

  const response = await fetch(url, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(payload)
  });

  const result = await response.json()
  console.log('📦 Réponse de l\'API:', result)
  console.log('📊 Status de la réponse:', response.status)
  console.log('✅ Success:', result.success)

  // L'API retourne directement les données au lieu d'un objet avec success
  // On considère que c'est un succès si on a un ID et que le status est 200
  if (response.ok && (result.success === true || result.id || result.nom_secretariat)) {
    addNotification(showEditModal.value ? 'Secrétariat modifié' : 'Secrétariat ajouté', 'success')
    showAddModal.value = false
    showEditModal.value = false
    loadSecretariats()
  } else {
    console.error('❌ Erreur de l\'API:', result)
    addNotification(result.message || 'Erreur lors de l\'enregistrement', 'error')
  }
} catch (error) {
  console.error('Erreur lors de l\'enregistrement:', error)
  addNotification('Erreur lors de l\'enregistrement', 'error')
} finally {
  isSubmitting.value = false
  // S'assure que le modal se ferme toujours
  if (showAddModal.value || showEditModal.value) {
    showAddModal.value = false
    showEditModal.value = false
  }
}
}

// Edit secretariat
const editSecretariat = (sec) => {
console.log('✏ Modification du secrétariat:', sec)
form.value = {
  id: sec.id,
  nom_secretariat: sec.nom_secretariat,
  secretaire_regional: sec.sr,
  contact_sr: sec.contact_sr,
  region_secretariat: sec.region_secretariat,
  matricule_sr: sec.matricule_sr || ''
}
showEditModal.value = true
}



// Confirm delete
const confirmDelete = (id, name) => {
deleteId.value = id
deleteName.value = name
showDeleteModal.value = true
}

// Delete secretariat
const deleteSecretariat = async (id) => {
try {
const response = await fetch(`${API_BASE_URL}?action=delete&id=${id}`, {
      method: 'DELETE', // ou 'GET' selon ton API
      headers: {
        'Content-Type': 'application/json',
      },
    });


  const result = await response.json()
  console.log('📦 Réponse de suppression:', result)

  if (result.success) {
    addNotification('Secrétariat supprimé avec succès', 'success')
    showDeleteModal.value = false
    loadSecretariats()
  } else {
    addNotification(result.message || 'Erreur lors de la suppression', 'error')
  }

} catch (error) {
  console.error('❌ Erreur lors de la suppression:', error)
  addNotification('Erreur lors de la suppression', 'error')
}
}


// Mount
onMounted(async () => {
await loadCurrentUser()
await loadSecretariats()
})
</script>



<style scoped>
/* Page Container */
.page-container {
  min-height: 100vh;
background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 50%, #f0fdf4 100%);
font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  color: #1f2937;
padding: 0;
  box-sizing: border-box;
}

/* Header */
.header {
  background: white;
color: #1f2937;
padding: 2rem 0;
margin-bottom: 2rem;
box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
border-bottom: 1px solid #e5e7eb;
}

.header-content {
  max-width: 1400px;
  margin: 0 auto;
padding: 0 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
gap: 2rem;
}

.header-content-wrapper {
  display: flex;
  align-items: center;
gap: 1rem;
}

.header-icon {
width: 3rem;
height: 3rem;
background: #f3f4f6;
border-radius: 0.75rem;
  display: flex;
  justify-content: center;
  align-items: center;
border: 1px solid #e5e7eb;
}

.header-icon .icon {
width: 1.5rem;
height: 1.5rem;
color: #6b7280;
}

.header-text {
  display: flex;
  flex-direction: column;
gap: 0.25rem;
}

.header-title {
font-size: 2.5rem;
font-weight: 800;
  margin: 0;
color: #1f2937;
letter-spacing: -0.025em;
}

.header-subtitle {
font-size: 1.125rem;
font-weight: 500;
margin: 0;
color: #6b7280;
}

.user-info {
display: flex;
align-items: center;
gap: 0.5rem;
margin-top: 0.5rem;
  font-size: 0.875rem;
  color: #6b7280;
}

.user-label {
font-weight: 500;
}

.user-name {
font-weight: 600;
color: #374151;
}

.user-id {
font-family: 'Monaco', 'Menlo', monospace;
font-size: 0.75rem;
color: #9ca3af;
}

.header-right {
  display: flex;
  align-items: center;
gap: 1.5rem;
}

.add-button {
display: flex;
align-items: center;
gap: 0.5rem;
padding: 0.75rem 1.5rem;
background: #3b82f6;
  color: white;
  border: none;
border-radius: 0.75rem;
font-weight: 600;
  cursor: pointer;
transition: all 0.2s;
}

.add-button:hover {
background: #2563eb;
transform: translateY(-2px);
box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
}

.add-icon {
width: 1.25rem;
height: 1.25rem;
}

.counter {
text-align: right;
}

.counter-text {
  font-size: 0.875rem;
font-weight: 500;
  color: #6b7280;
margin: 0;
}

.debug-text {
font-size: 0.75rem;
color: #9ca3af;
margin: 0.25rem 0 0 0;
}


/* Main Content */
.main-content {
  max-width: 1400px;
  margin: 0 auto;
padding: 0 2rem 2rem;
}

/* Loading Indicator */
.loading-indicator {
  display: flex;
flex-direction: column;
  align-items: center;
  justify-content: center;
padding: 4rem 2rem;
background: white;
border-radius: 1rem;
box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
}

.loading-spinner {
width: 3rem;
height: 3rem;
border: 4px solid #e5e7eb;
border-top: 4px solid #3b82f6;
border-radius: 50%;
animation: spin 1s linear infinite;
margin-bottom: 1rem;
}

@keyframes spin {
0% { transform: rotate(0deg); }
100% { transform: rotate(360deg); }
}

.loading-indicator p {
  color: #6b7280;
font-size: 1rem;
  margin: 0;
font-weight: 500;
}

/* Table View Container */
.table-view-container {
background: white;
border-radius: 1rem;
box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
overflow: hidden;
border: 1px solid #e5e7eb;
}

.table-header {
background: #f8fafc;
padding: 1rem 2rem;
border-bottom: 1px solid #e5e7eb;
display: flex;
justify-content: center;
align-items: center;
border-radius: 1rem 1rem 0 0;
}

.header-left {
display: flex;
flex-direction: column;
gap: 0.5rem;
}

.table-main-title {
font-size: 2rem;
font-weight: 800;
color: #1f2937;
margin: 0;
letter-spacing: -0.025em;
}

.user-info {
display: flex;
align-items: center;
gap: 0.5rem;
margin-top: 0.5rem;
font-size: 0.875rem;
color: #6b7280;
}

.user-label {
font-weight: 500;
}

.user-name {
font-weight: 600;
color: #374151;
}

.user-id {
font-family: 'Monaco', 'Menlo', monospace;
font-size: 0.75rem;
color: #9ca3af;
}

.header-center {
width: 100%;
max-width: 400px;
}

.search-input-wrapper {
  position: relative;
width: 100%;
}

.search-icon {
  position: absolute;
left: 1rem;
  top: 50%;
  transform: translateY(-50%);
width: 1.25rem;
height: 1.25rem;
  color: #9ca3af;
pointer-events: none;
}

.search-input {
  width: 100%;
padding: 0.75rem 1rem 0.75rem 3rem;
  border: 1px solid #d1d5db;
border-radius: 0.75rem;
  font-size: 1rem;
  transition: all 0.2s;
background: white;
}

.search-input:focus {
  outline: none;
border-color: #3b82f6;
box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.table-actions {
display: flex;
align-items: center;
gap: 1rem;
}

/* Table */
.table-wrapper {
overflow-x: auto;
}

.main-table {
width: 100%;
border-collapse: collapse;
font-size: 0.875rem;
}

.main-table thead {
background: #f8fafc;
border-bottom: 2px solid #e5e7eb;
}

.main-table th {
padding: 1rem 1.5rem;
text-align: left;
font-weight: 600;
color: #374151;
font-size: 0.875rem;
text-transform: uppercase;
letter-spacing: 0.05em;
}

.main-table td {
padding: 1rem 1.5rem;
border-bottom: 1px solid #f3f4f6;
vertical-align: middle;
}

.table-row:hover {
background: #f8fafc;
}

/* Table Columns */
.col-nom {
min-width: 200px;
}

.nom-cell {
display: flex;
align-items: center;
gap: 0.75rem;
}

.sr-prefix {
background: linear-gradient(135deg, #3b82f6, #1d4ed8);
  color: white;
padding: 0.25rem 0.5rem;
border-radius: 0.375rem;
font-size: 0.75rem;
font-weight: 600;
text-transform: uppercase;
letter-spacing: 0.05em;
}

.nom-text {
font-weight: 600;
color: #1f2937;
}

.col-sr {
font-weight: 500;
color: #374151;
}

.col-contact {
font-family: 'Monaco', 'Menlo', monospace;
font-size: 0.8rem;
color: #6b7280;
}

.col-region {
color: #4b5563;
font-weight: 500;
}

.col-matricule {
min-width: 120px;
}

.matricule-tag {
background: #f3f4f6;
color: #374151;
padding: 0.25rem 0.5rem;
border-radius: 0.375rem;
font-family: 'Monaco', 'Menlo', monospace;
font-size: 0.75rem;
font-weight: 500;
}

.col-date {
color: #6b7280;
font-size: 0.8rem;
white-space: nowrap;
}

.col-actions {
width: 120px;
text-align: center;
}

.action-buttons {
display: flex;
gap: 0.5rem;
justify-content: center;
}

.action-btn {
width: 2rem;
height: 2rem;
  border: none;
border-radius: 0.5rem;
  cursor: pointer;
display: flex;
align-items: center;
justify-content: center;
  transition: all 0.2s;
}

.action-btn .icon {
width: 1rem;
height: 1rem;
}

.edit-btn {
background: #dbeafe;
color: #1d4ed8;
}

.edit-btn:hover {
background: #bfdbfe;
  transform: translateY(-1px);
}

.delete-btn {
background: #fee2e2;
color: #dc2626;
}

.delete-btn:hover {
background: #fecaca;
transform: translateY(-1px);
}

/* Empty State */
.empty-state {
text-align: center;
padding: 4rem 2rem;
color: #6b7280;
background: white;
border-radius: 1rem;
box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
}

.empty-icon {
width: 4rem;
height: 4rem;
color: #d1d5db;
margin: 0 auto 1rem;
}

.empty-title {
font-size: 1.25rem;
font-weight: 600;
margin: 0 0 0.5rem 0;
color: #374151;
}

.empty-text {
margin: 0;
font-size: 1rem;
}

/* Modals */
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
padding: 1rem;
backdrop-filter: blur(4px);
}

.modal-content {
background: white;
border-radius: 1rem;
max-width: 600px;
width: 100%;
max-height: 90vh;
overflow-y: auto;
box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
animation: modalSlideIn 0.3s ease-out;
}

@keyframes modalSlideIn {
from {
  opacity: 0;
  transform: translateY(-20px) scale(0.95);
}
to {
  opacity: 1;
  transform: translateY(0) scale(1);
}
}

.modal-body {
padding: 2rem;
}

.modal-header {
display: flex;
justify-content: space-between;
align-items: center;
margin-bottom: 2rem;
padding-bottom: 1rem;
border-bottom: 1px solid #e5e7eb;
}

.modal-title {
font-size: 1.5rem;
font-weight: 700;
color: #1f2937;
  margin: 0;
}

.modal-close {
background: none;
border: none;
font-size: 1.5rem;
cursor: pointer;
color: #6b7280;
padding: 0.5rem;
border-radius: 0.5rem;
transition: all 0.2s;
}

.modal-close:hover {
color: #374151;
background: #f3f4f6;
}

.close-icon {
width: 1.25rem;
height: 1.25rem;
}

/* Step Indicator */
.step-indicator {
display: flex;
align-items: center;
justify-content: center;
margin-bottom: 2rem;
gap: 1rem;
}

.step {
  display: flex;
  flex-direction: column;
align-items: center;
  gap: 0.5rem;
}

.step-number {
width: 2rem;
height: 2rem;
border-radius: 50%;
  display: flex;
  align-items: center;
justify-content: center;
font-weight: 600;
font-size: 0.875rem;
transition: all 0.2s;
}

.step:not(.active):not(.completed) .step-number {
background: #f3f4f6;
color: #9ca3af;
}

.step.active .step-number {
background: #3b82f6;
color: white;
}

.step.completed .step-number {
background: #10b981;
color: white;
}

.step-label {
font-size: 0.75rem;
font-weight: 500;
color: #6b7280;
}

.step-line {
width: 2rem;
height: 2px;
background: #e5e7eb;
transition: all 0.2s;
}

.step-line.completed {
background: #10b981;
}

/* Verification Step */
.verification-step {
text-align: center;
}

.verification-content {
max-width: 400px;
margin: 0 auto;
}

.verification-icon {
width: 4rem;
height: 4rem;
background: linear-gradient(135deg, #dbeafe, #bfdbfe);
border-radius: 50%;
  display: flex;
  align-items: center;
justify-content: center;
margin: 0 auto 1.5rem;
}

.verification-icon svg {
width: 2rem;
height: 2rem;
color: #3b82f6;
}

.verification-title {
font-size: 1.25rem;
  font-weight: 600;
color: #1f2937;
margin: 0 0 0.5rem 0;
}

.verification-description {
color: #6b7280;
margin: 0 0 2rem 0;
line-height: 1.5;
}

.verification-actions {
margin-top: 1.5rem;
}

.verify-button {
display: flex;
align-items: center;
gap: 0.5rem;
padding: 0.75rem 1.5rem;
background: #3b82f6;
  color: white;
  border: none;
  border-radius: 0.75rem;
  font-weight: 600;
cursor: pointer;
  transition: all 0.2s;
margin: 0 auto;
}

.verify-button:hover:not(:disabled) {
background: #2563eb;
transform: translateY(-1px);
}

.verify-button:disabled {
opacity: 0.5;
cursor: not-allowed;
}

.verify-button .icon {
width: 1.25rem;
height: 1.25rem;
}

.verify-button .spinner {
width: 1.25rem;
height: 1.25rem;
animation: spin 1s linear infinite;
}

/* Form Step */
.form-step {
max-width: 500px;
margin: 0 auto;
}

.retrieved-info {
background: #f0fdf4;
border: 1px solid #bbf7d0;
border-radius: 0.75rem;
  padding: 1rem;
margin-bottom: 1.5rem;
}

.info-header {
display: flex;
align-items: center;
gap: 0.5rem;
}

.info-icon {
width: 1.25rem;
height: 1.25rem;
color: #10b981;
}

.info-header h4 {
margin: 0;
color: #065f46;
font-weight: 600;
font-size: 0.875rem;
}

/* Form */
.form {
display: flex;
flex-direction: column;
gap: 1.5rem;
}

.form-group {
display: flex;
flex-direction: column;
gap: 0.5rem;
}

.form-label {
font-size: 0.875rem;
font-weight: 600;
color: #374151;
}

.form-input {
padding: 0.75rem 1rem;
border: 1px solid #d1d5db;
border-radius: 0.5rem;
font-size: 1rem;
transition: all 0.2s;
background: white;
}

.form-input:focus {
outline: none;
border-color: #3b82f6;
box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-input:disabled {
background: #f9fafb;
color: #6b7280;
cursor: not-allowed;
}

.error-message {
color: #dc2626;
font-size: 0.875rem;
font-weight: 500;
margin-top: 0.25rem;
}

.form-actions {
display: flex;
gap: 1rem;
margin-top: 2rem;
}

.back-button {
display: flex;
align-items: center;
gap: 0.5rem;
padding: 0.75rem 1rem;
background: #f3f4f6;
color: #374151;
border: none;
border-radius: 0.5rem;
font-weight: 500;
cursor: pointer;
transition: all 0.2s;
}

.back-button:hover {
background: #e5e7eb;
}

.back-button .icon {
width: 1rem;
height: 1rem;
}

.cancel-button {
flex: 1;
padding: 0.75rem 1rem;
background: #f3f4f6;
color: #374151;
border: none;
border-radius: 0.5rem;
font-weight: 600;
cursor: pointer;
transition: all 0.2s;
}

.cancel-button:hover {
background: #e5e7eb;
}

.submit-button {
flex: 1;
padding: 0.75rem 1rem;
background: #3b82f6;
color: white;
border: none;
border-radius: 0.5rem;
font-weight: 600;
cursor: pointer;
transition: all 0.2s;
}

.submit-button:hover:not(:disabled) {
background: #2563eb;
transform: translateY(-1px);
}

.submit-button:disabled {
opacity: 0.5;
cursor: not-allowed;
}

/* Delete Modal */
.delete-modal {
max-width: 400px;
}

.modal-footer {
display: flex;
gap: 1rem;
margin-top: 2rem;
padding-top: 1rem;
border-top: 1px solid #e5e7eb;
}

.warning-icon {
font-size: 3rem;
text-align: center;
margin-bottom: 1rem;
}

.warning-text {
color: #dc2626;
font-weight: 500;
margin: 0.5rem 0 0 0;
}

.confirm-delete-button {
flex: 1;
padding: 0.75rem 1rem;
background: #dc2626;
color: white;
border: none;
border-radius: 0.5rem;
font-weight: 600;
cursor: pointer;
transition: all 0.2s;
}

.confirm-delete-button:hover {
background: #b91c1c;
transform: translateY(-1px);
}

/* Notifications */
.notifications-container {
  position: fixed;
  top: 1rem;
  right: 1rem;
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  max-width: 400px;
z-index: 9999;
}

.notification {
display: flex;
align-items: flex-start;
gap: 0.75rem;
padding: 1rem;
border-radius: 0.75rem;
box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
  animation: notificationSlideIn 0.3s ease-out;
cursor: pointer;
transition: all 0.2s;
background: white;
border: 1px solid #e5e7eb;
}

.notification:hover {
transform: translateY(-2px);
box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.notification-success {
border-left: 4px solid #10b981;
}

.notification-error {
border-left: 4px solid #ef4444;
}

.notification-warning {
border-left: 4px solid #f59e0b;
}

.notification-info {
border-left: 4px solid #3b82f6;
}

.notification-icon {
font-size: 1.25rem;
flex-shrink: 0;
}

.notification-content {
flex: 1;
}

.notification-message {
margin: 0 0 0.25rem 0;
color: #374151;
font-weight: 500;
font-size: 0.875rem;
}

.notification-time {
margin: 0;
color: #9ca3af;
font-size: 0.75rem;
}

.notification-close {
background: none;
border: none;
font-size: 1.25rem;
color: #9ca3af;
cursor: pointer;
padding: 0;
width: 1.5rem;
height: 1.5rem;
display: flex;
align-items: center;
justify-content: center;
border-radius: 0.25rem;
transition: all 0.2s;
}

.notification-close:hover {
color: #374151;
background: #f3f4f6;
}

@keyframes notificationSlideIn {
0% {
  opacity: 0;
  transform: translateX(100%);
}
100% {
  opacity: 1;
  transform: translateX(0);
}
}

/* Responsive */
@media (max-width: 768px) {
.header-content {
  flex-direction: column;
  gap: 1rem;
  text-align: center;
}

.header-title {
  font-size: 2rem;
}

.table-header {
  flex-direction: column;
  gap: 1rem;
}

.header-center {
  margin: 0;
  max-width: none;
}

.main-table {
  font-size: 0.8rem;
}

.main-table th,
.main-table td {
  padding: 0.75rem 1rem;
}

.form-actions {
  flex-direction: column;
}

.modal-content {
  margin: 1rem;
  max-height: calc(100vh - 2rem);
}

.notifications-container {
  left: 1rem;
  right: 1rem;
  max-width: none;
}
}
</style>