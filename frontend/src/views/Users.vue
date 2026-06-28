<script setup>
import { ref, onMounted, watch } from 'vue'
import api from '../api/axios'
import { useAuthStore } from '../stores/auth'
import Swal from 'sweetalert2'

const authStore = useAuthStore()

const users = ref([])
const isLoading = ref(true)
const showModal = ref(false)
const notification = ref(null)
const isEditing = ref(false)
const editingUserId = ref(null)

const newUser = ref({
  first_name: '',
  last_name: '',
  email: '',
  username: '',
  password: '',
  role: 'sales',
  role_profile_id: null,
  menus: []
})

const availableRoles = ref([])
const roleProfiles = ref([])

const showNotification = (message, type = 'success') => {
  notification.value = { message, type }
  setTimeout(() => {
    notification.value = null
  }, 3000)
}

const fetchUsers = async () => {
  try {
    const response = await api.get('/users')
    users.value = response.data
    isLoading.value = false
  } catch (error) {
    console.error('Failed to fetch users', error)
    showNotification('Failed to fetch users', 'error')
  }
}

const fetchProfiles = async () => {
  try {
    const response = await api.get('/role-profiles')
    roleProfiles.value = response.data
  } catch (error) {
    console.error('Failed to fetch role profiles', error)
  }
}

const openAddModal = () => {
  isEditing.value = false
  editingUserId.value = null
  newUser.value = { first_name: '', last_name: '', email: '', username: '', password: '', role: '', role_profile_id: null, menus: [] }
  if (availableRoles.value.length > 0) {
    newUser.value.role = availableRoles.value[0].slug;
  }
  showModal.value = true
}

const openEditModal = (user) => {
  isEditing.value = true
  editingUserId.value = user.id
  newUser.value = {
    first_name: user.first_name,
    last_name: user.last_name,
    email: user.email,
    username: user.username,
    password: '',
    role: user.role,
    role_profile_id: user.role_profile_id,
    menus: user.menus
  }
  showModal.value = true
}

// Watch role change to auto-assign profile menus
watch(() => newUser.value.role, (newRoleSlug) => {
  const roleNode = availableRoles.value.find(r => r.slug === newRoleSlug)
  if (roleNode && roleNode.role_profile_id) {
    newUser.value.role_profile_id = roleNode.role_profile_id
    const profile = roleProfiles.value.find(p => p.id === roleNode.role_profile_id)
    if (profile) {
      newUser.value.menus = profile.menus || []
    }
  } else {
    // If no profile linked in node, we could optionally map by name
    const profile = roleProfiles.value.find(p => p.name.toLowerCase() === (roleNode?.name.toLowerCase() || ''))
    if (profile) {
      newUser.value.role_profile_id = profile.id
      newUser.value.menus = profile.menus || []
    } else {
      newUser.value.role_profile_id = null
      newUser.value.menus = []
    }
  }
})

const saveUser = async () => {
  try {
    if (isEditing.value) {
      await api.put(`/users/${editingUserId.value}`, newUser.value)
      showNotification('User successfully updated!')
    } else {
      await api.post('/users', newUser.value)
      showNotification('User successfully created!')
    }
    
    showModal.value = false
    fetchUsers()
  } catch (error) {
    showNotification(error.response?.data?.message || 'Failed to save user', 'error')
  }
}

const deleteUser = async (id) => {
  const result = await Swal.fire({
    title: 'Delete User?',
    text: 'Are you sure you want to delete this user? This action cannot be undone.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#9ca3af',
    confirmButtonText: 'Yes, delete it!'
  })

  if (result.isConfirmed) {
    try {
      await api.delete(`/users/${id}`)
      fetchUsers()
      showNotification('User successfully deleted')
    } catch (error) {
      console.error(error)
      showNotification(error.response?.data?.message || 'Failed to delete user', 'error')
    }
  }
}

onMounted(async () => {
  await fetchProfiles()
  fetchUsers()
  
  const savedTree = localStorage.getItem('rolesTree_v2')
  if (savedTree) {
    const tree = JSON.parse(savedTree)
    const roles = []
    const traverse = (node) => {
      if (!node.isRoot) {
        roles.push({
          name: node.name,
          slug: node.name.toLowerCase().replace(/\s+/g, '_'),
          role_profile_id: node.role_profile_id || null
        })
      }
      if (node.children) {
        node.children.forEach(traverse)
      }
    }
    traverse(tree)
    availableRoles.value = roles
  } else {
    availableRoles.value = [
      { name: 'Admin', slug: 'admin' },
      { name: 'Pimpinan Sales', slug: 'pimpinan_sales' },
      { name: 'Director Sales', slug: 'director_sales' },
      { name: 'Verificator', slug: 'verificator' },
      { name: 'Sales', slug: 'sales' }
    ]
  }
})
</script>

<template>
  <div class="animate-fade-in pb-12">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
      <div>
        <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">Users Management</h1>
        <p class="text-gray-500 mt-2 text-lg">Manage system access, roles, and permissions.</p>
      </div>
      <div>
        <button @click="openAddModal" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl shadow-lg shadow-blue-200 transition-all font-semibold text-sm flex items-center gap-2 transform hover:-translate-y-0.5">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
          Add New User
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

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden relative">
      <div v-if="isLoading" class="flex flex-col items-center justify-center h-64 text-gray-500 font-bold">
        <div class="loader-spinner mb-4"></div>
        Loading users...
      </div>

      <div class="overflow-x-auto p-4" v-else>
        <table class="min-w-full">
          <thead>
              <th class="py-4 px-6 text-left text-xs font-extrabold text-gray-400 tracking-widest cursor-pointer hover:text-gray-600 flex items-center gap-1">⬍ Name</th>
              <th class="py-4 px-6 text-left text-xs font-extrabold text-gray-400 tracking-widest cursor-pointer hover:text-gray-600">⬍ Email</th>
              <th class="py-4 px-6 text-left text-xs font-extrabold text-gray-400 tracking-widest cursor-pointer hover:text-gray-600">⬍ Role</th>
              <th class="py-4 px-6 text-left text-xs font-extrabold text-gray-400 tracking-widest cursor-pointer hover:text-gray-600">⬍ User Name</th>
              <th class="py-4 px-6 text-left text-xs font-extrabold text-gray-400 tracking-widest cursor-pointer hover:text-gray-600">⬍ Action</th>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-for="user in users" :key="user.id" class="group hover:bg-gray-50/50 transition-colors">
              <td class="py-2 px-6">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-full bg-gray-800 text-white flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                  </div>
                  <div class="flex flex-col">
                    <router-link :to="`/users/${user.id}`" class="font-bold text-blue-600 text-[13px] uppercase tracking-wide cursor-pointer hover:underline">{{ user.first_name }} {{ user.last_name }}</router-link>
                  </div>
                </div>
              </td>
              <td class="py-2 px-6">
                <span class="text-gray-600 text-[13px]">{{ user.email || '-' }}</span>
              </td>
              <td class="py-2 px-6">
                <router-link :to="`/roles/edit/${user.role}`" class="text-blue-600 text-[13px] font-semibold uppercase hover:underline">{{ user.role === 'pimpinan_sales' ? 'Pimpinan Sales' : user.role }}</router-link>
              </td>
              <td class="py-2 px-6">
                <span class="text-gray-700 text-[13px] uppercase">{{ user.username }}</span>
              </td>
              <td class="py-2 px-6 flex items-center gap-3">
                <button @click="openEditModal(user)" class="text-blue-600 hover:text-blue-800 text-[13px] font-semibold flex items-center gap-1">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                  Edit
                </button>
                <button @click="deleteUser(user.id)" class="text-red-500 hover:text-red-700 text-[13px] font-semibold flex items-center gap-1">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Add/Edit User Modal -->
    <transition name="fade">
      <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/40 backdrop-blur-sm p-4">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-xl flex flex-col overflow-hidden transform transition-all animate-modal-in border border-white/40 relative">
          
          <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-8 py-6 flex justify-between items-center relative overflow-hidden shrink-0">
            <div class="absolute -right-8 -top-8 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
            <h3 class="text-2xl font-black text-white relative z-10 tracking-tight">{{ isEditing ? 'Edit User' : 'Create New User' }}</h3>
            <button type="button" @click="showModal = false" class="text-white/70 hover:text-white bg-white/10 hover:bg-white/20 rounded-full w-10 h-10 flex items-center justify-center transition-all relative z-10">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
          </div>

          <form @submit.prevent="saveUser" class="flex flex-col flex-1 overflow-hidden">
            <div class="p-8 space-y-5">
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">First Name</label>
                  <input type="text" v-model="newUser.first_name" required placeholder="John" class="w-full bg-gray-50 border border-gray-200 text-gray-900 rounded-xl px-4 py-3 font-medium focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none" />
                </div>
                <div>
                  <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Last Name</label>
                  <input type="text" v-model="newUser.last_name" placeholder="Doe" class="w-full bg-gray-50 border border-gray-200 text-gray-900 rounded-xl px-4 py-3 font-medium focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none" />
                </div>
              </div>

              <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Email Address</label>
                <input type="email" v-model="newUser.email" required placeholder="john.doe@example.com" class="w-full bg-gray-50 border border-gray-200 text-gray-900 rounded-xl px-4 py-3 font-medium focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none" />
              </div>

              <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Username (Login)</label>
                <input type="text" v-model="newUser.username" required placeholder="johndoe" class="w-full bg-gray-50 border border-gray-200 text-gray-900 rounded-xl px-4 py-3 font-medium focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none font-mono" />
              </div>
              
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Hierarchy Role</label>
                  <select v-model="newUser.role" class="w-full bg-gray-50 border border-gray-200 text-gray-900 rounded-xl px-4 py-3 font-medium focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none appearance-none cursor-pointer">
                    <option value="" disabled v-if="!newUser.role">Select a role</option>
                    <option v-for="role in availableRoles" :key="role.slug" :value="role.slug">
                      {{ role.name }}
                    </option>
                  </select>
                </div>
                <div>
                  <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">
                    Password <span v-if="isEditing" class="text-gray-400 font-normal normal-case">(Leave blank to keep current)</span>
                  </label>
                  <input type="password" v-model="newUser.password" :required="!isEditing" placeholder="••••••••" class="w-full bg-gray-50 border border-gray-200 text-gray-900 rounded-xl px-4 py-3 font-medium focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none" />
                </div>
              </div>
            </div>

            <div class="px-8 py-5 flex justify-end gap-3 border-t border-gray-100 bg-gray-50 shrink-0">
              <button type="button" @click="showModal = false" class="px-6 py-3 rounded-xl font-bold text-gray-600 bg-white border border-gray-200 hover:bg-gray-50 transition-all">Cancel</button>
              <button type="submit" class="px-6 py-3 rounded-xl font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-lg shadow-blue-200 transform hover:-translate-y-0.5 transition-all">{{ isEditing ? 'Update User' : 'Save User' }}</button>
            </div>
          </form>
        </div>
      </div>
    </transition>
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
.animate-modal-in {
  animation: modalScale 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
@keyframes modalScale {
  from { opacity: 0; transform: scale(0.9); }
  to { opacity: 1; transform: scale(1); }
}
.slide-down-enter-active, .slide-down-leave-active {
  transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}
.slide-down-enter-from, .slide-down-leave-to {
  opacity: 0; transform: translateY(-20px);
}
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
.loader-spinner {
  width: 40px; height: 40px; border: 4px solid #f3f3f3;
  border-top: 4px solid #3b82f6; border-radius: 50%;
  animation: spin 1s linear infinite;
}
@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>
