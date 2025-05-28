<script setup>
import { ref, onMounted } from 'vue'
import { fetchYears, createYear, deleteYear } from '../services/years'
import { fetchSubpages, createSubpage, updateSubpage, deleteSubpage } from '../services/subpage'
import { useConferenceStore } from '../stores/conferences'

const years = ref([])
const subpages = ref([])
const newYear = ref('')
const pageTitles = ref({})
const editPageId = ref(null)
const editPageTitle = ref('')

const conferenceStore = useConferenceStore()

async function loadYears() {
  years.value = await fetchYears()
}

async function loadSubpages() {
  subpages.value = await fetchSubpages()
}

onMounted(() => {
  loadYears()
  loadSubpages()
})

async function addYear() {
  if (newYear.value) {
    await createYear(Number(newYear.value))
    newYear.value = ''
    await loadYears()
    await conferenceStore.fetchConferences()
  }
}

async function removeYear(id) {
  await deleteYear(id)
  await loadYears()
  await loadSubpages()
  await conferenceStore.fetchConferences()
}

async function removePage(pageId) {
  await deleteSubpage(pageId)
  await loadSubpages()
}

// Pridanie podstránky
async function addPage(yearId) {
  const title = pageTitles.value[yearId]
  if (!title) return
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

// Zrušenie editácie
function cancelEditPage() {
  editPageId.value = null
  editPageTitle.value = ''
}
</script>

<template>
  <div class="dashboard-content">
    <h2>Správa ročníkov a podstránok</h2>
    <div class="add-year-box">
      <input v-model="newYear" placeholder="Zadaj nový rok" type="number" />
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
  background: #222;
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
  grid-template-columns: repeat(auto-fit, minmax(270px, 1fr));
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
  background: #1976d2;
  color: #fff;
  font-weight: bold;
  cursor: pointer;
  transition: background 0.2s;
}
.add-page-box button:hover {
  background: #125ea2;
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
  background: #e3f2fd;
}
.delete-btn:hover {
  background: #ffebee;
}
</style>