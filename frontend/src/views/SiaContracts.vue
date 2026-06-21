<template>
  <div class="pb-12 animate-fade-in h-full flex flex-col">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900 tracking-tight">SIA Contracts</h1>
        <p class="text-gray-500 mt-1">Generate Service Integration Agreements for Closed Deals.</p>
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
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Select Deal (Closed Won)</label>
            <select v-model="formData.deal_id" required class="w-full bg-gray-50 border border-gray-200 text-gray-900 rounded-xl px-4 py-3 font-medium focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none appearance-none cursor-pointer shadow-sm">
              <option value="" disabled>Select a closed deal...</option>
              <option v-for="deal in closedDeals" :key="deal.id" :value="deal.id">
                #{{ deal.id }} - {{ deal.name }} ({{ formatIDR(deal.amount) }})
              </option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Customer Number (3 Digits)</label>
            <input type="text" v-model="formData.customer_id" required maxlength="3" pattern="\d{3}" placeholder="e.g. 123" class="w-full bg-gray-50 border border-gray-200 text-gray-900 rounded-xl px-4 py-3 font-mono font-medium focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none shadow-sm" />
            <p class="text-xs text-gray-400 mt-1">Will be used for SIA numbering (YYYY + CUST + 001)</p>
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Company Legal Name</label>
            <input type="text" v-model="formData.company_name" required placeholder="PT. Enterprise Maju" class="w-full bg-gray-50 border border-gray-200 text-gray-900 rounded-xl px-4 py-3 font-medium focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none shadow-sm" />
          </div>
          
          <div class="mt-auto pt-8">
            <button type="submit" :disabled="isGenerating" class="w-full py-4 rounded-xl font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-md transition-all flex items-center justify-center gap-2 transform hover:-translate-y-1">
              <span v-if="isGenerating" class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
              <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
              Generate Contract & Lock Deal
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
            <p>The Provider agrees to furnish software integration services as specified in the agreed Deal Reference #{{ formData.deal_id || '[N/A]' }}.</p>
            
            <p class="mt-4 font-bold">2. FEES AND PAYMENT</p>
            <p>The Client agrees to pay the Provider the amount agreed upon in the aforementioned Deal prior to the commencement of the integration.</p>
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
              <th class="py-3 text-left text-xs font-bold text-gray-400 uppercase">Deal Value</th>
              <th class="py-3 text-left text-xs font-bold text-gray-400 uppercase">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-for="contract in contracts" :key="contract.id" class="hover:bg-gray-50">
              <td class="py-3 font-mono text-sm font-bold text-blue-600">{{ contract.sia_number }}</td>
              <td class="py-3 text-sm text-gray-700 font-semibold">{{ contract.company_name }}</td>
              <td class="py-3 text-sm text-gray-600">{{ formatIDR(contract.deal?.amount || 0) }}</td>
              <td class="py-3">
                <span class="bg-green-50 text-green-700 border border-green-200 px-2 py-1 rounded text-xs font-bold uppercase tracking-wide">
                  {{ contract.status }}
                </span>
              </td>
            </tr>
            <tr v-if="contracts.length === 0">
              <td colspan="4" class="py-6 text-center text-gray-400 font-medium text-sm">No contracts generated yet.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import api from '../api/axios'

const closedDeals = ref([])
const contracts = ref([])
const isGenerating = ref(false)
const notification = ref(null)

const formData = ref({
  deal_id: '',
  customer_id: '',
  company_name: ''
})

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

const showNotification = (message, type = 'success') => {
  notification.value = { message, type }
  setTimeout(() => { notification.value = null }, 4000)
}

const fetchData = async () => {
  try {
    // Fetch Deals to filter closed_won
    const dealsRes = await api.get('/deals')
    closedDeals.value = dealsRes.data.filter(d => d.stage === 'closed_won')

    // Fetch SIA Contracts
    const contractsRes = await api.get('/sia-contracts')
    contracts.value = contractsRes.data
  } catch (error) {
    console.error('Error fetching data', error)
  }
}

const generateContract = async () => {
  try {
    isGenerating.value = true
    await api.post('/sia-contracts', formData.value)
    showNotification('SIA Contract Generated successfully!')
    
    // Reset
    formData.value = { deal_id: '', customer_id: '', company_name: '' }
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
.slide-down-enter-active, .slide-down-leave-active { transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55); }
.slide-down-enter-from, .slide-down-leave-to { opacity: 0; transform: translateY(-20px); }
</style>
