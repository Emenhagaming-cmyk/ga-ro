<template>

<header
class="navbar"
:class="{ shrink: scrolled }"
>

<div class="logo">

  <img
    src="/public/logo.png"
    alt="Logo Sekolah"
    class="logo-img"
  />

  <div>

    <h2>SMK Bahrul Ulum</h2>

    <span>Smart School</span>

  </div>

</div>
    
<nav>

<a href="#">Home</a>

<a href="#">Services</a>

<a href="#">Journey</a>

<a href="#">Gallery</a>

<a href="#">Contact</a>

</nav>

<button class="ppdb">

SPMB

</button>

<div class="menu">

☰

</div>

</header>

</template>

<script setup>

import{

ref,

onMounted,

onUnmounted

}from"vue"

const scrolled=ref(false)

const scroll=()=>{

scrolled.value=window.scrollY>80

}

onMounted(()=>{

window.addEventListener("scroll",scroll)

})

onUnmounted(()=>{

window.removeEventListener("scroll",scroll)

})

</script>

<style scoped>


.menu {
  color: black;
}
.navbar{

position:fixed;

left:50%;

top:24px;

transform:translateX(-50%);

width:min(1180px,92%);

height:74px;

padding:0 24px;

background:rgba(255,255,255,.7);

backdrop-filter:blur(18px);

display:flex;

align-items:center;

justify-content:space-between;

border-radius:28px;

border:1px solid rgba(255,255,255,.5);

box-shadow:

0 12px 35px rgba(30,40,70,.08);

transition:.35s;

z-index:999;

}

.navbar.shrink{

height:62px;

width:min(980px,88%);

border-radius:999px;

}

.logo{

display:flex;

align-items:center;

gap:14px;

}

.logo-img{

width:50px;

height:50px;

object-fit:contain;

transition:.3s;

}

.logo-img:hover{

transform:rotate(-5deg) scale(1.05);

}

.logo h2{

margin:0;

font-size:20px;

font-weight:800;

color:#202330;

}

.logo span{

font-size:13px;

color:#7B8798;

}

nav{

display:flex;

gap:34px;

}

nav a{

text-decoration:none;

color:#5B6475;

font-weight:600;

transition:.25s;

}

nav a:hover{

color:#5B7FFF;

}

.ppdb{

height:48px;

padding:0 24px;

border:none;

border-radius:16px;

background:#5B7FFF;

color:white;

font-weight:700;

cursor:pointer;

transition:.3s;

}

.ppdb:hover{

transform:translateY(-3px);

}

.menu{

display:none;

font-size:28px;

cursor:pointer;

}

@media(max-width:900px){

nav{

display:none;

}

.ppdb{

display:none;

}

.menu{

display:block;

}

.logo h2{

font-size:18px;

}

.navbar{

height:66px;

padding:0 18px;

width:92%;

}

}

</style>