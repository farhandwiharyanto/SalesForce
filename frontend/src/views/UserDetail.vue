<template>
  <div class="h-full flex flex-col max-w-full overflow-hidden bg-gray-50 relative animate-fade-in pb-12">
    <!-- Header Section -->
    <div class="p-6 pb-4 shrink-0 border-b border-gray-100 flex justify-between items-center bg-white z-10">
      <div class="flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-100 to-blue-100 text-blue-700 flex items-center justify-center font-bold text-xl shadow-sm border border-blue-50">
          {{ (user?.first_name || '?').charAt(0).toUpperCase() }}
        </div>
        <div>
          <h1 class="text-2xl font-bold text-gray-900 tracking-tight uppercase">{{ user?.first_name }} {{ user?.last_name }}</h1>
        </div>
      </div>
      <div>
        <button class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl shadow-lg shadow-blue-200 transition-all font-semibold text-sm transform hover:-translate-y-0.5 mr-2">
          Edit
        </button>
      </div>
    </div>

    <!-- Content Section -->
    <div class="flex-1 overflow-auto p-8">
      
      <div v-if="isLoading" class="flex justify-center items-center h-64 text-gray-500 font-bold">
        <div class="loader-spinner mr-3"></div> Loading...
      </div>

      <div v-else class="max-w-6xl mx-auto space-y-8">
        
        <!-- Section: User Login & Role -->
        <div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden">
          <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-800">User Login & Role</h3>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 divide-y divide-gray-100 lg:divide-y-0 lg:divide-x">
            <!-- Col 1 -->
            <div class="flex flex-col">
              <div class="flex border-b border-gray-100">
                <div class="w-1/3 bg-gray-50/50 p-4 text-xs font-semibold text-gray-500 uppercase">User Name</div>
                <div class="w-2/3 p-4 text-sm font-medium text-gray-900">{{ user?.username }}</div>
              </div>
              <div class="flex">
                <div class="w-1/3 bg-gray-50/50 p-4 text-xs font-semibold text-gray-500 uppercase">Last Name</div>
                <div class="w-2/3 p-4 text-sm font-medium text-gray-900">{{ user?.last_name }}</div>
              </div>
            </div>
            
            <!-- Col 2 -->
            <div class="flex flex-col">
              <div class="flex border-b border-gray-100">
                <div class="w-1/3 bg-gray-50/50 p-4 text-xs font-semibold text-gray-500 uppercase">Primary Email</div>
                <div class="w-2/3 p-4 text-sm font-medium text-blue-600 hover:underline cursor-pointer">{{ user?.email }}</div>
              </div>
              <div class="flex">
                <div class="w-1/3 bg-gray-50/50 p-4 text-xs font-semibold text-gray-500 uppercase">Admin</div>
                <div class="w-2/3 p-4 text-sm font-medium text-gray-900">{{ user?.role === 'admin' ? 'Yes' : 'No' }}</div>
              </div>
            </div>

            <!-- Col 3 -->
            <div class="flex flex-col">
              <div class="flex border-b border-gray-100">
                <div class="w-1/3 bg-gray-50/50 p-4 text-xs font-semibold text-gray-500 uppercase">First Name</div>
                <div class="w-2/3 p-4 text-sm font-medium text-gray-900">{{ user?.first_name }}</div>
              </div>
              <div class="flex">
                <div class="w-1/3 bg-gray-50/50 p-4 text-xs font-semibold text-gray-500 uppercase">Role</div>
                <div class="w-2/3 p-4 text-sm font-medium text-blue-600 hover:underline cursor-pointer uppercase">
                  <router-link :to="`/roles/edit/${user?.role}`">{{ user?.role === 'pimpinan_sales' ? 'Pimpinan Sales' : user?.role }}</router-link>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '../api/axios'

const route = useRoute()
const user = ref(null)
const isLoading = ref(true)

const fetchUser = async () => {
  try {
    const response = await api.get(`/users`) // Assuming we fetch all and find, or if there is a get by ID
    // Since we don't have a specific GET /users/:id API in the previous steps (unless we do), let's fetch all and find.
    // Or normally api.get(`/users/${route.params.id}`)
    const users = response.data
    user.value = users.find(u => u.id == route.params.id)
    isLoading.value = false
  } catch (error) {
    console.error('Failed to fetch user', error)
    isLoading.value = false
  }
}

onMounted(() => {
  fetchUser()
})
</script>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.3s ease-out;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(5px); }
  to { opacity: 1; transform: translateY(0); }
}
.loader-spinner {
  width: 24px;
  height: 24px;
  border: 3px solid #f3f3f3;
  border-top: 3px solid #3b82f6;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}
@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>
