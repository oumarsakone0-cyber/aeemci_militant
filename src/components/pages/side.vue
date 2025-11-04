<template>
  <div class="app">
    <!-- Header -->
    <div class="main-container">
      <!-- Ajout de l'overlay mobile et classes conditionnelles pour le sidebar -->
      <div v-if="showMobileMenu" class="mobile-overlay" @click="closeMobileMenu"></div>
      
      <!-- Sidebar -->
      <aside class="sidebar" :class="{ 'mobile-open': showMobileMenu }">
        <!-- Ajout du bouton fermer pour mobile -->
        <button class="mobile-close-btn" @click="closeMobileMenu">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
        
        <nav class="sidebar-nav">
          <div class="nav-section">
            <div class="profile-card">
              <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1200px-User_icon_2.svg.png" alt="Profile" class="profile-card-avatar">
              <div class="profile-info">
                <h3 class="profile-card-name">Marie Dubois</h3>
                <p class="profile-card-status">En ligne</p>
              </div>
            </div>
          </div>
          
          <div class="nav-section">
            <h4 class="nav-title">Menu Principal</h4>
            <ul class="nav-list">
              <li class="nav-item active" @click="activeSection = 'publications'">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path d="M3 3h18v18H3zM9 9h6v6H9z"></path>
                </svg>
                Publications
              </li>
              <li class="nav-item" @click="activeSection = 'bibliotheque'">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                  <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                </svg>
                Bibliothèque
              </li>
              <li class="nav-item" @click="activeSection = 'activites'">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path d="M3 3v5h5M3 8l4-4 4 4 8-8"></path>
                </svg>
                Rapport d'activités
              </li>
              <li class="nav-item" @click="activeSection = 'amis'">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                  <circle cx="9" cy="7" r="4"></circle>
                  <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                  <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                Amis
              </li>
              <li class="nav-item" @click="activeSection = 'groupes'">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                  <circle cx="9" cy="7" r="4"></circle>
                  <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                  <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                Groupes
              </li>
              <li class="nav-item" @click="activeSection = 'evenements'">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                  <line x1="16" y1="2" x2="16" y2="6"></line>
                  <line x1="8" y1="2" x2="8" y2="6"></line>
                  <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                Événements
              </li>
            </ul>
          </div>
        </nav>
      </aside>

     
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'

const searchQuery = ref('')
const activeSection = ref('publications')
const newPostText = ref('')
// Ajout de l'état pour le menu mobile
const showMobileMenu = ref(false)

const posts = reactive([
  {
    id: 1,
    author: {
      name: 'Jean Martin',
      avatar: 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1200px-User_icon_2.svg.png'
    },
    content: 'Belle journée pour une promenade dans le parc ! 🌳',
    image: '/placeholder.svg?height=300&width=500',
    timestamp: 'Il y a 2 heures',
    likes: 24,
    isLiked: false,
    showComments: false,
    showAllComments: false, // Ajout pour gérer l'affichage de tous les commentaires
    newComment: '',
    comments: [
      {
        id: 1,
        author: {
          name: 'Sophie Durand',
          avatar: 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1200px-User_icon_2.svg.png'
        },
        text: 'Magnifique photo ! Quel endroit ?',
        timestamp: 'Il y a 1 heure'
      },
      // Ajout de plus de commentaires pour tester la fonctionnalité
      {
        id: 2,
        author: {
          name: 'Pierre Moreau',
          avatar: 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1200px-User_icon_2.svg.png'
        },
        text: 'J\'adore cet endroit aussi !',
        timestamp: 'Il y a 45 minutes'
      },
      {
        id: 3,
        author: {
          name: 'Julie Blanc',
          avatar: 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1200px-User_icon_2.svg.png'
        },
        text: 'Parfait pour une sortie en famille',
        timestamp: 'Il y a 30 minutes'
      },
      {
        id: 4,
        author: {
          name: 'Antoine Roux',
          avatar: 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1200px-User_icon_2.svg.png'
        },
        text: 'Les couleurs sont magnifiques !',
        timestamp: 'Il y a 20 minutes'
      },
      {
        id: 5,
        author: {
          name: 'Camille Noir',
          avatar: 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1200px-User_icon_2.svg.png'
        },
        text: 'Merci pour le partage',
        timestamp: 'Il y a 15 minutes'
      },
      {
        id: 6,
        author: {
          name: 'Lucas Vert',
          avatar: 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1200px-User_icon_2.svg.png'
        },
        text: 'Superbe cliché !',
        timestamp: 'Il y a 10 minutes'
      },
      {
        id: 7,
        author: {
          name: 'Emma Rose',
          avatar: 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1200px-User_icon_2.svg.png'
        },
        text: 'On devrait y aller ensemble la prochaine fois',
        timestamp: 'Il y a 5 minutes'
      }
    ]
  },
  {
    id: 2,
    author: {
      name: 'Claire Rousseau',
      avatar: '/placeholder.svg?height=40&width=40'
    },
    content: 'Nouveau projet terminé ! Fière du résultat 💪',
    image: '/placeholder.svg?height=300&width=500',
    timestamp: 'Il y a 4 heures',
    likes: 18,
    isLiked: true,
    showComments: false,
    showAllComments: false,
    newComment: '',
    comments: [
      {
        id: 1,
        author: {
          name: 'Marc Leblanc',
          avatar: 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1200px-User_icon_2.svg.png'
        },
        text: 'Félicitations ! Excellent travail',
        timestamp: 'Il y a 3 heures'
      },
      {
        id: 2,
        author: {
          name: 'Emma Petit',
          avatar: 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1200px-User_icon_2.svg.png'
        },
        text: 'Bravo Claire ! 👏',
        timestamp: 'Il y a 2 heures'
      }
    ]
  },
  {
    id: 3,
    author: {
      name: 'Thomas Bernard',
      avatar: '/placeholder.svg?height=40&width=40'
    },
    content: 'Soirée cuisine avec les amis ! Qui veut la recette ? 👨‍🍳',
    image: 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1200px-User_icon_2.svg.png',
    timestamp: 'Il y a 6 heures',
    likes: 31,
    isLiked: false,
    showComments: false,
    showAllComments: false,
    newComment: '',
    comments: []
  }
])

// Nouvelles fonctions pour le menu mobile
const toggleMobileMenu = () => {
  showMobileMenu.value = !showMobileMenu.value
}

const closeMobileMenu = () => {
  showMobileMenu.value = false
}

// Fonction pour gérer l'affichage des commentaires
const getVisibleComments = (post) => {
  if (post.showAllComments || post.comments.length <= 5) {
    return post.comments
  }
  return post.comments.slice(0, 5)
}

const showMoreComments = (post) => {
  post.showAllComments = true
}

const toggleLike = (post) => {
  post.isLiked = !post.isLiked
  post.likes += post.isLiked ? 1 : -1
}

const toggleComments = (post) => {
  post.showComments = !post.showComments
}

const addComment = (post) => {
  if (post.newComment.trim()) {
    post.comments.push({
      id: Date.now(),
      author: {
        name: 'Marie Dubois',
        avatar: 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1200px-User_icon_2.svg.png'
      },
      text: post.newComment,
      timestamp: 'À l\'instant'
    })
    post.newComment = ''
  }
}

const publishPost = () => {
  if (newPostText.value.trim()) {
    posts.unshift({
      id: Date.now(),
      author: {
        name: 'Marie Dubois',
        avatar: '/placeholder.svg?height=40&width=40'
      },
      content: newPostText.value,
      image: null,
      timestamp: 'À l\'instant',
      likes: 0,
      isLiked: false,
      showComments: false,
      showAllComments: false,
      newComment: '',
      comments: []
    })
    newPostText.value = ''
  }
}

const addPhoto = () => {
  console.log('Ajouter une photo')
}

const addVideo = () => {
  console.log('Ajouter une vidéo')
}
</script>

<style scoped>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

.app {
  min-height: 100vh;
  background: #f9fafb;
  color: #e8f5e8;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* Header */
.header {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  height: 60px;
  background: #1a2e1a;
  border-bottom: 1px solid #2d4a2d;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 20px;
  z-index: 1000;
}

.header-left {
  display: flex;
  align-items: center;
  gap: 20px;
}

.logo-text {
  font-size: 24px;
  font-weight: bold;
  color: #4ade80;
}

.search-container {
  position: relative;
  display: flex;
  align-items: center;
}

.search-input {
  width: 300px;
  height: 40px;
  background: #0f1f0f;
  border: 1px solid #2d4a2d;
  border-radius: 20px;
  padding: 0 40px 0 16px;
  color: #e8f5e8;
  font-size: 14px;
}

.search-input::placeholder {
  color: #9ca3af;
}

.search-icon {
  position: absolute;
  right: 12px;
  width: 20px;
  height: 20px;
  color: #9ca3af;
}

.header-right {
  display: flex;
  align-items: center;
}

.profile-section {
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  padding: 8px 12px;
  border-radius: 8px;
  transition: background-color 0.2s;
}

.profile-section:hover {
  background: #2d4a2d;
}

.profile-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
}

.profile-name {
  font-weight: 500;
  color: #e8f5e8;
}

.dropdown-icon {
  width: 16px;
  height: 16px;
  color: #9ca3af;
}

/* Main Container */
.main-container {
  display: flex;
  margin-top: 60px;
  min-height: calc(100vh - 60px);
}

/* Sidebar */
.sidebar {
  width: 280px;
  background: #1a2e1a;
  border-right: 1px solid #2d4a2d;
  padding: 20px;
  position: fixed;
  height: calc(100vh - 60px);
  overflow-y: auto;
}

.profile-card {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 16px;
  background: #0f1f0f;
  border-radius: 12px;
  margin-bottom: 20px;
}

.profile-card-avatar {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  object-fit: cover;
}

.profile-card-name {
  font-size: 16px;
  font-weight: 600;
  color: #e8f5e8;
  margin-bottom: 4px;
}

.profile-card-status {
  font-size: 12px;
  color: #4ade80;
}

.nav-section {
  margin-bottom: 24px;
}

.nav-title {
  font-size: 12px;
  font-weight: 600;
  color: #9ca3af;
  text-transform: uppercase;
  letter-spacing: 0.5px;
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
  transition: all 0.2s;
  font-size: 14px;
  font-weight: 500;
  margin-bottom: 4px;
}

.nav-item:hover {
  background: #2d4a2d;
}

.nav-item.active {
  background: #4ade80;
  color: #0a0f0a;
}

.nav-icon {
  width: 20px;
  height: 20px;
  stroke-width: 2;
}

/* Main Content */
.main-content {
  flex: 1;
  margin-left: 280px;
  padding: 20px;
  max-width: 600px;
}

/* Create Post */
.create-post-card {
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 20px;
  margin-bottom: 20px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.create-post-header {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 16px;
}

.create-post-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
}

.create-post-input {
  flex: 1;
  background: #f9fafb;
  border: 1px solid #d1d5db;
  border-radius: 20px;
  padding: 12px 16px;
  color: #1f2937;
  font-size: 14px;
}

.create-post-input::placeholder {
  color: #6b7280;
}

.create-post-actions {
  display: flex;
  align-items: center;
  gap: 12px;
}

.action-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  background: transparent;
  border: 1px solid #d1d5db;
  color: #374151;
  padding: 8px 16px;
  border-radius: 8px;
  cursor: pointer;
  font-size: 14px;
  transition: all 0.2s;
}

.action-btn:hover {
  background: #f3f4f6;
}

.action-icon {
  width: 16px;
  height: 16px;
}

.publish-btn {
  background: #4ade80;
  color: #ffffff;
  border: none;
  padding: 8px 20px;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  font-size: 14px;
  transition: all 0.2s;
}

.publish-btn:hover {
  background: #22c55e;
}

/* Posts */
.posts-feed {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.post-card {
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.post-header {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 16px 20px;
}

.post-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
}

.post-author-info {
  flex: 1;
}

.post-author-name {
  font-size: 14px;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 2px;
}

.post-timestamp {
  font-size: 12px;
  color: #6b7280;
}

.post-menu-btn {
  background: transparent;
  border: none;
  color: #6b7280;
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
  transition: all 0.2s;
}

.post-menu-btn:hover {
  background: #f3f4f6;
}

.post-menu-btn svg {
  width: 20px;
  height: 20px;
}

.post-content {
  padding: 0 20px 16px;
}

.post-text {
  font-size: 14px;
  line-height: 1.5;
  color: #1f2937;
  margin-bottom: 12px;
}

.post-image {
  width: 100%;
  height: 300px;
  object-fit: cover;
  border-radius: 8px;
}

.post-stats {
  display: flex;
  justify-content: space-between;
  padding: 0 20px 12px;
  font-size: 12px;
  color: #6b7280;
  border-bottom: 1px solid #e5e7eb;
}

.post-actions {
  display: flex;
  padding: 12px 20px;
}

.post-action-btn {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  background: transparent;
  border: none;
  color: #6b7280;
  padding: 8px;
  border-radius: 8px;
  cursor: pointer;
  font-size: 14px;
  font-weight: 500;
  transition: all 0.2s;
}

.post-action-btn:hover {
  background: #f3f4f6;
}

.post-action-btn.active {
  color: #4ade80;
}

.post-action-btn .action-icon {
  width: 18px;
  height: 18px;
}

/* Comments */
.comments-section {
  border-top: 1px solid #e5e7eb;
  padding: 16px 20px;
  background: #f9fafb;
}

.comment-input-container {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 16px;
}

.comment-avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  object-fit: cover;
}

.comment-input {
  flex: 1;
  background: #ffffff;
  border: 1px solid #d1d5db;
  border-radius: 16px;
  padding: 8px 12px;
  color: #1f2937;
  font-size: 12px;
}

.comment-input::placeholder {
  color: #6b7280;
}

.comment {
  display: flex;
  gap: 12px;
  margin-bottom: 12px;
}

.comment-content {
  flex: 1;
}

.comment-bubble {
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 16px;
  padding: 8px 12px;
  margin-bottom: 4px;
}

.comment-author {
  font-size: 12px;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 2px;
}

.comment-text {
  font-size: 12px;
  color: #1f2937;
  line-height: 1.4;
}

.comment-actions {
  display: flex;
  align-items: center;
  gap: 12px;
  font-size: 11px;
}

.comment-action {
  background: transparent;
  border: none;
  color: #6b7280;
  cursor: pointer;
  font-size: 11px;
  font-weight: 500;
}

.comment-action:hover {
  color: #4ade80;
}

.comment-timestamp {
  color: #6b7280;
}

/* Responsive */
@media (max-width: 1024px) {
  .main-content {
    margin-left: 260px;
  }
  
  .sidebar {
    width: 260px;
  }
}

@media (max-width: 768px) {
  .mobile-menu-btn {
    display: block;
  }
  
  .mobile-overlay {
    display: block;
  }
  
  .mobile-close-btn {
    display: block;
  }
  
  .sidebar {
    position: fixed;
    left: -100%;
    top: 0;
    height: 100vh;
    z-index: 1000;
    transition: left 0.3s ease;
    padding-top: 60px;
  }
  
  .sidebar.mobile-open {
    left: 0;
  }
  
  .main-content {
    margin-left: 0;
    max-width: 100%;
    padding: 15px;
  }
  
  .search-input {
    width: 180px;
  }
  
  .header-left {
    gap: 12px;
  }
  
  .header {
    padding: 0 15px;
  }
  
  .profile-name {
    display: none;
  }
  
  .create-post-card,
  .post-card {
    margin-bottom: 15px;
  }
}

@media (max-width: 480px) {
  .search-input {
    width: 120px;
    font-size: 12px;
  }
  
  .logo-text {
    font-size: 18px;
  }
  
  .header {
    height: 50px;
  }
  
  .main-container {
    margin-top: 50px;
  }
  
  .sidebar {
    padding-top: 50px;
  }
  
  .create-post-actions {
    flex-wrap: wrap;
    gap: 8px;
  }
  
  .action-btn {
    font-size: 12px;
    padding: 6px 12px;
  }
  
  .post-content {
    padding: 0 15px 12px;
  }
  
  .post-header {
    padding: 12px 15px;
  }
  
  .post-actions {
    padding: 8px 15px;
  }
  
  .comments-section {
    padding: 12px 15px;
  }
}

/* Styles pour le bouton menu mobile */
.mobile-menu-btn {
  display: none;
  background: transparent;
  border: none;
  color: #e8f5e8;
  cursor: pointer;
  padding: 8px;
  border-radius: 8px;
  transition: background-color 0.2s;
}

.mobile-menu-btn:hover {
  background: #2d4a2d;
}

.mobile-menu-btn svg {
  width: 24px;
  height: 24px;
}

/* Styles pour l'overlay mobile */
.mobile-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  z-index: 999;
  display: none;
}

/* Bouton fermer mobile dans le sidebar */
.mobile-close-btn {
  display: none;
  position: absolute;
  top: 15px;
  right: 15px;
  background: transparent;
  border: none;
  color: #e8f5e8;
  cursor: pointer;
  padding: 8px;
  border-radius: 8px;
  z-index: 1001;
}

.mobile-close-btn svg {
  width: 20px;
  height: 20px;
}

/* Styles pour les commentaires cliquables */
.post-comments.clickable {
  cursor: pointer;
  transition: color 0.2s;
}

.post-comments.clickable:hover {
  color: #4ade80;
}

/* Bouton "Voir plus de commentaires" */
.show-more-comments-btn {
  background: transparent;
  border: none;
  color: #6b7280;
  cursor: pointer;
  font-size: 12px;
  font-weight: 500;
  padding: 8px 0;
  margin-top: 8px;
  transition: color 0.2s;
}

.show-more-comments-btn:hover {
  color: #4ade80;
}
</style>