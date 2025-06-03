<template>
  <div class="pageview-content">
    <h1 class="page-title">{{ page.title }}</h1>
    <div v-html="page.content" class="page-html"></div>
    <div v-if="error" class="error">{{ error }}</div>
  </div>
</template>

<script>
import api from '../plugins/axios'

export default {
  props: ['slug', 'year'],
  data() {
    return {
      page: { title: '', content: '' },
      error: null
    }
  },
  async mounted() {
    try {
      const slug = this.slug || this.$route.params.slug
      const year = this.year || this.$route.params.year
      if (!slug || !year) {
        this.error = 'Slug alebo rok nie je definovaný.'
        return
      }
      const res = await api.get(`/subpages/slug/${year}/${slug}`)
      this.page = res.data
    } catch (e) {
      this.error = 'Stránka nebola nájdená.'
    }
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

.error {
  color: red;
  margin-top: 20px;
}
</style>