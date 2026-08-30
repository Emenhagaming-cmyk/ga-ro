import { getKnowledge } from "./knowledge/registry.js"
export default async function handler(req, res) {

  if(req.method !== "POST"){
    return res.status(405).json({
      error:"Method Not Allowed"
    })
  }

  const API_KEY = process.env.GROQ_API_KEY

  if (!API_KEY) {
    return res.status(200).json({
      reply: "Asisten BISA belum diaktifkan Hubungi admin sekolah untuk info lebih lanjut."
    })
  }

  const { history } = req.body || {}

  // Input validation: batasi ukuran & bentuk history (LLM04 DoS guard)
  if (!Array.isArray(history) || history.length === 0 || history.length > 20) {
    return res.status(400).json({
      reply: "Pesan tidak valid. Silakan ulangi percakapan."
    })
  }

  const sanitized = history.slice(-20).map((m) => ({
    role: m && m.role === "assistant" ? "assistant" : "user",
    content: String(m?.content || "").slice(0, 2000),
  })).filter((m) => m.content.trim() !== "")

  if (sanitized.length === 0) {
    return res.status(400).json({
      reply: "Pesan tidak boleh kosong."
    })
  }

  const latestMessage = sanitized.at(-1).content

  const knowledge = getKnowledge(latestMessage)
  try{
    const response = await fetch(
      "https://api.groq.com/openai/v1/chat/completions",
      {
        method:"POST",
        headers:{
          "Content-Type":"application/json",
          "Authorization":"Bearer " + API_KEY
        },
        body: JSON.stringify({
  model: "openai/gpt-oss-120b",

  messages: [
    {
      role: "system",
      content: knowledge
    },

    ...sanitized
  ],

  temperature: 0.7
})
      }
    )

    const data = await response.json()

    if (!response.ok) {
      console.log("GROQ HTTP ERROR", response.status, JSON.stringify(data))
      return res.status(200).json({
        reply: "Maaf, layanan AI sedang sibuk. Silakan coba lagi beberapa saat."
      })
    }

    const reply = data.choices?.[0]?.message?.content
    if (!reply) {
      console.log("GROQ EMPTY REPLY", JSON.stringify(data))
      return res.status(200).json({
        reply: "Maaf, saya belum bisa menjawab saat ini. Coba ulangi pertanyaannya."
      })
    }

    return res.status(200).json({
      reply
    })

  }catch(err){

    console.log(err)

    return res.status(500).json({
      reply:"Maaf, terjadi gangguan teknis. Silakan coba lagi."
    })

  }

}