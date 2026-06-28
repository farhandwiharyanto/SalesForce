<template>
  <div class="animate-fade-in">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
      <div>
        <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">Dashboard Overview</h1>
        <p class="text-gray-500 mt-2 text-lg">Welcome back, {{ authStore.user?.name || 'User' }}! Here's what's happening with your sales today.</p>
      </div>

    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
      
      <!-- Card 1: Total Opty -->
      <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md transition-all">
        <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 rounded-full bg-indigo-50 opacity-50 group-hover:scale-150 transition-transform duration-500"></div>
        <div class="relative z-10 flex justify-between items-start">
          <div>
            <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Total Opty</p>
            <h3 class="text-3xl font-black text-gray-800 tracking-tight">{{ stats.totalOpty }}</h3>
          </div>
          <div class="p-3 bg-indigo-100 text-indigo-600 rounded-xl shadow-inner">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
          </div>
        </div>
      </div>

      <!-- Card 2: Total Deals (Won) -->
      <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md transition-all">
        <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 rounded-full bg-blue-50 opacity-50 group-hover:scale-150 transition-transform duration-500"></div>
        <div class="relative z-10 flex justify-between items-start">
          <div>
            <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Total Leads</p>
            <h3 class="text-3xl font-black text-gray-800 tracking-tight">{{ stats.totalLeads }}</h3>
          </div>
          <div class="p-3 bg-blue-100 text-blue-600 rounded-xl shadow-inner">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
          </div>
        </div>
      </div>

      <!-- Card 3: Revenue -->
      <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md transition-all">
        <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 rounded-full bg-green-50 opacity-50 group-hover:scale-150 transition-transform duration-500"></div>
        <div class="relative z-10 flex justify-between items-start">
          <div>
            <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Total Customers</p>
            <h3 class="text-3xl font-black text-gray-800 tracking-tight">{{ stats.totalCustomers }}</h3>
          </div>
          <div class="p-3 bg-green-100 text-green-600 rounded-xl shadow-inner">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          </div>
        </div>
      </div>

      <!-- Card 4: Active Leads -->
      <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md transition-all">
        <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 rounded-full bg-purple-50 opacity-50 group-hover:scale-150 transition-transform duration-500"></div>
        <div class="relative z-10 flex justify-between items-start">
          <div>
            <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Total Contracts</p>
            <h3 class="text-3xl font-black text-gray-800 tracking-tight">{{ stats.totalContracts }}</h3>
          </div>
          <div class="p-3 bg-purple-100 text-purple-600 rounded-xl shadow-inner">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
          </div>
        </div>
      </div>

      <!-- Card 5: Win Rate -->
      <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md transition-all">
        <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 rounded-full bg-orange-50 opacity-50 group-hover:scale-150 transition-transform duration-500"></div>
        <div class="relative z-10 flex justify-between items-start">
          <div>
            <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Total Products</p>
            <h3 class="text-3xl font-black text-gray-800 tracking-tight">{{ stats.totalProducts }}</h3>
          </div>
          <div class="p-3 bg-orange-100 text-orange-600 rounded-xl shadow-inner">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content Area: Charts & Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      
      <!-- Recent Activities -->
      <div v-if="['admin', 'administrator'].includes(authStore.user?.role)" class="lg:col-span-3 bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-xl font-extrabold text-gray-900">Recent Activities</h2>
          <router-link to="/activities" class="text-blue-600 hover:text-blue-800 text-sm font-bold bg-blue-50 px-4 py-2 rounded-lg hover:bg-blue-100 transition-colors">View All &rarr;</router-link>
        </div>
        
        <div class="space-y-4">
          <div v-if="recentActivities.length === 0" class="text-center py-8 text-gray-400 text-base font-medium">
            No recent activities found.
          </div>
          
          <div v-for="activity in recentActivities" :key="activity.id" class="flex items-start gap-4 p-4 rounded-xl hover:bg-gray-50 transition-colors border border-transparent hover:border-gray-100">
            <div :class="`flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center shadow-sm border text-white font-bold text-sm ${getColorClass(activity.color)}`">
              {{ activity.type.charAt(0) }}
            </div>
            <div class="flex-grow">
              <div class="flex justify-between items-start">
                <h3 class="text-base font-bold text-gray-900">{{ activity.title }}</h3>
                <span class="text-xs font-medium text-gray-400 whitespace-nowrap">{{ formatDate(activity.date) }}</span>
              </div>
              <p class="text-gray-500 text-sm mt-0.5 font-medium">{{ activity.description }}</p>
            </div>
          </div>
        </div>
      </div>
      
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '../api/axios';
import { useAuthStore } from '../stores/auth';
import Swal from 'sweetalert2';

const authStore = useAuthStore();
const router = useRouter();

const stats = ref({
  totalOpty: 0,
  totalLeads: 0,
  totalCustomers: 0,
  totalContracts: 0,
  totalProducts: 0
});

const recentActivities = ref([]);

const formatNumber = (num) => {
  if (!num) return '0';
  return new Intl.NumberFormat('id-ID').format(num);
};

const fetchDashboardData = async () => {
  try {
    const [optysRes, leadsRes, customersRes, contractsRes, productsRes, activitiesRes] = await Promise.all([
      api.get('/optys'),
      api.get('/leads'),
      api.get('/customers'),
      api.get('/contracts'),
      api.get('/products'),
      api.get('/activities')
    ]);
    
    stats.value.totalOpty = optysRes.data.length;
    stats.value.totalLeads = leadsRes.data.length;
    stats.value.totalCustomers = customersRes.data.length;
    stats.value.totalContracts = contractsRes.data.length;
    stats.value.totalProducts = productsRes.data.length;
    
    recentActivities.value = activitiesRes.data.slice(0, 5); // top 5
    
  } catch (error) {
    console.error('Error fetching dashboard data:', error);
  }
};

const formatDate = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  return date.toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const getColorClass = (colorName) => {
  const map = {
    'blue': 'bg-blue-500 border-blue-600',
    'indigo': 'bg-indigo-500 border-indigo-600',
    'green': 'bg-green-500 border-green-600',
    'purple': 'bg-purple-500 border-purple-600',
    'orange': 'bg-orange-500 border-orange-600',
  };
  return map[colorName] || 'bg-gray-500 border-gray-600';
};

onMounted(() => {
  fetchDashboardData();

  if (sessionStorage.getItem('showWelcomeModal') === 'true') {
    sessionStorage.removeItem('showWelcomeModal');
    setTimeout(() => {
      Swal.fire({
        html: `
          <div class="relative -m-[1.25em] mb-5 bg-gradient-to-br from-indigo-50 to-blue-50 overflow-hidden flex flex-col items-center justify-center p-10 rounded-t-[24px]">
            <div class="absolute -right-8 -top-8 w-32 h-32 bg-indigo-200/50 rounded-full blur-xl"></div>
            <div class="absolute -left-8 -bottom-8 w-24 h-24 bg-blue-200/50 rounded-full blur-xl"></div>
            
            <div class="bg-white/90 backdrop-blur-sm p-4 rounded-full shadow-sm z-10 border border-white/60 mb-2" style="animation: bounce 2s infinite;">
              <span class="text-6xl inline-block" role="img" aria-label="Waving Hand">👋</span>
            </div>
          </div>
          
          <div class="text-center px-4 pb-2">
            <h2 class="text-2xl font-black text-gray-800 mb-2 tracking-tight">Selamat Datang, ${authStore.user?.name || 'User'}!</h2>
            <p class="text-gray-500 font-medium text-sm">Di aplikasi SalesForce CRM. Semoga harimu menyenangkan dan closing lebih banyak hari ini!</p>
          </div>
        `,
        showConfirmButton: true,
        confirmButtonText: 'Mulai Bekerja',
        customClass: {
          popup: '!rounded-[24px] !p-0 overflow-hidden shadow-2xl border border-gray-100',
          htmlContainer: '!m-0',
          actions: '!mt-0 !mb-6',
          confirmButton: 'bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded-xl font-bold transition-all shadow-md hover:shadow-lg w-[85%]',
          closeButton: '!absolute !top-2 !right-2 !bg-white/50 hover:!bg-white/80 !rounded-full !text-gray-600 transition-colors z-20 !w-8 !h-8 !flex !items-center !justify-center'
        },
        buttonsStyling: false,
        showCloseButton: true,
        focusConfirm: false,
      });
    }, 400);
  }
});
</script>

<style>
.animate-fade-in {
  animation: fadeIn 0.5s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
