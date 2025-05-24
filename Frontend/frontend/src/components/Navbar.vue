<script>
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

export default {
  name: 'Navbar',
  data() {
    return {}
  },
  computed: {
    user() {
      return this.auth.user
    },
    isLoggedIn() {
      return this.auth.user !== null
    }
  },
  created() {
    this.router = useRouter()
    this.auth = useAuthStore()
  },
  methods: {
    logout() {
      this.auth.logout()
      this.router.push('/login')
    },
    goBack() {
      this.router.back()
    }
  }
}
</script>

<template>
  <nav class="navbar">
    <button class="back-btn" @click="goBack" title="Späť">
      ←
    </button>

    <div class="nav-links">
      <router-link to="/" class="nav-link">Home</router-link>
      <router-link v-if="isLoggedIn" to="/dashboard" class="nav-link">Dashboard</router-link>
      <router-link v-if="!isLoggedIn" to="/login" class="nav-link">Login</router-link>
    </div>

    <div v-if="isLoggedIn" class="user-info">
      Prihlásený ako: <strong>{{ user.name }}</strong> ({{ user.role }})
      <button @click="logout">Odhlásiť sa</button>
    </div>
  </nav>
</template>

