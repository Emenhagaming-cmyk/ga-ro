<template>
  <div class="pesan-page">
    <div class="page-header">
      <h2>Pesan</h2>
      <p class="page-desc">Tanya langsung ke BKK soal lowongan, PKL, atau lamaran Anda. Balasan masuk di halaman ini.</p>
    </div>

    <div v-if="loading" class="loading-state">
      <i class="fas fa-spinner fa-spin"></i> Memuat percakapan...
    </div>

    <div v-else-if="error" class="error-state">
      <i class="fas fa-triangle-exclamation"></i>
      <p>Gagal memuat percakapan. Pastikan Anda sudah login lalu muat ulang halaman.</p>
    </div>

    <div v-else class="chat-card">
      <div ref="listEl" class="chat-list">
        <div v-if="messages.length === 0" class="chat-empty">
          <i class="fas fa-comments"></i>
          <p>Belum ada pesan. Tulis pertanyaan pertama Anda ke BKK.</p>
        </div>

        <div
          v-for="msg in messages"
          :key="msg.id"
          class="bubble-row"
          :class="msg.sender === 'admin' ? 'from-admin' : 'from-siswa'"
        >
          <div class="bubble">
            <div class="bubble-author">{{ msg.sender === 'admin' ? 'BKK / Admin' : 'Anda' }}</div>
            <p class="bubble-body">{{ msg.body }}</p>
            <time class="bubble-time">{{ formatTime(msg.created_at) }}</time>
          </div>
        </div>
      </div>

      <div v-if="sendError" class="send-error">
        <i class="fas fa-circle-exclamation"></i> {{ sendError }}
      </div>

      <form class="composer" @submit.prevent="sendMessage">
        <textarea
          v-model="draft"
          rows="2"
          maxlength="2000"
          placeholder="Tulis pesan untuk BKK..."
          aria-label="Tulis pesan"
          :disabled="sending"
          @keydown.enter.exact.prevent="sendMessage"
        ></textarea>
        <button type="submit" class="btn-send" :disabled="sending || !draft.trim()">
          <i class="fas" :class="sending ? 'fa-spinner fa-spin' : 'fa-paper-plane'"></i>
          Kirim
        </button>
      </form>
      <p class="composer-hint">Enter untuk kirim, Shift+Enter untuk baris baru. Maksimal 2000 karakter.</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, nextTick } from 'vue'
import { useAuthSession } from '@/composable/useAuthSession'
import { fetchJson } from '@/services/fetchJson'
import { getCsrfToken } from '@/services/csrf'

const { BACKEND } = useAuthSession()

const messages = ref([])
const draft = ref('')
const loading = ref(true)
const error = ref(false)
const sending = ref(false)
const sendError = ref('')
const listEl = ref(null)

let pollTimer = null

async function fetchMessages(silent = false) {
  if (!silent) loading.value = true
  try {
    const res = await fetchJson(`${BACKEND}/career/pesan`)
    if (!res.ok) throw new Error('gagal')
    const data = await res.json()
    const next = data.messages || []
    const grew = next.length !== messages.value.length
    messages.value = next
    if (grew) scrollToBottom()
  } catch (e) {
    if (!silent) error.value = true
  } finally {
    if (!silent) loading.value = false
  }
}

async function sendMessage() {
  const body = draft.value.trim()
  if (!body || sending.value) return

  sending.value = true
  sendError.value = ''
  try {
    const res = await fetchJson(`${BACKEND}/career/pesan`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': await getCsrfToken(),
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ body }),
    })

    if (res.status === 422) {
      const data = await res.json().catch(() => ({}))
      sendError.value = data.message || data.errors?.body?.[0] || 'Pesan tidak valid.'
      return
    }
    if (!res.ok) throw new Error('gagal')

    messages.value.push(await res.json())
    draft.value = ''
    scrollToBottom()
  } catch (e) {
    sendError.value = 'Pesan gagal terkirim. Coba lagi.'
  } finally {
    sending.value = false
  }
}

function scrollToBottom() {
  nextTick(() => {
    if (listEl.value) listEl.value.scrollTop = listEl.value.scrollHeight
  })
}

function formatTime(dateStr) {
  if (!dateStr) return ''
  return new Date(dateStr).toLocaleString('id-ID', {
    day: 'numeric',
    month: 'short',
    hour: '2-digit',
    minute: '2-digit',
  })
}

onMounted(async () => {
  await fetchMessages()
  // ponytail: polling ringan, bukan websocket — cukup untuk 1 thread per siswa
  pollTimer = setInterval(() => fetchMessages(true), 15000)
})

onBeforeUnmount(() => {
  if (pollTimer) clearInterval(pollTimer)
})
</script>

<style scoped>
.pesan-page {
  padding: 4px 0;
}

.page-header h2 {
  font-size: 24px;
  font-weight: 800;
  color: #1c2a23;
  margin: 0 0 6px;
}

.page-desc {
  font-size: 13px;
  color: #647067;
  margin: 0 0 20px;
}

.chat-card {
  background: #fff;
  border: 1px solid var(--color-border, #e5e7e6);
  border-radius: 20px;
  box-shadow: 0 4px 12px rgba(35, 55, 42, 0.05);
  overflow: hidden;
  display: flex;
  flex-direction: column;
}

.chat-list {
  padding: 20px 22px;
  height: min(56vh, 520px);
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  gap: 12px;
  background: #fafcfa;
}

.chat-empty {
  margin: auto;
  text-align: center;
  color: #647067;
  font-size: 13px;
}

.chat-empty i {
  display: block;
  font-size: 40px;
  opacity: 0.2;
  margin-bottom: 12px;
}

.bubble-row {
  display: flex;
}

.bubble-row.from-siswa {
  justify-content: flex-end;
}

.bubble {
  max-width: min(78%, 560px);
  padding: 12px 16px;
  border-radius: 16px;
  background: #fff;
  border: 1px solid #e5e7e6;
}

.bubble-row.from-siswa .bubble {
  background: #3a6450;
  border-color: #3a6450;
  color: #fff;
}

.bubble-author {
  font-size: 11px;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: #8a9a8f;
  margin-bottom: 4px;
}

.bubble-row.from-siswa .bubble-author {
  color: rgba(255, 255, 255, 0.7);
}

.bubble-body {
  margin: 0;
  font-size: 13.5px;
  line-height: 1.6;
  white-space: pre-wrap;
  word-break: break-word;
}

.bubble-time {
  display: block;
  margin-top: 6px;
  font-size: 11px;
  color: #96a098;
}

.bubble-row.from-siswa .bubble-time {
  color: rgba(255, 255, 255, 0.65);
}

.send-error {
  padding: 10px 22px 0;
  font-size: 12.5px;
  font-weight: 700;
  color: #c0392b;
}

.composer {
  display: flex;
  gap: 10px;
  padding: 16px 20px 10px;
  border-top: 1px solid #f0f3f0;
  align-items: flex-end;
}

.composer textarea {
  flex: 1;
  resize: vertical;
  min-height: 48px;
  max-height: 160px;
  padding: 12px 14px;
  border: 1.5px solid rgba(58, 100, 80, 0.16);
  border-radius: 14px;
  background: #fff;
  font-family: inherit;
  font-size: 13.5px;
  color: #1c2a23;
  outline: none;
  transition: border-color 0.2s, box-shadow 0.2s;
}

.composer textarea:focus {
  border-color: #3a6450;
  box-shadow: 0 0 0 3px rgba(58, 100, 80, 0.1);
}

.btn-send {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 12px 22px;
  border: none;
  border-radius: 14px;
  background: #3a6450;
  color: #fff;
  font-family: inherit;
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-send:hover:not(:disabled) {
  background: #2f5b45;
}

.btn-send:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.composer-hint {
  margin: 0;
  padding: 0 20px 16px;
  font-size: 11px;
  color: #96a098;
}

.loading-state,
.error-state {
  text-align: center;
  padding: 70px 20px;
  color: #647067;
}

.error-state i {
  font-size: 32px;
  display: block;
  margin-bottom: 12px;
  opacity: 0.35;
}

@media (max-width: 600px) {
  .bubble {
    max-width: 88%;
  }

  .composer {
    flex-direction: column;
    align-items: stretch;
  }
}
</style>
