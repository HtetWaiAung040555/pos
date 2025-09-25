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
import Branch from './views/Branch/Branch.vue';
import CreateBranch from './views/Branch/CreateBranch.vue';
import UpdateBranch from './views/Branch/UpdateBranch.vue';
import ToastService from 'primevue/toastservice';
import { useUserStore } from './stores/useUserStore';
import { API_URL } from './utils/config';
import axios from "axios";
import Role from './views/User_Role/Role.vue';
import CreateRole from './views/User_Role/CreateRole.vue';
import UpdateRole from './views/User_Role/UpdateRole.vue';
import Unauthorized from './views/Unauthorized.vue';
import User from './views/User/User.vue';
import CreateUser from './views/User/CreateUser.vue';
import UpdateUser from './views/User/UpdateUser.vue';
import Counter from './views/Counter/Counter.vue';
import CreateCounter from './views/Counter/CreateCounter.vue';
import UpdateCounter from './views/Counter/UpdateCounter.vue';
import Product from './views/Product/Product.vue';
import CreateProduct from './views/Product/CreateProduct.vue';
import UpdateProduct from './views/Product/UpdateProduct.vue';

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {path: '/login', name: 'Login', component: Login},
        {path: '/register', name: 'Register', component: Register},
        {path: '/unauthorized', name: 'Unauthorized', component: Unauthorized},
        {path: '/', name: 'Home', component: Home, meta: { requiresAuth: true }},
        {path: '/sales', name: 'Sales', component: Sales, meta: { requiresAuth: true }},
        {path: '/user', name: 'Users', component: User, meta: { requiresAuth: true, permission: { resource: 'User', action: 'View' }  }},
        {path: '/user/create', name: 'Create User', component: CreateUser, meta: { requiresAuth: true, permission: { resource: 'User', action: 'Create' } }},
        {path: '/user/update', name: 'Update User', component: UpdateUser, meta: { requiresAuth: true, permission: { resource: 'User', action: 'Update' } }},
        {path: '/branch', name: 'Branch', component: Branch, meta: { requiresAuth: true, permission: { resource: 'Branch', action: 'View' } }},
        {path: '/branch/create', name: 'Create Branch', component: CreateBranch, meta: { requiresAuth: true }},
        {path: '/branch/update', name: 'Update Branch', component: UpdateBranch, meta: { requiresAuth: true }},
        {path: '/role', name: 'Role', component: Role, meta: { requiresAuth: true, permission: { resource: 'Role', action: 'View' } }},
        {path: '/role/create', name: 'Create Role', component: CreateRole, meta: { requiresAuth: true, permission: { resource: 'Role', action: 'Create' } }},
        {path: '/role/update', name: 'Update Role', component: UpdateRole, meta: { requiresAuth: true, permission: { resource: 'Role', action: 'Update' } }},
        {path: '/role', name: 'Role', component: Role, meta: { requiresAuth: true }},
        {path: '/role/create', name: 'Create Role', component: CreateRole, meta: { requiresAuth: true }},
        {path: '/role/update', name: 'Update Role', component: UpdateRole, meta: { requiresAuth: true }},
        {path: '/counter', name: 'Counter', component: Counter, meta: { requiresAuth: true }},
        {path: '/counter/create', name: 'Create Counter', component: CreateCounter, meta: { requiresAuth: true }},
        {path: '/counter/update', name: 'Update Counter', component: UpdateCounter, meta: { requiresAuth: true }},
        {path: '/product', name: 'Product', component: Product, meta: { requiresAuth: true }},
        {path: '/product/create', name: 'Create Product', component: CreateProduct, meta: { requiresAuth: true }},
        {path: '/product/update', name: 'Update Product', component: UpdateProduct, meta: { requiresAuth: true }},
        { path: '/', redirect: '/login' }
    ]
});

// -----------------------------
// Axios Interceptor
// -----------------------------
axios.defaults.baseURL = API_URL;

// Attach token to requests
axios.interceptors.request.use((config) => {
    const userStore = useUserStore();
    if (userStore.token) {
        config.headers.Authorization = `Bearer ${userStore.token}`;
    }
    return config;
}, (error) => Promise.reject(error));

router.beforeEach((to, from, next) => {
    const useUser = useUserStore();
    if (to.meta.requiresAuth && !useUser.isAuthenticated) {
        return next('/login');
    } 
    if (to.meta.permission) {
        const {resource, action} = to.meta.permission;
        const user = JSON.parse(localStorage.getItem('user'));
        const hasPermission = user.permissions?.some(
            (p) => p.name === resource && p.action === action
        );
        if(!hasPermission) {
            return next('/unauthorized')
        }
    }
    next();
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
