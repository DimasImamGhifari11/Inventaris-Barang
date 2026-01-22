<template>
  <div class="page-container">
    <div class="page-header">
      <h1>Tambah Data Aset</h1>
    </div>

    <div class="content-grid">
      <!-- Import Excel Section -->
      <div class="card import-card">
        <h2>Import dari Excel</h2>
        <p class="card-description">Upload file Excel (.xlsx)</p>

        <div
          class="dropzone"
          :class="{ 'dropzone-active': isDragging, 'dropzone-success': uploadSuccess }"
          @dragover.prevent="isDragging = true"
          @dragleave.prevent="isDragging = false"
          @drop.prevent="handleDrop"
          @click="triggerFileInput"
        >
          <input
            type="file"
            ref="fileInput"
            @change="handleFileSelect"
            accept=".xlsx,.xls"
            hidden
          />
          <div class="dropzone-content">
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
              <polyline points="17 8 12 3 7 8"></polyline>
              <line x1="12" y1="3" x2="12" y2="15"></line>
            </svg>
            <p v-if="!selectedFile">Drag & drop file Excel atau <span>browse</span></p>
            <p v-else class="file-name">{{ selectedFile.name }}</p>
            <span class="file-hint">Format: .xlsx, .xls</span>
          </div>
        </div>

        <button
          class="btn btn-primary"
          @click="uploadExcel"
          :disabled="!selectedFile || uploading"
        >
          <span v-if="uploading">Uploading...</span>
          <span v-else>Upload & Import</span>
        </button>
      </div>

      <!-- Manual Input Section -->
      <div class="card form-card">
        <h2>Input Manual</h2>
        <p class="card-description">Tambah data aset satu per satu</p>

        <form @submit.prevent="submitForm" class="asset-form">
          <div class="form-row">
            <div class="form-group">
              <label>Kode Aset</label>
              <input type="text" v-model="form.kode_aset" placeholder="" required />
            </div>
            <div class="form-group">
              <label>Kode Barang</label>
              <input type="text" v-model="form.kode_barang" placeholder="" required />
            </div>
          </div>

          <div class="form-group">
            <label>Nama Aset</label>
            <input type="text" v-model="form.nama_aset" placeholder="" required />
          </div>

          <div class="form-row">
            <div class="form-group">
              <label>Jenis Aset</label>
              <input type="text" v-model="form.jenis_aset" placeholder="" required />
            </div>
            <div class="form-group">
              <label>Jumlah</label>
              <input type="number" v-model="form.jumlah" min="1" placeholder="" required />
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label>Kondisi</label>
              <select v-model="form.kondisi" required>
                <option value="">Pilih Kondisi</option>
                <option value="Baik">Baik</option>
                <option value="Rusak Ringan">Rusak Ringan</option>
                <option value="Rusak Berat">Rusak Berat</option>
              </select>
            </div>
            <div class="form-group">
              <label>Tahun Perolehan</label>
              <input type="number" v-model="form.tahun_perolehan" min="2000" :max="currentYear" placeholder="" required />
            </div>
          </div>

          <div class="form-group">
            <label>Lokasi Penyimpanan</label>
            <input type="text" v-model="form.lokasi_penyimpanan" placeholder="" required />
          </div>

          <div class="form-group">
            <label>Penanggung Jawab</label>
            <input type="text" v-model="form.penanggung_jawab" placeholder="" required />
          </div>

          <button type="submit" class="btn btn-primary" :disabled="submitting">
            <span v-if="submitting">Menyimpan...</span>
            <span v-else>Simpan Data</span>
          </button>
        </form>
      </div>
    </div>

    <!-- Data Table Section -->
    <div class="card table-card">
      <div class="table-header">
        <h2>Data Aset</h2>
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

        <div v-if="!loading && assets.length === 0" class="empty-table">
          <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
            <polyline points="13 2 13 9 20 9"></polyline>
          </svg>
          <p>Belum ada data aset</p>
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
        <svg v-else-if="notification.type === 'warning'" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
          <line x1="12" y1="9" x2="12" y2="13"></line>
          <line x1="12" y1="17" x2="12.01" y2="17"></line>
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
import axios from 'axios'
import * as XLSX from 'xlsx'

const API_URL = 'http://localhost:8000/api'

const currentYear = new Date().getFullYear()
const fileInput = ref(null)
const selectedFile = ref(null)
const isDragging = ref(false)
const uploading = ref(false)
const uploadSuccess = ref(false)
const submitting = ref(false)
const loading = ref(false)
const assets = ref([])
const tableKey = ref(0)
const searchQuery = ref('')
const searchTimeout = ref(null)

const pagination = ref({
  current_page: 1,
  last_page: 1,
  per_page: 10,
  total: 0
})

const form = ref({
  kode_aset: '',
  kode_barang: '',
  nama_aset: '',
  jenis_aset: '',
  jumlah: 1,
  kondisi: '',
  lokasi_penyimpanan: '',
  penanggung_jawab: '',
  tahun_perolehan: currentYear
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

const triggerFileInput = () => {
  fileInput.value.click()
}

const handleFileSelect = (event) => {
  const file = event.target.files[0]
  if (file) {
    selectedFile.value = file
    uploadSuccess.value = false
  }
}

const handleDrop = (event) => {
  isDragging.value = false
  const file = event.dataTransfer.files[0]
  if (file && (file.name.endsWith('.xlsx') || file.name.endsWith('.xls'))) {
    selectedFile.value = file
    uploadSuccess.value = false
  } else {
    showNotification('Format file harus .xlsx atau .xls', 'error')
  }
}

const uploadExcel = async () => {
  if (!selectedFile.value) return

  uploading.value = true

  try {
    const data = await readExcelFile(selectedFile.value)

    if (data.length === 0) {
      showNotification('File Excel kosong atau format tidak valid', 'error')
      uploading.value = false
      return
    }

    const response = await axios.post(`${API_URL}/barang/import`, { data })
    uploadSuccess.value = true

    // Show success with error count if any
    const successCount = response.data.count
    const errorCount = response.data.errors?.length || 0

    if (errorCount > 0) {
      console.log('Import errors:', response.data.errors)
      showNotification(`Berhasil import ${successCount} data, ${errorCount} gagal (cek console)`, 'warning')
    } else {
      showNotification(`Berhasil import ${successCount} data`)
    }

    selectedFile.value = null
    fileInput.value.value = ''
    fetchData()
  } catch (error) {
    showNotification(error.response?.data?.message || 'Gagal import data', 'error')
  } finally {
    uploading.value = false
  }
}

const readExcelFile = (file) => {
  return new Promise((resolve, reject) => {
    const reader = new FileReader()

    reader.onload = (e) => {
      try {
        const workbook = XLSX.read(e.target.result, { type: 'binary' })
        const sheetName = workbook.SheetNames[0]
        const worksheet = workbook.Sheets[sheetName]
        const jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1 })

        // Get header row
        const headerRow = jsonData[0] || []

        // Check if first column is "No" (row number), then skip it
        const firstHeader = String(headerRow[0] || '').toLowerCase().trim()
        const skipFirstCol = (firstHeader === 'no' || firstHeader === 'no.' || firstHeader === 'nomor')
        const offset = skipFirstCol ? 1 : 0

        console.log('Skip first column:', skipFirstCol)

        // Helper to safely get string value - treat "NaN" text as empty
        const safeString = (val) => {
          if (val === null || val === undefined || val === '') {
            return '-'
          }
          const strVal = String(val).trim()
          // Treat "NaN" text as empty
          if (strVal === 'NaN' || strVal === 'nan' || strVal === 'NULL' || strVal === 'null' || strVal === '') {
            return '-'
          }
          return strVal
        }

        // Skip header row and map to objects
        const rows = jsonData.slice(1).filter(row => row.some(cell => {
          if (cell === null || cell === undefined || cell === '') return false
          const str = String(cell).trim()
          return str !== '' && str !== 'NaN' && str !== 'nan'
        }))

        let autoKodeCounter = 1

        const mappedData = rows.map((row, index) => {
          let kodeAset = safeString(row[0 + offset])

          // Auto-generate kode_aset if empty or "-"
          if (kodeAset === '-') {
            kodeAset = `EGOV${String(autoKodeCounter).padStart(2, '0')}`
            autoKodeCounter++
          }

          return {
            kode_aset: kodeAset,
            kode_barang: safeString(row[1 + offset]),
            nama_aset: safeString(row[2 + offset]),
            jenis_aset: safeString(row[3 + offset]),
            jumlah: parseInt(row[4 + offset]) || 1,
            kondisi: safeString(row[5 + offset]) === '-' ? 'Baik' : safeString(row[5 + offset]),
            lokasi_penyimpanan: safeString(row[6 + offset]),
            penanggung_jawab: safeString(row[7 + offset]),
            tahun_perolehan: parseInt(row[8 + offset]) || new Date().getFullYear()
          }
        })

        console.log('Mapped data sample:', mappedData[0])
        console.log('Total rows:', mappedData.length)
        resolve(mappedData)
      } catch (error) {
        reject(error)
      }
    }

    reader.onerror = (error) => reject(error)
    reader.readAsBinaryString(file)
  })
}

const submitForm = async () => {
  submitting.value = true
  try {
    await axios.post(`${API_URL}/barang`, form.value)
    showNotification('Data berhasil disimpan')
    resetForm()
    fetchData()
  } catch (error) {
    showNotification(error.response?.data?.message || 'Gagal menyimpan data', 'error')
  } finally {
    submitting.value = false
  }
}

const resetForm = () => {
  form.value = {
    kode_aset: '',
    kode_barang: '',
    nama_aset: '',
    jenis_aset: '',
    jumlah: 1,
    kondisi: '',
    lokasi_penyimpanan: '',
    penanggung_jawab: '',
    tahun_perolehan: currentYear
  }
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
    fetchData(1)
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

.content-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 24px;
  margin-bottom: 24px;
}

.card {
  background: #ffffff;
  border-radius: 12px;
  border: 1px solid #e5e5e5;
  padding: 24px;
}

.card h2 {
  margin: 0 0 4px 0;
  font-size: 18px;
  font-weight: 600;
  color: #1d1d1f;
}

.card-description {
  margin: 0 0 20px 0;
  font-size: 14px;
  color: #86868b;
}

/* Dropzone */
.dropzone {
  border: 2px dashed #e5e5e5;
  border-radius: 12px;
  padding: 40px 24px;
  text-align: center;
  cursor: pointer;
  transition: all 0.2s ease;
  margin-bottom: 16px;
}

.dropzone:hover {
  border-color: #0071e3;
  background: #f5f5f7;
}

.dropzone-active {
  border-color: #0071e3;
  background: rgba(0, 113, 227, 0.05);
}

.dropzone-success {
  border-color: #34c759;
  background: rgba(52, 199, 89, 0.05);
}

.dropzone-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
  color: #86868b;
}

.dropzone-content svg {
  opacity: 0.5;
}

.dropzone-content p {
  margin: 0;
  font-size: 14px;
}

.dropzone-content p span {
  color: #0071e3;
  text-decoration: underline;
}

.dropzone-content .file-name {
  color: #1d1d1f;
  font-weight: 500;
}

.file-hint {
  font-size: 12px;
  color: #86868b;
}

/* Form */
.asset-form {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.form-group label {
  font-size: 13px;
  font-weight: 500;
  color: #1d1d1f;
}

.form-group input,
.form-group select {
  padding: 10px 14px;
  border: 1px solid #e5e5e5;
  border-radius: 8px;
  font-size: 14px;
  transition: all 0.2s ease;
  background: #ffffff;
}

.form-group input:focus,
.form-group select:focus {
  outline: none;
  border-color: #0071e3;
  box-shadow: 0 0 0 3px rgba(0, 113, 227, 0.1);
}

.form-group input::placeholder {
  color: #86868b;
}

/* Buttons */
.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 12px 24px;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  border: none;
}

.btn-primary {
  background: #0071e3;
  color: #ffffff;
}

.btn-primary:hover:not(:disabled) {
  background: #0077ed;
}

.btn-primary:disabled {
  background: #86868b;
  cursor: not-allowed;
}

.btn-secondary {
  background: #f5f5f7;
  color: #1d1d1f;
  border: 1px solid #e5e5e5;
}

.btn-secondary:hover {
  background: #e5e5e5;
}

/* Table */
.table-card {
  margin-top: 0;
}

.table-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.table-header h2 {
  margin: 0;
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
  font-size: 14px;
}

thead {
  background: #f5f5f7;
}

th {
  padding: 12px 16px;
  text-align: left;
  border-bottom: 1px solid #e5e5e5;
  font-weight: 600;
  color: #1d1d1f;
  white-space: nowrap;
}

td {
  padding: 12px 16px;
  text-align: left;
  border-bottom: 1px solid #f0f0f0;
  color: #1d1d1f;
  word-wrap: break-word;
  word-break: normal;
}

tbody tr:hover {
  background: #f5f5f7;
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

.empty-table {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 48px;
  color: #86868b;
  gap: 12px;
}

.empty-table svg {
  opacity: 0.5;
}

.empty-table p {
  margin: 0;
  font-size: 14px;
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

/* Table Fade Transition */
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
  z-index: 1000;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.notification.success {
  color: #34c759;
}

.notification.warning {
  color: #ff9500;
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

  .content-grid {
    grid-template-columns: 1fr;
    gap: 16px;
  }

  .card {
    padding: 20px;
  }

  .form-row {
    grid-template-columns: 1fr;
  }

  .dropzone {
    padding: 32px 16px;
  }

  .table-header {
    flex-direction: column;
    gap: 12px;
    align-items: stretch;
  }

  .table-header h2 {
    text-align: center;
  }

  .search-container {
    min-width: unset;
    width: 100%;
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

  .notification {
    left: 16px;
    right: 16px;
    transform: none;
  }
}

/* Responsive - Mobile */
@media (max-width: 480px) {
  .page-header {
    margin-bottom: 16px;
  }

  .page-header h1 {
    font-size: 18px;
  }

  .card {
    padding: 16px;
    border-radius: 8px;
  }

  .card h2 {
    font-size: 16px;
  }

  .card-description {
    font-size: 13px;
    margin-bottom: 16px;
  }

  .dropzone {
    padding: 24px 12px;
  }

  .dropzone-content svg {
    width: 36px;
    height: 36px;
  }

  .dropzone-content p {
    font-size: 13px;
  }

  .form-group input,
  .form-group select {
    padding: 12px;
    font-size: 16px; /* Prevent zoom on iOS */
  }

  .search-input {
    font-size: 16px; /* Prevent zoom on iOS */
  }

  .btn {
    padding: 12px 20px;
  }

  th, td {
    padding: 8px 10px;
    font-size: 11px;
  }

  .badge {
    padding: 3px 8px;
    font-size: 10px;
  }

  .empty-table {
    padding: 32px 16px;
  }

  .empty-table svg {
    width: 36px;
    height: 36px;
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

  .notification {
    padding: 12px 16px;
    font-size: 13px;
  }
}
</style>
