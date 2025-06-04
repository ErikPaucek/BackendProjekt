<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '../stores/auth'

const email = ref('')
const password = ref('')
const recaptchaToken = ref('')
const error = ref('')
const loading = ref(false)
const auth = useAuthStore()
const recaptchaWidgetId = ref(null)

function onCaptchaVerified(token) {
  recaptchaToken.value = token
}

function onCaptchaExpired() {
  recaptchaToken.value = ''
}

function renderRecaptcha() {
  if (window.grecaptcha) {
    recaptchaWidgetId.value = window.grecaptcha.render('recaptcha-container', {
      sitekey: '6LcDnFUrAAAAALvMpdnrjv1-1Af1IKE3nkzxVIkN',
      callback: onCaptchaVerified,
      'expired-callback': onCaptchaExpired
    })
  }
}

onMounted(() => {
  if (window.grecaptcha) {
    renderRecaptcha()
  } else {
    window.onloadCallback = renderRecaptcha
  }
})

async function onLogin() {
  error.value = ''
  if (!recaptchaToken.value) {
    error.value = 'Musíte potvrdiť, že nie ste robot (vyplniť captchu).'
    return
  }
  loading.value = true
  try {
    await auth.login(email.value, password.value, recaptchaToken.value)
    if (auth.user && auth.user.role === 'admin') {
      window.location.href = '/dashboard'
    } else if (auth.user && auth.user.role === 'editor') {
      window.location.href = '/editordashboard'
    } else {
      window.location.href = '/'
    }
  } catch (e) {
    error.value = e.response?.data?.message || 'Nesprávne prihlasovacie údaje alebo chyba servera.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <form @submit.prevent="onLogin" class="login-form">
    <input v-model="email" placeholder="Email" type="email" required />
    <input v-model="password" type="password" placeholder="Heslo" required />
    <div id="recaptcha-container"></div>
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