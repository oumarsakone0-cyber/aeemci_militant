/**
 * Utilitaires pour la gestion de l'authentification
 */

// Configuration de l'API
export const API_CONFIG = {
  BASE_URL: 'https://sogetrag.com/apistage/post_api.php'
}

// Clés pour le stockage local
const STORAGE_KEYS = {
  USER_MATRICULE: 'user_matricule',
  USER_DATA: 'user_data',
  SESSION_TOKEN: 'session_token'
}

/**
 * Récupérer le matricule de l'utilisateur connecté
 */
export const getCurrentUserMatricule = () => {
  return sessionStorage.getItem(STORAGE_KEYS.USER_MATRICULE) || 
         localStorage.getItem(STORAGE_KEYS.USER_MATRICULE)
}

/**
 * Définir l'utilisateur connecté
 */
export const setCurrentUser = (matricule, userData = null, rememberMe = false) => {
  const storage = rememberMe ? localStorage : sessionStorage
  
  storage.setItem(STORAGE_KEYS.USER_MATRICULE, matricule)
  
  if (userData) {
    storage.setItem(STORAGE_KEYS.USER_DATA, JSON.stringify(userData))
  }
}

/**
 * Récupérer les données utilisateur stockées
 */
export const getCachedUserData = () => {
  const data = sessionStorage.getItem(STORAGE_KEYS.USER_DATA) || 
               localStorage.getItem(STORAGE_KEYS.USER_DATA)
  
  return data ? JSON.parse(data) : null
}

/**
 * Vérifier si un utilisateur est connecté
 */
export const isUserLoggedIn = () => {
  return !!getCurrentUserMatricule()
}

/**
 * Déconnecter l'utilisateur
 */
export const logout = () => {
  // Nettoyer sessionStorage
  sessionStorage.removeItem(STORAGE_KEYS.USER_MATRICULE)
  sessionStorage.removeItem(STORAGE_KEYS.USER_DATA)
  sessionStorage.removeItem(STORAGE_KEYS.SESSION_TOKEN)
  
  // Nettoyer localStorage
  localStorage.removeItem(STORAGE_KEYS.USER_MATRICULE)
  localStorage.removeItem(STORAGE_KEYS.USER_DATA)
  localStorage.removeItem(STORAGE_KEYS.SESSION_TOKEN)
}

/**
 * Rediriger vers la page de connexion si non connecté
 */
export const requireAuth = (redirectUrl = '/login') => {
  if (!isUserLoggedIn()) {
    window.location.href = redirectUrl
    return false
  }
  return true
}

/**
 * Simuler une connexion (pour les tests)
 * À remplacer par la vraie logique de connexion
 */
export const simulateLogin = (matricule) => {
  // Pour les tests, on peut simuler une connexion
  setCurrentUser(matricule, null, true)
  console.log(`Utilisateur ${matricule} connecté (simulation)`)
}

// Export par défaut
export default {
  getCurrentUserMatricule,
  setCurrentUser,
  getCachedUserData,
  isUserLoggedIn,
  logout,
  requireAuth,
  simulateLogin
}
