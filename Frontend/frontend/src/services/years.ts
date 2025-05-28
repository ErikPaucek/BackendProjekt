import api from '../plugins/axios'

export async function fetchYears() {
  const res = await api.get('/api/years')
  return res.data
}

export async function createYear(year: number) {
  const res = await api.post('/api/years', { year })
  return res.data
}

export async function deleteYear(id: number) {
  await api.delete(`/api/years/${id}`)
}