<template>
  <div class="min-h-screen bg-[#F3F4F6] font-sans text-gray-800 flex">
    
    <!-- Sidebar Navigation -->
    <aside v-if="authStore.isAuthenticated" class="w-64 bg-white border-r border-gray-200 flex flex-col flex-shrink-0 sticky top-0 h-screen overflow-y-auto">
      
      <!-- Logo -->
      <div class="p-6 border-b border-gray-100 flex items-center gap-3 shrink-0">
        <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center shadow-lg shadow-blue-200 shrink-0">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
        </div>
        <div class="flex flex-col leading-tight overflow-hidden">
          <span class="text-xl font-black text-gray-900 tracking-tight truncate">Sales<span class="text-blue-600">Force</span></span>
          <span class="text-xs font-bold text-gray-400 tracking-widest uppercase">CRM Pro</span>
        </div>
      </div>

      <!-- Menus -->
      <nav class="flex-grow p-4 flex flex-col gap-1.5 overflow-y-auto">
        <p class="px-3 text-xs font-bold text-gray-400 uppercase tracking-widest mb-2 mt-2">Main Utama</p>
        
        <router-link v-if="hasMenu('Dashboard')" to="/" class="px-4 py-3 rounded-xl text-sm font-semibold transition-all text-gray-600 hover:bg-gray-50 hover:text-gray-900 flex items-center gap-3" active-class="bg-blue-50 text-blue-700 shadow-sm border border-blue-100/50">
          <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
          Dashboard
        </router-link>

        <router-link v-if="hasMenu('Customers')" to="/customers" class="px-4 py-3 rounded-xl text-sm font-semibold transition-all text-gray-600 hover:bg-gray-50 hover:text-gray-900 flex items-center gap-3" active-class="bg-blue-50 text-blue-700 shadow-sm border border-blue-100/50">
          <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
          Customers
        </router-link>
        
        <router-link v-if="hasMenu('Leads')" to="/leads" class="px-4 py-3 rounded-xl text-sm font-semibold transition-all text-gray-600 hover:bg-gray-50 hover:text-gray-900 flex items-center gap-3" active-class="bg-blue-50 text-blue-700 shadow-sm border border-blue-100/50">
          <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
          Leads
        </router-link>

        <router-link v-if="hasMenu('Opty')" to="/optys" class="px-4 py-3 rounded-xl text-sm font-semibold transition-all text-gray-600 hover:bg-gray-50 hover:text-gray-900 flex items-center gap-3" active-class="bg-blue-50 text-blue-700 shadow-sm border border-blue-100/50">
          <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          Opporunity
        </router-link>

        <router-link v-if="hasMenu('Products')" to="/products" class="px-4 py-3 rounded-xl text-sm font-semibold transition-all text-gray-600 hover:bg-gray-50 hover:text-gray-900 flex items-center gap-3" active-class="bg-blue-50 text-blue-700 shadow-sm border border-blue-100/50">
          <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
          Products
        </router-link>

        <router-link v-if="hasMenu('Service Instance Account')" to="/service-instance-accounts" class="px-4 py-3 rounded-xl text-sm font-semibold transition-all text-gray-600 hover:bg-gray-50 hover:text-gray-900 flex items-center gap-3" active-class="bg-blue-50 text-blue-700 shadow-sm border border-blue-100/50">
          <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
          Service Instance Account
        </router-link>

        <router-link v-if="hasMenu('Contract')" to="/contracts" class="px-4 py-3 rounded-xl text-sm font-semibold transition-all text-gray-600 hover:bg-gray-50 hover:text-gray-900 flex items-center gap-3" active-class="bg-blue-50 text-blue-700 shadow-sm border border-blue-100/50">
          <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
          Contract
        </router-link>

        <p v-if="authStore.role === 'admin' && hasMenu('User Management')" class="px-3 text-xs font-bold text-gray-400 uppercase tracking-widest mb-2 mt-2">Management User</p>

        <router-link v-if="authStore.role === 'admin' && hasMenu('User Management')" to="/users" class="px-4 py-3 rounded-xl text-sm font-semibold transition-all text-gray-600 hover:bg-gray-50 hover:text-gray-900 flex items-center gap-3" active-class="bg-blue-50 text-blue-700 shadow-sm border border-blue-100/50">
          <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
          Users
        </router-link>

        <router-link v-if="authStore.role === 'admin' && hasMenu('User Management')" to="/roles" class="px-4 py-3 rounded-xl text-sm font-semibold transition-all text-gray-600 hover:bg-gray-50 hover:text-gray-900 flex items-center gap-3" active-class="bg-blue-50 text-blue-700 shadow-sm border border-blue-100/50">
          <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
          Roles
        </router-link>

        <router-link v-if="authStore.role === 'admin' && hasMenu('User Management')" to="/user-history" class="px-4 py-3 rounded-xl text-sm font-semibold transition-all text-gray-600 hover:bg-gray-50 hover:text-gray-900 flex items-center gap-3" active-class="bg-blue-50 text-blue-700 shadow-sm border border-blue-100/50">
          <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
          User History
        </router-link>

        <p v-if="authStore.role === 'admin'" class="px-3 text-xs font-bold text-gray-400 uppercase tracking-widest mb-2 mt-2">Integration</p>

        <router-link v-if="authStore.role === 'admin'" to="/activities" class="px-4 py-3 rounded-xl text-sm font-semibold transition-all text-gray-600 hover:bg-gray-50 hover:text-gray-900 flex items-center gap-3" active-class="bg-blue-50 text-blue-700 shadow-sm border border-blue-100/50">
          <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
          Recent Activities
        </router-link>

        <router-link v-if="authStore.role === 'admin' && hasMenu('OrderSales Logs')" to="/webhooks" class="px-4 py-3 rounded-xl text-sm font-semibold transition-all text-gray-600 hover:bg-gray-50 hover:text-gray-900 flex items-center gap-3" active-class="bg-blue-50 text-blue-700 shadow-sm border border-blue-100/50">
          <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
          Audit Logs
        </router-link>

        <router-link v-if="authStore.role === 'admin' && hasMenu('Semua API')" to="/api-docs" class="px-4 py-3 rounded-xl text-sm font-semibold transition-all text-gray-600 hover:bg-gray-50 hover:text-gray-900 flex items-center gap-3" active-class="bg-blue-50 text-blue-700 shadow-sm border border-blue-100/50">
          <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
          Semua API
        </router-link>
      </nav>

      <!-- User Profile & Logout -->
      <div class="p-4 border-t border-gray-100 bg-gray-50/50 shrink-0">
        <div class="mb-4 px-2 flex items-center gap-3">
          <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-lg shadow-sm border border-blue-200 shrink-0">
            {{ authStore.user?.name?.charAt(0).toUpperCase() || 'U' }}
          </div>
          <div class="overflow-hidden">
            <p class="text-sm font-bold text-gray-800 truncate">{{ authStore.user?.name }}</p>
            <p class="text-xs text-gray-500 font-mono font-semibold truncate">{{ authStore.role?.toUpperCase() }}</p>
          </div>
        </div>
        <button @click="handleLogout" class="w-full text-sm font-bold text-red-600 bg-white hover:bg-red-50 border border-gray-200 hover:border-red-200 px-4 py-2.5 rounded-xl transition-all shadow-sm flex items-center justify-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
          Logout
        </button>
      </div>
    </aside>

    <!-- Main Content Area Wrapper -->
    <div class="flex flex-col flex-grow w-full overflow-x-hidden min-w-0">
      <main class="flex-grow w-full" :class="{ 'p-6 md:p-10 lg:p-12': authStore.isAuthenticated }">
        <div :class="{ 'w-full': authStore.isAuthenticated }">
          <router-view></router-view>
        </div>
      </main>

      <!-- Footer -->
      <footer class="bg-white border-t border-gray-200 mt-auto shrink-0">
        <div class="max-w-7xl mx-auto px-6 py-6 flex flex-col md:flex-row justify-between items-center gap-4">
          <p class="text-sm text-gray-500 font-medium">
            &copy; 2026 SalesForce CRM Prototype.
          </p>
          <p class="text-sm font-semibold text-gray-600 bg-gray-50 px-5 py-2.5 rounded-full border border-gray-100 shadow-sm flex items-center gap-1.5">
            Created By: Farhan Dwi Haryanto 
            <span class="text-red-500 animate-pulse text-base">❤️</span>
          </p>
        </div>
      </footer>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from './stores/auth';

const router = useRouter();
const authStore = useAuthStore();

const hasMenu = (menuName) => {
  return authStore.hasMenu(menuName);
};

const handleLogout = async () => {
  await authStore.logout();
  router.push('/login');
};

onMounted(() => {
  if (authStore.isAuthenticated && !authStore.user) {
    authStore.fetchUser();
  }
});
</script>

<style>
body {
  background-color: #F3F4F6;
}

/* Custom Scrollbar for Sidebar */
aside::-webkit-scrollbar {
  width: 6px;
}
aside::-webkit-scrollbar-track {
  background: transparent;
}
aside::-webkit-scrollbar-thumb {
  background-color: #e5e7eb;
  border-radius: 10px;
}
aside:hover::-webkit-scrollbar-thumb {
  background-color: #d1d5db;
}
</style>
