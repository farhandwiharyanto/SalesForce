<template>
  <div class="pb-12 animate-fade-in h-full flex flex-col" v-if="contract">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
      <div>
        <div class="flex items-center gap-3 mb-1">
          <router-link to="/contracts" class="text-gray-400 hover:text-blue-600 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
          </router-link>
          <h1 class="text-3xl font-bold text-gray-900 tracking-tight font-mono">{{ contract.contract_number }}</h1>
          <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider ml-2"
            :class="{
              'bg-gray-100 text-gray-700': contract.status === 'Created',
              'bg-blue-100 text-blue-700': contract.status === 'In Use',
              'bg-green-100 text-green-700': contract.status === 'Completed',
              'bg-red-100 text-red-700': contract.status === 'Terminate'
            }">
            {{ contract.status }}
          </span>
        </div>
        <p class="text-gray-500 font-medium ml-9">{{ contract.subject }}</p>
      </div>
      <div class="flex items-center gap-3">
        <button @click="openEditModal" class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 px-5 py-2.5 rounded-xl font-bold shadow-sm transition-all flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
          Edit Contract
        </button>
        <button @click="openHistoryModal" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2.5 rounded-xl shadow-sm transition-all font-semibold flex items-center gap-2 border border-gray-200">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          View History
        </button>
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

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
      
      <!-- Main Content (Left) -->
      <div class="xl:col-span-2 space-y-8">
        
        <!-- Details Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
          <div class="px-6 py-5 border-b border-gray-50 flex items-center gap-3 bg-gray-50/50">
            <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h2 class="text-lg font-bold text-gray-900">Contract Information</h2>
          </div>
          
          <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-8">
              
              <div>
                <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Contract Number</span>
                <span class="text-sm font-semibold text-gray-900 font-mono">{{ contract.contract_number }}</span>
              </div>
              
              <div>
                <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Subject</span>
                <span class="text-sm font-semibold text-gray-900">{{ contract.subject }}</span>
              </div>

              <div>
                <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Status</span>
                <span class="text-sm font-semibold text-gray-900">{{ contract.status }}</span>
              </div>

              <div>
                <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Contract Division</span>
                <span class="text-sm font-semibold text-gray-900">{{ contract.contract_division }}</span>
              </div>

              <div>
                <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Customer Name</span>
                <router-link v-if="contract.customer" :to="`/customers`" class="text-sm font-bold text-blue-600 hover:underline">
                  {{ contract.customer.customer_name }}
                </router-link>
                <span v-else class="text-sm font-medium text-gray-500">-</span>
              </div>

              <div>
                <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Assigned To</span>
                <span class="text-sm font-semibold text-gray-900">{{ contract.assignee ? contract.assignee.first_name + ' ' + (contract.assignee.last_name||'') : '-' }}</span>
              </div>

              <div>
                <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Start Date</span>
                <span class="text-sm font-semibold text-gray-900">{{ contract.start_date || '-' }}</span>
              </div>

              <div>
                <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Due Date</span>
                <span class="text-sm font-semibold text-gray-900">{{ contract.due_date || '-' }}</span>
              </div>

            </div>
          </div>
        </div>

      </div>

      <!-- Right Sidebar -->
      <div class="space-y-8">
        
        <!-- System Info Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
          <div class="px-6 py-5 border-b border-gray-50 flex items-center gap-3 bg-gray-50/50">
            <div class="w-8 h-8 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h2 class="text-lg font-bold text-gray-900">System Information</h2>
          </div>
          <div class="p-6 space-y-5">
            <div>
              <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Created Time</span>
              <span class="text-sm font-semibold text-gray-900">{{ formatDateTime(contract.created_at) }}</span>
            </div>
            <div>
              <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Modified Time</span>
              <span class="text-sm font-semibold text-gray-900">{{ formatDateTime(contract.updated_at) }}</span>
            </div>
            <div>
              <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Last Modified By</span>
              <span class="text-sm font-semibold text-gray-900">{{ contract.last_modified_by && contract.last_modified_by.first_name ? contract.last_modified_by.first_name + ' ' + (contract.last_modified_by.last_name||'') : '-' }}</span>
            </div>
          </div>
        </div>

        <!-- Service Instance Account Link Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
          <div class="px-6 py-5 border-b border-gray-50 flex items-center gap-3 bg-gray-50/50">
            <div class="w-8 h-8 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
            </div>
            <h2 class="text-lg font-bold text-gray-900">Linked SIA</h2>
          </div>
          <div class="p-6">
            
            <div v-if="linkedSia" class="p-4 bg-orange-50/50 border border-orange-100 rounded-xl">
              <div class="flex justify-between items-start">
                <div>
                  <span class="block text-xs font-bold text-orange-500 uppercase tracking-wider mb-1">SIA Number</span>
                  <router-link to="/service-instance-accounts" class="text-sm font-bold font-mono text-orange-700 hover:underline">{{ linkedSia.sia_number }}</router-link>
                </div>
                <button @click="unlinkSia" class="text-red-500 hover:text-red-700 p-1 transition-colors" title="Unlink SIA">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
              </div>
              <div class="mt-3">
                <span class="block text-xs font-bold text-orange-500 uppercase tracking-wider mb-1">Status</span>
                <span class="text-sm font-semibold text-orange-800">{{ linkedSia.status }}</span>
              </div>
            </div>

            <div v-else class="space-y-4">
              <p class="text-sm text-gray-500 font-medium">This contract is not linked to any Service Instance Account.</p>
              
              <div>
                <label class="block text-xs font-bold text-gray-600 mb-1.5">Select SIA to Link</label>
                <div class="flex gap-2">
                  <select v-model="selectedSiaToLink" class="flex-grow bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all shadow-sm">
                    <option :value="null">Select SIA...</option>
                    <option v-for="sia in availableSias" :key="sia.id" :value="sia.id">
                      {{ sia.sia_number }} ({{ sia.company_name }})
                    </option>
                  </select>
                  <button @click="linkSia" :disabled="!selectedSiaToLink || isLinkingSia" class="bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed text-white px-4 py-2 rounded-lg font-bold shadow-sm transition-all text-sm flex items-center">
                    <span v-if="isLinkingSia" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                    <span v-else>Link</span>
                  </button>
                </div>
              </div>
            </div>

          </div>
        </div>

      </div>
    </div>

    <!-- Edit Contract Modal -->
    <transition name="fade">
      <div v-if="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/40 backdrop-blur-sm p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-4xl overflow-hidden transform transition-all animate-modal-in border border-gray-100 my-8">
          <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center sticky top-0 z-10">
            <h3 class="text-lg font-bold text-gray-900">Edit Contract</h3>
            <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
          </div>
          <form @submit.prevent="saveEditContract" class="p-6 space-y-6">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-5">
              
              <!-- Column 1 -->
              <div class="space-y-5">
                <div>
                  <label class="block text-xs font-bold text-gray-600 mb-1.5">Subject <span class="text-red-500">*</span></label>
                  <input type="text" v-model="editData.subject" required class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all shadow-sm" />
                </div>

                <div>
                  <label class="block text-xs font-bold text-gray-600 mb-1.5">Account Name (Customer) <span class="text-red-500">*</span></label>
                  <div class="flex">
                    <input type="text" :value="editSelectedCustomerName" readonly required placeholder="Select customer..." class="flex-grow bg-white border border-gray-200 border-r-0 rounded-l-lg px-3 py-2.5 text-sm outline-none shadow-sm cursor-not-allowed" />
                    <button type="button" @click="openSelectModal('customer')" class="bg-gray-100 border border-gray-200 rounded-r-lg px-4 hover:bg-gray-200 transition-colors shadow-sm flex items-center justify-center">
                      <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </button>
                  </div>
                </div>

                <div>
                  <label class="block text-xs font-bold text-gray-600 mb-1.5">Assigned To <span class="text-red-500">*</span></label>
                  <div class="flex">
                    <input type="text" :value="editSelectedAssigneeName" readonly required placeholder="Select user..." class="flex-grow bg-white border border-gray-200 border-r-0 rounded-l-lg px-3 py-2.5 text-sm outline-none shadow-sm cursor-not-allowed" />
                    <button type="button" @click="openSelectModal('assignee')" class="bg-gray-100 border border-gray-200 rounded-r-lg px-4 hover:bg-gray-200 transition-colors shadow-sm flex items-center justify-center">
                      <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </button>
                  </div>
                </div>

              </div>

              <!-- Column 2 -->
              <div class="space-y-5">
                <div>
                  <label class="block text-xs font-bold text-gray-600 mb-1.5">Status</label>
                  <select v-model="editData.status" class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all shadow-sm">
                    <option value="Created">Created</option>
                    <option value="In Use">In Use</option>
                    <option value="Completed">Completed</option>
                    <option value="Terminate">Terminate</option>
                  </select>
                </div>
                
                <div>
                  <label class="block text-xs font-bold text-gray-600 mb-1.5">Contract Division</label>
                  <select v-model="editData.contract_division" class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all shadow-sm">
                    <option value="Corporate">Corporate</option>
                    <option value="Retail">Retail</option>
                  </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                  <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5 flex items-center gap-1">Start Date <span class="text-red-500">*</span></label>
                    <div class="relative">
                      <input type="date" v-model="editData.start_date" required class="w-full bg-white border border-gray-200 rounded-lg pl-3 pr-10 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all shadow-sm" />
                    </div>
                  </div>
                  <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5 flex items-center gap-1">Due Date <span class="text-red-500">*</span></label>
                    <div class="relative">
                      <input type="date" v-model="editData.due_date" required class="w-full bg-white border border-gray-200 rounded-lg pl-3 pr-10 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all shadow-sm" />
                    </div>
                  </div>
                </div>

              </div>
            </div>

            <div class="pt-6 flex justify-end gap-3 border-t border-gray-100 mt-8">
              <button type="button" @click="showEditModal = false" class="px-5 py-2.5 rounded-xl font-bold text-gray-600 hover:bg-gray-50 transition-colors text-sm border border-transparent">Cancel</button>
              <button type="submit" :disabled="isSaving" class="px-6 py-2.5 rounded-xl font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-md transition-all transform hover:-translate-y-0.5 text-sm flex items-center gap-2">
                <span v-if="isSaving" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                Save Changes
              </button>
            </div>
          </form>
        </div>
      </div>
    </transition>

    <!-- Generic Search/Select Modal for Edit -->
    <transition name="fade">
      <div v-if="showSelectModal" class="fixed inset-0 z-[60] flex items-center justify-center bg-gray-900/60 backdrop-blur-sm p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl overflow-hidden transform transition-all animate-modal-in flex flex-col max-h-[80vh]">
          <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center shrink-0">
            <h3 class="text-lg font-bold text-gray-900 capitalize">Select {{ selectModalType === 'assignee' ? 'User' : selectModalType }}</h3>
            <button @click="showSelectModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
          </div>
          
          <div class="p-4 border-b border-gray-100 shrink-0 bg-white">
            <div class="relative">
              <span class="absolute left-3 top-2.5 text-gray-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
              </span>
              <input type="text" v-model="modalSearchQuery" placeholder="Search..." class="w-full bg-gray-50 border border-gray-200 rounded-lg pl-10 pr-3 py-2 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all" />
            </div>
          </div>

          <div class="overflow-y-auto flex-grow p-0">
            <table class="min-w-full divide-y divide-gray-100">
              <thead class="bg-gray-50 sticky top-0 shadow-sm">
                <tr v-if="selectModalType === 'customer'">
                  <th class="py-3 px-6 text-left text-xs font-bold text-gray-500 uppercase">Customer Name</th>
                  <th class="py-3 px-6 text-center text-xs font-bold text-gray-500 uppercase">Action</th>
                </tr>
                <tr v-else>
                  <th class="py-3 px-6 text-left text-xs font-bold text-gray-500 uppercase">Name</th>
                  <th class="py-3 px-6 text-center text-xs font-bold text-gray-500 uppercase">Action</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100 bg-white">
                <template v-if="filteredModalList.length > 0">
                  <tr v-for="item in filteredModalList" :key="item.id" class="hover:bg-blue-50/50 transition-colors">
                    <template v-if="selectModalType === 'customer'">
                      <td class="py-3 px-6 text-sm font-semibold text-gray-800">{{ item.customer_name }}</td>
                    </template>
                    <template v-else>
                      <td class="py-3 px-6 text-sm font-semibold text-gray-800">{{ item.first_name }} {{ item.last_name }}</td>
                    </template>
                    <td class="py-3 px-6 text-center">
                      <button @click="selectItem(item)" class="bg-green-500 hover:bg-green-600 text-white px-4 py-1.5 rounded-lg text-xs font-bold shadow-sm transition-colors">Select</button>
                    </td>
                  </tr>
                </template>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </transition>
    <HistoryModal
      :show="showHistoryModal"
      :title="`History: ${contract.contract_number}`"
      :is-loading="isLoadingHistory"
      :histories="histories"
      @close="showHistoryModal = false"
    />
  </div>

  <div v-else-if="isLoading" class="flex flex-col items-center justify-center h-full flex-grow text-gray-500 font-semibold">
    <div class="loader-spinner mb-3"></div>
    Loading contract details...
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '../api/axios';
import { useAuthStore } from '../stores/auth';
import Swal from 'sweetalert2';
import HistoryModal from '../components/HistoryModal.vue';

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();
const contractId = route.params.id;

const contract = ref(null);
const isLoading = ref(true);
const notification = ref(null);

const showHistoryModal = ref(false);
const isLoadingHistory = ref(false);
const histories = ref([]);

const openHistoryModal = async () => {
  showHistoryModal.value = true;
  isLoadingHistory.value = true;
  histories.value = [];
  try {
    const response = await api.get(`/audit-logs/contract/${contract.value.id}`);
    histories.value = response.data;
  } catch (error) {
    console.error('Failed to fetch history', error);
  } finally {
    isLoadingHistory.value = false;
  }
};

const customers = ref([]);
const users = ref([]);
const allSias = ref([]);

const showEditModal = ref(false);
const isSaving = ref(false);
const editData = ref({});

const isLinkingSia = ref(false);
const selectedSiaToLink = ref(null);

// Formatter
const formatDateTime = (dateStr) => {
  if (!dateStr) return '-';
  const d = new Date(dateStr);
  return d.toLocaleString('en-US', {
    year: 'numeric', month: '2-digit', day: '2-digit',
    hour: 'numeric', minute: '2-digit', hour12: true
  }).replace(',', '');
};

const showNotification = (message, type = 'success') => {
  notification.value = { message, type };
  setTimeout(() => { notification.value = null }, 3000);
};

const linkedSia = computed(() => {
  return allSias.value.find(s => s.contract_id === contract.value?.id);
});

const availableSias = computed(() => {
  return allSias.value.filter(s => !s.contract_id);
});

const fetchData = async () => {
  try {
    const [contractRes, custRes, usersRes, siasRes] = await Promise.all([
      api.get(`/contracts/${contractId}`),
      api.get('/customers'),
      api.get('/users'),
      api.get('/service-instance-accounts')
    ]);
    contract.value = contractRes.data;
    customers.value = custRes.data;
    users.value = usersRes.data;
    allSias.value = siasRes.data;
    isLoading.value = false;
  } catch (error) {
    console.error('Error fetching contract details:', error);
    showNotification('Failed to load contract data', 'error');
  }
};

const openEditModal = () => {
  editData.value = {
    subject: contract.value.subject,
    status: contract.value.status,
    customer_id: contract.value.customer_id,
    assigned_to: contract.value.assigned_to,
    start_date: contract.value.start_date ? contract.value.start_date.split('T')[0].split(' ')[0] : '',
    due_date: contract.value.due_date ? contract.value.due_date.split('T')[0].split(' ')[0] : '',
    contract_division: contract.value.contract_division,
  };
  showEditModal.value = true;
};

const saveEditContract = async () => {
  try {
    isSaving.value = true;
    const res = await api.put(`/contracts/${contractId}`, editData.value);
    contract.value = res.data;
    showNotification('Contract updated successfully!');
    showEditModal.value = false;
  } catch (error) {
    console.error('Error updating contract:', error);
    showNotification(error.response?.data?.message || 'Failed to update contract', 'error');
  } finally {
    isSaving.value = false;
  }
};

const linkSia = async () => {
  if (!selectedSiaToLink.value) return;
  try {
    isLinkingSia.value = true;
    await api.put(`/service-instance-accounts/${selectedSiaToLink.value}`, {
      contract_id: contractId
    });
    showNotification('SIA linked successfully!');
    selectedSiaToLink.value = null;
    fetchData(); // Reload SIAs and Contract
  } catch (error) {
    console.error('Error linking SIA:', error);
    showNotification('Failed to link SIA', 'error');
  } finally {
    isLinkingSia.value = false;
  }
};

const unlinkSia = async () => {
  if (!linkedSia.value) return;

  const result = await Swal.fire({
    title: 'Unlink SIA',
    text: 'Are you sure you want to unlink this SIA?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#0f766e',
    cancelButtonColor: '#9ca3af',
    confirmButtonText: 'Yes, unlink it!',
    cancelButtonText: 'Cancel'
  });

  if (!result.isConfirmed) return;
  
  try {
    await api.put(`/service-instance-accounts/${linkedSia.value.id}`, {
      contract_id: null
    });
    showNotification('SIA unlinked successfully!');
    fetchData();
  } catch (error) {
    console.error('Error unlinking SIA:', error);
    showNotification('Failed to unlink SIA', 'error');
  }
};

// Select Modal
const showSelectModal = ref(false);
const selectModalType = ref(''); 
const modalSearchQuery = ref('');

const openSelectModal = (type) => {
  selectModalType.value = type;
  modalSearchQuery.value = '';
  showSelectModal.value = true;
};

const filteredModalList = computed(() => {
  const query = modalSearchQuery.value.toLowerCase();
  if (selectModalType.value === 'customer') {
    return customers.value.filter(c => c.customer_name?.toLowerCase().includes(query));
  } else {
    return users.value.filter(u => (u.first_name + ' ' + (u.last_name||'')).toLowerCase().includes(query));
  }
});

const selectItem = (item) => {
  if (selectModalType.value === 'customer') {
    editData.value.customer_id = item.id;
  } else if (selectModalType.value === 'assignee') {
    editData.value.assigned_to = item.id;
  }
  showSelectModal.value = false;
};

const editSelectedCustomerName = computed(() => {
  const c = customers.value.find(c => c.id === editData.value.customer_id);
  return c ? c.customer_name : '';
});

const editSelectedAssigneeName = computed(() => {
  const u = users.value.find(u => u.id === editData.value.assigned_to);
  return u ? `${u.first_name} ${u.last_name || ''}`.trim() : '';
});

onMounted(() => {
  fetchData();
});
</script>

<style scoped>
.animate-fade-in { animation: fadeIn 0.4s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
.animate-modal-in { animation: modalScale 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
@keyframes modalScale { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
.slide-down-enter-active, .slide-down-leave-active { transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55); }
.slide-down-enter-from, .slide-down-leave-to { opacity: 0; transform: translateY(-20px); }
.loader-spinner { width: 30px; height: 30px; border: 3px solid #f3f3f3; border-top: 3px solid #2563eb; border-radius: 50%; animation: spin 0.8s linear infinite; }
@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
input[type="date"]::-webkit-calendar-picker-indicator { cursor: pointer; opacity: 0; position: absolute; right: 0; top: 0; width: 100%; height: 100%; }
</style>
