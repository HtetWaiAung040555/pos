import { createApp } from 'vue'
import './style.css'
import App from './App.vue'
import { createRouter, createWebHistory } from 'vue-router';
import Home from './views/Home.vue';
import Sales from './views/Sales.vue';
import Login from './components/Login.vue';
import Register from './components/Register.vue';
import { createPinia } from 'pinia';
import '@fortawesome/fontawesome-free/css/all.css';
import PrimeVue from 'primevue/config';
import Aura from '@primevue/themes/aura';
import 'primeicons/primeicons.css';
import Users from './views/Users.vue';
import Branch from './views/Branch/Branch.vue';
import CreateBranch from './views/Branch/CreateBranch.vue';
import UpdateBranch from './views/Branch/UpdateBranch.vue';
import ToastService from 'primevue/toastservice';

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {path: '/login', name: 'Login', component: Login},
        {path: '/register', name: 'Register', component: Register},
        {path: '/', name: 'Home', component: Home, meta: { requiresAuth: true }},
        {path: '/sales', name: 'Sales', component: Sales, meta: { requiresAuth: true }},
        {path: '/users', name: 'Users', component: Users, meta: { requiresAuth: true }},
        {path: '/branch', name: 'Branch', component: Branch, meta: { requiresAuth: true }},
        {path: '/branch/create', name: 'Create Branch', component: CreateBranch, meta: { requiresAuth: true }},
        {path: '/branch/update', name: 'Update Branch', component: UpdateBranch, meta: { requiresAuth: true }},
        { path: '/', redirect: '/login' }
    ]
})

router.beforeEach((to, from, next) => {
    const isAuthenticated = localStorage.getItem('auth') === 'true';

    if (to.meta.requiresAuth && !isAuthenticated) {
        next('/login');
    } else {
        next();
    }
});

const app = createApp(App)
app.use(createPinia());
app.use(router);
app.use(PrimeVue, {
    theme: {
        preset: Aura,
    }
});
app.use(ToastService);
app.mount('#app');
