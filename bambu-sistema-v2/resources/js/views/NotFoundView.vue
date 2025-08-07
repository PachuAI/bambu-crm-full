<template>
  <div class="min-h-screen flex items-center justify-center bg-bg-primary">
    <div class="text-center">
      <h1 class="text-9xl font-bold text-primary mb-4">404</h1>
      <h2 class="text-2xl font-semibold text-text-primary mb-2">Página no encontrada</h2>
      <p class="text-text-secondary mb-8">
        La página que buscas no existe o ha sido movida.
      </p>
      <router-link to="/dashboard" class="btn btn-primary">
        Volver al Dashboard
      </router-link>
    </div>
  </div>
</template>

<script setup lang="ts">
// 404 page
</script>

<style scoped></style>