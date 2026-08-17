import { createRouter, createWebHistory } from "vue-router";
import { useAuthSession } from "../composable/useAuthSession";

import HomeView from "../views/HomeView.vue";
import ChatView from "../views/ChatView.vue";
import CareerCenterView from "../views/CareerCenterView.vue";
import NewsView from "../views/NewsView.vue";
import KoperasiView from "../views/KoperasiView.vue";
import ProdukSiswaView from "../views/ProdukSiswaView.vue";
import ELearningView from "../views/ELearningView.vue";
import ETracerView from "../views/ETracerView.vue";
import SpmbInfoView from "../views/SpmbInfoView.vue";

const routes = [
  {
    path: "/",
    component: HomeView,
  },
  {
    path: "/spmb-info",
    component: SpmbInfoView,
  },
  {
    path: "/chat",
    component: ChatView,
  },
  {
    path: "/career-center",
    component: CareerCenterView,
    meta: { requiresSiswa: true }
  },
  {
    path: "/berita",
    component: NewsView,
  },
  {
    path: "/koperasi",
    component: KoperasiView,
    meta: { requiresSiswa: true }
  },
  {
    path: "/produk-siswa",
    component: ProdukSiswaView,
    meta: { requiresSiswa: true }
  },
  {
    path: "/e-learning",
    component: ELearningView,
  },
  {
    path: "/e-tracer",
    component: ETracerView,
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach(async (to) => {
  const { session, fetchStatus } = useAuthSession();

  if (to.meta.requiresSiswa) {
    await fetchStatus();
    if (session.value.role !== "siswa") {
      return "/";
    }
  }

  return true;
});

export default router;
