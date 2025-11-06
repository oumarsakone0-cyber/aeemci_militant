<template>
  <div class="min-h-screen bg-gradient-to-br from-green-50 via-sky-50 to-green-50 relative overflow-hidden pb-24 md:pb-8">
    <!-- Décorations -->
    <div class="absolute top-0 left-0 w-96 h-96 bg-green-400/20 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2"></div>
    <div class="absolute bottom-0 right-0 w-96 h-96 bg-sky-400/20 rounded-full blur-3xl translate-x-1/2 translate-y-1/2"></div>
    <div class="absolute top-1/2 left-1/2 w-96 h-96 bg-green-400/10 rounded-full blur-3xl"></div>

    <!-- HEADER -->
    <header class="relative backdrop-blur-sm bg-white/80 shadow-lg border-b border-green-200/50 sticky top-0 z-40">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-6 flex justify-between items-center flex-wrap gap-4">
        <div class="flex items-center space-x-3">
          <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center shadow-lg shadow-green-500/30">
            <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 4a8 8 0 00-8 8v5a1 1 0 001 1h2v-4H6v-2a6 6 0 0112 0v2h-1v4h2a1 1 0 001-1v-5a8 8 0 00-8-8z" />
              <path d="M9 19h6v-6H9v6z" />
            </svg>
          </div>
          <div>
            <h1 class="text-xl md:text-2xl font-bold bg-gradient-to-r from-green-600 to-green-600 bg-clip-text text-transparent">
              Documents Officiels AEEMCI
            </h1>
            <p class="text-xs md:text-sm text-gray-600">PAF, Règlements, Statuts, Décisions…</p>
          </div>
        </div>

        <button
          @click="showAddModal = true"
          class="px-3 md:px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl hover:shadow-lg hover:shadow-green-500/50 transition-all duration-300 flex items-center space-x-2 text-sm md:text-base active:scale-95"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
          </svg>
          <span>Ajouter</span>
        </button>
      </div>
    </header>

    <!-- CONTENU PRINCIPAL -->
    <main class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-8">
      <!-- Barre de recherche -->
      <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl border border-green-100/50 p-4 md:p-6 mb-6 md:mb-8">
        <div class="flex flex-col lg:flex-row gap-3 md:gap-4">
          <div class="flex-1 relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input
              v-model="searchQuery"
              @input="filterDocs"
              type="text"
              placeholder="Rechercher un document..."
              class="w-full pl-10 pr-4 py-3 border-2 border-green-100 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all bg-white/50"
            />
          </div>
        </div>
      </div>

            
       <!-- Grille des documents -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <div
                v-for="doc in filteredDocs"
                :key="doc.id"
                class="bg-white/90 rounded-2xl border border-green-100 shadow-lg hover:-translate-y-1 hover:shadow-2xl transition-all duration-300 cursor-pointer overflow-hidden flex"
                @click="openDoc(doc)"
            >
                <!-- Image à gauche -->
                <div class="w-28 h-30 flex-shrink-0 overflow-hidden rounded-l-2xl bg-gray-50 flex items-center justify-center">
                  <img
                    :src="doc.image_url || 'https://upload.wikimedia.org/wikipedia/fr/4/42/Logo_AEEMCI.jpeg'"
                    alt="Image document"
                    class="max-w-full max-h-full object-contain"
                  />
                </div>


                <!-- Contenu à droite -->
                <div class="p-4 flex flex-col justify-between flex-1">
                <div>
                    <h3 class="font-bold text-gray-900 line-clamp-2 group-hover:text-green-600 transition-colors">
                    {{ doc.titre }}
                    </h3>
                    <p class="text-sm text-gray-700 mt-1 line-clamp-3">{{ doc.description  }}</p>
                </div>

                <div class="flex flex-col text-sm text-gray-600 space-y-1 mt-2">
                    <p>Ajouter le  {{ formatDate(doc.date) }}</p>
                    
                </div>
                </div>
            </div>
        </div>




      <!-- État vide -->
      <div v-if="filteredDocs.length === 0" class="text-center py-20">
        <div class="bg-white/80 p-8 rounded-2xl shadow-xl max-w-md mx-auto border border-green-100">
          <svg class="w-16 h-16 text-green-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
          </svg>
          <h3 class="text-lg font-semibold text-gray-900">Aucun document trouvé</h3>
          <p class="text-gray-500 text-sm">Essayez une autre recherche ou ajoutez un nouveau document.</p>
        </div>
      </div>
    </main>

    <!-- MODALE AJOUT -->
    <div
    v-if="showAddModal"
    class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 z-50"
    @click="closeModal"
    >
    <div
        class="bg-white/95 backdrop-blur-md rounded-3xl shadow-2xl max-w-md w-full border border-green-200 flex flex-col max-h-[70vh]"
        @click.stop
    >
        <!-- Header toujours visible -->
        <div class="flex items-center justify-between p-6 border-b sticky top-0 bg-white/95 z-10 rounded-t-3xl">
        <h2 class="text-xl font-bold bg-gradient-to-r from-green-600 to-green-600 bg-clip-text text-transparent">
            Ajouter un document
        </h2>
        <button @click="closeModal" class="text-gray-400 hover:text-gray-600 text-lg">✕</button>
        </div>

        <!-- Contenu scrollable -->
        <div class="p-6 overflow-y-auto space-y-4">
        <!-- Champs utilisateur -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Nom</label>
            <input type="text" :value="addDocForm.ajout_par_nom" disabled
            class="w-full px-4 py-3 border-2 border-green-100 rounded-xl bg-gray-100" />
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Prénom</label>
            <input type="text" :value="addDocForm.ajout_par_prenom" disabled
            class="w-full px-4 py-3 border-2 border-green-100 rounded-xl bg-gray-100" />
        </div>

        <!-- Titre -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Titre du document</label>
            <input v-model="addDocForm.titre" type="text" required
            class="w-full px-4 py-3 border-2 border-green-100 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500" />
        </div>

        <!-- Image -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Image</label>
            <input @change="handleImageUpload" type="file" accept="image/*"
            class="w-full px-4 py-3 border-2 border-green-100 rounded-xl" />
            <div v-if="imagePreview" class="mt-2 text-xs text-gray-600">
            {{ imagePreview.name }} ({{ imagePreview.size }})
            </div>
            <div v-if="isUploadingImage" class="text-green-500 text-xs mt-1">Upload image en cours...</div>
        </div>

        <!-- Fichier PDF -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Fichier PDF</label>
            <input @change="handlePdfUpload" type="file" accept=".pdf" required
            class="w-full px-4 py-3 border-2 border-green-100 rounded-xl" />
            <div v-if="pdfPreview" class="mt-2 text-xs text-gray-600">
            {{ pdfPreview.name }} ({{ pdfPreview.size }})
            </div>
            <div v-if="isUploadingPdf" class="text-green-500 text-xs mt-1">Upload PDF en cours...</div>
        </div>

        <!-- Bouton -->
        <div class="flex justify-end pt-4">
            <button :disabled="isSubmitting" type="button" @click="submitDoc"
            class="px-5 py-2 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl font-semibold hover:shadow-lg active:scale-95 transition-all">
            {{ isSubmitting ? 'Enregistrement...' : 'Enregistrer' }}
            </button>
        </div>
        </div>
    </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue"
import { useUserStore } from "../../stores/user"
import { uploadToCloudinary } from '../../utils/cloudinary.js'

const API_BASE_URL = "https://sogetrag.com/api/documents.php"
const userStore = useUserStore()

const docs = ref([])
const searchQuery = ref("")
const showAddModal = ref(false)
const pdfPreview = ref(null)
const imagePreview = ref(null)
const isUploadingPdf = ref(false)
const isUploadingImage = ref(false)
const isSubmitting = ref(false)

const addDocForm = ref({
  titre: "",
  description:`Document ajouté par ${userStore.user?.nom || ''} ${userStore.user?.prenom || ''}`,
  image_url: "",
  fichier: null,
  fichier_url: "",
  date: "",
  ajout_par_nom: userStore.user?.nom || '',
  ajout_par_prenom: userStore.user?.prenom || "Prénom Inconnu", // nouveau
  ajout_par_matricule: userStore.user?.matricule_gen ||''
});

const loadDocs = async () => {
  try {
    const res = await fetch(`${API_BASE_URL}?action=list`)
    const data = await res.json()
    if (data.success) {
      docs.value = data.data.map(d => ({
        ...d,
        fichier_url: d.fichier_url || "https://upload.wikimedia.org/wikipedia/fr/4/42/Logo_AEEMCI.jpeg",
        image_url: d.image_url || "https://upload.wikimedia.org/wikipedia/fr/4/42/Logo_AEEMCI.jpeg"
      }))
    }
  } catch (e) { console.error(e) }
}

const filteredDocs = computed(() => {
  return docs.value.filter(d =>
    (!searchQuery.value || d.titre.toLowerCase().includes(searchQuery.value.toLowerCase())) 
   
  )
})

const filterDocs = () => {}

const handlePdfUpload = async (event) => {
  const file = event.target.files[0]
  if (!file) return
  isUploadingPdf.value = true
  pdfPreview.value = { name: file.name, size: (file.size / 1024 / 1024).toFixed(2) + " MB" }

  try {
    const result = await uploadToCloudinary(file, 'pdf')
    if (result.success) {
      addDocForm.value.fichier = file
      addDocForm.value.fichier_url = result.url
    } else {
      alert(result.error)
      addDocForm.value.fichier = null
      addDocForm.value.fichier_url = ""
      pdfPreview.value = null
    }
  } catch (e) { console.error(e) }
  finally { isUploadingPdf.value = false }
}

const handleImageUpload = async (event) => {
  const file = event.target.files[0]
  if (!file) return
  isUploadingImage.value = true
  imagePreview.value = { name: file.name, size: (file.size / 1024 / 1024).toFixed(2) + " MB" }

  try {
    const result = await uploadToCloudinary(file, 'image')
    if (result.success) addDocForm.value.image_url = result.url
    else {
      alert(result.error)
      addDocForm.value.image_url = ""
      imagePreview.value = null
    }
  } catch (e) { console.error(e) }
  finally { isUploadingImage.value = false }
}


const submitDoc = async () => {
  if (!addDocForm.value.titre ||  !addDocForm.value.fichier_url) return;

  // Remplissage automatique du matricule et nom
  const payload = {
  titre: addDocForm.value.titre,
  description: addDocForm.value.description,
  image_url: addDocForm.value.image_url,
  fichier_url: addDocForm.value.fichier_url,
  ajout_par_nom: `${userStore.user?.nom || ''} ${userStore.user?.prenom || ''}`,
  ajout_par_matricule: userStore.user?.matricule_gen ||''
 };


  isSubmitting.value = true;

  try {
    const res = await fetch(`${API_BASE_URL}?action=add`, {
      method: 'POST',
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload)
    });

    const data = await res.json();
    if (data.success) {
      alert("Document ajouté avec succès !");
      addDocForm.value = { titre: "",fichier: null, fichier_url: "" };
      pdfPreview.value = null;
      await loadDocs();
      showAddModal.value = false;
    } else {
      alert("Erreur: " + data.error);
    }
  } catch (e) {
    console.error(e);
    alert("Erreur lors de l'ajout du document");
  } finally {
    isSubmitting.value = false;
  }
};

const deleteDoc = async (id) => {
  if (!confirm("Voulez-vous vraiment supprimer ce document ?")) return
  try {
    const res = await fetch(`${API_BASE_URL}?action=delete&id=${id}`, { method: 'DELETE' })
    const data = await res.json()
    if (data.success) loadDocs()
  } catch (e) { console.error(e) }
}

const openDoc = (doc) => {
  window.open(doc.fichier_url, "_blank")
}

const closeModal = () => {
  showAddModal.value = false
  addDocForm.value = {
    titre: "",
    description: "",
    image_url: "",
    fichier: null,
    fichier_url: "",
    date: "",
    ajout_par_matricule: userStore.user?.matricule_gen || " Inconnu",
    ajout_par_nom: userStore.user?.nom || "Nom Inconnu"
  }
  pdfPreview.value = null
  imagePreview.value = null
}

const formatDate = (d) => new Date(d).toLocaleDateString("fr-FR")

onMounted(loadDocs)
</script>

<style scoped>
@keyframes scale-in {
  0% { transform: scale(0.9); opacity: 0; }
  100% { transform: scale(1); opacity: 1; }
}
.animate-scale-in { animation: scale-in 0.3s ease; }
</style>
