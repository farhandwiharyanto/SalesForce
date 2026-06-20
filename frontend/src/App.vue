<template>
  <div class="min-h-screen bg-[#F3F4F6] font-sans text-gray-800 flex flex-col">
    <!-- Top Navigation -->
    <nav v-if="authStore.isAuthenticated" class="bg-white border-b border-gray-200 sticky top-0 z-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center gap-8">
            <div class="flex-shrink-0 flex items-center gap-2">
              <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center shadow-inner">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
              </div>
              <span class="text-xl font-black text-gray-900 tracking-tight">Sales<span class="text-blue-600">Force</span> CRM</span>
            </div>
            
            <div class="hidden sm:flex sm:space-x-2">
              <router-link to="/" class="px-3 py-2 rounded-md text-sm font-semibold transition-colors text-gray-600 hover:bg-gray-50 hover:text-gray-900" active-class="bg-blue-50 text-blue-700 hover:bg-blue-50 hover:text-blue-700" exact-active-class="bg-blue-50 text-blue-700 hover:bg-blue-50 hover:text-blue-700">
                Dashboard
              </router-link>
              <router-link to="/deals" class="px-3 py-2 rounded-md text-sm font-semibold text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-colors" active-class="bg-blue-50 text-blue-700 hover:bg-blue-50 hover:text-blue-700">
                Deals
              </router-link>
              <router-link to="/customers" class="px-3 py-2 rounded-md text-sm font-semibold text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-colors" active-class="bg-blue-50 text-blue-700 hover:bg-blue-50 hover:text-blue-700">
                Customers
              </router-link>
            </div>
          </div>
          
          <div class="flex items-center gap-4">
            <div class="text-xs font-bold text-gray-500 uppercase tracking-wider hidden md:block">
              {{ authStore.user?.name }} ({{ authStore.role }})
            </div>
            <button @click="handleLogout" class="text-sm font-bold text-red-600 bg-red-50 hover:bg-red-100 px-4 py-2 rounded-xl transition-colors shadow-sm">
              Logout
            </button>
          </div>
        </div>
      </div>
    </nav>

    <!-- Main Content Area -->
    <main class="flex-grow w-full" :class="{ 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8': authStore.isAuthenticated }">
      <router-view></router-view>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-auto relative z-10">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex flex-col md:flex-row justify-between items-center gap-4">
        <p class="text-sm text-gray-500 font-medium">
          &copy; 2026 SalesForce CRM Prototype.
        </p>
        <p class="text-sm font-semibold text-gray-600 bg-gray-50 px-5 py-2.5 rounded-full border border-gray-100 shadow-sm">
          Created By: Farhan Dwi Haryanto <span class="text-red-500 animate-pulse inline-block ml-1">❤️</span>
        </p>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from './stores/auth';

const router = useRouter();
const authStore = useAuthStore();

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
</style>
