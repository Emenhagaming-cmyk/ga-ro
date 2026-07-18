<script setup>
import { ref } from "vue"

import ChatHeader from "@/components/chat/ChatHeader.vue"
import ChatInput from "@/components/chat/ChatInput.vue"

import { sendMessage } from "@/services/chat"

const messages = ref([
  {
    role: "assistant",
    content: "Halo 👋 Saya BISA. Ada yang bisa saya bantu hari ini?"
  }
])

async function handleSend(text){

  // tampilkan pesan user
  messages.value.push({
    role:"user",
    content:text
  })

  try{

    const data = await sendMessage(text)

    messages.value.push({
      role:"assistant",
      content:data.reply
    })

  }catch(e){

    messages.value.push({
      role:"assistant",
      content:"Maaf, BISA sedang mengalami gangguan."
    })

  }

}
</script>

<template>

<div class="chat-page">

  <ChatHeader/>

  <div class="messages">

    <div
      v-for="(msg,index) in messages"
      :key="index"
      :class="['bubble',msg.role]"
    >

      {{ msg.content }}

    </div>

  </div>

  <ChatInput @send="handleSend"/>

</div>

</template>