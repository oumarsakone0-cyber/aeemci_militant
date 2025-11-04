<template>
  <div class="app">
    <!-- Header -->
    <header class="header">
      <div class="header-left">
        <!-- Ajout du bouton menu mobile -->
        <button class="mobile-menu-btn" @click="toggleMobileMenu">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <line x1="3" y1="6" x2="21" y2="6"></line>
            <line x1="3" y1="12" x2="21" y2="12"></line>
            <line x1="3" y1="18" x2="21" y2="18"></line>
          </svg>
        </button>
        <div class="logo">
          <span class="logo-text">SocialVert</span>
        </div>
        <div class="search-container">
          <input 
            type="text" 
            placeholder="Rechercher..." 
            class="search-input"
            v-model="searchQuery"
          >
          <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <circle cx="11" cy="11" r="8"></circle>
            <path d="m21 21-4.35-4.35"></path>
          </svg>
        </div>
      </div>
      
      <div class="header-right">
        <div class="profile-section">
          <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1200px-User_icon_2.svg.png" alt="Profile" class="profile-avatar">
          <span class="profile-name">Marie Dubois</span>
          <svg class="dropdown-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <polyline points="6,9 12,15 18,9"></polyline>
          </svg>
        </div>
      </div>
    </header>

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

      <!-- Main Content -->
      <main class="main-content">
        <!-- Create Post -->
        <div class="create-post-card">
          <div class="create-post-header">
            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1200px-User_icon_2.svg.png" alt="Profile" class="create-post-avatar">
            <input 
              type="text" 
              placeholder="À quoi pensez-vous, Marie ?" 
              class="create-post-input"
              v-model="newPostText"
            >
          </div>
          <div class="create-post-actions">
            <button class="action-btn" @click="addPhoto">
              <svg class="action-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                <polyline points="21,15 16,10 5,21"></polyline>
              </svg>
              Photo
            </button>
            <button class="action-btn" @click="addVideo">
              <svg class="action-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <polygon points="23 7 16 12 23 17 23 7"></polygon>
                <rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect>
              </svg>
              Vidéo
            </button>
            <button class="publish-btn" @click="publishPost">Publier</button>
          </div>
        </div>

        <!-- Posts Feed -->
        <div class="posts-feed">
            
          <article 
            v-for="post in posts" 
            :key="post.id" 
            class="post-card"
          >
            <div class="post-header">
              <img :src="post.author.avatar" :alt="post.author.name" class="post-avatar">
              <div class="post-author-info">
                <h4 class="post-author-name">{{ post.author.name }}</h4>
                <p class="post-timestamp">{{ post.timestamp }}</p>
              </div>
              <button class="post-menu-btn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <circle cx="12" cy="12" r="1"></circle>
                  <circle cx="19" cy="12" r="1"></circle>
                  <circle cx="5" cy="12" r="1"></circle>
                </svg>
              </button>
            </div>

            <div class="post-content">
              <p class="post-text">{{ post.content }}</p>
              <img 
                v-if="post.image" 
                :src="post.image" 
                :alt="post.content" 
                class="post-image"
              >
            </div>

            <div class="post-stats">
              <span class="post-likes">{{ post.likes }} J'aime</span>
              <span class="post-comments clickable" @click="toggleComments(post)">
                {{ post.comments_count }} commentaire{{ post.comments.length !== 1 ? 's' : '' }}
              </span>
            </div>

            <div class="post-actions">
              <button 
                class="post-action-btn" 
                :class="{ active: post.isLiked }"
                @click="toggleLike(post)"
              >
                <svg class="action-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                </svg>
                J'aime
              </button>
              <button class="post-action-btn" @click="toggleComments(post)">
                <svg class="action-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                </svg>
                Commenter
              </button>
              <button class="post-action-btn">
                <svg class="action-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"></path>
                  <polyline points="16,6 12,2 8,6"></polyline>
                  <line x1="12" y1="2" x2="12" y2="15"></line>
                </svg>
                Partager
              </button>
            </div>

            <!-- Comments Section -->
            <div v-if="post.showComments" class="comments-section">
              <div class="comment-input-container">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1200px-User_icon_2.svg.png" alt="Profile" class="comment-avatar">
                <input 
                  type="text" 
                  placeholder="Écrivez un commentaire..." 
                  class="comment-input"
                  v-model="post.newComment"
                  @keyup.enter="addComment(post)"
                >
              </div>
              
              <!-- Affichage limité des commentaires avec bouton "Voir plus" -->
              <div 
                v-for="(comment, index) in getVisibleComments(post)" 
                :key="comment.id" 
                class="comment"
              >
                <img :src="comment.author.avatar" :alt="comment.author.name" class="comment-avatar">
                <div class="comment-content">
                  <div class="comment-bubble">
                    <h5 class="comment-author">{{ comment.author.name }}</h5>
                    <p class="comment-text">{{ comment.text }}</p>
                  </div>
                  <div class="comment-actions">
                    <button class="comment-action">J'aime</button>
                    <button class="comment-action">Répondre</button>
                    <span class="comment-timestamp">{{ comment.timestamp }}</span>
                  </div>
                </div>
              </div>
              
              <!-- Bouton "Voir plus de commentaires" -->
              <button 
                v-if="post.comments.length > 5 && !post.showAllComments"
                class="show-more-comments-btn"
                @click="showMoreComments(post)"
              >
                Voir {{ post.comments.length - 5 }} commentaire{{ post.comments.length - 5 !== 1 ? 's' : '' }} de plus
              </button>
            </div>
          </article>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'

const searchQuery = ref('')
const activeSection = ref('publications')
const newPostText = ref('')
const showMobileMenu = ref(false)
const posts = reactive([])
const loading = ref(false)

const API_BASE_URL = 'https://sogetrag.com/api/comments.php'

const loadPosts = async () => {
  loading.value = true
  try {
    const randomKey = Math.random().toString(36).substring(7) // clé aléatoire
    const response = await fetch(`${API_BASE_URL}?action=posts&r=${randomKey}`)
    const data = await response.json()
    
    if (data.success) {
      posts.splice(
        0,
        posts.length,
        ...data.data.map(post => ({
          ...post,
          comments: [],
          showComments: false,
          showAllComments: false,
          newComment: ''
        }))
      )
    }
  } catch (error) {
    console.error('Erreur lors du chargement des posts:', error)
  } finally {
    loading.value = false
  }
}

const publishPost = async () => {
  if (!newPostText.value.trim()) return
  
  try {
    const response = await fetch(`${API_BASE_URL}?action=create_post`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        content: newPostText.value,
        nomuser: 'Marie Dubois', // Remplacer par les vraies données utilisateur
        imageuser: '/placeholder.svg?height=40&width=40',
        matricule: 'MD001'
      })
    })
    
    const data = await response.json()
    
    if (data.success) {
      newPostText.value = ''
      await loadPosts() // Recharger les posts
    }
  } catch (error) {
    console.error('Erreur lors de la publication:', error)
  }
}

const toggleLike = async (post) => {
  try {
    const response = await fetch(`${API_BASE_URL}?action=toggle_like`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        post_id: post.id,
        nomuser: 'Marie Dubois', // Remplacer par les vraies données utilisateur
        imageuser: '/placeholder.svg?height=40&width=40',
        matricule: 'MD001'
      })
    })
    
    const data = await response.json()
    
    if (data.success) {
      post.isLiked = data.isLiked
      post.likes = data.likes_count
    }
  } catch (error) {
    console.error('Erreur lors du like:', error)
  }
}

const addComment = async (post) => {
  if (!post.newComment.trim()) return
  
  try {
    const response = await fetch(`${API_BASE_URL}?action=create_comment`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        post_id: post.id,
        content: post.newComment,
        nomuser: 'Marie Dubois', // Remplacer par les vraies données utilisateur
        imageuser: '/placeholder.svg?height=40&width=40',
        matricule: 'MD001'
      })
    })
    
    const data = await response.json()
    
    if (data.success) {
      post.newComment = ''
      await loadCommentsForPost(post)
    }
  } catch (error) {
    console.error('Erreur lors de l\'ajout du commentaire:', error)
  }
}

const loadCommentsForPost = async (post) => {
  try {
    const response = await fetch(`${API_BASE_URL}?action=comments&post_id=${post.id}`)
    const data = await response.json()
    
    if (data.success) {
      // ⚠️ utiliser data.data et non data.comments
      post.comments = data.data 
    }
  } catch (error) {
    console.error('Erreur lors du chargement des commentaires:', error)
  }
}

const toggleComments = async (post) => {
  post.showComments = !post.showComments
  
  if (post.showComments && post.comments.length === 0) {
    await loadCommentsForPost(post)
  }
}

// Ajout de l'état pour le menu mobile
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

const addPhoto = () => {
  console.log('Ajouter une photo')
}

const addVideo = () => {
  console.log('Ajouter une vidéo')
}

onMounted(() => {
  loadPosts()
})
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
