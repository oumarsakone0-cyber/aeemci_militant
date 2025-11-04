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
          <input 
            type="text" 
            placeholder="Rechercher..." 
            class="search-input"
          />
          <button class="filter-btn">⚙</button>
        </div>

        <!-- User Section -->
        <div class="user-profile-desktop">
          <img 
            :src="user?.photo_membre" 
            :alt="user?.nom"
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
            :src="user?.photo_membre" 
            :alt="user?.nom"
            class="user-avatar-mobile"
            @error="onImageError"
          />
        </div>

        <!-- Search Bar Mobile -->
        <div class="search-container-mobile">
          <input 
            type="text" 
            placeholder="Rechercher..." 
            class="search-input-mobile"
          />
          <button class="filter-btn-mobile">☰</button>
        </div>
      </div>
    </div>
  </header>

  <!-- Spacer pour éviter le chevauchement du contenu -->
  <div class="navbar-spacer"></div>
</template>

<script setup>
import { computed } from 'vue'
import { useUserStore } from '../../stores/user'
import { useRouter } from 'vue-router'

const userStore = useUserStore()
const router = useRouter()

const user = computed(() => userStore.user)

const onImageError = (event) => {
  event.target.src = 'https://upload.wikimedia.org/wikipedia/fr/4/42/Logo_AEEMCI.jpeg'
}

const handleLogout = async () => {
  const confirmed = confirm('Voulez-vous vraiment vous déconnecter ?')
  if (!confirmed) return

  userStore.logout()
  localStorage.removeItem('user')
  localStorage.removeItem('authToken')
  
  router.push('/login')
}
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
}

.search-input {
  width: 100%;
  padding: 0.75rem 1rem;
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
}

.filter-btn:hover {
  background: rgba(16, 185, 129, 0.05);
  border-color: #10b981;
  transform: translateY(-2px);
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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
}

.search-input-mobile {
  flex: 1;
  padding: 0.75rem 1rem;
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
</style>
