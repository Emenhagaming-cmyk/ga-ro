<template>

<section class="feature">

<div class="heading">

<span>SERVICE KAMI</span>

<h2>
Satu platform,
Many Possibilities.
</h2>

<p>
Semua layanan sekolah terintegrasi
dalam satu website modern.
</p>
</div>

<div class="grid">

<div
class="card"
v-for="item in items"
:key="item.title"
>

<div class="layer"></div>
<div class="layer second"></div>

<div class="content">

<div class="top">

<div class="icon">

<component
:is="item.icon"
:size="28"
/>

</div>

<div class="badge">

{{item.badge}}

</div>

</div>

<h3>

{{item.title}}

</h3>

<p>

{{item.desc}}

</p>

<a href="#">

Explore 

</a>

</div>

</div>

</div>

</section>

</template>

<script setup>

import{

Bot,

GraduationCap,

BriefcaseBusiness,

ShoppingBag

}from"lucide-vue-next"

const items=[

{

title:"SPMB",

badge:"BUKA",

desc:"Sistem Penerimaan Didik Siswa Baru melalui online. Tertarik? daftar sekarang!",

icon:GraduationCap

},

{

title:"ChatBot",

badge:"ONLINE",

desc:"ChatBot hadir untuk membantu anda menjawab pertanyaan tentang informasi sekolah.",

icon:Bot

},

{

title:"Karir Center",

badge:"AKTIF",

desc:"Informasi PKL dan lowongan kerja ada disini.",

icon:BriefcaseBusiness

},

{

title:"Produk Siswa",

badge:"STORE",

desc:"Galeri hasil karya siswa smk bahrul ulum.",

icon:ShoppingBag

}

]

</script>

<style scoped>
  .feature{

padding:120px 7%;

background:#F8FAFF;

}

.heading{

text-align:center;

max-width:700px;

margin:auto;

margin-bottom:70px;

}

.heading span{

color:#5B7FFF;

font-weight:700;

letter-spacing:3px;

font-size:13px;

}

.heading h2{

font-size:52px;

margin:18px 0;

color:#202330;

}

.heading p{

color:#6B7280;

font-size:18px;

line-height:1.8;

}

.grid{

display:grid;

grid-template-columns:repeat(2,1fr);

gap:32px;

}

.card{

position:relative;

min-height:260px;

}

.layer{

position:absolute;

inset:0;

border-radius:28px;

background:#DCE7FF;

transform:translate(14px,14px);

opacity:.35;

transition:.35s;

}

.second{

transform:translate(7px,7px);

opacity:.6;

}

.content{

position:relative;

display:flex;

flex-direction:column;

height:100%;

background:white;

border-radius:28px;

padding:34px;

box-sizing:border-box;

box-shadow:
0 20px 50px rgba(0,0,0,.07);

border:1px solid #EDF1FA;

transition:.35s;

}

.card:hover .content{

transform:

translateY(-10px)

rotate(-1deg);

}

.card:hover .layer{

transform:translate(20px,20px);

}

.card:hover .second{

transform:translate(10px,10px);

}

.top{

display:flex;

justify-content:space-between;

align-items:center;

margin-bottom:28px;

}

.icon{

width:60px;

height:60px;

display:flex;

align-items:center;

justify-content:center;

background:#EEF3FF;

border-radius:18px;

color:#5B7FFF;

}

.badge{

padding:8px 14px;

background:#EEF3FF;

border-radius:999px;

font-size:12px;

font-weight:700;

color:#5B7FFF;

}

h3{

margin:0;

font-size:30px;

color:#202330;

}

p{

margin:18px 0 28px;

line-height:1.8;

color:#6B7280;

}

a{

margin-top:auto;

display:inline-flex;

align-items:center;

gap:8px;

font-weight:700;

color:#5B7FFF;

text-decoration:none;

transition:.3s;

}

a:hover{

transform:translateX(6px);

}

@media(max-width:900px){

.feature{

padding:80px 24px;

}

.heading h2{

font-size:38px;

}

.grid{

grid-template-columns:1fr;

gap:24px;

}

.card{

height:auto;

}

.content{

padding:28px;

}

}
</style>
  