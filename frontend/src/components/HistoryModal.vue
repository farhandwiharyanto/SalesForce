<template>
  <transition name="fade">
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/40 backdrop-blur-sm p-4">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl overflow-hidden transform transition-all animate-modal-in flex flex-col max-h-[90vh]">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center shrink-0">
          <h3 class="text-lg font-bold text-gray-900">{{ title }}</h3>
          <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
          </button>
        </div>
        <div class="p-6 overflow-y-auto bg-gray-50 flex-1">
          <div v-if="isLoading" class="flex justify-center py-8">
            <div class="loader-spinner"></div>
          </div>
          <div v-else-if="histories.length === 0" class="text-center py-8 text-gray-500">
            No history found.
          </div>
          <div v-else class="space-y-6">
            <div v-for="history in histories" :key="history.id" class="flex gap-4">
              <div class="flex flex-col items-center">
                <div class="w-3 h-3 bg-blue-500 rounded-full ring-4 ring-blue-100 mt-1.5"></div>
                <div class="w-0.5 h-full bg-gray-200 mt-2"></div>
              </div>
              <div class="pb-6 w-full">
                <div class="text-xs font-semibold text-gray-400 mb-1">
                  {{ new Date(history.created_at).toLocaleString('id-ID', { dateStyle: 'long', timeStyle: 'short' }) }}
                </div>
                <div class="text-sm text-gray-800 font-medium bg-white p-3 rounded-lg border border-gray-100 shadow-sm">
                  <div class="mb-1">
                    <span class="font-bold text-blue-600">{{ history.user ? history.user.first_name + ' ' + (history.user.last_name || '') : 'System' }}</span>
                    <span class="text-gray-500 ml-1">{{ history.description }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </transition>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue';

const props = defineProps({
  show: Boolean,
  title: {
    type: String,
    default: 'History'
  },
  isLoading: Boolean,
  histories: {
    type: Array,
    default: () => []
  }
});

defineEmits(['close']);
</script>

<style scoped>
.animate-modal-in { animation: modalScale 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
@keyframes modalScale { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
.loader-spinner { width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #4f46e5; border-radius: 50%; animation: spin 0.8s linear infinite; }
@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
</style>
