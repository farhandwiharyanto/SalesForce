<template>
  <div class="pb-12 animate-fade-in h-full flex flex-col">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Service Instance Accounts</h1>
        <p class="text-gray-500 mt-1">Manage Service Instance Accounts generated from Closed Opty.</p>
      </div>
      <div class="flex items-center gap-3">
        <button @click="showImportModal = true" class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 px-4 py-2.5 rounded-xl font-bold shadow-sm transition-all flex items-center gap-2">
          <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
          Import
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

    <!-- Main Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex-grow">
      <div class="overflow-x-auto">
        <table class="min-w-full">
          <thead>
            <tr class="border-b border-gray-100">
              <th class="py-3 px-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wide">SIA No.</th>
              <th class="py-3 px-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wide">Customer Name</th>
              <th class="py-3 px-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wide">Billing Account No.</th>
              <th class="py-3 px-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wide">Package Name</th>
              <th class="py-3 px-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wide">Status</th>
              <th class="py-3 px-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wide">Contract</th>
              <th class="py-3 px-4 text-right text-xs font-bold text-gray-400 uppercase tracking-wide">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-for="contract in contracts" :key="contract.id" class="hover:bg-gray-50 border-b border-gray-50">
              <td class="py-4 px-4 font-mono text-sm font-bold">
                <router-link :to="`/service-instance-accounts/${contract.id}`" class="text-blue-600 hover:text-blue-800 hover:underline">
                  {{ contract.sia_number }}
                </router-link>
              </td>
              <td class="py-4 px-4 text-sm text-gray-700 font-semibold">{{ contract.opty?.customer?.customer_name || contract.company_name }}</td>
              <td class="py-4 px-4 font-mono text-sm font-medium text-gray-500">{{ contract.billing_account_number || '-' }}</td>
              <td class="py-4 px-4 text-sm text-gray-600 max-w-[180px] truncate" :title="contract.opty?.product?.name">{{ contract.opty?.product?.name || '-' }}</td>
              <td class="py-4 px-4">
                <span class="px-2 py-1 rounded text-xs font-bold uppercase tracking-wide"
                      :class="{
                        'bg-blue-50 text-blue-700 border border-blue-200': contract.status === 'Registered',
                        'bg-green-50 text-green-700 border border-green-200': contract.status === 'Active',
                        'bg-red-50 text-red-700 border border-red-200': contract.status === 'Isolir',
                        'bg-gray-50 text-gray-700 border border-gray-200': !['Registered', 'Active', 'Isolir'].includes(contract.status)
                      }">
                  {{ contract.status }}
                </span>
              </td>
              <td class="py-4 px-4 text-sm font-medium">
                <router-link v-if="contract.contract_id" :to="`/contracts/${contract.contract_id}`" class="text-blue-600 hover:text-blue-800 hover:underline font-bold font-mono">
                  {{ contract.contract?.contract_number || 'Linked' }}
                </router-link>
                <span v-else class="text-gray-400">Pending</span>
              </td>
              <td class="py-4 px-4 text-right">
                <button @click="downloadContract(contract)" class="text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg text-xs font-bold transition-colors shadow-sm inline-flex items-center gap-1">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                  PDF
                </button>
              </td>
            </tr>
            <tr v-if="contracts.length === 0">
              <td colspan="7" class="py-8 text-center text-gray-400 font-medium text-sm">No Service Instance Accounts found.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

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
    <ImportModal 
      :show="showImportModal"
      moduleName="ServiceInstanceAccount"
      :columns="['SIA Number', 'Opty ID', 'Customer ID', 'Company Name', 'Billing Account Number', 'Status']"
      :requiredColumns="['Customer ID', 'Company Name']"
      :sampleRow="['2026123001', '4', '123', 'Acme Corporation', 'BA-999123', 'Registered']"
      apiEndpoint="/service-instance-accounts/bulk"
      @close="showImportModal = false"
      @import-success="onImportSuccess"
      @import-error="onImportError"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue'
import api from '../api/axios'
import ImportModal from '../components/ImportModal.vue'

const contracts = ref([])
const notification = ref(null)

const printTemplate = ref(null)
const printData = ref({})

const showImportModal = ref(false)

const onImportSuccess = (msg) => {
  showNotification(msg, 'success')
  fetchData()
}

const onImportError = (msg) => {
  showNotification(msg, 'error')
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
    // Fetch SIA Contracts
    const contractsRes = await api.get('/service-instance-accounts')
    contracts.value = contractsRes.data
  } catch (error) {
    console.error('Error fetching data', error)
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
