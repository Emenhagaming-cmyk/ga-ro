import { BACKEND } from "@/composable/useAuthSession";

// ponytail: satu titik buat fetch terautentikasi. Kirim `Accept: application/json`
// supaya saat cookie sesi kedaluwarsa/tak terkirim backend balas 401 JSON (bukan
// redirect halaman login HTML) → `res.json()` tidak error "<!DOCTYPE". Kalau 401,
// arahkan balik ke login backend untuk refresh sesi.
export async function fetchJson(url, options = {}) {
  const res = await fetch(url, {
    ...options,
    credentials: "include",
    headers: { Accept: "application/json", ...(options.headers || {}) },
  });

  if (res.status === 401) {
    window.location.assign(`${BACKEND}/login`);
    throw new Error("Sesi berakhir, silakan masuk kembali.");
  }

  return res;
}
