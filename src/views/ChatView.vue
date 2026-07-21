<script setup>
import { ref, nextTick } from "vue"
import ChatHeader from "@/components/chatbot/ChatHeader.vue"
import ChatMessages from "@/components/chatbot/ChatMessages.vue"
import ChatInput from "@/components/chatbot/ChatInput.vue"
import TypingIndicator from "@/components/chatbot/TypingIndicator.vue"
import { sendMessage } from "@/services/chat.js"

const messageBox = ref(null)

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
  await nextTick()

messageBox.value?.$el.scrollTo({
  top: messageBox.value.$el.scrollHeight,
  behavior: "smooth"
})
  }catch(e){

    messages.value.push({
      role:"assistant",
      content:"Maaf, BISA sedang mengalami gangguan."
    })
      await nextTick()

messageBox.value?.$el.scrollTo({
  top: messageBox.value.$el.scrollHeight,
  behavior: "smooth"
})
  }

}
</script>

<template>

<div class="chat-page">

  <ChatHeader/>

  <ChatMessages ref="messageBox" :messages="messages" />
<TypingIndicator v-if="typing" />
  <ChatInput @send="handleSend"/>

</div>

</template>
<style scoped>

.chat-page{
position:fixed;
top:0;
left:0;
right:0;
bottom:0;
display:flex;
flex-direction:column;
background:#F5F7FC;
overflow:hidden;
}

</style>