<template>
  <div class="min-h-screen bg-gradient-to-br from-emerald-50 via-white to-green-50 pb-24 relative overflow-hidden" style="margin-top: -10px;">
    <!-- Decorative background elements -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <div class="absolute top-0 left-0 w-96 h-96 bg-gradient-to-br from-green-200/30 to-transparent rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2"></div>
      <div class="absolute bottom-0 right-0 w-96 h-96 bg-gradient-to-tl from-emerald-200/30 to-transparent rounded-full blur-3xl translate-x-1/2 translate-y-1/2"></div>
      <div class="absolute top-1/2 left-1/2 w-64 h-64 bg-gradient-to-br from-blue-200/20 to-transparent rounded-full blur-3xl"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-6 md:px-6 md:py-8 relative z-10">
      <!-- Header Card 
      <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 p-6 mb-6 md:p-8 md:mb-8">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg transform rotate-3">
              <span class="text-white text-2xl font-bold transform -rotate-3">A</span>
            </div>
            <div>
              <h1 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">
                Tableau de bord
              </h1>
              <p class="text-sm text-gray-500 mt-1">Bienvenue dans votre espace</p>
            </div>
          </div>
          <button class="p-3 hover:bg-gray-100 rounded-xl transition-colors">
            <svg class="w-6 h-6 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
            </svg>
          </button>
        </div>
      </div>

      <!-- Formation Card 
      <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 p-6 mb-6 md:p-8 md:mb-8 overflow-hidden relative">
        <!-- Decorative gradient overlay 
        <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-bl from-green-100/50 to-transparent rounded-full blur-2xl"></div>
        
        <div class="relative z-10">
          <div class="flex flex-col md:flex-row items-center gap-6">
            <!-- Icon 
            <div class="flex-shrink-0 w-20 h-20 md:w-24 md:h-24 bg-gradient-to-br from-green-500 to-emerald-600 rounded-3xl flex items-center justify-center shadow-2xl shadow-green-500/40 animate-pulse-slow">
              <svg class="w-10 h-10 md:w-12 md:h-12 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
              </svg>
            </div>

            <!-- Content 
            <div class="flex-1 text-center md:text-left w-full">
              <div class="inline-flex items-center gap-2 bg-green-100 text-green-700 px-4 py-2 rounded-full text-sm font-semibold mb-4">
                <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                En direct
              </div>
              
              <h3 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6">Formation</h3>

              <!-- Timer or Button 
              <div class="mb-4">
                <!-- Timer 
                <div v-if="!isAvailable" class="bg-gradient-to-r from-blue-50 to-emerald-50 rounded-2xl p-6 md:p-8 border-2 border-blue-200/50">
                  <p class="text-sm font-semibold text-gray-600 mb-3">Début dans</p>
                  <div class="flex items-center justify-center gap-3 md:gap-4">
                    <div class="bg-white rounded-xl shadow-lg p-4 min-w-[80px] md:min-w-[100px]">
                      <div class="text-4xl md:text-5xl font-extrabold bg-gradient-to-br from-blue-600 to-emerald-600 bg-clip-text text-transparent">
                        {{ hours }}
                      </div>
                      <div class="text-xs text-gray-500 mt-1 font-semibold">HEURES</div>
                    </div>
                    <div class="text-3xl font-bold text-gray-400">:</div>
                    <div class="bg-white rounded-xl shadow-lg p-4 min-w-[80px] md:min-w-[100px]">
                      <div class="text-4xl md:text-5xl font-extrabold bg-gradient-to-br from-blue-600 to-emerald-600 bg-clip-text text-transparent">
                        {{ minutes }}
                      </div>
                      <div class="text-xs text-gray-500 mt-1 font-semibold">MINUTES</div>
                    </div>
                    <div class="text-3xl font-bold text-gray-400">:</div>
                    <div class="bg-white rounded-xl shadow-lg p-4 min-w-[80px] md:min-w-[100px]">
                      <div class="text-4xl md:text-5xl font-extrabold bg-gradient-to-br from-blue-600 to-emerald-600 bg-clip-text text-transparent">
                        {{ seconds }}
                      </div>
                      <div class="text-xs text-gray-500 mt-1 font-semibold">SECONDES</div>
                    </div>
                  </div>
                </div>

                <!-- Button 
                <a v-else href="https://meet.google.com/rqt-qpro-kof" target="_blank" rel="noopener noreferrer">
                  <button class="w-full bg-gradient-to-r from-green-500 to-emerald-600 text-white px-8 py-5 rounded-2xl font-bold shadow-2xl shadow-green-500/40 hover:shadow-green-500/60 hover:scale-[1.02] active:scale-[0.98] transition-all duration-300 flex items-center justify-center gap-3 text-lg md:text-xl group">
                    <svg class="w-7 h-7 group-hover:rotate-12 transition-transform" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                    </svg>
                    Accéder à la formation
                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                  </button>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
      -->
       <!-- Information section - order-1 on mobile to appear first 
        <div v-if="lala" class="order-1 bg-gradient-to-br from-green-50 to-emerald-50 rounded-3xl p-6 shadow-lg border border-green-200/50 md:p-8 backdrop-blur-sm">
          <div class="flex items-start gap-4">
            <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg shadow-green-500/30">
              <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
              </svg>
            </div>
            <div class="flex-1">
              <h3 class="text-xl font-bold text-green-900 mb-2 md:text-2xl">Quiz Aeemciste</h3>
              <p class="text-green-700 text-sm md:text-base leading-relaxed mb-4">
                Le jeu Quiz Aeemciste pour remporter de l'argent est disponible ! Testez vos connaissances et gagnez des récompenses.
              </p>
              <router-link to="/quiz">
              <button class="w-full sm:w-auto bg-gradient-to-r from-green-600 to-emerald-600 text-white px-6 py-3 rounded-xl font-semibold shadow-lg shadow-green-500/30 hover:shadow-xl hover:shadow-green-500/40 hover:scale-105 transition-all duration-300 flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                </svg>
                Oui je participe
              </button>
            </router-link>
            </div>
          </div>
        </div>
 -->    
        <div
          class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 p-6 mb-6 md:p-8 md:mb-8 overflow-hidden relative"
          v-if="userStore.user.je_suis === 'CE_CE' || userStore.user.je_suis === 'SR_SR' || userStore.user.je_suis === 'SR'"
        >
        <!-- Decorative gradient overlay -->
         
        <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-bl from-purple-100/50 to-transparent rounded-full blur-2xl"></div>
        
        <div class="relative z-10">
          <div class="flex flex-col md:flex-row items-center gap-6">
            <!-- Icon -->
            <div class="flex-shrink-0 w-20 h-20 md:w-24 md:h-24 bg-gradient-to-br from-purple-500 to-violet-600 rounded-3xl flex items-center justify-center shadow-2xl shadow-purple-500/40">
              <svg class="w-10 h-10 md:w-12 md:h-12 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-7h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11z"/>
              </svg>
            </div>

            <!-- Content -->
            <div class="flex-1 text-center md:text-left w-full">
              <div class="inline-flex items-center gap-2 bg-purple-100 text-purple-700 px-4 py-2 rounded-full text-sm font-semibold mb-4">
                <span class="w-2 h-2 bg-purple-500 rounded-full animate-pulse"></span>
                Inscription ouverte
              </div>
              
              <h3 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6">Inscription CIMA</h3>

              <!-- Button -->
              <router-link to="/cima">
                <button class="w-full bg-gradient-to-r from-purple-500 to-violet-600 text-white px-8 py-5 rounded-2xl font-bold shadow-2xl shadow-purple-500/40 hover:shadow-purple-500/60 hover:scale-[1.02] active:scale-[0.98] transition-all duration-300 flex items-center justify-center gap-3 text-lg md:text-xl group">
                  <svg class="w-7 h-7 group-hover:rotate-12 transition-transform" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                  </svg>
                  Inscrire un membre
                  <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                  </svg>
                </button>
              </router-link>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions Section -->
      <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 p-6 md:p-8">
        <div class="flex justify-between items-center mb-6">
          <div>
            <h2 class="text-2xl md:text-3xl font-bold text-gray-800">Actions rapides</h2>
            <p class="text-sm text-gray-500 mt-1">Accédez rapidement à vos outils</p>
          </div>
        </div>

        <!-- Menu buttons grid -->
        <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 md:gap-6">
          <!-- Aeemci Social -->
          <router-link to="/social" class="group">
            <div class="bg-gradient-to-br from-gray-50 to-white rounded-2xl p-4 border-2 border-gray-100 hover:border-blue-300 hover:shadow-xl transition-all duration-300 hover:scale-105">
              <div class="w-16 h-16 mx-auto bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl mb-3 flex items-center justify-center shadow-lg shadow-blue-500/30 group-hover:shadow-blue-500/50 transition-all">
                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M16 4c0-1.11.89-2 2-2s2 .89 2 2-.89 2-2 2-2-.89-2-2zm4 18v-6h2.5l-2.54-7.63A2.996 2.996 0 0 0 17.06 6c-.8 0-1.54.5-1.85 1.26l-1.92 5.77c-.24.74.16 1.54.9 1.78.74.24 1.54-.16 1.78-.9L17.5 10H18v12h2zM12.5 11.5c.83 0 1.5-.67 1.5-1.5s-.67-1.5-1.5-1.5S11 9.17 11 10s.67 1.5 1.5 1.5zM5.5 6c1.11 0 2-.89 2-2s-.89-2-2-2-2 .89-2 2 .89 2 2 2zm2 16v-7H9l-1.5-4.5c-.31-.93-1.31-1.43-2.24-1.12-.93.31-1.43 1.31-1.12 2.24L5.5 16H7v6h.5z"/>
                </svg>
              </div>
              <div class="text-xs text-center font-semibold text-gray-700 group-hover:text-blue-600 transition-colors leading-tight">
                Aeemci<br/>Social
              </div>
            </div>
          </router-link>

          <!-- Bibliotheque -->

          <router-link to="/bibliotheque" class="group">
            <div class="bg-gradient-to-br from-gray-50 to-white rounded-2xl p-4 border-2 border-gray-100 hover:border-amber-300 hover:shadow-xl transition-all duration-300 hover:scale-105">
              <div class="w-16 h-16 mx-auto bg-gradient-to-br from-amber-500 to-amber-600 rounded-2xl mb-3 flex items-center justify-center shadow-lg shadow-amber-500/30 group-hover:shadow-amber-500/50 transition-all">
                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M21 5c-1.11-.35-2.33-.5-3.5-.5-1.95 0-4.05.4-5.5 1.5-1.45-1.1-3.55-1.5-5.5-1.5S2.45 4.9 1 6v14.65c0 .25.25.5.5.5.1 0 .15-.05.25-.05C3.1 20.45 5.05 20 6.5 20c1.95 0 4.05.4 5.5 1.5 1.35-.85 3.8-1.5 5.5-1.5 1.65 0 3.35.3 4.75 1.05.1.05.15.05.25.05.25 0 .5-.25.5-.5V6c-.6-.45-1.25-.75-2-1zm0 13.5c-1.1-.35-2.3-.5-3.5-.5-1.7 0-4.15.65-5.5 1.5V8c1.35-.85 3.8-1.5 5.5-1.5 1.2 0 2.4.15 3.5.5v11.5z"/>
                </svg>
              </div>
              <div class="text-xs text-center font-semibold text-gray-700 group-hover:text-amber-600 transition-colors leading-tight">
                Bibliothèque
              </div>
            </div>
          </router-link>

          <router-link to="/documents" class="group">
            <div class="bg-gradient-to-br from-gray-50 to-white rounded-2xl p-4 border-2 border-gray-100 hover:border-emerald-300 hover:shadow-xl transition-all duration-300 hover:scale-105">
              <div class="w-16 h-16 mx-auto bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl mb-3 flex items-center justify-center shadow-lg shadow-emerald-500/30 group-hover:shadow-emerald-500/50 transition-all">
               <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M18 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM6 4h5v8l-2.5-1.5L6 12V4z"/>
                </svg>
              </div>
              <div class="text-xs text-center font-semibold text-gray-700 group-hover:text-emerald-600 transition-colors leading-tight">
                Documents Aeemci
              </div>
            </div>
          </router-link>


          <!-- Coin spirituel
          <router-link to="/coins" class="group">
            <div class="bg-gradient-to-br from-gray-50 to-white rounded-2xl p-4 border-2 border-gray-100 hover:border-violet-300 hover:shadow-xl transition-all duration-300 hover:scale-105">
              <div class="w-16 h-16 mx-auto bg-gradient-to-br from-violet-500 to-violet-600 rounded-2xl mb-3 flex items-center justify-center shadow-lg shadow-violet-500/30 group-hover:shadow-violet-500/50 transition-all">
                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.94-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                </svg>
              </div>
              <div class="text-xs text-center font-semibold text-gray-700 group-hover:text-violet-600 transition-colors leading-tight">
                Coin<br/>spirituel
              </div>
            </div>
          </router-link>
           -->

          <!-- Messages importants 
          <router-link to="/messages" class="group">
            <div class="bg-gradient-to-br from-gray-50 to-white rounded-2xl p-4 border-2 border-gray-100 hover:border-rose-300 hover:shadow-xl transition-all duration-300 hover:scale-105">
              <div class="w-16 h-16 mx-auto bg-gradient-to-br from-rose-500 to-rose-600 rounded-2xl mb-3 flex items-center justify-center shadow-lg shadow-rose-500/30 group-hover:shadow-rose-500/50 transition-all">
                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M20 2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h4l4 4 4-4h4c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-7 13h-2v-2h2v2zm0-4h-2V7h2v4z"/>
                </svg>
              </div>
              <div class="text-xs text-center font-semibold text-gray-700 group-hover:text-rose-600 transition-colors leading-tight">
                Messages<br/>importants
              </div>
            </div>
          </router-link>
          -->

          <!-- Mes livres -->
          <router-link to="/zoom" class="group">
            <div class="bg-gradient-to-br from-gray-50 to-white rounded-2xl p-4 border-2 border-gray-100 hover:border-amber-300 hover:shadow-xl transition-all duration-300 hover:scale-105">
              <div class="w-16 h-16 mx-auto bg-gradient-to-br from-amber-500 to-amber-600 rounded-2xl mb-3 flex items-center justify-center shadow-lg shadow-amber-500/30 group-hover:shadow-amber-500/50 transition-all">
                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M21 5c-1.11-.35-2.33-.5-3.5-.5-1.95 0-4.05.4-5.5 1.5-1.45-1.1-3.55-1.5-5.5-1.5S2.45 4.9 1 6v14.65c0 .25.25.5.5.5.1 0 .15-.05.25-.05C3.1 20.45 5.05 20 6.5 20c1.95 0 4.05.4 5.5 1.5 1.35-.85 3.8-1.5 5.5-1.5 1.65 0 3.35.3 4.75 1.05.1.05.15.05.25.05.25 0 .5-.25.5-.5V6c-.6-.45-1.25-.75-2-1zm0 13.5c-1.1-.35-2.3-.5-3.5-.5-1.7 0-4.15.65-5.5 1.5V8c1.35-.85 3.8-1.5 5.5-1.5 1.2 0 2.4.15 3.5.5v11.5z"/>
                </svg>
              </div>
              <div class="text-xs text-center font-semibold text-gray-700 group-hover:text-amber-600 transition-colors leading-tight">
                Formation Zoom
              </div>
            </div>
          </router-link>

          <!-- Mes Secrétariats (CE_CE only) -->
          <router-link to="/mes_sr" class="group" v-if="userStore.user.je_suis === 'CE_CE'">
            <div class="bg-gradient-to-br from-gray-50 to-white rounded-2xl p-4 border-2 border-gray-100 hover:border-teal-300 hover:shadow-xl transition-all duration-300 hover:scale-105">
              <div class="w-16 h-16 mx-auto bg-gradient-to-br from-teal-500 to-teal-600 rounded-2xl mb-3 flex items-center justify-center shadow-lg shadow-teal-500/30 group-hover:shadow-teal-500/50 transition-all">
                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 2 2h8c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
                </svg>
              </div>
              <div class="text-xs text-center font-semibold text-gray-700 group-hover:text-teal-600 transition-colors leading-tight">
                Mes<br/>Secrétariats
              </div>
            </div>
          </router-link>

          <!-- Mes Sous-comités (SR_SR or CE_CE) -->
          <router-link to="/mes_sous_comites" class="group" v-if="userStore.user.je_suis === 'SR_SR' || userStore.user.je_suis === 'CE_CE'">
            <div class="bg-gradient-to-br from-gray-50 to-white rounded-2xl p-4 border-2 border-gray-100 hover:border-indigo-300 hover:shadow-xl transition-all duration-300 hover:scale-105">
              <div class="w-16 h-16 mx-auto bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl mb-3 flex items-center justify-center shadow-lg shadow-indigo-500/30 group-hover:shadow-indigo-500/50 transition-all">
                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                </svg>
              </div>
              <div class="text-xs text-center font-semibold text-gray-700 group-hover:text-indigo-600 transition-colors leading-tight">
                Mes Sous<br/>comités
              </div>
            </div>
          </router-link>

          <router-link to="/mes_sous_comites" class="group" v-if="userStore.user.je_suis === 'SR_SR' || userStore.user.je_suis === 'CE_CE'">
            <div class="bg-gradient-to-br from-gray-50 to-white rounded-2xl p-4 border-2 border-gray-100 hover:border-indigo-300 hover:shadow-xl transition-all duration-300 hover:scale-105">
              <div class="w-16 h-16 mx-auto bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl mb-3 flex items-center justify-center shadow-lg shadow-indigo-500/30 group-hover:shadow-indigo-500/50 transition-all">
                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                </svg>
              </div>
              <div class="text-xs text-center font-semibold text-gray-700 group-hover:text-indigo-600 transition-colors leading-tight">
                Mes Sections
              </div>
            </div>
          </router-link>

          <!-- Mon Bureau (SR_SR or SC_SC) -->
          <router-link to="/mon-bureau" class="group" v-if="userStore.user.je_suis === 'SR_SR' || userStore.user.je_suis === 'SC_SC'">
            <div class="bg-gradient-to-br from-gray-50 to-white rounded-2xl p-4 border-2 border-gray-100 hover:border-yellow-300 hover:shadow-xl transition-all duration-300 hover:scale-105">
              <div class="w-16 h-16 mx-auto bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-2xl mb-3 flex items-center justify-center shadow-lg shadow-yellow-500/30 group-hover:shadow-yellow-500/50 transition-all">
                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M20 6h-2.18c.11-.31.18-.65.18-1 0-1.66-1.34-3-3-3-1.05 0-1.96.54-2.5 1.35l-.5.67-.5-.68C10.96 2.54 10.05 2 9 2 7.34 2 6 3.34 6 5c0 .35.07.69.18 1H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-5-2c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zM9 4c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm11 15H4v-2h16v2zm0-5H4V8h5.08L7 10.83 8.62 12 11 8.76l1-1.36 1 1.36L15.38 12 17 10.83 14.92 8H20v6z"/>
                </svg>
              </div>
              <div class="text-xs text-center font-semibold text-gray-700 group-hover:text-yellow-600 transition-colors leading-tight">
                Mon<br/>Bureau
              </div>
            </div>
          </router-link>

          <!-- Mes rapports (SR_SR, SC_SC, or SECTION) -->
          <router-link to="/mes-rapports" class="group" v-if="userStore.user.je_suis === 'SR_SR' || userStore.user.je_suis === 'SC_SC' || userStore.user.je_suis === 'SECTION'">
            <div class="bg-gradient-to-br from-gray-50 to-white rounded-2xl p-4 border-2 border-gray-100 hover:border-cyan-300 hover:shadow-xl transition-all duration-300 hover:scale-105">
              <div class="w-16 h-16 mx-auto bg-gradient-to-br from-cyan-500 to-cyan-600 rounded-2xl mb-3 flex items-center justify-center shadow-lg shadow-cyan-500/30 group-hover:shadow-cyan-500/50 transition-all">
                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                </svg>
              </div>
              <div class="text-xs text-center font-semibold text-gray-700 group-hover:text-cyan-600 transition-colors leading-tight">
                Mes<br/>rapports
              </div>
            </div>
          </router-link>

          <!-- Mes activités (SR_SR, SC_SC, or SECTION) -->
          <router-link to="/mes-activites" class="group" v-if="userStore.user.je_suis === 'SR_SR' || userStore.user.je_suis === 'SC_SC' || userStore.user.je_suis === 'SECTION'">
            <div class="bg-gradient-to-br from-gray-50 to-white rounded-2xl p-4 border-2 border-gray-100 hover:border-lime-300 hover:shadow-xl transition-all duration-300 hover:scale-105">
              <div class="w-16 h-16 mx-auto bg-gradient-to-br from-lime-500 to-lime-600 rounded-2xl mb-3 flex items-center justify-center shadow-lg shadow-lime-500/30 group-hover:shadow-lime-500/50 transition-all">
                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/>
                </svg>
              </div>
              <div class="text-xs text-center font-semibold text-gray-700 group-hover:text-lime-600 transition-colors leading-tight">
                Mes<br/>activités
              </div>
            </div>
          </router-link>

          <!-- Donner avis -->
          <router-link to="/avis" class="group">
            <div class="bg-gradient-to-br from-gray-50 to-white rounded-2xl p-4 border-2 border-gray-100 hover:border-pink-300 hover:shadow-xl transition-all duration-300 hover:scale-105">
              <div class="w-16 h-16 mx-auto bg-gradient-to-br from-pink-500 to-pink-600 rounded-2xl mb-3 flex items-center justify-center shadow-lg shadow-pink-500/30 group-hover:shadow-pink-500/50 transition-all">
                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                </svg>
              </div>
              <div class="text-xs text-center font-semibold text-gray-700 group-hover:text-pink-600 transition-colors leading-tight">
                Donner<br/>avis
              </div>
            </div>
          </router-link>
        </div>
      </div>
    </div>
    

    <!-- Mobile Navigation Bar -->
    <div class="fixed bottom-0 left-0 right-0 bg-white/90 backdrop-blur-xl border-t border-gray-200/50 px-4 py-3 md:hidden shadow-2xl z-50">
      <div class="flex justify-around items-center max-w-md mx-auto">
        <router-link to="/" class="flex flex-col items-center group">
          <div class="p-3 rounded-2xl bg-gradient-to-br from-green-500 to-emerald-600 shadow-lg shadow-green-500/30 transition-all group-hover:shadow-green-500/50 group-hover:scale-110">
            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
              <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
            </svg>
          </div>
          <span class="text-xs text-green-600 font-bold mt-1">Accueil</span>
        </router-link>

        <router-link to="/search" class="flex flex-col items-center group">
          <div class="p-3 rounded-2xl group-hover:bg-gray-100 transition-all">
            <svg class="w-6 h-6 text-gray-400 group-hover:text-gray-600" fill="currentColor" viewBox="0 0 24 24">
              <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
            </svg>
          </div>
          <span class="text-xs text-gray-400 group-hover:text-gray-600 font-medium mt-1">Recherche</span>
        </router-link>

        <router-link to="/notifications" class="flex flex-col items-center group">
          <div class="p-3 rounded-2xl group-hover:bg-gray-100 transition-all relative">
            <svg class="w-6 h-6 text-gray-400 group-hover:text-gray-600" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>
            </svg>
            <span class="absolute top-2 right-2 w-2.5 h-2.5 bg-rose-500 rounded-full border-2 border-white animate-pulse"></span>
          </div>
          <span class="text-xs text-gray-400 group-hover:text-gray-600 font-medium mt-1">Alertes</span>
        </router-link>

        <router-link to="/profile" class="flex flex-col items-center group">
          <div class="p-3 rounded-2xl group-hover:bg-gray-100 transition-all">
            <svg class="w-6 h-6 text-gray-400 group-hover:text-gray-600" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
            </svg>
          </div>
          <span class="text-xs text-gray-400 group-hover:text-gray-600 font-medium mt-1">Profil</span>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useUserStore } from '../../stores/user'

const userStore = useUserStore()

// Heure cible aujourd'hui
const targetHour = 21;
const targetMinute = 0;
const remainingTime = ref(0);
const isAvailable = ref(false);
let timerInterval = null;

// Calcul du timer
const hours = computed(() => String(Math.floor(remainingTime.value / 3600)).padStart(2, '0'));
const minutes = computed(() => String(Math.floor((remainingTime.value % 3600) / 60)).padStart(2, '0'));
const seconds = computed(() => String(remainingTime.value % 60).padStart(2, '0'));

function updateRemainingTime() {
  const now = new Date();
  const target = new Date();
  target.setHours(targetHour, targetMinute, 0, 0);

  // Si on est passé après 21h aujourd'hui
  if (now >= target) {
    remainingTime.value = 0;
    isAvailable.value = true;
    clearInterval(timerInterval);
    return;
  }

  remainingTime.value = Math.max(Math.floor((target - now) / 1000), 0);
}

function startTimer() {
  updateRemainingTime();
  timerInterval = setInterval(updateRemainingTime, 1000);
}

// Mock user data - replace with actual user data from your store/API
const user = ref({
  je_suis: 'CE_CE' // Can be: 'CE_CE', 'SR_SR', 'SC_SC', 'SECTION'
})

onMounted(() => startTimer());
onUnmounted(() => clearInterval(timerInterval));
</script>

<style scoped>
@keyframes pulse-slow {
  0%, 100% {
    opacity: 1;
    transform: scale(1);
  }
  50% {
    opacity: 0.8;
    transform: scale(1.05);
  }
}

.animate-pulse-slow {
  animation: pulse-slow 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

.bg-gradient-to-br {
  background-image: linear-gradient(to bottom right, var(--tw-gradient-stops));
}

.bg-gradient-to-tl {
  background-image: linear-gradient(to top left, var(--tw-gradient-stops));
}

.bg-gradient-to-r {
  background-image: linear-gradient(to right, var(--tw-gradient-stops));
}

.bg-gradient-to-bl {
  background-image: linear-gradient(to bottom left, var(--tw-gradient-stops));
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

.from-blue-200\/20 {
  --tw-gradient-from: rgba(191, 219, 254, 0.2);
  --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, rgba(191, 219, 254, 0));
}

.from-green-500 {
  --tw-gradient-from: #22c55e;
  --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, rgba(34, 197, 94, 0));
}

.to-emerald-600 {
  --tw-gradient-to: #059669;
}

.from-green-600 {
  --tw-gradient-from: #16a34a;
  --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, rgba(22, 163, 74, 0));
}

.from-blue-50 {
  --tw-gradient-from: #eff6ff;
  --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, rgba(239, 246, 255, 0));
}

.to-emerald-50 {
  --tw-gradient-to: #ecfdf5;
}

.from-blue-600 {
  --tw-gradient-from: #2563eb;
  --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, rgba(37, 99, 235, 0));
}

.from-green-100\/50 {
  --tw-gradient-from: rgba(220, 252, 231, 0.5);
  --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, rgba(220, 252, 231, 0));
}

.from-gray-50 {
  --tw-gradient-from: #f9fafb;
  --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, rgba(249, 250, 251, 0));
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
</style>
