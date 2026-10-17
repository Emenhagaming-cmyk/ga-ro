<template>
  <main class="auth-page">
    <div class="auth-illustration" aria-hidden="true">
      <div class="auth-blob blob-1"></div>
      <div class="auth-blob blob-2"></div>
      <div class="auth-blob blob-3"></div>
      <div class="auth-blob blob-4"></div>
    </div>

    <div class="auth-form-area">
      <div class="auth-intro">
        <div class="brand">
          <img src="/logo.png" alt="Logo SMK Bahrul Ulum" />
          <span>SMK Bahrul Ulum</span>
        </div>
        <h1>Selamat Datang di SMK Bahrul Ulum</h1>
        <p>Silahkan Login untuk menikmati fitur yang tersedia di SMK Bahrul Ulum</p>
      </div>

      <section class="auth-card" aria-labelledby="login-title">
        <h2 id="login-title" class="form-title">Masuk</h2>
        <p class="form-subtitle">Hai Siswa! Login Terlebih dahulu ya</p>

        <p v-if="errorMessage" class="alert alert-error" role="alert">{{ errorMessage }}</p>

        <form @submit.prevent="submitLogin">
          <div class="form-group">
            <label for="username">Username</label>
            <div class="input-wrap">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
              </svg>
              <input id="username" name="username" type="text" autocomplete="username" required autofocus placeholder="Masukkan username" />
            </div>
          </div>

          <div class="form-group">
            <label for="password">Password</label>
            <div class="input-wrap pw-field">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
              </svg>
              <input id="password" name="password" :type="showPassword ? 'text' : 'password'" autocomplete="current-password" required placeholder="password" />
              <button
                type="button"
                class="pw-toggle"
                :aria-label="showPassword ? 'Sembunyikan password' : 'Tampilkan password'"
                :aria-pressed="showPassword"
                :title="showPassword ? 'Sembunyikan password' : 'Tampilkan password'"
                @click="showPassword = !showPassword"
              >
                <svg v-if="!showPassword" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                <svg v-else width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
              </button>
            </div>
          </div>

          <input type="hidden" name="remember" value="0" />
          <button type="submit" class="btn btn-primary" :disabled="submitting">
            {{ submitting ? "Memproses..." : "Masuk" }}
          </button>
        </form>

        <p class="auth-links">
          <a :href="frontendPath('/forgot-password')">Lupa kata sandi?</a>
        </p>
        <p class="auth-links">
          Belum punya akun?
          <a :href="frontendPath('/register')">Daftar dulu ya!</a>
        </p>
      </section>
    </div>

    <aside class="auth-right" aria-hidden="true">
      <img src="/images/doodle-selfie.png" alt="" class="doodle-img" />
    </aside>
  </main>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import { BACKEND, frontendPath, useAuthSession } from "../composable/useAuthSession";

const router = useRouter();
const { fetchStatus } = useAuthSession();
const errorMessage = ref("");
const submitting = ref(false);
const showPassword = ref(false);

async function submitLogin(event) {
  if (submitting.value) return;
  submitting.value = true;
  errorMessage.value = "";
  const form = new FormData(event.currentTarget);

  try {
    const csrf = await fetch(`${BACKEND}/csrf-token`, { credentials: "include" });
    const csrfData = csrf.ok ? await csrf.json() : {};
    const response = await fetch(`${BACKEND}/login`, {
      method: "POST",
      credentials: "include",
      headers: {
        Accept: "application/json",
        ...(csrfData.csrf_token ? { "X-CSRF-TOKEN": csrfData.csrf_token } : {}),
      },
      body: form,
    });

    const data = await response.json().catch(() => ({}));
    if (!response.ok) {
      errorMessage.value = data.message || data.errors?.username?.[0] || "Username atau password salah.";
      return;
    }

    const status = await fetch(`${BACKEND}/auth-status`, { credentials: "include" });
    const session = await status.json();
    sessionStorage.setItem("spmb_session_status", JSON.stringify(session));
    // ponytail: push path RELATIF — frontendPath() di DEV menghasilkan URL absolut
    // (http://localhost:5174/) yang di-resolve vue-router jadi path ngawur →
    // navigasi gagal dan user nyangkut di halaman login.
    await router.push("/");
    // sync ref session bersama (Navbar/hero) langsung, jangan nunggu interval 30 dtk
    fetchStatus();
  } catch {
    errorMessage.value = "Login gagal terhubung ke server. Coba lagi.";
  } finally {
    submitting.value = false;
  }
}
</script>

<style scoped>
.auth-page {
  position: relative;
  display: flex;
  min-height: 100vh;
  overflow: hidden;
  padding: 40px 24px;
  background: linear-gradient(135deg, #214936 0%, #2f5b45 55%, #3a6450 100%);
  background-attachment: fixed;
  color: #1c2a23;
  font-family: Quicksand, system-ui, sans-serif;
  line-height: 1.6;
}

.auth-illustration {
  position: absolute;
  inset: 0;
  overflow: hidden;
}

.auth-blob {
  position: absolute;
  border-radius: 42% 58% 63% 37% / 46% 38% 62% 54%;
  filter: blur(2px);
  opacity: 0.9;
}

.blob-1 {
  width: 380px;
  height: 380px;
  top: -80px;
  left: -80px;
  background: rgba(255, 255, 255, 0.1);
}

.blob-2 {
  width: 280px;
  height: 280px;
  bottom: -60px;
  right: -60px;
  background: rgba(125, 184, 141, 0.28);
}

.blob-3 {
  width: 180px;
  height: 180px;
  top: 60%;
  left: 8%;
  background: rgba(255, 255, 255, 0.1);
}

.blob-4 {
  width: 120px;
  height: 120px;
  top: 12%;
  right: 10%;
  background: rgba(125, 184, 141, 0.22);
}

.auth-form-area {
  position: relative;
  z-index: 2;
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 28px;
  padding: 0 40px;
}

.auth-intro {
  color: #ffffff;
  max-width: 420px;
  text-align: center;
  padding: 0 16px;
}

.brand {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  margin-bottom: 16px;
}

.brand img {
  height: 44px;
  width: 44px;
  border-radius: 12px;
  background: #fff;
  padding: 6px;
  object-fit: contain;
}

.brand span {
  font-size: 18px;
  font-weight: 800;
  letter-spacing: -0.02em;
}

.auth-intro h1 {
  font-size: 26px;
  font-weight: 800;
  line-height: 1.25;
  letter-spacing: -0.03em;
  margin-bottom: 10px;
}

.auth-intro p {
  font-size: 14px;
  color: rgba(255, 255, 255, 0.85);
  line-height: 1.7;
}

.auth-card {
  width: 100%;
  max-width: 400px;
  background: rgba(255, 255, 255, 0.28);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border-radius: 24px;
  padding: 36px;
  box-shadow: 0 30px 70px rgba(13, 26, 20, 0.45);
  border: 1px solid rgba(255, 255, 255, 0.42);
}

.form-title {
  font-size: 26px;
  font-weight: 800;
  margin-bottom: 6px;
  color: #edf7f2;
  letter-spacing: -0.03em;
  text-align: center;
}

.form-subtitle {
  color: #f5f2f2;
  margin-bottom: 26px;
  font-size: 14px;
  line-height: 1.7;
  text-align: center;
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

.form-group {
  margin-bottom: 18px;
}

.form-group label {
  display: block;
  margin-bottom: 6px;
  font-weight: 700;
  color: #ffffff;
  font-size: 12px;
  letter-spacing: 0.08em;
  text-transform: uppercase;
}

.input-wrap {
  position: relative;
}

.input-wrap > svg {
  position: absolute;
  left: 14px;
  top: 50%;
  transform: translateY(-50%);
  color: #7d8a80;
}

.input-wrap input {
  width: 100%;
  padding: 11px 14px 11px 44px;
  border: 1.5px solid #dfe4dd;
  border-radius: 12px;
  font-family: inherit;
  font-size: 13px;
  color: #020202;
  background: #ffffff;
  transition: all 0.25s ease;
}

.input-wrap input::placeholder {
  color: #ccc;
}

.input-wrap input:focus {
  outline: none;
  border-color: #3a6450;
  box-shadow: 0 0 0 3px rgba(58, 100, 80, 0.1);
}

.pw-field input {
  padding-right: 44px;
}

.pw-toggle {
  position: absolute;
  right: 10px;
  top: 50%;
  transform: translateY(-50%);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 4px;
  background: none;
  border: none;
  border-radius: 8px;
  color: #7d8a80;
  cursor: pointer;
  transition: color 0.25s ease;
}

.pw-toggle:hover {
  color: #3a6450;
}

.pw-toggle:focus-visible {
  outline: 2px solid #3a6450;
  outline-offset: 2px;
}

.btn {
  width: 100%;
  padding: 12px 26px;
  border: none;
  border-radius: 14px;
  font-weight: 700;
  font-size: 15px;
  font-family: inherit;
  cursor: pointer;
  transition: all 0.25s ease;
  display: block;
  text-align: center;
}

.btn-primary {
  background: linear-gradient(90deg, #2f5b45 0%, #3a6450 100%);
  color: #fff;
  box-shadow: 0 10px 24px rgba(58, 100, 80, 0.22);
  margin-top: 6px;
}

.btn-primary:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 14px 30px rgba(58, 100, 80, 0.3);
}

.btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.auth-links {
  margin-top: 18px;
  text-align: center;
  font-size: 13px;
  color: #ffffff;
}

.auth-links a {
  color: #ffffff;
  font-weight: 700;
  text-decoration: none;
}

.auth-links a:hover {
  text-decoration: underline;
}

.auth-right {
  flex: 0 0 380px;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  z-index: 2;
}

.doodle-img {
  max-width: 380px;
  width: 100%;
  height: auto;
  animation: doodle-float 4s ease-in-out infinite;
  filter: drop-shadow(0 20px 40px rgba(0, 0, 0, 0.15));
}

@keyframes doodle-float {
  0%,
  100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-14px);
  }
}

@media (max-width: 768px) {
  .auth-right {
    display: none;
  }

  .brand {
    margin-bottom: 14px;
  }

  .brand img {
    height: 38px;
    width: 38px;
  }

  .brand span {
    font-size: 16px;
  }

  .auth-intro h1 {
    font-size: 22px;
  }

  .auth-intro p {
    font-size: 13px;
  }

  .blob-1 {
    width: 180px;
    height: 180px;
    top: -40px;
    left: -40px;
  }

  .blob-2 {
    width: 140px;
    height: 140px;
    bottom: -30px;
    right: -30px;
  }

  .blob-3 {
    width: 90px;
    height: 90px;
  }

  .blob-4 {
    width: 70px;
    height: 70px;
  }

  .auth-form-area {
    padding: 24px 16px 40px;
    gap: 20px;
  }

  .auth-card {
    padding: 28px 24px;
  }

  .input-wrap input {
    font-size: 16px;
  }
}
</style>
