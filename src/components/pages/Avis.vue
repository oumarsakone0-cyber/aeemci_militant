<script setup>
import { ref, computed, onMounted } from "vue";
import axios from "axios";
import { useUserStore } from "../../stores/user";

const userStore = useUserStore();
const showModal = ref(false);
const avis = ref("");
const avisList = ref([]);
const filtre = ref("");
const tri = ref("recent");

const chargerAvis = async () => {
  try {
    const rand = Math.random().toString(36).substring(2, 10);
    
    let response;
    if (userStore.user.je_suis == "CE_CE") {
      response = await axios.get(
        `http://sogetrag.com/apistage/avis.php?action=avis&rand=${rand}`
      );
    } else {
      response = await axios.get(
        `http://sogetrag.com/apistage/avis.php?action=avis_user&matricule=${userStore.user.matricule_gen}&rand=${rand}`
      );
    }

    avisList.value = response.data.data || [];
  } catch (error) {
    console.error("Erreur chargement avis :", error);
  }
};

const avisFiltres = computed(() => {
  let result = avisList.value;

  if (filtre.value) {
    result = result.filter(
      (a) =>
        a.matricule?.toLowerCase().includes(filtre.value.toLowerCase()) ||
        a.avis?.toLowerCase().includes(filtre.value.toLowerCase()) ||
        a.date_avis?.toLowerCase().includes(filtre.value.toLowerCase())
    );
  }

  if (tri.value === "recent") {
    result = result.sort(
      (a, b) => new Date(b.date_avis) - new Date(a.date_avis)
    );
  } else if (tri.value === "ancien") {
    result = result.sort(
      (a, b) => new Date(a.date_avis) - new Date(b.date_avis)
    );
  } else if (tri.value === "matricule") {
    result = result.sort((a, b) => a.matricule.localeCompare(b.matricule));
  }

  return result;
});

const envoyerAvis = async () => {
  if (!avis.value) {
    alert("Veuillez écrire un avis !");
    return;
  }

  try {
    await axios.post("http://sogetrag.com/apistage/avis.php?action=create_avis", {
      matricule: userStore.user.matricule_gen || "INCONNU",
      avis: avis.value,
    });
    avis.value = "";
    showModal.value = false;
    await chargerAvis();
  } catch (error) {
    console.error("Erreur envoi avis :", error);
    alert("Erreur lors de l'envoi");
  }
};

onMounted(chargerAvis);
</script>

<template>
  <div class="min-h-screen bg-gradient-to-br from-emerald-50 via-teal-50 to-cyan-50 p-6" style="margin-bottom: 50px;">
    <div class="max-w-4xl mx-auto">
      <!-- Header avec gradient et glassmorphism -->
      <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-500 via-teal-500 to-cyan-500 p-8 mb-8 shadow-2xl">
        <div class="absolute inset-0 bg-white/10 backdrop-blur-sm"></div>
        <div class="relative flex items-center justify-between">
          <div>
            <h1 class="text-4xl font-black text-white mb-2 tracking-tight" style="font-size: 16px; color: black">
              Avis et Suggestions
            </h1>
            <p class="text-emerald-50 font-medium">
              Partagez vos idées et consultez les retours
            </p>
          </div>
          <button
            @click="showModal = true"
            class="group relative px-6 py-3 bg-white text-emerald-600 font-bold rounded-xl shadow-lg hover:shadow-2xl hover:scale-105 transition-all duration-300 overflow-hidden"
          >
            <div class="absolute inset-0 bg-gradient-to-r from-emerald-400 to-teal-400 opacity-0 group-hover:opacity-20 transition-opacity"></div>
            <span class="relative flex items-center gap-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
              </svg>
              +
            </span>
          </button>
        </div>
      </div>

      <!-- Filtres et recherche -->
      <div class="bg-white/80 backdrop-blur-lg rounded-2xl shadow-xl p-6 mb-6 border border-emerald-100">
        <div class="flex flex-col sm:flex-row gap-4">
          <!-- Recherche -->
          <div class="flex-1 relative group">
            <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
              <svg class="w-5 h-5 text-emerald-400 group-focus-within:text-emerald-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
              </svg>
            </div>
            <input
              v-model="filtre"
              type="text"
              placeholder="Rechercher par matricule, texte ou date..."
              class="w-full pl-12 pr-4 py-3.5 bg-gradient-to-r from-emerald-50 to-teal-50 border-2 border-emerald-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 transition-all duration-300 font-medium"
            />
          </div>

          <!-- Tri -->
          <div class="relative group sm:w-64">
            <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
              <svg class="w-5 h-5 text-teal-400 group-focus-within:text-teal-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"/>
              </svg>
            </div>
            <select
              v-model="tri"
              class="w-full pl-12 pr-10 py-3.5 bg-gradient-to-r from-teal-50 to-cyan-50 border-2 border-teal-200 rounded-xl focus:outline-none focus:border-teal-500 focus:ring-4 focus:ring-teal-100 transition-all duration-300 font-medium appearance-none cursor-pointer"
            >
              <option value="recent">Plus récents</option>
              <option value="ancien">Plus anciens</option>
              <option value="matricule">Par matricule</option>
            </select>
            <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none">
              <svg class="w-5 h-5 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Liste des avis -->
      <div v-if="avisFiltres.length" class="space-y-4">
        <div
          v-for="(a, i) in avisFiltres"
          :key="i"
          class="group relative bg-white/90 backdrop-blur-sm rounded-2xl shadow-lg hover:shadow-2xl border border-emerald-100 hover:border-emerald-300 transition-all duration-300 overflow-hidden"
        >
          <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/0 to-teal-500/0 group-hover:from-emerald-500/5 group-hover:to-teal-500/5 transition-all duration-500"></div>
          
          <div class="relative p-6">
            <div class="flex items-start gap-4">
              <div class="relative flex-shrink-0">
                <div class="absolute inset-0 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-2xl blur opacity-50 group-hover:opacity-75 transition-opacity"></div>
                <div class="relative w-14 h-14 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-2xl flex items-center justify-center shadow-lg">
                  <span class="text-white font-black text-lg">
                    {{ a.matricule?.substring(0, 2) || "??" }}
                  </span>
                </div>
              </div>

              <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-3">
                  <span class="px-3 py-1 bg-gradient-to-r from-emerald-100 to-teal-100 text-emerald-700 font-bold text-sm rounded-lg">
                    {{ a.matricule }}
                  </span>
                </div>

                <div class="bg-gradient-to-br from-gray-50 to-emerald-50 rounded-xl p-4 mb-3 border border-emerald-100">
                  <p class="text-gray-700 font-medium leading-relaxed italic" style="font-size: 12px;">
                    "{{ a.avis }}"
                  </p>
                </div>

                <div class="flex items-center gap-2 text-sm text-gray-500">
                  <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                  </svg>
                  <span class="font-medium">{{ a.date_avis }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-else class="bg-white/80 backdrop-blur-lg rounded-2xl shadow-xl p-12 text-center border border-emerald-100">
        <div class="w-20 h-20 bg-gradient-to-br from-emerald-100 to-teal-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <svg class="w-10 h-10 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 00-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
          </svg>
        </div>
        <p class="text-gray-500 font-medium text-lg">Aucun avis trouvé avec ce filtre</p>
      </div>
    </div>

    <div
      v-if="showModal"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm transition-all duration-300"
      @click.self="showModal = false"
    >
      <div class="relative bg-white w-full max-w-2xl rounded-3xl shadow-2xl overflow-hidden transform transition-all duration-300 scale-100">
        <div class="relative bg-gradient-to-br from-emerald-500 via-teal-500 to-cyan-500 p-8">
          <div class="absolute inset-0 bg-white/10 backdrop-blur-sm"></div>
          <button
            @click="showModal = false"
            class="absolute top-4 right-4 w-10 h-10 bg-white/20 hover:bg-white/30 backdrop-blur-sm rounded-xl flex items-center justify-center transition-all duration-300 group"
          >
            <svg class="w-5 h-5 text-white group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
          <div class="relative">
            <h3 class="text-3xl font-black text-white mb-2">
              Donner votre avis
            </h3>
            <p class="text-emerald-50 font-medium">
              Votre retour est précieux pour nous
            </p>
          </div>
        </div>

        <div class="p-8">
          <div class="relative group">
            <textarea
              v-model="avis"
              placeholder="Écrivez votre avis ou suggestion..."
              class="w-full p-6 bg-gradient-to-br from-emerald-50 to-teal-50 border-2 border-emerald-200 rounded-2xl focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 transition-all duration-300 resize-none font-medium"
              rows="6"
            ></textarea>
            <div class="absolute bottom-4 right-4 text-sm font-medium text-emerald-400">
              {{ avis.length }} caractères
            </div>
          </div>

          <div class="flex justify-end gap-3 mt-6">
            <button
              @click="showModal = false"
              class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition-all duration-300"
            >
              Annuler
            </button>
            <button
              @click="envoyerAvis"
              class="group relative px-8 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-bold rounded-xl shadow-lg hover:shadow-2xl hover:scale-105 transition-all duration-300 overflow-hidden"
            >
              <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-20 transition-opacity"></div>
              <span class="relative flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Envoyer
              </span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="fixed bottom-0 left-0 right-0 bg-white/95 backdrop-blur-lg border-t border-emerald-100 px-4 py-2 md:hidden shadow-2xl">
    <div class="flex justify-around items-center max-w-md mx-auto">
      <router-link to="/" class="flex flex-col items-center group">
        <svg class="w-6 h-6 text-gray-600 group-hover:text-emerald-500 transition-colors" fill="currentColor" viewBox="0 0 24 24">
          <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
        </svg>
        <span class="text-xs text-gray-600 group-hover:text-emerald-500 mt-1 font-medium transition-colors">Accueil</span>
      </router-link>

      <router-link to="/" class="flex flex-col items-center group">
        <svg class="w-6 h-6 text-emerald-600" fill="currentColor" viewBox="0 0 24 24">
          <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
        </svg>
        <span class="text-xs text-emerald-600 mt-1 font-medium">Explorer</span>
      </router-link>

      <router-link to="/" class="flex flex-col items-center group">
        <svg class="w-6 h-6 text-gray-600 group-hover:text-emerald-500 transition-colors" fill="currentColor" viewBox="0 0 24 24">
          <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>
        </svg>
        <span class="text-xs text-gray-600 group-hover:text-emerald-500 mt-1 font-medium transition-colors">Fichiers</span>
      </router-link>

      <router-link to="/" class="flex flex-col items-center group">
        <svg class="w-6 h-6 text-gray-600 group-hover:text-emerald-500 transition-colors" fill="currentColor" viewBox="0 0 24 24">
          <path d="M3 3h18v2H3V3zm0 4h18v2H3V7zm0 4h18v2H3v-2z"/>
        </svg>
        <span class="text-xs text-gray-600 group-hover:text-emerald-500 mt-1 font-medium transition-colors">Onglets</span>
      </router-link>

      <router-link to="/" class="flex flex-col items-center relative group">
        <svg class="w-6 h-6 text-gray-600 group-hover:text-emerald-500 transition-colors" fill="currentColor" viewBox="0 0 24 24">
          <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
        </svg>
        <span class="text-xs text-gray-600 group-hover:text-emerald-500 mt-1 font-medium transition-colors">Profile</span>
        <div class="absolute -top-1 -right-1 bg-gradient-to-br from-emerald-500 to-teal-500 w-3 h-3 rounded-full animate-pulse"></div>
      </router-link>
    </div>
  </div>
</template>
