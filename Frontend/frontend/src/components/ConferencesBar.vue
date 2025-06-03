<script setup>
import { onMounted } from 'vue'
import { useConferenceStore } from '../stores/conferences'

const conferenceStore = useConferenceStore()
onMounted(() => {
  conferenceStore.fetchConferences()
})
</script>

<template>
  <aside class="sidebar">
    <h3>Konferencie</h3>
    <ul>
      <li v-for="conf in conferenceStore.conferences" :key="conf.id">
        <router-link :to="`/conference/${conf.year}`" class="conf-link">
          {{ conf.year }}
        </router-link>
      </li>
    </ul>
  </aside>
</template>