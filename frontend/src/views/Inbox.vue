<template>
  <div class="h-full bg-gray-50 flex flex-col p-6 overflow-y-auto">
    <!-- Header Area -->
    <div class="flex justify-between items-center mb-8">
      <div>
        <h1 class="text-2xl font-black text-gray-900 tracking-tight flex items-center gap-3">
          Inbox & Approvals
          <span v-if="optys.length" class="bg-red-100 text-red-600 px-2.5 py-0.5 rounded-full text-xs font-bold">{{ optys.length }} Pending</span>
        </h1>
        <p class="text-gray-500 text-sm mt-1 font-medium">Manage your pending opportunity approvals.</p>
      </div>
    </div>

    <!-- Notification -->
    <transition name="slide-down">
      <div v-if="notification" class="fixed top-20 right-8 z-50 px-6 py-4 rounded-xl shadow-2xl flex items-center gap-3 font-semibold text-white" :class="notification.type === 'error' ? 'bg-red-600' : 'bg-green-600'">
        <svg v-if="notification.type === 'success'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        {{ notification.message }}
      </div>
    </transition>

    <!-- Main Content -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col flex-grow">
      
      <!-- Table Header -->
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100">
          <thead class="bg-gray-50">
            <tr>
              <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Opportunity</th>
              <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Customer</th>
              <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Est. Value (OTC)</th>
              <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Contract Document</th>
              <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-100">
            <tr v-if="isLoading">
              <td colspan="5" class="px-6 py-12 text-center text-gray-500 font-semibold">
                <div class="flex flex-col items-center justify-center">
                  <div class="loader-spinner mb-3"></div>
                  Loading approvals...
                </div>
              </td>
            </tr>
            <tr v-else-if="optys.length === 0">
              <td colspan="5" class="px-6 py-12 text-center text-gray-500 font-medium">
                <div class="flex flex-col items-center justify-center">
                  <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                  Yey! Inbox bersih, tidak ada approval yang menunggu.
                </div>
              </td>
            </tr>
            <tr v-else v-for="opty in optys" :key="opty.id" class="hover:bg-blue-50/50 transition-colors group">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex flex-col">
                  <router-link :to="`/optys/${opty.id}`" class="text-sm font-bold text-blue-600 hover:text-blue-800 hover:underline">{{ opty.opportunity_number }}</router-link>
                  <span class="text-sm text-gray-900 font-semibold">{{ opty.name }}</span>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900 font-semibold">{{ opty.customer?.customer_name || '-' }}</div>
                <div class="text-xs text-gray-500">{{ opty.assignee ? opty.assignee.first_name + ' ' + (opty.assignee.last_name||'') : '-' }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-bold text-green-600">Rp {{ formatNumber(opty.estimated_value_otc) }}</div>
                <div v-if="opty.discount_amount > 0" class="text-xs font-bold text-red-500 mt-1">
                  Diskon: Rp {{ formatNumber(opty.discount_amount) }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <a v-if="opty.contract_document_path" :href="`http://localhost:8000/storage/${opty.contract_document_path}`" target="_blank" class="text-sm font-bold text-purple-600 hover:text-purple-800 hover:underline flex items-center gap-1.5">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                  View Contract
                </a>
                <span v-else class="text-sm text-gray-400 italic">No Document</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-center">
                <div class="flex justify-center gap-2">
                  <button @click="openApproveModal(opty)" class="px-4 py-2 bg-green-50 text-green-700 hover:bg-green-100 border border-green-200 rounded-lg text-xs font-bold transition-colors">
                    Approve
                  </button>
                  <button @click="openRejectModal(opty)" class="px-4 py-2 bg-red-50 text-red-700 hover:bg-red-100 border border-red-200 rounded-lg text-xs font-bold transition-colors">
                    Reject
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Approval/Reject Modal -->
    <transition name="fade">
      <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm" @click="closeModal"></div>
        
        <!-- Modal Content -->
        <div class="relative bg-white rounded-[24px] shadow-2xl w-full max-w-md overflow-hidden transform transition-all animate-modal-in border border-white/20">
          
          <!-- Decorative Top Accent -->
          <div class="h-2 w-full" :class="modalAction === 'approve' ? 'bg-gradient-to-r from-green-400 to-green-600' : 'bg-gradient-to-r from-red-400 to-red-600'"></div>

          <!-- Close Button -->
          <button @click="closeModal" class="absolute top-5 right-5 text-gray-400 hover:text-gray-700 bg-gray-50 hover:bg-gray-100 p-2 rounded-full transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
          </button>

          <div class="p-8">
            <!-- Icon -->
            <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-5 shadow-inner"
                 :class="modalAction === 'approve' ? 'bg-green-50' : 'bg-red-50'">
              <svg v-if="modalAction === 'approve'" class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
              <svg v-else class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>

            <!-- Title & Desc -->
            <div class="text-center mb-6">
              <h3 class="text-2xl font-black text-gray-900 tracking-tight mb-2">
                {{ modalAction === 'approve' ? 'Approve Opportunity' : 'Reject Opportunity' }}
              </h3>
              <p class="text-sm text-gray-500 font-medium">
                {{ modalAction === 'approve' 
                    ? 'Are you sure you want to approve this opportunity and push it forward?' 
                    : 'Please provide a reason for rejecting this opportunity. It will be sent back to the sales team.' 
                }}
              </p>
            </div>

            <!-- Note Input (Reject Only) -->
            <div v-if="modalAction === 'reject'" class="mb-6">
              <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-2">Rejection Note <span class="text-red-500">*</span></label>
              <textarea v-model="modalNote" rows="3" 
                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-900 outline-none focus:bg-white focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all resize-none shadow-inner" 
                placeholder="Tulis alasan penolakan secara detail..."></textarea>
            </div>

            <!-- Actions -->
            <div class="flex gap-3 mt-8">
              <button @click="closeModal" class="flex-1 px-5 py-3.5 rounded-xl font-bold text-gray-700 bg-gray-100 hover:bg-gray-200 transition-colors text-sm">
                Cancel
              </button>
              <button @click="submitAction" :disabled="isSubmitting || (modalAction === 'reject' && !modalNote)" 
                class="flex-1 px-5 py-3.5 rounded-xl font-bold text-white shadow-lg transition-all flex justify-center items-center gap-2 text-sm disabled:opacity-70 disabled:cursor-not-allowed"
                :class="modalAction === 'approve' ? 'bg-green-600 hover:bg-green-700 hover:shadow-green-500/30' : 'bg-red-600 hover:bg-red-700 hover:shadow-red-500/30'">
                <span v-if="isSubmitting" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                <span v-else>{{ modalAction === 'approve' ? 'Yes, Approve' : 'Submit Rejection' }}</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '../api/axios';

const router = useRouter();
const optys = ref([]);
const isLoading = ref(true);
const notification = ref(null);

const showModal = ref(false);
const modalAction = ref('');
const modalNote = ref('');
const selectedOpty = ref(null);
const isSubmitting = ref(false);

const formatNumber = (num) => {
  if (!num) return '0';
  return new Intl.NumberFormat('id-ID').format(num);
};

const showNotification = (message, type = 'success') => {
  notification.value = { message, type };
  setTimeout(() => { notification.value = null }, 3000);
};

const fetchInbox = async () => {
  isLoading.value = true;
  try {
    const response = await api.get('/opty-approvals/inbox');
    optys.value = response.data;
  } catch (error) {
    console.error('Error fetching inbox:', error);
    showNotification('Failed to load inbox', 'error');
  } finally {
    isLoading.value = false;
  }
};

const openApproveModal = (opty) => {
  selectedOpty.value = opty;
  modalAction.value = 'approve';
  modalNote.value = '';
  showModal.value = true;
};

const openRejectModal = (opty) => {
  selectedOpty.value = opty;
  modalAction.value = 'reject';
  modalNote.value = '';
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  selectedOpty.value = null;
  modalNote.value = '';
};

const submitAction = async () => {
  if (!selectedOpty.value) return;
  
  try {
    isSubmitting.value = true;
    await api.post(`/optys/${selectedOpty.value.id}/submit-approval`, {
      action: modalAction.value,
      note: modalNote.value
    });
    showNotification(`Opportunity successfully ${modalAction.value}d!`);
    closeModal();
    fetchInbox();
  } catch (error) {
    console.error('Error submitting action:', error);
    showNotification(error.response?.data?.message || 'Failed to submit action', 'error');
  } finally {
    isSubmitting.value = false;
  }
};

onMounted(() => {
  fetchInbox();
});
</script>

<style scoped>
.loader-spinner { width: 30px; height: 30px; border: 3px solid #f3f3f3; border-top: 3px solid #3b82f6; border-radius: 50%; animation: spin 0.8s linear infinite; }
@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
.slide-down-enter-active, .slide-down-leave-active { transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55); }
.slide-down-enter-from, .slide-down-leave-to { opacity: 0; transform: translateY(-20px); }
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
.animate-modal-in { animation: modalScale 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
@keyframes modalScale { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
</style>
