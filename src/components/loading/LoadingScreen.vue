<template>
  <div class="loading" :class="{ fadeOut }">
    <div class="brand">

      <div class="logo-wrapper" :class="{ up: logoUp }">
        <img src="/logo.png" class="logo" />
      </div>

      <div class="text" :class="{ show: showText }">
        <h1></h1>
        <p>smk bahrul ulum</p>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";

const logoUp = ref(false);
const showText = ref(false);
const fadeOut = ref(false);

onMounted(() => {

  // Logo naik
  setTimeout(() => {
    logoUp.value = true;
  }, 2200);

  // Text muncul
  setTimeout(() => {
    showText.value = true;
  }, 3000);

  // Text hilang
  setTimeout(() => {
    showText.value = false;
  }, 5600);

  // Logo turun
  setTimeout(() => {
    logoUp.value = false;
  }, 6300);

  // Loading hilang
  setTimeout(() => {
    fadeOut.value = true;
  }, 7600);

});
</script>

<style scoped>
.loading{
    position:fixed;
    inset:0;

    display:flex;
    justify-content:center;
    align-items:center;

    background:radial-gradient(circle at top left, rgba(125,184,141,.12), transparent 40%), #1c2a23;

    z-index:9999;

    transition:opacity 1.4s ease;

}

.loading.fadeOut{

    opacity:0;
    pointer-events:none;

}

.brand{

    display:flex;
    flex-direction:column;
    align-items:center;

}

.logo-wrapper{
    display:flex;
    justify-content:center;
    align-items:center;
    line-height:0;
    margin-bottom:-12px;
    transition:transform .9s cubic-bezier(.22,.61,.36,1);
}

.logo-wrapper.up{

    transform:translateY(-18px);

}

.logo{
    width:120px;
    display:block;
    opacity:0;
    animation:logoFade 1.8s forwards;
}

.text{
    opacity:0;
    margin-top:8px;
    transition:opacity 1.3s ease;
}

.text.show{

    opacity:1;

}

.text h1{
    margin:0;
    color:#fff;
    font-size:30px;
    font-weight:500;
    letter-spacing:2px;
    text-align:center;
    font-family: 'Quicksand', sans-serif;

}

.text p{

    margin:6px 0 0;
    color:rgba(255,255,255,.55);
    font-size:13px;
    letter-spacing:3px;
    text-align:center;
    font-family: 'Quicksand', sans-serif;
}

@keyframes logoFade{

    from{

        opacity:0;
        transform:scale(.96);

    }

    to{

        opacity:1;
        transform:scale(1);

    }

}

</style>