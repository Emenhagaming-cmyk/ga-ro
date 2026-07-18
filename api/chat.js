export default async function handler(req, res) {

  if(req.method !== "POST"){
    return res.status(405).json({
      error:"Method Not Allowed"
    })
  }

  const API_KEY = process.env.GROQ_API_KEY

  const { history } = req.body

  try{

    const response = await fetch(
      "https://api.groq.com/openai/v1/chat/completions",
      {
        method:"POST",
        headers:{
          "Content-Type":"application/json",
          "Authorization":"Bearer " + API_KEY
        },
        body:JSON.stringify({
          model:"llama-3.3-70b-versatile",
          messages:history,
          temperature:0.7
        })
      }
    )

    const data = await response.json()

    return res.status(200).json({
      reply:data.choices[0].message.content
    })

  }catch(err){

    console.log(err)

    return res.status(500).json({
      reply:"Groq Error"
    })

  }

}