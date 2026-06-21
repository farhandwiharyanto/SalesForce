<template>
  <div class="pb-12 animate-fade-in bg-[#f4f6f9] min-h-[calc(100vh-100px)] p-6">
    <!-- Header Area -->
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-semibold text-gray-800">OrderSales Integration Logs</h1>
      <button v-if="selectedLog" @click="selectedLog = null" class="text-sm font-medium text-blue-600 hover:text-blue-800 flex items-center gap-1 bg-white px-3 py-1.5 rounded border border-gray-200 shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Back to List
      </button>
    </div>

    <!-- Outer View (Table) -->
    <div v-if="!selectedLog" class="bg-white border border-gray-200 shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-600">
          <thead class="text-xs text-gray-700 bg-[#f8f9fa] border-b border-gray-200">
            <tr>
              <th scope="col" class="p-4 w-12 border-r border-gray-200">
                <input type="checkbox" class="w-4 h-4 text-blue-600 bg-white border-gray-300 rounded focus:ring-blue-500">
              </th>
              <th scope="col" class="px-4 py-3 border-r border-gray-200 font-bold whitespace-nowrap">
                <div class="flex items-center gap-1">
                  <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path></svg>
                  Log No
                </div>
              </th>
              <th scope="col" class="px-4 py-3 border-r border-gray-200 font-bold whitespace-nowrap">
                <div class="flex items-center gap-1">
                  <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path></svg>
                  API Name
                </div>
              </th>
              <th scope="col" class="px-4 py-3 border-r border-gray-200 font-bold whitespace-nowrap">
                <div class="flex items-center gap-1">
                  <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path></svg>
                  API Status
                </div>
              </th>
              <th scope="col" class="px-4 py-3 border-r border-gray-200 font-bold whitespace-nowrap">
                <div class="flex items-center gap-1">
                  <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path></svg>
                  API URL
                </div>
              </th>
              <th scope="col" class="px-4 py-3 font-bold whitespace-nowrap">
                <div class="flex items-center gap-1">
                  <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path></svg>
                  Created Time
                </div>
              </th>
            </tr>
            <!-- Filter Row -->
            <tr class="bg-[#f8f9fa]">
              <td class="p-2 border-r border-gray-200 text-center">
                <button class="bg-[#28a745] hover:bg-[#218838] text-white text-xs font-bold px-4 py-1.5 rounded transition-colors w-full">Search</button>
              </td>
              <td class="p-2 border-r border-gray-200"><input type="text" v-model="filters.logNo" class="w-full border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none focus:border-blue-500"></td>
              <td class="p-2 border-r border-gray-200"><input type="text" v-model="filters.apiName" class="w-full border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none focus:border-blue-500"></td>
              <td class="p-2 border-r border-gray-200"><input type="text" v-model="filters.apiStatus" class="w-full border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none focus:border-blue-500"></td>
              <td class="p-2 border-r border-gray-200"><input type="text" v-model="filters.apiUrl" class="w-full border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none focus:border-blue-500"></td>
              <td class="p-2"><input type="text" v-model="filters.createdTime" class="w-full border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none focus:border-blue-500"></td>
            </tr>
          </thead>
          
          <tbody v-if="isLoading">
            <tr>
              <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                <div class="inline-block w-6 h-6 border-2 border-blue-600 border-t-transparent rounded-full animate-spin mb-2"></div>
                <p>Loading logs...</p>
              </td>
            </tr>
          </tbody>

          <tbody v-else>
            <tr v-for="log in filteredLogs" :key="log.id" class="bg-white border-b border-gray-200 hover:bg-gray-50 transition-colors cursor-pointer group" @click="selectedLog = log">
              <td class="p-4 w-12 border-r border-gray-200" @click.stop>
                <input type="checkbox" class="w-4 h-4 text-blue-600 bg-white border-gray-300 rounded focus:ring-blue-500">
              </td>
              <td class="px-4 py-3 border-r border-gray-200 text-blue-600 group-hover:underline whitespace-nowrap">
                <div class="flex items-center gap-2">
                  <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path></svg>
                  LOG-{{ String(log.id).padStart(6, '0') }}
                </div>
              </td>
              <td class="px-4 py-3 border-r border-gray-200 whitespace-nowrap">{{ log.event_type }}</td>
              <td class="px-4 py-3 border-r border-gray-200 whitespace-nowrap">{{ log.status_code === 200 ? 'Success' : 'Failed' }}</td>
              <td class="px-4 py-3 border-r border-gray-200 max-w-[300px] truncate" :title="log.target_url">{{ log.target_url }}</td>
              <td class="px-4 py-3 whitespace-nowrap">{{ formatDate(log.created_at) }}</td>
            </tr>
            <tr v-if="filteredLogs.length === 0">
              <td colspan="6" class="px-6 py-8 text-center text-gray-500">No logs found matching your filters.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Inner View (Details) -->
    <div v-else class="bg-white border border-gray-200 shadow-sm">
      <div class="p-4 border-b border-gray-200 flex items-center justify-between bg-[#f8f9fa]">
        <div class="flex items-center gap-4">
          <h2 class="text-lg font-semibold text-gray-800">LOG-{{ String(selectedLog.id).padStart(6, '0') }}</h2>
          <span class="px-2 py-0.5 text-xs font-bold rounded" :class="selectedLog.status_code === 200 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
            {{ selectedLog.status_code === 200 ? 'Success' : 'Failed' }}
          </span>
        </div>
        <button v-if="selectedLog.status_code !== 200" @click="retrySync(selectedLog.id)" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-1.5 text-xs rounded shadow-sm font-bold transition-colors flex items-center gap-1">
          <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
          Retry Sync
        </button>
      </div>
      
      <div class="flex flex-col text-sm">
        <!-- Row 1 -->
        <div class="flex flex-col md:flex-row border-b border-gray-200">
          <div class="md:w-1/4 bg-[#f8f9fa] p-3 border-r border-gray-200 text-gray-600 font-medium">Log No</div>
          <div class="md:w-1/4 p-3 border-r border-gray-200">LOG-{{ String(selectedLog.id).padStart(6, '0') }}</div>
          <div class="md:w-1/4 bg-[#f8f9fa] p-3 border-r border-gray-200 text-gray-600 font-medium">Module</div>
          <div class="md:w-1/4 p-3">SalesForce Integration</div>
        </div>

        <!-- Row 2 -->
        <div class="flex flex-col md:flex-row border-b border-gray-200">
          <div class="md:w-1/4 bg-[#f8f9fa] p-3 border-r border-gray-200 text-gray-600 font-medium">API Title</div>
          <div class="md:w-1/4 p-3 border-r border-gray-200 break-all">{{ selectedLog.event_type }}</div>
          <div class="md:w-1/4 bg-[#f8f9fa] p-3 border-r border-gray-200 text-gray-600 font-medium">API Name</div>
          <div class="md:w-1/4 p-3 break-all">{{ selectedLog.event_type }}</div>
        </div>

        <!-- Row 3 -->
        <div class="flex flex-col md:flex-row border-b border-gray-200">
          <div class="md:w-1/4 bg-[#f8f9fa] p-3 border-r border-gray-200 text-gray-600 font-medium">API Status</div>
          <div class="md:w-1/4 p-3 border-r border-gray-200">{{ selectedLog.status_code === 200 ? 'Success' : 'Failed' }}</div>
          <div class="md:w-1/4 bg-[#f8f9fa] p-3 border-r border-gray-200 text-gray-600 font-medium">API URL</div>
          <div class="md:w-1/4 p-3 break-all">{{ selectedLog.target_url }}</div>
        </div>

        <!-- Row 4 -->
        <div class="flex flex-col border-b border-gray-200">
          <div class="flex md:flex-row flex-col">
            <div class="md:w-1/4 bg-[#f8f9fa] p-3 border-r border-gray-200 text-gray-600 font-medium flex items-center">Request</div>
            <div class="md:w-3/4 p-3 overflow-x-auto">
              <pre class="text-xs text-gray-800 font-mono whitespace-pre-wrap break-all bg-gray-50 p-2 rounded border border-gray-100">{{ JSON.stringify(selectedLog.payload) }}</pre>
            </div>
          </div>
        </div>

        <!-- Row 5 -->
        <div class="flex flex-col border-b border-gray-200">
          <div class="flex md:flex-row flex-col">
            <div class="md:w-1/4 bg-[#f8f9fa] p-3 border-r border-gray-200 text-gray-600 font-medium flex items-center">Response</div>
            <div class="md:w-3/4 p-3 overflow-x-auto">
              <pre class="text-xs text-gray-800 font-mono whitespace-pre-wrap break-all bg-gray-50 p-2 rounded border border-gray-100">{{ selectedLog.response || 'No response recorded' }}</pre>
            </div>
          </div>
        </div>

        <!-- Row 6 -->
        <div class="flex flex-col md:flex-row">
          <div class="md:w-1/4 bg-[#f8f9fa] p-3 border-r border-gray-200 text-gray-600 font-medium">Created Time</div>
          <div class="md:w-1/4 p-3 border-r border-gray-200">{{ formatDate(selectedLog.created_at) }}</div>
          <div class="md:w-1/4 bg-[#f8f9fa] p-3 border-r border-gray-200 text-gray-600 font-medium">Modified Time</div>
          <div class="md:w-1/4 p-3">{{ formatDate(selectedLog.updated_at) }}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import api from '../api/axios'
import Swal from 'sweetalert2'

const logs = ref([])
const isLoading = ref(true)
const selectedLog = ref(null)

const filters = ref({
  logNo: '',
  apiName: '',
  apiStatus: '',
  apiUrl: '',
  createdTime: ''
})

const formatDate = (dateString) => {
  const d = new Date(dateString);
  const year = d.getFullYear();
  const month = String(d.getMonth() + 1).padStart(2, '0');
  const day = String(d.getDate()).padStart(2, '0');
  let hours = d.getHours();
  const minutes = String(d.getMinutes()).padStart(2, '0');
  const ampm = hours >= 12 ? 'PM' : 'AM';
  hours = hours % 12;
  hours = hours ? hours : 12; // the hour '0' should be '12'
  return `${year}-${month}-${day} ${hours}:${minutes} ${ampm}`;
}

const fetchLogs = async () => {
  try {
    isLoading.value = true
    const response = await api.get('/webhook-logs')
    logs.value = response.data
  } catch (error) {
    console.error('Failed to fetch logs', error)
  } finally {
    isLoading.value = false
  }
}

const retrySync = async (id) => {
  try {
    const response = await api.post(`/webhook-logs/${id}/retry`)
    // Update local state
    const index = logs.value.findIndex(l => l.id === id)
    if (index !== -1) {
      logs.value[index] = response.data
      if (selectedLog.value && selectedLog.value.id === id) {
        selectedLog.value = response.data
      }
    }
    Swal.fire({
      icon: 'success',
      title: 'Sync Berhasil! 🎉',
      text: 'Data telah sukses dikirim ulang ke OrderSales.',
      confirmButtonColor: '#10b981'
    })
  } catch (error) {
    console.error('Failed to retry sync', error)
    Swal.fire({
      icon: 'error',
      title: 'Sync Gagal',
      text: error.response?.data?.message || 'Terjadi kesalahan saat mencoba mengirim ulang data.',
      confirmButtonColor: '#ef4444'
    })
  }
}

const filteredLogs = computed(() => {
  return logs.value.filter(log => {
    const logNo = `LOG-${String(log.id).padStart(6, '0')}`
    const apiStatus = log.status_code === 200 ? 'Success' : 'Failed'
    
    if (filters.value.logNo && !logNo.toLowerCase().includes(filters.value.logNo.toLowerCase())) return false;
    if (filters.value.apiName && !log.event_type.toLowerCase().includes(filters.value.apiName.toLowerCase())) return false;
    if (filters.value.apiStatus && !apiStatus.toLowerCase().includes(filters.value.apiStatus.toLowerCase())) return false;
    if (filters.value.apiUrl && !log.target_url.toLowerCase().includes(filters.value.apiUrl.toLowerCase())) return false;
    if (filters.value.createdTime && !formatDate(log.created_at).toLowerCase().includes(filters.value.createdTime.toLowerCase())) return false;
    
    return true;
  });
})

onMounted(() => {
  fetchLogs()
})
</script>

<style scoped>
.animate-fade-in { animation: fadeIn 0.4s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
</style>
