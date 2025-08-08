<template>
  <div class="login-container">
    <div class="login-wrapper">
      <!-- Logo and Title -->
      <div class="login-header">
        <h1 class="login-title">BAMBU</h1>
        <h2 class="login-subtitle">Iniciar Sesión</h2>
        <p class="login-description">
          Ingresa a tu cuenta para continuar
        </p>
      </div>

      <!-- Login Form -->
      <form @submit.prevent="handleLogin" class="login-form">
        <div class="login-card">
          <!-- Email Field -->
          <div class="form-group">
            <label for="email" class="form-label">
              Correo Electrónico
            </label>
            <input
              id="email"
              v-model="form.email"
              type="email"
              required
              class="form-input"
              placeholder="admin@bambu.com"
              :disabled="authStore.loading"
            />
          </div>

          <!-- Password Field -->
          <div class="form-group">
            <label for="password" class="form-label">
              Contraseña
            </label>
            <input
              id="password"
              v-model="form.password"
              type="password"
              required
              class="form-input"
              placeholder="••••••••"
              :disabled="authStore.loading"
            />
          </div>

          <!-- Remember Me -->
          <div class="form-actions">
            <label class="checkbox-wrapper">
              <input
                v-model="form.remember"
                type="checkbox"
                class="form-checkbox"
                :disabled="authStore.loading"
              />
              <span class="checkbox-label">Recordarme</span>
            </label>
            
            <a href="#" class="forgot-password">
              ¿Olvidaste tu contraseña?
            </a>
          </div>

          <!-- Quick Login Button -->
          <button
            type="button"
            @click="fillCredentials"
            class="btn-secondary"
          >
            🚀 Usar credenciales de prueba
          </button>

          <!-- Error Message -->
          <div v-if="authStore.error" class="error-message">
            <p>Error al iniciar sesión</p>
          </div>

          <!-- Submit Button -->
          <button
            type="submit"
            class="btn-primary"
            :disabled="authStore.loading"
          >
            <div v-if="authStore.loading" class="loading-spinner"></div>
            {{ authStore.loading ? 'Iniciando sesión...' : 'Iniciar Sesión' }}
          </button>
        </div>

        <!-- Register Link -->
        <div class="register-link">
          <p>
            ¿No tienes una cuenta?
            <router-link to="/register" class="link-primary">
              Regístrate aquí
            </router-link>
          </p>
        </div>
      </form>

      <!-- Demo Credentials Info -->
      <div class="demo-credentials">
        <p class="demo-title">💡 Credenciales de Prueba:</p>
        <p class="demo-text">Email: admin@bambu.com</p>
        <p class="demo-text">Contraseña: password</p>
        <p class="demo-hint">Usa el botón "🚀 Usar credenciales de prueba" para autocompletar</p>
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

<style scoped>
/* === LOGIN CONTAINER === */
.login-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--bg-base);
  padding: var(--space-md);
}

.login-wrapper {
  width: 100%;
  max-width: 400px;
  display: flex;
  flex-direction: column;
  gap: var(--space-xl);
}

/* === LOGIN HEADER === */
.login-header {
  text-align: center;
}

.login-title {
  font-size: var(--font-4xl);
  font-weight: 700;
  color: var(--primary);
  margin-bottom: var(--space-md);
  letter-spacing: -0.5px;
}

.login-subtitle {
  font-size: var(--font-2xl);
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: var(--space-sm);
}

.login-description {
  color: var(--text-secondary);
  font-size: var(--font-base);
}

/* === LOGIN FORM === */
.login-form {
  display: flex;
  flex-direction: column;
  gap: var(--space-lg);
}

.login-card {
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

/* === FORM ACTIONS === */
.form-actions {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding-top: var(--space-xs);
}

.checkbox-wrapper {
  display: flex;
  align-items: center;
  gap: var(--space-xs);
  cursor: pointer;
}

.form-checkbox {
  width: 16px;
  height: 16px;
  accent-color: var(--primary);
  cursor: pointer;
}

.checkbox-label {
  font-size: var(--font-sm);
  color: var(--text-secondary);
  cursor: pointer;
}

.forgot-password {
  font-size: var(--font-sm);
  color: var(--primary);
  text-decoration: none;
  transition: var(--transition-fast);
}

.forgot-password:hover {
  color: var(--primary-hover);
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

.btn-secondary {
  width: 100%;
  padding: var(--space-sm) var(--space-md);
  background: var(--bg-elevated);
  color: var(--text-secondary);
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  font-size: var(--font-sm);
  font-weight: 500;
  cursor: pointer;
  transition: var(--transition-fast);
}

.btn-secondary:hover {
  background: var(--bg-base);
  border-color: var(--border-hover);
  color: var(--text-primary);
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

/* === REGISTER LINK === */
.register-link {
  text-align: center;
}

.register-link p {
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

/* === DEMO CREDENTIALS === */
.demo-credentials {
  background: var(--primary-bg);
  border: 1px solid var(--primary);
  border-radius: var(--radius-md);
  padding: var(--space-md);
}

.demo-title {
  color: var(--primary);
  font-size: var(--font-sm);
  font-weight: 500;
  margin-bottom: var(--space-xs);
}

.demo-text {
  color: var(--text-secondary);
  font-size: var(--font-xs);
  font-family: monospace;
}

.demo-hint {
  color: var(--text-muted);
  font-size: var(--font-xs);
  margin-top: var(--space-xs);
}

/* === RESPONSIVE === */
@media (max-width: 640px) {
  .login-container {
    padding: var(--space-sm);
  }
  
  .login-wrapper {
    gap: var(--space-lg);
  }
  
  .login-card {
    padding: var(--space-lg);
  }
  
  .form-actions {
    flex-direction: column;
    align-items: flex-start;
    gap: var(--space-sm);
  }
}
</style>