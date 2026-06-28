<script setup>
import { ref, onMounted } from 'vue'
import api from '../api/axios'
import Swal from 'sweetalert2'

const profiles = ref([])
const isLoading = ref(true)

const fetchProfiles = async () => {
  try {
    const response = await api.get('/role-profiles')
    profiles.value = response.data
    isLoading.value = false
  } catch (error) {
    console.error('Failed to fetch role profiles', error)
  }
}

const deleteProfile = async (id, name) => {
  const result = await Swal.fire({
    title: 'Delete Role Profile?',
    text: `Are you sure you want to delete profile ${name}?`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#ef4444',
    confirmButtonText: 'Delete'
  })

  if (result.isConfirmed) {
    try {
      await api.delete(`/role-profiles/${id}`)
      fetchProfiles()
      Swal.fire('Deleted!', 'The profile has been deleted.', 'success')
    } catch (error) {
      console.error(error)
      Swal.fire('Error', 'Failed to delete profile', 'error')
    }
  }
}

onMounted(() => {
  fetchProfiles()
})
</script>

<template>
  <div class="animate-fade-in pb-12">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
      <div>
        <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">Role Based Profiles</h1>
        <p class="text-gray-500 mt-2 text-lg">Manage permission profiles for the application.</p>
      </div>
      <div>
        <router-link to="/role-profiles/add" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl shadow-lg shadow-blue-200 transition-all font-semibold text-sm flex items-center gap-2 transform hover:-translate-y-0.5">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
          Create Profile
        </router-link>
      </div>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden relative">
      <div v-if="isLoading" class="flex flex-col items-center justify-center h-64 text-gray-500 font-bold">
        <div class="loader-spinner mb-4"></div>
        Loading profiles...
      </div>
      <div class="overflow-x-auto p-4" v-else>
        <table class="min-w-full">
          <thead>
            <tr>
              <th class="py-4 px-6 text-left text-xs font-extrabold text-gray-400 tracking-widest uppercase">Profile Name</th>
              <th class="py-4 px-6 text-left text-xs font-extrabold text-gray-400 tracking-widest uppercase">Total Menus Allowed</th>
              <th class="py-4 px-6 text-left text-xs font-extrabold text-gray-400 tracking-widest uppercase">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-for="profile in profiles" :key="profile.id" class="group hover:bg-gray-50/50 transition-colors">
              <td class="py-4 px-6 font-bold text-gray-900">{{ profile.name }}</td>
              <td class="py-4 px-6 text-gray-600">{{ profile.menus ? profile.menus.length : 0 }} menus</td>
              <td class="py-4 px-6 flex gap-3">
                <router-link :to="`/role-profiles/edit/${profile.id}`" class="text-blue-600 hover:text-blue-800 font-semibold text-sm flex items-center gap-1">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                  Edit
                </router-link>
                <button @click="deleteProfile(profile.id, profile.name)" class="text-red-500 hover:text-red-700 font-semibold text-sm flex items-center gap-1">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                  Delete
                </button>
              </td>
            </tr>
            <tr v-if="profiles.length === 0">
              <td colspan="3" class="text-center py-8 text-gray-500 font-semibold">No role profiles found</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.5s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.loader-spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #f3f3f3;
  border-top: 4px solid #3b82f6;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}
@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>
