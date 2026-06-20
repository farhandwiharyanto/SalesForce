<template>
  <div class="animate-fade-in">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
      <div>
        <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">Dashboard Overview</h1>
        <p class="text-gray-500 mt-2 text-lg">Welcome back, Farhan! Here's what's happening with your sales today.</p>
      </div>
      <div class="flex gap-3">
        <button class="bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 px-4 py-2.5 rounded-xl shadow-sm transition-all font-medium text-sm flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
          Export Report
        </button>
        <router-link to="/deals" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl shadow-lg shadow-blue-200 transition-all font-semibold text-sm flex items-center gap-2 transform hover:-translate-y-0.5">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
          New Deal
        </router-link>
      </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <!-- Card 1 -->
      <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md transition-all">
        <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 rounded-full bg-blue-50 opacity-50 group-hover:scale-150 transition-transform duration-500"></div>
        <div class="relative z-10 flex justify-between items-start">
          <div>
            <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Total Deals</p>
            <h3 class="text-4xl font-black text-gray-800 tracking-tight">{{ stats.totalDeals }}</h3>
          </div>
          <div class="p-3 bg-blue-100 text-blue-600 rounded-xl shadow-inner">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
          </div>
        </div>
        <div class="relative z-10 mt-5 flex items-center text-sm font-medium">
          <span class="text-green-600 bg-green-50 px-2 py-0.5 rounded flex items-center"><svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg> 12%</span>
          <span class="text-gray-400 ml-2">vs last month</span>
        </div>
      </div>

      <!-- Card 2 -->
      <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md transition-all">
        <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 rounded-full bg-green-50 opacity-50 group-hover:scale-150 transition-transform duration-500"></div>
        <div class="relative z-10 flex justify-between items-start">
          <div>
            <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Revenue</p>
            <h3 class="text-4xl font-black text-gray-800 tracking-tight">Rp {{ stats.revenue }}</h3>
          </div>
          <div class="p-3 bg-green-100 text-green-600 rounded-xl shadow-inner">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          </div>
        </div>
        <div class="relative z-10 mt-5 flex items-center text-sm font-medium">
          <span class="text-green-600 bg-green-50 px-2 py-0.5 rounded flex items-center"><svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg> 8.4%</span>
          <span class="text-gray-400 ml-2">vs last month</span>
        </div>
      </div>

      <!-- Card 3 -->
      <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md transition-all">
        <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 rounded-full bg-purple-50 opacity-50 group-hover:scale-150 transition-transform duration-500"></div>
        <div class="relative z-10 flex justify-between items-start">
          <div>
            <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Active Leads</p>
            <h3 class="text-4xl font-black text-gray-800 tracking-tight">{{ stats.activeLeads }}</h3>
          </div>
          <div class="p-3 bg-purple-100 text-purple-600 rounded-xl shadow-inner">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
          </div>
        </div>
        <div class="relative z-10 mt-5 flex items-center text-sm font-medium">
          <span class="text-red-600 bg-red-50 px-2 py-0.5 rounded flex items-center"><svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg> 2.1%</span>
          <span class="text-gray-400 ml-2">vs last month</span>
        </div>
      </div>

      <!-- Card 4 -->
      <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md transition-all">
        <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 rounded-full bg-orange-50 opacity-50 group-hover:scale-150 transition-transform duration-500"></div>
        <div class="relative z-10 flex justify-between items-start">
          <div>
            <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Win Rate</p>
            <h3 class="text-4xl font-black text-gray-800 tracking-tight">{{ stats.winRate }}%</h3>
          </div>
          <div class="p-3 bg-orange-100 text-orange-600 rounded-xl shadow-inner">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
          </div>
        </div>
        <div class="relative z-10 mt-5 flex items-center text-sm font-medium">
          <span class="text-green-600 bg-green-50 px-2 py-0.5 rounded flex items-center"><svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg> 5.3%</span>
          <span class="text-gray-400 ml-2">vs last month</span>
        </div>
      </div>
    </div>

    <!-- Main Content Area: Charts & Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      
      <!-- Recent Deals Table -->
      <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-xl font-extrabold text-gray-900">Recent Deals Activity</h2>
          <router-link to="/deals" class="text-blue-600 hover:text-blue-800 text-sm font-bold bg-blue-50 px-4 py-2 rounded-lg hover:bg-blue-100 transition-colors">View All &rarr;</router-link>
        </div>
        
        <div class="overflow-x-auto">
          <table class="min-w-full">
            <thead>
              <tr class="border-b-2 border-gray-100">
                <th class="pb-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Client / Deal</th>
                <th class="pb-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Amount</th>
                <th class="pb-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-if="recentDeals.length === 0">
                <td colspan="3" class="py-12 text-center text-gray-400 text-base font-medium">No recent deals found. <br/> <router-link to="/deals" class="text-blue-600 hover:underline mt-2 inline-block">Create a new deal to get started.</router-link></td>
              </tr>
              <tr v-for="deal in recentDeals" :key="deal.id" class="group hover:bg-gray-50/50 transition-colors">
                <td class="py-5 pr-4">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 h-12 w-12 rounded-xl bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center text-blue-700 font-bold text-lg shadow-sm border border-blue-50">
                      {{ deal.contact?.name?.charAt(0) || '?' }}
                    </div>
                    <div class="ml-4">
                      <div class="text-base font-bold text-gray-900 group-hover:text-blue-600 transition-colors">{{ deal.name }}</div>
                      <div class="text-sm text-gray-500 font-medium mt-0.5">{{ deal.contact?.company || 'Unknown Company' }}</div>
                    </div>
                  </div>
                </td>
                <td class="py-5 px-4">
                  <div class="text-base font-bold text-gray-700 bg-gray-50 inline-block px-3 py-1 rounded-lg border border-gray-100">Rp {{ formatNumber(deal.amount) }}</div>
                </td>
                <td class="py-5 pl-4">
                  <span class="px-3.5 py-1.5 text-xs font-bold rounded-full border shadow-sm uppercase tracking-wide"
                    :class="{
                      'bg-green-50 text-green-700 border-green-200': deal.stage === 'closed_won',
                      'bg-red-50 text-red-700 border-red-200': deal.stage === 'closed_lost',
                      'bg-yellow-50 text-yellow-700 border-yellow-200': deal.stage !== 'closed_won' && deal.stage !== 'closed_lost'
                    }">
                    {{ formatStage(deal.stage) }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Quick Actions / Target -->
      <div class="space-y-8">
        <div class="bg-gradient-to-br from-blue-600 to-indigo-800 rounded-3xl shadow-xl p-8 text-white relative overflow-hidden">
          <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 rounded-full bg-white opacity-10"></div>
          <div class="absolute bottom-0 left-0 -ml-16 -mb-16 w-48 h-48 rounded-full bg-blue-400 opacity-20"></div>
          <h2 class="text-xl font-bold mb-2 relative z-10">Monthly Sales Target</h2>
          <p class="text-blue-100 text-sm mb-8 relative z-10 font-medium">You're almost there! Keep closing those deals.</p>
          
          <div class="relative z-10">
            <div class="flex justify-between text-sm font-bold mb-3">
              <span>Rp 150M</span>
              <span>Rp 200M</span>
            </div>
            <div class="w-full bg-blue-900/40 rounded-full h-4 mb-3 overflow-hidden shadow-inner p-0.5">
              <div class="bg-white h-full rounded-full relative" style="width: 75%">
                <div class="absolute inset-0 bg-white/30 rounded-full animate-pulse"></div>
              </div>
            </div>
            <p class="text-sm font-bold text-blue-200 text-center mt-4 bg-blue-900/20 py-2 rounded-xl">75% of monthly goal reached</p>
          </div>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
          <h2 class="text-xl font-extrabold text-gray-800 mb-5">Quick Tasks</h2>
          <div class="space-y-4">
            <label class="flex items-center p-4 bg-gray-50 hover:bg-gray-100 rounded-2xl cursor-pointer border border-transparent hover:border-gray-200 transition-all shadow-sm">
              <input type="checkbox" class="form-checkbox h-5 w-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500" />
              <span class="ml-3 text-sm text-gray-700 font-bold">Follow up with PT MMS</span>
            </label>
            <label class="flex items-center p-4 bg-gray-50 hover:bg-gray-100 rounded-2xl cursor-pointer border border-transparent hover:border-gray-200 transition-all shadow-sm">
              <input type="checkbox" class="form-checkbox h-5 w-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500" checked />
              <span class="ml-3 text-sm text-gray-400 font-bold line-through">Send proposal to Budi</span>
            </label>
            <label class="flex items-center p-4 bg-gray-50 hover:bg-gray-100 rounded-2xl cursor-pointer border border-transparent hover:border-gray-200 transition-all shadow-sm">
              <input type="checkbox" class="form-checkbox h-5 w-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500" />
              <span class="ml-3 text-sm text-gray-700 font-bold">Schedule demo for Cloud Sync</span>
            </label>
          </div>
        </div>
      </div>
      
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../api/axios';

const stats = ref({
  totalDeals: 0,
  revenue: '0',
  activeLeads: 48,
  winRate: 68
});

const recentDeals = ref([]);

const formatNumber = (num) => {
  return new Intl.NumberFormat('id-ID').format(num);
};

const formatStage = (stage) => {
  return stage.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
};

const fetchDashboardData = async () => {
  try {
    const response = await api.get('/deals');
    const deals = response.data;
    
    // Sort by latest
    deals.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
    
    recentDeals.value = deals.slice(0, 5); // get top 5
    
    stats.value.totalDeals = deals.length;
    
    const totalWon = deals.filter(d => d.stage === 'closed_won').reduce((sum, d) => sum + Number(d.amount), 0);
    
    if (totalWon > 1000000) {
      stats.value.revenue = (totalWon / 1000000).toFixed(1) + 'M';
    } else {
      stats.value.revenue = formatNumber(totalWon);
    }
    
    if (deals.length > 0) {
      const wonDeals = deals.filter(d => d.stage === 'closed_won').length;
      stats.value.winRate = Math.round((wonDeals / deals.length) * 100);
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
