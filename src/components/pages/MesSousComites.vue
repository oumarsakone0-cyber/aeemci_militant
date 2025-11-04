<template>
  <div class="min-h-screen bg-gradient-to-br from-emerald-50 via-teal-50 to-green-50 relative overflow-hidden" style="padding-bottom: 80px;">
    <!-- Added decorative background elements -->
    <div class="absolute top-0 left-0 w-96 h-96 bg-emerald-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
    <div class="absolute top-0 right-0 w-96 h-96 bg-teal-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
    <div class="absolute bottom-0 left-1/2 w-96 h-96 bg-green-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-4000"></div>

    <!-- Enhanced header with glassmorphism -->
    <header class="relative backdrop-blur-sm bg-white/80 shadow-lg border-b border-emerald-200/50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
          <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg transform hover:scale-105 transition-transform">
              <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
              </svg>
            </div>
            <div>
              <h1 class="text-2xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">Mes Sous Comités</h1>
              <p class="text-sm text-gray-600">Gérez vos sous comités</p>
            </div>
          </div>
          <div class="flex items-center space-x-4">
            <button
              @click="openAddModal"
              class="px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-xl hover:from-emerald-700 hover:to-teal-700 transition-all duration-300 flex items-center space-x-2 shadow-lg hover:shadow-xl transform hover:scale-105"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
              </svg>
              <span class="hidden sm:inline">Ajouter un sous comité</span>
              <span class="sm:hidden">Ajouter</span>
            </button>
            <div class="text-right hidden sm:block">
              <p class="text-sm font-medium text-emerald-600">{{ sousComites.length }} sous comités</p>
            </div>
          </div>
        </div>
      </div>
    </header>

    <main class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Enhanced search bar with glassmorphism -->
      <div class="backdrop-blur-sm bg-white/80 rounded-2xl shadow-lg border border-emerald-200/50 p-6 mb-8 transform hover:shadow-xl transition-all duration-300">
        <div class="relative">
          <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
          <input
            v-model="searchQuery"
            @input="searchSousComites"
            type="text"
            placeholder="Rechercher un sous comité..."
            class="w-full pl-12 pr-4 py-3.5 border-2 border-emerald-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300 bg-white/50 backdrop-blur-sm"
          />
        </div>
      </div>
        
      <!-- Loading State -->
      <div v-if="isLoading" class="flex flex-col items-center justify-center py-16">
        <div class="relative">
          <div class="animate-spin rounded-full h-16 w-16 border-4 border-emerald-200"></div>
          <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-emerald-600 absolute top-0 left-0"></div>
        </div>
        <p class="mt-6 text-gray-600 font-medium">Chargement des sous comités...</p>
      </div>

      <!-- Enhanced cards grid with better hover effects -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="sousComite in filteredSousComites"
          :key="sousComite.id"
          class="backdrop-blur-sm bg-white/80 rounded-2xl shadow-lg border border-emerald-200/50 overflow-hidden hover:shadow-2xl hover:border-emerald-300 hover:-translate-y-1 transition-all duration-300 group"
        >
          <div class="p-6">
            <div class="flex items-start justify-between mb-4">
              <div class="flex items-center space-x-3 flex-1 min-w-0">
                <div class="w-12 h-12 bg-gradient-to-br from-emerald-100 to-teal-100 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                  <span class="text-emerald-600 font-bold text-base">SC</span>
                </div>
                <div class="min-w-0 flex-1">
                  <h3 class="font-bold text-gray-900 text-left truncate" :title="sousComite.nom_sous_comite">{{ sousComite.nom_sous_comite }}</h3>
                  <p class="text-sm text-gray-500 truncate">Sous Comité - <span class="text-blue-600 font-medium">{{ sousComite.secretariat }}</span></p>
                </div>
              </div>
              <div class="flex space-x-1 flex-shrink-0 ml-2">
                <button
                  @click="editSousComite(sousComite)"
                  class="p-2 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all duration-200 transform hover:scale-110"
                  title="Modifier"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                  </svg>
                </button>
                <button
                  @click="confirmDelete(sousComite.id, sousComite.nom_sous_comite)"
                  class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all duration-200 transform hover:scale-110"
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
                <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span class="text-sm text-gray-700 font-medium truncate">{{ sousComite.nom_president_sc }}</span>
              </div>
              
              <div class="flex items-center space-x-3">
                <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                <span class="text-sm text-gray-600 truncate">{{ sousComite.contact_president_sc }}</span>
              </div>
              
              <div class="flex items-center space-x-3">
                <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                <span class="text-sm text-gray-600 font-mono truncate">{{ sousComite.email_president_sc }}</span>
              </div>
              
              <div class="flex items-center space-x-3">
                <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.22 0L3 15M3 3l7.89 4.26a2 2 0 002.22 0L21 3M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                <span class="text-sm text-gray-600 font-mono truncate">{{ sousComite.matricul_sc }}</span>
              </div>
            </div>
            
            <div class="mt-4 pt-4 border-t border-emerald-100">
              <p class="text-xs text-gray-500">Créé le {{ formatDate(sousComite.created_at) }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="filteredSousComites.length === 0 && !isLoading" class="text-center py-16">
        <div class="w-20 h-20 bg-gradient-to-br from-emerald-100 to-teal-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <svg class="w-10 h-10 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
          </svg>
        </div>
        <h3 class="mt-2 text-lg font-semibold text-gray-900">Aucun sous comité trouvé</h3>
        <p class="mt-1 text-sm text-gray-500">Commencez par ajouter votre premier sous comité.</p>
      </div>
    </main>

    <!-- Enhanced modal with glassmorphism and better animations -->
    <div
      v-if="showAddModal || showEditModal"
      class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4 animate-fadeIn"
      @click="closeModal"
    >
      <div
        class="backdrop-blur-xl bg-white/95 rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto animate-slideUp"
        @click.stop
      >
        <div class="p-6">
          <div class="flex items-center justify-between mb-6 pb-4 border-b border-emerald-200">
            <h2 class="text-xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
              {{ showEditModal ? 'Modifier le sous comité' : (currentStep === 1 ? 'Vérification du président' : 'Ajouter un sous comité') }}
            </h2>
            <button
              @click="closeModal"
              class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl transition-all duration-200 transform hover:scale-110"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>

          <!-- Indicateur d'étapes -->
          <div class="flex items-center justify-center mb-8">
            <div class="flex items-center">
              <div class="flex items-center">
                <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-300" 
                     :class="currentStep >= 1 ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-lg' : 'bg-gray-200 text-gray-500'">
                  1
                </div>
                <span class="ml-2 text-sm font-semibold transition-colors duration-300" 
                      :class="currentStep >= 1 ? 'text-emerald-600' : 'text-gray-500'">
                  Vérification
                </span>
              </div>
              <div class="w-16 h-1 mx-4 rounded-full transition-all duration-300" 
                   :class="currentStep > 1 ? 'bg-gradient-to-r from-emerald-600 to-teal-600' : 'bg-gray-200'"></div>
              <div class="flex items-center">
                <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-300" 
                     :class="currentStep >= 2 ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-lg' : 'bg-gray-200 text-gray-500'">
                  2
                </div>
                <span class="ml-2 text-sm font-semibold transition-colors duration-300" 
                      :class="currentStep >= 2 ? 'text-emerald-600' : 'text-gray-500'">
                  Formulaire
                </span>
              </div>
            </div>
          </div>


          <!-- Étape 1: Vérification du matricule -->
          <div v-if="currentStep === 1 && !showEditModal" class="text-center">
            <div class="w-20 h-20 bg-gradient-to-br from-emerald-100 to-teal-100 rounded-2xl flex items-center justify-center mx-auto mb-6 transform hover:scale-105 transition-transform">
              <svg class="w-10 h-10 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Vérification du président</h3>
            <p class="text-gray-600 mb-6">
              Saisissez le matricule du président pour récupérer automatiquement ses informations.
            </p>
              
            <div class="mb-6">
              <label class="block text-sm font-semibold text-gray-700 mb-2 text-left">Matricule du président *</label>
              <input
                v-model="matriculeInput"
                type="text"
                required
                class="w-full px-4 py-3.5 border-2 border-emerald-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300"
                placeholder="Ex: ABO-SOU-24-002"
                :disabled="isVerifying"
              />
              <div v-if="verificationError" class="mt-2 text-sm text-red-600 font-medium">
                {{ verificationError }}
              </div>
            </div>
              
            <div class="flex gap-3 justify-center">
              <button
                @click="closeModal"
                class="px-6 py-3 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all duration-200 font-medium"
              >
                Annuler
              </button>
              <button
                @click="verifyMatricule"
                :disabled="isVerifying || !matriculeInput.trim()"
                class="px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-xl hover:from-emerald-700 hover:to-teal-700 disabled:from-gray-300 disabled:to-gray-300 disabled:cursor-not-allowed transition-all duration-300 flex items-center gap-2 font-medium shadow-lg hover:shadow-xl transform hover:scale-105 disabled:transform-none"
              >
                <svg v-if="isVerifying" class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ isVerifying ? 'Vérification...' : 'Vérifier le matricule' }}
              </button>
            </div>
          </div>

          <!-- Étape 2: Formulaire de création -->
          <div v-if="currentStep === 2 || showEditModal">
            <!-- Informations récupérées (si ajout) -->
            <div v-if="foundUser && !showEditModal" class="mb-6 p-4 bg-gradient-to-r from-emerald-50 to-teal-50 border-2 border-emerald-200 rounded-xl">
              <div class="flex items-center gap-3">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h4 class="text-sm font-bold text-emerald-800">Informations récupérées avec succès</h4>
              </div>
            </div>

            <form @submit.prevent="submitForm" class="space-y-5">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nom du sous comité *</label>
                <input
                  v-model="form.nom_sous_comite"
                  type="text"
                  required
                  class="w-full px-4 py-3.5 border-2 border-emerald-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300"
                  placeholder="Ex: Abobo"
                />
              </div>

              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Président du sous comité *</label>
                <input
                  v-model="form.nom_president_sc"
                  type="text"
                  required
                  class="w-full px-4 py-3.5 border-2 border-emerald-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300"
                  placeholder="Ex: Mohamed Traoré"
                />
              </div>

              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Numéro du président SC *</label>
                <input
                  v-model="form.contact_president_sc"
                  type="tel"
                  required
                  class="w-full px-4 py-3.5 border-2 border-emerald-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300"
                  placeholder="Ex: 0714157323"
                />
              </div>

              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Email du président SC *</label>
                <input
                  v-model="form.email_president_sc"
                  type="email"
                  required
                  class="w-full px-4 py-3.5 border-2 border-emerald-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300"
                  placeholder="Ex: president@example.com"
                />
              </div>

              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Matricule SC *</label>
                <input
                  v-model="form.matricul_sc"
                  type="text"
                  required
                  class="w-full px-4 py-3.5 border-2 border-emerald-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300"
                  placeholder="Ex: ABO-SC-24-002"
                />
              </div>

              <div class="flex gap-3 justify-end pt-6 border-t border-emerald-200">
                <button
                  v-if="currentStep === 2 && !showEditModal"
                  type="button"
                  @click="backToVerification"
                  class="px-6 py-3 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all duration-200 flex items-center gap-2 font-medium"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                  </svg>
                  Retour
                </button>
                <button
                  type="button"
                  @click="closeModal"
                  class="px-6 py-3 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all duration-200 font-medium"
                >
                  Annuler
                </button>
                <button
                  type="submit"
                  :disabled="isSubmitting"
                  class="px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-xl hover:from-emerald-700 hover:to-teal-700 disabled:from-gray-300 disabled:to-gray-300 disabled:cursor-not-allowed transition-all duration-300 font-medium shadow-lg hover:shadow-xl transform hover:scale-105 disabled:transform-none"
                >
                  {{ isSubmitting ? 'Enregistrement...' : (showEditModal ? 'Modifier' : 'Ajouter') }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Enhanced delete modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4 animate-fadeIn" @click="closeDeleteModal">
      <div class="backdrop-blur-xl bg-white/95 rounded-2xl shadow-2xl max-w-md w-full animate-slideUp" @click.stop>
        <div class="p-6">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-gray-900">Confirmer la suppression</h3>
            <button @click="closeDeleteModal" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl transition-all duration-200">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
          <div class="text-center py-4">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
              </svg>
            </div>
            <p class="text-gray-700 mb-2">Êtes-vous sûr de vouloir supprimer le sous comité</p>
            <p class="font-bold text-gray-900 mb-2">"{{ deleteName }}"</p>
            <p class="text-sm text-red-600 font-medium">Cette action est irréversible !</p>
          </div>
          <div class="flex gap-3 mt-6">
            <button
              @click="closeDeleteModal"
              class="flex-1 px-6 py-3 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all duration-200 font-medium"
            >
              Annuler
            </button>
            <button
              @click="deleteSousComite(deleteId)"
              class="flex-1 px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-xl hover:from-red-700 hover:to-red-800 transition-all duration-300 font-medium shadow-lg hover:shadow-xl transform hover:scale-105"
            >
              Supprimer
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Enhanced notifications -->
    <div class="fixed top-4 right-4 z-[60] max-w-sm space-y-2">
      <div
        v-for="notification in notifications"
        :key="notification.id"
        :class="[
          'backdrop-blur-xl rounded-xl shadow-2xl border-2 p-4 cursor-pointer transform transition-all duration-300 hover:scale-105 animate-slideInRight',
          notification.type === 'success' ? 'bg-emerald-50/95 border-emerald-200 text-emerald-800' : '',
          notification.type === 'error' ? 'bg-red-50/95 border-red-200 text-red-800' : '',
          notification.type === 'warning' ? 'bg-amber-50/95 border-amber-200 text-amber-800' : '',
          notification.type === 'info' ? 'bg-blue-50/95 border-blue-200 text-blue-800' : ''
        ]"
        @click="removeNotification(notification.id)"
      >
        <div class="flex items-start gap-3">
          <div class="flex-shrink-0 text-2xl">
            <span v-if="notification.type === 'success'">✅</span>
            <span v-else-if="notification.type === 'error'">❌</span>
            <span v-else-if="notification.type === 'warning'">⚠️</span>
            <span v-else>ℹ️</span>
          </div>
          <div class="flex-1 min-w-0">
            <p class="font-semibold text-sm">{{ notification.message }}</p>
            <p class="text-xs opacity-75 mt-1">{{ formatTime(notification.timestamp) }}</p>
          </div>
          <button @click.stop="removeNotification(notification.id)" class="flex-shrink-0 text-xl opacity-50 hover:opacity-100 transition-opacity">×</button>
        </div>
      </div>
    </div>

    <!-- Mobile Navigation -->
    <div class="fixed bottom-0 left-0 right-0 backdrop-blur-xl bg-white/90 border-t-2 border-emerald-200 px-4 py-3 md:hidden z-40 shadow-2xl">
      <div class="flex justify-around items-center max-w-md mx-auto">
        <router-link to="/" class="flex flex-col items-center group">
          <svg class="w-6 h-6 text-gray-600 group-hover:text-emerald-600 transition-colors" fill="currentColor" viewBox="0 0 24 24">
            <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
          </svg>
          <span class="text-xs text-gray-600 group-hover:text-emerald-600 mt-1 font-medium transition-colors">Accueil</span>
        </router-link>

        <router-link to="/" class="flex flex-col items-center group">
          <svg class="w-6 h-6 text-emerald-600 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
          </svg>
          <span class="text-xs text-emerald-600 mt-1 font-semibold">Explorer</span>
        </router-link>

        <router-link to="/" class="flex flex-col items-center group">
          <svg class="w-6 h-6 text-gray-600 group-hover:text-emerald-600 transition-colors" fill="currentColor" viewBox="0 0 24 24">
            <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>
          </svg>
          <span class="text-xs text-gray-600 group-hover:text-emerald-600 mt-1 transition-colors">Fichiers</span>
        </router-link>

        <router-link to="/" class="flex flex-col items-center group">
          <svg class="w-6 h-6 text-gray-600 group-hover:text-emerald-600 transition-colors" fill="currentColor" viewBox="0 0 24 24">
            <path d="M3 3h18v2H3V3zm0 4h18v2H3V7zm0 4h18v2H3v-2z"/>
          </svg>
          <span class="text-xs text-gray-600 group-hover:text-emerald-600 mt-1 transition-colors">Onglets</span>
        </router-link>

        <router-link to="/" class="flex flex-col items-center relative group">
          <svg class="w-6 h-6 text-gray-600 group-hover:text-emerald-600 transition-colors" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
          </svg>
          <span class="text-xs text-gray-600 group-hover:text-emerald-600 mt-1 transition-colors">Profile</span>
          <div class="absolute -top-1 -right-1 bg-gradient-to-r from-red-500 to-red-600 w-3 h-3 rounded-full animate-pulse"></div>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useUserStore } from '../../stores/user'

const userStore = useUserStore()

// État
const sousComites = ref([])
const filteredSousComites = ref([])
const searchQuery = ref('')
const isLoading = ref(false)
const showAddModal = ref(false)
const showEditModal = ref(false)
const showDeleteModal = ref(false)
const deleteId = ref(null)
const deleteName = ref('')
const isSubmitting = ref(false)
const notifications = ref([])

// Système de vérification en 2 étapes
const currentStep = ref(1)
const matriculeInput = ref('')
const isVerifying = ref(false)
const verificationError = ref('')
const foundUser = ref(null)

// Formulaire
const form = ref({
  nom_sous_comite: '',
  nom_president_sc: '',
  contact_president_sc: '',
  email_president_sc: '',
  matricul_sc: '',
  secretariat: userStore.user.secretariat || ''
})

const editingId = ref(null)

// Charger les sous comités
const API_BASE_URL = 'https://sogetrag.com/apistage/sous-comites-api.php'
const ORGANES_API_URL = 'https://sogetrag.com/api/organes-api.php'

const loadSousComites = async () => {
  try {
    isLoading.value = true
    console.log('🔍 Tentative de chargement des sous comités...')

    const action = userStore.user.je_suis === 'CE_CE' ? 'listall' : 'list'
    
    const url = `${API_BASE_URL}?action=${action}&zone=${userStore.user.region}&r=${Math.random()}`
    
    console.log(`📡 Requête envoyée à : ${url}`)

    const response = await fetch(url, {
      method: 'GET',
      headers: {
        'Accept': 'application/json'
      }
    })
    
    if (!response.ok) {
      console.error('❌ Erreur HTTP:', response.status, response.statusText)
      addNotification('Erreur lors du chargement des données', 'error')
      return
    }
    
    const data = await response.json()
    console.log('📦 Données reçues:', data)
    
    if (Array.isArray(data)) {
      sousComites.value = [...data]
      filteredSousComites.value = [...data]
      console.log('✅ Sous comités chargés:', sousComites.value.length)
      addNotification(`${sousComites.value.length} sous comité(s) chargé(s)`, 'success')
    } else {
      console.error('❌ Les données ne sont pas un tableau:', data)
      if (data && data.error) {
        addNotification('Erreur API: ' + data.error, 'error')
      } else {
        addNotification('Format de données invalide', 'error')
      }
    }
  } catch (error) {
    console.error('💥 Erreur lors du chargement:', error)
    addNotification('Erreur de connexion: ' + error.message, 'error')
  } finally {
    isLoading.value = false
  }
}

// Recherche
const searchSousComites = () => {
  const query = searchQuery.value.toLowerCase().trim()
  if (!query) {
    filteredSousComites.value = sousComites.value
    return
  }
  
  filteredSousComites.value = sousComites.value.filter(sc =>
    sc.nom_sous_comite?.toLowerCase().includes(query) ||
    sc.nom_president_sc?.toLowerCase().includes(query) ||
    sc.matricul_sc?.toLowerCase().includes(query)
  )
}

// Vérifier le matricule
const verifyMatricule = async () => {
  if (!matriculeInput.value.trim()) {
    verificationError.value = 'Veuillez saisir un matricule'
    return
  }
  
  isVerifying.value = true
  verificationError.value = ''
  foundUser.value = null
  
  try {
    console.log('🔍 Vérification du matricule:', matriculeInput.value)
    
    const response = await fetch(`${ORGANES_API_URL}?action=get_user_by_matricule2`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        matricule: matriculeInput.value.trim()
      })
    })

    const data = await response.json()

    if (data.success && data.user) {
      console.log('✅ Utilisateur trouvé:', data.user)
      foundUser.value = data.user
      
      const nomComplet = data.user.nom + (data.user.prenom ? ' ' + data.user.prenom : '')
      
      const ville = data.user.ville || data.user.lieu_naiss || data.user.lieu || data.user.ville_naissance || data.user.ville_residence || ''
      
      console.log('🏙 Ville détectée:', ville)
      console.log('👤 Données utilisateur complètes:', data.user)
      
      form.value = {
        nom_sous_comite: data.user.ville_commune,
        nom_president_sc: nomComplet,
        contact_president_sc: data.user.telephone || data.user.contact || data.user.phone || '',
        email_president_sc: ('sc_' + data.user.ville_commune + '@aeemci-ce.ci').toLowerCase() || 'error@aeemci-ce.ci',
        matricul_sc: data.user.matricule || matriculeInput.value.trim(),
        secretariat: data.user.region ?? '',
      }
      
      currentStep.value = 2
      addNotification('Informations du président récupérées avec succès!', 'success')
    } else {
      console.log('❌ Aucun utilisateur trouvé')
      verificationError.value = data.error || 'Aucun utilisateur trouvé avec ce matricule'
    }
  } catch (err) {
    console.error('💥 Erreur lors de la vérification:', err)
    verificationError.value = 'Erreur lors de la vérification du matricule'
  } finally {
    isVerifying.value = false
  }
}

// Retour à l'étape 1
const backToVerification = () => {
  currentStep.value = 1
  foundUser.value = null
  resetForm()
}

// Ouvrir modal d'ajout
const openAddModal = () => {
  resetForm()
  currentStep.value = 1
  matriculeInput.value = ''
  verificationError.value = ''
  foundUser.value = null
  showAddModal.value = true
}

// Éditer
const editSousComite = (sousComite) => {
  editingId.value = sousComite.id
  form.value = {
    nom_sous_comite: sousComite.nom_sous_comite,
    nom_president_sc: sousComite.nom_president_sc,
    contact_president_sc: sousComite.contact_president_sc,
    email_president_sc: sousComite.email_president_sc,
    matricul_sc: sousComite.matricul_sc,
    secretariat: sousComite.secretariat
  }
  showEditModal.value = true
}

// Soumettre le formulaire
const submitForm = async () => {
  console.log('🚀 Début de la soumission du formulaire')
  console.log('📋 Données du formulaire:', form.value)
  console.log('🔍 Mode édition:', showEditModal.value)
  
  if (!form.value.nom_sous_comite || !form.value.nom_president_sc || !form.value.contact_president_sc || !form.value.email_president_sc || !form.value.matricul_sc) {
    console.log('❌ Validation échouée - champs manquants')
    addNotification('Veuillez remplir tous les champs obligatoires', 'error')
    return
  }
  
  isSubmitting.value = true
  
  try {
    const action = showEditModal.value ? 'update' : 'create'
    const url = showEditModal.value 
      ? `${API_BASE_URL}?action=${action}&id=${editingId.value}`
      : `${API_BASE_URL}?action=${action}`

    console.log('📡 URL de l\'API:', url)
    console.log('📤 Données envoyées:', form.value)

    const response = await fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(form.value)
    })
    
    console.log('📊 Status de la réponse:', response.status)
    console.log('📊 Status OK:', response.ok)
    
    const data = await response.json()
    console.log('📦 Données reçues:', data)
    
    if (response.ok) {
      console.log('✅ Enregistrement réussi')
      
      if (showEditModal.value) {
        const index = sousComites.value.findIndex(sc => sc.id === editingId.value)
        if (index !== -1) {
          sousComites.value[index] = { ...sousComites.value[index], ...data }
        }
        addNotification('Sous comité modifié avec succès!', 'success')
      } else {
        sousComites.value.unshift(data)
        addNotification('Sous comité ajouté avec succès!', 'success')
      }
      
      closeModal()
      await loadSousComites()
    } else {
      console.error('❌ Erreur de réponse:', data)
      addNotification('Erreur: ' + (data.error || 'Une erreur est survenue'), 'error')
    }
  } catch (error) {
    console.error('💥 Erreur lors de la soumission:', error)
    addNotification('Erreur lors de l\'enregistrement: ' + error.message, 'error')
  } finally {
    isSubmitting.value = false
  }
}

// Confirmer suppression
const confirmDelete = (id, nom) => {
  deleteId.value = id
  deleteName.value = nom
  showDeleteModal.value = true
}

// Supprimer
const deleteSousComite = async (id) => {
  console.log('🗑 Suppression du sous comité ID:', id)
  closeDeleteModal()
  
  try {
    const response = await fetch(`${API_BASE_URL}?action=delete&id=${id}`, {
      method: 'POST'
    })
    
    console.log('📊 Status de la réponse:', response.status)
    console.log('📊 Status OK:', response.ok)
    
    const data = await response.json()
    console.log('📦 Données reçues:', data)
    
    if (response.ok) {
      console.log('✅ Suppression réussie')
      
      const index = sousComites.value.findIndex(sc => sc.id === id)
      if (index !== -1) {
        sousComites.value.splice(index, 1)
        console.log('🗑 Sous comité supprimé de la liste')
      }
      
      addNotification('Sous comité supprimé avec succès!', 'success')
      await loadSousComites()
    } else {
      console.error('❌ Erreur de suppression:', data)
      addNotification('Erreur lors de la suppression: ' + (data.error || 'Une erreur est survenue'), 'error')
    }
  } catch (error) {
    console.error('💥 Erreur lors de la suppression:', error)
    addNotification('Erreur lors de la suppression: ' + error.message, 'error')
  }
}

// Fermer modals
const closeModal = () => {
  showAddModal.value = false
  showEditModal.value = false
  resetForm()
  currentStep.value = 1
  matriculeInput.value = ''
  verificationError.value = ''
  foundUser.value = null
}

const closeDeleteModal = () => {
  showDeleteModal.value = false
  deleteId.value = null
  deleteName.value = ''
}

// Réinitialiser formulaire
const resetForm = () => {
  form.value = {
    nom_sous_comite: '',
    nom_president_sc: '',
    contact_president_sc: '',
    email_president_sc: '',
    matricul_sc: '',
    secretariat: userStore.user.secretariat || ''
  }
  editingId.value = null
}

// Notifications
const addNotification = (message, type = 'info') => {
  const id = Date.now()
  notifications.value.push({
    id,
    message,
    type,
    timestamp: new Date()
  })
  
  setTimeout(() => {
    removeNotification(id)
  }, 5000)
}

const removeNotification = (id) => {
  const index = notifications.value.findIndex(n => n.id === id)
  if (index > -1) {
    notifications.value.splice(index, 1)
  }
}

// Formatage
const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  return date.toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const formatTime = (date) => {
  return date.toLocaleTimeString('fr-FR', {
    hour: '2-digit',
    minute: '2-digit'
  })
}

// Charger au montage
onMounted(async () => {
  console.log('🎯 Composant MesSousComites monté')
  await loadSousComites()
})
</script>

<style scoped>
/* Added animations and improved styles */
@keyframes blob {
  0%, 100% {
    transform: translate(0, 0) scale(1);
  }
  33% {
    transform: translate(30px, -50px) scale(1.1);
  }
  66% {
    transform: translate(-20px, 20px) scale(0.9);
  }
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

@keyframes slideUp {
  from {
    transform: translateY(20px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

@keyframes slideInRight {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

.animate-blob {
  animation: blob 7s infinite;
}

.animation-delay-2000 {
  animation-delay: 2s;
}

.animation-delay-4000 {
  animation-delay: 4s;
}

.animate-fadeIn {
  animation: fadeIn 0.2s ease-out;
}

.animate-slideUp {
  animation: slideUp 0.3s ease-out;
}

.animate-slideInRight {
  animation: slideInRight 0.3s ease-out;
}

/* Responsive adjustments */
@media (max-width: 640px) {
  .backdrop-blur-sm {
    backdrop-filter: blur(8px);
  }
  
  .backdrop-blur-xl {
    backdrop-filter: blur(16px);
  }
}
</style>
