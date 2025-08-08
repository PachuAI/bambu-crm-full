/**
 * BAMBU useBambuApi Composable - API Client para Dominio Químico
 * 
 * Versión: 1.0.0
 * Fecha: 2025-08-08
 * 
 * 🧪 Engineered for chemical industry by ÍTERA
 * https://iteraestudio.com
 * 
 * Maneja todas las llamadas API específicas del dominio BAMBU
 * con estados de loading, error handling y cache inteligente
 */

import { ref, computed } from 'vue'

export function useBambuApi() {
  // ========================================
  // STATE MANAGEMENT
  // ========================================
  
  const loading = ref(false)
  const error = ref(null)
  const cache = ref(new Map())
  
  // API Base Configuration
  const API_BASE = '/api/v1'
  const CACHE_TTL = 5 * 60 * 1000 // 5 minutos
  
  // ========================================
  // COMPUTED STATE
  // ========================================
  
  const isLoading = computed(() => loading.value)
  const hasError = computed(() => error.value !== null)
  const errorMessage = computed(() => error.value?.message || 'Error desconocido')
  
  // ========================================
  // CORE HTTP METHODS
  // ========================================
  
  const makeRequest = async (endpoint, options = {}) => {
    const {
      method = 'GET',
      data = null,
      useCache = false,
      cacheKey = endpoint,
      headers = {}
    } = options
    
    try {
      loading.value = true
      error.value = null
      
      // Check cache first for GET requests
      if (method === 'GET' && useCache) {
        const cached = getCachedData(cacheKey)
        if (cached) {
          loading.value = false
          return cached
        }
      }
      
      // Get CSRF token for non-GET requests
      if (method !== 'GET') {
        await getCsrfToken()
      }
      
      // Configure request
      const config = {
        method,
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          ...headers
        }
      }
      
      if (data) {
        config.body = JSON.stringify(data)
      }
      
      // Make request
      const response = await fetch(`${API_BASE}${endpoint}`, config)
      
      if (!response.ok) {
        throw new Error(`API Error: ${response.status} - ${response.statusText}`)
      }
      
      const result = await response.json()
      
      // Cache successful GET responses
      if (method === 'GET' && useCache) {
        setCachedData(cacheKey, result)
      }
      
      return result
      
    } catch (err) {
      error.value = err
      console.error('API Request failed:', err)
      throw err
    } finally {
      loading.value = false
    }
  }
  
  // ========================================
  // CACHE MANAGEMENT
  // ========================================
  
  const getCachedData = (key) => {
    const cached = cache.value.get(key)
    if (cached && Date.now() - cached.timestamp < CACHE_TTL) {
      return cached.data
    }
    cache.value.delete(key)
    return null
  }
  
  const setCachedData = (key, data) => {
    cache.value.set(key, {
      data,
      timestamp: Date.now()
    })
  }
  
  const clearCache = (pattern = null) => {
    if (pattern) {
      for (const key of cache.value.keys()) {
        if (key.includes(pattern)) {
          cache.value.delete(key)
        }
      }
    } else {
      cache.value.clear()
    }
  }
  
  // ========================================
  // CSRF TOKEN HANDLING
  // ========================================
  
  const getCsrfToken = async () => {
    await fetch('/sanctum/csrf-cookie')
  }
  
  // ========================================
  // DASHBOARD API METHODS
  // ========================================
  
  const getDashboardMetrics = async () => {
    return makeRequest('/dashboard/metrics', {
      useCache: true,
      cacheKey: 'dashboard-metrics'
    })
  }
  
  const getStockStatus = async () => {
    return makeRequest('/dashboard/stock-status', {
      useCache: true,
      cacheKey: 'stock-status'
    })
  }
  
  const getAlertasCriticas = async () => {
    return makeRequest('/dashboard/alertas', {
      useCache: true,
      cacheKey: 'alertas-criticas'
    })
  }
  
  const getVentasChart = async (periodo = '30d') => {
    return makeRequest(`/dashboard/ventas?periodo=${periodo}`, {
      useCache: true,
      cacheKey: `ventas-chart-${periodo}`
    })
  }
  
  const getProductosTop = async (limit = 10) => {
    return makeRequest(`/dashboard/productos-top?limit=${limit}`, {
      useCache: true,
      cacheKey: `productos-top-${limit}`
    })
  }
  
  // ========================================
  // PRODUCTOS API METHODS
  // ========================================
  
  const getProductos = async (filters = {}) => {
    const queryParams = new URLSearchParams(filters).toString()
    return makeRequest(`/productos${queryParams ? '?' + queryParams : ''}`)
  }
  
  const getProducto = async (id) => {
    return makeRequest(`/productos/${id}`, {
      useCache: true,
      cacheKey: `producto-${id}`
    })
  }
  
  const updateStock = async (productoId, cantidad) => {
    const result = await makeRequest(`/productos/${productoId}/stock`, {
      method: 'PATCH',
      data: { cantidad }
    })
    
    // Clear related cache
    clearCache('productos')
    clearCache('stock-status')
    clearCache('dashboard-metrics')
    
    return result
  }
  
  // ========================================
  // CLIENTES API METHODS
  // ========================================
  
  const getClientes = async (filters = {}) => {
    const queryParams = new URLSearchParams(filters).toString()
    return makeRequest(`/clientes${queryParams ? '?' + queryParams : ''}`)
  }
  
  const getCliente = async (id) => {
    return makeRequest(`/clientes/${id}`, {
      useCache: true,
      cacheKey: `cliente-${id}`
    })
  }
  
  // ========================================
  // PEDIDOS API METHODS  
  // ========================================
  
  const getPedidos = async (filters = {}) => {
    const queryParams = new URLSearchParams(filters).toString()
    return makeRequest(`/pedidos${queryParams ? '?' + queryParams : ''}`)
  }
  
  const createPedido = async (pedidoData) => {
    const result = await makeRequest('/pedidos', {
      method: 'POST',
      data: pedidoData
    })
    
    // Clear related cache
    clearCache('pedidos')
    clearCache('dashboard-metrics')
    clearCache('stock-status')
    
    return result
  }
  
  const updatePedidoStatus = async (pedidoId, status) => {
    const result = await makeRequest(`/pedidos/${pedidoId}/status`, {
      method: 'PATCH',
      data: { status }
    })
    
    clearCache('pedidos')
    clearCache('dashboard-metrics')
    
    return result
  }
  
  // ========================================
  // COTIZACIONES API METHODS
  // ========================================
  
  const getCotizaciones = async (filters = {}) => {
    const queryParams = new URLSearchParams(filters).toString()
    return makeRequest(`/cotizaciones${queryParams ? '?' + queryParams : ''}`)
  }
  
  const createCotizacion = async (cotizacionData) => {
    return makeRequest('/cotizaciones', {
      method: 'POST',
      data: cotizacionData
    })
  }
  
  const convertirCotizacionAPedido = async (cotizacionId) => {
    const result = await makeRequest(`/cotizaciones/${cotizacionId}/convert`, {
      method: 'POST'
    })
    
    clearCache('cotizaciones')
    clearCache('pedidos')
    clearCache('dashboard-metrics')
    
    return result
  }
  
  // ========================================
  // UTILITY METHODS
  // ========================================
  
  const retry = async (operation, maxAttempts = 3, delay = 1000) => {
    for (let attempt = 1; attempt <= maxAttempts; attempt++) {
      try {
        return await operation()
      } catch (err) {
        if (attempt === maxAttempts) throw err
        await new Promise(resolve => setTimeout(resolve, delay * attempt))
      }
    }
  }
  
  const uploadFile = async (endpoint, file, additionalData = {}) => {
    try {
      loading.value = true
      error.value = null
      
      await getCsrfToken()
      
      const formData = new FormData()
      formData.append('file', file)
      
      Object.entries(additionalData).forEach(([key, value]) => {
        formData.append(key, value)
      })
      
      const response = await fetch(`${API_BASE}${endpoint}`, {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
        },
        body: formData
      })
      
      if (!response.ok) {
        throw new Error(`Upload failed: ${response.status}`)
      }
      
      return await response.json()
      
    } catch (err) {
      error.value = err
      throw err
    } finally {
      loading.value = false
    }
  }
  
  // ========================================
  // BATCH OPERATIONS
  // ========================================
  
  const batchRequest = async (requests) => {
    const results = await Promise.allSettled(
      requests.map(req => makeRequest(req.endpoint, req.options))
    )
    
    return results.map((result, index) => ({
      ...requests[index],
      status: result.status,
      data: result.status === 'fulfilled' ? result.value : null,
      error: result.status === 'rejected' ? result.reason : null
    }))
  }
  
  // ========================================
  // ERROR RECOVERY
  // ========================================
  
  const clearError = () => {
    error.value = null
  }
  
  const isNetworkError = (err) => {
    return err.message.includes('Failed to fetch') || err.message.includes('network')
  }
  
  const isAuthError = (err) => {
    return err.message.includes('401') || err.message.includes('403')
  }
  
  // ========================================
  // RETURN API
  // ========================================
  
  return {
    // State
    loading: isLoading,
    error: hasError,
    errorMessage,
    
    // Core methods
    makeRequest,
    retry,
    uploadFile,
    batchRequest,
    
    // Cache management
    clearCache,
    
    // Dashboard
    getDashboardMetrics,
    getStockStatus,
    getAlertasCriticas,
    getVentasChart,
    getProductosTop,
    
    // Productos
    getProductos,
    getProducto,
    updateStock,
    
    // Clientes
    getClientes,
    getCliente,
    
    // Pedidos
    getPedidos,
    createPedido,
    updatePedidoStatus,
    
    // Cotizaciones
    getCotizaciones,
    createCotizacion,
    convertirCotizacionAPedido,
    
    // Error handling
    clearError,
    isNetworkError,
    isAuthError
  }
}