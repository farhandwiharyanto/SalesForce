<template>
  <div class="h-full flex flex-col max-w-full overflow-hidden bg-gray-50 relative pb-12">
    <!-- Header Section -->
    <div class="p-6 pb-4 shrink-0 border-b border-gray-100 bg-white z-10 flex items-center justify-between">
      <h1 class="text-2xl font-bold text-gray-900 tracking-tight">{{ isNewProfile ? 'Create Role Profile' : 'Edit Role Profile' }}</h1>
    </div>

    <!-- Content Section -->
    <div class="flex-1 overflow-auto p-8">
      <div class="max-w-6xl mx-auto bg-white border border-gray-200 shadow-sm p-8">
        
        <form @submit.prevent="saveProfile" class="space-y-6">
          
          <!-- Basic Info -->
          <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
            <div class="md:col-span-3 text-right">
              <label class="text-sm font-semibold text-gray-700">Profile Name <span class="text-red-500">*</span></label>
            </div>
            <div class="md:col-span-6">
              <input type="text" v-model="form.name" class="w-full bg-white border border-gray-300 text-gray-900 px-3 py-1.5 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none uppercase" required />
            </div>
          </div>

          <!-- Privileges Table -->
          <div class="pt-4">
            <h3 class="text-sm font-semibold text-gray-700 mb-2">Edit privileges of this profile</h3>
            <div class="border border-gray-300">
              <table class="w-full divide-y divide-gray-200">
                <thead class="bg-[#444444]">
                  <tr>
                    <th scope="col" class="px-4 py-2 text-left text-xs font-bold text-white flex items-center gap-2 border-r border-gray-500">
                      <input type="checkbox" @change="toggleAllModules" :checked="isAllModulesChecked" class="rounded w-3.5 h-3.5" />
                      Modules
                    </th>
                    <th scope="col" class="px-4 py-2 text-center text-xs font-bold text-white border-r border-gray-500">
                      <div class="flex items-center justify-center gap-2">
                        <input type="checkbox" @change="toggleAllAction('view', $event)" :checked="isAllActionChecked('view')" class="rounded w-3.5 h-3.5" /> View
                      </div>
                    </th>
                    <th scope="col" class="px-4 py-2 text-center text-xs font-bold text-white border-r border-gray-500">
                      <div class="flex items-center justify-center gap-2">
                        <input type="checkbox" @change="toggleAllAction('create', $event)" :checked="isAllActionChecked('create')" class="rounded w-3.5 h-3.5" /> Create
                      </div>
                    </th>
                    <th scope="col" class="px-4 py-2 text-center text-xs font-bold text-white border-r border-gray-500">
                      <div class="flex items-center justify-center gap-2">
                        <input type="checkbox" @change="toggleAllAction('edit', $event)" :checked="isAllActionChecked('edit')" class="rounded w-3.5 h-3.5" /> Edit
                      </div>
                    </th>
                    <th scope="col" class="px-4 py-2 text-center text-xs font-bold text-white">
                      <div class="flex items-center justify-center gap-2">
                        <input type="checkbox" @change="toggleAllAction('delete', $event)" :checked="isAllActionChecked('delete')" class="rounded w-3.5 h-3.5" /> Delete
                      </div>
                    </th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                  <tr v-for="module in modulesList" :key="module.name" class="hover:bg-gray-50">
                    <td class="px-4 py-2 text-sm font-semibold text-gray-700 flex items-center gap-2 border-r border-gray-200">
                      <input type="checkbox" v-model="module.enabled" @change="toggleModule(module)" class="rounded w-3.5 h-3.5 text-blue-600 focus:ring-blue-500" />
                      {{ module.name }}
                    </td>
                    <td class="px-4 py-2 text-center border-r border-gray-200">
                      <input type="checkbox" v-model="module.view" :disabled="!module.enabled" class="rounded w-3.5 h-3.5 text-blue-600 focus:ring-blue-500 disabled:opacity-50" />
                    </td>
                    <td class="px-4 py-2 text-center border-r border-gray-200">
                      <input type="checkbox" v-model="module.create" :disabled="!module.enabled" class="rounded w-3.5 h-3.5 text-blue-600 focus:ring-blue-500 disabled:opacity-50" />
                    </td>
                    <td class="px-4 py-2 text-center border-r border-gray-200">
                      <input type="checkbox" v-model="module.edit" :disabled="!module.enabled" class="rounded w-3.5 h-3.5 text-blue-600 focus:ring-blue-500 disabled:opacity-50" />
                    </td>
                    <td class="px-4 py-2 text-center">
                      <input type="checkbox" v-model="module.delete" :disabled="!module.enabled" class="rounded w-3.5 h-3.5 text-blue-600 focus:ring-blue-500 disabled:opacity-50" />
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Actions -->
          <div class="pt-8 flex justify-center gap-4">
            <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-8 py-2 text-sm font-bold rounded shadow-sm">Save</button>
            <router-link to="/role-profiles" class="text-red-500 hover:text-red-700 px-4 py-2 text-sm font-bold">Cancel</router-link>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '../api/axios'
import Swal from 'sweetalert2'

const route = useRoute()
const router = useRouter()

const isNewProfile = computed(() => !route.params.id)

const form = ref({
  name: ''
})

const modulesList = ref([
  { name: 'Dashboard', enabled: false, view: false, create: false, edit: false, delete: false },
  { name: 'Opty', enabled: false, view: false, create: false, edit: false, delete: false },
  { name: 'Customers', enabled: false, view: false, create: false, edit: false, delete: false },
  { name: 'Leads', enabled: false, view: false, create: false, edit: false, delete: false },
  { name: 'Products', enabled: false, view: false, create: false, edit: false, delete: false },
  { name: 'Service Instance Account', enabled: false, view: false, create: false, edit: false, delete: false },
  { name: 'Contract', enabled: false, view: false, create: false, edit: false, delete: false },
  { name: 'OrderSales Logs', enabled: false, view: false, create: false, edit: false, delete: false },
  { name: 'User Management', enabled: false, view: false, create: false, edit: false, delete: false },
  { name: 'Semua API', enabled: false, view: false, create: false, edit: false, delete: false }
])

const isAllModulesChecked = computed(() => modulesList.value.every(m => m.enabled))

const isAllActionChecked = (action) => {
  const enabledModules = modulesList.value.filter(m => m.enabled)
  return enabledModules.length > 0 && enabledModules.every(m => m[action])
}

const toggleAllModules = (e) => {
  const checked = e.target.checked
  modulesList.value.forEach(m => {
    m.enabled = checked
    m.view = checked
    m.create = checked
    m.edit = checked
    m.delete = checked
  })
}

const toggleAllAction = (action, e) => {
  const checked = e.target.checked
  modulesList.value.forEach(m => {
    if (m.enabled) m[action] = checked
  })
}

const toggleModule = (module) => {
  if (module.enabled) {
    module.view = true
  } else {
    module.view = false
    module.create = false
    module.edit = false
    module.delete = false
  }
}

onMounted(async () => {
  if (!isNewProfile.value) {
    try {
      const res = await api.get(`/role-profiles/${route.params.id}`);
      form.value.name = res.data.name;
      const menus = res.data.menus || [];
      
      if (menus.length > 0 && typeof menus[0] === 'object') {
        modulesList.value.forEach(m => {
          const savedMenu = menus.find(sm => sm.name === m.name);
          if (savedMenu) {
            m.enabled = true;
            m.view = !!savedMenu.view;
            m.create = !!savedMenu.create;
            m.edit = !!savedMenu.edit;
            m.delete = !!savedMenu.delete;
          }
        });
      }
    } catch (e) {
      console.error(e)
    }
  }
})

const saveProfile = async () => {
  try {
    const selectedMenus = modulesList.value
      .filter(m => m.enabled)
      .map(m => ({
        name: m.name,
        view: m.view,
        create: m.create,
        edit: m.edit,
        delete: m.delete
      }));

    if (isNewProfile.value) {
      await api.post('/role-profiles', {
        name: form.value.name,
        menus: selectedMenus
      });
    } else {
      await api.put(`/role-profiles/${route.params.id}`, {
        name: form.value.name,
        menus: selectedMenus
      });
    }

    Swal.fire({
      title: 'Success!',
      text: `Role profile ${form.value.name} has been ${isNewProfile.value ? 'created' : 'updated'}.`,
      icon: 'success',
      confirmButtonText: 'OK',
      confirmButtonColor: '#4CAF50'
    }).then(() => {
      router.push('/role-profiles')
    })
  } catch (error) {
    Swal.fire({
      title: 'Error!',
      text: error.response?.data?.message || 'Failed to save role profile.',
      icon: 'error',
      confirmButtonColor: '#f44336'
    })
  }
}
</script>
