<template>
  <div class="register-container">
    <div class="register-wrapper">
      <!-- Logo and Title -->
      <div class="register-header">
        <h1 class="register-title">BAMBU</h1>
        <h2 class="register-subtitle">Crear Cuenta</h2>
        <p class="register-description">
          Regístrate para comenzar a usar el sistema
        </p>
      </div>

      <!-- Register Form -->
      <form @submit.prevent="handleRegister" class="register-form">
        <div class="register-card">
          <!-- Name Field -->
          <div class="form-group">
            <label for="name" class="form-label">Nombre Completo</label>
            <input
              id="name"
              v-model="form.name"
              type="text"
              required
              class="form-input"
              placeholder="Juan Pérez"
              :disabled="authStore.loading"
            />
          </div>

          <!-- Email Field -->
          <div class="form-group">
            <label for="email" class="form-label">Correo Electrónico</label>
            <input
              id="email"
              v-model="form.email"
              type="email"
              required
              class="form-input"
              placeholder="juan@empresa.com"
              :disabled="authStore.loading"
            />
          </div>

          <!-- Password Field -->
          <div class="form-group">
            <label for="password" class="form-label">Contraseña</label>
            <input
              id="password"
              v-model="form.password"
              type="password"
              required
              minlength="8"
              class="form-input"
              placeholder="Mínimo 8 caracteres"
              :disabled="authStore.loading"
            />
          </div>

          <!-- Password Confirmation Field -->
          <div class="form-group">
            <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
            <input
              id="password_confirmation"
              v-model="form.password_confirmation"
              type="password"
              required
              minlength="8"
              class="form-input"
              placeholder="Repite la contraseña"
              :disabled="authStore.loading"
            />
          </div>

          <!-- Error Message -->
          <div v-if="authStore.error" class="error-message">
            <p>{{ authStore.error }}</p>
          </div>

          <!-- Submit Button -->
          <button
            type="submit"
            class="btn-primary"
            :disabled="authStore.loading"
          >
            <div v-if="authStore.loading" class="loading-spinner"></div>
            {{ authStore.loading ? 'Creando cuenta...' : 'Crear Cuenta' }}
          </button>
        </div>

        <!-- Login Link -->
        <div class="login-link">
          <p>
            ¿Ya tienes una cuenta?
            <router-link to="/login" class="link-primary">
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

<style scoped>
/* === REGISTER CONTAINER === */
.register-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--bg-base);
  padding: var(--space-md);
}

.register-wrapper {
  width: 100%;
  max-width: 400px;
  display: flex;
  flex-direction: column;
  gap: var(--space-xl);
}

/* === REGISTER HEADER === */
.register-header {
  text-align: center;
}

.register-title {
  font-size: var(--font-4xl);
  font-weight: 700;
  color: var(--primary);
  margin-bottom: var(--space-md);
  letter-spacing: -0.5px;
}

.register-subtitle {
  font-size: var(--font-2xl);
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: var(--space-sm);
}

.register-description {
  color: var(--text-secondary);
  font-size: var(--font-base);
}

/* === REGISTER FORM === */
.register-form {
  display: flex;
  flex-direction: column;
  gap: var(--space-lg);
}

.register-card {
  background: var(--bg-surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  padding: var(--space-xl);
  display: flex;
  flex-direction: column;
  gap: var(--space-lg);
}

/* === FORM ELEMENTS === */
.form-group {
  display: flex;
  flex-direction: column;
  gap: var(--space-xs);
}

.form-label {
  font-size: var(--font-sm);
  font-weight: 500;
  color: var(--text-primary);
}

.form-input {
  padding: var(--space-sm) var(--space-md);
  background: var(--bg-elevated);
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  color: var(--text-primary);
  font-size: var(--font-base);
  transition: var(--transition-fast);
}

.form-input::placeholder {
  color: var(--text-muted);
}

.form-input:focus {
  outline: none;
  border-color: var(--primary);
  box-shadow: 0 0 0 3px var(--primary-bg);
}

.form-input:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* === BUTTONS === */
.btn-primary {
  width: 100%;
  padding: var(--space-md) var(--space-lg);
  background: var(--primary);
  color: var(--text-inverse);
  border: none;
  border-radius: var(--radius-md);
  font-size: var(--font-base);
  font-weight: 500;
  cursor: pointer;
  transition: var(--transition-fast);
  display: flex;
  align-items: center;
  justify-content: center;
  gap: var(--space-xs);
  min-height: 48px;
}

.btn-primary:hover:not(:disabled) {
  background: var(--primary-hover);
  transform: translateY(-1px);
}

.btn-primary:active:not(:disabled) {
  background: var(--primary-active);
  transform: translateY(0);
}

.btn-primary:disabled {
  opacity: 0.5;
  cursor: not-allowed;
  transform: none;
}

/* === LOADING SPINNER === */
.loading-spinner {
  width: 20px;
  height: 20px;
  border: 2px solid transparent;
  border-top: 2px solid currentColor;
  border-radius: var(--radius-full);
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* === ERROR MESSAGE === */
.error-message {
  background: var(--error-bg);
  border: 1px solid var(--error);
  border-radius: var(--radius-md);
  padding: var(--space-md);
  color: var(--error);
  font-size: var(--font-sm);
}

/* === LOGIN LINK === */
.login-link {
  text-align: center;
}

.login-link p {
  color: var(--text-secondary);
  font-size: var(--font-sm);
}

.link-primary {
  color: var(--primary);
  text-decoration: none;
  font-weight: 500;
  transition: var(--transition-fast);
}

.link-primary:hover {
  color: var(--primary-hover);
}

/* === RESPONSIVE === */
@media (max-width: 640px) {
  .register-container {
    padding: var(--space-sm);
  }
  
  .register-wrapper {
    gap: var(--space-lg);
  }
  
  .register-card {
    padding: var(--space-lg);
  }
}
</style>