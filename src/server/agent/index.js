import { CONFIG } from './config.js';
import { TOOL_SCHEMA } from './tools/schema.js';
import { runTool } from './tools/index.js';
import { AGENT_SYSTEM_PROMPT } from './prompt.js';
import { groq } from './llm.js';

function getKnowledge() { return ''; }

const SMALL_TALK = /^(halo|hai|hi|selamat|terima kasih|makasih|siapa kamu|apa itu bisa)\b/i;
const FRESH_HINTS = [
  /berita|pengumuman|kabar terbaru|update|lowongan|loker|karir|prestasi terbaru/,
  /jadwal|tanggal|biaya|syarat|kuota|gelombang|alamat|lokasi|kontak|nomor|telepon|email/,
  /jurusan|program keahlian|rpl|tkj|fasilitas|ekstrakurikuler|koperasi|produk/,
];

function needsTools(text) {
  const t = String(text || '').trim();
  if (t.length < 3) return false;
  if (SMALL_TALK.test(t) && t.length < 40) return false;
  return FRESH_HINTS.some((re) => re.test(t.toLowerCase()));
}

const FALLBACK = 'Maaf, saya belum bisa menjawab saat ini. Coba ulangi pertanyaannya.';
const FALLBACK_BUSY = 'Maaf, layanan AI sedang sibuk. Silakan coba lagi beberapa saat.';
const FALLBACK_NOTFOUND = 'Maaf, informasi itu belum saya temukan di situs resmi sekolah. Silakan tanyakan langsung ke pihak sekolah.';

export async function runAgent({ history, apiKey }) {
  const latest = history.at(-1)?.content || '';
  let knowledge = '';
  try {
    if (getKnowledge) knowledge = getKnowledge(latest) || '';
  } catch {}

  const baseMessages = [
    { role: 'system', content: ${AGENT_SYSTEM_PROMPT}\n\n# DATA LOKAL (statis, fallback)\n },
    ...history,
  ];

  if (!needsTools(latest)) {
    const { ok, data } = await groq(apiKey, baseMessages, { remainingMs: CONFIG.LLM_TIMEOUT_MS });
    const reply = data?.choices?.[0]?.message?.content;
    return { reply: reply || FALLBACK, meta: { lane: 'fast', usedTools: [], pages: 0, iterations: 0 } };
  }

  const ctx = { pagesFetched: 0, toolCalls: 0, visited: new Set(), startedAt: Date.now() };
  const messages = [...baseMessages];
  const usedTools = [];
  let iterations = 0;
  let lastContent = '';

  while (iterations < CONFIG.MAX_ITERATIONS) {
    const remaining = CONFIG.BUDGET_TOTAL_MS - (Date.now() - ctx.startedAt);
    if (remaining <= 0) break;
    iterations++;
    const resGroq = await groq(apiKey, messages, { tools: TOOL_SCHEMA, tool_choice: 'auto', remainingMs: Math.min(CONFIG.LLM_TIMEOUT_MS, remaining) });
    if (!resGroq.ok) return { reply: FALLBACK_BUSY, meta: { lane: 'agent', usedTools, error: 'groq_error' } };
    const msg = resGroq.data?.choices?.[0]?.message;
    if (!msg) break;
    if (msg.content) lastContent = msg.content;
    const calls = Array.isArray(msg.tool_calls) ? msg.tool_calls : [];
    if (calls.length === 0) {
      return { reply: msg.content || lastContent || FALLBACK_NOTFOUND, meta: { lane: 'agent', usedTools, pages: ctx.pagesFetched, iterations } };
    }
    messages.push({ role: 'assistant', content: msg.content || null, tool_calls: calls });
    for (const call of calls) {
      let args = {};
      try { args = JSON.parse(call.function?.arguments || '{}'); } catch {}
      const name = call.function?.name;
      if (name && runTool) {
        const r = await runTool(name, args, ctx);
        usedTools.push(name);
        messages.push({ role: 'tool', tool_call_id: call.id, content: JSON.stringify(r).slice(0, 4000) });
      }
    }
  }

  const remaining2 = CONFIG.BUDGET_TOTAL_MS - (Date.now() - ctx.startedAt);
  const rg2 = await groq(apiKey, [...messages, { role: 'system', content: 'Budget habis. Jawab dengan fakta yang ada.' }], { tool_choice: 'none', remainingMs: Math.min(CONFIG.LLM_TIMEOUT_MS, remaining2 > 0 ? remaining2 : CONFIG.LLM_TIMEOUT_MS) });
  const finalReply = rg2.data?.choices?.[0]?.message?.content || lastContent || FALLBACK_NOTFOUND;
  return { reply: finalReply, meta: { lane: 'agent', usedTools, pages: ctx.pagesFetched, iterations, budget: remaining2 <= 0 ? 'exhausted' : 'ok' } };
}
