import api from '../plugins/axios'

export async function fetchSubpages() {
  const res = await api.get('/api/subpages')
  return res.data
}

export async function createSubpage(data: { year_id: number; title: string; content: string }) {
  const res = await api.post('/api/subpages', data)
  return res.data
}
export async function updateSubpage(id: number, data: { year_id: number; title: string; content: string }) {
  const res = await api.put(`/api/subpages/${id}`, data)
  return res.data
}

export async function deleteSubpage(id: number) {
  await api.delete(`/api/subpages/${id}`)
}