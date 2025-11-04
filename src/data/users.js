/**
 * Données des utilisateurs réels AEEMCI
 * Ces données correspondent aux vrais utilisateurs de la base de données
 */

export const REAL_USERS = {
  // Utilisateur principal - MOUSTAPHA OUAGA
  'MOUSTAPHA_OUAGA': {
    matricule: 'MO2024001',
    nom: 'OUAGA',
    prenom: 'MOUSTAPHA',
    full_name: 'MOUSTAPHA OUAGA',
    email: 'moustapha.ouaga@aeemci.org',
    photo_url: 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1200px-User_icon_2.svg.png',
    ville: 'BELIER',
    telephone: '+225 07 XX XX XX XX',
    role: 'Président CE_CE',
    bio: 'Président du Conseil Exécutif - Centre d\'Excellence. Passionné par l\'éducation islamique et le développement communautaire.',
    posts_count: 12,
    likes_received: 45,
    comments_received: 28,
    status: 'active',
    date_inscription: '2024-01-15'
  },

  // Autres membres de l'équipe
  'AMINATA_KONE': {
    matricule: 'AK2024002',
    nom: 'KONE',
    prenom: 'AMINATA',
    full_name: 'Dr. AMINATA KONE',
    email: 'aminata.kone@aeemci.org',
    photo_url: 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1200px-User_icon_2.svg.png',
    ville: 'Abidjan',
    telephone: '+225 05 XX XX XX XX',
    role: 'Directrice Académique',
    bio: 'Docteure en Sciences Islamiques. Spécialisée dans les méthodologies d\'enseignement moderne.',
    posts_count: 8,
    likes_received: 32,
    comments_received: 19,
    status: 'active',
    date_inscription: '2024-02-01'
  },

  'IBRAHIM_TRAORE': {
    matricule: 'IT2024003',
    nom: 'TRAORE',
    prenom: 'IBRAHIM',
    full_name: 'IBRAHIM TRAORE',
    email: 'ibrahim.traore@aeemci.org',
    photo_url: 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/1200px-User_icon_2.svg.png',
    ville: 'Bouaké',
    telephone: '+225 01 XX XX XX XX',
    role: 'Étudiant Niveau 3',
    bio: 'Étudiant passionné en Sciences Islamiques. Futur enseignant.',
    posts_count: 5,
    likes_received: 18,
    comments_received: 12,
    status: 'active',
    date_inscription: '2024-03-10'
  }
}

// Fonction pour récupérer un utilisateur par matricule
export const getUserByMatricule = (matricule) => {
  // Rechercher dans tous les utilisateurs
  for (const [key, user] of Object.entries(REAL_USERS)) {
    if (user.matricule === matricule) {
      return user
    }
  }
  
  // Si pas trouvé, retourner MOUSTAPHA OUAGA par défaut
  return REAL_USERS.MOUSTAPHA_OUAGA
}

// Fonction pour récupérer un utilisateur par nom complet
export const getUserByName = (fullName) => {
  for (const [key, user] of Object.entries(REAL_USERS)) {
    if (user.full_name === fullName) {
      return user
    }
  }
  return null
}

// Posts réalistes pour le feed
export const REAL_POSTS = [
  {
    id: 1,
    content: "Bienvenue sur notre nouvelle plateforme AEEMCI ! 🎉\n\nNous sommes ravis de vous présenter cette nouvelle interface qui permettra à notre communauté de mieux échanger et partager nos expériences académiques et spirituelles.\n\nCette plateforme facilitera la communication entre étudiants, enseignants et administrateurs.",
    author: REAL_USERS.MOUSTAPHA_OUAGA,
    timestamp: "Il y a 2 heures",
    reactions: 24,
    comments: [],
    shares: 8,
    showComments: false,
    isLiked: false,
    location: "BELIER",
    created_at: new Date(Date.now() - 2 * 60 * 60 * 1000).toISOString()
  },
  {
    id: 2,
    content: "Excellente session de formation aujourd'hui sur les nouvelles méthodologies d'enseignement islamique ! 📚✨\n\nMerci à tous les participants pour leur engagement exceptionnel. L'éducation reste notre priorité absolue.\n\n#formation #enseignement #aeemci",
    author: REAL_USERS.AMINATA_KONE,
    timestamp: "Il y a 4 heures",
    reactions: 18,
    comments: [],
    shares: 3,
    showComments: false,
    isLiked: true,
    location: "Abidjan",
    created_at: new Date(Date.now() - 4 * 60 * 60 * 1000).toISOString()
  },
  {
    id: 3,
    content: "Préparation intensive pour les examens de certification AEEMCI. Que Allah facilite nos efforts ! 🤲\n\nLes révisions se passent bien. Merci aux professeurs pour leur soutien constant.\n\n#examens #certification #dua #aeemci #etudes",
    author: REAL_USERS.IBRAHIM_TRAORE,
    timestamp: "Il y a 6 heures",
    reactions: 32,
    comments: [],
    shares: 2,
    showComments: false,
    isLiked: false,
    location: "Bouaké",
    created_at: new Date(Date.now() - 6 * 60 * 60 * 1000).toISOString()
  }
]

export default {
  REAL_USERS,
  getUserByMatricule,
  getUserByName,
  REAL_POSTS
}
