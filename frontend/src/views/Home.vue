<template>
  <div class="page-container">
    <div class="welcome-card">
      <h2>Selamat datang, {{ user?.name }}!</h2>
      <p>Sistem Inventaris Barang - Dinas Komunikasi Informatika dan Persandian</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const user = ref(null)

onMounted(() => {
  const userData = localStorage.getItem('user')
  if (userData) {
    user.value = JSON.parse(userData)
  }
})
</script>

<style scoped>
.page-container {
  padding: 24px 0;
}

.welcome-card {
  background: #ffffff;
  border-radius: 12px;
  border: 1px solid #e5e5e5;
  padding: 32px;
}

.welcome-card h2 {
  margin: 0 0 8px 0;
  font-size: 20px;
  font-weight: 600;
  color: #1d1d1f;
}

.welcome-card p {
  margin: 0;
  font-size: 14px;
  color: #86868b;
}
</style>
