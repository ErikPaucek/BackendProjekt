import { createRouter, createWebHistory } from 'vue-router'
import Home from '../views/Home.vue'
import Login from '../views/Login.vue'
import Dashboard from '../views/Dashboard.vue'
import ConferenceView from '../views/ConferenceView.vue'
import PageView from '../views/PageView.vue'

const routes = [
  { path: '/', name: 'Home', component: Home },
  { path: '/login', name: 'Login', component: Login },
  { path: '/dashboard', name: 'Dashboard', component: Dashboard },
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

export default router
