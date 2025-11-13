<template>
  <div class="min-h-screen flex items-center justify-center p-4 relative overflow-hidden bg-gradient-to-br from-emerald-50 via-white to-green-50">
    <!-- Decorative background elements -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <div class="absolute top-0 left-0 w-96 h-96 bg-gradient-to-br from-green-200/30 to-transparent rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2"></div>
      <div class="absolute bottom-0 right-0 w-96 h-96 bg-gradient-to-tl from-emerald-200/30 to-transparent rounded-full blur-3xl translate-x-1/2 translate-y-1/2"></div>
    </div>

    <!-- Main Container -->
    <div :class="['relative w-full', isMobile ? 'max-w-md min-h-screen' : 'max-w-5xl']">
      <div :class="[
        isMobile ? 'bg-transparent' : 'bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20',
        'overflow-hidden'
      ]">
        <div :class="['grid', isMobile ? 'grid-cols-1 min-h-screen' : 'lg:grid-cols-2 min-h-[600px]']">
          
          <!-- Form Section -->
          <div :class="[isMobile ? 'p-6 pt-12' : 'p-12', 'flex flex-col justify-center relative z-10']">
            <!-- Logo Card -->
            <div class="bg-white rounded-2xl shadow-lg p-8 mb-8 border border-green-100">
              <div class="flex items-center justify-center mb-4">
                <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg transform rotate-3">
                  <span class="text-white text-2xl font-bold transform -rotate-3">A</span>
                </div>
              </div>
              <h2 class="text-center text-2xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">
                Espace Aeemciste
              </h2>
            </div>

            <!-- Welcome Text -->
            <div class="mb-8">
              <h1 class="text-3xl font-bold text-gray-800 mb-3">
                As salamou alaykoum ! 👋
              </h1>
              <p class="text-gray-600 leading-relaxed">
                Pour vous connecter à votre compte veuillez saisir votre matricule et votre mot de passe.
              </p>
            </div>

            <!-- Form -->
            <div class="space-y-5">
              <!-- Matricule Input -->
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                  Matricule
                </label>
                <div class="relative">
                  <div class="absolute left-4 top-1/2 -translate-y-1/2 text-green-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                  </div>
                  <input
                    v-model="matricule"
                    type="text"
                    placeholder="XXX-XXX-XX-XXX"
                    maxlength="15"
                    @input="validateMatricule"
                    :class="[
                      'w-full pl-12 pr-4 py-4 bg-white border-2 rounded-xl focus:outline-none transition-all duration-300 text-gray-800 font-medium shadow-sm hover:shadow-md',
                      matriculeError ? 'border-red-300 focus:border-red-500' : 'border-gray-200 focus:border-green-500'
                    ]"
                  />
                </div>
                <p v-if="matriculeError" class="mt-2 text-sm text-red-500 flex items-center gap-1">
                  <span class="w-1 h-1 bg-red-500 rounded-full"></span>
                  {{ matriculeError }}
                </p>
              </div>

              <!-- Password Input -->
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                  Mot de passe
                </label>
                <div class="relative">
                  <div class="absolute left-4 top-1/2 -translate-y-1/2 text-green-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                  </div>
                  <input
                    v-model="password"
                    :type="showPassword ? 'text' : 'password'"
                    placeholder="Votre mot de passe"
                    class="w-full pl-12 pr-12 py-4 bg-white border-2 border-gray-200 rounded-xl focus:outline-none focus:border-green-500 transition-all duration-300 text-gray-800 font-medium shadow-sm hover:shadow-md"
                  />
                  <button
                    type="button"
                    @click="showPassword = !showPassword"
                    class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-green-600 transition-colors"
                  >
                    <svg v-if="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                    </svg>
                    <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                  </button>
                </div>
              </div>

              <!-- Forgot Password -->
              <div class="flex justify-between items-center">
                <a href="#" class="text-sm font-semibold text-orange-500 hover:text-orange-600 transition-colors">
                  Mot de passe oublié?
                </a>
              </div>

              <!-- Error Message -->
              <div v-if="error" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
                {{ error }}
              </div>

              <!-- Submit Button -->
              <button
                type="button"
                @click="handleLogin"
                :disabled="!isFormValid"
                :class="[
                  'w-full py-4 rounded-xl font-bold text-white transition-all duration-300 shadow-lg transform',
                  isFormValid
                    ? 'bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 hover:scale-[1.02] hover:shadow-xl active:scale-[0.98]'
                    : 'bg-gray-300 cursor-not-allowed'
                ]"
              >
                <span v-if="isFormValid" class="flex items-center justify-center gap-2">
                  Étape suivante
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </span>
                <span v-else>Étape suivante</span>
              </button>

              <!-- Sign Up Link -->
              <div class="text-center pt-4">
                <p class="text-gray-600">
                  Pas encore de compte?
                  <a href="/inscription" class="font-bold text-orange-500 hover:text-orange-600 transition-colors">
                    Créer un compte
                  </a>
                </p>
              </div>
            </div>
          </div>

          <!-- Visual Section - Hidden on Mobile -->
          <div v-if="!isMobile" class="bg-gradient-to-br from-green-500 via-emerald-600 to-green-700 p-12 flex flex-col items-center justify-center relative overflow-hidden">
            <!-- Decorative circles -->
            <div class="absolute top-10 right-10 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
            <div class="absolute bottom-10 left-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
            
            <!-- Illustration -->
            <div class="relative z-10 text-center">
              <div class="mb-8">
                <div class="inline-flex items-center justify-center w-32 h-32 bg-white/20 backdrop-blur-sm rounded-3xl shadow-2xl mb-6 transform hover:scale-110 transition-transform duration-300">
                  <svg class="w-20 h-20 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                  </svg>
                </div>
              </div>
              
              <h3 class="text-3xl font-bold text-white mb-4">
                Bienvenue dans votre espace
              </h3>
              <p class="text-white/90 text-lg leading-relaxed max-w-md mx-auto">
                Accédez à tous vos services et gérez votre compte militant en toute simplicité
              </p>

              <!-- Decorative elements -->
              <div class="flex justify-center gap-2 mt-8">
                <div class="w-2 h-2 bg-white/60 rounded-full animate-pulse"></div>
                <div class="w-2 h-2 bg-white/40 rounded-full animate-pulse delay-75"></div>
                <div class="w-2 h-2 bg-white/60 rounded-full animate-pulse delay-150"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useUserStore } from '../../stores/user'
import { setCurrentUser } from '../../utils/database.js'


const userStore = useUserStore()

const matricule = ref('')
const password = ref('')
const showPassword = ref(false)
const matriculeError = ref('')
const error = ref('')
const isMobile = ref(window.innerWidth <= 768)

const validateMatricule = () => {
  const value = matricule.value.toUpperCase()
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
  
  matricule.value = formatted
  
  const regex = /^[A-Z0-9]{3}-[A-Z0-9]{3}-[A-Z0-9]{2}-[0-9]{3}$/
  const lastFiveChars = formatted.replace(/-/g, '').slice(-5)
  const isLastFiveDigits = /^[0-9]{5}$/.test(lastFiveChars)
  
  if (formatted.length === 15) {
    if (!regex.test(formatted)) {
      matriculeError.value = 'Format invalide'
    } else if (!isLastFiveDigits) {
      matriculeError.value = 'Les 5 derniers caractères doivent être des chiffres'
    } else {
      matriculeError.value = ''
    }
  } else if (formatted.length > 0) {
    matriculeError.value = ''
  }
}

const isFormValid = computed(() => {
  return matricule.value.length >= 14 && !matriculeError.value && password.value.length > 0
})

const handleLogin = async () => {
  if (!isFormValid.value) return

  //isLoading.value = true

  try {
    const response = await fetch('https://sogetrag.com/api/organes-api.php?action=login_user', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        matricule: matricule.value,
        password: password.value
      })
    })

    const data = await response.json()

    if (data.success) {
      // Stocke toute la réponse dans le store
      userStore.login(data.user)
      
      // Stocker le matricule dans sessionStorage pour que Posts.vue puisse le récupérer
      const userMatricule = data.user.matricule_gen || data.user.matricule || data.user.id_membre || matricule.value
      if (userMatricule) {
        setCurrentUser(userMatricule, data.user, false)
        console.log('Matricule stocké:', userMatricule)
      }
      
      console.log('Utilisateur connecté:', data.user)
    } else {
        error.value = data.error || 'Échec de la connexion'
     // alert(data.error || 'Échec de la connexion')
    }
  } catch (err) {
    console.error('Erreur connexion:', err)
    //alert('Erreur serveur, réessayez plus tard')
    error.value = 'Erreur serveur, réessayez plus tard'
  } finally {
   // isLoading.value = false
  }
}


const handleResize = () => {
  isMobile.value = window.innerWidth <= 768
}

onMounted(() => {
  window.addEventListener('resize', handleResize)
})

onUnmounted(() => {
  window.removeEventListener('resize', handleResize)
})
</script>

<style scoped>
@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.5; }
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

.delay-75 {
  animation-delay: 75ms;
}

.delay-150 {
  animation-delay: 150ms;
}

/* Tailwind-like utility classes */
.bg-gradient-to-br {
  background-image: linear-gradient(to bottom right, var(--tw-gradient-stops));
}

.bg-gradient-to-tl {
  background-image: linear-gradient(to top left, var(--tw-gradient-stops));
}

.bg-gradient-to-r {
  background-image: linear-gradient(to right, var(--tw-gradient-stops));
}

.from-emerald-50 {
  --tw-gradient-from: #ecfdf5;
  --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, rgba(236, 253, 245, 0));
}

.via-white {
  --tw-gradient-stops: var(--tw-gradient-from), #ffffff, var(--tw-gradient-to, rgba(255, 255, 255, 0));
}

.to-green-50 {
  --tw-gradient-to: #f0fdf4;
}

.from-green-200\/30 {
  --tw-gradient-from: rgba(187, 247, 208, 0.3);
  --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, rgba(187, 247, 208, 0));
}

.to-transparent {
  --tw-gradient-to: transparent;
}

.from-emerald-200\/30 {
  --tw-gradient-from: rgba(167, 243, 208, 0.3);
  --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, rgba(167, 243, 208, 0));
}

.from-green-500 {
  --tw-gradient-from: #22c55e;
  --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, rgba(34, 197, 94, 0));
}

.to-emerald-600 {
  --tw-gradient-to: #059669;
}

.via-emerald-600 {
  --tw-gradient-stops: var(--tw-gradient-from), #059669, var(--tw-gradient-to, rgba(5, 150, 105, 0));
}

.to-green-700 {
  --tw-gradient-to: #15803d;
}

.from-green-600 {
  --tw-gradient-from: #16a34a;
  --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, rgba(22, 163, 74, 0));
}

.bg-clip-text {
  -webkit-background-clip: text;
  background-clip: text;
}

.text-transparent {
  color: transparent;
}

.backdrop-blur-xl {
  backdrop-filter: blur(24px);
}

.backdrop-blur-sm {
  backdrop-filter: blur(4px);
}

@media (max-width: 768px) {
  .lg\:grid-cols-2 {
    grid-template-columns: repeat(1, minmax(0, 1fr));
  }
}

@media (min-width: 769px) {
  .lg\:grid-cols-2 {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

/* ✅ Correction affichage input en mode dark */
input,
textarea,
select {
  color: black !important; /* texte toujours visible */
  background-color: white !important; /* fond clair pour éviter le blanc sur blanc */
  border: 1px solid #ccc !important;
}

input::placeholder,
textarea::placeholder {
  color: #888 !important; /* gris doux pour le placeholder */
}

/* ✅ Facultatif : si tu veux que le texte d’erreur reste visible aussi */
label,
p,
span {
  color: #222;
}

</style>