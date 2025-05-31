import { createRouter, createWebHistory } from 'vue-router'
import Home from '../views/Home.vue'
import Login from '../views/Login.vue'
import Dashboard from '../views/Dashboard.vue'
import ConferenceView from '../views/ConferenceView.vue'
import PageView from '../views/PageView.vue'
import EditorDashboard from '../views/EditorDashboard.vue' // <-- pridaj tento import
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
    path: '/editordashboard', // <-- pridaj túto route
    name: 'EditorDashboard',
    component: EditorDashboard,
    meta: { requiresAuth: true }
  },
  {
        path: '/conference/:yearId/page/:pageId',
        name: 'PageView',
        component: PageView,
        props: true
  },
  {
    path: '/conference/:id',
    name: 'ConferenceView',
    component: ConferenceView,
    props: true,
    children: [
     
    ],
  },
{
  path: '/conference/:yearId/page/:pageId/edit',
  name: 'PageEdit',
  component: () => import('@/views/PageEdit.vue')
}
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

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