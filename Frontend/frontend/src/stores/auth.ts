import { defineStore } from 'pinia'
import api from '../plugins/axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null as null | { id: number; email: string; role: string },
    token: null as string | null,
  }),
  actions: {
    async login(email: string, password: string) {
      const response = await api.post('/login', { email, password })
      this.token = response.data.token
      this.user = response.data.user
      if (this.token) {
        localStorage.setItem('token', this.token)
      }
      console.log('Login response:', response.data)
    },
    async logout() {
      await api.post('/logout')
      this.user = null
      this.token = null
      localStorage.removeItem('token')
    },
    async fetchUser() {
      if (!this.token) return
      const response = await api.get('/user')
      this.user = response.data
    },
    loadToken() {
      const token = localStorage.getItem('token')
      if (token) {
        this.token = token
      }
    }
  },
})