<template>

<div class="typing">

<span></span>

<span></span>

<span></span>

</div>

</template>

<style scoped>

.typing{

display:flex;

gap:6px;

padding:16px;

}

span{

width:8px;

height:8px;

background:#5B7FFF;

border-radius:50%;

animation:typing 1s infinite;

}

span:nth-child(2){

animation-delay:.2s;

}

span:nth-child(3){

animation-delay:.4s;

}

@keyframes typing{

0%,100%{

transform:translateY(0);

opacity:.3;

}

50%{

transform:translateY(-5px);

opacity:1;

}

}

</style>