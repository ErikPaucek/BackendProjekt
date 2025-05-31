<script setup>
import { computed, onMounted  } from 'vue'
import { useConferenceStore } from '../stores/conferences'
import { storeToRefs } from 'pinia'

const props = defineProps({
  conferenceId: {
    type: [String, Number],
    required: true
  }
})


const conferenceStore = useConferenceStore()
const { conferences } = storeToRefs(conferenceStore)
const conference = computed(() =>
conferences.value.find(c => c.id.toString() === props.conferenceId?.toString()))
onMounted(() => {
  conferenceStore.fetchConference(props.conferenceId)
})
</script>

<template>
  <aside class="sidebar">
    <template v-if="conference">
      <h3>{{ conference.title || 'Konferencia' }}</h3>
      <ul>
        <li v-for="page in conference.pages || []" :key="page.id">
          <router-link :to="`/conference/${conference.id}/page/${page.id}`" class="subpage-link">
            {{ page.title }}
          </router-link>
        </li>
      </ul>
    </template>
    <template v-else>
      <div>Načítavam konferenciu...</div>
    </template>
  </aside>
</template>