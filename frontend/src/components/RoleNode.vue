<template>
  <div class="relative">
    <div class="flex items-center group gap-2">
      <div 
        @click="editRole"
        class="border border-gray-300 rounded px-3 py-1.5 text-xs font-semibold shadow-sm whitespace-nowrap cursor-pointer transition-colors"
        :class="[
          node.isRoot ? 'bg-orange-500 text-white border-orange-600' : 'bg-[#f8f9fc] text-[#555a64] hover:bg-gray-100'
        ]"
      >
        {{ node.name }}
      </div>
      <div v-if="!node.isRoot" class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
        <button @click.prevent="addRole" class="text-gray-500 hover:text-green-600 p-1" title="Add Role">
          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"></path></svg>
        </button>
        <button @click.prevent="deleteRole" class="text-gray-500 hover:text-red-600 p-1" title="Delete Role">
          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
        </button>
      </div>
    </div>
    <div v-if="node.children && node.children.length" class="ml-6 border-l border-gray-300 pl-4 py-2 flex flex-col gap-3 relative before:content-[''] before:absolute before:left-[-1px] before:top-0 before:bottom-0 before:w-0.5 before:bg-gray-300">
      <!-- Horizontal lines connecting to children -->
      <div v-for="(child, index) in node.children" :key="index" class="relative">
        <div class="absolute -left-4 top-1/2 w-4 border-t border-gray-300"></div>
        <RoleNode :node="child" />
      </div>
    </div>
  </div>
</template>

<script setup>
import Swal from 'sweetalert2'
import { useRouter } from 'vue-router'

const props = defineProps({
  node: {
    type: Object,
    required: true
  }
});

const router = useRouter();

const editRole = () => {
  if (props.node.isRoot) return; // Optional: maybe don't edit Organization root
  router.push({
    name: 'EditRole',
    params: { roleName: props.node.name }
  })
}

const addRole = () => {
  router.push({
    name: 'AddRole',
    query: { reportsTo: props.node.name }
  })
}

const deleteRole = () => {
  Swal.fire({
    title: 'Delete Role',
    text: `Apakah benar anda ingin menghapus role ${props.node.name} ini?`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#ef4444',
    confirmButtonText: 'Delete'
  }).then(async (result) => {
    if (result.isConfirmed) {
      const savedTree = localStorage.getItem('rolesTree_v2');
      if (savedTree) {
        const tree = JSON.parse(savedTree);
        const removeNode = (node, targetName) => {
          if (node.children) {
            const idx = node.children.findIndex(c => c.name === targetName);
            if (idx !== -1) {
              node.children.splice(idx, 1);
              return true;
            }
            for (let child of node.children) {
              if (removeNode(child, targetName)) return true;
            }
          }
          return false;
        };
        removeNode(tree, props.node.name);
        localStorage.setItem('rolesTree_v2', JSON.stringify(tree));
        
        // Call backend API to nullify role for users
        try {
          // The API endpoint handles this
          await fetch(`http://localhost:8000/api/roles/${props.node.name}/remove-from-users`, {
            method: 'PUT',
            headers: {
              'Content-Type': 'application/json',
              'Authorization': `Bearer ${localStorage.getItem('auth_token') || ''}`
            }
          });
        } catch(e) {
            console.error(e)
        }

        window.location.reload();
      }
    }
  })
}
</script>
