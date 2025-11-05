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
              <h1 class="text-2xl font-bold text-gray-900">Bureaux SR</h1>
              <p class="text-sm text-gray-600">Gérez les bureaux des secrétariats régionaux</p>
            </div>
          </div>
          <button
            @click="openAddModal"
            class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors flex items-center space-x-2"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            <span>Ajouter un bureau</span>
          </button>
        </div>
      </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Loading State -->
      <div v-if="isLoading" class="flex flex-col items-center justify-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-emerald-600"></div>
        <p class="mt-4 text-gray-600">Chargement des bureaux...</p>
      </div>

      <!-- Bureaux Grid -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="bureau in bureaux"
          :key="bureau.id"
          class="bg-white rounded-xl shadow-sm border border-emerald-100 overflow-hidden hover:shadow-lg hover:border-emerald-200 transition-all duration-300"
        >
          <div class="p-6">
            <div class="flex items-start justify-between mb-4">
              <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                  <span class="text-emerald-600 font-bold text-sm">B</span>
                </div>
                <div>
                  <h3 class="font-semibold text-gray-900">{{ bureau.nom_bureau || 'Bureau SR' }}</h3>
                  <p class="text-sm text-gray-500">Poste: {{ bureau.poste || 'N/A' }}</p>
                </div>
              </div>
              <div class="flex space-x-2">
                <button
                  @click="editBureau(bureau)"
                  class="p-2 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors"
                  title="Modifier"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                  </svg>
                </button>
                <button
                  @click="confirmDelete(bureau.id)"
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
                <span class="text-sm text-gray-600">Matricule: {{ bureau.matricule_membre }}</span>
              </div>
              
              <div class="flex items-center space-x-3" v-if="bureau.nom_president">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span class="text-sm text-gray-600">Président: {{ bureau.nom_president }}</span>
              </div>
              <div class="flex items-center space-x-3" v-if="bureau.telephone">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                <span class="text-sm text-gray-600">{{ bureau.telephone }}</span>
              </div>
            </div>
            
            <div class="mt-4 pt-4 border-t border-gray-100">
              <p class="text-xs text-gray-500">Créé le {{ formatDate(bureau.created_at) }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="!isLoading && bureaux.length === 0" class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun bureau trouvé</h3>
        <p class="mt-1 text-sm text-gray-500">Commencez par ajouter votre premier bureau.</p>
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
        style="max-width: 600px; max-height: 90vh; overflow-y: auto;"
      >
        <div class="modal-body">
          <div class="modal-header">
            <h2 class="modal-title">
              {{ showEditModal ? 'Modifier le bureau' : 'Ajouter un bureau' }}
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

          <form @submit.prevent="submitForm" class="form">
            <!-- Matricule du membre (automatique) -->
            <div class="form-group">
              <label class="form-label">Matricule du membre *</label>
              <input
                v-model="form.matricule_membre"
                type="text"
                required
                class="form-input"
                :disabled="true"
                placeholder="Matricule du membre connecté"
              />
              <p class="text-xs text-gray-500 mt-1">Ce champ est automatiquement rempli avec votre matricule</p>
            </div>

            <!-- Nom du bureau -->
            <div class="form-group">
              <label class="form-label">Nom du bureau *</label>
              <input
                v-model="form.nom_bureau"
                type="text"
                required
                class="form-input"
                placeholder="Ex: Bureau de Coordination"
              />
            </div>

            <!-- Poste -->
            <div class="form-group">
              <label class="form-label">Poste *</label>
              <select
                v-model="form.poste"
                required
                class="form-input"
              >
                <option value="">Sélectionner un poste</option>
                <option value="Secrétaire Régional">Secrétaire Régional</option>
                <option value="Vice-Secrétaire Régional">Vice-Secrétaire Régional</option>
                <option value="Trésorier Régional">Trésorier Régional</option>
                <option value="Secrétaire à l'Organisation">Secrétaire à l'Organisation</option>
                <option value="Secrétaire aux Relations Extérieures">Secrétaire aux Relations Extérieures</option>
                <option value="Secrétaire à la Communication">Secrétaire à la Communication</option>
                <option value="Secrétaire à l'Éducation">Secrétaire à l'Éducation</option>
                <option value="Secrétaire aux Activités">Secrétaire aux Activités</option>
                <option value="Secrétaire à la Culture">Secrétaire à la Culture</option>
                <option value="Secrétaire aux Sports">Secrétaire aux Sports</option>
                <option value="Membre du Bureau">Membre du Bureau</option>
              </select>
            </div>

            <!-- Nom du président -->
            <div class="form-group">
              <label class="form-label">Nom du président *</label>
              <input
                v-model="form.nom_president"
                type="text"
                required
                class="form-input"
                placeholder="Ex: Mohamed Traoré"
              />
            </div>

            <!-- Nom du vice-président -->
            <div class="form-group">
              <label class="form-label">Nom du vice-président</label>
              <input
                v-model="form.nom_vice_president"
                type="text"
                class="form-input"
                placeholder="Ex: Fatou Diallo"
              />
            </div>

            <!-- Nom du trésorier -->
            <div class="form-group">
              <label class="form-label">Nom du trésorier</label>
              <input
                v-model="form.nom_tresorier"
                type="text"
                class="form-input"
                placeholder="Ex: Amadou Coulibaly"
              />
            </div>

            <!-- Nom du secrétaire -->
            <div class="form-group">
              <label class="form-label">Nom du secrétaire</label>
              <input
                v-model="form.nom_secretaire"
                type="text"
                class="form-input"
                placeholder="Ex: Aissatou Ba"
              />
            </div>

            <!-- Email -->
            <div class="form-group">
              <label class="form-label">Email</label>
              <input
                v-model="form.email"
                type="email"
                class="form-input"
                placeholder="Ex: bureau@aeemci.org"
              />
            </div>

            <!-- Téléphone -->
            <div class="form-group">
              <label class="form-label">Téléphone</label>
              <input
                v-model="form.telephone"
                type="tel"
                class="form-input"
                placeholder="Ex: +225 07 12 34 56 78"
              />
            </div>

            <!-- Adresse -->
            <div class="form-group">
              <label class="form-label">Adresse du bureau</label>
              <textarea
                v-model="form.adresse"
                class="form-input"
                rows="3"
                placeholder="Ex: Rue de la République, Abidjan, Côte d'Ivoire"
              ></textarea>
            </div>

            <!-- Description -->
            <div class="form-group">
              <label class="form-label">Description du bureau</label>
              <textarea
                v-model="form.description"
                class="form-input"
                rows="4"
                placeholder="Description du bureau et de ses activités..."
              ></textarea>
            </div>

            <div class="form-actions">
              <button
                type="button"
                @click="closeModal"
                class="btn-secondary"
              >
                Annuler
              </button>
              <button
                type="submit"
                :disabled="isSubmitting"
                class="btn-primary"
              >
                <svg v-if="isSubmitting" class="spinner" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                {{ isSubmitting ? 'Enregistrement...' : (showEditModal ? 'Modifier' : 'Ajouter') }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div
      v-if="showDeleteModal"
      class="modal-overlay"
      @click="closeDeleteModal"
    >
      <div class="modal-content" @click.stop style="max-width: 400px;">
        <div class="modal-body">
          <div class="modal-header">
            <h2 class="modal-title">Confirmer la suppression</h2>
            <button @click="closeDeleteModal" class="modal-close">
              <svg class="close-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
          <p class="text-gray-600 mb-6">Êtes-vous sûr de vouloir supprimer ce bureau ? Cette action est irréversible.</p>
          <div class="form-actions">
            <button @click="closeDeleteModal" class="btn-secondary">Annuler</button>
            <button @click="deleteBureau" class="btn-danger">Supprimer</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { getCurrentUserMatricule } from '../../utils/database.js'

// API spécifique pour les bureaux SR
const BUREAUX_API_URL = 'https://sogetrag.com/apistage/bureaux_sr_api.php'

const isLoading = ref(false)
const isSubmitting = ref(false)
const bureaux = ref([])

// Modal states
const showAddModal = ref(false)
const showEditModal = ref(false)
const showDeleteModal = ref(false)
const bureauToDelete = ref(null)

// Form data
const form = ref({
  matricule_membre: '',
  nom_bureau: '',
  poste: '',
  nom_president: '',
  nom_vice_president: '',
  nom_tresorier: '',
  nom_secretaire: '',
  email: '',
  telephone: '',
  adresse: '',
  description: ''
})

// Initialize form with current user matricule
const initializeForm = () => {
  const matricule = getCurrentUserMatricule()
  form.value = {
    matricule_membre: matricule || '',
    nom_bureau: '',
    poste: '',
    nom_president: '',
    nom_vice_president: '',
    nom_tresorier: '',
    nom_secretaire: '',
    email: '',
    telephone: '',
    adresse: '',
    description: ''
  }
}


// Load bureaux
const loadBureaux = async () => {
  isLoading.value = true
  try {
    const matricule = getCurrentUserMatricule()
    if (!matricule) {
      console.error('Matricule non trouvé')
      bureaux.value = []
      return
    }
    
    const response = await fetch(`${BUREAUX_API_URL}?action=get_bureaux&matricule=${matricule}`)
    const result = await response.json()
    
    if (result.success && result.data && Array.isArray(result.data)) {
      bureaux.value = result.data
    } else if (result.success && Array.isArray(result)) {
      bureaux.value = result
    } else {
      bureaux.value = []
    }
  } catch (error) {
    console.error('Erreur lors du chargement des bureaux:', error)
    bureaux.value = []
  } finally {
    isLoading.value = false
  }
}

// Open add modal
const openAddModal = () => {
  initializeForm()
  showAddModal.value = true
}

// Edit bureau
const editBureau = (bureau) => {
  form.value = {
    matricule_membre: bureau.matricule_membre,
    nom_bureau: bureau.nom_bureau || '',
    poste: bureau.poste || '',
    nom_president: bureau.nom_president || '',
    nom_vice_president: bureau.nom_vice_president || '',
    nom_tresorier: bureau.nom_tresorier || '',
    nom_secretaire: bureau.nom_secretaire || '',
    email: bureau.email || '',
    telephone: bureau.telephone || '',
    adresse: bureau.adresse || '',
    description: bureau.description || ''
  }
  
  showEditModal.value = true
  bureauToDelete.value = bureau
}

// Close modal
const closeModal = () => {
  showAddModal.value = false
  showEditModal.value = false
  initializeForm()
}

// Submit form
const submitForm = async () => {
  if (!form.value.matricule_membre || !form.value.nom_bureau || !form.value.poste || !form.value.nom_president) {
    alert('Veuillez remplir tous les champs obligatoires (nom du bureau, poste et nom du président)')
    return
  }

  isSubmitting.value = true
  try {
    const data = {
      action: showEditModal.value ? 'update_bureau' : 'create_bureau',
      matricule_membre: form.value.matricule_membre,
      nom_bureau: form.value.nom_bureau,
      poste: form.value.poste,
      nom_president: form.value.nom_president,
      nom_vice_president: form.value.nom_vice_president || null,
      nom_tresorier: form.value.nom_tresorier || null,
      nom_secretaire: form.value.nom_secretaire || null,
      email: form.value.email || null,
      telephone: form.value.telephone || null,
      adresse: form.value.adresse || null,
      description: form.value.description || null,
      ...(showEditModal.value && bureauToDelete.value ? { id: bureauToDelete.value.id } : {})
    }

    const response = await fetch(BUREAUX_API_URL, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(data)
    })

    const result = await response.json()

    if (result.success) {
      alert(showEditModal.value ? 'Bureau modifié avec succès' : 'Bureau ajouté avec succès')
      closeModal()
      await loadBureaux()
    } else {
      alert(result.error || 'Erreur lors de l\'enregistrement')
    }
  } catch (error) {
    console.error('Erreur:', error)
    alert('Erreur lors de l\'enregistrement du bureau')
  } finally {
    isSubmitting.value = false
  }
}

// Delete bureau
const confirmDelete = (id) => {
  bureauToDelete.value = bureaux.value.find(b => b.id === id)
  showDeleteModal.value = true
}

const closeDeleteModal = () => {
  showDeleteModal.value = false
  bureauToDelete.value = null
}

const deleteBureau = async () => {
  if (!bureauToDelete.value) return

  try {
    const response = await fetch(BUREAUX_API_URL, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        action: 'delete_bureau',
        id: bureauToDelete.value.id
      })
    })

    const result = await response.json()

    if (result.success) {
      alert('Bureau supprimé avec succès')
      closeDeleteModal()
      await loadBureaux()
    } else {
      alert(result.error || 'Erreur lors de la suppression')
    }
  } catch (error) {
    console.error('Erreur:', error)
    alert('Erreur lors de la suppression du bureau')
  }
}

// Format date
const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  return date.toLocaleDateString('fr-FR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  })
}

onMounted(() => {
  loadBureaux()
})
</script>

<style scoped>
/* Styles similaires à MesSr.vue */
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
}

.modal-content {
  background: white;
  border-radius: 12px;
  width: 100%;
  max-width: 500px;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1.5rem;
  border-bottom: 1px solid #e4e6ea;
}

.modal-title {
  font-size: 20px;
  font-weight: 600;
  color: #050505;
  margin: 0;
}

.modal-close {
  background: none;
  border: none;
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 50%;
  transition: background-color 0.2s;
}

.modal-close:hover {
  background: #f0f2f5;
}

.close-icon {
  width: 24px;
  height: 24px;
  color: #65676b;
}

.modal-body {
  padding: 1.5rem;
}

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
  font-size: 14px;
  font-weight: 600;
  color: #050505;
}

.form-input {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 1px solid #e4e6ea;
  border-radius: 8px;
  font-size: 14px;
  transition: all 0.2s;
}

.form-input:focus {
  outline: none;
  border-color: #10b981;
  box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.form-input:disabled {
  background: #f0f2f5;
  cursor: not-allowed;
}

.form-actions {
  display: flex;
  gap: 0.75rem;
  margin-top: 1rem;
}

.btn-primary,
.btn-secondary,
.btn-danger {
  flex: 1;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  border: none;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.btn-primary {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
}

.btn-primary:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 4px 6px rgba(16, 185, 129, 0.3);
}

.btn-primary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-secondary {
  background: #f0f2f5;
  color: #050505;
}

.btn-secondary:hover {
  background: #e4e6ea;
}

.btn-danger {
  background: #ef4444;
  color: white;
}

.btn-danger:hover {
  background: #dc2626;
}

.spinner {
  width: 16px;
  height: 16px;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>

