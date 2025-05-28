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
      const res = await api.get('/years') // OPRAVENÉ: správny endpoint
      conferences.value = res.data
    } catch (e: any) {
      error.value = e.response?.data?.message || 'Chyba pri načítaní konferencií'
    } finally {
      loading.value = false
    }
  }

  return { conferences, fetchConferences, loading, error }
})