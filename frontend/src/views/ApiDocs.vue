<template>
  <div class="animate-fade-in pb-12">
    <!-- Header Section -->
    <div class="mb-8">
      <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">Semua API</h1>
      <p class="text-gray-500 mt-2 text-lg">Dokumentasi dan daftar endpoint API lengkap untuk integrasi sistem.</p>
    </div>

    <div class="space-y-6">
      
      <!-- API Groups -->
      <div v-for="group in apiGroups" :key="group.title" class="bg-white border border-gray-200 p-6 rounded-2xl shadow-sm hover:shadow-md transition-shadow">
        <h3 class="text-xl font-bold text-gray-800 mb-1">{{ group.title }}</h3>
        <p class="text-sm text-gray-500 mb-4">{{ group.description }}</p>
        
        <div class="space-y-3">
          <div v-for="ep in group.endpoints" :key="ep.path" class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-4 rounded-xl bg-gray-50 border border-gray-100">
            <div class="flex flex-col gap-1">
              <div class="flex items-center gap-3">
                <span :class="getMethodClass(ep.method)" class="px-2.5 py-1 text-xs font-black tracking-wider rounded uppercase">
                  {{ ep.method }}
                </span>
                <code class="text-sm font-mono text-gray-800 font-semibold">{{ ep.path }}</code>
              </div>
            </div>
            <div class="text-sm text-gray-600 md:w-1/2 text-left md:text-right">
              {{ ep.description }}
            </div>
          </div>
        </div>
      </div>
      
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const getMethodClass = (method) => {
  if (method.includes('GET')) return 'bg-blue-100 text-blue-700';
  if (method.includes('POST')) return 'bg-green-100 text-green-700';
  if (method.includes('PUT')) return 'bg-orange-100 text-orange-700';
  if (method.includes('DELETE')) return 'bg-red-100 text-red-700';
  return 'bg-gray-200 text-gray-800';
};

const apiGroups = ref([
  {
    title: '🔑 Authentication API',
    description: 'Endpoint untuk autentikasi user dan manajemen sesi.',
    endpoints: [
      { method: 'POST', path: '/api/login', description: 'Login user dan mendapatkan token.' },
      { method: 'POST', path: '/api/logout', description: 'Logout user.' },
      { method: 'GET', path: '/api/me', description: 'Mendapatkan profil user saat ini.' },
    ]
  },
  {
    title: '👥 Users & Roles API',
    description: 'Manajemen pengguna dan sinkronisasi hak akses peran.',
    endpoints: [
      { method: 'GET/POST/PUT/DELETE', path: '/api/users', description: 'Manajemen penuh data Users (CRUD).' },
      { method: 'GET', path: '/api/roles/{role}/privileges', description: 'Mendapatkan daftar modul akses (privileges) yang saat ini aktif untuk suatu Role.' },
      { method: 'PUT', path: '/api/roles/{role}/privileges', description: 'Update modul akses (privileges) untuk suatu Role.' },
    ]
  },
  {
    title: '🔗 Integrasi & Webhook API',
    description: 'Endpoint terkait log integrasi eksternal, webhook, dan aktivitas sistem.',
    endpoints: [
      { method: 'GET', path: '/api/integration/ordersales/customers', description: 'Endpoint khusus integrasi eksternal OrderSales.' },
      { method: 'GET', path: '/api/webhook-logs', description: 'Melihat log webhook dari pihak ke-3.' },
      { method: 'POST', path: '/api/webhook-logs/{id}/retry', description: 'Mencoba ulang (retry) pengiriman webhook yang gagal.' },
      { method: 'GET', path: '/api/activities', description: 'Mengambil log aktivitas operasional CRM.' },
      { method: 'GET', path: '/api/login-histories', description: 'Mengambil log riwayat akses / login.' },
    ]
  },
  {
    title: '💼 Opportunities (Opty) API',
    description: 'Manajemen peluang penjualan (Opportunities) dan permintaan diskon.',
    endpoints: [
      { method: 'GET/POST/PUT/DELETE', path: '/api/optys', description: 'CRUD data Opportunities.' },
      { method: 'POST', path: '/api/optys/bulk', description: 'Membuat multiple data Opty sekaligus (Bulk Insert).' },
      { method: 'POST', path: '/api/optys/{id}/discount-request', description: 'Meminta persetujuan diskon tambahan.' },
      { method: 'POST', path: '/api/optys/{id}/discount-approve', description: 'Menyetujui diskon (oleh atasan).' },
      { method: 'POST', path: '/api/optys/{id}/discount-reject', description: 'Menolak diskon (oleh atasan).' },
    ]
  },
  {
    title: '🎯 Leads API',
    description: 'Manajemen calon pelanggan (Leads).',
    endpoints: [
      { method: 'GET/POST/PUT/DELETE', path: '/api/leads', description: 'CRUD data Leads.' },
      { method: 'POST', path: '/api/leads/bulk', description: 'Bulk insert Leads.' },
      { method: 'GET/POST/PUT/DELETE', path: '/api/contacts', description: 'Manajemen Kontak (Contacts).' },
    ]
  },
  {
    title: '🏢 Customers API',
    description: 'Manajemen data pelanggan tetap (Customers).',
    endpoints: [
      { method: 'GET/POST/PUT/DELETE', path: '/api/customers', description: 'CRUD data Customers.' },
      { method: 'POST', path: '/api/customers/bulk', description: 'Bulk insert Customers.' },
    ]
  },
  {
    title: '📦 Products API',
    description: 'Manajemen katalog produk.',
    endpoints: [
      { method: 'GET/POST/PUT/DELETE', path: '/api/products', description: 'CRUD data Products.' },
      { method: 'POST', path: '/api/products/bulk', description: 'Bulk insert Products.' },
    ]
  },
  {
    title: '📄 Contracts API',
    description: 'Manajemen data kontrak / SPK.',
    endpoints: [
      { method: 'GET/POST/PUT/DELETE', path: '/api/contracts', description: 'CRUD data Contracts.' },
      { method: 'POST', path: '/api/contracts/bulk', description: 'Bulk insert Contracts.' },
    ]
  },
  {
    title: '🔧 Service Instance Accounts (SIA) API',
    description: 'Manajemen operasional purna jual dan garansi.',
    endpoints: [
      { method: 'GET/POST/PUT', path: '/api/service-instance-accounts', description: 'Index, Store, Update data SIA.' },
      { method: 'POST', path: '/api/service-instance-accounts/bulk', description: 'Bulk insert SIA.' },
    ]
  },
]);
</script>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.5s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
