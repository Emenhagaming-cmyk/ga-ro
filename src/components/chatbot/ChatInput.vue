<script setup>

import { ref } from "vue"

const text = ref("")

const emit = defineEmits(["send"])

function send(){

if(!text.value.trim()) return

emit("send",text.value)

text.value=""

}

</script>

<template>

<div class="input-box">

<input

v-model="text"

placeholder="Ketik pesan..."

@keyup.enter="send"

/>

<button class="send" @click="send">

<svg
viewBox="0 0 24 24"
fill="none"
stroke="currentColor"
stroke-width="2">

<path
d="M22 2L11 13"/>

<path
d="M22 2L15 22L11 13L2 9L22 2"/>

</svg>

</button>

</div>

</template>

<style scoped>
.send{

width:52px;
height:52px;

border:none;

border-radius:50%;

background:#5B7FFF;

color:white;

display:flex;
align-items:center;
justify-content:center;

cursor:pointer;

transition:.25s;

}

.send svg{

width:22px;
height:22px;

}

.send:hover{

transform:scale(.95);

background:#4f46e5;

}
.input-box{

display:flex;

padding:16px;

gap:12px;

background:white;

border-top:1px solid #EEF2F7;

}

input{
color: black;
flex:1;

height:52px;

border:none;

outline:none;

background:#F5F7FC;

border-radius:16px;

padding:0 18px;

font-size:15px;

}

button{

width:52px;

border:none;

border-radius:16px;

background:#5B7FFF;

color:white;

cursor:pointer;

}

</style>