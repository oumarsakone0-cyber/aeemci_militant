<template>
  <div class="min-h-screen bg-gradient-to-br from-emerald-50 via-teal-50 to-green-50 relative overflow-hidden" style="padding-bottom: 80px;">
    <!-- Added decorative background elements -->
    <div class="absolute top-0 left-0 w-96 h-96 bg-emerald-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
    <div class="absolute top-0 right-0 w-96 h-96 bg-teal-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
    <div class="absolute bottom-0 left-1/2 w-96 h-96 bg-green-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-4000"></div>

    <!-- Enhanced header with glassmorphism -->
    <header class="relative backdrop-blur-sm bg-white/80 shadow-lg border-b border-emerald-200/50">
      <div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-8 py-4 sm:py-6">
        <div class="flex items-start justify-between gap-1 sm:gap-2 flex-nowrap">
          <div class="flex items-center space-x-1.5 sm:space-x-2 flex-shrink-0">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg transform hover:scale-105 transition-transform flex-shrink-0">
              <svg class="w-5 h-5 sm:w-7 sm:h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
              </svg>
            </div>
            <div class="flex flex-col min-w-0">
              <h1 class="text-lg sm:text-2xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent whitespace-nowrap">Bureaux SR</h1>
              <p class="text-xs sm:text-sm text-gray-600 whitespace-nowrap">Gérez les bureaux des secrétariats régionaux</p>
            </div>
          </div>
          <div class="flex items-center space-x-1 sm:space-x-2 flex-shrink-0">
            <button
              @click="openAddModal"
              class="px-2 sm:px-4 py-1.5 sm:py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-lg sm:rounded-xl hover:from-emerald-700 hover:to-teal-700 transition-all duration-300 flex items-center space-x-1 shadow-lg hover:shadow-xl transform hover:scale-105 whitespace-nowrap text-xs sm:text-base"
            >
              <svg class="w-3.5 h-3.5 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
              </svg>
              <span class="hidden sm:inline">Ajouter un bureau</span>
              <span class="sm:hidden">Ajouter</span>
            </button>
            <div class="text-right hidden sm:block">
              <p class="text-sm font-medium text-emerald-600">{{ bureaux.length }} bureau(x)</p>
            </div>
          </div>
        </div>
      </div>
    </header>

    <main class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Loading State -->
      <div v-if="isLoading" class="flex flex-col items-center justify-center py-16">
        <div class="relative">
          <div class="animate-spin rounded-full h-16 w-16 border-4 border-emerald-200"></div>
          <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-emerald-600 absolute top-0 left-0"></div>
        </div>
        <p class="mt-6 text-gray-600 font-medium">Chargement des bureaux...</p>
      </div>

      <!-- Enhanced cards grid -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="bureau in bureaux"
          :key="bureau.id"
          class="backdrop-blur-sm bg-white/80 rounded-2xl shadow-lg border border-emerald-200/50 overflow-hidden hover:shadow-2xl hover:border-emerald-300 hover:-translate-y-1 transition-all duration-300 group"
        >
          <div class="p-6">
            <div class="flex items-start justify-between mb-4">
              <div class="flex items-center space-x-3 flex-1 min-w-0">
                <div class="w-12 h-12 bg-gradient-to-br from-emerald-100 to-teal-100 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                  <span class="text-emerald-600 font-bold text-base">B</span>
                </div>
                <div class="min-w-0 flex-1">
                  <h3 class="font-bold text-gray-900 text-left truncate" :title="bureau.nom_bureau">{{ bureau.nom_bureau || 'Bureau SR' }}</h3>
                  <p class="text-sm text-gray-500 truncate">Poste: <span class="text-blue-600 font-medium">{{ bureau.poste || 'N/A' }}</span></p>
                </div>
              </div>
              <div class="flex space-x-1 flex-shrink-0 ml-2">
                <button
                  @click="editBureau(bureau)"
                  class="p-2 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all duration-200 transform hover:scale-110"
                  title="Modifier"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                  </svg>
                </button>
                <button
                  @click="confirmDelete(bureau.id, bureau.nom_bureau)"
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
                <span class="text-sm text-gray-700 font-medium truncate">{{ bureau.nom_president || 'N/A' }}</span>
              </div>
              
              <div class="flex items-center space-x-3" v-if="bureau.telephone">
                <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                <span class="text-sm text-gray-600 truncate">{{ bureau.telephone }}</span>
              </div>
              
              <div class="flex items-center space-x-3" v-if="bureau.email">
                <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                <span class="text-sm text-gray-600 font-mono truncate">{{ bureau.email }}</span>
              </div>
              
              <div class="flex items-center space-x-3">
                <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.22 0L3 15M3 3l7.89 4.26a2 2 0 002.22 0L21 3M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                <span class="text-sm text-gray-600 font-mono truncate">{{ bureau.matricule_membre }}</span>
              </div>
            </div>
            
            <div class="mt-4 pt-4 border-t border-emerald-100">
              <p class="text-xs text-gray-500">Créé le {{ formatDate(bureau.created_at) }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="bureaux.length === 0 && !isLoading" class="text-center py-16">
        <div class="w-20 h-20 bg-gradient-to-br from-emerald-100 to-teal-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <svg class="w-10 h-10 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
          </svg>
        </div>
        <h3 class="mt-2 text-lg font-semibold text-gray-900">Aucun bureau trouvé</h3>
        <p class="mt-1 text-sm text-gray-500">Commencez par ajouter votre premier bureau.</p>
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
              {{ showEditModal ? 'Modifier le bureau' : (currentStep === 1 ? 'Vérification du membre' : 'Ajouter un bureau') }}
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
            <h3 class="text-lg font-bold text-gray-900 mb-2">Vérification du membre</h3>
            <p class="text-gray-600 mb-6">
              Saisissez le matricule du membre pour récupérer automatiquement ses informations.
            </p>
              
            <div class="mb-6">
              <label class="block text-sm font-semibold text-gray-700 mb-2 text-left">Matricule du membre *</label>
              <input
                v-model="matriculeInput"
                type="text"
                required
                class="w-full px-4 py-3.5 border-2 border-emerald-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300"
                placeholder="Ex: BEL-SOU-18-001"
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
              <!-- Matricule du membre (automatique) -->
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Matricule du membre *</label>
                <input
                  v-model="form.matricule_membre"
                  type="text"
                  required
                  class="w-full px-4 py-3.5 border-2 border-emerald-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300"
                  :disabled="true"
                  placeholder="Matricule du membre"
                />
                <p class="text-xs text-gray-500 mt-1">Ce champ est automatiquement rempli</p>
              </div>

              <!-- Nom du bureau -->
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nom du bureau *</label>
                <input
                  v-model="form.nom_bureau"
                  type="text"
                  required
                  class="w-full px-4 py-3.5 border-2 border-emerald-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300"
                  placeholder="Ex: Bureau de Coordination"
                />
              </div>

              <!-- Poste -->
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Poste *</label>
                <select
                  v-model="form.poste"
                  required
                  class="w-full px-4 py-3.5 border-2 border-emerald-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300"
                >
                  <option value="">Sélectionner un poste</option>
                  <option value="Secrétaire Régional">Secrétaire Régional</option>
                  <option value="Vice-Secrétaire Régional">Vice-Secrétaire Régional</option>
                  <option value="Trésorier Régional">Trésorier Régional</option>
                  <option value="Secrétaire à l'Organisation">Secrétaire à l'Organisation</option>
                  <option value="Secrétaire aux Relations Extérieures">Secrétaire aux Relations Extérieures</option>
                  <option value="Secrétaire à la Communication">Secrétaire à la Communication</option>
                  <option value="Secrétaire à l'Éducation">Secrétaire à l'Éducation</option>
                  <option value="Secrétaire aux Activités">Secrétaire aux Activités</option>
                  <option value="Secrétaire à la Culture">Secrétaire à la Culture</option>
                  <option value="Secrétaire aux Sports">Secrétaire aux Sports</option>
                  <option value="Membre du Bureau">Membre du Bureau</option>
                </select>
              </div>

              <!-- Nom du président -->
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nom du président *</label>
                <input
                  v-model="form.nom_president"
                  type="text"
                  required
                  class="w-full px-4 py-3.5 border-2 border-emerald-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300"
                  placeholder="Ex: Mohamed Traoré"
                />
              </div>

              <!-- Nom du vice-président -->
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nom du vice-président</label>
                <input
                  v-model="form.nom_vice_president"
                  type="text"
                  class="w-full px-4 py-3.5 border-2 border-emerald-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300"
                  placeholder="Ex: Fatou Diallo"
                />
              </div>

              <!-- Nom du trésorier -->
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nom du trésorier</label>
                <input
                  v-model="form.nom_tresorier"
                  type="text"
                  class="w-full px-4 py-3.5 border-2 border-emerald-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300"
                  placeholder="Ex: Amadou Coulibaly"
                />
              </div>

              <!-- Nom du secrétaire -->
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nom du secrétaire</label>
                <input
                  v-model="form.nom_secretaire"
                  type="text"
                  class="w-full px-4 py-3.5 border-2 border-emerald-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300"
                  placeholder="Ex: Aissatou Ba"
                />
              </div>

              <!-- Email -->
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                <input
                  v-model="form.email"
                  type="email"
                  class="w-full px-4 py-3.5 border-2 border-emerald-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300"
                  placeholder="Ex: bureau@aeemci.org"
                />
              </div>

              <!-- Téléphone -->
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Téléphone</label>
                <input
                  v-model="form.telephone"
                  type="tel"
                  class="w-full px-4 py-3.5 border-2 border-emerald-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300"
                  placeholder="Ex: +225 07 12 34 56 78"
                />
              </div>

              <!-- Adresse -->
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Adresse du bureau</label>
                <textarea
                  v-model="form.adresse"
                  class="w-full px-4 py-3.5 border-2 border-emerald-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300"
                  rows="3"
                  placeholder="Ex: Rue de la République, Abidjan, Côte d'Ivoire"
                ></textarea>
              </div>

              <!-- Description -->
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Description du bureau</label>
                <textarea
                  v-model="form.description"
                  class="w-full px-4 py-3.5 border-2 border-emerald-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300"
                  rows="4"
                  placeholder="Description du bureau et de ses activités..."
                ></textarea>
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
            <p class="text-gray-700 mb-2">Êtes-vous sûr de vouloir supprimer le bureau</p>
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
              @click="deleteBureau(deleteId)"
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
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { getCurrentUserMatricule } from '../../utils/database.js'

// API spécifique pour les bureaux SR
const BUREAUX_API_URL = 'https://sogetrag.com/apistage/bureaux_sr_api.php'
const ORGANES_API_URL = 'https://sogetrag.com/api/organes-api.php'

const isLoading = ref(false)
const isSubmitting = ref(false)
const bureaux = ref([])

// Modal states
const showAddModal = ref(false)
const showEditModal = ref(false)
const showDeleteModal = ref(false)
const deleteId = ref(null)
const deleteName = ref('')
const notifications = ref([])

// Système de vérification en 2 étapes
const currentStep = ref(1)
const matriculeInput = ref('')
const isVerifying = ref(false)
const verificationError = ref('')
const foundUser = ref(null)

// Form data
const form = ref({
  matricule_membre: '',
  nom_bureau: '',
  poste: '',
  nom_president: '',
  nom_vice_president: '',
  nom_tresorier: '',
  nom_secretaire: '',
  email: '',
  telephone: '',
  adresse: '',
  description: ''
})

const editingId = ref(null)

// Load bureaux
const loadBureaux = async () => {
  isLoading.value = true
  try {
    const matricule = getCurrentUserMatricule()
    if (!matricule) {
      console.error('Matricule non trouvé')
      bureaux.value = []
      return
    }
    
    const response = await fetch(`${BUREAUX_API_URL}?action=get_bureaux&matricule=${matricule}`)
    const result = await response.json()
    
    if (result.success && result.data && Array.isArray(result.data)) {
      bureaux.value = result.data
    } else if (result.success && Array.isArray(result)) {
      bureaux.value = result
    } else {
      bureaux.value = []
    }
  } catch (error) {
    console.error('Erreur lors du chargement des bureaux:', error)
    bureaux.value = []
    addNotification('Erreur lors du chargement des bureaux', 'error')
  } finally {
    isLoading.value = false
  }
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
      
      // Pré-remplir le formulaire avec les informations récupérées
      form.value = {
        matricule_membre: data.user.matricule || matriculeInput.value.trim(),
        nom_bureau: '',
        poste: '',
        nom_president: nomComplet,
        nom_vice_president: '',
        nom_tresorier: '',
        nom_secretaire: '',
        email: data.user.email || '',
        telephone: data.user.telephone || data.user.contact || data.user.phone || '',
        adresse: data.user.ville_commune || data.user.ville || data.user.lieu || '',
        description: ''
      }
      
      currentStep.value = 2
      addNotification('Informations du membre récupérées avec succès!', 'success')
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

// Open add modal
const openAddModal = () => {
  resetForm()
  currentStep.value = 1
  matriculeInput.value = ''
  verificationError.value = ''
  foundUser.value = null
  showAddModal.value = true
}

// Edit bureau
const editBureau = (bureau) => {
  editingId.value = bureau.id
  form.value = {
    matricule_membre: bureau.matricule_membre,
    nom_bureau: bureau.nom_bureau || '',
    poste: bureau.poste || '',
    nom_president: bureau.nom_president || '',
    nom_vice_president: bureau.nom_vice_president || '',
    nom_tresorier: bureau.nom_tresorier || '',
    nom_secretaire: bureau.nom_secretaire || '',
    email: bureau.email || '',
    telephone: bureau.telephone || '',
    adresse: bureau.adresse || '',
    description: bureau.description || ''
  }
  
  showEditModal.value = true
}

// Close modal
const closeModal = () => {
  showAddModal.value = false
  showEditModal.value = false
  resetForm()
  currentStep.value = 1
  matriculeInput.value = ''
  verificationError.value = ''
  foundUser.value = null
}

// Reset form
const resetForm = () => {
  const matricule = getCurrentUserMatricule()
  form.value = {
    matricule_membre: matricule || '',
    nom_bureau: '',
    poste: '',
    nom_president: '',
    nom_vice_president: '',
    nom_tresorier: '',
    nom_secretaire: '',
    email: '',
    telephone: '',
    adresse: '',
    description: ''
  }
  editingId.value = null
}

// Submit form
const submitForm = async () => {
  if (!form.value.matricule_membre || !form.value.nom_bureau || !form.value.poste || !form.value.nom_president) {
    addNotification('Veuillez remplir tous les champs obligatoires', 'error')
    return
  }

  isSubmitting.value = true
  try {
    const data = {
      action: showEditModal.value ? 'update_bureau' : 'create_bureau',
      matricule_membre: form.value.matricule_membre,
      nom_bureau: form.value.nom_bureau,
      poste: form.value.poste,
      nom_president: form.value.nom_president,
      nom_vice_president: form.value.nom_vice_president || null,
      nom_tresorier: form.value.nom_tresorier || null,
      nom_secretaire: form.value.nom_secretaire || null,
      email: form.value.email || null,
      telephone: form.value.telephone || null,
      adresse: form.value.adresse || null,
      description: form.value.description || null,
      ...(showEditModal.value && editingId.value ? { id: editingId.value } : {})
    }

    const response = await fetch(BUREAUX_API_URL, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(data)
    })

    const result = await response.json()

    if (result.success) {
      addNotification(showEditModal.value ? 'Bureau modifié avec succès' : 'Bureau ajouté avec succès', 'success')
      closeModal()
      await loadBureaux()
    } else {
      addNotification(result.error || 'Erreur lors de l\'enregistrement', 'error')
    }
  } catch (error) {
    console.error('Erreur:', error)
    addNotification('Erreur lors de l\'enregistrement du bureau', 'error')
  } finally {
    isSubmitting.value = false
  }
}

// Delete bureau
const confirmDelete = (id, nom) => {
  deleteId.value = id
  deleteName.value = nom || 'ce bureau'
  showDeleteModal.value = true
}

const closeDeleteModal = () => {
  showDeleteModal.value = false
  deleteId.value = null
  deleteName.value = ''
}

const deleteBureau = async () => {
  if (!deleteId.value) return

  try {
    const response = await fetch(BUREAUX_API_URL, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        action: 'delete_bureau',
        id: deleteId.value
      })
    })

    const result = await response.json()

    if (result.success) {
      addNotification('Bureau supprimé avec succès', 'success')
      closeDeleteModal()
      await loadBureaux()
    } else {
      addNotification(result.error || 'Erreur lors de la suppression', 'error')
    }
  } catch (error) {
    console.error('Erreur:', error)
    addNotification('Erreur lors de la suppression du bureau', 'error')
  }
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

onMounted(() => {
  loadBureaux()
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
