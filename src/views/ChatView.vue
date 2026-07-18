<script setup>
import { sendMessage } from "../services/chat"

const test = async () => {

  console.log("Button diklik 🔥")

  const data = await sendMessage("Halo")

  console.log(data)

}
</script>

<template>
  <button @click="test">
    Test API
  </button>
</template>