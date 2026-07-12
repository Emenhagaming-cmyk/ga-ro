<template>
  <LoadingScreen />

  <main class="page">

    <h1></h1>

  </main>
</template>

<script setup>
import LoadingScreen from "./components/loading/LoadingScreen.vue";
</script>

<style>

body{

margin:0;
background:#090909;
font-family:Poppins,sans-serif;

}

.page{

height:100vh;

display:flex;
justify-content:center;
align-items:center;

color:white;

opacity:0;

animation:pageFade 1s forwards;
animation-delay:7.8s;

}

@keyframes pageFade{

to{

opacity:1;

}

}

</style>