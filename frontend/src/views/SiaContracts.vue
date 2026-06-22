<template>
  <div class="pb-12 animate-fade-in h-full flex flex-col">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900 tracking-tight">SIA Contracts</h1>
        <p class="text-gray-500 mt-1">Generate Service Integration Agreements for Closed Opty.</p>
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

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 flex-grow">
      
      <!-- Left Panel: Generator Form -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 flex flex-col">
        <div class="flex items-center gap-3 mb-8">
          <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
          </div>
          <h2 class="text-xl font-bold text-gray-900">Contract Metadata</h2>
        </div>

        <form @submit.prevent="generateContract" class="space-y-6 flex-grow flex flex-col">
          
          <div>
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Select Opty (Closed Won)</label>
            <div class="flex">
              <input type="text" :value="selectedOptyName" readonly required placeholder="Select a closed opty..." class="flex-grow bg-gray-50 border border-gray-200 border-r-0 rounded-l-xl px-4 py-3 font-medium outline-none shadow-sm cursor-not-allowed" />
              <button type="button" @click="openSelectModal" class="bg-gray-100 border border-gray-200 rounded-r-xl px-4 hover:bg-gray-200 transition-colors shadow-sm flex items-center justify-center">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
              </button>
            </div>
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Company Name</label>
            <input type="text" v-model="formData.company_name" required placeholder="PT. Enterprise Maju" class="w-full bg-gray-50 border border-gray-200 text-gray-900 rounded-xl px-4 py-3 font-medium focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none shadow-sm" />
          </div>
          
          <div>
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Customer Number (3 Digits)</label>
            <input type="text" v-model="formData.customer_id" required maxlength="3" pattern="\d{3}" placeholder="e.g. 123" class="w-full bg-gray-50 border border-gray-200 text-gray-900 rounded-xl px-4 py-3 font-mono font-medium focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none shadow-sm" />
            <p class="text-xs text-gray-400 mt-1">Will be used for SIA numbering (YYYY + CUST + 001)</p>
          </div>

          <div class="mt-auto pt-8">
            <button type="submit" :disabled="isGenerating" class="w-full py-4 rounded-xl font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-md transition-all flex items-center justify-center gap-2 transform hover:-translate-y-1">
              <span v-if="isGenerating" class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
              <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
              Generate Contract & Lock Opty
            </button>
          </div>
        </form>
      </div>

      <!-- Right Panel: Legal Preview -->
      <div class="bg-gray-50 rounded-xl border border-gray-200 p-8 flex flex-col relative overflow-hidden shadow-inner">
        <!-- Watermark -->
        <div class="absolute inset-0 flex items-center justify-center opacity-5 pointer-events-none">
          <span class="text-9xl font-black transform -rotate-45">DRAFT</span>
        </div>

        <div class="bg-white p-8 rounded-lg shadow-sm border border-gray-200 flex-grow relative z-10 font-serif text-gray-800">
          <div class="text-center mb-8 border-b border-gray-200 pb-6">
            <h1 class="text-xl font-bold uppercase tracking-widest text-gray-900">Service Integration Agreement</h1>
            <p class="text-sm text-gray-500 mt-2 font-mono">Nomor SIA: {{ previewSiaNumber }}</p>
          </div>

          <div class="space-y-4 text-sm leading-relaxed">
            <p>This Service Integration Agreement ("Agreement") is made effective on <strong>{{ new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) }}</strong>.</p>
            
            <p><strong>BETWEEN:</strong></p>
            <p>SalesForce CRM Provider (hereinafter referred to as the "Provider"),</p>
            <p><strong>AND</strong></p>
            <p><strong>{{ formData.company_name || '[COMPANY NAME]' }}</strong> (hereinafter referred to as the "Client"), with Customer ID: {{ formData.customer_id || '[ID]' }}.</p>
            
            <p class="mt-6 font-bold">1. SERVICES PROVIDED</p>
            <p>The Provider agrees to furnish software integration services as specified in the agreed Opty Reference #{{ optyNumberRef || formData.opty_id || '[N/A]' }}.</p>
            
            <p class="mt-4 font-bold">2. FEES AND PAYMENT</p>
            <p>The Client agrees to pay the Provider the amount agreed upon in the aforementioned Opty prior to the commencement of the integration.</p>
          </div>

          <div class="mt-16 flex justify-between">
            <div class="text-center">
              <div class="border-b border-gray-400 w-32 mb-2"></div>
              <span class="text-xs text-gray-500">Provider Signature</span>
            </div>
            <div class="text-center">
              <div class="border-b border-gray-400 w-32 mb-2"></div>
              <span class="text-xs text-gray-500">Client Signature</span>
            </div>
          </div>
        </div>
      </div>
      
    </div>

    <!-- Recently Generated List -->
    <div class="mt-12 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
      <h3 class="text-lg font-bold text-gray-900 mb-6">Generated Contracts</h3>
      <div class="overflow-x-auto">
        <table class="min-w-full">
          <thead>
            <tr class="border-b border-gray-100">
              <th class="py-3 text-left text-xs font-bold text-gray-400 uppercase">SIA Number</th>
              <th class="py-3 text-left text-xs font-bold text-gray-400 uppercase">Company</th>
              <th class="py-3 text-left text-xs font-bold text-gray-400 uppercase">Opty Value</th>
              <th class="py-3 text-left text-xs font-bold text-gray-400 uppercase">Status</th>
              <th class="py-3 text-right text-xs font-bold text-gray-400 uppercase">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-for="contract in contracts" :key="contract.id" class="hover:bg-gray-50">
              <td class="py-3 font-mono text-sm font-bold text-blue-600">{{ contract.sia_number }}</td>
              <td class="py-3 text-sm text-gray-700 font-semibold">{{ contract.company_name }}</td>
              <td class="py-3 text-sm text-gray-600">{{ formatIDR(contract.opty?.estimated_value_otc || contract.opty?.amount || 0) }}</td>
              <td class="py-3">
                <span class="bg-green-50 text-green-700 border border-green-200 px-2 py-1 rounded text-xs font-bold uppercase tracking-wide">
                  {{ contract.status }}
                </span>
              </td>
              <td class="py-3 text-right">
                <button @click="downloadContract(contract)" class="text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg text-xs font-bold transition-colors shadow-sm inline-flex items-center gap-1">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                  Download PDF
                </button>
              </td>
            </tr>
            <tr v-if="contracts.length === 0">
              <td colspan="4" class="py-6 text-center text-gray-400 font-medium text-sm">No contracts generated yet.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Generic Search/Select Modal -->
    <transition name="fade">
      <div v-if="showSelectModal" class="fixed inset-0 z-[60] flex items-center justify-center bg-gray-900/60 backdrop-blur-sm p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl overflow-hidden transform transition-all animate-modal-in flex flex-col max-h-[80vh]">
          <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center shrink-0">
            <h3 class="text-lg font-bold text-gray-900">Select Opty (Closed Won)</h3>
            <button @click="showSelectModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
          </div>
          
          <div class="p-4 border-b border-gray-100 shrink-0 bg-white">
            <div class="relative">
              <span class="absolute left-3 top-2.5 text-gray-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
              </span>
              <input type="text" v-model="modalSearchQuery" placeholder="Search opty..." class="w-full bg-gray-50 border border-gray-200 rounded-lg pl-10 pr-3 py-2 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all" />
            </div>
          </div>

          <div class="overflow-y-auto flex-grow p-0">
            <table class="min-w-full divide-y divide-gray-100">
              <thead class="bg-gray-50 sticky top-0 shadow-sm">
                <tr>
                  <th class="py-3 px-6 text-left text-xs font-bold text-gray-500 uppercase">Opportunity</th>
                  <th class="py-3 px-6 text-left text-xs font-bold text-gray-500 uppercase">Customer</th>
                  <th class="py-3 px-6 text-left text-xs font-bold text-gray-500 uppercase">Value (OTC)</th>
                  <th class="py-3 px-6 text-center text-xs font-bold text-gray-500 uppercase">Action</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100 bg-white">
                <template v-if="filteredOptyList.length > 0">
                  <tr v-for="opty in filteredOptyList" :key="opty.id" class="hover:bg-blue-50/50 transition-colors">
                    <td class="py-3 px-6 text-sm">
                      <div class="font-bold text-gray-800">{{ opty.name }}</div>
                      <div class="text-xs text-gray-500 font-mono mt-0.5">#{{ opty.opportunity_number || opty.id }}</div>
                    </td>
                    <td class="py-3 px-6 text-sm text-gray-600">{{ opty.customer?.customer_name || '-' }}</td>
                    <td class="py-3 px-6 text-sm text-gray-600 font-medium">{{ formatIDR(opty.estimated_value_otc || 0) }}</td>
                    <td class="py-3 px-6 text-center">
                      <button @click="selectOpty(opty)" class="bg-green-500 hover:bg-green-600 text-white px-4 py-1.5 rounded-lg text-xs font-bold shadow-sm transition-colors">Select</button>
                    </td>
                  </tr>
                </template>
                <tr v-else>
                  <td colspan="4" class="py-8 text-center text-gray-500 font-medium">No closed optys found.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </transition>

    <!-- Hidden Print Template (Visible only when printing) -->
    <div id="printTemplate" ref="printTemplate" class="hidden print:block absolute left-0 top-0 w-full bg-white p-12 text-gray-800 font-serif z-[9999]">
        <div class="text-center mb-12 border-b border-gray-300 pb-8">
          <h1 class="text-3xl font-bold uppercase tracking-widest text-gray-900">Service Integration Agreement</h1>
          <p class="text-lg text-gray-600 mt-4 font-mono">Nomor SIA: {{ printData.sia_number }}</p>
        </div>

        <div class="space-y-6 text-lg leading-relaxed">
          <p>This Service Integration Agreement ("Agreement") is made effective on <strong>{{ printData.date }}</strong>.</p>
          
          <p><strong>BETWEEN:</strong></p>
          <p>SalesForce CRM Provider (hereinafter referred to as the "Provider"),</p>
          <p><strong>AND</strong></p>
          <p><strong>{{ printData.company_name }}</strong> (hereinafter referred to as the "Client"), with Customer ID: {{ printData.customer_id }}.</p>
          
          <p class="mt-8 text-xl font-bold">1. SERVICES PROVIDED</p>
          <p>The Provider agrees to furnish software integration services as specified in the agreed Opty Reference #{{ printData.opty_ref }}.</p>
          
          <p class="mt-6 text-xl font-bold">2. FEES AND PAYMENT</p>
          <p>The Client agrees to pay the Provider the amount agreed upon in the aforementioned Opty prior to the commencement of the integration.</p>
        </div>

        <div class="mt-24 flex justify-between px-12">
          <div class="text-center">
            <div class="border-b-2 border-gray-800 w-48 mb-4"></div>
            <span class="text-sm text-gray-600 font-medium">Provider Signature</span>
          </div>
          <div class="text-center">
            <div class="border-b-2 border-gray-800 w-48 mb-4"></div>
            <span class="text-sm text-gray-600 font-medium">Client Signature</span>
          </div>
        </div>
      </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed, nextTick } from 'vue'
import api from '../api/axios'

const closedOpty = ref([])
const contracts = ref([])
const isGenerating = ref(false)
const notification = ref(null)

const showSelectModal = ref(false)
const modalSearchQuery = ref('')
const optyNumberRef = ref('')

const formData = ref({
  opty_id: '',
  customer_id: '',
  company_name: ''
})

const printTemplate = ref(null)
const printData = ref({})

const selectedOptyName = computed(() => {
  const o = closedOpty.value.find(o => o.id === formData.value.opty_id);
  return o ? `#${o.opportunity_number || o.id} - ${o.name}` : '';
});

const filteredOptyList = computed(() => {
  const query = modalSearchQuery.value.toLowerCase();
  return closedOpty.value.filter(o => 
    o.name?.toLowerCase().includes(query) || 
    String(o.opportunity_number || o.id).toLowerCase().includes(query) ||
    o.customer?.customer_name?.toLowerCase().includes(query)
  );
});

const openSelectModal = () => {
  modalSearchQuery.value = '';
  showSelectModal.value = true;
};

const selectOpty = (opty) => {
  formData.value.opty_id = opty.id;
  formData.value.company_name = opty.customer?.customer_name || '';
  optyNumberRef.value = opty.opportunity_number || opty.id;
  
  if (opty.customer_id) {
    formData.value.customer_id = String(opty.customer_id).padStart(3, '0');
  } else {
    formData.value.customer_id = '';
  }
  
  showSelectModal.value = false;
};

const previewSiaNumber = computed(() => {
  const year = new Date().getFullYear();
  const cust = formData.value.customer_id ? String(formData.value.customer_id).padStart(3, '0') : '[CUST]';
  return `${year}${cust}XXX`;
});

const formatIDR = (value) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(value)
}

const downloadContract = async (contract) => {
  printData.value = {
    sia_number: contract.sia_number,
    date: new Date(contract.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }),
    company_name: contract.company_name,
    customer_id: contract.customer_id,
    opty_ref: contract.opty?.opportunity_number || contract.opty_id
  }
  
  await nextTick()
  
  // Use native print dialog
  window.print();
}

const showNotification = (message, type = 'success') => {
  notification.value = { message, type }
  setTimeout(() => { notification.value = null }, 4000)
}

const fetchData = async () => {
  try {
    // Fetch Opty to filter Closed Won
    const optysRes = await api.get('/optys')
    closedOpty.value = optysRes.data.filter(d => d.stage === 'Closed Won')

    // Fetch SIA Contracts
    const contractsRes = await api.get('/sia-contracts')
    contracts.value = contractsRes.data
  } catch (error) {
    console.error('Error fetching data', error)
  }
}

const generateContract = async () => {
  if (!formData.value.opty_id || !formData.value.customer_id || !formData.value.company_name) {
    showNotification('Please complete all required fields.', 'error');
    return;
  }
  
  try {
    isGenerating.value = true
    await api.post('/sia-contracts', formData.value)
    showNotification('SIA Contract Generated successfully!')
    
    // Reset
    formData.value = { opty_id: '', customer_id: '', company_name: '' }
    optyNumberRef.value = ''
    fetchData()
  } catch (error) {
    showNotification(error.response?.data?.message || 'Failed to generate contract', 'error')
  } finally {
    isGenerating.value = false
  }
}

onMounted(() => {
  fetchData()
})
</script>

<style scoped>
.animate-fade-in { animation: fadeIn 0.5s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
.animate-modal-in { animation: modalScale 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
@keyframes modalScale { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
.slide-down-enter-active, .slide-down-leave-active { transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55); }
.slide-down-enter-from, .slide-down-leave-to { opacity: 0; transform: translateY(-20px); }
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

@media print {
  body * {
    visibility: hidden;
  }
  #printTemplate, #printTemplate * {
    visibility: visible;
  }
  #printTemplate {
    display: block !important;
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    margin: 0;
    padding: 20px;
  }
}
</style>
