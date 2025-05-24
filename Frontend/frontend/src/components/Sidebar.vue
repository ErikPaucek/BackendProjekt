<script>
import { useConferenceStore } from '../stores/conferences'

export default {
  name: 'Sidebar',
  data() {
    return {}
  },
  computed: {
    conferences() {
      return this.conferenceStore.conferences
    }
  },
  created() {
    this.conferenceStore = useConferenceStore()
  }
}
</script>

<template>
  <aside class="sidebar">
    <h3>Konferencie</h3>
    <ul>
      <li v-for="conf in conferences" :key="conf.id">
        <router-link :to="`/conference/${conf.id}`" class="sidebar-link">
          <span>{{ conf.title }} ({{ conf.year }})</span>
        </router-link>
      </li>
    </ul>
  </aside>
</template>
