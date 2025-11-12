<template>
  <div class="min-h-screen bg-gradient-to-br from-emerald-50 via-teal-50 to-green-50 relative overflow-hidden pb-20 md:pb-8">
    <!-- Decorative background elements -->
    <div class="absolute top-0 left-0 w-96 h-96 bg-emerald-400/20 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2"></div>
    <div class="absolute bottom-0 right-0 w-96 h-96 bg-teal-400/20 rounded-full blur-3xl translate-x-1/2 translate-y-1/2"></div>
    <div class="absolute top-1/2 left-1/2 w-96 h-96 bg-green-400/10 rounded-full blur-3xl"></div>

    <!-- Header with glassmorphism -->
    <header class="relative backdrop-blur-sm bg-white/80 shadow-lg border-b border-emerald-200/50 sticky top-0 z-40">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
          <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-500/30 transform hover:scale-105 transition-transform">
              <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
              </svg>
            </div>
            <div>
              <h1 class="text-xl md:text-2xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
                Formations Zoom
              </h1>
              <p class="text-xs md:text-sm text-gray-600">Rejoignez nos salons de formation</p>
            </div>
          </div>
          <div class="flex items-center gap-3">
            <button
            v-if="userStore.user.je_suis === 'CE_CE'"
              @click="showAddFormationModal = true"
              class="px-3 md:px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl hover:shadow-lg hover:shadow-emerald-500/50 transition-all duration-300 flex items-center space-x-2 text-sm md:text-base active:scale-95"
            >
              <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
              </svg>
              <span class="hidden sm:inline">Créer</span>
            </button>
            <div class="hidden md:block text-right">
              <p class="text-sm font-semibold text-emerald-600">{{ formations.length }}</p>
              <p class="text-xs text-gray-500">formations</p>
            </div>
          </div>
        </div>
      </div>
    </header>

    <main class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-8">
      <!-- Search and filters -->
      <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl border border-emerald-100/50 p-4 md:p-6 mb-6 md:mb-8 hover:shadow-2xl transition-shadow duration-300">
        <div class="flex flex-col lg:flex-row gap-3 md:gap-4">
          <div class="flex-1">
            <div class="relative group">
              <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-emerald-500 group-focus-within:text-emerald-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
              </svg>
              <input
                v-model="searchQuery"
                @input="searchFormations"
                type="text"
                placeholder="Rechercher une formation..."
                class="w-full pl-10 pr-4 py-3 border-2 border-emerald-100 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300 bg-white/50 backdrop-blur-sm"
              />
            </div>
          </div>
          
          <div class="lg:w-64">
            <select
              v-model="selectedStatus"
              @change="filterFormations"
              class="w-full px-4 py-3 border-2 border-emerald-100 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300 bg-white/50 backdrop-blur-sm"
            >
              <option value="">Tous les statuts</option>
              <option value="upcoming">À venir</option>
              <option value="ongoing">En cours</option>
              <option value="completed">Terminée</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Formations grid - Desktop -->
      <div class="hidden md:grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-6">
        <div
          v-for="formation in filteredFormations"
          :key="formation.id"
          @click="handleFormationClick(formation)"
          class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-lg border border-emerald-100/50 overflow-hidden hover:shadow-2xl hover:border-emerald-300 hover:-translate-y-2 transition-all duration-300 cursor-pointer group"
        >
          <div class="aspect-video bg-gradient-to-br from-emerald-100 to-teal-100 relative overflow-hidden flex items-center justify-center">
            <svg class="w-16 h-16 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
            </svg>
            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="absolute top-3 right-3">
              <span
                :class="[
                  'px-3 py-1 rounded-full text-xs font-semibold backdrop-blur-sm shadow-lg',
                  formation.status === 'ongoing' 
                    ? 'bg-green-500/90 text-white' 
                    : formation.status === 'upcoming'
                    ? 'bg-blue-500/90 text-white'
                    : 'bg-gray-500/90 text-white'
                ]"
              >
                {{ formation.status === 'ongoing' ? '🔴 En cours' : formation.status === 'upcoming' ? '📅 À venir' : '✓ Terminée' }}
              </span>
            </div>
          </div>
          
          <div class="p-4">
            <h3 class="font-bold text-gray-900 mb-2 line-clamp-2 group-hover:text-emerald-600 transition-colors text-sm">
              {{ formation.titre }}
            </h3>
            <p class="text-xs text-gray-600 mb-3">👨‍🏫 {{ formation.formateur }}</p>
            <div class="space-y-2 mb-3">
              <p class="text-xs text-gray-500">📅 {{ formatDate(formation.date_debut) }}</p>
              <p class="text-xs text-gray-500">⏰ {{ formation.heure_debut }} · {{ formation.duration }} min</p>
              <p class="text-xs text-emerald-600 font-semibold">👥 {{ formation.participants }}/{{ formation.capacite_max }} inscrits</p>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-xs px-3 py-1 bg-gradient-to-r from-emerald-50 to-teal-50 text-emerald-700 rounded-full font-medium border border-emerald-200">
                {{ formation.status === 'ongoing' ? 'En direct' : 'Formation' }}
              </span>
              <svg class="w-5 h-5 text-gray-400 group-hover:text-emerald-500 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Formations list - Mobile -->
      <div class="md:hidden space-y-3">
        <div
          v-for="formation in filteredFormations"
          :key="formation.id"
          @click="handleFormationClick(formation)"
          class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-lg border border-emerald-100/50 overflow-hidden hover:shadow-xl hover:border-emerald-300 transition-all duration-300 cursor-pointer active:scale-98"
        >
          <div class="flex p-3">
            <div class="w-20 h-20 bg-gradient-to-br from-emerald-100 to-teal-100 rounded-xl overflow-hidden flex-shrink-0 shadow-md flex items-center justify-center">
              <svg class="w-10 h-10 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
              </svg>
            </div>
            <div class="ml-3 flex-1 min-w-0" style="text-align: left;">
              <div class="flex items-start justify-between gap-2">
                <div class="flex-1 min-w-0">
                  <h3 class="font-bold text-gray-900 mb-1 line-clamp-2 text-sm">
                    {{ formation.titre }}
                  </h3>
                  <p class="text-xs text-gray-600 mb-2">👨‍🏫 {{ formation.formateur }}</p>
                  <div class="space-y-1">
                    <p class="text-xs text-gray-500">📅 {{ formatDate(formation.date_formation) }} à {{ formation.heure_debut }}</p>
                    <p class="text-xs text-emerald-600 font-semibold">👥 {{ formation.participants }}/{{ formation.capacite_max }}</p>
                  </div>
                </div>
                <div class="flex flex-col items-end gap-2">
                  <span
                    :class="[
                      'px-2 py-1 rounded-full text-xs font-semibold whitespace-nowrap',
                      formation.status === 'ongoing' 
                        ? 'bg-green-500 text-white' 
                        : formation.status === 'upcoming'
                        ? 'bg-blue-500 text-white'
                        : 'bg-gray-500 text-white'
                    ]"
                  >
                    {{ formation.status === 'ongoing' ? '🔴' : formation.status === 'upcoming' ? '📅' : '✓' }}
                  </span>
                  <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                  </svg>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty state -->
      <div v-if="filteredFormations.length === 0" class="text-center py-16">
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-emerald-100/50 p-8 max-w-md mx-auto">
          <svg class="mx-auto w-20 h-20 text-emerald-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
          </svg>
          <h3 class="text-lg font-bold text-gray-900 mb-2">Aucune formation trouvée</h3>
          <p class="text-gray-600 text-sm">Essayez de modifier vos critères de recherche.</p>
        </div>
      </div>
    </main>

    <!-- Formation detail modal -->
    <div
      v-if="showModal"
      style="z-index: 1111"
      class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 z-50 animate-fade-in"
      @click="closeModal"
    >
      <div
        class="bg-white/95 backdrop-blur-md rounded-3xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto border border-emerald-200/50 animate-scale-in"
        @click.stop
      >
        <div class="p-6">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
              Détails de la formation
            </h2>
            <button
              @click="closeModal"
              class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full p-2 transition-all"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>

          <div v-if="selectedFormation" class="space-y-6">
            <!-- Header with thumbnail -->
            <div class="bg-gradient-to-r from-emerald-50 to-teal-50 rounded-2xl p-6 border border-emerald-200">
              <div class="flex items-center gap-4 mb-4">
                <div class="w-16 h-16 bg-emerald-500 rounded-2xl flex items-center justify-center shadow-lg">
                  <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                  </svg>
                </div>
                <div class="flex-1">
                  <h3 class="text-xl font-bold text-gray-900 mb-1">{{ selectedFormation.titre }}</h3>
                  <p class="text-sm text-gray-600">👨‍🏫 {{ selectedFormation.formateur }}</p>
                </div>
              </div>
              <p class="text-gray-700 text-sm">{{ selectedFormation.description || 'Formation Zoom professionnelle' }}</p>
            </div>

            <!-- Formation details grid -->
            <div class="grid grid-cols-2 gap-4">
              <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                <p class="text-xs text-gray-500 font-semibold mb-1">DATE</p>
                <p class="font-bold text-gray-900">{{ formatDate(selectedFormation.date_formation) }}</p>
              </div>
              <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                <p class="text-xs text-gray-500 font-semibold mb-1">HEURE</p>
                <p class="font-bold text-gray-900">{{ selectedFormation.heure_debut }}</p>
              </div>
              <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                <p class="text-xs text-gray-500 font-semibold mb-1">DURÉE</p>
                <p class="font-bold text-gray-900">{{ selectedFormation.duration }} minutes</p>
              </div>
              <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                <p class="text-xs text-gray-500 font-semibold mb-1">PARTICIPANTS</p>
                <p class="font-bold text-gray-900">{{ selectedFormation.participants }}/{{ selectedFormation.capacite_max }}</p>
              </div>
            </div>

            <!-- Zoom details -->
            <div v-if="selectedFormation.zoom_meeting_id" class="bg-blue-50 rounded-2xl p-6 border border-blue-200">
              <h4 class="font-bold text-gray-900 mb-4">Informations Zoom</h4>
              <div class="space-y-3">
                <div>
                  <p class="text-xs text-gray-600 font-semibold mb-1">ID de réunion</p>
                  <p class="font-mono text-sm text-blue-600 bg-white rounded p-2 border border-blue-200">{{ selectedFormation.zoom_meeting_id }}</p>
                </div>
                <div>
                  <p class="text-xs text-gray-600 font-semibold mb-1">Code d'accès</p>
                  <p class="font-mono text-sm text-blue-600 bg-white rounded p-2 border border-blue-200">{{ selectedFormation.zoom_password }}</p>
                </div>
              </div>
            </div>

            <!-- Action buttons -->
            <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-gray-200">
              <button
                @click="closeModal"
                class="flex-1 px-4 py-3 border-2 border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition-all font-semibold"
              >
                Fermer
              </button>
              <button
                @click="registerFormation"
                :disabled="isRegistering"
                v-if="selectedFormation.status !== 'completed'"
                class="flex-1 px-4 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl hover:shadow-lg hover:shadow-emerald-500/50 transition-all disabled:opacity-50 font-semibold"
              >
                {{ isRegistering ? 'Inscription...' : "S'inscrire" }}
              </button>
              <button
                @click="joinFormation"
                v-if="selectedFormation.status === 'ongoing' && selectedFormation.zoom_join_url"
                class="flex-1 px-4 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl hover:shadow-lg hover:shadow-green-500/50 transition-all font-semibold"
              >
                🔴 Rejoindre en direct
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Create formation modal -->
    <div
      v-if="showAddFormationModal"
      style="z-index: 1111"
      class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 z-50 animate-fade-in"
      @click="closeAddFormationModal"
    >
      <div
        class="bg-white/95 backdrop-blur-md rounded-3xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto border border-emerald-200/50 animate-scale-in"
        @click.stop
      >
        <div class="p-6">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
              Créer une formation
            </h2>
            <button
              @click="closeAddFormationModal"
              class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full p-2 transition-all"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>

          <form @submit.prevent="submitAddFormation" class="space-y-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Matricule</label>
              <input
                v-model="addFormationForm.matricule"
                type="text"
                readonly
                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl bg-gray-50"
              />
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Titre de la formation</label>
              <input
                v-model="addFormationForm.titre"
                type="text"
                required
                placeholder="Ex: Vue.js Avancé"
                class="w-full px-4 py-3 border-2 border-emerald-100 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
              />
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
              <textarea
                v-model="addFormationForm.description"
                placeholder="Décrivez le contenu de la formation..."
                rows="3"
                class="w-full px-4 py-3 border-2 border-emerald-100 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
              ></textarea>
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Formateur</label>
              <input
                v-model="addFormationForm.formateur"
                type="text"
                required
                class="w-full px-4 py-3 border-2 border-emerald-100 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
              />
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Date</label>
              <input
                v-model="addFormationForm.date_formation"
                type="date"
                required
                class="w-full px-4 py-3 border-2 border-emerald-100 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
              />
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Heure</label>
                <input
                  v-model="addFormationForm.heure_debut"
                  type="time"
                  required
                  class="w-full px-4 py-3 border-2 border-emerald-100 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                />
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Durée (min)</label>
                <input
                  v-model.number="addFormationForm.duration"
                  type="number"
                  min="15"
                  required
                  class="w-full px-4 py-3 border-2 border-emerald-100 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                />
              </div>
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Capacité max (participants)</label>
              <input
                v-model.number="addFormationForm.capacite_max"
                type="number"
                min="1"
                required
                class="w-full px-4 py-3 border-2 border-emerald-100 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
              />
            </div>

            <div class="flex space-x-3 pt-4">
              <button
                type="button"
                @click="closeAddFormationModal"
                class="flex-1 px-4 py-3 border-2 border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition-all font-semibold"
              >
                Annuler
              </button>
              <button
                type="submit"
                :disabled="isAddingFormation"
                class="flex-1 px-4 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl hover:shadow-lg hover:shadow-emerald-500/50 transition-all disabled:opacity-50 font-semibold"
              >
                {{ isAddingFormation ? 'Création...' : 'Créer' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useUserStore } from "../../stores/user";

const userStore = useUserStore()

const API_BASE_URL = 'http://sogetrag.com/api/formations-zoom.php'

const searchQuery = ref('')
const selectedStatus = ref('')
const showModal = ref(false)
const showAddFormationModal = ref(false)
const selectedFormation = ref(null)
const isRegistering = ref(false)
const isAddingFormation = ref(false)
const formations = ref([])

const addFormationForm = ref({
  matricule: '',
  titre: '',
  description: '',
  formateur: '',
  date_debut: '',
  heure_debut: '',
  duration: 60,
  capacite_max: 50
})

const filteredFormations = computed(() => {
  let result = formations.value

  if (searchQuery.value) {
    result = result.filter(f => 
      f.titre.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      f.formateur.toLowerCase().includes(searchQuery.value.toLowerCase())
    )
  }

  if (selectedStatus.value) {
    result = result.filter(f => f.status === selectedStatus.value)
  }

  return result
})

const formatDate = (dateString) => {
  const options = { day: '2-digit', month: '2-digit', year: 'numeric' }
  return new Date(dateString).toLocaleDateString('fr-FR', options)
}

const handleFormationClick = (formation) => {
  selectedFormation.value = formation
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  selectedFormation.value = null
}

const closeAddFormationModal = () => {
  showAddFormationModal.value = false
  resetAddFormationForm()
}

const resetAddFormationForm = () => {
  addFormationForm.value = {
    matricule: userStore.user?.matricule_gen || 'N/A',
    titre: '',
    description: '',
    formateur: userStore.user?.nom || 'Formateur',
    date_debut: '',
    heure_debut: '',
    duration: 60,
    capacite_max: 50
  }
}

const joinFormation = () => {
  if (selectedFormation.value?.zoom_join_url) {
    window.open(selectedFormation.value.zoom_join_url, '_blank')
  }
}

const registerFormation = async () => {
  isRegistering.value = true
  try {
    const response = await fetch(`${API_BASE_URL}?action=register_participant`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        formation_id: selectedFormation.value.id,
        user_matricule: userStore.user?.matricule_gen,
        user_nom: userStore.user?.nom,
        user_email: userStore.user?.email,
        user_contact: userStore.user?.contact
      })
    })
    
    const result = await response.json()
    
    if (result.success) {
      alert('Inscription confirmée!')
      closeModal()
      await loadFormations()
    } else {
      alert('Erreur: ' + result.error)
    }
  } catch (error) {
    console.error('Error registering:', error)
    alert('Erreur lors de l\'inscription')
  } finally {
    isRegistering.value = false
  }
}

const submitAddFormation = async () => {
  isAddingFormation.value = true
  try {
    const response = await fetch(`${API_BASE_URL}?action=create_formation`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        titre: addFormationForm.value.titre,
        description: addFormationForm.value.description,
        formateur_nom: addFormationForm.value.formateur,
        date_formation: addFormationForm.value.date_debut,
        heure_debut: addFormationForm.value.heure_debut,
        duree_minutes: addFormationForm.value.duration,
        capacite_max: addFormationForm.value.capacite_max,
        statut: 'planifiee',
        created_by_matricule: addFormationForm.value.matricule
      })
    })
    
    const result = await response.json()
    
    if (result.success) {
      alert('Formation créée avec succès!')
      closeAddFormationModal()
      await loadFormations()
    } else {
      alert('Erreur: ' + result.error)
    }
  } catch (error) {
    console.error('Error creating formation:', error)
    alert('Erreur lors de la création')
  } finally {
    isAddingFormation.value = false
  }
}

const searchFormations = async () => {
  if (!searchQuery.value.trim()) {
    await loadFormations()
    return
  }
  
  try {
    const response = await fetch(`${API_BASE_URL}?action=search_formations&q=${encodeURIComponent(searchQuery.value)}`)
    const result = await response.json()
    if (result.success) {
      formations.value = result.data.map(f => ({
        id: f.id,
        titre: f.titre,
        formateur: f.formateur,
        date_debut: f.date_debut,
        heure_debut: f.heure_debut,
        duration: f.duration,
        participants: f.participants,
        capacite_max: f.capacite_max,
        status: f.status,
        zoom_meeting_id: f.zoom_meeting_id,
        zoom_password: f.zoom_password,
        zoom_join_url: f.zoom_join_url
      }))
    }
  } catch (error) {
    console.error('Error searching formations:', error)
  }
}

const loadFormations = async () => {
  try {
    // Génère un nombre aléatoire pour éviter la mise en cache
    const randomParam = Math.random().toString(36).substring(2)

    const response = await fetch(
      `${API_BASE_URL}?action=formations&limit=50&_rand=${randomParam}`
    )

    const result = await response.json()

    if (result.success) {
      formations.value = result.data.map(f => ({
        id: f.id,
        titre: f.titre,
        formateur: f.formateur_nom, // attention, ton champ est formateur_nom dans la table
        date_formation: f.date_formation,
        heure_debut: f.heure_debut,
        duree_minutes: f.duree_minutes,
        participants: f.nombre_inscrits,
        capacite_max: f.capacite_max,
        statut: f.statut,
        zoom_meeting_id: f.zoom_meeting_id,
        zoom_password: f.zoom_password,
        zoom_join_url: f.zoom_join_url
      }))
    } else {
      console.error('Erreur API:', result.error || 'Réponse invalide')
    }
  } catch (error) {
    console.error('Error loading formations:', error)
  }
}


onMounted(() => {
  addFormationForm.value.matricule = userStore.user?.matricule_gen || 'N/A'
  addFormationForm.value.formateur = userStore.user?.nom || 'Formateur'
  loadFormations()
})
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.max-h-\[90vh\]::-webkit-scrollbar {
  width: 8px;
}

.max-h-\[90vh\]::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 4px;
}

.max-h-\[90vh\]::-webkit-scrollbar-thumb {
  background: linear-gradient(to bottom, #10b981, #14b8a6);
  border-radius: 4px;
}

.max-h-\[90vh\]::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(to bottom, #059669, #0d9488);
}

@keyframes fade-in {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes scale-in {
  from {
    opacity: 0;
    transform: scale(0.95);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

.animate-fade-in {
  animation: fade-in 0.2s ease-out;
}

.animate-scale-in {
  animation: scale-in 0.3s ease-out;
}

.active\:scale-98:active {
  transform: scale(0.98);
}
</style>
