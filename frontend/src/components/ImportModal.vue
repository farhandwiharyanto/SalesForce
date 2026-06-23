<template>
  <transition name="fade">
    <div v-if="show" class="fixed inset-0 z-[60] flex items-center justify-center p-4 overflow-y-auto">
      <!-- Overlay Blur -->
      <div class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm" @click="!isImporting && close()"></div>

      <div class="relative bg-white/95 backdrop-blur-md rounded-3xl shadow-2xl w-full max-w-3xl overflow-hidden transform transition-all animate-modal-in flex flex-col my-8 border border-white/20">
        
        <!-- Header -->
        <div class="bg-gray-50/80 backdrop-blur-md px-6 py-4 border-b border-gray-200/60 flex justify-between items-center shrink-0 sticky top-0 z-10">
          <div>
            <h3 class="text-lg font-bold text-gray-900">Bulk Import {{ moduleName }}</h3>
            <p class="text-xs text-gray-500 mt-1">Upload a CSV file to insert multiple records at once.</p>
          </div>
          <button v-if="!isImporting" @click="close" class="text-gray-400 hover:text-gray-600 transition-colors bg-gray-200/50 hover:bg-gray-200 rounded-full p-1.5">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
          </button>
        </div>
        
        <!-- Progress State -->
        <div v-if="isImporting || isImportComplete || (hasImportError && processedCount > 0)" class="p-8 flex flex-col items-center">
          
          <!-- Circular Progress -->
          <div class="relative w-36 h-36 mb-6">
            <svg class="w-full h-full drop-shadow-md" viewBox="0 0 100 100">
              <circle class="text-gray-200 stroke-current" stroke-width="8" cx="50" cy="50" r="40" fill="transparent"></circle>
              <circle class="text-blue-500 stroke-current transition-all duration-300 ease-out" stroke-width="8" stroke-linecap="round" cx="50" cy="50" r="40" fill="transparent" :stroke-dasharray="251.2" :stroke-dashoffset="251.2 - (251.2 * importProgress) / 100" transform="rotate(-90 50 50)"></circle>
            </svg>
            <div class="absolute inset-0 flex items-center justify-center flex-col">
              <span class="text-3xl font-extrabold text-gray-800">{{ importProgress }}%</span>
            </div>
          </div>

          <h3 class="text-2xl font-bold text-gray-800 mb-2">
            <span v-if="isImportComplete" class="text-green-600 flex items-center gap-2"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Import Selesai!</span>
            <span v-else-if="hasImportError" class="text-red-600">Terjadi Kesalahan</span>
            <span v-else>Memproses Data...</span>
          </h3>
          <p class="text-gray-500 mb-6 font-medium bg-gray-100 px-4 py-1.5 rounded-full text-sm shadow-inner">
            <span class="text-blue-600 font-bold">{{ processedCount }}</span> / {{ totalToProcess }} Data Berhasil Diproses
          </p>

          <!-- Terminal Mini -->
          <div class="w-full bg-gray-900 rounded-xl p-4 overflow-hidden shadow-2xl border border-gray-700/50 relative group">
            <div class="absolute inset-0 bg-gradient-to-b from-white/5 to-transparent pointer-events-none"></div>
            <div class="flex items-center gap-2 mb-3 pb-3 border-b border-gray-800">
              <div class="flex gap-1.5">
                <div class="w-3 h-3 rounded-full bg-red-500/80 shadow-[0_0_8px_rgba(239,68,68,0.5)]"></div>
                <div class="w-3 h-3 rounded-full bg-yellow-500/80 shadow-[0_0_8px_rgba(234,179,8,0.5)]"></div>
                <div class="w-3 h-3 rounded-full bg-green-500/80 shadow-[0_0_8px_rgba(34,197,94,0.5)]"></div>
              </div>
              <span class="text-xs text-gray-500 font-mono ml-2">salesforce-import.log</span>
            </div>
            <div ref="terminalRef" class="h-48 overflow-y-auto font-mono text-[11px] leading-relaxed space-y-1.5 pr-2 custom-scrollbar">
              <div v-for="(log, i) in importLogs" :key="i" :class="{
                'text-blue-300': log.type === 'PROCESS',
                'text-green-400': log.type === 'SUCCESS' || log.type === 'COMPLETED',
                'text-red-400': log.type === 'ERROR'
              }">
                <span class="text-gray-600">[{{ log.time }}]</span> 
                <span :class="{'font-bold': log.type !== 'PROCESS'}">{{ log.message }}</span>
              </div>
            </div>
          </div>
          
          <div v-if="hasImportError || isImportComplete" class="mt-8">
            <button @click="close" class="px-8 py-2.5 rounded-xl font-bold text-white bg-gray-800 hover:bg-gray-900 shadow-lg transition-all text-sm">
              Tutup Modal
            </button>
          </div>
        </div>

        <!-- Normal Upload State -->
        <div v-else class="p-6 overflow-y-auto max-h-[70vh]">
          
          <!-- Step 1: Download Template -->
          <div class="mb-8">
            <h4 class="text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
              <span class="bg-blue-100 text-blue-600 w-5 h-5 rounded-full inline-flex items-center justify-center text-xs">1</span> 
              Download Template
            </h4>
            <p class="text-sm text-gray-500 mb-3 ml-7">Please download the CSV template and fill in the required data. Do not change the column headers.</p>
            <button @click="downloadTemplate" class="ml-7 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition-all flex items-center gap-2">
              <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
              Download {{ moduleName }} Template.csv
            </button>
          </div>

          <!-- Step 2: Upload CSV -->
          <div class="mb-6">
            <h4 class="text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
              <span class="bg-blue-100 text-blue-600 w-5 h-5 rounded-full inline-flex items-center justify-center text-xs">2</span> 
              Upload File
            </h4>
            
            <div 
              class="ml-7 border-2 border-dashed rounded-xl p-8 text-center transition-colors relative"
              :class="isDragging ? 'border-blue-500 bg-blue-50' : 'border-gray-300 hover:border-gray-400 bg-gray-50/50'"
              @dragover.prevent="isDragging = true"
              @dragleave.prevent="isDragging = false"
              @drop.prevent="handleDrop"
            >
              <input type="file" ref="fileInput" @change="handleFileSelect" accept=".csv" class="hidden" />
              
              <div v-if="!selectedFile" class="flex flex-col items-center">
                <svg class="w-10 h-10 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                <p class="text-sm font-medium text-gray-600">Drag and drop your CSV file here, or</p>
                <button @click="$refs.fileInput.click()" class="mt-2 text-blue-600 hover:text-blue-800 font-bold text-sm">browse files</button>
              </div>
              
              <div v-else class="flex flex-col items-center">
                <div class="w-12 h-12 bg-green-100 text-green-600 rounded-full flex items-center justify-center mb-2 shadow-sm">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <p class="text-sm font-bold text-gray-800">{{ selectedFile.name }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ (selectedFile.size / 1024).toFixed(1) }} KB</p>
                <button @click.stop="resetFile" class="mt-3 text-red-500 hover:text-red-700 font-bold text-xs bg-red-50 px-3 py-1 rounded-full">Remove file</button>
              </div>
            </div>
          </div>

          <!-- Preview & Validation -->
          <div v-if="parsedData.length > 0 || validationErrors.length > 0" class="ml-7 mt-6 animate-fade-in">
            <h4 v-if="parsedData.length > 0" class="text-sm font-bold text-gray-700 mb-2">Preview ({{ parsedData.length }} rows)</h4>
            
            <div v-if="validationErrors.length > 0" class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl shadow-sm">
              <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div>
                  <h5 class="text-sm font-bold text-red-800">Validation Errors Found</h5>
                  <ul class="mt-1 space-y-1 text-xs text-red-600 list-disc list-inside">
                    <li v-for="(err, i) in validationErrors.slice(0, 5)" :key="i">{{ err }}</li>
                    <li v-if="validationErrors.length > 5">...and {{ validationErrors.length - 5 }} more errors</li>
                  </ul>
                </div>
              </div>
            </div>

            <div v-if="parsedData.length > 0" class="overflow-x-auto border border-gray-200 rounded-lg shadow-sm">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th v-for="col in columns" :key="col" class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">{{ col }}</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                  <tr v-for="(row, idx) in parsedData.slice(0, 5)" :key="idx" class="hover:bg-gray-50 transition-colors">
                    <td v-for="col in columns" :key="col" class="px-4 py-2 text-sm text-gray-700 whitespace-nowrap max-w-[150px] truncate">{{ row[col] || '-' }}</td>
                  </tr>
                  <tr v-if="parsedData.length > 5">
                    <td :colspan="columns.length" class="px-4 py-3 text-center text-xs font-medium text-gray-500 bg-gray-50/50">... showing 5 of {{ parsedData.length }} rows</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div v-if="!isImporting && !isImportComplete && !(hasImportError && processedCount > 0)" class="bg-gray-50/80 backdrop-blur-md px-6 py-4 border-t border-gray-200/60 flex justify-end gap-3 shrink-0">
          <button @click="close" class="px-5 py-2.5 rounded-xl font-bold text-gray-600 hover:bg-gray-200 transition-colors text-sm">Cancel</button>
          <button 
            @click="processImport" 
            :disabled="parsedData.length === 0 || validationErrors.length > 0" 
            class="px-6 py-2.5 rounded-xl font-bold text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg transition-all text-sm flex items-center gap-2"
          >
            Import {{ parsedData.length }} Records
          </button>
        </div>

      </div>
    </div>
  </transition>
</template>

<script setup>
import { ref, watch, nextTick } from 'vue';
import Papa from 'papaparse';
import api from '../api/axios';

const props = defineProps({
  show: Boolean,
  moduleName: String,
  columns: Array,
  requiredColumns: Array,
  apiEndpoint: String,
  sampleRow: Array,
});

const emit = defineEmits(['close', 'import-success', 'import-error']);

const isDragging = ref(false);
const selectedFile = ref(null);
const fileInput = ref(null);
const parsedData = ref([]);
const validationErrors = ref([]);

// Progress State
const isImporting = ref(false);
const isImportComplete = ref(false);
const hasImportError = ref(false);
const importProgress = ref(0);
const processedCount = ref(0);
const totalToProcess = ref(0);
const importLogs = ref([]);
const terminalRef = ref(null);

watch(() => props.show, (newVal) => {
  if (newVal) {
    resetFile();
    resetProgress();
  }
});

const resetProgress = () => {
  isImporting.value = false;
  isImportComplete.value = false;
  hasImportError.value = false;
  importProgress.value = 0;
  processedCount.value = 0;
  totalToProcess.value = 0;
  importLogs.value = [];
};

const addLog = (type, message) => {
  const time = new Date().toLocaleTimeString('en-US', { hour12: false, hour: '2-digit', minute:'2-digit', second:'2-digit' });
  importLogs.value.push({ type, message, time });
  scrollToBottom();
};

const scrollToBottom = () => {
  nextTick(() => {
    setTimeout(() => {
      if (terminalRef.value) {
        terminalRef.value.scrollTop = terminalRef.value.scrollHeight;
      }
    }, 50);
  });
};

const downloadTemplate = () => {
  let csvContent = props.columns.join(",") + "\n";
  if (props.sampleRow && props.sampleRow.length > 0) {
    const row = props.sampleRow.map(val => `"${val}"`).join(",");
    csvContent += row + "\n";
  }
  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
  const link = document.createElement("a");
  const url = URL.createObjectURL(blob);
  link.setAttribute("href", url);
  link.setAttribute("download", `${props.moduleName}_Template.csv`);
  link.style.visibility = 'hidden';
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
};

const handleDrop = (e) => {
  isDragging.value = false;
  const files = e.dataTransfer.files;
  if (files.length > 0 && files[0].name.endsWith('.csv')) {
    processFile(files[0]);
  } else {
    validationErrors.value = ['Please upload a valid CSV file.'];
  }
};

const handleFileSelect = (e) => {
  const files = e.target.files;
  if (files.length > 0) {
    processFile(files[0]);
  }
};

const processFile = (file) => {
  selectedFile.value = file;
  validationErrors.value = [];
  parsedData.value = [];
  resetProgress();

  Papa.parse(file, {
    header: true,
    skipEmptyLines: true,
    complete: (results) => {
      if (results.errors.length > 0) {
        validationErrors.value.push(`CSV parsing error: ${results.errors[0].message}`);
        return;
      }
      
      const data = results.data;
      if (data.length === 0) {
        validationErrors.value.push("The CSV file is empty.");
        return;
      }

      const headers = Object.keys(data[0]);
      const missingHeaders = props.columns.filter(col => !headers.includes(col));
      if (missingHeaders.length > 0) {
        validationErrors.value.push(`Missing columns: ${missingHeaders.join(', ')}`);
        return;
      }

      data.forEach((row, index) => {
        props.requiredColumns.forEach(req => {
          if (!row[req] || row[req].trim() === '') {
            validationErrors.value.push(`Row ${index + 1}: '${req}' is required.`);
          }
        });
      });

      if (validationErrors.value.length === 0) {
        parsedData.value = data;
      }
    }
  });
};

const resetFile = () => {
  selectedFile.value = null;
  parsedData.value = [];
  validationErrors.value = [];
  if (fileInput.value) fileInput.value.value = '';
};

const close = () => {
  if (isImportComplete.value || hasImportError.value) {
    if (processedCount.value > 0) {
      emit('import-success', `Successfully imported ${processedCount.value} records.`);
    }
  }
  emit('close');
};

const processImport = async () => {
  isImporting.value = true;
  isImportComplete.value = false;
  hasImportError.value = false;
  importProgress.value = 0;
  processedCount.value = 0;
  totalToProcess.value = parsedData.value.length;
  importLogs.value = [];

  const chunkSize = 1; // Process row by row for real-time terminal effect
  
  addLog('PROCESS', `Memulai import ${totalToProcess.value} data...`);

  for (let i = 0; i < parsedData.value.length; i += chunkSize) {
    const chunk = parsedData.value.slice(i, i + chunkSize);
    const rowIdentifier = Object.values(chunk[0])[0] || `Baris ${i + 1}`;

    try {
      addLog('PROCESS', `Mengirim data: ${rowIdentifier}...`);
      
      await api.post(props.apiEndpoint, { data: chunk });
      
      processedCount.value += chunk.length;
      importProgress.value = Math.round((processedCount.value / totalToProcess.value) * 100);
      addLog('SUCCESS', `Tersimpan: ${rowIdentifier}`);
      
      if (totalToProcess.value < 50) {
        await new Promise(resolve => setTimeout(resolve, 50));
      }

    } catch (error) {
      console.error('Import error chunk:', error);
      let errorMsg = error.response?.data?.message || 'Gagal menyimpan data';
      
      if (errorMsg.includes('Foreign key violation') || errorMsg.includes('violates foreign key constraint')) {
        const detailMatch = errorMsg.match(/DETAIL:\s*(.*)/);
        errorMsg = detailMatch ? `Relational Error: ${detailMatch[1]}` : "Relational Error: ID tidak ditemukan di database";
      }
      
      addLog('ERROR', `Gagal pada ${rowIdentifier}: ${errorMsg}`);
      hasImportError.value = true;
      isImporting.value = false;
      addLog('ERROR', 'Proses import dihentikan karena error.');
      return;
    }
  }
  
  isImporting.value = false;
  isImportComplete.value = true;
  addLog('COMPLETED', `Selesai! ${processedCount.value} data berhasil di-import.`);
  
  setTimeout(() => {
    emit('import-success', `Successfully imported ${processedCount.value} records.`);
    emit('close');
  }, 2000);
};
</script>

<style scoped>
.animate-modal-in { animation: modalScale 0.25s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
@keyframes modalScale { from { opacity: 0; transform: scale(0.95) translateY(10px); } to { opacity: 1; transform: scale(1) translateY(0); } }
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
.animate-fade-in { animation: fadeIn 0.4s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

.custom-scrollbar::-webkit-scrollbar { width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: rgba(0, 0, 0, 0.2); border-radius: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.2); border-radius: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255, 255, 255, 0.3); }
</style>
