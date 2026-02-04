<template>
  <div class="page-container">
    <div class="page-header">
      <button class="btn-back" @click="goBack">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
        Kembali
      </button>
      <h1>Generate Label Barang</h1>
    </div>

    <div class="card">
      <div class="table-header">
        <div class="header-left">
          <h2>Pilih Barang untuk Generate Label</h2>
          <Transition name="fade">
            <button
              v-if="selectedIds.length > 1"
              class="btn-bulk-generate"
              @click="confirmBulkGenerate"
            >
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="6 9 6 2 18 2 18 9"></polyline>
                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                <rect x="6" y="14" width="12" height="8"></rect>
              </svg>
              Generate Label ({{ selectedIds.length }})
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
                <th>Tahun Pengadaan</th>
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
                  <button class="btn-download" @click="confirmSingleGenerate(asset)" title="Download Label">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                      <polyline points="7 10 12 15 17 10"></polyline>
                      <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                  </button>
                </td>
                <td>{{ getRowNumber(index) }}</td>
                <td>{{ asset.kode_aset }}</td>
                <td>{{ asset.kode_barang }}</td>
                <td>{{ asset.nama_aset }}</td>
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

    <!-- Generate Confirmation Modal -->
    <Transition name="modal">
      <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
        <div class="modal-content">
          <div class="modal-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="6 9 6 2 18 2 18 9"></polyline>
              <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
              <rect x="6" y="14" width="12" height="8"></rect>
            </svg>
          </div>
          <h3>Konfirmasi Generate Label</h3>

          <!-- Single Generate -->
          <template v-if="!isBulkGenerate && selectedAsset">
            <p class="modal-message">Apakah Anda yakin mencetak label berikut?</p>
            <div class="modal-detail">
              <div class="detail-row">
                <span class="detail-label">Kode Barang:</span>
                <span class="detail-value">{{ selectedAsset.kode_barang }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Tahun Pengadaan:</span>
                <span class="detail-value">{{ selectedAsset.tahun_perolehan }}</span>
              </div>
            </div>
          </template>

          <!-- Bulk Generate -->
          <template v-if="isBulkGenerate">
            <p class="modal-message">Apakah Anda yakin mencetak <strong>{{ selectedIds.length }}</strong> label yang dipilih?</p>
            <div class="modal-detail bulk-detail">
              <p class="bulk-info">Semua label akan didownload satu per satu.</p>
            </div>
          </template>

          <div class="modal-actions">
            <button class="btn btn-secondary" @click="closeModal" :disabled="generating">Batal</button>
            <button class="btn btn-primary" @click="executeGenerate" :disabled="generating">
              <span v-if="generating">Generating...</span>
              <span v-else>Ya, Generate{{ isBulkGenerate ? ` (${selectedIds.length})` : '' }}</span>
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
import { useRouter } from 'vue-router'
import api from '../api'

const router = useRouter()

const assets = ref([])
const loading = ref(false)
const tableKey = ref(0)
const searchQuery = ref('')
const searchTimeout = ref(null)
const showModal = ref(false)
const selectedAsset = ref(null)
const generating = ref(false)
const selectedIds = ref([])
const isBulkGenerate = ref(false)

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

const confirmSingleGenerate = (asset) => {
  isBulkGenerate.value = false
  selectedAsset.value = asset
  showModal.value = true
}

const confirmBulkGenerate = () => {
  if (selectedIds.value.length === 0) return
  isBulkGenerate.value = true
  selectedAsset.value = null
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  selectedAsset.value = null
  isBulkGenerate.value = false
}

const generateLabelPNG = (kodeBarang, tahunPengadaan) => {
  const canvas = document.createElement('canvas')
  const ctx = canvas.getContext('2d')

  canvas.width = 400
  canvas.height = 110

  // Background
  ctx.fillStyle = '#ffffff'
  ctx.fillRect(0, 0, canvas.width, canvas.height)

  // Border
  ctx.strokeStyle = '#000000'
  ctx.lineWidth = 3
  ctx.strokeRect(2, 2, canvas.width - 4, canvas.height - 4)

  // Kode Barang (top, bold, large)
  ctx.fillStyle = '#000000'
  ctx.font = 'bold 26px Arial, sans-serif'
  ctx.textAlign = 'center'
  ctx.textBaseline = 'middle'
  ctx.fillText(kodeBarang, canvas.width / 2, 32)

  // Divider line
  ctx.strokeStyle = '#cccccc'
  ctx.lineWidth = 1
  ctx.beginPath()
  ctx.moveTo(40, 54)
  ctx.lineTo(canvas.width - 40, 54)
  ctx.stroke()

  // Tahun Pengadaan (bottom, medium)
  ctx.fillStyle = '#333333'
  ctx.font = 'bold 20px Arial, sans-serif'
  ctx.fillText('Tahun: ' + tahunPengadaan, canvas.width / 2, 78)

  return canvas.toDataURL('image/png')
}

const downloadPNG = (dataUrl, filename) => {
  const link = document.createElement('a')
  link.download = filename
  link.href = dataUrl
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
}

const executeGenerate = async () => {
  generating.value = true

  try {
    if (isBulkGenerate.value) {
      const selectedAssets = assets.value.filter(a => selectedIds.value.includes(a.id))
      for (let i = 0; i < selectedAssets.length; i++) {
        const asset = selectedAssets[i]
        const dataUrl = generateLabelPNG(asset.kode_barang, asset.tahun_perolehan)
        const filename = `Label_${asset.kode_barang}_${asset.tahun_perolehan}.png`
        downloadPNG(dataUrl, filename)
        if (i < selectedAssets.length - 1) {
          await new Promise(resolve => setTimeout(resolve, 1000))
        }
      }
      showNotification(`${selectedAssets.length} label berhasil di-generate`)
      selectedIds.value = []
    } else if (selectedAsset.value) {
      const dataUrl = generateLabelPNG(selectedAsset.value.kode_barang, selectedAsset.value.tahun_perolehan)
      const filename = `Label_${selectedAsset.value.kode_barang}_${selectedAsset.value.tahun_perolehan}.png`
      downloadPNG(dataUrl, filename)
      showNotification('Label berhasil di-generate')
    }

    closeModal()
  } catch (error) {
    console.error('Error generating label:', error)
    showNotification('Gagal generate label', 'error')
  } finally {
    generating.value = false
  }
}

const goBack = () => {
  router.go(-1) // Go back to previous page
  // If router.go(-1) doesn't work as expected, we can navigate directly to home
  // router.push('/dashboard')
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
  display: flex;
  align-items: center;
  gap: 16px;
  margin-bottom: 24px;
}

.page-header h1 {
  margin: 0;
  font-size: 24px;
  font-weight: 600;
  color: var(--text-primary);
}

.btn-back {
  display: flex;
  align-items: center;
  gap: 4px;
  padding: 8px 12px;
  background: var(--bg-primary);
  border: 1px solid var(--border-color);
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  color: var(--text-primary);
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-back:hover {
  background: var(--border-color);
}

.btn-back:active {
  transform: scale(0.98);
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
  color: var(--text-primary);
}

.btn-bulk-generate {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 8px 16px;
  background: #0071e3;
  border: none;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  color: #ffffff;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-bulk-generate:hover {
  background: #0064cc;
}

.btn-bulk-generate:active {
  transform: scale(0.98);
}

.btn-bulk-generate svg {
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

tbody tr.row-selected {
  background: rgba(0, 113, 227, 0.05);
}

tbody tr.row-selected:hover {
  background: rgba(0, 113, 227, 0.1);
}

.checkbox-col {
  width: 40px;
  text-align: center;
}

.checkbox {
  width: 18px;
  height: 18px;
  cursor: pointer;
  accent-color: #0071e3;
}

.action-col {
  width: 60px;
  text-align: center;
}

.btn-download {
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

.btn-download svg {
  color: #ffffff;
}

.btn-download:hover {
  background: #0064cc;
  transform: scale(1.05);
}

.btn-download:active {
  transform: scale(0.98);
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
  background: var(--bg-card);
  border-radius: 16px;
  padding: 32px;
  max-width: 400px;
  width: 100%;
  text-align: center;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
}

.modal-icon {
  margin-bottom: 16px;
  color: #0071e3;
}

.modal-content h3 {
  margin: 0 0 8px 0;
  font-size: 20px;
  font-weight: 600;
  color: var(--text-primary);
}

.modal-message {
  margin: 0 0 20px 0;
  font-size: 14px;
  color: var(--text-secondary);
}

.modal-detail {
  background: var(--bg-primary);
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
  color: var(--text-secondary);
  flex-shrink: 0;
}

.detail-value {
  font-size: 13px;
  font-weight: 500;
  color: var(--text-primary);
  text-align: right;
  word-break: break-word;
}

.bulk-detail {
  text-align: center;
}

.bulk-info {
  margin: 0;
  font-size: 13px;
  color: var(--text-secondary);
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
  background: var(--bg-primary);
  color: var(--text-primary);
  border: 1px solid var(--border-color);
}

.btn-secondary:hover:not(:disabled) {
  background: var(--border-color);
}

.btn-primary {
  background: #0071e3;
  color: #ffffff;
}

.btn-primary:hover:not(:disabled) {
  background: #0064cc;
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
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes modalOut {
  from { opacity: 1; }
  to { opacity: 0; }
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

  .btn-bulk-generate {
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

  .btn-download {
    width: 28px;
    height: 28px;
  }

  .btn-download svg {
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

  .btn-bulk-generate {
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

  .btn-download {
    width: 26px;
    height: 26px;
  }

  .btn-download svg {
    width: 12px;
    height: 12px;
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
