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

<button @click="send">

➜

</button>

</div>

</template>

<style scoped>

.input-box{

display:flex;

padding:16px;

gap:12px;

background:white;

border-top:1px solid #EEF2F7;

}

input{

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