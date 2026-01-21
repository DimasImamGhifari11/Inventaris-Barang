<template>
  <div class="login-container">
    <!-- Notifikasi Pop-up -->
    <Transition name="notification">
      <div v-if="showNotification" class="notification">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
          <polyline points="22 4 12 14.01 9 11.01"></polyline>
        </svg>
        <span>Login berhasil</span>
      </div>
    </Transition>

    <div class="login-box">
      <div class="login-header">
        <h1>Inventaris Barang</h1>
        <p>Dinas Komunikasi Informatika dan Persandian Hulu Sungai Utara</p>
      </div>

      <form @submit.prevent="handleLogin" class="login-form">
        <div class="form-group">
          <input
            id="username"
            v-model="form.username"
            type="text"
            placeholder="Username"
            required
            autocomplete="username"
          />
        </div>

        <div class="form-group password-group">
          <input
            id="password"
            v-model="form.password"
            :type="showPassword ? 'text' : 'password'"
            placeholder="Password"
            required
            autocomplete="current-password"
          />
          <button
            type="button"
            class="toggle-password"
            @click="showPassword = !showPassword"
          >
            <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
              <circle cx="12" cy="12" r="3"></circle>
            </svg>
            <svg v-else xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
              <line x1="1" y1="1" x2="23" y2="23"></line>
            </svg>
          </button>
        </div>

        <div v-if="errorMessage" class="error-message">
          {{ errorMessage }}
        </div>

        <button type="submit" class="login-button" :disabled="loading">
          <span v-if="loading">Memuat...</span>
          <span v-else>Login</span>
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '../api'

const router = useRouter()

const form = ref({
  username: '',
  password: ''
})

const loading = ref(false)
const errorMessage = ref('')
const showPassword = ref(false)
const showNotification = ref(false)

const handleLogin = async () => {
  loading.value = true
  errorMessage.value = ''

  try {
    const response = await api.post('/login', {
      username: form.value.username,
      password: form.value.password
    })

    if (response.data.success) {
      localStorage.setItem('token', response.data.token)
      localStorage.setItem('user', JSON.stringify(response.data.user))

      // Tampilkan notifikasi
      showNotification.value = true

      // Redirect setelah 1.5 detik
      setTimeout(() => {
        router.push('/dashboard')
      }, 1500)
    }
  } catch (error) {
    if (error.response && error.response.data) {
      errorMessage.value = error.response.data.message || 'Login gagal. Periksa username dan password Anda.'
    } else {
      errorMessage.value = 'Terjadi kesalahan. Silakan coba lagi.'
    }
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.login-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f5f5f7;
  padding: 20px;
  position: relative;
}

/* Notifikasi */
.notification {
  position: fixed;
  top: 32px;
  left: 50%;
  transform: translateX(-50%);
  background: #ffffff;
  border: 1px solid #e5e5e5;
  border-radius: 10px;
  padding: 14px 24px;
  display: flex;
  align-items: center;
  gap: 10px;
  color: #1d1d1f;
  font-size: 14px;
  font-weight: 500;
  z-index: 1000;
}

.notification svg {
  color: #34c759;
}

/* Animasi notifikasi */
.notification-enter-active {
  animation: slideDown 0.4s ease-out;
}

.notification-leave-active {
  animation: slideUp 0.3s ease-in;
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateX(-50%) translateY(-20px);
  }
  to {
    opacity: 1;
    transform: translateX(-50%) translateY(0);
  }
}

@keyframes slideUp {
  from {
    opacity: 1;
    transform: translateX(-50%) translateY(0);
  }
  to {
    opacity: 0;
    transform: translateX(-50%) translateY(-20px);
  }
}

.login-box {
  background: #ffffff;
  border-radius: 12px;
  width: 100%;
  max-width: 360px;
  padding: 40px 36px;
  border: 1px solid #e5e5e5;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.login-box:hover {
  transform: translateY(-0.9px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
}

.login-header {
  text-align: center;
  margin-bottom: 28px;
}

.login-header h1 {
  color: #1d1d1f;
  font-size: 24px;
  font-weight: 700;
  margin: 0 0 6px 0;
  letter-spacing: -0.5px;
}

.login-header p {
  color: #86868b;
  font-size: 14px;
  margin: 0;
  font-weight: 400;
}

.login-form {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.password-group {
  position: relative;
}

.password-group input {
  padding-right: 44px;
}

.toggle-password {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  padding: 4px;
  cursor: pointer;
  color: #86868b;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: color 0.2s ease;
}

.toggle-password:hover {
  color: #1d1d1f;
}

.form-group input {
  background: #f5f5f7;
  border: 1px solid #d2d2d7;
  border-radius: 8px;
  padding: 12px 14px;
  color: #1d1d1f;
  font-size: 14px;
  font-weight: 400;
  transition: all 0.2s ease;
  outline: none;
  width: 100%;
}

.form-group input::placeholder {
  color: #86868b;
}

.form-group input:hover {
  border-color: #a1a1a6;
}

.form-group input:focus {
  border-color: #0071e3;
  background: #ffffff;
}

.error-message {
  background: #fff5f5;
  border: 1px solid #fed7d7;
  color: #c53030;
  padding: 10px 14px;
  border-radius: 8px;
  font-size: 13px;
  text-align: center;
}

.login-button {
  background: #1d1d1f;
  color: #ffffff;
  border: none;
  border-radius: 8px;
  padding: 12px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  margin-top: 8px;
}

.login-button:hover:not(:disabled) {
  background: #333336;
}

.login-button:active:not(:disabled) {
  transform: scale(0.98);
}

.login-button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

@media (max-width: 400px) {
  .login-box {
    padding: 32px 24px;
    margin: 0 16px;
  }

  .login-header h1 {
    font-size: 22px;
  }
}
</style>
