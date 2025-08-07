<template>
  <div class="min-h-screen flex items-center justify-center bg-bg-primary px-4 py-12">
    <div class="max-w-md w-full space-y-8">
      <!-- Logo and Title -->
      <div class="text-center">
        <h1 class="text-4xl font-bold text-primary mb-2">BAMBU</h1>
        <h2 class="text-2xl font-semibold text-text-primary">Crear Cuenta</h2>
        <p class="mt-2 text-text-secondary">
          Regístrate para comenzar a usar el sistema
        </p>
      </div>

      <!-- Register Form -->
      <form @submit.prevent="handleRegister" class="mt-8 space-y-6">
        <div class="bg-bg-secondary border border-surface-1 rounded-lg p-6 space-y-4">
          <!-- Name Field -->
          <div class="form-group">
            <label for="name" class="label">Nombre Completo</label>
            <input
              id="name"
              v-model="form.name"
              type="text"
              required
              class="input"
              placeholder="Juan Pérez"
              :disabled="authStore.loading"
            />
          </div>

          <!-- Email Field -->
          <div class="form-group">
            <label for="email" class="label">Correo Electrónico</label>
            <input
              id="email"
              v-model="form.email"
              type="email"
              required
              class="input"
              placeholder="juan@empresa.com"
              :disabled="authStore.loading"
            />
          </div>

          <!-- Password Field -->
          <div class="form-group">
            <label for="password" class="label">Contraseña</label>
            <input
              id="password"
              v-model="form.password"
              type="password"
              required
              minlength="8"
              class="input"
              placeholder="Mínimo 8 caracteres"
              :disabled="authStore.loading"
            />
          </div>

          <!-- Password Confirmation Field -->
          <div class="form-group">
            <label for="password_confirmation" class="label">Confirmar Contraseña</label>
            <input
              id="password_confirmation"
              v-model="form.password_confirmation"
              type="password"
              required
              minlength="8"
              class="input"
              placeholder="Repite la contraseña"
              :disabled="authStore.loading"
            />
          </div>

          <!-- Error Message -->
          <div v-if="authStore.error" class="bg-danger/10 border border-danger/20 rounded-md p-3">
            <p class="text-sm text-danger">{{ authStore.error }}</p>
          </div>

          <!-- Submit Button -->
          <button
            type="submit"
            class="w-full btn btn-primary btn-lg"
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
            {{ authStore.loading ? 'Creando cuenta...' : 'Crear Cuenta' }}
          </button>
        </div>

        <!-- Login Link -->
        <div class="text-center">
          <p class="text-text-secondary">
            ¿Ya tienes una cuenta?
            <router-link
              to="/login"
              class="text-primary hover:text-primary-hover font-medium"
            >
              Inicia sesión aquí
            </router-link>
          </p>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { reactive, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
})

onMounted(() => {
  authStore.clearError()
})

const handleRegister = async () => {
  if (form.password !== form.password_confirmation) {
    authStore.error = 'Las contraseñas no coinciden'
    return
  }
  
  const success = await authStore.register(form)
  
  if (success) {
    router.push('/dashboard')
  }
}
</script>

<style scoped></style>