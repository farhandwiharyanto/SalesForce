<template>
  <div class="pb-12 animate-fade-in">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Leads Management</h1>
        <p class="text-gray-500 mt-1">Track and convert your sales prospects.</p>
      </div>
      <div class="flex items-center gap-3">
        <button @click="showImportModal = true" class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 px-4 py-2.5 rounded-xl font-bold shadow-sm transition-all flex items-center gap-2">
          <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
          Import
        </button>
        <button @click="openAddModal" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl shadow-md transition-all font-semibold flex items-center gap-2 transform hover:-translate-y-0.5">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
          Add New Lead
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

    <div>
      <!-- Main Content (Table) -->
      <div class="w-full">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden relative">
          <div v-if="isLoading" class="flex flex-col items-center justify-center h-64 text-gray-500 font-semibold">
            <div class="loader-spinner mb-3"></div>
            Loading leads...
          </div>
          
          <div class="overflow-x-auto" v-else>
            <table class="min-w-full divide-y divide-gray-100">
              <thead class="bg-gray-50/50">
                <tr>
                  <th class="py-4 px-6 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Lead No.</th>
                  <th class="py-4 px-6 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Customer</th>
                  <th class="py-4 px-6 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                  <th class="py-4 px-6 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <tr v-if="leads.length === 0">
                  <td colspan="4" class="py-8 text-center text-gray-500 font-medium">No leads found. Start by adding one.</td>
                </tr>
                <tr v-for="lead in leads" :key="lead.id" class="hover:bg-gray-50 transition-colors group">
                  <td class="py-4 px-6">
                    <router-link :to="`/leads/${lead.id}`" class="text-sm font-mono font-bold text-blue-600 hover:text-blue-800 transition-colors">{{ lead.lead_number }}</router-link>
                  </td>
                  <td class="py-4 px-6 text-sm text-gray-600 font-medium">{{ lead.customer?.customer_name || '-' }}</td>
                  <td class="py-4 px-6">
                    <select v-model="lead.status" @change="updateStatus(lead)" class="text-xs font-bold rounded-full shadow-sm border px-3 py-1 outline-none"
                      :class="{
                        'bg-blue-50 text-blue-700 border-blue-200': lead.status === 'Open',
                        'bg-amber-50 text-amber-700 border-amber-200': lead.status === 'Contacted',
                        'bg-green-50 text-green-700 border-green-200': lead.status === 'Converted',
                        'bg-purple-50 text-purple-700 border-purple-200': lead.status === 'Qualified',
                        'bg-red-50 text-red-700 border-red-200': lead.status === 'Unqualified'
                      }">
                      <option value="Open">Open</option>
                      <option value="Contacted">Contacted</option>
                      <option value="Qualified">Qualified</option>
                      <option value="Unqualified">Unqualified</option>
                      <option value="Converted" disabled>Converted</option>
                    </select>
                  </td>
                  <td class="py-4 px-6 text-right flex justify-end gap-2">
                    <button @click="openEditModal(lead)" class="text-xs font-bold text-blue-600 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition-colors border border-blue-200 shadow-sm inline-flex items-center">
                      Edit
                    </button>
                    <router-link :to="`/leads/${lead.id}`" class="text-xs font-bold text-gray-600 bg-gray-50 hover:bg-gray-100 px-3 py-1.5 rounded-lg transition-colors border border-gray-200 shadow-sm inline-flex items-center gap-1">
                      View Details
                    </router-link>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Form: Add/Edit Lead -->
    <transition name="fade">
      <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/40 backdrop-blur-sm p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-xl overflow-hidden transform transition-all animate-modal-in border border-gray-100 my-8">
          <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center sticky top-0 z-10">
            <h3 class="text-lg font-bold text-gray-900">{{ editingId ? 'Edit Lead' : 'Add New Lead' }}</h3>
            <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
          </div>
          <form @submit.prevent="saveLead" class="p-6 space-y-5">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">First Name</label>
                <input type="text" v-model="newLead.first_name" required class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all shadow-sm" />
              </div>
              <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">Last Name</label>
                <input type="text" v-model="newLead.last_name" class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all shadow-sm" />
              </div>
            </div>

            <div>
              <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">Email</label>
              <input type="email" v-model="newLead.email" required class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all shadow-sm" />
            </div>

            <div>
              <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">Customer <span class="text-red-500">*</span></label>
              <div class="flex">
                <input type="text" :value="selectedCustomerName" readonly required placeholder="Select customer..." class="flex-grow bg-white border border-gray-200 border-r-0 rounded-l-lg px-3 py-2.5 text-sm outline-none shadow-sm cursor-not-allowed" />
                <button type="button" @click="openSelectModal('customer')" class="bg-gray-100 border border-gray-200 rounded-r-lg px-4 hover:bg-gray-200 transition-colors shadow-sm flex items-center justify-center">
                  <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </button>
              </div>
            </div>

            <div>
              <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">Lead Owner <span class="text-red-500">*</span></label>
              <div class="flex">
                <input type="text" :value="selectedOwnerName" readonly required placeholder="Select owner..." class="flex-grow bg-white border border-gray-200 border-r-0 rounded-l-lg px-3 py-2.5 text-sm outline-none shadow-sm cursor-not-allowed" />
                <button type="button" @click="openSelectModal('owner')" class="bg-gray-100 border border-gray-200 rounded-r-lg px-4 hover:bg-gray-200 transition-colors shadow-sm flex items-center justify-center">
                  <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </button>
              </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">Product Name <span class="text-red-500">*</span></label>
                <div class="flex">
                  <input type="text" :value="selectedProductName" readonly required placeholder="Select product..." class="flex-grow bg-white border border-gray-200 border-r-0 rounded-l-lg px-3 py-2.5 text-sm outline-none shadow-sm cursor-not-allowed" />
                  <button type="button" @click="openSelectModal('product')" class="bg-gray-100 border border-gray-200 rounded-r-lg px-3 hover:bg-gray-200 transition-colors shadow-sm flex items-center justify-center">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                  </button>
                </div>
              </div>
              <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">Product Number</label>
                <input type="text" :value="selectedProduct ? selectedProduct.product_number : ''" disabled class="w-full bg-gray-50 border border-gray-200 text-gray-500 font-mono rounded-lg px-3 py-2.5 text-sm shadow-sm cursor-not-allowed" placeholder="(Auto filled)" />
              </div>
            </div>

            <div>
              <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">Status</label>
              <select v-model="newLead.status" class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all shadow-sm">
                <option value="Open">Open</option>
                <option value="Contacted">Contacted</option>
                <option value="Qualified">Qualified</option>
                <option value="Unqualified">Unqualified</option>
              </select>
            </div>

            <div class="pt-4 flex justify-end gap-2 border-t border-gray-100 mt-6">
              <button type="button" @click="showModal = false" class="px-4 py-2 rounded-lg font-semibold text-gray-600 hover:bg-gray-50 transition-colors text-sm border border-transparent">Cancel</button>
              <button type="submit" :disabled="isSaving" class="px-4 py-2 rounded-lg font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition-colors text-sm flex items-center gap-2">
                <span v-if="isSaving" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                {{ editingId ? 'Update Lead' : 'Save Lead' }}
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
          
          <!-- Modal Header -->
          <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center shrink-0">
            <h3 class="text-lg font-bold text-gray-900 capitalize">Select {{ selectModalType }}</h3>
            <button @click="showSelectModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
          </div>
          
          <!-- Search Box inside Modal -->
          <div class="p-4 border-b border-gray-100 shrink-0 bg-white">
            <div class="relative">
              <span class="absolute left-3 top-2.5 text-gray-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
              </span>
              <input type="text" v-model="modalSearchQuery" placeholder="Search..." class="w-full bg-gray-50 border border-gray-200 rounded-lg pl-10 pr-3 py-2 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all" />
            </div>
          </div>

          <!-- Table Content -->
          <div class="overflow-y-auto flex-grow p-0">
            <table class="min-w-full divide-y divide-gray-100">
              <thead class="bg-gray-50 sticky top-0 shadow-sm">
                <tr v-if="selectModalType === 'customer'">
                  <th class="py-3 px-6 text-left text-xs font-bold text-gray-500 uppercase">Customer Name</th>
                  <th class="py-3 px-6 text-left text-xs font-bold text-gray-500 uppercase">Email</th>
                  <th class="py-3 px-6 text-left text-xs font-bold text-gray-500 uppercase">Customer No</th>
                  <th class="py-3 px-6 text-center text-xs font-bold text-gray-500 uppercase">Action</th>
                </tr>
                <tr v-else-if="selectModalType === 'owner'">
                  <th class="py-3 px-6 text-left text-xs font-bold text-gray-500 uppercase">Name</th>
                  <th class="py-3 px-6 text-left text-xs font-bold text-gray-500 uppercase">Email</th>
                  <th class="py-3 px-6 text-left text-xs font-bold text-gray-500 uppercase">Role</th>
                  <th class="py-3 px-6 text-center text-xs font-bold text-gray-500 uppercase">Action</th>
                </tr>
                <tr v-else-if="selectModalType === 'product'">
                  <th class="py-3 px-6 text-left text-xs font-bold text-gray-500 uppercase">Product No</th>
                  <th class="py-3 px-6 text-left text-xs font-bold text-gray-500 uppercase">Name</th>
                  <th class="py-3 px-6 text-left text-xs font-bold text-gray-500 uppercase">Price</th>
                  <th class="py-3 px-6 text-center text-xs font-bold text-gray-500 uppercase">Action</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100 bg-white">
                <template v-if="filteredModalList.length > 0">
                  <tr v-for="item in filteredModalList" :key="item.id" class="hover:bg-blue-50/50 transition-colors">
                    
                    <!-- Customer Row -->
                    <template v-if="selectModalType === 'customer'">
                      <td class="py-3 px-6 text-sm font-semibold text-gray-800">{{ item.customer_name }}</td>
                      <td class="py-3 px-6 text-sm text-gray-600">{{ item.email || '-' }}</td>
                      <td class="py-3 px-6 text-sm text-gray-500 font-mono">{{ item.nomor_customer }}</td>
                    </template>

                    <!-- Owner Row -->
                    <template v-else-if="selectModalType === 'owner'">
                      <td class="py-3 px-6 text-sm font-semibold text-gray-800">{{ item.first_name }} {{ item.last_name }}</td>
                      <td class="py-3 px-6 text-sm text-gray-600">{{ item.email }}</td>
                      <td class="py-3 px-6 text-sm text-gray-500">{{ item.role }}</td>
                    </template>

                    <!-- Product Row -->
                    <template v-else-if="selectModalType === 'product'">
                      <td class="py-3 px-6 text-sm font-mono text-gray-500">{{ item.product_number }}</td>
                      <td class="py-3 px-6 text-sm font-semibold text-gray-800">{{ item.name }}</td>
                      <td class="py-3 px-6 text-sm text-gray-600">Rp {{ item.price.toLocaleString('id-ID') }}</td>
                    </template>

                    <td class="py-3 px-6 text-center">
                      <button type="button" @click="selectItem(item)" class="bg-green-500 hover:bg-green-600 text-white px-4 py-1.5 rounded-lg text-xs font-bold shadow-sm transition-colors">
                        Select
                      </button>
                    </td>
                  </tr>
                </template>
                <tr v-else>
                  <td colspan="4" class="py-8 text-center text-gray-500 font-medium">No records found.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </transition>

    <ImportModal 
      :show="showImportModal"
      moduleName="Leads"
      :columns="['First Name', 'Last Name', 'Email', 'Status', 'Customer ID', 'Owner ID', 'Product ID']"
      :requiredColumns="['First Name', 'Last Name', 'Email']"
      :sampleRow="['John', 'Doe', 'john.doe@example.com', 'New', '12', '3', '5']"
      apiEndpoint="/leads/bulk"
      @close="showImportModal = false"
      @import-success="onImportSuccess"
      @import-error="onImportError"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import api from '../api/axios'
import ImportModal from '../components/ImportModal.vue'

const router = useRouter()
const authStore = useAuthStore()
const leads = ref([])
const customers = ref([])
const users = ref([])
const products = ref([])

const isLoading = ref(true)

const showImportModal = ref(false)

const onImportSuccess = (msg) => {
  showNotification(msg, 'success')
  fetchData()
}

const onImportError = (msg) => {
  showNotification(msg, 'error')
}
const isSaving = ref(false)
const showModal = ref(false)
const editingId = ref(null)
const notification = ref(null)

const newLead = ref({
  first_name: '',
  last_name: '',
  email: '',
  customer_id: null,
  owner_id: null,
  product_id: null,
  status: 'Open'
})

// Search Modal State
const showSelectModal = ref(false)
const selectModalType = ref('') // 'customer', 'owner', 'product'
const modalSearchQuery = ref('')

const openSelectModal = (type) => {
  selectModalType.value = type
  modalSearchQuery.value = ''
  showSelectModal.value = true
}

const filteredModalList = computed(() => {
  const query = modalSearchQuery.value.toLowerCase()
  if (selectModalType.value === 'customer') {
    return customers.value.filter(c => 
      c.customer_name?.toLowerCase().includes(query) || 
      c.nomor_customer?.toLowerCase().includes(query)
    )
  } else if (selectModalType.value === 'owner') {
    return users.value.filter(u => 
      (u.first_name + ' ' + (u.last_name||'')).toLowerCase().includes(query) ||
      u.email?.toLowerCase().includes(query)
    )
  } else if (selectModalType.value === 'product') {
    return products.value.filter(p => 
      p.name?.toLowerCase().includes(query) || 
      p.product_number?.toLowerCase().includes(query)
    )
  }
  return []
})

const selectItem = (item) => {
  if (selectModalType.value === 'customer') {
    newLead.value.customer_id = item.id
  } else if (selectModalType.value === 'owner') {
    newLead.value.owner_id = item.id
  } else if (selectModalType.value === 'product') {
    newLead.value.product_id = item.id
  }
  showSelectModal.value = false
}

const selectedCustomerName = computed(() => {
  const c = customers.value.find(c => c.id === newLead.value.customer_id)
  return c ? c.customer_name : ''
})

const selectedOwnerName = computed(() => {
  const u = users.value.find(u => u.id === newLead.value.owner_id)
  return u ? `${u.first_name} ${u.last_name || ''}`.trim() : ''
})

const selectedProductName = computed(() => {
  const p = products.value.find(p => p.id === newLead.value.product_id)
  return p ? p.name : ''
})

const selectedProduct = computed(() => products.value.find(p => p.id === newLead.value.product_id))

const showNotification = (message, type = 'success') => {
  notification.value = { message, type }
  setTimeout(() => { notification.value = null }, 3000)
}

const fetchData = async () => {
  try {
    const [leadsRes, custRes, usersRes, prodRes] = await Promise.all([
      api.get('/leads'),
      api.get('/customers'),
      api.get('/users'),
      api.get('/products')
    ])
    leads.value = leadsRes.data
    customers.value = custRes.data
    users.value = usersRes.data
    products.value = prodRes.data
    isLoading.value = false
  } catch (error) {
    console.error(error)
    showNotification('Failed to fetch data', 'error')
  }
}

const openAddModal = () => {
  editingId.value = null;
  newLead.value = { 
    first_name: authStore.user?.first_name || '', 
    last_name: authStore.user?.last_name || '', 
    email: authStore.user?.email || '', 
    customer_id: null, 
    owner_id: null, 
    product_id: null, 
    status: 'Open' 
  }
  showModal.value = true
}

const openEditModal = (lead) => {
  editingId.value = lead.id;
  newLead.value = {
    first_name: lead.first_name,
    last_name: lead.last_name,
    email: lead.email,
    customer_id: lead.customer_id,
    owner_id: lead.owner_id,
    product_id: lead.product_id,
    status: lead.status
  }
  showModal.value = true
}

const saveLead = async () => {
  if (!newLead.value.customer_id || !newLead.value.owner_id || !newLead.value.product_id) {
    showNotification('Please select valid customer, owner, and product from the list.', 'error')
    return
  }

  try {
    isSaving.value = true
    if (editingId.value) {
      await api.put(`/leads/${editingId.value}`, newLead.value)
      showNotification('Lead updated successfully!')
    } else {
      await api.post('/leads', newLead.value)
      showNotification('Lead added successfully!')
    }
    showModal.value = false
    fetchData()
  } catch (error) {
    showNotification(error.response?.data?.message || 'Failed to save lead', 'error')
  } finally {
    isSaving.value = false
  }
}

const updateStatus = async (lead) => {
  try {
    const response = await api.put(`/leads/${lead.id}`, { status: lead.status })
    showNotification('Status updated successfully!')
    Object.assign(lead, response.data)
  } catch (error) {
    showNotification('Failed to update status', 'error')
    fetchData() // Revert
  }
}

onMounted(() => {
  fetchData()
})
</script>

<style scoped>
.animate-fade-in { animation: fadeIn 0.4s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
.animate-modal-in { animation: modalScale 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
@keyframes modalScale { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
.slide-down-enter-active, .slide-down-leave-active { transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55); }
.slide-down-enter-from, .slide-down-leave-to { opacity: 0; transform: translateY(-20px); }
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
.loader-spinner { width: 30px; height: 30px; border: 3px solid #f3f3f3; border-top: 3px solid #2563eb; border-radius: 50%; animation: spin 0.8s linear infinite; }
@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
</style>
