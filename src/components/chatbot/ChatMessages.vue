<script setup>
import { marked } from "marked"

marked.use({
  renderer: {
    link({ href, title, tokens }) {
      const text = this.parser.parseInline(tokens)

      return `
        <a
          href="${href}"
          target="_blank"
          rel="noopener noreferrer"
          ${title ? `title="${title}"` : ""}
        >
          ${text}
        </a>
      `
    }
  }
})

defineProps({
  messages: Array
})

</script>

<template>

<div class="messages">

<div

v-for="(msg,index) in messages"

:key="index"

:class="['bubble',msg.role]"

>
<div v-html="marked(msg.content)"></div>

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