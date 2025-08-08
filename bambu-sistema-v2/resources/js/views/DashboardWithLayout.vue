<template>
  <MainLayoutSimple>
    <div class="dashboard">
      <h1 class="page-title">Dashboard</h1>
      
      <div class="metrics-grid">
        <div class="metric-card">
          <div class="metric-icon">💰</div>
          <div class="metric-content">
            <div class="metric-value">$150,000</div>
            <div class="metric-label">Ventas del Mes</div>
            <div class="metric-trend positive">+12%</div>
          </div>
        </div>
        
        <div class="metric-card">
          <div class="metric-icon">📋</div>
          <div class="metric-content">
            <div class="metric-value">23</div>
            <div class="metric-label">Pedidos Pendientes</div>
            <div class="metric-trend neutral">5 urgentes</div>
          </div>
        </div>
        
        <div class="metric-card">
          <div class="metric-icon">📦</div>
          <div class="metric-content">
            <div class="metric-value">8</div>
            <div class="metric-label">Stock Crítico</div>
            <div class="metric-trend negative">Requiere atención</div>
          </div>
        </div>
        
        <div class="metric-card">
          <div class="metric-icon">👥</div>
          <div class="metric-content">
            <div class="metric-value">45</div>
            <div class="metric-label">Clientes Activos</div>
            <div class="metric-trend positive">+3 nuevos</div>
          </div>
        </div>
      </div>

      <div class="simple-table">
        <div class="table-header">
          <span>Producto</span>
          <span>Stock</span>
          <span>Ventas</span>
        </div>
        
        <div class="table-row">
          <span>Ácido Muriático 5L</span>
          <span class="stock-low">120 L</span>
          <span>$15,000</span>
        </div>
        
        <div class="table-row">
          <span>Soda Cáustica 20L</span>
          <span class="stock-ok">450 L</span>
          <span>$12,000</span>
        </div>
        
        <div class="table-row">
          <span>Cloro Líquido IBC</span>
          <span class="stock-ok">2,300 L</span>
          <span>$8,500</span>
        </div>
      </div>
    </div>
  </MainLayoutSimple>
</template>

<script setup>
import MainLayoutSimple from '@/components/MainLayoutSimple.vue'
</script>

<style scoped>
.dashboard {
  width: 100%;
  /* Removemos el max-width y margin auto para que ocupe todo el espacio */
}

.page-title {
  font-size: var(--font-2xl);
  color: var(--text-primary);
  margin-bottom: var(--space-lg);
  font-weight: 600;
}

/* Grid responsive 1→2→4 columnas */
.metrics-grid {
  display: grid;
  gap: var(--space-md);
  margin-bottom: var(--space-xl);
  grid-template-columns: 1fr;
}

@media (min-width: 768px) {
  .metrics-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: var(--space-lg);
  }
}

@media (min-width: 1024px) {
  .metrics-grid {
    grid-template-columns: repeat(4, 1fr);
  }
}

.metric-card {
  background: var(--bg-surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  padding: var(--space-lg);
  display: flex;
  align-items: center;
  gap: var(--space-md);
  transition: var(--transition-normal);
  min-height: 100px;
}

.metric-card:hover {
  border-color: var(--border-hover);
  transform: translateY(-1px);
  box-shadow: var(--shadow-sm);
}

.metric-icon {
  font-size: var(--font-2xl);
  width: 48px;
  height: 48px;
  display: grid;
  place-items: center;
  background: var(--bg-elevated);
  border-radius: var(--radius-md);
  flex-shrink: 0;
}

.metric-content {
  flex: 1;
  min-width: 0;
}

.metric-value {
  font-size: var(--font-xl);
  font-weight: 600;
  color: var(--text-primary);
  line-height: 1.2;
}

.metric-label {
  font-size: var(--font-sm);
  color: var(--text-secondary);
  margin-top: var(--space-xs);
  line-height: 1.2;
}

.metric-trend {
  font-size: var(--font-xs);
  margin-top: var(--space-xs);
  font-weight: 500;
  line-height: 1;
}

.metric-trend.positive {
  color: var(--success);
}

.metric-trend.negative {
  color: var(--error);
}

.metric-trend.neutral {
  color: var(--warning);
}

/* Tabla simple */
.simple-table {
  background: var(--bg-surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  overflow: hidden;
}

.table-header,
.table-row {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr;
  gap: var(--space-md);
  padding: var(--space-md);
  align-items: center;
}

.table-header {
  background: var(--bg-elevated);
  font-weight: 600;
  color: var(--text-primary);
  font-size: var(--font-sm);
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.table-row {
  border-top: 1px solid var(--border);
  transition: var(--transition-fast);
}

.table-row:hover {
  background: var(--bg-elevated);
}

.stock-low {
  color: var(--error);
  font-weight: 600;
}

.stock-ok {
  color: var(--success);
  font-weight: 600;
}

/* Mobile: tabla como cards */
@media (max-width: 767px) {
  .table-header {
    display: none;
  }
  
  .table-row {
    display: block;
    padding: var(--space-lg);
  }
  
  .table-row > span {
    display: block;
    margin-bottom: var(--space-xs);
  }
  
  .table-row > span:first-child {
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: var(--space-sm);
  }
  
  .table-row > span:nth-child(2)::before {
    content: "Stock: ";
    color: var(--text-muted);
    font-weight: normal;
  }
  
  .table-row > span:nth-child(3)::before {
    content: "Ventas: ";
    color: var(--text-muted);
    font-weight: normal;
  }
}

/* Mobile optimizations */
@media (max-width: 767px) {
  .page-title {
    font-size: var(--font-xl);
    margin-bottom: var(--space-md);
  }
  
  .metric-card {
    padding: var(--space-md);
    min-height: 80px;
  }
  
  .metric-icon {
    width: 40px;
    height: 40px;
    font-size: var(--font-lg);
  }
  
  .metric-value {
    font-size: var(--font-lg);
  }
}
</style>