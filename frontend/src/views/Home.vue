<template>
  <div class="page-wrapper">
    <!-- Statistik -->
    <div class="stats-section">
      <div class="stat-card stat-card-blue">
        <div class="stat-card-bg">
          <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
          </svg>
        </div>
        <div class="stat-icon">
          <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
            <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
            <line x1="12" y1="22.08" x2="12" y2="12"></line>
          </svg>
        </div>
        <span class="stat-value">{{ displayTotal }}</span>
        <span class="stat-label">Total Aset</span>
      </div>
      <div class="stat-card stat-card-green">
        <div class="stat-card-bg">
          <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
            <rect x="1" y="3" width="15" height="13"></rect>
            <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
          </svg>
        </div>
        <div class="stat-icon">
          <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="1" y="3" width="15" height="13"></rect>
            <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
            <circle cx="5.5" cy="18.5" r="2.5"></circle>
            <circle cx="18.5" cy="18.5" r="2.5"></circle>
          </svg>
        </div>
        <span class="stat-value">{{ displayTotalUnit }}</span>
        <span class="stat-label">Total Unit Barang</span>
      </div>
      <div class="stat-card stat-card-orange">
        <div class="stat-card-bg">
          <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
          </svg>
        </div>
        <div class="stat-icon">
          <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
          </svg>
        </div>
        <span class="stat-value">{{ displayTotalAktivitas }}</span>
        <span class="stat-label">Total Aktivitas</span>
      </div>
      <div class="stat-card stat-card-teal">
        <div class="stat-card-bg">
          <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
            <polyline points="22 4 12 14.01 9 11.01"></polyline>
          </svg>
        </div>
        <div class="stat-icon">
          <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
            <polyline points="22 4 12 14.01 9 11.01"></polyline>
          </svg>
        </div>
        <span class="stat-value">{{ displayKondisiBaik }}</span>
        <span class="stat-label">Kondisi Baik</span>
      </div>
    </div>

    <!-- Chart & Activity Row -->
    <div class="chart-activity-row">
      <!-- Donut Chart Kondisi -->
      <div class="chart-section">
        <h3>Kondisi Barang</h3>
        <div class="chart-content">
          <div class="donut-wrapper">
            <div class="donut" :style="{ '--donut-bg': donutBg, '--donut-progress': donutProgress + '%' }">
              <div class="donut-hole">
                <span class="donut-total">{{ displayTotal }}</span>
                <span class="donut-total-label">Total</span>
              </div>
            </div>
          </div>
          <div class="chart-legend">
            <div class="legend-item" v-for="(value, key) in kondisiData" :key="key">
              <span class="legend-dot" :style="{ background: kondisiColors[key] }"></span>
              <span class="legend-label">{{ key }}</span>
              <span class="legend-value">{{ value }}</span>
              <span class="legend-percent">({{ getPercentage(value) }}%)</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Aktivitas Terbaru -->
      <div class="activity-section">
        <h3>Aktivitas Terbaru</h3>
        <div class="activity-list" v-if="recentActivity.length > 0">
          <div class="activity-item" v-for="item in recentActivity" :key="item.id">
            <div class="activity-icon" :class="getActivityIconClass(item.perubahan)">
              <svg v-if="item.perubahan.includes('Tambah')" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
              </svg>
              <svg v-else-if="item.perubahan.includes('Edit')" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
              </svg>
              <svg v-else xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="3 6 5 6 21 6"></polyline>
                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
              </svg>
            </div>
            <div class="activity-info">
              <span class="activity-name">{{ item.nama_aset }}</span>
              <span class="activity-detail">{{ item.perubahan }} &middot; {{ formatTimeAgo(item.created_at) }}</span>
            </div>
            <span class="activity-badge" :class="getActivityBadgeClass(item.perubahan)">{{ item.perubahan }}</span>
          </div>
        </div>
        <div class="activity-empty" v-else>
          <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"></circle>
            <polyline points="12 6 12 12 16 14"></polyline>
          </svg>
          <p>Belum ada aktivitas</p>
        </div>
      </div>
    </div>

    <div class="data-section">
      <div class="section-header">
        <h3>Data Inventaris Barang</h3>
        <div class="header-actions">
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
            <div class="custom-select" :class="{ 'is-open': isPerPageDropdownOpen }">
              <button
                type="button"
                class="select-trigger"
                @click="togglePerPageDropdown"
                @blur="closePerPageDropdownDelayed"
              >
                <span class="select-value">{{ perPage }}</span>
                <svg class="select-arrow" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
              </button>
              <Transition name="dropdown">
                <div v-if="isPerPageDropdownOpen" class="select-dropdown">
                  <button
                    v-for="option in perPageOptions"
                    :key="option"
                    type="button"
                    class="select-option"
                    :class="{ 'is-selected': perPage === option }"
                    @mousedown.prevent="selectPerPageOption(option)"
                  >
                    <span>{{ option }}</span>
                    <svg v-if="perPage === option" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
                <th class="center-col">Gambar</th>
                <th>Kode Aset</th>
                <th>Kode Barang</th>
                <th>Nama Aset</th>
                <th>Jenis Aset</th>
                <th class="center-col">Jumlah</th>
                <th class="kondisi-col">Kondisi</th>
                <th>Lokasi</th>
                <th>Penanggung Jawab</th>
                <th>Tahun</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in barangList" :key="item.id">
                <td>{{ getRowNumber(index) }}</td>
                <td class="center-col">
                  <div class="image-cell">
                    <img
                      v-if="hasValidImage(item)"
                      :src="getImageUrl(item.gambar)"
                      :alt="item.nama_aset"
                      class="table-image"
                      @click="openImageModal(item)"
                      @error="onImageError(item)"
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
                <td>{{ item.kode_aset }}</td>
                <td>{{ item.kode_barang }}</td>
                <td>{{ item.nama_aset }}</td>
                <td>{{ item.jenis_aset }}</td>
                <td class="center-col">{{ item.jumlah }}</td>
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
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '../api'
import * as XLSX from 'xlsx'

const user = ref(null)
const totalAset = ref(0)
const totalUnit = ref(0)
const totalAktivitas = ref(0)
const displayTotal = ref(0)
const displayTotalUnit = ref(0)
const displayTotalAktivitas = ref(0)
const displayKondisiBaik = ref(0)
const kondisiData = ref({ 'Baik': 0, 'Rusak Ringan': 0, 'Rusak Berat': 0 })
const recentActivity = ref([])
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

const imageModal = ref({
  show: false,
  item: null
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

const onImageError = (item) => {
  failedImages.value.add(item.id)
}

const hasValidImage = (item) => {
  return item.gambar && !failedImages.value.has(item.id)
}

const openImageModal = (item) => {
  if (item.gambar) {
    imageModal.value = { show: true, item }
  }
}

const closeImageModal = () => {
  imageModal.value = { show: false, item: null }
}

const pagination = ref({
  current_page: 1,
  last_page: 1,
  per_page: 10,
  total: 0
})

const perPageOptions = [10, 25, 50, 100, 250]
const perPage = ref(10)
const sortBy = ref('nama_aset')
const sortOptions = [
  { value: 'nama_aset', label: 'Nama Aset A-Z' },
  { value: 'kode_barang', label: 'Kode Barang' }
]
const sortByLabel = computed(() => {
  const found = sortOptions.find(o => o.value === sortBy.value)
  return found ? found.label : 'Nama Aset A-Z'
})

// Sort dropdown state
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

// PerPage dropdown state
const isPerPageDropdownOpen = ref(false)
const perPageDropdownTimeout = ref(null)

const togglePerPageDropdown = () => {
  isPerPageDropdownOpen.value = !isPerPageDropdownOpen.value
}

const closePerPageDropdownDelayed = () => {
  perPageDropdownTimeout.value = setTimeout(() => {
    isPerPageDropdownOpen.value = false
  }, 150)
}

const selectPerPageOption = (option) => {
  clearTimeout(perPageDropdownTimeout.value)
  if (perPage.value !== option) {
    perPage.value = option
    handlePerPageChange()
  }
  isPerPageDropdownOpen.value = false
}

const getRowNumber = (index) => {
  return (pagination.value.current_page - 1) * pagination.value.per_page + index + 1
}

const kondisiClass = (kondisi) => {
  if (kondisi === 'Baik') return 'kondisi-baik'
  if (kondisi === 'Rusak Ringan') return 'kondisi-ringan'
  if (kondisi === 'Rusak Berat') return 'kondisi-berat'
  return ''
}

const kondisiColors = {
  'Baik': '#34c759',
  'Rusak Ringan': '#ff9500',
  'Rusak Berat': '#ff3b30'
}

const donutProgress = ref(0)

const donutBg = computed(() => {
  const total = totalAset.value
  const baik = kondisiData.value['Baik']
  const ringan = kondisiData.value['Rusak Ringan']
  const berat = kondisiData.value['Rusak Berat']

  if (!total || (baik === 0 && ringan === 0 && berat === 0)) {
    return 'conic-gradient(#e0e0e0 0% 100%)'
  }

  const pBaik = (baik / total) * 100
  const pRingan = (ringan / total) * 100

  return `conic-gradient(
    ${kondisiColors['Baik']} 0% ${pBaik}%,
    ${kondisiColors['Rusak Ringan']} ${pBaik}% ${pBaik + pRingan}%,
    ${kondisiColors['Rusak Berat']} ${pBaik + pRingan}% 100%
  )`
})

const animateDonut = () => {
  const duration = 1200
  const targetTotal = totalAset.value
  const targetUnit = totalUnit.value
  const targetAktivitas = totalAktivitas.value
  const targetBaik = kondisiData.value['Baik']
  const start = performance.now()
  const step = (now) => {
    const elapsed = now - start
    const t = Math.min(elapsed / duration, 1)
    const ease = 1 - Math.pow(1 - t, 3)
    donutProgress.value = ease * 100
    displayTotal.value = Math.round(ease * targetTotal)
    displayTotalUnit.value = Math.round(ease * targetUnit)
    displayTotalAktivitas.value = Math.round(ease * targetAktivitas)
    displayKondisiBaik.value = Math.round(ease * targetBaik)
    if (t < 1) requestAnimationFrame(step)
  }
  requestAnimationFrame(step)
}

const getPercentage = (value) => {
  if (!totalAset.value) return 0
  return ((value / totalAset.value) * 100).toFixed(1)
}

const getActivityIconClass = (perubahan) => {
  if (perubahan.includes('Tambah')) return 'activity-icon-add'
  if (perubahan.includes('Edit')) return 'activity-icon-edit'
  return 'activity-icon-delete'
}

const getActivityBadgeClass = (perubahan) => {
  if (perubahan.includes('Tambah')) return 'badge-add'
  if (perubahan.includes('Edit')) return 'badge-edit'
  return 'badge-delete'
}

const formatTimeAgo = (datetime) => {
  if (!datetime) return ''
  const now = new Date()
  const date = new Date(datetime)
  const diff = Math.floor((now - date) / 1000)
  if (diff < 60) return 'Baru saja'
  if (diff < 3600) return `${Math.floor(diff / 60)} menit lalu`
  if (diff < 86400) return `${Math.floor(diff / 3600)} jam lalu`
  if (diff < 604800) return `${Math.floor(diff / 86400)} hari lalu`
  const y = date.getFullYear()
  const m = String(date.getMonth() + 1).padStart(2, '0')
  const d = String(date.getDate()).padStart(2, '0')
  return `${d}/${m}/${y}`
}

const fetchStatistik = async () => {
  try {
    const response = await api.get('/statistik')
    totalAset.value = response.data.data.total_aset
    totalUnit.value = response.data.data.total_unit || 0
    totalAktivitas.value = response.data.data.total_aktivitas || 0
    kondisiData.value = response.data.data.kondisi
    recentActivity.value = response.data.data.recent_activity || []
    animateDonut()
  } catch (error) {
    console.error('Error fetching statistik:', error)
  }
}

const fetchBarang = async (page = 1) => {
  loading.value = true
  failedImages.value.clear()
  try {
    let url = `/barang?page=${page}&per_page=${perPage.value}&sort_by=${sortBy.value}&sort_order=asc`
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

const handlePerPageChange = () => {
  const oldPerPage = pagination.value.per_page
  const currentPageVal = pagination.value.current_page
  const firstItemIndex = (currentPageVal - 1) * oldPerPage
  const newPage = Math.floor(firstItemIndex / perPage.value) + 1
  const newLastPage = Math.ceil(pagination.value.total / perPage.value)
  const targetPage = Math.min(newPage, newLastPage) || 1
  fetchBarang(targetPage)
}

const handleSortChange = () => {
  fetchBarang(1)
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
  fetchStatistik()
  fetchBarang()
})
</script>

<style scoped>
.stats-section {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
  margin-bottom: 24px;
}

.stat-card {
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 8px;
  border-radius: 14px;
  padding: 28px 20px;
  overflow: hidden;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  animation: statFadeIn 0.5s ease-out both;
}

.stat-card:nth-child(1) { animation-delay: 0s; }
.stat-card:nth-child(2) { animation-delay: 0.1s; }
.stat-card:nth-child(3) { animation-delay: 0.2s; }
.stat-card:nth-child(4) { animation-delay: 0.3s; }

@keyframes statFadeIn {
  from {
    opacity: 0;
    transform: translateY(16px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.stat-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
}

.stat-card-blue {
  background: linear-gradient(135deg, #2196f3, #1976d2);
  color: #ffffff;
}

.stat-card-green {
  background: linear-gradient(135deg, #34c759, #2aa84a);
  color: #ffffff;
}

.stat-card-orange {
  background: linear-gradient(135deg, #ff9500, #e08600);
  color: #ffffff;
}

.stat-card-teal {
  background: linear-gradient(135deg, #00bcd4, #0097a7);
  color: #ffffff;
}

.stat-card-bg {
  position: absolute;
  right: -10px;
  bottom: -10px;
  opacity: 0.15;
  pointer-events: none;
}

.stat-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 44px;
  height: 44px;
  border-radius: 12px;
  background: rgba(255, 255, 255, 0.2);
  color: #ffffff;
  flex-shrink: 0;
  backdrop-filter: blur(4px);
}

.stat-value {
  font-size: 32px;
  font-weight: 700;
  color: #ffffff;
  line-height: 1;
}

.stat-label {
  font-size: 13px;
  color: rgba(255, 255, 255, 0.85);
  font-weight: 500;
}

.chart-activity-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
  margin-bottom: 24px;
}

.chart-section {
  background: var(--bg-card);
  border: 1px solid var(--border-color);
  border-radius: 12px;
  padding: 24px;
  display: flex;
  flex-direction: column;
}

.chart-section h3 {
  margin: 0 0 20px 0;
  font-size: 18px;
  font-weight: 600;
  color: var(--text-primary);
}

.chart-content {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 40px;
  flex: 1;
}

.donut-wrapper {
  flex-shrink: 0;
}

.donut {
  width: 220px;
  height: 220px;
  border-radius: 50%;
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
}

.donut::before {
  content: '';
  position: absolute;
  inset: 0;
  border-radius: 50%;
  background: var(--donut-bg);
  -webkit-mask: conic-gradient(black 0% var(--donut-progress), transparent var(--donut-progress) 100%);
  mask: conic-gradient(black 0% var(--donut-progress), transparent var(--donut-progress) 100%);
}

.donut-hole {
  width: 140px;
  height: 140px;
  border-radius: 50%;
  background: var(--bg-card);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 2px;
  position: relative;
  z-index: 1;
}

.donut-total {
  font-size: 34px;
  font-weight: 700;
  color: var(--text-primary);
  line-height: 1;
}

.donut-total-label {
  font-size: 12px;
  color: var(--text-secondary);
  font-weight: 500;
}

.chart-legend {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.legend-item {
  display: flex;
  align-items: center;
  gap: 10px;
}

.legend-dot {
  width: 12px;
  height: 12px;
  border-radius: 3px;
  flex-shrink: 0;
}

.legend-label {
  font-size: 14px;
  color: var(--text-primary);
  font-weight: 500;
  min-width: 100px;
}

.legend-value {
  font-size: 14px;
  font-weight: 600;
  color: var(--text-primary);
}

.legend-percent {
  font-size: 13px;
  color: var(--text-secondary);
}

/* Activity Section */
.activity-section {
  background: var(--bg-card);
  border: 1px solid var(--border-color);
  border-radius: 12px;
  padding: 24px;
  display: flex;
  flex-direction: column;
}

.activity-section h3 {
  margin: 0 0 20px 0;
  font-size: 18px;
  font-weight: 600;
  color: var(--text-primary);
}

.activity-list {
  display: flex;
  flex-direction: column;
  gap: 0;
  flex: 1;
}

.activity-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 0;
  border-bottom: 1px solid var(--border-light);
  animation: activitySlideIn 0.4s ease-out both;
}

.activity-item:nth-child(1) { animation-delay: 0.15s; }
.activity-item:nth-child(2) { animation-delay: 0.3s; }
.activity-item:nth-child(3) { animation-delay: 0.45s; }
.activity-item:nth-child(4) { animation-delay: 0.6s; }
.activity-item:nth-child(5) { animation-delay: 0.75s; }

@keyframes activitySlideIn {
  from {
    opacity: 0;
    transform: translateX(20px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

.activity-item:last-child {
  border-bottom: none;
}

.activity-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  border-radius: 8px;
  flex-shrink: 0;
}

.activity-icon-add {
  background: #d4edda;
  color: #155724;
}

.activity-icon-edit {
  background: #fff3cd;
  color: #856404;
}

.activity-icon-delete {
  background: #f8d7da;
  color: #721c24;
}

.activity-info {
  display: flex;
  flex-direction: column;
  gap: 2px;
  flex: 1;
  min-width: 0;
}

.activity-name {
  font-size: 13px;
  font-weight: 600;
  color: var(--text-primary);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.activity-detail {
  font-size: 12px;
  color: var(--text-secondary);
}

.activity-badge {
  font-size: 11px;
  font-weight: 500;
  padding: 3px 8px;
  border-radius: 4px;
  white-space: nowrap;
  flex-shrink: 0;
}

.badge-add {
  background: #d4edda;
  color: #155724;
}

.badge-edit {
  background: #fff3cd;
  color: #856404;
}

.badge-delete {
  background: #f8d7da;
  color: #721c24;
}

.activity-empty {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  flex: 1;
  gap: 12px;
  color: var(--text-secondary);
  padding: 32px 0;
}

.activity-empty svg {
  opacity: 0.4;
}

.activity-empty p {
  font-size: 14px;
  margin: 0;
}

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

.sort-controls,
.pagination-controls {
  display: flex;
  align-items: center;
  gap: 10px;
}

.sort-controls label,
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

.center-col,
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

/* Table Image */
.image-cell {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 4px 0;
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
  color: rgba(255, 255, 255, 0.9);
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

/* Tablet */
@media (max-width: 768px) {
  .stats-section {
    grid-template-columns: 1fr 1fr;
  }

  .stat-card {
    padding: 20px 16px;
  }

  .stat-value {
    font-size: 26px;
  }

  .stat-card-bg svg {
    width: 60px;
    height: 60px;
  }

  .chart-activity-row {
    grid-template-columns: 1fr;
  }

  .chart-section {
    padding: 20px;
  }

  .chart-content {
    gap: 24px;
  }

  .donut {
    width: 150px;
    height: 150px;
  }

  .donut-hole {
    width: 90px;
    height: 90px;
  }

  .donut-total {
    font-size: 22px;
  }

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
  .stats-section {
    grid-template-columns: 1fr 1fr;
  }

  .stat-card {
    padding: 18px 14px;
    gap: 6px;
  }

  .stat-icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
  }

  .stat-icon svg {
    width: 18px;
    height: 18px;
  }

  .stat-value {
    font-size: 22px;
  }

  .stat-card-bg svg {
    width: 50px;
    height: 50px;
  }

  .chart-section {
    padding: 16px;
  }

  .chart-section h3,
  .activity-section h3 {
    font-size: 16px;
    margin-bottom: 16px;
  }

  .chart-content {
    flex-direction: column;
    align-items: center;
    gap: 20px;
  }

  .activity-section {
    padding: 16px;
  }

  .activity-item {
    gap: 10px;
    padding: 10px 0;
  }

  .activity-name {
    white-space: normal;
    overflow: visible;
    text-overflow: unset;
    font-size: 12px;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  .activity-detail {
    font-size: 11px;
  }

  .activity-icon {
    width: 28px;
    height: 28px;
    border-radius: 6px;
  }

  .activity-icon svg {
    width: 12px;
    height: 12px;
  }

  .activity-badge {
    display: none;
  }

  .donut {
    width: 140px;
    height: 140px;
  }

  .donut-hole {
    width: 86px;
    height: 86px;
  }

  .donut-total {
    font-size: 22px;
  }

  .donut-total-label {
    font-size: 10px;
  }

  .chart-legend {
    width: 100%;
  }

  .legend-item {
    gap: 8px;
  }

  .legend-label {
    font-size: 12px;
    min-width: 80px;
  }

  .legend-value {
    font-size: 12px;
  }

  .legend-percent {
    font-size: 11px;
  }

  .legend-dot {
    width: 10px;
    height: 10px;
  }

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
