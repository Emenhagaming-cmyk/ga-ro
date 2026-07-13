<template>

<div class="cards">

    <div class="card big">

        <div class="dot"></div>

        <h3>ChatBot</h3>

        <p>Punya pertanyaan?</p>

    </div>

    <div class="card small">

        <h3>SPMB</h3>

    </div>

    <div class="card small right">

        <h3>Career</h3>

    </div>

</div>

</template>

<style scoped>

.cards{

position:relative;

width:420px;

height:420px;

}

.card{

position:absolute;

background:white;

border-radius:24px;

padding:26px;

box-shadow:

0 12px 28px rgba(20,40,80,.08);

border:2px solid #eef1f7;

transition:.4s;

animation:float 5s ease-in-out infinite;

}

.card:hover{

transform:translateY(-10px) rotate(-2deg);

}

.big{

width:280px;

height:140px;

left:70px;

top:70px;

}

.small{

width:170px;

height:100px;

left:0;

bottom:40px;

animation-delay:1s;

}

.right{

left:auto;

right:0;

bottom:0;

animation-delay:2s;

}

.dot{

width:12px;

height:12px;

border-radius:50%;

background:#58d68d;

margin-bottom:16px;

}

h3{

margin:0;

color:#2b2f3a;

}

p{

color:#7b8598;

}

@keyframes float{

50%{

transform:translateY(-12px);

}

}

@media (max-width:768px){

.cards{

position:static;

width:100%;

height:auto;

display:flex;

flex-direction:column;

gap:18px;

margin-top:40px;

padding:0;

}

.card{

position:static;

width:100%;

height:auto;

transform:none !important;

animation:none;

}

.big,
.small,
.right{

position:static;

width:100%;

height:auto;

left:auto;

right:auto;

top:auto;

bottom:auto;

}

}

</style>