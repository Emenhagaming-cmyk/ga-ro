import { CONFIG } from "./config.js";
import { isAllowedUrl, normalizeUrl } from "./fetchGuard.js";
import { isSpaShell } from "./spa.js";

export const CANONICAL = Object.freeze({
  newsApi: ${CONFIG.API}/berita,
  newsFallback: ${CONFIG.APEX},
  lowonganApi: ${CONFIG.API}/lowongan,
  sitemap: ${CONFIG.APEX},
  robots: ${CONFIG.APEX}/robots.txt,
});

export function isWithinDepth(url) {
  try {
    const u = new URL(url);
    const parts = u.pathname.split("/").filter(Boolean);
    return parts.length <= CONFIG.MAX_DEPTH;
  } catch {
    return false;
  }
}

export function pathDepth(url) {
  try {
    const u = new URL(url);
    const parts = u.pathname.split("/").filter(Boolean);
    return parts.length;
  } catch {
    return 99;
  }
}

export function shouldBypassSpa(html) {
  return isSpaShell(html);
}
