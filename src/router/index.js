import { createRouter, createWebHistory } from "vue-router";
import { useAuthSession, BACKEND } from "../composable/useAuthSession";
// ponytail: HomeView di-import STATIS (bukan dynamic import). Route "/" adalah
// landing page — kalau dynamic, browser baru tahu chunk-nya SETELAH index.js
// selesai parse, jadi FCP/LCP nunggu satu round-trip extra ke chunk HomeView.
// Semua route lain tetap lazy (hanyadimuat saat benar-benar dibuka).
import HomeView from "../views/HomeView.vue";

const routes = [
  {
    path: "/",
    component: HomeView,
  },
  {
    path: "/login",
    beforeEnter: () => {
      window.location.href = `http://smkbu-sby.my.id/login`;
    },
  },
  {
    path: "/register",
    beforeEnter: () => {
      window.location.href = `${BACKEND}/register`;
    },
  },
  {
    path: "/spmb-info",
    component: () => import("../views/SpmbInfoView.vue"),
  },
  {
    path: "/chat",
    component: () => import("../views/ChatView.vue"),
  },
  {
    path: "/career-center",
    component: () => import("../views/CareerCenterView.vue"),
    redirect: "/career-center/search",
    children: [
      { path: "search", name: "career-search", component: () => import("../views/career/CariLowonganView.vue") },
      { path: "dashboard", name: "career-dashboard", component: () => import("../views/career/DashboardView.vue") },
      { path: "applications", name: "career-applications", component: () => import("../views/career/LamaranSayaView.vue"), meta: { requiresSiswa: true } },
      { path: "messages", name: "career-messages", component: () => import("../views/career/PesanView.vue") },
      { path: "statistics", name: "career-statistics", component: () => import("../views/career/StatistikView.vue") },
      { path: "news", name: "career-news", component: () => import("../views/career/BeritaKarirView.vue") },
    ]
  },
  {
    path: "/berita",
    component: () => import("../views/NewsView.vue"),
  },
  {
    path: "/berita/:slug",
    name: "berita-detail",
    component: () => import("../views/NewsDetail.vue"),
  },
  {
    path: "/koperasi",
    component: () => import("../views/KoperasiView.vue"),
    meta: { requiresSiswa: true }
  },
  {
    path: "/tabungan",
    component: () => import("../views/TabunganView.vue"),
    meta: { requiresSiswa: true }
  },
  {
    path: "/spp",
    component: () => import("../views/SppView.vue"),
    meta: { requiresSiswa: true }
  },
  {
    path: "/produk-siswa",
    component: () => import("../views/ProdukSiswaView.vue"),
  },
  {
    path: "/e-learning",
    component: () => import("../views/ELearningView.vue"),
  },
  {
    path: "/e-tracer",
    component: () => import("../views/ETracerView.vue"),
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach(async (to) => {
  const { session, fetchStatus } = useAuthSession();

  if (to.meta.requiresSiswa) {
    // cache sessionStorage dulu → navigasi instan; refresh hanya jika belum yakin siswa
    if (session.value.role !== "siswa") {
      await fetchStatus();
      if (session.value.role !== "siswa") {
        return "/";
      }
    } else {
      fetchStatus();
    }
  }

  return true;
});

export default router;
