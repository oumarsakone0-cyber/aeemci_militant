<template>
  <div class="min-h-screen bg-gradient-to-br from-emerald-50 via-teal-50 to-green-50 relative overflow-hidden">
    <!-- Background blobs -->
    <div class="absolute top-0 left-0 w-96 h-96 bg-emerald-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 blob-1"></div>
    <div class="absolute top-0 right-0 w-96 h-96 bg-teal-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 blob-2"></div>
    <div class="absolute bottom-0 left-1/2 w-96 h-96 bg-green-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 blob-3"></div>

    <!-- Header -->
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
              <h1 class="text-2xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">Mes Sections</h1>
              <p class="text-sm text-gray-600">Gérez vos sections</p>
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
              <span class="hidden sm:inline">Ajouter une section</span>
              <span class="sm:hidden">Ajouter</span>
            </button>
            <div class="text-right hidden sm:block">
              <p class="text-sm font-medium text-emerald-600">{{ sections.length }} sections</p>
            </div>
          </div>
        </div>
      </div>
    </header>

    <main class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Filters -->
      <div class="backdrop-blur-sm bg-white/80 rounded-2xl shadow-lg border border-emerald-200/50 p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Filtrer par sous-comité</label>
            <select
              v-model="selectedSousComiteId"
              @change="filterBySousComite"
              class="w-full px-4 py-3 border-2 border-emerald-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300"
            >
              <option value="">Tous les sous-comités</option>
              <option v-for="sc in sousComites" :key="sc.id" :value="sc.id">
                {{ sc.nom_sous_comite }}
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Rechercher</label>
            <div class="relative">
              <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
              </svg>
              <input
                v-model="searchQuery"
                @input="searchSections"
                type="text"
                placeholder="Rechercher une section..."
                class="w-full pl-12 pr-4 py-3 border-2 border-emerald-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300"
              />
            </div>
          </div>
        </div>
      </div>
        
      <!-- Loading State -->
      <div v-if="isLoading" class="flex flex-col items-center justify-center py-16">
        <div class="relative">
          <div class="animate-spin rounded-full h-16 w-16 border-4 border-emerald-200"></div>
          <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-emerald-600 absolute top-0 left-0" style="animation: spin 0.8s linear reverse infinite;"></div>
        </div>
        <p class="mt-6 text-gray-600 font-medium">Chargement des sections...</p>
      </div>

      <!-- Sections Grid -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="section in filteredSections"
          :key="section.id"
          class="backdrop-blur-sm bg-white/80 rounded-2xl shadow-lg border border-emerald-200/50 overflow-hidden hover:shadow-2xl hover:border-emerald-300 hover:-translate-y-1 transition-all duration-300 group"
        >
          <div class="p-6">
            <div class="flex items-start justify-between mb-4">
              <div class="flex items-center space-x-3 flex-1 min-w-0">
                <div class="w-12 h-12 bg-gradient-to-br from-emerald-100 to-teal-100 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                  <span class="text-emerald-600 font-bold text-base">SEC</span>
                </div>
                <div class="min-w-0 flex-1">
                  <h3 class="font-bold text-gray-900 text-left truncate" :title="section.nom_section">{{ section.nom_section }}</h3>
                  <p class="text-sm text-gray-500 truncate">Section - <span class="text-blue-600 font-medium">{{ getSousComiteName(section.sous_comite_id) }}</span></p>
                </div>
              </div>
              <div class="flex space-x-1 flex-shrink-0 ml-2">
                <button
                  @click="editSection(section)"
                  class="p-2 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all duration-200 transform hover:scale-110"
                  title="Modifier"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                  </svg>
                </button>
                <button
                  @click="confirmDelete(section.id, section.nom_section)"
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
                <span class="text-sm text-gray-700 font-medium truncate">{{ section.responsable || 'N/A' }}</span>
              </div>
              
              <div class="flex items-center space-x-3">
                <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                <span class="text-sm text-gray-600 truncate">{{ section.contact_responsable || 'N/A' }}</span>
              </div>
              
              <div class="flex items-center space-x-3">
                <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                <span class="text-sm text-gray-600 font-mono truncate">{{ section.email_responsable || 'N/A' }}</span>
              </div>
              
              <div class="flex items-center space-x-3">
                <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.22 0L3 15"/>
                </svg>
                <span class="text-sm text-gray-600 font-mono truncate">{{ section.matricule_section }}</span>
              </div>

              <!-- Added ville and commune display -->
            

              <div class="flex items-center space-x-3">
                <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <span class="text-sm text-gray-600 truncate">{{ section.commune || 'N/A' }}</span>
              </div>
              
              
            </div>
            
            <div class="mt-4 pt-4 border-t border-emerald-100">
              <p class="text-xs text-gray-500">Créé le {{ formatDate(section.created_at) }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="filteredSections.length === 0 && !isLoading" class="text-center py-16">
        <div class="w-20 h-20 bg-gradient-to-br from-emerald-100 to-teal-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <svg class="w-10 h-10 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
          </svg>
        </div>
        <h3 class="mt-2 text-lg font-semibold text-gray-900">Aucune section trouvée</h3>
        <p class="mt-1 text-sm text-gray-500">Commencez par ajouter votre première section.</p>
      </div>
    </main>

    <!-- Add/Edit Modal -->
    <div
      v-if="showAddModal || showEditModal"
      class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4"
      style="animation: fadeIn 0.2s ease-out;"
      @click="closeModal"
    >
      <div
        class="backdrop-blur-xl bg-white/95 rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto"
        style="animation: slideUp 0.3s ease-out;"
        @click.stop
      >
        <div class="p-6">
          <div class="flex items-center justify-between mb-6 pb-4 border-b border-emerald-200">
            <h2 class="text-xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
              {{ showEditModal ? 'Modifier la section' : (currentStep === 1 ? 'Vérification du responsable' : 'Ajouter une section') }}
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

          <!-- Step indicator for 2-step flow -->
          <div v-if="!showEditModal" class="flex items-center justify-center mb-8">
            <div class="flex items-center">
              <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-300" 
                   :class="currentStep >= 1 ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-lg' : 'bg-gray-200 text-gray-500'">
                1
              </div>
              <span class="ml-2 text-sm font-semibold transition-colors duration-300" 
                    :class="currentStep >= 1 ? 'text-emerald-600' : 'text-gray-500'">
                Vérification
              </span>
              <div class="w-16 h-1 mx-4 rounded-full transition-all duration-300" 
                   :class="currentStep > 1 ? 'bg-gradient-to-r from-emerald-600 to-teal-600' : 'bg-gray-200'"></div>
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

          <!-- Step 1: Matricule Verification -->
          <div v-if="currentStep === 1 && !showEditModal" class="text-center">
            <div class="w-20 h-20 bg-gradient-to-br from-emerald-100 to-teal-100 rounded-2xl flex items-center justify-center mx-auto mb-6 transform hover:scale-105 transition-transform">
              <svg class="w-10 h-10 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Vérification du responsable</h3>
            <p class="text-gray-600 mb-6">
              Saisissez le matricule du responsable pour récupérer automatiquement ses informations.
            </p>
              
            <div class="mb-6">
              <label class="block text-sm font-semibold text-gray-700 mb-2 text-left">Matricule du responsable *</label>
              <div class="relative">
                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-emerald-600">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                </div>
                <input
                  v-model="matriculeInput"
                  type="text"
                  maxlength="15"
                  @input="validateMatricule"
                  required
                  class="w-full pl-12 pr-4 py-3.5 border-2 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300"
                  :class="verificationError ? 'border-red-300' : 'border-emerald-200 focus:border-emerald-500'"
                  placeholder="XXX-XXX-XX-XXX"
                  :disabled="isVerifying"
                />
              </div>
              <div v-if="verificationError" class="mt-2 text-sm text-red-600 font-medium flex items-center gap-1">
                <span class="w-1 h-1 bg-red-500 rounded-full"></span>
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

          <!-- Step 2: Form with retrieved information -->
          <div v-if="currentStep === 2 || showEditModal">
            <!-- Info retrieved banner -->
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
                <label class="block text-sm font-semibold text-gray-700 mb-2">Sous-comité *</label>
                <select
                  v-model="form.sous_comite_id"
                  required
                  class="w-full px-4 py-3 border-2 border-emerald-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300"
                >
                  <option value="">Sélectionner un sous-comité</option>
                  <option v-for="sc in sousComites" :key="sc.id" :value="sc.id">
                    {{ sc.nom_sous_comite }}
                  </option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nom de la section *</label>
                <input
                  v-model="form.nom_section"
                  type="text"
                  required
                  class="w-full px-4 py-3 border-2 border-emerald-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300"
                  placeholder="Ex: Section Abobo"
                />
              </div>

              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Responsable</label>
                <input
                  v-model="form.responsable"
                  type="text"
                  class="w-full px-4 py-3 border-2 border-emerald-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300"
                  placeholder="Ex: Mohamed Traoré"
                />
              </div>

              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Contact responsable</label>
                <input
                  v-model="form.contact_responsable"
                  type="tel"
                  class="w-full px-4 py-3 border-2 border-emerald-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300"
                  placeholder="Ex: 0714157323"
                />
              </div>

              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Email responsable</label>
                <input
                  v-model="form.email_responsable"
                  type="email"
                  class="w-full px-4 py-3 border-2 border-emerald-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300"
                  placeholder="Ex: responsable@example.com"
                />
              </div>

              <!-- Matricule section en lecture seule pour l'ajout, éditable pour la modification -->
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                  Matricule section * 
                  <span v-if="!showEditModal" class="text-xs text-emerald-600 font-normal">(généré automatiquement)</span>
                </label>
                <div class="relative">
                  <div class="absolute left-4 top-1/2 -translate-y-1/2 text-emerald-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.22 0L3 15"/>
                    </svg>
                  </div>
                  <input
                    v-model="form.matricule_section"
                    type="text"
                    maxlength="15"
                    required
                    :readonly="!showEditModal"
                    class="w-full pl-12 pr-4 py-3 border-2 rounded-xl transition-all duration-300 font-mono"
                    :class="showEditModal ? 'border-emerald-200 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500' : 'bg-emerald-50 border-emerald-300 cursor-not-allowed'"
                    placeholder="XXX-SEC-YY-NNN"
                  />
                </div>
                <p v-if="!showEditModal" class="mt-1 text-xs text-gray-500">
                  Le matricule est généré automatiquement lors de la création
                </p>
              </div>

              <!-- Added ville and commune fields -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-semibold text-gray-700 mb-2">Ville *</label>
                  <input
                    v-model="form.ville"
                    type="text"
                    required
                    class="w-full px-4 py-3 border-2 border-emerald-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300"
                    placeholder="Ex: Abobo"
                  />
                </div>

                <div>
                  <label class="block text-sm font-semibold text-gray-700 mb-2">Commune *</label>
                  <input
                    v-model="form.commune"
                    type="text"
                    required
                    class="w-full px-4 py-3 border-2 border-emerald-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300"
                    placeholder="Ex: Abobo Commune"
                  />
                </div>
              </div>

              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Statut</label>
                <select
                  v-model="form.status"
                  class="w-full px-4 py-3 border-2 border-emerald-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300"
                >
                  <option value="active">Actif</option>
                  <option value="inactive">Inactif</option>
                </select>
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
                <!-- Bouton ajouter désactivé seulement pendant la soumission -->
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

    <!-- Delete Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4" style="animation: fadeIn 0.2s ease-out;" @click="closeDeleteModal">
      <div class="backdrop-blur-xl bg-white/95 rounded-2xl shadow-2xl max-w-md w-full" style="animation: slideUp 0.3s ease-out;" @click.stop>
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
            <p class="text-gray-700 mb-2">Êtes-vous sûr de vouloir supprimer</p>
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
              @click="deleteSection(deleteId)"
              class="flex-1 px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-xl hover:from-red-700 hover:to-red-800 transition-all duration-300 font-medium shadow-lg hover:shadow-xl transform hover:scale-105"
            >
              Supprimer
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Notifications -->
    <div class="fixed top-4 right-4 z-50 max-w-sm space-y-2">
      <div
        v-for="notification in notifications"
        :key="notification.id"
        :class="[
          'backdrop-blur-xl rounded-xl shadow-2xl border-2 p-4 cursor-pointer transform transition-all duration-300 hover:scale-105',
          notification.type === 'success' ? 'bg-emerald-50/95 border-emerald-200 text-emerald-800' : '',
          notification.type === 'error' ? 'bg-red-50/95 border-red-200 text-red-800' : '',
          notification.type === 'warning' ? 'bg-amber-50/95 border-amber-200 text-amber-800' : '',
          notification.type === 'info' ? 'bg-blue-50/95 border-blue-200 text-blue-800' : ''
        ]"
        style="animation: slideInRight 0.3s ease-out;"
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
import { ref, onMounted, computed,watch } from 'vue'
import { useUserStore } from '../../stores/user'
import Secretariat from './Secretariat.vue'

let userStore = null

const sections = ref([])
const sousComites = ref([])
const filteredSections = ref([])
const searchQuery = ref('')
const selectedSousComiteId = ref('')
const isLoading = ref(false)
const showAddModal = ref(false)
const showEditModal = ref(false)
const showDeleteModal = ref(false)
const deleteId = ref(null)
const deleteName = ref('')
const isSubmitting = ref(false)
const notifications = ref([])
const communes = ref([])
const matriculeError = ref('')
const checkingMatricule = ref(false)

const form = ref({
  nom_section: '',
  sous_comite_id: '',
  responsable: '',
  nom_sous_comite: sousComites.value.find(sc => sc.id === form.value.sous_comite_id)?.nom_sous_comite || '',
  contact_responsable: '',
  email_responsable: '',
  matricule_section: '',
  Secretariat:'',
  ville: '',
  commune: '',
  status: 'active'
})

const editingId = ref(null)

const SECTIONS_API_URL = 'https://sogetrag.com/apistage/sections.php'
const SOUS_COMITES_API_URL = 'https://sogetrag.com/apistage/sous-comites-api.php'

const currentStep = ref(1)
const matriculeInput = ref('')
const isVerifying = ref(false)
const verificationError = ref('')
const foundUser = ref(null)

onMounted(() => {
  userStore = useUserStore()
  loadSousComites().then(() => {
    loadSections()
  })
})

const loadSections = async () => {
  try {
    isLoading.value = true
    if (!userStore?.user?.region) return
    
    const region = userStore.user.region
    const url = `${SECTIONS_API_URL}?action=list&secretariat=${encodeURIComponent(region)}&t=${Date.now()}`
    
    console.log('[v1] Reloading sections from:', url)
    
    const response = await fetch(url, { headers: { 'Accept': 'application/json' } })
    const data = await response.json()
    
    if (Array.isArray(data)) {
      sections.value = data
      filteredSections.value = data
      console.log('[v1] Sections loaded:', sections.value.length)
    } else {
      console.error('[v1] Invalid data:', data)
    }
  } catch (error) {
    console.error('[v1] Error:', error)
  } finally {
    isLoading.value = false
  }
}


const loadSousComites = async () => {
  try {
    const url = `${SOUS_COMITES_API_URL}?action=listall&r=${Math.random()}`
    
    const response = await fetch(url, {
      method: 'GET',
      headers: { 'Accept': 'application/json' }
    })
    
    if (!response.ok) return
    
    const data = await response.json()
    if (Array.isArray(data)) {
      sousComites.value = data
    }
  } catch (error) {
    console.error('[v0] Error loading sous-comités:', error)
  }
  // 🔁 Met à jour le nom du sous-comité dès que l’ID change
    watch(() => form.value.sous_comite_id, (newId) => {
    const sc = sousComites.value.find(s => s.id === parseInt(newId))
    form.value.nom_sous_comite = sc ? sc.nom_sous_comite : ''
    })

}

const getSousComiteName = (id) => {
  const sc = sousComites.value.find(s => s.id === parseInt(id))
  return sc?.nom_sous_comite || 'N/A'
}

const filterBySousComite = () => {
  searchSections()
}

const searchSections = () => {
  const query = searchQuery.value.toLowerCase().trim()
  
  let results = sections.value
  
  if (selectedSousComiteId.value) {
    results = results.filter(s => s.sous_comite_id === parseInt(selectedSousComiteId.value))
  }
  
  if (query) {
    results = results.filter(s =>
      s.nom_section?.toLowerCase().includes(query) ||
      s.responsable?.toLowerCase().includes(query) ||
      s.matricule_section?.toLowerCase().includes(query) ||
      s.ville?.toLowerCase().includes(query) ||
      s.commune?.toLowerCase().includes(query)
    )
  }
  
  filteredSections.value = results
}

const openAddModal = () => {
  resetForm()
  currentStep.value = 1
  matriculeInput.value = ''
  verificationError.value = ''
  foundUser.value = null
  showAddModal.value = true
}

const editSection = (section) => {
  editingId.value = section.id
  form.value = { ...section }
  showEditModal.value = true
  currentStep.value = 2 // Directly go to step 2 for editing
  foundUser.value = { // Pre-fill foundUser for editing context if needed
    nom: section.responsable?.split(' ')[0] || '',
    prenom: section.responsable?.split(' ').slice(1).join(' ') || '',
    contact: section.contact_responsable || '',
    email: section.email_responsable || '',
    Secretariat: section.Secretariat || ''
  }
}

const validateMatricule = () => {
  let value = matriculeInput.value.toUpperCase();

  // Supprimer tout sauf lettres et chiffres
  value = value.replace(/[^A-Z0-9]/g, '');

  // Limiter à 11 caractères (3+3+2+3)
  value = value.slice(0, 11);

  // Reformater XXX-XXX-XX-XXX
  let formatted = '';
  if (value.length > 0) formatted += value.slice(0, 3);
  if (value.length > 3) formatted += '-' + value.slice(3, 6);
  if (value.length > 6) formatted += '-' + value.slice(6, 8);
  if (value.length > 8) formatted += '-' + value.slice(8, 11);

  matriculeInput.value = formatted;

  if (formatted.length === 15) {
    const regex = /^[A-Z0-9]{3}-[A-Z0-9]{3}-[A-Z0-9]{2}-[0-9]{3}$/;
    const lastFiveChars = value.slice(-5);
    const isLastFiveDigits = /^[0-9]{5}$/.test(lastFiveChars);

    if (!regex.test(formatted)) {
      verificationError.value = 'Format invalide';
    } else if (!isLastFiveDigits) {
      verificationError.value = 'Les 5 derniers caractères doivent être des chiffres';
    } else {
      verificationError.value = '';
    }
  } else {
    verificationError.value = '';
  }
};


const checkMatricule = async () => {
  if (!form.value.matricule_section.trim()) {
    matriculeError.value = ''
    return
  }

  // Format the matricule first
  const value = form.value.matricule_section.toUpperCase()
  let formatted = value.replace(/[^A-Z0-9-]/g, '')
  
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
  
  form.value.matricule_section = formatted

  // Validate format
  const regex = /^[A-Z0-9]{3}-[A-Z0-9]{3}-[A-Z0-9]{2}-[0-9]{3}$/
  const lastFiveChars = formatted.replace(/-/g, '').slice(-5)
  const isLastFiveDigits = /^[0-9]{5}$/.test(lastFiveChars)
  
  if (formatted.length === 15) {
    if (!regex.test(formatted)) {
      matriculeError.value = 'Format invalide'
      return
    } else if (!isLastFiveDigits) {
      matriculeError.value = 'Les 5 derniers caractères doivent être des chiffres'
      return
    }
  } else {
    // If not full length, we might not need to check for uniqueness yet,
    // but still ensure it's not marked as an error if partially entered correctly.
    // However, for simplicity, we'll consider it potentially valid if it's not the exact format and allow submission later
    // unless it's a clear format violation.
    if (formatted.length > 0 && formatted.length < 15) {
        // If it's partially entered but not yet the full length, clear the error.
        // The submission validation will catch incomplete required fields.
        // We'll rely on the user to complete the format.
        // However, if it's already wrong before reaching 15 chars, mark it.
        // This logic can be tricky; for now, assume valid if not full length and not obviously wrong.
        // A more robust approach would be to validate partial entries.
    } else if (formatted.length === 0) {
        // Empty, no error.
    }
  }

  checkingMatricule.value = true
  matriculeError.value = '' // Clear previous errors before checking

  try {
    const response = await fetch(
      `${SECTIONS_API_URL}?action=check_matricule&matricule=${encodeURIComponent(form.value.matricule_section)}`,
      {
        method: 'GET',
        headers: { 'Accept': 'application/json' }
      }
    )

    const data = await response.json()

    if (data.exists && data.id !== editingId.value) {
      // Only show error if it exists AND it's not the current record being edited
      matriculeError.value = `Matricule déjà utilisé${data.counter ? ' - Suggestion: ' + data.suggestion : ''}`
    } else {
      matriculeError.value = ''
    }
  } catch (error) {
    console.error('[v0] Error checking matricule:', error)
    // Optionally add a notification for connection errors during check
    // addNotification('Erreur lors de la vérification du matricule.', 'error')
    matriculeError.value = 'Erreur de connexion' // Indicate connection issue
  } finally {
    checkingMatricule.value = false
  }
}

const verifyMatricule = async () => {
  if (!matriculeInput.value.trim()) {
    verificationError.value = 'Veuillez saisir un matricule'
    return
  }
  
  isVerifying.value = true
  verificationError.value = ''
  foundUser.value = null
  
  try {
    console.log('[v0] Verification du matricule:', matriculeInput.value)
    
    const response = await fetch('https://sogetrag.com/api/organes-api.php?action=get_user_by_matricule2', {
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
      console.log('[v0] Utilisateur trouvé:', data.user)
      foundUser.value = data.user
      
      const nomComplet = data.user.nom + (data.user.prenom ? ' ' + data.user.prenom : '')
      
      const generatedMatricule = await generateSectionMatricule(data.user.ville_commune)
      
      form.value = {
        nom_section: data.user.section || '',
        responsable: nomComplet,
        contact_responsable: data.user.telephone || data.user.contact || data.user.phone || '',
        email_responsable: ('section_' + (data.user.ville_commune || 'section') + '@aeemci-ce.ci').toLowerCase().replace(/\s+/g, '_'),
        matricule_section: generatedMatricule, // Utilisation du matricule généré automatiquement
        sous_comite_id: '',
        nom_sous_comite: '',
        Secretariat: data.user.Secretariat || '',
        ville: data.user.ville_commune || '',
        commune: data.user.ville_commune || '',
        status: 'active'
      }
      
      currentStep.value = 2
      // addNotification('Informations du responsable récupérées avec succès!', 'success') // Moved to inside the form part for better UX
    } else {
      console.log('[v0] Aucun utilisateur trouvé')
      verificationError.value = data.error || 'Aucun utilisateur trouvé avec ce matricule'
    }
  } catch (err) {
    console.error('[v0] Erreur lors de la verification:', err)
    verificationError.value = 'Erreur lors de la verification du matricule'
  } finally {
    isVerifying.value = false
  }
}

const generateSectionMatricule = async (villeCommune) => {
  try {
    // Créer un préfixe basé sur la ville/commune
    let prefix = 'SEC'
    if (villeCommune && villeCommune.length >= 3) {
      prefix = villeCommune.substring(0, 3).toUpperCase().replace(/[^A-Z]/g, 'X')
    }
    
    // Obtenir l'année actuelle (2 derniers chiffres)
    const year = new Date().getFullYear().toString().slice(-2)
    
    // Obtenir le prochain numéro de séquence
    const response = await fetch(`${SECTIONS_API_URL}?action=get_next_sequence&prefix=${prefix}&year=${year}`, {
      method: 'GET',
      headers: { 'Accept': 'application/json' }
    })
    
    let sequence = '001'
    if (response.ok) {
      const data = await response.json()
      sequence = data.next_sequence || '001'
    } else {
      // Si l'API ne supporte pas cette fonctionnalité, utiliser un compteur basé sur le nombre de sections existantes
      const existingSections = sections.value.filter(s => 
        s.matricule_section && s.matricule_section.startsWith(prefix)
      )
      const nextNum = existingSections.length + 1
      sequence = nextNum.toString().padStart(3, '0')
    }
    
    // Format: XXX-SEC-YY-NNN (ex: ABO-SEC-25-001)
    return `${prefix}-SEC-${year}-${sequence}`
  } catch (error) {
    console.error('[v0] Error generating matricule:', error)
    // Fallback: utiliser un timestamp
    const timestamp = Date.now().toString().slice(-3)
    return `SEC-XXX-25-${timestamp}`
  }
}

// La validation n'est plus nécessaire en mode création

const backToVerification = () => {
  currentStep.value = 1
  foundUser.value = null
  // Reset form fields that were pre-filled by verification
  form.value.responsable = ''
  form.value.contact_responsable = ''
  form.value.email_responsable = ''
  form.value.matricule_section = '' // Keep the matricule or reset? Let's keep it for now.
  form.value.ville = '' // Reset potentially pre-filled ville/commune
  form.value.commune = ''
  matriculeInput.value = '' // Clear the matricule input for the next verification
  verificationError.value = ''
}

const submitForm = async () => {
  if (!form.value.nom_section || !form.value.sous_comite_id) {
    addNotification('Veuillez remplir tous les champs obligatoires (Section, Sous-comité)', 'error')
    return
  }
  
  if (!form.value.ville || !form.value.commune) {
    addNotification('Veuillez remplir la ville et la commune', 'error')
    return
  }

  if (showEditModal.value && form.value.matricule_section) {
    if (form.value.matricule_section.length === 15) {
      const regex = /^[A-Z0-9]{3}-[A-Z0-9]{3}-[A-Z0-9]{2}-[0-9]{3}$/
      if (!regex.test(form.value.matricule_section)) {
        addNotification('Format du matricule invalide', 'error')
        return
      }
    }
  }
  
  isSubmitting.value = true
  
  try {
    const action = showEditModal.value ? 'update' : 'create'
    const url = showEditModal.value 
      ? `${SECTIONS_API_URL}?action=${action}&id=${editingId.value}`
      : `${SECTIONS_API_URL}?action=${action}`

    const response = await fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(form.value)
    })
    
    const data = await response.json()
    
    if (response.ok) {
      if (showEditModal.value) {
        const index = sections.value.findIndex(s => s.id === editingId.value)
        if (index !== -1) {
          // Update local section data with new data from API response
          sections.value[index] = { ...sections.value[index], ...data }
        }
        addNotification('Section modifiée avec succès!', 'success')
      } else {
        // Add new section to the beginning of the list for immediate visibility
        sections.value.unshift(data)
        addNotification('Section ajoutée avec succès!', 'success')
      }
      
      closeModal()
      await loadSections() // Re-fetch to ensure data consistency
    } else {
      addNotification('Erreur: ' + (data.error || 'Une erreur est survenue'), 'error')
    }
  } catch (error) {
    console.error('[v0] Error submitting form:', error)
    addNotification('Erreur lors de l\'enregistrement: ' + error.message, 'error')
  } finally {
    isSubmitting.value = false
  }
}

const confirmDelete = (id, nom) => {
  deleteId.value = id
  deleteName.value = nom
  showDeleteModal.value = true
}

const deleteSection = async (id) => {
  closeDeleteModal()
  
  try {
    const response = await fetch(`${SECTIONS_API_URL}?action=delete&id=${id}`, {
      method: 'POST'
    })
    
    const data = await response.json()
    
    if (response.ok) {
      // Remove deleted section from local list
      const index = sections.value.findIndex(s => s.id === id)
      if (index !== -1) {
        sections.value.splice(index, 1)
      }
      // Update filtered sections as well
      filteredSections.value = filteredSections.value.filter(s => s.id !== id);
      
      addNotification('Section supprimée avec succès!', 'success')
      await loadSections() // Re-fetch to ensure data consistency
    } else {
      addNotification('Erreur lors de la suppression: ' + (data.error || 'Une erreur est survenue'), 'error')
    }
  } catch (error) {
    console.error('[v0] Error deleting section:', error)
    addNotification('Erreur lors de la suppression: ' + error.message, 'error')
  }
}

const closeModal = () => {
  showAddModal.value = false
  showEditModal.value = false
  resetForm()
}

const closeDeleteModal = () => {
  showDeleteModal.value = false
  deleteId.value = null
  deleteName.value = ''
}

const resetForm = () => {
  form.value = {
    nom_section: '',
    sous_comite_id: '',
    nom_sous_comite: '',
    responsable: '',
    contact_responsable: '',
    email_responsable: '',
    matricule_section: '',
   secretariat: userStore.user.secretariat || '',
    ville: '',
    commune: '',
    status: 'active'
  }
  editingId.value = null
  matriculeError.value = ''
  currentStep.value = 1
  matriculeInput.value = ''
  verificationError.value = ''
  foundUser.value = null // Clear found user data
}

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
  // Ensure date is a Date object, if not, attempt to convert
  const dateObj = typeof date === 'string' ? new Date(date) : date;
  if (dateObj instanceof Date && !isNaN(dateObj)) {
    return dateObj.toLocaleTimeString('fr-FR', {
      hour: '2-digit',
      minute: '2-digit'
    });
  }
  return 'N/A';
}
</script>



<style scoped>
@keyframes blob {
  0%, 100% { transform: translate(0, 0) scale(1); }
  33% { transform: translate(30px, -50px) scale(1.1); }
  66% { transform: translate(-20px, 20px) scale(0.9); }
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes slideUp {
  from { transform: translateY(20px); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}

@keyframes slideInRight {
  from { transform: translateX(100%); opacity: 0; }
  to { transform: translateX(0); opacity: 1; }
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

.blob-1 {
  animation: blob 7s infinite 0s;
}

.blob-2 {
  animation: blob 7s infinite 2s;
}

.blob-3 {
  animation: blob 7s infinite 4s;
}

.animate-blob {
  animation: blob 7s infinite;
}

.animate-spin {
  animation: spin 0.8s linear infinite;
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

@media (max-width: 640px) {
  .backdrop-blur-sm { backdrop-filter: blur(8px); }
  .backdrop-blur-xl { backdrop-filter: blur(16px); }
}
</style>
