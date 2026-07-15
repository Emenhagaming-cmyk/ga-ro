<template>
  <div class="bg">

    <div class="grid"></div>

    <div
      v-for="dot in dots"
      :key="dot.id"
      class="dot"
      :style="{
        left: dot.x + '%',
        top: dot.y + '%',
        animationDelay: dot.delay + 's'
      }"
    />

    <div class="orbit orbit1"></div>
    <div class="orbit orbit2"></div>

  </div>
</template>

<script setup>
const dots = [
  { id:1,x:12,y:18,delay:0 },
  { id:2,x:24,y:65,delay:1 },
  { id:3,x:41,y:33,delay:2 },
  { id:4,x:58,y:72,delay:3 },
  { id:5,x:71,y:21,delay:1.5 },
  { id:6,x:84,y:48,delay:2.5 },
  { id:7,x:33,y:87,delay:4 },
  { id:8,x:91,y:76,delay:3.5 }
]
</script>

<style scoped>

.bg{

position:fixed;
inset:0;

overflow:hidden;

pointer-events:none;

z-index:-2;

background:#F8FAFF;

}

/* GRID */

.grid{

position:absolute;
inset:0;

background-image:

linear-gradient(rgba(91,127,255,.05) 1px,transparent 1px),

linear-gradient(90deg,rgba(91,127,255,.05) 1px,transparent 1px);

background-size:70px 70px;

}

/* DOT */

.dot{

position:absolute;

width:7px;
height:7px;

border-radius:50%;

background:#5B7FFF;

opacity:.18;

animation:

floating 12s ease-in-out infinite;

}

/* ORBIT */

.orbit{

position:absolute;

border:

1px solid rgba(91,127,255,.08);

border-radius:50%;

animation:

rotate 60s linear infinite;

}

.orbit1{

width:700px;
height:700px;

left:-250px;
top:-150px;

}

.orbit2{

width:900px;
height:900px;

right:-350px;
bottom:-350px;

animation-direction:reverse;

}

/* ANIMATION */

@keyframes floating{

0%{

transform:
translateY(0)
scale(1);

}

50%{

transform:
translateY(-25px)
scale(1.5);

opacity:.35;

}

100%{

transform:
translateY(0)
scale(1);

}

}

@keyframes rotate{

from{

transform:rotate(0deg);

}

to{

transform:rotate(360deg);

}

}

@media(max-width:768px){

.orbit{

display:none;

}

.grid{

background-size:45px 45px;

}

}

</style>