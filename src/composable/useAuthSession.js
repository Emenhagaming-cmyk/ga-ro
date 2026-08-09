import { ref } from "vue";

const BACKEND = import.meta.env.VITE_BACKEND_URL || "http://localhost:8000";
const STORAGE_KEY = "spmb_session_status";

const session = ref(
  JSON.parse(sessionStorage.getItem(STORAGE_KEY) || "null") || {
    logged_in: false,
    role: null,
    name: null,
    has_pendaftaran: false,
    status: null,
  }
);
const loaded = ref(!!sessionStorage.getItem(STORAGE_KEY));

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
        session.value = await res.json();
        persist(session.value);
      }
    } catch (e) {
      // backend off / cors blocked — anggap guest
      session.value = {
        logged_in: false,
        role: null,
        name: null,
        has_pendaftaran: false,
        status: null,
      };
      persist(session.value);
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
  setInterval(fetchStatus, 30000);

  return { session, loaded, fetchStatus, isSiswaLoggedIn, spmbTarget, BACKEND };
}