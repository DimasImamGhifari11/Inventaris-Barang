<template>
  <div class="page-container">
    <div class="page-header">
      <h1>Hapus Data Aset</h1>
    </div>

    <div class="card">
      <div class="table-header">
        <div class="header-left">
          <h2>Pilih Data untuk Dihapus</h2>
          <Transition name="fade">
            <button
              v-if="selectedIds.length > 1"
              class="btn-bulk-delete"
              @click="confirmBulkDelete"
            >
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="3 6 5 6 21 6"></polyline>
                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
              </svg>
              Hapus Terpilih ({{ selectedIds.length }})
            </button>
          </Transition>
        </div>
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
                <th class="checkbox-col">
                  <input
                    type="checkbox"
                    :checked="isAllSelected"
                    @change="toggleSelectAll"
                    class="checkbox"
                  />
                </th>
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
              <tr v-for="(asset, index) in assets" :key="asset.id" :class="{ 'row-selected': selectedIds.includes(asset.id) }">
                <td class="checkbox-col">
                  <input
                    type="checkbox"
                    :checked="selectedIds.includes(asset.id)"
                    @change="toggleSelect(asset.id)"
                    class="checkbox"
                  />
                </td>
                <td class="action-col">
                  <button class="btn-delete" @click="confirmDelete(asset)" title="Hapus data">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <polyline points="3 6 5 6 21 6"></polyline>
                      <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                      <line x1="10" y1="11" x2="10" y2="17"></line>
                      <line x1="14" y1="11" x2="14" y2="17"></line>
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

    <!-- Delete Confirmation Modal -->
    <Transition name="modal">
      <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
        <div class="modal-content">
          <div class="modal-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="12" y1="8" x2="12" y2="12"></line>
              <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
          </div>
          <h3>Konfirmasi Hapus</h3>

          <!-- Single Delete -->
          <template v-if="!isBulkDelete && selectedAsset">
            <p class="modal-message">Apakah anda yakin akan menghapus data ini?</p>
            <div class="modal-detail">
              <div class="detail-row">
                <span class="detail-label">Nama Aset:</span>
                <span class="detail-value">{{ selectedAsset.nama_aset }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Kode Barang:</span>
                <span class="detail-value">{{ selectedAsset.kode_barang }}</span>
              </div>
            </div>
          </template>

          <!-- Bulk Delete -->
          <template v-if="isBulkDelete">
            <p class="modal-message">Apakah anda yakin akan menghapus <strong>{{ selectedIds.length }}</strong> data yang dipilih?</p>
            <div class="modal-detail bulk-detail">
              <p class="bulk-warning">Data yang dihapus tidak dapat dikembalikan.</p>
            </div>
          </template>

          <div class="modal-actions">
            <button class="btn btn-secondary" @click="closeModal" :disabled="deleting">Batal</button>
            <button class="btn btn-danger" @click="deleteAsset" :disabled="deleting">
              <span v-if="deleting">Menghapus...</span>
              <span v-else>Ya, Hapus{{ isBulkDelete ? ` (${selectedIds.length})` : '' }}</span>
            </button>
          </div>
        </div>
      </div>
    </Transition>

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
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const API_URL = 'http://localhost:8000/api'

const assets = ref([])
const loading = ref(false)
const tableKey = ref(0)
const searchQuery = ref('')
const searchTimeout = ref(null)
const showModal = ref(false)
const selectedAsset = ref(null)
const deleting = ref(false)
const selectedIds = ref([])
const isBulkDelete = ref(false)

const isAllSelected = computed(() => {
  return assets.value.length > 0 && selectedIds.value.length === assets.value.length
})

const pagination = ref({
  current_page: 1,
  last_page: 1,
  per_page: 10,
  total: 0
})

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

const fetchData = async (page = 1) => {
  loading.value = true
  try {
    let url = `${API_URL}/barang?page=${page}&per_page=10`
    if (searchQuery.value) {
      url += `&search=${encodeURIComponent(searchQuery.value)}`
    }
    const response = await axios.get(url)
    assets.value = response.data.data || []
    if (response.data.pagination) {
      pagination.value = response.data.pagination
    }
  } catch (error) {
    console.error('Error fetching data:', error)
  } finally {
    loading.value = false
  }
}

const handleSearch = () => {
  clearTimeout(searchTimeout.value)
  searchTimeout.value = setTimeout(() => {
    tableKey.value++
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

const toggleSelect = (id) => {
  const index = selectedIds.value.indexOf(id)
  if (index === -1) {
    selectedIds.value.push(id)
  } else {
    selectedIds.value.splice(index, 1)
  }
}

const toggleSelectAll = () => {
  if (isAllSelected.value) {
    selectedIds.value = []
  } else {
    selectedIds.value = assets.value.map(asset => asset.id)
  }
}

const confirmDelete = (asset) => {
  isBulkDelete.value = false
  selectedAsset.value = asset
  showModal.value = true
}

const confirmBulkDelete = () => {
  if (selectedIds.value.length === 0) return
  isBulkDelete.value = true
  selectedAsset.value = null
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  selectedAsset.value = null
  isBulkDelete.value = false
}

const deleteAsset = async () => {
  if (isBulkDelete.value) {
    await bulkDeleteAssets()
    return
  }

  if (!selectedAsset.value) return

  deleting.value = true
  try {
    await axios.delete(`${API_URL}/barang/${selectedAsset.value.id}`)
    showNotification('Data berhasil dihapus')
    closeModal()
    fetchData(pagination.value.current_page)
  } catch (error) {
    showNotification(error.response?.data?.message || 'Gagal menghapus data', 'error')
  } finally {
    deleting.value = false
  }
}

const bulkDeleteAssets = async () => {
  if (selectedIds.value.length === 0) return

  deleting.value = true
  try {
    await Promise.all(
      selectedIds.value.map(id => axios.delete(`${API_URL}/barang/${id}`))
    )
    showNotification(`${selectedIds.value.length} data berhasil dihapus`)
    selectedIds.value = []
    closeModal()
    fetchData(pagination.value.current_page)
  } catch (error) {
    showNotification(error.response?.data?.message || 'Gagal menghapus data', 'error')
  } finally {
    deleting.value = false
  }
}

onMounted(() => {
  fetchData()
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
  color: #1d1d1f;
}

.card {
  background: #ffffff;
  border-radius: 12px;
  border: 1px solid #e5e5e5;
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

.header-left {
  display: flex;
  align-items: center;
  gap: 16px;
  flex-wrap: wrap;
}

.table-header h2 {
  margin: 0;
  font-size: 18px;
  font-weight: 600;
  color: #1d1d1f;
}

.btn-bulk-delete {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 8px 16px;
  background: #ff3b30;
  border: none;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  color: #ffffff;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-bulk-delete:hover {
  background: #e6352b;
}

.btn-bulk-delete:active {
  transform: scale(0.98);
}

.btn-bulk-delete svg {
  flex-shrink: 0;
}

.search-container {
  display: flex;
  align-items: center;
  gap: 8px;
  background: #f5f5f7;
  border: 1px solid #e5e5e5;
  border-radius: 8px;
  padding: 8px 12px;
  min-width: 300px;
}

.search-container svg {
  color: #86868b;
  flex-shrink: 0;
}

.search-input {
  border: none;
  background: transparent;
  outline: none;
  font-size: 14px;
  width: 100%;
  color: #1d1d1f;
}

.search-input::placeholder {
  color: #86868b;
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
  background: #f5f5f7;
}

th {
  padding: 12px 16px;
  text-align: left;
  font-weight: 600;
  color: #1d1d1f;
  border-bottom: 1px solid #e5e5e5;
  white-space: nowrap;
}

td {
  padding: 12px 16px;
  border-bottom: 1px solid #f0f0f0;
  color: #1d1d1f;
  word-wrap: break-word;
  word-break: normal;
}

tbody tr:hover {
  background: #f5f5f7;
}

tbody tr.row-selected {
  background: rgba(255, 59, 48, 0.05);
}

tbody tr.row-selected:hover {
  background: rgba(255, 59, 48, 0.1);
}

.checkbox-col {
  width: 40px;
  text-align: center;
}

.checkbox {
  width: 18px;
  height: 18px;
  cursor: pointer;
  accent-color: #ff3b30;
}

.action-col {
  width: 60px;
  text-align: center;
}

.btn-delete {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  background: #ff3b30;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-delete svg {
  color: #ffffff;
}

.btn-delete:hover {
  background: #e6352b;
  transform: scale(1.05);
}

.btn-delete:active {
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
  color: #86868b;
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
  color: #86868b;
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
  border-top: 1px solid #e5e5e5;
}

.pagination-btn {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 8px 16px;
  background: #f5f5f7;
  border: 1px solid #e5e5e5;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  color: #1d1d1f;
  cursor: pointer;
  transition: all 0.2s ease;
}

.pagination-btn:hover:not(:disabled) {
  background: #e5e5e5;
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
  color: #86868b;
  font-weight: 500;
}

/* Modal */
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
  padding: 20px;
}

.modal-content {
  background: #ffffff;
  border-radius: 16px;
  padding: 32px;
  max-width: 400px;
  width: 100%;
  text-align: center;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
}

.modal-icon {
  margin-bottom: 16px;
  color: #ff3b30;
}

.modal-content h3 {
  margin: 0 0 8px 0;
  font-size: 20px;
  font-weight: 600;
  color: #1d1d1f;
}

.modal-message {
  margin: 0 0 20px 0;
  font-size: 14px;
  color: #86868b;
}

.modal-detail {
  background: #f5f5f7;
  border-radius: 8px;
  padding: 16px;
  margin-bottom: 24px;
  text-align: left;
}

.detail-row {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 12px;
}

.detail-row + .detail-row {
  margin-top: 8px;
}

.detail-label {
  font-size: 13px;
  color: #86868b;
  flex-shrink: 0;
}

.detail-value {
  font-size: 13px;
  font-weight: 500;
  color: #1d1d1f;
  text-align: right;
  word-break: break-word;
}

.bulk-detail {
  text-align: center;
}

.bulk-warning {
  margin: 0;
  font-size: 13px;
  color: #ff3b30;
}

.modal-actions {
  display: flex;
  gap: 12px;
}

.btn {
  flex: 1;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 12px 24px;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  border: none;
}

.btn-secondary {
  background: #f5f5f7;
  color: #1d1d1f;
  border: 1px solid #e5e5e5;
}

.btn-secondary:hover:not(:disabled) {
  background: #e5e5e5;
}

.btn-danger {
  background: #ff3b30;
  color: #ffffff;
}

.btn-danger:hover:not(:disabled) {
  background: #e6352b;
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Modal Transition */
.modal-enter-active {
  animation: modalIn 0.3s ease;
}

.modal-leave-active {
  animation: modalOut 0.2s ease;
}

@keyframes modalIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

@keyframes modalOut {
  from {
    opacity: 1;
  }
  to {
    opacity: 0;
  }
}

.modal-enter-active .modal-content {
  animation: modalContentIn 0.3s ease;
}

.modal-leave-active .modal-content {
  animation: modalContentOut 0.2s ease;
}

@keyframes modalContentIn {
  from {
    opacity: 0;
    transform: scale(0.95) translateY(-20px);
  }
  to {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}

@keyframes modalContentOut {
  from {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
  to {
    opacity: 0;
    transform: scale(0.95) translateY(-20px);
  }
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

/* Notification */
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
  font-size: 14px;
  font-weight: 500;
  z-index: 1001;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
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

  .header-left {
    flex-direction: column;
    align-items: stretch;
    gap: 12px;
  }

  .table-header h2 {
    text-align: center;
    font-size: 16px;
  }

  .btn-bulk-delete {
    justify-content: center;
  }

  .search-container {
    min-width: unset;
    width: 100%;
  }

  th, td {
    padding: 10px 12px;
    font-size: 12px;
  }

  .checkbox-col {
    width: 35px;
  }

  .checkbox {
    width: 16px;
    height: 16px;
  }

  .btn-delete {
    width: 28px;
    height: 28px;
  }

  .btn-delete svg {
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

  .modal-content {
    padding: 24px;
  }

  .notification {
    left: 16px;
    right: 16px;
    transform: none;
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

  .btn-bulk-delete {
    padding: 8px 12px;
    font-size: 13px;
  }

  .search-input {
    font-size: 16px;
  }

  th, td {
    padding: 8px 10px;
    font-size: 11px;
  }

  .checkbox-col {
    width: 30px;
  }

  .checkbox {
    width: 14px;
    height: 14px;
  }

  .action-col {
    width: 45px;
  }

  .btn-delete {
    width: 26px;
    height: 26px;
  }

  .btn-delete svg {
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

  .modal-content {
    padding: 20px;
    margin: 16px;
  }

  .modal-content h3 {
    font-size: 18px;
  }

  .modal-icon svg {
    width: 40px;
    height: 40px;
  }

  .modal-actions {
    flex-direction: column;
  }

  .notification {
    padding: 12px 16px;
    font-size: 13px;
  }
}
</style>
