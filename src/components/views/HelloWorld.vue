<template>
  <div class="golf-dashboard">
    <!-- Dividing the app into two separate components -->
    <MainContent />
  </div>
</template>

<script setup>
import MainContent from '../pages/MainContent.vue'
</script>

<style scoped>
.golf-dashboard {
  min-height: 100vh;
  background: linear-gradient(135deg, #0184040a 0%, #0184040a 100%);
}
</style>
