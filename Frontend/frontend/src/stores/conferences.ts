import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '../plugins/axios'

export const useConferenceStore = defineStore('conferences', () => {
  const conferences = ref<any[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  async function fetchConferences() {
    loading.value = true
    error.value = null
    try {
      const res = await api.get('/conferences') // uprav podľa svojho backend endpointu
      conferences.value = res.data
    } catch (e: any) {
      error.value = e.response?.data?.message || 'Chyba pri načítaní konferencií'
    } finally {
      loading.value = false
    }
  }

  // Prípadne ďalšie metódy na pridávanie/úpravu/mazanie môžu volať API

  return { conferences, fetchConferences, loading, error }
})