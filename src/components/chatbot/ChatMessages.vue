<script setup>

defineProps({
messages:Array
})

</script>

<template>

<div class="messages">

<div

v-for="(msg,index) in messages"

:key="index"

:class="['bubble',msg.role]"

>

{{msg.content}}

</div>

</div>

</template>

<style scoped>

.messages{

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