<template>
  <div class="pageview-content">
    <h1 class="page-title">{{ page.title }}</h1>
    <div v-html="page.content" class="page-html"></div>
  </div>
</template>

<script>
import api from '../plugins/axios'

export default {
  props: ['pageId'],
  data() {
    return {
      page: { title: '', content: '' }
    }
  },
  async mounted() {
    const res = await api.get(`/subpages/${this.pageId}`)
    this.page = res.data
  }
}
</script>

<style scoped>
.pageview-content {
  min-height: 100vh;
  padding: 40px 0 0 40px; 
  background: none;
  box-shadow: none;
  max-width: 100vw;
}

.page-title {
  font-size: 2rem;
  font-weight: bold;
  margin-bottom: 24px;
  color: #222;
  text-align: left;
}

.page-html {
  font-size: 1.1rem;
  line-height: 1.7;
  color: #222;
  word-break: break-word;
  text-align: left;
  background: none;
}
</style>