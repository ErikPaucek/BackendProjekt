import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '../plugins/axios'

export const useConferenceStore = defineStore('conferences', () => {
  const conferences = ref<any[]>([])
  const pages = ref<any[]>([]) 
  const loading = ref(false)
  const error = ref<string | null>(null)

  async function fetchConferences() {
    loading.value = true
    error.value = null
    try {
      const res = await api.get('/years')
      res.data.forEach((year: any) => {
        const idx = conferences.value.findIndex(c => c.id.toString() === year.id.toString())
        if (idx !== -1) {
          conferences.value[idx] = { ...conferences.value[idx], ...year }
        } else {
          conferences.value.push(year)
        }
      })
    } catch (e: any) {
      error.value = e.response?.data?.message || 'Chyba pri načítaní konferencií'
    } finally {
      loading.value = false
    }
  }

  async function fetchConference(id: number | string) {
    try {
      const res = await api.get(`/years/${id}`)
      const idx = conferences.value.findIndex(c => c.id.toString() === id.toString())
      if (idx !== -1) {
        conferences.value[idx] = res.data
      } else {
        conferences.value.push(res.data)
      }
    } catch (e: any) {
      console.error('Chyba pri načítaní konferencie:', e)
    }
  }

  async function fetchPages() {
    try {
      const res = await api.get('/subpages')
      pages.value = res.data
    } catch (e: any) {
      console.error('Chyba pri načítaní podstránok:', e)
    }
  }

  function getPageById(id: number | string) {
    return pages.value.find(p => p.id.toString() === id.toString())
  }

  return {
    conferences,
    pages,
    fetchConferences,
    fetchConference,
    fetchPages, 
    getPageById,
    loading,
    error
  }
})