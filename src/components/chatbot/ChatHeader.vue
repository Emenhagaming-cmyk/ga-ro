<template>
  <header class="header">

    <button class="back" @click="$router.back()">
      ←
    </button>

    <div class="info">

      <img src="/cet.png" class="avatar">

      <div>

        <h3>AI Sekolah</h3>

        <span>
          <i></i>
          Online
        </span>

      </div>

    </div>

  </header>
</template>

<style scoped>

.header{

height:70px;

padding:0 18px;

display:flex;

align-items:center;

gap:16px;

background:white;

border-bottom:1px solid #EEF2F7;

}

.back{

border:none;

background:none;

font-size:28px;

cursor:pointer;

}

.info{

display:flex;

align-items:center;

gap:14px;

}

.avatar{

width:48px;

height:48px;

border-radius:16px;

object-fit:cover;

}

h3{

margin:0;

font-size:18px;

}

span{

display:flex;

align-items:center;

gap:6px;

font-size:13px;

color:#777;

}

i{

width:8px;

height:8px;

background:#22C55E;

border-radius:50%;

}

</style>