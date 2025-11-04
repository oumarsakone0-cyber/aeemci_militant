<template>
  <header class="navbar">
    <div class="container">
      <!-- Logo -->
      <div class="navbar-left">
        <span class="logo">E-Learn</span>
      </div>

      <!-- Menu Desktop -->
      <nav class="navbar-menu">
        <ul>
          <li><a href="#" class="active">Accueil</a></li>
          <li><a href="#">Cours</a></li>
          <li><a href="#">Formations</a></li>
          <li><a href="#">Ressources</a></li>
          <li><a href="#">Contact</a></li>
        </ul>
      </nav>

      <!-- Barre droite -->
      <div class="navbar-right">
        <!-- Recherche -->
        <div class="search-box">
          <input type="text" placeholder="Rechercher..." v-model="searchQuery" />
          <svg viewBox="0 0 24 24" class="icon">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
          </svg>
        </div>

        <!-- Profil -->
        <div class="profile">
          <img src="https://upload.wikimedia.org/wikipedia/commons/1/12/User_icon_2.svg" alt="Profil" />
          <span>Marie Dubois</span>
        </div>

        <!-- Bouton Mobile -->
        <button class="mobile-btn" @click="toggleMobileMenu">
          <svg viewBox="0 0 24 24">
            <line x1="3" y1="6" x2="21" y2="6"></line>
            <line x1="3" y1="12" x2="21" y2="12"></line>
            <line x1="3" y1="18" x2="21" y2="18"></line>
          </svg>
        </button>
      </div>
    </div>

    <!-- Menu Mobile -->
    <div class="mobile-menu" v-if="showMobileMenu">
      <ul>
        <li><a href="#">Accueil</a></li>
        <li><a href="#">Cours</a></li>
        <li><a href="#">Formations</a></li>
        <li><a href="#">Ressources</a></li>
        <li><a href="#">Contact</a></li>
      </ul>
    </div>
  </header>
</template>

<script setup>
import { ref } from "vue"

const searchQuery = ref("")
const showMobileMenu = ref(false)

const toggleMobileMenu = () => {
  showMobileMenu.value = !showMobileMenu.value
}
</script>

<style scoped>
/* NAVBAR */
.navbar {
  background: #ffffff;
  border-bottom: 1px solid #e5e7eb;
  position: sticky;
  top: 0;
  z-index: 100;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  height: 64px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 20px;
}

/* LOGO */
.logo {
  font-size: 22px;
  font-weight: 700;
  color: #2563eb;
}

/* MENU */
.navbar-menu ul {
  display: flex;
  gap: 30px;
  list-style: none;
}

.navbar-menu a {
  text-decoration: none;
  font-size: 15px;
  color: #374151;
  font-weight: 500;
  transition: color 0.2s;
}

.navbar-menu a:hover,
.navbar-menu a.active {
  color: #2563eb;
}

/* RIGHT SECTION */
.navbar-right {
  display: flex;
  align-items: center;
  gap: 20px;
}

/* SEARCH */
.search-box {
  position: relative;
}

.search-box input {
  height: 36px;
  padding: 0 35px 0 12px;
  border: 1px solid #d1d5db;
  border-radius: 18px;
  font-size: 14px;
}

.search-box .icon {
  position: absolute;
  right: 10px;
  top: 8px;
  width: 18px;
  height: 18px;
  stroke: #6b7280;
}

/* PROFILE */
.profile {
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
}

.profile img {
  width: 36px;
  height: 36px;
  border-radius: 50%;
}

/* MOBILE BTN */
.mobile-btn {
  display: none;
  background: none;
  border: none;
  cursor: pointer;
}

.mobile-btn svg {
  width: 28px;
  height: 28px;
  stroke: #374151;
}

/* MOBILE MENU */
.mobile-menu {
  display: none;
  background: #ffffff;
  border-top: 1px solid #e5e7eb;
  padding: 15px 20px;
}

.mobile-menu ul {
  list-style: none;
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.mobile-menu a {
  text-decoration: none;
  font-size: 16px;
  color: #374151;
  font-weight: 500;
}

/* RESPONSIVE */
@media (max-width: 900px) {
  .navbar-menu {
    display: none;
  }
  .search-box {
    display: none;
  }
  .mobile-btn {
    display: block;
  }
  .mobile-menu {
    display: block;
  }
}
</style>
