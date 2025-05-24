<script>
import { useConferenceStore } from '../stores/conferences'

export default {
  name: 'ConferenceBar',
  props: {
    conferenceId: {
      type: [String, Number],
      required: true
    }
  },
  computed: {
    conference() {
      const store = useConferenceStore()
      return store.conferences.find(c => c.id.toString() === this.conferenceId?.toString())
    }
  }
}
</script>

<template>
  <aside class="sidebar">
    <h3>{{ conference?.title || 'Konferencia' }}</h3>
    <ul>
      <li>
        <router-link :to="`/conference/${conference.id}`" class="subpage-link">Home</router-link>
      </li>
      <li v-for="page in conference.pages" :key="page.id">
        <router-link :to="`/conference/${conference.id}/page/${page.id}`" class="subpage-link">
          {{ page.title }}
        </router-link>
      </li>
    </ul>
  </aside>
</template>
