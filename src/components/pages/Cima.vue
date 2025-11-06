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
                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
              </svg>
            </div>
            <div class="flex flex-col min-w-0">
              <h1 class="text-lg sm:text-2xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent whitespace-nowrap">Inscription CIMA</h1>
              <p class="text-xs sm:text-sm text-gray-600 whitespace-nowrap">Certificat des inscriptions des membres de CIMA</p>
            </div>
          </div>
        </div>
      </div>
    </header>

    <main class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Indicateur d'étapes -->
      <div class="mb-8">
        <div class="flex items-center justify-center space-x-4">
          <div class="flex items-center">
            <div :class="[
              'w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all duration-300',
              currentStep >= 1 ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-lg' : 'bg-gray-200 text-gray-500'
            ]">
              1
            </div>
            <span :class="[
              'ml-2 text-sm font-medium',
              currentStep >= 1 ? 'text-emerald-600' : 'text-gray-500'
            ]">Vérification</span>
          </div>
          <div class="w-16 h-0.5 bg-gray-200">
            <div :class="[
              'h-full transition-all duration-300',
              currentStep >= 2 ? 'bg-gradient-to-r from-emerald-600 to-teal-600' : 'bg-gray-200'
            ]"></div>
          </div>
          <div class="flex items-center">
            <div :class="[
              'w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all duration-300',
              currentStep >= 2 ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-lg' : 'bg-gray-200 text-gray-500'
            ]">
              2
            </div>
            <span :class="[
              'ml-2 text-sm font-medium',
              currentStep >= 2 ? 'text-emerald-600' : 'text-gray-500'
            ]">Formulaire</span>
          </div>
        </div>
      </div>

      <!-- Étape 1: Vérification du matricule -->
      <div v-if="currentStep === 1" class="backdrop-blur-sm bg-white/80 rounded-2xl shadow-lg border border-emerald-200/50 p-6 sm:p-8">
        <div class="text-center mb-6">
          <h2 class="text-2xl font-bold text-gray-900 mb-2">Vérification du matricule</h2>
          <p class="text-gray-600">
            Saisissez votre matricule pour vérifier votre éligibilité à l'inscription CIMA.
            <br>
            <span class="font-semibold text-emerald-600">Seuls les Délégués culturels, SR, Formateurs et Imams peuvent s'inscrire.</span>
          </p>
          
          <!-- Bouton de debug (temporaire) -->
          <div v-if="matriculeInput" class="mb-4 text-center">
            <button
              @click="debugMatricule"
              class="text-xs text-gray-500 hover:text-gray-700 underline"
              type="button"
            >
              🔍 Voir les détails du rôle (debug)
            </button>
          </div>
        </div>

        <div class="mb-6">
          <label class="block text-sm font-semibold text-gray-700 mb-2 text-left">Matricule *</label>
          <div class="relative">
            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-emerald-600">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
            </div>
            <input
              v-model="matriculeInput"
              type="text"
              required
              placeholder="XXX-XXX-XX-XXX"
              maxlength="15"
              @input="formatMatricule"
              :class="[
                'w-full pl-12 pr-4 py-4 bg-white border-2 rounded-xl focus:outline-none transition-all duration-300 text-gray-800 font-medium shadow-sm hover:shadow-md',
                verificationError ? 'border-red-300 focus:border-red-500' : 'border-emerald-200 focus:border-emerald-500'
              ]"
              :disabled="isVerifying"
            />
          </div>
          <div v-if="verificationError" class="mt-3 p-4 bg-red-50 border-2 border-red-200 rounded-xl animate-fadeIn">
            <div class="flex items-start gap-2">
              <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <div class="flex-1">
                <p class="text-sm font-semibold text-red-800 mb-2">Inscription non autorisée</p>
                <p class="text-sm text-red-700 whitespace-pre-line leading-relaxed">{{ verificationError }}</p>
              </div>
            </div>
          </div>
        </div>

        <div class="flex gap-3 justify-center">
          <button
            @click="verifyMatricule"
            :disabled="isVerifying || !isMatriculeValid"
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

      <!-- Étape 2: Formulaire d'inscription -->
      <div v-if="currentStep === 2" class="backdrop-blur-sm bg-white/80 rounded-2xl shadow-lg border border-emerald-200/50 p-6 sm:p-8">
        <!-- Informations récupérées -->
        <div v-if="foundUser" class="mb-6 p-4 bg-gradient-to-r from-emerald-50 to-teal-50 border-2 border-emerald-200 rounded-xl">
          <div class="flex items-center gap-3">
            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h4 class="text-sm font-bold text-emerald-800">Informations récupérées avec succès</h4>
          </div>
        </div>

        <!-- Avertissement sur le montant -->
        <div class="mb-6 p-4 bg-amber-50 border-2 border-amber-200 rounded-xl">
          <div class="flex items-start gap-3">
            <svg class="w-6 h-6 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div>
              <h4 class="text-sm font-bold text-amber-800 mb-1">Montant de l'inscription</h4>
              <p class="text-sm text-amber-700">
                Le montant de l'inscription CIMA est de <span class="font-bold">3000 F CFA</span>.
                Veuillez renseigner les informations de paiement ci-dessous.
              </p>
            </div>
          </div>
        </div>

        <form @submit.prevent="submitForm" class="space-y-5">
          <!-- Informations personnelles (lecture seule) -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Matricule *</label>
              <input
                v-model="form.matricule_membre"
                type="text"
                required
                class="w-full px-4 py-3.5 border-2 border-emerald-200 rounded-xl bg-gray-50"
                disabled
              />
              <p class="text-xs text-gray-500 mt-1">Ce champ est automatiquement rempli</p>
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Prénom *</label>
              <input
                v-model="form.prenom"
                type="text"
                required
                class="w-full px-4 py-3.5 border-2 border-emerald-200 rounded-xl bg-gray-50"
                disabled
              />
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Nom *</label>
              <input
                v-model="form.nom"
                type="text"
                required
                class="w-full px-4 py-3.5 border-2 border-emerald-200 rounded-xl bg-gray-50"
                disabled
              />
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Qualité/Rôle *</label>
              <input
                v-model="form.qualite_membre"
                type="text"
                required
                class="w-full px-4 py-3.5 border-2 border-emerald-200 rounded-xl bg-gray-50"
                disabled
              />
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Téléphone</label>
              <input
                v-model="form.telephone"
                type="text"
                class="w-full px-4 py-3.5 border-2 border-emerald-200 rounded-xl bg-gray-50"
                disabled
              />
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm font-semibold text-gray-700 mb-2">Ville/Commune</label>
              <input
                v-model="form.ville_ou_commune"
                type="text"
                class="w-full px-4 py-3.5 border-2 border-emerald-200 rounded-xl bg-gray-50"
                disabled
              />
            </div>
          </div>

          <!-- Informations de paiement -->
          <div class="border-t-2 border-emerald-200 pt-6 mt-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Informations de paiement</h3>
            
            <div class="bg-emerald-50 border-2 border-emerald-200 rounded-xl p-4 mb-5">
              <p class="text-sm font-semibold text-emerald-800">
                Montant de l'inscription : <span class="text-lg">3 000 F CFA</span>
              </p>
              <p class="text-xs text-emerald-700 mt-1">
                Le statut de paiement et la référence seront enregistrés après le paiement.
              </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Contact de paiement</label>
                <input
                  v-model="form.contact_paiement"
                  type="text"
                  class="w-full px-4 py-3.5 border-2 border-emerald-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300"
                  placeholder="Téléphone ou email utilisé pour le paiement"
                />
              </div>
            </div>
          </div>

          <!-- Boutons d'action -->
          <div class="flex gap-3 justify-end pt-6 border-t-2 border-emerald-200">
            <button
              type="button"
              @click="resetForm"
              class="px-6 py-3 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all duration-200 font-medium"
            >
              Annuler
            </button>
            <button
              type="submit"
              :disabled="isSubmitting"
              class="px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-xl hover:from-emerald-700 hover:to-teal-700 disabled:from-gray-300 disabled:to-gray-300 disabled:cursor-not-allowed transition-all duration-300 flex items-center gap-2 font-medium shadow-lg hover:shadow-xl transform hover:scale-105 disabled:transform-none"
            >
              <svg v-if="isSubmitting" class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
              </svg>
              <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
              </svg>
              {{ isSubmitting ? 'Enregistrement...' : 'S\'inscrire' }}
            </button>
          </div>
        </form>
      </div>

      <!-- Message de succès -->
      <div v-if="showSuccessModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full">
          <div class="text-center">
            <div class="w-16 h-16 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
              </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-2">Inscription réussie !</h3>
            <p class="text-gray-600 mb-6">
              Votre inscription CIMA a été enregistrée avec succès.
            </p>
            <button
              @click="closeSuccessModal"
              class="px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-xl hover:from-emerald-700 hover:to-teal-700 transition-all duration-300 font-medium"
            >
              Fermer
            </button>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, nextTick } from 'vue'

const API_BASE_URL = 'https://sogetrag.com/apistage/cima_api.php'

// État
const currentStep = ref(1)
const matriculeInput = ref('')
const isVerifying = ref(false)
const verificationError = ref('')
const foundUser = ref(null)
const isSubmitting = ref(false)
const showSuccessModal = ref(false)

// Formulaire
const form = ref({
  matricule_membre: '',
  prenom: '',
  nom: '',
  qualite_membre: '',
  telephone: '',
  ville_ou_commune: '',
  contact_paiement: ''
})

// Formatage automatique du matricule (comme dans Login.vue)
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
  verificationError.value = ''
}

const isMatriculeValid = computed(() => {
  const regex = /^[A-Z0-9]{3}-[A-Z0-9]{3}-[A-Z0-9]{2}-[0-9]{3}$/
  return regex.test(matriculeInput.value)
})

// Debug matricule (pour voir le rôle exact)
const debugMatricule = async () => {
  if (!isMatriculeValid.value) {
    verificationError.value = 'Format de matricule invalide'
    return
  }
  
  try {
    const response = await fetch(`${API_BASE_URL}?action=debug_matricule&matricule=${encodeURIComponent(matriculeInput.value)}`, {
      method: 'GET',
      headers: {
        'Accept': 'application/json'
      }
    })
    
    if (!response.ok) {
      console.error('Erreur HTTP debug:', response.status)
      return
    }
    
    const data = await response.json()
    
    if (data.success) {
      console.log('🔍 DEBUG - Informations du matricule:', data)
      console.log('🔍 Rôle actuel:', data.qualite_membre)
      console.log('🔍 Rôle autorisé?', data.is_authorized)
      console.log('🔍 Longueur du rôle:', data.qualite_membre_length)
      console.log('🔍 Rôle en hex:', data.qualite_membre_hex)
      alert(`Rôle actuel: "${data.qualite_membre}"\nAutorisé: ${data.is_authorized ? 'OUI' : 'NON'}\n\nVoir la console pour plus de détails.`)
    }
  } catch (error) {
    console.error('Erreur debug:', error)
  }
}

// Vérifier le matricule
const verifyMatricule = async () => {
  if (!isMatriculeValid.value) {
    verificationError.value = 'Format de matricule invalide'
    return
  }
  
  isVerifying.value = true
  // Ne pas réinitialiser verificationError immédiatement pour garder les erreurs précédentes visibles
  // verificationError.value = ''
  foundUser.value = null
  
  try {
    const response = await fetch(`${API_BASE_URL}?action=verify_matricule&matricule=${encodeURIComponent(matriculeInput.value)}`, {
      method: 'GET',
      headers: {
        'Accept': 'application/json'
      }
    })
    
    // Essayer de parser la réponse même si le status n'est pas OK
    let data
    try {
      const text = await response.text()
      console.log('📄 Réponse brute:', text.substring(0, 200))
      data = text ? JSON.parse(text) : { success: false, error: 'Réponse vide' }
      console.log('📦 Données parsées:', data)
    } catch (parseError) {
      console.error('❌ Erreur parsing JSON:', parseError)
      verificationError.value = `Erreur HTTP ${response.status}: ${response.statusText}`
      return
    }
    
    // Si la réponse n'est pas OK, essayer d'extraire le message d'erreur
    if (!response.ok) {
      console.error('❌ Réponse non OK:', response.status, data)
      
      // Extraire le message d'erreur de l'API
      let errorMessage = 'Vous ne pouvez pas vous inscrire.'
      let currentRole = null
      
      if (data && data.error) {
        console.log('🔍 data.error:', data.error, 'Type:', typeof data.error)
        
        // Si l'erreur est un objet avec des détails
        if (typeof data.error === 'object' && data.error.message) {
          errorMessage = data.error.message
          currentRole = data.error.current_role || null
          console.log('✅ Message extrait:', errorMessage)
          console.log('✅ Rôle extrait:', currentRole)
          if (data.error.user_info) {
            console.error('🔍 Informations utilisateur:', data.error.user_info)
            console.error('🔍 Rôle actuel:', data.error.user_info.qualite_membre)
          }
        } else if (typeof data.error === 'string') {
          errorMessage = data.error
          console.log('✅ Message string:', errorMessage)
        }
      } else {
        console.warn('⚠️ Pas de data.error trouvé dans la réponse')
      }
      
      // Construire le message d'erreur avec le rôle
      if (currentRole) {
        verificationError.value = `Vous ne pouvez pas vous inscrire.\n\nVotre rôle actuel est : "${currentRole}"\n\nSeuls les Délégués culturels, SR (Secrétaire Régional), Formateurs et Imams peuvent s'inscrire.`
      } else {
        verificationError.value = errorMessage
      }
      
      console.log('📝 Message d\'erreur final:', verificationError.value)
      isVerifying.value = false // Réinitialiser isVerifying avant de retourner
      
      // Forcer la mise à jour de Vue
      await nextTick()
      console.log('✅ verificationError après nextTick:', verificationError.value)
      
      return
    }
    
    if (!data.success) {
      // Afficher le message d'erreur détaillé de l'API
      let errorMessage = data.error
      let currentRole = null
      
      // Si l'erreur est un objet avec des détails
      if (typeof data.error === 'object' && data.error.message) {
        errorMessage = data.error.message
        currentRole = data.error.current_role || null
        if (data.error.user_info) {
          console.error('🔍 Informations utilisateur:', data.error.user_info)
          console.error('🔍 Rôle actuel:', data.error.user_info.qualite_membre)
        }
        console.error('Erreur API CIMA (détaillée):', data.error)
      } else if (typeof data.error === 'string') {
        errorMessage = data.error
      }
      
      // Construire le message d'erreur avec le rôle
      if (currentRole) {
        verificationError.value = `Vous ne pouvez pas vous inscrire.\n\nVotre rôle actuel est : "${currentRole}"\n\nSeuls les Délégués culturels, SR (Secrétaire Régional), Formateurs et Imams peuvent s'inscrire.`
      } else {
        verificationError.value = errorMessage || 'Vous ne pouvez pas vous inscrire.'
      }
      
      isVerifying.value = false // Réinitialiser isVerifying
      
      // Forcer la mise à jour de Vue
      await nextTick()
      console.log('✅ verificationError après nextTick (data.success=false):', verificationError.value)
      
      return
    }
    
    if (data.already_registered) {
      verificationError.value = 'Ce matricule est déjà inscrit'
      isVerifying.value = false
      return
    }
    
    // Remplir le formulaire avec les données récupérées
    foundUser.value = data.user
    
    // Log pour debug
    console.log('📋 Données utilisateur reçues:', data.user)
    console.log('📞 Téléphone reçu:', data.user.telephone)
    
    form.value = {
      matricule_membre: data.user.matricule,
      prenom: data.user.prenom || '',
      nom: data.user.nom || '',
      qualite_membre: data.user.qualite_membre || '',
      telephone: data.user.telephone || '',
      ville_ou_commune: data.user.ville_ou_commune || '',
      contact_paiement: ''
    }
    
    // Log pour vérifier que le formulaire est bien rempli
    console.log('📝 Formulaire rempli:', form.value)
    console.log('📞 Téléphone dans formulaire:', form.value.telephone)
    
    // Passer à l'étape 2
    currentStep.value = 2
  } catch (error) {
    console.error('Erreur vérification:', error)
    verificationError.value = 'Erreur de connexion. Veuillez réessayer.'
  } finally {
    isVerifying.value = false
  }
}

// Le statut de paiement sera calculé automatiquement côté serveur
// Plus besoin de cette fonction car les champs sont cachés

// Soumettre le formulaire
const submitForm = async () => {
  isSubmitting.value = true
  
  try {
    // Préparer les données avec les valeurs par défaut
    const registrationData = {
      ...form.value,
      somme_payee: 0, // Montant fixe, sera mis à jour après paiement
      statut_paiement: 'non_paye', // Sera calculé côté serveur
      reference_paiement: '' // Sera rempli après paiement
    }
    
    const response = await fetch(`${API_BASE_URL}?action=create_registration`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify(registrationData)
    })
    
    const data = await response.json()
    
    if (!response.ok || !data.success) {
      alert(data.error || 'Erreur lors de l\'enregistrement')
      return
    }
    
    // Afficher le message de succès
    showSuccessModal.value = true
  } catch (error) {
    console.error('Erreur enregistrement:', error)
    alert('Erreur de connexion. Veuillez réessayer.')
  } finally {
    isSubmitting.value = false
  }
}

// Réinitialiser le formulaire
const resetForm = () => {
  currentStep.value = 1
  matriculeInput.value = ''
  verificationError.value = ''
  foundUser.value = null
  form.value = {
    matricule_membre: '',
    prenom: '',
    nom: '',
    qualite_membre: '',
    telephone: '',
    ville_ou_commune: '',
    contact_paiement: ''
  }
}

// Fermer le modal de succès
const closeSuccessModal = () => {
  showSuccessModal.value = false
  resetForm()
}
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

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fadeIn {
  animation: fadeIn 0.3s ease-out;
}
</style>

