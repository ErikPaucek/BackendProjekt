<script>
import { computed } from 'vue'
import { useConferenceStore } from '../stores/conferences'

export default {
  name: 'PageView',
  props: ['confId', 'pageId'],
  data() {
    return {
      store: useConferenceStore()
    }
  },
  computed: {
    page() {
      const id = parseInt(this.pageId)
      return this.store.getPageById(id) || { title: '', content: '', attachments: [] }
    }
  }
}
</script>

<template>
  <div class="content">
    <h2>{{ page.title }}</h2>
    <div v-html="page.content"></div>
    <div v-if="page.attachments.length">
      <h4>Prílohy:</h4>
      <ul>
        <li v-for="att in page.attachments" :key="att.url">
          <a :href="att.url" target="_blank" rel="noopener">{{ att.name }}</a>
        </li>
      </ul>
    </div>
  </div>
</template>

<style scoped>
.content {
  padding: 20px;
}
</style>


