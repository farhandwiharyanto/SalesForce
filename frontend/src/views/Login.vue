<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
    <div class="absolute inset-0 z-0">
      <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 rounded-full bg-blue-100 opacity-50"></div>
      <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-72 h-72 rounded-full bg-indigo-100 opacity-50"></div>
    </div>
    
    <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-3xl shadow-xl relative z-10 border border-gray-100">
      <div>
        <div class="mx-auto w-16 h-16 rounded-2xl bg-blue-600 flex items-center justify-center shadow-lg shadow-blue-200">
          <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
        </div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
          Sign in to your account
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
          SalesForce CRM Prototype
        </p>
      </div>
      
      <form class="mt-8 space-y-6" @submit.prevent="handleLogin">
        <div class="rounded-md shadow-sm -space-y-px">
          <div>
            <label for="email-address" class="sr-only">Email address</label>
            <input id="email-address" name="email" type="email" autocomplete="email" required v-model="email" class="appearance-none rounded-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-xl focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" placeholder="Email address" />
          </div>
          <div>
            <label for="password" class="sr-only">Password</label>
            <input id="password" name="password" type="password" autocomplete="current-password" required v-model="password" class="appearance-none rounded-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-xl focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" placeholder="Password" />
          </div>
        </div>

        <div v-if="error" class="text-sm text-red-500 text-center font-medium bg-red-50 py-2 rounded-lg">
          {{ error }}
        </div>

        <div>
          <button type="submit" :disabled="loading" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-semibold rounded-xl text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all shadow-md">
            <span v-if="loading">Signing in...</span>
            <span v-else>Sign in</span>
          </button>
        </div>
      </form>
      
      <div class="mt-6">
        <p class="text-xs text-gray-500 text-center font-medium mb-2">Dummy Accounts (Password: password123):</p>
        <div class="flex flex-col gap-2 text-xs">
          <div class="bg-gray-50 p-2 rounded border border-gray-100 flex justify-between items-center cursor-pointer hover:bg-gray-100 transition-colors" @click="email='admin@salesforce.com'; password='password123'">
            <span class="font-bold text-gray-700">Admin</span> <span class="text-blue-600">admin@salesforce.com</span>
          </div>
          <div class="bg-gray-50 p-2 rounded border border-gray-100 flex justify-between items-center cursor-pointer hover:bg-gray-100 transition-colors" @click="email='pimpinan@salesforce.com'; password='password123'">
            <span class="font-bold text-gray-700">Pimpinan</span> <span class="text-blue-600">pimpinan@salesforce.com</span>
          </div>
          <div class="bg-gray-50 p-2 rounded border border-gray-100 flex justify-between items-center cursor-pointer hover:bg-gray-100 transition-colors" @click="email='sales@salesforce.com'; password='password123'">
            <span class="font-bold text-gray-700">Sales</span> <span class="text-blue-600">sales@salesforce.com</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const router = useRouter();
const authStore = useAuthStore();

const email = ref('');
const password = ref('');
const error = ref('');
const loading = ref(false);

const handleLogin = async () => {
  try {
    loading.value = true;
    error.value = '';
    await authStore.login(email.value, password.value);
    router.push('/');
  } catch (err) {
    error.value = err.response?.data?.message || 'Login failed. Please try again.';
  } finally {
    loading.value = false;
  }
};
</script>
