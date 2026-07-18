export async function sendMessage(message){

  const res = await fetch("/api/chat",{

    method:"POST",

    headers:{
      "Content-Type":"application/json"
    },

    body:JSON.stringify({

      history:[
        {
          role:"user",
          content:message
        }
      ],

      user:"guest"

    })

  })

  return await res.json()

}