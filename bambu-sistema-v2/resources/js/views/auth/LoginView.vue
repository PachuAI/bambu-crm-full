<template>
  <div class="min-h-screen flex items-center justify-center bg-slate-900">
    <div class="max-w-md w-full space-y-8 px-6">
      <!-- Logo and Title -->
      <div class="text-center">
        <h1 class="text-4xl font-bold mb-4 text-indigo-400">BAMBU</h1>
        <h2 class="text-2xl font-semibold text-white mb-3">Iniciar Sesión</h2>
        <p class="text-slate-400">
          Ingresa a tu cuenta para continuar
        </p>
      </div>

      <!-- Login Form -->
      <form @submit.prevent="handleLogin" class="mt-8 space-y-6">
        <div class="bg-slate-800 border border-slate-700 rounded-lg p-6 space-y-6">
          <!-- Email Field -->
          <div class="space-y-2">
            <label for="email" class="block text-sm font-medium text-slate-300">
              Correo Electrónico
            </label>
            <input
              id="email"
              v-model="form.email"
              type="email"
              required
              class="w-full px-4 py-3 bg-slate-700 border border-slate-600 rounded-md text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-150"
              placeholder="admin@bambu.com"
              :disabled="authStore.loading"
            />
          </div>

          <!-- Password Field -->
          <div class="space-y-2">
            <label for="password" class="block text-sm font-medium text-slate-300">
              Contraseña
            </label>
            <input
              id="password"
              v-model="form.password"
              type="password"
              required
              class="w-full px-4 py-3 bg-slate-700 border border-slate-600 rounded-md text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-150"
              placeholder="••••••••"
              :disabled="authStore.loading"
            />
          </div>

          <!-- Remember Me -->
          <div class="flex items-center justify-between pt-2">
            <label class="flex items-center">
              <input
                v-model="form.remember"
                type="checkbox"
                class="w-4 h-4 text-indigo-600 bg-slate-700 border-slate-600 rounded focus:ring-indigo-500"
                :disabled="authStore.loading"
              />
              <span class="ml-2 text-sm text-slate-400">Recordarme</span>
            </label>
            
            <a href="#" class="text-sm text-indigo-400 hover:text-indigo-300">
              ¿Olvidaste tu contraseña?
            </a>
          </div>

          <!-- Quick Login Button -->
          <button
            type="button"
            @click="fillCredentials"
            class="w-full px-4 py-2 bg-slate-700 hover:bg-slate-600 border border-slate-600 rounded-md font-medium text-sm text-slate-300 transition-all duration-150"
          >
            🚀 Usar credenciales de prueba
          </button>

          <!-- Error Message -->
          <div v-if="authStore.error" class="bg-red-900/20 border border-red-700/50 rounded-md p-3">
            <p class="text-sm text-red-400">{{ authStore.error }}</p>
          </div>

          <!-- Submit Button -->
          <button
            type="submit"
            class="w-full px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md font-medium text-base inline-flex items-center justify-center gap-2 transition-all duration-150 hover:scale-[1.02] active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
            :disabled="authStore.loading"
          >
            <svg
              v-if="authStore.loading"
              class="animate-spin -ml-1 mr-2 h-5 w-5 text-white"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
            >
              <circle
                class="opacity-25"
                cx="12"
                cy="12"
                r="10"
                stroke="currentColor"
                stroke-width="4"
              ></circle>
              <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
              ></path>
            </svg>
            {{ authStore.loading ? 'Iniciando sesión...' : 'Iniciar Sesión' }}
          </button>
        </div>

        <!-- Register Link -->
        <div class="text-center">
          <p class="text-slate-400">
            ¿No tienes una cuenta?
            <router-link
              to="/register"
              class="font-medium text-indigo-400 hover:text-indigo-300"
            >
              Regístrate aquí
            </router-link>
          </p>
        </div>
      </form>

      <!-- Demo Credentials Info -->
      <div class="bg-indigo-900/20 border border-indigo-700/50 rounded-lg p-4">
        <p class="text-sm font-medium mb-2 text-indigo-300">💡 Credenciales de Prueba:</p>
        <p class="text-xs text-slate-400">Email: admin@bambu.com</p>
        <p class="text-xs text-slate-400">Contraseña: password</p>
        <p class="text-xs text-slate-500 mt-2">Usa el botón "🚀 Usar credenciales de prueba" para autocompletar</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { reactive, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

const form = reactive({
  email: '',
  password: '',
  remember: true
})

onMounted(() => {
  authStore.clearError()
  // Ensure dark theme is applied
  document.documentElement.classList.add('dark')
  document.body.className = 'bg-slate-900 text-white'
})

const fillCredentials = () => {
  form.email = 'admin@bambu.com'
  form.password = 'password'
}

const handleLogin = async () => {
  const success = await authStore.login(form)
  
  if (success) {
    const redirectTo = route.query.redirect as string || '/dashboard'
    router.push(redirectTo)
  }
}
</script>