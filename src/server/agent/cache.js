import { CONFIG } from "./config.js";

const STORE = new Map();

export function cacheGet(url) {
  const key = String(url || "");
  const e = STORE.get(key);
  if (!e) return null;
  if (Date.now() - e.ts > CONFIG.CACHE_TTL_MS) {
    STORE.delete(key);
    return null;
  }
  return e.val;
}

export function cacheSet(url, val) {
  const key = String(url || "");
  STORE.set(key, { ts: Date.now(), val });
}

export function cacheHas(url) {
  return cacheGet(url) !== null;
}

export function cacheClear() {
  STORE.clear();
}
