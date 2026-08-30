import { BACKEND } from "@/composable/useAuthSession"

// ponytail: cache token sekali per sesi; backend memutar token per sesi, bukan per request
let tokenPromise = null

export function getCsrfToken() {
  if (!tokenPromise) {
    tokenPromise = fetch(`${BACKEND}/csrf-token`, { credentials: "include" })
      .then((r) => (r.ok ? r.json() : null))
      .then((d) => d?.csrf_token ?? null)
      .catch(() => null)
      .then((token) => {
        // ponytail: retry di panggilan berikutnya kalau gagal, jangan cache null selamanya
        if (!token) tokenPromise = null
        return token
      })
  }
  return tokenPromise
}