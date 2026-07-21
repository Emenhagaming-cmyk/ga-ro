<template>

<button
class="floating-ai"
@click="openChat"
>

<div class="ring"></div>

<img
src="/ch.png"
alt="BU AI"
class="chat-logo"
/>

<span class="online"></span>

</button>

</template>

<script setup>

import { useRouter } from "vue-router"

const router = useRouter()

function openChat(){

router.push("/chat")

}

</script>

<style scoped>

.floating-ai{

position:fixed;

right:24px;

bottom:24px;

width:70px;

height:70px;

border:none;

border-radius:22px;

background:rgba(255,255,255,.85);

backdrop-filter:blur(16px);

-webkit-backdrop-filter:blur(16px);

display:flex;

justify-content:center;

align-items:center;

cursor:pointer;

overflow:hidden;

z-index:9999;

border:1px solid rgba(255,255,255,.6);

box-shadow:
0 15px 35px rgba(91,127,255,.18);

transition:.35s;

}

.floating-ai:hover{

transform:
translateY(-5px)
scale(1.05);

box-shadow:
0 20px 45px rgba(91,127,255,.35);

}

.chat-logo{

width:52px;

height:52px;

object-fit:contain;

position:relative;

z-index:2;

transition:.35s;

}

.floating-ai:hover .chat-logo{

transform:scale(1.08);

}

.online{

position:absolute;

top:8px;

right:8px;

width:10px;

height:10px;

background:#34D399;

border-radius:50%;

border:2px solid white;

animation:blink 2s infinite;

}

.ring{

position:absolute;

inset:0;

background:linear-gradient(
135deg,
rgba(91,127,255,.12),
transparent,
rgba(91,127,255,.08)
);

opacity:0;

transition:.3s;

}

.floating-ai:hover .ring{

opacity:1;

}

@keyframes blink{

0%,100%{

opacity:1;

}

50%{

opacity:.4;

}

}

@media(max-width:768px){

.floating-ai{

right:18px;

bottom:18px;

width:64px;

height:64px;

}

.chat-logo{

width:48px;

height:48px;

}

}

</style>