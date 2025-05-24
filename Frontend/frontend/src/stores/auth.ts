import { defineStore } from 'pinia'
import { ref} from 'vue'
import type { Ref } from 'vue'

type User = {
  name: string
  role: string
}

export const useAuthStore = defineStore('auth', () => {
  const user: Ref<User | null> = ref(null)

  function login(name: string, role: string) {
    user.value = { name, role }
  }

  function logout() {
    user.value = null
  }

  return { user, login, logout }
})
