<template>
  <div class="animate-fade-in">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-gray-800">Customers Directory</h1>
      <button v-if="authStore.role !== 'admin'" @click="showCreateForm = true" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl shadow-md transition-all font-semibold flex items-center gap-2 transform hover:-translate-y-0.5">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        <span>Add Customer</span>
      </button>
    </div>

    <!-- Create Form -->
    <div v-if="showCreateForm" class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-6">
      <h2 class="text-xl font-bold mb-4">New Customer</h2>
      <form @submit.prevent="createCustomer" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Nomor SIA</label>
          <input v-model="form.nomor_sia" required type="text" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2 border" placeholder="e.g. 100123" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Customer</label>
          <input v-model="form.nomor_customer" required type="text" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2 border" placeholder="e.g. 99012" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Customer Name</label>
          <input v-model="form.customer_name" required type="text" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2 border" placeholder="e.g. PT Jaya Abadi" />
        </div>
        <div class="md:col-span-3 flex justify-end gap-2 mt-2">
          <button type="button" @click="showCreateForm = false" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium">Cancel</button>
          <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">Save Customer</button>
        </div>
      </form>
    </div>
    
    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
      <table class="min-w-full divide-y divide-gray-100">
        <thead class="bg-gray-50/50">
          <tr>
            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ID</th>
            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nomor SIA</th>
            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nomor Customer</th>
            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Customer Name</th>
            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Integration Ready</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100">
          <tr v-if="customers.length === 0">
            <td colspan="5" class="px-6 py-12 text-center text-gray-400 font-medium">No customers found.</td>
          </tr>
          <tr v-for="customer in customers" :key="customer.id" class="hover:bg-gray-50/50 transition-colors">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">#{{ customer.id }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">{{ customer.nomor_sia }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-700">{{ customer.nomor_customer }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ customer.customer_name }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-bold rounded-full border border-green-200">Yes</span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Integration Endpoint Info -->
    <div class="mt-8 bg-blue-50 border border-blue-100 p-6 rounded-2xl">
      <h3 class="text-lg font-bold text-blue-900 mb-2 flex items-center gap-2">
        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
        API Integrasi OrderSales
      </h3>
      <p class="text-sm text-blue-800 mb-2">Endpoint ini sudah siap digunakan oleh project OrderSales untuk menarik data customer (GET Request):</p>
      <code class="block bg-white p-3 rounded-lg border border-blue-200 text-sm font-mono text-gray-800">
        GET http://localhost:8000/api/integration/ordersales/customers
      </code>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../api/axios';
import { useAuthStore } from '../stores/auth';
import Swal from 'sweetalert2';

const authStore = useAuthStore();
const customers = ref([]);
const showCreateForm = ref(false);
const form = ref({
  nomor_sia: '',
  nomor_customer: '',
  customer_name: ''
});

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
    form.value = { nomor_sia: '', nomor_customer: '', customer_name: '' };
    showCreateForm.value = false;
    await fetchCustomers();
    Swal.fire('Berhasil', 'Customer berhasil ditambahkan!', 'success');
  } catch (error) {
    Swal.fire('Gagal', error.response?.data?.message || 'Gagal menambahkan customer', 'error');
  }
};

onMounted(() => {
  fetchCustomers();
});
</script>
