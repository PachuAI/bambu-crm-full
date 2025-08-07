<template>
  <router-view />
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import { useTheme } from '@/composables/useTheme'
import { useAuthStore } from '@/stores/auth'

const { initTheme } = useTheme()
const authStore = useAuthStore()

onMounted(async () => {
  // Initialize theme
  initTheme()
  
  // Check if user is authenticated on app load
  if (authStore.token && !authStore.user) {
    await authStore.fetchUser()
  }
})
</script>