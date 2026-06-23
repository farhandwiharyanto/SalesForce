<template>
  <div class="animate-fade-in pb-12">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-gray-800 tracking-tight">Opty Management</h1>
      <div class="flex items-center gap-3">
        <button @click="showImportModal = true" class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 px-4 py-2.5 rounded-xl font-bold shadow-sm transition-all flex items-center gap-2">
          <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
          Import
        </button>
        <button @click="openAddModal" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl shadow-md transition-all font-semibold flex items-center gap-2 transform hover:-translate-y-0.5">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
          <span>Add Opty</span>
        </button>
      </div>
    </div>
    
    <!-- Notification -->
    <transition name="slide-down">
      <div v-if="notification" class="fixed top-20 right-8 z-50 px-6 py-4 rounded-xl shadow-2xl flex items-center gap-3 font-semibold text-white" :class="notification.type === 'error' ? 'bg-red-600' : 'bg-green-600'">
        <svg v-if="notification.type === 'success'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        {{ notification.message }}
      </div>
    </transition>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden relative">
      <div v-if="isLoading" class="flex flex-col items-center justify-center h-64 text-gray-500 font-semibold">
        <div class="loader-spinner mb-3"></div>
        Loading optys...
      </div>
      
      <div class="overflow-x-auto" v-else>
        <table class="min-w-full divide-y divide-gray-100">
          <thead class="bg-gray-50/50">
            <tr>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Opportunity Number</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Opportunity Name</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Assigned To</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Customer</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Target Close</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Expected RFS</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Created Time</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">From Lead</th>
              <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-100">
            <tr v-if="optys.length === 0">
              <td colspan="9" class="px-6 py-12 text-center text-gray-400 font-medium">No optys found. <span>Create a new opty to get started.</span></td>
            </tr>
            <tr v-for="opty in optys" :key="opty.id" class="hover:bg-gray-50/50 transition-colors group">
              <td class="px-6 py-4 whitespace-nowrap">
                <router-link :to="`/optys/${opty.id}`" class="text-sm font-mono font-bold text-blue-600 hover:text-blue-800 transition-colors">{{ opty.opportunity_number }}</router-link>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-bold text-gray-900">{{ opty.name }}</div>
                <div class="text-[10px] uppercase font-bold text-gray-400 mt-1">{{ opty.stage }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-semibold text-blue-600">{{ opty.assignee ? (opty.assignee.first_name + ' ' + (opty.assignee.last_name||'')).trim() : '-' }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <router-link v-if="opty.customer" :to="`/customers`" class="text-sm font-semibold text-blue-600 hover:text-blue-800 flex items-center gap-1">
                  {{ opty.customer.customer_name }}
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                </router-link>
                <span v-else class="text-sm text-gray-500">-</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-medium">
                {{ opty.target_close_date || '-' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-medium">
                {{ opty.customer_expected_rfs || '-' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-medium">
                {{ new Date(opty.created_at).toLocaleString('id-ID', { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit' }) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-bold rounded-md" 
                  :class="opty.is_converted_from_lead ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'">
                  {{ opty.is_converted_from_lead ? 'Yes' : 'No' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right flex justify-end gap-2">
                <button v-if="opty.stage === 'Closed Won'" @click="generateSia(opty)" :disabled="isGeneratingSia === opty.id" class="text-xs font-bold text-green-600 bg-green-50 hover:bg-green-100 px-3 py-1.5 rounded-lg transition-colors border border-green-200 shadow-sm inline-flex items-center gap-1 disabled:opacity-50">
                  <span v-if="isGeneratingSia === opty.id" class="w-3 h-3 border-2 border-green-600/30 border-t-green-600 rounded-full animate-spin"></span>
                  <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                  Generate SIA
                </button>
                <button @click="openEditModal(opty)" class="text-xs font-bold text-blue-600 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition-colors border border-blue-200 shadow-sm inline-flex items-center">
                  Edit
                </button>
                <router-link :to="`/optys/${opty.id}`" class="text-xs font-bold text-gray-600 bg-gray-50 hover:bg-gray-100 px-3 py-1.5 rounded-lg transition-colors border border-gray-200 shadow-sm inline-flex items-center gap-1">
                  View Details
                </router-link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal Form: Add Opty -->
    <transition name="fade">
      <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/40 backdrop-blur-sm p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-4xl overflow-hidden transform transition-all animate-modal-in border border-gray-100 my-8">
          <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center sticky top-0 z-10">
            <h3 class="text-lg font-bold text-gray-900">{{ editingId ? 'Edit Opportunity' : 'Add New Opportunity' }}</h3>
            <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
          </div>
          <form @submit.prevent="saveOpty" class="p-6 space-y-6">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-5">
              
              <!-- Column 1 -->
              <div class="space-y-5">
                <div>
                  <label class="block text-xs font-bold text-gray-600 mb-1.5">Opportunity Number</label>
                  <input type="text" disabled placeholder="(Auto generated POT...)" class="w-full bg-gray-50 border border-gray-200 text-gray-400 font-mono rounded-lg px-3 py-2.5 text-sm shadow-sm cursor-not-allowed" />
                </div>
                
                <div>
                  <label class="block text-xs font-bold text-gray-600 mb-1.5">Opportunity Name <span class="text-red-500">*</span></label>
                  <input type="text" v-model="newOpty.name" required class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all shadow-sm" placeholder="e.g. PSB ME MDP PLG 2Gbps" />
                </div>

                <div>
                  <label class="block text-xs font-bold text-gray-600 mb-1.5">Assigned To (Sales) <span class="text-red-500">*</span></label>
                  <div class="flex">
                    <input type="text" :value="selectedAssigneeName" readonly required placeholder="Select sales..." class="flex-grow bg-white border border-gray-200 border-r-0 rounded-l-lg px-3 py-2.5 text-sm outline-none shadow-sm cursor-not-allowed" />
                    <button type="button" @click="openSelectModal('assignee')" class="bg-gray-100 border border-gray-200 rounded-r-lg px-4 hover:bg-gray-200 transition-colors shadow-sm flex items-center justify-center">
                      <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </button>
                  </div>
                </div>

                <div>
                  <label class="block text-xs font-bold text-gray-600 mb-1.5">Opportunity Owner <span class="text-red-500">*</span></label>
                  <div class="flex">
                    <input type="text" :value="selectedOwnerName" readonly required placeholder="Select owner..." class="flex-grow bg-white border border-gray-200 border-r-0 rounded-l-lg px-3 py-2.5 text-sm outline-none shadow-sm cursor-not-allowed" />
                    <button type="button" @click="openSelectModal('owner')" class="bg-gray-100 border border-gray-200 rounded-r-lg px-4 hover:bg-gray-200 transition-colors shadow-sm flex items-center justify-center">
                      <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </button>
                  </div>
                </div>

                <div>
                  <label class="block text-xs font-bold text-gray-600 mb-1.5">Account Name (Customer) <span class="text-red-500">*</span></label>
                  <div class="flex">
                    <input type="text" :value="selectedCustomerName" readonly required placeholder="Select customer..." class="flex-grow bg-white border border-gray-200 border-r-0 rounded-l-lg px-3 py-2.5 text-sm outline-none shadow-sm cursor-not-allowed" />
                    <button type="button" @click="openSelectModal('customer')" class="bg-gray-100 border border-gray-200 rounded-r-lg px-4 hover:bg-gray-200 transition-colors shadow-sm flex items-center justify-center">
                      <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </button>
                  </div>
                </div>

                <div>
                  <label class="block text-xs font-bold text-gray-600 mb-1.5">Request Type</label>
                  <select v-model="newOpty.request_type" class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all shadow-sm">
                    <option value="New Installation">New Installation</option>
                    <option value="Addon">Addon</option>
                    <option value="Change Service">Change Service</option>
                    <option value="Change Plan">Change Plan</option>
                    <option value="Relocation">Relocation</option>
                  </select>
                </div>
              </div>

              <!-- Column 2 -->
              <div class="space-y-5">
                <div class="grid grid-cols-2 gap-3">
                  <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5 flex items-center gap-1">Target Close Date <span class="text-red-500">*</span></label>
                    <div class="relative">
                      <input type="date" v-model="newOpty.target_close_date" required class="w-full bg-white border border-gray-200 rounded-lg pl-3 pr-10 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all shadow-sm" />
                      <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                      </div>
                    </div>
                  </div>
                  <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5 flex items-center gap-1">Customer RFS Date <span class="text-red-500">*</span></label>
                    <div class="relative">
                      <input type="date" v-model="newOpty.customer_expected_rfs" required class="w-full bg-white border border-gray-200 rounded-lg pl-3 pr-10 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all shadow-sm" />
                      <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                      </div>
                    </div>
                  </div>
                </div>

                <div>
                  <label class="block text-xs font-bold text-gray-600 mb-1.5">Stage</label>
                  <select v-model="newOpty.stage" class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all shadow-sm">
                    <option value="Prospect and Analysis">Prospect and Analysis</option>
                    <option value="Value Proposition">Value Proposition</option>
                    <option value="Perception Analysis">Perception Analysis</option>
                    <option value="Proposal or Quote">Proposal or Quote</option>
                    <option value="Negotiation or Review">Negotiation or Review</option>
                    <option value="Closed Won">Closed Won</option>
                    <option value="Closed Lost">Closed Lost</option>
                  </select>
                </div>

                <div>
                  <label class="block text-xs font-bold text-gray-600 mb-1.5">Confidence Level</label>
                  <select v-model="newOpty.confidence_level" class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all shadow-sm">
                    <option value="PipeLine">PipeLine</option>
                    <option value="Base Case">Base Case</option>
                    <option value="Commit">Commit</option>
                  </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                  <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5">Estimated Value (OTC)</label>
                    <div class="relative">
                      <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-500 font-semibold text-sm">Rp</div>
                      <input type="number" v-model="newOpty.estimated_value_otc" class="w-full bg-white border border-gray-200 rounded-lg pl-10 pr-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all shadow-sm" placeholder="0" />
                    </div>
                  </div>
                  <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5">Estimated Value (MRC)</label>
                    <div class="relative">
                      <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-500 font-semibold text-sm">Rp</div>
                      <input type="number" v-model="newOpty.estimated_value_mrc" class="w-full bg-white border border-gray-200 rounded-lg pl-10 pr-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all shadow-sm" placeholder="0" />
                    </div>
                  </div>
                </div>

                <div>
                  <label class="block text-xs font-bold text-gray-600 mb-1.5">Is Converted From Lead</label>
                  <select v-model="newOpty.is_converted_from_lead" class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all shadow-sm">
                    <option :value="false">No</option>
                    <option :value="true">Yes</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="pt-6 flex justify-end gap-3 border-t border-gray-100 mt-8">
              <button type="button" @click="showModal = false" class="px-5 py-2.5 rounded-xl font-bold text-gray-600 hover:bg-gray-50 transition-colors text-sm border border-transparent">Cancel</button>
              <button type="submit" :disabled="isSaving" class="px-6 py-2.5 rounded-xl font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-md transition-all transform hover:-translate-y-0.5 text-sm flex items-center gap-2">
                <span v-if="isSaving" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                {{ editingId ? 'Update Opportunity' : 'Save Opportunity' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </transition>

    <!-- Generic Search/Select Modal -->
    <transition name="fade">
      <div v-if="showSelectModal" class="fixed inset-0 z-[60] flex items-center justify-center bg-gray-900/60 backdrop-blur-sm p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl overflow-hidden transform transition-all animate-modal-in flex flex-col max-h-[80vh]">
          <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center shrink-0">
            <h3 class="text-lg font-bold text-gray-900 capitalize">Select {{ selectModalType === 'assignee' ? 'Sales Agent' : selectModalType }}</h3>
            <button @click="showSelectModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
          </div>
          
          <div class="p-4 border-b border-gray-100 shrink-0 bg-white">
            <div class="relative">
              <span class="absolute left-3 top-2.5 text-gray-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
              </span>
              <input type="text" v-model="modalSearchQuery" placeholder="Search..." class="w-full bg-gray-50 border border-gray-200 rounded-lg pl-10 pr-3 py-2 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all" />
            </div>
          </div>

          <div class="overflow-y-auto flex-grow p-0">
            <table class="min-w-full divide-y divide-gray-100">
              <thead class="bg-gray-50 sticky top-0 shadow-sm">
                <tr v-if="selectModalType === 'customer'">
                  <th class="py-3 px-6 text-left text-xs font-bold text-gray-500 uppercase">Customer Name</th>
                  <th class="py-3 px-6 text-left text-xs font-bold text-gray-500 uppercase">Email</th>
                  <th class="py-3 px-6 text-center text-xs font-bold text-gray-500 uppercase">Action</th>
                </tr>
                <tr v-else>
                  <th class="py-3 px-6 text-left text-xs font-bold text-gray-500 uppercase">Name</th>
                  <th class="py-3 px-6 text-left text-xs font-bold text-gray-500 uppercase">Email</th>
                  <th class="py-3 px-6 text-left text-xs font-bold text-gray-500 uppercase">Role</th>
                  <th class="py-3 px-6 text-center text-xs font-bold text-gray-500 uppercase">Action</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100 bg-white">
                <template v-if="filteredModalList.length > 0">
                  <tr v-for="item in filteredModalList" :key="item.id" class="hover:bg-blue-50/50 transition-colors">
                    <template v-if="selectModalType === 'customer'">
                      <td class="py-3 px-6 text-sm font-semibold text-gray-800">{{ item.customer_name }}</td>
                      <td class="py-3 px-6 text-sm text-gray-600">{{ item.email || '-' }}</td>
                    </template>
                    <template v-else>
                      <td class="py-3 px-6 text-sm font-semibold text-gray-800">{{ item.first_name }} {{ item.last_name }}</td>
                      <td class="py-3 px-6 text-sm text-gray-600">{{ item.email }}</td>
                      <td class="py-3 px-6 text-sm text-gray-500">{{ item.role }}</td>
                    </template>
                    <td class="py-3 px-6 text-center">
                      <button @click="selectItem(item)" class="bg-green-500 hover:bg-green-600 text-white px-4 py-1.5 rounded-lg text-xs font-bold shadow-sm transition-colors">Select</button>
                    </td>
                  </tr>
                </template>
                <tr v-else>
                  <td :colspan="selectModalType === 'customer' ? 3 : 4" class="py-8 text-center text-gray-500 font-medium">No records found.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </transition>

    <ImportModal 
      :show="showImportModal"
      moduleName="Opty"
      :columns="['Opportunity Number', 'Name', 'Target Close Date', 'Stage', 'Confidence Level', 'Estimated Value OTC', 'Estimated Value MRC', 'Customer ID', 'Assigned To (User ID)', 'Owner ID', 'Product ID', 'Request Type']"
      :requiredColumns="['Name', 'Target Close Date']"
      :sampleRow="['OPTY-2026001', 'Q3 Software Renewal', '2026-09-30', 'Discovery', 'Medium', '15000000', '2500000', '12', '3', '1', '5', 'New Installation']"
      apiEndpoint="/optys/bulk"
      @close="showImportModal = false"
      @import-success="onImportSuccess"
      @import-error="onImportError"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import api from '../api/axios'
import ImportModal from '../components/ImportModal.vue';
import { useAuthStore } from '../stores/auth';

const authStore = useAuthStore();
const optys = ref([]);
const customers = ref([]);
const users = ref([])
const showImportModal = ref(false)

const onImportSuccess = (msg) => {
  showNotification(msg, 'success')
  fetchData()
}

const onImportError = (msg) => {
  showNotification(msg, 'error')
}

const formatCurrency = (val) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(val || 0);
};

const isLoading = ref(true);
const showModal = ref(false);
const isSaving = ref(false);
const isGeneratingSia = ref(null);
const notification = ref(null);
const editingId = ref(null);

const newOpty = ref({
  name: '',
  assigned_to: null,
  owner_id: null,
  customer_id: null,
  target_close_date: '',
  customer_expected_rfs: '',
  request_type: 'New Installation',
  stage: 'Prospect and Analysis',
  confidence_level: 'PipeLine',
  estimated_value_otc: null,
  estimated_value_mrc: null,
  is_converted_from_lead: false
});

const showNotification = (message, type = 'success') => {
  notification.value = { message, type };
  setTimeout(() => { notification.value = null }, 3000);
};

// Search Modal State
const showSelectModal = ref(false);
const selectModalType = ref(''); 
const modalSearchQuery = ref('');

const openSelectModal = (type) => {
  selectModalType.value = type;
  modalSearchQuery.value = '';
  showSelectModal.value = true;
};

const filteredModalList = computed(() => {
  const query = modalSearchQuery.value.toLowerCase();
  if (selectModalType.value === 'customer') {
    return customers.value.filter(c => 
      c.customer_name?.toLowerCase().includes(query) || 
      c.nomor_customer?.toLowerCase().includes(query)
    );
  } else {
    return users.value.filter(u => 
      (u.first_name + ' ' + (u.last_name||'')).toLowerCase().includes(query) ||
      u.email?.toLowerCase().includes(query)
    );
  }
});

const selectItem = (item) => {
  if (selectModalType.value === 'customer') {
    newOpty.value.customer_id = item.id;
  } else if (selectModalType.value === 'owner') {
    newOpty.value.owner_id = item.id;
  } else if (selectModalType.value === 'assignee') {
    newOpty.value.assigned_to = item.id;
  }
  showSelectModal.value = false;
};

const selectedCustomerName = computed(() => {
  const c = customers.value.find(c => c.id === newOpty.value.customer_id);
  return c ? c.customer_name : '';
});

const selectedOwnerName = computed(() => {
  const u = users.value.find(u => u.id === newOpty.value.owner_id);
  return u ? `${u.first_name} ${u.last_name || ''}`.trim() : '';
});

const selectedAssigneeName = computed(() => {
  const u = users.value.find(u => u.id === newOpty.value.assigned_to);
  return u ? `${u.first_name} ${u.last_name || ''}`.trim() : '';
});

const fetchData = async () => {
  try {
    const [optysRes, custRes, usersRes] = await Promise.all([
      api.get('/optys'),
      api.get('/customers'),
      api.get('/users')
    ]);
    optys.value = optysRes.data;
    customers.value = custRes.data;
    users.value = usersRes.data;
    isLoading.value = false;
  } catch (error) {
    console.error('Error fetching data:', error);
    showNotification('Failed to fetch data', 'error');
  }
};

const openAddModal = () => {
  editingId.value = null;
  newOpty.value = {
    name: '',
    assigned_to: authStore.user?.id || null,
    owner_id: authStore.user?.id || null,
    customer_id: null,
    target_close_date: '',
    customer_expected_rfs: '',
    request_type: 'New Installation',
    stage: 'Prospect and Analysis',
    confidence_level: 'PipeLine',
    estimated_value_otc: null,
    estimated_value_mrc: null,
    is_converted_from_lead: false
  };
  showModal.value = true;
};

const openEditModal = (opty) => {
  editingId.value = opty.id;
  newOpty.value = {
    name: opty.name,
    assigned_to: opty.assigned_to,
    owner_id: opty.owner_id,
    customer_id: opty.customer_id,
    target_close_date: opty.target_close_date || '',
    customer_expected_rfs: opty.customer_expected_rfs || '',
    request_type: opty.request_type,
    stage: opty.stage,
    confidence_level: opty.confidence_level,
    estimated_value_otc: opty.estimated_value_otc,
    estimated_value_mrc: opty.estimated_value_mrc,
    is_converted_from_lead: opty.is_converted_from_lead
  };
  showModal.value = true;
};

const saveOpty = async () => {
  if (!newOpty.value.customer_id || !newOpty.value.owner_id || !newOpty.value.assigned_to || !newOpty.value.target_close_date || !newOpty.value.customer_expected_rfs) {
    showNotification('Please fill all required fields.', 'error');
    return;
  }

  try {
    isSaving.value = true;
    if (editingId.value) {
      await api.put(`/optys/${editingId.value}`, newOpty.value);
      showNotification('Opportunity updated successfully!');
    } else {
      await api.post('/optys', newOpty.value);
      showNotification('Opportunity added successfully!');
    }
    showModal.value = false;
    fetchData();
  } catch (error) {
    console.error('Error saving opty:', error);
    showNotification(error.response?.data?.message || 'Failed to save opty', 'error');
  } finally {
    isSaving.value = false;
  }
};

const generateSia = async (opty) => {
  if (!opty.customer_id) {
    showNotification('Opty must have a customer to generate SIA.', 'error');
    return;
  }
  
  if (!confirm(`Are you sure you want to generate a Service Instance Account for Opty #${opty.opportunity_number}?`)) {
    return;
  }

  try {
    isGeneratingSia.value = opty.id;
    await api.post('/service-instance-accounts', {
      opty_id: opty.id,
      customer_id: String(opty.customer_id).padStart(3, '0'),
      company_name: opty.customer?.customer_name || 'Unknown Company'
    });
    showNotification('Service Instance Account Generated successfully!');
  } catch (error) {
    console.error('Error generating SIA:', error);
    showNotification(error.response?.data?.message || 'Failed to generate SIA', 'error');
  } finally {
    isGeneratingSia.value = null;
  }
};

onMounted(() => {
  fetchData();
});
</script>

<style scoped>
.animate-fade-in { animation: fadeIn 0.4s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
.animate-modal-in { animation: modalScale 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
@keyframes modalScale { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
.slide-down-enter-active, .slide-down-leave-active { transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55); }
.slide-down-enter-from, .slide-down-leave-to { opacity: 0; transform: translateY(-20px); }
.loader-spinner { width: 30px; height: 30px; border: 3px solid #f3f3f3; border-top: 3px solid #2563eb; border-radius: 50%; animation: spin 0.8s linear infinite; }
@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
input[type="date"]::-webkit-calendar-picker-indicator { cursor: pointer; opacity: 0; position: absolute; right: 0; top: 0; width: 100%; height: 100%; }
</style>
