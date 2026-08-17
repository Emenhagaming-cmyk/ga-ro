<template>
  <div class="login-page">
    <div class="login-section">
      <h1 class="form-title">Masuk</h1>
      <p class="form-subtitle">Masuk untuk mengelola pendaftaran SPMB Anda.</p>

      <div v-if="error" class="alert alert-error">{{ error }}</div>

      <form @submit.prevent="handleLogin">
        <div class="form-group">
          <label>Username</label>
          <input
            v-model="form.username"
            type="text"
            placeholder="Masukkan username"
            required
            autofocus
          />
        </div>

        <div class="form-group">
          <label>Password</label>
          <input
            v-model="form.password"
            type="password"
            placeholder="Masukkan password"
            required
          />
        </div>

        <div class="btn-group">
          <button type="submit" class="btn btn-primary" :disabled="loading" style="width:100%;">
            {{ loading ? 'Memproses...' : 'Masuk' }}
          </button>
        </div>
      </form>

      <p style="margin-top:14px;text-align:center;font-size:13px;color:#647067;">
        Belum punya akun?
        <a href="/register" style="color:#3a6450;font-weight:700;text-decoration:none;">Daftar di sini</a>
      </p>

      <p style="margin-top:16px;text-align:center;font-size:13px;color:#647067;">
        <a href="/" style="color:#3a6450;font-weight:700;text-decoration:none;text-decoration:none;">&larr; Kembali ke Beranda</a>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useAuthSession } from '@/composable/useAuthSession';

const BACKEND = import.meta.env.VITE_BACKEND_URL || 'http://localhost:8000';

const form = ref({
  username: '',
  password: ''
});

const loading = ref(false);
const error = ref('');

const { fetchStatus } = useAuthSession();

const handleLogin = async () => {
  loading.value = true;
  error.value = '';

  try {
    const pageRes = await fetch(`${BACKEND}/login`, {
      credentials: 'include',
    });
    const html = await pageRes.text();
    const match = html.match(/name="_token"\s+value="([^"]+)"/);
    const token = match ? match[1] : '';

    if (!token) {
      error.value = 'Gagal memuat CSRF token';
      loading.value = false;
      return;
    }

    const res = await fetch(`${BACKEND}/login`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
        'X-Requested-With': 'XMLHttpRequest',
      },
      credentials: 'include',
      body: new URLSearchParams({
        _token: token,
        username: form.value.username,
        password: form.value.password,
      }).toString(),
      redirect: 'follow',
    });

    const finalUrl = res.url || '';

    if (finalUrl.includes('/dashboard-admin') || finalUrl.includes('/dashboard-siswa') || finalUrl === `${BACKEND}/` || finalUrl === `${BACKEND}` || res.ok) {
      await fetchStatus();
      window.location.href = '/';
    } else {
      const text = await res.text();
      if (text.includes('credentials') || text.includes('password')) {
        error.value = 'Username atau password salah';
      } else {
        error.value = 'Login gagal, silakan coba lagi';
      }
    }
  } catch (err) {
    error.value = 'Gagal terhubung ke server';
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
.login-page {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  min-height: 100dvh;
  background:
    radial-gradient(circle at top left, rgba(125, 184, 141, 0.14), transparent 28%),
    #f2f4f1;
  padding: 24px;
}

.login-section {
  background: #fbfcfa;
  border: 1px solid #dfe4dd;
  padding: 36px 40px;
  border-radius: 22px;
  box-shadow: 0 12px 24px rgba(35, 55, 42, 0.06);
  width: 100%;
  max-width: 420px;
  position: relative;
  overflow: hidden;
}

.login-section::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(90deg, #2a5238 0%, #3a6450 58%, #7db88d 100%);
}

.form-title {
  font-size: 24px;
  font-weight: 800;
  margin-bottom: 6px;
  color: #1c2a23;
  letter-spacing: -0.03em;
}

.form-subtitle {
  color: #647067;
  margin-bottom: 28px;
  font-size: 14px;
  line-height: 1.7;
}

.form-group {
  margin-bottom: 18px;
}

label {
  display: block;
  margin-bottom: 6px;
  font-weight: 700;
  color: #3a6450;
  font-size: 12px;
  letter-spacing: 0.08em;
  text-transform: uppercase;
}

input {
  width: 100%;
  padding: 10px 14px;
  border: 1.5px solid #dfe4dd;
  border-radius: 12px;
  font-family: inherit;
  font-size: 13px;
  color: #1c2a23;
  background: #ffffff;
  transition: all 0.25s ease;
  box-sizing: border-box;
}

input::placeholder {
  color: #a3a8a4;
}

input:focus {
  outline: none;
  border-color: #3a6450;
  box-shadow: 0 0 0 3px rgba(58, 100, 80, 0.1);
}

.btn-group {
  margin-top: 28px;
}

.btn {
  padding: 11px 26px;
  border: none;
  border-radius: 14px;
  font-weight: 700;
  font-size: 14px;
  font-family: inherit;
  cursor: pointer;
  transition: all 0.25s ease;
}

.btn-primary {
  background: #3a6450;
  color: #fff;
  box-shadow: 0 10px 24px rgba(58, 100, 80, 0.17);
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 14px 30px rgba(58, 100, 80, 0.25);
}

.btn-primary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

.alert {
  padding: 12px 16px;
  border-radius: 12px;
  margin-bottom: 20px;
  font-size: 13px;
  font-weight: 600;
  border: 1px solid;
}

.alert-error {
  background: #fef2f2;
  color: #991b1b;
  border-color: rgba(153, 27, 27, 0.15);
}

@media (max-width: 480px) {
  .login-section {
    padding: 28px 24px;
    border-radius: 18px;
  }

  .form-title {
    font-size: 20px;
  }
}
</style>
