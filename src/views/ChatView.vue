<script setup>

import { ref } from "vue"

import ChatHeader from "@/components/chatbot/ChatHeader.vue"
import ChatMessages from "@/components/chatbot/ChatMessages.vue"
import ChatInput from "@/components/chatbot/ChatInput.vue"
import TypingIndicator from "@/components/chatbot/TypingIndicator.vue"

const messages = ref([
{
role:"assistant",
content:"Halo 👋 Ada yang bisa saya bantu?"
}
])

const loading = ref(false)

async function send(text){

messages.value.push({
role:"user",
content:text
})

loading.value=true

setTimeout(()=>{

messages.value.push({
role:"assistant",
content:"Ini masih dummy response 😎"
})

loading.value=false

},1000)

}

</script>

<template>

<div class="chat-page">

<ChatHeader/>

<ChatMessages
:messages="messages"
/>

<TypingIndicator
v-if="loading"
/>

<ChatInput
@send="send"
/>

</div>

</template>

<style scoped>

.chat-page{

height:100vh;

display:flex;

flex-direction:column;

background:#F8FAFF;

}

</style>