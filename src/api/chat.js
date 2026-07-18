const test = async () => {
  console.log("Tombol diklik")

  try {
    const data = await sendMessage("Halo")
    console.log("Respon:", data)
    alert(JSON.stringify(data))
  } catch (err) {
    console.error(err)
    alert(err.message)
  }
}