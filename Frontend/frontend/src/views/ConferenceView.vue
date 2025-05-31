<script>
import ConferenceBar from '../components/ConferenceBar.vue'
import { useConferenceStore } from '../stores/conferences'

export default {
  name: 'ConferenceView',
  components: { ConferenceBar },
  props: ['id'],
  data() {
    return {
      conference: null
    }
  },
  async created() {
    const store = useConferenceStore()
    await store.fetchConference(this.id)
    this.conference = store.conferences.find(
      c => c.id.toString() === this.id.toString()
    )
  },
  watch: {
    id: {
      immediate: true,
      async handler(newId) {
        const store = useConferenceStore()
        await store.fetchConference(newId)
        this.conference = store.conferences.find(
          c => c.id.toString() === newId.toString()
        )
      }
    }
  }
}
</script>

<template>
  <div class="conference-content">
    <ConferenceBar :conferenceId="id" :key="id" />
  </div>
</template>
