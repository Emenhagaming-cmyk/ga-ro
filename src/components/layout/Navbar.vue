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
    
<nav class="desktop-nav">

<a href="#">Home</a>

<a href="#">Services</a>

<a href="#">Journey</a>

<a href="#">Gallery</a>

<a href="#">Contact</a>

</nav>

<button class="ppdb">

SPMB

</button>

<button class="menu" :class="{ active: menuOpen }" type="button" aria-label="Buka menu" :aria-expanded="menuOpen" @click="toggleMenu">
  <span></span>
  <span></span>
  <span></span>
</button>

<Transition name="mobile-menu">
  <nav v-if="menuOpen" class="mobile-nav">
    <a href="#" @click="closeMenu">Home</a>
    <a href="#" @click="closeMenu">Services</a>
    <a href="#" @click="closeMenu">Journey</a>
    <a href="#" @click="closeMenu">Gallery</a>
    <a href="#" @click="closeMenu">Contact</a>
    <a href="#" class="mobile-ppdb" @click="closeMenu">SPMB</a>
  </nav>
</Transition>

</header>

</template>

<script setup>

import{

ref,

onMounted,

onUnmounted

}from"vue"

const scrolled=ref(false)
const menuOpen=ref(false)

const toggleMenu=()=>{
  menuOpen.value=!menuOpen.value
}

const closeMenu=()=>{
  menuOpen.value=false
}

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

.desktop-nav{

display:flex;

gap:34px;

}

.desktop-nav a{

text-decoration:none;

color:#5B6475;

font-weight:600;

transition:.25s;

}

.desktop-nav a:hover{

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
  width:42px;
  height:42px;
  padding:0;
  border:1px solid #dfe4dd;
  border-radius:12px;
  background:#fff;
  cursor:pointer;
}

.menu span{
  display:block;
  width:19px;
  height:2px;
  margin:4px auto;
  border-radius:999px;
  background:#1c2a23;
  transition:transform .3s ease, opacity .2s ease;
}

.menu.active span:nth-child(1){
  transform:translateY(6px) rotate(45deg);
}

.menu.active span:nth-child(2){
  opacity:0;
}

.menu.active span:nth-child(3){
  transform:translateY(-6px) rotate(-45deg);
}

.mobile-nav{
  display:none;
}

.mobile-menu-enter-active,
.mobile-menu-leave-active{
  transition:opacity .25s ease, transform .25s ease;
}

.mobile-menu-enter-from,
.mobile-menu-leave-to{
  opacity:0;
  transform:translateY(-12px);
}

@media(max-width:900px){

.desktop-nav{
  display:none;
}

.ppdb{
  display:none;
}

.menu{
  display:block;
}

.mobile-nav{
  position:absolute;
  top:calc(100% + 12px);
  right:0;
  left:0;
  display:flex;
  flex-direction:column;
  gap:4px;
  padding:12px;
  border:1px solid rgba(223,228,221,.9);
  border-radius:20px;
  background:rgba(251,252,250,.97);
  box-shadow:0 18px 38px rgba(35,55,42,.12);
  backdrop-filter:blur(18px);
}

.mobile-nav a{
  padding:13px 15px;
  border-radius:12px;
  color:#1c2a23;
  font-size:15px;
  font-weight:700;
  text-decoration:none;
  transition:background .2s ease, color .2s ease;
}

.mobile-nav a:hover{
  background:#e8f0e6;
  color:#3a6450;
}

.mobile-nav .mobile-ppdb{
  margin-top:4px;
  background:#3a6450;
  color:#fff;
  text-align:center;
}

.mobile-nav .mobile-ppdb:hover{
  background:#2a5238;
  color:#fff;
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