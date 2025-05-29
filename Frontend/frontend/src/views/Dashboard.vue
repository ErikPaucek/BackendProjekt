<script setup>
import { ref, onMounted } from 'vue'
import { fetchYears, createYear, deleteYear } from '../services/years'
import { fetchSubpages, createSubpage, updateSubpage, deleteSubpage } from '../services/subpage'
import { useConferenceStore } from '../stores/conferences'
import api from '../plugins/axios'

const years = ref([])
const subpages = ref([])
const newYear = ref('')
const pageTitles = ref({})
const editPageId = ref(null)
const editPageTitle = ref('')

const editors = ref([]) // všetci používatelia s rolou editor
const selectedEditor = ref({}) // podľa ročníka

const conferenceStore = useConferenceStore()

async function loadYears() {
  years.value = await fetchYears()
}

async function loadSubpages() {
  subpages.value = await fetchSubpages()
}

async function fetchEditors() {
  const res = await api.get('/users?role=editor')
  editors.value = res.data
}

async function assignEditor(yearId) {
  const editorId = selectedEditor.value[yearId]
  if (!editorId) return
  try {
    await api.post(`/years/${yearId}/editors`, { user_id: editorId })
    selectedEditor.value[yearId] = ''
    await loadYears()
  } catch (e) {
    if (e.response && e.response.status === 409) {
      alert('Tento editor je už priradený k tomuto ročníku.')
    } else {
      alert('Chyba pri priraďovaní editora.')
    }
  }
}

async function removeEditor(yearId, editorId) {
  await api.delete(`/years/${yearId}/editors/${editorId}`)
  await loadYears()
}

onMounted(() => {
  loadYears()
  loadSubpages()
  fetchEditors()
})

async function addYear() {
  if (newYear.value) {
    try {
      await createYear(Number(newYear.value))
      newYear.value = ''
      await loadYears()
      await conferenceStore.fetchConferences()
    } catch (e) {
      if (e.response && e.response.status === 422) {
        alert('Tento ročník už existuje alebo je neplatný.');
      } else {
        alert('Chyba pri pridávaní ročníka.');
      }
    }
  }
}

async function removeYear(id) {
  try {
    await deleteYear(id)
    await loadYears()
    await loadSubpages()
    await conferenceStore.fetchConferences()
  } catch (e) {
    alert('Chyba pri mazaní ročníka: ' + (e.response?.data?.message || e.message))
  }
}

// Pridanie podstránky
async function addPage(yearId) {
  const title = pageTitles.value[yearId]?.trim()
  if (!title) return

  // Skontroluj, či už existuje podstránka s rovnakým názvom v tomto ročníku
  const exists = subpages.value.some(
    (p) => p.year_id === yearId && p.title.trim().toLowerCase() === title.toLowerCase()
  )
  if (exists) {
    alert('Podstránka s týmto názvom už v tomto ročníku existuje.')
    return
  }

  await createSubpage({ year_id: yearId, title })
  pageTitles.value[yearId] = ''
  await loadSubpages()
}

// Začiatok editácie podstránky
function startEditPage(page) {
  editPageId.value = page.id
  editPageTitle.value = page.title
}

// Uloženie editovanej podstránky
async function saveEditPage(page) {
  if (!editPageTitle.value) return
  await updateSubpage(page.id, {
    year_id: page.year_id,
    title: editPageTitle.value,
    content: page.content ?? ''
  })
  editPageId.value = null
  editPageTitle.value = ''
  await loadSubpages()
}

// Odstránenie podstránky
async function removePage(pageId) {
  await deleteSubpage(pageId)
  await loadSubpages()
}

// Zrušenie editácie
function cancelEditPage() {
  editPageId.value = null
  editPageTitle.value = ''
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
  grid-template-columns: 1fr 1fr;
  gap: 36px;
}
h2 {
  text-align: center;
  margin-bottom: 32px;
  color: #222;
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
</style>