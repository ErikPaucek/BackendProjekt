<script>
import { useConferenceStore } from '../stores/conferences'

export default {
  name: 'Dashboard',
  data() {
    return {
      newYear: '',
      newTitle: '',
      pageTitles: {}
    }
  },
  computed: {
    conferences() {
      return this.conferenceStore.conferences
    }
  },
  created() {
    this.conferenceStore = useConferenceStore()
  },
  methods: {
    addConference() {
      if (this.newYear && this.newTitle) {
        this.conferenceStore.addConference(this.newYear, this.newTitle)
        this.newYear = ''
        this.newTitle = ''
      }
    },
    addPage(confId) {
      const title = this.pageTitles[confId]
      if (title) {
        this.conferenceStore.addPage(confId, title)
        this.$set(this.pageTitles, confId, '')
      }
    }
  }
}
</script>

<template>
  <div class="dashboard-content">
    <h2>Dashboard - Správa konferencií</h2>

    <div>
      <input v-model="newYear" placeholder="Rok" />
      <input v-model="newTitle" placeholder="Názov konferencie" />
      <button @click="addConference">Pridať ročník</button>
    </div>

    <div class="conferences-grid">
      <div v-for="conf in conferences" :key="conf.id" class="conf">
        <h3>{{ conf.year }} - {{ conf.title }}</h3>
        <input v-model="pageTitles[conf.id]" placeholder="Názov podstránky" />
        <button @click="addPage(conf.id)">Pridať podstránku</button>

        <ul>
          <li v-for="page in conf.pages" :key="page.id">{{ page.title }}</li>
        </ul>
      </div>
    </div>
  </div>
</template>
<style scoped>
.dashboard-content {
  width: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  place-content: center;
  padding: 40px 32px 32px 40px; 
  background: #fff;
  min-height: calc(100vh - 48px); 
  box-sizing: border-box;
}

.conferences-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 20px;
  width: 100%;
}

.conf {
  margin-bottom: 32px;
  padding: 18px 20px;
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  background: #f9f9f9;
}

.conf h3 {
  margin-top: 0;
}

input {
  margin-right: 10px;
  margin-bottom: 10px;
  padding: 8px;
  border-radius: 4px;
  border: 1px solid #bbb;
}

button {
  padding: 8px 14px;
  border-radius: 4px;
  border: none;
  background: black;
  color: #fff;
  cursor: pointer;
  transition: background 0.2s;
}

button:hover {
  background: rgba(0, 0, 0, 0.836);
}
</style>