<template>
  <div class="h-full bg-gray-50 flex flex-col p-6 overflow-y-auto">
    <!-- Header Area -->
    <div class="flex justify-between items-start mb-6">
      <div class="flex items-center gap-4">
        <button @click="router.push('/service-instance-accounts')" class="p-2.5 bg-white text-gray-500 hover:text-gray-800 rounded-xl border border-gray-200 shadow-sm transition-all hover:-translate-x-1">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </button>
        <div>
          <h1 class="text-2xl font-black text-gray-900 tracking-tight flex items-center gap-3">
            SIA Detail
            <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs rounded-full font-bold uppercase tracking-wider shadow-sm border border-blue-200">{{ sia?.sia_number }}</span>
          </h1>
          <p class="text-gray-500 text-sm mt-1 font-medium">{{ sia?.opty?.name || 'N/A' }}</p>
        </div>
      </div>
    </div>

    <div v-if="isLoading" class="flex flex-col items-center justify-center h-64 text-gray-500 font-semibold bg-white rounded-2xl shadow-sm border border-gray-100">
      <div class="loader-spinner mb-3"></div>
      Loading SIA detail...
    </div>

    <div v-else-if="sia" class="space-y-6">
      
      <!-- Service Instance Account Information -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
          <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            Service Instance Account Information
          </h2>
        </div>
        
        <div class="p-6">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-0 border border-gray-100 rounded-xl overflow-hidden divide-x divide-gray-100">
            <!-- Column 1 -->
            <div class="flex flex-col divide-y divide-gray-100 bg-white">
              <div class="py-3 px-4 hover:bg-gray-50 transition-colors">
                <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Service Instance Account No</div>
                <div class="text-sm font-semibold text-gray-900">{{ sia.sia_number }}</div>
              </div>
              <div class="py-3 px-4 hover:bg-gray-50 transition-colors">
                <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Currency</div>
                <div class="text-sm font-semibold text-gray-900">IDR (Rupiahs)</div>
              </div>
              <div class="py-3 px-4 hover:bg-gray-50 transition-colors">
                <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Service Type</div>
                <div class="text-sm font-semibold text-gray-900">{{ sia.opty?.product?.name || 'N/A' }}</div>
              </div>
              <div class="py-3 px-4 hover:bg-gray-50 transition-colors">
                <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Created At</div>
                <div class="text-sm font-semibold text-gray-900">{{ new Date(sia.created_at).toLocaleString() }}</div>
              </div>
            </div>

            <!-- Column 2 -->
            <div class="flex flex-col divide-y divide-gray-100 bg-white">
              <div class="py-3 px-4 hover:bg-gray-50 transition-colors">
                <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Customer Name</div>
                <div class="text-sm font-bold">
                  <router-link :to="`/customers`" class="text-blue-600 hover:text-blue-800 hover:underline uppercase">{{ sia.opty?.customer?.customer_name || sia.company_name }}</router-link>
                </div>
              </div>
              <div class="py-3 px-4 hover:bg-gray-50 transition-colors">
                <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Package Name</div>
                <div class="text-sm font-semibold text-gray-900">{{ sia.opty?.product?.name || 'N/A' }}</div>
              </div>
              <div class="py-3 px-4 hover:bg-gray-50 transition-colors">
                <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Status</div>
                <div>
                  <span class="px-3 py-1 bg-green-50 text-green-700 font-bold text-xs rounded-full border border-green-200">
                    {{ sia.status || 'Active' }}
                  </span>
                </div>
              </div>
            </div>

            <!-- Column 3 -->
            <div class="flex flex-col divide-y divide-gray-100 bg-white">
              <div class="py-3 px-4 hover:bg-gray-50 transition-colors">
                <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Billing Account Number</div>
                <div class="text-sm font-bold text-blue-600">{{ sia.billing_account_number || '-' }}</div>
              </div>
              <div class="py-3 px-4 hover:bg-gray-50 transition-colors">
                <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Order (Opty ID)</div>
                <div class="text-sm font-semibold">
                  <router-link v-if="sia.opty?.id" :to="`/optys/${sia.opty?.id}`" class="text-blue-600 hover:text-blue-800 hover:underline">{{ sia.opty?.opportunity_number }}</router-link>
                  <span v-else>-</span>
                </div>
              </div>
              <div class="py-3 px-4 hover:bg-gray-50 transition-colors">
                <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Account Status</div>
                <div class="text-sm font-semibold text-gray-900">{{ sia.opty?.customer?.status || 'Active' }}</div>
              </div>
              <div class="py-3 px-4 hover:bg-gray-50 transition-colors">
                <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Contract Number</div>
                <div class="text-sm font-bold">
                  <router-link v-if="sia.contract?.id" :to="`/contracts/${sia.contract.id}`" class="text-blue-600 hover:text-blue-800 hover:underline">{{ sia.contract.contract_number }}</router-link>
                  <span v-else class="text-blue-600">-</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Service Instance -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mt-6">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
          <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            Service Instance
          </h2>
        </div>
        
        <div class="p-6">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-0 border border-gray-100 rounded-xl overflow-hidden divide-x divide-gray-100">
            <!-- Column 1 -->
            <div class="flex flex-col divide-y divide-gray-100 bg-white">
              <div class="py-3 px-4 hover:bg-gray-50 transition-colors">
                <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Assigned To</div>
                <div class="text-sm font-semibold">
                  <router-link v-if="sia.opty?.owner_id" :to="`/users/${sia.opty.owner_id}`" class="text-blue-600 hover:text-blue-800 hover:underline">
                    {{ (sia.opty?.owner?.first_name + ' ' + (sia.opty?.owner?.last_name || '')).trim() }}
                  </router-link>
                  <span v-else class="text-blue-600 cursor-pointer hover:underline">System</span>
                </div>
              </div>
              <div class="py-3 px-4 hover:bg-gray-50 transition-colors">
                <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Start Date</div>
                <div class="text-sm font-semibold text-gray-900">{{ new Date(sia.created_at).toLocaleString() }}</div>
              </div>
              <div class="py-3 px-4 hover:bg-gray-50 transition-colors">
                <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Estimated Value (OTC)</div>
                <div class="text-lg font-bold text-indigo-600">Rp {{ formatNumber(sia.opty?.estimated_value_otc) }}</div>
              </div>
            </div>

            <!-- Column 2 -->
            <div class="flex flex-col divide-y divide-gray-100 bg-white">
              <div class="py-3 px-4 hover:bg-gray-50 transition-colors">
                <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">SI User Name</div>
                <div class="text-sm font-semibold text-gray-900">{{ sia.sia_number }}</div>
              </div>
              <div class="py-3 px-4 hover:bg-gray-50 transition-colors">
                <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Created Time</div>
                <div class="text-sm font-semibold text-gray-900">{{ new Date(sia.created_at).toLocaleString() }}</div>
              </div>
              <div class="py-3 px-4 hover:bg-gray-50 transition-colors">
                <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Expiry Date</div>
                <div class="text-sm font-semibold text-gray-900">-</div>
              </div>
            </div>

            <!-- Column 3 -->
            <div class="flex flex-col divide-y divide-gray-100 bg-white">
              <div class="py-3 px-4 hover:bg-gray-50 transition-colors">
                <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Description</div>
                <div class="text-sm font-semibold text-gray-900">{{ sia.opty?.description || '-' }}</div>
              </div>
              <div class="py-3 px-4 hover:bg-gray-50 transition-colors">
                <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Estimated Value (MRC)</div>
                <div class="text-lg font-bold text-green-600">Rp {{ formatNumber(sia.opty?.estimated_value_mrc) }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '../api/axios';
import Swal from 'sweetalert2';

const route = useRoute();
const router = useRouter();
const sia = ref(null);
const isLoading = ref(true);

const formatNumber = (num) => {
  if (!num) return '0';
  return new Intl.NumberFormat('id-ID').format(num);
};

const fetchSiaDetails = async () => {
  try {
    const response = await api.get(`/service-instance-accounts/${route.params.id}`);
    sia.value = response.data;
  } catch (error) {
    console.error('Error fetching SIA details:', error);
    Swal.fire('Error', 'SIA not found or failed to load.', 'error');
    router.push('/service-instance-accounts');
  } finally {
    isLoading.value = false;
  }
};

onMounted(() => {
  fetchSiaDetails();
});
</script>

<style scoped>
.loader-spinner { width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #4f46e5; border-radius: 50%; animation: spin 0.8s linear infinite; }
@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
</style>
