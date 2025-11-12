<template>
  <div class="min-h-screen bg-gradient-to-br from-emerald-50 via-teal-50 to-green-50 relative overflow-hidden" style="padding-bottom: 80px;">
    <!-- Decorative background elements -->
    <div class="absolute top-0 left-0 w-96 h-96 bg-emerald-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
    <div class="absolute top-0 right-0 w-96 h-96 bg-teal-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
    <div class="absolute bottom-0 left-1/2 w-96 h-96 bg-green-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-4000"></div>

    <!-- Header -->
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
              <p class="text-xs sm:text-sm text-gray-600 whitespace-nowrap">Gérez vos bureaux et leurs membres</p>
            </div>
          </div>
          <div class="flex items-center space-x-1 sm:space-x-2 flex-shrink-0">
            <button
              v-if="isAuthorized"
              @click="openAddBureauModal"
              class="px-2 sm:px-4 py-1.5 sm:py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-lg sm:rounded-xl hover:from-emerald-700 hover:to-teal-700 transition-all duration-300 flex items-center space-x-1 shadow-lg hover:shadow-xl transform hover:scale-105 whitespace-nowrap text-xs sm:text-base"
            >
              <svg class="w-3.5 h-3.5 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
              </svg>
              <span class="hidden sm:inline">Nouveau bureau</span>
              <span class="sm:hidden">Ajouter</span>
            </button>
          </div>
        </div>
      </div>
    </header>

    <main class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Vérification des droits -->
      <div v-if="checkingAuthorization" class="flex flex-col items-center justify-center py-16">
        <div class="animate-spin rounded-full h-16 w-16 border-4 border-emerald-200 border-t-emerald-600"></div>
        <p class="mt-6 text-gray-600 font-medium">Vérification des droits d'accès...</p>
      </div>

      <!-- Message d'accès non autorisé -->
      <div v-else-if="!isAuthorized" class="text-center py-16">
        <div class="bg-red-50 border-2 border-red-200 rounded-xl p-8 max-w-md mx-auto">
          <svg class="w-16 h-16 text-red-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
          </svg>
          <h3 class="text-xl font-bold text-red-800 mb-2">Accès non autorisé</h3>
          <p class="text-red-700">{{ authorizationMessage }}</p>
        </div>
      </div>

      <!-- Contenu principal -->
      <div v-else>
        <!-- Loading State -->
        <div v-if="isLoading" class="flex flex-col items-center justify-center py-16">
          <div class="animate-spin rounded-full h-16 w-16 border-4 border-emerald-200 border-t-emerald-600"></div>
          <p class="mt-6 text-gray-600 font-medium">Chargement des bureaux...</p>
        </div>

        <!-- Grille des bureaux (3 par ligne) -->
        <div v-else-if="bureaux.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="bureau in bureaux"
            :key="bureau.id"
            class="backdrop-blur-sm bg-white/80 rounded-2xl shadow-lg border border-emerald-200/50 overflow-hidden hover:shadow-2xl hover:border-emerald-300 hover:-translate-y-1 transition-all duration-300 group"
          >
            <div class="p-6">
              <!-- En-tête du bureau -->
              <div class="flex items-start justify-between mb-4">
                <div class="flex items-center space-x-3 flex-1 min-w-0">
                  <div class="w-12 h-12 bg-gradient-to-br from-emerald-100 to-teal-100 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                    <span class="text-emerald-600 font-bold text-base">BR</span>
                  </div>
                  <div class="min-w-0 flex-1">
                    <h3 class="font-bold text-gray-900 text-left truncate" :title="bureau.nom_bureau">{{ bureau.nom_bureau }}</h3>
                    <p v-if="bureau.description" class="text-sm text-gray-500 truncate">{{ bureau.description }}</p>
                  </div>
                </div>
                <div v-if="isAuthorized" class="flex space-x-1 flex-shrink-0 ml-2">
                  <button
                    @click="openAddMembreModal(bureau)"
                    class="p-2 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all duration-200 transform hover:scale-110"
                    title="Ajouter un membre"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                  </button>
                  <button
                    @click="confirmDeleteBureau(bureau.id, bureau.nom_bureau)"
                    class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all duration-200 transform hover:scale-110"
                    title="Supprimer le bureau"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                  </button>
                </div>
              </div>

              <!-- Informations du bureau -->
              <div class="space-y-3">
                <div v-if="bureau.email" class="flex items-center space-x-3">
                  <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                  </svg>
                  <span class="text-sm text-gray-600 font-mono truncate">{{ bureau.email }}</span>
                </div>
                
                <div v-if="bureau.telephone" class="flex items-center space-x-3">
                  <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                  </svg>
                  <span class="text-sm text-gray-600 truncate">{{ bureau.telephone }}</span>
                </div>

                <div v-if="bureau.adresse" class="flex items-center space-x-3">
                  <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                  </svg>
                  <span class="text-sm text-gray-600 truncate">{{ bureau.adresse }}</span>
                </div>

                <!-- Section membres -->
                <div class="pt-2 border-t border-emerald-100">
                  <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center space-x-2">
                      <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                      </svg>
                      <span class="text-sm font-semibold text-gray-700">Membres ({{ bureau.membres ? bureau.membres.length : 0 }})</span>
                    </div>
                    <router-link
                      v-if="bureau.membres && bureau.membres.length > 0"
                      :to="`/bureaux_sr/${bureau.id}/membres`"
                      class="text-xs text-emerald-600 hover:text-emerald-700 font-medium"
                    >
                      Voir tout
                    </router-link>
                  </div>
                  
                  <!-- Liste compacte des membres -->
                  <div v-if="bureau.membres && bureau.membres.length > 0" class="space-y-2">
                    <div
                      v-for="(membre, index) in bureau.membres.slice(0, 2)"
                      :key="membre.id"
                      class="flex items-center space-x-2 text-xs"
                    >
                      <div class="w-6 h-6 bg-emerald-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-emerald-600 font-bold text-xs">{{ (membre.nom_complet || 'M').charAt(0).toUpperCase() }}</span>
                      </div>
                      <div class="flex-1 min-w-0">
                        <p class="text-gray-700 font-medium truncate">{{ membre.nom_complet || 'N/A' }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ membre.poste || 'Membre' }}</p>
                      </div>
                    </div>
                    <div v-if="bureau.membres.length > 2" class="text-xs text-gray-500 pt-1">
                      + {{ bureau.membres.length - 2 }} autre(s)
                    </div>
                  </div>

                  <!-- Message si aucun membre -->
                  <div v-else class="text-center py-2 text-gray-500">
                    <p class="text-xs mb-2">Aucun membre</p>
                    <button
                      v-if="isAuthorized"
                      @click="openAddMembreModal(bureau)"
                      class="px-2 py-1 bg-emerald-600 text-white text-xs rounded hover:bg-emerald-700 transition-all"
                    >
                      Ajouter
                    </button>
                  </div>
                </div>
              </div>
              
              <div class="mt-4 pt-4 border-t border-emerald-100">
                <p class="text-xs text-gray-500">Créé le {{ formatDate(bureau.created_at) }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-16">
          <div class="w-20 h-20 bg-gradient-to-br from-emerald-100 to-teal-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-10 h-10 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
            </svg>
          </div>
          <h3 class="mt-2 text-lg font-semibold text-gray-900">Aucun bureau trouvé</h3>
          <p class="mt-1 text-sm text-gray-500 mb-4">Commencez par créer votre premier bureau.</p>
          <button
            @click="openAddBureauModal"
            class="px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-xl hover:from-emerald-700 hover:to-teal-700 transition-all"
          >
            Créer un bureau
          </button>
        </div>
      </div>
    </main>

    <!-- Modal: Créer un bureau -->
    <div
      v-if="showAddBureauModal"
      class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4"
      @click="closeAddBureauModal"
    >
      <div
        class="backdrop-blur-xl bg-white/95 rounded-2xl shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto"
        @click.stop
      >
        <div class="p-6">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Créer un nouveau bureau</h2>
            <button @click="closeAddBureauModal" class="text-gray-400 hover:text-gray-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>

          <!-- Indicateur d'étapes -->
          <div class="mb-6 flex items-center justify-center space-x-2">
            <div :class="['flex items-center', bureauStep === 1 ? 'text-emerald-600' : 'text-gray-400']">
              <div :class="['w-8 h-8 rounded-full flex items-center justify-center font-bold', bureauStep === 1 ? 'bg-emerald-600 text-white' : 'bg-gray-200']">
                1
              </div>
              <span class="ml-2 text-sm font-medium">Vérification</span>
            </div>
            <div class="w-12 h-0.5 bg-gray-300"></div>
            <div :class="['flex items-center', bureauStep === 2 ? 'text-emerald-600' : 'text-gray-400']">
              <div :class="['w-8 h-8 rounded-full flex items-center justify-center font-bold', bureauStep === 2 ? 'bg-emerald-600 text-white' : 'bg-gray-200']">
                2
              </div>
              <span class="ml-2 text-sm font-medium">Formulaire</span>
            </div>
          </div>

          <!-- Étape 1: Vérification du matricule -->
          <div v-if="bureauStep === 1">
            <div class="mb-6">
              <label class="block text-sm font-semibold text-gray-700 mb-2">Matricule du responsable *</label>
              <input
                v-model="bureauMatriculeInput"
                type="text"
                required
                maxlength="15"
                class="w-full px-4 py-3.5 border-2 border-emerald-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                placeholder="XXX-XXX-XX-XXX"
                :disabled="bureauIsVerifying"
                @input="formatBureauMatricule"
              />
              <div v-if="bureauVerificationError" class="mt-2 text-sm text-red-600 font-medium">
                {{ bureauVerificationError }}
              </div>
            </div>

            <div class="flex gap-3 justify-end">
              <button
                type="button"
                @click="closeAddBureauModal"
                class="px-6 py-3 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all font-medium"
              >
                Annuler
              </button>
              <button
                @click="verifyBureauMatricule"
                :disabled="bureauIsVerifying || !bureauMatriculeInput.trim()"
                class="px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-xl hover:from-emerald-700 hover:to-teal-700 disabled:opacity-50 transition-all flex items-center gap-2 font-medium"
              >
                <svg v-if="bureauIsVerifying" class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                {{ bureauIsVerifying ? 'Vérification...' : 'Vérifier' }}
              </button>
            </div>
          </div>

          <!-- Étape 2: Formulaire de création -->
          <form v-if="bureauStep === 2 && bureauFoundUser" @submit.prevent="createBureau" class="space-y-5">
            <div class="mb-6 p-4 bg-emerald-50 border-2 border-emerald-200 rounded-xl">
              <h4 class="font-semibold text-emerald-800 mb-2">Informations du responsable</h4>
              <p class="text-sm text-gray-700"><strong>Nom:</strong> {{ bureauFoundUser.prenom }} {{ bureauFoundUser.nom }}</p>
              <p class="text-sm text-gray-700"><strong>Matricule:</strong> {{ bureauFoundUser.matricule }}</p>
              <p v-if="bureauFoundUser.qualite_membre" class="text-sm text-gray-700"><strong>Rôle:</strong> {{ bureauFoundUser.qualite_membre }}</p>
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Nom du bureau *</label>
              <input
                v-model="bureauForm.nom_bureau"
                type="text"
                required
                class="w-full px-4 py-3.5 border-2 border-emerald-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                placeholder="Ex: Bureau de Coordination Régionale"
              />
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
              <textarea
                v-model="bureauForm.description"
                rows="3"
                class="w-full px-4 py-3.5 border-2 border-emerald-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                placeholder="Description du bureau..."
              ></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                <input
                  v-model="bureauForm.email"
                  type="email"
                  class="w-full px-4 py-3.5 border-2 border-emerald-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                  :placeholder="bureauFoundUser.email || 'bureau@aeemci.org'"
                />
              </div>

              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Téléphone</label>
                <input
                  v-model="bureauForm.telephone"
                  type="text"
                  class="w-full px-4 py-3.5 border-2 border-emerald-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                  :placeholder="bureauFoundUser.telephone || '+225 XX XX XX XX XX'"
                />
              </div>
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Adresse</label>
              <input
                v-model="bureauForm.adresse"
                type="text"
                class="w-full px-4 py-3.5 border-2 border-emerald-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                placeholder="Adresse du bureau"
              />
            </div>

            <div class="flex gap-3 justify-end pt-4">
              <button
                type="button"
                @click="bureauStep = 1"
                class="px-6 py-3 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all font-medium"
              >
                Retour
              </button>
              <button
                type="submit"
                :disabled="isSubmitting || !bureauForm.nom_bureau.trim()"
                class="px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-xl hover:from-emerald-700 hover:to-teal-700 disabled:opacity-50 transition-all font-medium flex items-center gap-2"
              >
                <svg v-if="isSubmitting" class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                {{ isSubmitting ? 'Création...' : 'Créer le bureau' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal: Ajouter un membre -->
    <div
      v-if="showAddMembreModal"
      class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4"
      @click="closeAddMembreModal"
    >
      <div
        class="backdrop-blur-xl bg-white/95 rounded-2xl shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto"
        @click.stop
      >
        <div class="p-6">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Ajouter un membre</h2>
            <button @click="closeAddMembreModal" class="text-gray-400 hover:text-gray-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>

          <!-- Étape 1: Vérification du matricule -->
          <div v-if="currentStep === 1">
            <div class="mb-6">
              <label class="block text-sm font-semibold text-gray-700 mb-2">Matricule du membre *</label>
              <input
                v-model="matriculeInput"
                type="text"
                required
                maxlength="15"
                class="w-full px-4 py-3.5 border-2 border-emerald-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                placeholder="XXX-XXX-XX-XXX"
                :disabled="isVerifying"
                @input="formatMatricule"
              />
              <div v-if="verificationError" class="mt-2 text-sm text-red-600 font-medium">
                {{ verificationError }}
              </div>
            </div>

            <div class="flex gap-3 justify-end">
              <button
                type="button"
                @click="closeAddMembreModal"
                class="px-6 py-3 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all font-medium"
              >
                Annuler
              </button>
              <button
                @click="verifyMatricule"
                :disabled="isVerifying || !matriculeInput.trim()"
                class="px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-xl hover:from-emerald-700 hover:to-teal-700 disabled:opacity-50 transition-all flex items-center gap-2 font-medium"
              >
                <svg v-if="isVerifying" class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                {{ isVerifying ? 'Vérification...' : 'Vérifier' }}
              </button>
            </div>
          </div>

          <!-- Étape 2: Sélection du poste -->
          <div v-if="currentStep === 2 && foundUser">
            <div class="mb-6 p-4 bg-emerald-50 border-2 border-emerald-200 rounded-xl">
              <h4 class="font-semibold text-emerald-800 mb-2">Informations du membre</h4>
              <p class="text-sm text-gray-700"><strong>Nom:</strong> {{ foundUser.prenom }} {{ foundUser.nom }}</p>
              <p class="text-sm text-gray-700"><strong>Matricule:</strong> {{ foundUser.matricule }}</p>
              <p v-if="foundUser.qualite_membre" class="text-sm text-gray-700"><strong>Poste actuel:</strong> {{ foundUser.qualite_membre }}</p>
            </div>

            <div class="mb-6">
              <label class="block text-sm font-semibold text-gray-700 mb-2">Poste dans le bureau *</label>
              
              <!-- Option pour choisir entre liste et poste personnalisé -->
              <div class="mb-3 flex items-center space-x-4">
                <label class="flex items-center cursor-pointer">
                  <input
                    type="radio"
                    :value="false"
                    v-model="useCustomPoste"
                    class="mr-2 text-emerald-600 focus:ring-emerald-500"
                    @change="membreForm.postePersonnalise = ''"
                  />
                  <span class="text-sm text-gray-700">Sélectionner dans la liste</span>
                </label>
                <label class="flex items-center cursor-pointer">
                  <input
                    type="radio"
                    :value="true"
                    v-model="useCustomPoste"
                    class="mr-2 text-emerald-600 focus:ring-emerald-500"
                    @change="membreForm.poste = ''"
                  />
                  <span class="text-sm text-gray-700">Poste personnalisé</span>
                </label>
              </div>

              <!-- Liste déroulante -->
              <select
                v-if="!useCustomPoste"
                v-model="membreForm.poste"
                required
                class="w-full px-4 py-3.5 border-2 border-emerald-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
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

              <!-- Champ de saisie pour poste personnalisé -->
              <input
                v-else
                v-model="membreForm.postePersonnalise"
                type="text"
                required
                class="w-full px-4 py-3.5 border-2 border-emerald-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                placeholder="Entrez le poste personnalisé"
                maxlength="255"
              />
            </div>

            <div class="flex gap-3 justify-end">
              <button
                type="button"
                @click="currentStep = 1"
                class="px-6 py-3 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all font-medium"
              >
                Retour
              </button>
              <button
                @click="addMembre"
                :disabled="isSubmitting || (!membreForm.poste && !membreForm.postePersonnalise.trim())"
                class="px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-xl hover:from-emerald-700 hover:to-teal-700 disabled:opacity-50 transition-all flex items-center gap-2 font-medium"
              >
                <svg v-if="isSubmitting" class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                {{ isSubmitting ? 'Ajout...' : 'Ajouter le membre' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal: Confirmation suppression bureau -->
    <div
      v-if="showDeleteBureauModal"
      class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4"
      @click="showDeleteBureauModal = false"
    >
      <div class="backdrop-blur-xl bg-white/95 rounded-2xl shadow-2xl max-w-md w-full p-6" @click.stop>
        <h3 class="text-xl font-bold text-gray-900 mb-4">Confirmer la suppression</h3>
        <p class="text-gray-700 mb-6">Êtes-vous sûr de vouloir supprimer le bureau "{{ deleteBureauName }}" ? Tous les membres seront également supprimés.</p>
        <div class="flex gap-3 justify-end">
          <button
            @click="showDeleteBureauModal = false"
            class="px-6 py-3 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all font-medium"
          >
            Annuler
          </button>
          <button
            @click="deleteBureau"
            class="px-6 py-3 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-all font-medium"
          >
            Supprimer
          </button>
        </div>
      </div>
    </div>

    <!-- Modal: Confirmation retrait membre -->
    <div
      v-if="showRemoveMembreModal"
      class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4"
      @click="showRemoveMembreModal = false"
    >
      <div class="backdrop-blur-xl bg-white/95 rounded-2xl shadow-2xl max-w-md w-full p-6" @click.stop>
        <h3 class="text-xl font-bold text-gray-900 mb-4">Retirer le membre</h3>
        <p class="text-gray-700 mb-6">Êtes-vous sûr de vouloir retirer "{{ removeMembreName }}" de ce bureau ?</p>
        <div class="flex gap-3 justify-end">
          <button
            @click="showRemoveMembreModal = false"
            class="px-6 py-3 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all font-medium"
          >
            Annuler
          </button>
          <button
            @click="removeMembre"
            class="px-6 py-3 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-all font-medium"
          >
            Retirer
          </button>
        </div>
      </div>
    </div>

    <!-- Notifications -->
    <div class="fixed top-4 right-4 z-50 space-y-2">
      <div
        v-for="(notif, index) in notifications"
        :key="index"
        :class="[
          'px-6 py-4 rounded-xl shadow-lg backdrop-blur-sm animate-slideIn',
          notif.type === 'success' ? 'bg-emerald-500 text-white' : 'bg-red-500 text-white'
        ]"
      >
        {{ notif.message }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useRoute } from 'vue-router'
import { getCurrentUserMatricule } from '../../utils/database.js'

const route = useRoute()

const BUREAUX_API_URL = 'https://sogetrag.com/apistage/bureaux_sr_api.php'

// États
const isLoading = ref(false)
const isSubmitting = ref(false)
const checkingAuthorization = ref(true)
const isAuthorized = ref(false)
const authorizationMessage = ref('')
const bureaux = ref([])

// Modals
const showAddBureauModal = ref(false)
const showAddMembreModal = ref(false)
const showDeleteBureauModal = ref(false)
const showRemoveMembreModal = ref(false)

// Formulaires
const bureauForm = ref({
  nom_bureau: '',
  description: '',
  email: '',
  telephone: '',
  adresse: ''
})

// Variables pour la création de bureau (2 étapes)
const bureauStep = ref(1)
const bureauMatriculeInput = ref('')
const bureauIsVerifying = ref(false)
const bureauVerificationError = ref('')
const bureauFoundUser = ref(null)

// Variables pour l'ajout de membre (2 étapes)
const matriculeInput = ref('')
const currentStep = ref(1)
const isVerifying = ref(false)
const verificationError = ref('')
const foundUser = ref(null)
const selectedBureauId = ref(null)

const membreForm = ref({
  poste: '',
  postePersonnalise: ''
})
const useCustomPoste = ref(false)

// Édition de poste dans la liste
const editingPosteId = ref(null)
const editingPosteValue = ref('')

// Gestionnaire pour fermer le menu en cliquant en dehors
let handleClickOutside = null

// Suppression
const deleteBureauId = ref(null)
const deleteBureauName = ref('')
const removeMembreId = ref(null)
const removeMembreName = ref('')
const removeMembreBureauId = ref(null)

// Notifications
const notifications = ref([])

// Formatage automatique du matricule (pour ajout de membre)
const formatMatricule = () => {
  const value = matriculeInput.value.toUpperCase()
  let formatted = value.replace(/[^A-Z0-9]/g, '')
  
  if (formatted.length > 3) {
    formatted = formatted.slice(0, 3) + '-' + formatted.slice(3)
  }
  if (formatted.length > 7) {
    formatted = formatted.slice(0, 7) + '-' + formatted.slice(7)
  }
  if (formatted.length > 10) {
    formatted = formatted.slice(0, 10) + '-' + formatted.slice(10)
  }
  if (formatted.length > 15) {
    formatted = formatted.slice(0, 15)
  }
  
  matriculeInput.value = formatted
}

// Formatage automatique du matricule (pour création de bureau)
const formatBureauMatricule = () => {
  const value = bureauMatriculeInput.value.toUpperCase()
  let formatted = value.replace(/[^A-Z0-9]/g, '')
  
  if (formatted.length > 3) {
    formatted = formatted.slice(0, 3) + '-' + formatted.slice(3)
  }
  if (formatted.length > 7) {
    formatted = formatted.slice(0, 7) + '-' + formatted.slice(7)
  }
  if (formatted.length > 10) {
    formatted = formatted.slice(0, 10) + '-' + formatted.slice(10)
  }
  if (formatted.length > 15) {
    formatted = formatted.slice(0, 15)
  }
  
  bureauMatriculeInput.value = formatted
}

// Vérifier le matricule du responsable pour la création de bureau
const verifyBureauMatricule = async () => {
  if (!bureauMatriculeInput.value.trim()) {
    bureauVerificationError.value = 'Veuillez saisir un matricule'
    return
  }

  bureauIsVerifying.value = true
  bureauVerificationError.value = ''
  bureauFoundUser.value = null

  try {
    const response = await fetch(`${BUREAUX_API_URL}?action=get_user_by_matricule&matricule=${encodeURIComponent(bureauMatriculeInput.value.trim())}`)
    const result = await response.json()

    if (result.success && result.data) {
      bureauFoundUser.value = result.data
      // Pré-remplir les champs avec les informations du responsable
      bureauForm.value.email = result.data.email || ''
      bureauForm.value.telephone = result.data.telephone || ''
      bureauStep.value = 2
    } else {
      bureauVerificationError.value = result.error || 'Responsable non trouvé'
    }
  } catch (error) {
    console.error('Erreur vérification:', error)
    bureauVerificationError.value = 'Erreur lors de la vérification'
  } finally {
    bureauIsVerifying.value = false
  }
}

// Vérifier les droits d'accès
const checkAuthorization = async () => {
  checkingAuthorization.value = true
  try {
    const matricule = getCurrentUserMatricule()
    if (!matricule) {
      isAuthorized.value = false
      authorizationMessage.value = 'Matricule non trouvé'
      return
    }

    const response = await fetch(`${BUREAUX_API_URL}?action=check_authorization&matricule=${matricule}`)
    const result = await response.json()

    if (result.success && result.data) {
      isAuthorized.value = result.data.authorized || false
      authorizationMessage.value = result.data.message || 'Accès non autorisé'
    } else {
      isAuthorized.value = false
      authorizationMessage.value = result.error || 'Erreur lors de la vérification'
    }
  } catch (error) {
    console.error('Erreur vérification autorisation:', error)
    isAuthorized.value = false
    authorizationMessage.value = 'Erreur lors de la vérification des droits'
  } finally {
    checkingAuthorization.value = false
  }
}

// Charger les bureaux
const loadBureaux = async () => {
  isLoading.value = true
  try {
    const matricule = getCurrentUserMatricule()
    if (!matricule) {
      addNotification('Matricule non trouvé', 'error')
      return
    }

    const response = await fetch(`${BUREAUX_API_URL}?action=get_bureaux&matricule=${matricule}`)
    
    // Vérifier si la réponse est OK
    if (!response.ok) {
      // Essayer de parser le JSON même si le status n'est pas OK
      let errorData = null
      try {
        const text = await response.text()
        errorData = JSON.parse(text)
      } catch (e) {
        // Si on ne peut pas parser, utiliser le texte brut
        addNotification(`Erreur ${response.status}: ${response.statusText}`, 'error')
        return
      }
      
      // Afficher l'erreur détaillée
      const errorMsg = errorData?.error || `Erreur ${response.status}: ${response.statusText}`
      addNotification(errorMsg, 'error')
      console.error('Erreur API:', errorData)
      bureaux.value = []
      return
    }

    let result = null
    try {
      const text = await response.text()
      result = JSON.parse(text)
    } catch (e) {
      console.error('Erreur parsing réponse loadBureaux:', e)
      bureaux.value = []
      addNotification('Erreur lors du chargement des bureaux', 'error')
      return
    }

    if (result.success && result.data) {
      console.log('✅ Bureaux chargés:', result.data.length, 'bureaux')
      console.log('📋 Détails des bureaux:', result.data.map(b => ({ id: b.id, nom: b.nom_bureau, matricule: b.matricule_responsable || b.matricule_membre })))
      bureaux.value = Array.isArray(result.data) ? result.data : [result.data]
      console.log('📊 Bureaux assignés:', bureaux.value.length)
    } else {
      bureaux.value = []
      const errorMsg = result.error || 'Erreur lors du chargement'
      addNotification(errorMsg, 'error')
      console.error('❌ Erreur dans la réponse:', result)
    }
  } catch (error) {
    console.error('Erreur chargement bureaux:', error)
    bureaux.value = []
    addNotification(`Erreur réseau: ${error.message}`, 'error')
  } finally {
    isLoading.value = false
  }
}


// Créer un bureau
const createBureau = async () => {
  if (!bureauForm.value.nom_bureau.trim()) {
    addNotification('Le nom du bureau est requis', 'error')
    return
  }

  // Utiliser le matricule de l'utilisateur connecté
  const matricule = getCurrentUserMatricule()
  if (!matricule) {
    addNotification('Matricule non trouvé. Veuillez vous reconnecter.', 'error')
    return
  }

  isSubmitting.value = true
  try {
    const response = await fetch(BUREAUX_API_URL, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        action: 'create_bureau',
        matricule_responsable: matricule,
        ...bureauForm.value
      })
    })

    let result = null
    try {
      const text = await response.text()
      result = JSON.parse(text)
    } catch (e) {
      console.error('Erreur parsing réponse:', e)
      addNotification(`Erreur ${response.status}: ${response.statusText}`, 'error')
      isSubmitting.value = false
      return
    }

    if (result.success) {
      console.log('✅ Bureau créé avec succès:', result)
      addNotification('Bureau créé avec succès', 'success')
      closeAddBureauModal()
      // Recharger immédiatement puis à nouveau après un délai pour s'assurer que la base est à jour
      await loadBureaux()
      setTimeout(async () => {
        console.log('🔄 Rechargement des bureaux après création (délai)...')
        await loadBureaux()
      }, 1000)
    } else {
      console.error('❌ Erreur création bureau:', result)
      // Afficher le message d'erreur détaillé de l'API
      const errorMsg = result.error || 'Erreur lors de la création'
      addNotification(errorMsg, 'error')
      // Si c'est une erreur d'autorisation, afficher aussi le rôle détecté
      if (result.code === 403 && bureauFoundUser.value) {
        console.warn('⚠️ Rôle du responsable vérifié:', bureauFoundUser.value.qualite_membre)
      }
    }
  } catch (error) {
    console.error('Erreur création bureau:', error)
    addNotification('Erreur lors de la création du bureau', 'error')
  } finally {
    isSubmitting.value = false
  }
}

// Vérifier le matricule d'un membre
const verifyMatricule = async () => {
  if (!matriculeInput.value.trim()) {
    verificationError.value = 'Veuillez saisir un matricule'
    return
  }

  isVerifying.value = true
  verificationError.value = ''
  foundUser.value = null

  try {
    const response = await fetch(`${BUREAUX_API_URL}?action=get_user_by_matricule&matricule=${encodeURIComponent(matriculeInput.value.trim())}`)
    const result = await response.json()

    if (result.success && result.data) {
      foundUser.value = result.data
      currentStep.value = 2
    } else {
      verificationError.value = result.error || 'Membre non trouvé'
    }
  } catch (error) {
    console.error('Erreur vérification:', error)
    verificationError.value = 'Erreur lors de la vérification'
  } finally {
    isVerifying.value = false
  }
}

// Ajouter un membre au bureau
const addMembre = async () => {
  // Déterminer le poste à utiliser (liste ou personnalisé)
  const posteFinal = useCustomPoste.value && membreForm.value.postePersonnalise.trim() 
    ? membreForm.value.postePersonnalise.trim() 
    : membreForm.value.poste

  if (!posteFinal) {
    addNotification('Veuillez sélectionner ou saisir un poste', 'error')
    return
  }

  if (!selectedBureauId.value) {
    addNotification('Erreur: Bureau non sélectionné', 'error')
    return
  }

  if (!foundUser.value || !foundUser.value.matricule) {
    addNotification('Erreur: Membre non trouvé', 'error')
    return
  }

  isSubmitting.value = true
  try {
    const matricule = getCurrentUserMatricule()
    if (!matricule) {
      addNotification('Matricule non trouvé. Veuillez vous reconnecter.', 'error')
      isSubmitting.value = false
      return
    }
    
    const requestData = {
      action: 'add_membre',
      bureau_id: parseInt(selectedBureauId.value), // S'assurer que c'est un nombre
      matricule_membre: foundUser.value.matricule,
      poste: posteFinal,
      matricule_responsable: matricule
    }
    
    console.log('📤 Ajout membre - Données envoyées:', requestData)
    console.log('📤 Bureau ID type:', typeof requestData.bureau_id, 'value:', requestData.bureau_id)
    
    const response = await fetch(BUREAUX_API_URL, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(requestData)
    })

    // Parser la réponse même si le status n'est pas 200
    let result = null
    try {
      const text = await response.text()
      result = JSON.parse(text)
    } catch (e) {
      console.error('Erreur parsing réponse:', e)
      addNotification(`Erreur ${response.status}: ${response.statusText}`, 'error')
      isSubmitting.value = false
      return
    }

    if (result.success) {
      addNotification('Membre ajouté avec succès', 'success')
      // Fermer le modal et réinitialiser les états
      showAddMembreModal.value = false
      currentStep.value = 1
      matriculeInput.value = ''
      verificationError.value = ''
      foundUser.value = null
      membreForm.value.poste = ''
      membreForm.value.postePersonnalise = ''
      useCustomPoste.value = false
      selectedBureauId.value = null
      editingPosteId.value = null
      editingPosteValue.value = null
      // Recharger les bureaux
      await loadBureaux()
    } else {
      // Afficher le message d'erreur détaillé de l'API
      const errorMsg = result.error || `Erreur ${response.status}: ${response.statusText}`
      addNotification(errorMsg, 'error')
      console.error('Erreur API add_membre:', result)
    }
  } catch (error) {
    console.error('Erreur ajout membre:', error)
    addNotification(`Erreur réseau: ${error.message}`, 'error')
  } finally {
    isSubmitting.value = false
  }
}

// Liste des postes standards
const postesStandards = [
  'Secrétaire Régional',
  'Vice-Secrétaire Régional',
  'Trésorier Régional',
  'Secrétaire à l\'Organisation',
  'Secrétaire aux Relations Extérieures',
  'Secrétaire à la Communication',
  'Secrétaire à l\'Éducation',
  'Secrétaire aux Activités',
  'Secrétaire à la Culture',
  'Secrétaire aux Sports',
  'Membre du Bureau'
]

// Vérifier si un poste est dans la liste standard
const isPosteInList = (poste) => {
  return postesStandards.includes(poste)
}

// Ouvrir le menu pour modifier le poste
const openPosteMenu = (membreId, bureauId) => {
  // Si le menu est déjà ouvert pour ce membre, le fermer
  if (editingPosteId.value === membreId) {
    cancelEditPoste()
    return
  }
  
  // Récupérer le membre pour pré-remplir le poste actuel
  const membre = bureaux.value
    .flatMap(b => b.membres || [])
    .find(m => m.id === membreId)
  
  editingPosteId.value = membreId
  editingPosteValue.value = null // null = afficher le select, string = afficher l'input
}

// Gérer le changement de poste
const handlePosteChange = (membreId, nouveauPoste, bureauId) => {
  if (nouveauPoste === '__CUSTOM__') {
    // Afficher le champ de saisie pour poste personnalisé
    // Récupérer le poste actuel du membre pour pré-remplir
    const membre = bureaux.value
      .flatMap(b => b.membres || [])
      .find(m => m.id === membreId)
    editingPosteValue.value = membre?.poste || ''
  } else if (nouveauPoste && nouveauPoste !== '') {
    // Poste sélectionné dans la liste
    updateMembrePoste(membreId, nouveauPoste, bureauId)
  }
}

// Sauvegarder le poste personnalisé
const saveCustomPoste = async (membreId, bureauId) => {
  if (!editingPosteValue.value || !editingPosteValue.value.trim()) {
    cancelEditPoste()
    return
  }
  
  await updateMembrePoste(membreId, editingPosteValue.value.trim(), bureauId)
  cancelEditPoste()
}

// Annuler l'édition du poste
const cancelEditPoste = () => {
  editingPosteId.value = null
  editingPosteValue.value = null
}

// Modifier le poste d'un membre
const updateMembrePoste = async (membreId, nouveauPoste, bureauId) => {
  if (!nouveauPoste) return

  try {
    const matricule = getCurrentUserMatricule()
    const response = await fetch(BUREAUX_API_URL, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        action: 'update_membre_poste',
        membre_id: membreId,
        poste: nouveauPoste,
        matricule_responsable: matricule
      })
    })

    const result = await response.json()

    if (result.success) {
      addNotification('Poste modifié avec succès', 'success')
      await loadBureaux()
    } else {
      addNotification(result.error || 'Erreur lors de la modification', 'error')
    }
  } catch (error) {
    console.error('Erreur modification poste:', error)
    addNotification('Erreur lors de la modification du poste', 'error')
  }
}

// Ouvrir modal ajouter membre
const openAddMembreModal = (bureau) => {
  selectedBureauId.value = bureau.id
  currentStep.value = 1
  matriculeInput.value = ''
  verificationError.value = ''
  foundUser.value = null
  membreForm.value.poste = ''
  membreForm.value.postePersonnalise = ''
  useCustomPoste.value = false
  showAddMembreModal.value = true
}

// Retirer un membre
const confirmRemoveMembre = (membreId, nom, bureauId) => {
  removeMembreId.value = membreId
  removeMembreName.value = nom
  removeMembreBureauId.value = bureauId
  showRemoveMembreModal.value = true
}

const removeMembre = async () => {
  try {
    const matricule = getCurrentUserMatricule()
    const response = await fetch(BUREAUX_API_URL, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        action: 'remove_membre',
        membre_id: removeMembreId.value,
        matricule_responsable: matricule
      })
    })

    const result = await response.json()

    if (result.success) {
      addNotification('Membre retiré avec succès', 'success')
      showRemoveMembreModal.value = false
      await loadBureaux()
    } else {
      addNotification(result.error || 'Erreur lors du retrait', 'error')
    }
  } catch (error) {
    console.error('Erreur retrait membre:', error)
    addNotification('Erreur lors du retrait du membre', 'error')
  }
}

// Supprimer un bureau
const confirmDeleteBureau = (id, nom) => {
  deleteBureauId.value = id
  deleteBureauName.value = nom
  showDeleteBureauModal.value = true
}

const deleteBureau = async () => {
  try {
    const matricule = getCurrentUserMatricule()
    const response = await fetch(BUREAUX_API_URL, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        action: 'delete_bureau',
        bureau_id: deleteBureauId.value,
        matricule_responsable: matricule
      })
    })

    const result = await response.json()

    if (result.success) {
      addNotification('Bureau supprimé avec succès', 'success')
      showDeleteBureauModal.value = false
      await loadBureaux()
    } else {
      addNotification(result.error || 'Erreur lors de la suppression', 'error')
    }
  } catch (error) {
    console.error('Erreur suppression bureau:', error)
    addNotification('Erreur lors de la suppression du bureau', 'error')
  }
}

// Modals
const openAddBureauModal = async () => {
  // Récupérer automatiquement les infos de l'utilisateur connecté
  const matricule = getCurrentUserMatricule()
  if (!matricule) {
    addNotification('Matricule non trouvé. Veuillez vous reconnecter.', 'error')
    return
  }

  // Charger les infos de l'utilisateur
  try {
    const response = await fetch(`${BUREAUX_API_URL}?action=get_user_by_matricule&matricule=${encodeURIComponent(matricule)}`)
    const result = await response.json()
    
    if (result.success && result.data) {
      bureauFoundUser.value = result.data
      bureauForm.value.email = result.data.email || ''
      bureauForm.value.telephone = result.data.telephone || ''
      bureauStep.value = 2 // Passer directement à l'étape 2
    } else {
      addNotification('Erreur lors du chargement de vos informations', 'error')
      return
    }
  } catch (error) {
    console.error('Erreur chargement utilisateur:', error)
    addNotification('Erreur lors du chargement de vos informations', 'error')
    return
  }

  bureauMatriculeInput.value = ''
  bureauVerificationError.value = ''
  bureauForm.value.nom_bureau = ''
  bureauForm.value.description = ''
  bureauForm.value.adresse = ''
  showAddBureauModal.value = true
}

const closeAddBureauModal = () => {
  showAddBureauModal.value = false
  bureauStep.value = 1
  bureauMatriculeInput.value = ''
  bureauVerificationError.value = ''
  bureauFoundUser.value = null
  bureauForm.value = {
    nom_bureau: '',
    description: '',
    email: '',
    telephone: '',
    adresse: ''
  }
}

const closeAddMembreModal = () => {
  showAddMembreModal.value = false
  currentStep.value = 1
  matriculeInput.value = ''
  verificationError.value = ''
  foundUser.value = null
  membreForm.value.poste = ''
  membreForm.value.postePersonnalise = ''
  useCustomPoste.value = false
  selectedBureauId.value = null
}

// Notifications
const addNotification = (message, type = 'success') => {
  notifications.value.push({ message, type })
  setTimeout(() => {
    notifications.value.shift()
  }, 3000)
}

// Format date
const formatDate = (date) => {
  if (!date) return 'N/A'
  const d = new Date(date)
  return d.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' })
}


// Obtenir les postes uniques
const getUniquePostes = (membres) => {
  if (!membres || membres.length === 0) return []
  const postes = membres.map(m => m.poste).filter(p => p)
  return [...new Set(postes)]
}

// Obtenir la date du dernier membre ajouté
const getLastMemberDate = (membres) => {
  if (!membres || membres.length === 0) return 'N/A'
  const dates = membres.map(m => m.created_at).filter(d => d).sort().reverse()
  if (dates.length > 0) {
    return formatDate(dates[0])
  }
  return 'N/A'
}

// Initialisation
onMounted(async () => {
  // Gestionnaire pour fermer le menu de poste en cliquant en dehors
  handleClickOutside = (event) => {
    if (editingPosteId.value && !event.target.closest('.relative')) {
      cancelEditPoste()
    }
  }
  document.addEventListener('click', handleClickOutside)
  await checkAuthorization()
  if (isAuthorized.value) {
    await loadBureaux()
    // Si un bureau_id est passé en paramètre, ouvrir le modal d'ajout de membre
    const bureauId = route.query.bureau_id
    if (bureauId) {
      const foundBureau = bureaux.value.find(b => b.id == bureauId)
      if (foundBureau) {
        openAddMembreModal(foundBureau)
      }
    }
  }
})

// Nettoyer l'event listener au démontage
onUnmounted(() => {
  if (handleClickOutside) {
    document.removeEventListener('click', handleClickOutside)
  }
})
</script>

<style scoped>
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

.animate-blob {
  animation: blob 7s infinite;
}

.animation-delay-2000 {
  animation-delay: 2s;
}

.animation-delay-4000 {
  animation-delay: 4s;
}

@keyframes slideIn {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

.animate-slideIn {
  animation: slideIn 0.3s ease-out;
}
</style>
