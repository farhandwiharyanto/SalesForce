<template>
  <div class="pb-12 animate-fade-in">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
      <div class="flex items-center gap-3">
        <button @click="router.push('/optys')" class="text-gray-400 hover:text-blue-600 transition-colors p-2 -ml-2 rounded-lg hover:bg-blue-50">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </button>
        <div>
          <h1 class="text-3xl font-bold text-gray-900 tracking-tight flex items-center gap-3">
            {{ opty?.opportunity_number }}
            <span v-if="opty" class="px-3 py-1 text-[11px] font-bold rounded-full bg-blue-100 text-blue-700 tracking-wider uppercase border border-blue-200 shadow-sm">OPTY</span>
          </h1>
          <p class="text-gray-500 text-sm mt-1 font-medium">{{ opty?.name }}</p>
        </div>
      </div>
      <div class="flex items-center gap-3" v-if="opty">
        <button v-if="opty.stage !== 'Closed Won' && opty.stage !== 'Closed Lost'" @click="showStageModal = true" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl shadow-sm transition-all font-semibold flex items-center gap-2 transform hover:-translate-y-0.5 border border-blue-700">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
          Update Stage
        </button>
      </div>
    </div>

    <div v-if="isLoading" class="flex flex-col items-center justify-center h-64 text-gray-500 font-semibold bg-white rounded-2xl shadow-sm border border-gray-100">
      <div class="loader-spinner mb-3"></div>
      Loading opty details...
    </div>
    
    <div v-else-if="opty" class="space-y-6">
      
      <!-- PIPELINE PROGRESS BAR -->
      <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <h3 class="text-sm font-bold text-gray-800 uppercase tracking-widest mb-6 flex items-center gap-2">
          <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
          Pipeline Stage
        </h3>
        
        <div class="flex w-full overflow-x-auto pb-2 scrollbar-hide pl-4 pr-6">
          <div v-for="(stage, index) in pipelineStages" :key="stage" 
               class="chevron-stage"
               :class="[
                 getStageIndex(opty.stage) >= index && opty.stage !== 'Closed Lost' ? 'chevron-active' : 'chevron-inactive',
                 opty.stage === 'Closed Lost' && stage === 'Closed Lost' ? 'chevron-lost' : '',
                 index === 0 ? 'first' : '',
                 index === pipelineStages.length - 1 ? 'last' : ''
               ]">
               <span class="z-10 px-6 text-center leading-tight whitespace-nowrap">{{ stage }}</span>
          </div>
        </div>
      </div>

      <!-- OPPORTUNITY DETAILS -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gray-50/80 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
          <h2 class="text-sm font-bold text-gray-800 uppercase tracking-widest flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Opportunity Information
          </h2>
        </div>
        
        <div class="p-0">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 divide-y lg:divide-y-0 lg:divide-x divide-gray-100">
            
            <!-- Column 1 -->
            <div class="flex flex-col divide-y divide-gray-100">
              <div class="py-4 px-6 hover:bg-gray-50 transition-colors">
                <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Opportunity Number</div>
                <div class="text-sm font-bold text-blue-700">{{ opty.opportunity_number }}</div>
              </div>
              <div class="py-4 px-6 hover:bg-gray-50 transition-colors">
                <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Opportunity Name</div>
                <div class="text-sm font-semibold text-gray-900">{{ opty.name }}</div>
              </div>
              <div class="py-4 px-6 hover:bg-gray-50 transition-colors">
                <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Account Name (Customer)</div>
                <div class="text-sm font-semibold text-gray-900">{{ opty.customer ? opty.customer.customer_name : '-' }}</div>
              </div>
              <div class="py-4 px-6 hover:bg-gray-50 transition-colors">
                <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Request Type</div>
                <div>
                  <span class="px-3 py-1 bg-gray-100 text-gray-700 font-bold text-xs rounded-full uppercase border border-gray-200">{{ opty.request_type || '-' }}</span>
                </div>
              </div>
            </div>

            <!-- Column 2 -->
            <div class="flex flex-col divide-y divide-gray-100">
              <div class="py-4 px-6 hover:bg-gray-50 transition-colors">
                <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Assigned To</div>
                <div class="text-sm font-semibold text-blue-600">{{ opty.assignee ? (opty.assignee.first_name + ' ' + (opty.assignee.last_name||'')).trim() : '-' }}</div>
              </div>
              <div class="py-4 px-6 hover:bg-gray-50 transition-colors">
                <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Opportunity Owner</div>
                <div class="text-sm font-semibold text-blue-600">{{ opty.owner ? (opty.owner.first_name + ' ' + (opty.owner.last_name||'')).trim() : '-' }}</div>
              </div>
              <div class="py-4 px-6 hover:bg-gray-50 transition-colors">
                <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Target Opportunity Close Date</div>
                <div class="flex items-center gap-2 text-sm font-semibold text-gray-900">
                  <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                  {{ opty.target_close_date || '-' }}
                </div>
              </div>
              <div class="py-4 px-6 hover:bg-gray-50 transition-colors">
                <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Customer RFS Date</div>
                <div class="flex items-center gap-2 text-sm font-semibold text-gray-900">
                  <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                  {{ opty.customer_expected_rfs || '-' }}
                </div>
              </div>
            </div>

            <!-- Column 3 -->
            <div class="flex flex-col divide-y divide-gray-100">
              <div class="py-4 px-6 hover:bg-gray-50 transition-colors">
                <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Confidence Level</div>
                <div>
                  <span class="px-3 py-1 bg-blue-50 text-blue-700 font-bold text-xs rounded-full uppercase border border-blue-100">{{ opty.confidence_level || '-' }}</span>
                </div>
              </div>
              <div class="py-4 px-6 hover:bg-gray-50 transition-colors">
                <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Estimated Value (OTC)</div>
                <div class="text-lg font-bold text-green-600">Rp {{ formatNumber(opty.estimated_value_otc) }}</div>
              </div>
              <div class="py-4 px-6 hover:bg-gray-50 transition-colors">
                <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Estimated Value (MRC)</div>
                <div class="text-lg font-bold text-green-600">Rp {{ formatNumber(opty.estimated_value_mrc) }}</div>
              </div>
              <div class="py-4 px-6 hover:bg-gray-50 transition-colors">
                <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1 flex items-center justify-between">
                  <span>Converted From Lead</span>
                  <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-bold rounded-md" 
                    :class="opty.is_converted_from_lead ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'">
                    {{ opty.is_converted_from_lead ? 'Yes' : 'No' }}
                  </span>
                </div>
              </div>
              <div class="py-4 px-6 hover:bg-gray-50 transition-colors">
                <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Created Time</div>
                <div class="text-sm font-semibold text-gray-600">{{ new Date(opty.created_at).toLocaleString('id-ID', { dateStyle: 'long', timeStyle: 'short' }) }}</div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>

    <!-- Update Stage Modal -->
    <transition name="fade">
      <div v-if="showStageModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/40 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden transform transition-all animate-modal-in border border-gray-100">
          <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-900">Update Stage</h3>
            <button @click="showStageModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
          </div>
          <div class="p-6">
            <label class="block text-sm font-bold text-gray-700 mb-2">Select New Stage</label>
            <select v-model="selectedStageToUpdate" class="w-full bg-white border border-gray-300 rounded-xl px-4 py-3 text-gray-800 font-semibold focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 outline-none transition-all shadow-sm appearance-none cursor-pointer">
              <option v-for="stage in pipelineStages" :key="stage" :value="stage">{{ stage }}</option>
            </select>
            
            <div class="mt-6 flex justify-end gap-3">
              <button @click="showStageModal = false" class="px-5 py-2.5 rounded-xl font-bold text-gray-600 hover:bg-gray-100 transition-colors">Cancel</button>
              <button @click="updateStage" :disabled="isUpdating" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-bold shadow-md transition-all flex items-center gap-2">
                <span v-if="isUpdating" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                Save Stage
              </button>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '../api/axios';
import Swal from 'sweetalert2';

const route = useRoute();
const router = useRouter();

const opty = ref(null);
const isLoading = ref(true);

const showStageModal = ref(false);
const isUpdating = ref(false);
const selectedStageToUpdate = ref('');

const pipelineStages = [
  'Prospect and Analysis',
  'Value Proposition',
  'Perception Analysis',
  'Proposal or Quote',
  'Negotiation or Review',
  'Closed Won',
  'Closed Lost'
];

const getStageIndex = (stage) => {
  return pipelineStages.indexOf(stage);
};

const formatNumber = (num) => {
  if (!num) return '0';
  return new Intl.NumberFormat('id-ID').format(num);
};

const fetchOptyDetails = async () => {
  try {
    const response = await api.get(`/optys/${route.params.id}`);
    opty.value = response.data;
    selectedStageToUpdate.value = opty.value.stage;
  } catch (error) {
    console.error('Error fetching opty details:', error);
    Swal.fire('Error', 'Opportunity not found or failed to load.', 'error');
    router.push('/optys');
  } finally {
    isLoading.value = false;
  }
};

const updateStage = async () => {
  if (selectedStageToUpdate.value === 'Closed Won' && opty.value.discount_status === 'pending') {
    Swal.fire('Peringatan', 'Tidak bisa Closed Won karena diskon masih pending', 'warning');
    return;
  }

  try {
    isUpdating.value = true;
    await api.put(`/optys/${opty.value.id}`, { stage: selectedStageToUpdate.value });
    Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Stage berhasil diupdate', timer: 1500, showConfirmButton: false });
    showStageModal.value = false;
    await fetchOptyDetails();
  } catch (error) {
    console.error('Error updating stage:', error);
    Swal.fire('Error', error.response?.data?.message || 'Gagal mengupdate stage', 'error');
  } finally {
    isUpdating.value = false;
  }
};

onMounted(() => {
  fetchOptyDetails();
});
</script>

<style scoped>
.animate-fade-in { animation: fadeIn 0.4s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
.animate-modal-in { animation: modalScale 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
@keyframes modalScale { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
.loader-spinner { width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #4f46e5; border-radius: 50%; animation: spin 0.8s linear infinite; }
@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

/* PIPELINE CHEVRON CSS */
.chevron-stage {
  position: relative;
  flex: 1;
  height: 48px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.75rem;
  font-weight: 700;
  margin-right: 4px;
}
.chevron-stage::after {
  content: '';
  position: absolute;
  top: 0;
  right: -16px;
  width: 0;
  height: 0;
  border-top: 24px solid transparent;
  border-bottom: 24px solid transparent;
  border-left: 16px solid;
  z-index: 2;
}
.chevron-stage::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 0;
  height: 0;
  border-top: 24px solid transparent;
  border-bottom: 24px solid transparent;
  border-left: 16px solid white;
  z-index: 1;
}
.chevron-stage.first::before {
  display: none;
}
.chevron-stage.first {
  border-top-left-radius: 4px;
  border-bottom-left-radius: 4px;
}
.chevron-stage.last::after {
  display: none;
}
.chevron-stage.last {
  margin-right: 0;
  border-top-right-radius: 4px;
  border-bottom-right-radius: 4px;
}

/* Pipeline Colors */
.chevron-active {
  background-color: #22c55e; /* green-500 */
  color: white;
}
.chevron-active::after {
  border-left-color: #22c55e;
}
.chevron-inactive {
  background-color: #f3f4f6; /* gray-100 */
  color: #6b7280; /* gray-500 */
}
.chevron-inactive::after {
  border-left-color: #f3f4f6;
}
.chevron-lost {
  background-color: #ef4444; /* red-500 */
  color: white;
}
.chevron-lost::after {
  border-left-color: #ef4444;
}

/* Utilities */
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>
