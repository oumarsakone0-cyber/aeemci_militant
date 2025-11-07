<template>
  <header class="navbar-header">
    <!-- Desktop Navbar -->
    <div class="navbar-desktop">
      <div class="navbar-container">
        <!-- Logo -->
        <div class="navbar-logo">
          <div class="logo-icon">A</div>
          <span class="logo-text">Espace Aeemciste</span>
        </div>

        <!-- Search Bar -->
        <div class="search-container">
          <svg class="search-icon-header" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"></circle>
            <path d="m21 21-4.35-4.35"></path>
          </svg>
          <input 
            v-model="searchQuery"
            type="text" 
            placeholder="Rechercher..." 
            class="search-input"
            @input="handleSearch"
          />
          <button class="filter-btn" @click="toggleMenu">☰</button>
          
          <!-- Menu déroulant -->
          <div v-if="showMenu" class="menu-dropdown">
            <div class="menu-item" @click="showFilters">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="3"></circle>
                <path d="M12 1v6m0 6v6M5.64 5.64l4.24 4.24m4.24 4.24l4.24 4.24M1 12h6m6 0h6M5.64 18.36l4.24-4.24m4.24-4.24l4.24-4.24"></path>
              </svg>
              Filtres
            </div>
            <div class="menu-item" @click="showNotifications">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
              </svg>
              Notifications
            </div>
            <div class="menu-item" @click="showSettings">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="3"></circle>
                <path d="M12 1v6m0 6v6M5.64 5.64l4.24 4.24m4.24 4.24l4.24 4.24M1 12h6m6 0h6M5.64 18.36l4.24-4.24m4.24-4.24l4.24-4.24"></path>
              </svg>
              Paramètres
            </div>
          </div>
        </div>

        <!-- User Section -->
        <div class="user-profile-desktop">
          <img 
            :src="user?.photo_membre || user?.photo_url || 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1200px-User_icon_2.svg.png'" 
            :alt="user?.nom || user?.full_name || 'Utilisateur'"
            class="user-avatar"
            @error="onImageError"
          />
          <button @click="handleLogout" class="logout-btn" title="Déconnexion">
            ✕
          </button>
        </div>
      </div>
    </div>

    <!-- Mobile Navbar -->
    <div class="navbar-mobile">
      <!-- Header avec gradient -->
      <div class="mobile-header">
        <div class="header-top">
          <div class="navbar-logo-mobile">
            <div class="logo-icon-mobile">A</div>
            <span class="logo-text-mobile">Espace Aeemciste</span>
          </div>
          <img 
            :src="user?.photo_membre || user?.photo_url || 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1200px-User_icon_2.svg.png'" 
            :alt="user?.nom || user?.full_name || 'Utilisateur'"
            class="user-avatar-mobile"
            @error="onImageError"
          />
        </div>

        <!-- Search Bar Mobile -->
        <div class="search-container-mobile">
          <svg class="search-icon-header-mobile" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"></circle>
            <path d="m21 21-4.35-4.35"></path>
          </svg>
          <input 
            v-model="searchQuery"
            type="text" 
            placeholder="Rechercher..." 
            class="search-input-mobile"
            @input="handleSearch"
          />
          <button class="filter-btn-mobile" @click="toggleMenu">☰</button>
          
          <!-- Menu déroulant mobile -->
          <div v-if="showMenu" class="menu-dropdown-mobile">
            <div class="menu-item" @click="showFilters">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="3"></circle>
                <path d="M12 1v6m0 6v6M5.64 5.64l4.24 4.24m4.24 4.24l4.24 4.24M1 12h6m6 0h6M5.64 18.36l4.24-4.24m4.24-4.24l4.24-4.24"></path>
              </svg>
              Filtres
            </div>
            <div class="menu-item" @click="showNotifications">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
              </svg>
              Notifications
            </div>
            <div class="menu-item" @click="showSettings">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="3"></circle>
                <path d="M12 1v6m0 6v6M5.64 5.64l4.24 4.24m4.24 4.24l4.24 4.24M1 12h6m6 0h6M5.64 18.36l4.24-4.24m4.24-4.24l4.24-4.24"></path>
              </svg>
              Paramètres
            </div>
          </div>
        </div>
      </div>
    </div>
  </header>

  <!-- Spacer pour éviter le chevauchement du contenu -->
  <div class="navbar-spacer"></div>

  <!-- Modale Filtres -->
  <div v-if="showFiltersModal" class="modal-overlay" @click="closeFiltersModal">
    <div class="modal-content" @click.stop>
      <div class="modal-header">
        <h2>Filtres</h2>
        <button class="modal-close" @click="closeFiltersModal">✕</button>
      </div>
      <div class="modal-body">
        <div class="filter-section">
          <h3>Type de contenu</h3>
          <label class="filter-option">
            <input type="checkbox" v-model="filters.images" />
            <span>Publications avec images</span>
          </label>
          <label class="filter-option">
            <input type="checkbox" v-model="filters.videos" />
            <span>Publications avec vidéos</span>
          </label>
          <label class="filter-option">
            <input type="checkbox" v-model="filters.textOnly" />
            <span>Publications texte uniquement</span>
          </label>
        </div>
        <div class="filter-section">
          <h3>Période</h3>
          <label class="filter-option">
            <input type="radio" v-model="filters.period" value="all" />
            <span>Toutes les publications</span>
          </label>
          <label class="filter-option">
            <input type="radio" v-model="filters.period" value="today" />
            <span>Aujourd'hui</span>
          </label>
          <label class="filter-option">
            <input type="radio" v-model="filters.period" value="week" />
            <span>Cette semaine</span>
          </label>
          <label class="filter-option">
            <input type="radio" v-model="filters.period" value="month" />
            <span>Ce mois</span>
          </label>
        </div>
        <div class="modal-actions">
          <button class="btn-secondary" @click="resetFilters">Réinitialiser</button>
          <button class="btn-primary" @click="applyFilters">Appliquer</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modale Notifications -->
  <div v-if="showNotificationsModal" class="modal-overlay" @click="closeNotificationsModal">
    <div class="modal-content" @click.stop>
      <div class="modal-header">
        <h2>Notifications</h2>
        <button class="modal-close" @click="closeNotificationsModal">✕</button>
      </div>
      <div class="modal-body">
        <div class="notifications-list">
          <div v-if="notifications.length === 0" class="empty-state">
            <p>Aucune notification</p>
          </div>
          <div v-for="notification in notifications" :key="notification.id" class="notification-item">
            <div class="notification-icon">
              {{ notification.icon }}
            </div>
            <div class="notification-content">
              <p class="notification-text">{{ notification.text }}</p>
              <span class="notification-time">{{ notification.time }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modale Paramètres -->
  <div v-if="showSettingsModal" class="modal-overlay" @click="closeSettingsModal">
    <div class="modal-content" @click.stop>
      <div class="modal-header">
        <h2>Paramètres</h2>
        <button class="modal-close" @click="closeSettingsModal">✕</button>
      </div>
      <div class="modal-body">
        <div class="settings-section">
          <h3>Notifications</h3>
          <label class="setting-option">
            <input type="checkbox" v-model="settings.emailNotifications" />
            <span>Notifications par email</span>
          </label>
          <label class="setting-option">
            <input type="checkbox" v-model="settings.pushNotifications" />
            <span>Notifications push</span>
          </label>
        </div>
        <div class="settings-section">
          <h3>Confidentialité</h3>
          <label class="setting-option">
            <input type="checkbox" v-model="settings.profilePublic" />
            <span>Profil public</span>
          </label>
          <label class="setting-option">
            <input type="checkbox" v-model="settings.showEmail" />
            <span>Afficher l'email</span>
          </label>
        </div>
        <div class="settings-section">
          <h3>Apparence</h3>
          <label class="setting-option">
            <span>Thème</span>
            <select v-model="settings.theme" class="setting-select">
              <option value="light">Clair</option>
              <option value="dark">Sombre</option>
              <option value="auto">Automatique</option>
            </select>
          </label>
        </div>
        <div class="modal-actions">
          <button class="btn-secondary" @click="resetSettings">Réinitialiser</button>
          <button class="btn-primary" @click="saveSettings">Enregistrer</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modale de confirmation (paramètres sauvegardés) -->
  <div v-if="showSuccessModal" class="modal-overlay" @click="closeSuccessModal">
    <div class="modal-content success-modal" @click.stop>
      <div class="modal-header">
        <h2>✅ Succès</h2>
        <button class="modal-close" @click="closeSuccessModal">✕</button>
      </div>
      <div class="modal-body">
        <p class="success-message">{{ successMessage }}</p>
        <div class="modal-actions">
          <button class="btn-primary" @click="closeSuccessModal">OK</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, onMounted } from 'vue'
import { useUserStore } from '../../stores/user'
import { useRouter } from 'vue-router'
import { getCurrentUserMatricule } from '../../utils/database.js'

const userStore = useUserStore()
const router = useRouter()

// État local pour le profil utilisateur avec photo
const userProfile = ref(null)

// User computed qui combine store et profil chargé
const user = computed(() => {
  // Si on a un profil chargé, l'utiliser en priorité
  if (userProfile.value) {
    return userProfile.value
  }
  // Sinon, utiliser le store
  return userStore.user
})

// Charger le profil utilisateur depuis l'API
const loadUserProfile = async () => {
  try {
    const matricule = getCurrentUserMatricule() || userStore.user?.matricule_gen || userStore.user?.matricule
    
    if (!matricule) {
      console.warn('Aucun matricule disponible pour charger le profil')
      return
    }

    const response = await fetch(`https://sogetrag.com/apistage/post_api.php?action=get_user_profile&matricule=${matricule}`)
    const result = await response.json()

    if (result.success && result.data) {
      // Utiliser photo_url depuis la base de données (photo_membre)
      let photoUrl = result.data.photo_url || result.data.photo_membre || null
      
      const defaultImage = 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1200px-User_icon_2.svg.png'
      
      if (!photoUrl || typeof photoUrl !== 'string') {
        photoUrl = defaultImage
      } else {
        const trimmedUrl = photoUrl.trim()
        
        // Si vide ou null
        if (trimmedUrl === '' || trimmedUrl === 'null' || trimmedUrl === 'NULL') {
          photoUrl = defaultImage
        }
        // Construire l'URL complète pour les chemins relatifs
        else if (trimmedUrl.startsWith('/uploads/') || trimmedUrl.startsWith('uploads/')) {
          const baseUrl = 'http://sogetrag.com/apistage/'
          photoUrl = baseUrl + (trimmedUrl.startsWith('/') ? trimmedUrl.substring(1) : trimmedUrl)
        }
        // Pour les URLs Cloudinary, on les laisse passer - le gestionnaire @error les remplacera si elles échouent
        // Cela évite les erreurs "Tracking Prevention" car on ne bloque plus préventivement
      }

      userProfile.value = {
        ...userStore.user, // Garder les données du store
        ...result.data,    // Surcharger avec les données de l'API
        photo_membre: photoUrl,
        photo_url: photoUrl,
        nom: result.data.full_name?.split(' ')[1] || result.data.nom || '',
        prenom: result.data.full_name?.split(' ')[0] || result.data.prenom || ''
      }
    }
  } catch (error) {
    console.error('Erreur lors du chargement du profil:', error)
  }
}

onMounted(() => {
  // Charger le profil utilisateur au montage du composant
  loadUserProfile()
})
const searchQuery = ref('')
const showMenu = ref(false)

// États des modales
const showFiltersModal = ref(false)
const showNotificationsModal = ref(false)
const showSettingsModal = ref(false)
const showSuccessModal = ref(false)
const successMessage = ref('')

// Filtres
const filters = ref({
  images: false,
  videos: false,
  textOnly: false,
  period: 'all'
})

// Paramètres
const settings = ref({
  emailNotifications: true,
  pushNotifications: true,
  profilePublic: true,
  showEmail: false,
  theme: 'light'
})

// Notifications (exemple)
const notifications = ref([
  {
    id: 1,
    icon: '👍',
    text: 'Votre post a reçu 5 nouvelles réactions',
    time: 'Il y a 2 heures'
  },
  {
    id: 2,
    icon: '💬',
    text: 'Nouveau commentaire sur votre publication',
    time: 'Il y a 5 heures'
  }
])

const onImageError = (event) => {
  // Si l'image ne peut pas être chargée (Cloudinary bloqué, 401, Tracking Prevention, etc.), utiliser l'image par défaut
  const defaultImage = 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1200px-User_icon_2.svg.png'
  if (event.target && event.target.src !== defaultImage) {
    // Empêcher les tentatives répétées
    event.target.onerror = null
    event.target.src = defaultImage
    // Mettre à jour aussi le profil pour éviter de réessayer
    if (userProfile.value) {
      userProfile.value.photo_membre = defaultImage
      userProfile.value.photo_url = defaultImage
    }
  }
}

const handleLogout = async () => {
  const confirmed = confirm('Voulez-vous vraiment vous déconnecter ?')
  if (!confirmed) return

  userStore.logout()
  localStorage.removeItem('user')
  localStorage.removeItem('authToken')
  
  router.push('/login')
}

// Fonction de recherche
const handleSearch = () => {
  // Émettre un événement pour que Posts.vue puisse écouter
  window.dispatchEvent(new CustomEvent('search-posts', { detail: searchQuery.value }))
}

// Toggle menu hamburger
const toggleMenu = () => {
  showMenu.value = !showMenu.value
}

// Fonctions du menu
const showFilters = () => {
  showMenu.value = false
  showFiltersModal.value = true
}

const showNotifications = () => {
  showMenu.value = false
  showNotificationsModal.value = true
}

const showSettings = () => {
  showMenu.value = false
  showSettingsModal.value = true
}

// Fonctions pour fermer les modales
const closeFiltersModal = () => {
  showFiltersModal.value = false
}

const closeNotificationsModal = () => {
  showNotificationsModal.value = false
}

const closeSettingsModal = () => {
  showSettingsModal.value = false
}

// Fonctions des filtres
const resetFilters = () => {
  filters.value = {
    images: false,
    videos: false,
    textOnly: false,
    period: 'all'
  }
}

const applyFilters = () => {
  // Émettre un événement pour que Posts.vue puisse appliquer les filtres
  window.dispatchEvent(new CustomEvent('apply-filters', { detail: filters.value }))
  closeFiltersModal()
}

// Fonctions des paramètres
const resetSettings = () => {
  settings.value = {
    emailNotifications: true,
    pushNotifications: true,
    profilePublic: true,
    showEmail: false,
    theme: 'light'
  }
}

const saveSettings = () => {
  // Sauvegarder les paramètres (localStorage ou API)
  localStorage.setItem('userSettings', JSON.stringify(settings.value))
  // Émettre un événement pour informer que les paramètres sont sauvegardés
  window.dispatchEvent(new CustomEvent('settings-saved', { detail: settings.value }))
  closeSettingsModal()
  // Afficher la modale de succès
  successMessage.value = 'Paramètres enregistrés avec succès'
  showSuccessModal.value = true
}

const closeSuccessModal = () => {
  showSuccessModal.value = false
  successMessage.value = ''
}

// Fermer le menu si on clique ailleurs
document.addEventListener('click', (e) => {
  if (!e.target.closest('.search-container') && !e.target.closest('.search-container-mobile')) {
    showMenu.value = false
  }
})
</script>

<style scoped>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

/* ========== DESKTOP NAVBAR ========== */
.navbar-header {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  z-index: 1000;
}

.navbar-desktop {
  display: none;
  background: linear-gradient(135deg, rgba(16, 185, 129, 0.05) 0%, rgba(5, 150, 105, 0.05) 100%);
  backdrop-filter: blur(10px);
  border-bottom: 1px solid rgba(16, 185, 129, 0.1);
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
}

@media (min-width: 768px) {
  .navbar-desktop {
    display: flex;
  }
}

.navbar-container {
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 100%;
  padding: 1rem 2rem;
  gap: 2rem;
}

.navbar-logo {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  flex-shrink: 0;
}

.logo-icon {
  width: 40px;
  height: 40px;
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 18px;
  box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3);
  transition: all 0.3s ease;
}

.logo-icon:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 8px -1px rgba(16, 185, 129, 0.4);
}

.logo-text {
  font-weight: 700;
  font-size: 16px;
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

/* Search Bar Desktop */
.search-container {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  flex: 1;
  max-width: 400px;
  position: relative;
}

.search-icon-header {
  position: absolute;
  left: 12px;
  width: 18px;
  height: 18px;
  color: #65676b;
  pointer-events: none;
  z-index: 1;
}

.search-input {
  width: 100%;
  padding: 0.75rem 1rem 0.75rem 40px;
  border: 1px solid rgba(16, 185, 129, 0.2);
  border-radius: 10px;
  font-size: 14px;
  background: white;
  transition: all 0.3s ease;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.search-input:focus {
  outline: none;
  border-color: #10b981;
  background: white;
  box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.filter-btn {
  width: 40px;
  height: 40px;
  border: 1px solid rgba(16, 185, 129, 0.2);
  background: white;
  border-radius: 10px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
  transition: all 0.3s ease;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
  position: relative;
}

.filter-btn:hover {
  background: rgba(16, 185, 129, 0.05);
  border-color: #10b981;
  transform: translateY(-2px);
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

/* Menu déroulant */
.menu-dropdown {
  position: absolute;
  top: calc(100% + 8px);
  right: 0;
  background: white;
  border-radius: 10px;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
  min-width: 200px;
  z-index: 1000;
  overflow: hidden;
  padding: 8px 0;
}

.menu-item {
  padding: 12px 16px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 12px;
  font-size: 14px;
  color: #050505;
  transition: background-color 0.2s;
}

.menu-item:hover {
  background: #f0f2f5;
}

.menu-item svg {
  width: 18px;
  height: 18px;
  color: #65676b;
}

/* User Section Desktop */
.user-profile-desktop {
  display: flex;
  align-items: center;
  gap: 1rem;
  flex-shrink: 0;
}

.user-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid #10b981;
  box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2);
  transition: all 0.3s ease;
}

.user-avatar:hover {
  transform: scale(1.05);
  box-shadow: 0 4px 6px rgba(16, 185, 129, 0.3);
}

.logout-btn {
  width: 40px;
  height: 40px;
  border: 1px solid rgba(16, 185, 129, 0.2);
  background: white;
  border-radius: 10px;
  cursor: pointer;
  font-size: 18px;
  transition: all 0.3s ease;
  color: #059669;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.logout-btn:hover {
  background: rgba(239, 68, 68, 0.1);
  border-color: #ef4444;
  color: #ef4444;
  transform: translateY(-2px);
  box-shadow: 0 4px 6px rgba(239, 68, 68, 0.2);
}

/* ========== MOBILE NAVBAR ========== */
.navbar-mobile {
  display: flex;
  background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(5, 150, 105, 0.1) 100%);
  backdrop-filter: blur(10px);
  border-bottom: 1px solid rgba(16, 185, 129, 0.2);
  padding: 1rem;
  gap: 0.75rem;
  flex-direction: column;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
}

@media (min-width: 768px) {
  .navbar-mobile {
    display: none;
  }
}

.mobile-header {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.header-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.navbar-logo-mobile {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.logo-icon-mobile {
  width: 36px;
  height: 36px;
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 16px;
  flex-shrink: 0;
  box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3);
}

.logo-text-mobile {
  font-weight: 700;
  font-size: 15px;
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.user-avatar-mobile {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid #10b981;
  flex-shrink: 0;
  box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2);
}

/* Search Bar Mobile */
.search-container-mobile {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  position: relative;
}

.search-icon-header-mobile {
  position: absolute;
  left: 12px;
  width: 18px;
  height: 18px;
  color: #65676b;
  pointer-events: none;
  z-index: 1;
}

.search-input-mobile {
  flex: 1;
  padding: 0.75rem 1rem 0.75rem 40px;
  border: 1px solid rgba(16, 185, 129, 0.2);
  border-radius: 10px;
  font-size: 14px;
  background: white;
  transition: all 0.3s ease;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.search-input-mobile:focus {
  outline: none;
  border-color: #10b981;
  box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.filter-btn-mobile {
  width: 40px;
  height: 40px;
  border: 1px solid rgba(16, 185, 129, 0.2);
  background: white;
  border-radius: 10px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  transition: all 0.3s ease;
  flex-shrink: 0;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
  color: #059669;
}

.filter-btn-mobile:active {
  background: rgba(16, 185, 129, 0.1);
  transform: scale(0.95);
}

/* Menu déroulant mobile */
.menu-dropdown-mobile {
  position: absolute;
  top: calc(100% + 8px);
  right: 0;
  background: white;
  border-radius: 10px;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
  min-width: 200px;
  z-index: 1000;
  overflow: hidden;
  padding: 8px 0;
  width: 100%;
}

/* Spacers */
.navbar-spacer {
  height: 140px;
  width: 100%;
}

@media (min-width: 768px) {
  .navbar-spacer {
    height: 80px;
  }
}

/* ========== MODALES ========== */
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
  z-index: 10000;
  padding: 1rem;
}

.modal-content {
  background: white;
  border-radius: 12px;
  width: 100%;
  max-width: 500px;
  max-height: 90vh;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1.5rem;
  border-bottom: 1px solid #e4e6ea;
}

.modal-header h2 {
  font-size: 20px;
  font-weight: 600;
  color: #050505;
  margin: 0;
}

.modal-close {
  background: none;
  border: none;
  font-size: 24px;
  color: #65676b;
  cursor: pointer;
  padding: 0;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  transition: background-color 0.2s;
}

.modal-close:hover {
  background: #f0f2f5;
}

.modal-body {
  padding: 1.5rem;
  overflow-y: auto;
  flex: 1;
}

/* Filtres */
.filter-section {
  margin-bottom: 2rem;
}

.filter-section h3 {
  font-size: 16px;
  font-weight: 600;
  color: #050505;
  margin-bottom: 1rem;
}

.filter-option {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem 0;
  cursor: pointer;
  font-size: 14px;
  color: #050505;
}

.filter-option input[type="checkbox"],
.filter-option input[type="radio"] {
  width: 18px;
  height: 18px;
  cursor: pointer;
}

/* Notifications */
.notifications-list {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.empty-state {
  text-align: center;
  padding: 2rem;
  color: #65676b;
}

.notification-item {
  display: flex;
  align-items: flex-start;
  gap: 1rem;
  padding: 1rem;
  border-radius: 8px;
  background: #f0f2f5;
  transition: background-color 0.2s;
}

.notification-item:hover {
  background: #e4e6ea;
}

.notification-icon {
  font-size: 24px;
  flex-shrink: 0;
}

.notification-content {
  flex: 1;
}

.notification-text {
  font-size: 14px;
  color: #050505;
  margin: 0 0 0.25rem 0;
}

.notification-time {
  font-size: 12px;
  color: #65676b;
}

/* Paramètres */
.settings-section {
  margin-bottom: 2rem;
}

.settings-section h3 {
  font-size: 16px;
  font-weight: 600;
  color: #050505;
  margin-bottom: 1rem;
}

.setting-option {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.75rem;
  padding: 0.75rem 0;
  font-size: 14px;
  color: #050505;
}

.setting-option input[type="checkbox"] {
  width: 18px;
  height: 18px;
  cursor: pointer;
}

.setting-select {
  padding: 0.5rem 0.75rem;
  border: 1px solid #e4e6ea;
  border-radius: 8px;
  font-size: 14px;
  background: white;
  cursor: pointer;
}

/* Actions des modales */
.modal-actions {
  display: flex;
  gap: 0.75rem;
  margin-top: 2rem;
  padding-top: 1.5rem;
  border-top: 1px solid #e4e6ea;
}

.btn-primary,
.btn-secondary {
  flex: 1;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  border: none;
}

.btn-primary {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 6px rgba(16, 185, 129, 0.3);
}

.btn-secondary {
  background: #f0f2f5;
  color: #050505;
}

.btn-secondary:hover {
  background: #e4e6ea;
}

/* Modale de succès */
.success-modal {
  max-width: 400px;
}

.success-message {
  text-align: center;
  font-size: 16px;
  color: #050505;
  padding: 1rem 0;
  margin: 0;
}
</style>
