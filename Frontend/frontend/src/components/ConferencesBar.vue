<script>
import { useConferenceStore } from '../stores/conferences'

export default {
  name: 'ConferencesBar',
  computed: {
    conferences() {
      const store = useConferenceStore()
      return store.conferences
    }
  }
}
</script>

<template>
  <aside class="sidebar">
    <h3>Konferencie</h3>
    <ul>
      <li v-for="conf in conferences" :key="conf.id">
        <router-link :to="`/conference/${conf.id}`" class="conf-link">
          {{ conf.title }} ({{ conf.year }})
        </router-link>
      </li>
    </ul>
  </aside>
</template>
