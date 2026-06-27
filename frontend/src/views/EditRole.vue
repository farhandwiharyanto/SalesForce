<template>
  <div class="h-full flex flex-col max-w-full overflow-hidden bg-gray-50 relative pb-12">
    <!-- Header Section -->
    <div class="p-6 pb-4 shrink-0 border-b border-gray-100 bg-white z-10 flex items-center justify-between">
      <h1 class="text-2xl font-bold text-gray-900 tracking-tight">{{ isNewRole ? 'Create Role' : 'Edit Role' }}</h1>
    </div>

    <!-- Content Section -->
    <div class="flex-1 overflow-auto p-8">
      <div class="max-w-6xl mx-auto bg-white border border-gray-200 shadow-sm p-8">
        
        <form @submit.prevent="saveRole" class="space-y-6">
          
          <!-- Basic Role Info -->
          <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
            <div class="md:col-span-3 text-right">
              <label class="text-sm font-semibold text-gray-700">Name Role <span class="text-red-500">*</span></label>
            </div>
            <div class="md:col-span-6">
              <input type="text" v-model="form.name" class="w-full bg-white border border-gray-300 text-gray-900 px-3 py-1.5 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none uppercase" required />
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
            <div class="md:col-span-3 text-right">
              <label class="text-sm font-semibold text-gray-700">Reports To</label>
            </div>
            <div class="md:col-span-6">
              <select v-model="form.reportsTo" class="w-full bg-gray-100 border border-gray-300 text-gray-900 px-3 py-1.5 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none uppercase cursor-not-allowed" disabled required>
                <option value="" disabled>Select superior role...</option>
                <option value="Admin">Admin</option>
                <option value="Pimpinan Sales">Pimpinan Sales</option>
              </select>
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
                    <th scope="col" class="px-4 py-2 text-center text-xs font-bold text-white border-r border-gray-500">
                      <div class="flex items-center justify-center gap-2">
                        <input type="checkbox" @change="toggleAllAction('delete', $event)" :checked="isAllActionChecked('delete')" class="rounded w-3.5 h-3.5" /> Delete
                      </div>
                    </th>

                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="mod in modulesList" :key="mod.name" class="hover:bg-gray-50">
                    <td class="px-4 py-2 whitespace-nowrap text-xs font-medium text-gray-900 flex items-center gap-2 border-r border-gray-200">
                      <input type="checkbox" v-model="mod.enabled" @change="handleModuleToggle(mod)" class="rounded text-blue-600 focus:ring-blue-500 border-gray-300 w-3.5 h-3.5" />
                      {{ mod.name }}
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-center border-r border-gray-200">
                      <input type="checkbox" v-model="mod.view" :disabled="!mod.enabled" class="rounded text-blue-600 focus:ring-blue-500 border-gray-300 w-3.5 h-3.5" />
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-center border-r border-gray-200">
                      <input type="checkbox" v-model="mod.create" :disabled="!mod.enabled" class="rounded text-blue-600 focus:ring-blue-500 border-gray-300 w-3.5 h-3.5" />
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-center border-r border-gray-200">
                      <input type="checkbox" v-model="mod.edit" :disabled="!mod.enabled" class="rounded text-blue-600 focus:ring-blue-500 border-gray-300 w-3.5 h-3.5" />
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-center border-r border-gray-200">
                      <input type="checkbox" v-model="mod.delete" :disabled="!mod.enabled" class="rounded text-blue-600 focus:ring-blue-500 border-gray-300 w-3.5 h-3.5" />
                    </td>

                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Form Actions -->
          <div class="fixed bottom-0 left-0 right-0 bg-gray-50 border-t border-gray-200 p-4 flex justify-center gap-4 z-20" style="margin-left: 16rem;"> <!-- Assuming 16rem sidebar -->
            <button type="submit" class="bg-[#4CAF50] hover:bg-green-600 text-white px-8 py-1.5 text-sm font-semibold shadow-sm transition-colors rounded-sm">Save</button>
            <button type="button" @click="$router.push('/roles')" class="text-red-500 hover:text-red-700 px-4 py-1.5 text-sm font-semibold transition-colors">Cancel</button>
          </div>
          
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import Swal from 'sweetalert2'
import api from '../api/axios'

const route = useRoute()
const router = useRouter()

const isNewRole = computed(() => route.path.includes('/add') || !route.params.roleName)

const form = ref({
  name: route.params.roleName || '',
  reportsTo: route.query.reportsTo || ''
})

onMounted(async () => {
  if (form.value.name) {
    const roleNameUpper = form.value.name.toUpperCase();
    if (roleNameUpper === 'SALES') {
      form.value.reportsTo = 'Pimpinan Sales';
    } else if (roleNameUpper === 'PIMPINAN SALES') {
      form.value.reportsTo = 'Admin';
    } else if (roleNameUpper === 'ADMIN') {
      form.value.reportsTo = 'Admin';
    }

    try {
      const res = await api.get(`/roles/${form.value.name}/privileges`);
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
      } else {
        modulesList.value.forEach(m => {
          if (menus.includes(m.name)) {
            m.enabled = true;
            m.view = true;
            m.create = true;
            m.edit = true;
            m.delete = true;
          }
        });
      }
    } catch (error) {
      console.error('Error fetching privileges:', error);
    }
  }
})

const availableModules = [
  'Dashboard', 'Opty', 'Customers', 'Leads', 'Products', 
  'Service Instance Account', 'Contract', 'OrderSales Logs', 
  'User Management', 'Semua API'
]

const modulesList = ref(availableModules.map(name => ({
  name,
  enabled: false,
  view: false,
  create: false,
  edit: false,
  delete: false
})))

const handleModuleToggle = (mod) => {
  if (!mod.enabled) {
    mod.view = false;
    mod.create = false;
    mod.edit = false;
    mod.delete = false;
  } else {
    mod.view = true;
    mod.create = true;
    mod.edit = true;
    mod.delete = true;
  }
}

const isAllModulesChecked = computed(() => {
  return modulesList.value.length > 0 && modulesList.value.every(m => m.enabled)
})

const toggleAllModules = (e) => {
  const isChecked = e.target.checked
  modulesList.value.forEach(m => {
    m.enabled = isChecked
    m.view = isChecked
    m.create = isChecked
    m.edit = isChecked
    m.delete = isChecked
  })
}

const isAllActionChecked = (action) => {
  return modulesList.value.length > 0 && modulesList.value.every(m => m[action])
}

const toggleAllAction = (action, e) => {
  const isChecked = e.target.checked
  modulesList.value.forEach(m => {
    if (action === 'view' && isChecked) {
      m.enabled = true
    }
    if (m.enabled) {
      m[action] = isChecked
    }
  })
}

const saveRole = async () => {
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
    
    if (form.value.name) {
      await api.put(`/roles/${form.value.name}/privileges`, {
        menus: selectedMenus
      });
    }

    Swal.fire({
      title: 'Success!',
      text: `Role ${form.value.name} has been ${isNewRole.value ? 'created' : 'updated'} and synced.`,
      icon: 'success',
      confirmButtonText: 'OK',
      confirmButtonColor: '#4CAF50'
    }).then(() => {
      router.push('/roles')
    })
  } catch (error) {
    Swal.fire({
      title: 'Error',
      text: 'Failed to sync role privileges',
      icon: 'error'
    })
  }
}
</script>

<style scoped>
/* Reset some default styles to match image */
table th, table td {
  padding-top: 0.5rem;
  padding-bottom: 0.5rem;
}
</style>
