<template>
  <div class="min-h-screen bg-gradient-to-br from-emerald-50 via-teal-50 to-cyan-50">
    <!-- Header -->
    <header class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-full flex items-center justify-center text-white font-bold text-xl shadow-lg">
              C
            </div>
            <div>
              <h1 class="text-2xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
                AEEMCI - CIMA / AGO
              </h1>
              <p class="text-sm text-gray-600">Confirmation d'Inscription CIMA / AGO</p>
            </div>
          </div>
          <button
            @click="nouvelleInscription"
            class="px-4 py-2 bg-white border-2 border-emerald-500 text-emerald-600 rounded-lg hover:bg-emerald-50 transition-all duration-200 flex items-center gap-2 font-medium"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Nouvelle inscription
          </button>
        </div>
      </div>
    </header>

    <!-- Loading State -->
    <div v-if="isLoading" class="flex items-center justify-center min-h-[70vh] px-4">
      <div class="text-center max-w-md">
        <div class="w-20 h-20 border-4 border-emerald-200 border-t-emerald-600 rounded-full animate-spin mx-auto mb-6"></div>
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Validation du paiement...</h2>
        <p class="text-gray-600">Veuillez patienter pendant que nous validons votre paiement</p>
        <p class="text-sm text-gray-500 mt-2">Matricule: {{ matricule }}</p>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="errorMessage" class="flex items-center justify-center min-h-[70vh] px-4">
      <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full text-center">
        <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Erreur</h2>
        <p class="text-gray-600 mb-6">{{ errorMessage }}</p>
        <div class="flex flex-col gap-3">
          <button
            @click="retryValidation"
            class="px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-lg hover:shadow-lg transition-all duration-200 flex items-center justify-center gap-2 font-medium"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Réessayer
          </button>
          <button
            @click="nouvelleInscription"
            class="px-6 py-3 bg-white border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all duration-200 font-medium"
          >
            Nouvelle inscription
          </button>
        </div>
      </div>
    </div>

    <!-- Success State -->
    <div v-else-if="inscription" class="max-w-6xl mx-auto px-4 py-8">
      <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
        <!-- Success Header -->
        <div class="bg-gradient-to-br from-emerald-500 via-teal-500 to-cyan-500 p-8 text-center relative overflow-hidden">
          <div class="absolute inset-0 bg-white opacity-10">
            <div class="absolute inset-0" style="background-image: radial-gradient(circle, white 1px, transparent 1px); background-size: 20px 20px;"></div>
          </div>
          <div class="relative z-10">
            <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-2xl animate-bounce">
              <svg class="w-12 h-12 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
              </svg>
            </div>
            <h1 class="text-4xl font-bold text-white mb-2 drop-shadow-lg">Félicitations !</h1>
            <p class="text-xl text-white mb-6 drop-shadow">Votre inscription au CIMA/AGO a été validée avec succès</p>
            
            <div class="bg-white rounded-xl p-6 max-w-md mx-auto shadow-xl">
              <div class="inline-flex items-center gap-2 bg-emerald-100 text-emerald-700 px-4 py-2 rounded-lg font-semibold mb-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Paiement validé
              </div>
              <p class="text-gray-700 mb-2 font-medium">Votre matricule d'inscription</p>
              <p class="text-4xl font-bold text-emerald-600 tracking-wider">{{ inscription.matricule_membre }}</p>
            </div>
          </div>
        </div>

        <!-- Récapitulatif -->
        <div class="p-8">
          <h3 class="text-2xl font-bold text-gray-800 mb-6 text-center flex items-center justify-center gap-2">
            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Récapitulatif de votre inscription
          </h3>
          
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
            <!-- Nom complet -->
            <div class="flex items-start gap-3 p-4 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl border border-gray-200 hover:shadow-md transition-shadow">
              <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-emerald-600" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Nom complet</p>
                <p class="text-base font-bold text-gray-800 truncate">{{ inscription.prenom }} {{ inscription.nom }}</p>
              </div>
            </div>

            <!-- Contact -->
            <div class="flex items-start gap-3 p-4 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl border border-gray-200 hover:shadow-md transition-shadow">
              <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Téléphone</p>
                <p class="text-base font-bold text-gray-800">{{ inscription.telephone || 'Non renseigné' }}</p>
              </div>
            </div>

            <!-- Qualité membre -->
            <div class="flex items-start gap-3 p-4 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl border border-gray-200 hover:shadow-md transition-shadow">
              <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-indigo-600" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 2L1 21h22L12 2zm0 3.99L19.53 19H4.47L12 5.99zM11 16h2v2h-2zm0-6h2v4h-2z"/>
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Qualité/Rôle</p>
                <p class="text-base font-bold text-gray-800">{{ inscription.qualite_membre || 'Non renseigné' }}</p>
              </div>
            </div>

            <!-- Ville/Commune -->
            <div class="flex items-start gap-3 p-4 bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl border-2 border-orange-200 hover:shadow-md transition-shadow">
              <div class="w-12 h-12 bg-orange-200 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-orange-700" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-xs text-orange-700 uppercase font-bold mb-1">Secrétariat</p>
                <p class="text-base font-bold text-gray-800">{{ inscription.ville_ou_commune || 'Non renseigné' }}</p>
              </div>
            </div>

            <!-- Thème attribué -->
            <div class="flex items-start gap-3 p-4 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl border-2 border-purple-200 hover:shadow-md transition-shadow">
              <div class="w-12 h-12 bg-purple-200 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-purple-700" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-xs text-purple-700 uppercase font-bold mb-1">Thème attribué</p>
                <p class="text-base font-bold text-gray-800">{{ inscription.theme || 'En attente' }}</p>
              </div>
            </div>

            <!-- Montant payé -->
            <div class="flex items-start gap-3 p-4 bg-gradient-to-br from-green-50 to-emerald-100 rounded-xl border-2 border-emerald-200 hover:shadow-md transition-shadow">
              <div class="w-12 h-12 bg-emerald-200 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-emerald-700" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/>
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-xs text-emerald-700 uppercase font-bold mb-1">Montant payé</p>
                <p class="text-base font-bold text-gray-800">{{ inscription.somme_payee || inscription.montant_inscription || '3000' }} F CFA</p>
              </div>
            </div>
          </div>

          <!-- Action principale - Télécharger PDF -->
          <div class="mb-6 max-w-2xl mx-auto">
            <button
              @click="generatePDF"
              :disabled="isGeneratingPDF"
              class="w-full px-8 py-5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl hover:shadow-2xl transition-all duration-200 font-bold text-lg flex items-center justify-center gap-3 disabled:opacity-50 disabled:cursor-not-allowed transform hover:scale-[1.02]"
            >
              <div v-if="isGeneratingPDF" class="w-6 h-6 border-3 border-white border-t-transparent rounded-full animate-spin"></div>
              <svg v-else class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              {{ isGeneratingPDF ? 'Génération en cours...' : 'Télécharger mon reçu d\'inscription PDF' }}
            </button>
          </div>

          <!-- Informations importantes -->
          <div class="space-y-4 mb-6">
            <div class="flex gap-4 p-6 bg-gradient-to-r from-emerald-50 to-teal-50 border-2 border-emerald-300 rounded-xl shadow-sm">
              <div class="w-14 h-14 bg-emerald-500 rounded-xl flex items-center justify-center flex-shrink-0 shadow-md">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
              </div>
              <div>
                <h4 class="font-bold text-emerald-800 mb-2 text-lg">Votre inscription est confirmée !</h4>
                <p class="text-sm text-emerald-700 leading-relaxed">
                  Conservez précieusement votre matricule <strong class="font-bold">{{ inscription.matricule_membre }}</strong> et votre reçu d'inscription. 
                  Ils attestent de votre inscription au CIMA/AGO.
                </p>
              </div>
            </div>

            <div v-if="inscription.theme" class="flex gap-4 p-6 bg-gradient-to-r from-purple-50 to-pink-50 border-2 border-purple-300 rounded-xl shadow-sm">
              <div class="w-14 h-14 bg-purple-500 rounded-xl flex items-center justify-center flex-shrink-0 shadow-md">
                <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                </svg>
              </div>
              <div>
                <h4 class="font-bold text-purple-800 mb-2 text-lg">Thème attribué</h4>
                <p class="text-sm text-purple-700 leading-relaxed">
                  Vous avez été attribué au <strong class="font-bold">{{ inscription.theme }}</strong> en fonction de votre secrétariat.
                  Cette répartition assure que chaque ville participe équitablement aux trois thèmes du CIMA.
                </p>
              </div>
            </div>

            <div class="flex gap-4 p-6 bg-gradient-to-r from-blue-50 to-cyan-50 border-2 border-blue-300 rounded-xl shadow-sm">
              <div class="w-14 h-14 bg-blue-500 rounded-xl flex items-center justify-center flex-shrink-0 shadow-md">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
              </div>
              <div>
                <h4 class="font-bold text-blue-800 mb-2 text-lg">Paiement validé</h4>
                <p class="text-sm text-blue-700 leading-relaxed">
                  Votre paiement de <strong class="font-bold">{{ inscription.somme_payee || inscription.montant_inscription || '3000' }} F CFA</strong> a été validé avec succès.
                </p>
                <p v-if="inscription.reference_paiement" class="text-xs text-blue-600 mt-2 font-medium">
                  Référence: {{ inscription.reference_paiement }}
                </p>
                <p v-if="inscription.contact_paiement" class="text-xs text-blue-600 mt-1 font-medium">
                  Contact: {{ inscription.contact_paiement }}
                </p>
              </div>
            </div>
          </div>

          <!-- Actions secondaires -->
          <div class="flex flex-wrap gap-3 justify-center pt-4 border-t border-gray-200">
            <button
              @click="shareInscription"
              class="px-6 py-3 bg-white border-2 border-emerald-500 text-emerald-600 rounded-lg hover:bg-emerald-50 transition-all duration-200 flex items-center gap-2 font-medium shadow-sm hover:shadow-md"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
              </svg>
              Partager
            </button>

            <button
              @click="contactSupport"
              class="px-6 py-3 bg-white border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all duration-200 flex items-center gap-2 font-medium shadow-sm hover:shadow-md"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
              Support
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import QRCode from 'qrcode'

const API_BASE_URL = 'https://sogetrag.com/api/verifier_cima.php'

// État
const inscription = ref(null)
const isLoading = ref(false)
const errorMessage = ref('')
const isGeneratingPDF = ref(false)
const matricule = ref('')

onMounted(() => {
  // Récupérer le matricule depuis l'URL (ex: /cima_paye/XXX-XXX-XX-XXX)
  const pathParts = window.location.pathname.split('/')
  const matriculeFromUrl = pathParts[pathParts.length - 1]
  
  if (matriculeFromUrl) {
    matricule.value = matriculeFromUrl
    validateAndLoadInscriptionData()
  } else {
    errorMessage.value = 'Matricule invalide dans l\'URL'
  }
})

const validateAndLoadInscriptionData = async () => {
  if (!matricule.value.trim()) {
    errorMessage.value = 'Matricule manquant'
    return
  }

  try {
    isLoading.value = true
    errorMessage.value = ''

    const response = await fetch(API_BASE_URL, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify({
        matricule: matricule.value.trim()
      })
    })

    const data = await response.json()
    console.log('[v0] API Response:', data)

    if (data.success) {
      inscription.value = data.membre
      console.log('[v0] Payment validated successfully:', data)
      
      // If payment already confirmed
      if (data.already_paid) {
        console.log('[v0] Payment already confirmed')
      }
    } else {
      errorMessage.value = data.message || 'Erreur lors de la validation du paiement'
      console.error('[v0] API Error:', data)
    }
  } catch (error) {
    console.error('[v0] Fetch Error:', error)
    errorMessage.value = 'Erreur lors de la validation du paiement. Veuillez réessayer.'
  } finally {
    isLoading.value = false
  }
}

const generatePDF = async () => {
  isGeneratingPDF.value = true

  try {
    const { default: jsPDF } = await import('jspdf')
    const doc = new jsPDF('p', 'mm', 'a4')
    const today = new Date()
    const receiptNumber = 'CIMA-' + Math.floor(Math.random() * 99999).toString().padStart(5, '0')
    const qrText = `${window.location.origin}/cima_paye/${inscription.value.matricule_membre}`
    const qrDataURL = await QRCode.toDataURL(qrText, {
      width: 120,
      margin: 1,
    })

    console.log('[v0] Starting PDF generation with data:', inscription.value)

    doc.setFont('helvetica', 'bold')
    doc.setFontSize(8)
    doc.setTextColor(245, 245, 245)
    for (let y = 20; y <= 280; y += 20) {
      for (let x = 10; x <= 200; x += 40) {
        doc.text('AEEMCI', x, y)
      }
    }

    doc.setFillColor(0, 128, 0)
    doc.rect(0, 0, 210, 35, 'F')
    
    // Logo
    try {
      doc.addImage('https://upload.wikimedia.org/wikipedia/fr/4/42/Logo_AEEMCI.jpeg', 'JPEG', 10, 8, 28, 25)
    } catch (e) {
      console.warn('[v0] Logo loading failed')
    }
    
    // Titre principal en blanc
    doc.setFont('helvetica', 'bold')
    doc.setFontSize(20)
    doc.setTextColor(255, 255, 255)
    doc.text('AEEMCI', 105, 15, { align: 'center' })
    doc.setFontSize(10)
    doc.text("Association des Elèves et Etudiants Musulmans de Côte d'Ivoire", 105, 20, { align: 'center' })
    
    // Sous-titre
    doc.setFontSize(14)
    doc.text("REÇU D'INSCRIPTION CIMA / AGO", 105, 28, { align: 'center' })

    let startY = 45

    doc.setTextColor(0, 0, 0)
    doc.setFont('helvetica', 'normal')
    doc.setFontSize(9)
    doc.text(`REÇU N° : ${receiptNumber}`, 18, startY)
    doc.text(`Date : ${today.toLocaleDateString('fr-FR')}`, 165, startY)
    
    doc.setFont('helvetica', 'bold')
    doc.setFontSize(12)
    doc.text(`MATRICULE: ${inscription.value.matricule_membre}`, 105, startY + 7, { align: 'center' })

    // Ligne de séparation
    doc.setLineWidth(0.5)
    doc.setDrawColor(0, 128, 0)
    doc.line(15, startY + 12, 195, startY + 12)

    console.log('[v0] Header section complete')

    let yPos = startY + 20
    doc.setFillColor(240, 248, 255)
    doc.rect(15, yPos - 5, 180, 8, 'F')
    doc.setFont('helvetica', 'bold')
    doc.setFontSize(11)
    doc.setTextColor(0, 0, 0)
    doc.text("IDENTIFICATION DU MEMBRE", 20, yPos)

    if (inscription.value.photo_membre) {
      try {
        doc.addImage(inscription.value.photo_membre, 'JPEG', 155, yPos + 2, 30, 30)
        console.log('[v0] Photo added successfully')
      } catch (e) {
        console.warn('[v0] Could not add photo:', e)
      }
    }

    yPos += 10
    doc.setFontSize(9)
    
    // Colonne gauche
    doc.setFont('helvetica', 'normal')
    doc.text('Nom :', 20, yPos)
    doc.setFont('helvetica', 'bold')
    doc.text(inscription.value.nom || 'N/A', 35, yPos)
    
    doc.setFont('helvetica', 'normal')
    doc.text('Prénom(s) :', 20, yPos + 6)
    doc.setFont('helvetica', 'bold')
    doc.text(inscription.value.prenom || 'N/A', 40, yPos + 6)
    
    doc.setFont('helvetica', 'normal')
    doc.text('Matricule :', 20, yPos + 12)
    doc.setFont('helvetica', 'bold')
    doc.text(inscription.value.matricule_membre || 'N/A', 42, yPos + 12)
    
    doc.setFont('helvetica', 'normal')
    doc.text('Téléphone :', 20, yPos + 18)
    doc.setFont('helvetica', 'bold')
    doc.text(inscription.value.telephone || 'Non renseigné', 42, yPos + 18)

    // Colonne droite
    doc.setFont('helvetica', 'normal')
    doc.text('Qualité :', 90, yPos)
    doc.setFont('helvetica', 'bold')
    doc.text(inscription.value.qualite_membre || 'N/A', 107, yPos)
    
    doc.setFont('helvetica', 'normal')
    doc.text('Secrétariat :', 90, yPos + 6)
    doc.setFont('helvetica', 'bold')
    doc.text(inscription.value.ville_ou_commune || 'N/A', 112, yPos + 6)

    doc.setFont('helvetica', 'normal');
    doc.setTextColor(0, 0, 0);
    doc.text('Atelier :', 90, yPos + 12);

    doc.setFont('helvetica', 'bold');
    doc.setTextColor(128, 0, 128);

    const theme = inscription.value.theme || 'En attente';

    // Largeur maximale autorisée pour le texte (en points)
    const maxWidth = 80;

    // Diviser le texte si trop long
    const splitTheme = doc.splitTextToSize(theme, maxWidth);

    // Afficher le texte, ligne par ligne
    let lineY = yPos + 12;
    splitTheme.forEach(line => {
      doc.text(line, 104, lineY);
      lineY += 5; // espacement entre les lignes
    });

    doc.setTextColor(0, 0, 0);

    console.log('[v0] Identification section complete')

    yPos += 35
    doc.setFillColor(240, 248, 255)
    doc.rect(15, yPos - 5, 180, 8, 'F')
    doc.setFont('helvetica', 'bold')
    doc.setFontSize(11)
    doc.setTextColor(0, 0, 0)
    doc.text("INFORMATIONS DE PAIEMENT", 20, yPos)
    
    yPos += 8
    doc.setFontSize(9)
    
    doc.setFont('helvetica', 'normal')
    doc.text('Référence :', 20, yPos)
    doc.setFont('helvetica', 'bold')
    doc.text(inscription.value.ref_paiement || inscription.value.ref_paiement || 'N/A', 42, yPos)
    
    doc.setFont('helvetica', 'normal')
    doc.text('Statut :', 90, yPos)
    doc.setFont('helvetica', 'bold')
    const statutText = inscription.value.statut_paiement === 'paye' ? 'PAYÉ' : 'EN ATTENTE'
    doc.setTextColor(0, 128, 0)
    doc.text(statutText, 105, yPos)
    doc.setTextColor(0, 0, 0)
    
    doc.setFont('helvetica', 'normal')
    doc.text('Contact paiement :', 20, yPos + 6)
    doc.setFont('helvetica', 'bold')
    doc.text(inscription.value.contact_paiement || 'N/A', 52, yPos + 6)
    
    doc.setFont('helvetica', 'normal')
    doc.text('Date inscription :', 90, yPos + 6)
    doc.setFont('helvetica', 'bold')
    const dateInscription = new Date(inscription.value.created_at).toLocaleDateString('fr-FR')
    doc.text(dateInscription, 118, yPos + 6)

    doc.setFont('helvetica', 'normal')
    doc.text('Montant payé :', 20, yPos + 12)
    doc.setFont('helvetica', 'bold')
    doc.setFontSize(11)
    doc.setTextColor(0, 128, 0)
    const montant = inscription.value.somme_payee || inscription.value.montant_inscription || '3000'
    doc.text(`${montant} F CFA`, 48, yPos + 12)
    doc.setTextColor(0, 0, 0)
    doc.setFontSize(9)

    doc.addImage(qrDataURL, 'PNG', 160, yPos - 15, 25, 25)

    console.log('[v0] Payment section complete')

    yPos += 25
    doc.setFillColor(240, 255, 240)
    doc.rect(15, yPos, 180, 30, 'F')
    doc.setDrawColor(0, 128, 0)
    doc.setLineWidth(2)
    doc.rect(15, yPos, 180, 30)
    
    doc.setFont('helvetica', 'bold')
    doc.setFontSize(14)
    doc.setTextColor(0, 128, 0)
    doc.text('INSCRIPTION VALIDÉE', 105, yPos + 12, { align: 'center' })
    doc.setFontSize(10)
    doc.text(`Reçu d'inscription au CIMA / AGO`, 105, yPos + 20, { align: 'center' })
    doc.setTextColor(0, 0, 0)

    console.log('[v0] Validation stamp complete')

    yPos += 40
    doc.setFillColor(255, 248, 220)
    doc.rect(15, yPos - 5, 180, 20, 'F')
    doc.setFont('helvetica', 'bold')
    doc.setFontSize(10)
    doc.setTextColor(204, 102, 0)
    doc.text("NOTE IMPORTANTE", 20, yPos)

    yPos += 6
    doc.setTextColor(0, 0, 0)
    doc.setFont('helvetica', 'normal')
    doc.setFontSize(8)
    doc.text("Ce reçu atteste de votre inscription au CIMA/AGO. Conservez-le précieusement.", 20, yPos)
    yPos += 4
    doc.text("Il pourra vous être demandé lors des activités AEEMCI.", 20, yPos)

    if (inscription.value.theme) {
      yPos += 5
      doc.setFont('helvetica', 'bold')
      doc.setTextColor(128, 0, 128)
      doc.text(`Vous êtes inscrit au ${inscription.value.theme}.`, 20, yPos)
      doc.setTextColor(0, 0, 0)
    }

    console.log('[v0] Important note complete')

    doc.setFillColor(0, 128, 0)
    doc.rect(0, 270, 210, 27, 'F')
    
    doc.setFont('helvetica', 'normal')
    doc.setFontSize(7)
    doc.setTextColor(255, 255, 255)
    doc.text("Siège Social : Mosquée An-Nour de la Riviera II / 08 BP 2462 Abidjan 08", 105, 278, { align: 'center' })
    doc.text("27 33 75 42 12 / 07 09 99 55 31 / 05 44 54 34 89 / aeemci@yahoo.fr / www.aeemci-ce.ci", 105, 282, { align: 'center' })
    
    doc.setFont('helvetica', 'bold')
    doc.setFontSize(9)
    doc.text("AEEMCI, pour une identité islamique !", 105, 289, { align: 'center' })

    console.log('[v0] Footer complete, saving PDF...')

    const filename = `recu_inscription_cima_${inscription.value.matricule_membre}_${today.toISOString().split('T')[0]}.pdf`
    doc.save(filename)

    console.log('[v0] PDF generated and downloaded successfully:', filename)

  } catch (error) {
    console.error('[v0] PDF generation error:', error)
    alert("Erreur lors de la génération du PDF. Veuillez réessayer.")
  } finally {
    isGeneratingPDF.value = false
  }
}

// Actions
const shareInscription = () => {
  const text = `Je me suis inscrit au CIMA/AGO ! Matricule: ${inscription.value.matricule_membre}`
  if (navigator.share) {
    navigator.share({
      title: 'Mon inscription CIMA / AGO',
      text: text,
      url: window.location.href
    })
  } else {
    navigator.clipboard.writeText(text).then(() => {
      alert('Texte copié dans le presse-papiers !')
    })
  }
}

const contactSupport = () => {
  window.open('mailto:aeemci@yahoo.fr?subject=Support - Inscription CIMA / AGO ' + inscription.value.matricule_membre, '_blank')
}

const nouvelleInscription = () => {
  window.location.href = '/cima'
}

const retryValidation = () => {
  errorMessage.value = ''
  validateAndLoadInscriptionData()
}
</script>

<style scoped>
@keyframes spin {
  to { transform: rotate(360deg); }
}

@keyframes bounce {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-10px);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}

.animate-bounce {
  animation: bounce 1s ease-in-out infinite;
}
</style>
