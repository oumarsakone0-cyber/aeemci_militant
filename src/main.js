import { createApp } from 'vue'
import { createPinia } from 'pinia'   // ⬅️ il manquait ça
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate'
import router from './router/index'
import './style.css'
import './assets/style.css'


import App from './App.vue'

const pinia = createPinia()
pinia.use(piniaPluginPersistedstate)

createApp(App)
  .use(pinia)
  .use(router)
  .mount('#app')
