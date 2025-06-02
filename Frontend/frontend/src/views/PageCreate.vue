<script setup>
import { ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import TinymceEditor from '../components/TinymceEditor.vue'
import api from '../plugins/axios'

const route = useRoute()
const router = useRouter()
const yearId = route.params.yearId

const title = ref('')
const content = ref('')
const success = ref(false)
const error = ref(false)
const titleError = ref(false)

async function savePage() {
  success.value = false
  error.value = false
  titleError.value = false
  if (!title.value.trim()) {
    titleError.value = true
    return
  }
  try {
    await api.post('/subpages', {
      year_id: yearId,
      title: title.value,
      content: content.value
    })
    success.value = true
    setTimeout(() => router.back(), 800)
  } catch (e) {
    error.value = true
  }
}
</script>

<template>
  <div class="editor-outer">
    <div class="editor-inner">
      <label for="page-title" class="page-title-label">Názov podstránky *</label>
      <input
        id="page-title"
        v-model="title"
        placeholder="Názov podstránky"
        class="page-title-input"
        :class="{ 'input-error': titleError }"
        required
        autocomplete="off"
      />
      <span v-if="titleError" class="error-msg">Názov podstránky je povinný.</span>
      <TinymceEditor v-model="content" />
      <div class="editor-actions">
        <button class="save-btn" @click="savePage">Vytvoriť</button>
        <button class="cancel-btn" @click="router.back()">Zrušiť</button>
        <span v-if="success" class="success-msg">Vytvorené!</span>
        <span v-if="error" class="error-msg">Chyba pri vytváraní.</span>
      </div>
    </div>
  </div>
</template>

<style scoped>
.page-title-label {
  font-weight: bold;
  margin-bottom: 4px;
  display: block;
}
.page-title-input {
  width: 100%;
  padding: 8px 12px;
  margin-bottom: 8px;
  border-radius: 4px;
  border: 1px solid #ccc;
  font-size: 1.1em;
}
.input-error {
  border: 1.5px solid #e74c3c;
}
.error-msg {
  color: #e74c3c;
  font-size: 0.95em;
  margin-bottom: 8px;
  display: block;
}
.success-msg {
  color: #27ae60;
  font-size: 0.95em;
  margin-bottom: 8px;
  display: block;
}
.editor-outer {
  width: 100%;
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: flex-start;
  background: #f8f8f8;
  padding: 60px 0 24px 0;
}
.editor-inner {
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.07);
  padding: 32px 24px 24px 24px;
  min-width: 400px;
  max-width: 700px;
  width: 100%;
}
.editor-actions {
  margin-top: 18px;
  display: flex;
  gap: 12px;
  align-items: center;
}
.save-btn, .cancel-btn {
  padding: 8px 18px;
  border-radius: 5px;
  border: none;
  font-weight: bold;
  cursor: pointer;
}
.save-btn {
  background: #222;
  color: #fff;
}
.cancel-btn {
  background: #eee;
  color: #222;
}
</style>