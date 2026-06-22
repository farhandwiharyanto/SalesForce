<template>
  <div class="pb-12 animate-fade-in">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Products & Price Books</h1>
        <p class="text-gray-500 mt-1">Manage catalog and base pricing.</p>
      </div>
      <button v-if="['admin', 'administrator'].includes(authStore.user?.role)" @click="openCreateModal" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl shadow-md transition-all font-semibold flex items-center gap-2 transform hover:-translate-y-0.5">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        New Product
      </button>
    </div>

    <!-- Notification -->
    <transition name="slide-down">
      <div v-if="notification" class="fixed top-20 right-8 z-50 px-6 py-4 rounded-xl shadow-2xl flex items-center gap-3 font-semibold text-white" :class="notification.type === 'error' ? 'bg-red-600' : 'bg-green-600'">
        <svg v-if="notification.type === 'success'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        {{ notification.message }}
      </div>
    </transition>

    <div v-if="isLoading" class="flex flex-col items-center justify-center h-64 text-gray-500 font-semibold bg-white rounded-xl shadow-sm border border-gray-100">
      <div class="loader-spinner mb-3"></div>
      Loading products...
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="product in products" :key="product.id" class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md hover:border-blue-100 transition-all group flex flex-col h-full">
        <div class="flex justify-between items-start mb-4">
          <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shadow-inner">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
          </div>
          <span class="text-xs font-mono font-bold text-gray-400 bg-gray-50 px-2 py-1 rounded-md border border-gray-100">{{ product.product_number || 'PRO-' }}</span>
        </div>
        
        <h3 class="text-lg font-bold text-gray-900 mb-1 group-hover:text-blue-700 transition-colors line-clamp-1">{{ product.name }}</h3>
        <p class="text-sm text-gray-500 mb-6 line-clamp-2 flex-grow">{{ product.description || 'No description available.' }}</p>
        
        <div class="border-t border-gray-100 pt-4 mt-auto">
          <div class="flex justify-between items-end">
            <div>
              <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Base Price</p>
              <p class="text-xl font-black text-gray-900 tracking-tight">{{ formatIDR(product.price) }}</p>
            </div>
            <button v-if="['admin', 'administrator'].includes(authStore.user?.role)" @click="openEditModal(product)" class="bg-blue-50 hover:bg-blue-100 text-blue-600 px-3 py-1.5 rounded-lg text-sm font-bold border border-blue-100 transition-colors">
              Edit
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-if="!isLoading && products.length === 0" class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center flex flex-col items-center">
      <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
      </div>
      <h3 class="text-lg font-bold text-gray-900 mb-1">No Products Found</h3>
      <p class="text-sm text-gray-500">Your catalog is empty. Click "New Product" to add one.</p>
    </div>

    <!-- Modal Form -->
    <transition name="fade">
      <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/40 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden transform transition-all animate-modal-in border border-gray-100">
          <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-900">{{ editingId ? 'Edit Product' : 'Add New Product' }}</h3>
            <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
          </div>
          <form @submit.prevent="saveProduct" class="p-6 space-y-4">
            <div>
              <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">Product Name</label>
              <input type="text" v-model="form.name" required class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all shadow-sm" placeholder="e.g. Enterprise Database Sync" />
            </div>
            <div>
              <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">Base Price (IDR)</label>
              <div class="relative">
                <span class="absolute left-3 top-2.5 text-sm font-bold text-gray-400">Rp</span>
                <input type="number" v-model="form.price" required class="w-full bg-white border border-gray-200 rounded-lg pl-10 pr-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all shadow-sm font-mono" placeholder="15000000" />
              </div>
            </div>
            <div>
              <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">Description</label>
              <textarea v-model="form.description" rows="3" class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all shadow-sm"></textarea>
            </div>
            <div class="pt-4 flex justify-end gap-2 border-t border-gray-100 mt-6">
              <button type="button" @click="showModal = false" class="px-4 py-2 rounded-lg font-semibold text-gray-600 hover:bg-gray-50 transition-colors text-sm border border-transparent">Cancel</button>
              <button type="submit" :disabled="isSaving" class="px-4 py-2 rounded-lg font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition-colors text-sm flex items-center gap-2">
                <span v-if="isSaving" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                {{ editingId ? 'Update Product' : 'Save Product' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '../api/axios'
import { useAuthStore } from '../stores/auth'

const authStore = useAuthStore()
const products = ref([])
const isLoading = ref(true)
const isSaving = ref(false)
const showModal = ref(false)
const editingId = ref(null)
const notification = ref(null)

const form = ref({
  name: '',
  description: '',
  price: ''
})

const formatIDR = (value) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(value)
}

const showNotification = (message, type = 'success') => {
  notification.value = { message, type }
  setTimeout(() => { notification.value = null }, 3000)
}

const fetchProducts = async () => {
  try {
    const response = await api.get('/products')
    products.value = response.data
    isLoading.value = false
  } catch (error) {
    console.error(error)
    showNotification('Failed to fetch products', 'error')
  }
}

const openCreateModal = () => {
  editingId.value = null;
  form.value = { name: '', description: '', price: '' };
  showModal.value = true;
};

const openEditModal = (product) => {
  editingId.value = product.id;
  form.value = {
    name: product.name,
    description: product.description,
    price: product.price
  };
  showModal.value = true;
};

const saveProduct = async () => {
  try {
    isSaving.value = true
    if (editingId.value) {
      await api.put(`/products/${editingId.value}`, form.value)
      showNotification('Product updated successfully!')
    } else {
      await api.post('/products', form.value)
      showNotification('Product added successfully!')
    }
    showModal.value = false
    fetchProducts()
  } catch (error) {
    showNotification(error.response?.data?.message || 'Failed to save product', 'error')
  } finally {
    isSaving.value = false
  }
}

onMounted(() => {
  fetchProducts()
})
</script>

<style scoped>
.animate-fade-in { animation: fadeIn 0.4s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
.animate-modal-in { animation: modalScale 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
@keyframes modalScale { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
.slide-down-enter-active, .slide-down-leave-active { transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55); }
.slide-down-enter-from, .slide-down-leave-to { opacity: 0; transform: translateY(-20px); }
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
.loader-spinner { width: 30px; height: 30px; border: 3px solid #f3f3f3; border-top: 3px solid #2563eb; border-radius: 50%; animation: spin 0.8s linear infinite; }
@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
</style>
