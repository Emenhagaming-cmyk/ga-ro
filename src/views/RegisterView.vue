<template>
  <div class="register-container">
    <div class="register-wrapper">
      <!-- Sidebar Progress -->
      <aside class="progress-sidebar">
        <div class="progress-header">
          <h2>Pendaftaran</h2>
          <p class="step-counter">Step {{ currentStep }} of 5</p>
        </div>

        <div class="progress-steps">
          <div 
            v-for="(step, idx) in steps" 
            :key="idx"
            class="step-item"
            :class="{ 
              active: currentStep === idx + 1,
              completed: currentStep > idx + 1
            }"
          >
            <div class="step-number">
              <span v-if="currentStep > idx + 1" class="checkmark">?</span>
              <span v-else>{{ idx + 1 }}</span>
            </div>
            <div class="step-content">
              <p class="step-title">{{ step.title }}</p>
              <p class="step-desc">{{ step.desc }}</p>
            </div>
          </div>
        </div>

        <div class="progress-bar">
          <div class="progress-fill" :style="{ width: progressPercent + '%' }"></div>
        </div>
      </aside>

      <!-- Main Form -->
      <main class="form-container">
        <div class="form-content">
          <!-- Step 1: Data Diri -->
          <div v-show="currentStep === 1" class="form-step">
            <h3>Data Diri Calon Siswa</h3>
            <div class="form-group">
              <label>Nama Lengkap *</label>
              <input v-model="formData.nama_lengkap" type="text" placeholder="Masukkan nama lengkap" required />
            </div>

            <div class="form-row">
              <div class="form-group">
                <label>NISN *</label>
                <input v-model="formData.nisn" type="text" placeholder="Nomor Induk Siswa Nasional" required />
              </div>
              <div class="form-group">
                <label>NIK *</label>
                <input v-model="formData.nik" type="text" placeholder="Nomor Induk Kependudukan" required />
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label>Tempat Lahir *</label>
                <input v-model="formData.tempat_lahir" type="text" placeholder="Kota/Kabupaten" required />
              </div>
              <div class="form-group">
                <label>Tanggal Lahir *</label>
                <input v-model="formData.tanggal_lahir" type="date" required />
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label>Jenis Kelamin *</label>
                <select v-model="formData.jenis_kelamin" required>
                  <option value="">Pilih Jenis Kelamin</option>
                  <option value="Laki-laki">Laki-laki</option>
                  <option value="Perempuan">Perempuan</option>
                </select>
              </div>
              <div class="form-group">
                <label>Email *</label>
                <input v-model="formData.email" type="email" placeholder="email@example.com" required />
              </div>
            </div>

            <div class="form-group">
              <label>Alamat Lengkap *</label>
              <textarea v-model="formData.alamat" placeholder="Masukkan alamat lengkap" required></textarea>
            </div>

            <div class="form-group">
              <label>No. HP *</label>
              <input v-model="formData.no_hp" type="tel" placeholder="08xxxxxxxxxx" required />
            </div>
          </div>

          <!-- Step 2: Data Sekolah -->
          <div v-show="currentStep === 2" class="form-step">
            <h3>Data Sekolah Asal</h3>
            
            <div class="form-group">
              <label>Asal Sekolah *</label>
              <input v-model="formData.asal_sekolah" type="text" placeholder="Nama lengkap sekolah" required />
            </div>

            <div class="form-row">
              <div class="form-group">
                <label>Jurusan Pilihan 1 *</label>
                <select v-model="formData.jurusan_pilihan" required>
                  <option value="">Pilih Jurusan</option>
                  <option value="RPL">Rekayasa Perangkat Lunak (RPL)</option>
                  <option value="TKJ">Teknik Komputer Jaringan (TKJ)</option>
                  <option value="AKL">Akuntansi (AKL)</option>
                </select>
              </div>
            </div>

            <div class="info-box">
              <p>Pilih jurusan sesuai dengan minat dan kemampuan Anda. Pilihan ini akan menjadi pertimbangan penerimaan.</p>
            </div>
          </div>

          <!-- Step 3: Data Orang Tua -->
          <div v-show="currentStep === 3" class="form-step">
            <h3>Data Orang Tua/Wali</h3>
            
            <div class="form-group">
              <label>Nama Orang Tua/Wali *</label>
              <input v-model="formData.nama_orang_tua" type="text" placeholder="Nama lengkap orang tua" required />
            </div>

            <div class="form-group">
              <label>No. HP Orang Tua/Wali *</label>
              <input v-model="formData.no_hp_orang_tua" type="tel" placeholder="08xxxxxxxxxx" required />
            </div>

            <div class="info-box">
              <p>Data orang tua/wali diperlukan untuk keperluan administrasi dan komunikasi penting.</p>
            </div>
          </div>

          <!-- Step 4: Unggah Berkas -->
          <div v-show="currentStep === 4" class="form-step">
            <h3>Unggah Dokumen Pendukung</h3>
            
            <div class="upload-section">
              <div class="upload-box">
                <div class="upload-icon">??</div>
                <p class="upload-text">Siapkan dokumen berikut (opsional):</p>
                <ul class="upload-list">
                  <li>Fotokopi Kartu Keluarga (KK)</li>
                  <li>Fotokopi Akta Kelahiran</li>
                  <li>Fotokopi Raport Terakhir</li>
                  <li>Foto 3x4 (warna)</li>
                </ul>
                <p class="upload-note">Dokumen akan diminta jika pendaftaran diterima.</p>
              </div>
            </div>

            <div class="info-box info-warning">
              <p>Pastikan semua data yang Anda isi sudah benar sebelum melanjutkan ke step berikutnya.</p>
            </div>
          </div>

          <!-- Step 5: Konfirmasi -->
          <div v-show="currentStep === 5" class="form-step">
            <h3>Konfirmasi & Setujui Syarat</h3>
            
            <div class="confirmation-box">
              <h4>Ringkasan Data Pendaftaran</h4>
              <div class="confirmation-item">
                <span class="label">Nama Lengkap:</span>
                <span class="value">{{ formData.nama_lengkap }}</span>
              </div>
              <div class="confirmation-item">
                <span class="label">Email:</span>
                <span class="value">{{ formData.email }}</span>
              </div>
              <div class="confirmation-item">
                <span class="label">Asal Sekolah:</span>
                <span class="value">{{ formData.asal_sekolah }}</span>
              </div>
              <div class="confirmation-item">
                <span class="label">Jurusan Pilihan:</span>
                <span class="value">{{ formData.jurusan_pilihan }}</span>
              </div>
              <div class="confirmation-item">
                <span class="label">Orang Tua:</span>
                <span class="value">{{ formData.nama_orang_tua }}</span>
              </div>
            </div>

            <div class="terms-section">
              <label class="checkbox-label">
                <input v-model="agreedToTerms" type="checkbox" />
                <span>Saya setuju dengan syarat dan ketentuan pendaftaran</span>
              </label>
            </div>

            <div class="info-box">
              <p>Dengan mengklik tombol "Selesai", data Anda akan disimpan dan Anda bisa melihat status pendaftaran di dashboard.</p>
            </div>
          </div>
        </div>

        <!-- Buttons -->
        <div class="form-actions">
          <button v-if="currentStep > 1" @click="prevStep" class="btn-secondary">
            Sebelumnya
          </button>
          <button 
            v-if="currentStep < 5" 
            @click="nextStep" 
            class="btn-primary"
            :disabled="!isCurrentStepValid"
          >
            Selanjutnya
          </button>
          <button 
            v-if="currentStep === 5" 
            @click="handleSubmit" 
            class="btn-primary btn-submit"
            :disabled="!agreedToTerms || loading"
          >
            {{ loading ? 'Memproses...' : 'Selesai' }}
          </button>
        </div>

        <p class="error" v-if="error">{{ error }}</p>
        <p class="success" v-if="success">{{ success }}</p>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();

const currentStep = ref(1);
const loading = ref(false);
const error = ref('');
const success = ref('');
const agreedToTerms = ref(false);

const formData = ref({
  nama_lengkap: '',
  nisn: '',
  nik: '',
  tempat_lahir: '',
  tanggal_lahir: '',
  jenis_kelamin: '',
  email: '',
  alamat: '',
  no_hp: '',
  asal_sekolah: '',
  jurusan_pilihan: '',
  nama_orang_tua: '',
  no_hp_orang_tua: ''
});

const steps = [
  { title: 'Data Diri', desc: 'Informasi pribadi' },
  { title: 'Data Sekolah', desc: 'Asal sekolah & jurusan' },
  { title: 'Data Orang Tua', desc: 'Informasi orang tua' },
  { title: 'Unggah Berkas', desc: 'Dokumen pendukung' },
  { title: 'Konfirmasi', desc: 'Review & submit' }
];

const progressPercent = computed(() => {
  return (currentStep.value / 5) * 100;
});

const isCurrentStepValid = computed(() => {
  if (currentStep.value === 1) {
    return (
      formData.value.nama_lengkap &&
      formData.value.nisn &&
      formData.value.nik &&
      formData.value.tempat_lahir &&
      formData.value.tanggal_lahir &&
      formData.value.jenis_kelamin &&
      formData.value.email &&
      formData.value.alamat &&
      formData.value.no_hp
    );
  } else if (currentStep.value === 2) {
    return formData.value.asal_sekolah && formData.value.jurusan_pilihan;
  } else if (currentStep.value === 3) {
    return formData.value.nama_orang_tua && formData.value.no_hp_orang_tua;
  } else if (currentStep.value === 4) {
    return true;
  }
  return true;
});

const nextStep = () => {
  if (isCurrentStepValid.value && currentStep.value < 5) {
    currentStep.value++;
    error.value = '';
  }
};

const prevStep = () => {
  if (currentStep.value > 1) {
    currentStep.value--;
    error.value = '';
  }
};

const handleSubmit = async () => {
  if (!agreedToTerms.value) {
    error.value = 'Silakan setuju dengan syarat dan ketentuan';
    return;
  }

  loading.value = true;
  error.value = '';

  try {
    const response = await fetch('http://localhost:8000/api/register', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(formData.value)
    });

    const data = await response.json();

    if (!response.ok) {
      error.value = data.message || 'Registrasi gagal';
      return;
    }

    // Store token & user
    localStorage.setItem('token', data.token);
    localStorage.setItem('user', JSON.stringify(data.user));

    success.value = 'Pendaftaran berhasil! Redirecting...';
    
    setTimeout(() => {
      router.push('/dashboard-siswa');
    }, 1500);
  } catch (err) {
    error.value = 'Terjadi kesalahan: ' + err.message;
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
.register-container {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem;
}

.register-wrapper {
  display: grid;
  grid-template-columns: 350px 1fr;
  gap: 2rem;
  max-width: 1200px;
  width: 100%;
}

/* Sidebar */
.progress-sidebar {
  background: white;
  border-radius: 12px;
  padding: 2rem;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  height: fit-content;
  position: sticky;
  top: 2rem;
}

.progress-header h2 {
  margin: 0 0 0.5rem;
  font-size: 1.5rem;
  color: #333;
}

.step-counter {
  margin: 0;
  color: #999;
  font-size: 0.9rem;
}

.progress-steps {
  margin: 2rem 0;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.step-item {
  display: flex;
  gap: 1rem;
  align-items: flex-start;
  opacity: 0.5;
  transition: all 0.3s ease;
}

.step-item.active {
  opacity: 1;
}

.step-item.completed {
  opacity: 1;
}

.step-number {
  min-width: 40px;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: #f0f0f0;
  color: #999;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  transition: all 0.3s ease;
  flex-shrink: 0;
}

.step-item.active .step-number {
  background: #667eea;
  color: white;
}

.step-item.completed .step-number {
  background: #4caf50;
  color: white;
}

.checkmark {
  font-size: 1.2rem;
}

.step-content {
  flex: 1;
}

.step-title {
  margin: 0;
  font-weight: 600;
  color: #333;
  font-size: 0.95rem;
}

.step-desc {
  margin: 0.25rem 0 0;
  color: #999;
  font-size: 0.85rem;
}

.progress-bar {
  margin-top: 2rem;
  height: 4px;
  background: #eee;
  border-radius: 2px;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  background: linear-gradient(90deg, #667eea, #764ba2);
  transition: width 0.3s ease;
}

/* Form Container */
.form-container {
  background: white;
  border-radius: 12px;
  padding: 2.5rem;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  min-height: 600px;
  display: flex;
  flex-direction: column;
}

.form-content {
  flex: 1;
}

.form-step h3 {
  margin: 0 0 2rem;
  font-size: 1.5rem;
  color: #333;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 600;
  color: #555;
  font-size: 0.95rem;
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
  transition: all 0.3s ease;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-group textarea {
  resize: vertical;
  min-height: 100px;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
}

.info-box {
  background: #f0f4ff;
  border-left: 3px solid #667eea;
  padding: 1rem;
  border-radius: 6px;
  margin: 1.5rem 0;
}

.info-box p {
  margin: 0;
  color: #555;
  font-size: 0.9rem;
  line-height: 1.6;
}

.info-box.info-warning {
  background: #fff3cd;
  border-left-color: #ffc107;
}

/* Upload Section */
.upload-section {
  margin: 2rem 0;
}

.upload-box {
  background: #f9f9f9;
  border: 2px dashed #ddd;
  border-radius: 8px;
  padding: 2rem;
  text-align: center;
}

.upload-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
}

.upload-text {
  margin: 1rem 0;
  font-weight: 600;
  color: #333;
}

.upload-list {
  text-align: left;
  display: inline-block;
  margin: 1rem 0;
  padding-left: 1.5rem;
  color: #666;
}

.upload-list li {
  margin: 0.5rem 0;
}

.upload-note {
  margin: 1rem 0 0;
  font-size: 0.85rem;
  color: #999;
}

/* Confirmation */
.confirmation-box {
  background: #f9f9f9;
  border: 1px solid #ddd;
  border-radius: 8px;
  padding: 1.5rem;
  margin: 1.5rem 0;
}

.confirmation-box h4 {
  margin: 0 0 1rem;
  font-size: 1rem;
  color: #333;
}

.confirmation-item {
  display: flex;
  justify-content: space-between;
  padding: 0.75rem 0;
  border-bottom: 1px solid #eee;
}

.confirmation-item:last-child {
  border-bottom: none;
}

.confirmation-item .label {
  font-weight: 600;
  color: #666;
}

.confirmation-item .value {
  color: #333;
  text-align: right;
}

/* Terms */
.terms-section {
  margin: 2rem 0;
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  cursor: pointer;
  font-size: 0.95rem;
  color: #555;
}

.checkbox-label input {
  width: 20px;
  height: 20px;
  cursor: pointer;
}

/* Buttons */
.form-actions {
  display: flex;
  gap: 1rem;
  margin-top: 2rem;
  padding-top: 1.5rem;
  border-top: 1px solid #eee;
}

.btn-primary,
.btn-secondary {
  flex: 1;
  padding: 0.75rem;
  border: none;
  border-radius: 6px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 1rem;
}

.btn-primary {
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
}

.btn-primary:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 16px rgba(102, 126, 234, 0.3);
}

.btn-primary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-secondary {
  background: #f0f0f0;
  color: #333;
}

.btn-secondary:hover {
  background: #e0e0e0;
}

.btn-submit {
  flex: 2;
}

.error {
  color: #e74c3c;
  background: #fadbd8;
  padding: 1rem;
  border-radius: 6px;
  margin-top: 1rem;
  border-left: 3px solid #e74c3c;
}

.success {
  color: #27ae60;
  background: #d5f4e6;
  padding: 1rem;
  border-radius: 6px;
  margin-top: 1rem;
  border-left: 3px solid #27ae60;
}

/* Responsive */
@media (max-width: 900px) {
  .register-wrapper {
    grid-template-columns: 1fr;
  }

  .progress-sidebar {
    position: static;
  }

  .form-row {
    grid-template-columns: 1fr;
  }

  .form-actions {
    flex-direction: column;
  }

  .btn-submit {
    flex: 1;
  }
}

@media (max-width: 600px) {
  .register-container {
    padding: 1rem;
  }

  .form-container {
    padding: 1.5rem;
  }

  .progress-sidebar {
    padding: 1.5rem;
  }
}
</style>
