export async function sendMessage(history) {

  const res = await fetch("/api/chat", {
    method: "POST",
    headers: {
      "Content-Type": "application/json"
    },
    body: JSON.stringify({
      history
    })
  })

  return await res.json()
}