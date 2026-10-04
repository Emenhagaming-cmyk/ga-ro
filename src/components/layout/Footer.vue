<script setup>
defineEmits(['openContact'])
</script>

<template>
  <footer class="site-footer" id="contact">
    <div class="container">
      <div class="footer-content">
        <div class="footer-col col-brand">
          <div class="brand-section">
            <img src="/logo.webp" alt="Logo SMK Bahrul Ulum" class="logo" width="32" height="32" />
            <div class="brand-info">
              <h3 class="footer-brand">SMK Bahrul Ulum</h3>
              <p class="tagline">Sekolah Expert IT</p>
              <p class="description">
                Mencetak Generasi Qur'ani dan Unggul di Bidang Teknologi Informasi dengan komitmen penuh terhadap pengembangan karakter, integritas, dan keunggulan akademik untuk mempersiapkan generasi pemimpin masa depan.
              </p>
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
            <svg
              viewBox="0 0 512 512"
              width="18"
              height="18"
              fill="currentColor"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path d="M48 64C21.5 64 0 85.5 0 112c0 15.1 7.1 29.3 19.2 38.4L236.8 313.6c11.4 8.5 27 8.5 38.4 0L492.8 150.4c12.1-9.1 19.2-23.3 19.2-38.4c0-26.5-21.5-48-48-48H48zM0 176V384c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V176L294.4 339.2c-22.8 17.1-54 17.1-76.8 0L0 176z"/>
            </svg>
            <span>info@smk-bahrululum.sch.id</span>
          </a>

          <button class="contact-btn" @click="$emit('openContact')">
            <svg
              width="16"
              height="16"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
            </svg>
            Hubungi Kami
          </button>

          <div class="social-section">
            <p class="social-label">Ikuti kami:</p>

            <div class="social-links">
              <a
                href="https://www.instagram.com/smkbu.sby?igsh=Mmlnemt1dTM5Ymcz"
                target="_blank"
                rel="noopener"
                class="social-icon"
                title="Instagram"
              >
                <svg
                  viewBox="0 0 24 24"
                  width="18"
                  height="18"
                  fill="currentColor"
                  xmlns="http://www.w3.org/2000/svg"
                  aria-hidden="true"
                >
                  <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm6.406-11.845a1.44 1.44 0 1 0 0 2.881 1.44 1.44 0 0 0 0-2.881z"/>
                </svg>
              </a>

              <a
                href="https://tiktok.com/@smkbahrululum"
                target="_blank"
                rel="noopener"
                class="social-icon"
                title="TikTok"
              >
                <svg
                  viewBox="0 0 448 512"
                  width="18"
                  height="18"
                  fill="currentColor"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <path d="M448 209.9a210.1 210.1 0 0 1-122.8-39.25V349.4A162.55 162.55 0 1 1 185 188.31V278.2a74.62 74.62 0 1 0 52.23 71.18V0l88 0a121.18 121.18 0 0 0 1.86 22.17h0A122.18 122.18 0 0 0 381 102.39a121.43 121.43 0 0 0 67 20.14z"/>
                </svg>
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

      <!-- Logo partner: file .webp sudah di-resize ke ukuran render (48px) + loading lazy
           karena blok ini di bawah fold. Sumber .png asli (3802x2310 dll) tidak dipakai lagi. -->
      <div class="partner-logos">
        <img src="/logos/jhic.webp" alt="JHIC 2.0" width="79" height="48" loading="lazy" />
        <img src="/logos/jagoan-hosting.webp" alt="Jagoan Hosting" width="153" height="48" loading="lazy" />
        <img src="/logos/komdigi.webp" alt="KOMDIGI" width="66" height="48" loading="lazy" />
        <img src="/logos/garuda-spark.webp" alt="Garuda Spark" width="88" height="48" loading="lazy" />
        <img src="/logos/ngalup.webp" alt="Ngalup.co" width="160" height="48" loading="lazy" />
      </div>

      <div class="footer-divider"></div>

      <div class="footer-bottom">
        <p>© 2026 SMK Bahrul Ulum</p>
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

.footer-content {
  display: grid;
  grid-template-columns: 1.5fr 1fr 1.2fr;
  gap: 48px;
  align-items: start;
}

.footer-col {
  position: relative;
}

.col-brand {
  flex-shrink: 0;
}

.col-info {
  padding-top: 4px;
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

.description {
  margin-top: 12px;
  max-width: 380px;
  line-height: 1.8;
  color: #647067;
}

.footer-nav {
  margin-top: 24px;
  display: flex;
  flex-wrap: wrap;
  gap: 18px;
}

.footer-nav a {
  font-size: 14px;
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
  display: inline-flex;
  align-items: center;
  gap: 8px;
  margin-top: 16px;
  padding: 10px 20px;
  background: #ffffff;
  border: 1.5px solid rgba(58, 100, 80, 0.2);
  border-radius: 12px;
  color: #3a6450;
  font-size: 13px;
  font-weight: 700;
  font-family: inherit;
  cursor: pointer;
  transition: all 0.2s ease;
}

.contact-btn:hover {
  background: #3a6450;
  color: #fff;
  border-color: #3a6450;
  transform: translateY(-1px);
  box-shadow: 0 6px 16px rgba(58, 100, 80, 0.25);
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
  border-radius: 12px;
  color: #3a6450;
  font-size: 18px;
  transition: all 0.2s ease;
  background: #fff;
  /* UA stylesheet men-underline semua <a href>; tanpa ini glif Font Awesome
     (teks) dapat garis bawah, ikon SVG lama tidak. */
  text-decoration: none;
}

/* Ikon sosial sekarang inline SVG (Instagram + TikTok), bukan <i class="fa-brands">.
   Konsekuensinya brands.min.css + fa-brands-400.woff2 (115KB) tidak pernah dimuat
   di homepage. Font Awesome CDN yang tersisa hanya untuk ikon `fas` di halaman
   career-center, dan itu tetap dimuat non-blocking lewat <link rel=preload>. */

.social-icon:hover {
  background: #3a6450;
  color: #fff;
  border-color: #3a6450;
}

.map-container {
  height: 210px;
  border-radius: 18px;
  overflow: hidden;
  box-shadow: none;
  border: 1px solid #E5E7EB;
}

.map-container iframe {
  display: block;
  width: 100%;
  height: 100%;
  border: 0;
}

.partner-logos {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 50px;
  width: 100%;
  margin-top: 42px;
  padding: 28px 0;
  border-top: 1px solid #e5e7eb;
}

.partner-logos img {
  display: block;
  width: auto;
  height: 48px;
  max-width: 160px;
  object-fit: contain;
  opacity: 0.9;
  transition: opacity 0.2s ease, transform 0.2s ease;
}

.partner-logos img:hover {
  opacity: 1;
  transform: translateY(-2px);
}

.footer-divider {
  margin: 40px 0 20px;
}

.footer-bottom {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 13px;
  /* ponytail: #8B95A5 di atas white cuma ~3:1 -> gagal WCAG AA (butuh 4.5:1).
     Diganti #6B7280 (~4.8:1) supaya audit kontras Lighthouse lolos. */
  color: #6b7280;
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

  .partner-logos {
    gap: 32px;
  }

  .partner-logos img {
    height: 42px;
    max-width: 140px;
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

  .partner-logos {
    gap: 28px;
    flex-wrap: wrap;
    margin-top: 36px;
  }

  .partner-logos img {
    height: 40px;
    max-width: 135px;
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
    font-size: 12px;
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

  .partner-logos {
    gap: 20px;
    padding: 22px 0;
  }

  .partner-logos img {
    height: 34px;
    max-width: 110px;
  }
}
</style>