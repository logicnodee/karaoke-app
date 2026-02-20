@extends('layouts.dashboard')

@section('dashboard-role', 'Admin Panel')
@section('dashboard-role-icon', 'shield-check')

@section('title', 'Membership - Admin Dashboard')
@section('page-title', 'Membership Customer')

@section('sidebar-nav')
    @include('dashboard.admin._sidebar', ['active' => 'membership'])
@endsection

@section('dashboard-content')
    <div x-data="{
        members: {{ json_encode($members) }},
        showAddModal: false,
        showDeleteModal: false,
        isEditing: false,
        editIndex: null,
        deleteIndex: null,
        newMember: { nama: '', nomor: '', domisili: '', tier: 'Bronze', poin: 0 },
        showToast: false,
        toastMessage: '',
        toastType: 'success',

        showNotification(message, type = 'success') {
            this.toastMessage = message;
            this.toastType = type;
            this.showToast = true;
            setTimeout(() => this.showToast = false, 3000);
        },

        openAddModal() {
            this.isEditing = false;
            this.newMember = { nama: '', nomor: '', domisili: '', tier: 'Bronze', poin: 0 };
            this.showAddModal = true;
        },

        editMember(index) {
            this.isEditing = true;
            this.editIndex = index;
            this.newMember = { ...this.members[index] };
            this.showAddModal = true;
        },

        saveMember() {
            if(!this.newMember.nama || !this.newMember.nomor) return;
            
            if (this.isEditing) {
                this.members[this.editIndex] = { ...this.newMember };
                this.showNotification('Data member berhasil diperbarui', 'success');
            } else {
                this.members.push({
                    nama: this.newMember.nama,
                    nomor: this.newMember.nomor,
                    domisili: this.newMember.domisili || '-',
                    tier: this.newMember.tier,
                    poin: 0
                });
                this.showNotification('Member baru berhasil ditambahkan', 'success');
            }

            this.showAddModal = false;
            this.newMember = { nama: '', nomor: '', domisili: '', tier: 'Bronze', poin: 0 };
        },

        deleteMember(index) {
            this.deleteIndex = index;
            this.showDeleteModal = true;
        },

        confirmDelete() {
            if (this.deleteIndex !== null) {
                this.members.splice(this.deleteIndex, 1);
                this.showNotification('Member berhasil dihapus', 'success');
                this.showDeleteModal = false;
                this.deleteIndex = null;
            }
        },

        init() {
            this.$nextTick(() => { if(window.lucide) window.lucide.createIcons(); });
            this.$watch('showAddModal', (value) => {
                setTimeout(() => { if(window.lucide) window.lucide.createIcons(); }, 100);
            });
             this.$watch('members', () => {
                this.$nextTick(() => { if(window.lucide) window.lucide.createIcons(); });
            });
        }
    }">
        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <h2 class="text-lg font-semibold text-white" style="font-family: 'Inter';">Daftar Member</h2>
                <p class="text-sm text-gray-500 mt-1" style="font-family: 'Inter';">Data pelanggan membership dan poin loyalty</p>
            </div>
            <button @click="openAddModal()" class="text-xs bg-[#D0B75B] text-black font-semibold px-4 py-2.5 rounded-lg hover:bg-[#e0c86b] transition-colors flex items-center gap-2 self-start" style="font-family: 'Inter';">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Tambah Member
            </button>
        </div>

        {{-- Member Table --}}
        <div class="bg-[#080808] border border-white/5 rounded-2xl overflow-hidden mb-8">
            <div class="overflow-x-auto">
                <table class="w-full text-xs" style="font-family: 'Inter';">
                    <thead>
                        <tr class="text-gray-600 text-[9px] font-black uppercase tracking-[0.25em] bg-black/40">
                            <th class="text-left px-5 py-3">Nama Lengkap</th>
                            <th class="text-left px-5 py-3">Nomor Telepon</th>
                            <th class="text-left px-5 py-3">Domisili</th>
                            <th class="text-center px-5 py-3">Tier</th>
                            <th class="text-center px-5 py-3">Poin</th>
                            <th class="text-center px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <template x-for="(member, index) in members" :key="index">
                            <tr class="hover:bg-white/[0.01] transition-colors">
                                <td class="px-5 py-3">
                                    <span class="text-white font-bold" x-text="member.nama"></span>
                                </td>
                                <td class="px-5 py-3 text-gray-400 font-medium font-mono" x-text="member.nomor"></td>
                                <td class="px-5 py-3 text-gray-400 font-medium" x-text="member.domisili"></td>
                                <td class="px-5 py-3 text-center">
                                    <span class="text-[9px] px-3 py-1 rounded-full font-black uppercase tracking-widest border"
                                          :class="{
                                              'bg-[#D0B75B]/10 text-[#D0B75B] border-[#D0B75B]/20': member.tier === 'Gold',
                                              'bg-gray-300/10 text-gray-300 border-gray-300/20': member.tier === 'Silver',
                                              'bg-amber-700/10 text-amber-700 border-amber-700/20': member.tier === 'Bronze',
                                              'bg-cyan-400/10 text-cyan-400 border-cyan-400/20': member.tier === 'Platinum'
                                          }" x-text="member.tier">
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <span class="text-[#D0B75B] font-bold" x-text="member.poin + ' Poin'"></span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button @click="editMember(index)" class="w-8 h-8 rounded-lg bg-white/5 text-gray-400 hover:bg-[#D0B75B] hover:text-black transition-all flex items-center justify-center" title="Edit">
                                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                                        </button>
                                        <button @click="deleteMember(index)" class="w-8 h-8 rounded-lg bg-white/5 text-gray-400 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center" title="Hapus">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Add/Edit Member Modal --}}
        <template x-teleport="body">
            <div x-show="showAddModal" style="display: none;" 
                 class="fixed inset-0 z-[99] flex items-center justify-center bg-black/80 backdrop-blur-xl p-4" x-transition.opacity>
                <div @click.away="showAddModal = false" class="bg-[#0A0A0A] border border-white/10 rounded-2xl w-full max-w-lg overflow-hidden relative">
                     <div class="px-6 py-4 border-b border-white/5 bg-zinc-900/20 flex justify-between items-center">
                        <h3 class="text-white font-bold" x-text="isEditing ? 'Edit Member' : 'Tambah Member Baru'"></h3>
                        <button @click="showAddModal = false"><i data-lucide="x" class="w-5 h-5 text-gray-500 hover:text-white transition-colors"></i></button>
                     </div>
                     <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-xs uppercase font-bold text-gray-500 mb-2">Nama Lengkap</label>
                            <input type="text" x-model="newMember.nama" class="w-full bg-zinc-900 border border-white/10 rounded-lg px-4 py-2 text-white focus:border-[#D0B75B] outline-none" placeholder="Contoh: Bpk. Ahmad">
                        </div>
                        <div>
                            <label class="block text-xs uppercase font-bold text-gray-500 mb-2">Nomor Telepon</label>
                            <input type="text" x-model="newMember.nomor" class="w-full bg-zinc-900 border border-white/10 rounded-lg px-4 py-2 text-white focus:border-[#D0B75B] outline-none" placeholder="Contoh: 081234567890">
                        </div>
                        <div>
                            <label class="block text-xs uppercase font-bold text-gray-500 mb-2">Domisili</label>
                            <input type="text" x-model="newMember.domisili" class="w-full bg-zinc-900 border border-white/10 rounded-lg px-4 py-2 text-white focus:border-[#D0B75B] outline-none" placeholder="Contoh: Jakarta Selatan">
                        </div>
                         <div>
                            <label class="block text-xs uppercase font-bold text-gray-500 mb-2">Tier Membership</label>
                            <div class="relative">
                                <select x-model="newMember.tier" class="w-full bg-zinc-900 border border-white/10 rounded-lg px-4 py-2 text-white focus:border-[#D0B75B] outline-none appearance-none cursor-pointer">
                                    <option value="Bronze">Bronze</option>
                                    <option value="Silver">Silver</option>
                                    <option value="Gold">Gold</option>
                                    <option value="Platinum">Platinum</option>
                                </select>
                                <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                            </div>
                        </div>
                        
                        {{-- Only show points when editing --}}
                        <template x-if="isEditing">
                            <div>
                                <label class="block text-xs uppercase font-bold text-gray-500 mb-2">Poin Loyalty</label>
                                <input type="number" x-model="newMember.poin" class="w-full bg-zinc-900 border border-white/10 rounded-lg px-4 py-2 text-white focus:border-[#D0B75B] outline-none">
                            </div>
                        </template>

                        <div class="pt-4">
                             <button @click="saveMember()" class="w-full bg-[#D0B75B] hover:bg-[#e0c86b] text-black font-bold uppercase tracking-widest py-3 rounded-xl transition-all" x-text="isEditing ? 'Simpan Perubahan' : 'Simpan Member'">
                            </button>
                        </div>
                     </div>
                </div>
            </div>
        </template>
        
        {{-- Delete Confirmation Modal --}}
        <template x-teleport="body">
            <div x-show="showDeleteModal" style="display: none;" 
                 class="fixed inset-0 z-[99] flex items-center justify-center bg-black/80 backdrop-blur-xl p-4" x-transition.opacity>
                <div @click.away="showDeleteModal = false" class="bg-[#0A0A0A] border border-white/10 rounded-2xl w-full max-w-sm overflow-hidden relative text-center">
                     <div class="p-8">
                        <div class="w-16 h-16 rounded-full bg-red-500/10 flex items-center justify-center mx-auto mb-4 border border-red-500/20">
                            <i data-lucide="trash-2" class="w-8 h-8 text-red-500"></i>
                        </div>
                        <h3 class="text-white font-bold text-lg mb-2">Hapus Member?</h3>
                        <p class="text-gray-400 text-sm mb-6">Apakah Anda yakin ingin menghapus data member ini? Tindakan ini tidak dapat dibatalkan.</p>
                        
                        <div class="flex gap-3">
                            <button @click="showDeleteModal = false" class="flex-1 bg-white/5 hover:bg-white/10 text-white font-bold py-3 rounded-xl transition-all uppercase tracking-widest text-xs">
                                Batal
                            </button>
                            <button @click="confirmDelete()" class="flex-1 bg-red-500 hover:bg-red-600 text-white font-bold py-3 rounded-xl transition-all uppercase tracking-widest text-xs shadow-lg shadow-red-500/20">
                                Ya, Hapus
                            </button>
                        </div>
                     </div>
                </div>
            </div>
        </template>

        {{-- Toast Notification --}}
        <template x-teleport="body">
            <div x-show="showToast" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-2"
                 class="fixed top-4 right-4 z-[100] flex items-center gap-3 px-4 py-3 rounded-xl border shadow-2xl min-w-[300px] bg-[#0A0A0A] border-green-500/20 text-green-500">
                <div class="p-2 rounded-full bg-green-500/10">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                </div>
                <div>
                    <h4 class="text-sm font-bold uppercase tracking-wider">Berhasil</h4>
                    <p class="text-xs text-gray-400 font-medium mt-0.5" x-text="toastMessage"></p>
                </div>
            </div>
        </template>
    </div>
@endsection
