<template>
  <header class="chat-header">

    <button class="back-btn" @click="$router.push('/')">
    <i class="fas fa-angle-left"></i>
</button>

    <div class="info">

      <img src="/ch.png" class="avatar">

      <div>

        <h3>BISA</h3>

        <span class="status">
          <i class="dot"></i>
          onglen
        </span>

      </div>

    </div>

  </header>
</template>

<style scoped>
.status{
display:flex;
align-items:center;
gap:8px;
font-size:.9rem;
color:#666;
}

.dot{
width:8px;
height:8px;
border-radius:50%;
background:#22c55e;
box-shadow:0 0 12px #22c55e;

}
.chat-header{
  position: sticky;
  top: 0;
  display: flex;
  align-items: center;
  height: 72px;
  padding: 0 18px;
  color: black;
  background: rgba(255,255,255,.85);
  backdrop-filter: blur(18px);
  border-bottom:1px solid #eee;
  z-index:20;
}

.back-btn{
  position:absolute;
  left:18px;
  width:38px;
  height:38px;
  display:flex;
  align-items:center;
  justify-content:center;
  color: black;
  border:none;
  background:#fff;
  border-radius:50px;
  box-shadow:0 6px 20px rgba(0,0,0,.08);
  cursor:pointer;
}

.back-btn svg{
width:18px;
height:18px;
stroke: currentColor;
}
.back-btn:hover{
transform:scale(.95);
}

.info{
  margin:0 auto;
  display:flex;
  align-items:center;
  gap:14px;
}

.avatar{
width:50px;
height:50px;
border-radius:50px;
object-fit:cover;
box-shadow:0 10px 25px rgba(0,0,0,.12);
}

h3{
margin:0;
font-size:18px;
}
</style>