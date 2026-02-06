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

          <div class="form-group">
            <label>Gambar Barang (Opsional)</label>
            <div
              class="image-dropzone"
              :class="{ 'dropzone-active': isImageDragging, 'has-image': imagePreview }"
              @dragover.prevent="isImageDragging = true"
              @dragleave.prevent="isImageDragging = false"
              @drop.prevent="handleImageDrop"
              @click="triggerImageInput"
            >
              <input
                type="file"
                ref="imageInput"
                @change="handleImageSelect"
                accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                hidden
              />
              <div v-if="!imagePreview" class="dropzone-content">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                  <circle cx="8.5" cy="8.5" r="1.5"></circle>
                  <polyline points="21 15 16 10 5 21"></polyline>
                </svg>
                <p>Drag & drop gambar atau <span>browse</span></p>
                <span class="file-hint">Format: JPG, PNG, GIF, WebP (Max 2MB)</span>
              </div>
              <div v-else class="image-preview">
                <img :src="imagePreview" alt="Preview" />
                <button type="button" class="remove-image" @click.stop="removeImage">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                  </svg>
                </button>
              </div>
            </div>
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
        <div class="header-right">
          <div class="sort-controls">
            <div class="custom-select" :class="{ 'is-open': isSortDropdownOpen }">
              <button
                type="button"
                class="select-trigger"
                @click="toggleSortDropdown"
                @blur="closeSortDropdownDelayed"
              >
                <span class="select-value">{{ sortByLabel }}</span>
                <svg class="select-arrow" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
              </button>
              <Transition name="dropdown">
                <div v-if="isSortDropdownOpen" class="select-dropdown">
                  <button
                    v-for="option in sortOptions"
                    :key="option.value"
                    type="button"
                    class="select-option"
                    :class="{ 'is-selected': sortBy === option.value }"
                    @mousedown.prevent="selectSortOption(option.value)"
                  >
                    <span>{{ option.label }}</span>
                    <svg v-if="sortBy === option.value" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                  </button>
                </div>
              </Transition>
            </div>
          </div>
          <div class="pagination-controls">
            <div class="custom-select" :class="{ 'is-open': isDropdownOpen }">
              <button
                type="button"
                class="select-trigger"
                @click="toggleDropdown"
                @blur="closeDropdownDelayed"
              >
                <span class="select-value">{{ selectedPerPage }}</span>
                <svg class="select-arrow" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
              </button>
              <Transition name="dropdown">
                <div v-if="isDropdownOpen" class="select-dropdown">
                  <button
                    v-for="option in perPageOptions"
                    :key="option"
                    type="button"
                    class="select-option"
                    :class="{ 'is-selected': selectedPerPage === option }"
                    @mousedown.prevent="selectOption(option)"
                  >
                    <span>{{ option }}</span>
                    <svg v-if="selectedPerPage === option" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                  </button>
                </div>
              </Transition>
            </div>
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
      </div>

      <div class="table-container">
        <Transition name="fade" mode="out-in">
          <table v-if="assets.length > 0" :key="tableKey">
            <thead>
              <tr>
                <th>No</th>
                <th class="center-col">Gambar</th>
                <th>Kode Aset</th>
                <th>Kode Barang</th>
                <th>Nama Aset</th>
                <th>Jenis</th>
                <th class="center-col">Jumlah</th>
                <th class="kondisi-col">Kondisi</th>
                <th>Lokasi</th>
                <th>Penanggung Jawab</th>
                <th>Tahun</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(asset, index) in assets" :key="asset.id">
                <td>{{ getRowNumber(index) }}</td>
                <td class="center-col">
                  <div class="image-cell">
                    <img
                      v-if="hasValidImage(asset)"
                      :src="getImageUrl(asset.gambar)"
                      :alt="asset.nama_aset"
                      class="table-image"
                      @click.stop="openImageModal(asset)"
                      @error="onImageError(asset)"
                      loading="lazy"
                    />
                    <div v-else class="no-image">
                      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <circle cx="8.5" cy="8.5" r="1.5"></circle>
                        <polyline points="21 15 16 10 5 21"></polyline>
                      </svg>
                    </div>
                  </div>
                </td>
                <td>{{ asset.kode_aset }}</td>
                <td>{{ asset.kode_barang }}</td>
                <td>{{ asset.nama_aset }}</td>
                <td>{{ asset.jenis_aset }}</td>
                <td class="center-col">{{ asset.jumlah }}</td>
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

    <!-- Image Modal -->
    <Transition name="modal">
      <div v-if="imageModal.show" class="image-modal-overlay" @click="closeImageModal">
        <div class="image-modal-content" @click.stop>
          <button class="modal-close" @click="closeImageModal">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="18" y1="6" x2="6" y2="18"></line>
              <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
          </button>
          <img :src="getImageUrl(imageModal.item?.gambar)" :alt="imageModal.item?.nama_aset" />
          <div class="modal-caption">{{ imageModal.item?.nama_aset }}</div>
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
import { ref, computed, onMounted } from 'vue'
import api from '../api'
import * as XLSX from 'xlsx'

const currentYear = new Date().getFullYear()
const fileInput = ref(null)
const imageInput = ref(null)
const selectedFile = ref(null)
const selectedImage = ref(null)
const imagePreview = ref(null)
const isDragging = ref(false)
const isImageDragging = ref(false)
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

const perPageOptions = [10, 25, 50, 100, 250]
const selectedPerPage = ref(10)
const sortBy = ref('nama_aset')
const sortOptions = [
  { value: 'nama_aset', label: 'Nama Aset A-Z' },
  { value: 'kode_barang', label: 'Kode Barang' }
]
const sortByLabel = computed(() => {
  const found = sortOptions.find(o => o.value === sortBy.value)
  return found ? found.label : 'Nama Aset A-Z'
})
const isSortDropdownOpen = ref(false)
const sortDropdownTimeout = ref(null)

const toggleSortDropdown = () => {
  isSortDropdownOpen.value = !isSortDropdownOpen.value
}

const closeSortDropdownDelayed = () => {
  sortDropdownTimeout.value = setTimeout(() => {
    isSortDropdownOpen.value = false
  }, 150)
}

const selectSortOption = (value) => {
  clearTimeout(sortDropdownTimeout.value)
  if (sortBy.value !== value) {
    sortBy.value = value
    handleSortChange()
  }
  isSortDropdownOpen.value = false
}

const isDropdownOpen = ref(false)
const dropdownTimeout = ref(null)

const toggleDropdown = () => {
  isDropdownOpen.value = !isDropdownOpen.value
}

const closeDropdownDelayed = () => {
  dropdownTimeout.value = setTimeout(() => {
    isDropdownOpen.value = false
  }, 150)
}

const selectOption = (option) => {
  clearTimeout(dropdownTimeout.value)
  if (selectedPerPage.value !== option) {
    selectedPerPage.value = option
    handlePerPageChange()
  }
  isDropdownOpen.value = false
}

const handlePerPageChange = () => {
  const oldPerPage = pagination.value.per_page
  const currentPage = pagination.value.current_page
  const firstItemIndex = (currentPage - 1) * oldPerPage
  const newPage = Math.floor(firstItemIndex / selectedPerPage.value) + 1
  const newLastPage = Math.ceil(pagination.value.total / selectedPerPage.value)
  const targetPage = Math.min(newPage, newLastPage) || 1
  tableKey.value++
  fetchData(targetPage)
}

const handleSortChange = () => {
  tableKey.value++
  fetchData(1)
}

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

const failedImages = ref(new Set())

const showNotification = (message, type = 'success') => {
  notification.value = { show: true, message, type }
  setTimeout(() => {
    notification.value.show = false
  }, 3000)
}

const getImageUrl = (filename) => {
  if (!filename) return ''
  let baseUrl = import.meta.env.VITE_API_URL || 'http://localhost:8000'
  baseUrl = baseUrl.replace(/\/api\/?$/, '')
  return `${baseUrl}/storage/gambar_barang/${filename}`
}

const onImageError = (asset) => {
  failedImages.value.add(asset.id)
}

const hasValidImage = (asset) => {
  return asset.gambar && !failedImages.value.has(asset.id)
}

const imageModal = ref({
  show: false,
  item: null
})

const openImageModal = (asset) => {
  if (asset.gambar) {
    imageModal.value = { show: true, item: asset }
  }
}

const closeImageModal = () => {
  imageModal.value = { show: false, item: null }
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

const triggerImageInput = () => {
  imageInput.value.click()
}

const handleImageSelect = (event) => {
  const file = event.target.files[0]
  if (file) {
    validateAndSetImage(file)
  }
}

const handleImageDrop = (event) => {
  isImageDragging.value = false
  const file = event.dataTransfer.files[0]
  if (file) {
    validateAndSetImage(file)
  }
}

const validateAndSetImage = (file) => {
  const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp']
  if (!validTypes.includes(file.type)) {
    showNotification('Format gambar harus JPG, PNG, GIF, atau WebP', 'error')
    return
  }
  if (file.size > 2 * 1024 * 1024) {
    showNotification('Ukuran gambar maksimal 2MB', 'error')
    return
  }
  selectedImage.value = file
  const reader = new FileReader()
  reader.onload = (e) => {
    imagePreview.value = e.target.result
  }
  reader.readAsDataURL(file)
}

const removeImage = () => {
  selectedImage.value = null
  imagePreview.value = null
  if (imageInput.value) {
    imageInput.value.value = ''
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

    const response = await api.post('/barang/import', { data })
    uploadSuccess.value = true

    // Show success message from backend
    const errorCount = response.data.errors?.length || 0

    if (errorCount > 0) {
      console.log('Import errors:', response.data.errors)
      showNotification(`${response.data.message}, ${errorCount} gagal`, 'warning')
    } else {
      showNotification(response.data.message)
    }

    selectedFile.value = null
    fileInput.value.value = ''
    fetchData(pagination.value.current_page)
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

        const headerRow = jsonData[0] || []

        const safeString = (val) => {
          if (val === null || val === undefined || val === '') return '-'
          const strVal = String(val).trim()
          if (strVal === 'NaN' || strVal === 'nan' || strVal === 'NULL' || strVal === 'null' || strVal === '') return '-'
          return strVal
        }

        // Mapping nama header yang dikenali → field database
        const headerAliases = {
          kode_aset: ['kode aset', 'kode_aset', 'kodeaset', 'asset code'],
          kode_barang: ['kode barang', 'kode_barang', 'kodebarang', 'item code'],
          nama_aset: ['nama aset', 'nama_aset', 'namaaset', 'nama barang', 'nama_barang', 'asset name'],
          jenis_aset: ['jenis aset', 'jenis_aset', 'jenisaset', 'jenis', 'jenis barang', 'type'],
          jumlah: ['jumlah', 'qty', 'quantity', 'jml'],
          kondisi: ['kondisi', 'condition', 'status'],
          lokasi_penyimpanan: ['lokasi penyimpanan', 'lokasi_penyimpanan', 'lokasi', 'location'],
          penanggung_jawab: ['penanggung jawab', 'penanggung_jawab', 'pj', 'pic'],
          tahun_perolehan: ['tahun perolehan', 'tahun_perolehan', 'tahun', 'year']
        }

        // Coba cocokkan header dengan alias
        const headerMap = {}
        const normalizedHeaders = headerRow.map(h => String(h || '').toLowerCase().trim())

        for (const [field, aliases] of Object.entries(headerAliases)) {
          const idx = normalizedHeaders.findIndex(h => aliases.includes(h))
          if (idx !== -1) headerMap[field] = idx
        }

        const useHeaderMapping = Object.keys(headerMap).length >= 3

        // Fallback: urutan posisi kolom (skip kolom "No" jika ada)
        let offset = 0
        if (!useHeaderMapping) {
          const firstHeader = normalizedHeaders[0]
          if (firstHeader === 'no' || firstHeader === 'no.' || firstHeader === 'nomor') offset = 1
        }

        const rows = jsonData.slice(1).filter(row => row.some(cell => {
          if (cell === null || cell === undefined || cell === '') return false
          return String(cell).trim() !== '' && String(cell).trim() !== 'NaN'
        }))

        const mappedData = rows.map((row) => {
          let kodeAset, kodeBarang, namaAset, jenisAset, jumlah, kondisi, lokasi, pj, tahun

          if (useHeaderMapping) {
            kodeAset = safeString(row[headerMap.kode_aset])
            kodeBarang = safeString(row[headerMap.kode_barang])
            namaAset = safeString(row[headerMap.nama_aset])
            jenisAset = safeString(row[headerMap.jenis_aset])
            jumlah = parseInt(row[headerMap.jumlah]) || 1
            kondisi = safeString(row[headerMap.kondisi])
            lokasi = safeString(row[headerMap.lokasi_penyimpanan])
            pj = safeString(row[headerMap.penanggung_jawab])
            tahun = parseInt(row[headerMap.tahun_perolehan]) || new Date().getFullYear()
          } else {
            kodeAset = safeString(row[0 + offset])
            kodeBarang = safeString(row[1 + offset])
            namaAset = safeString(row[2 + offset])
            jenisAset = safeString(row[3 + offset])
            jumlah = parseInt(row[4 + offset]) || 1
            kondisi = safeString(row[5 + offset])
            lokasi = safeString(row[6 + offset])
            pj = safeString(row[7 + offset])
            tahun = parseInt(row[8 + offset]) || new Date().getFullYear()
          }

          if (kondisi === '-') kondisi = 'Baik'

          return {
            kode_aset: kodeAset,
            kode_barang: kodeBarang,
            nama_aset: namaAset,
            jenis_aset: jenisAset,
            jumlah,
            kondisi,
            lokasi_penyimpanan: lokasi,
            penanggung_jawab: pj,
            tahun_perolehan: tahun
          }
        })

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
    const formData = new FormData()
    Object.keys(form.value).forEach(key => {
      formData.append(key, form.value[key])
    })
    if (selectedImage.value) {
      formData.append('gambar', selectedImage.value)
    }
    await api.post('/barang', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
    showNotification('Data berhasil disimpan')
    resetForm()
    fetchData(pagination.value.current_page)
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
  removeImage()
}

const fetchData = async (page = 1) => {
  loading.value = true
  try {
    let url = `/barang?page=${page}&per_page=${selectedPerPage.value}&sort_by=${sortBy.value}&sort_order=asc`
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
  color: var(--text-primary);
}

.content-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 24px;
  margin-bottom: 24px;
}

.card {
  background: var(--bg-card);
  border-radius: 12px;
  border: 1px solid var(--border-color);
  padding: 24px;
}

.card h2 {
  margin: 0 0 4px 0;
  font-size: 18px;
  font-weight: 600;
  color: var(--text-primary);
}

.card-description {
  margin: 0 0 20px 0;
  font-size: 14px;
  color: var(--text-secondary);
}

/* Dropzone */
.dropzone {
  border: 2px dashed var(--border-color);
  border-radius: 12px;
  padding: 40px 24px;
  text-align: center;
  cursor: pointer;
  transition: all 0.2s ease;
  margin-bottom: 16px;
}

.dropzone:hover {
  border-color: #0071e3;
  background: var(--bg-primary);
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
  color: var(--text-secondary);
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
  color: var(--text-primary);
  font-weight: 500;
}

.file-hint {
  font-size: 12px;
  color: var(--text-secondary);
}

/* Image Dropzone */
.image-dropzone {
  border: 2px dashed var(--border-color);
  border-radius: 12px;
  padding: 24px;
  text-align: center;
  cursor: pointer;
  transition: all 0.2s ease;
  min-height: 140px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--bg-primary);
}

.image-dropzone:hover {
  border-color: #0071e3;
  background: rgba(0, 113, 227, 0.03);
}

.image-dropzone.dropzone-active {
  border-color: #0071e3;
  background: rgba(0, 113, 227, 0.05);
}

.image-dropzone.has-image {
  padding: 12px;
  border-style: solid;
  border-color: var(--border-color);
  background: var(--bg-card);
}

.image-dropzone .dropzone-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  color: var(--text-secondary);
}

.image-dropzone .dropzone-content svg {
  opacity: 0.4;
}

.image-dropzone .dropzone-content p {
  margin: 0;
  font-size: 13px;
}

.image-dropzone .dropzone-content p span {
  color: #0071e3;
  text-decoration: underline;
  cursor: pointer;
}

.image-preview {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
}

.image-preview img {
  max-width: 100%;
  max-height: 180px;
  border-radius: 8px;
  object-fit: contain;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  border: 1px solid var(--border-color);
}

.remove-image {
  position: absolute;
  top: -8px;
  right: calc(50% - 98px);
  width: 28px;
  height: 28px;
  border-radius: 50%;
  background: #ff3b30;
  border: 2px solid var(--bg-card);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s ease;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
}

.remove-image:hover {
  background: #e0352b;
  transform: scale(1.1);
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
  color: var(--text-primary);
}

.form-group input,
.form-group select {
  padding: 10px 14px;
  border: 1px solid var(--border-color);
  border-radius: 8px;
  font-size: 14px;
  transition: all 0.2s ease;
  background: var(--bg-card);
}

.form-group input:focus,
.form-group select:focus {
  outline: none;
  border-color: #0071e3;
  box-shadow: 0 0 0 3px rgba(0, 113, 227, 0.1);
}

.form-group input::placeholder {
  color: var(--text-secondary);
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
  background: var(--text-secondary);
  cursor: not-allowed;
}

.btn-secondary {
  background: var(--bg-primary);
  color: var(--text-primary);
  border: 1px solid var(--border-color);
}

.btn-secondary:hover {
  background: var(--border-color);
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

.header-right {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
}

.sort-controls {
  display: flex;
  align-items: center;
  gap: 10px;
}

.sort-controls label {
  font-size: 14px;
  font-weight: 500;
  color: var(--text-secondary);
  white-space: nowrap;
}

.pagination-controls {
  display: flex;
  align-items: center;
  gap: 10px;
}

.pagination-controls label {
  font-size: 14px;
  font-weight: 500;
  color: var(--text-secondary);
  white-space: nowrap;
}

/* Custom Dropdown */
.custom-select {
  position: relative;
  min-width: 80px;
}

.select-trigger {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
  padding: 8px 12px;
  border: 1px solid var(--border-color);
  border-radius: 8px;
  background: var(--bg-card);
  color: var(--text-primary);
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
  width: 100%;
}

.select-trigger:hover {
  border-color: var(--text-secondary);
  background: var(--bg-primary);
}

.select-trigger:focus {
  outline: none;
  border-color: #0071e3;
  box-shadow: 0 0 0 3px rgba(0, 113, 227, 0.15);
}

.custom-select.is-open .select-trigger {
  border-color: #0071e3;
  box-shadow: 0 0 0 3px rgba(0, 113, 227, 0.15);
}

.select-value {
  font-weight: 600;
}

.select-arrow {
  color: var(--text-secondary);
  transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  flex-shrink: 0;
}

.custom-select.is-open .select-arrow {
  transform: rotate(180deg);
  color: #0071e3;
}

.select-dropdown {
  position: absolute;
  top: calc(100% + 6px);
  left: 0;
  right: 0;
  background: var(--bg-card);
  border: 1px solid var(--border-color);
  border-radius: 10px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
  overflow: hidden;
  z-index: 100;
}

.select-option {
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 100%;
  padding: 10px 14px;
  border: none;
  background: transparent;
  color: var(--text-primary);
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.15s ease;
  text-align: left;
}

.select-option:hover {
  background: var(--bg-primary);
}

.select-option.is-selected {
  color: #0071e3;
  background: rgba(0, 113, 227, 0.08);
}

.select-option.is-selected:hover {
  background: rgba(0, 113, 227, 0.12);
}

.select-option svg {
  color: #0071e3;
}

/* Dropdown Animation */
.dropdown-enter-active {
  animation: dropdownIn 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

.dropdown-leave-active {
  animation: dropdownOut 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

@keyframes dropdownIn {
  0% {
    opacity: 0;
    transform: translateY(-8px) scale(0.96);
  }
  100% {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

@keyframes dropdownOut {
  0% {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
  100% {
    opacity: 0;
    transform: translateY(-8px) scale(0.96);
  }
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
  font-size: 14px;
}

thead {
  background: var(--bg-primary);
}

th {
  padding: 12px 16px;
  text-align: left;
  border-bottom: 1px solid var(--border-color);
  font-weight: 600;
  color: var(--text-primary);
  white-space: nowrap;
}

td {
  padding: 12px 16px;
  text-align: left;
  border-bottom: 1px solid var(--border-light);
  color: var(--text-primary);
  word-wrap: break-word;
  word-break: normal;
}

tbody tr:hover {
  background: var(--bg-primary);
}

.badge {
  display: inline-block;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 500;
  white-space: nowrap;
}

.badge-success {
  background: #d4edda;
  color: #155724;
}

.badge-warning {
  background: #fff3cd;
  color: #856404;
}

.badge-danger {
  background: #f8d7da;
  color: #721c24;
}

.center-col,
.kondisi-col {
  text-align: center;
}

/* Table Image */
.image-cell {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 4px 0;
  position: relative;
}

.table-image {
  width: 48px;
  height: 48px;
  object-fit: cover;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s ease;
  border: 1px solid var(--border-color);
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
  background: var(--bg-primary);
  position: relative;
  z-index: 1;
}

.table-image:hover {
  transform: scale(1.15);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  z-index: 10;
}

.no-image {
  width: 48px;
  height: 48px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--bg-primary);
  border-radius: 8px;
  color: var(--text-secondary);
  border: 1px dashed var(--border-color);
  opacity: 0.6;
}

.no-image svg {
  opacity: 0.5;
}

/* Image Modal */
.image-modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.85);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2000;
  padding: 20px;
  backdrop-filter: blur(4px);
}

.image-modal-content {
  position: relative;
  max-width: 90vw;
  max-height: 90vh;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.image-modal-content img {
  max-width: 100%;
  max-height: 80vh;
  border-radius: 12px;
  object-fit: contain;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
}

.modal-close {
  position: absolute;
  top: -45px;
  right: -5px;
  width: 36px;
  height: 36px;
  background: rgba(255, 255, 255, 0.15);
  border: none;
  border-radius: 50%;
  color: white;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
}

.modal-close:hover {
  background: rgba(255, 255, 255, 0.25);
  transform: scale(1.1);
}

.modal-caption {
  margin-top: 16px;
  color: white;
  font-size: 14px;
  font-weight: 500;
  text-align: center;
  max-width: 80%;
}

.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-active .image-modal-content,
.modal-leave-active .image-modal-content {
  transition: transform 0.3s ease;
}

.modal-enter-from .image-modal-content {
  transform: scale(0.9);
}

.modal-leave-to .image-modal-content {
  transform: scale(0.9);
}

.empty-table {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 48px;
  color: var(--text-secondary);
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
  background: var(--bg-card);
  border: 1px solid var(--border-color);
  border-radius: 10px;
  padding: 14px 24px;
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 14px;
  font-weight: 500;
  z-index: 1000;
  box-shadow: 0 4px 12px var(--shadow-color);
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

  .header-right {
    flex-direction: column;
    width: 100%;
  }

  .pagination-controls {
    justify-content: center;
    width: 100%;
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
