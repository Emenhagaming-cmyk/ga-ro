import { CONFIG } from "../config.js";
import { fetchWithGuard } from "../fetchGuard.js";
import { CANONICAL } from "../sources.js";
import { loadJsonSafe } from "../registry.js";

export async function listBerita(ctx = {}) {
  // 1) news.fallback.json
  const fb = loadJsonSafe("news.fallback.json");
  if (Array.isArray(fb) && fb.length > 0) {
    const items = fb.slice(0, ctx.limit || 6).map((n) => ({
      slug: n.slug,
      title: n.title,
      excerpt: n.excerpt || n.content?.slice?.(0, 120) || "",
      publishedAt: n.publishedAt || n.createdAt || n.date || null,
      category: n.category || null,
    }));
    return { items, source: "news.fallback.json" };
  }

  // 2) API
  try {
    const res = await fetchWithGuard(CANONICAL.newsApi, { ctx });
    if (res.ok && res.okJson && Array.isArray(res.json) && res.json.length > 0) {
      const items = res.json.slice(0, ctx.limit || 6).map((n) => ({
        slug: n.slug,
        title: n.title,
        excerpt: n.excerpt || "",
        publishedAt: n.publishedAt || null,
        category: n.category || null,
      }));
      return { items, source: "api.berita" };
    }
  } catch (e) {
    // ignore
  }

  // 3) sitemap minimal (optional) - return empty if not found
  return { items: [], source: "none" };
}
