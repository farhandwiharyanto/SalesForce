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
      <div class="flex items-center gap-3">
        <button v-if="sia && authStore.hasAction('Service Instance Account', 'edit')" @click="openEditModal" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl shadow-sm transition-all font-semibold flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
          Edit SIA
        </button>
        <button v-if="sia" @click="openHistoryModal" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2.5 rounded-xl shadow-sm transition-all font-semibold flex items-center gap-2 border border-gray-200">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          View History
        </button>
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
    <HistoryModal
      :show="showHistoryModal"
      :title="`History: ${sia?.sia_number}`"
      :is-loading="isLoadingHistory"
      :histories="histories"
      @close="showHistoryModal = false"
    />
    <!-- Edit SIA Modal -->
    <div v-if="showEditModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden flex flex-col max-h-[90vh]">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
          <h3 class="text-lg font-bold text-gray-900">Edit Service Instance Account</h3>
          <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
          </button>
        </div>
        
        <div class="p-6 overflow-y-auto">
          <form @submit.prevent="submitEditSia" class="space-y-4">
            <div>
              <label class="block text-sm font-bold text-gray-700 mb-1">Company Name <span class="text-red-500">*</span></label>
              <input v-model="editForm.company_name" type="text" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm" required>
            </div>
            
            <div>
              <label class="block text-sm font-bold text-gray-700 mb-1">Billing Account Number</label>
              <input v-model="editForm.billing_account_number" type="text" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm">
            </div>
            
            <div>
              <label class="block text-sm font-bold text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
              <select v-model="editForm.status" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm" required>
                <option value="Registered">Registered</option>
                <option value="Active">Active</option>
                <option value="Suspended">Suspended</option>
                <option value="Terminated">Terminated</option>
              </select>
            </div>
          </form>
        </div>
        
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50 flex justify-end gap-3">
          <button @click="showEditModal = false" class="px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-200 bg-gray-100 rounded-xl transition-colors">
            Cancel
          </button>
          <button @click="submitEditSia" :disabled="isSubmittingEdit" class="px-5 py-2.5 text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-sm transition-all flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
            <div v-if="isSubmittingEdit" class="w-4 h-4 border-2 border-white/20 border-t-white rounded-full animate-spin"></div>
            Save Changes
          </button>
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
import HistoryModal from '../components/HistoryModal.vue';
import { useAuthStore } from '../stores/auth';

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();
const sia = ref(null);
const isLoading = ref(true);

const showEditModal = ref(false);
const isSubmittingEdit = ref(false);
const editForm = ref({
  company_name: '',
  billing_account_number: '',
  status: ''
});

const showHistoryModal = ref(false);
const isLoadingHistory = ref(false);
const histories = ref([]);

const openHistoryModal = async () => {
  showHistoryModal.value = true;
  isLoadingHistory.value = true;
  histories.value = [];
  try {
    const response = await api.get(`/audit-logs/sia/${sia.value.id}`);
    histories.value = response.data;
  } catch (error) {
    console.error('Failed to fetch history', error);
  } finally {
    isLoadingHistory.value = false;
  }
};

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

const openEditModal = () => {
  if (!sia.value) return;
  editForm.value = {
    company_name: sia.value.company_name || '',
    billing_account_number: sia.value.billing_account_number || '',
    status: sia.value.status || 'Registered'
  };
  showEditModal.value = true;
};

const submitEditSia = async () => {
  isSubmittingEdit.value = true;
  try {
    const response = await api.put(`/service-instance-accounts/${route.params.id}`, editForm.value);
    sia.value = response.data;
    showEditModal.value = false;
    Swal.fire({
      icon: 'success',
      title: 'Success',
      text: 'Service Instance Account has been updated!',
      timer: 1500,
      showConfirmButton: false
    });
  } catch (error) {
    console.error('Failed to update SIA:', error);
    Swal.fire({
      icon: 'error',
      title: 'Failed',
      text: error.response?.data?.message || 'Failed to update Service Instance Account'
    });
  } finally {
    isSubmittingEdit.value = false;
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
