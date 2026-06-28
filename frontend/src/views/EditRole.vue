<template>
  <div class="h-full flex flex-col max-w-full overflow-hidden bg-gray-50 relative pb-12">
    <!-- Header Section -->
    <div class="p-6 pb-4 shrink-0 border-b border-gray-100 bg-white z-10 flex items-center justify-between">
      <h1 class="text-2xl font-bold text-gray-900 tracking-tight">{{ isNewRole ? 'Create Hierarchy Node' : 'Edit Hierarchy Node' }}</h1>
    </div>

    <!-- Content Section -->
    <div class="flex-1 overflow-auto p-8">
      <div class="max-w-4xl mx-auto bg-white border border-gray-200 shadow-sm p-8">
        
        <form @submit.prevent="saveRole" class="space-y-6">
          
          <!-- Basic Role Info -->
          <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
            <div class="md:col-span-3 text-right">
              <label class="text-sm font-semibold text-gray-700">Hierarchy Role Name <span class="text-red-500">*</span></label>
            </div>
            <div class="md:col-span-8">
              <input type="text" v-model="form.name" class="w-full bg-white border border-gray-300 text-gray-900 px-3 py-2 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none uppercase" required />
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
            <div class="md:col-span-3 text-right">
              <label class="text-sm font-semibold text-gray-700">Reports To</label>
            </div>
            <div class="md:col-span-8">
              <input type="text" v-model="form.reportsTo" class="w-full bg-gray-100 border border-gray-300 text-gray-900 px-3 py-2 text-sm outline-none uppercase cursor-not-allowed" readonly required />
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
            <div class="md:col-span-3 text-right">
              <label class="text-sm font-semibold text-gray-700">Role Profile <span class="text-red-500">*</span></label>
            </div>
            <div class="md:col-span-8">
              <select v-model="form.role_profile_id" class="w-full bg-white border border-gray-300 text-gray-900 px-3 py-2 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none" required>
                <option value="" disabled>Select Role Profile</option>
                <option v-for="profile in roleProfiles" :key="profile.id" :value="profile.id">
                  {{ profile.name }}
                </option>
              </select>
              <p class="text-xs text-gray-500 mt-1">Users assigned to this hierarchy node will inherit permissions from this profile.</p>
            </div>
          </div>

          <!-- Form Actions -->
          <div class="pt-8 flex justify-center gap-4">
            <button type="submit" class="bg-[#4CAF50] hover:bg-green-600 text-white px-8 py-2 text-sm font-semibold shadow-sm transition-colors rounded">Save</button>
            <button type="button" @click="$router.push('/roles')" class="text-red-500 hover:text-red-700 px-4 py-2 text-sm font-semibold transition-colors">Cancel</button>
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
  reportsTo: route.query.reportsTo || '',
  role_profile_id: ''
})

const roleProfiles = ref([])

onMounted(async () => {
  try {
    const response = await api.get('/role-profiles')
    roleProfiles.value = response.data
  } catch (error) {
    console.error('Failed to fetch role profiles:', error)
  }

  if (form.value.name) {
    const roleNameUpper = form.value.name.toUpperCase();
    if (roleNameUpper === 'SALES') {
      form.value.reportsTo = 'Pimpinan Sales';
    } else if (roleNameUpper === 'PIMPINAN SALES') {
      form.value.reportsTo = 'Director Sales'; // Fixed reports to based on updated hierarchy
    } else if (roleNameUpper === 'DIRECTOR SALES') {
      form.value.reportsTo = 'Director Utama';
    } else if (roleNameUpper === 'DIRECTOR UTAMA') {
      form.value.reportsTo = 'Admin';
    } else if (roleNameUpper === 'VERIFICATOR') {
      form.value.reportsTo = 'Admin';
    } else if (roleNameUpper === 'ADMIN') {
      form.value.reportsTo = 'Organization';
    }

    // Load from localStorage tree to see if it has a profile_id
    const savedTree = localStorage.getItem('rolesTree_v2');
    if (savedTree) {
      const tree = JSON.parse(savedTree);
      let found = false;
      const findNode = (node, targetName) => {
        if (node.name.toLowerCase() === targetName.toLowerCase()) {
          if (node.role_profile_id) {
            form.value.role_profile_id = node.role_profile_id;
          } else {
             // Auto map to legacy profile if matches
             const profile = roleProfiles.value.find(p => p.name.toLowerCase() === node.name.toLowerCase())
             if (profile) form.value.role_profile_id = profile.id;
          }
          found = true;
          return;
        }
        if (node.children && !found) {
          for (let child of node.children) {
            findNode(child, targetName);
          }
        }
      }
      findNode(tree, form.value.name);
    }
  }
})

const saveRole = async () => {
  try {
    const savedTree = localStorage.getItem('rolesTree_v2');
    if (savedTree) {
      const tree = JSON.parse(savedTree);
      
      if (isNewRole.value) {
        const addNode = (node, parentName, newNode) => {
          if (node.name.toLowerCase() === parentName.toLowerCase()) {
            if (!node.children) node.children = [];
            if (!node.children.find(c => c.name.toLowerCase() === newNode.name.toLowerCase())) {
              node.children.push(newNode);
            }
            return true;
          }
          if (node.children) {
            for (let child of node.children) {
              if (addNode(child, parentName, newNode)) return true;
            }
          }
          return false;
        };
        if (form.value.reportsTo) {
          addNode(tree, form.value.reportsTo, { 
            name: form.value.name, 
            role_profile_id: form.value.role_profile_id,
            children: [] 
          });
        }
      } else {
        let found = false;
        const updateNode = (node, targetName) => {
          if (node.name.toLowerCase() === targetName.toLowerCase()) {
            node.role_profile_id = form.value.role_profile_id;
            // Name update is intentionally left out to keep tree integrity simple,
            // but if they change name, we would update node.name = form.value.name.
            // (Assuming form.value.name matches targetName for simplicity)
            found = true;
            return;
          }
          if (node.children && !found) {
            for (let child of node.children) {
              updateNode(child, targetName);
            }
          }
        }
        updateNode(tree, route.params.roleName);
      }
      
      localStorage.setItem('rolesTree_v2', JSON.stringify(tree));
    }

    Swal.fire({
      title: 'Success!',
      text: `Hierarchy node ${form.value.name} has been ${isNewRole.value ? 'created' : 'updated'}.`,
      icon: 'success',
      confirmButtonText: 'OK',
      confirmButtonColor: '#4CAF50'
    }).then(() => {
      router.push('/roles')
    })
  } catch (error) {
    console.error(error)
    Swal.fire({
      title: 'Error',
      text: 'Failed to update hierarchy node.',
      icon: 'error'
    })
  }
}
</script>
