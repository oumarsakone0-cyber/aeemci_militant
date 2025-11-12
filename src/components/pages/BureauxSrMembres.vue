<template>
  <div class="min-h-screen bg-gradient-to-br from-emerald-50 via-teal-50 to-green-50 relative overflow-hidden" style="padding-bottom: 80px;">
    <!-- Decorative background elements -->
    <div class="absolute top-0 left-0 w-96 h-96 bg-emerald-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
    <div class="absolute top-0 right-0 w-96 h-96 bg-teal-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
    <div class="absolute bottom-0 left-1/2 w-96 h-96 bg-green-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-4000"></div>

    <!-- Header -->
    <header class="relative backdrop-blur-sm bg-white/80 shadow-lg border-b border-emerald-200/50">
      <div class="max-w-7xl mx-auto px-4 lg:px-8 py-4">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <button
              @click="$router.push('/bureaux_sr')"
              class="p-2 text-gray-600 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all"
              title="Retour"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
              </svg>
            </button>
            <div>
              <h1 class="text-2xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
                Membres du bureau: {{ bureau?.nom_bureau || 'Chargement...' }}
              </h1>
              <p class="text-sm text-gray-600 mt-1">{{ bureau?.description || '' }}</p>
            </div>
          </div>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="relative z-10 max-w-7xl mx-auto px-4 lg:px-8 py-8">
      <div v-if="isLoading" class="text-center py-16">
        <div class="inline-block animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-emerald-600"></div>
        <p class="mt-4 text-gray-600">Chargement des membres...</p>
      </div>

      <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-lg p-6 text-center">
        <p class="text-red-600">{{ error }}</p>
        <button
          @click="loadMembres"
          class="mt-4 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-all"
        >
          Réessayer
        </button>
      </div>

      <div v-else-if="membres && membres.length > 0" class="bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Table Header -->
        <div class="bg-emerald-50 px-6 py-4 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">
              Liste des membres ({{ membres.length }})
            </h2>
            <button
              @click="openAddMembreModal"
              class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-all flex items-center gap-2"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
              </svg>
              Ajouter un membre
            </button>
          </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
          <table class="w-full border-collapse">
            <thead>
              <tr class="bg-gray-50">
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase border-b border-gray-200">Nom complet</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase border-b border-gray-200">Matricule</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase border-b border-gray-200">Poste dans le bureau</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase border-b border-gray-200">Téléphone</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase border-b border-gray-200">Email</th>
                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase border-b border-gray-200">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="membre in membres"
                :key="membre.id"
                class="hover:bg-gray-50 transition-colors"
              >
                <td class="px-6 py-4 border-b border-gray-100">
                  <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center flex-shrink-0">
                      <span class="text-emerald-600 font-bold">{{ membre.nom_complet?.charAt(0)?.toUpperCase() || 'M' }}</span>
                    </div>
                    <span class="font-medium text-gray-900">{{ membre.nom_complet || 'N/A' }}</span>
                  </div>
                </td>
                <td class="px-6 py-4 border-b border-gray-100 font-mono text-sm text-gray-600">{{ membre.matricule_membre }}</td>
                <td class="px-6 py-4 border-b border-gray-100">
                  <div class="flex items-center gap-2">
                    <span v-if="membre.poste" class="inline-flex items-center px-3 py-1 rounded text-sm font-medium bg-emerald-100 text-emerald-700">
                      {{ membre.poste }}
                    </span>
                    <button
                      @click="openPosteMenu(membre.id)"
                      class="p-1.5 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded transition-all"
                      title="Modifier le poste"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                      </svg>
                    </button>
                  </div>
                </td>
                <td class="px-6 py-4 border-b border-gray-100 text-sm text-gray-600">{{ membre.telephone || '-' }}</td>
                <td class="px-6 py-4 border-b border-gray-100 text-sm text-gray-600">{{ membre.email || '-' }}</td>
                <td class="px-6 py-4 border-b border-gray-100 text-center">
                  <button
                    @click="confirmRemoveMembre(membre.id, membre.nom_complet)"
                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-all"
                    title="Retirer du bureau"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div v-else class="bg-white rounded-xl shadow-lg p-12 text-center">
        <div class="w-20 h-20 bg-gradient-to-br from-emerald-100 to-teal-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <svg class="w-10 h-10 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
          </svg>
        </div>
        <h3 class="text-lg font-semibold text-gray-900 mb-2">Aucun membre</h3>
        <p class="text-gray-600 mb-6">Ce bureau n'a pas encore de membres.</p>
        <button
          @click="openAddMembreModal"
          class="px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-xl hover:from-emerald-700 hover:to-teal-700 transition-all"
        >
          Ajouter le premier membre
        </button>
      </div>
    </main>

    <!-- Modal: Menu pour modifier le poste -->
    <div
      v-if="editingPosteId"
      class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4"
      @click="cancelEditPoste"
    >
      <div
        class="backdrop-blur-xl bg-white/95 rounded-2xl shadow-2xl max-w-md w-full p-6"
        @click.stop
      >
        <h3 class="text-xl font-bold text-gray-900 mb-4">Modifier le poste</h3>
        <div class="mb-4">
          <label class="text-sm font-medium text-gray-700 mb-2 block">Sélectionner ou saisir un poste</label>
          <!-- Mode édition poste personnalisé -->
          <input
            v-if="editingPosteValue !== null && editingPosteValue !== ''"
            v-model="editingPosteValue"
            @keyup.enter="saveCustomPoste"
            @keyup.esc="cancelEditPoste"
            type="text"
            class="w-full px-4 py-2 border-2 border-emerald-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm mb-2"
            placeholder="Entrez le poste personnalisé"
            maxlength="255"
            autofocus
          />
          <!-- Dropdown pour sélectionner/modifier le poste -->
          <select
            v-else
            :value="getCurrentMembrePoste() || ''"
            @change="handlePosteChange($event.target.value)"
            class="w-full px-4 py-2 border-2 border-emerald-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm"
          >
            <option value="" v-if="!getCurrentMembrePoste()">-- Sélectionner --</option>
            <option v-if="getCurrentMembrePoste() && !isPosteInList(getCurrentMembrePoste())" :value="getCurrentMembrePoste()">{{ getCurrentMembrePoste() }}</option>
            <option value="Secrétaire Régional">Secrétaire Régional</option>
            <option value="Vice-Secrétaire Régional">Vice-Secrétaire Régional</option>
            <option value="Trésorier Régional">Trésorier Régional</option>
            <option value="Secrétaire à l'Organisation">Secrétaire à l'Organisation</option>
            <option value="Secrétaire aux Relations Extérieures">Secrétaire aux Relations Extérieures</option>
            <option value="Secrétaire à la Communication">Secrétaire à la Communication</option>
            <option value="Secrétaire à l'Éducation">Secrétaire à l'Éducation</option>
            <option value="Secrétaire aux Activités">Secrétaire aux Activités</option>
            <option value="Secrétaire à la Culture">Secrétaire à la Culture</option>
            <option value="Secrétaire aux Sports">Secrétaire aux Sports</option>
            <option value="Membre du Bureau">Membre du Bureau</option>
            <option value="__CUSTOM__">➕ Poste personnalisé...</option>
          </select>
        </div>
        <div class="flex gap-3 justify-end">
          <button
            @click="cancelEditPoste"
            class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-all"
          >
            Annuler
          </button>
          <button
            v-if="editingPosteValue !== null && editingPosteValue !== ''"
            @click="saveCustomPoste"
            class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-all"
          >
            Enregistrer
          </button>
        </div>
      </div>
    </div>

    <!-- Modal: Confirmation retrait membre -->
    <div
      v-if="showRemoveMembreModal"
      class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4"
      @click="showRemoveMembreModal = false"
    >
      <div class="backdrop-blur-xl bg-white/95 rounded-2xl shadow-2xl max-w-md w-full p-6" @click.stop>
        <h3 class="text-xl font-bold text-gray-900 mb-4">Retirer le membre</h3>
        <p class="text-gray-700 mb-6">Êtes-vous sûr de vouloir retirer "{{ removeMembreName }}" de ce bureau ?</p>
        <div class="flex gap-3 justify-end">
          <button
            @click="showRemoveMembreModal = false"
            class="px-6 py-3 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all font-medium"
          >
            Annuler
          </button>
          <button
            @click="removeMembre"
            class="px-6 py-3 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-all font-medium"
          >
            Retirer
          </button>
        </div>
      </div>
    </div>

    <!-- Notifications -->
    <div class="fixed top-4 right-4 z-50 space-y-2">
      <div
        v-for="(notif, index) in notifications"
        :key="index"
        :class="[
          'px-6 py-4 rounded-xl shadow-lg backdrop-blur-sm animate-slideIn',
          notif.type === 'success' ? 'bg-emerald-500 text-white' : 'bg-red-500 text-white'
        ]"
      >
        {{ notif.message }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { getCurrentUserMatricule } from '../../utils/database.js'

const route = useRoute()
const router = useRouter()
const BUREAUX_API_URL = 'https://sogetrag.com/apistage/bureaux_sr_api.php'

// États
const isLoading = ref(true)
const error = ref('')
const bureau = ref(null)
const membres = ref([])

// Modals
const showRemoveMembreModal = ref(false)
const removeMembreId = ref(null)
const removeMembreName = ref('')

// Édition de poste
const editingPosteId = ref(null)
const editingPosteValue = ref(null)

// Notifications
const notifications = ref([])

// Liste des postes standards
const postesStandards = [
  'Secrétaire Régional',
  'Vice-Secrétaire Régional',
  'Trésorier Régional',
  'Secrétaire à l\'Organisation',
  'Secrétaire aux Relations Extérieures',
  'Secrétaire à la Communication',
  'Secrétaire à l\'Éducation',
  'Secrétaire aux Activités',
  'Secrétaire à la Culture',
  'Secrétaire aux Sports',
  'Membre du Bureau'
]

// Vérifier si un poste est dans la liste standard
const isPosteInList = (poste) => {
  return postesStandards.includes(poste)
}

// Obtenir le poste actuel du membre en cours d'édition
const getCurrentMembrePoste = () => {
  if (!editingPosteId.value) return ''
  const membre = membres.value.find(m => m.id === editingPosteId.value)
  return membre?.poste || ''
}

// Charger les membres du bureau
const loadMembres = async () => {
  const bureauId = route.params.id
  if (!bureauId) {
    error.value = 'ID du bureau manquant'
    isLoading.value = false
    return
  }

  isLoading.value = true
  error.value = ''
  
  try {
    const matricule = getCurrentUserMatricule()
    const response = await fetch(`${BUREAUX_API_URL}?action=get_bureaux&matricule=${matricule}`)
    const result = await response.json()

    if (result.success && result.data) {
      const foundBureau = result.data.find(b => b.id == bureauId)
      if (foundBureau) {
        bureau.value = foundBureau
        membres.value = foundBureau.membres || []
      } else {
        error.value = 'Bureau non trouvé'
      }
    } else {
      error.value = result.error || 'Erreur lors du chargement'
    }
  } catch (err) {
    console.error('Erreur chargement membres:', err)
    error.value = 'Erreur réseau'
  } finally {
    isLoading.value = false
  }
}

// Ouvrir le menu pour modifier le poste
const openPosteMenu = (membreId) => {
  const membre = membres.value.find(m => m.id === membreId)
  editingPosteId.value = membreId
  editingPosteValue.value = null
}

// Gérer le changement de poste
const handlePosteChange = (nouveauPoste) => {
  if (nouveauPoste === '__CUSTOM__') {
    const membre = membres.value.find(m => m.id === editingPosteId.value)
    editingPosteValue.value = membre?.poste || ''
  } else if (nouveauPoste && nouveauPoste !== '') {
    updateMembrePoste(nouveauPoste)
  }
}

// Sauvegarder le poste personnalisé
const saveCustomPoste = async () => {
  if (!editingPosteValue.value || !editingPosteValue.value.trim()) {
    cancelEditPoste()
    return
  }
  
  await updateMembrePoste(editingPosteValue.value.trim())
}

// Annuler l'édition du poste
const cancelEditPoste = () => {
  editingPosteId.value = null
  editingPosteValue.value = null
}

// Modifier le poste d'un membre
const updateMembrePoste = async (nouveauPoste) => {
  if (!nouveauPoste || !editingPosteId.value) return

  try {
    const matricule = getCurrentUserMatricule()
    const response = await fetch(BUREAUX_API_URL, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        action: 'update_membre_poste',
        membre_id: editingPosteId.value,
        poste: nouveauPoste,
        matricule_responsable: matricule
      })
    })

    const result = await response.json()

    if (result.success) {
      addNotification('Poste modifié avec succès', 'success')
      // Mettre à jour le membre dans la liste
      const membre = membres.value.find(m => m.id === editingPosteId.value)
      if (membre) {
        membre.poste = nouveauPoste
      }
      cancelEditPoste()
    } else {
      addNotification(result.error || 'Erreur lors de la modification', 'error')
    }
  } catch (error) {
    console.error('Erreur modification poste:', error)
    addNotification('Erreur réseau', 'error')
  }
}

// Confirmer le retrait d'un membre
const confirmRemoveMembre = (membreId, nom) => {
  removeMembreId.value = membreId
  removeMembreName.value = nom
  showRemoveMembreModal.value = true
}

// Retirer un membre
const removeMembre = async () => {
  if (!removeMembreId.value) return

  try {
    const matricule = getCurrentUserMatricule()
    const response = await fetch(BUREAUX_API_URL, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        action: 'remove_membre',
        membre_id: removeMembreId.value,
        matricule_responsable: matricule
      })
    })

    const result = await response.json()

    if (result.success) {
      addNotification('Membre retiré avec succès', 'success')
      // Retirer le membre de la liste
      membres.value = membres.value.filter(m => m.id !== removeMembreId.value)
      showRemoveMembreModal.value = false
      removeMembreId.value = null
      removeMembreName.value = ''
    } else {
      addNotification(result.error || 'Erreur lors du retrait', 'error')
    }
  } catch (error) {
    console.error('Erreur retrait membre:', error)
    addNotification('Erreur réseau', 'error')
  }
}

// Ouvrir le modal d'ajout de membre (rediriger vers la page bureaux_sr)
const openAddMembreModal = () => {
  router.push(`/bureaux_sr?bureau_id=${route.params.id}`)
}

// Notifications
const addNotification = (message, type = 'success') => {
  notifications.value.push({ message, type })
  setTimeout(() => {
    notifications.value.shift()
  }, 3000)
}

// Initialisation
onMounted(() => {
  loadMembres()
})
</script>

<style scoped>
@keyframes blob {
  0%, 100% {
    transform: translate(0, 0) scale(1);
  }
  33% {
    transform: translate(30px, -50px) scale(1.1);
  }
  66% {
    transform: translate(-20px, 20px) scale(0.9);
  }
}

.animate-blob {
  animation: blob 7s infinite;
}

.animation-delay-2000 {
  animation-delay: 2s;
}

.animation-delay-4000 {
  animation-delay: 4s;
}

@keyframes slideIn {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

.animate-slideIn {
  animation: slideIn 0.3s ease-out;
}
</style>


