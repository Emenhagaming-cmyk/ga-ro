import { CONFIG } from '../config.js';
import { loadJsonSafe } from '../registry.js';

export async function searchInSite(ctx = {}, { query } = {}) {
  const q = String(query || '').toLowerCase().trim();
  if (q.length < 2) return { found: false, reason: 'query_too_short' };
  const results = [];
  const fb = loadJsonSafe('news.fallback.json');
  if (Array.isArray(fb)) {
    for (const n of fb) {
      const hay = ${n.title || ''}  .toLowerCase();
      if (hay.includes(q)) {
        results.push({ type: 'berita', slug: n.slug, title: n.title });
        if (results.length >= 10) break;
      }
    }
  }
  return { found: results.length > 0, query: q, results: results.slice(0, 10) };
}
