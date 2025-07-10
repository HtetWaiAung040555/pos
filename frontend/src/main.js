import { createApp } from 'vue'
import './style.css'
import App from './App.vue'
import { createRouter, createWebHistory } from 'vue-router';
import Home from './views/Home.vue';
import Sales from './views/Sales.vue';
import Login from './components/Login.vue';
import Register from './components/Register.vue';
import { createPinia } from 'pinia';

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {path: '/login', name: 'Login', component: Login},
        {path: '/register', name: 'Register', component: Register},
        {path: '/', name: 'Home', component: Home, meta: { requiresAuth: true }},
        {path: '/sales', name: 'Sales', component: Sales, meta: { requiresAuth: true }},
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

createApp(App).use(createPinia()).use(router).mount('#app')
