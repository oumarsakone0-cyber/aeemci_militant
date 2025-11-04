<template>
  <div class="main-container">
            <!-- Zone de création de post style Facebook -->
            <div class="create-post-card">
              <div class="create-post-header">
                <img :src="currentUser.photo_url || 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1200px-User_icon_2.svg.png'" 
                     :alt="currentUser.full_name" 
                     class="create-post-avatar">
                <div class="create-post-input-wrapper" @click="openPublishModal">
                  <div class="create-post-input">
                    {{ currentUser.full_name ? 'Exprimez-vous, ' + currentUser.full_name.split(' ')[0] + '...' : 'Exprimez-vous...' }}
                  </div>
                </div>
              </div>
            </div>




        <!-- Fil des publications -->

        <div class="posts-feed">

          <article v-for="post in filteredPosts" :key="post.id" class="post-card">

            <div class="post-header">

              <img :src="post.author.avatar" :alt="post.author.name" class="post-avatar">

              <div class="post-author-info">

                <h4 class="post-author-name">{{ post.author.name }}</h4>

                <p class="post-author-role">{{ post.author.role }}</p>

                <p class="post-timestamp">{{ post.timestamp }}</p>

              </div>

              <div class="post-menu">
                <button class="post-menu-btn" @click="togglePostMenu(post)">⋯</button>
                <div v-if="post.showMenu" class="post-menu-dropdown">
                  <!-- Options pour le propriétaire du post -->
                  <div v-if="isPostOwner(post)" class="menu-item" @click="editPost(post)">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                      <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>
                    Modifier
                  </div>
                  <div v-if="isPostOwner(post)" class="menu-item delete" @click="deletePost(post)">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <polyline points="3,6 5,6 21,6"></polyline>
                      <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                      <line x1="10" y1="11" x2="10" y2="17"></line>
                      <line x1="14" y1="11" x2="14" y2="17"></line>
                    </svg>
                    Supprimer
                  </div>
                  <!-- Option pour signaler le post (si ce n'est pas le propriétaire) -->
                  <div v-if="!isPostOwner(post)" class="menu-item" @click="reportPost(post)">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <circle cx="12" cy="12" r="10"></circle>
                      <line x1="12" y1="8" x2="12" y2="12"></line>
                      <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    Signaler
                  </div>
                </div>
              </div>

            </div>



            <div class="post-content">

              <div v-if="post.content && post.content.trim()" class="post-text" v-html="formatPostContent(post.content)"></div>

              

              <!-- Affichage des médias (images et vidéos) -->

              <div v-if="post.image" class="post-image-container">

                <div v-if="post.caption && post.caption.trim()" class="post-caption-above">{{ post.caption }}</div>

                <img :src="post.image" class="post-image">

              </div>

              

              <div v-if="post.video" class="post-video-container">

                <div v-if="post.video_caption && post.video_caption.trim()" class="post-caption-above">{{ post.video_caption }}</div>

                <video :src="post.video" class="post-video" 
                       controls 
                       autoplay 
                       muted 
                       loop 
                       preload="metadata"
                       playsinline>

                  Votre navigateur ne supporte pas la lecture de vidéos.

                </video>

              </div>

              

              <!-- Support pour plusieurs médias -->

              <div v-if="post.media && post.media.length > 0" class="post-media-grid">

                <div v-for="(media, index) in post.media" :key="index" class="post-media-item">

                  <img v-if="media.type === 'image'" :src="media.url" class="post-media-image">

                  <video v-else-if="media.type === 'video'" :src="media.url" class="post-media-video" 
                         controls 
                         autoplay 
                         muted 
                         loop 
                         preload="metadata"
                         playsinline>

                    Votre navigateur ne supporte pas la lecture de vidéos.

                  </video>

                </div>
              </div>

            </div> <!-- Fin du post-content -->

            <!-- Statistiques d'engagement -->
            <div class="post-stats">

              <div class="reactions-summary">

                <span class="reactions-count">{{ post.reactions || 0 }} réactions</span>

              </div>

              <div class="engagement-stats">

                <span>{{ getTotalCommentsCount(post) }} commentaire{{ getTotalCommentsCount(post) > 1 ? 's' : '' }}</span>

                <span>{{ post.shares || 0 }} partage{{ (post.shares || 0) > 1 ? 's' : '' }}</span>

              </div>

            </div>



            <div class="post-actions">

              <div class="reaction-container">

                <button class="post-action-btn reaction-btn" @click="toggleReactionMenu(post)" :class="{ 
  active: showReactionMenu[post.id], 
  liked: post.selectedReaction?.name === 'J\'aime', 
  loved: post.selectedReaction?.name === 'Amour', 
  haha: post.selectedReaction?.name === 'Haha', 
  wow: post.selectedReaction?.name === 'Wow', 
  dua: post.selectedReaction?.name === 'Dua' 
}">

                  <span class="action-icon">{{ post.selectedReaction?.emoji || '👍' }}</span>

                  {{ post.selectedReaction?.name || 'Réagir' }}

                </button>

                

                <!-- Menu des réactions -->

                <div v-if="showReactionMenu[post.id]" class="reactions-menu">

                  <button v-for="reaction in availableReactions" :key="reaction.emoji" 

                          @click="addReaction(post, reaction)" 

                          class="reaction-option"

                          :title="reaction.name">

                    {{ reaction.emoji }}

                  </button>

                </div>

              </div>

              <button class="post-action-btn" @click="toggleComments(post)">

                <span class="action-icon">💬</span>

                Commenter

              </button>

              <div class="post-action-container">

                <button class="post-action-btn" @click="shareOnWhatsApp(post)">

                  <span class="action-icon">🔄</span>

                  Partager

                </button>

              </div>

            </div>



            <!-- Zone commentaires -->

            <div v-if="post.showComments" class="comments-section">

              <div v-if="userProfileLoaded" class="comment-input-container">

                <img :src="currentUser.photo_url" class="comment-avatar">

                <input 

                  v-model="post.newComment"

                  type="text" 

                  placeholder="Écrire un commentaire..."

                  class="comment-input"

                  @keyup.enter="addComment(post)"

                >

              </div>



              <div class="comments-list">

                <div v-for="(comment, index) in (post.comments || []).filter(c => c).slice().reverse().slice(0, post.visibleCommentsCount || 5)" :key="comment?.id || index" class="comment">

                  <img :src="comment?.author?.avatar || 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1200px-User_icon_2.svg.png'" class="comment-avatar">

                  <div class="comment-content">

                    <div class="comment-bubble">

                      <h5 class="comment-author">{{ comment?.author?.name || 'Utilisateur' }}</h5>

                      <p class="comment-text">{{ comment?.text || '' }}</p>

                    </div>

                    <div class="comment-actions">

                      <button class="comment-action" @click="toggleCommentLike(post, comment)" :class="{ liked: comment?.isLiked }">

                        <span class="action-icon like-icon" :class="{ 'animate-like': comment?.isLiked }">👍</span>

                        <span class="like-text">J'aime</span>

                        <span v-if="comment?.likes > 0" class="like-count">({{ comment?.likes }})</span>

                      </button>

                      <button class="comment-action" @click="toggleReplyInput(post, comment)">

                        <span class="action-icon">💬</span>

                        Répondre

                      </button>

                      <span class="comment-time">{{ comment?.timestamp || 'À l\'instant' }}</span>

                    </div>

                    <!-- Zone de réponse -->

                    <div v-if="comment && comment.showReplyInput" class="reply-input-container">

                      <img :src="currentUser.photo_url" class="reply-avatar">

                      <input 

                        v-model="comment.replyText"

                        type="text" 

                        :placeholder="`Répondre à ${comment?.author?.name}...`"

                        class="reply-input"

                        @keyup.enter="addReply(post, comment)"

                        @keyup.escape="cancelReply(comment)"
                      >
                    </div>

                    <!-- Affichage des réponses -->
                    <div v-if="comment && comment.replies && comment.replies.length > 0" class="replies-container">
                      <div v-for="reply in comment.replies" :key="reply.id" class="reply">
                        <img :src="reply.author?.avatar || 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1200px-User_icon_2.svg.png'" class="reply-avatar">
                        <div class="reply-content">

                          <div class="reply-bubble">

                            <h6 class="reply-author">{{ reply.author?.name || 'Utilisateur' }}</h6>

                            <p class="reply-text">{{ reply.text }}</p>

                          </div>

                          <div class="reply-actions">

                            <button class="reply-action" @click="toggleReplyLike(post, comment, reply)" :class="{ liked: reply?.isLiked }">

                              <span class="action-icon like-icon" :class="{ 'animate-like': reply?.isLiked }">👍</span>

                              <span class="like-text">J'aime</span>

                              <span v-if="reply?.likes > 0" class="like-count">({{ reply?.likes }})</span>

                            </button>

                            <button class="reply-action" @click="toggleReplyToReplyInput(post, comment, reply)">

                              <span class="action-icon">💬</span>

                              Répondre

                            </button>

                            <span class="reply-time">{{ reply?.timestamp || 'À l\'instant' }}</span>

                          </div>

                          <!-- Zone de réponse à la réponse -->

                          <div v-if="reply.showReplyInput" class="reply-to-reply-input-container">

                            <img :src="currentUser.photo_url" class="reply-avatar">

                            <input 

                              v-model="reply.replyText"

                              type="text" 

                              :placeholder="`Répondre à ${reply?.author?.name}...`"

                              class="reply-input"

                              @keyup.enter="addReplyToReply(post, comment, reply)"

                              @keyup.escape="cancelReplyToReply(reply)"

                            >

                          </div>

                          <!-- Affichage des réponses aux réponses -->

                          <div v-if="reply.replies && reply.replies.length > 0" class="sub-replies-container">

                            <div v-for="subReply in reply.replies" :key="subReply.id" class="sub-reply">

                              <img :src="subReply.author?.avatar || 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1200px-User_icon_2.svg.png'" class="reply-avatar">

                              <div class="reply-content">

                                <div class="reply-bubble">

                                  <h6 class="reply-author">{{ subReply.author?.name || 'Utilisateur' }}</h6>

                                  <p class="reply-text">{{ subReply.text }}</p>

                                </div>

                                <div class="reply-actions">

                                  <button class="reply-action" @click="toggleSubReplyLike(post, comment, reply, subReply)" :class="{ liked: subReply?.isLiked }">

                                    <span class="action-icon like-icon" :class="{ 'animate-like': subReply?.isLiked }">👍</span>

                                    <span class="like-text">J'aime</span>

                                    <span v-if="subReply?.likes > 0" class="like-count">({{ subReply?.likes }})</span>

                                  </button>

                                  <span class="reply-time">{{ subReply?.timestamp || 'À l\'instant' }}</span>

                                </div>

                              </div>

                            </div>

                          </div>

                        </div>

                      </div>

                    </div>

                  </div>

                  <div v-if="index === ((post.visibleCommentsCount || 5) - 1) && (post.comments?.length || 0) > (post.visibleCommentsCount || 5)" class="comments-load-more">
                    <a href="#" class="load-more-link" @click.prevent="showMoreComments(post)">Voir plus</a>
                  </div>

                </div>

              </div>

            </div>

          </article>

        </div>




    <!-- Modale de publication moderne style Facebook -->
    <div v-if="showPublishModal" class="publish-modal-overlay" @click="closePublishModal">
      <div class="publish-modal" @click.stop>
        <!-- Header de la modale -->
        <div class="modal-header">
          <h2 class="modal-title">Créer une publication</h2>
          <button @click="closePublishModal" class="close-btn">✕</button>
        </div>

        <!-- Informations utilisateur -->
        <div v-if="userProfileLoaded" class="modal-user-info">
          <img :src="currentUser.photo_url || 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1200px-User_icon_2.svg.png'" 
               :alt="currentUser.full_name" 
               class="modal-avatar">
          <div class="modal-user-details">
            <h4>{{ currentUser.full_name || 'Utilisateur AEEMCI' }}</h4>
            <select v-model="privacyLevel" class="privacy-select">
              <option value="public">🌍 Public</option>
              <option value="friends">👥 Amis</option>
              <option value="private">🔒 Privé</option>
            </select>
          </div>
        </div>

        <!-- Éditeur WYSIWYG -->
        <div class="modal-editor-wrapper">
          <WysiwygEditor
            ref="wysiwygEditorRef"
            v-model="publishContent"
            :placeholder="selectedMedia.length > 0 ? 'Ajouter un commentaire (optionnel)...' : 'Que voulez-vous partager ?'"
            :min-height="selectedMedia.length > 0 ? '150px' : '250px'"
            @focus="handleTextEditorFocus"
            @blur="handleTextEditorBlur"
          />
        </div>

        <!-- Zone de prévisualisation des médias -->
        <div v-if="selectedMedia.length > 0" class="media-preview-section">
          <div class="media-preview-header">
            <h4>Médias ({{ selectedMedia.length }})</h4>
            <button @click="clearAllMedia" class="clear-all-btn">Tout supprimer</button>
          </div>
          <div class="media-preview-grid">
            <div v-for="(media, index) in selectedMedia" :key="index" class="media-preview-item">
              <div class="media-preview-container">
                <img v-if="media.type === 'image'" :src="media.url" class="media-preview" alt="Preview" />
                <video v-else-if="media.type === 'video'" :src="media.url" class="media-preview" controls></video>
                <button @click="removeMedia(index)" class="remove-media-btn" title="Supprimer">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                  </svg>
                </button>
              </div>
              <div class="media-caption-section">
                <textarea 
                  v-model="media.caption" 
                  placeholder="Ajouter une légende pour ce média..." 
                  class="media-caption-input"
                  rows="2"
                  maxlength="200"
                ></textarea>
                <div class="caption-char-count">{{ (media.caption || '').length }}/200</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Actions de la modale -->
        <div class="modal-actions">
          <button @click="triggerMediaUpload" class="modal-action-btn photo-action">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
              <circle cx="8.5" cy="8.5" r="1.5"></circle>
              <polyline points="21 15 16 10 5 21"></polyline>
            </svg>
            <span>Photo/Vidéo</span>
          </button>
          <button @click="showEmojiPicker = !showEmojiPicker" class="modal-action-btn emoji-action">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10"></circle>
              <path d="M8 14s1.5 2 4 2 4-2 4-2"></path>
              <line x1="9" y1="9" x2="9.01" y2="9"></line>
              <line x1="15" y1="9" x2="15.01" y2="9"></line>
            </svg>
            <span>Émoji</span>
          </button>
          <button @click="showLocationModal = true" class="modal-action-btn location-action">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
              <circle cx="12" cy="10" r="3"></circle>
            </svg>
            <span>Lieu</span>
          </button>
        </div>

        <!-- Footer de la modale -->
        <div class="modal-footer">
          <div class="character-count">{{ getTextLength(publishContent) }}/5000</div>
          <button 
            @click="publishFromModal" 
            class="publish-btn" 
            :disabled="(!getPlainText(publishContent).trim() && selectedMedia.length === 0) || isUploading"
            :class="{ 'publishing': isUploading }"
          >
            {{ isUploading ? 'Publication...' : 'Publier' }}
          </button>
        </div>

        <!-- Sélecteur d'émojis -->
        <div v-if="showEmojiPicker" class="emoji-picker-container">
          <div class="emoji-grid">
            <button 
              v-for="emoji in emojis" 
              :key="emoji" 
              @click="insertEmoji(emoji)" 
              class="emoji-btn"
            >
              {{ emoji }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Input file caché pour l'upload -->
    <input 
      ref="fileInput" 
      type="file" 
      multiple 
      accept="image/*,video/*" 
      @change="handleFileSelect" 
      style="display: none"
    />



    <!-- Modale de localisation -->

    <div v-if="showLocationModal" class="location-modal-overlay" @click="showLocationModal = false">

      <div class="location-modal" @click.stop>

        <div class="location-modal-header">

          <h3>Ajouter une localisation</h3>

          <button @click="showLocationModal = false" class="close-btn">x</button>

        </div>

        <div class="location-modal-body">

          <input 

            v-model="locationInput" 

            type="text" 

            placeholder="Où êtes-vous ?" 

            class="location-input"

            @keyup.enter="insertLocation"

          >

          <div class="location-suggestions">

            <button @click="locationInput = 'Paris, France'" class="location-suggestion"> Paris, France</button>

            <button @click="locationInput = 'Casablanca, Maroc'" class="location-suggestion">Casablanca, Maroc</button>

            <button @click="locationInput = 'Tunis, Tunisie'" class="location-suggestion">Tunis, Tunisie</button>

            <button @click="locationInput = 'Alger, Algérie'" class="location-suggestion">Alger, Algérie</button>

          </div>

        </div>

        <div class="location-modal-footer">

          <button @click="showLocationModal = false" class="cancel-btn">Annuler</button>

          <button @click="insertLocation" class="add-location-btn" :disabled="!locationInput.trim()">

            Ajouter

          </button>

        </div>

      </div>

    </div>



    <!-- Modale d'informations profil -->

    <div v-if="showProfileInfo" class="profile-info-modal-overlay" @click="showProfileInfo = false">

      <div class="profile-info-modal" @click.stop>

        <div class="profile-info-header">

          <h3>Informations du profil</h3>

          <button @click="showProfileInfo = false" class="close-btn">x</button>

        </div>

        <div v-if="userProfileLoaded" class="profile-info-body">

          <div class="profile-info-avatar">

            <img :src="currentUser.photo_url" alt="Profile">

          </div>

          <div class="profile-info-details">

            <h4>{{ currentUser.full_name }}</h4>

            <p>{{ currentUser.role }}</p>

            <div class="profile-stats">

              <div class="stat">

                <span class="stat-number">{{ currentUser.posts_count }}</span>

                <span class="stat-label">Publications</span>

              </div>

              <div class="stat">

                <span class="stat-number">{{ currentUser.likes_received }}</span>

                <span class="stat-label">J'aime reçus</span>

              </div>

              <div class="stat">

                <span class="stat-number">{{ currentUser.comments_received }}</span>

                <span class="stat-label">Commentaires</span>

              </div>

            </div>

            <div class="profile-bio" v-if="currentUser.bio">

              <h5>Bio</h5>

              <p>{{ currentUser.bio }}</p>

            </div>

          </div>

        </div>

      </div>

    </div>



    <!-- Modale d'édition de post -->

    <div v-if="showEditModal" class="edit-modal-overlay" @click="closeEditModal">

      <div class="edit-modal" @click.stop>

        <div class="edit-modal-header">

          <h3>Modifier la publication</h3>

          <button @click="closeEditModal" class="close-btn">×</button>

        </div>

        

        <div class="edit-modal-body">

          <div v-if="userProfileLoaded" class="edit-user-info">

            <img :src="currentUser.photo_url" class="edit-avatar">

            <div>

              <h4>{{ currentUser.full_name }}</h4>

              <p>{{ currentUser.role }}</p>

            </div>

          </div>

          

          <div class="edit-editor">

            <div 

              ref="editEditor"

              class="edit-editor-content" 

              contenteditable="true" 

              @input="updateEditContent"

              placeholder="Modifiez votre publication..."

            ></div>

          </div>

        </div>

        

        <div class="edit-modal-footer">

          <button @click="closeEditModal" class="cancel-edit-btn">Annuler</button>

          <button @click="saveEditedPost" class="save-edit-btn" :disabled="!editContent.trim()">

            Enregistrer

          </button>

        </div>

      </div>

    </div>



    <!-- Modale de confirmation de suppression -->

    <div v-if="showDeleteModal" class="delete-modal-overlay" @click="cancelDelete">

      <div class="delete-modal" @click.stop>

        <div class="delete-modal-header">

          <div class="delete-icon">

            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">

              <circle cx="12" cy="12" r="10"></circle>

              <line x1="15" y1="9" x2="9" y2="15"></line>

              <line x1="9" y1="9" x2="15" y2="15"></line>

            </svg>

          </div>

          <h3>Supprimer la publication</h3>

        </div>

        

        <div class="delete-modal-body">

          <p>Cette action est irréversible. Êtes-vous sûr de vouloir supprimer définitivement cette publication ?</p>

          <div v-if="postToDelete" class="post-preview">

            <div class="preview-author">

              <img :src="postToDelete.author.avatar" class="preview-avatar">

              <div>

                <h5>{{ postToDelete.author.name }}</h5>

                <p>{{ postToDelete.timestamp }}</p>

              </div>

            </div>

            <div class="preview-content">

              {{ postToDelete.content.substring(0, 100) }}{{ postToDelete.content.length > 100 ? '...' : '' }}

            </div>

          </div>

        </div>

        

        <div class="delete-modal-footer">

          <button @click="cancelDelete" class="cancel-delete-btn">Annuler</button>

          <button @click="confirmDelete" class="confirm-delete-btn">

            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">

              <polyline points="3,6 5,6 21,6"></polyline>

              <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>

              <line x1="10" y1="11" x2="10" y2="17"></line>

              <line x1="14" y1="11" x2="14" y2="17"></line>

            </svg>

            Supprimer définitivement

          </button>

        </div>

      </div>

    </div>
  </div>

</template>



<script setup>

import { ref, reactive, onMounted, computed } from 'vue'
import WysiwygEditor from '../common/WysiwygEditor.vue'

// ============================================
// CODE INTÉGRÉ - UTILITAIRES D'AUTHENTIFICATION
// ============================================
const STORAGE_KEYS = {
  USER_MATRICULE: 'user_matricule',
  USER_DATA: 'user_data',
  SESSION_TOKEN: 'session_token'
}

const getCurrentUserMatricule = () => {
  return sessionStorage.getItem(STORAGE_KEYS.USER_MATRICULE) || 
         localStorage.getItem(STORAGE_KEYS.USER_MATRICULE)
}

// ============================================
// CODE INTÉGRÉ - CONFIGURATION API
// ============================================
const API_CONFIG = {
  BASE_URL: 'https://sogetrag.com/apistage/post_api.php', // API distante
  TIMEOUT: 15000, // 15 secondes pour serveur distant
  RETRY_ATTEMPTS: 2,
  RETRY_DELAY: 1000 // 1 seconde
}

// ============================================
// CODE INTÉGRÉ - DONNÉES UTILISATEURS
// ============================================
const REAL_USERS = {
  'MOUSTAPHA_OUAGA': {
    matricule: 'MO2024001',
    nom: 'OUAGA',
    prenom: 'MOUSTAPHA',
    full_name: 'MOUSTAPHA OUAGA',
    email: 'moustapha.ouaga@aeemci.org',
    photo_url: 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1200px-User_icon_2.svg.png',
    ville: 'BELIER',
    telephone: '+225 07 XX XX XX XX',
    role: 'Président CE_CE',
    bio: 'Président du Conseil Exécutif - Centre d\'Excellence. Passionné par l\'éducation islamique et le développement communautaire.',
    posts_count: 12,
    likes_received: 45,
    comments_received: 28,
    status: 'active',
    date_inscription: '2024-01-15'
  },
  'AMINATA_KONE': {
    matricule: 'AK2024002',
    nom: 'KONE',
    prenom: 'AMINATA',
    full_name: 'Dr. AMINATA KONE',
    email: 'aminata.kone@aeemci.org',
    photo_url: 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1200px-User_icon_2.svg.png',
    ville: 'Abidjan',
    telephone: '+225 05 XX XX XX XX',
    role: 'Directrice Académique',
    bio: 'Docteure en Sciences Islamiques. Spécialisée dans les méthodologies d\'enseignement moderne.',
    posts_count: 8,
    likes_received: 32,
    comments_received: 19,
    status: 'active',
    date_inscription: '2024-02-01'
  },
  'IBRAHIM_TRAORE': {
    matricule: 'IT2024003',
    nom: 'TRAORE',
    prenom: 'IBRAHIM',
    full_name: 'IBRAHIM TRAORE',
    email: 'ibrahim.traore@aeemci.org',
    photo_url: 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1200px-User_icon_2.svg.png',
    ville: 'Bouaké',
    telephone: '+225 01 XX XX XX XX',
    role: 'Étudiant Niveau 3',
    bio: 'Étudiant passionné en Sciences Islamiques. Futur enseignant.',
    posts_count: 5,
    likes_received: 18,
    comments_received: 12,
    status: 'active',
    date_inscription: '2024-03-10'
  }
}

const getUserByMatricule = (matricule) => {
  // Rechercher dans tous les utilisateurs
  for (const [key, user] of Object.entries(REAL_USERS)) {
    if (user.matricule === matricule) {
      return user
    }
  }
  
  // Si pas trouvé, retourner MOUSTAPHA OUAGA par défaut
  return REAL_USERS.MOUSTAPHA_OUAGA
}

const REAL_POSTS = [
  {
    id: 1,
    content: "Bienvenue sur notre nouvelle plateforme AEEMCI ! 🎉\n\nNous sommes ravis de vous présenter cette nouvelle interface qui permettra à notre communauté de mieux échanger et partager nos expériences académiques et spirituelles.\n\nCette plateforme facilitera la communication entre étudiants, enseignants et administrateurs.",
    author: REAL_USERS.MOUSTAPHA_OUAGA,
    timestamp: "Il y a 2 heures",
    reactions: 24,
    comments: [],
    shares: 8,
    showComments: false,
    isLiked: false,
    location: "BELIER",
    created_at: new Date(Date.now() - 2 * 60 * 60 * 1000).toISOString()
  },
  {
    id: 2,
    content: "Excellente session de formation aujourd'hui sur les nouvelles méthodologies d'enseignement islamique ! 📚✨\n\nMerci à tous les participants pour leur engagement exceptionnel. L'éducation reste notre priorité absolue.\n\n#formation #enseignement #aeemci",
    author: REAL_USERS.AMINATA_KONE,
    timestamp: "Il y a 4 heures",
    reactions: 18,
    comments: [],
    shares: 3,
    showComments: false,
    isLiked: true,
    location: "Abidjan",
    created_at: new Date(Date.now() - 4 * 60 * 60 * 1000).toISOString()
  },
  {
    id: 3,
    content: "Préparation intensive pour les examens de certification AEEMCI. Que Allah facilite nos efforts ! 🤲\n\nLes révisions se passent bien. Merci aux professeurs pour leur soutien constant.\n\n#examens #certification #dua #aeemci #etudes",
    author: REAL_USERS.IBRAHIM_TRAORE,
    timestamp: "Il y a 6 heures",
    reactions: 32,
    comments: [],
    shares: 2,
    showComments: false,
    isLiked: false,
    location: "Bouaké",
    created_at: new Date(Date.now() - 6 * 60 * 60 * 1000).toISOString()
  }
]

// ============================================
// FIN DU CODE INTÉGRÉ
// ============================================

// Variables réactives

const searchQuery = ref('')
const posts = ref([])

// Fonction de recherche
const handleSearch = () => {
  // La recherche se fait automatiquement via computed filteredPosts
  console.log('Recherche:', searchQuery.value)
}

// Écouter les événements de recherche depuis le header (sera ajouté dans onMounted principal)

// Filtres actifs
const activeFilters = ref({
  images: false,
  videos: false,
  textOnly: false,
  period: 'all'
})

// Fonction pour appliquer les filtres
const applyFiltersToPosts = (filters) => {
  activeFilters.value = { ...filters }
}

// Posts filtrés selon la recherche et les filtres
const filteredPosts = computed(() => {
  let filtered = posts.value

  // Appliquer la recherche
  if (searchQuery.value.trim()) {
    const query = searchQuery.value.toLowerCase().trim()
    filtered = filtered.filter(post => {
      const contentMatch = post.content?.toLowerCase().includes(query)
      const authorMatch = post.author?.name?.toLowerCase().includes(query)
      const roleMatch = post.author?.role?.toLowerCase().includes(query)
      return contentMatch || authorMatch || roleMatch
    })
  }

  // Appliquer les filtres de type de contenu
  if (activeFilters.value.images) {
    filtered = filtered.filter(post => {
      return post.image || (post.media && post.media.some(m => m.type === 'image'))
    })
  }

  if (activeFilters.value.videos) {
    filtered = filtered.filter(post => {
      return post.video || (post.media && post.media.some(m => m.type === 'video'))
    })
  }

  if (activeFilters.value.textOnly) {
    filtered = filtered.filter(post => {
      return !post.image && !post.video && (!post.media || post.media.length === 0)
    })
  }

  // Appliquer le filtre de période
  if (activeFilters.value.period !== 'all') {
    const now = new Date()
    filtered = filtered.filter(post => {
      if (!post.created_at && !post.timestamp) return false
      
      const postDate = new Date(post.created_at || post.timestamp)
      
      switch (activeFilters.value.period) {
        case 'today':
          return postDate.toDateString() === now.toDateString()
        case 'week':
          const weekAgo = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000)
          return postDate >= weekAgo
        case 'month':
          const monthAgo = new Date(now.getTime() - 30 * 24 * 60 * 60 * 1000)
          return postDate >= monthAgo
        default:
          return true
      }
    })
  }

  return filtered
})

const isUploading = ref(false)
const uploadProgress = ref(0)
const uploadFileName = ref('')
const showUploadProgress = ref(false)
const isPublishing = ref(false)
const showPublishingProgress = ref(false)

const showPublishModal = ref(false)

const showLocationModal = ref(false)

const showEmojiPicker = ref(false)

const publishContent = ref('')

const wysiwygEditorRef = ref(null)
const fileInput = ref(null)
const privacyLevel = ref('public')

const notifications = ref(3)

const showProfileMenu = ref(false)

const isLoadingUser = ref(false)

const backgroundColors = ['#ffffff', '#e3f2fd', '#f3e5f5', '#e8f5e8', '#fff3e0']

const selectedMedia = ref([])  // Nouveau: pour stocker les médias sélectionnés

const focusedCaptionIndex = ref(-1)  // Pour savoir quelle légende est active



// Utilisateur connecté

const currentUser = ref({

  matricule: '',

  full_name: '',

  photo_url: 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1200px-User_icon_2.svg.png',

  role: '',

  email: '',

  ville: '',

  bio: '',

  posts_count: 0,

  likes_received: 0,

  comments_received: 0

})



// État de chargement pour éviter les affichages en doublon

const userProfileLoaded = ref(false)



// API Configuration

// API_BASE_URL remplacé par API_CONFIG.BASE_URL

const USE_MOCK_DATA_ONLY = false // Utilisation directe de l'API

const API_FALLBACK_TO_MOCK = false // Désactiver toute donnée de test, API réelle uniquement



// Configuration pour le mode développement sans serveur PHP

const DEVELOPMENT_MODE = false // Mode production avec API distante


// Fonctions API

// Fonction pour uploader des fichiers volumineux vers le serveur
const uploadLargeFile = async (file, matricule) => {
  return new Promise((resolve, reject) => {
    const formData = new FormData()
    formData.append('media', file)
    formData.append('matricule', matricule)
    
    const xhr = new XMLHttpRequest()
    
    // Gestionnaire de progression
    xhr.upload.addEventListener('progress', (e) => {
      if (e.lengthComputable) {
        const percentComplete = (e.loaded / e.total) * 100
        uploadProgress.value = percentComplete
        console.log(`📤 Upload: ${Math.round(percentComplete)}%`)
      }
    })
    
    xhr.addEventListener('load', () => {
      if (xhr.status === 200) {
        try {
          const response = JSON.parse(xhr.responseText)
          if (response.success) {
            resolve(response.data.url)
          } else {
            reject(new Error(response.error || 'Erreur upload'))
          }
        } catch (e) {
          reject(new Error('Réponse serveur invalide'))
        }
      } else {
        reject(new Error(`Erreur HTTP ${xhr.status}`))
      }
    })
    
    xhr.addEventListener('error', () => {
      reject(new Error('Erreur réseau lors de l\'upload'))
    })
    
    xhr.open('POST', `${API_CONFIG.BASE_URL}?action=upload_media`)
    xhr.send(formData)
  })
}

// Fonction utilitaire pour traiter les vidéos (maintenant avec upload)
const processVideo = async (videoFile, matricule) => {
  try {
    console.log('🔄 Upload de la vidéo en cours...', {
      name: videoFile.name,
      size: Math.round(videoFile.size / 1024 / 1024) + 'MB'
    })
    
    // Afficher l'indicateur de progression
    showUploadProgress.value = true
    uploadProgress.value = 0
    uploadFileName.value = videoFile.name
    
    // Upload vers le serveur
    const videoUrl = await uploadLargeFile(videoFile, matricule)
    console.log('✅ Vidéo uploadée:', videoUrl)
    
    // Masquer l'indicateur
    showUploadProgress.value = false
    
    return videoUrl
    
  } catch (error) {
    console.error('❌ Erreur upload vidéo:', error)
    showUploadProgress.value = false
    throw error
  }
}

// Fonction utilitaire pour compresser les images de manière très agressive
const compressImage = (file, maxWidth = 400, maxHeight = 400, quality = 0.3) => {
  return new Promise((resolve) => {
    const canvas = document.createElement('canvas')
    const ctx = canvas.getContext('2d')
    const img = new Image()
    
    img.onload = () => {
      // Calculer les nouvelles dimensions en gardant le ratio
      let { width, height } = img
      
      if (width > height) {
        if (width > maxWidth) {
          height = (height * maxWidth) / width
          width = maxWidth
        }
      } else {
        if (height > maxHeight) {
          width = (width * maxHeight) / height
          height = maxHeight
        }
      }
      
      canvas.width = width
      canvas.height = height
      
      // Dessiner l'image redimensionnée
      ctx.imageSmoothingEnabled = true
      ctx.imageSmoothingQuality = 'medium'
      ctx.drawImage(img, 0, 0, width, height)
      
      // Compression progressive avec plusieurs niveaux
      let compressedDataUrl = canvas.toDataURL('image/jpeg', quality)
      
      // Niveau 1: Si >200KB, réduire à 20%
      if (compressedDataUrl.length > 200000) {
        compressedDataUrl = canvas.toDataURL('image/jpeg', 0.2)
      }
      
      // Niveau 2: Si encore >150KB, réduire les dimensions et qualité
      if (compressedDataUrl.length > 150000) {
        const smallCanvas = document.createElement('canvas')
        const smallCtx = smallCanvas.getContext('2d')
        smallCanvas.width = width * 0.7
        smallCanvas.height = height * 0.7
        
        smallCtx.imageSmoothingEnabled = true
        smallCtx.drawImage(img, 0, 0, smallCanvas.width, smallCanvas.height)
        compressedDataUrl = smallCanvas.toDataURL('image/jpeg', 0.15)
      }
      
      // Niveau 3: Si encore >100KB, compression extrême
      if (compressedDataUrl.length > 100000) {
        const tinyCanvas = document.createElement('canvas')
        const tinyCtx = tinyCanvas.getContext('2d')
        tinyCanvas.width = width * 0.5
        tinyCanvas.height = height * 0.5
        
        tinyCtx.imageSmoothingEnabled = true
        tinyCtx.drawImage(img, 0, 0, tinyCanvas.width, tinyCanvas.height)
        compressedDataUrl = tinyCanvas.toDataURL('image/jpeg', 0.1)
      }
      
      console.log(`🔧 Image compressée: ${Math.round(compressedDataUrl.length / 1024)}KB`)
      resolve(compressedDataUrl)
    }
    
    img.src = file
  })
}

const getUserFromSession = () => {
  let matricule = null

  try {

    // Récupérer le matricule depuis le stockage local
    matricule = getCurrentUserMatricule()

    // Si on a des données utilisateur en cache, essayer de récupérer le matricule depuis là
    try {
      const cachedData = sessionStorage.getItem(STORAGE_KEYS.USER_DATA) || 
                         localStorage.getItem(STORAGE_KEYS.USER_DATA)
      if (cachedData) {
        const u = JSON.parse(cachedData)
        matricule = u.matricule_gen || u.matricule || u.id_membre || matricule
      }
    } catch (e) {}

  } catch (e) {}

  if (!matricule) matricule = getCurrentUserMatricule()

  if (matricule === 'TEST001') matricule = null

  if (matricule) {

    try { sessionStorage.setItem('user_matricule', matricule) } catch (e) {}

  }

  return matricule

}



    // Fonction pour formater le contenu des posts (comme Facebook)

    const formatPostContent = (content) => {

      if (!content) return ''

      

      // Supprimer complètement les légendes du contenu HTML

      let cleaned = content

        .replace(/<div[^>]*class="media-caption-label"[^>]*>.*?<\/div>/g, '') // Supprimer les légendes

        .replace(/<div[^>]*class="media-container"[^>]*>.*?<\/div>/g, '') // Supprimer les conteneurs de média

        .replace(/<img[^>]*>/g, '') // Supprimer les images

        .replace(/<button[^>]*>.*?<\/button>/g, '') // Supprimer les boutons

      

      // Nettoyer les espaces multiples

      cleaned = cleaned.replace(/\s+/g, ' ').trim()

      

      // Convertir les sauts de ligne en <br>

      let formatted = cleaned.replace(/\n/g, '<br>')

      

      // Détecter et formater les emojis

      formatted = formatted.replace(/([\u{1F600}-\u{1F64F}]|[\u{1F300}-\u{1F5FF}]|[\u{1F680}-\u{1F6FF}]|[\u{1F1E0}-\u{1F1FF}]|[\u{2600}-\u{26FF}]|[\u{2700}-\u{27BF}])/gu, '<span class="emoji">$1</span>')

      

      // Détecter et formater les hashtags

      formatted = formatted.replace(/#(\w+)/g, '<span class="hashtag">#$1</span>')

      

      // Détecter et formater les mentions

      formatted = formatted.replace(/@(\w+)/g, '<span class="mention">@$1</span>')

      

      return formatted

}



const loadUserProfile = async () => {

  try {

    isLoadingUser.value = true

    let matricule = getUserFromSession()

    

    // Si aucun utilisateur n'est connecté, rediriger vers le login

    if (!matricule) {

      console.warn('Aucun utilisateur connecté, redirection vers /login')

      window.location.href = '/login'

      return

    }



    // Utiliser les vraies données de l'utilisateur connecté

    if (USE_MOCK_DATA_ONLY || DEVELOPMENT_MODE) {

      // Récupérer les vraies données depuis le fichier users.js

      const userData = getUserByMatricule(matricule)

      if (userData) {

      currentUser.value = {

        matricule: userData.matricule,

        full_name: userData.full_name,

        photo_url: userData.photo_url || 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1200px-User_icon_2.svg.png',

        role: userData.role,

        email: userData.email,

        ville: userData.ville,

        bio: userData.bio,

          posts_count: userData.posts_count || 0,

          likes_received: userData.likes_received || 0,

          comments_received: userData.comments_received || 0

        }

        userProfileLoaded.value = true

      } else {

        console.error('Utilisateur non trouvé dans les données de test')

        // Utiliser des données par défaut

        currentUser.value = {

          matricule: matricule,

          full_name: 'Utilisateur AEEMCI',

          photo_url: 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1200px-User_icon_2.svg.png',

          role: 'Membre AEEMCI',

          email: '',

          ville: '',

          bio: '',

          posts_count: 0,

          likes_received: 0,

          comments_received: 0

        }

        userProfileLoaded.value = true

      }

    } else {

      // Mode API : utiliser l'API réelle avec fallback

      try {

        const response = await fetch(`${API_CONFIG.BASE_URL}?action=get_user_profile&matricule=${matricule}`)

        const result = await response.json()



        if (result.success) {

          currentUser.value = {

            matricule: result.data.matricule,

            full_name: result.data.full_name,

            photo_url: result.data.photo_url || 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1200px-User_icon_2.svg.png',

            role: result.data.role || 'Membre AEEMCI',

            email: result.data.email,

            ville: result.data.ville,

            bio: result.data.bio,

            posts_count: result.data.posts_count || 0,

            likes_received: result.data.likes_received || 0,

            comments_received: result.data.comments_received || 0

          }

          userProfileLoaded.value = true

        } else {

          console.error('Erreur lors du chargement du profil:', result.error)

          if (API_FALLBACK_TO_MOCK) {

            // Fallback vers les données de test

            const userData = getUserByMatricule('BEL-SOU-18-001')

            if (userData) {

            currentUser.value = {

                matricule: userData.matricule,

                full_name: userData.full_name,

                photo_url: userData.photo_url || 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1200px-User_icon_2.svg.png',

                role: userData.role,

                email: userData.email,

                ville: userData.ville,

                bio: userData.bio,

                posts_count: userData.posts_count || 0,

                likes_received: userData.likes_received || 0,

                comments_received: userData.comments_received || 0

              }

            } else {

            currentUser.value = {

              matricule: 'BEL-SOU-18-001',

              full_name: 'Marie Dubois',

              photo_url: 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1200px-User_icon_2.svg.png',

              role: 'Étudiante en Sciences Islamiques',

              email: 'marie.dubois@aeemci.org',

              ville: 'Paris',

              bio: 'Passionnée par les sciences islamiques et l\'éducation. Membre active de l\'AEEMCI.',

              posts_count: 5,

              likes_received: 23,

              comments_received: 12

            }

            }

            userProfileLoaded.value = true

          }

        }

      } catch (apiError) {

        console.warn('Serveur PHP non disponible, utilisation des données de test')

        if (API_FALLBACK_TO_MOCK) {

          // Fallback vers les données de test

          const userData = getUserByMatricule('BEL-SOU-18-001')

          if (userData) {

          currentUser.value = {

              matricule: userData.matricule,

              full_name: userData.full_name,

              photo_url: userData.photo_url || 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1200px-User_icon_2.svg.png',

              role: userData.role,

              email: userData.email,

              ville: userData.ville,

              bio: userData.bio,

              posts_count: userData.posts_count || 0,

              likes_received: userData.likes_received || 0,

              comments_received: userData.comments_received || 0

            }

          } else {

          currentUser.value = {

            matricule: 'BEL-SOU-18-001',

            full_name: 'Marie Dubois',

            photo_url: 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1200px-User_icon_2.svg.png',

            role: 'Étudiante en Sciences Islamiques',

            email: 'marie.dubois@aeemci.org',

            ville: 'Paris',

            bio: 'Passionnée par les sciences islamiques et l\'éducation. Membre active de l\'AEEMCI.',

            posts_count: 5,

            likes_received: 23,

            comments_received: 12

          }

          }

          userProfileLoaded.value = true

        }

      }

    }

  } catch (error) {

    console.error('Erreur de connexion:', error)

    // En cas d'erreur, utiliser des données par défaut

    currentUser.value = {

      matricule: 'UNKNOWN',

      full_name: 'Utilisateur AEEMCI',

      photo_url: 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1200px-User_icon_2.svg.png',

      role: 'Membre AEEMCI',

      email: '',

      ville: '',

      bio: '',

      posts_count: 0,

      likes_received: 0,

      comments_received: 0

    }

    userProfileLoaded.value = true

  } finally {

    isLoadingUser.value = false

  }

}



const loadUserFeed = async () => {

  try {

    const matricule = getUserFromSession()

    if (!matricule) return



    if (USE_MOCK_DATA_ONLY || DEVELOPMENT_MODE) {

      // Utiliser les données réalistes depuis users.js

      posts.value = REAL_POSTS.map(post => ({

        id: post.id,

        content: post.content,

        author: {

          name: post.author.full_name,

          role: post.author.role,

          avatar: post.author.photo_url

        },

        timestamp: post.timestamp,

        reactions: post.reactions,

        comments: post.comments,

        shares: post.shares,

        showComments: post.showComments,

        isLiked: post.isLiked,

        location: post.location

      }))

    } else {
      // Mode API : utiliser l'API distante

      try {

        console.log(`🔄 Appel API distante pour get_posts de l'utilisateur ${matricule}`)

        // Ajouter un timestamp pour éviter le cache

        const timestamp = Date.now()

        const response = await fetch(`${API_CONFIG.BASE_URL}?action=get_posts&matricule=${matricule}&limit=20&offset=0&_t=${timestamp}`)

        console.log(`🔄 API distante appelée, status: ${response.status}`)
        

        const result = await response.json()

        console.log('📦 Résultat API (complet):', JSON.stringify(result, null, 2))

        // Détecter si c'est une réponse "service actif" (métadonnées)
        const isMetadataResponse = result && result.success && result.message && result.endpoints && !result.data
        
        if (isMetadataResponse) {
          console.warn('⚠️ API a retourné des métadonnées au lieu de posts')
          throw new Error('API a retourné des métadonnées')
        }

        if (result.success && result.data && Array.isArray(result.data) && result.data.length > 0) {

          posts.value = result.data.map(p => {

            const content = p.content ?? p.post_content ?? p.text ?? p.description ?? ''

            const authorName = p.author_name ?? p.full_name ?? p.author ?? ''

            const authorVille = p.author_ville ?? p.ville ?? ''
            const authorPhoto = p.author_photo ?? p.photo_url ?? p.photo_membre ?? 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1200px-User_icon_2.svg.png'

            const createdAt = p.created_at ?? p.createdAt ?? p.date_creation ?? null

            

            // Traiter les médias

            let media = []

            if (p.media_json) {

              try {

                media = JSON.parse(p.media_json)

              } catch (e) {

                console.warn('Erreur parsing media_json:', e)

              }

            }

            

            // Si pas de media_json mais des URLs individuelles, créer l'objet média

            if (media.length === 0) {

              if (p.image_url) {

                media.push({

                  type: 'image',

                  url: p.image_url,

                  caption: ''

                })

              }

              if (p.video_url) {

                media.push({

                  type: 'video',

                  url: p.video_url,

                  caption: ''

                })

              }

            }

            

            console.log(`📷 Post ${p.id} - Médias trouvés:`, media.length)

            if (media.length > 0) {

              console.log('🔍 Détails des médias:', media.map(m => ({

                type: m.type,

                url: m.url ? m.url.substring(0, 50) + '...' : 'null',

                caption: m.caption

              })))

            }

            

            return {

              id: p.id,

              content,

              media: media, // Ajouter les médias

              author: {

                name: authorName || (p.author_matricule || 'Utilisateur AEEMCI'),

                role: authorVille ? `${authorVille}` : 'Membre AEEMCI',

                avatar: authorPhoto

              },

              author_matricule: p.author_matricule || null, // Conserver le matricule du créateur

              timestamp: createdAt ? formatTimestamp(createdAt) : 'À l\'instant',

              reactions: p.reactions_count || 0,

              comments: [], // Les commentaires seront chargés à la demande

              commentsCount: 0, // Sera chargé depuis l'API

              shares: p.shares_count || 0,

              showComments: false,

              isLiked: false,

              location: p.location || '',

              commentsLoaded: false, // Flag pour savoir si les commentaires ont été chargés

              selectedReaction: null // Réaction sélectionnée par l'utilisateur

            }
          })

          

          // Charger seulement le nombre de commentaires pour chaque post

          for (const post of posts.value) {

            await loadCommentsCount(post)

          }

        } else if (result.success && (!result.data || !Array.isArray(result.data) || result.data.length === 0)) {
          // API a répondu avec succès mais pas de posts
          console.log('ℹ️ API a répondu avec succès mais aucun post disponible')
          posts.value = []
        } else if (!result.success) {
          // API a retourné une erreur
          console.warn('⚠️ API a retourné une erreur:', result.error || result.message)
          throw new Error(result.error || result.message || 'Erreur API')
        }
        
        // Si on arrive ici et qu'il n'y a toujours pas de posts, activer le fallback si disponible
        if (posts.value.length === 0 && API_FALLBACK_TO_MOCK) {

          // Fallback vers les données de test

          posts.value = [

            {

              id: 1,

              content: "Bienvenue sur notre nouvelle plateforme AEEMCI ! 🎉\n\nNous sommes ravis de vous présenter cette nouvelle interface qui permettra à notre communauté de mieux échanger et partager.",

              author: {

                name: "Admin AEEMCI",

                role: "Administrateur",

                avatar: "https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1200px-User_icon_2.svg.png"

              },

              timestamp: "Il y a 2 heures",

              reactions: 12,

              comments: [],

              shares: 3,

              showComments: false,

              isLiked: false

            },

            {

              id: 2,

              content: "Excellente conférence sur les sciences islamiques aujourd'hui ! 🔍✨\n\nLe professeur nous a parlé de l'importance de l'éducation dans l'Islam. Très enrichissant !",

              author: {

                name: "Marie Dubois",

                role: "Étudiante en Sciences Islamiques",

                avatar: "https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1200px-User_icon_2.svg.png"

              },

              timestamp: "Il y a 4 heures",

              reactions: 8,

              comments: [],

              shares: 1,

              showComments: false,

              isLiked: true,

              location: "Paris, France"

            }

          ]

        }

      } catch (apiError) {

        console.warn('Serveur PHP non disponible pour le feed, utilisation des données de test')

        if (API_FALLBACK_TO_MOCK) {

          // Fallback vers les données de test

          posts.value = [

            {

              id: 1,

              content: "Bienvenue sur notre nouvelle plateforme AEEMCI ! 🎉\n\nNous sommes ravis de vous présenter cette nouvelle interface qui permettra à notre communauté de mieux échanger et partager.",

              author: {

                name: "Admin AEEMCI",

                role: "Administrateur",

                avatar: "https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1200px-User_icon_2.svg.png"

              },

              timestamp: "Il y a 2 heures",

              reactions: 12,

              comments: [],

              shares: 3,

              showComments: false,

              isLiked: false

            },

            {

              id: 2,

              content: "Excellente conférence sur les sciences islamiques aujourd'hui ! 🔍✨\n\nLe professeur nous a parlé de l'importance de l'éducation dans l'Islam. Très enrichissant !",

              author: {

                name: "Marie Dubois",

                role: "Étudiante en Sciences Islamiques",

                avatar: "https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1200px-User_icon_2.svg.png"

              },

              timestamp: "Il y a 4 heures",

              reactions: 8,

              comments: [],

              shares: 1,

              showComments: false,

              isLiked: true,

              location: "Paris, France"

            }

          ]

        }

      }

    }

  } catch (error) {

    console.error('Erreur lors du chargement du feed:', error)

  }

}



const formatTimestamp = (timestamp) => {

  const date = new Date(timestamp)

  const now = new Date()

  const diff = now - date

  const minutes = Math.floor(diff / 60000)

  const hours = Math.floor(diff / 3600000)

  const days = Math.floor(diff / 86400000)



  if (minutes < 1) return 'À l\'instant'

  if (minutes < 60) return `Il y a ${minutes} min`

  if (hours < 24) return `Il y a ${hours}h`

  if (days < 7) return `Il y a ${days} jour${days > 1 ? 's' : ''}`

  return date.toLocaleDateString('fr-FR')

}



// Fonctions

const toggleProfileMenu = () => {

  showProfileMenu.value = !showProfileMenu.value

}



// Fermer le menu profil quand on clique ailleurs

const closeProfileMenu = () => {

  showProfileMenu.value = false

}



// Écouter les clics sur le document

const handleClickOutside = (event) => {

  const profileDropdown = event.target.closest('.profile-dropdown')

  if (!profileDropdown && showProfileMenu.value) {

    closeProfileMenu()

  }

}



const openPublishModal = () => {

  showPublishModal.value = true

}



const closePublishModal = () => {

  showPublishModal.value = false

  publishContent.value = ''

  selectedMedia.value = []  // Nettoyer les médias

  focusedCaptionIndex.value = -1  // Réinitialiser le focus

  showEmojiPicker.value = false  // Fermer le picker d'emojis

  captionRefs.value = []  // Nettoyer les références

  if (wysiwygEditorRef.value && wysiwygEditorRef.value.editor) {
    wysiwygEditorRef.value.editor.innerHTML = ''
  }

}

// Fonction pour extraire le texte brut du HTML
const getPlainText = (html) => {
  if (!html) return ''
  const temp = document.createElement('div')
  temp.innerHTML = html
  return temp.textContent || temp.innerText || ''
}

// Fonction pour calculer la longueur du texte (sans HTML)
const getTextLength = (html) => {
  return getPlainText(html).length
}

// Fonction pour déclencher l'upload de médias
const triggerMediaUpload = () => {
  if (fileInput.value) {
    fileInput.value.click()
  }
}

// Fonction pour gérer la sélection de fichiers
const handleFileSelect = async (event) => {
  const files = Array.from(event.target.files || [])
  
  if (files.length === 0) return
  
  for (const file of files) {
    const isImage = file.type.startsWith('image/')
    const isVideo = file.type.startsWith('video/')
    
    if (!isImage && !isVideo) {
      alert(`Le fichier ${file.name} n'est pas une image ou une vidéo`)
      continue
    }
    
    // Vérifier la taille (max 1GB pour vidéos, 10MB pour images)
    const maxSize = isVideo ? 1024 * 1024 * 1024 : 10 * 1024 * 1024
    if (file.size > maxSize) {
      alert(`Le fichier ${file.name} est trop volumineux (max ${isVideo ? '1GB' : '10MB'})`)
      continue
    }
    
    // Créer une URL de prévisualisation
    const url = URL.createObjectURL(file)
    
    selectedMedia.value.push({
      type: isImage ? 'image' : 'video',
      url: url,
      file: file, // Conserver le fichier pour l'upload
      caption: ''
    })
  }
  
  // Réinitialiser l'input
  if (event.target) {
    event.target.value = ''
  }
}

// Fonction pour supprimer un média
const removeMedia = (index) => {
  const media = selectedMedia.value[index]
  if (media && media.url && media.url.startsWith('blob:')) {
    URL.revokeObjectURL(media.url)
  }
  selectedMedia.value.splice(index, 1)
}

// Fonction pour supprimer tous les médias
const clearAllMedia = () => {
  selectedMedia.value.forEach(media => {
    if (media.url && media.url.startsWith('blob:')) {
      URL.revokeObjectURL(media.url)
    }
  })
  selectedMedia.value = []
}

// Fonctions pour gérer le focus/blur de l'éditeur texte
const handleTextEditorFocus = () => {
  // Focus sur l'éditeur WYSIWYG
}

const handleTextEditorBlur = () => {
  // Blur de l'éditeur WYSIWYG
}

const updateModalContent = () => {
  if (wysiwygEditorRef.value && wysiwygEditorRef.value.editor) {
    publishContent.value = wysiwygEditorRef.value.editor.innerHTML
  }
}



/**
 * Fonction principale de publication de post
 * Structure organisée en sections logiques
 */
const publishFromModal = async () => {
  // ============================================
  // SECTION 1: VALIDATION DES DONNÉES
  // ============================================
  const plainText = getPlainText(publishContent.value)
  
  if (!plainText.trim() && selectedMedia.value.length === 0) {
    alert('Veuillez ajouter du texte ou des médias')
    return
  }

  // Vérifier l'authentification
  const matricule = getUserFromSession()
  if (!matricule) {
    alert('Erreur: Utilisateur non connecté')
    return
  }

  // ============================================
  // SECTION 2: TRAITEMENT DES MÉDIAS
  // ============================================
  isUploading.value = true

  try {



        // Préparer le contenu avec les nouveaux médias (utiliser getPlainText pour extraire le texte)

        let textContent = getPlainText(publishContent.value)

          .replace(/\s+/g, ' ') // Remplacer les espaces multiples par un seul
          .trim() // Supprimer les espaces en début/fin

        

        // Préparer les médias et légendes avec compression des images
        const images = []
        const captions = []
        const videos = []
        
        // Traiter les médias (upload pour vidéos, compression pour images)
        for (const media of selectedMedia.value) {
          if (media.type === 'image') {
            try {
              // Pour les images, on garde la compression
              if (media.file) {
                // Si c'est un fichier original, l'uploader
                console.log('🔄 Upload de l\'image...')
                showUploadProgress.value = true
                uploadProgress.value = 0
                uploadFileName.value = media.file.name
                
                const imageUrl = await uploadLargeFile(media.file, matricule)
                images.push(imageUrl)
                console.log('✅ Image uploadée:', imageUrl)
                
                showUploadProgress.value = false
              } else {
                // Sinon, compresser comme avant
                console.log('🔄 Compression de l\'image...')
                const compressedImage = await compressImage(media.url, 400, 400, 0.3)
                images.push(compressedImage)
                console.log('✅ Image compressée:', {
                  original: Math.round(media.url.length / 1024) + 'KB',
                  compressed: Math.round(compressedImage.length / 1024) + 'KB'
                })
              }
            } catch (error) {
              console.warn('⚠️ Erreur traitement image:', error)
              showUploadProgress.value = false // Masquer en cas d'erreur
              if (media.url) {
                images.push(media.url) // Fallback
              }
            }
          } else if (media.type === 'video') {
            try {
              if (media.file) {
                // Upload de la vidéo originale (peut faire jusqu'à 1GB)
                console.log('🔄 Upload de la vidéo...', {
                  name: media.file.name,
                  size: Math.round(media.file.size / 1024 / 1024) + 'MB'
                })
                const videoUrl = await processVideo(media.file, matricule)
                videos.push(videoUrl)
                console.log('✅ Vidéo uploadée:', videoUrl)
              } else {
                // Fallback pour les vidéos déjà en base64 (petites)
                videos.push(media.url)
              }
            } catch (error) {
              console.error('❌ Erreur upload vidéo:', error)
              alert(`Erreur lors de l'upload de la vidéo: ${error.message}`)
            }
          }
          if (media.caption && media.caption.trim()) {
            captions.push(media.caption.trim())
          }
        }
        
        // Si pas de texte principal mais des légendes, utiliser les légendes comme contenu
        if (!textContent && captions.length > 0) {
          textContent = captions.join(' ')
        }

        // ============================================
        // SECTION 3: FERMETURE DE LA MODALE
        // ============================================
        console.log('📤 Uploads terminés, début de la publication...')
        closePublishModal()
        showPublishingProgress.value = true

        // ============================================
        // SECTION 4: MODE DÉVELOPPEMENT (FALLBACK)
        // ============================================
        if (DEVELOPMENT_MODE) {
          // Mode développement : simuler la création de post

          const newPost = {

            id: Date.now(),

            content: textContent,

            image: images[0] || null, // Première image si disponible

            video: videos[0] || null, // Première vidéo si disponible

            caption: captions[0] || null, // Première légende si disponible

            media: selectedMedia.value.map(media => ({

              type: media.type,

              url: media.url,

              caption: media.caption

            })), // Tous les médias

            author: {

              name: currentUser.value.full_name || 'Utilisateur AEEMCI',

              role: currentUser.value.role || 'Membre AEEMCI',

              avatar: currentUser.value.photo_url || 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1200px-User_icon_2.svg.png'

            },

            author_matricule: currentUser.value?.matricule || null, // Ajouter le matricule du créateur

            timestamp: 'À l\'instant',

            reactions: 0,

            comments: [],

            shares: 0,

            showComments: false,

            isLiked: false

          }

      

      posts.value.unshift(newPost)

      closePublishModal()

      

      // Mettre Ã  jour le compteur de posts

      currentUser.value.posts_count++

      

      console.log('Post créé en mode développement:', newPost)

      return

        }

        // ============================================
        // SECTION 5: PUBLICATION VIA API DISTANTE
        // ============================================
        let response, result

        try {
          // Préparer les données à envoyer
          const postData = {
            action: 'create_post',
            matricule: matricule,
            content: textContent,
            images: images,
            videos: videos,
            captions: captions,
            media: selectedMedia.value.map((media, index) => ({
              type: media.type,
              url: media.type === 'image' ? images[index] || media.url : media.url,
              caption: media.caption
            })),
            location: '',
            privacy_level: 'public'
          }

          response = await fetch(API_CONFIG.BASE_URL, {

      method: 'POST',

      headers: {

        'Content-Type': 'application/json'

      },

      body: JSON.stringify({

        action: 'create_post',

        matricule: matricule,

        content: textContent,

        images: images, // Ajouter les images

        videos: videos, // Ajouter les vidéos

        captions: captions, // Ajouter les légendes

        media: selectedMedia.value.map((media, index) => ({
          type: media.type,
          url: media.type === 'image' ? images[index] || media.url : media.url,
          caption: media.caption
        })), // Ajouter tous les médias avec leurs métadonnées (images compressées)

        location: '', // TODO: Ajouter la gestion de la localisation

        privacy_level: 'public'

      })

    })

          result = await response.json()

          if (!result.success) {
            throw new Error(result.error || 'Erreur API distante')
          }

          console.log('✅ Post créé via API distante')

        } catch (error) {
          console.error('🚨 Erreur API:', error)
          
          // Vérifier si c'est une erreur de taille de paquet
          if (error.message && error.message.includes('max_allowed_packet')) {
            throw new Error('Image trop volumineuse. Veuillez utiliser une image plus petite.')
          }
          
          throw new Error('Erreur API distante: ' + error.message)
        }

        // ============================================
        // SECTION 6: POST-PUBLICATION
        // ============================================
        if (result.success) {
          console.log('🎉 Post créé avec succès!')
          
          // Délai pour laisser le temps à la base de données de se synchroniser
          setTimeout(async () => {
            console.log('🔄 Rechargement des posts...')
            await loadUserFeed()
            
            // Masquer l'indicateur de publication
            showPublishingProgress.value = false
            
            console.log('✅ Nouveau post affiché!')
          }, 1500)
        } else {
          showPublishingProgress.value = false
          alert('Erreur lors de la publication: ' + result.error)
        }

    

  } catch (error) {

    console.error('Erreur lors de la publication:', error)
    
    // Masquer les indicateurs en cas d'erreur
    showPublishingProgress.value = false
    showUploadProgress.value = false

    if (DEVELOPMENT_MODE) {

      console.log('Mode développement : simulation de la publication')

      // En mode développement, créer le post mÃªme en cas d'erreur

      const textContent = publishContent.value.replace(/<[^>]*>/g, '')

      const newPost = {

        id: Date.now(),

        content: textContent,

        author: {

          name: currentUser.value.full_name || 'Utilisateur AEEMCI',

          role: currentUser.value.role || 'Membre AEEMCI',

          avatar: currentUser.value.photo_url || 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1200px-User_icon_2.svg.png'

        },

        author_matricule: currentUser.value?.matricule || null, // Ajouter le matricule du créateur

        timestamp: 'À l\'instant',

        reactions: 0,

        comments: [],

        shares: 0,

        showComments: false,

        isLiked: false

      }

      

      posts.value.unshift(newPost)

      closePublishModal()

      currentUser.value.posts_count++

    } else {

    alert('Erreur de connexion lors de la publication')

    }

  } finally {

    isUploading.value = false

  }

}



const toggleLike = async (post) => {

  try {

    const matricule = getUserFromSession()

    if (!matricule) {

      alert('Erreur: Utilisateur non connecté')

      return

    }
    // Appel direct Ã  l'API distante

    console.log('🔄 Appel API distante pour toggle_reaction')

    const response = await fetch(`${API_CONFIG.BASE_URL}?action=toggle_reaction`, {

      method: 'POST',

      headers: {

        'Content-Type': 'application/json',

      },

      body: JSON.stringify({

        matricule: matricule,

        post_id: post.id,

        reaction_type: 'like'

      })

    })

    console.log('”„ API distante appelée, status:', response.status)



    if (!response.ok) {

      throw new Error(`HTTP ${response.status}: ${response.statusText}`)

    }



    const result = await response.json()

    console.log('🔄” Debug réaction - Response:', result)



    if (result.success) {

      // Mettre Ã  jour l'état local

      let action = result.data?.action || result.action

      if (!action) {

        // Fallback: si l'API ne renvoie pas 'action', on alterne localement

        action = post.isLiked ? 'removed' : 'added'

      }

      post.isLiked = action === 'added'

      if (action === 'added') {

        post.reactions = (post.reactions || 0) + 1

      } else {

        post.reactions = Math.max(0, (post.reactions || 0) - 1)

      }

      console.log('Réaction:', result.data?.message || result.message || action)

    } else {

      throw new Error(result.error || 'Erreur lors de la réaction')

    }

  } catch (error) {

    console.error('Erreur lors de la réaction:', error)

    alert('Erreur lors de la réaction: ' + error.message)

  }

}



const toggleComments = async (post) => {

  post.showComments = !post.showComments

  if (post.showComments) {
    post.visibleCommentsCount = 5
  }
  

  // Si on ouvre les commentaires et qu'ils ne sont pas encore chargés, les charger

  if (post.showComments && !post.commentsLoaded) {

    await loadComments(post)

    post.commentsLoaded = true

    // Réinitialiser la visibilité après chargement
    post.visibleCommentsCount = 5

  }

}



const showMoreComments = (post) => {
  const total = post.comments?.length || 0
  const current = post.visibleCommentsCount || 5
  post.visibleCommentsCount = Math.min(total, current + 5)
}



const loadCommentsCount = async (post) => {

  try {

    const response = await fetch(`${API_CONFIG.BASE_URL}?action=get_comments_count&post_id=${post.id}`)

    

    if (!response.ok) {

      throw new Error(`HTTP ${response.status}: ${response.statusText}`)

    }



    const result = await response.json()



    if (result.success) {

      post.commentsCount = result.data.count || 0

    } else {

      post.commentsCount = 0

    }

  } catch (error) {

    console.error('Erreur lors du chargement du nombre de commentaires:', error)

    post.commentsCount = 0

  }

}



const loadComments = async (post) => {

  try {

    console.log('”„ Chargement des commentaires pour le post:', post.id)

    const response = await fetch(`${API_CONFIG.BASE_URL}?action=get_comments_with_replies&post_id=${post.id}`)

    

    if (!response.ok) {

      throw new Error(`HTTP ${response.status}: ${response.statusText}`)

    }



    const result = await response.json()
    console.log('🔄” Debug commentaires - Response:', result)

    if (result.success) {

      post.comments = result.data?.comments || result.comments || []

      console.log(`✅ ${result.data?.count || result.count || 0} commentaires avec réponses chargés pour le post ${post.id}`)

    } else {

      console.error('Erreur lors du chargement des commentaires:', result.error)

      post.comments = []

    }

  } catch (error) {

    console.error('Erreur lors du chargement des commentaires:', error)

  }

}



const addComment = async (post) => {

  if (!post.newComment?.trim()) return

  try {

    const matricule = getUserFromSession()

    if (!matricule) {

      alert('Erreur: Utilisateur non connecté')

      return

    }

    // Appel direct à l'API distante
    console.log('🔄”„ Appel API distante pour add_comment')

    const response = await fetch(`${API_CONFIG.BASE_URL}?action=add_comment`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        matricule: matricule,
        post_id: post.id,
        content: post.newComment
      })
    })

    console.log('🔄”„ API distante appelée, status:', response.status)

    if (!response.ok) {
      throw new Error(`HTTP ${response.status}: ${response.statusText}`)
    }

    const result = await response.json()
    console.log('🔄” Debug commentaire - Response:', result)

    if (result.success) {
      // Vider le champ de commentaire
      post.newComment = ''

      // Mettre à jour le compteur de commentaires
      post.commentsCount = (post.commentsCount || 0) + 1

      // Ajouter le nouveau commentaire directement à la liste si elle est affichée
      if (post.showComments) {
        if (!post.comments) post.comments = []

        // Enrichir le commentaire avec les informations utilisateur actuelles
        const enhancedComment = {
          ...result.data.comment,
          author: {
            name: currentUser.value.full_name || 'Utilisateur AEEMCI',
            avatar: currentUser.value.photo_url || 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1200px-User_icon_2.svg.png'
          }
        }

        post.comments.push(enhancedComment)
      }

      console.log('Commentaire ajouté:', result.data || result.message)
    } else {
      throw new Error(result.error || 'Erreur lors de l\'ajout du commentaire')
    }

  } catch (error) {
    console.error('Erreur lors de l\'ajout du commentaire:', error)
    alert('Erreur lors de l\'ajout du commentaire: ' + error.message)
  }

}



const formatText = (command) => {

  document.execCommand(command, false, null)

}



const setBackgroundColor = (color) => {

  if (modalEditor.value) {

    modalEditor.value.style.backgroundColor = color

  }

}



const addMedia = () => {

  const input = document.createElement('input')

  input.type = 'file'

  input.accept = 'image/*,video/*'

  input.multiple = true

  input.onchange = (e) => {

    const files = Array.from(e.target.files)

    files.forEach(file => {

      const reader = new FileReader()

      reader.onload = (event) => {

        const mediaType = file.type.startsWith('image/') ? 'image' : 'video'

        const mediaObject = {

          type: mediaType,

          url: event.target.result,

          caption: '',

          file: file,

          name: file.name,

          size: file.size

        }

        selectedMedia.value.push(mediaObject)

        updateModalContent()

      }

      reader.readAsDataURL(file)

    })

  }

  input.click()

}


// Les fonctions removeMedia et clearAllMedia sont déjà définies plus haut



// Références pour les inputs de légende

const captionRefs = ref([])

const setCaptionRef = (el, index) => {

  if (el) {

    captionRefs.value[index] = el

  }

}



const emojis = ['😊', '😍', '🤔', '😢', '😂', '👍', '❤️', '🔥', '💯', '🙏', '📚', '🕌', '☪️', '🤲', '📖', '✨', '🌟', '💫', '🌙', '🌸']


// Gestion du focus des légendes

const handleCaptionFocus = (index) => {

  focusedCaptionIndex.value = index

  console.log('🎯 FOCUS ACTIVÉ sur la légende du média', index)

  console.log('📊 État des médias:', selectedMedia.value.length)

}



const handleCaptionBlur = () => {

  // Délai plus long pour permettre le clic sur les emojis

  setTimeout(() => {

    // Ne retirer le focus QUE si aucun emoji picker n'est ouvert

    if (!showEmojiPicker.value) {

      focusedCaptionIndex.value = -1

      console.log('📝 Focus retiré des légendes (picker fermé)')

    } else {

      console.log('🔒 Focus maintenu (picker ouvert)')

    }

  }, 500)

}



const addEmoji = () => {

  showEmojiPicker.value = true

}



const insertEmoji = (emoji) => {

  console.log('🔍 Tentative d\'insertion emoji:', emoji)

  console.log('📊 État focusedCaptionIndex:', focusedCaptionIndex.value)

  console.log('📊 Nombre de médias:', selectedMedia.value.length)

  

  // STRICT: Si une légende est explicitement active, FORCER l'insertion dans la légende

  if (focusedCaptionIndex.value >= 0 && 
      focusedCaptionIndex.value < selectedMedia.value.length &&
      selectedMedia.value[focusedCaptionIndex.value] && 
      captionRefs.value[focusedCaptionIndex.value]) {

    

    const captionInput = captionRefs.value[focusedCaptionIndex.value]

    const currentValue = selectedMedia.value[focusedCaptionIndex.value].caption || ''

    const cursorPos = captionInput.selectionStart || currentValue.length

    const newValue = currentValue.slice(0, cursorPos) + emoji + currentValue.slice(cursorPos)

    

    // FORCER la mise à jour

    selectedMedia.value[focusedCaptionIndex.value].caption = newValue

    

    // Remettre le focus et la position du curseur

    setTimeout(() => {

      captionInput.focus()

      captionInput.setSelectionRange(cursorPos + emoji.length, cursorPos + emoji.length)

    }, 50)

    

    console.log('✅ Emoji FORCÉ dans la légende du média', focusedCaptionIndex.value, newValue)

    return // SORTIR ICI pour éviter l'insertion dans le texte

  }

  

  // SINON: Insérer dans l'éditeur WYSIWYG principal

  if (wysiwygEditorRef.value && wysiwygEditorRef.value.editor) {

    const editorElement = wysiwygEditorRef.value.editor

    editorElement.focus()

    
    const selection = window.getSelection()

    if (selection.rangeCount > 0) {

      const range = selection.getRangeAt(0)

      range.deleteContents()

      range.insertNode(document.createTextNode(emoji))

      range.collapse(false)

      selection.removeAllRanges()

      selection.addRange(range)

    } else {

      const textNode = document.createTextNode(emoji)

      editorElement.appendChild(textNode)

      const range = document.createRange()

      range.selectNodeContents(textNode)

      range.collapse(false)

      selection.removeAllRanges()

      selection.addRange(range)

    }

    

    publishContent.value = editorElement.innerHTML

    updateModalContent()

    

    console.log('✅ Emoji inséré dans le texte principal WYSIWYG')

  } else {

    console.error('❌ Aucun éditeur WYSIWYG disponible pour insérer l\'emoji')

    // Fallback: ajouter directement au contenu

    publishContent.value += emoji

  }

}



// Fonction globale pour insérer l'émoji

window.insertEmojiInEditor = (emoji) => {

  if (modalEditor.value) {

    modalEditor.value.focus()

    document.execCommand('insertText', false, emoji)

    updateModalContent()

  }

  // Supprimer le sélecteur d'émojis

  const picker = document.querySelector('.emoji-picker')

  if (picker) {

    picker.remove()

  }

}



const locationInput = ref('')

const showShareMenu = ref({})

const showReactionMenu = ref({})

const showProfileInfo = ref(false)

// Réactions disponibles

const availableReactions = ref([

  { emoji: '👍', name: 'J\'aime' },

  { emoji: '❤️', name: 'Amour' },

  { emoji: '😂', name: 'Haha' },

  { emoji: '😮', name: 'Wow' },

  { emoji: '🤲', name: 'Dua' }

])

const showDeleteModal = ref(false)

const postToDelete = ref(null)



// Gestion des réactions

const toggleReactionMenu = (post) => {

  showReactionMenu.value[post.id] = !showReactionMenu.value[post.id]

  // Fermer les autres menus

  Object.keys(showReactionMenu.value).forEach(key => {

    if (key != post.id) {

      showReactionMenu.value[key] = false

    }

  })

}



const addReaction = async (post, reaction) => {

  try {

    console.log(`🔄 Ajout réaction ${reaction.emoji} au post ${post.id}`)

    

    const response = await fetch(`${API_CONFIG.BASE_URL}?action=toggle_reaction`, {

      method: 'POST',

      headers: {

        'Content-Type': 'application/json',

      },

      body: JSON.stringify({

        matricule: currentUser.value.matricule,

        post_id: post.id,

        reaction_type: reaction.name.toLowerCase()

      })

    })



    if (!response.ok) {

      throw new Error(`HTTP ${response.status}: ${response.statusText}`)

    }



    const result = await response.json()

    

    if (result.success) {

      // Mettre à jour le compteur de réactions

      const action = result.data?.action || 'added'

      if (action === 'added') {

        post.reactions = (post.reactions || 0) + 1

        post.selectedReaction = reaction // Sauvegarder la réaction sélectionnée

      } else {

        post.reactions = Math.max(0, (post.reactions || 0) - 1)

        post.selectedReaction = null // Retirer la réaction sélectionnée

      }

      

      console.log(`✅ Réaction ${reaction.emoji} ${action === 'added' ? 'ajoutée' : 'supprimée'}`)

    }

    

    // Fermer le menu des réactions

    showReactionMenu.value[post.id] = false

    

  } catch (error) {

    console.error('Erreur lors de l\'ajout de la réaction:', error)

    alert('Erreur lors de l\'ajout de la réaction: ' + error.message)

  }

}



// Gestion des likes de commentaires

const toggleCommentLike = async (post, comment) => {

  try {

    const matricule = getUserFromSession()

    if (!matricule) {

      alert('Erreur: Utilisateur non connecté')

      return

    }

    

    console.log(`🔄 Toggle like commentaire ${comment.id}`)

    

    const response = await fetch(`${API_CONFIG.BASE_URL}?action=toggle_comment_like`, {

      method: 'POST',

      headers: {

        'Content-Type': 'application/json',

      },

      body: JSON.stringify({

        matricule: matricule,

        comment_id: comment.id,

        post_id: post.id

      })

    })

    

    const result = await response.json()

    

    if (result.success) {

      comment.isLiked = !comment.isLiked

      comment.likes = (comment.likes || 0) + (comment.isLiked ? 1 : -1)

      console.log('✅ Like commentaire mis à jour')

    }

    

  } catch (error) {

    console.error('❌ Erreur lors du like commentaire:', error)

  }

}



// Gestion des réponses aux commentaires

// Afficher/masquer l'input de réponse

const toggleReplyInput = (post, comment) => {

  // Fermer toutes les autres réponses ouvertes dans ce post

  if (post.comments) {

    post.comments.forEach(c => {

      if (c.id !== comment.id) {

        c.showReplyInput = false

        c.replyText = ''

      }

    })

  }

  

  // Toggle l'input de réponse pour ce commentaire

  comment.showReplyInput = !comment.showReplyInput

  comment.replyText = comment.replyText || ''

  

  console.log('💬 Toggle réponse au commentaire:', comment.id)

}



// Annuler une réponse

const cancelReply = (comment) => {

  comment.showReplyInput = false

  comment.replyText = ''

}






// Calculer le nombre total de commentaires (commentaires + réponses)

const calculateTotalComments = (post) => {

  let total = 0

  if (post && post.comments && Array.isArray(post.comments)) {

    post.comments.forEach(comment => {

      if (comment) {

        total += 1 // Le commentaire principal

        if (comment.replies && Array.isArray(comment.replies) && comment.replies.length > 0) {

          total += comment.replies.length // Les réponses

          // Compter aussi les sous-réponses

          comment.replies.forEach(reply => {

            if (reply && reply.replies && Array.isArray(reply.replies) && reply.replies.length > 0) {

              total += reply.replies.length

            }

          })

        }

      }

    })

  }

  return total

}



// Fonction helper pour éviter les calculs multiples

const getTotalCommentsCount = (post) => {

  if (!post) return 0

  return calculateTotalComments(post)

}



// Ajouter une réponse à un commentaire

const addReply = async (post, comment) => {

  if (!comment.replyText?.trim()) return

  

  try {

    const matricule = getUserFromSession()

    if (!matricule) {

      alert('Erreur: Utilisateur non connecté')

      return

    }

    

    console.log(`💬 Ajout réponse au commentaire ${comment.id}`)

    

    const response = await fetch(`${API_CONFIG.BASE_URL}?action=add_reply`, {

      method: 'POST',

      headers: {

        'Content-Type': 'application/json',

      },

      body: JSON.stringify({

        matricule: matricule,

        comment_id: comment.id,

        post_id: post.id,

        text: comment.replyText.trim()

      })

    })

    

    const result = await response.json()

    

    if (result.success) {

      // Initialiser le tableau des réponses si nécessaire

      if (!comment.replies) {

        comment.replies = []

      }

      

      // Ajouter la nouvelle réponse

      const newReply = {

        id: result.reply_id || Date.now(),

        text: comment.replyText.trim(),

        author: {

          name: currentUser.value.full_name,

          avatar: currentUser.value.photo_url

        },

        timestamp: 'À l\'instant',

        likes: 0,

        isLiked: false

      }

      

      comment.replies.push(newReply)

      

      // Réinitialiser l'input

      comment.replyText = ''

      comment.showReplyInput = false

      
      console.log('✅ Réponse ajoutée avec succès')

      

    } else {

      console.error('❌ Erreur lors de l\'ajout de la réponse:', result.error)

      alert('Erreur lors de l\'ajout de la réponse')

    }

    

  } catch (error) {

    console.error('❌ Erreur lors de l\'ajout de la réponse:', error)

    alert('Erreur lors de l\'ajout de la réponse')

  }

}



// Gestion des likes de réponses

const toggleReplyLike = async (post, comment, reply) => {

  try {

    const matricule = getUserFromSession()

    if (!matricule) {

      alert('Erreur: Utilisateur non connecté')

      return

    }

    

    console.log(`🔄 Toggle like réponse ${reply.id} (comment: ${comment.id}, post: ${post.id})`)

    

    const response = await fetch(`${API_CONFIG.BASE_URL}?action=toggle_reply_like`, {

      method: 'POST',

      headers: {

        'Content-Type': 'application/json',

      },

      body: JSON.stringify({

        matricule: matricule,

        reply_id: reply.id,

        comment_id: comment.id,

        post_id: post.id

      })

    })

    

    const result = await response.json()

    

    console.log('Résultat API toggle_reply_like:', result)

    

    if (result.success) {

      reply.isLiked = !reply.isLiked

      reply.likes = (reply.likes || 0) + (reply.isLiked ? 1 : -1)

      console.log('✅ Like réponse mis à jour')

    } else {

      console.error('❌ Erreur API toggle_reply_like:', result.error)

      alert('Erreur lors du like de la réponse: ' + (result.error || 'Erreur inconnue'))

    }

    

  } catch (error) {

    console.error('❌ Erreur lors du like réponse:', error)

    alert('Erreur réseau lors du like de la réponse')

  }

}



// Fonction de compatibilité (ancienne fonction)

const replyToComment = (post, comment) => {

  toggleReplyInput(post, comment)

}



// Gestion des réponses aux réponses (commentaires imbriqués niveau 3)

// Afficher/masquer l'input de réponse à une réponse

const toggleReplyToReplyInput = (post, comment, reply) => {

  // Fermer toutes les autres réponses ouvertes dans ce commentaire

  if (comment.replies) {

    comment.replies.forEach(r => {

      if (r.id !== reply.id) {

        r.showReplyInput = false

        r.replyText = ''

      }

    })

  }

  

  // Toggle l'input de réponse pour cette réponse

  reply.showReplyInput = !reply.showReplyInput

  reply.replyText = reply.replyText || ''

  

  console.log('💬 Toggle réponse à la réponse:', reply.id)

}



// Annuler une réponse à une réponse

const cancelReplyToReply = (reply) => {

  reply.showReplyInput = false

  reply.replyText = ''

}



// Ajouter une réponse à une réponse

const addReplyToReply = async (post, comment, reply) => {

  if (!reply.replyText?.trim()) return

  

  try {

    const matricule = getUserFromSession()

    if (!matricule) {

      alert('Erreur: Utilisateur non connecté')

      return

    }

    

    console.log(`💬 Ajout réponse à la réponse ${reply.id}`)

    

    const response = await fetch(`${API_CONFIG.BASE_URL}?action=add_reply`, {

      method: 'POST',

      headers: {

        'Content-Type': 'application/json',

      },

      body: JSON.stringify({

        matricule: matricule,

        comment_id: reply.id, // La réponse devient le parent

        post_id: post.id,

        text: reply.replyText.trim()

      })

    })

    

    const result = await response.json()

    

    if (result.success) {

      // Initialiser le tableau des réponses si nécessaire

      if (!reply.replies) {

        reply.replies = []

      }

      

      // Ajouter la nouvelle réponse

      const newSubReply = {

        id: result.reply_id || Date.now(),

        text: reply.replyText.trim(),

        author: {

          name: currentUser.value.full_name,

          avatar: currentUser.value.photo_url

        },

        timestamp: 'À l\'instant',

        likes: 0,

        isLiked: false

      }

      

      reply.replies.push(newSubReply)

      

      // Réinitialiser l'input

      reply.replyText = ''

      reply.showReplyInput = false

      

      console.log('✅ Réponse à la réponse ajoutée avec succès')

      

    } else {

      console.error('❌ Erreur lors de l\'ajout de la réponse à la réponse:', result.error)

      alert('Erreur lors de l\'ajout de la réponse')

    }

    

  } catch (error) {

    console.error('❌ Erreur lors de l\'ajout de la réponse à la réponse:', error)

    alert('Erreur lors de l\'ajout de la réponse')

  }

}



// Gestion des likes de réponses aux réponses (sous-réponses)

const toggleSubReplyLike = async (post, comment, reply, subReply) => {

  try {

    const matricule = getUserFromSession()

    if (!matricule) {

      alert('Erreur: Utilisateur non connecté')

      return

    }

    

    console.log(`🔄 Toggle like sous-réponse ${subReply.id}`)

    

    const response = await fetch(`${API_CONFIG.BASE_URL}?action=toggle_reply_like`, {

      method: 'POST',

      headers: {

        'Content-Type': 'application/json',

      },

      body: JSON.stringify({

        matricule: matricule,

        reply_id: subReply.id,

        comment_id: reply.id,

        post_id: post.id

      })

    })

    

    const result = await response.json()

    

    if (result.success) {

      subReply.isLiked = !subReply.isLiked

      subReply.likes = (subReply.likes || 0) + (subReply.isLiked ? 1 : -1)

      console.log('✅ Like sous-réponse mis à jour')

    }

    

  } catch (error) {

    console.error('❌ Erreur lors du like sous-réponse:', error)

  }

}



const addLocation = () => {

  showLocationModal.value = true

}



const insertLocation = () => {

  if (locationInput.value.trim() && wysiwygEditorRef.value && wysiwygEditorRef.value.editor) {

    const editorElement = wysiwygEditorRef.value.editor

    editorElement.focus()

    

    const locationText = `📍 ${locationInput.value} `

    

    const selection = window.getSelection()

    if (selection.rangeCount > 0) {

      const range = selection.getRangeAt(0)

      range.deleteContents()

      const textNode = document.createTextNode(locationText)

      range.insertNode(textNode)

      range.collapse(false)

      selection.removeAllRanges()

      selection.addRange(range)

    } else {

      const textNode = document.createTextNode(locationText)

      editorElement.appendChild(textNode)

      const range = document.createRange()

      range.selectNodeContents(textNode)

      range.collapse(false)

      selection.removeAllRanges()

      selection.addRange(range)

    }

    

    publishContent.value = editorElement.innerHTML

    updateModalContent()

  }

  showLocationModal.value = false

  locationInput.value = ''

}



// Variables pour l'édition

const showEditModal = ref(false)

const editingPost = ref(null)

const editContent = ref('')

const editEditor = ref(null)



// Fonctions pour les menus et actions

const togglePostMenu = (post) => {

  post.showMenu = !post.showMenu

}



const editPost = (post) => {
  // Vérifier à nouveau avant d'éditer (sécurité supplémentaire)
  if (!isPostOwner(post)) {
    alert('Vous n\'êtes pas autorisé à modifier ce post')
    return
  }
  
  editingPost.value = post

  editContent.value = post.content

  showEditModal.value = true

  post.showMenu = false

  

  // Focus sur l'éditeur après ouverture

  setTimeout(() => {

    if (editEditor.value) {

      editEditor.value.innerHTML = post.content

      editEditor.value.focus()

    }

  }, 100)

}



const saveEditedPost = () => {

  if (!editingPost.value) return

  // Vérifier une dernière fois que l'utilisateur est propriétaire avant de sauvegarder
  if (!isPostOwner(editingPost.value)) {
    alert('Vous n\'êtes pas autorisé à modifier ce post')
    closeEditModal()
    return
  }

  if (editContent.value.trim()) {

    editingPost.value.content = editContent.value

    editingPost.value.timestamp = 'Modifié Ã  l\'instant'

    closeEditModal()

  }

}



const closeEditModal = () => {

  showEditModal.value = false

  editingPost.value = null

  editContent.value = ''

}



const updateEditContent = () => {

  if (editEditor.value) {

    editContent.value = editEditor.value.innerHTML

  }

}



// Fonction pour vérifier si l'utilisateur actuel est le créateur du post
const isPostOwner = (post) => {
  const currentMatricule = currentUser.value?.matricule
  const postAuthorMatricule = post.author_matricule
  
  // Si le post n'a pas d'author_matricule, on ne peut pas vérifier (sécurité)
  if (!postAuthorMatricule) return false
  
  // Comparer les matricules
  return currentMatricule && postAuthorMatricule && currentMatricule === postAuthorMatricule
}

// Fonction pour signaler un post
const reportPost = (post) => {
  post.showMenu = false
  if (confirm(`Voulez-vous signaler cette publication de ${post.author.name} ?`)) {
    // TODO: Implémenter l'API de signalement
    console.log('Post signalé:', post.id)
    alert('Merci pour votre signalement. Nous examinerons cette publication.')
  }
}

const deletePost = (post) => {
  // Vérifier à nouveau avant de supprimer (sécurité supplémentaire)
  if (!isPostOwner(post)) {
    alert('Vous n\'êtes pas autorisé à supprimer ce post')
    return
  }
  
  postToDelete.value = post

  showDeleteModal.value = true

  post.showMenu = false

}



const confirmDelete = async () => {

  if (!postToDelete.value) return

  // Vérifier une dernière fois que l'utilisateur est propriétaire avant de supprimer
  if (!isPostOwner(postToDelete.value)) {
    alert('Vous n\'êtes pas autorisé à supprimer ce post')
    showDeleteModal.value = false
    postToDelete.value = null
    return
  }

  

  try {

    const matricule = getUserFromSession()

    if (!matricule) {

      alert('Erreur: Utilisateur non connecté')

      return

    }



    console.log(`🗑️ Suppression du post ${postToDelete.value.id}`)

    

    const response = await fetch(`${API_CONFIG.BASE_URL}?action=delete_post`, {

      method: 'POST',

      headers: {

        'Content-Type': 'application/json',

      },

      body: JSON.stringify({

        matricule: matricule,

        post_id: postToDelete.value.id

      })

    })

    

    const result = await response.json()

    

    console.log('Résultat suppression post:', result)

    

    if (result.success) {

      // Supprimer le post de la liste locale

      const index = posts.value.findIndex(p => p.id === postToDelete.value.id)

      if (index !== -1) {

        posts.value.splice(index, 1)

      }

      console.log('✅ Post supprimé avec succès')

    } else {

      console.error('❌ Erreur lors de la suppression:', result.error)

      alert('Erreur lors de la suppression du post: ' + (result.error || 'Erreur inconnue'))

    }

    

  } catch (error) {

    console.error('❌ Erreur lors de la suppression:', error)

    alert('Erreur réseau lors de la suppression du post')

  }

  

  showDeleteModal.value = false

  postToDelete.value = null

}



const cancelDelete = () => {

  showDeleteModal.value = false

  postToDelete.value = null

}



const shareOnWhatsApp = (post) => {
  // Créer le texte à partager avec le contenu du post
  const postText = `${post.content}\n\n- ${post.author.name}`;
  
  // Encoder le texte pour l'URL WhatsApp
  const encodedText = encodeURIComponent(postText);
  
  // Créer l'URL WhatsApp
  const whatsappUrl = `https://wa.me/?text=${encodedText}`;
  
  // Ouvrir WhatsApp dans un nouvel onglet
  window.open(whatsappUrl, '_blank');
}



const shareOnPage = (post) => {

  // Partager sur la page (republier)

  const newPost = {

    id: Date.now(),

    content: `Partagé: ${post.content}`,

    author: {

      name: currentUser.value.full_name || 'Utilisateur AEEMCI',

      role: currentUser.value.role || 'Membre AEEMCI',

      avatar: currentUser.value.photo_url || 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1200px-User_icon_2.svg.png'

    },

    author_matricule: currentUser.value?.matricule || null, // Ajouter le matricule du créateur

    timestamp: 'À l\'instant',

    reactions: 0,

    comments: [],

    shares: 0,

    showComments: false,

    isLiked: false,

    originalPost: post

  }

  posts.value.unshift(newPost)

  showShareMenu.value[post.id] = false

}



const copyLink = (post) => {

  const link = `${window.location.origin}/post/${post.id}`

  navigator.clipboard.writeText(link).then(() => {

    alert('Lien copié dans le presse-papiers !')

  })

  showShareMenu.value[post.id] = false

}



const downloadPost = (post) => {

  const element = document.createElement('a')

  const content = `${post.author.name} - ${post.timestamp}\n\n${post.content}`

  const file = new Blob([content], { type: 'text/plain' })

  element.href = URL.createObjectURL(file)

  element.download = `post_${post.id}.txt`

  document.body.appendChild(element)

  element.click()

  document.body.removeChild(element)

  showShareMenu.value[post.id] = false

}



// Fonctions du menu profil

const showProfile = () => {

  showProfileInfo.value = true

  showProfileMenu.value = false

}



const showSettings = () => {

  alert('Fonctionnalité Paramètres en cours de développement')

  showProfileMenu.value = false

}



const logout = () => {

  if (confirm('Êtes-vous sûr de vouloir vous déconnecter ?')) {

    // Simulation de déconnexion

    alert('Déconnexion réussie')

    // Ici vous pourriez rediriger vers la page de connexion

    // window.location.href = '/login'

  }

  showProfileMenu.value = false

}





onMounted(async () => {
  // Écouter les événements de recherche depuis le header
  window.addEventListener('search-posts', (event) => {
    searchQuery.value = event.detail
    console.log('Recherche depuis header:', event.detail)
  })

  // Écouter les événements de filtres depuis le header
  window.addEventListener('apply-filters', (event) => {
    applyFiltersToPosts(event.detail)
    console.log('Filtres appliqués:', event.detail)
  })

  // Ajouter l'écouteur pour fermer le menu profil
  document.addEventListener('click', handleClickOutside)

  

  try {

    // Charger le profil utilisateur et le feed

    await loadUserProfile()

    await loadUserFeed()

  } catch (error) {

    console.error('Erreur lors de l\'initialisation:', error)

    isLoadingUser.value = false

  }

})

</script>

<style scoped>

* {

  margin: 0;

  padding: 0;

  box-sizing: border-box;

}



/* Supprimer tous les contours de focus */

*:focus {

  outline: none !important;

}



button:focus,

input:focus,

select:focus,

textarea:focus,

div[contenteditable]:focus {

  outline: none !important;

  box-shadow: none !important;

}



.app {

  min-height: 100vh;

  background: #f0f2f5;

  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;

}







/* Barre de recherche personnalisée */
.search-container-custom {
  position: relative;
  margin: 1rem 0;
  display: block;
}



.search-input-custom {

  width: 100%;

  padding: 10px 16px 10px 40px;

  border: 2px solid #e4e6ea;

  border-radius: 20px;

  background: #f0f2f5;

  font-size: 14px;

  transition: all 0.2s ease;

}



.search-input-custom:focus {

  background: white;

  box-shadow: 0 0 0 2px #1877f2;

  outline: none;

}



.search-icon-custom {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  width: 18px;
  height: 18px;
  color: #65676b;
  pointer-events: none;
}



/* Navigation links */

.nav-links {

  display: flex;

  gap: 2rem;

}



.desktop-nav {

  display: flex;

}



.mobile-nav {

  display: none;

  flex-direction: column;

  gap: 0;

  padding: 0;

  flex: 1;

}



.nav-link {

  display: flex;

  align-items: center;

  gap: 0.5rem;

  padding: 0.75rem 1rem;

  color: #374151;

  text-decoration: none;

  font-size: 16px;

  font-weight: 500;

  border-radius: 8px;

  transition: all 0.2s;

  border-bottom: 2px solid transparent;

}



.nav-link:hover {

  background: #f3f4f6;

  color: #1f2937;

}



.nav-link.active {

  color: #ef4444;

  border-bottom-color: #ef4444;

  background: #fef2f2;

}



.nav-icon {

  font-size: 18px;

}



/* Header actions */

.header-actions {

  display: flex;

  align-items: center;

  gap: 1rem;

}



/* Mobile menu button */

.mobile-menu-btn {

  display: none;

  flex-direction: column;

  gap: 4px;

  background: none;

  border: none;

  cursor: pointer;

  padding: 8px;

  z-index: 2001;

}



.mobile-menu-btn span {

  width: 20px;

  height: 2px;

  background: #1f2937;

  transition: all 0.3s ease;

  transform-origin: center;

}



/* Navigation header pour mobile */

.nav-header {

  display: none;

  justify-content: space-between;

  align-items: center;

  padding: 1rem;

  border-bottom: 1px solid rgba(0, 0, 0, 0.1);

}



.nav-close {

  background: none;

  border: none;

  font-size: 24px;

  cursor: pointer;

  padding: 0.5rem;

  color: #6b7280;

  transition: color 0.2s;

}



.nav-close:hover {

  color: #1f2937;

}



/* Mobile menu overlay */

.mobile-menu-overlay {

  display: none;

  position: fixed;

  top: 0;

  left: 0;

  right: 0;

  bottom: 0;

  background: rgba(0, 0, 0, 0.5);

  z-index: 1998;

  opacity: 0;

  visibility: hidden;

  transition: all 0.3s ease;

}



.mobile-menu-overlay.active {

  opacity: 1;

  visibility: visible;

}



/* Responsive styles */

@media (max-width: 768px) {

  .header {

    padding: 1rem;

  }



  .header-content {

    position: relative;

  }



  /* Show mobile menu button */

  .mobile-menu-btn {

    display: flex;

  }



  /* Hide desktop navigation */

  .desktop-nav {

    display: none;

  }

  

  /* Show mobile navigation with slide-in animation */

  .nav {

    position: fixed;

    top: 0;

    left: 0;

    height: 100vh;

    width: 320px;

    max-width: 85vw;

    background: white;

    z-index: 1999;

    flex-direction: column;

    gap: 0;

    transform: translateX(-100%);

    transition: transform 0.3s ease;

    box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);

    overflow-y: auto;

  }



  .nav.mobile-open {

    transform: translateX(0);

  }



  .nav-header {

    display: flex;

  }



  .nav-profile {

    display: flex;

  }



  .search-container-custom {

    display: block;

  }



  .mobile-nav {

    display: flex;

    gap: 0;

    padding: 0;

    flex: 1;

  }

  

  .mobile-nav .nav-link {

    padding: 1rem 1.5rem;

    border-left: 3px solid transparent;

    border-bottom: none;

    display: flex;

    align-items: center;

    gap: 1rem;

    font-size: 16px;

    transition: all 0.2s;

  }

  

  .mobile-nav .nav-link.active {

    border-left-color: #ef4444;

    background: #fef2f2;

    color: #ef4444;

  }



  .mobile-nav .nav-link:hover {

    background: #f9fafb;

  }



  /* Ajuster le contenu principal pour l'en-tÃªte fixe */

  .main-container-centered {

    margin-top: 80px;

  }

}



.search-container {

  position: relative;

  flex: 1;

  max-width: 320px;

  margin: 0 16px;

}



.search-input {

  width: 100%;

  padding: 10px 16px 10px 40px;

  border: none;

  border-radius: 20px;

  background: #f0f2f5;

  outline: none;

  font-size: 15px;

  transition: all 0.2s ease;

}



.search-input:focus {

  background: white;

  box-shadow: 0 0 0 2px #1877f2;

}



.search-icon {

  position: absolute;

  left: 12px;

  top: 50%;

  transform: translateY(-50%);

  width: 16px;

  height: 16px;

  color: #65676b;

}



.header-right {

  display: flex;

  align-items: center;

  gap: 8px;

}



.header-btn {

  position: relative;

  width: 40px;

  height: 40px;

  border: none;

  border-radius: 50%;

  background: #e4e6ea;

  cursor: pointer;

  display: flex;

  align-items: center;

  justify-content: center;

  transition: background-color 0.2s;

}



.header-btn:hover {

  background: #d8dadf;

}



.header-btn svg {

  width: 20px;

  height: 20px;

  color: #050505;

}



.notification-badge {

  position: absolute;

  top: -2px;

  right: -2px;

  background: #e41e3f;

  color: white;

  border-radius: 50%;

  width: 18px;

  height: 18px;

  font-size: 11px;

  font-weight: 600;

  display: flex;

  align-items: center;

  justify-content: center;

}



.profile-dropdown {

  position: relative;

}



.profile-btn {

  width: 36px;

  height: 36px;

  border: none;

  border-radius: 50%;

  background: none;

  cursor: pointer;

  padding: 2px;

  display: flex;

  align-items: center;

  justify-content: center;

}



.header-profile-img {

  width: 32px;

  height: 32px;

  border-radius: 50%;

  border: 1px solid #e4e6ea;

  object-fit: cover;

}



.profile-menu {

  position: absolute;

  top: 100%;

  right: 0;

  background: white;

  border-radius: 8px;

  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);

  padding: 8px 0;

  min-width: 180px;

  z-index: 1001;

}



.profile-menu-item {

  padding: 12px 16px;

  cursor: pointer;

  transition: background-color 0.2s;

  font-weight: 500;

  display: flex;

  align-items: center;

  gap: 12px;

}



.profile-menu-item svg {

  width: 16px;

  height: 16px;

}



.profile-menu-item:hover {

  background: #f0f2f5;

}



.profile-menu-item.logout {

  color: #e41e3f;

  border-top: 1px solid #e4e6ea;

}

/* Contenu principal */
.main-container-centered {
  max-width: 680px;
  margin: 0 auto;
  padding: 20px;
  background: #f0f2f5;
  min-height: 100vh;
}

.main-container {
  max-width: 100%;
  margin: 0;
  padding: 20px;
  background: #f0f2f5;
  min-height: 100vh;
}

.create-post-card {

  background: white;

  border-radius: 12px;

  padding: 16px;

  margin-bottom: 20px;

  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);

}



.create-post-header {

  display: flex;

  align-items: center;

  gap: 12px;

  margin-bottom: 12px;

}



.create-post-avatar {

  width: 40px;

  height: 40px;

  border-radius: 50%;

}

/* Zone de création de post style Facebook */
.create-post-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
  padding: 12px 16px;
  margin-bottom: 20px;
}

.create-post-header {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 12px;
}

.create-post-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
  flex-shrink: 0;
}

.create-post-input-wrapper {
  flex: 1;
}

.create-post-input {
  background: #f0f2f5;
  border: 1px solid #e4e6ea;
  border-radius: 24px;
  padding: 10px 16px;
  color: #65676b;
  cursor: pointer;
  transition: all 0.2s;
  width: 100%;
  font-size: 15px;
  outline: none;
  min-height: 40px;
  display: flex;
  align-items: center;
}

.create-post-input:hover {
  background: #e4e6ea;
  border-color: #d8dadf;
}

.create-post-input:focus {
  background: white;
  border-color: #1877f2;
  box-shadow: 0 0 0 2px rgba(24, 119, 242, 0.1);
}

.create-post-actions {
  display: flex;
  gap: 8px;
  padding-top: 8px;
  border-top: 1px solid #e4e6ea;
}

.action-btn {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 8px 12px;
  border: none;
  background: transparent;
  border-radius: 8px;
  color: #65676b;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.action-btn:hover {
  background: #f0f2f5;
  color: #1877f2;
}

.action-btn svg {
  flex-shrink: 0;
}

.photo-btn:hover {
  color: #45bd62;
}

.video-btn:hover {
  color: #f02849;
}

.feeling-btn:hover {
  color: #f7b928;
}

/* ... */

.posts-feed {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.post-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  text-align: left;
}

.post-header {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 16px;
  position: relative;
}

.post-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
}

.post-author-info {
  flex: 1;
  text-align: left !important;
}

.post-author-name {
  font-size: 15px;
  font-weight: 600;
  color: #050505;
  margin-bottom: 2px;
  text-align: left !important;
}

.post-author-role {
  font-size: 12px;
  color: #65676b;
  margin-bottom: 2px;
  text-align: left !important;
}

.post-timestamp {
  font-size: 12px;
  color: #65676b;
  text-align: left !important;
}

.post-menu {
  position: relative;
}

.post-menu-btn {
  background: none;
  border: none;
  font-size: 20px;
  color: #65676b;
  cursor: pointer;
  padding: 8px;
  border-radius: 50%;
  transition: background-color 0.2s;
}

.post-menu-btn:hover {
  background: #f0f2f5;
}

.post-menu-dropdown {
  position: absolute;
  top: 100%;
  right: 0;
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
  min-width: 180px;
  z-index: 1000;
  margin-top: 8px;
  overflow: hidden;
  padding: 8px 0;
}

.menu-item {
  padding: 12px 16px;
  cursor: pointer;
  transition: background-color 0.2s;
  display: flex;
  align-items: center;
  gap: 12px;
  font-size: 14px;
  font-weight: 500;
  color: #050505;
}

.menu-item svg {
  width: 16px;
  height: 16px;
}

.menu-item:hover {
  background: #f0f2f5;
}

.menu-item.delete {
  color: #e41e3f;
}

.menu-item.delete:hover {
  background: rgba(228, 30, 63, 0.1);
}

.post-content {
  padding: 0 16px 16px;
  text-align: left;
}

.post-text {
  font-size: 15px;
  line-height: 1.4;
  color: #050505;
  margin-bottom: 12px;
}

.post-image {
  width: 100%;
  border-radius: 8px;
}

.post-stats {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 16px 8px;
  font-size: 13px;
  color: #65676b;
  text-align: left !important;
}

.reactions-summary {
  display: flex;
  align-items: center;
  gap: 6px;
  text-align: left !important;
}

.reaction-icons {
  display: flex;
  gap: -2px;
}

.reaction-icon {
  font-size: 16px;
  margin-left: -2px;
}

.engagement-stats {
  display: flex;
  gap: 16px;
  text-align: left !important;
}

.post-actions {
  display: flex;
  border-top: 1px solid #e4e6ea;
}

.post-action-btn {
  flex: 1;
  padding: 12px;
  border: none;
  background: none;
  cursor: pointer;
  font-weight: 600;
  color: #65676b;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  transition: background-color 0.2s;
}

.post-action-btn:hover {
  background: #f0f2f5;
}

.post-action-btn.liked {
  color: #1877f2;
  background: transparent;
}

.post-action-btn.liked .action-icon {
  color: #1877f2;
}

/* Menu des réactions */

.reaction-container {
  position: relative;
}

.reactions-menu {
  position: absolute;
  bottom: 100%;

  left: 0;

  background: white;

  border: 1px solid #e4e6ea;

  border-radius: 20px;

  padding: 8px;

  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.15);

  display: flex;

  gap: 4px;

  z-index: 1000;

  margin-bottom: 8px;





}
.reaction-option {

  background: none;

  border: none;

  font-size: 24px;

  padding: 8px;

  border-radius: 50%;

  cursor: pointer;

  transition: transform 0.2s ease;

  display: flex;

  align-items: center;

  justify-content: center;

  width: 40px;

  height: 40px;





}
.reaction-option:hover {

  transform: scale(1.2);

  background: #f0f2f5;





}
/* Commentaires */

.comments-section {

  border-top: 1px solid #e4e6ea;

  padding: 16px;

  background: #f8f9fa;

  text-align: left !important;

}

.comments-list {

  text-align: left !important;

}

.comments-load-more {
  display: flex;
  justify-content: flex-end;
  margin-top: 8px;
}

.load-more-link {
  font-size: 12px;
  color: #3b82f6;
  text-decoration: none;
  padding: 4px 6px;
  border-radius: 4px;
}

.load-more-link:hover {
  text-decoration: underline;
}

/* Styles détaillés des commentaires */
.comment-input-container {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 10px;
}

.comment-input {
  flex: 1;
  height: 36px;
  padding: 8px 12px;
  border: 1px solid #e4e6ea;
  border-radius: 18px;
  background: #fff;
  font-size: 14px;
  outline: none;
}

.comment-input:focus {
  border-color: #cfd2d6;
  box-shadow: 0 0 0 2px rgba(24, 119, 242, 0.08);
}

.comment {
  display: flex;
  align-items: flex-start;
  gap: 8px;
  margin: 8px 0;
}

.comment-avatar,
.reply-avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  object-fit: cover;
  flex-shrink: 0;
}

.comment-content {
  flex: 1;
}

.comment-bubble,
.reply-bubble {
  background: #f0f2f5;
  border-radius: 12px;
  padding: 8px 12px;
  text-align: left !important;
}

.comment-author {
  font-size: 13px;
  font-weight: 600;
  color: #050505;
  margin-bottom: 2px;
}

.comment-text {
  font-size: 14px;
  color: #050505;
  margin: 0;
}

.comment-actions {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-top: 4px;
}

.comment-action,
.reply-action {
  background: none;
  border: none;
  color: #65676b;
  font-size: 12px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 4px;
}

.like-icon {
  font-size: 14px;
}

.comment-action.liked .like-icon,
.reply-action.liked .like-icon {
  color: #1877f2;
}

.replies-container,
.sub-replies-container {
  margin-left: 40px;
}

.reply {
  display: flex;
  align-items: flex-start;
  gap: 8px;
  margin-top: 6px;
}

/* Styles pour les modales - Overlay commun */
.publish-modal-overlay,
.delete-modal-overlay,
.edit-modal-overlay,
.location-modal-overlay,
.profile-info-modal-overlay {
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
  padding: 20px;
  overflow-y: auto;
}

/* Modale de publication */
.publish-modal {
  background: white;
  border-radius: 12px;
  width: 100%;
  max-width: 600px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
  display: flex;
  flex-direction: column;
}

/* Modale de suppression */
.delete-modal {
  background: white;
  border-radius: 12px;
  width: 100%;
  max-width: 500px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
  display: flex;
  flex-direction: column;
  animation: modalSlideIn 0.3s ease-out;
}

@keyframes modalSlideIn {
  from {
    transform: translateY(-20px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

.delete-modal-header {
  padding: 24px;
  border-bottom: 1px solid #e4e6ea;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 16px;
}

.delete-icon {
  width: 64px;
  height: 64px;
  border-radius: 50%;
  background: #fee2e2;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #ef4444;
}

.delete-icon svg {
  width: 32px;
  height: 32px;
}

.delete-modal-header h3 {
  font-size: 20px;
  font-weight: 600;
  color: #050505;
  margin: 0;
}

.delete-modal-body {
  padding: 24px;
}

.delete-modal-body p {
  color: #65676b;
  margin-bottom: 16px;
  text-align: center;
}

.post-preview {
  background: #f0f2f5;
  border-radius: 8px;
  padding: 12px;
  margin-top: 16px;
}

.preview-author {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 8px;
}

.preview-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
}

.preview-author h5 {
  margin: 0;
  font-size: 14px;
  font-weight: 600;
  color: #050505;
}

.preview-author p {
  margin: 0;
  font-size: 12px;
  color: #65676b;
}

.preview-content {
  font-size: 14px;
  color: #050505;
  line-height: 1.4;
}

.delete-modal-footer {
  padding: 16px 24px;
  border-top: 1px solid #e4e6ea;
  display: flex;
  gap: 12px;
  justify-content: flex-end;
}

.cancel-delete-btn,
.confirm-delete-btn {
  padding: 10px 20px;
  border-radius: 8px;
  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
  border: none;
  transition: all 0.2s;
}

.cancel-delete-btn {
  background: #f0f2f5;
  color: #050505;
}

.cancel-delete-btn:hover {
  background: #e4e6ea;
}

.confirm-delete-btn {
  background: #ef4444;
  color: white;
  display: flex;
  align-items: center;
  gap: 8px;
}

.confirm-delete-btn:hover {
  background: #dc2626;
}

.confirm-delete-btn svg {
  width: 18px;
  height: 18px;
}

/* Modale d'édition */
.edit-modal {
  background: white;
  border-radius: 12px;
  width: 100%;
  max-width: 600px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
  display: flex;
  flex-direction: column;
  animation: modalSlideIn 0.3s ease-out;
}

.edit-modal-header {
  padding: 20px;
  border-bottom: 1px solid #e4e6ea;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.edit-modal-header h3 {
  font-size: 20px;
  font-weight: 600;
  color: #050505;
  margin: 0;
}

.edit-modal-body {
  padding: 20px;
}

.edit-modal-footer {
  padding: 16px 20px;
  border-top: 1px solid #e4e6ea;
  display: flex;
  gap: 12px;
  justify-content: flex-end;
}

.cancel-edit-btn {
  padding: 10px 20px;
  border-radius: 8px;
  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
  border: none;
  background: #f0f2f5;
  color: #050505;
  transition: all 0.2s;
}

.cancel-edit-btn:hover {
  background: #e4e6ea;
}

/* Modale de localisation */
.location-modal {
  background: white;
  border-radius: 12px;
  width: 100%;
  max-width: 500px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
  display: flex;
  flex-direction: column;
  animation: modalSlideIn 0.3s ease-out;
}

.location-modal-header {
  padding: 20px;
  border-bottom: 1px solid #e4e6ea;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.location-modal-header h3 {
  font-size: 20px;
  font-weight: 600;
  color: #050505;
  margin: 0;
}

.location-modal-body {
  padding: 20px;
}

.location-modal-footer {
  padding: 16px 20px;
  border-top: 1px solid #e4e6ea;
  display: flex;
  gap: 12px;
  justify-content: flex-end;
}

/* Modale de profil */
.profile-info-modal {
  background: white;
  border-radius: 12px;
  width: 100%;
  max-width: 500px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
  display: flex;
  flex-direction: column;
  animation: modalSlideIn 0.3s ease-out;
}

.profile-info-header {
  padding: 20px;
  border-bottom: 1px solid #e4e6ea;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.profile-info-header h3 {
  font-size: 20px;
  font-weight: 600;
  color: #050505;
  margin: 0;
}

.profile-info-body {
  padding: 20px;
}

/* Bouton de fermeture commun pour toutes les modales */
.close-btn {
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
  transition: all 0.2s;
}

.close-btn:hover {
  background: #f0f2f5;
  color: #050505;
}

/* Styles pour la modale de publication moderne */
.modal-header {
  padding: 16px 20px;
  border-bottom: 1px solid #e4e6ea;
  display: flex;
  justify-content: space-between;
  align-items: center;
  position: sticky;
  top: 0;
  background: white;
  z-index: 10;
  border-radius: 12px 12px 0 0;
}

.modal-title {
  font-size: 20px;
  font-weight: 700;
  color: #050505;
  margin: 0;
}

.modal-user-info {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 20px;
  border-bottom: 1px solid #e4e6ea;
}

.modal-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
  flex-shrink: 0;
}

.modal-user-details {
  flex: 1;
}

.modal-user-details h4 {
  margin: 0 0 4px 0;
  font-size: 15px;
  font-weight: 600;
  color: #050505;
}

.privacy-select {
  border: none;
  background: transparent;
  font-size: 13px;
  color: #65676b;
  cursor: pointer;
  padding: 4px 8px;
  border-radius: 6px;
  transition: background 0.2s;
}

.privacy-select:hover {
  background: #f0f2f5;
}

.modal-editor-wrapper {
  padding: 16px 20px;
  min-height: 200px;
}

/* Zone de prévisualisation des médias */
.media-preview-section {
  padding: 16px 20px;
  border-top: 1px solid #e4e6ea;
  border-bottom: 1px solid #e4e6ea;
  background: #f8f9fa;
}

.media-preview-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
}

.media-preview-header h4 {
  margin: 0;
  font-size: 15px;
  font-weight: 600;
  color: #050505;
}

.clear-all-btn {
  background: none;
  border: none;
  color: #1877f2;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  padding: 4px 8px;
  border-radius: 6px;
  transition: background 0.2s;
}

.clear-all-btn:hover {
  background: #e4e6eb;
}

.media-preview-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 16px;
}

.media-preview-item {
  background: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.media-preview-container {
  position: relative;
  width: 100%;
  aspect-ratio: 16 / 9;
  overflow: hidden;
  background: #000;
}

.media-preview {
  width: 100%;
  height: 100%;
  object-fit: contain;
}

.media-preview video {
  width: 100%;
  height: 100%;
}

.remove-media-btn {
  position: absolute;
  top: 8px;
  right: 8px;
  background: rgba(0, 0, 0, 0.6);
  border: none;
  border-radius: 50%;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  color: white;
  transition: all 0.2s;
  z-index: 5;
}

.remove-media-btn:hover {
  background: rgba(0, 0, 0, 0.8);
  transform: scale(1.1);
}

.media-caption-section {
  padding: 12px;
}

.media-caption-input {
  width: 100%;
  border: 1px solid #e4e6ea;
  border-radius: 8px;
  padding: 8px 12px;
  font-size: 14px;
  font-family: inherit;
  resize: vertical;
  min-height: 60px;
  outline: none;
  transition: border-color 0.2s;
}

.media-caption-input:focus {
  border-color: #1877f2;
  box-shadow: 0 0 0 2px rgba(24, 119, 242, 0.1);
}

.caption-char-count {
  font-size: 12px;
  color: #65676b;
  text-align: right;
  margin-top: 4px;
}

/* Actions de la modale */
.modal-actions {
  display: flex;
  gap: 8px;
  padding: 12px 20px;
  border-top: 1px solid #e4e6ea;
  border-bottom: 1px solid #e4e6ea;
}

.modal-action-btn {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 10px 16px;
  border: none;
  background: transparent;
  border-radius: 8px;
  color: #65676b;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.modal-action-btn:hover {
  background: #f0f2f5;
}

.photo-action:hover {
  color: #45bd62;
}

.emoji-action:hover {
  color: #f7b928;
}

.location-action:hover {
  color: #1877f2;
}

.modal-action-btn svg {
  flex-shrink: 0;
}

/* Footer de la modale */
.modal-footer {
  padding: 12px 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-top: 1px solid #e4e6ea;
  position: sticky;
  bottom: 0;
  background: white;
  border-radius: 0 0 12px 12px;
}

.character-count {
  font-size: 13px;
  color: #65676b;
}

.publish-btn {
  background: #1877f2;
  color: white;
  border: none;
  border-radius: 8px;
  padding: 10px 24px;
  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  min-width: 100px;
}

.publish-btn:hover:not(:disabled) {
  background: #166fe5;
  transform: translateY(-1px);
  box-shadow: 0 2px 8px rgba(24, 119, 242, 0.3);
}

.publish-btn:disabled {
  background: #e4e6eb;
  color: #bcc0c4;
  cursor: not-allowed;
}

.publish-btn.publishing {
  background: #1877f2;
  opacity: 0.7;
  cursor: wait;
}

/* Sélecteur d'émojis */
.emoji-picker-container {
  padding: 16px 20px;
  border-top: 1px solid #e4e6ea;
  background: #f8f9fa;
  max-height: 200px;
  overflow-y: auto;
}

.emoji-grid {
  display: grid;
  grid-template-columns: repeat(10, 1fr);
  gap: 8px;
}

.emoji-btn {
  background: white;
  border: 1px solid #e4e6ea;
  border-radius: 8px;
  padding: 8px;
  font-size: 20px;
  cursor: pointer;
  transition: all 0.2s;
  aspect-ratio: 1;
  display: flex;
  align-items: center;
  justify-content: center;
}

.emoji-btn:hover {
  background: #f0f2f5;
  border-color: #1877f2;
  transform: scale(1.1);
}

/* Responsive */
@media (max-width: 768px) {
  .create-post-actions {
    flex-direction: column;
    gap: 4px;
  }
  
  .action-btn {
    justify-content: flex-start;
  }
  
  .media-preview-grid {
    grid-template-columns: 1fr;
  }
  
  .publish-modal {
    max-width: 100%;
    margin: 0;
    border-radius: 0;
    max-height: 100vh;
  }
  
  .modal-header {
    border-radius: 0;
  }
  
  .modal-footer {
    border-radius: 0;
  }
}

</style>
