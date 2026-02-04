<template>
  <transition name="logout-fade">
  <div class="dashboard" :class="{ 'sidebar-open': sidebarOpen }" v-if="!loggingOut">
    <!-- Overlay for mobile -->
    <div class="sidebar-overlay" v-if="sidebarOpen" @click="closeSidebar"></div>

    <!-- Sidebar -->
    <aside class="sidebar">
      <nav class="sidebar-nav">
        <router-link to="/dashboard" class="nav-item" exact-active-class="active" @click="closeSidebar">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
            <polyline points="9 22 9 12 15 12 15 22"></polyline>
          </svg>
          <span>Home</span>
        </router-link>
        <router-link to="/dashboard/tambah-data" class="nav-item" active-class="active" @click="closeSidebar">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="8" x2="12" y2="16"></line>
            <line x1="8" y1="12" x2="16" y2="12"></line>
          </svg>
          <span>Tambah Data Aset</span>
        </router-link>
        <router-link to="/dashboard/update-data" class="nav-item" active-class="active" @click="closeSidebar">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
          </svg>
          <span>Update Data Aset</span>
        </router-link>
        <router-link to="/dashboard/hapus-data" class="nav-item" active-class="active" @click="closeSidebar">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="3 6 5 6 21 6"></polyline>
            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
            <line x1="10" y1="11" x2="10" y2="17"></line>
            <line x1="14" y1="11" x2="14" y2="17"></line>
          </svg>
          <span>Hapus Data Aset</span>
        </router-link>
        <router-link to="/dashboard/riwayat" class="nav-item" active-class="active" @click="closeSidebar">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"></circle>
            <polyline points="12 6 12 12 16 14"></polyline>
          </svg>
          <span>Riwayat</span>
        </router-link>
      </nav>
      <div class="sidebar-footer">
        <div class="dark-mode-toggle" @click="toggleDarkMode">
          <div class="toggle-track" :class="{ active: isDarkMode }">
            <div class="toggle-thumb">
              <svg v-if="!isDarkMode" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="5"></circle>
                <line x1="12" y1="1" x2="12" y2="3"></line>
                <line x1="12" y1="21" x2="12" y2="23"></line>
                <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                <line x1="1" y1="12" x2="3" y2="12"></line>
                <line x1="21" y1="12" x2="23" y2="12"></line>
                <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
              </svg>
              <svg v-else xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
              </svg>
            </div>
          </div>
          <span class="toggle-label">{{ isDarkMode ? 'Dark Mode' : 'Light Mode' }}</span>
        </div>
      </div>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
      <div class="dashboard-header">
        <div class="header-left">
          <button class="hamburger-button" :class="{ 'is-active': sidebarOpen }" @click="toggleSidebar">
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
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

    <!-- Logout Confirmation Modal -->
    <Transition name="modal">
      <div v-if="showLogoutConfirm" class="modal-overlay" @click.self="cancelLogout">
        <div class="modal-content">
          <div class="modal-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
              <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
              <polyline points="16 17 21 12 16 7"></polyline>
              <line x1="21" y1="12" x2="9" y2="12"></line>
            </svg>
          </div>
          <h3>Konfirmasi Logout</h3>
          <p class="modal-message">Apakah Anda yakin ingin keluar?</p>
          <div class="modal-actions">
            <button class="btn btn-secondary" @click="cancelLogout">Batal</button>
            <button class="btn btn-danger" @click="confirmLogout">Ya, Logout</button>
          </div>
        </div>
      </div>
    </Transition>
  </div>
  </transition>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { logout } from '../api'

const router = useRouter()
const sidebarOpen = ref(false)
const loggingOut = ref(false)
const isDarkMode = ref(false)

const toggleDarkMode = () => {
  document.documentElement.classList.add('theme-transition')
  isDarkMode.value = !isDarkMode.value
  document.documentElement.setAttribute('data-theme', isDarkMode.value ? 'dark' : 'light')
  localStorage.setItem('darkMode', isDarkMode.value ? 'true' : 'false')
  setTimeout(() => {
    document.documentElement.classList.remove('theme-transition')
  }, 400)
}

onMounted(() => {
  const saved = localStorage.getItem('darkMode')
  if (saved === 'true') {
    isDarkMode.value = true
    document.documentElement.setAttribute('data-theme', 'dark')
  }
})

const toggleSidebar = () => {
  sidebarOpen.value = !sidebarOpen.value
}

const closeSidebar = () => {
  if (window.innerWidth <= 768) {
    sidebarOpen.value = false
  }
}

const showLogoutConfirm = ref(false)

const handleLogout = () => {
  showLogoutConfirm.value = true
}

const confirmLogout = async () => {
  showLogoutConfirm.value = false
  loggingOut.value = true
  await logout()
  setTimeout(() => {
    router.push('/login')
  }, 400)
}

const cancelLogout = () => {
  showLogoutConfirm.value = false
}
</script>

<style scoped>
.dashboard {
  height: 100vh;
  background: var(--bg-primary);
  display: flex;
  overflow: hidden;
  transition: background 0.3s ease;
}

/* Sidebar */
.sidebar {
  width: 0;
  height: 100vh;
  background: var(--bg-card);
  border-right: 1px solid var(--border-color);
  overflow: hidden;
  transition: width 0.3s ease;
  flex-shrink: 0;
}

.dashboard.sidebar-open .sidebar {
  width: 240px;
  overflow-y: auto;
  overflow-x: hidden;
}

/* Custom Scrollbar */
.sidebar::-webkit-scrollbar,
.main-content::-webkit-scrollbar {
  width: 6px;
}

.sidebar::-webkit-scrollbar-track,
.main-content::-webkit-scrollbar-track {
  background: transparent;
}

.sidebar::-webkit-scrollbar-thumb,
.main-content::-webkit-scrollbar-thumb {
  background: var(--text-secondary);
  border-radius: 3px;
}

.sidebar::-webkit-scrollbar-thumb:hover,
.main-content::-webkit-scrollbar-thumb:hover {
  background: var(--text-primary);
}

.sidebar {
  display: flex;
  flex-direction: column;
}

.sidebar-nav {
  padding: 24px 0;
  display: flex;
  flex-direction: column;
  gap: 4px;
  flex: 1;
}

.sidebar-footer {
  padding: 16px 24px;
  border-top: 1px solid var(--border-color);
}

.dark-mode-toggle {
  display: flex;
  align-items: center;
  gap: 12px;
  cursor: pointer;
  padding: 8px 0;
  white-space: nowrap;
}

.toggle-track {
  width: 44px;
  height: 24px;
  background: var(--border-color);
  border-radius: 12px;
  position: relative;
  transition: background 0.3s ease;
  flex-shrink: 0;
}

.toggle-track.active {
  background: #0071e3;
}

.toggle-thumb {
  position: absolute;
  top: 2px;
  left: 2px;
  width: 20px;
  height: 20px;
  background: var(--bg-card);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: transform 0.3s ease;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
}

.toggle-track.active .toggle-thumb {
  transform: translateX(20px);
}

.toggle-thumb svg {
  color: var(--text-secondary);
}

.toggle-track.active .toggle-thumb svg {
  color: #0071e3;
}

.toggle-label {
  font-size: 13px;
  font-weight: 500;
  color: var(--text-secondary);
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 24px;
  color: var(--text-primary);
  text-decoration: none;
  font-size: 14px;
  font-weight: 500;
  transition: all 0.2s ease;
  white-space: nowrap;
}

.nav-item:hover {
  background: var(--bg-hover);
}

.nav-item.active {
  background: var(--bg-hover);
  color: #0071e3;
}

.nav-item.active svg {
  color: #0071e3;
}

.nav-item svg {
  flex-shrink: 0;
  color: var(--text-secondary);
}

/* Main Content */
.main-content {
  flex: 1;
  padding: 24px;
  transition: all 0.3s ease;
  height: 100vh;
  overflow-y: auto;
  overflow-x: hidden;
}

.dashboard-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 28px;
  background: var(--bg-card);
  border-radius: 12px;
  border: 1px solid var(--border-color);
  margin-bottom: 24px;
  transition: background 0.3s ease, border-color 0.3s ease;
}

.header-left {
  display: flex;
  align-items: center;
  gap: 12px;
}

.hamburger-button {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 5px;
  width: 36px;
  height: 36px;
  background: var(--bg-input);
  border: 1px solid var(--border-color);
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.hamburger-button:hover {
  background: var(--bg-hover);
}

.hamburger-button:active {
  transform: scale(0.96);
}

.hamburger-line {
  display: block;
  width: 16px;
  height: 2px;
  min-height: 2px;
  max-height: 2px;
  background: var(--text-primary);
  border-radius: 1px;
  flex-shrink: 0;
  transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1),
              opacity 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.hamburger-line:nth-child(1) {
  transition-delay: 0.1s;
}

.hamburger-line:nth-child(2) {
  transition-delay: 0s;
}

.hamburger-line:nth-child(3) {
  transition-delay: 0.1s;
}

.hamburger-button.is-active .hamburger-line:nth-child(1) {
  transform: translateY(7px) rotate(45deg);
  transition-delay: 0s;
}

.hamburger-button.is-active .hamburger-line:nth-child(2) {
  opacity: 0;
  transform: scaleX(0);
  transition-delay: 0.1s;
}

.hamburger-button.is-active .hamburger-line:nth-child(3) {
  transform: translateY(-7px) rotate(-45deg);
  transition-delay: 0s;
}

.dashboard-header h1 {
  margin: 0;
  font-size: 18px;
  font-weight: 600;
  color: var(--text-primary);
  letter-spacing: -0.3px;
}

.logout-button {
  background: var(--text-primary);
  color: var(--bg-card);
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

/* Logout transition */
.logout-fade-leave-active {
  transition: opacity 0.4s ease, transform 0.4s ease;
}

.logout-fade-leave-to {
  opacity: 0;
  transform: scale(0.98);
}

/* Sidebar Overlay */
.sidebar-overlay {
  display: none;
}

/* Tablet & Mobile */
@media (max-width: 768px) {
  .sidebar-overlay {
    display: block;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.4);
    z-index: 90;
    animation: fadeIn 0.3s ease;
  }

  @keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
  }

  .sidebar {
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    z-index: 100;
    transform: translateX(-100%);
    transition: transform 0.3s ease;
    width: 260px !important;
    overflow-y: auto;
    overflow-x: hidden;
  }

  .dashboard.sidebar-open .sidebar {
    transform: translateX(0);
    width: 260px !important;
  }

  .main-content {
    padding: 16px;
    width: 100%;
    height: 100vh;
    overflow-y: auto;
  }

  .dashboard-header {
    padding: 16px;
    flex-wrap: wrap;
    gap: 12px;
  }

  .dashboard-header h1 {
    font-size: 14px;
  }

  .logout-button {
    padding: 8px 16px;
    font-size: 13px;
  }
}

/* Mobile small */
@media (max-width: 480px) {
  .main-content {
    padding: 12px;
  }

  .dashboard-header {
    padding: 12px;
  }

  .dashboard-header h1 {
    font-size: 13px;
  }

  .header-left {
    gap: 8px;
  }

  .hamburger-button {
    width: 32px;
    height: 32px;
  }

  .logout-button {
    padding: 8px 12px;
    font-size: 12px;
  }
}

/* Logout Confirmation Modal */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: var(--bg-card);
  border-radius: 16px;
  padding: 32px;
  width: 360px;
  max-width: 90vw;
  text-align: center;
  box-shadow: 0 20px 60px var(--shadow-color);
  border: 1px solid var(--border-color);
}

.modal-icon {
  margin-bottom: 16px;
  color: var(--text-secondary);
}

.modal-content h3 {
  margin: 0 0 8px 0;
  font-size: 18px;
  font-weight: 600;
  color: var(--text-primary);
}

.modal-message {
  margin: 0 0 24px 0;
  font-size: 14px;
  color: var(--text-secondary);
}

.modal-actions {
  display: flex;
  gap: 12px;
  justify-content: center;
}

.btn {
  padding: 10px 24px;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  border: none;
  transition: all 0.2s ease;
}

.btn-secondary {
  background: var(--bg-primary);
  color: var(--text-primary);
  border: 1px solid var(--border-color);
}

.btn-secondary:hover {
  background: var(--border-color);
}

.btn-danger {
  background: #ff3b30;
  color: #ffffff;
}

.btn-danger:hover {
  background: #e0352b;
}

/* Modal transition */
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.25s ease;
}

.modal-enter-active .modal-content,
.modal-leave-active .modal-content {
  transition: transform 0.25s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-from .modal-content {
  transform: scale(0.95);
}

.modal-leave-to .modal-content {
  transform: scale(0.95);
}
</style>
