<template>
  <div class="animate-fade-in pb-12 h-full flex flex-col">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-gray-800 tracking-tight">Contracts Management</h1>
      <div class="flex items-center gap-3">
        <button v-if="authStore.hasAction('Contract', 'create')" @click="showImportModal = true" class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 px-4 py-2.5 rounded-xl font-bold shadow-sm transition-all flex items-center gap-2">
          <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
          Import
        </button>
        <button v-if="authStore.hasAction('Contract', 'create')" @click="openAddModal" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl shadow-md transition-all font-semibold flex items-center gap-2 transform hover:-translate-y-0.5">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
          <span>Add Contract</span>
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

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden relative flex-grow flex flex-col">
      <div v-if="isLoading" class="flex flex-col items-center justify-center h-64 text-gray-500 font-semibold flex-grow">
        <div class="loader-spinner mb-3"></div>
        Loading contracts...
      </div>
      
      <div class="overflow-x-auto flex-grow" v-else>
        <table class="min-w-full divide-y divide-gray-100">
          <thead class="bg-gray-50/50">
            <tr>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Contract Number</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Subject</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Customer</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Assigned To</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Start Date</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Due Date</th>
              <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-100">
            <tr v-if="contracts.length === 0">
              <td colspan="8" class="px-6 py-12 text-center text-gray-400 font-medium">No contracts found. <span>Create a new contract to get started.</span></td>
            </tr>
            <tr v-for="contract in contracts" :key="contract.id" class="hover:bg-gray-50/50 transition-colors group">
              <td class="px-6 py-4 whitespace-nowrap">
                <router-link :to="`/contracts/${contract.id}`" class="text-sm font-mono font-bold text-blue-600 hover:text-blue-800 transition-colors">{{ contract.contract_number }}</router-link>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-bold text-gray-900">{{ contract.subject }}</div>
                <div class="text-[10px] uppercase font-bold text-gray-400 mt-1">{{ contract.contract_division }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-bold rounded-md" 
                  :class="{
                    'bg-gray-100 text-gray-700': contract.status === 'Created',
                    'bg-blue-100 text-blue-700': contract.status === 'In Use',
                    'bg-green-100 text-green-700': contract.status === 'Completed',
                    'bg-red-100 text-red-700': contract.status === 'Terminate'
                  }">
                  {{ contract.status }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <router-link v-if="contract.customer" :to="`/customers`" class="text-sm font-semibold text-blue-600 hover:text-blue-800 flex items-center gap-1">
                  {{ contract.customer.customer_name }}
                </router-link>
                <span v-else class="text-sm text-gray-500">-</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-semibold text-gray-700">{{ contract.assignee ? (contract.assignee.first_name + ' ' + (contract.assignee.last_name||'')).trim() : '-' }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-medium">
                {{ contract.start_date || '-' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-medium">
                {{ contract.due_date || '-' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right flex justify-end gap-2">
                <router-link :to="`/contracts/${contract.id}`" class="text-xs font-bold text-gray-600 bg-gray-50 hover:bg-gray-100 px-3 py-1.5 rounded-lg transition-colors border border-gray-200 shadow-sm inline-flex items-center gap-1">
                  View Details
                </router-link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal Form: Add Contract -->
    <transition name="fade">
      <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/40 backdrop-blur-sm p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-4xl overflow-hidden transform transition-all animate-modal-in border border-gray-100 my-8">
          <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center sticky top-0 z-10">
            <h3 class="text-lg font-bold text-gray-900">Add New Contract</h3>
            <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
          </div>
          <form @submit.prevent="saveContract" class="p-6 space-y-6">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-5">
              
              <!-- Column 1 -->
              <div class="space-y-5">
                <div>
                  <label class="block text-xs font-bold text-gray-600 mb-1.5">Contract Number</label>
                  <input type="text" disabled placeholder="(Auto generated CON...)" class="w-full bg-gray-50 border border-gray-200 text-gray-400 font-mono rounded-lg px-3 py-2.5 text-sm shadow-sm cursor-not-allowed" />
                </div>
                
                <div>
                  <label class="block text-xs font-bold text-gray-600 mb-1.5">Subject <span class="text-red-500">*</span></label>
                  <input type="text" v-model="newContract.subject" required class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all shadow-sm" placeholder="e.g. PROPOSAL PENAWARAN HARGA INTERNET" />
                </div>

                <div>
                  <label class="block text-xs font-bold text-gray-600 mb-1.5">Account Name (Customer) <span class="text-red-500">*</span></label>
                  <div class="flex">
                    <input type="text" :value="selectedCustomerName" readonly required placeholder="Select customer..." class="flex-grow bg-white border border-gray-200 border-r-0 rounded-l-lg px-3 py-2.5 text-sm outline-none shadow-sm cursor-not-allowed" />
                    <button type="button" @click="openSelectModal('customer')" class="bg-gray-100 border border-gray-200 rounded-r-lg px-4 hover:bg-gray-200 transition-colors shadow-sm flex items-center justify-center">
                      <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </button>
                  </div>
                </div>

                <div>
                  <label class="block text-xs font-bold text-gray-600 mb-1.5">Assigned To <span class="text-red-500">*</span></label>
                  <div class="flex">
                    <input type="text" :value="selectedAssigneeName" readonly required placeholder="Select user..." class="flex-grow bg-white border border-gray-200 border-r-0 rounded-l-lg px-3 py-2.5 text-sm outline-none shadow-sm cursor-not-allowed" />
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
                  <select v-model="newContract.status" class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all shadow-sm">
                    <option value="Created">Created</option>
                    <option value="In Use">In Use</option>
                    <option value="Completed">Completed</option>
                    <option value="Terminate">Terminate</option>
                  </select>
                </div>
                
                <div>
                  <label class="block text-xs font-bold text-gray-600 mb-1.5">Contract Division</label>
                  <select v-model="newContract.contract_division" class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all shadow-sm">
                    <option value="Corporate">Corporate</option>
                    <option value="Retail">Retail</option>
                  </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                  <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5 flex items-center gap-1">Start Date <span class="text-red-500">*</span></label>
                    <div class="relative">
                      <input type="date" v-model="newContract.start_date" required class="w-full bg-white border border-gray-200 rounded-lg pl-3 pr-10 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all shadow-sm" />
                      <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                      </div>
                    </div>
                  </div>
                  <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5 flex items-center gap-1">Due Date <span class="text-red-500">*</span></label>
                    <div class="relative">
                      <input type="date" v-model="newContract.due_date" required class="w-full bg-white border border-gray-200 rounded-lg pl-3 pr-10 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all shadow-sm" />
                      <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                      </div>
                    </div>
                  </div>
                </div>

              </div>
            </div>

            <div class="pt-6 flex justify-end gap-3 border-t border-gray-100 mt-8">
              <button type="button" @click="showModal = false" class="px-5 py-2.5 rounded-xl font-bold text-gray-600 hover:bg-gray-50 transition-colors text-sm border border-transparent">Cancel</button>
              <button type="submit" :disabled="isSaving" class="px-6 py-2.5 rounded-xl font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-md transition-all transform hover:-translate-y-0.5 text-sm flex items-center gap-2">
                <span v-if="isSaving" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                Save Contract
              </button>
            </div>
          </form>
        </div>
      </div>
    </transition>

    <!-- Generic Search/Select Modal -->
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
                  <th class="py-3 px-6 text-left text-xs font-bold text-gray-500 uppercase">Email</th>
                  <th class="py-3 px-6 text-center text-xs font-bold text-gray-500 uppercase">Action</th>
                </tr>
                <tr v-else>
                  <th class="py-3 px-6 text-left text-xs font-bold text-gray-500 uppercase">Name</th>
                  <th class="py-3 px-6 text-left text-xs font-bold text-gray-500 uppercase">Email</th>
                  <th class="py-3 px-6 text-left text-xs font-bold text-gray-500 uppercase">Role</th>
                  <th class="py-3 px-6 text-center text-xs font-bold text-gray-500 uppercase">Action</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100 bg-white">
                <template v-if="filteredModalList.length > 0">
                  <tr v-for="item in filteredModalList" :key="item.id" class="hover:bg-blue-50/50 transition-colors">
                    <template v-if="selectModalType === 'customer'">
                      <td class="py-3 px-6 text-sm font-semibold text-gray-800">{{ item.customer_name }}</td>
                      <td class="py-3 px-6 text-sm text-gray-600">{{ item.email || '-' }}</td>
                    </template>
                    <template v-else>
                      <td class="py-3 px-6 text-sm font-semibold text-gray-800">{{ item.first_name }} {{ item.last_name }}</td>
                      <td class="py-3 px-6 text-sm text-gray-600">{{ item.email }}</td>
                      <td class="py-3 px-6 text-sm text-gray-500">{{ item.role }}</td>
                    </template>
                    <td class="py-3 px-6 text-center">
                      <button @click="selectItem(item)" class="bg-green-500 hover:bg-green-600 text-white px-4 py-1.5 rounded-lg text-xs font-bold shadow-sm transition-colors">Select</button>
                    </td>
                  </tr>
                </template>
                <tr v-else>
                  <td :colspan="selectModalType === 'customer' ? 3 : 4" class="py-8 text-center text-gray-500 font-medium">No records found.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </transition>

    <ImportModal 
      :show="showImportModal"
      moduleName="Contract"
      :columns="['Subject', 'Customer ID', 'Assigned To (User ID)', 'Start Date', 'Due Date', 'Division', 'Status']"
      :requiredColumns="['Subject', 'Customer ID']"
      :sampleRow="['Layanan Internet Bulanan', '12', '3', '2026-06-01', '2027-06-01', 'Corporate', 'Created']"
      apiEndpoint="/contracts/bulk"
      @close="showImportModal = false"
      @import-success="onImportSuccess"
      @import-error="onImportError"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router'
import api from '../api/axios';
import ImportModal from '../components/ImportModal.vue';
import { useAuthStore } from '../stores/auth';

const authStore = useAuthStore();
const contracts = ref([]);
const customers = ref([]);
const users = ref([]);
const showImportModal = ref(false)

const onImportSuccess = (msg) => {
  showNotification(msg, 'success')
  fetchData()
}

const onImportError = (msg) => {
  showNotification(msg, 'error')
}

const isLoading = ref(true);
const showModal = ref(false);
const isSaving = ref(false);
const notification = ref(null);

const newContract = ref({
  subject: '',
  assigned_to: null,
  customer_id: null,
  start_date: '',
  due_date: '',
  status: 'Created',
  contract_division: 'Corporate',
});

const showNotification = (message, type = 'success') => {
  notification.value = { message, type };
  setTimeout(() => { notification.value = null }, 3000);
};

// Search Modal State
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
    return customers.value.filter(c => 
      c.customer_name?.toLowerCase().includes(query) || 
      c.nomor_customer?.toLowerCase().includes(query)
    );
  } else {
    return users.value.filter(u => 
      (u.first_name + ' ' + (u.last_name||'')).toLowerCase().includes(query) ||
      u.email?.toLowerCase().includes(query)
    );
  }
});

const selectItem = (item) => {
  if (selectModalType.value === 'customer') {
    newContract.value.customer_id = item.id;
  } else if (selectModalType.value === 'assignee') {
    newContract.value.assigned_to = item.id;
  }
  showSelectModal.value = false;
};

const selectedCustomerName = computed(() => {
  const c = customers.value.find(c => c.id === newContract.value.customer_id);
  return c ? c.customer_name : '';
});

const selectedAssigneeName = computed(() => {
  const u = users.value.find(u => u.id === newContract.value.assigned_to);
  return u ? `${u.first_name} ${u.last_name || ''}`.trim() : '';
});

const fetchData = async () => {
  try {
    const [contractsRes, custRes, usersRes] = await Promise.all([
      api.get('/contracts'),
      api.get('/customers'),
      api.get('/users')
    ]);
    contracts.value = contractsRes.data;
    customers.value = custRes.data;
    users.value = usersRes.data;
    isLoading.value = false;
  } catch (error) {
    console.error('Error fetching data:', error);
    showNotification('Failed to fetch data', 'error');
  }
};

const openAddModal = () => {
  newContract.value = {
    subject: '',
    assigned_to: authStore.user?.id || null,
    customer_id: null,
    start_date: new Date().toISOString().split('T')[0],
    due_date: '',
    status: 'Created',
    contract_division: 'Corporate',
  };
  showModal.value = true;
};

const saveContract = async () => {
  if (!newContract.value.customer_id || !newContract.value.assigned_to || !newContract.value.start_date || !newContract.value.due_date) {
    showNotification('Please fill all required fields.', 'error');
    return;
  }

  try {
    isSaving.value = true;
    await api.post('/contracts', newContract.value);
    showNotification('Contract added successfully!');
    showModal.value = false;
    fetchData();
  } catch (error) {
    console.error('Error saving contract:', error);
    showNotification(error.response?.data?.message || 'Failed to save contract', 'error');
  } finally {
    isSaving.value = false;
  }
};

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
