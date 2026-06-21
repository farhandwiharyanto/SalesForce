import { createRouter, createWebHistory } from 'vue-router';
import Dashboard from '../views/Dashboard.vue';
import Deals from '../views/Deals.vue';
import Customers from '../views/Customers.vue';
import Leads from '../views/Leads.vue';
import Products from '../views/Products.vue';
import SiaContracts from '../views/SiaContracts.vue';
import Webhooks from '../views/Webhooks.vue';
import Login from '../views/Login.vue';
import Users from '../views/Users.vue';
import { useAuthStore } from '../stores/auth';

const routes = [
    {
        path: '/login',
        name: 'Login',
        component: Login,
        meta: { guest: true }
    },
    {
        path: '/users',
        name: 'Users',
        component: Users,
        meta: { requiresAuth: true, requiresAdmin: true }
    },
    {
        path: '/leads',
        name: 'Leads',
        component: Leads,
        meta: { requiresAuth: true }
    },
    {
        path: '/products',
        name: 'Products',
        component: Products,
        meta: { requiresAuth: true }
    },
    {
        path: '/sia-contracts',
        name: 'SiaContracts',
        component: SiaContracts,
        meta: { requiresAuth: true }
    },
    {
        path: '/webhooks',
        name: 'Webhooks',
        component: Webhooks,
        meta: { requiresAuth: true, requiresAdmin: true }
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
    } else if (to.meta.requiresAdmin && authStore.role !== 'admin') {
        next({ name: 'Dashboard' });
    } else {
        next();
    }
});

export default router;
