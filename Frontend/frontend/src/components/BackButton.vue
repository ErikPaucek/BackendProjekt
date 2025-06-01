<script setup>
import { useRouter, useRoute } from 'vue-router'
import { ref, onMounted, watch } from 'vue'

const router = useRouter()
const route = useRoute()
const canGoBack = ref(false)

function isAllowedPath(path) {
  return (
    /^\/conference(\/|$)/.test(path) ||
    /^\/page(\/|$)/.test(path)
  )
}

function updateCanGoBack() {
  canGoBack.value = window.history.length > 1 && isAllowedPath(route.path)
}

onMounted(updateCanGoBack)
watch(() => route.path, updateCanGoBack)

function goBack() {
  router.back()
}
</script>

<template>
  <button v-if="canGoBack" class="back-btn" @click="goBack">←</button>
</template>

<style scoped>
.back-btn {
  background: none;
  border: none;
  color: #222;
  font-size: 30px;
  cursor: pointer;
  padding: 6px 14px;
  border-radius: 6px; 
  margin-bottom: 6px;  
  transition: background 0.15s;
  font-weight: bold;
}
.back-btn:hover {
  background: #eeeeee;
}
</style>