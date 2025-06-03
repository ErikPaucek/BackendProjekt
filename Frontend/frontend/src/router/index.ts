import { createRouter, createWebHistory } from 'vue-router'
import Home from '../views/Home.vue'
import Login from '../views/Login.vue'
import Dashboard from '../views/Dashboard.vue'
import ConferenceView from '../views/ConferenceView.vue'
import PageView from '../views/PageView.vue'
import EditorDashboard from '../views/EditorDashboard.vue'
import { useAuthStore } from '../stores/auth'
import PageCreate from '../views/PageCreate.vue'

const routes = [
  { path: '/', name: 'Home', component: Home },
  { path: '/login', name: 'Login', component: Login },
  { 
    path: '/dashboard', 
    name: 'Dashboard', 
    component: Dashboard, 
    meta: { requiresAuth: true } 
  },
  {
    path: '/editordashboard',
    name: 'EditorDashboard',
    component: EditorDashboard,
    meta: { requiresAuth: true }
  },
  {
    path: '/conference/:year/page/new',
    name: 'PageCreate',
    component: PageCreate,
    meta: { requiresAuth: true }
  },
  {
    path: '/conference/:year/page/:slug',
    name: 'PageView',
    component: PageView,
    props: true
  },
  {
    path: '/conference/:year',
    name: 'ConferenceView',
    component: ConferenceView,
    props: true,
    children: []
  },
  {
    path: '/conference/:year/page/:slug/edit',
    name: 'PageEdit',
    component: () => import('@/views/PageEdit.vue'),
    meta: { requiresAuth: true }
  }
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
})

router.beforeEach(async (to, from, next) => {
  const auth = useAuthStore()
  if (!auth.user && localStorage.getItem('token')) {
    await auth.fetchUser()
  }
  if (to.meta.requiresAuth && !auth.user) {
    next('/login')
  } else if (to.name === 'Dashboard' && auth.user?.role !== 'admin') {
    next('/editordashboard')
  } else if (to.name === 'EditorDashboard' && auth.user?.role !== 'editor') {
    next('/dashboard')
  } else {
    next()
  }
})

export default router