<template>
  <div class="pb-12 animate-fade-in">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
      <div class="flex items-center gap-3">
        <button @click="router.push('/leads')" class="text-gray-400 hover:text-blue-600 transition-colors p-2 -ml-2 rounded-lg hover:bg-blue-50">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </button>
        <div>
          <h1 class="text-3xl font-bold text-gray-900 tracking-tight flex items-center gap-3">
            Lead Detail
            <span v-if="lead" class="text-xl font-mono text-gray-400 font-normal">#{{ lead.lead_number }}</span>
          </h1>
          <p class="text-gray-500 mt-1">View comprehensive information about this lead.</p>
        </div>
      </div>
      <div class="flex gap-2">
        <button @click="showUpdatesModal = true" class="text-sm font-bold text-gray-600 bg-white hover:bg-gray-50 px-4 py-2.5 rounded-xl transition-colors border border-gray-200 shadow-sm flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          View Updates
        </button>
        <button v-if="lead && lead.status !== 'Converted'" @click="convertToOpty" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl shadow-md transition-all font-semibold flex items-center gap-2 transform hover:-translate-y-0.5">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
          Convert to Opty
        </button>
        <div v-else-if="lead && lead.status === 'Converted'" class="bg-green-50 text-green-700 border border-green-200 px-5 py-2.5 rounded-xl font-semibold flex items-center gap-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
          Converted
        </div>
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

    <div v-if="isLoading" class="flex flex-col items-center justify-center h-64 text-gray-500 font-semibold bg-white rounded-xl shadow-sm border border-gray-100">
      <div class="loader-spinner mb-3"></div>
      Loading lead details...
    </div>

    <div v-else-if="lead" class="space-y-6">
      
      <!-- Lead Information Section -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gray-50/80 px-6 py-4 border-b border-gray-100 flex items-center gap-2">
          <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          <h2 class="text-sm font-bold text-gray-800 uppercase tracking-widest">Lead Information</h2>
        </div>
        
        <div class="p-0">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 divide-y lg:divide-y-0 lg:divide-x divide-gray-100">
            
            <!-- Column 1 -->
            <div class="flex flex-col divide-y divide-gray-100">
              <div class="flex py-3 px-6 hover:bg-gray-50 transition-colors">
                <div class="w-1/3 text-xs font-semibold text-gray-500">Lead Number</div>
                <div class="w-2/3 text-sm font-bold text-gray-900">{{ lead.lead_number }}</div>
              </div>
              <div class="flex py-3 px-6 hover:bg-gray-50 transition-colors">
                <div class="w-1/3 text-xs font-semibold text-gray-500">Created By</div>
                <div class="w-2/3 text-sm font-semibold text-blue-600">{{ lead.owner ? (lead.owner.first_name + ' ' + (lead.owner.last_name||'')).trim() : '-' }}</div>
              </div>
              <div class="flex py-3 px-6 hover:bg-gray-50 transition-colors">
                <div class="w-1/3 text-xs font-semibold text-gray-500">Lead Owner</div>
                <div class="w-2/3 text-sm font-semibold text-blue-600">{{ lead.owner ? (lead.owner.first_name + ' ' + (lead.owner.last_name||'')).trim() : '-' }}</div>
              </div>
            </div>

            <!-- Column 2 -->
            <div class="flex flex-col divide-y divide-gray-100">
              <div class="flex py-3 px-6 hover:bg-gray-50 transition-colors">
                <div class="w-1/3 text-xs font-semibold text-gray-500">First Name</div>
                <div class="w-2/3 text-sm font-semibold text-gray-900">{{ lead.first_name }}</div>
              </div>
              <div class="flex py-3 px-6 hover:bg-gray-50 transition-colors">
                <div class="w-1/3 text-xs font-semibold text-gray-500">Last Name</div>
                <div class="w-2/3 text-sm font-semibold text-gray-900">{{ lead.last_name || '-' }}</div>
              </div>
              <div class="flex py-3 px-6 hover:bg-gray-50 transition-colors">
                <div class="w-1/3 text-xs font-semibold text-gray-500">Email</div>
                <div class="w-2/3 text-sm font-semibold text-blue-600">{{ lead.email }}</div>
              </div>
            </div>

            <!-- Column 3 -->
            <div class="flex flex-col divide-y divide-gray-100">
              <div class="flex py-3 px-6 hover:bg-gray-50 transition-colors">
                <div class="w-1/3 text-xs font-semibold text-gray-500">Customer</div>
                <div class="w-2/3 text-sm font-semibold text-gray-900">{{ lead.customer ? lead.customer.customer_name : '-' }}</div>
              </div>
              <div class="flex py-3 px-6 hover:bg-gray-50 transition-colors">
                <div class="w-1/3 text-xs font-semibold text-gray-500">Product Interest</div>
                <div class="w-2/3 text-sm font-semibold text-gray-900">{{ lead.product ? lead.product.name : '-' }}</div>
              </div>
              <div class="flex py-3 px-6 hover:bg-gray-50 transition-colors">
                <div class="w-1/3 text-xs font-semibold text-gray-500">Status</div>
                <div class="w-2/3">
                  <select v-model="lead.status" @change="updateStatus" class="text-xs font-bold rounded-full shadow-sm border px-3 py-1 outline-none"
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
                </div>
              </div>
              <div class="flex py-3 px-6 hover:bg-gray-50 transition-colors">
                <div class="w-1/3 text-xs font-semibold text-gray-500">Creation Date</div>
                <div class="w-2/3 text-sm text-gray-600">{{ new Date(lead.created_at).toLocaleString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }) }}</div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>

    <!-- Updates Timeline Modal -->
    <transition name="fade">
      <div v-if="showUpdatesModal" class="fixed inset-0 z-[70] flex items-center justify-center bg-gray-900/60 backdrop-blur-sm p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden transform transition-all animate-modal-in flex flex-col max-h-[80vh]">
          <!-- Modal Header -->
          <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center shrink-0">
            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
              <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
              Lead Updates
            </h3>
            <button @click="showUpdatesModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
          </div>
          
          <!-- Timeline Content -->
          <div class="overflow-y-auto flex-grow p-6">
            <div v-if="!lead.updates || lead.updates.length === 0" class="text-center text-gray-500 py-8">
              No updates available for this lead yet.
            </div>
            <div v-else class="relative border-l-2 border-gray-200 ml-4 space-y-8">
              <div v-for="(update, index) in lead.updates" :key="index" class="relative pl-8">
                <!-- Timeline Dot -->
                <div class="absolute -left-[9px] top-0.5 w-4 h-4 rounded-full border-2 border-white"
                     :class="{
                       'bg-green-500': update.action === 'created',
                       'bg-blue-500': update.action === 'status_updated',
                       'bg-purple-500': update.action === 'converted_to_opty'
                     }">
                </div>
                
                <!-- Content -->
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-2">
                  <div>
                    <div class="flex items-center gap-2 mb-1">
                      <span class="font-bold text-gray-900 text-sm">
                        {{ update.user ? (update.user.first_name + ' ' + (update.user.last_name || '')).trim() : 'System' }}
                      </span>
                      <span class="text-xs font-semibold px-2 py-0.5 rounded-full"
                            :class="{
                              'bg-green-100 text-green-700': update.action === 'created',
                              'bg-blue-100 text-blue-700': update.action === 'status_updated',
                              'bg-purple-100 text-purple-700': update.action === 'converted_to_opty'
                            }">
                        {{ update.action.replace(/_/g, ' ') }}
                      </span>
                    </div>
                    <p class="text-gray-600 text-sm leading-relaxed">{{ update.description }}</p>
                  </div>
                  
                  <div class="text-xs font-semibold text-gray-400 shrink-0 mt-1 sm:mt-0 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ new Date(update.created_at).toLocaleString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }) }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import api from '../api/axios'
import Swal from 'sweetalert2'

const router = useRouter()
const route = useRoute()

const lead = ref(null)
const isLoading = ref(true)
const notification = ref(null)
const showUpdatesModal = ref(false)

const showNotification = (message, type = 'success') => {
  notification.value = { message, type }
  setTimeout(() => { notification.value = null }, 3000)
}

const fetchLead = async () => {
  try {
    const res = await api.get(`/leads/${route.params.id}`)
    lead.value = res.data
    isLoading.value = false
  } catch (error) {
    console.error(error)
    showNotification('Failed to fetch lead details', 'error')
    isLoading.value = false
  }
}

const updateStatus = async () => {
  try {
    const response = await api.put(`/leads/${lead.value.id}`, { status: lead.value.status })
    showNotification('Status updated successfully!')
    Object.assign(lead.value, response.data)
  } catch (error) {
    showNotification('Failed to update status', 'error')
    fetchLead() // Revert
  }
}

const convertToOpty = async () => {
  const result = await Swal.fire({
    title: 'Convert to Opty?',
    text: `Are you sure you want to convert ${lead.value.first_name} ${lead.value.last_name || ''} to an Opty?`,
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#3b82f6',
    cancelButtonColor: '#9ca3af',
    confirmButtonText: 'Yes, Convert'
  })

  if (result.isConfirmed) {
    try {
      await api.put(`/leads/${lead.value.id}`, { ...lead.value, status: 'Converted' })
      showNotification('Lead converted to Opty successfully!')
      fetchLead()
    } catch (error) {
      console.error(error)
      Swal.fire('Error', error.response?.data?.message || 'Failed to convert lead', 'error')
    }
  }
}

onMounted(() => {
  fetchLead()
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
