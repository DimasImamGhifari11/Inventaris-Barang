<template>
  <div class="dashboard" :class="{ 'sidebar-open': sidebarOpen }">
    <!-- Sidebar -->
    <aside class="sidebar">
      <nav class="sidebar-nav">
        <router-link to="/dashboard" class="nav-item" exact-active-class="active">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
            <polyline points="9 22 9 12 15 12 15 22"></polyline>
          </svg>
          <span>Home</span>
        </router-link>
        <router-link to="/dashboard/tambah-data" class="nav-item" active-class="active">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="8" x2="12" y2="16"></line>
            <line x1="8" y1="12" x2="16" y2="12"></line>
          </svg>
          <span>Tambah Data Aset</span>
        </router-link>
        <router-link to="/dashboard/update-data" class="nav-item" active-class="active">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
          </svg>
          <span>Update Data Aset</span>
        </router-link>
        <router-link to="/dashboard/hapus-data" class="nav-item" active-class="active">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="3 6 5 6 21 6"></polyline>
            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
            <line x1="10" y1="11" x2="10" y2="17"></line>
            <line x1="14" y1="11" x2="14" y2="17"></line>
          </svg>
          <span>Hapus Data Aset</span>
        </router-link>
        <router-link to="/dashboard/riwayat" class="nav-item" active-class="active">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"></circle>
            <polyline points="12 6 12 12 16 14"></polyline>
          </svg>
          <span>Riwayat</span>
        </router-link>
      </nav>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
      <div class="dashboard-header">
        <div class="header-left">
          <button class="hamburger-button" :class="{ 'is-active': sidebarOpen }" @click="toggleSidebar">
            <span class="hamburger-icon"></span>
          </button>
          <h1>Inventaris Barang Diskominfo</h1>
        </div>
        <button @click="handleLogout" class="logout-button">Logout</button>
      </div>
      <router-view v-slot="{ Component }">
        <transition name="page" mode="out-in">
          <component :is="Component" />
        </transition>
      </router-view>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const sidebarOpen = ref(false)

const toggleSidebar = () => {
  sidebarOpen.value = !sidebarOpen.value
}

const handleLogout = () => {
  localStorage.removeItem('token')
  localStorage.removeItem('user')
  router.push('/login')
}
</script>

<style scoped>
.dashboard {
  min-height: 100vh;
  background: #f5f5f7;
  display: flex;
}

/* Sidebar */
.sidebar {
  width: 0;
  min-height: 100vh;
  background: #ffffff;
  border-right: 1px solid #e5e5e5;
  overflow: hidden;
  transition: width 0.3s ease;
  flex-shrink: 0;
}

.dashboard.sidebar-open .sidebar {
  width: 240px;
}

.sidebar-nav {
  padding: 24px 0;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 24px;
  color: #1d1d1f;
  text-decoration: none;
  font-size: 14px;
  font-weight: 500;
  transition: all 0.2s ease;
  white-space: nowrap;
}

.nav-item:hover {
  background: #f5f5f7;
}

.nav-item.active {
  background: #f5f5f7;
  color: #0071e3;
}

.nav-item.active svg {
  color: #0071e3;
}

.nav-item svg {
  flex-shrink: 0;
  color: #86868b;
}

/* Main Content */
.main-content {
  flex: 1;
  padding: 24px;
  padding-bottom: 0;
  transition: all 0.3s ease;
  display: flex;
  flex-direction: column;
}

.dashboard-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 28px;
  background: #ffffff;
  border-radius: 12px;
  border: 1px solid #e5e5e5;
}

.header-left {
  display: flex;
  align-items: center;
  gap: 12px;
}

.hamburger-button {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  background: #f5f5f7;
  border: 1px solid #e5e5e5;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.hamburger-button:hover {
  background: #e5e5e5;
}

.hamburger-button:active {
  transform: scale(0.96);
}

.hamburger-icon {
  position: relative;
  width: 16px;
  height: 2px;
  background: #1d1d1f;
  border-radius: 1px;
  transition: background 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.hamburger-icon::before,
.hamburger-icon::after {
  content: '';
  position: absolute;
  left: 0;
  width: 16px;
  height: 2px;
  background: #1d1d1f;
  border-radius: 1px;
  transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.hamburger-icon::before {
  top: -5px;
}

.hamburger-icon::after {
  top: 5px;
}

.hamburger-button.is-active .hamburger-icon {
  background: transparent;
}

.hamburger-button.is-active .hamburger-icon::before {
  transform: translateY(5px) rotate(45deg);
}

.hamburger-button.is-active .hamburger-icon::after {
  transform: translateY(-5px) rotate(-45deg);
}

.dashboard-header h1 {
  margin: 0;
  font-size: 18px;
  font-weight: 600;
  color: #1d1d1f;
  letter-spacing: -0.3px;
}

.logout-button {
  background: #1d1d1f;
  color: #ffffff;
  border: none;
  border-radius: 8px;
  padding: 10px 20px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.logout-button:hover {
  background: #333336;
}

.logout-button:active {
  transform: scale(0.98);
}

/* Page transitions */
.page-enter-active,
.page-leave-active {
  transition: opacity 0.25s ease, transform 0.25s ease;
}

.page-enter-from {
  opacity: 0;
  transform: translateY(10px);
}

.page-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>
