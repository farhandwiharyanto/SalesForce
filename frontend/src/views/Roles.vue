<template>
  <div class="h-full flex flex-col max-w-full overflow-hidden bg-white relative">
    <div class="p-6 pb-4 shrink-0 border-b border-gray-100 flex justify-between items-center bg-white z-10">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Roles</h1>
        <p class="text-sm text-gray-500 mt-1">Manage organizational hierarchy and roles</p>
      </div>
    </div>

    <div class="flex-1 overflow-auto bg-white p-8">
      <RoleNode :node="rolesTree" />
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import RoleNode from '../components/RoleNode.vue';

const rolesTree = ref({
  name: 'Organization',
  isRoot: true,
  children: [
    {
      name: 'Admin',
      children: [
        {
          name: 'Pimpinan Sales',
          children: [
            {
              name: 'Sales'
            }
          ]
        }
      ]
    }
  ]
});
</script>
