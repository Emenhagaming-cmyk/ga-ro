<template>
  <div class="dashboard-container">
    <header class="dashboard-header">
      <div class="header-content">
        <h1>Dashboard Admin</h1>
        <p class="welcome-text">Selamat datang, {{ user?.name }}</p>
      </div>
      <button @click="handleLogout" class="btn-logout">Logout</button>
    </header>

    <div class="dashboard-content">
      <div class="stats-section">
        <div class="stat-card">
          <span class="stat-label">Total Pendaftar</span>
          <span class="stat-value">{{ stats.total }}</span>
        </div>
        <div class="stat-card">
          <span class="stat-label">Status Baru</span>
          <span class="stat-value">{{ stats.baru }}</span>
        </div>
        <div class="stat-card">
          <span class="stat-label">Status Diproses</span>
          <span class="stat-value">{{ stats.diproses }}</span>
        </div>
        <div class="stat-card">
          <span class="stat-label">Status Diterima</span>
          <span class="stat-value">{{ stats.diterima }}</span>
        </div>
        <div class="stat-card">
          <span class="stat-label">Status Ditolak</span>
          <span class="stat-value">{{ stats.ditolak }}</span>
        </div>
      </div>

      <div class="data-section">
        <h2>Daftar Pendaftar</h2>
        
        <div class="filter-section">
          <input 
            v-model="searchQuery" 
            type="text" 
            placeholder="Cari nama atau email..."
            class="search-input"
          />
          <select v-model="filterStatus" class="filter-select">
            <option value="">Semua Status</option>
            <option value="baru">Baru</option>
            <option value="diproses">Diproses</option>
            <option value="diterima">Diterima</option>
            <option value="ditolak">Ditolak</option>
          </select>
        </div>

        <div class="table-responsive">
          <table class="data-table">
            <thead>
              <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Jurusan</th>
                <th>Status</th>
                <th>Tanggal Daftar</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, idx) in filteredData" :key="item.id">
                <td>{{ idx + 1 }}</td>
                <td>{{ item.nama_lengkap }}</td>
                <td>{{ item.email }}</td>
                <td>{{ item.jurusan_pilihan }}</td>
                <td>
                  <span class="status-badge" :class="item.status">
                    {{ item.status }}
                  </span>
                </td>
                <td>{{ formatDate(item.created_at) }}</td>
                <td class="action-cell">
                  <button @click="openDetail(item)" class="btn-small btn-view">Lihat</button>
                  <button @click="openStatusUpdate(item)" class="btn-small btn-edit">Update</button>
                  <button @click="deleteItem(item.id)" class="btn-small btn-delete">Hapus</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <p v-if="filteredData.length === 0" class="no-data">Tidak ada data</p>
      </div>

      <!-- Detail Modal -->
      <div v-if="showDetailModal" class="modal-overlay" @click.self="showDetailModal = false">
        <div class="modal-content">
          <div class="modal-header">
            <h3>Detail Pendaftaran</h3>
            <button @click="showDetailModal = false" class="btn-close-modal">&times;</button>
          </div>
          <div class="detail-content">
            <div class="detail-item">
              <span class="label">Nama Lengkap:</span>
              <span class="value">{{ selectedItem?.nama_lengkap }}</span>
            </div>
            <div class="detail-item">
              <span class="label">Email:</span>
              <span class="value">{{ selectedItem?.email }}</span>
            </div>
            <div class="detail-item">
              <span class="label">Tempat, Tanggal Lahir:</span>
              <span class="value">{{ selectedItem?.tempat_lahir }}, {{ selectedItem?.tanggal_lahir }}</span>
            </div>
            <div class="detail-item">
              <span class="label">Jenis Kelamin:</span>
              <span class="value">{{ selectedItem?.jenis_kelamin }}</span>
            </div>
            <div class="detail-item">
              <span class="label">Alamat:</span>
              <span class="value">{{ selectedItem?.alamat }}</span>
            </div>
            <div class="detail-item">
              <span class="label">Asal Sekolah:</span>
              <span class="value">{{ selectedItem?.asal_sekolah }}</span>
            </div>
            <div class="detail-item">
              <span class="label">No. HP:</span>
              <span class="value">{{ selectedItem?.no_hp }}</span>
            </div>
            <div class="detail-item">
              <span class="label">Jurusan Pilihan:</span>
              <span class="value">{{ selectedItem?.jurusan_pilihan }}</span>
            </div>
            <div class="detail-item">
              <span class="label">Nama Orang Tua:</span>
              <span class="value">{{ selectedItem?.nama_orang_tua }}</span>
            </div>
            <div class="detail-item">
              <span class="label">No. HP Orang Tua:</span>
              <span class="value">{{ selectedItem?.no_hp_orang_tua }}</span>
            </div>
            <div class="detail-item">
              <span class="label">Status:</span>
              <span class="value status" :class="selectedItem?.status">{{ selectedItem?.status }}</span>
            </div>
          </div>
          <button @click="showDetailModal = false" class="btn-close">Tutup</button>
        </div>
      </div>

      <!-- Status Update Modal -->
      <div v-if="showStatusModal" class="modal-overlay" @click.self="showStatusModal = false">
        <div class="modal-content">
          <div class="modal-header">
            <h3>Update Status - {{ selectedItem?.nama_lengkap }}</h3>
            <button @click="showStatusModal = false" class="btn-close-modal">&times;</button>
          </div>
          
          <div style="padding: 1.5rem;">
            <div class="form-group">
              <label>Status Baru</label>
              <select v-model="newStatus" class="form-select">
                <option value="">Pilih Status</option>
                <option value="baru">Baru</option>
                <option value="diproses">Diproses</option>
                <option value="diterima">Diterima</option>
                <option value="ditolak">Ditolak</option>
              </select>
            </div>

            <div class="modal-buttons">
              <button @click="handleStatusUpdate" class="btn-save" :disabled="loading">
                {{ loading ? 'Menyimpan...' : 'Simpan' }}
              </button>
              <button @click="showStatusModal = false" class="btn-cancel">Batal</button>
            </div>

            <p class="error" v-if="error">{{ error }}</p>
          </div>
        </div>
      </div>

      <p class="error main-error" v-if="error">{{ error }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuth } from '../composable/useAuth';

const BACKEND = import.meta.env.VITE_BACKEND_URL || "http://localhost:8000";

const router = useRouter();
const { user, logout, getAuthHeader } = useAuth();

const pendaftarans = ref([]);
const searchQuery = ref('');
const filterStatus = ref('');
const showDetailModal = ref(false);
const showStatusModal = ref(false);
const selectedItem = ref(null);
const newStatus = ref('');
const loading = ref(false);
const error = ref('');

const stats = computed(() => {
  const items = pendaftarans.value;
  return {
    total: items.length,
    baru: items.filter(p => p.status === 'baru').length,
    diproses: items.filter(p => p.status === 'diproses').length,
    diterima: items.filter(p => p.status === 'diterima').length,
    ditolak: items.filter(p => p.status === 'ditolak').length,
  };
});

const filteredData = computed(() => {
  return pendaftarans.value.filter(item => {
    const matchSearch = !searchQuery.value || 
      item.nama_lengkap.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      item.email.toLowerCase().includes(searchQuery.value.toLowerCase());
    
    const matchStatus = !filterStatus.value || item.status === filterStatus.value;
    
    return matchSearch && matchStatus;
  });
});

onMounted(async () => {
  if (!user.value || user.value.role !== 'admin') {
    router.push('/login');
    return;
  }

  await fetchPendaftarans();
});

const fetchPendaftarans = async () => {
  try {
    const response = await fetch(`${BACKEND}/api/pendaftaran`, {
      headers: getAuthHeader()
    });

    if (!response.ok) throw new Error('Gagal mengambil data');

    const data = await response.json();
    pendaftarans.value = data.data || [];
  } catch (err) {
    error.value = err.message;
  }
};

const openDetail = (item) => {
  selectedItem.value = item;
  showDetailModal.value = true;
};

const openStatusUpdate = (item) => {
  selectedItem.value = item;
  newStatus.value = item.status;
  showStatusModal.value = true;
};

const handleStatusUpdate = async () => {
  loading.value = true;
  error.value = '';

  try {
    const response = await fetch(`${BACKEND}/api/pendaftaran/${selectedItem.value.id}/status`, {
      method: 'PUT',
      headers: getAuthHeader(),
      body: JSON.stringify({ status: newStatus.value })
    });

    const data = await response.json();

    if (!response.ok) {
      error.value = data.message || 'Gagal memperbarui status';
      return;
    }

    const idx = pendaftarans.value.findIndex(p => p.id === selectedItem.value.id);
    if (idx !== -1) {
      pendaftarans.value[idx] = data.data;
    }

    showStatusModal.value = false;
  } catch (err) {
    error.value = 'Terjadi kesalahan: ' + err.message;
  } finally {
    loading.value = false;
  }
};

const deleteItem = async (id) => {
  if (!confirm('Yakin ingin menghapus data ini?')) return;

  try {
    const response = await fetch(`${BACKEND}/api/pendaftaran/${id}`, {
      method: 'DELETE',
      headers: getAuthHeader()
    });

    if (!response.ok) throw new Error('Gagal menghapus data');

    pendaftarans.value = pendaftarans.value.filter(p => p.id !== id);
  } catch (err) {
    error.value = err.message;
  }
};

const handleLogout = async () => {
  try {
    await fetch(`${BACKEND}/api/logout`, {
      method: 'POST',
      headers: getAuthHeader()
    });
  } catch (err) {
    console.error(err);
  }

  logout();
  router.push('/login');
};

const formatDate = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};
</script>

<style scoped>
.dashboard-container {
  min-height: 100vh;
  background: #f5f7fa;
}

.dashboard-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.header-content h1 {
  margin: 0 0 0.5rem;
  font-size: 1.8rem;
}

.welcome-text {
  margin: 0;
  opacity: 0.9;
  font-size: 0.95rem;
}

.btn-logout {
  background: rgba(255, 255, 255, 0.2);
  color: white;
  border: 1px solid white;
  padding: 0.6rem 1.2rem;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.2s;
}

.btn-logout:hover {
  background: rgba(255, 255, 255, 0.3);
  transform: translateY(-2px);
}

.dashboard-content {
  max-width: 1200px;
  margin: 2rem auto;
  padding: 0 1rem;
}

.stats-section {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 1rem;
  margin-bottom: 2rem;
}

.stat-card {
  background: white;
  padding: 1.5rem;
  border-radius: 10px;
  text-align: center;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.stat-label {
  font-size: 0.9rem;
  color: #666;
  font-weight: 500;
}

.stat-value {
  font-size: 2rem;
  font-weight: bold;
  color: #667eea;
}

.data-section {
  background: white;
  padding: 2rem;
  border-radius: 10px;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
}

.data-section h2 {
  margin-top: 0;
  margin-bottom: 1.5rem;
  color: #333;
}

.filter-section {
  display: flex;
  gap: 1rem;
  margin-bottom: 1.5rem;
  flex-wrap: wrap;
}

.search-input,
.filter-select {
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 6px;
  font-size: 1rem;
}

.search-input {
  flex: 1;
  min-width: 200px;
}

.search-input:focus,
.filter-select:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.table-responsive {
  overflow-x: auto;
}

.data-table {
  width: 100%;
  border-collapse: collapse;
}

.data-table thead {
  background: #f9f9f9;
}

.data-table th,
.data-table td {
  padding: 1rem;
  text-align: left;
  border-bottom: 1px solid #eee;
}

.data-table th {
  font-weight: 600;
  color: #333;
}

.data-table tbody tr:hover {
  background: #f5f5f5;
}

.status-badge {
  padding: 0.3rem 0.7rem;
  border-radius: 4px;
  font-size: 0.85rem;
  font-weight: 600;
  display: inline-block;
}

.status-badge.baru {
  background: #fff3cd;
  color: #856404;
}

.status-badge.diproses {
  background: #cfe2ff;
  color: #084298;
}

.status-badge.diterima {
  background: #d1e7dd;
  color: #0f5132;
}

.status-badge.ditolak {
  background: #f8d7da;
  color: #842029;
}

.action-cell {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.btn-small {
  padding: 0.4rem 0.8rem;
  border: none;
  border-radius: 4px;
  font-size: 0.85rem;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.2s;
}

.btn-small:hover {
  transform: translateY(-2px);
}

.btn-view {
  background: #667eea;
  color: white;
}

.btn-edit {
  background: #f39c12;
  color: white;
}

.btn-delete {
  background: #e74c3c;
  color: white;
}

.no-data {
  text-align: center;
  color: #999;
  padding: 2rem;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
  padding: 1rem;
}

.modal-content {
  background: white;
  border-radius: 10px;
  max-width: 600px;
  width: 100%;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid #eee;
}

.modal-header h3 {
  margin: 0;
}

.btn-close-modal {
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
  color: #999;
}

.detail-content {
  padding: 1.5rem;
  display: grid;
  gap: 1rem;
}

.detail-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem;
  background: #f9f9f9;
  border-radius: 6px;
}

.detail-item .label {
  font-weight: 600;
  color: #555;
}

.detail-item .value {
  color: #333;
  text-align: right;
}

.form-group {
  margin-bottom: 1rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 600;
  color: #555;
}

.form-select {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 6px;
  font-size: 1rem;
}

.form-select:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.modal-buttons {
  display: flex;
  gap: 1rem;
}

.btn-save,
.btn-cancel,
.btn-close {
  flex: 1;
  padding: 0.75rem;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.2s;
}

.btn-save {
  background: #667eea;
  color: white;
}

.btn-save:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-cancel {
  background: #ddd;
  color: #333;
}

.btn-close {
  background: #667eea;
  color: white;
}

.btn-save:hover:not(:disabled),
.btn-cancel:hover,
.btn-close:hover {
  transform: translateY(-2px);
}

.error {
  color: #e74c3c;
  background: #fadbd8;
  padding: 1rem;
  border-radius: 6px;
  border-left: 3px solid #e74c3c;
  margin-top: 1rem;
}

.main-error {
  margin-top: 2rem;
}
</style>
