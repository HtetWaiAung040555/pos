import { createApp } from 'vue'
import './style.css'
import App from './App.vue'
import { createRouter, createWebHistory } from 'vue-router';
import Home from '@/views/Home.vue';
import Sales from '@/views/Sales.vue';

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {path: '/', name: 'Home', component: Home},
        {path: '/sales', name: 'Sales', component: Sales}
    ]
})

createApp(App).use(router).mount('#app')
