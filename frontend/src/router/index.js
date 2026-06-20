import { createRouter, createWebHistory } from 'vue-router';
import Dashboard from '../views/Dashboard.vue';
import Deals from '../views/Deals.vue';
import Customers from '../views/Customers.vue';
import Login from '../views/Login.vue';
import { useAuthStore } from '../stores/auth';

const routes = [
    {
        path: '/login',
        name: 'Login',
        component: Login,
        meta: { guest: true }
    },
    {
        path: '/',
        name: 'Dashboard',
        component: Dashboard,
        meta: { requiresAuth: true }
    },
    {
        path: '/deals',
        name: 'Deals',
        component: Deals,
        meta: { requiresAuth: true }
    },
    {
        path: '/customers',
        name: 'Customers',
        component: Customers,
        meta: { requiresAuth: true }
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

router.beforeEach((to, from, next) => {
    const authStore = useAuthStore();
    
    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
        next({ name: 'Login' });
    } else if (to.meta.guest && authStore.isAuthenticated) {
        next({ name: 'Dashboard' });
    } else {
        next();
    }
});

export default router;
