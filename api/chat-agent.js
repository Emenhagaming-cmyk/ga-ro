import { runAgent } from '../src/server/agent/index.js';

export default async function handler(req, res) {
  if (req.method !== 'POST') return res.status(405).json({ error: 'Method Not Allowed' });
  const API_KEY = process.env.GROQ_API_KEY;
  if (!API_KEY) {
    return res.status(200).json({ reply: 'Asisten BISA belum diaktifkan. Hubungi admin sekolah untuk info lebih lanjut.' });
  }
  const { history } = req.body || {};
  if (!Array.isArray(history) || history.length === 0 || history.length > 20) {
    return res.status(400).json({ reply: 'Pesan tidak valid. Silakan ulangi percakapan.' });
  }
  const sanitized = history
    .slice(-20)
    .map((m) => ({ role: m && m.role === 'assistant' ? 'assistant' : 'user', content: String(m?.content || '').slice(0, 2000) }))
    .filter((m) => m.content.trim() !== '');
  if (sanitized.length === 0) return res.status(400).json({ reply: 'Pesan tidak boleh kosong.' });
  try {
    const { reply, meta } = await runAgent({ history: sanitized, apiKey: API_KEY });
    return res.status(200).json({ reply, meta });
  } catch (err) {
    console.log('AGENT ERROR', String(err?.message || err));
    return res.status(200).json({ reply: 'Maaf, terjadi gangguan teknis. Silakan coba lagi.' });
  }
}
