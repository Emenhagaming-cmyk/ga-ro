<template>
  <footer class="site-footer" id="contact">
    <div class="container">
      <div class="footer-content">
        <div class="footer-col col-brand">
          <div class="brand-section">
            <img src="/logo.png" alt="Logo SMK Bahrul Ulum" class="logo" />
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
            <i class="fas fa-envelope"></i>
            <span>info@smk-bahrululum.sch.id</span>
          </a>
          <div class="social-section">
            <p class="social-label">Ikuti kami:</p>
            <div class="social-links">
              <a href="https://instagram.com/smkbahrululum" target="_blank" rel="noopener" class="social-icon" title="Instagram">
                <i class="fab fa-instagram"></i>
              </a>
              <a href="https://tiktok.com/@smkbahrululum" target="_blank" rel="noopener" class="social-icon" title="TikTok">
                <i class="fab fa-tiktok"></i>
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
  <p>Made by RPL SMK Bahrul Ulum</p>
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

.contact-link i {
  font-size: 18px;
  color: #3a6450;
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
  border-radius: 6px;
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
