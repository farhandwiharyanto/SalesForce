<template>
  <div class="animate-fade-in">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-gray-800">Customers Directory</h1>
      <div class="flex items-center gap-3">
        <button v-if="authStore.hasAction('Customers', 'create')" @click="showImportModal = true" class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 px-4 py-2.5 rounded-xl font-bold shadow-sm transition-all flex items-center gap-2">
          <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
          Import
        </button>
        <button v-if="authStore.hasAction('Customers', 'create')" @click="showCreateForm = true" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl shadow-md transition-all font-semibold flex items-center gap-2 transform hover:-translate-y-0.5">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
          <span>Add Customer</span>
        </button>
      </div>
    </div>

    <!-- Create/Edit Form Modal -->
    <div v-if="showCreateForm || showEditForm" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl overflow-hidden animate-slide-up">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
          <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            {{ showEditForm ? 'Edit Customer' : 'New Customer' }}
          </h3>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-700 transition-colors p-1 rounded-lg hover:bg-gray-100">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
          </button>
        </div>
        <div class="p-6">
          <form @submit.prevent="showEditForm ? updateCustomer() : createCustomer()" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Customer Name</label>
              <input v-model="form.customer_name" required type="text" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2 border" placeholder="e.g. NASOYA INDONESIA GROUP" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Initial</label>
              <input v-model="form.initial" required type="text" maxlength="4" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2 border uppercase" placeholder="e.g. AIIR" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
              <input v-model="form.email" required type="email" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2 border" placeholder="e.g. ADMIN@NASOYAGROUP.COM" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
              <select v-model="form.status" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2 border bg-white">
                <option value="Registered">Registered</option>
                <option value="Active">Active</option>
                <option value="Deactivated">Deactivated</option>
              </select>
            </div>
            <div v-if="authStore.user?.role === 'admin' || authStore.user?.role === 'administrator'">
              <label class="block text-sm font-medium text-gray-700 mb-1">Account Owner (Sales)</label>
              <select v-model="form.owner_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2 border bg-white">
                <option :value="null">-- Select Owner --</option>
                <option v-for="user in users" :key="user.id" :value="user.id">
                  {{ user.first_name }} {{ user.last_name }} ({{ user.role }})
                </option>
              </select>
            </div>
            <div class="md:col-span-2 flex justify-end gap-3 mt-4 pt-4 border-t border-gray-100">
              <button type="button" @click="closeModal" class="px-5 py-2 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 font-semibold transition-colors">Cancel</button>
              <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 font-semibold transition-colors shadow-md shadow-blue-200">
                {{ showEditForm ? 'Update Customer' : 'Save Customer' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
    
    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
      <table class="min-w-full divide-y divide-gray-100">
        <thead class="bg-gray-50/50">
          <tr>
            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ID</th>
            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Initial</th>
            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Customer Name</th>
            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Customer No</th>
            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Email</th>
            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Account Owner</th>
            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Action</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100">
          <tr v-if="customers.length === 0">
            <td colspan="7" class="px-6 py-12 text-center text-gray-400 font-medium">No customers found.</td>
          </tr>
          <tr v-for="customer in customers" :key="customer.id" class="hover:bg-gray-50/50 transition-colors">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">#{{ customer.id }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">{{ customer.initial }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ customer.customer_name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-700">{{ customer.nomor_customer }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ customer.email }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-blue-600">
              {{ customer.owner ? (customer.owner.first_name + ' ' + (customer.owner.last_name || '')).trim() : '-' }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span class="px-3 py-1 text-xs font-bold rounded-full border shadow-sm"
                :class="{
                  'bg-blue-50 text-blue-700 border-blue-200': customer.status === 'Registered',
                  'bg-green-50 text-green-700 border-green-200': customer.status === 'Active',
                  'bg-red-50 text-red-700 border-red-200': customer.status === 'Deactivated'
                }">
                {{ customer.status || 'Registered' }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <button v-if="authStore.hasAction('Customers', 'edit') && (authStore.user?.role === 'admin' || authStore.user?.role === 'administrator' || customer.owner_id === authStore.user?.id)" @click="openEditCustomer(customer)" class="text-blue-600 hover:text-blue-800 font-bold text-sm bg-blue-50 px-3 py-1.5 rounded-lg border border-blue-100 hover:bg-blue-100 transition-colors">
                Edit
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Notification Toast -->
    <div v-if="notification" class="fixed bottom-6 right-6 z-50 px-6 py-4 rounded-xl shadow-2xl flex items-center gap-3 font-bold text-white animate-slide-up" :class="notification.type === 'error' ? 'bg-red-600' : 'bg-green-600'">
      <svg v-if="notification.type === 'success'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
      <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
      {{ notification.message }}
    </div>

    <ImportModal 
      :show="showImportModal"
      moduleName="Customers"
      :columns="['Customer Name', 'Email', 'Initial', 'Customer ID', 'SIA Number', 'Status']"
      :requiredColumns="['Customer Name', 'Email']"
      :sampleRow="['Acme Corporation', 'contact@acme.com', 'ACME', '551', 'SIA-551', 'Active']"
      apiEndpoint="/customers/bulk"
      @close="showImportModal = false"
      @import-success="onImportSuccess"
      @import-error="onImportError"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../api/axios';
import { useAuthStore } from '../stores/auth';
import ImportModal from '../components/ImportModal.vue';
import Swal from 'sweetalert2';

const authStore = useAuthStore();
const customers = ref([]);
const users = ref([]);
const showCreateForm = ref(false);
const showEditForm = ref(false);
const notification = ref(null);
const showImportModal = ref(false);

const showNotification = (msg, type = 'success') => {
  notification.value = { message: msg, type };
  setTimeout(() => { notification.value = null }, 3000);
}

const onImportSuccess = (msg) => {
  showNotification(msg, 'success');
  fetchCustomers();
};

const onImportError = (msg) => {
  showNotification(msg, 'error');
};

const editingId = ref(null);
const form = ref({
  customer_name: '',
  email: '',
  initial: '',
  status: 'Registered',
  owner_id: null
});

const resetForm = () => {
  form.value = { customer_name: '', email: '', initial: '', status: 'Registered', owner_id: null };
  editingId.value = null;
}

const closeModal = () => {
  showCreateForm.value = false;
  showEditForm.value = false;
  resetForm();
}

const fetchCustomers = async () => {
  try {
    const response = await api.get('/customers');
    customers.value = response.data;
  } catch (error) {
    console.error('Error fetching customers:', error);
  }
};

const createCustomer = async () => {
  try {
    await api.post('/customers', form.value);
    closeModal();
    await fetchCustomers();
    Swal.fire({
      icon: 'success',
      title: 'Success!',
      text: 'Customer created successfully.',
      timer: 1500,
      showConfirmButton: false
    });
  } catch (error) {
    Swal.fire('Error', error.response?.data?.message || 'Failed to create customer', 'error');
  }
};

const openEditCustomer = (customer) => {
  editingId.value = customer.id;
  form.value = {
    customer_name: customer.customer_name,
    email: customer.email,
    initial: customer.initial,
    status: customer.status || 'Registered',
    owner_id: customer.owner_id
  };
  showEditForm.value = true;
};

const updateCustomer = async () => {
  try {
    await api.put(`/customers/${editingId.value}`, form.value);
    closeModal();
    await fetchCustomers();
    Swal.fire({
      icon: 'success',
      title: 'Success!',
      text: 'Customer updated successfully.',
      timer: 1500,
      showConfirmButton: false
    });
  } catch (error) {
    Swal.fire('Error', error.response?.data?.message || 'Failed to update customer', 'error');
  }
};

const fetchUsers = async () => {
  if (authStore.user?.role === 'admin' || authStore.user?.role === 'administrator') {
    try {
      const res = await api.get('/users');
      users.value = res.data;
    } catch (err) {
      console.error(err);
    }
  }
};

onMounted(() => {
  fetchCustomers();
  fetchUsers();
});
</script>

<style scoped>
.animate-fade-in { animation: fadeIn 0.4s ease-out; }
.animate-slide-up { animation: slideUp 0.3s ease-out; }

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes slideUp {
  from { opacity: 0; transform: translateY(20px) scale(0.95); }
  to { opacity: 1; transform: translateY(0) scale(1); }
}
</style>
