// Service API pour communiquer avec le backend
const API_BASE_URL = 'http://sogetrag.com/apistage/messages-importants-api.php'
const ORGANES_API_URL = 'http://localhost:8000/src/api/organes-api.php'

class ApiService {
  constructor() {
    this.API_BASE_URL = API_BASE_URL
    this.ORGANES_API_URL = ORGANES_API_URL
  }

  // Méthode générique pour les requêtes
  async request(url, options = {}) {
    try {
      const response = await fetch(url, {
        headers: {
          'Content-Type': 'application/json',
          ...options.headers
        },
        ...options
      })

      if (!response.ok) {
        throw new Error(`HTTP ${response.status}: ${response.statusText}`)
      }

      return await response.json()
    } catch (error) {
      throw error
    }
  }

  // Récupérer un utilisateur par matricule
  async getUserByMatricule(matricule) {
    try {
      const response = await fetch(`${this.ORGANES_API_URL}?action=get_user_by_matricule&matricule=${matricule}`)
      const data = await response.json()
      return { success: response.ok, data }
    } catch (error) {
      throw error
    }
  }

  // Messages importants - Mode API avec fallback
  async getMessages(limit = 20, offset = 0) {
    try {
      // Essayer d'abord l'API
      const response = await fetch(`${this.API_BASE_URL}?action=get_messages_importants&limit=${limit}&offset=${offset}&_t=${Date.now()}&_v=2`)
      
      if (!response.ok) {
        throw new Error(`HTTP ${response.status}: ${response.statusText}`)
      }
      
      const data = await response.json()
      
      if (data.success) {
        console.log('✅ API - Messages chargés depuis le serveur')
        return data
      } else {
        throw new Error(data.error || 'Erreur API inconnue')
      }
    } catch (error) {
      console.log('🔄 API indisponible, utilisation du mode fallback:', error.message)
      
      // Mode fallback avec localStorage
      const savedMessages = localStorage.getItem('messages_importants')
      let messages = []
      
      if (savedMessages) {
        messages = JSON.parse(savedMessages)
      } else {
        // Messages d'exemple réalistes pour démonstration
        messages = [
          {
            id: 1,
            type: 'information',
            content: '<p><strong>🎉 Nouvelle plateforme digitale AEEMCI</strong></p><p>L\'AEEMCI a le plaisir de vous informer que la plateforme digitale est maintenant opérationnelle. Vous pouvez désormais accéder à tous les services en ligne, consulter les actualités et participer aux activités de la communauté.</p><p>Connectez-vous avec vos identifiants habituels pour découvrir toutes les fonctionnalités disponibles.</p>',
            matricule: 'AEEMCI-ADMIN-001',
            author_name: 'Secrétaire Général',
            created_at: '2024-01-15T10:30:00Z'
          },
          {
            id: 2,
            type: 'activite',
            content: '<p><strong>📚 Conférence : "L\'importance de la recherche de la science en Islam"</strong></p><p>L\'AEEMCI a l\'honneur de vous convier à une conférence animée par <strong>Imam Ahmad Ben Touré</strong> sur le thème :</p><p><strong>« L\'importance de la recherche de la science en Islam »</strong></p><p>Venez vous ressourcer à la lumière du Coran et de la Sunna, et redécouvrir la valeur de la science dans la vie du musulman.</p>',
            matricule: 'AEEMCI-ORG-002',
            author_name: 'Responsable des Activités',
            activity_date: '2024-01-20',
            activity_time: '14:00',
            activity_location: 'Siège de l\'AEEMCI - Salle de conférence',
            created_at: '2024-01-14T15:45:00Z'
          },
          {
            id: 3,
            type: 'information',
            content: '<p><strong>📋 Mise à jour des statuts</strong></p><p>Nous vous informons que les nouveaux statuts de l\'AEEMCI ont été approuvés lors de l\'assemblée générale extraordinaire du 10 janvier 2024.</p><p>Les documents officiels sont disponibles dans la section "Documents" de la plateforme.</p>',
            matricule: 'AEEMCI-LEGAL-003',
            author_name: 'Service Juridique',
            created_at: '2024-01-12T09:15:00Z'
          },
          {
            id: 4,
            type: 'activite',
            content: '<p><strong>🌙 Cérémonie de clôture du Ramadan</strong></p><p>L\'AEEMCI organise une cérémonie de clôture du mois de Ramadan avec :</p><ul><li>Récitation du Coran</li><li>Conférence spirituelle</li><li>Repas de rupture du jeûne</li><li>Distribution d\'aumônes</li></ul><p>Cette célébration sera l\'occasion de renforcer les liens fraternels au sein de notre communauté.</p>',
            matricule: 'AEEMCI-EVENT-004',
            author_name: 'Comité des Célébrations',
            activity_date: '2024-03-10',
            activity_time: '18:30',
            activity_location: 'Centre Culturel Islamique - Grande salle',
            created_at: '2024-01-10T16:20:00Z'
          },
          {
            id: 5,
            type: 'information',
            content: '<p><strong>💡 Programme de formation continue</strong></p><p>L\'AEEMCI lance un nouveau programme de formation continue destiné aux membres actifs. Les modules couvriront :</p><ul><li>Études coraniques approfondies</li><li>Histoire de l\'Islam</li><li>Langue arabe</li><li>Gestion associative</li></ul><p>Les inscriptions sont ouvertes jusqu\'au 31 janvier 2024.</p>',
            matricule: 'AEEMCI-EDU-005',
            author_name: 'Service Éducatif',
            created_at: '2024-01-08T11:00:00Z'
          }
        ]
        localStorage.setItem('messages_importants', JSON.stringify(messages))
      }
      
      const paginatedMessages = messages.slice(offset, offset + limit)
      
      return { 
        success: true, 
        data: { 
          messages: paginatedMessages,
          total: messages.length,
          limit,
          offset
        } 
      }
    }
  }

  async createMessage(messageData) {
    try {
      // Essayer d'abord l'API
      const response = await fetch(`${this.API_BASE_URL}?action=create_message`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(messageData)
      })
      
      if (!response.ok) {
        throw new Error(`HTTP ${response.status}: ${response.statusText}`)
      }
      
      const data = await response.json()
      
      if (data.success) {
        console.log('✅ API - Message créé sur le serveur')
        return data
      } else {
        throw new Error(data.error || 'Erreur API inconnue')
      }
    } catch (error) {
      console.log('🔄 API indisponible, création locale:', error.message)
      
      // Mode fallback avec localStorage
      const savedMessages = localStorage.getItem('messages_importants')
      let messages = savedMessages ? JSON.parse(savedMessages) : []
      
      const newMessage = {
        id: Date.now(),
        ...messageData,
        created_at: new Date().toISOString(),
        updated_at: new Date().toISOString()
      }
      
      messages.unshift(newMessage)
      localStorage.setItem('messages_importants', JSON.stringify(messages))
      
      return { 
        success: true, 
        data: newMessage,
        message: 'Message créé avec succès (mode local)'
      }
    }
  }

  async updateMessage(id, messageData) {
    try {
      // Essayer d'abord l'API
      const response = await fetch(`${this.API_BASE_URL}?action=update_message`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ id, ...messageData })
      })
      
      if (!response.ok) {
        throw new Error(`HTTP ${response.status}: ${response.statusText}`)
      }
      
      const data = await response.json()
      
      if (data.success) {
        console.log('✅ API - Message mis à jour sur le serveur')
        return data
      } else {
        throw new Error(data.error || 'Erreur API inconnue')
      }
    } catch (error) {
      console.log('🔄 API indisponible, mise à jour locale:', error.message)
      
      // Mode fallback avec localStorage
      const savedMessages = localStorage.getItem('messages_importants')
      let messages = savedMessages ? JSON.parse(savedMessages) : []
      
      const messageIndex = messages.findIndex(msg => msg.id == id)
      if (messageIndex === -1) {
        return { success: false, error: 'Message non trouvé' }
      }
      
      messages[messageIndex] = {
        ...messages[messageIndex],
        ...messageData,
        updated_at: new Date().toISOString()
      }
      
      localStorage.setItem('messages_importants', JSON.stringify(messages))
      
      return { 
        success: true, 
        data: messages[messageIndex],
        message: 'Message modifié avec succès (mode local)'
      }
    }
  }

  async deleteMessage(id) {
    try {
      // Essayer d'abord l'API
      const response = await fetch(`${this.API_BASE_URL}?action=delete_message`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ id })
      })
      
      if (!response.ok) {
        throw new Error(`HTTP ${response.status}: ${response.statusText}`)
      }
      
      const data = await response.json()
      
      if (data.success) {
        console.log('✅ API - Message supprimé du serveur')
        return data
      } else {
        throw new Error(data.error || 'Erreur API inconnue')
      }
    } catch (error) {
      console.log('🔄 API indisponible, suppression locale:', error.message)
      
      // Mode fallback avec localStorage
      const savedMessages = localStorage.getItem('messages_importants')
      let messages = savedMessages ? JSON.parse(savedMessages) : []
      
      const messageIndex = messages.findIndex(msg => msg.id == id)
      if (messageIndex === -1) {
        return { success: false, error: 'Message non trouvé' }
      }
      
      messages.splice(messageIndex, 1)
      localStorage.setItem('messages_importants', JSON.stringify(messages))
      
      return { 
        success: true, 
        message: 'Message supprimé avec succès (mode local)'
      }
    }
  }

  // Authentification (utilise l'API existante)
  async login(matricule, password) {
    try {
      const response = await fetch(`${this.ORGANES_API_URL}?action=login_user`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ matricule, password })
      })
      const data = await response.json()
      return { success: response.ok, data }
    } catch (error) {
      throw error
    }
  }
}


export default new ApiService()