<template>
  <div class="pb-12 animate-fade-in">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Leads Management</h1>
        <p class="text-gray-500 mt-1">Track and convert your sales prospects.</p>
      </div>
      <button @click="showModal = true" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl shadow-md transition-all font-semibold flex items-center gap-2 transform hover:-translate-y-0.5">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Add New Lead
      </button>
    </div>

    <!-- Notification -->
    <transition name="slide-down">
      <div v-if="notification" class="fixed top-20 right-8 z-50 px-6 py-4 rounded-xl shadow-2xl flex items-center gap-3 font-semibold text-white" :class="notification.type === 'error' ? 'bg-red-600' : 'bg-green-600'">
        <svg v-if="notification.type === 'success'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        {{ notification.message }}
      </div>
    </transition>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
      <!-- Main Content (Table) -->
      <div class="lg:col-span-3">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden relative">
          <div v-if="isLoading" class="flex flex-col items-center justify-center h-64 text-gray-500 font-semibold">
            <div class="loader-spinner mb-3"></div>
            Loading leads...
          </div>
          
          <div class="overflow-x-auto" v-else>
            <table class="min-w-full divide-y divide-gray-100">
              <thead class="bg-gray-50/50">
                <tr>
                  <th class="py-4 px-6 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Lead Name</th>
                  <th class="py-4 px-6 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Company</th>
                  <th class="py-4 px-6 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Email</th>
                  <th class="py-4 px-6 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                  <th class="py-4 px-6 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <tr v-if="leads.length === 0">
                  <td colspan="5" class="py-8 text-center text-gray-500 font-medium">No leads found. Start by adding one.</td>
                </tr>
                <tr v-for="lead in leads" :key="lead.id" class="hover:bg-gray-50 transition-colors group">
                  <td class="py-4 px-6">
                    <div class="flex items-center gap-3">
                      <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs">
                        {{ lead.name.charAt(0).toUpperCase() }}
                      </div>
                      <span class="font-semibold text-gray-800">{{ lead.name }}</span>
                    </div>
                  </td>
                  <td class="py-4 px-6 text-sm text-gray-600 font-medium">{{ lead.company || '-' }}</td>
                  <td class="py-4 px-6 text-sm text-gray-600">{{ lead.email || '-' }}</td>
                  <td class="py-4 px-6">
                    <span class="px-3 py-1 text-xs font-bold rounded-full shadow-sm"
                      :class="{
                        'bg-blue-50 text-blue-700 border border-blue-200': lead.status === 'new',
                        'bg-amber-50 text-amber-700 border border-amber-200': lead.status === 'contacted',
                        'bg-green-50 text-green-700 border border-green-200': lead.status === 'qualified'
                      }">
                      {{ lead.status.toUpperCase() }}
                    </span>
                  </td>
                  <td class="py-4 px-6 text-right">
                    <button @click="convertToDeal(lead)" v-if="lead.status !== 'qualified'" class="text-xs font-bold text-blue-600 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition-colors border border-blue-100 shadow-sm">
                      Convert to Deal
                    </button>
                    <span v-else class="text-xs font-bold text-green-600">Converted</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Right Sidebar (Analytics) -->
      <div class="lg:col-span-1">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col items-center justify-center text-center">
          <h3 class="text-sm font-bold text-gray-500 uppercase tracking-widest mb-6">Conversion Rate</h3>
          
          <!-- CSS Circular Progress -->
          <div class="relative w-32 h-32 flex items-center justify-center rounded-full bg-gray-50 shadow-inner mb-4">
            <svg class="w-32 h-32 transform -rotate-90">
              <circle cx="64" cy="64" r="56" stroke="currentColor" stroke-width="12" fill="transparent" class="text-gray-100" />
              <circle cx="64" cy="64" r="56" stroke="currentColor" stroke-width="12" fill="transparent" :stroke-dasharray="351.8" :stroke-dashoffset="351.8 - (351.8 * conversionRate / 100)" class="text-blue-600 transition-all duration-1000 ease-out" />
            </svg>
            <div class="absolute inset-0 flex flex-col items-center justify-center">
              <span class="text-2xl font-black text-gray-900">{{ Math.round(conversionRate) }}%</span>
            </div>
          </div>
          
          <div class="flex justify-between w-full text-xs font-bold text-gray-400 mt-2 px-2">
            <span>Total: {{ leads.length }}</span>
            <span>Converted: {{ qualifiedCount }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Form -->
    <transition name="fade">
      <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/40 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden transform transition-all animate-modal-in border border-gray-100">
          <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-900">Add New Lead</h3>
            <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
          </div>
          <form @submit.prevent="saveLead" class="p-6 space-y-4">
            <div>
              <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">Lead Name</label>
              <input type="text" v-model="newLead.name" required class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all shadow-sm" />
            </div>
            <div>
              <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">Company</label>
              <input type="text" v-model="newLead.company" class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all shadow-sm" />
            </div>
            <div>
              <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">Email</label>
              <input type="email" v-model="newLead.email" class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all shadow-sm" />
            </div>
            <div>
              <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">Phone</label>
              <input type="text" v-model="newLead.phone" class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all shadow-sm" />
            </div>
            <div class="pt-4 flex justify-end gap-2 border-t border-gray-100 mt-6">
              <button type="button" @click="showModal = false" class="px-4 py-2 rounded-lg font-semibold text-gray-600 hover:bg-gray-50 transition-colors text-sm border border-transparent">Cancel</button>
              <button type="submit" :disabled="isSaving" class="px-4 py-2 rounded-lg font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition-colors text-sm flex items-center gap-2">
                <span v-if="isSaving" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                Save Lead
              </button>
            </div>
          </form>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import api from '../api/axios'
import Swal from 'sweetalert2'

const router = useRouter()
const leads = ref([])
const isLoading = ref(true)
const isSaving = ref(false)
const showModal = ref(false)
const notification = ref(null)

const newLead = ref({
  name: '',
  company: '',
  email: '',
  phone: ''
})

const qualifiedCount = computed(() => leads.value.filter(l => l.status === 'qualified').length)
const conversionRate = computed(() => leads.value.length ? (qualifiedCount.value / leads.value.length) * 100 : 0)

const showNotification = (message, type = 'success') => {
  notification.value = { message, type }
  setTimeout(() => { notification.value = null }, 3000)
}

const fetchLeads = async () => {
  try {
    const response = await api.get('/leads')
    leads.value = response.data
    isLoading.value = false
  } catch (error) {
    console.error(error)
    showNotification('Failed to fetch leads', 'error')
  }
}

const saveLead = async () => {
  try {
    isSaving.value = true
    await api.post('/leads', newLead.value)
    showNotification('Lead added successfully!')
    showModal.value = false
    newLead.value = { name: '', company: '', email: '', phone: '' }
    fetchLeads()
  } catch (error) {
    showNotification(error.response?.data?.message || 'Failed to add lead', 'error')
  } finally {
    isSaving.value = false
  }
}

const convertToDeal = async (lead) => {
  const result = await Swal.fire({
    title: 'Convert to Deal?',
    text: `Are you sure you want to convert ${lead.name} to a Deal?`,
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#3b82f6',
    cancelButtonColor: '#9ca3af',
    confirmButtonText: 'Yes, Convert'
  })

  if (result.isConfirmed) {
    try {
      // 1. Mark lead as qualified
      await api.put(`/leads/${lead.id}`, { ...lead, status: 'qualified' })
      
      // 2. Create Contact
      const contactRes = await api.post('/contacts', {
        name: lead.name,
        email: lead.email,
        phone: lead.phone,
        company: lead.company
      })
      
      showNotification('Lead converted successfully!')
      fetchLeads()
    } catch (error) {
      console.error(error)
      Swal.fire('Error', error.response?.data?.message || 'Failed to convert lead', 'error')
    }
  }
}
onMounted(() => {
  fetchLeads()
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
