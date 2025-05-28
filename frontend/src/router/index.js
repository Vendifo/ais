import { createRouter, createWebHistory } from 'vue-router'
import Login from '../views/Login.vue'
import Register from '../views/Register.vue'
import Dashboard from '../views/Dashboard.vue'

// Админские страницы
import AdminUsers from '../views/admin/AdminUsers.vue'
import AdminDepartments from '../views/admin/AdminDepartments.vue'
import AdminDisciplines from '../views/admin/AdminDisciplines.vue'
import AdminGroups from '../views/admin/AdminGroups.vue'

const routes = [
  { path: '/', redirect: '/login' },
  { path: '/login', name: 'Login', component: Login },
  { path: '/register', name: 'Register', component: Register },

  {
    path: '/dashboard',
    name: 'Dashboard',
    component: Dashboard,
    meta: { requiresAuth: true }
  },

  // Админ-панель
  {
    path: '/admin/users',
    name: 'AdminUsers',
    component: AdminUsers,
    meta: { requiresAuth: true }
  },

  {
    path: '/admin/departments',
    name: 'AdminDepartments',
    component: AdminDepartments,
    meta: { requiresAuth: true }
  },
  {
    path: '/admin/disciplines',
    name: 'AdminDisciplines',
    component: AdminDisciplines,
    meta: { requiresAuth: true }
  },
  
  {
    path: '/admin/groups',
    name: 'AdminGroups',
    component: AdminGroups,
    meta: { requiresAuth: true }
  },
  {
    path: '/loads/planned',
    name: 'PlannedLoads',
    component: () => import('@/views/AdminPlannedLoads.vue'),
    meta: { roles: ['admin', 'methodist'] }
  }
  
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

// Глобальная навигационная защита
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token')

  if (to.meta.requiresAuth && !token) {
    next('/login')
  } else {
    next()
  }
})

export default router
