import { defineStore } from 'pinia'
import router from '../router/index'

export const useUserStore = defineStore('user', {
  state: () => ({
    user: null,
    isLoggedIn: false
  }),
  persist: true, // Persist dans localStorage
  actions: {
    login(userData) {
      this.user = userData
      this.isLoggedIn = true
      router.push('/') // ou '/dashboard' selon ton besoin
    },
    logout() {
      this.user = null
      this.isLoggedIn = false
      router.push('/login')
    }
  }
})
