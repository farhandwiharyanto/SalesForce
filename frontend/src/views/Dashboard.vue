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
            <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Total Deals (Won)</p>
            <h3 class="text-3xl font-black text-gray-800 tracking-tight">{{ stats.totalDeals }}</h3>
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
            <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Revenue</p>
            <h3 class="text-3xl font-black text-gray-800 tracking-tight">Rp {{ stats.revenue }}</h3>
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
            <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Active Leads</p>
            <h3 class="text-3xl font-black text-gray-800 tracking-tight">{{ stats.activeLeads }}</h3>
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
            <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Win Rate</p>
            <h3 class="text-3xl font-black text-gray-800 tracking-tight">{{ stats.winRate }}%</h3>
          </div>
          <div class="p-3 bg-orange-100 text-orange-600 rounded-xl shadow-inner">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content Area: Charts & Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      
      <!-- Recent Optys Table -->
      <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-xl font-extrabold text-gray-900">Recent Opportunities</h2>
          <router-link to="/optys" class="text-blue-600 hover:text-blue-800 text-sm font-bold bg-blue-50 px-4 py-2 rounded-lg hover:bg-blue-100 transition-colors">View All &rarr;</router-link>
        </div>
        
        <div class="overflow-x-auto">
          <table class="min-w-full">
            <thead>
              <tr class="border-b-2 border-gray-100">
                <th class="pb-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Client / Opty Name</th>
                <th class="pb-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Estimated Value (OTC)</th>
                <th class="pb-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Stage</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-if="recentOptys.length === 0">
                <td colspan="3" class="py-12 text-center text-gray-400 text-base font-medium">No recent opportunities found. <br/> <router-link to="/optys" class="text-blue-600 hover:underline mt-2 inline-block">Create a new opty to get started.</router-link></td>
              </tr>
              <tr v-for="opty in recentOptys" :key="opty.id" class="group hover:bg-gray-50/50 transition-colors cursor-pointer" @click="router.push('/optys/' + opty.id)">
                <td class="py-5 pr-4">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 h-12 w-12 rounded-xl bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center text-blue-700 font-bold text-lg shadow-sm border border-blue-50">
                      {{ opty.customer?.customer_name?.charAt(0) || '?' }}
                    </div>
                    <div class="ml-4">
                      <div class="text-base font-bold text-gray-900 group-hover:text-blue-600 transition-colors">{{ opty.name }}</div>
                      <div class="text-sm text-gray-500 font-medium mt-0.5">{{ opty.customer?.customer_name || 'Unknown Company' }}</div>
                    </div>
                  </div>
                </td>
                <td class="py-5 px-4">
                  <div class="text-base font-bold text-gray-700 bg-gray-50 inline-block px-3 py-1 rounded-lg border border-gray-100">Rp {{ formatNumber(opty.estimated_value_otc) }}</div>
                </td>
                <td class="py-5 pl-4">
                  <span class="px-3.5 py-1.5 text-[10px] font-bold rounded-full border shadow-sm uppercase tracking-wider"
                    :class="{
                      'bg-green-50 text-green-700 border-green-200': opty.stage === 'Closed Won',
                      'bg-red-50 text-red-700 border-red-200': opty.stage === 'Closed Lost',
                      'bg-blue-50 text-blue-700 border-blue-200': opty.stage !== 'Closed Won' && opty.stage !== 'Closed Lost'
                    }">
                    {{ opty.stage }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Quick Actions / Target -->
      <div class="space-y-8">
        <div v-if="['admin', 'administrator'].includes(authStore.user?.role)" class="bg-gradient-to-br from-blue-600 to-indigo-800 rounded-3xl shadow-xl p-8 text-white relative overflow-hidden">
          <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 rounded-full bg-white opacity-10"></div>
          <div class="absolute bottom-0 left-0 -ml-16 -mb-16 w-48 h-48 rounded-full bg-blue-400 opacity-20"></div>
          <h2 class="text-xl font-bold mb-2 relative z-10">Monthly Sales Target</h2>
          <p class="text-blue-100 text-sm mb-8 relative z-10 font-medium">Keep closing those deals to reach the ultimate target!</p>
          
          <div class="relative z-10">
            <div class="flex justify-between text-sm font-bold mb-3">
              <span>Rp {{ formatNumber(stats.revenueValue) }}</span>
              <span>Rp 6T</span>
            </div>
            <div class="w-full bg-blue-900/40 rounded-full h-4 mb-3 overflow-hidden shadow-inner p-0.5">
              <div class="bg-white h-full rounded-full relative" :style="`width: ${Math.min(100, Math.max(0, (stats.revenueValue / 6000000000000) * 100))}%`">
                <div class="absolute inset-0 bg-white/30 rounded-full animate-pulse"></div>
              </div>
            </div>
            <p class="text-sm font-bold text-blue-200 text-center mt-4 bg-blue-900/20 py-2 rounded-xl">{{ (Math.min(100, Math.max(0, (stats.revenueValue / 6000000000000) * 100))).toLocaleString('id-ID', {maximumFractionDigits: 5}) }}% of monthly goal reached</p>
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

const authStore = useAuthStore();
const router = useRouter();

const stats = ref({
  totalOpty: 0,
  totalDeals: 0,
  revenue: '0',
  revenueValue: 0,
  activeLeads: 0,
  winRate: 0
});

const recentOptys = ref([]);

const formatNumber = (num) => {
  if (!num) return '0';
  return new Intl.NumberFormat('id-ID').format(num);
};

const fetchDashboardData = async () => {
  try {
    const [optysRes, leadsRes] = await Promise.all([
      api.get('/optys'),
      api.get('/leads')
    ]);
    
    const optys = optysRes.data;
    const leads = leadsRes.data;
    
    // Sort Optys by latest
    optys.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
    recentOptys.value = optys.slice(0, 5); // get top 5
    
    stats.value.totalOpty = optys.length;
    
    // Active leads (exclude Converted and Unqualified)
    const activeLeads = leads.filter(l => l.status !== 'Converted' && l.status !== 'Unqualified');
    stats.value.activeLeads = activeLeads.length;
    
    // Won deals & Revenue
    const wonOptys = optys.filter(o => o.stage === 'Closed Won');
    stats.value.totalDeals = wonOptys.length;
    
    const totalWonAmount = wonOptys.reduce((sum, o) => sum + Number(o.estimated_value_otc || 0), 0);
    stats.value.revenueValue = totalWonAmount;
    
    if (totalWonAmount >= 1000000) {
      stats.value.revenue = (totalWonAmount / 1000000).toFixed(1) + 'M';
    } else {
      stats.value.revenue = formatNumber(totalWonAmount);
    }
    
    // Win rate
    if (optys.length > 0) {
      stats.value.winRate = Math.round((wonOptys.length / optys.length) * 100);
    } else {
      stats.value.winRate = 0;
    }
    
  } catch (error) {
    console.error('Error fetching dashboard data:', error);
  }
};

onMounted(() => {
  fetchDashboardData();
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
