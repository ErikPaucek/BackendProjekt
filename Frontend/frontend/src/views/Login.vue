<script>
import { useAuthStore } from '../stores/auth'

export default {
  name: 'Login',
  data() {
    return {
      email: '',
      password: '',
      error: '',
      loading: false,
      auth: useAuthStore()
    }
  },
  methods: { // ← tu bola chyba, musí byť bez medzery a s čiarkou pred tým
    async onLogin() {
      this.error = ''
      this.loading = true
      try {
        await this.auth.login(this.email, this.password)
        if (this.auth.user && this.auth.user.role === 'admin') {
          this.$router.push('/dashboard')
        } else if (this.auth.user && this.auth.user.role === 'editor') {
          this.$router.push('/editordashboard')
        } else {
          this.$router.push('/')
        }
      } catch (e) {
        this.error = e.response?.data?.message || 'Nesprávne prihlasovacie údaje alebo chyba servera.'
      } finally {
        this.loading = false
      }
    }
  }
}
</script>

<template>
  <form @submit.prevent="onLogin" class="login-form">
    <input v-model="email" placeholder="Email" type="email" required />
    <input v-model="password" type="password" placeholder="Heslo" required />
    <button type="submit" :disabled="loading">Prihlásiť sa</button>
    <p v-if="error" style="color:red">{{ error }}</p>
  </form>
</template>

<style scoped>
.login-form {
  background: white;
  padding: 40px;
  border-radius: 8px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  display: flex;
  flex-direction: column;
  gap: 15px;
  width: 300px;
}
.login-form input {
  padding: 10px;
  font-size: 14px;
}
.login-form button {
  padding: 10px;
  font-size: 14px;
  background-color: black;
  color: white;
  border: none;
  border-radius: 5px;
}
.login-form button:hover {
  background-color: rgba(0, 0, 0, 0.836);
  cursor: pointer;
}
</style>