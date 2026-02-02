<template>
  <div class="page-container">
    <div class="page-header">
      <h1>Update Data Aset</h1>
    </div>

    <div class="card">
      <div class="table-header">
        <h2>Pilih Data untuk Diupdate</h2>
        <div class="search-container">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"></circle>
            <path d="m21 21-4.35-4.35"></path>
          </svg>
          <input
            type="text"
            v-model="searchQuery"
            @input="handleSearch"
            placeholder="Cari kode aset, kode barang, atau nama aset..."
            class="search-input"
          />
        </div>
      </div>

      <div class="table-container">
        <Transition name="fade" mode="out-in">
          <table v-if="assets.length > 0" :key="tableKey">
            <thead>
              <tr>
                <th class="action-col">Aksi</th>
                <th>No</th>
                <th>Kode Aset</th>
                <th>Kode Barang</th>
                <th>Nama Aset</th>
                <th>Jenis</th>
                <th>Jumlah</th>
                <th>Kondisi</th>
                <th>Lokasi</th>
                <th>Penanggung Jawab</th>
                <th>Tahun</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(asset, index) in assets" :key="asset.id">
                <td class="action-col">
                  <button class="btn-edit" @click="goToEdit(asset.id)" title="Edit data">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                      <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>
                  </button>
                </td>
                <td>{{ getRowNumber(index) }}</td>
                <td>{{ asset.kode_aset }}</td>
                <td>{{ asset.kode_barang }}</td>
                <td>{{ asset.nama_aset }}</td>
                <td>{{ asset.jenis_aset }}</td>
                <td>{{ asset.jumlah }}</td>
                <td class="kondisi-col">
                  <span class="badge" :class="getBadgeClass(asset.kondisi)">
                    {{ asset.kondisi }}
                  </span>
                </td>
                <td>{{ asset.lokasi_penyimpanan }}</td>
                <td>{{ asset.penanggung_jawab }}</td>
                <td>{{ asset.tahun_perolehan }}</td>
              </tr>
            </tbody>
          </table>
        </Transition>

        <div v-if="!loading && assets.length === 0" class="empty-state">
          <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
            <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
            <line x1="12" y1="22.08" x2="12" y2="12"></line>
          </svg>
          <p v-if="searchQuery">Tidak ada data yang sesuai dengan pencarian</p>
          <p v-else>Belum ada data barang</p>
        </div>

        <div v-if="loading" class="loading-state">
          <p>Memuat data...</p>
        </div>
      </div>

      <!-- Pagination -->
      <div class="pagination" v-if="pagination.last_page > 1">
        <button
          class="pagination-btn"
          @click="goToPage(pagination.current_page - 1)"
          :disabled="pagination.current_page === 1"
        >
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="15 18 9 12 15 6"></polyline>
          </svg>
          Prev
        </button>
        <span class="pagination-info">
          Page {{ pagination.current_page }} of {{ pagination.last_page }}
        </span>
        <button
          class="pagination-btn"
          @click="goToPage(pagination.current_page + 1)"
          :disabled="pagination.current_page === pagination.last_page"
        >
          Next
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="9 18 15 12 9 6"></polyline>
          </svg>
        </button>
      </div>
    </div>

    <!-- Notification -->
    <Transition name="notification">
      <div v-if="notification.show" class="notification" :class="notification.type">
        <svg v-if="notification.type === 'success'" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
          <polyline points="22 4 12 14.01 9 11.01"></polyline>
        </svg>
        <svg v-else xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="10"></circle>
          <line x1="15" y1="9" x2="9" y2="15"></line>
          <line x1="9" y1="9" x2="15" y2="15"></line>
        </svg>
        <span>{{ notification.message }}</span>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import api from '../api'

const router = useRouter()
const route = useRoute()

const assets = ref([])
const loading = ref(false)
const tableKey = ref(0)
const searchQuery = ref('')
const searchTimeout = ref(null)

const notification = ref({
  show: false,
  message: '',
  type: 'success'
})

const showNotification = (message, type = 'success') => {
  notification.value = { show: true, message, type }
  setTimeout(() => {
    notification.value.show = false
  }, 3000)
}

const pagination = ref({
  current_page: 1,
  last_page: 1,
  per_page: 10,
  total: 0
})

const fetchData = async (page = 1) => {
  loading.value = true
  try {
    let url = `/barang?page=${page}&per_page=10`
    if (searchQuery.value) {
      url += `&search=${encodeURIComponent(searchQuery.value)}`
    }
    const response = await api.get(url)
    assets.value = response.data.data || []
    if (response.data.pagination) {
      pagination.value = response.data.pagination
    }
  } catch (error) {
    console.error('Error fetching data:', error)
    showNotification('Gagal memuat data. Periksa koneksi ke server.', 'error')
  } finally {
    loading.value = false
  }
}

const handleSearch = () => {
  clearTimeout(searchTimeout.value)
  searchTimeout.value = setTimeout(() => {
    tableKey.value++
    // Jika search kosong, tetap di halaman saat ini
    const page = searchQuery.value.trim() === '' ? pagination.value.current_page : 1
    fetchData(page)
  }, 300)
}

const goToPage = (page) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    tableKey.value++
    fetchData(page)
  }
}

const getRowNumber = (index) => {
  return (pagination.value.current_page - 1) * pagination.value.per_page + index + 1
}

const getBadgeClass = (kondisi) => {
  switch (kondisi) {
    case 'Baik': return 'badge-success'
    case 'Rusak Ringan': return 'badge-warning'
    case 'Rusak Berat': return 'badge-danger'
    default: return ''
  }
}

const goToEdit = (id) => {
  router.push(`/dashboard/update-data/${id}?page=${pagination.value.current_page}`)
}

onMounted(() => {
  const page = parseInt(route.query.page) || 1
  fetchData(page)
})
</script>

<style scoped>
.page-container {
  padding: 24px 0;
}

.page-header {
  margin-bottom: 24px;
}

.page-header h1 {
  margin: 0;
  font-size: 24px;
  font-weight: 600;
  color: var(--text-primary);
}

.card {
  background: var(--bg-card);
  border-radius: 12px;
  border: 1px solid var(--border-color);
  padding: 24px;
}

.table-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  flex-wrap: wrap;
  gap: 16px;
}

.table-header h2 {
  margin: 0;
  font-size: 18px;
  font-weight: 600;
  color: var(--text-primary);
}

.search-container {
  display: flex;
  align-items: center;
  gap: 8px;
  background: var(--bg-primary);
  border: 1px solid var(--border-color);
  border-radius: 8px;
  padding: 8px 12px;
  min-width: 300px;
}

.search-container svg {
  color: var(--text-secondary);
  flex-shrink: 0;
}

.search-input {
  border: none;
  background: transparent;
  outline: none;
  font-size: 14px;
  width: 100%;
  color: var(--text-primary);
}

.search-input::placeholder {
  color: var(--text-secondary);
}

.table-container {
  overflow-x: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
  font-size: 13px;
}

thead {
  background: var(--bg-primary);
}

th {
  padding: 12px 16px;
  text-align: left;
  font-weight: 600;
  color: var(--text-primary);
  border-bottom: 1px solid var(--border-color);
  white-space: nowrap;
}

td {
  padding: 12px 16px;
  border-bottom: 1px solid var(--border-light);
  color: var(--text-primary);
  word-wrap: break-word;
  word-break: normal;
}

tbody tr:hover {
  background: var(--bg-primary);
}

.action-col {
  width: 60px;
  text-align: center;
}

.btn-edit {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  background: #0071e3;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-edit svg {
  color: #ffffff;
}

.btn-edit:hover {
  background: #0077ed;
  transform: scale(1.05);
}

.btn-edit:active {
  transform: scale(0.98);
}

.badge {
  display: inline-block;
  padding: 4px 10px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 500;
}

.badge-success {
  background: rgba(52, 199, 89, 0.1);
  color: #34c759;
}

.badge-warning {
  background: rgba(255, 149, 0, 0.1);
  color: #ff9500;
}

.badge-danger {
  background: rgba(255, 59, 48, 0.1);
  color: #ff3b30;
}

.kondisi-col {
  text-align: center;
}

.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 48px;
  gap: 16px;
  color: var(--text-secondary);
}

.empty-state svg {
  opacity: 0.5;
}

.empty-state p {
  font-size: 14px;
  margin: 0;
}

.loading-state {
  display: flex;
  justify-content: center;
  padding: 48px;
  color: var(--text-secondary);
}

.loading-state p {
  font-size: 14px;
  margin: 0;
}

/* Pagination */
.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 16px;
  margin-top: 20px;
  padding-top: 20px;
  border-top: 1px solid var(--border-color);
}

.pagination-btn {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 8px 16px;
  background: var(--bg-primary);
  border: 1px solid var(--border-color);
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  color: var(--text-primary);
  cursor: pointer;
  transition: all 0.2s ease;
}

.pagination-btn:hover:not(:disabled) {
  background: var(--border-color);
}

.pagination-btn:active:not(:disabled) {
  transform: scale(0.98);
}

.pagination-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.pagination-btn svg {
  flex-shrink: 0;
}

.pagination-info {
  font-size: 14px;
  color: var(--text-secondary);
  font-weight: 500;
}

/* Fade Transition */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.25s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Responsive - Tablet */
@media (max-width: 768px) {
  .page-container {
    padding: 16px 0;
  }

  .page-header h1 {
    font-size: 20px;
  }

  .card {
    padding: 16px;
  }

  .table-header {
    flex-direction: column;
    align-items: stretch;
  }

  .table-header h2 {
    text-align: center;
    font-size: 16px;
  }

  .search-container {
    min-width: unset;
    width: 100%;
  }

  th, td {
    padding: 10px 12px;
    font-size: 12px;
  }

  .btn-edit {
    width: 28px;
    height: 28px;
  }

  .btn-edit svg {
    width: 14px;
    height: 14px;
  }

  .pagination {
    gap: 12px;
  }

  .pagination-btn {
    padding: 8px 12px;
    font-size: 13px;
  }

  .pagination-info {
    font-size: 13px;
  }
}

/* Responsive - Mobile */
@media (max-width: 480px) {
  .page-header h1 {
    font-size: 18px;
  }

  .card {
    padding: 12px;
    border-radius: 8px;
  }

  .table-header h2 {
    font-size: 15px;
  }

  .search-input {
    font-size: 16px;
  }

  th, td {
    padding: 8px 10px;
    font-size: 11px;
  }

  .action-col {
    width: 45px;
  }

  .btn-edit {
    width: 26px;
    height: 26px;
  }

  .btn-edit svg {
    width: 12px;
    height: 12px;
  }

  .badge {
    padding: 3px 6px;
    font-size: 10px;
  }

  .pagination {
    flex-wrap: wrap;
    gap: 8px;
  }

  .pagination-btn {
    padding: 6px 10px;
    font-size: 12px;
  }

  .pagination-info {
    font-size: 12px;
    width: 100%;
    text-align: center;
    order: -1;
  }

  .empty-state {
    padding: 32px 16px;
  }

  .empty-state svg {
    width: 36px;
    height: 36px;
  }
}

/* Notification */
.notification {
  position: fixed;
  top: 32px;
  left: 50%;
  transform: translateX(-50%);
  background: var(--bg-card);
  border: 1px solid var(--border-color);
  border-radius: 10px;
  padding: 14px 24px;
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 14px;
  font-weight: 500;
  z-index: 1001;
  box-shadow: 0 4px 12px var(--shadow-color);
}

.notification.success { color: #34c759; }
.notification.error { color: #ff3b30; }

.notification-enter-active { animation: slideDown 0.3s ease; }
.notification-leave-active { animation: slideUp 0.3s ease; }

@keyframes slideDown {
  from { opacity: 0; transform: translateX(-50%) translateY(-20px); }
  to { opacity: 1; transform: translateX(-50%) translateY(0); }
}

@keyframes slideUp {
  from { opacity: 1; transform: translateX(-50%) translateY(0); }
  to { opacity: 0; transform: translateX(-50%) translateY(-20px); }
}
</style>
