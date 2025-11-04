<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import HeaderAndHero from './components/pages/HeaderAndHero.vue'

const route = useRoute()

// On masque le Hero si on est sur login ou inscription
const showHero = computed(() => {
  return !(route.name === 'login' || route.name === 'inscription')
})
</script>

<template>
  <div id="app">
    <!-- Hero affiché seulement si la route n'est pas login/inscription -->
    <HeaderAndHero v-if="showHero" />

    <!-- Page courante -->
    <router-view />

    <!-- Footer -->
  </div>
</template>

<style scoped>
.logo {
  height: 6em;
  padding: 1.5em;
  will-change: filter;
  transition: filter 300ms;
}
.logo:hover {
  filter: drop-shadow(0 0 2em #646cffaa);
}
.logo.vue:hover {
  filter: drop-shadow(0 0 2em #42b883aa);
}
</style>
