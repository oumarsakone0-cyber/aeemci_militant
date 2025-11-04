<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 pb-20">
    <div class="max-w-7xl mx-auto px-4 py-6 md:px-6 md:py-8">
      <!-- Header -->
      <div class="mb-6 md:mb-8">
        <div class="flex items-center gap-3 mb-2">
          <button @click="goBack" class="p-2 hover:bg-white/50 rounded-xl transition-colors">
            <svg class="w-6 h-6 text-slate-700" fill="currentColor" viewBox="0 0 24 24">
              <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
            </svg>
          </button>
          <h1 class="text-3xl md:text-4xl font-bold text-slate-800">Quiz Aeemciste</h1>
        </div>
        <p class="text-slate-600 ml-14">Répondez aux questions et gagnez des récompenses !</p>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="flex justify-center items-center py-20">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600"></div>
      </div>

      <!-- Completed Questions with Winners -->
      <div v-if="completedQuestionsWithWinners.length > 0" class="space-y-6 mb-6">
        <div 
          v-for="item in completedQuestionsWithWinners" 
          :key="item.question.id"
          class="bg-gradient-to-br from-emerald-50 via-green-50 to-teal-50 rounded-3xl p-6 md:p-8 shadow-xl border-2 border-emerald-200/50 backdrop-blur-sm overflow-hidden relative"
        >
          <!-- ... existing code ... -->
          
          <div class="relative z-10">
            <!-- Celebration Header -->
            <div class="text-center mb-6">
              <div class="inline-flex items-center justify-center gap-2 mb-3">
                <svg class="w-8 h-8 text-yellow-500 animate-bounce" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                </svg>
                <h3 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-emerald-600 to-green-600 bg-clip-text text-transparent">
                  Félicitations au Gagnant !
                </h3>
                <svg class="w-8 h-8 text-yellow-500 animate-bounce" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                </svg>
              </div>
              <p class="text-emerald-700 font-medium">Question terminée</p>
            </div>

            <!-- Question -->
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-4 mb-6 border border-emerald-200/50">
              <p class="text-sm text-emerald-600 font-semibold mb-1">Question</p>
              <p class="text-slate-800 font-medium">{{ item.question.question }}</p>
            </div>

            <!--  Added correct answer display for winners -->
            <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl p-6 mb-6 border-2 border-blue-200/50">
              <div class="flex items-start gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl flex items-center justify-center flex-shrink-0">
                  <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                  </svg>
                </div>
                <div class="flex-1">
                  <p class="text-sm text-blue-700 font-semibold mb-1">Bonne Réponse</p>
                  <p class="text-lg font-bold text-blue-900">{{ item.question.reponse }}</p>
                </div>
              </div>
            </div>

            <!-- Winner Card -->
            <div v-if="item.winner" class="bg-white rounded-2xl p-6 shadow-lg border border-emerald-200/50">
              <!-- ... existing code ... -->
              <div class="flex flex-col md:flex-row items-center gap-6">
                <!-- Profile Picture -->
                <div class="relative">
                  <div class="w-24 h-24 md:w-28 md:h-28 rounded-full overflow-hidden ring-4 ring-emerald-400 shadow-xl">
                    <img 
                      v-if="item.winner.photo_membre" 
                      :src="item.winner.photo_membre" 
                      class="w-full h-full object-cover"
                    />
                    <div v-else class="w-full h-full bg-gradient-to-br from-emerald-500 to-green-600 flex items-center justify-center">
                      <span class="text-3xl font-bold text-white">{{ getInitials(item.winner.nom) }}</span>
                    </div>
                  </div>
                  <!-- Trophy Badge -->
                  <div class="absolute -bottom-2 -right-2 w-12 h-12 bg-gradient-to-br from-yellow-400 to-amber-500 rounded-full flex items-center justify-center shadow-lg ring-4 ring-white">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M20 7h-5V4c0-1.1-.9-2-2-2h-2c-1.1 0-2 .9-2 2v3H4c-1.1 0-2 .9-2 2v11c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V9c0-1.1-.9-2-2-2zM9 4h6v3H9V4zm11 16H4V9h5c0 1.1.9 2 2 2h2c1.1 0 2-.9 2-2h5v11z"/>
                    </svg>
                  </div>
                </div>

                <!-- Winner Info -->
                <div class="flex-1 text-center md:text-left">
                  <h4 class="text-2xl md:text-3xl font-bold text-slate-800 mb-2">{{ item.winner.nom }} {{ item.winner.prenom }}</h4>
                  <div class="space-y-2">
                    <div class="flex items-center justify-center md:justify-start gap-2">
                      <span class="text-slate-600 font-medium">Matricule: <span class="text-emerald-700 font-bold">{{ item.winner.matricule_gen }}</span></span>
                    </div>
                    <div class="flex items-center justify-center md:justify-start gap-2">
                      <svg class="w-5 h-5 text-emerald-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                      </svg>
                      <span class="text-slate-600 font-medium">{{ item.winner.region || 'Secrétariat Régional' }}</span>
                    </div>
                  </div>
                </div>

                <!-- Prize -->
                <div class="bg-gradient-to-br from-amber-50 to-yellow-50 rounded-2xl p-4 border-2 border-amber-300/50 text-center min-w-[140px]">
                  <p class="text-sm text-amber-700 font-semibold mb-1">Récompense</p>
                  <p class="text-2xl font-bold text-amber-900">{{ item.question.gain }} FCFA</p>
                </div>
              </div>

              <!-- Congratulations Message -->
              <div class="mt-6 pt-6 border-t border-emerald-200">
                <p class="text-center text-emerald-700 font-medium text-lg">
                  🎉 Bravo pour cette excellente réponse ! 🎉
                </p>
              </div>
            </div>

            <!-- Loading Winner Info -->
            <div v-else class="bg-white rounded-2xl p-6 shadow-lg border border-emerald-200/50 text-center">
              <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-emerald-600 mx-auto"></div>
              <p class="text-slate-600 mt-3">Chargement des informations du gagnant...</p>
            </div>
          </div>
        </div>
      </div>

      <!--  Completed questions without winners section -->
      <div v-if="completedQuestionsNoWinner.length > 0" class="space-y-6 mb-6">
        <div 
          v-for="question in completedQuestionsNoWinner" 
          :key="question.id"
          class="bg-gradient-to-br from-slate-50 via-gray-50 to-slate-100 rounded-3xl p-6 md:p-8 shadow-xl border-2 border-slate-200/50 backdrop-blur-sm overflow-hidden relative"
        >
          <!-- Decorative elements -->
          <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-slate-400/10 to-gray-500/10 rounded-full blur-3xl"></div>
          <div class="absolute bottom-0 left-0 w-32 h-32 bg-gradient-to-tr from-gray-400/10 to-slate-500/10 rounded-full blur-3xl"></div>
          
          <div class="relative z-10">
            <!-- Header -->
            <div class="text-center mb-6">
              <div class="inline-flex items-center justify-center gap-2 mb-3">
                <svg class="w-8 h-8 text-slate-500" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
                </svg>
                <h3 class="text-2xl md:text-3xl font-bold text-slate-700">
                  Temps Écoulé
                </h3>
              </div>
              <p class="text-slate-600 font-medium">Aucune réponse reçue</p>
            </div>

            <!-- Question -->
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-4 mb-6 border border-slate-200/50">
              <p class="text-sm text-slate-600 font-semibold mb-1">Question</p>
              <p class="text-slate-800 font-medium">{{ question.question }}</p>
            </div>

            <!-- Correct Answer -->
            <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl p-6 mb-6 border-2 border-blue-200/50">
              <div class="flex items-start gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl flex items-center justify-center flex-shrink-0">
                  <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                  </svg>
                </div>
                <div class="flex-1">
                  <p class="text-sm text-blue-700 font-semibold mb-1">Bonne Réponse</p>
                  <p class="text-lg font-bold text-blue-900">{{ question.reponse }}</p>
                </div>
              </div>
            </div>

            <!-- No Winner Message -->
            <div class="bg-white rounded-2xl p-6 shadow-lg border border-slate-200/50 text-center">
              <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-slate-400" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                </svg>
              </div>
              <p class="text-slate-700 font-medium text-lg mb-2">Aucun participant n'a répondu</p>
              <p class="text-slate-500 text-sm">La récompense de <span class="font-bold text-amber-600">{{ question.gain }} FCFA</span> n'a pas été attribuée</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Active Questions -->
      <div v-if="activeQuestions.length > 0" class="space-y-6">
        <div 
          v-for="question in activeQuestions" 
          :key="question.id"
          class="bg-white rounded-3xl p-6 md:p-8 shadow-lg border border-slate-200/50 backdrop-blur-sm"
        >
          <!-- ... existing code ... -->
          <!-- Question Header -->
          <div class="flex items-start justify-between mb-6">
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg shadow-green-500/30">
                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                </svg>
              </div>
              <div>
                <span class="text-sm font-semibold text-green-600 bg-green-50 px-3 py-1 rounded-full">
                  Question #{{ question.id }}
                </span>
              </div>
            </div>
            <div class="flex items-center gap-2 text-sm text-slate-500">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
              </svg>
              <span>En direct</span>
            </div>
          </div>

          <!-- Question Text -->
          <div class="mb-6">
            <h2 class="text-xl md:text-2xl font-bold text-slate-800 mb-2 leading-relaxed">
              {{ question.question }}
            </h2>
          </div>

          <!-- Countdown Timer -->
          <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl p-6 mb-6 border border-amber-200/50">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl flex items-center justify-center">
                  <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M15 1H9v2h6V1zm-4 13h2V8h-2v6zm8.03-6.61l1.42-1.42c-.43-.51-.9-.99-1.41-1.41l-1.42 1.42C16.07 4.74 14.12 4 12 4c-4.97 0-9 4.03-9 9s4.02 9 9 9 9-4.03 9-9c0-2.12-.74-4.07-1.97-5.61zM12 20c-3.87 0-7-3.13-7-7s3.13-7 7-7 7 3.13 7 7z"/>
                  </svg>
                </div>
                <div>
                  <p class="text-sm text-amber-700 font-medium">Temps restant</p>
                  <p class="text-2xl md:text-3xl font-bold text-amber-900">
                    {{ formatTimeRemaining(question.debut) }}
                  </p>
                </div>
              </div>
              <div class="hidden md:block">
                <div class="text-right">
                  <p class="text-xs text-amber-600">Récompense</p>
                  <p class="text-xl font-bold text-amber-900">{{ question.gain }} FCFA</p>
                </div>
              </div>
            </div>
            <div class="md:hidden mt-3 pt-3 border-t border-amber-200">
              <p class="text-xs text-amber-600">Récompense</p>
              <p class="text-xl font-bold text-amber-900">{{ question.gain }} FCFA</p>
            </div>
            
            <!-- Progress Bar -->
            <div class="mt-4">
              <div class="flex-1 bg-gray-200 rounded-full h-2 overflow-hidden">
                <div 
                  :class="getProgressColor(calculateProgress(question.debut))"
                  class="h-full transition-all duration-1000 ease-linear"
                  :style="{ width: `${calculateProgress(question.debut)}%` }"
                ></div>
              </div>
            </div>
          </div>

          <!-- Call to Answer -->
          <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-6 border border-blue-200/50">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center">
                  <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
                  </svg>
                </div>
                <div>
                  <p class="text-sm text-blue-700 font-medium">Appelez pour répondre</p>
                  <p class="text-2xl md:text-3xl font-bold text-blue-900">{{ question.contact }}</p>
                </div>
              </div>
              <a 
                :href="`tel:${question.contact}`"
                class="w-full md:w-auto bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-6 py-3 rounded-xl font-semibold shadow-lg shadow-blue-500/30 hover:shadow-xl hover:shadow-blue-500/40 hover:scale-105 transition-all duration-300 flex items-center justify-center gap-2"
              >
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
                </svg>
                Appeler maintenant
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- No Active Questions -->
      <div v-if="activeQuestions.length === 0 && completedQuestionsWithWinners.length === 0 && completedQuestionsNoWinner.length === 0" class="bg-gradient-to-br from-violet-50 to-purple-50 rounded-3xl p-8 md:p-12 shadow-lg border border-violet-200/50 backdrop-blur-sm text-center">
        <!-- ... existing code ... -->
        <div class="max-w-md mx-auto">
          <div class="w-20 h-20 bg-gradient-to-br from-violet-500 to-purple-600 rounded-3xl mx-auto mb-6 flex items-center justify-center shadow-lg shadow-violet-500/30">
            <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 24 24">
              <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
            </svg>
          </div>
          <h2 class="text-2xl md:text-3xl font-bold text-violet-900 mb-3">Aucune question disponible</h2>
          <p class="text-violet-700 mb-6 leading-relaxed">
            Le prochain jeu Quiz Aeemciste aura lieu bientôt. Revenez pour tenter votre chance !
          </p>
          <div v-if="nextQuizDate" class="bg-white rounded-2xl p-6 mb-6">
            <p class="text-sm text-violet-600 font-medium mb-2">Prochain Quiz</p>
            <p class="text-3xl font-bold text-violet-900 mb-1">{{ nextQuizDate }}</p>
            <p class="text-sm text-violet-600">{{ nextQuizTime }}</p>
          </div>
          <button 
            @click="goBack"
            class="w-full sm:w-auto bg-gradient-to-r from-violet-600 to-purple-600 text-white px-8 py-3 rounded-xl font-semibold shadow-lg shadow-violet-500/30 hover:shadow-xl hover:shadow-violet-500/40 hover:scale-105 transition-all duration-300"
          >
            Retour au tableau de bord
          </button>
        </div>
      </div>
    </div>

    <!-- Mobile Navigation Bar -->
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-slate-200 px-4 py-3 md:hidden shadow-2xl">
      <!-- ... existing code ... -->
      <div class="flex justify-around items-center max-w-md mx-auto">
        <router-link to="/" class="flex flex-col items-center group">
          <div class="p-2 rounded-xl group-hover:bg-slate-50 transition-colors">
            <svg class="w-6 h-6 text-slate-400 group-hover:text-slate-600" fill="currentColor" viewBox="0 0 24 24">
              <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
            </svg>
          </div>
          <span class="text-xs text-slate-400 group-hover:text-slate-600 font-medium mt-1">Accueil</span>
        </router-link>

        <router-link to="/quiz" class="flex flex-col items-center group">
          <div class="p-2 rounded-xl group-hover:bg-green-50 transition-colors">
            <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
            </svg>
          </div>
          <span class="text-xs text-green-600 font-medium mt-1">Quiz</span>
        </router-link>

        <router-link to="/notifications" class="flex flex-col items-center group">
          <div class="p-2 rounded-xl group-hover:bg-slate-50 transition-colors relative">
            <svg class="w-6 h-6 text-slate-400 group-hover:text-slate-600" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>
            </svg>
          </div>
          <span class="text-xs text-slate-400 group-hover:text-slate-600 font-medium mt-1">Alertes</span>
        </router-link>

        <router-link to="/profile" class="flex flex-col items-center group">
          <div class="p-2 rounded-xl group-hover:bg-slate-50 transition-colors">
            <svg class="w-6 h-6 text-slate-400 group-hover:text-slate-600" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
            </svg>
          </div>
          <span class="text-xs text-slate-400 group-hover:text-slate-600 font-medium mt-1">Profil</span>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'

const API_URL = 'https://www.sogetrag.com/api/questionnaire.php'
const USER_API_URL = 'https://www.sogetrag.com/api/questionnaire.php'

const allQuestions = ref([])
const loading = ref(true)
const nextQuizDate = ref('')
const nextQuizTime = ref('')

let countdownInterval = null
let pollingInterval = null

//  Helper function to check if a question's time has expired
const isTimeExpired = (debutDate) => {
  if (!debutDate) return false
  
  const debut = new Date(debutDate)
  const now = new Date()
  const elapsed = (now - debut) / 1000 // seconds
  const total = 15 * 60 // 15 minutes in seconds
  
  return elapsed >= total
}

//  Computed property for active questions - excludes expired questions
const activeQuestions = computed(() => {
  return allQuestions.value.filter(q => 
    q.etat === 'En cours' && 
    q.debut !== null && 
    q.debut !== '' &&
    !isTimeExpired(q.debut) // Exclude questions with expired time
  )
})

//  Computed property for completed questions with winners
const completedQuestionsWithWinners = computed(() => {
  return allQuestions.value.filter(q => 
    q.etat === 'Terminé' && q.repondu !== null && q.repondu !== ''
  ).map(question => {
    const existingItem = completedWithWinnersData.value.find(
      item => item.question.id === question.id
    )
    return existingItem || { question, winner: null }
  })
})

//  Computed property for completed questions without winners - includes client-side expired questions
const completedQuestionsNoWinner = computed(() => {
  return allQuestions.value.filter(q => {
    // Backend marked as completed without winner
    const isBackendCompleted = q.etat === 'Terminé' && (q.repondu === null || q.repondu === '')
    
    // Client-side time expired (even if backend hasn't updated yet)
    const isClientExpired = q.etat === 'En cours' && q.debut !== null && q.debut !== '' && isTimeExpired(q.debut)
    
    return isBackendCompleted || isClientExpired
  })
})

// Store winner data separately to avoid reactivity issues
const completedWithWinnersData = ref([])

const questionsAreEqual = (arr1, arr2) => {
  if (arr1.length !== arr2.length) return false
  
  return arr1.every((q1, index) => {
    const q2 = arr2[index]
    return q1.id === q2.id && 
           q1.question === q2.question && 
           q1.debut === q2.debut && 
           q1.etat === q2.etat &&
           q1.repondu === q2.repondu
  })
}

const fetchWinnerInfo = async (matricule) => {
  try {
    const response = await fetch(`${API_URL}?action=get_user_by_matricule&matricule=${matricule}`)
    const data = await response.json()
    
    if (data.success && data.data) {
      return data.data
    }
    return null
  } catch (err) {
    console.error('Error fetching winner info:', err)
    return null
  }
}

const getInitials = (name) => {
  if (!name) return '?'
  const parts = name.split(' ')
  if (parts.length >= 2) {
    return (parts[0][0] + parts[1][0]).toUpperCase()
  }
  return name.substring(0, 2).toUpperCase()
}

const fetchQuestionnaires = async (silent = false) => {
  if (!silent) {
    loading.value = true
  }

  try {
    const cacheBuster = Math.random().toString(36).substring(2)
    const response = await fetch(`${API_URL}?action=questionnaires&cb=${cacheBuster}`)
    
    const data = await response.json()

    if (data.success) {
      if (!questionsAreEqual(allQuestions.value, data.data)) {
        allQuestions.value = data.data
      }

      // Fetch winner info for completed questions with winners
      const completedWithWinners = data.data.filter(q => 
        q.etat === 'Terminé' && q.repondu !== null && q.repondu !== ''
      )

      for (const question of completedWithWinners) {
        const existingItem = completedWithWinnersData.value.find(
          item => item.question.id === question.id
        )
        
        if (!existingItem) {
          completedWithWinnersData.value.push({
            question,
            winner: null
          })
          
          const winnerInfo = await fetchWinnerInfo(question.repondu)
          
          const itemIndex = completedWithWinnersData.value.findIndex(
            item => item.question.id === question.id
          )
          if (itemIndex !== -1) {
            completedWithWinnersData.value[itemIndex].winner = winnerInfo
          }
        }
      }

      completedWithWinnersData.value = completedWithWinnersData.value.filter(
        item => completedWithWinners.some(q => q.id === item.question.id)
      )

      // Handle next quiz date
      if (activeQuestions.value.length === 0 && completedQuestionsWithWinners.value.length === 0 && completedQuestionsNoWinner.value.length === 0) {
        const upcomingQuestions = data.data.filter(q => 
          q.etat === 'En attente' && q.date
        ).sort((a, b) => new Date(a.date) - new Date(b.date))

        if (upcomingQuestions.length > 0) {
          const nextQuiz = upcomingQuestions[0]
          const nextDate = new Date(nextQuiz.date)
          const newNextQuizDate = nextDate.toLocaleDateString('fr-FR', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
          })
          const newNextQuizTime = nextDate.toLocaleTimeString('fr-FR', {
            hour: '2-digit',
            minute: '2-digit'
          })
          
          if (nextQuizDate.value !== newNextQuizDate) {
            nextQuizDate.value = newNextQuizDate
          }
          if (nextQuizTime.value !== newNextQuizTime) {
            nextQuizTime.value = newNextQuizTime
          }
        } else {
          if (nextQuizDate.value !== 'À venir') {
            nextQuizDate.value = 'À venir'
          }
          if (nextQuizTime.value !== 'Restez connecté') {
            nextQuizTime.value = 'Restez connecté'
          }
        }
      }
    }
  } catch (err) {
    console.error('Error fetching questionnaires:', err)
  } finally {
    if (!silent) {
      loading.value = false
    }
  }
}

const calculateProgress = (debutDate) => {
  if (!debutDate) return 0
  
  const debut = new Date(debutDate)
  const now = new Date()
  const elapsed = (now - debut) / 1000 // seconds
  const total = 15 * 60 // 15 minutes in seconds
  
  const progress = Math.min(Math.round((elapsed / total) * 100), 100)
  return progress
}

const formatTimeRemaining = (debutDate) => {
  if (!debutDate) return '15:00'
  
  const debut = new Date(debutDate)
  const now = new Date()
  const elapsed = (now - debut) / 1000 // seconds
  const total = 15 * 60 // 15 minutes in seconds
  const remaining = Math.max(total - elapsed, 0)
  
  const minutes = Math.floor(remaining / 60)
  const seconds = Math.floor(remaining % 60)
  
  return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`
}

const getProgressColor = (progress) => {
  if (progress < 50) return 'bg-blue-500'
  if (progress < 80) return 'bg-yellow-500'
  return 'bg-red-500'
}

const updateCountdown = async () => {
  // Force re-render by triggering computed properties
  allQuestions.value = [...allQuestions.value]
}

const goBack = () => {
  window.history.back()
}

onMounted(() => {
  fetchQuestionnaires(false)
  
  countdownInterval = setInterval(updateCountdown, 1000)
  
  pollingInterval = setInterval(() => {
    fetchQuestionnaires(true)
  }, 10000)
})

onUnmounted(() => {
  if (countdownInterval) {
    clearInterval(countdownInterval)
  }
  if (pollingInterval) {
    clearInterval(pollingInterval)
  }
})
</script>