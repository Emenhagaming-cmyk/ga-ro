<template>
  <section class="stats">

    <div class="title">
      <span>OUR ACHIEVEMENT</span>
      <h2>Dipercaya Membangun Generasi Berkualitas</h2>
    </div>

    <div class="stats-grid">

      <div class="item">
        <h3>1200+</h3>
        <p>Siswa Aktif</p>
      </div>

      <div class="item">
        <h3>98%</h3>
        <p>Lulusan Bekerja / Kuliah</p>
      </div>

      <div class="item">
        <h3>35+</h3>
        <p>Mitra Industri</p>
      </div>

      <div class="item">
        <h3>150+</h3>
        <p>Prestasi</p>
      </div>

    </div>

  </section>
</template>

<style scoped>

.stats{

padding:120px 8%;

background:#090909;

color:white;

}

.title{

text-align:center;

margin-bottom:70px;

}

.title span{

color:#d62b2b;

letter-spacing:4px;

font-size:13px;

}

.title h2{

margin-top:18px;

font-size:42px;

font-weight:600;

}

.stats-grid{

display:grid;

grid-template-columns:repeat(4,1fr);

gap:30px;

}

.item{

padding:45px 20px;

border-top:2px solid #d62b2b;

background:#111;

text-align:center;

border-radius:18px;

transition:.35s;

}

.item:hover{

transform:translateY(-8px);

background:#171717;

}

.item h3{

font-size:52px;

margin-bottom:10px;

}

.item p{

color:#999;

}

@media(max-width:900px){

.stats-grid{

grid-template-columns:repeat(2,1fr);

}

}

@media(max-width:600px){

.stats-grid{

grid-template-columns:1fr;

}

.title h2{

font-size:32px;

}

}

</style>