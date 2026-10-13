import { CONFIG } from "../config.js";
import { fetchHtml } from "./fetchHtml.js";
import { extractMainContent } from "./extractMainContent.js";

export async function getPageSummary(ctx = {}, { url } = {}) {
  if (!url) return { ok: false, reason: "invalid_url" };
  const r = await fetchHtml(ctx, { url });
  if (!r.ok) return { ok: false, reason: r.reason || "fetch_fail" };
  if (r.spaShell) return { ok: false, reason: "spa-shell" };
  if (r.isJson && r.html) {
    try {
      const j = JSON.parse(r.html);
      const keys = Object.keys(j || {}).slice(0, 20);
      return { ok: true, kind: "json_obj", keys, sample: String(r.html).slice(0, 1200) };
    } catch {}
  }
  const text = extractMainContent(r.html || "", 1500);
  return { ok: true, kind: "html", text };
}
