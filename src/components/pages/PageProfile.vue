<template>
  <div class="profil-page min-h-screen bg-gray-100 py-12 px-4 flex justify-center">

    <!-- Conteneur principal -->
    <div class="w-full max-w-4xl bg-white rounded-3xl shadow-xl overflow-hidden">

      <!-- HEADER PHOTO ET NOM -->
      <div class="bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 p-8 flex flex-col items-center relative">
        <div class="relative group">
          <img 
            :src="user.photo_membre || '/default-avatar.png'" 
            alt="Photo de profil" 
            class="w-36 h-36 rounded-full border-4 border-white object-cover shadow-lg"
          />
          <div 
            v-if="editing"
            @click="$refs.fileInput.click()"
            class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-25 rounded-full opacity-0 group-hover:opacity-100 cursor-pointer transition-opacity"
          >
            <span class="text-white font-semibold text-lg">Changer</span>
          </div>
        </div>
        <input type="file" ref="fileInput" @change="uploadPhoto" class="hidden"/>
        <div class="text-center mt-4">
          <h2 class="text-2xl font-bold text-white">{{ user.nom }} {{ user.prenom }}</h2>
          <p class="text-indigo-200 mt-1">
            {{ user.je_suis === 'CE_CE' ? 'Comité Exécutif' : (user.je_suis === 'SR' || user.je_suis === 'SR_SR' ? 'Secrétaire Régional' : user.je_suis) }}
          </p>
        </div>
      </div>

      <!-- INFOS DU PROFIL -->
      <div class="p-8 space-y-6">

        <!-- Boutons Modifier / Enregistrer / Annuler -->
       

        <!-- Informations personnelles -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="info-card">📇 <span class="font-medium">{{ user.matricule_gen }}</span></div>
          <div class="info-card">🧑 <span class="font-medium">
            {{ user.je_suis }}
          </span></div>
        </div>

        <!-- Poste et section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="info-card">
            🏢 
            <span v-if="!editing" class="font-medium">{{ user.souscomite_debut }}</span>
            <input v-else v-model="editableUser.souscomite_debut" class="input-style" />
          </div>
          <div class="info-card">
            📂
            <span v-if="!editing" class="font-medium">{{ user.section }}</span>
            <input v-else v-model="editableUser.section" class="input-style" />
          </div>
          <div class="info-card">
            💼
            <span v-if="!editing" class="font-medium">{{ user.poste_definition }}</span>
            <input v-else v-model="editableUser.poste_definition" class="input-style" />
          </div>
          <div class="info-card">
            📞
            <span v-if="!editing" class="font-medium">{{ user.contact }}</span>
            <input v-else v-model="editableUser.contact" class="input-style" />
          </div>
        </div>

        <!-- Champs Nom / Prénom -->
        <div v-if="editing" class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
          <input v-model="editableUser.nom" placeholder="Nom" class="input-style"/>
          <input v-model="editableUser.prenom" placeholder="Prénom" class="input-style"/>
        </div>

        <!-- Boutons Modifier / Enregistrer / Annuler -->
        <div class="flex justify-center mb-6 gap-4">
          <button v-if="!editing" @click="editing = true" class="px-10 py-3 bg-indigo-600 text-white font-semibold rounded-full shadow-lg hover:bg-indigo-700 transform transition duration-300 hover:scale-105">
            ✏️ Modifier le profil
          </button>
          <template v-else>
            <button @click="saveChanges" class="px-10 py-3 bg-indigo-600 text-white font-semibold rounded-full shadow-lg hover:bg-indigo-700 transform transition duration-300 hover:scale-105">
              💾 Enregistrer
            </button>
            <button @click="cancelEdit" class="px-10 py-3 bg-gray-400 text-white font-semibold rounded-full shadow-lg hover:bg-gray-500 transform transition duration-300 hover:scale-105">
              ❌ Annuler
            </button>
          </template>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useUserStore } from "../../stores/user";

const userStore = useUserStore()
const user = computed(() => userStore.user)

const editing = ref(false)
const editableUser = ref({ ...user.value })

const saveChanges = async () => {
  const memberId = user.value.id || user.value.id_membre
  try {
    const response = await fetch(`https://sogetrag.com/api/membres.php?action=update_membre&id=${memberId}`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        nom: editableUser.value.nom,
        prenom: editableUser.value.prenom,
        sousComite: editableUser.value.souscomite_debut,
        section: editableUser.value.section,
        poste: editableUser.value.poste_definition,
        contact: editableUser.value.contact || '',
        statut: user.value.je_suis
      })
    })
    const data = await response.json()
    alert(data.message)
    Object.assign(user.value, editableUser.value)
    editing.value = false
  } catch (error) {
    console.error('Erreur MAJ profil:', error)
  }
}

const cancelEdit = () => {
  editableUser.value = { ...user.value }
  editing.value = false
}

const fileInput = ref(null)
const uploadPhoto = async (e) => {
  const file = e.target.files[0]
  if (!file || !user.value?.id) return

  const formData = new FormData()
  formData.append('photo', file)

  try {
    const response = await fetch(`https://sogetrag.com/api/membres.php?action=upload_photo&id=${user.value.id}`, {
      method: 'POST',
      body: formData
    })
    const data = await response.json()
    if (data.success) user.value.photo_membre = data.photo_url
    else alert('Erreur: ' + data.message)
  } catch (error) {
    console.error('Erreur upload photo:', error)
  }
}
</script>

<style scoped>
.info-card {
  background-color: #f9fafb;
  padding: 1rem;
  border-radius: 1rem;
  box-shadow: 0 2px 6px rgba(0,0,0,0.05);
  font-weight: 500;
  color: #1f2937;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: transform 0.2s, box-shadow 0.2s;
}
.info-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.input-style {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 1px solid #d1d5db;
  border-radius: 0.75rem;
  background-color: #f9fafb;
  transition: all 0.3s;
}
.input-style:focus {
  border-color: #6366f1;
  box-shadow: 0 0 0 2px rgba(99,102,241,0.2);
  outline: none;
}
</style>
