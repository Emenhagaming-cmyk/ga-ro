export async function sendMessage(history) {
  // Client-side guard mirip backend: batasi panjang & jumlah pesan
  const trimmed = (history || [])
    .slice(-20)
    .map(m => ({ ...m, content: String(m.content || "").slice(0, 2000) }))

  // Session count tracking (localStorage per browser session)
  const STORAGE_KEY = "bisa_chat_session_count"
  let sessionCount = 0
  try {
    const stored = localStorage.getItem(STORAGE_KEY)
    sessionCount = stored ? parseInt(stored, 10) : 0
  } catch {}

  sessionCount += 1
  try {
    localStorage.setItem(STORAGE_KEY, String(sessionCount))
  } catch {}

  const res = await fetch("/api/chat", {
    method: "POST",
    headers: {
      "Content-Type": "application/json"
    },
    body: JSON.stringify({
      history: trimmed,
      sessionCount
    })
  })

  return await res.json()
}