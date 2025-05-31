<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import Editor from '@tinymce/tinymce-vue'
import api from '../plugins/axios'
import { useConferenceStore } from '../stores/conferences'

const route = useRoute()
const router = useRouter()
const pageId = route.params.pageId

const content = ref('')
const success = ref(false)
const error = ref(false)
const store = useConferenceStore()

const tinymceConfig = {
  height: 400,
  menubar: true,
  plugins: [
  'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview', 'anchor',
  'searchreplace', 'visualblocks', 'code', 'fullscreen',
  'insertdatetime', 'media', 'table', 'help', 'wordcount'
],
  toolbar:
    'undo redo | formatselect | bold italic underline backcolor | ' +
    'alignleft aligncenter alignright alignjustify | ' +
    'bullist numlist outdent indent | removeformat | help | link image table',
  image_title: true,
  automatic_uploads: true,
  file_picker_types: 'image',
}

onMounted(async () => {
  try {
    const res = await api.get(`/subpages/${pageId}`)
    content.value = res.data.content || ''
  } catch (e) {
    error.value = true
  }
})

async function savePage() {
  success.value = false
  error.value = false
  try {
    const res = await api.get(`/subpages/${pageId}`)
    const page = res.data

    await api.put(`/subpages/${pageId}`, {
      year_id: page.year_id,
      title: page.title,
      content: content.value
    })
    if (store.fetchPages) {
      await store.fetchPages()
    }
    success.value = true
  } catch (e) {
    error.value = true
  }
}

function cancelEdit() {
  router.back()
}
</script>

<template>
  <div class="editor-outer">
    <div class="editor-inner">
      <Editor
        v-model="content"
        :init="tinymceConfig"
        api-key="8tszzirtg4tqdocu670ilh2klf3sgeotmljezhfywqc0tawp"
      />
      <div class="editor-actions">
        <button class="save-btn" @click="savePage">Uložiť</button>
        <button class="cancel-btn" @click="cancelEdit">Zrušiť</button>
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
  width: 100vw;
  min-height: calc(100vh - 48px);
  display: flex;
  justify-content: center;
  align-items: flex-start;
  background: #f8f8f8;
  padding: 60px 0 40px 0; 
  padding-top: 170px;
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