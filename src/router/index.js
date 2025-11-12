import { createRouter, createWebHistory } from "vue-router"
import { useUserStore } from "../stores/user"

// Pages
import Home from "../components/views/HelloWorld.vue"
import Posts from "../components/pages/Posts.vue"
import Login from "../components/login/Login.vue"
import Inscription from "../components/login/InscriptionPage.vue"
import Bibliotheque from "../components/pages/Bibliotheque.vue"
import Livres from "../components/pages/Livres.vue"
import Activites from "../components/pages/Activites.vue"
import Idee from "../components/pages/Idee.vue"
import Secretariat from "../components/pages/Secretariat.vue"
import MesSousComites from "../components/pages/MesSousComites.vue"
import Dashboard from "../components/pages/Dashboard.vue"
import Social from "../components/pages/Social.vue"
import Coins from "../components/pages/Coins.vue"
import Messages from "../components/pages/Messages.vue"
import MesLivres from "../components/pages/MesLivres.vue"
import Avis from "../components/pages/Avis.vue"
import MesSr from "../components/pages/MesSr.vue"
import BureauxSr from "../components/pages/BureauxSr.vue"
import BureauxSrMembres from "../components/pages/BureauxSrMembres.vue"
import Cima from "../components/pages/Cima.vue"
import Quiz from "../components/pages/Quiz.vue"
import Meet from "../components/pages/Meet.vue"
import PageProfile from "../components/pages/PageProfile.vue"
import DocPage from "../components/pages/DocPage.vue"

const routes = [
  { path: "/", name: "dashboard", component: Dashboard },
  { path: "/posts", name: "posts", component: Posts },
  { path: "/livres", name: "livres", component: Livres },
  { path: "/idees", name: "idees", component: Idee },
  { path: "/secretariat", name: "secretariat", component: Secretariat },
  { path: "/activite", name: "activite", component: Messages },
  { path: "/quiz", name: "quiz", component: Quiz },
  { path: "/login", name: "login", component: Login },
  { path: "/inscription", name: "inscription", component: Inscription },
  { path: "/mes_sous_comites", name: "mes_sous_comites", component: MesSousComites },
  { path: "/bibliotheque", name: "bibliotheque", component: Bibliotheque },
  { path: "/documents", name: "documents", component: DocPage },

  { path: "/social", name: "social", component: Social },
  { path: "/coins", name: "coins", component: Coins },
  { path: "/messages", name: "messages", component: Messages },
  { path: "/mes_livres", name: "mes_livres", component: MesLivres },
  { path: "/avis", name: "avis", component: Avis },
  { path: "/mes_sr", name: "mes_sr", component: MesSr },
  { path: "/bureaux_sr", name: "bureaux_sr", component: BureauxSr },
  { path: "/BureauxSr", redirect: "/bureaux_sr" }, // Alias pour compatibilité
  { path: "/bureaux_sr/:id/membres", name: "bureaux_sr_membres", component: BureauxSrMembres },
  { path: "/cima", name: "cima", component: Cima },
  { path: "/meet", name: "meet", component: Meet },
  { path:"/profile",name:"profile",component:PageProfile}
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

// 🔒 Guard global
router.beforeEach((to, from, next) => {
  const userStore = useUserStore()

  const publicPages = ["login", "inscription"]

  const isPublic = publicPages.includes(to.name)

  // Si pas connecté et route protégée → redirection login
  if (!userStore.isLoggedIn && !isPublic) {
    return next({ name: "login" })
  }

  // This allows logged-in users to access inscription and login pages if needed
  // If you want to prevent this, you can add specific logic here

  next()
})

export default router
