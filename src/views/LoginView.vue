<template>
  <div class="login-container">
    <div class="login-card">
      <h1>Login</h1>
      
      <form @submit.prevent="handleLogin">
        <div class="form-group">
          <label for="email">Email</label>
          <input 
            v-model="form.email" 
            type="email" 
            id="email" 
            required
            placeholder="Masukkan email"
          />
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <input 
            v-model="form.password" 
            type="password" 
            id="password" 
            required
            placeholder="Masukkan password"
          />
        </div>

        <button type="submit" class="btn-submit" :disabled="loading">
          {{ loading ? 'Loading...' : 'Login' }}
        </button>
      </form>

      <p class="error" v-if="error">{{ error }}</p>
      
      <div class="info-section">
        <p class="info">
          Belum punya akun? <router-link to="/register">Daftar di sini</router-link>
        </p>
        
        <div class="admin-info">
          <p class="admin-label">Info Login Admin:</p>
          <p class="admin-creds">
            Email: <code>admin@pendaftaran.com</code><br>
            Password: <code>admin123</code>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuth } from '../composable/useAuth';

const router = useRouter();
const { login } = useAuth();

const form = ref({
  email: '',
  password: ''
});

const loading = ref(false);
const error = ref('');

const handleLogin = async () => {
  loading.value = true;
  error.value = '';

  try {
    const response = await fetch('http://localhost:8000/api/login', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(form.value)
    });

    const data = await response.json();

    if (!response.ok) {
      error.value = data.message || 'Login gagal';
      return;
    }

    // Store token & user
    localStorage.setItem('token', data.token);
    localStorage.setItem('user', JSON.stringify(data.user));

    // Redirect based on role
    if (data.user.role === 'admin') {
      router.push('/dashboard-admin');
    } else {
      router.push('/dashboard-siswa');
    }
  } catch (err) {
    error.value = 'Terjadi kesalahan: ' + err.message;
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
.login-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.login-card {
  background: white;
  padding: 2rem;
  border-radius: 8px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
  width: 100%;
  max-width: 400px;
}

h1 {
  text-align: center;
  margin-bottom: 2rem;
  color: #333;
}

.form-group {
  margin-bottom: 1.5rem;
}

label {
  display: block;
  margin-bottom: 0.5rem;
  color: #555;
  font-weight: 500;
}

input {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 1rem;
  box-sizing: border-box;
}

input:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.btn-submit {
  width: 100%;
  padding: 0.75rem;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: transform 0.2s;
}

.btn-submit:hover:not(:disabled) {
  transform: translateY(-2px);
}

.btn-submit:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.error {
  color: #e74c3c;
  text-align: center;
  margin-top: 1rem;
  font-size: 0.9rem;
}

.info-section {
  margin-top: 1.5rem;
}

.info {
  text-align: center;
  color: #666;
  font-size: 0.9rem;
  margin: 0 0 1rem;
}

.info a {
  color: #667eea;
  text-decoration: none;
  font-weight: 600;
}

.info a:hover {
  text-decoration: underline;
}

.admin-info {
  background: #f5f5f5;
  padding: 1rem;
  border-radius: 6px;
  border-left: 3px solid #667eea;
}

.admin-label {
  margin: 0 0 0.5rem;
  font-weight: 600;
  color: #333;
  font-size: 0.9rem;
}

.admin-creds {
  margin: 0;
  color: #666;
  font-size: 0.85rem;
  line-height: 1.6;
}

.admin-creds code {
  background: white;
  padding: 2px 6px;
  border-radius: 3px;
  font-family: monospace;
  color: #667eea;
}
</style>
