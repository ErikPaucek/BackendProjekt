<script setup>
import { ref } from 'vue'
import { useAuthStore } from '../stores/auth'
import axios from 'axios'

const email = ref('')
const password = ref('')
const error = ref('')
const loading = ref(false)
const auth = useAuthStore()

const show2fa = ref(false)
const twofaCode = ref('')
const pendingUserId = ref(null)

async function onLogin() {
  error.value = ''
  loading.value = true
  try {
    const res = await axios.post('/api/login', {
      email: email.value,
      password: password.value
    })
    if (res.data['2fa_required']) {
      show2fa.value = true
      pendingUserId.value = res.data.user_id
    } else if (res.data.token) {
      await auth.setTokenAndUser(res.data.token, res.data.user)
      redirectByRole()
    }
  } catch (e) {
    error.value = e.response?.data?.message || 'Nesprávne prihlasovacie údaje alebo chyba servera.'
  } finally {
    loading.value = false
  }
}

async function onVerify2fa() {
  error.value = ''
  loading.value = true
  try {
    const res = await axios.post('/api/verify-2fa', {
      user_id: pendingUserId.value,
      code: twofaCode.value
    })
    await auth.setTokenAndUser(res.data.token, res.data.user)
    redirectByRole()
  } catch (e) {
    error.value = e.response?.data?.message || 'Nesprávny alebo expirovaný kód.'
  } finally {
    loading.value = false
  }
}

function redirectByRole() {
  if (auth.user && auth.user.role === 'admin') {
    window.location.href = '/dashboard'
  } else if (auth.user && auth.user.role === 'editor') {
    window.location.href = '/editordashboard'
  } else {
    window.location.href = '/'
  }
}
</script>

<template>
  <form v-if="!show2fa" @submit.prevent="onLogin" class="login-form">
    <input v-model="email" placeholder="Email" type="email" required />
    <input v-model="password" type="password" placeholder="Heslo" required />
    <button type="submit" :disabled="loading">Prihlásiť sa</button>
    <p v-if="error" style="color:red">{{ error }}</p>
  </form>
  <form v-else @submit.prevent="onVerify2fa" class="login-form">
    <input v-model="twofaCode" placeholder="2FA kód" required />
    <button type="submit" :disabled="loading">Overiť kód</button>
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