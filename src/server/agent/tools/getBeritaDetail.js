import { CONFIG } from "../config.js";
import { fetchWithGuard } from "../fetchGuard.js";
import { CANONICAL } from "../sources.js";
import { loadJsonSafe } from "../registry.js";

export async function getBeritaDetail(ctx = {}, { slug } = {}) {
  if (!slug || typeof slug !== "string") return { found: false, reason: "invalid_slug" };
  if (!CONFIG.SLUG_RE.test(slug)) return { found: false, reason: "invalid_slug" };

  // 1) API first
  try {
    const url = ${CANONICAL.newsApi}/;
    const res = await fetchWithGuard(url, { ctx });
    if (res.ok && res.okJson && res.json && (res.json.slug === slug || res.json.id)) {
      return { found: true, berita: res.json, source: "api" };
    }
  } catch (e) {
    // fall through
  }

  // 2) fallback news.fallback.json
  const fb = loadJsonSafe("news.fallback.json");
  if (Array.isArray(fb)) {
    const item = fb.find((n) => n.slug === slug);
    if (item) return { found: true, berita: item, source: "news.fallback.json" };
  }

  return { found: false, reason: "not_found" };
}
