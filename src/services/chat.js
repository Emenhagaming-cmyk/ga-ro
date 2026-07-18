export async function sendMessage(message){

  try{

    const res = await fetch("/api/chat",{
      method:"POST",
      headers:{
        "Content-Type":"application/json"
      },
      body:JSON.stringify({ message })
    })

    console.log("Status:", res.status)

    return await res.json()

  }catch(err){

    console.error(err)

  }

}