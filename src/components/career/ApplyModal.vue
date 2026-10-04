<template>
    <div class="modal-backdrop" @click.self="$emit('close')">
      <div class="modal-card">
        <template v-if="!success">
        <button class="modal-close" @click="$emit('close')">
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
            <path d="M12 4L4 12M4 4l8 8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          </svg>
        </button>

        <div class="modal-header">
          <div
            class="modal-logo"
            :style="{ background: job.company_color || '#e8f0eb', color: job.company_text_color || '#3a6450' }"
          >
            {{ job.company_initial || job.company?.charAt(0) || '?' }}
          </div>
          <div>
            <div class="modal-title">{{ job.title }}</div>
            <div class="modal-company">{{ job.company }}</div>
          </div>
        </div>

        <div class="modal-tags">
          <span
            class="tag"
            :style="{ background: typeColor.bg, color: typeColor.fg }"
          >
            {{ job.type }}
          </span>
          <span v-if="job.location" class="tag tag-loc">{{ job.location }}</span>
          <span v-if="job.jurusan" class="tag tag-jur">{{ job.jurusan }}</span>
        </div>

        <p class="modal-desc">{{ job.description }}</p>

        <hr class="divider" />

        <template v-if="!user || !user.role">
          <div class="login-required">
            <i class="fas fa-lock"></i>
            <p>Anda harus login sebagai siswa untuk melamar.</p>
            <a :href="`${BACKEND}/login`" class="btn-login">
              <i class="fas fa-right-to-bracket"></i> Login Sekarang
            </a>
          </div>
        </template>

        <template v-else>
        <h3 class="form-section-title">Formulir Lamaran</h3>

        <form @submit.prevent="submitApplication">
          <div class="form-grid">
            <div class="form-group">
              <label class="form-label">Nama Lengkap</label>
              <input class="input-field" type="text" v-model="formName" />
            </div>
            <div class="form-group">
              <label class="form-label">Email</label>
              <input class="input-field" type="email" v-model="formEmail" />
            </div>
          </div>

          <div class="form-grid">
            <div class="form-group">
              <label class="form-label">NISN</label>
              <input class="input-field" type="text" v-model="formNisn" />
            </div>
            <div class="form-group">
              <label class="form-label">Jurusan</label>
              <select class="input-field" v-model="formJurusan">
                <option value="">Pilih Jurusan</option>
                <option value="RPL">RPL</option>
                <option value="TKJ">TKJ</option>
                <option value="AKL">AKL</option>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">CV (Opsional)</label>
            <div class="file-input-wrap">
              <input
                id="cv-upload"
                class="file-input"
                type="file"
                accept=".pdf,.doc,.docx"
                @change="onFileChange"
              />
              <label for="cv-upload" class="file-label">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                  <path d="M10 1v12M10 13l-4-4M10 13l4-4M2 15h16" stroke="#647067" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>{{ cvFile ? cvFile.name : 'Pilih file (PDF/DOC, max 2MB)' }}</span>
              </label>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Surat Lamaran *</label>
            <textarea
              v-model="coverLetter"
              class="input-textarea"
              rows="6"
              placeholder="Ceritakan mengapa Anda cocok untuk posisi ini..."
              minlength="50"
              maxlength="2000"
              required
            ></textarea>
            <span class="char-count" :style="{ color: coverLetter.length < 50 ? '#991b1b' : '#647067' }">
              {{ coverLetter.length }}/2000
            </span>
          </div>

          <div v-if="error" class="form-error">{{ error }}</div>

          <div class="form-actions">
            <button type="button" class="btn-cancel" @click="$emit('close')">Batal</button>
            <button
              type="submit"
              class="btn-submit"
              :disabled="loading || coverLetter.length < 50"
            >
              {{ loading ? 'Mengirim...' : 'Kirim Lamaran' }}
            </button>
          </div>
        </form>
        </template>
        </template>

        <template v-else>
        <div class="success-content">
          <div class="success-icon">
            <svg width="48" height="48" viewBox="0 0 48 48" fill="none">
              <circle cx="24" cy="24" r="24" fill="#ecfdf5"/>
              <path d="M15 24l6 6 12-12" stroke="#047857" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <h3 class="success-title">Lamaran Berhasil Dikirim!</h3>
          <p class="success-desc">Lowongan <strong>{{ job.title }}</strong> di <strong>{{ job.company }}</strong></p>
          <p class="success-sub">Anda akan dihubungi jika lamaran diterima.</p>
          <button class="success-btn" @click="$emit('close')">Tutup</button>
        </div>
        </template>
      </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useAuthSession } from '@/composable/useAuthSession'
import { getCsrfToken } from '@/services/csrf'

const props = defineProps({
  job: { type: Object, required: true }
})

const emit = defineEmits(['close', 'submitted'])

const { session, BACKEND } = useAuthSession()

const coverLetter = ref('')
const cvFile = ref(null)
const loading = ref(false)
const error = ref('')
const success = ref('')

const user = computed(() => session.value)

const formName = ref(user.value?.name || '')
const formEmail = ref(user.value?.email || '')
const formNisn = ref(user.value?.nisn || '')
const formJurusan = ref(user.value?.jurusan || '')

watch(user, (u) => {
  if (u?.name && !formName.value) formName.value = u.name
  if (u?.email && !formEmail.value) formEmail.value = u.email
  if (u?.nisn && !formNisn.value) formNisn.value = u.nisn
  if (u?.jurusan && !formJurusan.value) formJurusan.value = u.jurusan
})

const typeColor = computed(() => {
  const map = {
    Magang: { bg: 'rgba(58,100,80,0.1)', fg: '#3a6450' },
    Kerja: { bg: 'rgba(79,140,201,0.1)', fg: '#4f8cc9' },
    BKK: { bg: 'rgba(124,92,191,0.1)', fg: '#7c5cbf' }
  }
  return map[props.job.type] || map.Kerja
})

function onFileChange(e) {
  const file = e.target.files?.[0]
  if (!file) return
  if (file.size > 2 * 1024 * 1024) {
    error.value = 'Ukuran file maksimal 2MB.'
    cvFile.value = null
    e.target.value = ''
    return
  }
  error.value = ''
  cvFile.value = file
}

async function submitApplication() {
  error.value = ''
  success.value = ''

  if (coverLetter.value.length < 50) {
    error.value = 'Surat lamaran minimal 50 karakter.'
    return
  }

  loading.value = true
  try {
    const fd = new FormData()
    fd.append('lowongan_id', props.job.id)
    fd.append('cover_letter', coverLetter.value)
    fd.append('nama_lengkap', formName.value)
    fd.append('email', formEmail.value)
    fd.append('nisn', formNisn.value)
    fd.append('jurusan_pilihan', formJurusan.value)
    if (cvFile.value) fd.append('cv', cvFile.value)

    const res = await fetch(`${BACKEND}/lamaran`, {
      method: 'POST',
      credentials: 'include',
      headers: { 'X-CSRF-TOKEN': await getCsrfToken() },
      body: fd
    })

    if (!res.ok) {
      const data = await res.json().catch(() => ({}))
      throw new Error(data.message || 'Gagal mengirim lamaran.')
    }

    success.value = 'Lamaran berhasil dikirim!'
    emit('submitted')
  } catch (e) {
    error.value = e.message || 'Terjadi kesalahan.'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.modal-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(28, 42, 35, 0.5);
  backdrop-filter: blur(4px);
  z-index: 9000;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
}

.modal-card {
  background: #fff;
  border-radius: 24px;
  max-width: 600px;
  max-height: 90vh;
  overflow-y: auto;
  padding: 32px;
  position: relative;
  box-shadow: 0 24px 48px rgba(0, 0, 0, 0.12);
}

.modal-close {
  position: absolute;
  top: 16px;
  right: 16px;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  border: none;
  background: rgba(58, 100, 80, 0.08);
  color: #3a6450;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: background 0.2s;
}

.modal-close:hover {
  background: rgba(58, 100, 80, 0.15);
}

.modal-header {
  display: flex;
  align-items: center;
  gap: 14px;
  margin-bottom: 16px;
}

.modal-logo {
  width: 52px;
  height: 52px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 22px;
  font-weight: 800;
  flex-shrink: 0;
}

.modal-title {
  font-size: 18px;
  font-weight: 800;
  color: #1c2a23;
}

.modal-company {
  font-size: 13px;
  font-weight: 700;
  color: #647067;
}

.modal-tags {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
  margin-bottom: 14px;
}

.tag {
  padding: 5px 12px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 700;
}

.tag-loc {
  background: rgba(232, 101, 60, 0.1);
  color: #e8653c;
}

.tag-jur {
  background: rgba(100, 112, 103, 0.1);
  color: #647067;
}

.modal-desc {
  font-size: 13px;
  color: #647067;
  line-height: 1.7;
  margin: 0;
}

.divider {
  border: none;
  border-top: 1px solid #e5e7e6;
  margin: 20px 0;
}

.form-section-title {
  font-size: 15px;
  font-weight: 800;
  color: #1c2a23;
  margin: 0 0 16px 0;
}

.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 14px;
}

.form-group {
  margin-bottom: 14px;
}

.form-label {
  display: block;
  font-size: 12px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: #647067;
  margin-bottom: 6px;
}

.input-readonly {
  width: 100%;
  padding: 10px 14px;
  border: 1px solid #e5e7e6;
  border-radius: 10px;
  background: #f7f8f7;
  font-size: 13px;
  color: #647067;
  box-sizing: border-box;
}

.input-field {
  width: 100%;
  padding: 10px 14px;
  border: 1.5px solid #e5e7e6;
  border-radius: 10px;
  background: #fff;
  font-size: 13px;
  color: #1c2a23;
  box-sizing: border-box;
  font-family: inherit;
}

.input-field:focus {
  outline: none;
  border-color: #3a6450;
  box-shadow: 0 0 0 3px rgba(58, 100, 80, 0.1);
}

.file-input-wrap {
  position: relative;
  overflow: hidden;
}

.file-input {
  position: absolute;
  opacity: 0;
  width: 100%;
  height: 100%;
}

.file-label {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 14px 18px;
  border: 2px dashed #e5e7e6;
  border-radius: 16px;
  background: #f7f8f7;
  font-size: 13px;
  color: #647067;
  cursor: pointer;
  transition: border-color 0.2s;
}

.file-label:hover {
  border-color: #3a6450;
}

.input-textarea {
  width: 100%;
  padding: 12px 16px;
  border: 1.5px solid #e5e7e6;
  border-radius: 16px;
  font-size: 13px;
  line-height: 1.7;
  resize: vertical;
  font-family: inherit;
  box-sizing: border-box;
}

.input-textarea:focus {
  outline: none;
  border-color: #3a6450;
  box-shadow: 0 0 0 3px rgba(58, 100, 80, 0.1);
}

.char-count {
  display: block;
  text-align: right;
  font-size: 11px;
  margin-top: 4px;
}

.form-error {
  padding: 12px 16px;
  border-radius: 16px;
  background: #fef2f2;
  color: #991b1b;
  font-size: 13px;
  margin-bottom: 14px;
}

.form-success {
  padding: 12px 16px;
  border-radius: 16px;
  background: #ecfdf5;
  color: #047857;
  font-size: 13px;
  margin-bottom: 14px;
}

.success-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 40px 24px;
  text-align: center;
  min-height: 280px;
}

.success-icon {
  margin-bottom: 16px;
}

.success-title {
  font-size: 18px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 8px;
}

.success-desc {
  font-size: 13px;
  color: #647067;
  margin: 0 0 4px;
  line-height: 1.5;
}

.success-sub {
  font-size: 12px;
  color: #9ca3af;
  margin: 0 0 20px;
}

.success-btn {
  padding: 10px 32px;
  border-radius: 16px;
  border: none;
  background: #047857;
  color: #fff;
  font-size: 14px;
  font-weight: 700;
  cursor: pointer;
  transition: background 0.2s;
}

.success-btn:hover {
  background: #065f46;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-top: 16px;
}

.btn-cancel {
  padding: 11px 22px;
  border-radius: 16px;
  border: 1px solid #e5e7e6;
  background: #fff;
  font-size: 14px;
  font-weight: 700;
  color: #647067;
  cursor: pointer;
}

.btn-submit {
  padding: 11px 24px;
  border-radius: 16px;
  border: none;
  background: #3a6450;
  color: #fff;
  font-size: 14px;
  font-weight: 700;
  cursor: pointer;
  transition: opacity 0.2s;
}

.btn-submit:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.login-required {
  text-align: center;
  padding: 24px 0;
}

.login-required i {
  font-size: 32px;
  color: var(--color-text-secondary, #647067);
  opacity: 0.4;
  margin-bottom: 12px;
}

.login-required p {
  font-size: 14px;
  color: var(--color-text-secondary, #647067);
  margin-bottom: 16px;
}

.btn-login {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 11px 24px;
  border-radius: var(--radius, 16px);
  background: var(--primary, #3a6450);
  color: #fff;
  font-size: 14px;
  font-weight: 700;
  text-decoration: none;
}
</style>
