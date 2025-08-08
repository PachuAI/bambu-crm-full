<template>
  <div class="debug-container">
    <h1>🚀 DEBUG DASHBOARD</h1>
    <p>Si ves esto, el routing funciona</p>
    
    <div class="test-grid">
      <div class="test-card">
        <h3>💰 Ventas</h3>
        <p>$150,000</p>
      </div>
      
      <div class="test-card">
        <h3>📋 Pedidos</h3>
        <p>23</p>
      </div>
      
      <div class="test-card">
        <h3>📦 Stock</h3>
        <p>8 críticos</p>
      </div>
      
      <div class="test-card">
        <h3>👥 Clientes</h3>
        <p>45</p>
      </div>
    </div>
    
    <button @click="testClick" class="test-btn">
      Test Click
    </button>
    
    <button @click="handleLogout" class="logout-btn">
      Logout
    </button>
  </div>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()

const testClick = () => {
  alert('¡JavaScript funciona!')
}

const handleLogout = async () => {
  await authStore.logout()
}
</script>

<style scoped>
.debug-container {
  padding: var(--space-lg);
  min-height: 100vh;
  background: var(--bg-base);
  color: var(--text-primary);
}

.debug-container h1 {
  font-size: 2rem;
  margin-bottom: var(--space-lg);
  color: var(--primary);
}

.test-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: var(--space-md);
  margin-bottom: var(--space-xl);
}

.test-card {
  background: var(--bg-surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  padding: var(--space-lg);
  text-align: center;
}

.test-card h3 {
  margin-bottom: var(--space-sm);
  font-size: var(--font-lg);
}

.test-card p {
  font-size: var(--font-xl);
  font-weight: 600;
  color: var(--primary);
}

.test-btn,
.logout-btn {
  padding: var(--space-sm) var(--space-lg);
  margin: var(--space-sm);
  background: var(--primary);
  color: white;
  border: none;
  border-radius: var(--radius-md);
  cursor: pointer;
  font-weight: 500;
}

.logout-btn {
  background: var(--error);
}

.test-btn:hover,
.logout-btn:hover {
  opacity: 0.9;
}
</style>