<template>
  <div class="app">

    <div class="main-container">
      <!-- Ajout de l'overlay mobile et classes conditionnelles pour le sidebar -->
      <div v-if="showMobileMenu" class="mobile-overlay" @click="closeMobileMenu"></div>

      <!-- Main Content -->
      <main class="main-content">
        <div class="bibliotheque-container">
        <!-- Create Book Section (maintenant pour les livres) -->
        <div class="create-post-card">
        <div class="create-post-header">
            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1200px-User_icon_2.svg.png" alt="Profile" class="create-post-avatar">
            <div class="create-book-text">
            <span>Partagez un nouveau livre avec la communauté</span>
            </div>
        </div>
        <div class="create-post-actions">
            <button class="action-btn" @click="showAddBookModal = true">
            <svg class="action-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
            </svg>
            Ajouter un livre
            </button>
            <div class="book-count">
              <span>{{ books.length }} livres disponibles</span>
            </div>
        </div>
        </div>

        <!-- Posts Feed (maintenant Books Feed) -->
        <div class="posts-feed">
        <!-- Search and Filter Card -->
        <div class="post-card search-filter-card">
            <div class="search-filters-content">
            <div class="search-section">
                <div class="search-input-container">
                <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.35-4.35"></path>
                </svg>
                <input
                    v-model="searchQuery"
                    @input="searchBooks"
                    type="text"
                    placeholder="Rechercher un livre..."
                    class="search-input"
                />
                </div>
            </div>
            <div class="filter-section">
                <select
                v-model="selectedCategory"
                @change="filterBooks"
                class="category-select"
                >
                <option value="">Toutes les catégories</option>
                <option v-for="category in categories" :key="category" :value="category">
                    {{ category }}
                </option>
                </select>
            </div>
            </div>
        </div>

        <!-- Books Grid Container -->
        <div class="books-grid-container">
            <div
            v-for="book in filteredBooks"
            :key="book.id"
            @click="handleBookClick(book)"
            class="book-card"
            >
            <div class="book-image-container">
                <img
                :src="book.image_url || 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1200px-User_icon_2.svg.png'"
                :alt="book.titre"
                class="book-image"
                />
                <div class="availability-badge">
                <span
                    :class="[
                    'badge',
                    book.availability === 'online'
                        ? 'badge-online'
                        : 'badge-physical'
                    ]"
                >
                    {{ book.availability === 'online' ? 'En ligne' : 'Au siège' }}
                </span>
                </div>
            </div>

            <div class="book-info">
                <h3 class="book-title">{{ book.titre }}</h3>
                <p class="book-author">{{ book.auteur }}</p>
                <div class="book-footer">
                <span class="book-category">{{ book.category }}</span>
                <svg class="arrow-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                </div>
            </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-if="filteredBooks.length === 0" class="post-card empty-state">
            <svg class="empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            <h3>Aucun livre trouvé</h3>
            <p>Essayez de modifier vos critères de recherche.</p>
        </div>
        </div>

        <!-- Reservation Modal -->
        <div
        v-if="showModal"
        class="modal-overlay"
        @click="closeModal"
        >
        <div class="modal-content" @click.stop>
            <div class="modal-header">
            <h2>Réservation de livre</h2>
            <button @click="closeModal" class="close-btn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            </div>

            <div class="selected-book-info">
            <div class="book-preview">
                <img
                :src="selectedBook?.image_url || 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1200px-User_icon_2.svg.png'"
                :alt="selectedBook?.titre"
                class="book-preview-image"
                />
                <div class="book-preview-info">
                <h3>{{ selectedBook?.titre }}</h3>
                <p>{{ selectedBook?.auteur }}</p>
                </div>
            </div>
            </div>

            <form @submit.prevent="submitReservation" class="reservation-form">
            <div class="form-group">
                <label>Matricule</label>
                <input
                v-model="reservationForm.matricule"
                type="text"
                required
                class="form-input"
                />
            </div>

            <div class="form-group">
                <label>Nom complet</label>
                <input
                v-model="reservationForm.name"
                type="text"
                required
                class="form-input"
                />
            </div>

            <div class="form-group">
                <label>Contact</label>
                <input
                v-model="reservationForm.contact"
                type="text"
                required
                placeholder="Email ou téléphone"
                class="form-input"
                />
            </div>

            <div class="form-group">
                <label>Photo (optionnel)</label>
                <input
                @change="handlePhotoUpload"
                type="file"
                accept="image/*"
                class="form-input"
                />
            </div>

            <div class="form-group">
                <label>Type de consultation</label>
                <select
                v-model="reservationForm.consultationType"
                required
                class="form-select"
                >
                <option value="">Sélectionner une option</option>
                <option value="on-site">Lecture au siège</option>
                <option value="borrow">Emprunt pour lecture externe</option>
                </select>
            </div>

            <div v-if="reservationForm.consultationType === 'on-site'" class="form-group">
                <label>Date et heure de lecture</label>
                <div class="date-time-inputs">
                <input
                    v-model="reservationForm.readingDate"
                    type="date"
                    required
                    class="form-input"
                />
                <input
                    v-model="reservationForm.readingTime"
                    type="time"
                    required
                    class="form-input"
                />
                </div>
            </div>

            <div v-if="reservationForm.consultationType === 'borrow'" class="form-group">
                <label>Période d'emprunt</label>
                <div class="date-range-inputs">
                <div class="date-input-group">
                    <label class="date-label">Date de début</label>
                    <input
                    v-model="reservationForm.startDate"
                    type="date"
                    required
                    class="form-input"
                    />
                </div>
                <div class="date-input-group">
                    <label class="date-label">Date de fin</label>
                    <input
                    v-model="reservationForm.endDate"
                    type="date"
                    required
                    class="form-input"
                    />
                </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="button" @click="closeModal" class="cancel-btn">
                Annuler
                </button>
                <button
                type="submit"
                :disabled="isSubmitting"
                class="submit-btn"
                >
                {{ isSubmitting ? 'Envoi...' : 'Confirmer la réservation' }}
                </button>
            </div>
            </form>
        </div>
        </div>

        <!-- Add Book Modal -->
        <div
        v-if="showAddBookModal"
        class="modal-overlay"
        @click="closeAddBookModal"
        >
        <div class="modal-content" @click.stop>
            <div class="modal-header">
            <h2>Ajouter un livre</h2>
            <button @click="closeAddBookModal" class="close-btn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            </div>

            <form @submit.prevent="submitAddBook" class="add-book-form">
            <div class="form-group">
                <label>Matricule</label>
                <input
                v-model="addBookForm.matricule"
                type="text"
                required
                class="form-input"
                />
            </div>

            <div class="form-group">
                <label>Nom</label>
                <input
                v-model="addBookForm.name"
                type="text"
                required
                class="form-input"
                />
            </div>

            <div class="form-group">
                <label>Type de livre</label>
                <select
                v-model="addBookForm.type"
                required
                class="form-select"
                >
                <option value="">Sélectionner un type</option>
                <option value="online">En ligne</option>
                <option value="physical">Physique</option>
                </select>
            </div>

            <div class="form-group">
                <label>Titre du livre</label>
                <input
                v-model="addBookForm.title"
                type="text"
                required
                class="form-input"
                />
            </div>

            <div v-if="addBookForm.type === 'online'" class="online-book-fields">
                <div class="form-group">
                <label>Nom de l'auteur</label>
                <input
                    v-model="addBookForm.author"
                    type="text"
                    required
                    class="form-input"
                />
                </div>

                <div class="form-group">
                <label>Catégorie</label>
                <select
                    v-model="addBookForm.category"
                    required
                    class="form-select"
                >
                    <option value="">Sélectionner une catégorie</option>
                    <option value="Hadith">Hadith</option>
                    <option value="Tafsir">Tafsir</option>
                    <option value="Jurisprudence">Jurisprudence</option>
                    <option value="Biographie">Biographie</option>
                    <option value="Spiritualité">Spiritualité</option>
                    <option value="Aqidah">Aqidah</option>
                </select>
                </div>

                <div class="form-group">
                <label>Image du livre</label>
                <input
                    @change="handleBookImageUpload"
                    type="file"
                    accept="image/*"
                    required
                    class="form-input"
                />
                <div v-if="isUploadingImage" class="upload-progress">
                    <div class="spinner"></div>
                    <span>Téléchargement en cours...</span>
                </div>
                <div v-if="imagePreview && !isUploadingImage" class="upload-success">
                    <img :src="imagePreview" alt="Aperçu" class="preview-image" />
                    <span>Image téléchargée avec succès</span>
                </div>
                </div>

                <div class="form-group">
                <label>Fichier PDF</label>
                <input
                    @change="handlePdfUpload"
                    type="file"
                    accept=".pdf"
                    required
                    class="form-input"
                />
                <div v-if="isUploadingPdf" class="upload-progress">
                    <div class="spinner"></div>
                    <span>Téléchargement du PDF en cours...</span>
                    <div class="progress-bar">
                    <div class="progress-fill" :style="{ width: pdfUploadProgress + '%' }"></div>
                    </div>
                    <span>{{ pdfUploadProgress }}%</span>
                </div>
                <div v-if="pdfPreview && !isUploadingPdf" class="upload-success">
                    <div class="pdf-icon">📄</div>
                    <div>
                    <p>{{ pdfPreview.name }}</p>
                    <p>{{ pdfPreview.size }} - Téléchargé avec succès</p>
                    </div>
                </div>
                </div>
            </div>

            <div v-if="addBookForm.type === 'physical'" class="physical-book-fields">
                <div class="form-group">
                <label>Image du livre (optionnel)</label>
                <input
                    @change="handleBookImageUpload"
                    type="file"
                    accept="image/*"
                    class="form-input"
                />
                <p class="help-text">Vous pouvez ajouter une image du livre si vous le souhaitez</p>
                <div v-if="isUploadingImage" class="upload-progress">
                    <div class="spinner"></div>
                    <span>Téléchargement en cours...</span>
                </div>
                <div v-if="imagePreview && !isUploadingImage" class="upload-success">
                    <img :src="imagePreview" alt="Aperçu" class="preview-image" />
                    <span>Image téléchargée avec succès</span>
                </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="button" @click="closeAddBookModal" class="cancel-btn">
                Annuler
                </button>
                <button
                v-if="canSubmitBook"
                type="submit"
                :disabled="isAddingBook"
                class="submit-btn"
                >
                {{ isAddingBook ? 'Ajout...' : 'Ajouter le livre' }}
                </button>
            </div>
            </form>
        </div>
        </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, reactive } from 'vue'
import { uploadToCloudinary } from '../../utils/cloudinary.js'

const API_BASE_URL = 'https://sogetrag.com/api/bibliotheque.php'

const searchQuery = ref('')
const activeSection = ref('bibliotheque') // Changed default to bibliotheque
const newPostText = ref('')
// Ajout de l'état pour le menu mobile
const showMobileMenu = ref(false)
const selectedCategory = ref('')
const showModal = ref(false)
const showAddBookModal = ref(false)
const selectedBook = ref(null)
const isSubmitting = ref(false)
const isAddingBook = ref(false)

const uploadProgress = ref(0)
const uploadError = ref('')
const imagePreview = ref(null)
const pdfPreview = ref(null)
const isUploadingImage = ref(false)
const isUploadingPdf = ref(false)
const pdfUploadProgress = ref(0)

const books = ref([])
const categories = ref([])


const reservationForm = ref({
  matricule: '',
  name: '',
  contact: '',
  photo: null,
  consultationType: '',
  readingDate: '',
  readingTime: '',
  startDate: '',
  endDate: ''
})

const addBookForm = ref({
  matricule: '',
  name: '',
  type: '',
  title: '',
  author: '',
  category: '',
  image: null,
  pdf: null,
  imageUrl: '',
  pdfUrl: ''
})


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

// const categories = ref(['Hadith', 'Tafsir', 'Jurisprudence', 'Biographie', 'Spiritualité', 'Aqidah'])

const loadBooks = async () => {
  try {
    const response = await fetch(`${API_BASE_URL}?action=books`)
    const data = await response.json()
    if (data.success) {
      books.value = data.data
    }
  } catch (error) {
    console.error('Error loading books:', error)
  }
}

const loadCategories = async () => {
  try {
    const response = await fetch(`${API_BASE_URL}?action=categories`)
    const data = await response.json()
    if (data.success) {
      categories.value = data.data.map(cat => cat.nom_categorie)
    }
  } catch (error) {
    console.error('Error loading categories:', error)
  }
}

const searchBooks = async () => {
  if (!searchQuery.value.trim()) {
    await loadBooks()
    return
  }

  try {
    const response = await fetch(`${API_BASE_URL}?action=search_books&q=${encodeURIComponent(searchQuery.value)}`)
    const data = await response.json()
    if (data.success) {
      books.value = data.data
    }
  } catch (error) {
    console.error('Error searching books:', error)
  }
}

const filterBooks = async () => {
  try {
    let url = `${API_BASE_URL}?action=books`
    if (selectedCategory.value) {
      const categoryData = await fetch(`${API_BASE_URL}?action=categories`)
      const catResponse = await categoryData.json()
      if (catResponse.success) {
        const category = catResponse.data.find(cat => cat.nom_categorie === selectedCategory.value)
        if (category) {
          url += `&category=${category.id}`
        }
      }
    }

    const response = await fetch(url)
    const data = await response.json()
    if (data.success) {
      books.value = data.data
    }
  } catch (error) {
    console.error('Error filtering books:', error)
  }
}

// Filtered books computed property
const filteredBooks = computed(() => {
  let filtered = books.value

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(book =>
      book.titre.toLowerCase().includes(query) ||
      book.auteur.toLowerCase().includes(query) ||
      book.category.toLowerCase().includes(query)
    )
  }

  if (selectedCategory.value) {
    filtered = filtered.filter(book => book.category === selectedCategory.value)
  }

  return filtered
})

const handleBookClick = (book) => {
  selectedBook.value = book

  if (book.availability === 'online') {
    console.log('Redirecting to online book:', book.titre)
    // Here we could open the online book
  } else {
    showModal.value = true
    const today = new Date()
    const nextWeek = new Date(today.getTime() + 7 * 24 * 60 * 60 * 1000)
    reservationForm.value.readingDate = today.toISOString().split('T')[0]
    reservationForm.value.startDate = today.toISOString().split('T')[0]
    reservationForm.value.endDate = nextWeek.toISOString().split('T')[0]
  }
}

const closeModal = () => {
  showModal.value = false
  selectedBook.value = null
  resetReservationForm()
}

const closeAddBookModal = () => {
  showAddBookModal.value = false
  resetAddBookForm()
}

const resetReservationForm = () => {
  reservationForm.value = {
    matricule: '',
    name: '',
    contact: '',
    photo: null,
    consultationType: '',
    readingDate: '',
    readingTime: '',
    startDate: '',
    endDate: ''
  }
}

const resetAddBookForm = () => {
  addBookForm.value = {
    matricule: '',
    name: '',
    type: '',
    title: '',
    author: '',
    category: '',
    image: null,
    pdf: null,
    imageUrl: '',
    pdfUrl: ''
  }
  imagePreview.value = null
  pdfPreview.value = null
  uploadError.value = ''
  uploadProgress.value = 0
  pdfUploadProgress.value = 0
}

const handlePhotoUpload = (event) => {
  const file = event.target.files[0]
  if (file) {
    reservationForm.value.photo = file
  }
}

const handleBookImageUpload = async (event) => {
  const file = event.target.files[0]
  if (file) {
    isUploadingImage.value = true
    uploadError.value = ''

    const reader = new FileReader()
    reader.onload = (e) => {
      imagePreview.value = e.target.result
    }
    reader.readAsDataURL(file)

    const result = await uploadToCloudinary(file)

    if (result.success) {
      addBookForm.value.image = file
      addBookForm.value.imageUrl = result.url
      console.log('Image uploaded successfully:', result.url)
    } else {
      uploadError.value = result.error
      imagePreview.value = null
    }

    isUploadingImage.value = false
  }
}

const handlePdfUpload = async (event) => {
  const file = event.target.files[0]
  if (file) {
    isUploadingPdf.value = true
    uploadError.value = ''
    pdfUploadProgress.value = 0

    pdfPreview.value = {
      name: file.name,
      size: (file.size / 1024 / 1024).toFixed(2) + ' MB'
    }

    const progressInterval = setInterval(() => {
      if (pdfUploadProgress.value < 90) {
        pdfUploadProgress.value += Math.random() * 15
      }
    }, 200)

    const result = await uploadToCloudinary(file)

    clearInterval(progressInterval)
    pdfUploadProgress.value = 100

    if (result.success) {
      addBookForm.value.pdf = file
      addBookForm.value.pdfUrl = result.url
      console.log('PDF uploaded successfully:', result.url)
    } else {
      uploadError.value = result.error
      pdfPreview.value = null
      pdfUploadProgress.value = 0
    }

    isUploadingPdf.value = false
  }
}

const submitReservation = async () => {
  isSubmitting.value = true

  try {
    const reservationData = {
      book_id: selectedBook.value.id,
      user_matricule: reservationForm.value.matricule,
      user_nom: reservationForm.value.name.split(' ')[1] || reservationForm.value.name,
      user_prenom: reservationForm.value.name.split(' ')[0],
      user_contact: reservationForm.value.contact,
      user_photo: reservationForm.value.photo ? 'uploaded' : null,
      type_reservation: reservationForm.value.consultationType === 'on-site' ? 'lecture_siege' : 'emprunt_externe',
      date_debut: reservationForm.value.consultationType === 'on-site' ?
        reservationForm.value.readingDate : reservationForm.value.startDate,
      heure_debut: reservationForm.value.consultationType === 'on-site' ?
        reservationForm.value.readingTime : null,
      date_fin: reservationForm.value.consultationType === 'borrow' ?
        reservationForm.value.endDate : null,
      periode_emprunt: reservationForm.value.consultationType === 'borrow' ?
        `Du ${reservationForm.value.startDate} au ${reservationForm.value.endDate}` : null
    }

    const response = await fetch(API_BASE_URL, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        action: 'create_reservation',
        ...reservationData
      })
    })

    const data = await response.json()

    if (data.success) {
      alert('Réservation confirmée ! Vous recevrez un email de confirmation.')
      closeModal()
    } else {
      alert('Erreur lors de la réservation: ' + data.error)
    }
  } catch (error) {
    console.error('Error submitting reservation:', error)
    alert('Erreur lors de la réservation')
  } finally {
    isSubmitting.value = false
  }
}

const submitAddBook = async () => {
  isAddingBook.value = true

  try {
    const bookData = {
      titre: addBookForm.value.title,
      auteur: addBookForm.value.author || 'Auteur inconnu',
      ajout_par_matricule: addBookForm.value.matricule,
      ajout_par_nom: addBookForm.value.name,
      disponibilite: addBookForm.value.type === 'online' ? 'online' : 'headquarters',
      category_id: null,
      description: `Livre ajouté par ${addBookForm.value.name}`,
      image_url: addBookForm.value.imageUrl || null,
      pdf_url: addBookForm.value.pdfUrl || null
    }

    if (addBookForm.value.type === 'online' && addBookForm.value.category) {
      const categoryResponse = await fetch(`${API_BASE_URL}?action=categories`)
      const catData = await categoryResponse.json()
      if (catData.success) {
        const category = catData.data.find(cat => cat.nom_categorie === addBookForm.value.category)
        if (category) {
          bookData.category_id = category.id
        }
      }
    }

    const response = await fetch(`${API_BASE_URL}?action=add_book`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        action: 'add_book',
        ...bookData
      })
    })

    const data = await response.json()

    if (data.success) {
      alert('Livre ajouté avec succès!')
      closeAddBookModal()
      await loadBooks()
    } else {
      alert('Erreur lors de l\'ajout du livre: ' + data.error)
    }
  } catch (error) {
    console.error('Error adding book:', error)
    alert('Erreur lors de l\'ajout du livre')
  } finally {
    isAddingBook.value = false
  }
}

const canSubmitBook = computed(() => {
  if (addBookForm.value.type === 'online') {
    return !isUploadingImage.value &&
           !isUploadingPdf.value &&
           addBookForm.value.imageUrl &&
           addBookForm.value.pdfUrl &&
           addBookForm.value.title &&
           addBookForm.value.author &&
           addBookForm.value.category
  } else if (addBookForm.value.type === 'physical') {
    return addBookForm.value.title &&
           !isUploadingImage.value
  }
  return false
})

onMounted(async () => {
  await loadBooks()
  await loadCategories()
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
  max-width: 1250px;
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

.bibliotheque-container {
  max-width: 1200px;
  margin: 0 auto;
}

/* Create Post Card (maintenant pour les livres) */
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

.create-book-text {
  flex: 1;
  padding: 12px 16px;
  background: #f9fafb;
  border: 1px solid #e5e7eb;
  border-radius: 24px;
  color: #6b7280;
  font-size: 16px;
}

.create-post-actions {
  display: flex;
  gap: 12px;
  align-items: center;
}

.action-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 16px;
  background: transparent;
  border: none;
  border-radius: 8px;
  color: #6b7280;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.action-btn:hover {
  background: #f0fdf4;
  color: #4ade80;
}

.action-icon {
  width: 20px;
  height: 20px;
}

/* Posts Feed (maintenant Books Feed) */
.posts-feed {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

/* Search Filter Card */
.search-filter-card {
  padding: 20px !important;
}

.search-filters-content {
  display: flex;
  gap: 16px;
  align-items: center;
}

.search-section {
  flex: 1;
}

.search-input-container {
  position: relative;
  display: flex;
  align-items: center;
}

.search-icon {
  position: absolute;
  left: 12px;
  width: 20px;
  height: 20px;
  color: #9ca3af;
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

.filter-section {
  min-width: 200px;
}

.category-select {
  width: 100%;
  padding: 12px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 14px;
  color: #1f2937;
  background: white;
}

/* Books Grid Container */
.books-grid-container {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 20px;
  padding: 20px;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.book-card {
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  overflow: hidden;
  cursor: pointer;
  transition: all 0.2s;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.book-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  border-color: #4ade80;
}

.book-image-container {
  position: relative;
  aspect-ratio: 3/4;
  background: linear-gradient(135deg, #f0fdf4, #dcfce7);
  overflow: hidden;
}

.book-image {
  width: 100%;
  height: 100%;
  object-fit: contain;
  transition: transform 0.2s;
}

.book-card:hover .book-image {
  transform: scale(1.05);
}

.availability-badge {
  position: absolute;
  top: 8px;
  right: 8px;
}

.badge {
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 500;
}

.badge-online {
  background: #dcfce7;
  color: #166534;
}

.badge-physical {
  background: #fef3c7;
  color: #92400e;
}

.book-info {
  padding: 16px;
}

.book-title {
  font-size: 14px;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 8px;
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.book-author {
  font-size: 12px;
  color: #6b7280;
  margin-bottom: 12px;
}

.book-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.book-category {
  font-size: 10px;
  padding: 2px 8px;
  background: #f0fdf4;
  color: #166534;
  border-radius: 12px;
}

.arrow-icon {
  width: 16px;
  height: 16px;
  color: #9ca3af;
  transition: color 0.2s;
}

.book-card:hover .arrow-icon {
  color: #4ade80;
}

/* Post Card (pour empty state) */
.post-card {
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 60px 20px;
}

.empty-icon {
  width: 64px;
  height: 64px;
  color: #d1d5db;
  margin: 0 auto 16px;
}

.empty-state h3 {
  font-size: 18px;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 8px;
}

.empty-state p {
  color: #6b7280;
}

/* Modal Styles (cohérents avec le thème vert) */
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
  padding: 20px;
  z-index: 1000;
}

.modal-content {
  background: white;
  border-radius: 12px;
  max-width: 500px;
  width: 100%;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 20px 24px;
  border-bottom: 1px solid #e5e7eb;
}

.modal-header h2 {
  font-size: 18px;
  font-weight: 600;
  color: #1f2937;
}

.close-btn {
  background: transparent;
  border: none;
  color: #9ca3af;
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
  transition: color 0.2s;
}

.close-btn:hover {
  color: #6b7280;
}

.close-btn svg {
  width: 20px;
  height: 20px;
}

.selected-book-info {
  padding: 20px 24px;
  border-bottom: 1px solid #e5e7eb;
}

.book-preview {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px;
  background: #f0fdf4;
  border-radius: 8px;
}

.book-preview-image {
  width: 48px;
  height: 64px;
  object-fit: cover;
  border-radius: 4px;
}

.book-preview-info h3 {
  font-size: 14px;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 4px;
}

.book-preview-info p {
  font-size: 12px;
  color: #6b7280;
}

/* Forms */
.reservation-form,
.add-book-form {
  padding: 20px 24px;
}

.form-group {
  margin-bottom: 16px;
}

.form-group label {
  display: block;
  font-size: 14px;
  font-weight: 500;
  color: #374151;
  margin-bottom: 6px;
}

.form-input,
.form-select {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 14px;
  color: #1f2937;
}

.form-input:focus,
.form-select:focus {
  outline: none;
  border-color: #4ade80;
  box-shadow: 0 0 0 3px rgba(74, 222, 128, 0.1);
}

.date-time-inputs,
.date-range-inputs {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

.date-input-group {
  display: flex;
  flex-direction: column;
}

.date-label {
  font-size: 12px;
  color: #6b7280;
  margin-bottom: 4px;
}

.online-book-fields,
.physical-book-fields {
  border-top: 1px solid #e5e7eb;
  padding-top: 16px;
  margin-top: 16px;
}

.form-actions {
  display: flex;
  gap: 12px;
  padding-top: 16px;
  border-top: 1px solid #e5e7eb;
}

.cancel-btn {
  flex: 1;
  padding: 10px 16px;
  border: 1px solid #d1d5db;
  background: white;
  color: #374151;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 500;
  transition: background-color 0.2s;
}

.cancel-btn:hover {
  background: #f9fafb;
}

.submit-btn {
  flex: 1;
  padding: 10px 16px;
  background: #4ade80;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 500;
  transition: background-color 0.2s;
}

.submit-btn:hover:not(:disabled) {
  background: #22c55e;
}

.submit-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Responsive */
@media (max-width: 768px) {
  .bibliotheque-container {
    padding: 0 15px;
  }

  .search-filters-content {
    flex-direction: column;
    gap: 12px;
  }

  .filter-section {
    min-width: auto;
    width: 100%;
  }

  .books-grid-container {
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 15px;
    padding: 15px;
  }

  .create-post-actions {
    flex-direction: column;
    gap: 8px;
  }

  .action-btn {
    width: 100%;
    justify-content: center;
  }

  .modal-content {
    margin: 20px;
    max-width: none;
  }

  .date-time-inputs,
  .date-range-inputs {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 480px) {
  .books-grid-container {
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 12px;
    padding: 12px;
  }

  .book-info {
    padding: 12px;
  }

  .book-title {
    font-size: 12px;
  }

  .book-author {
    font-size: 11px;
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

.bibliotheque-container {
  max-width: 1200px;
  margin: 0 auto;
}

/* Create Post Card (maintenant pour les livres) */
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

.create-book-text {
  flex: 1;
  padding: 12px 16px;
  background: #f9fafb;
  border: 1px solid #e5e7eb;
  border-radius: 24px;
  color: #6b7280;
  font-size: 16px;
}

.create-post-actions {
  display: flex;
  gap: 12px;
  align-items: center;
}

.action-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 16px;
  background: transparent;
  border: none;
  border-radius: 8px;
  color: #6b7280;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.action-btn:hover {
  background: #f0fdf4;
  color: #4ade80;
}

.action-icon {
  width: 20px;
  height: 20px;
}

/* Posts Feed (maintenant Books Feed) */
.posts-feed {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

/* Search Filter Card */
.search-filter-card {
  padding: 20px !important;
}

.search-filters-content {
  display: flex;
  gap: 16px;
  align-items: center;
}

.search-section {
  flex: 1;
}

.search-input-container {
  position: relative;
  display: flex;
  align-items: center;
}

.search-icon {
  position: absolute;
  left: 12px;
  width: 20px;
  height: 20px;
  color: #9ca3af;
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

.filter-section {
  min-width: 200px;
}

.category-select {
  width: 100%;
  padding: 12px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 14px;
  color: #1f2937;
  background: white;
}

/* Books Grid Container */
.books-grid-container {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 20px;
  padding: 20px;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.book-card {
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  overflow: hidden;
  cursor: pointer;
  transition: all 0.2s;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.book-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  border-color: #4ade80;
}

.book-image-container {
  position: relative;
  aspect-ratio: 3/4;
  background: linear-gradient(135deg, #f0fdf4, #dcfce7);
  overflow: hidden;
}

.book-image {
  width: 100%;
  height: 100%;
  object-fit: contain;
  transition: transform 0.2s;
}

.book-card:hover .book-image {
  transform: scale(1.05);
}

.availability-badge {
  position: absolute;
  top: 8px;
  right: 8px;
}

.badge {
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 500;
}

.badge-online {
  background: #dcfce7;
  color: #166534;
}

.badge-physical {
  background: #fef3c7;
  color: #92400e;
}

.book-info {
  padding: 16px;
}

.book-title {
  font-size: 14px;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 8px;
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.book-author {
  font-size: 12px;
  color: #6b7280;
  margin-bottom: 12px;
}

.book-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.book-category {
  font-size: 10px;
  padding: 2px 8px;
  background: #f0fdf4;
  color: #166534;
  border-radius: 12px;
}

.arrow-icon {
  width: 16px;
  height: 16px;
  color: #9ca3af;
  transition: color 0.2s;
}

.book-card:hover .arrow-icon {
  color: #4ade80;
}

/* Post Card (pour empty state) */
.post-card {
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 60px 20px;
}

.empty-icon {
  width: 64px;
  height: 64px;
  color: #d1d5db;
  margin: 0 auto 16px;
}

.empty-state h3 {
  font-size: 18px;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 8px;
}

.empty-state p {
  color: #6b7280;
}

/* Modal Styles (cohérents avec le thème vert) */
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
  padding: 20px;
  z-index: 1000;
}

.modal-content {
  background: white;
  border-radius: 12px;
  max-width: 500px;
  width: 100%;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 20px 24px;
  border-bottom: 1px solid #e5e7eb;
}

.modal-header h2 {
  font-size: 18px;
  font-weight: 600;
  color: #1f2937;
}

.close-btn {
  background: transparent;
  border: none;
  color: #9ca3af;
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
  transition: color 0.2s;
}

.close-btn:hover {
  color: #6b7280;
}

.close-btn svg {
  width: 20px;
  height: 20px;
}

.selected-book-info {
  padding: 20px 24px;
  border-bottom: 1px solid #e5e7eb;
}

.book-preview {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px;
  background: #f0fdf4;
  border-radius: 8px;
}

.book-preview-image {
  width: 48px;
  height: 64px;
  object-fit: cover;
  border-radius: 4px;
}

.book-preview-info h3 {
  font-size: 14px;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 4px;
}

.book-preview-info p {
  font-size: 12px;
  color: #6b7280;
}

/* Forms */
.reservation-form,
.add-book-form {
  padding: 20px 24px;
}

.form-group {
  margin-bottom: 16px;
}

.form-group label {
  display: block;
  font-size: 14px;
  font-weight: 500;
  color: #374151;
  margin-bottom: 6px;
}

.form-input,
.form-select {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 14px;
  color: #1f2937;
}

.form-input:focus,
.form-select:focus {
  outline: none;
  border-color: #4ade80;
  box-shadow: 0 0 0 3px rgba(74, 222, 128, 0.1);
}

.date-time-inputs,
.date-range-inputs {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

.date-input-group {
  display: flex;
  flex-direction: column;
}

.date-label {
  font-size: 12px;
  color: #6b7280;
  margin-bottom: 4px;
}

.online-book-fields,
.physical-book-fields {
  border-top: 1px solid #e5e7eb;
  padding-top: 16px;
  margin-top: 16px;
}

.form-actions {
  display: flex;
  gap: 12px;
  padding-top: 16px;
  border-top: 1px solid #e5e7eb;
}

.cancel-btn {
  flex: 1;
  padding: 10px 16px;
  border: 1px solid #d1d5db;
  background: white;
  color: #374151;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 500;
  transition: background-color 0.2s;
}

.cancel-btn:hover {
  background: #f9fafb;
}

.submit-btn {
  flex: 1;
  padding: 10px 16px;
  background: #4ade80;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 500;
  transition: background-color 0.2s;
}

.submit-btn:hover:not(:disabled) {
  background: #22c55e;
}

.submit-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Responsive */
@media (max-width: 768px) {
  .bibliotheque-container {
    padding: 0 15px;
  }

  .search-filters-content {
    flex-direction: column;
    gap: 12px;
  }

  .filter-section {
    min-width: auto;
    width: 100%;
  }

  .books-grid-container {
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 15px;
    padding: 15px;
  }

  .create-post-actions {
    flex-direction: column;
    gap: 8px;
  }

  .action-btn {
    width: 100%;
    justify-content: center;
  }

  .modal-content {
    margin: 20px;
    max-width: none;
  }

  .date-time-inputs,
  .date-range-inputs {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 480px) {
  .books-grid-container {
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 12px;
    padding: 12px;
  }

  .book-info {
    padding: 12px;
  }

  .book-title {
    font-size: 12px;
  }

  .book-author {
    font-size: 11px;
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

.bibliotheque-container {
  max-width: 1200px;
  margin: 0 auto;
}

/* Create Post Card (maintenant pour les livres) */
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

.create-book-text {
  flex: 1;
  padding: 12px 16px;
  background: #f9fafb;
  border: 1px solid #e5e7eb;
  border-radius: 24px;
  color: #6b7280;
  font-size: 16px;
}

.create-post-actions {
  display: flex;
  gap: 12px;
  align-items: center;
}

.action-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 16px;
  background: transparent;
  border: none;
  border-radius: 8px;
  color: #6b7280;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.action-btn:hover {
  background: #f0fdf4;
  color: #4ade80;
}

.action-icon {
  width: 20px;
  height: 20px;
}

/* Posts Feed (maintenant Books Feed) */
.posts-feed {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

/* Search Filter Card */
.search-filter-card {
  padding: 20px !important;
}

.search-filters-content {
  display: flex;
  gap: 16px;
  align-items: center;
}

.search-section {
  flex: 1;
}

.search-input-container {
  position: relative;
  display: flex;
  align-items: center;
}

.search-icon {
  position: absolute;
  left: 12px;
  width: 20px;
  height: 20px;
  color: #9ca3af;
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

.filter-section {
  min-width: 200px;
}

.category-select {
  width: 100%;
  padding: 12px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 14px;
  color: #1f2937;
  background: white;
}

/* Books Grid Container */
.books-grid-container {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 20px;
  padding: 20px;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.book-card {
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  overflow: hidden;
  cursor: pointer;
  transition: all 0.2s;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.book-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  border-color: #4ade80;
}

.book-image-container {
  position: relative;
  aspect-ratio: 3/4;
  background: linear-gradient(135deg, #f0fdf4, #dcfce7);
  overflow: hidden;
}

.book-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.2s;
}

.book-card:hover .book-image {
  transform: scale(1.05);
}

.availability-badge {
  position: absolute;
  top: 8px;
  right: 8px;
}

.badge {
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 500;
}

.badge-online {
  background: #dcfce7;
  color: #166534;
}

.badge-physical {
  background: #fef3c7;
  color: #92400e;
}

.book-info {
  padding: 16px;
}

.book-title {
  font-size: 14px;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 8px;
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.book-author {
  font-size: 12px;
  color: #6b7280;
  margin-bottom: 12px;
}

.book-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.book-category {
  font-size: 10px;
  padding: 2px 8px;
  background: #f0fdf4;
  color: #166534;
  border-radius: 12px;
}

.arrow-icon {
  width: 16px;
  height: 16px;
  color: #9ca3af;
  transition: color 0.2s;
}

.book-card:hover .arrow-icon {
  color: #4ade80;
}

/* Post Card (pour empty state) */
.post-card {
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 60px 20px;
}

.empty-icon {
  width: 64px;
  height: 64px;
  color: #d1d5db;
  margin: 0 auto 16px;
}

.empty-state h3 {
  font-size: 18px;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 8px;
}

.empty-state p {
  color: #6b7280;
}

/* Modal Styles (cohérents avec le thème vert) */
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
  padding: 20px;
  z-index: 1000;
}

.modal-content {
  background: white;
  border-radius: 12px;
  max-width: 500px;
  width: 100%;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 20px 24px;
  border-bottom: 1px solid #e5e7eb;
}

.modal-header h2 {
  font-size: 18px;
  font-weight: 600;
  color: #1f2937;
}

.close-btn {
  background: transparent;
  border: none;
  color: #9ca3af;
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
  transition: color 0.2s;
}

.close-btn:hover {
  color: #6b7280;
}

.close-btn svg {
  width: 20px;
  height: 20px;
}

.selected-book-info {
  padding: 20px 24px;
  border-bottom: 1px solid #e5e7eb;
}

.book-preview {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px;
  background: #f0fdf4;
  border-radius: 8px;
}

.book-preview-image {
  width: 48px;
  height: 64px;
  object-fit: cover;
  border-radius: 4px;
}

.book-preview-info h3 {
  font-size: 14px;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 4px;
}

.book-preview-info p {
  font-size: 12px;
  color: #6b7280;
}

/* Forms */
.reservation-form,
.add-book-form {
  padding: 20px 24px;
}

.form-group {
  margin-bottom: 16px;
}

.form-group label {
  display: block;
  font-size: 14px;
  font-weight: 500;
  color: #374151;
  margin-bottom: 6px;
}

.form-input,
.form-select {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 14px;
  color: #1f2937;
}

.form-input:focus,
.form-select:focus {
  outline: none;
  border-color: #4ade80;
  box-shadow: 0 0 0 3px rgba(74, 222, 128, 0.1);
}

.date-time-inputs,
.date-range-inputs {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

.date-input-group {
  display: flex;
  flex-direction: column;
}

.date-label {
  font-size: 12px;
  color: #6b7280;
  margin-bottom: 4px;
}

.online-book-fields,
.physical-book-fields {
  border-top: 1px solid #e5e7eb;
  padding-top: 16px;
  margin-top: 16px;
}

.form-actions {
  display: flex;
  gap: 12px;
  padding-top: 16px;
  border-top: 1px solid #e5e7eb;
}

.cancel-btn {
  flex: 1;
  padding: 10px 16px;
  border: 1px solid #d1d5db;
  background: white;
  color: #374151;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 500;
  transition: background-color 0.2s;
}

.cancel-btn:hover {
  background: #f9fafb;
}

.submit-btn {
  flex: 1;
  padding: 10px 16px;
  background: #4ade80;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 500;
  transition: background-color 0.2s;
}

.submit-btn:hover:not(:disabled) {
  background: #22c55e;
}

.submit-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Responsive */
@media (max-width: 768px) {
  .bibliotheque-container {
    padding: 0 15px;
  }

  .search-filters-content {
    flex-direction: column;
    gap: 12px;
  }

  .filter-section {
    min-width: auto;
    width: 100%;
  }

  .books-grid-container {
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 15px;
    padding: 15px;
  }

  .create-post-actions {
    flex-direction: column;
    gap: 8px;
  }

  .action-btn {
    width: 100%;
    justify-content: center;
  }

  .modal-content {
    margin: 20px;
    max-width: none;
  }

  .date-time-inputs,
  .date-range-inputs {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 480px) {
  .books-grid-container {
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 12px;
    padding: 12px;
  }

  .book-info {
    padding: 12px;
  }

  .book-title {
    font-size: 12px;
  }

  .book-author {
    font-size: 11px;
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

.bibliotheque-container {
  max-width: 1200px;
  margin: 0 auto;
}

/* Create Post Card (maintenant pour les livres) */
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

.create-book-text {
  flex: 1;
  padding: 12px 16px;
  background: #f9fafb;
  border: 1px solid #e5e7eb;
  border-radius: 24px;
  color: #6b7280;
  font-size: 16px;
}

.create-post-actions {
  display: flex;
  gap: 12px;
  align-items: center;
}

.action-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 16px;
  background: transparent;
  border: none;
  border-radius: 8px;
  color: #6b7280;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.action-btn:hover {
  background: #f0fdf4;
  color: #4ade80;
}

.action-icon {
  width: 20px;
  height: 20px;
}

/* Posts Feed (maintenant Books Feed) */
.posts-feed {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

/* Search Filter Card */
.search-filter-card {
  padding: 20px !important;
}

.search-filters-content {
  display: flex;
  gap: 16px;
  align-items: center;
}

.search-section {
  flex: 1;
}

.search-input-container {
  position: relative;
  display: flex;
  align-items: center;
}

.search-icon {
  position: absolute;
  left: 12px;
  width: 20px;
  height: 20px;
  color: #9ca3af;
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

.filter-section {
  min-width: 200px;
}

.category-select {
  width: 100%;
  padding: 12px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 14px;
  color: #1f2937;
  background: white;
}

/* Books Grid Container */
.books-grid-container {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 20px;
  padding: 20px;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.book-card {
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  overflow: hidden;
  cursor: pointer;
  transition: all 0.2s;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.book-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  border-color: #4ade80;
}

.book-image-container {
  position: relative;
  aspect-ratio: 3/4;
  background: linear-gradient(135deg, #f0fdf4, #dcfce7);
  overflow: hidden;
}

.book-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.2s;
}

.book-card:hover .book-image {
  transform: scale(1.05);
}

.availability-badge {
  position: absolute;
  top: 8px;
  right: 8px;
}

.badge {
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 500;
}

.badge-online {
  background: #dcfce7;
  color: #166534;
}

.badge-physical {
  background: #fef3c7;
  color: #92400e;
}

.book-info {
  padding: 16px;
}

.book-title {
  font-size: 14px;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 8px;
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.book-author {
  font-size: 12px;
  color: #6b7280;
  margin-bottom: 12px;
}

.book-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.book-category {
  font-size: 10px;
  padding: 2px 8px;
  background: #f0fdf4;
  color: #166534;
  border-radius: 12px;
}

.arrow-icon {
  width: 16px;
  height: 16px;
  color: #9ca3af;
  transition: color 0.2s;
}

.book-card:hover .arrow-icon {
  color: #4ade80;
}

/* Post Card (pour empty state) */
.post-card {
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 60px 20px;
}

.empty-icon {
  width: 64px;
  height: 64px;
  color: #d1d5db;
  margin: 0 auto 16px;
}

.empty-state h3 {
  font-size: 18px;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 8px;
}

.empty-state p {
  color: #6b7280;
}

/* Modal Styles (cohérents avec le thème vert) */
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
  padding: 20px;
  z-index: 1000;
}

.modal-content {
  background: white;
  border-radius: 12px;
  max-width: 500px;
  width: 100%;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 20px 24px;
  border-bottom: 1px solid #e5e7eb;
}

.modal-header h2 {
  font-size: 18px;
  font-weight: 600;
  color: #1f2937;
}

.close-btn {
  background: transparent;
  border: none;
  color: #9ca3af;
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
  transition: color 0.2s;
}

.close-btn:hover {
  color: #6b7280;
}

.close-btn svg {
  width: 20px;
  height: 20px;
}

.selected-book-info {
  padding: 20px 24px;
  border-bottom: 1px solid #e5e7eb;
}

.book-preview {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px;
  background: #f0fdf4;
  border-radius: 8px;
}

.book-preview-image {
  width: 48px;
  height: 64px;
  object-fit: cover;
  border-radius: 4px;
}

.book-preview-info h3 {
  font-size: 14px;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 4px;
}

.book-preview-info p {
  font-size: 12px;
  color: #6b7280;
}

/* Forms */
.reservation-form,
.add-book-form {
  padding: 20px 24px;
}

.form-group {
  margin-bottom: 16px;
}

.form-group label {
  display: block;
  font-size: 14px;
  font-weight: 500;
  color: #374151;
  margin-bottom: 6px;
}

.form-input,
.form-select {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 14px;
  color: #1f2937;
}

.form-input:focus,
.form-select:focus {
  outline: none;
  border-color: #4ade80;
  box-shadow: 0 0 0 3px rgba(74, 222, 128, 0.1);
}

.date-time-inputs,
.date-range-inputs {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

.date-input-group {
  display: flex;
  flex-direction: column;
}

.date-label {
  font-size: 12px;
  color: #6b7280;
  margin-bottom: 4px;
}

.online-book-fields,
.physical-book-fields {
  border-top: 1px solid #e5e7eb;
  padding-top: 16px;
  margin-top: 16px;
}

.form-actions {
  display: flex;
  gap: 12px;
  padding-top: 16px;
  border-top: 1px solid #e5e7eb;
}

.cancel-btn {
  flex: 1;
  padding: 10px 16px;
  border: 1px solid #d1d5db;
  background: white;
  color: #374151;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 500;
  transition: background-color 0.2s;
}

.cancel-btn:hover {
  background: #f9fafb;
}

.submit-btn {
  flex: 1;
  padding: 10px 16px;
  background: #4ade80;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 500;
  transition: background-color 0.2s;
}

.submit-btn:hover:not(:disabled) {
  background: #22c55e;
}

.submit-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Responsive */
@media (max-width: 768px) {
  .bibliotheque-container {
    padding: 0 15px;
  }

  .search-filters-content {
    flex-direction: column;
    gap: 12px;
  }

  .filter-section {
    min-width: auto;
    width: 100%;
  }

  .books-grid-container {
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 15px;
    padding: 15px;
  }

  .create-post-actions {
    flex-direction: column;
    gap: 8px;
  }

  .action-btn {
    width: 100%;
    justify-content: center;
  }

  .modal-content {
    margin: 20px;
    max-width: none;
  }

  .date-time-inputs,
  .date-range-inputs {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 480px) {
  .books-grid-container {
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 12px;
    padding: 12px;
  }

  .book-info {
    padding: 12px;
  }

  .book-title {
    font-size: 12px;
  }

  .book-author {
    font-size: 11px;
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

.bibliotheque-container {
  max-width: 1200px;
  margin: 0 auto;
}

/* Create Post Card (maintenant pour les livres) */
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

.create-book-text {
  flex: 1;
  padding: 12px 16px;
  background: #f9fafb;
  border: 1px solid #e5e7eb;
  border-radius: 24px;
  color: #6b7280;
  font-size: 16px;
}

.create-post-actions {
  display: flex;
  gap: 12px;
  align-items: center;
}

.action-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 16px;
  background: transparent;
  border: none;
  border-radius: 8px;
  color: #6b7280;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.action-btn:hover {
  background: #f0fdf4;
  color: #4ade80;
}

.action-icon {
  width: 20px;
  height: 20px;
}

/* Posts Feed (maintenant Books Feed) */
.posts-feed {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

/* Search Filter Card */
.search-filter-card {
  padding: 20px !important;
}

.search-filters-content {
  display: flex;
  gap: 16px;
  align-items: center;
}

.search-section {
  flex: 1;
}

.search-input-container {
  position: relative;
  display: flex;
  align-items: center;
}

.search-icon {
  position: absolute;
  left: 12px;
  width: 20px;
  height: 20px;
  color: #9ca3af;
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

.filter-section {
  min-width: 200px;
}

.category-select {
  width: 100%;
  padding: 12px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 14px;
  color: #1f2937;
  background: white;
}

/* Books Grid Container */
.books-grid-container {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 20px;
  padding: 20px;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.book-card {
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  overflow: hidden;
  cursor: pointer;
  transition: all 0.2s;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.book-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  border-color: #4ade80;
}

.book-image-container {
  position: relative;
  aspect-ratio: 3/4;
  background: linear-gradient(135deg, #f0fdf4, #dcfce7);
  overflow: hidden;
}

.book-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.2s;
}

.book-card:hover .book-image {
  transform: scale(1.05);
}

.availability-badge {
  position: absolute;
  top: 8px;
  right: 8px;
}

.badge {
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 500;
}

.badge-online {
  background: #dcfce7;
  color: #166534;
}

.badge-physical {
  background: #fef3c7;
  color: #92400e;
}

.book-info {
  padding: 16px;
}

.book-title {
  font-size: 14px;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 8px;
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.book-author {
  font-size: 12px;
  color: #6b7280;
  margin-bottom: 12px;
}

.book-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.book-category {
  font-size: 10px;
  padding: 2px 8px;
  background: #f0fdf4;
  color: #166534;
  border-radius: 12px;
}

.arrow-icon {
  width: 16px;
  height: 16px;
  color: #9ca3af;
  transition: color 0.2s;
}

.book-card:hover .arrow-icon {
  color: #4ade80;
}

/* Post Card (pour empty state) */
.post-card {
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 60px 20px;
}

.empty-icon {
  width: 64px;
  height: 64px;
  color: #d1d5db;
  margin: 0 auto 16px;
}

.empty-state h3 {
  font-size: 18px;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 8px;
}

.empty-state p {
  color: #6b7280;
}

/* Modal Styles (cohérents avec le thème vert) */
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
  padding: 20px;
  z-index: 1000;
}

.modal-content {
  background: white;
  border-radius: 12px;
  max-width: 500px;
  width: 100%;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 20px 24px;
  border-bottom: 1px solid #e5e7eb;
}

.modal-header h2 {
  font-size: 18px;
  font-weight: 600;
  color: #1f2937;
}

.close-btn {
  background: transparent;
  border: none;
  color: #9ca3af;
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
  transition: color 0.2s;
}

.close-btn:hover {
  color: #6b7280;
}

.close-btn svg {
  width: 20px;
  height: 20px;
}

.selected-book-info {
  padding: 20px 24px;
  border-bottom: 1px solid #e5e7eb;
}

.book-preview {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px;
  background: #f0fdf4;
  border-radius: 8px;
}

.book-preview-image {
  width: 48px;
  height: 64px;
  object-fit: cover;
  border-radius: 4px;
}

.book-preview-info h3 {
  font-size: 14px;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 4px;
}

.book-preview-info p {
  font-size: 12px;
  color: #6b7280;
}

/* Forms */
.reservation-form,
.add-book-form {
  padding: 20px 24px;
}

.form-group {
  margin-bottom: 16px;
}

.form-group label {
  display: block;
  font-size: 14px;
  font-weight: 500;
  color: #374151;
  margin-bottom: 6px;
}

.form-input,
.form-select {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 14px;
  color: #1f2937;
}

.form-input:focus,
.form-select:focus {
  outline: none;
  border-color: #4ade80;
  box-shadow: 0 0 0 3px rgba(74, 222, 128, 0.1);
}

.date-time-inputs,
.date-range-inputs {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

.date-input-group {
  display: flex;
  flex-direction: column;
}

.date-label {
  font-size: 12px;
  color: #6b7280;
  margin-bottom: 4px;
}

.online-book-fields,
.physical-book-fields {
  border-top: 1px solid #e5e7eb;
  padding-top: 16px;
  margin-top: 16px;
}

.form-actions {
  display: flex;
  gap: 12px;
  padding-top: 16px;
  border-top: 1px solid #e5e7eb;
}

.cancel-btn {
  flex: 1;
  padding: 10px 16px;
  border: 1px solid #d1d5db;
  background: white;
  color: #374151;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 500;
  transition: background-color 0.2s;
}

.cancel-btn:hover {
  background: #f9fafb;
}

.submit-btn {
  flex: 1;
  padding: 10px 16px;
  background: #4ade80;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 500;
  transition: background-color 0.2s;
}

.submit-btn:hover:not(:disabled) {
  background: #22c55e;
}

.submit-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Responsive */
@media (max-width: 768px) {
  .bibliotheque-container {
    padding: 0 15px;
  }

  .search-filters-content {
    flex-direction: column;
    gap: 12px;
  }

  .filter-section {
    min-width: auto;
    width: 100%;
  }

  .books-grid-container {
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 15px;
    padding: 15px;
  }

  .create-post-actions {
    flex-direction: column;
    gap: 8px;
  }

  .action-btn {
    width: 100%;
    justify-content: center;
  }

  .modal-content {
    margin: 20px;
    max-width: none;
  }

  .date-time-inputs,
  .date-range-inputs {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 480px) {
  .books-grid-container {
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 12px;
    padding: 12px;
  }

  .book-info {
    padding: 12px;
  }

  .book-title {
    font-size: 12px;
  }

  .book-author {
    font-size: 11px;
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

.bibliotheque-container {
  max-width: 1200px;
  margin: 0 auto;
}

/* Create Post Card (maintenant pour les livres) */
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

.create-book-text {
  flex: 1;
  padding: 12px 16px;
  background: #f9fafb;
  border: 1px solid #e5e7eb;
  border-radius: 24px;
  color: #6b7280;
  font-size: 16px;
}

.create-post-actions {
  display: flex;
  gap: 12px;
  align-items: center;
}

.action-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 16px;
  background: transparent;
  border: none;
  border-radius: 8px;
  color: #6b7280;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.action-btn:hover {
  background: #f0fdf4;
  color: #4ade80;
}

.action-icon {
  width: 20px;
  height: 20px;
}

/* Posts Feed (maintenant Books Feed) */
.posts-feed {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

/* Search Filter Card */
.search-filter-card {
  padding: 20px !important;
}

.search-filters-content {
  display: flex;
  gap: 16px;
  align-items: center;
}

.search-section {
  flex: 1;
}

.search-input-container {
  position: relative;
  display: flex;
  align-items: center;
}

.search-icon {
  position: absolute;
  left: 12px;
  width: 20px;
  height: 20px;
  color: #9ca3af;
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

.filter-section {
  min-width: 200px;
}

.category-select {
  width: 100%;
  padding: 12px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 14px;
  color: #1f2937;
  background: white;
}

/* Books Grid Container */
.books-grid-container {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 20px;
  padding: 20px;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.book-card {
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  overflow: hidden;
  cursor: pointer;
  transition: all 0.2s;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.book-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  border-color: #4ade80;
}

.book-image-container {
  position: relative;
  aspect-ratio: 3/4;
  background: linear-gradient(135deg, #f0fdf4, #dcfce7);
  overflow: hidden;
}

.book-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.2s;
}

.book-card:hover .book-image {
  transform: scale(1.05);
}

.availability-badge {
  position: absolute;
  top: 8px;
  right: 8px;
}

.badge {
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 500;
}

.badge-online {
  background: #dcfce7;
  color: #166534;
}

.badge-physical {
  background: #fef3c7;
  color: #92400e;
}

.book-info {
  padding: 16px;
}

.book-title {
  font-size: 14px;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 8px;
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.book-author {
  font-size: 12px;
  color: #6b7280;
  margin-bottom: 12px;
}

.book-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.book-category {
  font-size: 10px;
  padding: 2px 8px;
  background: #f0fdf4;
  color: #166534;
  border-radius: 12px;
}

.arrow-icon {
  width: 16px;
  height: 16px;
  color: #9ca3af;
  transition: color 0.2s;
}

.book-card:hover .arrow-icon {
  color: #4ade80;
}

/* Post Card (pour empty state) */
.post-card {
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 60px 20px;
}

.empty-icon {
  width: 64px;
  height: 64px;
  color: #d1d5db;
  margin: 0 auto 16px;
}

.empty-state h3 {
  font-size: 18px;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 8px;
}

.empty-state p {
  color: #6b7280;
}

/* Modal Styles (cohérents avec le thème vert) */
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
  padding: 20px;
  z-index: 1000;
}

.modal-content {
  background: white;
  border-radius: 12px;
  max-width: 500px;
  width: 100%;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 20px 24px;
  border-bottom: 1px solid #e5e7eb;
}

.modal-header h2 {
  font-size: 18px;
  font-weight: 600;
  color: #1f2937;
}

.close-btn {
  background: transparent;
  border: none;
  color: #9ca3af;
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
  transition: color 0.2s;
}

.close-btn:hover {
  color: #6b7280;
}

.close-btn svg {
  width: 20px;
  height: 20px;
}

.selected-book-info {
  padding: 20px 24px;
  border-bottom: 1px solid #e5e7eb;
}

.book-preview {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px;
  background: #f0fdf4;
  border-radius: 8px;
}

.book-preview-image {
  width: 48px;
  height: 64px;
  object-fit: cover;
  border-radius: 4px;
}

.book-preview-info h3 {
  font-size: 14px;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 4px;
}

.book-preview-info p {
  font-size: 12px;
  color: #6b7280;
}

/* Forms */
.reservation-form,
.add-book-form {
  padding: 20px 24px;
}

.form-group {
  margin-bottom: 16px;
}

.form-group label {
  display: block;
  font-size: 14px;
  font-weight: 500;
  color: #374151;
  margin-bottom: 6px;
}

.form-input,
.form-select {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 14px;
  color: #1f2937;
}

.form-input:focus,
.form-select:focus {
  outline: none;
  border-color: #4ade80;
  box-shadow: 0 0 0 3px rgba(74, 222, 128, 0.1);
}

.date-time-inputs,
.date-range-inputs {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

.date-input-group {
  display: flex;
  flex-direction: column;
}

.date-label {
  font-size: 12px;
  color: #6b7280;
  margin-bottom: 4px;
}

.online-book-fields,
.physical-book-fields {
  border-top: 1px solid #e5e7eb;
  padding-top: 16px;
  margin-top: 16px;
}

.form-actions {
  display: flex;
  gap: 12px;
  padding-top: 16px;
  border-top: 1px solid #e5e7eb;
}

.cancel-btn {
  flex: 1;
  padding: 10px 16px;
  border: 1px solid #d1d5db;
  background: white;
  color: #374151;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 500;
  transition: background-color 0.2s;
}

.cancel-btn:hover {
  background: #f9fafb;
}

.submit-btn {
  flex: 1;
  padding: 10px 16px;
  background: #4ade80;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 500;
  transition: background-color 0.2s;
}

.submit-btn:hover:not(:disabled) {
  background: #22c55e;
}

.submit-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Responsive */
@media (max-width: 768px) {
  .bibliotheque-container {
    padding: 0 15px;
  }

  .search-filters-content {
    flex-direction: column;
    gap: 12px;
  }

  .filter-section {
    min-width: auto;
    width: 100%;
  }

  .books-grid-container {
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 15px;
    padding: 15px;
  }

  .create-post-actions {
    flex-direction: column;
    gap: 8px;
  }

  .action-btn {
    width: 100%;
    justify-content: center;
  }

  .modal-content {
    margin: 20px;
    max-width: none;
  }

  .date-time-inputs,
  .date-range-inputs {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 480px) {
  .books-grid-container {
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 12px;
    padding: 12px;
  }

  .book-info {
    padding: 12px;
  }

  .book-title {
    font-size: 12px;
  }

  .book-author {
    font-size: 11px;
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

.bibliotheque-container {
  max-width: 1200px;
  margin: 0 auto;
}

/* Create Post Card (maintenant pour les livres) */
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

.create-book-text {
  flex: 1;
  padding: 12px 16px;
  background: #f9fafb;
  border: 1px solid #e5e7eb;
  border-radius: 24px;
  color: #6b7280;
  font-size: 16px;
}

.create-post-actions {
  display: flex;
  gap: 12px;
  align-items: center;
}

.action-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 16px;
  background: transparent;
  border: none;
  border-radius: 8px;
  color: #6b7280;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.action-btn:hover {
  background: #f0fdf4;
  color: #4ade80;
}

.action-icon {
  width: 20px;
  height: 20px;
}

/* Posts Feed (maintenant Books Feed) */
.posts-feed {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

/* Search Filter Card */
.search-filter-card {
  padding: 20px !important;
}

.search-filters-content {
  display: flex;
  gap: 16px;
  align-items: center;
}

.search-section {
  flex: 1;
}

.search-input-container {
  position: relative;
  display: flex;
  align-items: center;
}

.search-icon {
  position: absolute;
  left: 12px;
  width: 20px;
  height: 20px;
  color: #9ca3af;
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

.filter-section {
  min-width: 200px;
}

.category-select {
  width: 100%;
  padding: 12px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 14px;
  color: #1f2937;
  background: white;
}

/* Books Grid Container */
.books-grid-container {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 20px;
  padding: 20px;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.book-card {
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  overflow: hidden;
  cursor: pointer;
  transition: all 0.2s;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.book-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  border-color: #4ade80;
}

.book-image-container {
  position: relative;
  aspect-ratio: 3/4;
  background: linear-gradient(135deg, #f0fdf4, #dcfce7);
  overflow: hidden;
}

.book-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.2s;
}

.book-card:hover .book-image {
  transform: scale(1.05);
}

.availability-badge {
  position: absolute;
  top: 8px;
  right: 8px;
}

.badge {
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 500;
}

.badge-online {
  background: #dcfce7;
  color: #166534;
}

.badge-physical {
  background: #fef3c7;
  color: #92400e;
}

.book-info {
  padding: 16px;
}

.book-title {
  font-size: 14px;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 8px;
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.book-author {
  font-size: 12px;
  color: #6b7280;
  margin-bottom: 12px;
}

.book-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.book-category {
  font-size: 10px;
  padding: 2px 8px;
  background: #f0fdf4;
  color: #166534;
  border-radius: 12px;
}

.arrow-icon {
  width: 16px;
  height: 16px;
  color: #9ca3af;
  transition: color 0.2s;
}

.book-card:hover .arrow-icon {
  color: #4ade80;
}

/* Post Card (pour empty state) */
.post-card {
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 60px 20px;
}

.empty-icon {
  width: 64px;
  height: 64px;
  color: #d1d5db;
  margin: 0 auto 16px;
}

.empty-state h3 {
  font-size: 18px;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 8px;
}

.empty-state p {
  color: #6b7280;
}

/* Modal Styles (cohérents avec le thème vert) */
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
  padding: 20px;
  z-index: 1000;
}

.modal-content {
  background: white;
  border-radius: 12px;
  max-width: 500px;
  width: 100%;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 20px 24px;
  border-bottom: 1px solid #e5e7eb;
}

.modal-header h2 {
  font-size: 18px;
  font-weight: 600;
  color: #1f2937;
}

.close-btn {
  background: transparent;
  border: none;
  color: #9ca3af;
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
  transition: color 0.2s;
}

.close-btn:hover {
  color: #6b7280;
}

.close-btn svg {
  width: 20px;
  height: 20px;
}

.selected-book-info {
  padding: 20px 24px;
  border-bottom: 1px solid #e5e7eb;
}

.book-preview {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px;
  background: #f0fdf4;
  border-radius: 8px;
}

.book-preview-image {
  width: 48px;
  height: 64px;
  object-fit: cover;
  border-radius: 4px;
}

.book-preview-info h3 {
  font-size: 14px;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 4px;
}

.book-preview-info p {
  font-size: 12px;
  color: #6b7280;
}

/* Forms */
.reservation-form,
.add-book-form {
  padding: 20px 24px;
}

.form-group {
  margin-bottom: 16px;
}

.form-group label {
  display: block;
  font-size: 14px;
  font-weight: 500;
  color: #374151;
  margin-bottom: 6px;
}

.form-input,
.form-select {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 14px;
  color: #1f2937;
}

.form-input:focus,
.form-select:focus {
  outline: none;
  border-color: #4ade80;
  box-shadow: 0 0 0 3px rgba(74, 222, 128, 0.1);
}

.date-time-inputs,
.date-range-inputs {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

.date-input-group {
  display: flex;
  flex-direction: column;
}

.date-label {
  font-size: 12px;
  color: #6b7280;
  margin-bottom: 4px;
}

.online-book-fields,
.physical-book-fields {
  border-top: 1px solid #e5e7eb;
  padding-top: 16px;
  margin-top: 16px;
}

.form-actions {
  display: flex;
  gap: 12px;
  padding-top: 16px;
  border-top: 1px solid #e5e7eb;
}

.cancel-btn {
  flex: 1;
  padding: 10px 16px;
  border: 1px solid #d1d5db;
  background: white;
  color: #374151;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 500;
  transition: background-color 0.2s;
}

.cancel-btn:hover {
  background: #f9fafb;
}

.submit-btn {
  flex: 1;
  padding: 10px 16px;
  background: #4ade80;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 500;
  transition: background-color 0.2s;
}

.submit-btn:hover:not(:disabled) {
  background: #22c55e;
}

.submit-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Responsive */
@media (max-width: 768px) {
  .bibliotheque-container {
    padding: 0 15px;
  }

  .search-filters-content {
    flex-direction: column;
    gap: 12px;
  }

  .filter-section {
    min-width: auto;
    width: 100%;
  }

  .books-grid-container {
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 15px;
    padding: 15px;
  }

  .create-post-actions {
    flex-direction: column;
    gap: 8px;
  }

  .action-btn {
    width: 100%;
    justify-content: center;
  }

  .modal-content {
    margin: 20px;
    max-width: none;
  }

  .date-time-inputs,
  .date-range-inputs {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 480px) {
  .books-grid-container {
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 12px;
    padding: 12px;
  }

  .book-info {
    padding: 12px;
  }

  .book-title {
    font-size: 12px;
  }

  .book-author {
    font-size: 11px;
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

.bibliotheque-container {
  max-width: 1200px;
  margin: 0 auto;
}

/* Create Post Card (maintenant pour les livres) */
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

.create-book-text {
  flex: 1;
  padding: 12px 16px;
  background: #f9fafb;
  border: 1px solid #e5e7eb;
  border-radius: 24px;
  color: #6b7280;
  font-size: 16px;
}

.create-post-actions {
  display: flex;
  gap: 12px;
  align-items: center;
}

.action-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 16px;
  background: transparent;
  border: none;
  border-radius: 8px;
  color: #6b7280;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.action-btn:hover {
  background: #f0fdf4;
  color: #4ade80;
}

.action-icon {
  width: 20px;
  height: 20px;
}

/* Posts Feed (maintenant Books Feed) */
.posts-feed {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

/* Search Filter Card */
.search-filter-card {
  padding: 20px !important;
}

.search-filters-content {
  display: flex;
  gap: 16px;
  align-items: center;
}

.search-section {
  flex: 1;
}

.search-input-container {
  position: relative;
  display: flex;
  align-items: center;
}

.search-icon {
  position: absolute;
  left: 12px;
  width: 20px;
  height: 20px;
  color: #9ca3af;
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

.filter-section {
  min-width: 200px;
}

.category-select {
  width: 100%;
  padding: 12px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 14px;
  color: #1f2937;
  background: white;
}

/* Books Grid Container */
.books-grid-container {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 20px;
  padding: 20px;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.book-card {
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  overflow: hidden;
  cursor: pointer;
  transition: all 0.2s;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.book-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  border-color: #4ade80;
}

.book-image-container {
  position: relative;
  aspect-ratio: 3/4;
  background: linear-gradient(135deg, #f0fdf4, #dcfce7);
  overflow: hidden;
}

.book-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.2s;
}

.book-card:hover .book-image {
  transform: scale(1.05);
}

.availability-badge {
  position: absolute;
  top: 8px;
  right: 8px;
}

.badge {
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 500;
}

.badge-online {
  background: #dcfce7;
  color: #166534;
}

.badge-physical {
  background: #fef3c7;
  color: #92400e;
}

.book-info {
  padding: 16px;
}

.book-title {
  font-size: 14px;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 8px;
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.book-author {
  font-size: 12px;
  color: #6b7280;
  margin-bottom: 12px;
}

.book-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.book-category {
  font-size: 10px;
  padding: 2px 8px;
  background: #f0fdf4;
  color: #166534;
  border-radius: 12px;
}

.arrow-icon {
  width: 16px;
  height: 16px;
  color: #9ca3af;
  transition: color 0.2s;
}

.book-card:hover .arrow-icon {
  color: #4ade80;
}

/* Post Card (pour empty state) */
.post-card {
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 60px 20px;
}

.empty-icon {
  width: 64px;
  height: 64px;
  color: #d1d5db;
  margin: 0 auto 16px;
}

.empty-state h3 {
  font-size: 18px;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 8px;
}

.empty-state p {
  color: #6b7280;
}

/* Modal Styles (cohérents avec le thème vert) */
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
  padding: 20px;
  z-index: 1000;
}

.modal-content {
  background: white;
  border-radius: 12px;
  max-width: 500px;
  width: 100%;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 20px 24px;
  border-bottom: 1px solid #e5e7eb;
}

.modal-header h2 {
  font-size: 18px;
  font-weight: 600;
  color: #1f2937;
}

.close-btn {
  background: transparent;
  border: none;
  color: #9ca3af;
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
  transition: color 0.2s;
}

.close-btn:hover {
  color: #6b7280;
}

.close-btn svg {
  width: 20px;
  height: 20px;
}

.selected-book-info {
  padding: 20px 24px;
  border-bottom: 1px solid #e5e7eb;
}

.book-preview {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px;
  background: #f0fdf4;
  border-radius: 8px;
}

.book-preview-image {
  width: 48px;
  height: 64px;
  object-fit: cover;
  border-radius: 4px;
}

.book-preview-info h3 {
  font-size: 14px;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 4px;
}

.book-preview-info p {
  font-size: 12px;
  color: #6b7280;
}

/* Forms */
.reservation-form,
.add-book-form {
  padding: 20px 24px;
}

.form-group {
  margin-bottom: 16px;
}

.form-group label {
  display: block;
  font-size: 14px;
  font-weight: 500;
  color: #374151;
  margin-bottom: 6px;
}

.form-input,
.form-select {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 14px;
  color: #1f2937;
}

.form-input:focus,
.form-select:focus {
  outline: none;
  border-color: #4ade80;
  box-shadow: 0 0 0 3px rgba(74, 222, 128, 0.1);
}

.date-time-inputs,
.date-range-inputs {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

.date-input-group {
  display: flex;
  flex-direction: column;
}

.date-label {
  font-size: 12px;
  color: #6b7280;
  margin-bottom: 4px;
}

.online-book-fields,
.physical-book-fields {
  border-top: 1px solid #e5e7eb;
  padding-top: 16px;
  margin-top: 16px;
}

.form-actions {
  display: flex;
  gap: 12px;
  padding-top: 16px;
  border-top: 1px solid #e5e7eb;
}

.cancel-btn {
  flex: 1;
  padding: 10px 16px;
  border: 1px solid #d1d5db;
  background: white;
  color: #374151;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 500;
  transition: background-color 0.2s;
}

.cancel-btn:hover {
  background: #f9fafb;
}

.submit-btn {
  flex: 1;
  padding: 10px 16px;
  background: #4ade80;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 500;
  transition: background-color 0.2s;
}

.submit-btn:hover:not(:disabled) {
  background: #22c55e;
}

.submit-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Responsive */
@media (max-width: 768px) {
  .bibliotheque-container {
    padding: 0 15px;
  }

  .search-filters-content {
    flex-direction: column;
    gap: 12px;
  }

  .filter-section {
    min-width: auto;
    width: 100%;
  }

  .books-grid-container {
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 15px;
    padding: 15px;
  }

  .create-post-actions {
    flex-direction: column;
    gap: 8px;
  }

  .action-btn {
    width: 100%;
    justify-content: center;
  }

  .modal-content {
    margin: 20px;
    max-width: none;
  }

  .date-time-inputs,
  .date-range-inputs {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 480px) {
  .books-grid-container {
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 12px;
    padding: 12px;
  }

  .book-info {
    padding: 12px;
  }

  .book-title {
    font-size: 12px;
  }

  .book-author {
    font-size: 11px;
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

.bibliotheque-container {
  max-width: 1200px;
  margin: 0 auto;
}

/* Create Post Card (maintenant pour les livres) */
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

.create-book-text {
  flex: 1;
  padding: 12px 16px;
  background: #f9fafb;
  border: 1px solid #e5e7eb;
  border-radius: 24px;
  color: #6b7280;
  font-size: 16px;
}

.create-post-actions {
  display: flex;
  gap: 12px;
  align-items: center;
}

.action-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 16px;
  background: transparent;
  border: none;
  border-radius: 8px;
  color: #6b7280;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.action-btn:hover {
  background: #f0fdf4;
  color: #4ade80;
}

.action-icon {
  width: 20px;
  height: 20px;
}

/* Posts Feed (maintenant Books Feed) */
.posts-feed {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

/* Search Filter Card */
.search-filter-card {
  padding: 20px !important;
}

.search-filters-content {
  display: flex;
  gap: 16px;
  align-items: center;
}

.search-section {
  flex: 1;
}

.search-input-container {
  position: relative;
  display: flex;
  align-items: center;
}

.search-icon {
  position: absolute;
  left: 12px;
  width: 20px;
  height: 20px;
  color: #9ca3af;
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

.filter-section {
  min-width: 200px;
}

.category-select {
  width: 100%;
  padding: 12px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 14px;
  color: #1f2937;
  background: white;
}

/* Books Grid Container */
.books-grid-container {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 20px;
  padding: 20px;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.book-card {
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  overflow: hidden;
  cursor: pointer;
  transition: all 0.2s;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.book-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  border-color: #4ade80;
}

.book-image-container {
  position: relative;
  aspect-ratio: 3/4;
  background: linear-gradient(135deg, #f0fdf4, #dcfce7);
  overflow: hidden;
}

.book-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.2s;
}

.book-card:hover .book-image {
  transform: scale(1.05);
}

.availability-badge {
  position: absolute;
  top: 8px;
  right: 8px;
}

.badge {
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 500;
}

.badge-online {
  background: #dcfce7;
  color: #166534;
}

.badge-physical {
  background: #fef3c7;
  color: #92400e;
}

.book-info {
  padding: 16px;
}

.book-title {
  font-size: 14px;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 8px;
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.book-author {
  font-size: 12px;
  color: #6b7280;
  margin-bottom: 12px;
}

.book-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.book-category {
  font-size: 10px;
  padding: 2px 8px;
  background: #f0fdf4;
  color: #166534;
  border-radius: 12px;
}

.arrow-icon {
  width: 16px;
  height: 16px;
  color: #9ca3af;
  transition: color 0.2s;
}

.book-card:hover .arrow-icon {
  color: #4ade80;
}

/* Post Card (pour empty state) */
.post-card {
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 60px 20px;
}

.empty-icon {
  width: 64px;
  height: 64px;
  color: #d1d5db;
  margin: 0 auto 16px;
}

.empty-state h3 {
  font-size: 18px;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 8px;
}

.empty-state p {
  color: #6b7280;
}

/* Modal Styles (cohérents avec le thème vert) */
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
  padding: 20px;
  z-index: 1000;
}

.modal-content {
  background: white;
  border-radius: 12px;
  max-width: 500px;
  width: 100%;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 20px 24px;
  border-bottom: 1px solid #e5e7eb;
}

.modal-header h2 {
  font-size: 18px;
  font-weight: 600;
  color: #1f2937;
}

.close-btn {
  background: transparent;
  border: none;
  color: #9ca3af;
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
  transition: color 0.2s;
}

.close-btn:hover {
  color: #6b7280;
}

.close-btn svg {
  width: 20px;
  height: 20px;
}

.selected-book-info {
  padding: 20px 24px;
  border-bottom: 1px solid #e5e7eb;
}

.book-preview {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px;
  background: #f0fdf4;
  border-radius: 8px;
}

.book-preview-image {
  width: 48px;
  height: 64px;
  object-fit: cover;
  border-radius: 4px;
}

.book-preview-info h3 {
  font-size: 14px;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 4px;
}

.book-preview-info p {
  font-size: 12px;
  color: #6b7280;
}

/* Forms */
.reservation-form,
.add-book-form {
  padding: 20px 24px;
}

.form-group {
  margin-bottom: 16px;
}

.form-group label {
  display: block;
  font-size: 14px;
  font-weight: 500;
  color: #374151;
  margin-bottom: 6px;
}

.form-input,
.form-select {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 14px;
  color: #1f2937;
}

.form-input:focus,
.form-select:focus {
  outline: none;
  border-color: #4ade80;
  box-shadow: 0 0 0 3px rgba(74, 222, 128, 0.1);
}

.date-time-inputs,
.date-range-inputs {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

.date-input-group {
  display: flex;
  flex-direction: column;
}

.date-label {
  font-size: 12px;
  color: #6b7280;
  margin-bottom: 4px;
}

.online-book-fields,
.physical-book-fields {
  border-top: 1px solid #e5e7eb;
  padding-top: 16px;
  margin-top: 16px;
}

.form-actions {
  display: flex;
  gap: 12px;
  padding-top: 16px;
  border-top: 1px solid #e5e7eb;
}

.cancel-btn {
  flex: 1;
  padding: 10px 16px;
  border: 1px solid #d1d5db;
  background: white;
  color: #374151;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 500;
  transition: background-color 0.2s;
}

.cancel-btn:hover {
  background: #f9fafb;
}

.submit-btn {
  flex: 1;
  padding: 10px 16px;
  background: #4ade80;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 500;
  transition: background-color 0.2s;
}

.submit-btn:hover:not(:disabled) {
  background: #22c55e;
}

.submit-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Responsive */
@media (max-width: 768px) {
  .bibliotheque-container {
    padding: 0 15px;
  }

  .search-filters-content {
    flex-direction: column;
    gap: 12px;
  }

  .filter-section {
    min-width: auto;
    width: 100%;
  }

  .books-grid-container {
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 15px;
    padding: 15px;
  }

  .create-post-actions {
    flex-direction: column;
    gap: 8px;
  }

  .action-btn {
    width: 100%;
    justify-content: center;
  }

  .modal-content {
    margin: 20px;
    max-width: none;
  }

  .date-time-inputs,
  .date-range-inputs {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 480px) {
  .books-grid-container {
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 12px;
    padding: 12px;
  }

  .book-info {
    padding: 12px;
  }

  .book-title {
    font-size: 12px;
  }

  .book-author {
    font-size: 11px;
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

.bibliotheque-container {
  max-width: 1200px;
  margin: 0 auto;
}

/* Create Post Card (maintenant pour les livres) */
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

.create-book-text {
  flex: 1;
  padding: 12px 16px;
  background: #f9fafb;
  border: 1px solid #e5e7eb;
  border-radius: 24px;
  color: #6b7280;
  font-size: 16px;
}

.create-post-actions {
  display: flex;
  gap: 12px;
  align-items: center;
}

.action-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 16px;
  background: transparent;
  border: none;
  border-radius: 8px;
  color: #6b7280;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.action-btn:hover {
  background: #f0fdf4;
  color: #4ade80;
}

.action-icon {
  width: 20px;
  height: 20px;
}

/* Posts Feed (maintenant Books Feed) */
.posts-feed {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

/* Search Filter Card */
.search-filter-card {
  padding: 20px !important;
}

.search-filters-content {
  display: flex;
  gap: 16px;
  align-items: center;
}

.search-section {
  flex: 1;
}

.search-input-container {
  position: relative;
  display: flex;
  align-items: center;
}

.search-icon {
  position: absolute;
  left: 12px;
  width: 20px;
  height: 20px;
  color: #9ca3af;
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

.filter-section {
  min-width: 200px;
}

.category-select {
  width: 100%;
  padding: 12px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 14px;
  color: #1f2937;
  background: white;
}

/* Books Grid Container */
.books-grid-container {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 20px;
  padding: 20px;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.book-card {
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  overflow: hidden;
  cursor: pointer;
  transition: all 0.2s;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.book-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  border-color: #4ade80;
}

.book-image-container {
  position: relative;
  aspect-ratio: 3/4;
  background: linear-gradient(135deg, #f0fdf4, #dcfce7);
  overflow: hidden;
}

.book-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.2s;
}

.book-card:hover .book-image {
  transform: scale(1.05);
}

.availability-badge {
  position: absolute;
  top: 8px;
  right: 8px;
}

.badge {
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 500;
}

.badge-online {
  background: #dcfce7;
  color: #166534;
}

.badge-physical {
  background: #fef3c7;
  color: #92400e;
}

.book-info {
  padding: 16px;
}

.book-title {
  font-size: 14px;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 8px;
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.book-author {
  font-size: 12px;
  color: #6b7280;
  margin-bottom: 12px;
}

.book-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.book-category {
  font-size: 10px;
  padding: 2px 8px;
  background: #f0fdf4;
  color: #166534;
  border-radius: 12px;
}

.arrow-icon {
  width: 16px;
  height: 16px;
  color: #9ca3af;
  transition: color 0.2s;
}

.book-card:hover .arrow-icon {
  color: #4ade80;
}

/* Post Card (pour empty state) */
.post-card {
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 60px 20px;
}

.empty-icon {
  width: 64px;
  height: 64px;
  color: #d1d5db;
  margin: 0 auto 16px;
}

.empty-state h3 {
  font-size: 18px;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 8px;
}

.empty-state p {
  color: #6b7280;
}

/* Modal Styles (cohérents avec le thème vert) */
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
  padding: 20px;
  z-index: 1000;
}

.modal-content {
  background: white;
  border-radius: 12px;
  max-width: 500px;
  width: 100%;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 20px 24px;
  border-bottom: 1px solid #e5e7eb;
}

.modal-header h2 {
  font-size: 18px;
  font-weight: 600;
  color: #1f2937;
}

.close-btn {
  background: transparent;
  border: none;
  color: #9ca3af;
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
  transition: color 0.2s;
}

.close-btn:hover {
  color: #6b7280;
}

.close-btn svg {
  width: 20px;
  height: 20px;
}

.selected-book-info {
  padding: 20px 24px;
  border-bottom: 1px solid #e5e7eb;
}

.book-preview {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px;
  background: #f0fdf4;
  border-radius: 8px;
}

.book-preview-image {
  width: 48px;
  height: 64px;
  object-fit: cover;
  border-radius: 4px;
}

.book-preview-info h3 {
  font-size: 14px;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 4px;
}

.book-preview-info p {
  font-size: 12px;
  color: #6b7280;
}

/* Forms */
.reservation-form,
.add-book-form {
  padding: 20px 24px;
}

.form-group {
  margin-bottom: 16px;
}

.form-group label {
  display: block;
  font-size: 14px;
  font-weight: 500;
  color: #374151;
  margin-bottom: 6px;
}

.form-input,
.form-select {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 14px;
  color: #1f2937;
}

.form-input:focus,
.form-select:focus {
  outline: none;
  border-color: #4ade80;
  box-shadow: 0 0 0 3px rgba(74, 222, 128, 0.1);
}

.date-time-inputs,
.date-range-inputs {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

.date-input-group {
  display: flex;
  flex-direction: column;
}

.date-label {
  font-size: 12px;
  color: #6b7280;
  margin-bottom: 4px;
}

.online-book-fields,
.physical-book-fields {
  border-top: 1px solid #e5e7eb;
  padding-top: 16px;
  margin-top: 16px;
}

.form-actions {
  display: flex;
  gap: 12px;
  padding-top: 16px;
  border-top: 1px solid #e5e7eb;
}

.cancel-btn {
  flex: 1;
  padding: 10px 16px;
  border: 1px solid #d1d5db;
  background: white;
  color: #374151;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 500;
  transition: background-color 0.2s;
}

.cancel-btn:hover {
  background: #f9fafb;
}

.submit-btn {
  flex: 1;
  padding: 10px 16px;
  background: #4ade80;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 500;
  transition: background-color 0.2s;
}

.submit-btn:hover:not(:disabled) {
  background: #22c55e;
}

.submit-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Responsive */
@media (max-width: 768px) {
  .bibliotheque-container {
    padding: 0 15px;
  }

  .search-filters-content {
    flex-direction: column;
    gap: 12px;
  }

  .filter-section {
    min-width: auto;
    width: 100%;
  }

  .books-grid-container {
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 15px;
    padding: 15px;
  }

  .create-post-actions {
    flex-direction: column;
    gap: 8px;
  }

  .action-btn {
    width: 100%;
    justify-content: center;
  }

  .modal-content {
    margin: 20px;
    max-width: none;
  }

  .date-time-inputs,
  .date-range-inputs {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 480px) {
  .books-grid-container {
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 12px;
    padding: 12px;
  }

  .book-info {
    padding: 12px;
  }

  .book-title {
    font-size: 12px;
  }

  .book-author {
    font-size: 11px;
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

.bibliotheque-container {
  max-width: 1200px;
  margin: 0 auto;
}

/* Create Post Card (maintenant pour les livres) */
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

.create-book-text {
  flex: 1;
  padding: 12px 16px;
  background: #f9fafb;
  border: 1px solid #e5e7eb;
  border-radius: 24px;
  color: #6b7280;
  font-size: 16px;
}

.create-post-actions {
  display: flex;
  gap: 12px;
  align-items: center;
}

.action-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 16px;
  background: transparent;
  border: none;
  border-radius: 8px;
  color: #6b7280;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.action-btn:hover {
  background: #f0fdf4;
  color: #4ade80;
}

.action-icon {
  width: 20px;
  height: 20px;
}

/* Posts Feed (maintenant Books Feed) */
.posts-feed {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

/* Search Filter Card */
.search-filter-card {
  padding: 20px !important;
}

.search-filters-content {
  display: flex;
  gap: 16px;
  align-items: center;
}

.search-section {
  flex: 1;
}

.search-input-container {
  position: relative;
  display: flex;
  align-items: center;
}

.search-icon {
  position: absolute;
  left: 12px;
  width: 20px;
  height: 20px;
  color: #9ca3af;
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

.filter-section {
  min-width: 200px;
}

.category-select {
  width: 100%;
  padding: 12px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 14px;
  color: #1f2937;
  background: white;
}

/* Books Grid Container */
.books-grid-container {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 20px;
  padding: 20px;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.book-card {
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  overflow: hidden;
  cursor: pointer;
  transition: all 0.2s;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.book-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  border-color: #4ade80;
}

.book-image-container {
  position: relative;
  aspect-ratio: 3/4;
  background: linear-gradient(135deg, #f0fdf4, #dcfce7);
  overflow: hidden;
}

.book-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.2s;
}

.book-card:hover .book-image {
  transform: scale(1.05);
}

.availability-badge {
  position: absolute;
  top: 8px;
  right: 8px;
}

.badge {
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 500;
}

.badge-online {
  background: #dcfce7;
  color: #166534;
}

.badge-physical {
  background: #fef3c7;
  color: #92400e;
}

.book-info {
  padding: 16px;
}

.book-title {
  font-size: 14px;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 8px;
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.book-author {
  font-size: 12px;
  color: #6b7280;
  margin-bottom: 12px;
}

.book-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.book-category {
  font-size: 10px;
  padding: 2px 8px;
  background: #f0fdf4;
  color: #166534;
  border-radius: 12px;
}

.arrow-icon {
  width: 16px;
  height: 16px;
  color: #9ca3af;
  transition: color 0.2s;
}

.book-card:hover .arrow-icon {
  color: #4ade80;
}

/* Post Card (pour empty state) */
.post-card {
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 60px 20px;
}

.empty-icon {
  width: 64px;
  height: 64px;
  color: #d1d5db;
  margin: 0 auto 16px;
}

.empty-state h3 {
  font-size: 18px;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 8px;
}

.empty-state p {
  color: #6b7280;
}

/* Modal Styles (cohérents avec le thème vert) */
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
  padding: 20px;
  z-index: 1000;
}

.modal-content {
  background: white;
  border-radius: 12px;
  max-width: 500px;
  width: 100%;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 20px 24px;
  border-bottom: 1px solid #e5e7eb;
}

.modal-header h2 {
  font-size: 18px;
  font-weight: 600;
  color: #1f2937;
}

.close-btn {
  background: transparent;
  border: none;
  color: #9ca3af;
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
  transition: color 0.2s;
}

.close-btn:hover {
  color: #6b7280;
}

.close-btn svg {
  width: 20px;
  height: 20px;
}

.selected-book-info {
  padding: 20px 24px;
  border-bottom: 1px solid #e5e7eb;
}

.book-preview {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px;
  background: #f0fdf4;
  border-radius: 8px;
}

.book-preview-image {
  width: 48px;
  height: 64px;
  object-fit: cover;
  border-radius: 4px;
}

.book-preview-info h3 {
  font-size: 14px;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 4px;
}

.book-preview-info p {
  font-size: 12px;
  color: #6b7280;
}

/* Forms */
.reservation-form,
.add-book-form {
  padding: 20px 24px;
}

.form-group {
  margin-bottom: 16px;
}

.form-group label {
  display: block;
  font-size: 14px;
  font-weight: 500;
  color: #374151;
  margin-bottom: 6px;
}

.form-input,
.form-select {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 14px;
  color: #1f2937;
}

.form-input:focus,
.form-select:focus {
  outline: none;
  border-color: #4ade80;
  box-shadow: 0 0 0 3px rgba(74, 222, 128, 0.1);
}

.date-time-inputs,
.date-range-inputs {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

.date-input-group {
  display: flex;
  flex-direction: column;
}

.date-label {
  font-size: 12px;
  color: #6b7280;
  margin-bottom: 4px;
}

.online-book-fields,
.physical-book-fields {
  border-top: 1px solid #e5e7eb;
  padding-top: 16px;
  margin-top: 16px;
}

.form-actions {
  display: flex;
  gap: 12px;
  padding-top: 16px;
  border-top: 1px solid #e5e7eb;
}

.cancel-btn {
  flex: 1;
  padding: 10px 16px;
  border: 1px solid #d1d5db;
  background: white;
  color: #374151;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 500;
  transition: background-color 0.2s;
}

.cancel-btn:hover {
  background: #f9fafb;
}

.submit-btn {
  flex: 1;
  padding: 10px 16px;
  background: #4ade80;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 500;
  transition: background-color 0.2s;
}

.submit-btn:hover:not(:disabled) {
  background: #22c55e;
}

.submit-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Responsive */
@media (max-width: 768px) {
  .bibliotheque-container {
    padding: 0 15px;
  }

  .search-filters-content {
    flex-direction: column;
    gap: 12px;
  }

  .filter-section {
    min-width: auto;
    width: 100%;
  }

  .books-grid-container {
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 15px;
    padding: 15px;
  }

  .create-post-actions {
    flex-direction: column;
    gap: 8px;
  }

  .action-btn {
    width: 100%;
    justify-content: center;
  }

  .modal-content {
    margin: 20px;
    max-width: none;
  }

  .date-time-inputs,
  .date-range-inputs {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 480px) {
  .books-grid-container {
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 12px;
    padding: 12px;
  }

  .book-info {
    padding: 12px;
  }

  .book-title {
    font-size: 12px;
  }

  .book-author {
    font-size: 11px;
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

.bibliotheque-container {
  max-width: 1200px;
  margin: 0 auto;
}

/* Create Post Card (maintenant pour les livres) */
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

.create-book-text {
  flex: 1;
  padding: 12px 16px;
  background: #f9fafb;
  border: 1px solid #e5e7eb;
  border-radius: 24px;
  color: #6b7280;
  font-size: 16px;
}

.create-post-actions {
  display: flex;
  gap: 12px;
  align-items: center;
}

.action-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 16px;
  background: transparent;
  border: none;
  border-radius: 8px;
  color: #6b7280;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.action-btn:hover {
  background: #f0fdf4;
  color: #4ade80;
}

.action-icon {
  width: 20px;
  height: 20px;
}

/* Posts Feed (maintenant Books Feed) */
.posts-feed {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

/* Search Filter Card */
.search-filter-card {
  padding: 20px !important;
}

.search-filters-content {
  display: flex;
  gap: 16px;
  align-items: center;
}

.search-section {
  flex: 1;
}

.search-input-container {
  position: relative;
  display: flex;
  align-items: center;
}

.search-icon {
  position: absolute;
  left: 12px;
  width: 20px;
  height: 20px;
  color: #9ca3af;
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

.filter-section {
  min-width: 200px;
}

.category-select {
  width: 100%;
  padding: 12px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 14px;
  color: #1f2937;
  background: white;
}

/* Books Grid Container */
.books-grid-container {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 20px;
  padding: 20px;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.book-card {
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  overflow: hidden;
  cursor: pointer;
  transition: all 0.2s;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.book-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  border-color: #4ade80;
}

.book-image-container {
  position: relative;
  aspect-ratio: 3/4;
  background: linear-gradient(135deg, #f0fdf4, #dcfce7);
  overflow: hidden;
}

.book-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.2s;
}

.book-card:hover .book-image {
  transform: scale(1.05);
}

.availability-badge {
  position: absolute;
  top: 8px;
  right: 8px;
}

.badge {
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 500;
}

.badge-online {
  background: #dcfce7;
  color: #166534;
}

.badge-physical {
  background: #fef3c7;
  color: #92400e;
}

.book-info {
  padding: 16px;
}

.book-title {
  font-size: 14px;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 8px;
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.book-author {
  font-size: 12px;
  color: #6b7280;
  margin-bottom: 12px;
}

.book-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.book-category {
  font-size: 10px;
  padding: 2px 8px;
  background: #f0fdf4;
  color: #166534;
  border-radius: 12px;
}

.arrow-icon {
  width: 16px;
  height: 16px;
  color: #9ca3af;
  transition: color 0.2s;
}

.book-card:hover .arrow-icon {
  color: #4ade80;
}

/* Post Card (pour empty state) */
.post-card {
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 60px 20px;
}

.empty-icon {
  width: 64px;
  height: 64px;
  color: #d1d5db;
  margin: 0 auto 16px;
}

.empty-state h3 {
  font-size: 18px;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 8px;
}

.empty-state p {
  color: #6b7280;
}

/* Modal Styles (cohérents avec le thème vert) */
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
  padding: 20px;
  z-index: 1000;
}

.modal-content {
  background: white;
  border-radius: 12px;
  max-width: 500px;
  width: 100%;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 20px 24px;
  border-bottom: 1px solid #e5e7eb;
}

.modal-header h2 {
  font-size: 18px;
  font-weight: 600;
  color: #1f2937;
}

.close-btn {
  background: transparent;
  border: none;
  color: #9ca3af;
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
  transition: color 0.2s;
}

.close-btn:hover {
  color: #6b7280;
}

.close-btn svg {
  width: 20px;
  height: 20px;
}

.selected-book-info {
  padding: 20px 24px;
}
</style>