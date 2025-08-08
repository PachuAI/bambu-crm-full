import { defineStore } from 'pinia'
import axios from 'axios'
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'

interface User {
  id: number
  name: string
  email: string
  email_verified_at?: string
  created_at: string
  updated_at: string
}

interface LoginCredentials {
  email: string
  password: string
  remember?: boolean
}

interface RegisterData {
  name: string
  email: string
  password: string
  password_confirmation: string
}

export const useAuthStore = defineStore('auth', () => {
  const router = useRouter()
  
  // State
  const user = ref<User | null>(null)
  const token = ref<string | null>(localStorage.getItem('auth_token'))
  const loading = ref(false)
  const error = ref<string | null>(null)
  
  // Getters
  const isAuthenticated = computed(() => !!token.value && !!user.value)
  const userName = computed(() => user.value?.name || '')
  const userEmail = computed(() => user.value?.email || '')
  
  // Configure axios defaults
  if (token.value) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
  }
  
  // Actions
  async function login(credentials: LoginCredentials) {
    loading.value = true
    error.value = null
    
    try {
      // Login directly without CSRF for API
      const response = await axios.post('/api/v1/auth/login', credentials)
      
      if (response.data.token) {
        token.value = response.data.token
        user.value = response.data.user
        
        // Save token to localStorage
        localStorage.setItem('auth_token', response.data.token)
        
        // Set axios default header
        axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`
        
        // Redirect to dashboard
        await router.push('/dashboard')
        
        return true
      }
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Error al iniciar sesión'
      console.error('Login error:', err)
      return false
    } finally {
      loading.value = false
    }
  }
  
  async function register(data: RegisterData) {
    loading.value = true
    error.value = null
    
    try {
      // Get CSRF token first
      await axios.get('/sanctum/csrf-cookie')
      
      // Register
      const response = await axios.post('/api/v1/auth/register', data)
      
      if (response.data.token) {
        token.value = response.data.token
        user.value = response.data.user
        
        // Save token to localStorage
        localStorage.setItem('auth_token', response.data.token)
        
        // Set axios default header
        axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`
        
        // Redirect to dashboard
        await router.push('/dashboard')
        
        return true
      }
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Error al registrar usuario'
      console.error('Register error:', err)
      return false
    } finally {
      loading.value = false
    }
  }
  
  async function logout() {
    loading.value = true
    
    try {
      await axios.post('/api/v1/auth/logout')
    } catch (err) {
      console.error('Logout error:', err)
    } finally {
      // Clear local data regardless of API response
      token.value = null
      user.value = null
      localStorage.removeItem('auth_token')
      delete axios.defaults.headers.common['Authorization']
      
      loading.value = false
      
      // Redirect to login
      await router.push('/login')
    }
  }
  
  async function fetchUser() {
    if (!token.value) return false
    
    loading.value = true
    error.value = null
    
    try {
      const response = await axios.get('/api/v1/auth/user')
      user.value = response.data
      return true
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Error al obtener usuario'
      
      // If unauthorized, clear token
      if (err.response?.status === 401) {
        token.value = null
        user.value = null
        localStorage.removeItem('auth_token')
        delete axios.defaults.headers.common['Authorization']
        await router.push('/login')
      }
      
      return false
    } finally {
      loading.value = false
    }
  }
  
  function clearError() {
    error.value = null
  }
  
  return {
    // State
    user,
    token,
    loading,
    error,
    
    // Getters
    isAuthenticated,
    userName,
    userEmail,
    
    // Actions
    login,
    register,
    logout,
    fetchUser,
    clearError
  }
})