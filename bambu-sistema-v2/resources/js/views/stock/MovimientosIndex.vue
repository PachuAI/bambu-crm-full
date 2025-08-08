<template>
  <div class="page-container">
    <h1 class="page-title">Movimientos de Stock</h1>
    
    <div class="coming-soon">
      <div class="icon">📋</div>
      <p>Módulo Movimientos en desarrollo - Sistema BAMBU</p>
    </div>
  </div>
</template>

<script setup>
// Stock movements module - desarrollo pendiente
</script>

<style scoped>
.page-container {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: var(--space-xl);
  padding: var(--space-lg);
  background: var(--bg-base);
}

.page-title {
  font-size: var(--font-2xl);
  font-weight: 600;
  color: var(--text-primary);
  margin: 0;
}

.coming-soon {
  background: var(--bg-surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  padding: var(--space-xl);
  text-align: center;
  max-width: 400px;
}

.icon {
  font-size: var(--font-4xl);
  margin-bottom: var(--space-md);
}

.coming-soon p {
  color: var(--text-secondary);
  font-size: var(--font-md);
  margin: 0;
}
</style>
