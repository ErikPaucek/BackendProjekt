<script>
import ConferenceBar from '../components/ConferenceBar.vue'
import { useConferenceStore } from '../stores/conferences'

export default {
  name: 'ConferenceView',
  components: { ConferenceBar },
  props: ['year'],
  data() {
    return {
      conference: null
    }
  },
  async created() {
    const store = useConferenceStore()
    await store.fetchConferences()
    this.conference = store.conferences.find(
      c => c.year.toString() === this.year.toString()
    )
  },
  watch: {
    year: {
      immediate: true,
      async handler(newYear) {
        const store = useConferenceStore()
        await store.fetchConferences()
        this.conference = store.conferences.find(
          c => c.year.toString() === newYear.toString()
        )
      }
    }
  }
}
</script>

<template>
  <div class="conference-content">
    <ConferenceBar :year="year" :key="year" />
  </div>
</template>