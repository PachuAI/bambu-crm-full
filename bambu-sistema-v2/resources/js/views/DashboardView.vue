<template>
  <div class="dashboard-container">
    <h1 class="dashboard-title">En Construcción</h1>
    
    <!-- Toggle de tema básico -->
    <button @click="toggleTheme" class="theme-toggle">
      {{ isDark ? '☀️ Modo Claro' : '🌙 Modo Oscuro' }}
    </button>
    
    <!-- Botón de logout -->
    <button @click="handleLogout" class="logout-btn">
      Cerrar Sesión
    </button>
  </div>
</template>

<script setup>
import { useTheme } from '@/composables/useTheme'
import { useAuthStore } from '@/stores/auth'

const { isDark, toggleTheme, initTheme } = useTheme()
const authStore = useAuthStore()

// Inicializar tema al montar
initTheme()

const handleLogout = async () => {
  await authStore.logout()
}
</script>

<style scoped>
.dashboard-container {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: var(--space-xl);
  padding: var(--space-lg);
  background: var(--bg-base);
  color: var(--text-primary);
}

.dashboard-title {
  font-size: var(--font-2xl);
  font-weight: 600;
  text-align: center;
  margin: 0;
}

.theme-toggle,
.logout-btn {
  padding: var(--space-md) var(--space-lg);
  background: var(--primary);
  color: var(--text-inverse);
  border: none;
  border-radius: var(--radius-md);
  cursor: pointer;
  font-weight: 500;
  transition: var(--transition-fast);
}

.theme-toggle:hover,
.logout-btn:hover {
  background: var(--primary-hover);
}

.logout-btn {
  background: var(--error);
}

.logout-btn:hover {
  background: var(--error);
  opacity: 0.9;
}
</style>