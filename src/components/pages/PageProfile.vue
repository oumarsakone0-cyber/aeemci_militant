<template>
  <div class="profil-page min-h-screen bg-gradient-to-br from-green-50 via-white to-emerald-50 py-12 px-4 flex justify-center">
    <div class="w-full max-w-4xl bg-white rounded-3xl shadow-2xl overflow-hidden border border-emerald-100">

      <!-- HEADER -->
      <div class="bg-gradient-to-r from-green-600 via-emerald-500 to-green-400 p-8 flex flex-col items-center relative">
        <div class="relative group">
          <img 
            :src="user.photo_membre || '/default-avatar.png'" 
            alt="Photo de profil" 
            class="w-36 h-36 rounded-full border-4 border-white object-cover shadow-lg"
          />
          <!-- Icône caméra visible uniquement quand on édite -->
          <div
            v-if="editing"
            @click="$refs.fileInput.click(handleUploadPhotoRequest)"
            class="absolute bottom-0 right-0 bg-white p-2 rounded-full cursor-pointer shadow-md hover:bg-emerald-100 transition"
          >
         
            <i class="bx bx-camera text-green-600 text-xl"></i>
             changer la photo
          </div>
        </div>

        <input type="file" ref="fileInput" @change="uploadPhoto" class="hidden" />

        <div class="text-center mt-4">
          <h2 class="text-2xl font-bold text-white drop-shadow-sm">
            {{ user.nom }} {{ user.prenom }}
          </h2>
          <p class="text-green-100 mt-1 font-medium">
            {{ user.je_suis === 'CE_CE' ? 'Comité Exécutif' : (user.je_suis === 'SR' || user.je_suis === 'SR_SR' ? 'Secrétaire Régional' : user.je_suis) }}
          </p>
        </div>
      </div>

      <!-- CONTENU -->
      <div class="p-8 space-y-6">

        <!-- Infos principales -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="info-card">📇 <span class="font-medium">{{ user.matricule_gen }}</span></div>
          <div class="info-card">🧑 <span class="font-medium">{{ user.je_suis }}</span></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="info-card">🏢 <span class="font-medium">{{ user.souscomite_debut }}</span></div>
          <div class="info-card">📂 <span class="font-medium">{{ user.section }}</span></div>
          <div class="info-card">💼 <span class="font-medium">{{ user.poste_definition }}</span></div>

          <!-- Champ modifiable -->
          <div class="info-card">
            📞
            <span v-if="!editing" class="font-medium">{{ user.contact }}</span>
            <input
              v-else
              v-model="editableUser.contact"
              class="input-style"
              placeholder="Contact"
            />
          </div>
        </div>

        <!-- Boutons -->
        <div class="flex justify-center mt-8 gap-4">
          <button
            v-if="!editing"
            @click="startEdit"
            class="btn-green"
          >
            ✏️ Modifier
          </button>

          <template v-else>
            <button
              @click="handleUpdateContactRequest"
              class="btn-green-dark"
            >
              💾 Enregistrer
            </button>
            <button
              @click="cancelEdit"
              class="btn-cancel"
            >
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
import { useUserStore } from "../../stores/user"

const userStore = useUserStore()
const user = computed(() => userStore.user)

const editing = ref(false)
const editableUser = ref({ ...user.value })

const startEdit = () => {
  editableUser.value.contact = user.value.contact
  editing.value = true
}

const handleUpdateContactRequest = async () => {
  const memberId = user.value.id || user.value.id_membre
  try {
    const response = await fetch(`https://sogetrag.com/api/membres.php?action=update_contact&id=${memberId}`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ contact: editableUser.value.contact })
    })
    const data = await response.json()
    alert(data.message || 'Mise à jour effectuée avec succès ✅')
    user.value.contact = editableUser.value.contact
    editing.value = false
  } catch (error) {
    console.error('Erreur MAJ contact:', error)
  }
}

const cancelEdit = () => {
  editableUser.value.contact = user.value.contact
  editing.value = false
}

const fileInput = ref(null)
const handleUploadPhotoRequest = async (e) => {
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
    if (data.success) {
      user.value.photo_membre = data.photo_url
    } else {
      alert('Erreur: ' + data.message)
    }
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
  border: 1px solid #a7f3d0;
  border-radius: 0.75rem;
  background-color: #ecfdf5;
  transition: all 0.3s;
}
.input-style:focus {
  border-color: #10b981;
  box-shadow: 0 0 0 2px rgba(16,185,129,0.2);
  outline: none;
}
.btn-green {
  padding: 0.75rem 2rem;
  background: linear-gradient(to right, #419075, #2f715c);
  color: white;
  font-weight: 600;
  border-radius: 9999px;
  box-shadow: 0 4px 10px rgba(16,185,129,0.3);
  transition: all 0.3s ease;
}
.btn-green:hover {
  transform: scale(1.05);
  box-shadow: 0 6px 14px rgba(5,150,105,0.4);
}
.btn-green-dark {
  padding: 0.75rem 2rem;
  background: linear-gradient(to right, #047857, #46c19e);
  color: white;
  font-weight: 600;
  border-radius: 9999px;
  box-shadow: 0 4px 10px rgba(4,120,87,0.3);
  transition: all 0.3s ease;
}
.btn-green-dark:hover {
  transform: scale(1.05);
  box-shadow: 0 6px 14px rgba(4,120,87,0.4);
}
.btn-cancel {
  padding: 0.75rem 2rem;
  background: #e5e7eb;
  color: #374151;
  font-weight: 600;
  border-radius: 9999px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
  transition: all 0.3s;
}
.btn-cancel:hover {
  background: #d1d5db;
  transform: scale(1.05);
}
</style>
