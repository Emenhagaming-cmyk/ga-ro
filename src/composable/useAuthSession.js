import { ref } from "vue";

// ponytail: SATU sumber kebenaran untuk host backend. Vercel bisa punya
// VITE_BACKEND_URL ngawur ( pernah ketemu isinya base64 bukan URL ), jadi nilai
// env divalidasi dulu — kalau bukan http(s) DIABAIKAN, bukan dipakai. Tanpa ini
// satu env salah = semua fetch bocor diam-diam ke host ngawur.
const PROD_BACKEND = "https://pendaftaranspmb.vercel.app";
const envBackend = String(import.meta.env.VITE_BACKEND_URL || "");
const BACKEND = /^https?:\/\//i.test(envBackend)
  ? envBackend.replace(/\/+$/, "")
  : import.meta.env.DEV
    ? "http://localhost:8000"
    : PROD_BACKEND;
const STORAGE_KEY = "spmb_session_status";

const GUEST = {
  logged_in: false,
  role: null,
  name: null,
  avatar: null,
  has_pendaftaran: false,
  status: null,
};

// UI hanya membedakan admin vs siswa; role DB 'pendaftar' = siswa di frontend
function norm(d) {
  if (d && d.logged_in && d.role === "pendaftar") {
    return { ...d, role: "siswa" };
  }
  return d;
}

function sessionFromStorage() {
  try {
    return norm(JSON.parse(sessionStorage.getItem(STORAGE_KEY) || "null"));
  } catch {
    return null;
  }
}

// ?auth=... dikirim backend lewat link "ke landing" (mobile memblokir cookie
// third-party, jadi status dibawa via URL, bukan cookie).
(function applyAuthQuery() {
  const q = new URLSearchParams(window.location.search).get("auth");
  if (!q) return;
  let data = null;
  try {
    data = JSON.parse(atob(q));
  } catch {
    return;
  }
  if (!data || typeof data.logged_in !== "boolean") return;
  data = norm(data);
  try {
    sessionStorage.setItem(STORAGE_KEY, JSON.stringify(data));
  } catch {}
  const url = new URL(window.location.href);
  url.searchParams.delete("auth");
  history.replaceState(null, "", url.toString());
})();

const session = ref(sessionFromStorage() || { ...GUEST });
const loaded = ref(true); // ponytail: start loaded, update async — jangan block render
let bfcacheBound = false;

function persist(s) {
  sessionStorage.setItem(STORAGE_KEY, JSON.stringify(s));
}

async function fetchStatus() {
  try {
    const res = await fetch(`${BACKEND}/auth-status`, {
      credentials: "include",
    });
    if (res.ok) {
      const data = await res.json();
      // ponytail: mobile memblokir cookie third-party → server menjawab
      // "guest" walau user login; jangan downgrade cache yang sudah login
      if (data.logged_in || !session.value.logged_in) {
        session.value = norm(data);
        persist(session.value);
      }
    }
  } catch (e) {
    // backend off / cors blocked — anggap guest, tapi jangan downgrade login cache
    if (!session.value.logged_in) {
      session.value = { ...GUEST };
      persist(session.value);
    }
  }
  loaded.value = true;
}

// ponytail: SATU interval global (banyak komponen memanggil composable ini),
// dan HANYA fetch saat login — guest di landing publik hemat jaringan/baterai.
// Intervalnya sendiri tetap jalan (timer JS murah); yang di-skip adalah fetch.
let intervalBound = false;
if (!intervalBound) {
  intervalBound = true;
  setInterval(() => {
    if (session.value.logged_in) fetchStatus();
  }, 30000);
}

export function useAuthSession() {
  const isSiswaLoggedIn = () =>
    session.value.logged_in && session.value.role === "siswa";

  const spmbTarget = () => {
    if (isSiswaLoggedIn() && session.value.has_pendaftaran) {
      return `${BACKEND}/dashboard-siswa`;
    }
    return `${BACKEND}/pendaftaran`;
  };

  // Refresh di background + sesekali revalidate; render pakai cache instan.
  fetchStatus();

  // bfcache: back dari halaman backend (login/form) → state basi, revalidate
  if (!bfcacheBound) {
    window.addEventListener("pageshow", (e) => {
      if (e.persisted) fetchStatus();
    });
    bfcacheBound = true;
  }

  return { session, loaded, fetchStatus, isSiswaLoggedIn, spmbTarget, BACKEND };
}

export { BACKEND };