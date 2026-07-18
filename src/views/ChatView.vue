<script setup>
import { ref } from "vue"
import { sendMessage } from "@/services/chat"

const reply = ref("")

async function testApi() {
  const history = [
    {
      role: "system",
      content: "Kamu adalah AI sekolah."
    },
    {
      role: "user",
      content: "Halo"
    }
  ]

  const data = await sendMessage(history)

  reply.value = data.reply
}
</script>

<template>
  <div style="padding:20px">

    <button @click="testApi">
      Test API
    </button>

    <p>{{ reply }}</p>

  </div>
</template>