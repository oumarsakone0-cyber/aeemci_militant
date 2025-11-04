<template>
  <div class="login-container">
    <div class="login-card">
      <!-- Section formulaire -->
      <div class="form-section">
        <div class="header">
          <div class="logo">
            <span class="logo-text">Espace </span><span class="logo-accent">Militant</span>
          </div>
        </div>
        
        <div class="form-content">
          <!-- Étape 1: Saisie du matricule -->
          <div v-if="currentStep === 1">
            <h1 class="title">Inscription</h1>
            <p class="subtitle">
              Pour vous inscrire, veuillez d'abord saisir votre matricule 
              pour récupérer vos informations.
            </p>
            
            <form @submit.prevent="validateMatricule" class="login-form">
              <div class="input-group">
                <input 
                  v-model="matricule"
                  type="text" 
                  placeholder="Matricule (XXX-XXX-XX-XXX)"
                  class="input-field"
                  :class="{ 'error': matriculeError }"
                  @input="formatMatricule"
                  maxlength="15"
                  required
                />
                <span v-if="matriculeError" class="error-message">{{ matriculeError }}</span>
              </div>
              
              <button type="submit" class="login-button" :disabled="!isMatriculeValid || isLoading">
                {{ isLoading ? 'Vérification...' : 'Valider le matricule' }}
              </button>
            </form>
          </div>

          <!-- Étape 2: Formulaire d'inscription avec données pré-remplies -->
          <div v-else-if="currentStep === 2">
            <h1 class="title">Finaliser l'inscription</h1>
            <p class="subtitle">
              Vérifiez vos informations et créez votre mot de passe.
            </p>
            
            <!-- Amélioration de la carte utilisateur avec photo et style élégant -->
            <div class="user-profile-card">
              <div class="profile-header">
                <div class="profile-avatar">
                  <img v-if="userData.photo" :src="userData.photo" :alt="userData.nom_complet" />
                  <div v-else class="avatar-placeholder">
                    {{ userData.nom_complet ? userData.nom_complet.charAt(0) : 'U' }}
                  </div>
                </div>
                <div class="profile-info">
                  <h3 class="profile-name">{{ userData.nom_complet }}</h3>
                  <p class="profile-matricule">{{ userData.matricule }}</p>
                </div>
              </div>
              
              <div class="profile-details">
                <div class="detail-row">
                  <div class="detail-icon">💼</div>
                  <div class="detail-content">
                    <span class="detail-label">Poste actuel</span>
                    <span class="detail-value">{{ userData.qualite_membre || userData.poste }}</span>
                  </div>
                </div>
                
                <div class="detail-row">
                  <div class="detail-icon">🏢</div>
                  <div class="detail-content">
                    <span class="detail-label">Organe</span>
                    <span class="detail-value">
                    {{ userData.section 
                        ? 'Section' 
                        : userData.souscomite 
                            ? 'Sous-comité' 
                            : userData.secretariat 
                            ? 'Secrétariat' 
                            : '' }}
                    </span>
                  </div>
                </div>
                
                
                <div v-if="userData.mandat" class="detail-row">
                  <div class="detail-icon">📅</div>
                  <div class="detail-content">
                    <span class="detail-label">Mandat</span>
                    <span class="detail-value">{{ userData.mandat.statut }} - {{ userData.mandat.date_nomination }}</span>
                  </div>
                </div>
              </div>
            </div>
            
            <form @submit.prevent="handleInscription" class="login-form">
              <div class="input-group">
                <input 
                  v-model="password"
                  type="password" 
                  placeholder="Créer un mot de passe"
                  class="input-field"
                  :class="{ 'error': passwordError }"
                  @input="validatePassword"
                  required
                />
                <span v-if="passwordError" class="error-message">{{ passwordError }}</span>
              </div>

              <div class="input-group">
                <input 
                  v-model="confirmPassword"
                  type="password" 
                  placeholder="Confirmer le mot de passe"
                  class="input-field"
                  :class="{ 'error': confirmPasswordError }"
                  @input="validateConfirmPassword"
                  required
                />
                <span v-if="confirmPasswordError" class="error-message">{{ confirmPasswordError }}</span>
              </div>
              
              <button type="submit" class="login-button" :disabled="!isFormValid || isLoading">
                {{ isLoading ? 'Inscription...' : 'Finaliser l\'inscription' }}
              </button>

              <button type="button" @click="goBack" class="back-button">
                Retour
              </button>
            </form>
          </div>
          
          <!-- Étape 3: Confirmation d'inscription -->
          <div v-else-if="currentStep === 3">
            <h1 class="title">Inscription réussie!</h1>
            <p class="subtitle">
              Votre compte a été créé avec succès. Vous pouvez maintenant vous connecter.
            </p>
            
            <button @click="goToLogin" class="login-button">
              Aller à la connexion
            </button>
          </div>
          
          <p class="contact-text">
            N'hésitez pas à <a href="#" class="contact-link">nous contacter</a>
          </p>
        </div>
      </div>
      
      <!-- Section visuelle -->
      <div class="visual-section">
        <div class="plant-container">
          <div class="plant-pot pot-1">
            <div class="pot"></div>
            <div class="plant">
              <div class="leaf leaf-1"></div>
              <div class="leaf leaf-2"></div>
              <div class="leaf leaf-3"></div>
              <div class="leaf leaf-4"></div>
            </div>
          </div>
          <div class="plant-pot pot-2">
            <div class="pot"></div>
            <div class="plant">
              <div class="leaf leaf-1"></div>
              <div class="leaf leaf-2"></div>
              <div class="leaf leaf-3"></div>
            </div>
          </div>
        </div>
        
        <div class="info-card">
          <div class="play-button">▶</div>
          <h3>Rejoignez notre communauté</h3>
          <p>Créez votre compte pour accéder à toutes les fonctionnalités et services personnalisés.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const currentStep = ref(1)
const isLoading = ref(false)

const matricule = ref('')
const password = ref('')
const confirmPassword = ref('')
const userData = ref({})

const matriculeError = ref('')
const passwordError = ref('')
const confirmPasswordError = ref('')

const formatMatricule = () => {
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

const validatePassword = () => {
  if (password.value.length < 6) {
    passwordError.value = 'Le mot de passe doit contenir au moins 6 caractères'
  } else {
    passwordError.value = ''
  }
  validateConfirmPassword()
}

const validateConfirmPassword = () => {
  if (confirmPassword.value && confirmPassword.value !== password.value) {
    confirmPasswordError.value = 'Les mots de passe ne correspondent pas'
  } else {
    confirmPasswordError.value = ''
  }
}

const isMatriculeValid = computed(() => {
  return matricule.value.length >= 14 && !matriculeError.value
})

const isFormValid = computed(() => {
  return password.value.length >= 6 && 
         !passwordError.value && 
         confirmPassword.value === password.value &&
         !confirmPasswordError.value
})

const validateMatricule = async () => {
  if (!isMatriculeValid.value) return
  
  isLoading.value = true
  matriculeError.value = ''
  
  try {
    // Simulation d'appel API - remplacez par votre endpoint
    const response = await fetch(`https://sogetrag.com/api/organes-api.php?action=get_user_by_matricule`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ matricule: matricule.value })
    })
    

      const data = await response.json()
      userData.value = {
        matricule: data.user.matricule,
        nom_complet: data.user.nom_complet,
        nom: data.user.nom,
        prenom: data.user.prenom,
        contact: data.user.contact,
        photo: data.user.photo_membre,
        poste: data.user.poste,
        organe: data.user.organe,
        mandat: data.user.mandat,
        section: data.user.section,
        souscomite: data.user.souscomite,
        secretariat: data.user.secretariat,
        qualite_membre: data.user.qualite_membre
      }
      currentStep.value = 2
    
  } catch (error) {
    console.error('Erreur lors de la validation:', error)
    matriculeError.value = 'Erreur de connexion. Veuillez réessayer.'
  } finally {
    isLoading.value = false
  }
}

const handleInscription = async () => {
  if (!isFormValid.value) return
  
  isLoading.value = true
  
  try {
    const response = await fetch('https://sogetrag.com/api/organes-api.php?action=set_password_for_matricule', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        matricule: userData.value.matricule,
        password: password.value
      })
    })
    
    if (response.ok) {
      currentStep.value = 3
    } else {
      const error = await response.json()
      console.error('Erreur inscription:', error)
    }
  } catch (error) {
    console.error('Erreur lors de l\'inscription:', error)
  } finally {
    isLoading.value = false
  }
}

const goBack = () => {
  currentStep.value = 1
  password.value = ''
  confirmPassword.value = ''
  passwordError.value = ''
  confirmPasswordError.value = ''
}

const goToLogin = () => {
  // Navigation vers la page de connexion
  router.push({ name: 'login' })
}
</script>

<style scoped>

/* Styles spécifiques pour l'inscription */
.user-profile-card {
  background: linear-gradient(135deg, #ffffff 0%, #f8fffe 100%);
  border: 1px solid #e1f5e1;
  border-radius: 20px;
  padding: 24px;
  margin-bottom: 24px;
  box-shadow: 0 8px 32px rgba(1, 132, 4, 0.08);
  transition: all 0.3s ease;
}

.user-profile-card:hover {
  box-shadow: 0 12px 40px rgba(1, 132, 4, 0.12);
  transform: translateY(-2px);
}

.profile-header {
  display: flex;
  align-items: center;
  gap: 16px;
  margin-bottom: 20px;
  padding-bottom: 20px;
  border-bottom: 1px solid #f0f8f0;
}

.profile-avatar {
  position: relative;
}

.profile-avatar img {
  width: 70px;
  height: 70px;
  border-radius: 50%;
  object-fit: cover;
  border: 3px solid #018404;
  box-shadow: 0 4px 12px rgba(1, 132, 4, 0.2);
}

.avatar-placeholder {
  width: 70px;
  height: 70px;
  border-radius: 50%;
  background: linear-gradient(135deg, #018404, #02a305);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
  font-weight: 600;
  box-shadow: 0 4px 12px rgba(1, 132, 4, 0.2);
}

.profile-info {
  flex: 1;
}

.profile-name {
  color: #333;
  font-size: 20px;
  font-weight: 700;
  margin-bottom: 4px;
  line-height: 1.2;
}

.profile-matricule {
  color: #018404;
  font-size: 14px;
  font-weight: 600;
  background: #f0f8f0;
  padding: 4px 12px;
  border-radius: 20px;
  display: inline-block;
}

.profile-details {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.detail-row {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  padding: 12px;
  background: #fafffe;
  border-radius: 12px;
  border-left: 3px solid #018404;
  transition: all 0.2s ease;
}

.detail-row:hover {
  background: #f5fdf5;
  transform: translateX(4px);
}

.detail-icon {
  font-size: 18px;
  width: 24px;
  text-align: center;
  flex-shrink: 0;
}

.detail-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.detail-label {
  font-size: 12px;
  color: #666;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.detail-value {
  font-size: 14px;
  color: #333;
  font-weight: 600;
  line-height: 1.3;
}

/* Suppression des anciens styles de user-info-card */
.user-info-card {
  display: none;
}

.back-button {
  background: transparent;
  color: #666;
  border: 2px solid #e5e5e5;
  padding: 16px;
  border-radius: 12px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  margin-top: 12px;
  width: 100%;
}

.back-button:hover {
  border-color: #018404;
  color: #018404;
}

/* Responsive pour la nouvelle carte */
@media (max-width: 480px) {
  .user-profile-card {
    padding: 20px;
  }
  
  .profile-header {
    flex-direction: column;
    text-align: center;
    gap: 12px;
  }
  
  .profile-avatar img,
  .avatar-placeholder {
    width: 60px;
    height: 60px;
  }
  
  .avatar-placeholder {
    font-size: 20px;
  }
  
  .profile-name {
    font-size: 18px;
  }
  
  .detail-row {
    padding: 10px;
  }
  
  .detail-icon {
    font-size: 16px;
  }
}

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

.login-container {
  min-height: 100vh;
  background: linear-gradient(135deg, #e8f5e8 0%, #f0f8f0 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.login-card {
  background: white;
  border-radius: 24px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  display: grid;
  grid-template-columns: 1fr 1fr;
  max-width: 1000px;
  width: 100%;
  min-height: 600px;
}

.form-section {
  padding: 40px;
  display: flex;
  flex-direction: column;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 40px;
}

.logo {
  font-size: 20px;
  font-weight: 600;
}

.logo-text {
  color: #333;
}

.logo-accent {
  color: #018404;
}

.language {
  font-size: 14px;
  color: #666;
}

.form-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.title {
  font-size: 32px;
  font-weight: 700;
  color: #333;
  margin-bottom: 16px;
}

.subtitle {
  color: #666;
  line-height: 1.5;
  margin-bottom: 32px;
  font-size: 14px;
}

.login-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.input-group {
  position: relative;
}

.input-field {
  width: 100%;
  padding: 16px;
  border: 2px solid #e5e5e5;
  border-radius: 12px;
  font-size: 16px;
  transition: all 0.3s ease;
  background: #fafafa;
}

.input-field.error {
  border-color: #dc3545;
  background: #fff5f5;
}

.input-field:focus {
  outline: none;
  border-color: #018404;
  background: white;
}

.login-button {
  background: #333;
  color: white;
  border: none;
  padding: 16px;
  border-radius: 12px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  margin-top: 8px;
}

.login-button:disabled {
  background: #ccc;
  cursor: not-allowed;
  transform: none;
}

.login-button:disabled:hover {
  background: #ccc;
  transform: none;
}

.login-button:hover {
  background: #018404;
  transform: translateY(-2px);
}

.contact-text {
  margin-top: 24px;
  font-size: 14px;
  color: #666;
  text-align: center;
}

.contact-link {
  color: #f5841f;
  text-decoration: none;
}

.contact-link:hover {
  text-decoration: underline;
}

.visual-section {
  background: linear-gradient(135deg, #f8f8f8 0%, #e8e8e8 100%);
  position: relative;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  padding: 40px;
}

.plant-container {
  display: flex;
  gap: 20px;
  margin-bottom: 40px;
}

.plant-pot {
  position: relative;
}

.pot {
  width: 80px;
  height: 60px;
  background: linear-gradient(145deg, #d4a574, #c49660);
  border-radius: 8px 8px 40px 40px;
  position: relative;
}

.pot::before {
  content: '';
  position: absolute;
  top: -4px;
  left: -4px;
  right: -4px;
  height: 8px;
  background: linear-gradient(145deg, #e6b885, #d4a574);
  border-radius: 4px;
}

.plant {
  position: absolute;
  top: -20px;
  left: 50%;
  transform: translateX(-50%);
}

.leaf {
  position: absolute;
  background: #018404;
  border-radius: 50% 10px;
  transform-origin: bottom center;
}

.pot-1 .leaf-1 {
  width: 25px;
  height: 40px;
  transform: rotate(-30deg) translateX(-10px);
}

.pot-1 .leaf-2 {
  width: 30px;
  height: 45px;
  transform: rotate(10deg) translateX(5px);
}

.pot-1 .leaf-3 {
  width: 20px;
  height: 35px;
  transform: rotate(45deg) translateX(15px);
}

.pot-1 .leaf-4 {
  width: 28px;
  height: 42px;
  transform: rotate(-60deg) translateX(-15px);
}

.pot-2 .leaf-1 {
  width: 22px;
  height: 38px;
  transform: rotate(-20deg) translateX(-8px);
}

.pot-2 .leaf-2 {
  width: 26px;
  height: 40px;
  transform: rotate(20deg) translateX(8px);
}

.pot-2 .leaf-3 {
  width: 24px;
  height: 36px;
  transform: rotate(-50deg) translateX(-12px);
}

.info-card {
  background: rgba(255, 255, 255, 0.9);
  padding: 24px;
  border-radius: 16px;
  max-width: 280px;
  text-align: left;
  backdrop-filter: blur(10px);
}

.play-button {
  width: 40px;
  height: 40px;
  background: #018404;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 14px;
  margin-bottom: 16px;
}

.info-card h3 {
  font-size: 16px;
  font-weight: 600;
  color: #333;
  margin-bottom: 12px;
  line-height: 1.4;
}

.info-card p {
  font-size: 13px;
  color: #666;
  line-height: 1.5;
}

.error-message {
  color: #dc3545;
  font-size: 12px;
  margin-top: 4px;
  display: block;
}

/* Responsive Design */
@media (max-width: 768px) {
  .login-card {
    grid-template-columns: 1fr;
    max-width: 400px;
    min-height: auto;
  }
  
  .form-section {
    padding: 30px 24px;
  }
  
  .visual-section {
    padding: 30px 24px;
    min-height: 300px;
  }
  
  .title {
    font-size: 28px;
  }
  
  .plant-container {
    transform: scale(0.8);
    margin-bottom: 20px;
  }
  
  .info-card {
    max-width: 100%;
  }
}

@media (max-width: 480px) {
  .login-container {
    padding: 10px;
  }
  
  .form-section {
    padding: 24px 20px;
  }
  
  .visual-section {
    padding: 24px 20px;
  }
  
  .title {
    font-size: 24px;
  }
  
  .input-field {
    padding: 14px;
    font-size: 16px;
  }
  
  .login-button {
    padding: 14px;
  }
}
</style>
