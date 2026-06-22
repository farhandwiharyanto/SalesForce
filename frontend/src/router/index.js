import { createRouter, createWebHistory } from 'vue-router';
import Dashboard from '../views/Dashboard.vue';
import Opty from '../views/Opty.vue';
import Customers from '../views/Customers.vue';
import Leads from '../views/Leads.vue';
import Products from '../views/Products.vue';
import SiaContracts from '../views/SiaContracts.vue';
import Webhooks from '../views/Webhooks.vue';
import Login from '../views/Login.vue';
import Users from '../views/Users.vue';
import ApiDocs from '../views/ApiDocs.vue';
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
      component: () => import('../views/Leads.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/leads/:id',
      name: 'LeadDetail',
      component: () => import('../views/LeadDetail.vue'),
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
        path: '/optys',
        name: 'Opty',
        component: Opty,
        meta: { requiresAuth: true }
    },
    {
        path: '/optys/:id',
        name: 'OptyDetail',
        component: () => import('../views/OptyDetail.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/customers',
        name: 'Customers',
        component: Customers,
        meta: { requiresAuth: true }
    },
    {
        path: '/api-docs',
        name: 'ApiDocs',
        component: ApiDocs,
        meta: { requiresAuth: true, requiresAdmin: true }
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore();
    
    if (authStore.isAuthenticated && !authStore.user) {
        await authStore.fetchUser();
    }
    
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
