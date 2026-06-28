import { createRouter, createWebHistory } from 'vue-router';
import Dashboard from '../views/Dashboard.vue';
import Opty from '../views/Opty.vue';
import Customers from '../views/Customers.vue';
import Leads from '../views/Leads.vue';
import Products from '../views/Products.vue';
import ServiceInstanceAccounts from '../views/ServiceInstanceAccounts.vue';
import Contracts from '../views/Contracts.vue';
import Webhooks from '../views/Webhooks.vue';
import Login from '../views/Login.vue';
import Users from '../views/Users.vue';
import UserDetail from '../views/UserDetail.vue';
import Roles from '../views/Roles.vue';
import EditRole from '../views/EditRole.vue';
import RoleProfiles from '../views/RoleProfiles.vue';
import EditRoleProfile from '../views/EditRoleProfile.vue';
import UserHistory from '../views/UserHistory.vue';
import ApiDocs from '../views/ApiDocs.vue';
import Activities from '../views/Activities.vue';
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
        path: '/users/:id',
        name: 'UserDetail',
        component: UserDetail,
        meta: { requiresAuth: true, requiresAdmin: true }
    },
    {
        path: '/roles',
        name: 'Roles',
        component: Roles,
        meta: { requiresAuth: true, requiresAdmin: true }
    },
    {
        path: '/roles/add',
        name: 'AddRole',
        component: EditRole,
        meta: { requiresAuth: true, requiresAdmin: true }
    },
    {
        path: '/roles/edit/:roleName',
        name: 'EditRole',
        component: EditRole,
        meta: { requiresAuth: true, requiresAdmin: true }
    },
    {
        path: '/role-profiles',
        name: 'RoleProfiles',
        component: RoleProfiles,
        meta: { requiresAuth: true, requiresAdmin: true }
    },
    {
        path: '/role-profiles/add',
        name: 'AddRoleProfile',
        component: EditRoleProfile,
        meta: { requiresAuth: true, requiresAdmin: true }
    },
    {
        path: '/role-profiles/edit/:id',
        name: 'EditRoleProfile',
        component: EditRoleProfile,
        meta: { requiresAuth: true, requiresAdmin: true }
    },
    {
        path: '/user-history',
        name: 'UserHistory',
        component: UserHistory,
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
        path: '/service-instance-accounts',
        name: 'ServiceInstanceAccounts',
        component: ServiceInstanceAccounts,
        meta: { requiresAuth: true }
    },
    {
        path: '/service-instance-accounts/:id',
        name: 'SiaDetail',
        component: () => import('../views/SiaDetail.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/contracts',
        name: 'Contracts',
        component: Contracts,
        meta: { requiresAuth: true }
    },
    {
        path: '/contracts/:id',
        name: 'ContractDetail',
        component: () => import('../views/ContractDetail.vue'),
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
    },
    {
        path: '/activities',
        name: 'Activities',
        component: Activities,
        meta: { requiresAuth: true, requiresAdmin: true }
    },
    {
        path: '/inbox',
        name: 'Inbox',
        component: () => import('../views/Inbox.vue'),
        meta: { requiresAuth: true }
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
    } else if (to.meta.requiresAdmin && !['admin', 'administrator'].includes(authStore.role)) {
        next({ name: 'Dashboard' });
    } else {
        next();
    }
});

export default router;
