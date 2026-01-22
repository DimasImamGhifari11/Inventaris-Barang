<template>
  <div class="page-container">
    <div class="page-header">
      <button class="btn-back" @click="goBack">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
        Kembali
      </button>
      <h1>Update Data Aset</h1>
    </div>

    <div class="card" v-if="!loading && asset">
      <h2>Form Update</h2>
      <p class="card-description">Edit data aset yang dipilih</p>

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

        <div class="form-actions">
          <button type="button" class="btn btn-secondary" @click="goBack">Batal</button>
          <button type="submit" class="btn btn-primary" :disabled="submitting">
            <span v-if="submitting">Menyimpan...</span>
            <span v-else>Simpan Perubahan</span>
          </button>
        </div>
      </form>
    </div>

    <div class="card" v-if="loading">
      <div class="loading-state">
        <p>Memuat data...</p>
      </div>
    </div>

    <div class="card" v-if="!loading && !asset">
      <div class="empty-state">
        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="10"></circle>
          <line x1="15" y1="9" x2="9" y2="15"></line>
          <line x1="9" y1="9" x2="15" y2="15"></line>
        </svg>
        <p>Data tidak ditemukan</p>
        <button class="btn btn-primary" @click="goBack">Kembali</button>
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
import axios from 'axios'

const API_URL = 'http://localhost:8000/api'
const router = useRouter()
const route = useRoute()

const currentYear = new Date().getFullYear()
const loading = ref(true)
const submitting = ref(false)
const asset = ref(null)

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

const fetchAsset = async () => {
  loading.value = true
  try {
    const id = route.params.id
    const response = await axios.get(`${API_URL}/barang/${id}`)
    if (response.data.success) {
      asset.value = response.data.data
      form.value = {
        kode_aset: asset.value.kode_aset,
        kode_barang: asset.value.kode_barang,
        nama_aset: asset.value.nama_aset,
        jenis_aset: asset.value.jenis_aset,
        jumlah: asset.value.jumlah,
        kondisi: asset.value.kondisi,
        lokasi_penyimpanan: asset.value.lokasi_penyimpanan,
        penanggung_jawab: asset.value.penanggung_jawab,
        tahun_perolehan: asset.value.tahun_perolehan
      }
    }
  } catch (error) {
    console.error('Error fetching asset:', error)
    asset.value = null
  } finally {
    loading.value = false
  }
}

const submitForm = async () => {
  submitting.value = true
  try {
    const id = route.params.id
    await axios.put(`${API_URL}/barang/${id}`, form.value)
    showNotification('Data berhasil diupdate')
    setTimeout(() => {
      const page = route.query.page || 1
      router.push(`/dashboard/update-data?page=${page}`)
    }, 1500)
  } catch (error) {
    showNotification(error.response?.data?.message || 'Gagal mengupdate data', 'error')
  } finally {
    submitting.value = false
  }
}

const goBack = () => {
  const page = route.query.page || 1
  router.push(`/dashboard/update-data?page=${page}`)
}

onMounted(() => {
  fetchAsset()
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
  color: #1d1d1f;
}

.btn-back {
  display: flex;
  align-items: center;
  gap: 4px;
  padding: 8px 12px;
  background: #f5f5f7;
  border: 1px solid #e5e5e5;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  color: #1d1d1f;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-back:hover {
  background: #e5e5e5;
}

.btn-back:active {
  transform: scale(0.98);
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
  margin: 0 0 24px 0;
  font-size: 14px;
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

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  margin-top: 8px;
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

/* States */
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

/* Responsive - Tablet */
@media (max-width: 768px) {
  .page-container {
    padding: 16px 0;
  }

  .page-header {
    flex-wrap: wrap;
    gap: 12px;
  }

  .page-header h1 {
    font-size: 20px;
  }

  .card {
    padding: 20px;
  }

  .form-row {
    grid-template-columns: 1fr;
  }

  .form-actions {
    flex-direction: column;
  }

  .form-actions .btn {
    width: 100%;
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
    width: 100%;
  }

  .btn-back {
    font-size: 13px;
    padding: 6px 10px;
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
    margin-bottom: 20px;
  }

  .form-group input,
  .form-group select {
    padding: 12px;
    font-size: 16px;
  }

  .btn {
    padding: 12px 20px;
  }

  .empty-state {
    padding: 32px 16px;
  }

  .empty-state svg {
    width: 36px;
    height: 36px;
  }

  .notification {
    padding: 12px 16px;
    font-size: 13px;
  }
}
</style>
