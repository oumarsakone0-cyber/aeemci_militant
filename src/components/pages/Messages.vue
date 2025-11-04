<template>
  <div class="min-h-screen bg-gray-50" style="margin-bottom: 50px;">
    <div class="p-4 space-y-4 max-w-md mx-auto md:max-w-[1400px] md:p-8 md:space-y-8 text-left">
      <!-- Header -->
      <div class="bg-white rounded-2xl p-4 shadow-sm md:p-6">
        <div class="flex justify-between items-center mb-4">
          <div>
            <h1 class="text-2xl font-bold text-purple-600 md:text-3xl">Informations & Activités</h1>
            <div class="flex items-center gap-2 mt-1">
              <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
                Mode démonstration
              </span>
            </div>
          </div>
          <button 
            @click="openCreateModal"
            class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors flex items-center gap-2"
          >
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
              <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
            </svg>
            Créer un message
          </button>
        </div>
        <p class="text-gray-600">Consultez les messages importants de la communauté</p>
      </div>

      <!-- Filtres et recherche -->
      <div class="bg-white rounded-2xl p-4 shadow-sm md:p-6 mb-4">
        <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
          <div class="flex gap-2">
            <button 
              @click="filterType = 'all'"
              :class="[
                'px-4 py-2 rounded-lg text-sm font-medium transition-colors',
                filterType === 'all' 
                  ? 'bg-purple-600 text-white' 
                  : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
              ]"
            >
              Tous
            </button>
            <button 
              @click="filterType = 'information'"
              :class="[
                'px-4 py-2 rounded-lg text-sm font-medium transition-colors',
                filterType === 'information' 
                  ? 'bg-green-600 text-white' 
                  : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
              ]"
            >
              Informations
            </button>
            <button 
              @click="filterType = 'activite'"
              :class="[
                'px-4 py-2 rounded-lg text-sm font-medium transition-colors',
                filterType === 'activite' 
                  ? 'bg-blue-600 text-white' 
                  : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
              ]"
            >
              Activités
            </button>
          </div>
          
          <div class="relative">
            <input 
              v-model="searchQuery"
              type="text" 
              placeholder="Rechercher un message..."
              class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent w-64"
            >
            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
          </div>
        </div>
      </div>

      <!-- Messages existants -->
      <div class="bg-white rounded-2xl p-4 shadow-sm md:p-6">
        <div class="flex justify-between items-left mb-6">
          <h2 class="text-xl font-bold text-gray-800">
            Messages {{ filterType === 'all' ? 'récents' : filterType === 'information' ? 'd\'information' : 'd\'activité' }}
            <span class="text-sm font-normal text-gray-500 ml-2">({{ filteredMessages.length }})</span>
          </h2>
        </div>
        
        <div v-if="loading" class="text-left py-8">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600 mx-auto"></div>
          <p class="text-gray-600 mt-2">Chargement des messages...</p>
        </div>

        <div v-else-if="messages.length === 0" class="text-left py-8">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
          </svg>
          <p class="text-gray-600 mt-2">Aucun message important pour le moment</p>
        </div>

        <div v-else class="space-y-6 text-left messages-container" style="text-align: left !important;">
          <div 
            v-for="(message, index) in filteredMessages" 
            :key="message.id"
            class="relative text-left"
            style="text-align: left !important;"
          >
            <!-- Séparateur entre les messages -->
            <div v-if="index > 0" class="absolute -top-3 left-1/2 transform -translate-x-1/2">
              <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                <div class="w-2 h-2 bg-gray-400 rounded-full"></div>
              </div>
            </div>
            
            <!-- Carte du message -->
            <div class="bg-white rounded-2xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden" style="text-align: left !important;">
            <!-- En-tête coloré selon le type -->
            <div :class="[
              'px-6 py-4',
              message.type === 'activite' 
                ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white' 
                : 'bg-gradient-to-r from-green-500 to-green-600 text-white'
            ]" style="text-align: left !important;">
              <div class="flex items-start justify-between" style="text-align: left !important;">
                <div class="flex items-center gap-3 text-left" style="text-align: left !important;">
                  <div class="w-8 h-8 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                    <svg v-if="message.type === 'activite'" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/>
                    </svg>
                    <svg v-else class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M20 2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h4l4 4 4-4h4c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z"/>
                    </svg>
                  </div>
                  <div class="text-left flex flex-col items-start" style="text-align: left !important;">
                    <h3 class="font-semibold text-lg text-left" style="text-align: left !important;">{{ message.author_name }}</h3>
                    <p class="text-sm opacity-90 text-left" style="text-align: left !important;">{{ message.matricule }} • {{ formatDate(message.created_at) }}</p>
                  </div>
                </div>
                <div class="flex items-center gap-2">
                  <span class="px-3 py-1 text-xs font-medium rounded-full bg-white bg-opacity-20">
                    {{ message.type === 'activite' ? 'Activité' : 'Information' }}
                  </span>
                  
                  <!-- Menu d'actions -->
                  <div class="relative">
                    <button 
                    v-if="user.matricule_gen == message.matricule"
                      @click="toggleMessageMenu(message.id)"
                      class="p-2 text-white hover:bg-white hover:bg-opacity-20 rounded-lg transition-colors"
                    >
                      <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                      </svg>
                    </button>
                    
                    <!-- Dropdown menu -->
                    <div 
                      v-if="activeMessageMenu === message.id"
                      class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border border-gray-200 z-10"
                    >
                      <div class="py-1">
                        <button 
                          @click="editMessage(message)"
                          class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2"
                        >
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                          </svg>
                          Modifier
                        </button>
                        <button 
                          @click="confirmDeleteMessage(message.id)"
                          class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2"
                        >
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                          </svg>
                          Supprimer
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Contenu du message -->
            <div class="p-6" style="text-align: left !important;">

              <!-- Détails de l'activité -->
              <div v-if="message.type === 'activite' && message.activity_date" class="mb-6 p-5 bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl text-left">
                <div class="flex items-center gap-2 mb-3 text-left">
                  <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/>
                  </svg>
                  <h4 class="font-semibold text-blue-800 text-left">Détails de l'activité</h4>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-left">
                  <div class="flex items-center gap-2 bg-white p-3 rounded-lg shadow-sm text-left">
                    <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/>
                    </svg>
                    <div class="text-left flex flex-col items-start">
                      <span class="text-gray-600 text-left">Date:</span>
                      <span class="font-medium text-gray-900 text-left">{{ formatDate(message.activity_date) }}</span>
                    </div>
                  </div>
                  <div class="flex items-center gap-2 bg-white p-3 rounded-lg shadow-sm text-left">
                    <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M16.2,16.2L11,13V7H12.5V12.2L17,14.9L16.2,16.2Z"/>
                    </svg>
                    <div class="text-left flex flex-col items-start">
                      <span class="text-gray-600 text-left">Heure:</span>
                      <span class="font-medium text-gray-900 text-left">{{ message.activity_time }}</span>
                    </div>
                  </div>
                  <div class="flex items-center gap-2 bg-white p-3 rounded-lg shadow-sm text-left">
                    <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                    </svg>
                    <div class="text-left flex flex-col items-start">
                      <span class="text-gray-600 text-left">Lieu:</span>
                      <span class="font-medium text-gray-900 text-left">{{ message.activity_location }}</span>
                    </div>
                  </div>
                </div>
              </div>

                  <!-- Contenu du message -->
                  <div class="bg-gray-50 p-5 rounded-xl text-left [&>*]:text-left [&>p]:text-left [&>div]:text-left [&>h1]:text-left [&>h2]:text-left [&>h3]:text-left [&>ul]:text-left [&>ol]:text-left [&>li]:text-left" 
                       style="text-align: left !important; direction: ltr !important;"
                       v-html="message.content">
                  </div>
            </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modale de création de message -->
    <div 
      v-if="showCreateModal" 
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
      @click="closeCreateModal"
    >
      <div 
        class="bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto"
        @click.stop
      >
        <!-- Header de la modale -->
        <div class="flex justify-between items-center p-6 border-b border-gray-200">
          <h2 class="text-2xl font-bold text-gray-800">Créer un message important</h2>
          <button 
            @click="closeCreateModal"
            class="text-gray-400 hover:text-gray-600 transition-colors"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <!-- Contenu de la modale -->
        <div class="p-6">
          <form @submit.prevent="submitMessage" class="space-y-6">
            <!-- Type de message -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Type de message <span class="text-red-500">*</span>
              </label>
              <div class="flex gap-4">
                <label class="flex items-center">
                  <input 
                    type="radio" 
                    v-model="messageForm.type" 
                    value="information"
                    class="mr-2 text-purple-600 focus:ring-purple-500"
                  >
                  <span class="text-sm text-gray-700">Information</span>
                </label>
                <label class="flex items-center">
                  <input 
                    type="radio" 
                    v-model="messageForm.type" 
                    value="activite"
                    class="mr-2 text-purple-600 focus:ring-purple-500"
                  >
                  <span class="text-sm text-gray-700">Activité</span>
                </label>
              </div>
            </div>

            <!-- Champs conditionnels pour les activités -->
            <div v-if="messageForm.type === 'activite'" class="space-y-4">
              <!-- Date de l'activité -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Date de l'activité <span class="text-red-500">*</span>
                </label>
                <input 
                  type="date" 
                  v-model="messageForm.activityDate"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                  required
                >
              </div>

              <!-- Heure de l'activité -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Heure de l'activité <span class="text-red-500">*</span>
                </label>
                <input 
                  type="time" 
                  v-model="messageForm.activityTime"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                  required
                >
              </div>

              <!-- Lieu de l'activité -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Lieu de l'activité <span class="text-red-500">*</span>
                </label>
                <input 
                  type="text" 
                  v-model="messageForm.activityLocation"
                  placeholder="Ex: Siège de l'AEEMCI, Salle de conférence..."
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                  required
                >
              </div>
            </div>

            <!-- Contenu du message (WYSIWYG) -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Contenu du message <span class="text-red-500">*</span>
              </label>
              <WysiwygEditor
                v-model="messageForm.content"
                :placeholder="messageForm.type === 'activite' ? 'Décrivez l\'activité...' : 'Rédigez votre information...'"
                min-height="300px"
                @focus="onEditorFocus"
                @blur="onEditorBlur"
              />
            </div>

            <!-- Informations sur l'auteur (lecture seule) -->
            <div class="bg-gray-50 rounded-lg p-4">
              <h3 class="text-sm font-medium text-gray-700 mb-2">Informations sur l'auteur</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                  <span class="text-gray-600">Nom:</span>
                  <span class="ml-2 font-medium">{{ user?.nom || 'Non défini' }}</span>
                </div>
              </div>
            </div>

            <!-- Boutons d'action -->
            <div class="flex gap-4 pt-4">
              <button 
                type="button" 
                @click="closeCreateModal"
                class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
              >
                Annuler
              </button>
              <button 
                type="submit" 
                :disabled="isSubmitting || !isFormValid"
                class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center gap-2"
              >
                <svg v-if="isSubmitting" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ isSubmitting ? 'Publication...' : 'Publier le message' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modale d'édition de message -->
    <div 
      v-if="showEditModal" 
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
      @click="closeEditModal"
    >
      <div 
        class="bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto"
        @click.stop
      >
        <!-- Header de la modale -->
        <div class="flex justify-between items-center p-6 border-b border-gray-200">
          <h2 class="text-2xl font-bold text-gray-800">Modifier le message</h2>
          <button 
            @click="closeEditModal"
            class="text-gray-400 hover:text-gray-600 transition-colors"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <!-- Contenu de la modale d'édition -->
        <div class="p-6">
          <form @submit.prevent="updateMessage" class="space-y-6">
            <!-- Type de message -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Type de message <span class="text-red-500">*</span>
              </label>
              <div class="flex gap-4">
                <label class="flex items-center">
                  <input 
                    type="radio" 
                    v-model="editForm.type" 
                    value="information"
                    class="mr-2 text-purple-600 focus:ring-purple-500"
                  >
                  <span class="text-sm text-gray-700">Information</span>
                </label>
                <label class="flex items-center">
                  <input 
                    type="radio" 
                    v-model="editForm.type" 
                    value="activite"
                    class="mr-2 text-purple-600 focus:ring-purple-500"
                  >
                  <span class="text-sm text-gray-700">Activité</span>
                </label>
              </div>
            </div>

            <!-- Champs conditionnels pour les activités -->
            <div v-if="editForm.type === 'activite'" class="space-y-4">
              <!-- Date de l'activité -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Date de l'activité <span class="text-red-500">*</span>
                </label>
                <input 
                  type="date" 
                  v-model="editForm.activityDate"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                  required
                >
              </div>

              <!-- Heure de l'activité -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Heure de l'activité <span class="text-red-500">*</span>
                </label>
                <input 
                  type="time" 
                  v-model="editForm.activityTime"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                  required
                >
              </div>

              <!-- Lieu de l'activité -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Lieu de l'activité <span class="text-red-500">*</span>
                </label>
                <input 
                  type="text" 
                  v-model="editForm.activityLocation"
                  placeholder="Ex: Siège de l'AEEMCI, Salle de conférence..."
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                  required
                >
              </div>
            </div>

            <!-- Contenu du message (WYSIWYG) -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Contenu du message <span class="text-red-500">*</span>
              </label>
              <WysiwygEditor
                v-model="editForm.content"
                :placeholder="editForm.type === 'activite' ? 'Décrivez l\'activité...' : 'Rédigez votre information...'"
                min-height="300px"
                @focus="onEditEditorFocus"
                @blur="onEditEditorBlur"
              />
            </div>

            <!-- Boutons d'action -->
            <div class="flex gap-4 pt-4">
              <button 
                type="button" 
                @click="closeEditModal"
                class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
              >
                Annuler
              </button>
              <button 
                type="submit" 
                :disabled="isUpdating || !isEditFormValid"
                class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center gap-2"
              >
                <svg v-if="isUpdating" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ isUpdating ? 'Mise à jour...' : 'Mettre à jour' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Notifications -->
    <div class="fixed top-4 right-4 z-[9999] space-y-2 pointer-events-none">
      <TransitionGroup name="notification" tag="div">
        <div
          v-for="notification in notifications"
          :key="notification.id"
          :class="[
            'px-4 py-3 rounded-lg shadow-xl max-w-sm border-2',
            {
              'bg-green-50 border-green-400 text-green-900': notification.type === 'success',
              'bg-red-50 border-red-400 text-red-900': notification.type === 'error',
              'bg-blue-50 border-blue-400 text-blue-900': notification.type === 'info',
              'bg-yellow-50 border-yellow-400 text-yellow-900': notification.type === 'warning'
            }
          ]"
          class="flex items-center justify-between pointer-events-auto"
        >
          <div class="flex items-center gap-2">
            <svg 
              v-if="notification.type === 'success'"
              class="w-5 h-5 text-green-600" 
              fill="currentColor" 
              viewBox="0 0 20 20"
            >
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            <svg 
              v-else-if="notification.type === 'error'"
              class="w-5 h-5 text-red-600" 
              fill="currentColor" 
              viewBox="0 0 20 20"
            >
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
            </svg>
            <svg 
              v-else-if="notification.type === 'info'"
              class="w-5 h-5 text-blue-600" 
              fill="currentColor" 
              viewBox="0 0 20 20"
            >
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
            </svg>
            <svg 
              v-else-if="notification.type === 'warning'"
              class="w-5 h-5 text-yellow-600" 
              fill="currentColor" 
              viewBox="0 0 20 20"
            >
              <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
            <span class="text-sm font-medium">{{ notification.message }}</span>
          </div>
          <button 
            @click="removeNotification(notification.id)"
            class="ml-2 text-gray-400 hover:text-gray-600 transition-colors"
          >
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
            </svg>
          </button>
        </div>
      </TransitionGroup>
    </div>

    <!-- Mobile Navigation -->
     <div class="fixed bottom-0 left-0 right-0 bg-white border-t px-4 py-2 md:hidden">
    <div class="flex justify-around items-center max-w-md mx-auto">
      
      <router-link to="/" class="flex flex-col items-center">
        <svg class="w-6 h-6 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
          <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
        </svg>
        <span class="text-xs text-gray-600 mt-1 font-medium">Accueil</span>
      </router-link>

      <router-link to="/" class="flex flex-col items-center">
        <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
          <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
        </svg>
        <span class="text-xs text-blue-600 mt-1">Explorer</span>
      </router-link>

      <router-link to="/" class="flex flex-col items-center">
        <svg class="w-6 h-6 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
          <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>
        </svg>
        <span class="text-xs text-gray-600 mt-1">Fichiers</span>
      </router-link>

      <router-link to="/" class="flex flex-col items-center">
        <svg class="w-6 h-6 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
          <path d="M3 3h18v2H3V3zm0 4h18v2H3V7zm0 4h18v2H3v-2z"/>
        </svg>
        <span class="text-xs text-gray-600 mt-1">Onglets</span>
      </router-link>

      <router-link to="/" class="flex flex-col items-center relative">
        <svg class="w-6 h-6 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
          <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
        </svg>
        <span class="text-xs text-gray-600 mt-1">Profile</span>
        <div class="absolute -top-1 -right-1 bg-red-500 w-3 h-3 rounded-full"></div>
      </router-link>

    </div>
  </div>

    <!-- Modale de confirmation de suppression -->
    <div 
      v-if="showDeleteModal" 
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
      @click="cancelDelete"
    >
      <div 
        class="bg-white rounded-lg p-6 w-full max-w-md mx-auto shadow-xl"
        @click.stop
      >
        <div class="flex items-center gap-3 mb-4">
          <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
          </div>
          <div>
            <h3 class="text-lg font-semibold text-gray-900">Supprimer le message</h3>
            <p class="text-sm text-gray-600">Cette action est irréversible</p>
          </div>
        </div>
        
        <p class="text-gray-700 mb-6">
          Êtes-vous sûr de vouloir supprimer ce message ? Cette action ne peut pas être annulée.
        </p>
        
        <div class="flex gap-3 justify-end">
          <button 
            @click="cancelDelete"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors"
          >
            Annuler
          </button>
          <button 
            @click="deleteMessage"
            class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors"
          >
            Supprimer
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import { useUserStore } from '../../stores/user'
import WysiwygEditor from '../common/WysiwygEditor.vue'
import ApiService from '../../utils/api'

const userStore = useUserStore()

// État du formulaire
const messageForm = ref({
  type: 'activite',
  content: '',
  activityDate: '',
  activityTime: '',
  activityLocation: ''
})

// Formulaire d'édition
const editForm = ref({
  type: 'activite',
  content: '',
  activityDate: '',
  activityTime: '',
  activityLocation: ''
})

// État de l'application
const isSubmitting = ref(false)
const loading = ref(false)
const messages = ref([])
const showCreateModal = ref(false)
const showEditModal = ref(false)
const activeMessageMenu = ref(null)
const isUpdating = ref(false)
const editingMessageId = ref(null)

// Filtres et recherche
const filterType = ref('all')
const searchQuery = ref('')

// Modale de suppression
const showDeleteModal = ref(false)
const deletingMessageId = ref(null)

// Notifications
const notifications = ref([])
const notificationId = ref(0)

// Computed properties
const user = computed(() => {
  return userStore.user
})
const matricule = computed(() => {
  return userStore.user?.matricule || userStore.user?.id_membre || 'AEEMCI-USER-001'
})

const isFormValid = computed(() => {
  if (!messageForm.value.content.trim()) return false
  if (messageForm.value.type === 'activite') {
    return messageForm.value.activityDate && 
           messageForm.value.activityTime && 
           messageForm.value.activityLocation.trim()
  }
  return true
})

const isEditFormValid = computed(() => {
  if (!editForm.value.content.trim()) return false
  if (editForm.value.type === 'activite') {
    return editForm.value.activityDate && 
           editForm.value.activityTime && 
           editForm.value.activityLocation.trim()
  }
  return true
})

// Messages filtrés
const filteredMessages = computed(() => {
  let filtered = messages.value

  // Filtrer par type
  if (filterType.value !== 'all') {
    filtered = filtered.filter(message => message.type === filterType.value)
  }

  // Filtrer par recherche
  if (searchQuery.value.trim()) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(message => 
      message.content.toLowerCase().includes(query) ||
      message.author_name.toLowerCase().includes(query) ||
      (message.activity_location && message.activity_location.toLowerCase().includes(query))
    )
  }

  return filtered
})

// Méthodes pour l'éditeur WYSIWYG
const onEditorFocus = () => {
  // Gérer le focus de l'éditeur principal
}

const onEditorBlur = () => {
  // Gérer la perte de focus de l'éditeur principal
}

const onEditEditorFocus = () => {
  // Gérer le focus de l'éditeur d'édition
}

const onEditEditorBlur = () => {
  // Gérer la perte de focus de l'éditeur d'édition
}

// Méthodes pour les notifications
const showNotification = (message, type = 'success', duration = 5000) => {
  const id = ++notificationId.value
  const notification = {
    id,
    message,
    type,
    duration
  }
  
  notifications.value.push(notification)
  
  // Supprimer automatiquement après la durée spécifiée
  setTimeout(() => {
    removeNotification(id)
  }, duration)
}

const removeNotification = (id) => {
  const index = notifications.value.findIndex(n => n.id === id)
  if (index > -1) {
    notifications.value.splice(index, 1)
  }
}

const openCreateModal = () => {
  showCreateModal.value = true
  // Réinitialiser le formulaire
  resetForm()
}


const closeCreateModal = () => {
  showCreateModal.value = false
  resetForm()
}

const toggleMessageMenu = (messageId) => {
  activeMessageMenu.value = activeMessageMenu.value === messageId ? null : messageId
}

const editMessage = (message) => {
  // Fermer le menu
  activeMessageMenu.value = null
  
  // Remplir le formulaire d'édition
  editForm.value = {
    type: message.type,
    content: message.content,
    activityDate: message.activity_date || '',
    activityTime: message.activity_time || '',
    activityLocation: message.activity_location || ''
  }
  
  editingMessageId.value = message.id 
  showEditModal.value = true
}

const closeEditModal = () => {
  showEditModal.value = false
  editingMessageId.value = null
  editForm.value = {
    type: 'activite',
    content: '',
    activityDate: '',
    activityTime: '',
    activityLocation: ''
  }
}

const confirmDeleteMessage = (messageId) => {
  deletingMessageId.value = messageId
  showDeleteModal.value = true
  activeMessageMenu.value = null
}

const deleteMessage = async () => {
  if (!deletingMessageId.value) return
  
  try {
    const response = await ApiService.deleteMessage(deletingMessageId.value)
    
    if (response.success) {
      // Recharger les messages depuis la base
      await loadMessages()
      
      // Fermer la modale
      showDeleteModal.value = false
      deletingMessageId.value = null
      
      showNotification('Message supprimé avec succès!', 'success')
    } else {
      // Si l'API distante ne trouve pas le message, utiliser le mode fallback
      console.log('🔄 Message non trouvé sur le serveur, suppression locale')
      
      // Supprimer localement
      messages.value = messages.value.filter(msg => msg.id !== deletingMessageId.value)
      
      // Sauvegarder dans localStorage
      localStorage.setItem('messages_importants', JSON.stringify(messages.value))
      
      // Fermer la modale
      showDeleteModal.value = false
      deletingMessageId.value = null
      
      showNotification('Message supprimé localement!', 'success')
    }
  } catch (error) {
    console.error('Erreur lors de la suppression:', error)
    
    // Mode fallback en cas d'erreur
    console.log('🔄 Erreur API, suppression locale')
    
    // Supprimer localement
    messages.value = messages.value.filter(msg => msg.id !== deletingMessageId.value)
    
    // Sauvegarder dans localStorage
    localStorage.setItem('messages_importants', JSON.stringify(messages.value))
    
    // Fermer la modale
    showDeleteModal.value = false
    deletingMessageId.value = null
    
    showNotification('Message supprimé localement!', 'success')
  }
}

const cancelDelete = () => {
  showDeleteModal.value = false
  deletingMessageId.value = null
}

const updateMessage = async () => {
  if (!isEditFormValid.value) return
  
  isUpdating.value = true
  
  try {
    // Récupérer le matricule de l'utilisateur connecté
    let finalMatricule = user.value?.matricule
    
    if (!finalMatricule) {
      // Essayer différentes propriétés du user
      finalMatricule = user.value?.matricule || 
                      user.value?.matricule_id || 
                      user.value?.id_membre || 
                      user.value?.id || 
                      user.value?.user_id
    }
    
    // Générer un matricule valide si nécessaire
    if (finalMatricule && !finalMatricule.startsWith('BEL-SOU')) {
      finalMatricule = `BEL-SOU-18-${String(finalMatricule).padStart(3, '0')}`
    }
    
    if (!finalMatricule) {
      showNotification('Impossible de récupérer votre matricule. Veuillez vous reconnecter.', 'error')
      return
    }
    
    // Préparer les données
    const messageData = {
      type: editForm.value.type,
      content: editForm.value.content,
      matricule: finalMatricule,
      author_name: user.value?.nom ? `${user.value.nom} ${user.value.prenom || ''}`.trim() : 'Utilisateur',
      activity_date: editForm.value.type === 'activite' ? editForm.value.activityDate : null,
      activity_time: editForm.value.type === 'activite' ? editForm.value.activityTime : null,
      activity_location: editForm.value.type === 'activite' ? editForm.value.activityLocation : null
    }
    
    // Appel API pour mettre à jour
    const response = await ApiService.updateMessage(editingMessageId.value, messageData)
    
    if (response.success) {
      // Petit délai pour s'assurer que la base de données est mise à jour
      await new Promise(resolve => setTimeout(resolve, 100))
      
      // Recharger les messages depuis la base (la base est correctement mise à jour)
      await loadMessages()
      
      // Fermer la modale et réinitialiser
      closeEditModal()
      
      showNotification('Message mis à jour avec succès!', 'success')
    } else {
      console.error('❌ Erreur API:', response)
      throw new Error(response.error || 'Erreur lors de la mise à jour')
    }
    
  } catch (error) {
    console.error('Erreur lors de la mise à jour:', error)
    showNotification('Erreur lors de la mise à jour du message', 'error')
  } finally {
    isUpdating.value = false
  }
}

const resetForm = () => {
  messageForm.value = {
    type: 'activite',
    content: '',
    activityDate: '',
    activityTime: '',
    activityLocation: ''
  }
}

const submitMessage = async () => {
  if (!isFormValid.value) {
    showNotification('Veuillez remplir tous les champs requis', 'error')
    return
  }
  
  isSubmitting.value = true
  
  try {
    // Récupérer le matricule avec plusieurs méthodes de fallback
    let finalMatricule = matricule.value
    
    if (!finalMatricule) {
      // Essayer différentes propriétés du user
      finalMatricule = user.value?.matricule || 
                      user.value?.matricule_id || 
                      user.value?.id_membre || 
                      user.value?.id || 
                      user.value?.user_id
    }
    
    // Si on a un matricule, essayer de récupérer les infos complètes
    if (finalMatricule) {
      try {
        const userResponse = await ApiService.getUserByMatricule(finalMatricule)
        
        if (userResponse.success && userResponse.data) {
          // Chercher le vrai matricule dans la réponse
          if (userResponse.data.matricule) {
            finalMatricule = userResponse.data.matricule
          } else if (userResponse.data.data && userResponse.data.data.matricule) {
            finalMatricule = userResponse.data.data.matricule
          } else {
            // Essayer de construire le matricule avec le format BEL-SOU-18-001
            // En utilisant l'id_membre pour générer un matricule temporaire
            finalMatricule = `BEL-SOU-18-${String(finalMatricule).padStart(3, '0')}`
          }
        }
      } catch (error) {
        // Générer un matricule temporaire
        finalMatricule = `BEL-SOU-18-${String(finalMatricule).padStart(3, '0')}`
      }
    }
    
    if (!finalMatricule) {
      showNotification('Impossible de récupérer votre matricule. Veuillez vous reconnecter.', 'error')
      return
    }
    
    // Préparer les données du message
    const messageData = {
      type: messageForm.value.type,
      content: messageForm.value.content,
      matricule: finalMatricule,
      author_name: user.value?.nom || user.value?.prenom || 'Utilisateur',
      activity_date: messageForm.value.type === 'activite' ? messageForm.value.activityDate : null,
      activity_time: messageForm.value.type === 'activite' ? messageForm.value.activityTime : null,
      activity_location: messageForm.value.type === 'activite' ? messageForm.value.activityLocation : null
    }
    
    
    // Envoyer à l'API
    try {
      const response = await ApiService.createMessage(messageData)
      
      if (response.success) {
        // Recharger les messages depuis la base
        await loadMessages()
        
        // Fermer la modale et réinitialiser le formulaire
        closeCreateModal()
        
        showNotification('Message publié avec succès!', 'success')
      } else {
        throw new Error(response.error || 'Erreur lors de la création')
      }
    } catch (apiError) {
      console.log('🔄 API non disponible, utilisation du mode fallback')
      console.log('❌ Erreur API:', apiError.message)
      
      // Mode fallback - ajouter le message localement
      const newMessage = {
        id: Date.now(),
        ...messageData,
        created_at: new Date().toISOString()
      }
      
      messages.value.unshift(newMessage)
      
      // Fermer la modale et réinitialiser le formulaire
      closeCreateModal()
      
      showNotification('Message ajouté localement - API backend non configurée', 'warning')
    }
    
  } catch (error) {
    console.error('Erreur lors de la publication:', error)
    showNotification(`Erreur: ${error.message}`, 'error')
  } finally {
    isSubmitting.value = false
  }
}

const loadMessages = async () => {
  loading.value = true
  try {
    const response = await ApiService.getMessages(20, 0)
    
    if (response && response.success && response.messages) {
      messages.value = response.messages
      console.log('✅ Messages chargés depuis la base de données:', messages.value.length)
    } else {
      throw new Error(response?.error || 'Erreur lors du chargement des messages')
    }
  } catch (error) {
    console.error('Erreur lors du chargement des messages:', error)
    
    // Mode fallback avec localStorage
    console.log('🔄 Utilisation du mode fallback avec localStorage')
    
    // Charger depuis localStorage
    const savedMessages = localStorage.getItem('messages_importants')
    if (savedMessages) {
      messages.value = JSON.parse(savedMessages)
      console.log('📱 Messages chargés depuis localStorage:', messages.value.length)
    } else {
      // Messages d'exemple réalistes pour démonstration
      messages.value = [
        {
          id: 1,
          type: 'information',
          content: '<p><strong>🎉 Nouvelle plateforme digitale AEEMCI</strong></p><p>L\'AEEMCI a le plaisir de vous informer que la plateforme digitale est maintenant opérationnelle. Vous pouvez désormais accéder à tous les services en ligne, consulter les actualités et participer aux activités de la communauté.</p><p>Connectez-vous avec vos identifiants habituels pour découvrir toutes les fonctionnalités disponibles.</p>',
          matricule: 'AEEMCI-ADMIN-001',
          author_name: 'Secrétaire Général',
          created_at: '2024-01-15T10:30:00Z'
        },
        {
          id: 2,
          type: 'activite',
          content: '<p><strong>📚 Conférence : "L\'importance de la recherche de la science en Islam"</strong></p><p>L\'AEEMCI a l\'honneur de vous convier à une conférence animée par <strong>Imam Ahmad Ben Touré</strong> sur le thème :</p><p><strong>« L\'importance de la recherche de la science en Islam »</strong></p><p>Venez vous ressourcer à la lumière du Coran et de la Sunna, et redécouvrir la valeur de la science dans la vie du musulman.</p>',
          matricule: 'AEEMCI-ORG-002',
          author_name: 'Responsable des Activités',
          activity_date: '2024-01-20',
          activity_time: '14:00',
          activity_location: 'Siège de l\'AEEMCI - Salle de conférence',
          created_at: '2024-01-14T15:45:00Z'
        },
        {
          id: 3,
          type: 'information',
          content: '<p><strong>📋 Mise à jour des statuts</strong></p><p>Nous vous informons que les nouveaux statuts de l\'AEEMCI ont été approuvés lors de l\'assemblée générale extraordinaire du 10 janvier 2024.</p><p>Les documents officiels sont disponibles dans la section "Documents" de la plateforme.</p>',
          matricule: 'AEEMCI-LEGAL-003',
          author_name: 'Service Juridique',
          created_at: '2024-01-12T09:15:00Z'
        },
        {
          id: 4,
          type: 'activite',
          content: '<p><strong>🌙 Cérémonie de clôture du Ramadan</strong></p><p>L\'AEEMCI organise une cérémonie de clôture du mois de Ramadan avec :</p><ul><li>Récitation du Coran</li><li>Conférence spirituelle</li><li>Repas de rupture du jeûne</li><li>Distribution d\'aumônes</li></ul><p>Cette célébration sera l\'occasion de renforcer les liens fraternels au sein de notre communauté.</p>',
          matricule: 'AEEMCI-EVENT-004',
          author_name: 'Comité des Célébrations',
          activity_date: '2024-03-10',
          activity_time: '18:30',
          activity_location: 'Centre Culturel Islamique - Grande salle',
          created_at: '2024-01-10T16:20:00Z'
        },
        {
          id: 5,
          type: 'information',
          content: '<p><strong>💡 Programme de formation continue</strong></p><p>L\'AEEMCI lance un nouveau programme de formation continue destiné aux membres actifs. Les modules couvriront :</p><ul><li>Études coraniques approfondies</li><li>Histoire de l\'Islam</li><li>Langue arabe</li><li>Gestion associative</li></ul><p>Les inscriptions sont ouvertes jusqu\'au 31 janvier 2024.</p>',
          matricule: 'AEEMCI-EDU-005',
          author_name: 'Service Éducatif',
          created_at: '2024-01-08T11:00:00Z'
        }
      ]
    }
    
    showNotification('Mode démonstration activé - Données d\'exemple chargées', 'info')
  } finally {
    loading.value = false
  }
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

// Lifecycle
onMounted(async () => {
  // Essayer de récupérer les infos utilisateur complètes
  if (user.value?.id_membre) {
    try {
      const userResponse = await ApiService.getUserByMatricule(user.value.id_membre)
      
      if (userResponse.success && userResponse.data) {
        // Chercher le vrai matricule dans la réponse
        let realMatricule = null
        if (userResponse.data.matricule) {
          realMatricule = userResponse.data.matricule
        } else if (userResponse.data.data && userResponse.data.data.matricule) {
          realMatricule = userResponse.data.data.matricule
        } else {
          // Générer un matricule temporaire
          realMatricule = `BEL-SOU-18-${String(user.value.id_membre).padStart(3, '0')}`
        }
        
        // Mettre à jour le store avec les infos complètes
        userStore.user = { 
          ...userStore.user, 
          ...userResponse.data,
          matricule: realMatricule
        }
      }
    } catch (error) {
      // Erreur silencieuse - continuer avec les données existantes
    }
  }
  
  loadMessages()
  
  // Fermer les menus déroulants quand on clique ailleurs
  document.addEventListener('click', (e) => {
    if (!e.target.closest('.relative')) {
      activeMessageMenu.value = null
    }
  })
})
</script>

<style scoped>
/* Styles pour la prose dans l'affichage des messages */
.prose {
  color: #374151;
  line-height: 1.6;
}

.prose p {
  margin-bottom: 1rem;
}

.prose ul, .prose ol {
  margin-bottom: 1rem;
  padding-left: 1.5rem;
}

.prose li {
  margin-bottom: 0.5rem;
}

.prose strong {
  font-weight: 600;
}

.prose em {
  font-style: italic;
}

.prose u {
  text-decoration: underline;
}

/* Styles pour l'affichage des messages existants */
:deep(.prose h1, .prose h2, .prose h3) {
  font-weight: 600;
  margin-bottom: 0.5rem;
  color: #1f2937;
  text-align: left;
}

:deep(.prose h1) {
  font-size: 1.5rem;
}

:deep(.prose h2) {
  font-size: 1.25rem;
}

:deep(.prose h3) {
  font-size: 1.125rem;
}

:deep(.prose p) {
  text-align: left;
  margin-bottom: 1rem;
}

:deep(.prose ul, .prose ol) {
  text-align: left;
  margin-left: 1.5rem;
}

:deep(.prose li) {
  text-align: left;
}

:deep(.prose strong) {
  text-align: left;
}

:deep(.prose a) {
  color: #8b5cf6;
  text-decoration: underline;
}

:deep(.prose a:hover) {
  color: #7c3aed;
}

    /* Alignement simple avec Tailwind */
    .message-content {
      text-align: left !important;
    }

    .message-content * {
      text-align: left !important;
    }

/* Forcer l'alignement à gauche sur tous les éléments de texte */
.message-content p,
.message-content div,
.message-content span,
.message-content h1,
.message-content h2,
.message-content h3,
.message-content h4,
.message-content h5,
.message-content h6 {
  text-align: left !important;
  direction: ltr !important;
  margin-left: 0 !important;
  margin-right: auto !important;
  padding-left: 0 !important;
  padding-right: 0 !important;
  width: 100% !important;
  max-width: 100% !important;
  display: block !important;
}

/* Styles spécifiques pour les éléments HTML dans les messages */
.message-content h1,
.message-content h2,
.message-content h3,
.message-content h4,
.message-content h5,
.message-content h6 {
  text-align: left !important;
  margin-left: 0 !important;
  margin-right: auto !important;
  width: 100% !important;
}

.message-content p {
  text-align: left !important;
  margin-left: 0 !important;
  margin-right: auto !important;
  width: 100% !important;
}

.message-content ul,
.message-content ol {
  text-align: left !important;
  margin-left: 1.5rem !important;
  margin-right: auto !important;
  width: calc(100% - 1.5rem) !important;
}

.message-content li {
  text-align: left !important;
  margin-left: 0 !important;
  margin-right: auto !important;
}

.message-content strong,
.message-content b {
  text-align: left !important;
}

.message-content em,
.message-content i {
  text-align: left !important;
}

/* Supprimer tout centrage */
.messages-container {
  text-align: left !important;
}

.messages-container * {
  text-align: left !important;
}

/* Styles globaux pour forcer l'alignement à gauche */
.messages-container h1,
.messages-container h2,
.messages-container h3,
.messages-container h4,
.messages-container h5,
.messages-container h6 {
  text-align: left !important;
}

.messages-container p {
  text-align: left !important;
}

.messages-container ul,
.messages-container ol {
  text-align: left !important;
  margin-left: 1.5rem !important;
}

.messages-container li {
  text-align: left !important;
}

.messages-container strong,
.messages-container b {
  text-align: left !important;
}

.messages-container em,
.messages-container i {
  text-align: left !important;
}

/* Styles globaux pour forcer l'alignement à gauche sur toute la page */
.messages-container {
  text-align: left !important;
  justify-content: flex-start !important;
  align-items: flex-start !important;
  direction: ltr !important;
}

.messages-container > * {
  text-align: left !important;
  justify-content: flex-start !important;
  align-items: flex-start !important;
  direction: ltr !important;
}

/* Forcer l'alignement à gauche sur tous les éléments de message */
.messages-container .message-content {
  text-align: left !important;
  display: block !important;
  width: 100% !important;
  direction: ltr !important;
}

.messages-container .message-content > * {
  text-align: left !important;
  display: block !important;
  width: 100% !important;
  margin-left: 0 !important;
  margin-right: auto !important;
  direction: ltr !important;
}

/* Styles CSS globaux pour forcer l'alignement à gauche */
.messages-container .message-content * {
  text-align: left !important;
  direction: ltr !important;
  margin-left: 0 !important;
  margin-right: auto !important;
  padding-left: 0 !important;
  padding-right: 0 !important;
  width: 100% !important;
  max-width: 100% !important;
  display: block !important;
  float: none !important;
  clear: both !important;
}

/* Force l'alignement à gauche sur TOUS les éléments */
.messages-container,
.messages-container *,
.messages-container *::before,
.messages-container *::after {
  text-align: left !important;
  direction: ltr !important;
  justify-content: flex-start !important;
  align-items: flex-start !important;
}

/* Force l'alignement à gauche sur les éléments inline */
.messages-container span,
.messages-container a,
.messages-container strong,
.messages-container em,
.messages-container b,
.messages-container i,
.messages-container u {
  text-align: left !important;
  display: inline !important;
  direction: ltr !important;
}

/* Force l'alignement à gauche sur les paragraphes */
.messages-container p {
  text-align: left !important;
  direction: ltr !important;
  margin-left: 0 !important;
  margin-right: auto !important;
  width: 100% !important;
}

/* Force l'alignement à gauche sur les titres */
.messages-container h1,
.messages-container h2,
.messages-container h3,
.messages-container h4,
.messages-container h5,
.messages-container h6 {
  text-align: left !important;
  direction: ltr !important;
  margin-left: 0 !important;
  margin-right: auto !important;
  width: 100% !important;
}

/* Force l'alignement à gauche sur les listes */
.messages-container ul,
.messages-container ol {
  text-align: left !important;
  direction: ltr !important;
  margin-left: 1.5rem !important;
  margin-right: 0 !important;
  padding-left: 1.5rem !important;
  padding-right: 0 !important;
}

.messages-container li {
  text-align: left !important;
  direction: ltr !important;
  margin-left: 0 !important;
  margin-right: 0 !important;
}

/* Forcer l'alignement à gauche sur les éléments inline */
.messages-container .message-content span,
.messages-container .message-content a,
.messages-container .message-content strong,
.messages-container .message-content em,
.messages-container .message-content b,
.messages-container .message-content i {
  text-align: left !important;
  display: inline !important;
  margin-left: 0 !important;
  margin-right: auto !important;
  direction: ltr !important;
  float: none !important;
  clear: none !important;
}

/* Styles pour les éléments de liste */
.messages-container .message-content ul,
.messages-container .message-content ol {
  text-align: left !important;
  margin-left: 1.5rem !important;
  margin-right: 0 !important;
  padding-left: 1.5rem !important;
  padding-right: 0 !important;
  direction: ltr !important;
  width: calc(100% - 3rem) !important;
  max-width: calc(100% - 3rem) !important;
}

.messages-container .message-content li {
  text-align: left !important;
  margin-left: 0 !important;
  margin-right: 0 !important;
  padding-left: 0 !important;
  padding-right: 0 !important;
}

/* Styles pour tous les éléments HTML possibles */
.messages-container .message-content div,
.messages-container .message-content section,
.messages-container .message-content article,
.messages-container .message-content header,
.messages-container .message-content footer,
.messages-container .message-content main,
.messages-container .message-content aside,
.messages-container .message-content nav {
  text-align: left !important;
  margin-left: 0 !important;
  margin-right: auto !important;
  width: 100% !important;
}

/* Styles pour les éléments de texte */
.messages-container .message-content blockquote,
.messages-container .message-content pre,
.messages-container .message-content code {
  text-align: left !important;
  margin-left: 0 !important;
  margin-right: auto !important;
  width: 100% !important;
}

/* Solution ultime - forcer l'alignement avec !important sur tout */
.message-content,
.message-content *,
.message-content *::before,
.message-content *::after {
  text-align: left !important;
  direction: ltr !important;
  margin-left: 0 !important;
  margin-right: auto !important;
  padding-left: 0 !important;
  padding-right: 0 !important;
  justify-content: flex-start !important;
  align-items: flex-start !important;
  display: block !important;
  width: 100% !important;
  max-width: 100% !important;
  float: none !important;
  clear: both !important;
}

/* Exception pour les éléments inline */
.message-content span,
.message-content a,
.message-content strong,
.message-content em,
.message-content b,
.message-content i,
.message-content u {
  display: inline !important;
  text-align: left !important;
  direction: ltr !important;
  float: none !important;
  clear: none !important;
}

/* Animations pour les notifications */
.notification-enter-active,
.notification-leave-active {
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.notification-enter-from {
  opacity: 0;
  transform: translateX(100%) scale(0.9);
}

.notification-leave-to {
  opacity: 0;
  transform: translateX(100%) scale(0.9);
}

.notification-move {
  transition: transform 0.3s ease;
}

/* Styles supplémentaires pour les notifications */
.notification-enter-active {
  animation: slideInRight 0.4s ease-out;
}

@keyframes slideInRight {
  from {
    opacity: 0;
    transform: translateX(100%) scale(0.9);
  }
  to {
    opacity: 1;
    transform: translateX(0) scale(1);
  }
}

/* Améliorer la visibilité */
.fixed.top-4.right-4 {
  position: fixed !important;
  top: 1rem !important;
  right: 1rem !important;
  z-index: 9999 !important;
}

/* Forcer l'alignement à gauche des informations utilisateur dans l'en-tête */
.messages-container .text-left h3,
.messages-container .text-left p {
  text-align: left !important;
  margin-left: 0 !important;
  margin-right: auto !important;
  direction: ltr !important;
}

/* Forcer l'alignement à gauche de tous les éléments de l'en-tête */
.messages-container .flex.items-start.justify-between .flex.items-center.gap-3 .text-left {
  text-align: left !important;
  align-items: flex-start !important;
  justify-content: flex-start !important;
}

.messages-container .flex.items-start.justify-between .flex.items-center.gap-3 .text-left h3,
.messages-container .flex.items-start.justify-between .flex.items-center.gap-3 .text-left p {
  text-align: left !important;
  margin-left: 0 !important;
  margin-right: auto !important;
  direction: ltr !important;
  width: 100% !important;
  max-width: 100% !important;
}

/* Forcer l'alignement à gauche des détails d'activité */
.messages-container .bg-gradient-to-r .text-left {
  text-align: left !important;
}

.messages-container .bg-gradient-to-r .text-left h4,
.messages-container .bg-gradient-to-r .text-left span {
  text-align: left !important;
  margin-left: 0 !important;
  margin-right: auto !important;
  direction: ltr !important;
}

/* Forcer l'alignement à gauche des éléments de grille */
.messages-container .grid .text-left {
  text-align: left !important;
}

.messages-container .grid .text-left span {
  text-align: left !important;
  margin-left: 0 !important;
  margin-right: auto !important;
  direction: ltr !important;
}

/* Styles globaux pour forcer l'alignement à gauche sur toute la page */
.min-h-screen {
  text-align: left !important;
  direction: ltr !important;
}

.min-h-screen * {
  text-align: left !important;
  direction: ltr !important;
}

/* Force l'alignement à gauche sur les conteneurs principaux */
.p-4.space-y-4.max-w-md.mx-auto,
.p-4.space-y-4.max-w-md.mx-auto * {
  text-align: left !important;
  direction: ltr !important;
}

/* Force l'alignement à gauche sur les cartes de messages */
.border.border-gray-200.rounded-lg,
.border.border-gray-200.rounded-lg * {
  text-align: left !important;
  direction: ltr !important;
}

/* Force l'alignement à gauche sur le contenu HTML des messages */
.bg-gray-50.p-4.rounded-lg,
.bg-gray-50.p-4.rounded-lg * {
  text-align: left !important;
  direction: ltr !important;
  margin-left: 0 !important;
  margin-right: auto !important;
  padding-left: 0 !important;
  padding-right: 0 !important;
  width: 100% !important;
  max-width: 100% !important;
}

/* Force l'alignement à gauche sur tous les éléments de texte */
.bg-gray-50.p-4.rounded-lg p,
.bg-gray-50.p-4.rounded-lg div,
.bg-gray-50.p-4.rounded-lg span,
.bg-gray-50.p-4.rounded-lg h1,
.bg-gray-50.p-4.rounded-lg h2,
.bg-gray-50.p-4.rounded-lg h3,
.bg-gray-50.p-4.rounded-lg h4,
.bg-gray-50.p-4.rounded-lg h5,
.bg-gray-50.p-4.rounded-lg h6,
.bg-gray-50.p-4.rounded-lg ul,
.bg-gray-50.p-4.rounded-lg ol,
.bg-gray-50.p-4.rounded-lg li,
.bg-gray-50.p-4.rounded-lg strong,
.bg-gray-50.p-4.rounded-lg em,
.bg-gray-50.p-4.rounded-lg b,
.bg-gray-50.p-4.rounded-lg i,
.bg-gray-50.p-4.rounded-lg u,
.bg-gray-50.p-4.rounded-lg a {
  text-align: left !important;
  direction: ltr !important;
  margin-left: 0 !important;
  margin-right: auto !important;
  padding-left: 0 !important;
  padding-right: 0 !important;
  width: 100% !important;
  max-width: 100% !important;
  display: block !important;
  float: none !important;
  clear: both !important;
}

/* Exception pour les éléments inline */
.bg-gray-50.p-4.rounded-lg span,
.bg-gray-50.p-4.rounded-lg a,
.bg-gray-50.p-4.rounded-lg strong,
.bg-gray-50.p-4.rounded-lg em,
.bg-gray-50.p-4.rounded-lg b,
.bg-gray-50.p-4.rounded-lg i,
.bg-gray-50.p-4.rounded-lg u {
  display: inline !important;
  text-align: left !important;
  direction: ltr !important;
  float: none !important;
  clear: none !important;
}
</style>
