import { createRouter, createWebHistory } from "vue-router";
import { useAuth } from "../composable/useAuth";

import HomeView from "../views/HomeView.vue";
import ChatView from "../views/ChatView.vue";
import CareerCenterView from "../views/CareerCenterView.vue";
import NewsView from "../views/NewsView.vue";
import KoperasiView from "../views/KoperasiView.vue";
import ProdukSiswaView from "../views/ProdukSiswaView.vue";
import LoginView from "../views/LoginView.vue";
import RegisterView from "../views/RegisterView.vue";
import DashboardSiswa from "../views/DashboardSiswa.vue";
import DashboardAdmin from "../views/DashboardAdmin.vue";

const routes = [
  {
    path: "/",
    component: HomeView,
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
    path: "/login",
    component: LoginView,
    meta: { requiresGuest: true }
  },
  {
    path: "/register",
    component: RegisterView,
    meta: { requiresGuest: true }
  },
  {
    path: "/dashboard-siswa",
    component: DashboardSiswa,
    meta: { requiresAuth: true, role: 'siswa' }
  },
  {
    path: "/dashboard-admin",
    component: DashboardAdmin,
    meta: { requiresAuth: true, role: 'admin' }
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to, from, next) => {
  const { isAuthenticated, user } = useAuth();

  if (to.meta.requiresSiswa && user.value?.role !== "siswa") {
    next("/");
  } else if (to.meta.requiresAuth) {
    if (!isAuthenticated.value) {
      next("/login");
    } else if (to.meta.role && user.value?.role !== to.meta.role) {
      next("/");
    } else {
      next();
    }
  } else if (to.meta.requiresGuest) {
    if (isAuthenticated.value) {
      const role = user.value?.role;
      if (role === "admin") {
        next("/dashboard-admin");
      } else if (role === "siswa") {
        next("/");
      } else {
        next("/login");
      }
    } else {
      next();
    }
  } else {
    next();
  }
});

export default router;
