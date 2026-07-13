<template>
  <section class="portal">

    <div class="heading">
      <span>Digital Ecosystem</span>
      <h2>Semua Layanan Dalam Satu Platform</h2>
      <p>
        Website sekolah yang menghubungkan PPDB, Career Center,
        Produk Siswa, dan AI Assistant dalam satu ekosistem digital.
      </p>
    </div>

    <div class="cards">

      <div class="card">
        <div class="icon">🎓</div>
        <h3>SPMB Digital</h3>
        <p>Sistem Penerimaan Siswa Baru secara online.</p>
        <button>Daftar</button>
      </div>

      <div class="card">
        <div class="icon">🛍️</div>
        <h3>Produk Siswa</h3>
        <p>Lihat karya dan produk hasil siswa.</p>
        <button>Lihat →</button>
      </div>

      <div class="card">
        <div class="icon">💼</div>
        <h3>Career Center</h3>
        <p>Informasi PKL dan lowongan kerja.</p>
        <button>Buka →</button>
      </div>

      <div class="card">
        <div class="icon">🤖</div>
        <h3>ChatBot</h3>
        <p>Punya pertanyaan terkait sekolah? ChatBot siap bantu menjawab</p>
        <button>Chat</button>
      </div>

    </div>

  </section>
</template>

<style scoped>

.portal{

padding:120px 8%;

background:#0c0c0c;

color:white;

}

.heading{

text-align:center;

max-width:760px;

margin:auto auto 70px;

}

.heading span{

color:#d62b2b;

letter-spacing:3px;

font-size:13px;

text-transform:uppercase;

}

.heading h2{

margin:18px 0;

font-size:46px;

font-weight:600;

}

.heading p{

color:#9b9b9b;

line-height:1.8;

}

.cards{

display:grid;

grid-template-columns:repeat(4,1fr);

gap:24px;

}

.card{

background:rgba(255,255,255,.03);

backdrop-filter:blur(18px);

border:1px solid rgba(255,255,255,.08);

border-radius:28px;

padding:32px;

transition:.45s;

overflow:hidden;

position:relative;

}

.card:hover{

transform:translateY(-10px);

border-color:rgba(214,40,40,.4);

box-shadow:

0 20px 60px rgba(214,40,40,.08);

}
.card::before{

content:"";

position:absolute;

left:0;

top:0;

width:0;

height:2px;

background:#D62828;

transition:.5s;

}

.card:hover::before{

width:100%;

}
.icon{

font-size:42px;

margin-bottom:20px;

}

.card h3{

margin-bottom:12px;

}

.card p{

color:#999;

line-height:1.7;

margin-bottom:28px;

}

.card button{

background:none;

border:none;

color:white;

font-weight:600;

cursor:pointer;

}

@media(max-width:900px){

.cards{

grid-template-columns:1fr;

}

.heading h2{

font-size:34px;

}

}

</style>