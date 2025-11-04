<template>
  <div id="jitsi-container" ref="jitsiContainer" style="height: 600px; width: 100%;"></div>
</template>

<script>
import { useUserStore } from "../../stores/user";

export default {
  name: "Mmeet",
  props: {
    roomName: {
      type: String,
      required: true
    }
  },
  data() {
    return {
      api: null
    };
  },
  mounted() {
    this.loadJitsiScript()
      .then(() => {
        this.startJitsi();
      })
      .catch(err => console.error("Erreur de chargement Jitsi:", err));
  },
  methods: {
    loadJitsiScript() {
      return new Promise((resolve, reject) => {
        if (window.JitsiMeetExternalAPI) {
          resolve();
          return;
        }

        const script = document.createElement("script");
        script.src = "https://meet.jit.si/external_api.js";
        script.async = true;
        script.onload = () => resolve();
        script.onerror = () => reject(new Error("Impossible de charger Jitsi"));
        document.body.appendChild(script);
      });
    },
    startJitsi() {
      const userStore = useUserStore();
      const userName = userStore.user?.nom || "Nom Inconnu";

      const domain = "meet.jit.si"; // Pour Jitsi self-hosted
      // Pour JaaS, tu utiliserais le domaine fourni par 8x8 et un JWT
      const options = {
        roomName: this.roomName,
        parentNode: this.$refs.jitsiContainer,
        width: "100%",
        height: 600,
        userInfo: {
          displayName: userName
        },
        // Pour JaaS (Magic Cookie), ajouter jwt : "vpaas-magic-cookie-xxxx"
        jwt: "vpaas-magic-cookie-88b1e1e5b77c49d3a43f6b9b08edf41f",
        interfaceConfigOverwrite: {
          TOOLBAR_BUTTONS: ['microphone', 'camera', 'hangup', 'chat', 'fullscreen']
        }
      };

      this.api = new window.JitsiMeetExternalAPI(domain, options);

      this.api.addEventListener('videoConferenceJoined', () => {
        console.log(`${userName} a rejoint la conférence`);
      });
    }
  },
  beforeUnmount() {
    if (this.api) {
      this.api.dispose();
    }
  }
};
</script>

<style scoped>
#jitsi-container {
  border: 1px solid #ccc;
  border-radius: 8px;
}
</style>
