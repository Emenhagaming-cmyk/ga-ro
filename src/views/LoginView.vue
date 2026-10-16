<template>
  <main class="login-page">
    <section class="login-card" aria-labelledby="login-title">
      <img src="/logo.webp" alt="Logo SMK Bahrul Ulum" class="logo" />
      <h1 id="login-title">Masuk</h1>
      <p>Login untuk menikmati fitur yang tersedia di SMK Bahrul Ulum.</p>
      <form @submit.prevent="submitLogin">
        <label for="username">Username</label>
        <input id="username" name="username" type="text" autocomplete="username" required autofocus placeholder="Masukkan username" />
        <label for="password">Password</label>
        <input id="password" name="password" type="password" autocomplete="current-password" required placeholder="Password" />
        <input type="hidden" name="remember" value="0" />
        <button type="submit">Masuk</button>
      </form>
      <p v-if="errorMessage" class="login-error" role="alert">{{ errorMessage }}</p>
      <a :href="frontendPath('/forgot-password')">Lupa kata sandi?</a>
      <span>Belum punya akun? <a :href="frontendPath('/register')">Daftar dulu</a></span>
    </section>
  </main>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import { BACKEND, frontendPath } from "../composable/useAuthSession";

const router = useRouter();
const errorMessage = ref("");
const submitting = ref(false);

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
    await router.push(frontendPath("/"));
  } catch {
    errorMessage.value = "Login gagal terhubung ke server. Coba lagi.";
  } finally {
    submitting.value = false;
  }
}
</script>

<style scoped>
.login-page{min-height:100vh;display:grid;place-items:center;padding:24px;background:linear-gradient(135deg,#214936,#3a6450);font-family:Quicksand,system-ui,sans-serif}.login-card{width:min(100%,400px);padding:36px;background:#ffffff;border-radius:24px;box-shadow:0 20px 60px #10291c66;text-align:center}.logo{width:72px;height:72px;object-fit:contain;margin-bottom:12px}.login-card h1{margin:0;color:#214936}.login-card p{color:#647067;margin:8px 0 24px}.login-card form{display:grid;gap:8px;text-align:left}.login-card label{font-weight:700;font-size:13px;color:#214936;margin-top:8px}.login-card input{padding:12px;border:1px solid #ccd8cf;border-radius:10px;font:inherit}.login-card button{margin-top:16px;padding:13px;border:0;border-radius:10px;background:#2f5b45;color:#fff;font:inherit;font-weight:700;cursor:pointer}.login-card a{display:block;margin-top:16px;color:#2f5b45;font-weight:700;text-decoration:none}.login-card span{display:block;margin-top:12px;color:#647067;font-size:13px}.login-card span a{display:inline;margin:0}
</style>
