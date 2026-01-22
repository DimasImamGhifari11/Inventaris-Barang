import { createRouter, createWebHistory } from 'vue-router'
import Login from '../views/Login.vue'
import Dashboard from '../views/Dashboard.vue'
import Home from '../views/Home.vue'
import TambahData from '../views/TambahData.vue'
import UpdateData from '../views/UpdateData.vue'
import UpdateForm from '../views/UpdateForm.vue'
import HapusData from '../views/HapusData.vue'
import Riwayat from '../views/Riwayat.vue'

const routes = [
  {
    path: '/',
    redirect: '/login'
  },
  {
    path: '/login',
    name: 'Login',
    component: Login
  },
  {
    path: '/dashboard',
    component: Dashboard,
    meta: { requiresAuth: true },
    children: [
      {
        path: '',
        name: 'Home',
        component: Home
      },
      {
        path: 'tambah-data',
        name: 'TambahData',
        component: TambahData
      },
      {
        path: 'update-data',
        name: 'UpdateData',
        component: UpdateData
      },
      {
        path: 'update-data/:id',
        name: 'UpdateForm',
        component: UpdateForm
      },
      {
        path: 'hapus-data',
        name: 'HapusData',
        component: HapusData
      },
      {
        path: 'riwayat',
        name: 'Riwayat',
        component: Riwayat
      }
    ]
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token')

  if (to.meta.requiresAuth && !token) {
    next('/login')
  } else if (to.path === '/login' && token) {
    next('/dashboard')
  } else {
    next()
  }
})

export default router
