<template>
  <div class="min-h-screen bg-gradient-to-br from-emerald-50 via-teal-50 to-green-50 relative overflow-hidden pb-24 md:pb-8">
    <div class="absolute top-0 left-0 w-96 h-96 bg-emerald-400/20 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2"></div>
    <div class="absolute bottom-0 right-0 w-96 h-96 bg-teal-400/20 rounded-full blur-3xl translate-x-1/2 translate-y-1/2"></div>
    <div class="absolute top-1/2 left-1/2 w-96 h-96 bg-green-400/10 rounded-full blur-3xl"></div>

    <header class="relative backdrop-blur-sm bg-white/80 shadow-lg border-b border-emerald-200/50 sticky top-0 z-40">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
          <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-500/30 transform hover:scale-105 transition-transform">
              <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
              </svg>
            </div>
            <div>
              <h1 class="text-xl md:text-2xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
                Bibliothèque Islamique
              </h1>
              <p class="text-xs md:text-sm text-gray-600">Découvrez notre collection</p>
            </div>
          </div>
          <div class="flex items-center gap-3">
            <button
              @click="showAddBookModal = true"
              class="px-3 md:px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl hover:shadow-lg hover:shadow-emerald-500/50 transition-all duration-300 flex items-center space-x-2 text-sm md:text-base active:scale-95"
            >
              <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
              </svg>
              <span class="hidden sm:inline">Ajouter</span>
            </button>
            <div class="hidden md:block text-right">
              <p class="text-sm font-semibold text-emerald-600">{{ books.length }}</p>
              <p class="text-xs text-gray-500">livres</p>
            </div>
          </div>
        </div>
      </div>
    </header>

    <main class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-8">
      <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl border border-emerald-100/50 p-4 md:p-6 mb-6 md:mb-8 hover:shadow-2xl transition-shadow duration-300">
        <div class="flex flex-col lg:flex-row gap-3 md:gap-4">
          <div class="flex-1">
            <div class="relative group">
              <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-emerald-500 group-focus-within:text-emerald-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
              </svg>
              <input
                v-model="searchQuery"
                @input="searchBooks"
                type="text"
                placeholder="Rechercher un livre..."
                class="w-full pl-10 pr-4 py-3 border-2 border-emerald-100 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300 bg-white/50 backdrop-blur-sm"
              />
            </div>
          </div>
          
          <div class="lg:w-64">
            <select
              v-model="selectedCategory"
              @change="filterBooks"
              class="w-full px-4 py-3 border-2 border-emerald-100 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300 bg-white/50 backdrop-blur-sm"
            >
              <option value="">Toutes les catégories</option>
              <option v-for="category in categories" :key="category" :value="category">
                {{ category }}
              </option>
            </select>
          </div>
        </div>
      </div>

      <div class="hidden md:grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 md:gap-6">
        <div
          v-for="book in filteredBooks"
          :key="book.id"
          @click="handleBookClick(book)"
          class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-lg border border-emerald-100/50 overflow-hidden hover:shadow-2xl hover:border-emerald-300 hover:-translate-y-2 transition-all duration-300 cursor-pointer group"
        >
          <div class="aspect-[3/4] bg-gradient-to-br from-emerald-100 to-teal-100 relative overflow-hidden">
            <img
              :src="book.image_url || 'https://upload.wikimedia.org/wikipedia/fr/4/42/Logo_AEEMCI.jpeg'"
              :alt="book.titre"
              class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
              @error="(e) => e.target.src = 'https://upload.wikimedia.org/wikipedia/fr/4/42/Logo_AEEMCI.jpeg'"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="absolute top-3 right-3">
              <span
                :class="[
                  'px-3 py-1 rounded-full text-xs font-semibold backdrop-blur-sm shadow-lg',
                  book.disponibilite === 'online' 
                    ? 'bg-green-500/90 text-white' 
                    : 'bg-amber-500/90 text-white'
                ]"
              >
                {{ book.disponibilite === 'online' ? '📖 En ligne' : '🏢 Au siège' }}
              </span>
            </div>
          </div>
          
          <div class="p-4">
            <h3 class="font-bold text-gray-900 mb-2 line-clamp-2 group-hover:text-emerald-600 transition-colors text-sm">
              {{ book.titre }}
            </h3>
            <p class="text-xs text-gray-600 mb-3">{{ book.auteur }}</p>
            <div class="flex items-center justify-between">
              <span class="text-xs px-3 py-1 bg-gradient-to-r from-emerald-50 to-teal-50 text-emerald-700 rounded-full font-medium border border-emerald-200">
                {{ book.category }}
              </span>
              <svg class="w-5 h-5 text-gray-400 group-hover:text-emerald-500 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
              </svg>
            </div>
          </div>
        </div>
      </div>

      <div class="md:hidden space-y-3">
        <div
          v-for="book in filteredBooks"
          :key="book.id"
          @click="handleBookClick(book)"
          class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-lg border border-emerald-100/50 overflow-hidden hover:shadow-xl hover:border-emerald-300 transition-all duration-300 cursor-pointer active:scale-98"
        >
          <div class="flex p-3">
            <div class="w-20 h-28 bg-gradient-to-br from-emerald-100 to-teal-100 rounded-xl overflow-hidden flex-shrink-0 shadow-md">
              <img
                :src="book.image_url || 'https://upload.wikimedia.org/wikipedia/fr/4/42/Logo_AEEMCI.jpeg'"
                :alt="book.titre"
                class="w-full h-full object-cover"
                @error="(e) => e.target.src = 'https://upload.wikimedia.org/wikipedia/fr/4/42/Logo_AEEMCI.jpeg'"
              />
            </div>
            <div class="ml-3 flex-1 min-w-0">
              <div class="flex items-start justify-between gap-2">
                <div class="flex-1 min-w-0">
                  <h3 class="font-bold text-gray-900 mb-1 line-clamp-2 text-sm">
                    {{ book.titre }}
                  </h3>
                  <p class="text-xs text-gray-600 mb-2">{{ book.auteur }}</p>
                  <span class="inline-block text-xs px-2 py-1 bg-gradient-to-r from-emerald-50 to-teal-50 text-emerald-700 rounded-full font-medium border border-emerald-200">
                    {{ book.category }}
                  </span>
                </div>
                <div class="flex flex-col items-end gap-2">
                  <span
                    :class="[
                      'px-2 py-1 rounded-full text-xs font-semibold whitespace-nowrap',
                      book.disponibilite === 'online' 
                        ? 'bg-green-500 text-white' 
                        : 'bg-amber-500 text-white'
                    ]"
                  >
                    {{ book.disponibilite === 'online' ? '📖' : '🏢' }}
                  </span>
                  <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                  </svg>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-if="filteredBooks.length === 0" class="text-center py-16">
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-emerald-100/50 p-8 max-w-md mx-auto">
          <svg class="mx-auto w-20 h-20 text-emerald-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
          </svg>
          <h3 class="text-lg font-bold text-gray-900 mb-2">Aucun livre trouvé</h3>
          <p class="text-gray-600 text-sm">Essayez de modifier vos critères de recherche.</p>
        </div>
      </div>
    </main>

    <div
      v-if="showModal"
      style="z-index: 1111"
      class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 z-50 animate-fade-in"
      @click="closeModal"
    >
      <div
        class="bg-white/95 backdrop-blur-md rounded-3xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto border border-emerald-200/50 animate-scale-in"
        @click.stop
      >
        <div class="p-6">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
              Réservation de livre
            </h2>
            <button
              @click="closeModal"
              class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full p-2 transition-all"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>

          <div class="mb-6">
            <div class="flex items-center space-x-3 p-3 bg-gradient-to-r from-emerald-50 to-teal-50 rounded-2xl border border-emerald-200">
              <img
                :src="selectedBook?.image_url || 'https://upload.wikimedia.org/wikipedia/fr/4/42/Logo_AEEMCI.jpeg'"
                :alt="selectedBook?.titre"
                class="w-12 h-16 object-cover rounded-lg shadow-md"
                @error="(e) => e.target.src = 'https://upload.wikimedia.org/wikipedia/fr/4/42/Logo_AEEMCI.jpeg'"
              />
              <div>
                <h3 class="font-semibold text-gray-900 text-sm">{{ selectedBook?.titre }}</h3>
                <p class="text-xs text-gray-600">{{ selectedBook?.auteur }}</p>
              </div>
            </div>
          </div>

          <form @submit.prevent="submitReservation" class="space-y-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2 text-left">Matricule</label>
              <input
                v-model="reservationForm.matricule"
                type="text"
                readonly
                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl bg-gray-50"
              />
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2 text-left">Nom complet</label>
              <input
                v-model="reservationForm.name"
                type="text"
                required
                class="w-full px-4 py-3 border-2 border-emerald-100 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
              />
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2 text-left">Contact</label>
              <input
                v-model="reservationForm.contact"
                type="text"
                required
                placeholder="Email ou téléphone"
                class="w-full px-4 py-3 border-2 border-emerald-100 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
              />
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2 text-left">Type de consultation</label>
              <select
                v-model="reservationForm.consultationType"
                required
                class="w-full px-4 py-3 border-2 border-emerald-100 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
              >
                <option value="">Sélectionner une option</option>
                <option value="on-site">Lecture au siège</option>
                <option value="borrow">Emprunt pour lecture externe</option>
              </select>
            </div>

            <div v-if="reservationForm.consultationType === 'on-site'">
              <label class="block text-sm font-semibold text-gray-700 mb-2 text-left">Date et heure de lecture</label>
              <div class="grid grid-cols-2 gap-3">
                <input
                  v-model="reservationForm.readingDate"
                  type="date"
                  required
                  class="w-full px-4 py-3 border-2 border-emerald-100 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                />
                <input
                  v-model="reservationForm.readingTime"
                  type="time"
                  required
                  class="w-full px-4 py-3 border-2 border-emerald-100 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                />
              </div>
            </div>

            <div v-if="reservationForm.consultationType === 'borrow'">
              <label class="block text-sm font-semibold text-gray-700 mb-2 text-left">Période d'emprunt</label>
              <div class="grid grid-cols-2 gap-3">
                <div>
                  <label class="block text-xs text-gray-500 mb-1 text-left">Date de début</label>
                  <input
                    v-model="reservationForm.startDate"
                    type="date"
                    required
                    class="w-full px-4 py-3 border-2 border-emerald-100 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                  />
                </div>
                <div>
                  <label class="block text-xs text-gray-500 mb-1 text-left">Date de fin</label>
                  <input
                    v-model="reservationForm.endDate"
                    type="date"
                    required
                    class="w-full px-4 py-3 border-2 border-emerald-100 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                  />
                </div>
              </div>
            </div>

            <div class="flex space-x-3 pt-4">
              <button
                type="button"
                @click="closeModal"
                class="flex-1 px-4 py-3 border-2 border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition-all font-semibold"
              >
                Annuler
              </button>
              <button
                type="submit"
                :disabled="isSubmitting"
                class="flex-1 px-4 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl hover:shadow-lg hover:shadow-emerald-500/50 transition-all disabled:opacity-50 font-semibold"
              >
                {{ isSubmitting ? 'Envoi...' : 'Confirmer' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div
      v-if="showConfirmationModal"
      style="z-index: 1111"
      class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 z-50 animate-fade-in"
      @click="closeConfirmationModal"
    >
      <div
        class="bg-white/95 backdrop-blur-md rounded-3xl shadow-2xl max-w-md w-full border border-emerald-200/50 animate-scale-in overflow-hidden"
        @click.stop
      >
        <div class="bg-gradient-to-br from-emerald-500 to-teal-600 px-6 pt-8 pb-6 text-center">
          <div class="mx-auto w-20 h-20 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center mb-4 animate-bounce-once">
            <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
          </div>
          <h2 class="text-2xl font-bold text-white mb-2">
            Réservation confirmée !
          </h2>
          <p class="text-emerald-50 text-sm">
            Votre demande a été prise en compte
          </p>
        </div>

        <div class="p-6 space-y-4">
          <div class="bg-gradient-to-r from-emerald-50 to-teal-50 rounded-2xl p-4 border border-emerald-200">
            <div class="flex items-center space-x-3 mb-3">
              <img
                :src="confirmedReservation?.book?.image_url || 'https://upload.wikimedia.org/wikipedia/fr/4/42/Logo_AEEMCI.jpeg'"
                :alt="confirmedReservation?.book?.titre"
                class="w-12 h-16 object-cover rounded-lg shadow-md"
                @error="(e) => e.target.src = 'https://upload.wikimedia.org/wikipedia/fr/4/42/Logo_AEEMCI.jpeg'"
              />
              <div class="flex-1 min-w-0">
                <h3 class="font-bold text-gray-900 text-sm line-clamp-2">
                  {{ confirmedReservation?.book?.titre }}
                </h3>
                <p class="text-xs text-gray-600">
                  {{ confirmedReservation?.book?.auteur }}
                </p>
              </div>
            </div>
            
            <div class="space-y-2">
              <div class="flex items-center text-xs text-gray-700">
                <svg class="w-4 h-4 text-emerald-600 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span class="font-medium">{{ confirmedReservation?.name }}</span>
              </div>
              
              <div class="flex items-center text-xs text-gray-700">
                <svg class="w-4 h-4 text-emerald-600 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span>
                  {{ confirmedReservation?.consultationType === 'on-site' ? 
                    `${confirmedReservation?.readingDate} à ${confirmedReservation?.readingTime}` :
                    `Du ${confirmedReservation?.startDate} au ${confirmedReservation?.endDate}`
                  }}
                </span>
              </div>
              
              <div class="flex items-center text-xs text-gray-700">
                <svg class="w-4 h-4 text-emerald-600 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span>
                  {{ confirmedReservation?.consultationType === 'on-site' ? 
                    'Lecture au siège' : 
                    'Emprunt pour lecture externe'
                  }}
                </span>
              </div>
            </div>
          </div>


          <button
            @click="closeConfirmationModal"
            class="w-full px-4 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl hover:shadow-lg hover:shadow-emerald-500/50 transition-all font-semibold active:scale-95"
          >
            Continuer ma navigation
          </button>
        </div>
      </div>
    </div>

    <div
      v-if="showAddBookModal"
      style="z-index: 1111"
      class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 z-50 animate-fade-in"
      @click="closeAddBookModal"
    >
      <div
        class="bg-white/95 backdrop-blur-md rounded-3xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto border border-emerald-200/50 animate-scale-in"
        @click.stop
      >
        <div class="p-6">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
              Ajouter un livre
            </h2>
            <button
              @click="closeAddBookModal"
              class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full p-2 transition-all"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>

          <form @submit.prevent="submitAddBook" class="space-y-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2 text-left">Matricule</label>
              <input
                v-model="addBookForm.matricule"
                type="text"
                :readonly="true"
                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl bg-gray-50"
              />
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2 text-left">Nom</label>
              <input
                v-model="addBookForm.name"
                type="text"
                required
                class="w-full px-4 py-3 border-2 border-emerald-100 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
              />
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2 text-left">Type de livre</label>
              <select
                v-model="addBookForm.type"
                required
                class="w-full px-4 py-3 border-2 border-emerald-100 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
              >
                <option value="">Sélectionner un type</option>
                <option value="online">En ligne</option>
                <option value="physical">Physique</option>
              </select>
            </div>

            <div v-if="addBookForm.type === 'online'" class="space-y-4">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2 text-left">Titre du livre</label>
                <input
                  v-model="addBookForm.title"
                  type="text"
                  required
                  class="w-full px-4 py-3 border-2 border-emerald-100 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                />
              </div>

              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2 text-left">Nom de l'auteur</label>
                <input
                  v-model="addBookForm.author"
                  type="text"
                  required
                  class="w-full px-4 py-3 border-2 border-emerald-100 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                />
              </div>

              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2 text-left">Catégorie</label>
                <select
                  v-model="addBookForm.category"
                  required
                  class="w-full px-4 py-3 border-2 border-emerald-100 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
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

              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2 text-left">Image du livre</label>
                <input
                  @change="handleBookImageUpload"
                  type="file"
                  accept="image/*"
                  required
                  class="w-full px-4 py-3 border-2 border-emerald-100 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                />
                <div v-if="isUploadingImage" class="mt-3 flex items-center space-x-2 p-3 bg-blue-50 rounded-xl">
                  <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-emerald-600"></div>
                  <span class="text-sm text-gray-600 font-medium">Téléchargement en cours...</span>
                </div>
                <div v-if="imagePreview && !isUploadingImage" class="mt-3">
                  <div class="flex items-center space-x-3 p-3 bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-xl">
                    <img :src="imagePreview" alt="Aperçu" class="w-12 h-16 object-cover rounded-lg shadow-md" />
                    <div class="flex-1">
                      <p class="text-sm font-semibold text-green-800">Image téléchargée</p>
                      <p class="text-xs text-green-600">Prête à être ajoutée</p>
                    </div>
                    <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                  </div>
                </div>
              </div>

              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2 text-left">Fichier PDF</label>
                <input
                  @change="handlePdfUpload"
                  type="file"
                  accept=".pdf"
                  required
                  class="w-full px-4 py-3 border-2 border-emerald-100 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                />
                <div v-if="isUploadingPdf" class="mt-3">
                  <div class="flex items-center space-x-2 mb-2 p-3 bg-blue-50 rounded-xl">
                    <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-emerald-600"></div>
                    <span class="text-sm text-gray-600 font-medium">Téléchargement du PDF...</span>
                  </div>
                  <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                    <div 
                      class="bg-gradient-to-r from-emerald-500 to-teal-600 h-3 rounded-full transition-all duration-300 shadow-lg" 
                      :style="{ width: pdfUploadProgress + '%' }"
                    ></div>
                  </div>
                  <p class="text-xs text-gray-500 mt-1 font-medium">{{ pdfUploadProgress }}%</p>
                </div>
                <div v-if="pdfPreview && !isUploadingPdf" class="mt-3">
                  <div class="flex items-center space-x-3 p-3 bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-xl">
                    <div class="w-12 h-16 bg-red-100 rounded-lg flex items-center justify-center shadow-md">
                      <svg class="w-7 h-7 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                      </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                      <p class="text-sm font-semibold text-blue-800 truncate">{{ pdfPreview.name }}</p>
                      <p class="text-xs text-blue-600">{{ pdfPreview.size }} - Téléchargé</p>
                    </div>
                    <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                  </div>
                </div>
              </div>
            </div>

            <div v-if="addBookForm.type === 'physical'" class="space-y-4">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2 text-left">Nom du livre</label>
                <input
                  v-model="addBookForm.title"
                  type="text"
                  required
                  class="w-full px-4 py-3 border-2 border-emerald-100 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                />
              </div>
              
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2 text-left">Image du livre (optionnel)</label>
                <input
                  @change="handleBookImageUpload"
                  type="file"
                  accept="image/*"
                  class="w-full px-4 py-3 border-2 border-emerald-100 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                />
                <p class="text-xs text-gray-500 mt-1">Vous pouvez ajouter une image du livre si vous le souhaitez</p>
                <div v-if="isUploadingImage" class="mt-3 flex items-center space-x-2 p-3 bg-blue-50 rounded-xl">
                  <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-emerald-600"></div>
                  <span class="text-sm text-gray-600 font-medium">Téléchargement en cours...</span>
                </div>
                <div v-if="imagePreview && !isUploadingImage" class="mt-3">
                  <div class="flex items-center space-x-3 p-3 bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-xl">
                    <img :src="imagePreview" alt="Aperçu" class="w-12 h-16 object-cover rounded-lg shadow-md" />
                    <div class="flex-1">
                      <p class="text-sm font-semibold text-green-800">Image téléchargée</p>
                      <p class="text-xs text-green-600">Prête à être ajoutée</p>
                    </div>
                    <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                  </div>
                </div>
              </div>
            </div>

            <div class="flex space-x-3 pt-4">
              <button
                type="button"
                @click="closeAddBookModal"
                class="flex-1 px-4 py-3 border-2 border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition-all font-semibold"
              >
                Annuler
              </button>
              <button
                v-if="canSubmitBook"
                type="submit"
                :disabled="isAddingBook"
                class="flex-1 px-4 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl hover:shadow-lg hover:shadow-emerald-500/50 transition-all disabled:opacity-50 font-semibold"
              >
                {{ isAddingBook ? 'Ajout...' : 'Ajouter' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { uploadToCloudinary } from '../../utils/cloudinary.js'
import { useUserStore } from "../../stores/user";

const userStore = useUserStore();

const API_BASE_URL = 'https://sogetrag.com/api/bibliotheque.php'

const searchQuery = ref('')
const selectedCategory = ref('')
const showModal = ref(false)
const showAddBookModal = ref(false)
const showConfirmationModal = ref(false)
const confirmedReservation = ref(null)

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

const filteredBooks = computed(() => books.value)

const loadBooks = async () => {
  try {
    const randomValue = Math.random()
    const response = await fetch(`${API_BASE_URL}?action=books&_=${randomValue}`)
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

const handleBookClick = (book) => {
  selectedBook.value = book
  
  if (book.disponibilite === 'online') {
    window.open(book.pdf_url, '_blank')
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

const closeConfirmationModal = () => {
  showConfirmationModal.value = false
  confirmedReservation.value = null
}

const closeAddBookModal = () => {
  showAddBookModal.value = false
  resetAddBookForm()
}

const resetReservationForm = () => {
  reservationForm.value = {
    matricule: userStore.user?.matricule_gen || "INCONNU",
    name: `${userStore.user?.nom || ""} ${userStore.user?.prenom || ""}`.trim() || "Nom Inconnu",
    contact: userStore.user?.contact || "Contact Inconnu",
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
    matricule : userStore.user?.matricule_gen || "INCONNU",
    name : userStore.user?.nom || "Nom Inconnu",
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

    const response = await fetch(`${API_BASE_URL}?action=create_reservation&type=hotel`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(reservationData)
    })
    
    const data = await response.json()
    
    if (data.success) {
      confirmedReservation.value = {
        book: selectedBook.value,
        name: reservationForm.value.name,
        contact: reservationForm.value.contact,
        consultationType: reservationForm.value.consultationType,
        readingDate: reservationForm.value.readingDate,
        readingTime: reservationForm.value.readingTime,
        startDate: reservationForm.value.startDate,
        endDate: reservationForm.value.endDate
      }
      
      closeModal()
      showConfirmationModal.value = true
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
      image_url: addBookForm.value.imageUrl || 'https://upload.wikimedia.org/wikipedia/fr/4/42/Logo_AEEMCI.jpeg',
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
  addBookForm.value.matricule = userStore.user?.matricule_gen || "INCONNU";
  addBookForm.value.name = userStore.user?.nom || "Nom Inconnu";
  reservationForm.value.matricule = userStore.user?.matricule_gen || "INCONNU";
  reservationForm.value.name = `${userStore.user?.nom || ""} ${userStore.user?.prenom || ""}`.trim() || "Nom Inconnu";
  reservationForm.value.contact = userStore.user?.contact || "Contact Inconnu";
  
  await loadBooks()
  await loadCategories()
})
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.max-h-\[90vh\]::-webkit-scrollbar {
  width: 8px;
}

.max-h-\[90vh\]::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 4px;
}

.max-h-\[90vh\]::-webkit-scrollbar-thumb {
  background: linear-gradient(to bottom, #10b981, #14b8a6);
  border-radius: 4px;
}

.max-h-\[90vh\]::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(to bottom, #059669, #0d9488);
}

@keyframes fade-in {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

@keyframes scale-in {
  from {
    opacity: 0;
    transform: scale(0.95);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

@keyframes bounce-once {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-10px);
  }
}

.animate-fade-in {
  animation: fade-in 0.2s ease-out;
}

.animate-scale-in {
  animation: scale-in 0.3s ease-out;
}

.animate-bounce-once {
  animation: bounce-once 0.6s ease-in-out;
}

.active\:scale-98:active {
  transform: scale(0.98);
}

.active\:scale-95:active {
  transform: scale(0.95);
}
</style>
