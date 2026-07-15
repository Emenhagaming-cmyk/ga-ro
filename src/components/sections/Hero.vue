<template>
  <section class="hero">

    <!-- Background -->
    <div class="grid"></div>
    <div class="blur blur1"></div>
    <div class="blur blur2"></div>

    <div class="container">

      <!-- LEFT -->
      <div class="left">

        <span class="badge">
          SMART SCHOOL
        </span>

        <h1>
          Pelajari.<br>
          Buat Project.<br>
          Berinnovasi.
        </h1>

        <div class="bg-word">
          SCHOOL
        </div>

        <p>
          Platform digital sekolah yang menghubungkan
          SPMB, Career Center, Produk Siswa,
          dan ChatBot dalam satu tempat.
        </p>

        <div class="buttons">

          <button class="primary">
            Jelajahi
          </button>

          <button class="secondary">
            SPMB
          </button>

        </div>

      </div>

      <!-- RIGHT -->
      <div class="right">

        <div class="ai-card">

          <div class="status">
            <span></span>
            ONLINE
          </div>

          <h2>ChatBot</h2>

          <p>
            Siap membantu menjawab seputar informasi sekolah
          </p>

        </div>

      </div>

    </div>

    <div class="scroll">

      Scroll

      <div class="line"></div>

    </div>

  </section>
</template>

<style scoped>
.secondary {
color: black;
}
.hero{

position:relative;

min-height:100vh;

overflow:hidden;

background:#F8FAFF;

display:flex;

align-items:center;

justify-content:center;

padding:120px 7%;

}

/* Background */

.grid{

position:absolute;

inset:0;

background-image:

linear-gradient(rgba(91,127,255,.05) 1px,transparent 1px),

linear-gradient(90deg,rgba(91,127,255,.05) 1px,transparent 1px);

background-size:70px 70px;

}

.blur{

position:absolute;

border-radius:50%;

filter:blur(80px);

opacity:.5;

}

.blur1{

width:280px;

height:280px;

background:#8BB8FF;

left:-80px;

top:150px;

}

.blur2{

width:320px;

height:320px;

background:#D5C7FF;

right:-120px;

bottom:100px;

}

/* Container */

.container{

position:relative;

z-index:2;

width:100%;

max-width:1300px;

display:grid;

grid-template-columns:1fr 420px;

gap:80px;

align-items:center;

}

/* Left */

.left{

position:relative;

}

.badge{

display:inline-block;

padding:10px 20px;

border-radius:999px;

background:#EDF2FF;

color:#5B7FFF;

font-size:14px;

font-weight:700;

letter-spacing:2px;

margin-bottom:30px;

}

h1{

font-size:92px;

line-height:.95;

margin:0;

color:#202330;

position:relative;

z-index:2;

}

.bg-word{

position:absolute;

top:70px;

left:-10px;

font-size:170px;

font-weight:800;

color:#5B7FFF;

opacity:.04;

pointer-events:none;

}

p{

margin-top:35px;

font-size:20px;

line-height:1.9;

max-width:620px;

color:#6B7280;

}

.buttons{

display:flex;

gap:20px;

margin-top:45px;

}

button{

height:60px;

padding:0 34px;

border-radius:18px;

font-size:18px;

cursor:pointer;

transition:.35s;

}

.primary{

border:none;

background:#5B7FFF;

color:white;

box-shadow:0 18px 40px rgba(91,127,255,.25);

}

.primary:hover{

transform:translateY(-4px);

}

.secondary{

background:white;

border:1px solid #E5E7EB;

}

/* AI Card */

.ai-card{

background:white;

padding:35px;

border-radius:30px;

box-shadow:0 20px 60px rgba(0,0,0,.08);

position:relative;

overflow:hidden;

}

.ai-card::before{

content:"";

position:absolute;

top:0;

left:0;

width:100%;

height:6px;

background:linear-gradient(90deg,#5B7FFF,#7BA8FF);

}

.status{

display:flex;

align-items:center;

gap:10px;

font-size:13px;

font-weight:700;

color:#6B7280;

}

.status span{

width:10px;

height:10px;

border-radius:50%;

background:#34D399;

}

.ai-card h2{

margin:25px 0 12px;

font-size:34px;

color:#202330;

}

.ai-card p{

font-size:17px;

margin:0;

line-height:1.8;

}

/* Scroll */

.scroll{

position:absolute;

bottom:30px;

left:50%;

transform:translateX(-50%);

display:flex;

flex-direction:column;

align-items:center;

font-size:13px;

letter-spacing:2px;

color:#6B7280;

}

.line{

margin-top:10px;

width:2px;

height:50px;

background:#5B7FFF;

animation:scroll 1.6s infinite;

}

@keyframes scroll{

0%{

transform:scaleY(0);

transform-origin:top;

}

50%{

transform:scaleY(1);

transform-origin:top;

}

100%{

transform:scaleY(0);

transform-origin:bottom;

}

}

/* Mobile */

@media(max-width:900px){

.hero{

padding:140px 24px 80px;

}

.container{

display:grid;

grid-template-columns:1fr;

gap:50px;

width:100%;

max-width:100%;

}

h1{

font-size:62px;

}

.bg-word{

font-size:110px;

top:60px;

}

p{

font-size:18px;

}

.buttons{

flex-direction:column;

}

button{

width:100%;

}

.right{

width:100%;

display:flex;

justify-content:center;

}

.ai-card{

width:100%;

}

.scroll{

display:none;

}

}

</style>