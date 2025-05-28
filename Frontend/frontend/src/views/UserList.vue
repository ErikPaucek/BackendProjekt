<template>
  <div>
    <h2>Správa používateľov</h2>
    <form @submit.prevent="addUser" class="user-form">
      <input v-model="form.email" type="email" placeholder="Email" required />
      <input v-model="form.password" type="password" placeholder="Heslo" required />
      <select v-model="form.role" required>
        <option value="" disabled>Vyber rolu</option>
        <option value="admin">Admin</option>
        <option value="editor">Editor</option>
      </select>
      <button type="submit">Pridať používateľa</button>
    </form>
    <div v-if="error" class="error">{{ error }}</div>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Email</th>
          <th>Rola</th>
          <th>Akcie</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="user in users" :key="user.id">
          <td>{{ user.id }}</td>
          <td>{{ user.email }}</td>
          <td>{{ user.role }}</td>
          <td>
            <button @click="editUser(user)">Upraviť</button>
            <button @click="deleteUser(user.id)">Zmazať</button>
          </td>
        </tr>
      </tbody>
    </table>
    <div v-if="edit.id" class="edit-section">
      <h3>Upraviť používateľa</h3>
      <form @submit.prevent="updateUser">
        <input v-model="edit.email" type="email" placeholder="Email" required />
        <input v-model="edit.password" type="password" placeholder="Nové heslo (nepovinné)" />
        <select v-model="edit.role" required>
          <option value="admin">Admin</option>
          <option value="editor">Editor</option>
        </select>
        <button type="submit">Uložiť</button>
        <button type="button" @click="edit.id = null">Zrušiť</button>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import api from '../plugins/axios'

interface User {
  id: number
  email: string
  role: string
}

const users = ref<User[]>([])
const form = ref({ email: '', password: '', role: '' })
const edit = ref<{ id: number|null, email: string, password: string, role: string }>({ id: null, email: '', password: '', role: '' })
const error = ref('')

async function fetchUsers() {
  try {
    const res = await api.get('/users')
    users.value = res.data
  } catch (e) {
    error.value = 'Nepodarilo sa načítať používateľov'
  }
}

async function addUser() {
  error.value = ''
  try {
    await api.post('/users', form.value)
    form.value = { email: '', password: '', role: '' }
    fetchUsers()
  } catch (e: any) {
    error.value = e.response?.data?.message || 'Chyba pri pridávaní používateľa'
  }
}

function editUser(user: User) {
  edit.value = { id: user.id, email: user.email, password: '', role: user.role }
}

async function updateUser() {
  error.value = ''
  try {
    const payload: any = { email: edit.value.email, role: edit.value.role }
    if (edit.value.password) payload.password = edit.value.password
    await api.put(`/users/${edit.value.id}`, payload)
    edit.value = { id: null, email: '', password: '', role: '' }
    fetchUsers()
  } catch (e: any) {
    error.value = e.response?.data?.message || 'Chyba pri úprave používateľa'
  }
}

async function deleteUser(id: number) {
  if (!confirm('Naozaj zmazať používateľa?')) return
  try {
    await api.delete(`/users/${id}`)
    fetchUsers()
  } catch (e) {
    error.value = 'Chyba pri mazaní používateľa'
  }
}

onMounted(fetchUsers)
</script>

<style scoped>
.user-form, .edit-section form {
  display: flex;
  gap: 8px;
  margin-bottom: 16px;
}
.error {
  color: red;
  margin-bottom: 10px;
}
table {
  border-collapse: collapse;
  width: 100%;
  margin-top: 16px;
}
th, td {
  border: 1px solid #ccc;
  padding: 6px 10px;
  text-align: left;
}
.edit-section {
  margin-top: 24px;
  padding: 12px;
  background: #f7f7f7;
  border-radius: 8px;
}
</style>