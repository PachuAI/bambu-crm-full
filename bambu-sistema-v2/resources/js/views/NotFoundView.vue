<template>
  <div class="not-found-container">
    <div class="not-found-content">
      <h1 class="not-found-number">404</h1>
      <h2 class="not-found-title">Página no encontrada</h2>
      <p class="not-found-description">
        La página que buscas no existe o ha sido movida.
      </p>
      <router-link to="/dashboard" class="btn-primary">
        Volver al Dashboard
      </router-link>
    </div>
  </div>
</template>

<script setup lang="ts">
// 404 page
</script>

<style scoped>
.not-found-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--bg-base);
  padding: var(--space-md);
}

.not-found-content {
  text-align: center;
  max-width: 500px;
}

.not-found-number {
  font-size: 8rem;
  font-weight: 700;
  color: var(--primary);
  margin-bottom: var(--space-md);
  line-height: 1;
}

.not-found-title {
  font-size: var(--font-2xl);
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: var(--space-sm);
}

.not-found-description {
  color: var(--text-secondary);
  font-size: var(--font-base);
  margin-bottom: var(--space-xl);
  line-height: 1.6;
}

.btn-primary {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: var(--space-md) var(--space-lg);
  background: var(--primary);
  color: var(--text-inverse);
  border: none;
  border-radius: var(--radius-md);
  font-size: var(--font-base);
  font-weight: 500;
  cursor: pointer;
  transition: var(--transition-fast);
  text-decoration: none;
  min-height: 48px;
}

.btn-primary:hover {
  background: var(--primary-hover);
  transform: translateY(-1px);
}

.btn-primary:active {
  background: var(--primary-active);
  transform: translateY(0);
}

/* === RESPONSIVE === */
@media (max-width: 640px) {
  .not-found-number {
    font-size: 6rem;
  }
  
  .not-found-container {
    padding: var(--space-sm);
  }
}
</style>