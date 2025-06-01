<script>
import { useAuthStore } from './stores/auth'
import ConferencesBar from './components/ConferencesBar.vue'
import ConferenceBar from './components/ConferenceBar.vue'
import BackButton from './components/BackButton.vue'

export default {
  name: 'App',
  components: {
    ConferencesBar,
    ConferenceBar,
    BackButton,
  },
  data() {
    return {
      auth: useAuthStore()
    }
  },
  computed: {
    route() {
      return this.$route
    },
    showSidebar() {
      return this.$route.path !== '/login'
    },
    sidebarComponent() {
      if (this.$route.path.startsWith('/conference/')) {
        return 'ConferenceBar'
      }
      return 'ConferencesBar'
    },
    conferenceId() {
      return this.$route.params.id || this.$route.params.confId || null
    }
  },
  methods: {
    logout() {
      this.auth.logout()
      this.$router.push('/')
    }
  }
}
</script>

<template>
  <div>
    <nav class="navbar" v-if="route.path !== '/login'">
      <router-link to="/" class="logo-link">
        <img src="/logo.png" alt="Logo" class="logo-img" />
      </router-link>
      <BackButton/>
      <router-link to="/">Home</router-link>
      <router-link v-if="auth.user && auth.user.role === 'admin'" to="/dashboard">Dashboard</router-link>
      <router-link v-if="auth.user && auth.user.role === 'editor'" to="/editordashboard">EditorDashboard</router-link>
      <div class="auth-area">
        <template v-if="auth.user">
          <span>Prihlásený ako: <strong>{{ auth.user.name }}</strong> ({{ auth.user.role }})</span>
          <button @click="logout">Odhlásiť sa</button>
        </template>
        <template v-else>
          <router-link to="/login">Prihlásiť sa</router-link>
        </template>
      </div>
    </nav>

    <div v-if="route.path === '/login'" class="login-page">
      <router-view />
    </div>

    <div v-else class="main-layout">
      <component
        v-if="route.path === '/'"
        :is="sidebarComponent"
        :conferenceId="conferenceId"
      />
      <router-view />
    </div>
  </div>
</template>

<style>
.navbar {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  z-index: 1000;
  display: flex;
  align-items: center;
  background: #f0f0f0;
  padding: 10px 10px;
  gap: 20px;
  border-bottom: 1px solid #e0e0e0;
  min-height: 48px;
}

.navbar a {
  color: black;
  text-decoration: none;
  text-transform: uppercase;
  font-size: 15px;
  font-weight: bold;
}

.navbar a:hover {
  text-decoration: underline;
}

.auth-area {
  margin-left: auto; 
  display: flex;
  align-items: center;
  gap: 15px;
  margin-right: 20px;
}

.auth-area button {
  cursor: pointer;
  padding: 5px 10px;
  background-color: black;
  border: none;
  color: white;
  border-radius: 3px;
}

.auth-area button:hover {
  background-color: rgba(0, 0, 0, 0.836);
}

.main-layout {
  display: flex;
  height: calc(100vh - 48px);
  background: white;
  margin-top: 48px; 
}

.login-page {
  height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  background: #f4f4f4;
}

.logo-link {
  display: flex;
  align-items: center;
  margin-right: 10px;
}
.logo-img {
  height: 56px;
  width: auto;
  display: block;
}
</style>