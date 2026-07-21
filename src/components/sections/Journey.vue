<template>

<section class="journey">

<div class="title">

<span>JURNEY</span>

<h2>
Dari belajar menjadi <b>Profesional</b>
</h2>

<p>
Perjalananmu dimulai dari sini.
Setiap langkah membawa pengalaman baru.
</p>

</div>

<div class="timeline">

<div
v-for="(item,index) in journey"
:key="item.title"
class="step"
:class="{right:index%2}"
>

<div class="dot"></div>

<div class="card">

<span>{{item.tag}}</span>

<h3>{{item.title}}</h3>

<p>{{item.desc}}</p>

</div>

</div>

</div>

</section>

</template>

<style scoped>
  *{
box-sizing:border-box;
}
.journey{
overflow:hidden;
padding:140px 7%;
position:relative;
}

.title{

text-align:center;

max-width:700px;

margin:auto;

margin-bottom:90px;

}

.title span{

color:#5B7FFF;

font-weight:700;

letter-spacing:3px;

font-size:13px;

}

.title h2{

font-size:54px;

margin:20px 0;

color:#202330;

}

.title p{

color:#6B7280;

line-height:1.8;

font-size:18px;

}

.timeline{
overflow:hidden;
position:relative;
max-width:900px;
margin:auto;
}

.timeline::before{

content:"";

position:absolute;

left:50%;

top:0;

width:3px;

height:100%;

background:#DDE6FF;

transform:translateX(-50%);

}

.step{

position:relative;

display:flex;

justify-content:flex-start;

margin:70px 0;

}

.step.right{

justify-content:flex-end;

}

.dot{

position:absolute;

left:50%;

top:40px;

transform:translate(-50%,-50%);

width:20px;

height:20px;

background:#5B7FFF;

border:6px solid white;

border-radius:50%;

box-shadow:0 0 0 6px #EEF3FF;

z-index:5;

}

.card{

width: 100%;
max-width: 360px;
background:white;
box-sizing: border-box;
padding:30px;

border-radius:24px;

box-shadow:

0 15px 40px rgba(0,0,0,.08);

transition:.35s;

}

.card:hover{

transform:translateY(-8px);

}

.card span{

color:#5B7FFF;

font-size:13px;

font-weight:700;

letter-spacing:2px;

}

.card h3{

margin:14px 0;

font-size:30px;

color:#202330;

}

.card p{

color:#6B7280;

line-height:1.8;

}

@media(max-width:900px){

.timeline::before{

left:18px;

}

.step,
.step.right{

justify-content:flex-start;

padding-left:40px;

padding-right:16px;

}

.dot{

left:18px;

}

.card{

width:100%;

}

.title h2{

font-size:38px;

}

}
</style>

<script setup>

const journey=[

{

tag:"STEP 01",

title:"SPMB",

desc:"Daftar sekolah dengan sangat mudah lewat online"

},

{

tag:"STEP 02",

title:"Belajar",

desc:"Mempelajari banyak hal disekolah, mulai dari belajar coding, robotik dan sebagainya"

},

{

tag:"STEP 03",

title:"Project",

desc:"Bangun project mu, dan mulailah mencoba membuat portofolio"

},

{

tag:"STEP 04",

title:"PKL",

desc:"Magang langsung bersama industri ternama yaitu maspion."

},

{

tag:"STEP 05",

title:"karir",

desc:"Siap bekerja maupun melanjutkan kuliah."

}

]

</script>