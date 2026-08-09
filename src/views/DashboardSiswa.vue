<template>
  <div class="dashboard-container">
    <header class="dashboard-header">
      <div class="header-content">
        <h1>Dashboard Siswa</h1>
        <p class="welcome-text">Selamat datang, {{ user?.name }}</p>
      </div>
      <button @click="handleLogout" class="btn-logout">Logout</button>
    </header>

    <div class="dashboard-content">
      <div class="status-section" v-if="pendaftaran">
        <h2>Status Pendaftaran Anda</h2>
        
        <div class="status-card">
          <div class="status-item">
            <span class="label">Nama Lengkap:</span>
            <span class="value">{{ pendaftaran.nama_lengkap }}</span>
          </div>
          <div class="status-item">
            <span class="label">Jurusan Pilihan:</span>
            <span class="value">{{ pendaftaran.jurusan_pilihan }}</span>
          </div>
          <div class="status-item">
            <span class="label">Status:</span>
            <span class="value status" :class="pendaftaran.status">{{ pendaftaran.status }}</span>
          </div>
          <div class="status-item">
            <span class="label">Tanggal Pendaftaran:</span>
            <span class="value">{{ formatDate(pendaftaran.created_at) }}</span>
          </div>
          <div class="status-item" v-if="pendaftaran.status_updated_at">
            <span class="label">Terakhir Diperbarui:</span>
            <span class="value">{{ formatDate(pendaftaran.status_updated_at) }}</span>
          </div>
        </div>

        <div class="action-buttons" v-if="pendaftaran.status === 'baru'">
          <button @click="showEditForm = true" class="btn-edit">Edit Form Pendaftaran</button>
        </div>

        <div class="status-info" v-if="pendaftaran.status !== 'baru'">
          <p>Form Anda sudah diproses. Hubungi admin untuk perubahan data.</p>
        </div>
      </div>

      <div v-else class="no-data">
        <h2>Belum Ada Pendaftaran</h2>
        <p>Anda belum melakukan pendaftaran</p>
        <router-link to="/register" class="btn-register">Isi Form Pendaftaran</router-link>
      </div>

      <!-- Edit Form Modal -->
      <div v-if="showEditForm" class="modal-overlay" @click.self="showEditForm = false">
        <div class="modal-content">
          <div class="modal-header">
            <h3>Edit Pendaftaran</h3>
            <button @click="showEditForm = false" class="btn-close-modal">&times;</button>
          </div>
          
          <form @submit.prevent="handleUpdateForm" class="edit-form">
            <div class="form-row">
              <div class="form-group">
                <label>Nama Lengkap</label>
                <input v-model="formData.nama_lengkap" type="text" required />
              </div>
              <div class="form-group">
                <label>NISN</label>
                <input v-model="formData.nisn" type="text" />
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label>NIK</label>
                <input v-model="formData.nik" type="text" />
              </div>
              <div class="form-group">
                <label>Jenis Kelamin</label>
                <select v-model="formData.jenis_kelamin" required>
                  <option value="">Pilih Jenis Kelamin</option>
                  <option value="Laki-laki">Laki-laki</option>
                  <option value="Perempuan">Perempuan</option>
                </select>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label>Tempat Lahir</label>
                <input v-model="formData.tempat_lahir" type="text" required />
              </div>
              <div class="form-group">
                <label>Tanggal Lahir</label>
                <input v-model="formData.tanggal_lahir" type="date" required />
              </div>
            </div>

            <div class="form-group">
              <label>Alamat</label>
              <textarea v-model="formData.alamat" required></textarea>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label>Asal Sekolah</label>
                <input v-model="formData.asal_sekolah" type="text" required />
              </div>
              <div class="form-group">
                <label>No. HP</label>
                <input v-model="formData.no_hp" type="text" required />
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label>Email</label>
                <input v-model="formData.email" type="email" />
              </div>
              <div class="form-group">
                <label>Jurusan Pilihan</label>
                <select v-model="formData.jurusan_pilihan" required>
                  <option value="">Pilih Jurusan</option>
                  <option value="RPL">RPL</option>
                  <option value="TKJ">TKJ</option>
                  <option value="AKL">AKL</option>
                </select>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label>Nama Orang Tua</label>
                <input v-model="formData.nama_orang_tua" type="text" required />
              </div>
              <div class="form-group">
                <label>No. HP Orang Tua</label>
                <input v-model="formData.no_hp_orang_tua" type="text" />
              </div>
            </div>

            <div class="modal-buttons">
              <button type="submit" class="btn-save" :disabled="loading">
                {{ loading ? 'Menyimpan...' : 'Simpan' }}
              </button>
              <button type="button" @click="showEditForm = false" class="btn-cancel">
                Batal
              </button>
            </div>
          </form>

          <p class="error" v-if="error">{{ error }}</p>
        </div>
      </div>

      <p class="error main-error" v-if="error">{{ error }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuth } from '../composable/useAuth';

const BACKEND = import.meta.env.VITE_BACKEND_URL || "http://localhost:8000";

const router = useRouter();
const { user, logout, getAuthHeader } = useAuth();

const pendaftaran = ref(null);
const showEditForm = ref(false);
const loading = ref(false);
const error = ref('');

const formData = ref({
  nama_lengkap: '',
  nisn: '',
  nik: '',
  tempat_lahir: '',
  tanggal_lahir: '',
  jenis_kelamin: '',
  alamat: '',
  asal_sekolah: '',
  no_hp: '',
  email: '',
  jurusan_pilihan: '',
  nama_orang_tua: '',
  no_hp_orang_tua: ''
});

onMounted(async () => {
  if (!user.value) {
    router.push('/login');
    return;
  }

  await fetchPendaftaran();
});

const fetchPendaftaran = async () => {
  try {
    const response = await fetch(`${BACKEND}/api/pendaftaran/my`, {
      headers: getAuthHeader()
    });

    if (response.status === 404) {
      return;
    }

    if (!response.ok) throw new Error('Gagal mengambil data');

    const data = await response.json();
    pendaftaran.value = data;
    Object.assign(formData.value, data);
  } catch (err) {
    error.value = err.message;
  }
};

const handleUpdateForm = async () => {
  loading.value = true;
  error.value = '';

  try {
    const response = await fetch(`${BACKEND}/api/pendaftaran/${pendaftaran.value.id}`, {
      method: 'PUT',
      headers: getAuthHeader(),
      body: JSON.stringify(formData.value)
    });

    const data = await response.json();

    if (!response.ok) {
      error.value = data.message || 'Gagal memperbarui data';
      return;
    }

    pendaftaran.value = data.data;
    showEditForm.value = false;
    error.value = '';
  } catch (err) {
    error.value = 'Terjadi kesalahan: ' + err.message;
  } finally {
    loading.value = false;
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
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
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
  max-width: 900px;
  margin: 2rem auto;
  padding: 0 1rem;
}

.status-section {
  background: white;
  padding: 2rem;
  border-radius: 10px;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
}

.status-section h2 {
  margin-top: 0;
  margin-bottom: 1.5rem;
  color: #333;
}

.status-card {
  display: grid;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.status-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem;
  background: #f9f9f9;
  border-radius: 6px;
  border-left: 3px solid #667eea;
}

.status-item .label {
  font-weight: 600;
  color: #555;
}

.status-item .value {
  color: #333;
  text-align: right;
}

.status.baru {
  background: #fff3cd;
  color: #856404;
  padding: 0.3rem 0.7rem;
  border-radius: 4px;
  font-weight: 600;
}

.status.diproses {
  background: #cfe2ff;
  color: #084298;
  padding: 0.3rem 0.7rem;
  border-radius: 4px;
  font-weight: 600;
}

.status.diterima {
  background: #d1e7dd;
  color: #0f5132;
  padding: 0.3rem 0.7rem;
  border-radius: 4px;
  font-weight: 600;
}

.status.ditolak {
  background: #f8d7da;
  color: #842029;
  padding: 0.3rem 0.7rem;
  border-radius: 4px;
  font-weight: 600;
}

.action-buttons {
  display: flex;
  gap: 1rem;
}

.btn-edit {
  background: #667eea;
  color: white;
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.2s;
}

.btn-edit:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.status-info {
  background: #e3f2fd;
  color: #1565c0;
  padding: 1rem;
  border-radius: 6px;
  margin-top: 1rem;
  border-left: 3px solid #1565c0;
}

.no-data {
  background: white;
  padding: 3rem 2rem;
  border-radius: 10px;
  text-align: center;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
}

.no-data h2 {
  color: #333;
  margin-top: 0;
}

.no-data p {
  color: #999;
}

.btn-register {
  display: inline-block;
  background: #667eea;
  color: white;
  padding: 0.75rem 1.5rem;
  border-radius: 6px;
  text-decoration: none;
  font-weight: 600;
  transition: all 0.2s;
}

.btn-register:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
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

.edit-form {
  padding: 1.5rem;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
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

.form-group input,
.form-group select,
.form-group textarea {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 6px;
  font-size: 1rem;
  font-family: inherit;
  box-sizing: border-box;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.modal-buttons {
  display: flex;
  gap: 1rem;
  margin-top: 1.5rem;
  padding-top: 1rem;
  border-top: 1px solid #eee;
}

.btn-save,
.btn-cancel {
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

.btn-save:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.btn-cancel {
  background: #ddd;
  color: #333;
}

.btn-cancel:hover {
  background: #ccc;
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
