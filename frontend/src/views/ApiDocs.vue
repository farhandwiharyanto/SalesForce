<template>
  <div class="animate-fade-in pb-12">
    <!-- Header Section -->
    <div class="mb-8">
      <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">Semua API</h1>
      <p class="text-gray-500 mt-2 text-lg">Dokumentasi dan daftar endpoint API untuk integrasi sistem.</p>
    </div>

    <div class="space-y-6">
      <!-- OrderSales API -->
      <div class="bg-blue-50 border border-blue-100 p-6 rounded-2xl shadow-sm">
        <h3 class="text-lg font-bold text-blue-900 mb-2 flex items-center gap-2">
          <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
          API Integrasi OrderSales
        </h3>
        <p class="text-sm text-blue-800 mb-2">Endpoint ini sudah siap digunakan oleh project OrderSales untuk menarik data customer (GET Request):</p>
        <code class="block bg-white p-3 rounded-lg border border-blue-200 text-sm font-mono text-gray-800 shadow-sm overflow-x-auto">
          GET http://localhost:8000/api/integration/ordersales/customers
        </code>
      </div>

      <!-- You can add more APIs here in the future -->
    </div>
  </div>
</template>

<script setup>
// Script for API Docs if needed
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
