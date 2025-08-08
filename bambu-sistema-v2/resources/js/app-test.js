/**
 * app-test.js - Archivo de prueba para validar el layout canónico
 * 
 * Este archivo monta una aplicación Vue mínima para probar
 * que el AppShell y el patrón canónico funcionan correctamente.
 */

import { createApp } from 'vue'
import { createRouter, createWebHistory } from 'vue-router'

// Importar componentes
import AppShell from './layouts/AppShell.vue'
import DashboardPage from './pages/DashboardPage.vue'

// Configurar rutas
const routes = [
  {
    path: '/',
    redirect: '/dashboard'
  },
  {
    path: '/dashboard',
    component: DashboardPage
  },
  {
    path: '/productos',
    component: { template: '<div><h1>Productos</h1><p>Página de productos (placeholder)</p></div>' }
  },
  {
    path: '/clientes',
    component: { template: '<div><h1>Clientes</h1><p>Página de clientes (placeholder)</p></div>' }
  },
  {
    path: '/pedidos',
    component: { template: '<div><h1>Pedidos</h1><p>Página de pedidos (placeholder)</p></div>' }
  },
  {
    path: '/stock',
    component: { template: '<div><h1>Stock</h1><p>Página de stock (placeholder)</p></div>' }
  },
  {
    path: '/reportes',
    component: { template: '<div><h1>Reportes</h1><p>Página de reportes (placeholder)</p></div>' }
  }
]

// Crear router
const router = createRouter({
  history: createWebHistory(),
  routes
})

// Crear y montar app
const app = createApp(AppShell)
app.use(router)
app.mount('#app')