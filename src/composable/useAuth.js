import { ref, computed } from 'vue';

const user = ref(null);
const token = ref(null);

export function useAuth() {
  // Load dari localStorage on init
  const loadFromStorage = () => {
    const storedUser = localStorage.getItem('user');
    const storedToken = localStorage.getItem('token');
    
    if (storedUser) user.value = JSON.parse(storedUser);
    if (storedToken) token.value = storedToken;
  };

  const isAuthenticated = computed(() => !!token.value);
  const isAdmin = computed(() => user.value?.role === 'admin');
  const isSiswa = computed(() => user.value?.role === 'siswa');

  const login = (userData, authToken) => {
    user.value = userData;
    token.value = authToken;
    localStorage.setItem('user', JSON.stringify(userData));
    localStorage.setItem('token', authToken);
  };

  const logout = () => {
    user.value = null;
    token.value = null;
    localStorage.removeItem('user');
    localStorage.removeItem('token');
  };

  const getAuthHeader = () => {
    return {
      'Authorization': `Bearer ${token.value}`,
      'Content-Type': 'application/json'
    };
  };

  // Load on module init
  loadFromStorage();

  return {
    user,
    token,
    isAuthenticated,
    isAdmin,
    isSiswa,
    login,
    logout,
    getAuthHeader,
    loadFromStorage
  };
}
