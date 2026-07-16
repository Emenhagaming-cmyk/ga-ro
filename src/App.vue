<script setup>
import CursorGlow from "./components/common/CursorGlow.vue";
import LoadingScreen from "./components/loading/LoadingScreen.vue"
import Navbar from "./components/layout/Navbar.vue"
import Hero from "./components/sections/Hero.vue"
import Feature from "./components/sections/feature.vue"
import Journey from "./components/sections/Journey.vue"
import BackgroundFX from "./components/common/BackgroundFX.vue"

</script>

<template>

<LoadingScreen/>
<BackgroundFX/>
<CursorGlow/>

<div class="page">

<Navbar/>

<Hero/>

<Journey/>
<Feature/>

</div>

</template>
<style>

*{
box-sizing:border-box;
}

html,
body,
#app{

margin:0;
padding:0;

width:100%;

overflow-x:hidden;

font-family:"Quicksand",sans-serif;

background:#F8FAFF;

}

.page{

width:100%;

overflow-x:hidden;

}

</style>