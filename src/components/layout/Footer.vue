<script setup>
defineEmits(['openContact'])
</script>

<template>
  <footer class="site-footer" id="contact">
    <div class="container">
      <div class="footer-content">
        <div class="footer-col col-brand">
          <div class="brand-section">
            <img src="/logo.png" alt="Logo SMK Bahrul Ulum" class="logo" width="32" height="32" />
            <div class="brand-info">
              <h3 class="footer-brand">SMK Bahrul Ulum</h3>
              <p class="tagline">Sekolah Expert IT</p>
              <p class="description">Mencetak Generasi Qur'ani dan Unggul di Bidang Teknologi Informasi dengan komitmen penuh terhadap pengembangan karakter, integritas, dan keunggulan akademik untuk mempersiapkan generasi pemimpin masa depan.</p>
            </div>
          </div>

          <nav class="footer-nav">
            <a href="#top">Beranda</a>
            <a href="#layanan">Layanan</a>
            <a href="#tentang">Tentang</a>
            <a href="#contact">Kontak</a>
          </nav>
        </div>

        <div class="footer-col col-info">
          <h4 class="footer-title">Informasi Kontak</h4>
          <a href="mailto:info@smk-bahrululum.sch.id" class="contact-link">
            <svg viewBox="0 0 512 512" width="18" height="18" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M48 64C21.5 64 0 85.5 0 112c0 15.1 7.1 29.3 19.2 38.4L236.8 313.6c11.4 8.5 27 8.5 38.4 0L492.8 150.4c12.1-9.1 19.2-23.3 19.2-38.4c0-26.5-21.5-48-48-48H48zM0 176V384c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V176L294.4 339.2c-22.8 17.1-54 17.1-76.8 0L0 176z"/></svg>
            <span>info@smk-bahrululum.sch.id</span>
          </a>
          <button class="contact-btn" @click="$emit('openContact')">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
            </svg>
            Hubungi Kami
          </button>
          <div class="social-section">
            <p class="social-label">Ikuti kami:</p>
            <div class="social-links">
              <a href="https://www.instagram.com/smkbu.sby?igsh=Mmlnemt1dTM5Ymcz" target="_blank" rel="noopener" class="social-icon" title="Instagram">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M7.0301.084c-1.2768.0602-2.1487.264-2.911.5634-.7888.3075-1.4575.72-2.1228 1.3877-.6652.6677-1.075 1.3368-1.3802 2.127-.2954.7638-.4956 1.6365-.552 2.914-.0564 1.2775-.0689 1.6882-.0626 4.947.0062 3.2586.0206 3.6671.0825 4.9473.061 1.2765.264 2.1482.5635 2.9107.308.7889.72 1.4573 1.388 2.1228.6679.6655 1.3365 1.0743 2.1285 1.38.7632.295 1.6361.4961 2.9134.552 1.2773.056 1.6884.069 4.9462.0627 3.2578-.0062 3.668-.0207 4.9478-.0814 1.28-.0607 2.147-.2652 2.9098-.5633.7889-.3086 1.4578-.72 2.1228-1.3881.665-.6682 1.0745-1.3378 1.3795-2.1284.2957-.7632.4966-1.636.552-2.9124.056-1.2809.0692-1.6898.063-4.948-.0063-3.2583-.021-3.6668-.0817-4.9465-.0607-1.2797-.264-2.1487-.5633-2.9117-.3084-.7889-.72-1.4568-1.3876-2.1228C21.2982 1.33 20.628.9208 19.8378.6165 19.074.321 18.2017.1197 16.9244.0645 15.6471.0093 15.236-.005 11.977.0014 8.718.0076 8.31.0215 7.0301.0839m.1402 21.6932c-1.17-.0509-1.8053-.2453-2.2287-.408-.5606-.216-.96-.4771-1.3819-.895-.422-.4178-.6811-.8186-.9-1.378-.1644-.4234-.3624-1.058-.4171-2.228-.0595-1.2645-.072-1.6442-.079-4.848-.007-3.2037.0053-3.583.0607-4.848.05-1.169.2456-1.805.408-2.2282.216-.5613.4762-.96.895-1.3816.4188-.4217.8184-.6814 1.3783-.9003.423-.1651 1.0575-.3614 2.227-.4171 1.2655-.06 1.6447-.072 4.848-.079 3.2033-.007 3.5835.005 4.8495.0608 1.169.0508 1.8053.2445 2.228.408.5608.216.96.4754 1.3816.895.4217.4194.6816.8176.9005 1.3787.1653.4217.3617 1.056.4169 2.2263.0602 1.2655.0739 1.645.0796 4.848.0058 3.203-.0055 3.5834-.061 4.848-.051 1.17-.245 1.8055-.408 2.2294-.216.5604-.4763.96-.8954 1.3814-.419.4215-.8181.6811-1.3783.9-.4224.1649-1.0577.3617-2.2262.4174-1.2656.0595-1.6448.072-4.8493.079-3.2045.007-3.5825-.006-4.848-.0608M16.953 5.5864A1.44 1.44 0 1 0 18.39 4.144a1.44 1.44 0 0 0-1.437 1.4424M5.8385 12.012c.0067 3.4032 2.7706 6.1557 6.173 6.1493 3.4026-.0065 6.157-2.7701 6.1506-6.1733-.0065-3.4032-2.771-6.1565-6.174-6.1498-3.403.0067-6.156 2.771-6.1496 6.1738M8 12.0077a4 4 0 1 1 4.008 3.9921A3.9996 3.9996 0 0 1 8 12.0077"/></svg>
              </a>
              <a href="https://tiktok.com/@smkbahrululum" target="_blank" rel="noopener" class="social-icon" title="TikTok">
                <svg viewBox="0 0 448 512" width="18" height="18" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M448 209.9a210.1 210.1 0 0 1-122.8-39.25V349.4A162.55 162.55 0 1 1 185 188.31V278.2a74.62 74.62 0 1 0 52.23 71.18V0l88 0a121.18 121.18 0 0 0 1.86 22.17h0A122.18 122.18 0 0 0 381 102.39a121.43 121.43 0 0 0 67 20.14z"/></svg>
              </a>
            </div>
          </div>
        </div>

        <div class="footer-col col-map">
          <h4 class="footer-title">Lokasi</h4>
          <div class="map-container">
            <iframe
              src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3955.8287046373536!2d112.72071842346976!3d-7.275732192633451!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd7fb537daf6c61%3A0xbce04c09ffdd1941!2sSMK%20Bahrul%20Ulum!5e0!3m2!1sid!2sid!4v1722437908735"
              style="border:0;"
              allowfullscreen
              loading="lazy"
              referrerpolicy="no-referrer-when-downgrade"
              title="Google Maps - SMK Bahrul Ulum"
            ></iframe>
          </div>
        </div>
      </div>

      <div class="footer-divider"></div>

 <div class="footer-bottom">
  <p>© 2026 SMK Bahrul Ulum</p>
  <!-- <p>Made by RPL SMK Bahrul Ulum</p> -->
</div>
    </div>
  </footer>
</template>

<style scoped>
.site-footer {
  background: #fbfcfa;
  color: #1c2a23;
  padding: 48px 7% 32px;
  position: relative;
}

.container {
  max-width: 1180px;
  margin: 0 auto;
}

.footer-content{
  display:grid;
  grid-template-columns:1.5fr 1fr 1.2fr;
  gap:48px;
  align-items:start;
}

.footer-col {
  position: relative;
}

.col-brand {
  flex-shrink: 0;
}
.col-info{
    padding-top:4px;
}
.brand-section {
  display: flex;
  align-items: flex-start;
  gap: 16px;
  margin-bottom: 36px;
}

.logo {
  height: 52px;
  width: auto;
  flex-shrink: 0;
}

.brand-info {
  flex: 1;
}

.footer-brand {
  font-size: 22px;
  font-weight: 700;
  margin: 0;
  line-height: 1.2;
}

.tagline {
  margin: 6px 0 0;
  color: #3a6450;
  font-size: 14px;
  font-weight: 700;
  letter-spacing: 0.08em;
}

.description{
  margin-top:12px;
  max-width:380px;
  line-height:1.8;
  color:#647067;
}

.footer-nav{
  margin-top:24px;
  display:flex;
  flex-wrap:wrap;
  gap:18px;
}

.footer-nav a{
  font-size:14px;
}

.footer-nav a:hover {
  color: #3a6450;
}

.footer-title {
  font-size: 12px;
  font-weight: 700;
  margin: 0 0 20px;
  text-transform: uppercase;
  letter-spacing: 0.15em;
  color: #3a6450;
}

.contact-link {
  display: inline-flex;
  align-items: center;
  gap: 12px;
  color: #1c2a23;
  text-decoration: none;
  font-size: 15px;
  transition: color 0.2s ease;
  margin-bottom: 24px;
}

.contact-link:hover {
  color: #3a6450;
  text-decoration: underline;
}

.contact-btn {
  display: inline-flex; align-items: center; gap: 8px;
  margin-top: 16px; padding: 10px 20px;
  background: rgba(58, 100, 80, 0.08); border: 1.5px solid rgba(58, 100, 80, 0.2);
  border-radius: 12px; color: #3a6450;
  font-size: 13px; font-weight: 700; font-family: inherit;
  cursor: pointer; transition: all 0.2s ease;
}
.contact-btn:hover {
  background: #3a6450; color: #fff; border-color: #3a6450;
  transform: translateY(-1px); box-shadow: 0 6px 16px rgba(58, 100, 80, 0.25);
}

.contact-link i {
  font-size: 18px;
  color: #3a6450;
  font-family: "Font Awesome 7 Free";
  font-weight: 900;
}

.social-section {
  margin-top: 28px;
}

.social-label {
  font-size: 13px;
  font-weight: 600;
  margin: 0 0 14px;
  color: #1c2a23;
}

.social-links {
  display: flex;
  gap: 16px;
}

.social-icon {

  display: grid;
  place-items: center;
  width: 44px;
  height: 44px;
  border: 1px solid #dfe4dd;
  border-radius: 12px;
  color: #3a6450;
  font-size: 18px;
  transition: all 0.2s ease;
  background: #fff;
}

.social-icon:hover {
  background: #3a6450;
  color: #fff;
  border-color: #3a6450;
}

.map-container{

height:210px;

border-radius:18px;

overflow:hidden;

box-shadow:none;

border:1px solid #E5E7EB;

}

.map-container iframe {
  display: block;
  width: 100%;
  height: 100%;
  border: 0;
}

.footer-divider{

margin:40px 0 20px;

}

.footer-bottom{

display:flex;

justify-content:space-between;

align-items:center;

font-size:13px;

color:#8B95A5;

}
@media (max-width: 1024px) {
  .footer-content {
    grid-template-columns: 1fr 1fr;
    gap: 48px;
  }

  .col-map {
    grid-column: 1 / 3;
  }

  .map-container {
    height: 220px;
  }
}

@media (max-width: 768px) {
  .site-footer {
    padding: 40px 5% 28px;
  }

  .footer-content {
    grid-template-columns: 1fr;
    gap: 40px;
  }

  .col-map {
    grid-column: 1;
  }

  .brand-section {
    flex-direction: column;
    align-items: flex-start;
  }

  .logo {
    height: 48px;
  }

  .footer-nav {
    flex-direction: row;
    flex-wrap: wrap;
    gap: 20px;
  }

  .map-container {
    height: 200px;
  }
}

@media (max-width: 520px) {
  .site-footer {
    padding: 32px 5% 20px;
  }

  .footer-content {
    gap: 32px;
  }

  .footer-brand {
    font-size: 18px;
  }

  .tagline {
    font-size: 13px;
  }

  .description {
    font-size: 12px;
  }

  .footer-title {
    font-size: 11px;
  }

  .footer-nav a {
    font-size: 13px;
  }

  .contact-link {
    font-size: 14px;
    margin-bottom: 20px;
  }

  .social-icon {
    width: 40px;
    height: 40px;
    font-size: 16px;
  }

  .map-container {
    height: 180px;
  }
}
</style>
