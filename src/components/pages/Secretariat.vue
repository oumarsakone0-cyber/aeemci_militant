<template>
  <div class="bibliotheque-page">
    <!-- Navbar -->
    <header class="navbar">
      <div class="logo">📚 Ma Bibliothèque</div>
      <nav class="menu">
        <ul>
          <li @click="activeTab = 'home'" :class="{active: activeTab==='home'}">Accueil</li>
          <li @click="activeTab = 'livres'" :class="{active: activeTab==='livres'}">Livres</li>
          <li @click="activeTab = 'categories'" :class="{active: activeTab==='categories'}">Catégories</li>
          <li @click="activeTab = 'auteurs'" :class="{active: activeTab==='auteurs'}">Auteurs</li>
          <li @click="activeTab = 'contact'" :class="{active: activeTab==='contact'}">Contact</li>
        </ul>
      </nav>
    </header>

    <!-- Contenu principal -->
    <main>
      <!-- Accueil -->
      <section v-if="activeTab === 'home'" class="section-home">
        <h1>Bienvenue dans la Bibliothèque</h1>
        <p>Explorez nos collections de livres par catégories, auteurs ou directement par recherche.</p>
      </section>

      <!-- Section Livres -->
      <section v-if="activeTab === 'livres'" class="section-livres">
        <div class="search-bar">
          <input 
            type="text" 
            v-model="searchQuery" 
            placeholder="Rechercher un livre..."
          />
        </div>

        <div class="livres-container">
          <aside class="sidebar">
            <h3>Catégories</h3>
            <ul>
              <li 
                v-for="cat in categories" 
                :key="cat"
                @click="filterCategory = cat"
                :class="{active: filterCategory === cat}"
              >
                {{ cat }}
              </li>
            </ul>
          </aside>

          <div class="livres-list">
            <div 
              class="livre-card" 
              v-for="livre in filteredLivres" 
              :key="livre.id"
            >
              <img :src="livre.image" alt="Livre" />
              <h4>{{ livre.titre }}</h4>
              <p>{{ livre.auteur }}</p>
              <small>{{ livre.categorie }}</small>
            </div>
          </div>
        </div>
      </section>

      <!-- Catégories -->
      <section v-if="activeTab === 'categories'" class="section-categories">
        <h2>Nos Catégories</h2>
        <ul>
          <li v-for="cat in categories" :key="cat">{{ cat }}</li>
        </ul>
      </section>

      <!-- Auteurs -->
      <section v-if="activeTab === 'auteurs'" class="section-auteurs">
        <h2>Auteurs Populaires</h2>
        <div class="auteurs-list">
          <div 
            v-for="auteur in auteurs" 
            :key="auteur.nom"
            class="auteur-card"
          >
            <img :src="auteur.photo" alt="Auteur" />
            <h4>{{ auteur.nom }}</h4>
            <small>{{ auteur.nationalite }}</small>
          </div>
        </div>
      </section>

      <!-- Contact -->
      <section v-if="activeTab === 'contact'" class="section-contact">
        <h2>Contactez-nous</h2>
        <form @submit.prevent="envoyerMessage">
          <input type="text" placeholder="Votre nom" v-model="form.nom" required />
          <input type="email" placeholder="Votre email" v-model="form.email" required />
          <textarea placeholder="Votre message" v-model="form.message" required></textarea>
          <button type="submit">Envoyer</button>
        </form>
      </section>
    </main>

    <!-- Footer -->
    <footer class="footer">
      <p>&copy; 2025 Ma Bibliothèque - Tous droits réservés</p>
    </footer>
  </div>
</template>

<script>
export default {
  name: "BibliothequePage",
  data() {
    return {
      activeTab: "home",
      searchQuery: "",
      filterCategory: "",
      categories: ["Romans", "Sciences", "Histoire", "Informatique", "Art", "Philosophie"],
      livres: [
        { id: 1, titre: "L'Étranger", auteur: "Albert Camus", categorie: "Romans", image: "https://picsum.photos/120/160?random=1" },
        { id: 2, titre: "Introduction à l’IA", auteur: "John Smith", categorie: "Informatique", image: "https://picsum.photos/120/160?random=2" },
        { id: 3, titre: "La Peinture Moderne", auteur: "Sophie Bernard", categorie: "Art", image: "https://picsum.photos/120/160?random=3" },
        { id: 4, titre: "La Révolution Française", auteur: "Jean Dupont", categorie: "Histoire", image: "https://picsum.photos/120/160?random=4" },
      ],
      auteurs: [
        { nom: "Victor Hugo", nationalite: "Française", photo: "https://picsum.photos/100?random=5" },
        { nom: "Chinua Achebe", nationalite: "Nigériane", photo: "https://picsum.photos/100?random=6" },
        { nom: "Albert Camus", nationalite: "Française", photo: "https://picsum.photos/100?random=7" },
      ],
      form: {
        nom: "",
        email: "",
        message: ""
      }
    };
  },
  computed: {
    filteredLivres() {
      return this.livres.filter(livre => {
        const matchSearch = livre.titre.toLowerCase().includes(this.searchQuery.toLowerCase());
        const matchCategory = this.filterCategory ? livre.categorie === this.filterCategory : true;
        return matchSearch && matchCategory;
      });
    }
  },
  methods: {
    envoyerMessage() {
      alert(`Merci ${this.form.nom}, votre message a été envoyé !`);
      this.form = { nom: "", email: "", message: "" };
    }
  }
};
</script>

<style scoped>
.bibliotheque-page {
  font-family: Arial, sans-serif;
  color: #333;
}
.navbar {
  display: flex;
  justify-content: space-between;
  background: #2c3e50;
  color: white;
  padding: 1rem;
}
.menu ul {
  list-style: none;
  display: flex;
  gap: 1rem;
}
.menu li {
  cursor: pointer;
  padding: 5px 10px;
}
.menu li.active {
  background: #16a085;
  border-radius: 5px;
}
.section-livres {
  display: flex;
  flex-direction: column;
  padding: 1rem;
}
.search-bar {
  margin-bottom: 1rem;
}
.livres-container {
  display: flex;
}
.sidebar {
  width: 200px;
}
.sidebar ul {
  list-style: none;
  padding: 0;
}
.sidebar li {
  cursor: pointer;
  padding: 5px;
}
.sidebar li.active {
  background: #ddd;
  border-radius: 5px;
}
.livres-list {
  flex: 1;
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
  gap: 1rem;
  margin-left: 1rem;
}
.livre-card {
  border: 1px solid #ddd;
  padding: 10px;
  text-align: center;
  border-radius: 5px;
}
.auteurs-list {
  display: flex;
  gap: 1rem;
  padding: 1rem;
}
.auteur-card {
  text-align: center;
}
.section-contact form {
  display: flex;
  flex-direction: column;
  max-width: 400px;
  gap: 10px;
}
.footer {
  background: #2c3e50;
  color: white;
  text-align: center;
  padding: 1rem;
  margin-top: 1rem;
}
</style>
