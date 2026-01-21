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

        <form @submit.prevent="submitForm" class="asset-form" novalidate>
          <div class="form-row">
            <div class="form-group" :class="{ 'has-error': errors.kode_aset }">
              <label>Kode Aset</label>
              <input type="text" v-model="form.kode_aset" @input="clearError('kode_aset')" />
              <span class="error-message" v-if="errors.kode_aset">{{ errors.kode_aset }}</span>
            </div>
            <div class="form-group" :class="{ 'has-error': errors.kode_barang }">
              <label>Kode Barang</label>
              <input type="text" v-model="form.kode_barang" @input="clearError('kode_barang')" />
              <span class="error-message" v-if="errors.kode_barang">{{ errors.kode_barang }}</span>
            </div>
          </div>

          <div class="form-group" :class="{ 'has-error': errors.nama_aset }">
            <label>Nama Aset</label>
            <input type="text" v-model="form.nama_aset" @input="clearError('nama_aset')" />
            <span class="error-message" v-if="errors.nama_aset">{{ errors.nama_aset }}</span>
          </div>

          <div class="form-row">
            <div class="form-group" :class="{ 'has-error': errors.jenis_aset }">
              <label>Jenis Aset</label>
              <input type="text" v-model="form.jenis_aset" @input="clearError('jenis_aset')" />
              <span class="error-message" v-if="errors.jenis_aset">{{ errors.jenis_aset }}</span>
            </div>
            <div class="form-group" :class="{ 'has-error': errors.jumlah }">
              <label>Jumlah</label>
              <input type="number" v-model="form.jumlah" min="1" @input="clearError('jumlah')" />
              <span class="error-message" v-if="errors.jumlah">{{ errors.jumlah }}</span>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group" :class="{ 'has-error': errors.kondisi }">
              <label>Kondisi</label>
              <select v-model="form.kondisi" @change="clearError('kondisi')">
                <option value="">Pilih Kondisi</option>
                <option value="Baik">Baik</option>
                <option value="Rusak Ringan">Rusak Ringan</option>
                <option value="Rusak Berat">Rusak Berat</option>
              </select>
              <span class="error-message" v-if="errors.kondisi">{{ errors.kondisi }}</span>
            </div>
            <div class="form-group" :class="{ 'has-error': errors.tahun_perolehan }">
              <label>Tahun Perolehan</label>
              <input type="number" v-model="form.tahun_perolehan" min="2000" :max="currentYear" @input="clearError('tahun_perolehan')" />
              <span class="error-message" v-if="errors.tahun_perolehan">{{ errors.tahun_perolehan }}</span>
            </div>
          </div>

          <div class="form-group" :class="{ 'has-error': errors.lokasi_penyimpanan }">
            <label>Lokasi Penyimpanan</label>
            <input type="text" v-model="form.lokasi_penyimpanan" @input="clearError('lokasi_penyimpanan')" />
            <span class="error-message" v-if="errors.lokasi_penyimpanan">{{ errors.lokasi_penyimpanan }}</span>
          </div>

          <div class="form-group" :class="{ 'has-error': errors.penanggung_jawab }">
            <label>Penanggung Jawab</label>
            <input type="text" v-model="form.penanggung_jawab" @input="clearError('penanggung_jawab')" />
            <span class="error-message" v-if="errors.penanggung_jawab">{{ errors.penanggung_jawab }}</span>
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
        <button class="btn btn-secondary" @click="fetchData">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="23 4 23 10 17 10"></polyline>
            <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path>
          </svg>
          Refresh
        </button>
      </div>

      <div class="table-container">
        <table v-if="assets.length > 0">
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
              <td>{{ index + 1 }}</td>
              <td :title="asset.kode_aset">{{ asset.kode_aset }}</td>
              <td :title="asset.kode_barang">{{ asset.kode_barang }}</td>
              <td :title="asset.nama_aset">{{ asset.nama_aset }}</td>
              <td :title="asset.jenis_aset">{{ asset.jenis_aset }}</td>
              <td>{{ asset.jumlah }}</td>
              <td>
                <span class="badge" :class="getBadgeClass(asset.kondisi)">
                  {{ asset.kondisi }}
                </span>
              </td>
              <td :title="asset.lokasi_penyimpanan">{{ asset.lokasi_penyimpanan }}</td>
              <td :title="asset.penanggung_jawab">{{ asset.penanggung_jawab }}</td>
              <td>{{ asset.tahun_perolehan }}</td>
            </tr>
          </tbody>
        </table>

        <div v-else class="empty-table">
          <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
            <polyline points="13 2 13 9 20 9"></polyline>
          </svg>
          <p>Belum ada data aset</p>
        </div>
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
const assets = ref([])

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

const errors = ref({})

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
    showNotification(`Berhasil import ${response.data.count} data`)
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
        const jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1, defval: '' })

        console.log('Total rows in Excel:', jsonData.length)
        console.log('Header:', jsonData[0])
        console.log('All data:', jsonData)

        // Skip header row and filter non-empty rows
        const rows = jsonData.slice(1).filter(row => {
          if (!Array.isArray(row)) return false
          // Check if at least the first column (kode_aset) has value
          return row[0] !== undefined && row[0] !== null && row[0] !== ''
        })

        console.log('Filtered rows:', rows.length)

        const cleanValue = (val) => {
          if (val === undefined || val === null) return ''
          const str = String(val).trim()
          if (str.toLowerCase() === 'nan' || str === '#N/A' || str === '#REF!') return ''
          return str
        }

        const mappedData = rows.map((row, index) => {
          const kodeAset = cleanValue(row[0])
          const kodeBarang = cleanValue(row[1])

          return {
            // Auto-generate kode_aset: EGOV01, EGOV02, dst
            kode_aset: kodeAset || `EGOV${String(index + 1).padStart(2, '0')}`,
            kode_barang: kodeBarang,
            nama_aset: cleanValue(row[2]),
            jenis_aset: cleanValue(row[3]),
            jumlah: parseInt(row[4]) || 1,
            kondisi: cleanValue(row[5]) || 'Baik',
            lokasi_penyimpanan: cleanValue(row[6]) || '-',
            penanggung_jawab: cleanValue(row[7]) || '-',
            tahun_perolehan: parseInt(row[8]) || new Date().getFullYear()
          }
        })

        console.log('Mapped data:', mappedData)

        resolve(mappedData)
      } catch (error) {
        console.error('Excel parse error:', error)
        reject(error)
      }
    }

    reader.onerror = (error) => reject(error)
    reader.readAsBinaryString(file)
  })
}

const validateForm = () => {
  errors.value = {}

  if (!form.value.kode_aset.trim()) {
    errors.value.kode_aset = 'Kode Aset harus diisi'
  }
  if (!form.value.kode_barang.trim()) {
    errors.value.kode_barang = 'Kode Barang harus diisi'
  }
  if (!form.value.nama_aset.trim()) {
    errors.value.nama_aset = 'Nama Aset harus diisi'
  }
  if (!form.value.jenis_aset.trim()) {
    errors.value.jenis_aset = 'Jenis Aset harus diisi'
  }
  if (!form.value.jumlah || form.value.jumlah < 1) {
    errors.value.jumlah = 'Jumlah minimal 1'
  }
  if (!form.value.kondisi) {
    errors.value.kondisi = 'Kondisi harus dipilih'
  }
  if (!form.value.tahun_perolehan) {
    errors.value.tahun_perolehan = 'Tahun harus diisi'
  }
  if (!form.value.lokasi_penyimpanan.trim()) {
    errors.value.lokasi_penyimpanan = 'Lokasi harus diisi'
  }
  if (!form.value.penanggung_jawab.trim()) {
    errors.value.penanggung_jawab = 'Penanggung Jawab harus diisi'
  }

  return Object.keys(errors.value).length === 0
}

const clearError = (field) => {
  if (errors.value[field]) {
    delete errors.value[field]
  }
}

const submitForm = async () => {
  if (!validateForm()) {
    return
  }

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

const fetchData = async () => {
  try {
    const response = await axios.get(`${API_URL}/barang`)
    assets.value = response.data.data
  } catch (error) {
    console.error('Error fetching data:', error)
  }
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

/* Error styles */
.form-group.has-error input,
.form-group.has-error select {
  border-color: #ff3b30;
}

.form-group.has-error input:focus,
.form-group.has-error select:focus {
  border-color: #ff3b30;
  box-shadow: 0 0 0 3px rgba(255, 59, 48, 0.1);
}

.error-message {
  font-size: 12px;
  color: #ff3b30;
  margin-top: 2px;
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

th, td {
  padding: 12px 16px;
  text-align: left;
  border-bottom: 1px solid #e5e5e5;
  overflow-wrap: break-word;
  word-break: normal;
}

th {
  font-weight: 600;
  color: #1d1d1f;
  white-space: nowrap;
}

td {
  color: #1d1d1f;
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

/* Responsive */
@media (max-width: 768px) {
  .content-grid {
    grid-template-columns: 1fr;
  }

  .form-row {
    grid-template-columns: 1fr;
  }
}
</style>
