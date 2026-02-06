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

    <!-- Image Modal -->
    <Transition name="image-modal">
      <div v-if="imageModal.show" class="image-modal-overlay" @click="closeImageModal">
        <div class="image-modal-content" @click.stop>
          <button class="image-modal-close" @click="closeImageModal">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="18" y1="6" x2="6" y2="18"></line>
              <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
          </button>
          <img :src="getImageUrl(imageModal.item?.gambar)" :alt="imageModal.item?.nama_aset" />
          <div class="image-modal-caption">{{ imageModal.item?.nama_aset }}</div>
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
import api from '../api'

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

const handleSortChange = () => {
  tableKey.value++
  fetchData(1)
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
    await api.delete(`/barang/${selectedAsset.value.id}`)
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
    const response = await api.post('/barang/bulk-delete', {
      ids: selectedIds.value
    })
    showNotification(response.data.message || `${selectedIds.value.length} data berhasil dihapus`)
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

.image-modal-close {
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

.image-modal-close:hover {
  background: rgba(255, 255, 255, 0.25);
  transform: scale(1.1);
}

.image-modal-caption {
  margin-top: 16px;
  color: white;
  font-size: 14px;
  font-weight: 500;
  text-align: center;
  max-width: 80%;
}

.image-modal-enter-active,
.image-modal-leave-active {
  transition: opacity 0.3s ease;
}

.image-modal-enter-from,
.image-modal-leave-to {
  opacity: 0;
}

.image-modal-enter-active .image-modal-content,
.image-modal-leave-active .image-modal-content {
  transition: transform 0.3s ease;
}

.image-modal-enter-from .image-modal-content {
  transform: scale(0.9);
}

.image-modal-leave-to .image-modal-content {
  transform: scale(0.9);
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
  color: #ff3b30;
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
  background: var(--bg-primary);
  color: var(--text-primary);
  border: 1px solid var(--border-color);
}

.btn-secondary:hover:not(:disabled) {
  background: var(--border-color);
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

  .header-right {
    flex-direction: column;
    width: 100%;
  }

  .pagination-controls {
    justify-content: center;
    width: 100%;
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
