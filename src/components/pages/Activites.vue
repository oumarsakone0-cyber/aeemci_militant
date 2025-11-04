<template>
  <div class="app">

    <div class="main-container">
      <div v-if="showMobileMenu" class="mobile-overlay" @click="closeMobileMenu"></div>

      <!-- Main Content -->
      <main class="main-content">
        <div class="content-wrapper">
          <!-- Activities Section -->
          <div class="activities-section">
            <!-- Header with Add Button -->
            <div class="section-header">
              <h2 class="section-title">Activités de la Base</h2>
              <button class="add-activity-btn" @click="showAddActivityModal = true">
                <svg class="add-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <line x1="12" y1="5" x2="12" y2="19"></line>
                  <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Ajouter une activité
              </button>
            </div>

            <!-- Filters -->
            <div class="filters-container">
              <div class="search-section">
                <div class="search-container">
                  <input
                    type="text"
                    placeholder="Rechercher par nom d'activité..."
                    class="search-input"
                    v-model="searchQuery"
                    @input="searchActivities"
                  >
                  <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.35-4.35"></path>
                  </svg>
                </div>
              </div>
              <div class="filter-section">
                <select v-model="selectedType" @change="filterActivities" class="type-select">
                  <option value="">Tous les types</option>
                  <option value="conférence">Conférence</option>
                  <option value="séminaire">Séminaire</option>
                  <option value="demi journée">Demi journée</option>
                  <option value="rupture collective">Rupture collective</option>
                  <option value="causerie débat">Causerie débat</option>
                  <option value="autre">Autre</option>
                </select>
              </div>
            </div>

            <!-- Activities Grid -->
            <div class="activities-grid-container" v-if="filteredActivities.length > 0">
              <div
                v-for="activity in filteredActivities"
                :key="activity.id"
                class="activity-card"
                @click="openActivityModal(activity)"
              >
                <div class="activity-header">
                  <div class="activity-type-badge" :class="`badge-${activity.type.replace(' ', '-')}`">
                    {{ activity.type }}
                  </div>
                  <div class="activity-date">
                    {{ formatDate(activity.date) }}
                  </div>
                </div>
                <div class="activity-info">
                  <h3 class="activity-title">{{ activity.name }}</h3>
                  <div class="activity-details">
                    <div class="activity-time">
                      <svg class="detail-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12,6 12,12 16,14"></polyline>
                      </svg>
                      {{ activity.time }}
                    </div>
                    <div class="activity-organizer">
                      <svg class="detail-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                      </svg>
                      {{ activity.organizer }}
                    </div>
                    <div class="activity-contact">
                      <svg class="detail-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                      </svg>
                      {{ activity.contact }}
                    </div>
                  </div>
                  <div class="activity-actions" v-if="activity.program">
                    <button class="download-btn" @click.stop="downloadProgram(activity.program)">
                      <svg class="download-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7,10 12,15 17,10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                      </svg>
                      Programme
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Empty State -->
            <div v-else class="empty-state">
              <svg class="empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
              </svg>
              <h3 class="empty-title">Aucune activité trouvée</h3>
              <p class="empty-description">Commencez par ajouter votre première activité</p>
            </div>
          </div>
        </div>
      </main>
    </div>

    <!-- Add Activity Modal -->
    <div v-if="showAddActivityModal" class="modal-overlay" @click="closeAddActivityModal">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3 class="modal-title">Ajouter une nouvelle activité</h3>
          <button class="modal-close" @click="closeAddActivityModal">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <line x1="18" y1="6" x2="6" y2="18"></line>
              <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
          </button>
        </div>

        <form @submit.prevent="submitAddActivity" class="modal-form">
          <div class="form-group">
            <label class="form-label">Nom de l'activité *</label>
            <input
              type="text"
              v-model="addActivityForm.name"
              class="form-input"
              placeholder="Ex: Conférence sur l'Islam moderne"
              required
            >
          </div>

          <div class="form-group">
            <label class="form-label">Type d'activité *</label>
            <select v-model="addActivityForm.type" class="form-select" required>
              <option value="">Sélectionner un type</option>
              <option value="conférence">Conférence</option>
              <option value="séminaire">Séminaire</option>
              <option value="demi journée">Demi journée</option>
              <option value="rupture collective">Rupture collective</option>
              <option value="causerie débat">Causerie débat</option>
              <option value="autre">Autre</option>
            </select>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Date de l'activité *</label>
              <input
                type="date"
                v-model="addActivityForm.date"
                class="form-input"
                required
              >
            </div>
            <div class="form-group">
              <label class="form-label">Heure de l'activité *</label>
              <input
                type="time"
                v-model="addActivityForm.time"
                class="form-input"
                required
              >
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Président du comité d'organisation *</label>
            <input
              type="text"
              v-model="addActivityForm.organizer"
              class="form-input"
              placeholder="Nom du président"
              required
            >
          </div>

          <div class="form-group">
            <label class="form-label">Contact *</label>
            <input
              type="text"
              v-model="addActivityForm.contact"
              class="form-input"
              placeholder="Téléphone ou email"
              required
            >
          </div>

          <div class="form-group">
            <label class="form-label">Description</label>
            <textarea
              v-model="addActivityForm.description"
              class="form-input"
              placeholder="Description de l'activité"
            ></textarea>
          </div>

          <div class="form-group">
            <label class="form-label">Lieu</label>
            <input
              type="text"
              v-model="addActivityForm.location"
              class="form-input"
              placeholder="Lieu de l'activité"
            >
          </div>

          <div class="form-group">
            <label class="form-label">Programme (PDF)</label>
            <div class="file-upload-area">
              <input
                type="file"
                ref="programFileInput"
                @change="handleProgramUpload"
                accept=".pdf"
                class="file-input"
              >
              <div class="file-upload-content">
                <svg class="upload-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                  <polyline points="17,8 12,3 7,8"></polyline>
                  <line x1="12" y1="3" x2="12" y2="15"></line>
                </svg>
                <span v-if="!programPreview">Cliquez pour télécharger le programme (PDF)</span>
                <span v-else class="file-name">{{ programPreview.name }}</span>
              </div>
            </div>
            <div v-if="isUploadingProgram" class="upload-progress">
              <div class="progress-bar">
                <div class="progress-fill" :style="{ width: programUploadProgress + '%' }"></div>
              </div>
              <span class="progress-text">{{ programUploadProgress }}%</span>
            </div>
          </div>

          <div class="modal-actions">
            <button type="button" class="cancel-btn" @click="closeAddActivityModal">
              Annuler
            </button>
            <button type="submit" class="submit-btn" :disabled="isAddingActivity">
              <span v-if="isAddingActivity">Ajout en cours...</span>
              <span v-else>Ajouter l'activité</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Activity Detail Modal -->
    <div v-if="showModal && selectedActivity" class="modal-overlay" @click="closeModal">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3 class="modal-title">{{ selectedActivity.name }}</h3>
          <button class="modal-close" @click="closeModal">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <line x1="18" y1="6" x2="6" y2="18"></line>
              <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
          </button>
        </div>

        <div class="activity-details-modal">
          <div class="detail-row">
            <strong>Type:</strong> {{ selectedActivity.type }}
          </div>
          <div class="detail-row">
            <strong>Date:</strong> {{ formatDate(selectedActivity.date) }}
          </div>
          <div class="detail-row">
            <strong>Heure:</strong> {{ selectedActivity.time }}
          </div>
          <div class="detail-row">
            <strong>Président du comité:</strong> {{ selectedActivity.organizer }}
          </div>
          <div class="detail-row">
            <strong>Contact:</strong> {{ selectedActivity.contact }}
          </div>
          <div v-if="selectedActivity.description" class="detail-row">
            <strong>Description:</strong> {{ selectedActivity.description }}
          </div>
          <div v-if="selectedActivity.location" class="detail-row">
            <strong>Lieu:</strong> {{ selectedActivity.location }}
          </div>
          <div v-if="selectedActivity.program" class="detail-row">
            <strong>Programme:</strong>
            <button class="download-btn" @click="downloadProgram(selectedActivity.program)">
              <svg class="download-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="7,10 12,15 17,10"></polyline>
                <line x1="12" y1="15" x2="12" y2="3"></line>
              </svg>
              Télécharger le programme
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { uploadToCloudinary } from '../../utils/cloudinary.js'

// API Configuration
const API_BASE_URL = 'https://sogetrag.com/api/activites.php'

// Reactive state
const activeSection = ref('activites')
const showMobileMenu = ref(false)
const selectedType = ref('')
const showModal = ref(false)
const showAddActivityModal = ref(false)
const selectedActivity = ref(null)
const isAddingActivity = ref(false)
const searchQuery = ref('')

const programUploadProgress = ref(0)
const programPreview = ref(null)
const isUploadingProgram = ref(false)

const activities = ref([])

const addActivityForm = ref({
  name: '',
  type: '',
  date: '',
  time: '',
  organizer: '',
  contact: '',
  description: '',
  location: '',
  program: null
})

// Computed properties
const filteredActivities = computed(() => {
  let filtered = activities.value

  if (selectedType.value) {
    filtered = filtered.filter(activity => activity.type === selectedType.value)
  }

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(activity =>
      activity.name.toLowerCase().includes(query) ||
      activity.organizer.toLowerCase().includes(query) ||
      activity.type.toLowerCase().includes(query)
    )
  }

  return filtered
})

// Methods
const toggleMobileMenu = () => {
  showMobileMenu.value = !showMobileMenu.value
}

const closeMobileMenu = () => {
  showMobileMenu.value = false
}

const openActivityModal = (activity) => {
  selectedActivity.value = activity
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  selectedActivity.value = null
}

const closeAddActivityModal = () => {
  showAddActivityModal.value = false
  resetAddActivityForm()
}

const resetAddActivityForm = () => {
  addActivityForm.value = {
    name: '',
    type: '',
    date: '',
    time: '',
    organizer: '',
    contact: '',
    description: '',
    location: '',
    program: null
  }
  programPreview.value = null
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const searchActivities = async () => {
  if (!searchQuery.value.trim()) {
    await loadActivities()
    return
  }

  try {
    const response = await fetch(`${API_BASE_URL}?action=search&query=${encodeURIComponent(searchQuery.value)}`)
    const data = await response.json()
    
    if (data.success) {
      activities.value = data.data || []
    } else {
      console.error('Erreur recherche:', data.message)
      activities.value = []
    }
  } catch (error) {
    console.error('Erreur lors de la recherche:', error)
  }
}

const filterActivities = async () => {
  if (!selectedType.value) {
    await loadActivities()
    return
  }

  try {
    const response = await fetch(`${API_BASE_URL}?action=filter&type=${encodeURIComponent(selectedType.value)}`)
    const data = await response.json()
    
    if (data.success) {
      activities.value = data.data || []
    } else {
      console.error('Erreur filtrage:', data.message)
      activities.value = []
    }
  } catch (error) {
    console.error('Erreur lors du filtrage:', error)
  }
}

const loadActivities = async () => {
  try {
    const response = await fetch(`${API_BASE_URL}?action=list`)
    const data = await response.json()
    
    if (data.success) {
      activities.value = data.data || []
    } else {
      console.error('Erreur API:', data.message)
      activities.value = []
    }
  } catch (error) {
    console.error('Erreur lors du chargement des activités:', error)
    activities.value = []
  }
}

const handleProgramUpload = async (event) => {
  const file = event.target.files[0]
  if (!file) return

  if (file.type !== 'application/pdf') {
    alert('Veuillez sélectionner un fichier PDF')
    return
  }

  if (file.size > 10 * 1024 * 1024) { // 10MB limit
    alert('Le fichier est trop volumineux (max 10MB)')
    return
  }

  programPreview.value = file
  isUploadingProgram.value = true
  programUploadProgress.value = 0

  try {
    const uploadedUrl = await uploadToCloudinary(file, 'raw', (progress) => {
      programUploadProgress.value = progress
    })
    
    addActivityForm.value.program = uploadedUrl
    isUploadingProgram.value = false
  } catch (error) {
    console.error('Erreur lors de l\'upload:', error)
    alert('Erreur lors de l\'upload du programme')
    isUploadingProgram.value = false
    programPreview.value = null
  }
}

const submitAddActivity = async () => {
  if (isAddingActivity.value) return

  isAddingActivity.value = true

  try {
    const formData = new FormData()
    formData.append('action', 'add')
    formData.append('name', addActivityForm.value.name)
    formData.append('type', addActivityForm.value.type)
    formData.append('date', addActivityForm.value.date)
    formData.append('time', addActivityForm.value.time)
    formData.append('organizer', addActivityForm.value.organizer)
    formData.append('contact', addActivityForm.value.contact)
    formData.append('description', addActivityForm.value.description || '')
    formData.append('location', addActivityForm.value.location || '')
    
    if (addActivityForm.value.program) {
      formData.append('program', addActivityForm.value.program)
    }

    const response = await fetch(API_BASE_URL, {
      method: 'POST',
      body: formData
    })

    const data = await response.json()

    if (data.success) {
      alert('Activité ajoutée avec succès!')
      closeAddActivityModal()
      await loadActivities()
    } else {
      alert('Erreur lors de l\'ajout: ' + (data.message || 'Erreur inconnue'))
    }
  } catch (error) {
    console.error('Erreur lors de l\'ajout:', error)
    alert('Erreur lors de l\'ajout de l\'activité')
  } finally {
    isAddingActivity.value = false
  }
}

const downloadProgram = (programUrl) => {
  if (programUrl) {
    window.open(programUrl, '_blank')
  }
}

// Lifecycle
onMounted(() => {
  loadActivities()
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
  background: #4ade80;
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

/* Activities Section */
.activities-section {
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

.add-activity-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  background: #4ade80;
  color: #ffffff;
  border: none;
  padding: 12px 20px;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  font-size: 14px;
  transition: all 0.2s;
}

.add-activity-btn:hover {
  background: #22c55e;
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

.filter-section {
  min-width: 200px;
}

.type-select {
  width: 100%;
  padding: 12px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 14px;
  color: #1f2937;
  background: white;
}

/* Activities Grid */
.activities-grid-container {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 20px;
  padding: 24px;
}

.activity-card {
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  overflow: hidden;
  cursor: pointer;
  transition: all 0.2s;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.activity-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  border-color: #4ade80;
}

.activity-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px;
  background: #f8fafc;
  border-bottom: 1px solid #e5e7eb;
}

.activity-type-badge {
  padding: 4px 12px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 500;
  text-transform: capitalize;
}

.badge-conférence {
  background: #dbeafe;
  color: #1e40af;
}

.badge-séminaire {
  background: #fef3c7;
  color: #92400e;
}

.badge-demi-journée {
  background: #e0e7ff;
  color: #3730a3;
}

.badge-rupture-collective {
  background: #fce7f3;
  color: #be185d;
}

.badge-causerie-débat {
  background: #dcfce7;
  color: #166534;
}

.badge-autre {
  background: #f3f4f6;
  color: #374151;
}

.activity-date {
  font-size: 14px;
  color: #6b7280;
  font-weight: 500;
}

.activity-info {
  padding: 16px;
}

.activity-title {
  font-size: 18px;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 12px;
  line-height: 1.4;
}

.activity-details {
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-bottom: 16px;
}

.activity-time,
.activity-organizer,
.activity-contact {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 14px;
  color: #6b7280;
}

.detail-icon {
  width: 16px;
  height: 16px;
  flex-shrink: 0;
}

.activity-actions {
  display: flex;
  gap: 8px;
}

.download-btn {
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

.download-btn:hover {
  background: #e5e7eb;
}

.download-icon {
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

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

.form-label {
  display: block;
  font-size: 14px;
  font-weight: 500;
  color: #374151;
  margin-bottom: 6px;
}

.form-input,
.form-select {
  width: 100%;
  padding: 12px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 14px;
  color: #1f2937;
  background: white;
  transition: border-color 0.2s;
}

.form-input:focus,
.form-select:focus {
  outline: none;
  border-color: #4ade80;
  box-shadow: 0 0 0 3px rgba(74, 222, 128, 0.1);
}

/* File Upload */
.file-upload-area {
  border: 2px dashed #d1d5db;
  border-radius: 8px;
  padding: 24px;
  cursor: pointer;
  transition: all 0.2s;
}

.file-upload-area:hover {
  border-color: #4ade80;
  background: #f0fdf4;
}

.file-input {
  position: absolute;
  opacity: 0;
  pointer-events: none;
}

.file-upload-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
}

.upload-icon {
  width: 32px;
  height: 32px;
  color: #9ca3af;
}

.file-name {
  color: #4ade80;
  font-weight: 500;
}

/* Upload Progress */
.upload-progress {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-top: 12px;
}

.progress-bar {
  flex: 1;
  height: 8px;
  background: #e5e7eb;
  border-radius: 4px;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  background: #4ade80;
  transition: width 0.3s;
}

.progress-text {
  font-size: 12px;
  color: #6b7280;
  font-weight: 500;
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
  background: #4ade80;
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
  background: #22c55e;
}

.submit-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Activity Details Modal */
.activity-details-modal {
  padding: 24px;
}

.detail-row {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 0;
  border-bottom: 1px solid #f3f4f6;
}

.detail-row:last-child {
  border-bottom: none;
}

.detail-row strong {
  min-width: 120px;
  color: #374151;
  font-weight: 600;
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

  .filters-container {
    flex-direction: column;
    gap: 12px;
  }

  .activities-grid-container {
    grid-template-columns: 1fr;
    gap: 16px;
    padding: 16px;
  }

  .form-row {
    grid-template-columns: 1fr;
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

  .add-activity-btn {
    justify-content: center;
  }
}
</style>
