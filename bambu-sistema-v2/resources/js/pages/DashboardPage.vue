<!-- 
  DashboardPage.vue - Página de prueba para validar el layout
  Usa el AppShell y muestra contenido para verificar que todo funciona
-->

<template>
  <div class="dashboard">
    <h1 class="page-title">Dashboard</h1>
    
    <!-- Métricas Grid -->
    <section class="metrics-section">
      <div class="metrics-grid">
        <div class="metric-card">
          <div class="metric-icon">💰</div>
          <div class="metric-content">
            <div class="metric-value">$150,000</div>
            <div class="metric-label">Ventas del Mes</div>
            <div class="metric-trend positive">+12% vs mes anterior</div>
          </div>
        </div>
        
        <div class="metric-card">
          <div class="metric-icon">📋</div>
          <div class="metric-content">
            <div class="metric-value">23</div>
            <div class="metric-label">Pedidos Pendientes</div>
            <div class="metric-trend warning">5 urgentes</div>
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
    </section>
    
    <!-- Tabla simple para probar scroll -->
    <section class="table-section">
      <h2 class="section-title">Productos con Stock Crítico</h2>
      
      <div class="table-wrapper">
        <table class="simple-table">
          <thead>
            <tr>
              <th>Producto</th>
              <th>Stock Actual</th>
              <th>Stock Mínimo</th>
              <th>Estado</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Ácido Muriático 5L</td>
              <td class="stock-critical">120 L</td>
              <td>500 L</td>
              <td><span class="badge badge-danger">Crítico</span></td>
            </tr>
            <tr>
              <td>Soda Cáustica 20L</td>
              <td class="stock-low">450 L</td>
              <td>300 L</td>
              <td><span class="badge badge-warning">Bajo</span></td>
            </tr>
            <tr>
              <td>Cloro Líquido IBC</td>
              <td class="stock-ok">2,300 L</td>
              <td>1,000 L</td>
              <td><span class="badge badge-success">OK</span></td>
            </tr>
            <tr>
              <td>Hipoclorito de Sodio</td>
              <td class="stock-critical">80 L</td>
              <td>200 L</td>
              <td><span class="badge badge-danger">Crítico</span></td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>
  </div>
</template>

<script setup>
// Esta página es solo para testing del layout
</script>

<style scoped>
/* Estilos de la página dashboard */
.dashboard {
  padding: 0;
}

.page-title {
  font-size: var(--font-3xl);
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: var(--space-xl);
}

.section-title {
  font-size: var(--font-xl);
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: var(--space-lg);
}

/* Grid de métricas responsive */
.metrics-grid {
  display: grid;
  gap: var(--space-md);
  margin-bottom: var(--space-2xl);
  
  /* Mobile: 1 columna */
  grid-template-columns: 1fr;
}

/* Tablet: 2 columnas */
@media (min-width: 768px) {
  .metrics-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: var(--space-lg);
  }
}

/* Desktop: 4 columnas */
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
  align-items: flex-start;
  gap: var(--space-md);
  transition: var(--transition-fast);
}

.metric-card:hover {
  border-color: var(--border-hover);
  transform: translateY(-2px);
  box-shadow: var(--shadow-md);
}

.metric-icon {
  font-size: var(--font-2xl);
  width: 48px;
  height: 48px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--bg-elevated);
  border-radius: var(--radius-md);
}

.metric-content {
  flex: 1;
}

.metric-value {
  font-size: var(--font-2xl);
  font-weight: 600;
  color: var(--text-primary);
  line-height: 1;
}

.metric-label {
  font-size: var(--font-sm);
  color: var(--text-secondary);
  margin-top: var(--space-xs);
}

.metric-trend {
  font-size: var(--font-xs);
  margin-top: var(--space-sm);
  font-weight: 500;
}

.metric-trend.positive {
  color: var(--success);
}

.metric-trend.warning {
  color: var(--warning);
}

.metric-trend.negative {
  color: var(--error);
}

/* Tabla simple */
.table-wrapper {
  background: var(--bg-surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  overflow: hidden;
}

.simple-table {
  width: 100%;
  border-collapse: collapse;
}

.simple-table th {
  background: var(--bg-elevated);
  padding: var(--space-md);
  text-align: left;
  font-weight: 600;
  color: var(--text-primary);
  font-size: var(--font-sm);
  text-transform: uppercase;
  letter-spacing: 0.5px;
  border-bottom: 1px solid var(--border);
}

.simple-table td {
  padding: var(--space-md);
  border-bottom: 1px solid var(--border);
  color: var(--text-primary);
}

.simple-table tr:last-child td {
  border-bottom: none;
}

.simple-table tr:hover {
  background: var(--bg-elevated);
}

/* Stock colors */
.stock-critical {
  color: var(--error);
  font-weight: 600;
}

.stock-low {
  color: var(--warning);
  font-weight: 600;
}

.stock-ok {
  color: var(--success);
  font-weight: 600;
}

/* Badges */
.badge {
  display: inline-block;
  padding: var(--space-xs) var(--space-sm);
  border-radius: var(--radius-md);
  font-size: var(--font-xs);
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.badge-success {
  background: var(--success-bg);
  color: var(--success);
}

.badge-warning {
  background: var(--warning-bg);
  color: var(--warning);
}

.badge-danger {
  background: var(--error-bg);
  color: var(--error);
}

/* Mobile: Tabla responsive */
@media (max-width: 767px) {
  .table-wrapper {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }
  
  .simple-table {
    min-width: 600px;
  }
}
</style>