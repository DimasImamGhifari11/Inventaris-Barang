<template>
  <div class="page-wrapper">
    <div class="data-section">
      <div class="section-header">
        <h3>Data Inventaris Barang</h3>
        <div class="header-actions">
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
          <button class="btn-download" @click="downloadExcel" :disabled="pagination.total === 0">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
              <polyline points="7 10 12 15 17 10"></polyline>
              <line x1="12" y1="15" x2="12" y2="3"></line>
            </svg>
            Download Excel
          </button>
          <button class="btn-label" @click="$router.push('/dashboard/generate-label')">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="6 9 6 2 18 2 18 9"></polyline>
              <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
              <rect x="6" y="14" width="12" height="8"></rect>
            </svg>
            Generate Label Barang
          </button>
        </div>
      </div>

      <div class="table-container" v-if="barangList.length > 0">
        <Transition name="fade" mode="out-in">
          <table :key="tableKey">
            <thead>
              <tr>
                <th>No</th>
                <th>Kode Aset</th>
                <th>Kode Barang</th>
                <th>Nama Aset</th>
                <th>Jenis Aset</th>
                <th>Jumlah</th>
                <th>Kondisi</th>
                <th>Lokasi</th>
                <th>Penanggung Jawab</th>
                <th>Tahun</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in barangList" :key="item.id">
                <td>{{ getRowNumber(index) }}</td>
                <td>{{ item.kode_aset }}</td>
                <td>{{ item.kode_barang }}</td>
                <td>{{ item.nama_aset }}</td>
                <td>{{ item.jenis_aset }}</td>
                <td>{{ item.jumlah }}</td>
                <td class="kondisi-col">
                  <span :class="['kondisi-badge', kondisiClass(item.kondisi)]">
                    {{ item.kondisi }}
                  </span>
                </td>
                <td>{{ item.lokasi_penyimpanan }}</td>
                <td>{{ item.penanggung_jawab }}</td>
                <td>{{ item.tahun_perolehan }}</td>
              </tr>
            </tbody>
          </table>
        </Transition>
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

      <div class="empty-state" v-if="!loading && barangList.length === 0">
        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
          <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
          <line x1="12" y1="22.08" x2="12" y2="12"></line>
        </svg>
        <p v-if="searchQuery">Tidak ada data yang sesuai dengan pencarian</p>
        <p v-else>Belum ada data barang</p>
      </div>

      <div class="loading-state" v-if="loading">
        <p>Memuat data...</p>
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
import api from '../api'
import * as XLSX from 'xlsx'

const user = ref(null)
const barangList = ref([])
const allBarangList = ref([]) // For Excel download
const searchQuery = ref('')
const loading = ref(true)
const searchTimeout = ref(null)
const tableKey = ref(0)

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

const getRowNumber = (index) => {
  return (pagination.value.current_page - 1) * pagination.value.per_page + index + 1
}

const kondisiClass = (kondisi) => {
  if (kondisi === 'Baik') return 'kondisi-baik'
  if (kondisi === 'Rusak Ringan') return 'kondisi-ringan'
  if (kondisi === 'Rusak Berat') return 'kondisi-berat'
  return ''
}

const fetchBarang = async (page = 1) => {
  loading.value = true
  try {
    let url = `/barang?page=${page}&per_page=10`
    if (searchQuery.value) {
      url += `&search=${encodeURIComponent(searchQuery.value)}`
    }
    const response = await api.get(url)
    barangList.value = response.data.data || []
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

const fetchAllBarang = async () => {
  try {
    const response = await api.get('/barang?per_page=1000')
    allBarangList.value = response.data.data || []
  } catch (error) {
    console.error('Error fetching all data:', error)
    showNotification('Gagal mengambil data untuk export.', 'error')
  }
}

const handleSearch = () => {
  clearTimeout(searchTimeout.value)
  searchTimeout.value = setTimeout(() => {
    tableKey.value++
    // Jika search kosong, tetap di halaman saat ini
    const page = searchQuery.value.trim() === '' ? pagination.value.current_page : 1
    fetchBarang(page)
  }, 300)
}

const goToPage = (page) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    tableKey.value++
    fetchBarang(page)
  }
}

const downloadExcel = async () => {
  // Fetch all data first for complete export
  await fetchAllBarang()

  const data = allBarangList.value.map((item, index) => ({
    'No': index + 1,
    'Kode Aset': item.kode_aset,
    'Kode Barang': item.kode_barang,
    'Nama Aset': item.nama_aset,
    'Jenis Aset': item.jenis_aset,
    'Jumlah': item.jumlah,
    'Kondisi': item.kondisi,
    'Lokasi Penyimpanan': item.lokasi_penyimpanan,
    'Penanggung Jawab': item.penanggung_jawab,
    'Tahun Perolehan': item.tahun_perolehan
  }))

  const worksheet = XLSX.utils.json_to_sheet(data)
  const workbook = XLSX.utils.book_new()
  XLSX.utils.book_append_sheet(workbook, worksheet, 'Data Inventaris')

  const colWidths = [
    { wch: 5 },
    { wch: 15 },
    { wch: 20 },
    { wch: 40 },
    { wch: 15 },
    { wch: 8 },
    { wch: 12 },
    { wch: 25 },
    { wch: 20 },
    { wch: 15 }
  ]
  worksheet['!cols'] = colWidths

  const today = new Date()
  const dateStr = today.toISOString().split('T')[0]
  XLSX.writeFile(workbook, `Inventaris_Barang_${dateStr}.xlsx`)
}

onMounted(() => {
  const userData = sessionStorage.getItem('user')
  if (userData) {
    user.value = JSON.parse(userData)
  }
  fetchBarang()
})
</script>

<style scoped>
.data-section {
  background: var(--bg-card);
  border-radius: 12px;
  border: 1px solid var(--border-color);
  padding: 24px;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  flex-wrap: wrap;
  gap: 16px;
}

.section-header h3 {
  margin: 0;
  font-size: 18px;
  font-weight: 600;
  color: var(--text-primary);
}

.header-actions {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
}

.btn-download {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 16px;
  background: #34c759;
  color: #ffffff;
  border: none;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  white-space: nowrap;
}

.btn-download:hover:not(:disabled) {
  background: #2db84d;
}

.btn-download:active:not(:disabled) {
  transform: scale(0.98);
}

.btn-download:disabled {
  background: var(--text-secondary);
  cursor: not-allowed;
}

.btn-download svg {
  flex-shrink: 0;
}

.btn-label {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 16px;
  background: var(--bg-card);
  color: var(--text-primary);
  border: 1px solid var(--border-color);
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  white-space: nowrap;
}

.btn-label:hover {
  background: var(--bg-primary);
}

.btn-label:active {
  transform: scale(0.98);
}

.btn-label svg {
  flex-shrink: 0;
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
  overflow-y: hidden;
}

/* Table Pagination Transitions */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.25s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
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
  background: var(--bg-hover);
}

.kondisi-badge {
  display: inline-block;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 500;
  white-space: nowrap;
}

.kondisi-col {
  text-align: center;
}

.kondisi-baik {
  background: #d4edda;
  color: #155724;
}

.kondisi-ringan {
  background: #fff3cd;
  color: #856404;
}

.kondisi-berat {
  background: #f8d7da;
  color: #721c24;
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

.notification.success {
  color: #34c759;
}

.notification.error {
  color: #ff3b30;
}

.notification-enter-active {
  animation: slideDown 0.3s ease;
}

.notification-leave-active {
  animation: slideUp 0.3s ease;
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

/* Tablet */
@media (max-width: 768px) {
  .data-section {
    padding: 16px;
  }

  .section-header {
    flex-direction: column;
    align-items: stretch;
  }

  .section-header h3 {
    font-size: 16px;
    text-align: center;
  }

  .header-actions {
    flex-direction: column;
    width: 100%;
  }

  .search-container {
    min-width: unset;
    width: 100%;
  }

  .btn-download,
  .btn-label {
    width: 100%;
    justify-content: center;
  }

  th, td {
    padding: 10px 12px;
    font-size: 12px;
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

  .empty-state {
    padding: 32px 16px;
  }
}

/* Mobile */
@media (max-width: 480px) {
  .data-section {
    padding: 12px;
    border-radius: 8px;
  }

  .section-header h3 {
    font-size: 15px;
  }

  .search-input {
    font-size: 16px;
  }

  th, td {
    padding: 8px 10px;
    font-size: 11px;
  }

  .kondisi-badge {
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
    padding: 24px 12px;
  }

  .empty-state svg {
    width: 36px;
    height: 36px;
  }

  .empty-state p {
    font-size: 13px;
  }
}
</style>
