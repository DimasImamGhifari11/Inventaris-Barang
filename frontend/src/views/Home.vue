<template>
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
              placeholder="Cari kode aset, kode barang, atau nama aset..."
              class="search-input"
            />
          </div>
          <button class="btn-download" @click="downloadExcel" :disabled="barangList.length === 0">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
              <polyline points="7 10 12 15 17 10"></polyline>
              <line x1="12" y1="15" x2="12" y2="3"></line>
            </svg>
            Download Excel
          </button>
        </div>
      </div>

      <div class="table-container" v-if="filteredBarang.length > 0">
        <table>
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
            <tr v-for="(item, index) in filteredBarang" :key="item.id">
              <td>{{ index + 1 }}</td>
              <td>{{ item.kode_aset }}</td>
              <td>{{ item.kode_barang }}</td>
              <td>{{ item.nama_aset }}</td>
              <td>{{ item.jenis_aset }}</td>
              <td>{{ item.jumlah }}</td>
              <td>
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
      </div>

      <div class="empty-state" v-else-if="!loading">
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
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import * as XLSX from 'xlsx'

const user = ref(null)
const barangList = ref([])
const searchQuery = ref('')
const loading = ref(true)

const filteredBarang = computed(() => {
  if (!searchQuery.value) {
    return barangList.value
  }

  const query = searchQuery.value.toLowerCase()
  return barangList.value.filter(item => {
    return (
      item.kode_aset?.toLowerCase().includes(query) ||
      item.kode_barang?.toLowerCase().includes(query) ||
      item.nama_aset?.toLowerCase().includes(query)
    )
  })
})

const kondisiClass = (kondisi) => {
  if (kondisi === 'Baik') return 'kondisi-baik'
  if (kondisi === 'Rusak Ringan') return 'kondisi-ringan'
  if (kondisi === 'Rusak Berat') return 'kondisi-berat'
  return ''
}

const fetchBarang = async () => {
  loading.value = true
  try {
    const response = await axios.get('http://localhost:8000/api/barang')
    barangList.value = response.data.data || []
  } catch (error) {
    console.error('Error fetching data:', error)
  } finally {
    loading.value = false
  }
}

const downloadExcel = () => {
  const data = barangList.value.map((item, index) => ({
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

  // Auto-size columns
  const colWidths = [
    { wch: 5 },   // No
    { wch: 15 },  // Kode Aset
    { wch: 20 },  // Kode Barang
    { wch: 40 },  // Nama Aset
    { wch: 15 },  // Jenis Aset
    { wch: 8 },   // Jumlah
    { wch: 12 },  // Kondisi
    { wch: 25 },  // Lokasi Penyimpanan
    { wch: 20 },  // Penanggung Jawab
    { wch: 15 }   // Tahun Perolehan
  ]
  worksheet['!cols'] = colWidths

  const today = new Date()
  const dateStr = today.toISOString().split('T')[0]
  XLSX.writeFile(workbook, `Inventaris_Barang_${dateStr}.xlsx`)
}

onMounted(() => {
  const userData = localStorage.getItem('user')
  if (userData) {
    user.value = JSON.parse(userData)
  }
  fetchBarang()
})
</script>

<style scoped>
.page-container {
  padding: 24px 0;
}

.welcome-card {
  background: #ffffff;
  border-radius: 12px;
  border: 1px solid #e5e5e5;
  padding: 32px;
  margin-bottom: 24px;
}

.welcome-card h2 {
  margin: 0 0 8px 0;
  font-size: 20px;
  font-weight: 600;
  color: #1d1d1f;
}

.welcome-card p {
  margin: 0;
  font-size: 14px;
  color: #86868b;
}

.data-section {
  background: #ffffff;
  border-radius: 12px;
  border: 1px solid #e5e5e5;
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
  color: #1d1d1f;
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
  background: #86868b;
  cursor: not-allowed;
}

.btn-download svg {
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
  background: #fafafa;
}

.kondisi-badge {
  display: inline-block;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 500;
  white-space: nowrap;
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
</style>
