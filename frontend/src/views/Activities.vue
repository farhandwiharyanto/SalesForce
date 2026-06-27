<template>
  <div class="animate-fade-in">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
      <div>
        <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">Recent Activities</h1>
        <p class="text-gray-500 mt-2 text-lg">Monitor all recent actions and updates across the project.</p>
      </div>
    </div>

    <!-- Main Content Area -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
      <div v-if="loading" class="flex justify-center py-12">
        <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
      </div>

      <div v-else-if="activities.length === 0" class="text-center py-12 text-gray-400 text-base font-medium">
        No recent activities found.
      </div>

      <div v-else class="space-y-6">
        <div v-for="activity in activities" :key="activity.id" class="flex items-start gap-4 p-4 rounded-xl hover:bg-gray-50 transition-colors border border-transparent hover:border-gray-100">
          <div :class="`flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center shadow-sm border text-white font-bold text-lg ${getColorClass(activity.color)}`">
            {{ activity.type.charAt(0) }}
          </div>
          <div class="flex-grow">
            <div class="flex justify-between items-start">
              <h3 class="text-lg font-bold text-gray-900">{{ activity.title }}</h3>
              <span class="text-sm font-medium text-gray-400 whitespace-nowrap">{{ formatDate(activity.date) }}</span>
            </div>
            <p class="text-gray-500 text-sm mt-1 font-medium">{{ activity.description }}</p>
            <span class="inline-block mt-2 px-2.5 py-1 text-xs font-bold rounded-md bg-gray-100 text-gray-600 uppercase tracking-wider">{{ activity.type }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../api/axios';

const activities = ref([]);
const loading = ref(true);

const fetchActivities = async () => {
  try {
    const res = await api.get('/activities');
    activities.value = res.data;
  } catch (error) {
    console.error('Error fetching activities:', error);
  } finally {
    loading.value = false;
  }
};

const formatDate = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  return date.toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const getColorClass = (colorName) => {
  const map = {
    'blue': 'bg-blue-500 border-blue-600',
    'indigo': 'bg-indigo-500 border-indigo-600',
    'green': 'bg-green-500 border-green-600',
    'purple': 'bg-purple-500 border-purple-600',
    'orange': 'bg-orange-500 border-orange-600',
  };
  return map[colorName] || 'bg-gray-500 border-gray-600';
};

onMounted(() => {
  fetchActivities();
});
</script>
