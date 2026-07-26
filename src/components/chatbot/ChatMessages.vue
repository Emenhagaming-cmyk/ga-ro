<script setup>
import { marked } from "marked"
import school from "@/config/school"

defineProps({
  messages:Array
})

function renderMessage(text){

  // Instagram
  text = text.replace(
    new RegExp(`@${school.instagram}`, "gi"),
    `[@${school.instagram}](https://instagram.com/${school.instagram})`
  )

  // Email
  text = text.replace(
    new RegExp(school.email.replace(".", "\\."), "gi"),
    `[${school.email}](mailto:${school.email})`
  )

  // Nomor Telepon
  text = text.replace(
    /\(031\)\s*9953\s*9925/g,
    `[${school.phone}](tel:${school.phone.replace(/\D/g,"")})`
  )

  return marked(text)

}
</script>
<template>

<div class="messages">

<div

v-for="(msg,index) in messages"

:key="index"

:class="['bubble',msg.role]"

>
<div v-html="renderMessage(msg.content)"></div>

</div>

</div>

</template>

<style scoped>
.assistant a{
  color:#5B7FFF;
  font-weight:600;
  text-decoration:none;
}

.assistant a:hover{
  text-decoration:underline;
}
.assistant :deep(a){
  color:#2F80ED;
  font-weight:600;
  text-decoration:none;
  transition:.2s;
}

.assistant :deep(a:hover){
  text-decoration:underline;
}
.messages{
-webkit-overflow-scrolling:touch;
padding-bottom:20px;
flex:1;
padding:20px;
display:flex;
flex-direction:column;
gap:14px;
overflow-y:auto;
}

.bubble{

max-width:75%;

padding:14px 18px;

border-radius:20px;

animation:show .25s;

word-break:break-word;

}

.user{

background:#5B7FFF;

color:white;

align-self:flex-end;

}

.assistant{
color: black;
background:white;

border:1px solid #EEF2F7;

align-self:flex-start;

}

@keyframes show{

from{

opacity:0;

transform:translateY(10px);

}

to{

opacity:1;

transform:none;

}

}

</style>