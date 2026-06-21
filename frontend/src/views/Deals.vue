<template>
  <div class="animate-fade-in">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-gray-800">Deals Management</h1>
      <button @click="showModal = true" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl shadow-md transition-all font-semibold flex items-center gap-2 transform hover:-translate-y-0.5">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        <span>Add Deal</span>
      </button>
    </div>
    
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
      <table class="min-w-full divide-y divide-gray-100">
        <thead class="bg-gray-50/50">
          <tr>
            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Deal Name</th>
            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Customer</th>
            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Amount & Discount</th>
            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100">
          <tr v-if="deals.length === 0">
            <td colspan="5" class="px-6 py-12 text-center text-gray-400 font-medium">No deals found. <span v-if="authStore.role !== 'admin'">Create a mock deal to get started.</span></td>
          </tr>
          <tr v-for="deal in deals" :key="deal.id" class="hover:bg-gray-50/50 transition-colors group">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-bold text-gray-900">{{ deal.name }}</div>
              <div class="text-xs text-gray-500">ID: #{{ deal.id }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm text-gray-800 font-medium">{{ deal.contact ? deal.contact.name : '-' }}</div>
              <div class="text-xs text-gray-500">{{ deal.contact ? deal.contact.company : '' }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-bold text-gray-700">Rp {{ formatNumber(deal.amount) }}</div>
              <div v-if="deal.discount_amount" class="text-xs text-red-500 font-medium mt-1">
                Discount: Rp {{ formatNumber(deal.discount_amount) }}
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex flex-col gap-2 items-start">
                <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full shadow-sm uppercase tracking-wide border" 
                  :class="{
                    'bg-green-50 text-green-700 border-green-200': deal.stage === 'closed_won',
                    'bg-red-50 text-red-700 border-red-200': deal.stage === 'closed_lost',
                    'bg-yellow-50 text-yellow-700 border-yellow-200': deal.stage !== 'closed_won' && deal.stage !== 'closed_lost'
                  }">
                  Stage: {{ formatStage(deal.stage) }}
                </span>
                <span v-if="deal.discount_status !== 'none'" class="px-2 py-0.5 inline-flex text-[10px] font-bold rounded-full uppercase"
                  :class="{
                    'bg-orange-100 text-orange-800': deal.discount_status === 'pending',
                    'bg-green-100 text-green-800': deal.discount_status === 'approved',
                    'bg-red-100 text-red-800': deal.discount_status === 'rejected',
                  }">
                  Disc: {{ deal.discount_status }}
                </span>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
              <div class="flex items-center gap-2">
                <!-- Admin Actions -->
                <span v-if="authStore.role === 'admin'" class="text-gray-400 italic text-xs">Read Only</span>

                <!-- Sales Actions -->
                <template v-if="authStore.role === 'sales'">
                  <button v-if="deal.discount_status === 'none' && deal.stage !== 'closed_won'" @click="requestDiscount(deal.id, deal.amount)" class="text-orange-600 hover:text-orange-900 bg-orange-50 px-3 py-1.5 rounded-lg border border-orange-100 hover:bg-orange-100 transition-colors shadow-sm">
                    Req Discount
                  </button>
                  <button v-if="deal.stage !== 'closed_won'" @click="markAsWon(deal.id, deal.discount_status)" class="text-blue-600 hover:text-white bg-blue-50 hover:bg-blue-600 px-3 py-1.5 rounded-lg border border-blue-200 transition-colors shadow-sm">
                    Mark as Won
                  </button>
                </template>

                <!-- Pimpinan Sales Actions -->
                <template v-if="authStore.role === 'pimpinan_sales'">
                  <button v-if="deal.discount_status === 'pending'" @click="approveDiscount(deal.id)" class="text-green-600 bg-green-50 hover:bg-green-100 px-3 py-1.5 rounded-lg border border-green-200 transition-colors shadow-sm">
                    Approve
                  </button>
                  <button v-if="deal.discount_status === 'pending'" @click="rejectDiscount(deal.id)" class="text-red-600 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg border border-red-200 transition-colors shadow-sm">
                    Reject
                  </button>
                  <span v-if="deal.discount_status !== 'pending' && deal.stage === 'closed_won'" class="text-gray-400 italic text-xs">Closed</span>
                  <button v-if="deal.discount_status !== 'pending' && deal.stage !== 'closed_won'" @click="markAsWon(deal.id, deal.discount_status)" class="text-blue-600 hover:text-white bg-blue-50 hover:bg-blue-600 px-3 py-1.5 rounded-lg border border-blue-200 transition-colors shadow-sm">
                    Mark as Won
                  </button>
                </template>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <!-- Modal Form -->
    <transition name="fade">
      <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/40 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden transform transition-all animate-modal-in border border-gray-100">
          <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-900">Add New Deal</h3>
            <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
          </div>
          <form @submit.prevent="saveDeal" class="p-6 space-y-4 max-h-[80vh] overflow-y-auto">
            <!-- Deal Details -->
            <div class="border-b border-gray-100 pb-4 mb-4">
              <h4 class="text-sm font-bold text-blue-600 mb-3">Deal Details</h4>
              <div class="space-y-3">
                <div>
                  <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">Deal Name</label>
                  <input type="text" v-model="newDeal.name" required class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all shadow-sm" />
                </div>
                <div>
                  <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">Amount (Rp)</label>
                  <input type="number" v-model="newDeal.amount" required class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all shadow-sm" />
                </div>
              </div>
            </div>

            <!-- Customer Details -->
            <div>
              <h4 class="text-sm font-bold text-blue-600 mb-3">Customer Details</h4>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                  <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">Customer Name</label>
                  <input type="text" v-model="newDeal.contact_name" required class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all shadow-sm" />
                </div>
                <div>
                  <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">Company</label>
                  <input type="text" v-model="newDeal.contact_company" class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all shadow-sm" />
                </div>
                <div>
                  <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">Email</label>
                  <input type="email" v-model="newDeal.contact_email" class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all shadow-sm" />
                </div>
                <div>
                  <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">Phone</label>
                  <input type="text" v-model="newDeal.contact_phone" class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all shadow-sm" />
                </div>
              </div>
            </div>

            <div class="pt-4 flex justify-end gap-2 border-t border-gray-100 mt-6">
              <button type="button" @click="showModal = false" class="px-4 py-2 rounded-lg font-semibold text-gray-600 hover:bg-gray-50 transition-colors text-sm border border-transparent">Cancel</button>
              <button type="submit" :disabled="isSaving" class="px-4 py-2 rounded-lg font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition-colors text-sm flex items-center gap-2">
                <span v-if="isSaving" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                Save Deal
              </button>
            </div>
          </form>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../api/axios';
import { useAuthStore } from '../stores/auth';
import Swal from 'sweetalert2';

const authStore = useAuthStore();
const deals = ref([]);

const showModal = ref(false);
const isSaving = ref(false);
const newDeal = ref({
  name: '',
  amount: null,
  contact_name: '',
  contact_company: '',
  contact_email: '',
  contact_phone: ''
});

const formatNumber = (num) => {
  return new Intl.NumberFormat('id-ID').format(num);
};

const formatStage = (stage) => {
  return stage.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
};

const fetchDeals = async () => {
  try {
    const response = await api.get('/deals');
    deals.value = response.data;
  } catch (error) {
    console.error('Error fetching deals:', error);
  }
};

const saveDeal = async () => {
  try {
    isSaving.value = true;
    let contactResponse = await api.post('/contacts', {
      name: newDeal.value.contact_name,
      email: newDeal.value.contact_email,
      phone: newDeal.value.contact_phone,
      company: newDeal.value.contact_company
    });
    
    await api.post('/deals', {
      name: newDeal.value.name,
      amount: newDeal.value.amount,
      stage: 'prospecting',
      contact_id: contactResponse.data.id
    });
    
    await fetchDeals();
    showModal.value = false;
    newDeal.value = {
      name: '',
      amount: null,
      contact_name: '',
      contact_company: '',
      contact_email: '',
      contact_phone: ''
    };
    Swal.fire('Berhasil', 'Deal berhasil dibuat.', 'success');
  } catch (error) {
    console.error('Error creating deal:', error);
    Swal.fire('Error', error.response?.data?.message || 'Failed to create deal', 'error');
  } finally {
    isSaving.value = false;
  }
};

const requestDiscount = async (id, maxAmount) => {
  const { value: amountStr } = await Swal.fire({
    title: '💡 Ajukan Diskon',
    html: `
      <div class="text-sm text-gray-500 mb-2">
        Masukkan nominal diskon untuk transaksi ini. Pimpinan Sales akan mereview pengajuan Anda.
      </div>
      <div class="bg-blue-50 text-blue-800 font-bold py-2 px-4 rounded-lg mb-4 text-sm inline-block border border-blue-100">
        Maksimal: Rp ${formatNumber(maxAmount)}
      </div>
    `,
    input: 'text',
    inputPlaceholder: 'Contoh: 1500000',
    showCancelButton: true,
    confirmButtonText: 'Kirim Pengajuan',
    cancelButtonText: 'Batal',
    buttonsStyling: false,
    background: '#ffffff',
    backdrop: `rgba(15, 23, 42, 0.6) backdrop-blur-sm`,
    customClass: {
      popup: 'rounded-[2rem] shadow-2xl border border-gray-100 p-6',
      title: 'text-2xl font-extrabold text-gray-800 mb-2',
      input: 'w-[85%] mx-auto mt-2 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 transition-all text-center text-lg font-bold text-gray-700 p-3',
      confirmButton: 'bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg transform transition-all hover:-translate-y-1 mx-2 mt-4',
      cancelButton: 'bg-white border-2 border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-gray-800 font-bold py-3 px-8 rounded-xl transition-all mx-2 mt-4'
    }
  });

  if (!amountStr) return;
  const amount = parseInt(amountStr.replace(/[^0-9]/g, ''), 10);
  
  if (isNaN(amount) || amount <= 0 || amount > maxAmount) {
    Swal.fire({
      icon: 'error',
      title: 'Oops!',
      text: 'Nominal tidak valid atau melebihi batas maksimal!',
      confirmButtonColor: '#ef4444'
    });
    return;
  }

  try {
    await api.post(`/deals/${id}/discount-request`, { discount_amount: amount });
    await fetchDeals();
    Swal.fire({
      icon: 'success',
      title: 'Terkirim!',
      text: 'Pengajuan diskon berhasil dikirim ke Pimpinan Sales.',
      confirmButtonColor: '#10b981'
    });
  } catch (error) {
    Swal.fire('Error', error.response?.data?.message || 'Error requesting discount', 'error');
  }
};

const approveDiscount = async (id) => {
  try {
    await api.post(`/deals/${id}/discount-approve`);
    await fetchDeals();
    Swal.fire('Approved', 'Diskon berhasil disetujui', 'success');
  } catch (error) {
    Swal.fire('Error', error.response?.data?.message || 'Error', 'error');
  }
};

const rejectDiscount = async (id) => {
  try {
    await api.post(`/deals/${id}/discount-reject`);
    await fetchDeals();
    Swal.fire('Rejected', 'Diskon ditolak', 'info');
  } catch (error) {
    Swal.fire('Error', error.response?.data?.message || 'Error', 'error');
  }
};

const markAsWon = async (id, discountStatus) => {
  if (discountStatus === 'pending') {
    Swal.fire({
      icon: 'warning',
      title: 'Peringatan',
      text: 'Tidak bisa menutup transaksi (Won) karena pengajuan diskon masih "Pending". Tunggu persetujuan Pimpinan.',
    });
    return;
  }

  try {
    await api.put(`/deals/${id}`, {
      stage: 'closed_won'
    });
    await fetchDeals();
    Swal.fire({
      icon: 'success',
      title: 'Deal Won! 🎉',
      text: 'Opportunity sudah ClosedWon',
    });
  } catch (error) {
    Swal.fire('Error', error.response?.data?.message || 'Error updating deal', 'error');
  }
};

onMounted(() => {
  fetchDeals();
});
</script>

<style scoped>
.animate-fade-in { animation: fadeIn 0.4s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
.animate-modal-in { animation: modalScale 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
@keyframes modalScale { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
