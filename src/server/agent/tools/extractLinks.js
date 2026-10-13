import { CONFIG } from "../config.js";
import { resolveSameSite } from "../fetchGuard.js";

export function extractLinks(html = "", baseUrl = "") {
  const links = [];
  const seen = new Set();
  const re = /<a\b[^>]*href\s*=\s*["']([^"']+)["']/gi;
  let m;
  while ((m = re.exec(html)) && links.length < CONFIG.MAX_LINKS) {
    const href = m[1].trim();
    if (!href || href.startsWith("javascript:") || href.startsWith("mailto:") || href.startsWith("tel:")) continue;
    const abs = resolveSameSite(href, baseUrl);
    if (!abs) continue;
    if (seen.has(abs)) continue;
    seen.add(abs);
    links.push(abs);
  }
  return links;
}
