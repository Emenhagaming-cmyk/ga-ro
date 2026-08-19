import { ref } from "vue";

const BACKEND = import.meta.env.VITE_BACKEND_URL || "http://localhost:8000";
const STORAGE_KEY = "spmb_session_status";

const GUEST = {
  logged_in: false,
  role: null,
  name: null,
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
const loaded = ref(!!sessionStorage.getItem(STORAGE_KEY));
let bfcacheBound = false;
let intervalBound = false;

function persist(s) {
  sessionStorage.setItem(STORAGE_KEY, JSON.stringify(s));
}

export function useAuthSession() {
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

  const isSiswaLoggedIn = () =>
    session.value.logged_in && session.value.role === "siswa";

  const spmbTarget = () => {
    if (isSiswaLoggedIn() && session.value.has_pendaftaran) {
      return `${BACKEND}/dashboard-siswa`;
    }
    return `${BACKEND}/pendaftaran/create`;
  };

  // Refresh di background + sesekali revalidate; render pakai cache instan.
  fetchStatus();
  // ponytail: satu interval global — 7 komponen memanggil composable ini
  if (!intervalBound) {
    setInterval(fetchStatus, 30000);
    intervalBound = true;
  }

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