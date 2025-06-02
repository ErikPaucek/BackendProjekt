<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import TinymceEditor from '../components/TinymceEditor.vue'
import api from '../plugins/axios'

const route = useRoute()
const router = useRouter()
const pageId = route.params.pageId

const title = ref('')
const content = ref('')
const yearId = ref(null)
const success = ref(false)
const error = ref(false)

onMounted(async () => {
  try {
    const res = await api.get(`/subpages/${pageId}`)
    title.value = res.data.title || ''
    content.value = res.data.content || ''
    yearId.value = res.data.year_id // <-- pridaj toto
  } catch (e) {
    error.value = true
  }
})

async function savePage() {
  success.value = false
  error.value = false
  try {
    await api.put(`/subpages/${pageId}`, {
      year_id: yearId.value, // <-- pridaj toto
      title: title.value,
      content: content.value
    })
    success.value = true
    router.back()
  } catch (e) {
    error.value = true
  }
}
</script>

<template>
  <div class="editor-outer">
    <div class="editor-inner">
      <input v-model="title" placeholder="Názov podstránky" class="page-title-input" />
      <TinymceEditor v-model="content" />
      <div class="editor-actions">
        <button class="save-btn" @click="savePage">Uložiť</button>
        <button class="cancel-btn" @click="router.back()">Zrušiť</button>
        <span v-if="success" class="success-msg">Uložené!</span>
        <span v-if="error" class="error-msg">Chyba pri ukladaní.</span>
      </div>
    </div>
  </div>
</template>

<style scoped>
body{
    background: #f0f0f0;
    min-height: 100vh;
}
.editor-outer {
  width: 100%;
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: flex-start;
  background: #f8f8f8;
  padding: 120px 0 24px 0; /* upravené paddingy */
  box-sizing: border-box;
}
.editor-inner {
  width: 100%;
  max-width: 750px;
  background: #fff;
  border-radius: 14px;
  box-shadow: 0 2px 16px rgba(0,0,0,0.10);
  padding: 32px 24px;
  margin: 0 16px;
  display: flex;
  flex-direction: column;
  align-items: stretch;
}

.editor-inner :deep(.tox) {
  border-radius: 8px;
  box-shadow: 0 1px 8px rgba(0,0,0,0.06);
}

.editor-actions {
  display: flex;
  gap: 16px;
  margin-top: 24px;
  align-items: center;
}

.save-btn {
  background: #111;
  color: #fff;
  border: none;
  padding: 10px 22px;
  border-radius: 5px;
  font-weight: bold;
  cursor: pointer;
  transition: background 0.2s, color 0.2s;
  box-shadow: 0 2px 8px rgba(0,0,0,0.07);
  letter-spacing: 1px;
}
.save-btn:hover {
  background: #333;
  color: #fff;
}
.cancel-btn {
  background: #eee;
  color: #333;
  border: none;
  padding: 10px 22px;
  border-radius: 5px;
  font-weight: bold;
  cursor: pointer;
  transition: background 0.2s;
}
.cancel-btn:hover {
  background: #ccc;
}

.success-msg {
  color: #388e3c;
  margin-left: 16px;
  font-weight: bold;
}
.error-msg {
  color: #d32f2f;
  margin-left: 16px;
  font-weight: bold;
}
</style>