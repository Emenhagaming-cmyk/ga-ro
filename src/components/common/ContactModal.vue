<template>
  <Teleport to="body">
    <Transition name="cm">
      <div v-if="modelValue" class="cm-backdrop" @click.self="close">
        <div class="cm-card">
          <svg v-if="!success" class="cm-wave-top" viewBox="0 0 480 80" fill="none" preserveAspectRatio="none">
            <path d="M0 0h480v40c-60 20-140 45-240 30S60 35 0 55V0z" fill="#90cdf4" opacity="0.45"/>
            <path d="M0 0h480v28c-80 22-180 50-260 35S40 20 0 42V0z" fill="#a8d8ff" opacity="0.35"/>
          </svg>
          <svg v-else class="cm-wave-bottom" viewBox="0 0 480 80" fill="none" preserveAspectRatio="none">
            <path d="M0 80H480V40c-60-20-140-45-240-30S60 45 0 25V80z" fill="#90cdf4" opacity="0.45"/>
            <path d="M0 80H480V52c-80-22-180-50-260-35S40 60 0 38V80z" fill="#a8d8ff" opacity="0.35"/>
          </svg>
          <button class="cm-close" @click="close">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
              <path d="M12 4L4 12M4 4l8 8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
          </button>

          <template v-if="!success">
            <h2 class="cm-title">Hubungi Kami</h2>
            <div class="cm-info">
              <span>smkbahrululum.sch.id</span>
              <span class="cm-info-sep">|</span>
              <span>+62 812-3456-7890</span>
            </div>

            <form @submit.prevent="submit" class="cm-form">
              <div class="cm-field">
                <input v-model="form.name" type="text" class="cm-input" required placeholder="Nama Lengkap" />
              </div>
              <div class="cm-field">
                <input v-model="form.email" type="email" class="cm-input" required placeholder="Email" />
              </div>
              <div class="cm-row">
                <select v-model="form.code" class="cm-select">
                  <option value="+62">+62</option>
                </select>
                <input v-model="form.phone" type="tel" class="cm-input cm-input-phone" placeholder="No. Telepon" />
              </div>
              <div class="cm-field">
                <textarea v-model="form.message" class="cm-textarea" rows="4" required placeholder="Pesan Anda..."></textarea>
              </div>

              <button type="submit" class="cm-submit" :disabled="sending">
                {{ sending ? 'Mengirim...' : 'Kirim' }}
              </button>
            </form>
          </template>

          <template v-else>
            <div class="cm-success">
              <div class="cm-success-icon">
                <svg width="56" height="56" viewBox="0 0 56 56" fill="none">
                  <circle cx="28" cy="28" r="28" fill="#1c2a23"/>
                  <path d="M16 22c0-2.2 1.8-4 4-4h16c2.2 0 4 1.8 4 4v10c0 2.2-1.8 4-4 4H24l-5 4v-4h-1c-1.1 0-2-.9-2-2V22z" fill="#fff"/>
                  <path d="M23 27l3 3 6-6" stroke="#1c2a23" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
              <h3 class="cm-success-title">Terima Kasih!!</h3>
              <p class="cm-success-desc">Pesan Anda telah kami terima. Tim SMK Bahrul Ulum akan segera menghubungi Anda.</p>
              <button class="cm-success-btn" @click="close">Kembali</button>
            </div>
          </template>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({ modelValue: Boolean })
const emit = defineEmits(['update:modelValue'])

const form = ref({ name: '', email: '', code: '+62', phone: '', message: '' })
const sending = ref(false)
const success = ref(false)

function close() {
  emit('update:modelValue', false)
}

watch(() => props.modelValue, (v) => {
  if (!v) {
    setTimeout(() => {
      form.value = { name: '', email: '', code: '+62', phone: '', message: '' }
      success.value = false
    }, 300)
  }
})

function submit() {
  sending.value = true
  setTimeout(() => { success.value = true; sending.value = false }, 800)
}
</script>

<style scoped>
.cm-backdrop {
  position: fixed; inset: 0;
  background: rgba(28, 42, 35, 0.45);
  backdrop-filter: blur(4px);
  z-index: 9000;
  display: flex; align-items: center; justify-content: center;
  padding: 20px;
}

.cm-card {
  background: #fff;
  border-radius: 20px;
  max-width: 480px; width: 100%;
  max-height: 90vh; overflow-y: auto;
  padding: 40px 36px 36px;
  position: relative;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
}

.cm-wave-top, .cm-wave-bottom {
  position: absolute; left: 0; right: 0;
  width: 100%; height: 70px;
  pointer-events: none;
  border-radius: 20px 20px 0 0;
}
.cm-wave-top { top: 0; }
.cm-wave-bottom { bottom: 0; border-radius: 0 0 20px 20px; }

.cm-close {
  position: absolute; top: 16px; right: 16px;
  width: 32px; height: 32px; border-radius: 50%;
  border: none; background: #f3f4f6;
  color: #647067; display: flex; align-items: center; justify-content: center;
  cursor: pointer; transition: background 0.2s;
}
.cm-close:hover { background: #e5e7eb; }

.cm-title {
  font-size: 28px; font-weight: 800; color: #1c2a23;
  margin: 0 0 6px; letter-spacing: -0.02em;
}

.cm-info {
  display: flex; align-items: center; gap: 10px;
  font-size: 13px; color: #9ca3af; margin-bottom: 32px;
}
.cm-info-sep { color: #d1d5db; }

.cm-form { display: flex; flex-direction: column; gap: 20px; }

.cm-field { display: flex; flex-direction: column; }

.cm-input, .cm-select {
  width: 100%; padding: 12px 0;
  border: none; border-bottom: 1.5px solid #e5e7e6;
  background: transparent; font-size: 15px; color: #1c2a23;
  font-family: inherit; outline: none;
  transition: border-color 0.2s;
}
.cm-input:focus, .cm-select:focus { border-color: #1c2a23; }
.cm-input::placeholder { color: #c0c5cc; }

.cm-row { display: flex; gap: 16px; align-items: end; }

.cm-select {
  width: auto; flex-shrink: 0;
  padding: 12px 8px; cursor: pointer;
  appearance: auto;
}
.cm-select:focus { border-color: #1c2a23; }

.cm-input-phone { flex: 1; }

.cm-textarea {
  width: 100%; padding: 12px 0;
  border: none; border-bottom: 1.5px solid #e5e7e6;
  background: transparent; font-size: 15px; color: #1c2a23;
  line-height: 1.7; resize: vertical; font-family: inherit;
  outline: none; transition: border-color 0.2s;
}
.cm-textarea:focus { border-color: #1c2a23; }
.cm-textarea::placeholder { color: #c0c5cc; }

.cm-submit {
  width: 100%; padding: 14px; border: none; border-radius: 12px;
  background: #1c2a23; color: #fff; font-size: 15px; font-weight: 700;
  cursor: pointer; transition: background 0.2s, transform 0.15s;
  margin-top: 4px;
}
.cm-submit:hover { background: #0f1a13; transform: translateY(-1px); }
.cm-submit:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }

.cm-success { display: flex; flex-direction: column; align-items: center; padding: 28px 0 12px; text-align: center; }
.cm-success-icon { margin-bottom: 20px; }
.cm-success-title { font-size: 22px; font-weight: 800; color: #1c2a23; margin: 0 0 10px; }
.cm-success-desc { font-size: 14px; color: #647067; margin: 0 0 28px; line-height: 1.7; max-width: 320px; }
.cm-success-btn {
  padding: 12px 40px; border-radius: 12px; border: none;
  background: #1c2a23; color: #fff; font-size: 15px; font-weight: 700;
  cursor: pointer; transition: background 0.2s;
}
.cm-success-btn:hover { background: #0f1a13; }

.cm-enter-active, .cm-leave-active { transition: opacity 0.25s ease; }
.cm-enter-from, .cm-leave-to { opacity: 0; }

@media (max-width: 520px) {
  .cm-card { padding: 32px 24px 28px; }
  .cm-title { font-size: 24px; }
  .cm-row { flex-direction: column; gap: 20px; }
  .cm-select { width: 100%; }
}
</style>
