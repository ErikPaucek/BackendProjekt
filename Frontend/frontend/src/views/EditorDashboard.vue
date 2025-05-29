<template>
  <div class="dashboard-content">
    <!-- Panel 1: Správa podstránok -->
    <div>
      <h2>Správa podstránok</h2>
      <div class="years-grid">
        <div v-for="year in editorYears" :key="year.id" class="year-card">
          <div class="year-header">
            <span class="year-title">{{ year.year }}</span>
          </div>
          <div class="add-page-box">
            <input v-model="pageTitles[year.id]" placeholder="Názov podstránky" />
            <button @click="addPage(year.id)">Pridať podstránku</button>
          </div>
          <ul class="subpage-list">
            <li v-for="page in subpages.filter(p => p.year_id === year.id)" :key="page.id" class="subpage-item">
              <template v-if="editPageId === page.id">
                <input v-model="editPageTitle" />
                <button @click="saveEditPage(page)">Uložiť</button>
                <button @click="cancelEditPage">Zrušiť</button>
              </template>
              <template v-else>
                <span>{{ page.title }}</span>
                <button class="edit-btn" @click="startEditPage(page)" title="Upraviť">✏️</button>
                <button class="delete-btn" @click="removePage(page.id)" title="Vymazať">🗑️</button>
              </template>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { fetchSubpages, createSubpage, updateSubpage, deleteSubpage } from '../services/subpage'
import { fetchYears } from '../services/years'
import { useAuthStore } from '../stores/auth'

export default {
  data() {
    return {
      years: [],
      subpages: [],
      pageTitles: {},
      editPageId: null,
      editPageTitle: '',
      authStore: useAuthStore()
    }
  },
  computed: {
    editorYears() {
      return this.years.filter(year => 
        year.editors?.some(editor => editor.id === this.authStore.user.id)
      )
    }
  },
  async created() {
    await this.loadYears()
    await this.loadSubpages()
  },
  methods: {
    async loadYears() {
      this.years = await fetchYears()
    },
    async loadSubpages() {
      this.subpages = await fetchSubpages()
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
    },
    async removePage(pageId) {
      await deleteSubpage(pageId)
      await this.loadSubpages()
    },
    cancelEditPage() {
      this.editPageId = null
      this.editPageTitle = ''
    }
  }
}
</script>

<style scoped>
.dashboard-content {
  max-width: 900px;
  margin: 40px auto;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 2px 12px rgba(0,0,0,0.08);
  padding: 32px 24px 40px 24px;
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
.add-page-box input {
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
  border: none;
  cursor: pointer;
  font-size: 1.1em;
  padding: 2px 6px;
  border-radius: 4px;
  transition: background 0.15s;
}
.edit-btn:hover, .delete-btn:hover {
  background: #dde1e3;
}
</style>