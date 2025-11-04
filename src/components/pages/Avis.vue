<script setup>
import { ref, computed, onMounted } from "vue";
import axios from "axios";
import { useUserStore } from "../../stores/user";

const userStore = useUserStore ? useUserStore() : { matricule: null };

const showModal = ref(false);
const avis = ref("");
const avisList = ref([]);
const filtre = ref(""); // 🔍 filtre texte
const tri = ref("recent"); // ⚡ critère de tri

// Charger les avis
const chargerAvis = async () => {
  try {
    let response;
    if (userStore.user.je_suis == "CE_CE") {
      response = await axios.get(
        "http://sogetrag.com/apistage/avis.php?action=avis"
      );
    } else {
      response = await axios.get(
        "http://sogetrag.com/apistage/avis.php?action=avis_user&matricule=" +
          userStore.user.matricule_gen
      );
    }
    avisList.value = response.data.data || [];
  } catch (error) {
    console.error("Erreur chargement avis :", error);
  }
};

// Avis filtrés et triés
const avisFiltres = computed(() => {
  let result = avisList.value;

  // 🔍 Filtrage
  if (filtre.value) {
    result = result.filter(
      (a) =>
        a.matricule?.toLowerCase().includes(filtre.value.toLowerCase()) ||
        a.avis?.toLowerCase().includes(filtre.value.toLowerCase()) ||
        a.date_avis?.toLowerCase().includes(filtre.value.toLowerCase())
    );
  }

  // 🔄 Tri
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

// Envoyer un avis
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
    alert("❌ Erreur lors de l’envoi");
  }
};

onMounted(chargerAvis);
</script>

<template>
  <div class="max-w-3xl mx-auto p-6" style="margin-bottom: 50px;">
    <!-- Titre + bouton -->
    <div class="flex items-center justify-between mb-6">
      <h2 class="text-3xl font-extrabold text-gray-800">📋 Avis et suggestions</h2>
      <button
        @click="showModal = true"
        class="px-5 py-2.5 bg-green-600 text-white font-semibold rounded-lg shadow-md hover:bg-green-700 transition"
      >
        ➕ Donner un avis
      </button>
    </div>

    <!-- Barre de recherche et tri -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
      <!-- Recherche -->
      <input
        v-model="filtre"
        type="text"
        placeholder="🔍 Filtrer par matricule, texte ou date..."
        class="flex-1 p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
      />

      <!-- Sélecteur de tri -->
      <select
        v-model="tri"
        class="p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
      >
        <option value="recent">⬆ Plus récents</option>
        <option value="ancien">⬇ Plus anciens</option>
        <option value="matricule">🔠 Par matricule</option>
      </select>
    </div>

    <!-- Liste des avis -->
    <div v-if="avisFiltres.length" class="space-y-4 relative">
      <div
        v-for="(a, i) in avisFiltres"
        :key="i"
        class="relative p-5 bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition"
      >
        <div class="flex items-start space-x-3">
          <div
            class="flex items-center justify-center w-10 h-10 rounded-full bg-green-100 text-green-700 font-bold"
          >
            {{ a.matricule?.substring(0, 2) || "??" }}
          </div>
          <div>
            <p class="text-gray-800">
              <strong class="text-green-700">{{ a.matricule }}</strong>
              <span class="ml-2 text-gray-700">a écrit :</span>
            </p>
            <p class="mt-2 text-gray-600 italic">“{{ a.avis }}”</p>
            <p class="mt-2 text-sm text-gray-400">📅 {{ a.date_avis }}</p>
          </div>
        </div>
      </div>
    </div>

    <p v-else class="text-gray-500 text-center mt-10">
      Aucun avis trouvé avec ce filtre ❌
    </p>

    <!-- Modal -->
    <div
      v-if="showModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 transition"
      @click.self="showModal = false"
    >
      <div
        class="relative bg-white w-full max-w-lg p-6 rounded-2xl shadow-lg transform transition-all scale-95"
      >
        <!-- Bouton fermer -->
        <button
          @click="showModal = false"
          class="absolute top-3 right-3 text-gray-500 hover:text-gray-800"
        >
          ✖
        </button>

        <h3 class="text-2xl font-bold mb-5 text-center text-blue-700">
          ✍ Donner un avis
        </h3>

        <textarea
          v-model="avis"
          placeholder="Écrivez votre avis..."
          class="w-full p-4 border rounded-lg mb-4 focus:outline-none focus:ring-2 focus:ring-blue-500"
          rows="5"
        ></textarea>

        <div class="flex justify-end space-x-3">
          <button
            @click="showModal = false"
            class="px-4 py-2 bg-gray-400 hover:bg-gray-500 text-white rounded-lg transition"
          >
            Annuler
          </button>
          <button
            @click="envoyerAvis"
            class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow-md transition"
          >
            ✅ Envoyer
          </button>
        </div>
      </div>
    </div>
  </div>
   <div class="fixed bottom-0 left-0 right-0 bg-white border-t px-4 py-2 md:hidden">
    <div class="flex justify-around items-center max-w-md mx-auto">
      
      <router-link to="/" class="flex flex-col items-center">
        <svg class="w-6 h-6 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
          <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
        </svg>
        <span class="text-xs text-gray-600 mt-1 font-medium">Accueil</span>
      </router-link>

      <router-link to="/" class="flex flex-col items-center">
        <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
          <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
        </svg>
        <span class="text-xs text-blue-600 mt-1">Explorer</span>
      </router-link>

      <router-link to="/" class="flex flex-col items-center">
        <svg class="w-6 h-6 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
          <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>
        </svg>
        <span class="text-xs text-gray-600 mt-1">Fichiers</span>
      </router-link>

      <router-link to="/" class="flex flex-col items-center">
        <svg class="w-6 h-6 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
          <path d="M3 3h18v2H3V3zm0 4h18v2H3V7zm0 4h18v2H3v-2z"/>
        </svg>
        <span class="text-xs text-gray-600 mt-1">Onglets</span>
      </router-link>

      <router-link to="/" class="flex flex-col items-center relative">
        <svg class="w-6 h-6 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
          <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
        </svg>
        <span class="text-xs text-gray-600 mt-1">Profile</span>
        <div class="absolute -top-1 -right-1 bg-red-500 w-3 h-3 rounded-full"></div>
      </router-link>

    </div>
  </div>
</template>