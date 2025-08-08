<template>
  <div class="cotizador-container">
    <h1 class="cotizador-title">Cotizador</h1>
    <p class="cotizador-description">
      Módulo en desarrollo - Se implementará siguiendo el sistema de diseño BAMBU
    </p>
    
    <div class="coming-soon">
      <div class="icon">🧪</div>
      <p>Próximamente: Cotizador con productos químicos BAMBU</p>
    </div>
  </div>
</template>

<script setup>
// Cotizador temporalmente vacío - requiere revisión completa vs MVP
// Ver STATUS.md: "Lógica diferente al MVP anterior - requiere revisión completa"
</script>

<style scoped>
.cotizador-container {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: var(--space-lg);
  padding: var(--space-lg);
  background: var(--bg-base);
  color: var(--text-primary);
}

.cotizador-title {
  font-size: var(--font-3xl);
  font-weight: 600;
  color: var(--text-primary);
  margin: 0;
}

.cotizador-description {
  color: var(--text-secondary);
  font-size: var(--font-md);
  text-align: center;
  max-width: 600px;
}

.coming-soon {
  background: var(--bg-surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  padding: var(--space-xl);
  text-align: center;
}

.coming-soon .icon {
  font-size: var(--font-3xl);
  margin-bottom: var(--space-md);
}

.coming-soon p {
  color: var(--text-secondary);
  font-size: var(--font-sm);
  margin: 0;
}
</style>