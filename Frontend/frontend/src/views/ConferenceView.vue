<script>
import { computed } from 'vue'
import { useConferenceStore } from '../stores/conferences'

export default {
  name: 'ConferenceContent',
  props: ['id'],
  data() {
    return {
      store: useConferenceStore()
    }
  },
  computed: {
    conference() {
      return this.store.conferences.find(c => c.id.toString() === this.id)
    }
  }
}
</script>

<<template>
  <div class="conference-content">
    <h2>{{ conference.title }} ({{ conference.year }})</h2>
    <router-view /> 
  </div>
</template>
