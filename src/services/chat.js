export async function sendMessage(history){

  // Client-side guard mirip backend: batasi panjang & jumlah pesan
  const trimmed = (history || [])
    .slice(-20)
    .map(m => ({ ...m, content: String(m.content || "").slice(0, 2000) }))

  const res = await fetch("/api/chat",{

    method:"POST",

    headers:{
      "Content-Type":"application/json"
    },

    body:JSON.stringify({

      history: trimmed

    })

  })

  return await res.json()

}