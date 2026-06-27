<template>
  <div class="h-full flex flex-col max-w-full overflow-hidden bg-white relative">
    <div class="p-6 pb-4 shrink-0 border-b border-gray-100 flex justify-between items-center bg-white z-10">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">User History</h1>
        <p class="text-sm text-gray-500 mt-1">View user login and logout history</p>
      </div>
    </div>

    <!-- Filter/Search Bar matching image style -->
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between gap-4 shrink-0 bg-white z-10">
      <div class="flex items-center gap-2">
        <select v-model="filterStatus" class="px-3 py-1.5 border border-gray-200 rounded text-sm text-gray-700 bg-white min-w-[150px]">
          <option value="All">All</option>
          <option value="Signed in">Signed in</option>
          <option value="Signed out">Signed out</option>
        </select>
      </div>
      <div class="flex items-center gap-4">
        <span class="text-sm text-gray-500">{{ startIndex }} to {{ endIndex }} of {{ totalItems }}</span>
        <div class="flex gap-1">
          <button 
            @click="currentPage > 1 ? currentPage-- : null"
            :class="['p-1 border border-gray-200 rounded', currentPage > 1 ? 'text-gray-600 hover:bg-gray-50' : 'text-gray-400 cursor-not-allowed']"
            :disabled="currentPage === 1"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
          </button>
          <button 
            @click="currentPage < totalPages ? currentPage++ : null"
            :class="['p-1 border border-gray-200 rounded', currentPage < totalPages ? 'text-gray-600 hover:bg-gray-50' : 'text-gray-400 cursor-not-allowed']"
            :disabled="currentPage === totalPages"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </button>
        </div>
      </div>
    </div>

    <div class="flex-1 overflow-auto bg-gray-50/30">
      <div class="min-w-[1000px] p-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200/60 overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
              <thead>
                <tr class="border-b border-gray-200 bg-gray-50/50">
                  <th class="py-3 px-4 text-xs font-semibold text-gray-600">User Name</th>
                  <th class="py-3 px-4 text-xs font-semibold text-gray-600">User IP Address</th>
                  <th class="py-3 px-4 text-xs font-semibold text-gray-600">Sign-in Time</th>
                  <th class="py-3 px-4 text-xs font-semibold text-gray-600">Sign-out Time</th>
                  <th class="py-3 px-4 text-xs font-semibold text-gray-600">Status</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="loading" class="border-b border-gray-100">
                  <td colspan="5" class="py-8 text-center text-gray-400">Loading history...</td>
                </tr>
                <tr v-else-if="filteredHistories.length === 0" class="border-b border-gray-100">
                  <td colspan="5" class="py-8 text-center text-gray-400">No login history found.</td>
                </tr>
                <tr v-for="history in paginatedHistories" :key="history.id" class="border-b border-gray-50 hover:bg-gray-50/50 transition-colors group">
                  <td class="py-3 px-4">
                    <span class="text-sm font-medium text-gray-900 uppercase">{{ history.user?.name || history.user?.username || 'Unknown User' }}</span>
                  </td>
                  <td class="py-3 px-4 text-sm text-gray-600">
                    {{ history.ip_address || 'Unknown IP' }}
                  </td>
                  <td class="py-3 px-4 text-sm text-gray-600">
                    {{ formatDate(history.sign_in_time) }}
                  </td>
                  <td class="py-3 px-4 text-sm text-gray-600">
                    {{ history.sign_out_time ? formatDate(history.sign_out_time) : '---' }}
                  </td>
                  <td class="py-3 px-4 text-sm text-gray-600">
                    {{ history.status }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useAuthStore } from '../stores/auth';
import axios from 'axios';

const authStore = useAuthStore();
const histories = ref([]);
const loading = ref(true);
const filterStatus = ref('All');

const currentPage = ref(1);
const itemsPerPage = ref(10);

const fetchHistories = async () => {
  try {
    loading.value = true;
    const response = await axios.get('http://localhost:8000/api/login-histories', {
      headers: { Authorization: `Bearer ${authStore.token}` }
    });
    histories.value = response.data;
  } catch (error) {
    console.error('Error fetching login histories:', error);
  } finally {
    loading.value = false;
  }
};

const filteredHistories = computed(() => {
  if (filterStatus.value === 'All') return histories.value;
  return histories.value.filter(h => h.status === filterStatus.value);
});

watch(filterStatus, () => {
  currentPage.value = 1;
});

const totalItems = computed(() => filteredHistories.value.length);
const totalPages = computed(() => Math.ceil(totalItems.value / itemsPerPage.value) || 1);
const startIndex = computed(() => totalItems.value === 0 ? 0 : (currentPage.value - 1) * itemsPerPage.value + 1);
const endIndex = computed(() => Math.min(currentPage.value * itemsPerPage.value, totalItems.value));

const paginatedHistories = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  const end = start + itemsPerPage.value;
  return filteredHistories.value.slice(start, end);
});

const formatDate = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  return date.toLocaleString('en-CA', { 
    year: 'numeric', 
    month: '2-digit', 
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit',
    hour12: true
  }).replace(',', '');
};

onMounted(() => {
  fetchHistories();
});
</script>
