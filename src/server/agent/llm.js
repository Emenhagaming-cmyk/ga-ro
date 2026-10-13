import { CONFIG } from './config.js';

const GROQ_URL = 'https://api.groq.com/openai/v1/chat/completions';
const MODEL = 'openai/gpt-oss-120b';

export async function groq(apiKey, messages, extra = {}) {
  const ctrl = new AbortController();
  const timeout = Math.min(CONFIG.LLM_TIMEOUT_MS, extra.remainingMs || CONFIG.LLM_TIMEOUT_MS);
  const t = setTimeout(() => ctrl.abort(), timeout);
  try {
    const res = await fetch(GROQ_URL, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': Bearer ,
      },
      signal: ctrl.signal,
      body: JSON.stringify({
        model: MODEL,
        messages,
        temperature: 0.4,
        ...extra,
      }),
    });
    const data = await res.json().catch(() => ({}));
    return { ok: res.ok, status: res.status, data };
  } catch (e) {
    return { ok: false, error: e?.name === 'AbortError' ? 'TIMEOUT' : 'FETCH_ERROR' };
  } finally {
    clearTimeout(t);
  }
}
