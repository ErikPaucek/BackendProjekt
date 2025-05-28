import api from '../plugins/axios'

export const login = async (email: string, password: string) => {
  const response = await api.post('/login', { email, password })
  return response.data
}

export const register = async (email: string, password: string, password_confirmation: string, role: string) => {
  const response = await api.post('/register', { email, password, password_confirmation, role })
  return response.data
}

export const logout = async () => {
  const response = await api.post('/logout')
  return response.data
}

export const fetchUser = async () => {
  const response = await api.get('/user')
  return response.data
}
