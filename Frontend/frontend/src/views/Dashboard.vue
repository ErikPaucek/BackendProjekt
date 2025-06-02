<script>
import { fetchYears, createYear, deleteYear } from '../services/years'
import { fetchSubpages, createSubpage, updateSubpage, deleteSubpage } from '../services/subpage'
import { useConferenceStore } from '../stores/conferences'
import api from '../plugins/axios'

export default {
  data() {
    return {
      years: [],
      subpages: [],
      newYear: '',
      pageTitles: {},
      editPageId: null,
      editPageTitle: '',
      editors: [],
      selectedEditor: {},
      users: [],
      newUser: { email: '', password: '', role: '' },
      conferenceStore: useConferenceStore(),
      editUserId: null,
      editUser: { email: '', password: '', role: '' },
    }
  },
  async created() {
    await this.loadYears()
    await this.loadSubpages()
    await this.fetchEditors()
    await this.loadUsers()
  },
  methods: {
    async loadYears() {
      this.years = await fetchYears()
    },
    async loadSubpages() {
      this.subpages = await fetchSubpages()
    },
    async fetchEditors() {
      const res = await api.get('/users?role=editor')
      this.editors = res.data
    },
    async loadUsers() {
      const res = await api.get('/users')
      this.users = res.data
    },
    async assignEditor(yearId) {
      const editorId = this.selectedEditor[yearId]
      if (!editorId) return
      try {
        await api.post(`/years/${yearId}/editors`, { user_id: editorId })
        this.selectedEditor[yearId] = ''
        await this.loadYears()
      } catch (e) {
        if (e.response && e.response.status === 409) {
          alert('Tento editor je už priradený k tomuto ročníku.')
        } else {
          alert('Chyba pri priraďovaní editora.')
        }
      }
    },
    async removeEditor(yearId, editorId) {
      await api.delete(`/years/${yearId}/editors/${editorId}`)
      await this.loadYears()
    },
    async addYear() {
      if (this.newYear) {
        try {
          await createYear(Number(this.newYear))
          this.newYear = ''
          await this.loadYears()
          await this.conferenceStore.fetchConferences()
        } catch (e) {
          if (e.response && e.response.status === 422) {
            alert('Tento ročník už existuje alebo je neplatný.');
          } else {
            alert('Chyba pri pridávaní ročníka.');
          }
        }
      }
    },
    async removeYear(id) {
      try {
        await deleteYear(id)
        await this.loadYears()
        await this.loadSubpages()
        await this.conferenceStore.fetchConferences()
      } catch (e) {
        alert('Chyba pri mazaní ročníka: ' + (e.response?.data?.message || e.message))
      }
    },
    async addPage(yearId) {
      const title = this.pageTitles[yearId]?.trim()
      if (!title) return

      const exists = this.subpages.some(
        (p) => p.year_id === yearId && p.title.trim().toLowerCase() === title.toLowerCase()
      )
      if (exists) {
        alert('Podstránka s týmto názvom už v tomto ročníku existuje.')
        return
      }

      await createSubpage({ year_id: yearId, title })
      this.pageTitles[yearId] = ''
      await this.loadSubpages()
      await this.conferenceStore.fetchConference(yearId) // ← doplnené
    },
    startEditPage(page) {
      this.editPageId = page.id
      this.editPageTitle = page.title
    },
    async saveEditPage(page) {
      if (!this.editPageTitle) return
      await updateSubpage(page.id, {
        year_id: page.year_id,
        title: this.editPageTitle,
        content: page.content ?? ''
      })
      this.editPageId = null
      this.editPageTitle = ''
      await this.loadSubpages()
      await this.conferenceStore.fetchConference(page.year_id) // ← doplnené
    },
    async removePage(pageId) {
      const page = this.subpages.find(p => p.id === pageId)
      await deleteSubpage(pageId)
      await this.loadSubpages()
      if (page) await this.conferenceStore.fetchConference(page.year_id) // ← doplnené
    },
    cancelEditPage() {
      this.editPageId = null
      this.editPageTitle = ''
    },
    async addUser() {
      if (!this.newUser.email || !this.newUser.password || !this.newUser.role) {
        alert('Vyplň všetky polia.')
        return
      }
      if (this.newUser.password.length < 6) {
        alert('Heslo musí mať aspoň 6 znakov.')
        return
      }
      try {
        await api.post('/users', this.newUser)
        this.newUser = { email: '', password: '', role: '' }
        await this.loadUsers()
        alert('Používateľ bol vytvorený.')
      } catch (e) {
        if (e.response && e.response.status === 422) {
          const errors = e.response.data.errors
          if (errors) {
            if (errors.email && errors.email[0].includes('unique')) {
              alert('Používateľ s týmto emailom už existuje.')
            } else if (errors.email) {
              alert(errors.email[0])
            } else if (errors.password) {
              alert(errors.password[0])
            } else if (errors.role) {
              alert(errors.role[0])
            } else {
              alert('Údaje nie sú platné.')
            }
          } else {
            alert('Údaje nie sú platné.')
          }
        } else {
          alert('Chyba pri vytváraní používateľa.')
        }
      }
    },
  
    startEditUser(user) {
      this.editUserId = user.id
      this.editUser = { email: user.email, password: '', role: user.role }
    },
    cancelEditUser() {
      this.editUserId = null
      this.editUser = { email: '', password: '', role: '' }
    },
    async saveEditUser(userId) {
      if (!this.editUser.email || !this.editUser.role) {
        alert('Vyplň všetky polia okrem hesla.')
        return
      }
      if (this.editUser.password && this.editUser.password.length < 6) {
        alert('Heslo musí mať aspoň 6 znakov.')
        return
      }
      try {
        const payload = {
          email: this.editUser.email,
          role: this.editUser.role
        }
        if (this.editUser.password) payload.password = this.editUser.password

        await api.put(`/users/${userId}`, payload)
        await this.loadUsers()
        this.cancelEditUser()
        alert('Používateľ upravený.')
      } catch (e) {
        const errors = e.response?.data?.errors
        if (errors) {
          if (errors.email && errors.email[0].includes('unique')) {
            alert('Používateľ s týmto emailom už existuje.')
          } else if (errors.email) {
            alert(errors.email[0])
          } else if (errors.password) {
            alert(errors.password[0])
          } else if (errors.role) {
            alert(errors.role[0])
          } else {
            alert('Údaje nie sú platné.')
          }
        } else {
          alert('Chyba pri úprave používateľa.')
        }
      }
    },
    async removeUser(userId) {
      if (!confirm('Naozaj chceš zmazať tohto používateľa?')) return
      await api.delete(`/users/${userId}`)
      await this.loadUsers()
    }
  }
}
</script>

<template>
  <div class="dashboard-content">
    <div class="dashboard-panels-grid">
      <!-- Panel 1: Správa ročníkov a podstránok -->
      <div>
        <h2>Správa ročníkov a podstránok</h2>
        <div class="add-year-box">
          <input
            v-model.number="newYear"
            type="number"
            min="1900"
            step="1"
            pattern="\d*"
            inputmode="numeric"
            placeholder="Zadaj nový rok"
          />
          <button @click="addYear">Pridať ročník</button>
        </div>
        <div class="years-grid">
          <div v-for="year in years" :key="year.id" class="year-card">
            <div class="year-header">
              <span class="year-title">{{ year.year }}</span>
              <button class="delete-btn" @click="removeYear(year.id)" title="Vymazať ročník">🗑️</button>
            </div>
                        <!-- filepath: c:\xampp\htdocs\BB\BackendProjekt-1\Frontend\frontend\src\views\Dashboard.vue -->
            <div class="add-page-box">
              <router-link
                :to="`/conference/${year.id}/page/new`"
                class="add-page-btn"
                style="padding: 6px 14px; border-radius: 5px; border: none; background: black; color: #fff; font-weight: bold; cursor: pointer; text-decoration: none;"
              >
                Pridať podstránku
              </router-link>
            </div>
            <ul class="subpage-list">
              <li v-for="page in subpages.filter(p => p.year_id === year.id)" :key="page.id" class="subpage-item">
                <span>{{ page.title }}</span>
                <router-link
                :to="`/conference/${year.id}/page/${page.id}/edit`"
                class="edit-btn"
                title="Upraviť"
                style="background: none; border: none; padding: 2px 6px; font-size: 1.1em;"
                >
                ✏️
                </router-link>
                <button class="delete-btn" @click="removePage(page.id)" title="Vymazať">🗑️</button>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Panel 2: Správa editorov pre ročníky -->
      <div>
        <h2>Správa editorov pre ročníky</h2>
        <div class="years-grid">
          <div v-for="year in years" :key="year.id" class="year-card">
            <div class="year-header">
              <span class="year-title">{{ year.year }}</span>
            </div>
            <div>
              <h4 style="margin: 0 0 8px 0;">Editori pre tento ročník:</h4>
              <ul>
                <li v-for="editor in year.editors || []" :key="editor.id" class="subpage-item">
                  {{ editor.email }}
                  <button class="delete-btn" @click="removeEditor(year.id, editor.id)" title="Odobrať editora">❌</button>
                </li>
              </ul>
              <div class="add-page-box" style="margin-bottom:0;">
                <select v-model="selectedEditor[year.id]">
                  <option value="">Vyber editora</option>
                  <option v-for="user in editors" :key="user.id" :value="user.id">{{ user.email }}</option>
                </select>
                <button @click="assignEditor(year.id)">Pridať editora</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Panel 3: Správa používateľov -->
  <div>
    <h2>Správa používateľov</h2>
    <div class="add-user-box">
      <input v-model="newUser.email" type="email" placeholder="Email" />
      <input v-model="newUser.password" type="password" placeholder="Heslo" />
      <select v-model="newUser.role">
        <option value="">Vyber rolu</option>
        <option value="editor">Editor</option>
        <option value="admin">Admin</option>
      </select>
      <button @click="addUser">Pridať používateľa</button>
    </div>
    <div class="users-grid">
      <div v-for="user in users" :key="user.id" class="user-card">
        <template v-if="editUserId !== user.id">
          <div class="user-header">
            <span class="user-email">{{ user.email }}</span>
            <span class="user-role">{{ user.role }}</span>
            <button class="edit-btn" @click="startEditUser(user)" title="Upraviť">✏️</button>
            <button class="delete-btn" @click="removeUser(user.id)" title="Vymazať používateľa">🗑️</button>
          </div>
        </template>
        <template v-else>
          <div class="user-header">
            <input v-model="editUser.email" type="email" placeholder="Email" />
            <input v-model="editUser.password" type="password" placeholder="Nové heslo (nepovinné)" />
            <select v-model="editUser.role">
              <option value="">Vyber rolu</option>
              <option value="editor">Editor</option>
              <option value="admin">Admin</option>
            </select>
            <button @click="saveEditUser(user.id)">Uložiť</button>
            <button @click="cancelEditUser">Zrušiť</button>
          </div>
        </template>
      </div>
    </div>
  </div>
</template>

<style scoped>
.dashboard-content {
  max-width: 900px;
  margin: 40px auto;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 2px 12px rgba(0,0,0,0.08);
  padding: 32px 24px 40px 24px;
}
.dashboard-panels-grid {
  display: grid;
  grid-template-columns: 1fr 1fr 1fr;
  gap: 36px;
}
h2 {
  text-align: center;
  margin-bottom: 32px;
  color: #222;
  margin-top: 48px; /* pridane pre posunutie nadpisu nizsie */
}
.add-year-box {
  display: flex;
  gap: 12px;
  justify-content: center;
  margin-bottom: 32px;
}
.add-year-box input {
  padding: 8px 12px;
  border-radius: 6px;
  border: 1px solid #ccc;
  font-size: 16px;
}
.add-year-box button {
  padding: 8px 18px;
  border-radius: 6px;
  border: none;
  background: black;
  color: #fff;
  font-weight: bold;
  cursor: pointer;
  transition: background 0.2s;
}
.add-year-box button:hover {
  background: #444;
}
.years-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 28px;
}
.year-card {
  background: #f9f9f9;
  border-radius: 10px;
  box-shadow: 0 1px 6px rgba(0,0,0,0.04);
  padding: 20px 18px 16px 18px;
  display: flex;
  flex-direction: column;
  gap: 12px;
}
.year-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 8px;
}
.year-title {
  font-size: 1.3rem;
  font-weight: bold;
  color: #222;
}
.add-page-box {
  display: flex;
  gap: 8px;
  margin-bottom: 10px;
}
.add-page-box input, .add-page-box select {
  flex: 1;
  padding: 6px 10px;
  border-radius: 5px;
  border: 1px solid #ccc;
}
.add-page-box button {
  padding: 6px 14px;
  border-radius: 5px;
  border: none;
  background: black;
  color: #fff;
  font-weight: bold;
  cursor: pointer;
  transition: background 0.2s;
}
.add-page-box button:hover {
  background: #444;
}
.subpage-list {
  list-style: none;
  padding: 0;
  margin: 0;
}
.subpage-item {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 4px 0;
}
.edit-btn, .delete-btn {
  background: none;
  text-decoration: none;
  border: none;
  cursor: pointer;
  font-size: 1.1em;
  padding: 2px 6px;
  border-radius: 4px;
  transition: background 0.15s;
}
.edit-btn:hover {
  background: #dde1e3;
}
.delete-btn:hover {
  background: #dde1e3;
}

.add-user-box {
  display: flex;
  gap: 10px;
  justify-content: center;
  margin-bottom: 32px;
}
.add-user-box input, .add-user-box select {
  padding: 8px 12px;
  border-radius: 6px;
  border: 1px solid #ccc;
  font-size: 16px;
}
.add-user-box button {
  padding: 8px 18px;
  border-radius: 6px;
  border: none;
  background: black;
  color: #fff;
  font-weight: bold;
  cursor: pointer;
  transition: background 0.2s;
}
.add-user-box button:hover {
  background: #444;
}
.users-grid {
  display: flex;
  flex-direction: column;
  gap: 16px;
}
.user-card {
  background: #f9f9f9;
  border-radius: 10px;
  box-shadow: 0 1px 6px rgba(0,0,0,0.04);
  padding: 14px 18px;
  display: flex;
  flex-direction: column;
}
.user-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
}
.user-email {
  font-weight: bold;
  color: #222;
}
.user-role {
  background: #e3e3e3;
  border-radius: 6px;
  padding: 2px 10px;
  font-size: 0.95em;
  color: #333;
}
</style>