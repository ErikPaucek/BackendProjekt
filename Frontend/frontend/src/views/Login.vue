<script>
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

export default {
  name: 'LoginForm',
  data() {
    return {
      name: '',
      password: '',  
      role: '',       
    }
  },
  created() {
    this.router = useRouter()
    this.auth = useAuthStore()
  },
  methods: {
    onLogin() {
      this.auth.login(this.name, this.role)
      this.router.push('/')
    }
  }
}
</script>

<template>
  <form @submit.prevent="onLogin" class="login-form">
    <input v-model="name" placeholder="Meno" required />
    <input v-model="password" type="password" placeholder="Heslo" required />
    <select v-model="role" required>
      <option disabled value="">Vyber rolu</option>
      <option value="admin">Admin</option>
      <option value="editor">Editor</option>
      <option value="anonym">Anonym</option>
    </select>
    <button type="submit">Prihlásiť sa</button>
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
.login-form input,
.login-form select {
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
