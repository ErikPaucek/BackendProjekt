import { createRouter, createWebHistory } from 'vue-router'
import Home from '../views/Home.vue'
import Login from '../views/Login.vue'
import Dashboard from '../views/Dashboard.vue'
import ConferenceView from '../views/ConferenceView.vue'
import PageView from '../views/PageView.vue'
import { useAuthStore } from '../stores/auth'

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
    path: '/conference/:id',
    name: 'ConferenceView',
    component: ConferenceView,
    props: true,
    children: [
      {
        path: 'page/:pageId',
        name: 'PageView',
        component: PageView,
        props: true,
      },
    ],
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

// Ochrana routovania podľa prihlásenia a role
router.beforeEach(async (to, from, next) => {
  const auth = useAuthStore()
  if (!auth.user && localStorage.getItem('token')) {
    await auth.fetchUser()
  }
  if (to.meta.requiresAuth && !auth.user) {
    next('/login')
  } else {
    next()
  }
})

export default router