<script>
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import BackButton from './BackButton.vue'

export default {
  components: {
    BackButton
  },
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
    <div class="nav-links">
      <BackButton />
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

