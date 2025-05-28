import api from '../plugins/axios'

export async function fetchSubpages() {
  const res = await api.get('/subpages')
  return res.data
}

export async function createSubpage(data: { year_id: number; title: string; content: string }) {
  const res = await api.post('/subpages', data)
  return res.data
}
export async function updateSubpage(id: number, data: { year_id: number; title: string; content: string }) {
  const res = await api.put(`/subpages/${id}`, data)
  return res.data
}

export async function deleteSubpage(id: number) {
  await api.delete(`/subpages/${id}`)
}