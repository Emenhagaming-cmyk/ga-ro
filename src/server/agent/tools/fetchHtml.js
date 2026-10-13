import { CONFIG } from "../config.js";
import { fetchWithGuard } from "../fetchGuard.js";
import { isSpaShell } from "../spa.js";

export async function fetchHtml(ctx = {}, { url } = {}) {
  if (!url || typeof url !== "string") return { ok: false, reason: "invalid_url" };
  try {
    const res = await fetchWithGuard(url, { ctx });
    const spaShell = res.ok && res.contentType && res.contentType.includes("text/html") ? isSpaShell(res.body || "") : false;
    return {
      ok: res.ok,
      status: res.status,
      contentType: res.contentType,
      html: res.body || "",
      body: res.body || "",
      spaShell,
      isJson: !!res.okJson,
    };
  } catch (e) {
    return { ok: false, reason: e?.message || "FETCH_ERROR" };
  }
}
