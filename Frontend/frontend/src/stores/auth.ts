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
      if (response.data.token) {
        this.token = response.data.token
        this.user = response.data.user
        if (this.token) {
          localStorage.setItem('token', this.token)
        }
        api.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
      }
      return response.data
    },
    setTokenAndUser(token: string, user: { id: number; email: string; role: string }) {
      this.token = token
      this.user = user
      if (token) {
        localStorage.setItem('token', token)
        api.defaults.headers.common['Authorization'] = `Bearer ${token}`
      }
    },
    async logout() {
      try {
        await api.post('/logout')
      } catch (e) {
      }
      this.user = null
      this.token = null
      localStorage.removeItem('token')
      delete api.defaults.headers.common['Authorization']
    },
    async fetchUser() {
      if (!this.token) return
      api.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
      const response = await api.get('/user')
      this.user = response.data
    },
    loadToken() {
      const token = localStorage.getItem('token')
      if (token) {
        this.token = token
        api.defaults.headers.common['Authorization'] = `Bearer ${token}`
      }
    }
  },
})