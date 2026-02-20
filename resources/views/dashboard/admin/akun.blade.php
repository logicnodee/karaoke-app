@extends('layouts.dashboard')

@section('dashboard-role', 'Admin Panel')
@section('dashboard-role-icon', 'shield-check')

@section('title', 'Manajemen Akun - Admin Dashboard')
@section('page-title', 'Manajemen Akun')

@section('sidebar-nav')
    @include('dashboard.admin._sidebar', ['active' => 'akun'])
@endsection

@section('dashboard-content')
    <div x-data="{
        users: {{ json_encode($daftarUser) }},
        showAddModal: false,
        newUser: { nama: '', email: '', password: '', ip_address: '', role: '', features: [] },
        availableFeatures: [
            'Ringkasan', 'Manajemen Ruangan', 'Manajemen Lagu', 'Manajemen Kategori',
            'Food & Beverages', 'Keuangan', 'Riwayat Billing', 'Manajemen Akun',
            'Log Aktivitas', 'Manajemen Operator', 'Manajemen Absensi', 'Membership',
            'Buat Pesanan', 'Reservasi Room', 'Pesanan Aktif', 'Panggilan Room', 'Laporan'
        ],
        isEditing: false,
        editIndex: null,
        
        // Feature View State
        showFeaturesModal: false,
        viewingUserFeatures: [],
        viewingUserName: '',
        viewingUserIndex: null,
        
        // Remove Feature Confirmation State
        showRemoveFeatureModal: false,
        featureToRemove: '',

        // Password Visibility State
        showPasswordIndex: null,

        // Attendance Modal State
        showAttendanceModal: false,
        attendanceData: [],
        attendanceUser: null,

        viewAttendance(user) {
            this.attendanceUser = user;
            
            // Mock Data Generation
            const dates = [
                '17 Feb 2026', '16 Feb 2026', '15 Feb 2026', '14 Feb 2026', '13 Feb 2026'
            ];
            
            this.attendanceData = dates.map((date, i) => {
                // Determine if IP matches (mostly valid for demo)
                // Randomly make one invalid for demo purposes if index is 3
                const isIpValid = i === 3 ? false : true; 
                const usedIp = isIpValid ? (user.ip_address || '192.168.1.xxx') : '192.168.1.99';
                
                // Random times
                const startHour = 8 + Math.floor(Math.random() * 2);
                const endHour = 16 + Math.floor(Math.random() * 4);
                const startMin = Math.floor(Math.random() * 60).toString().padStart(2, '0');
                const endMin = Math.floor(Math.random() * 60).toString().padStart(2, '0');
                
                return {
                    date: date,
                    checkIn: `${startHour}:${startMin}`,
                    checkOut: `${endHour}:${endMin}`,
                    ip: usedIp,
                    isValidIp: user.ip_address ? (usedIp === user.ip_address) : true, // If no IP assigned, assume valid or handle differently
                    duration: `${endHour - startHour} Jam ${Math.abs(endMin - startMin)} Menit`
                };
            });

            this.showAttendanceModal = true;
        },

        viewFeaturesList(user, index) {
            this.viewingUserFeatures = [...(user.features || [])];
            this.viewingUserName = user.nama;
            this.viewingUserIndex = index;
            this.showFeaturesModal = true;
        },

        initiateRemoveFeature(feature) {
            this.featureToRemove = feature;
            this.showRemoveFeatureModal = true;
        },

        confirmRemoveFeature() {
             // Remove from local view
             this.viewingUserFeatures = this.viewingUserFeatures.filter(f => f !== this.featureToRemove);
             
             // Sync update to main users array
             if (this.viewingUserIndex !== null) {
                 this.users[this.viewingUserIndex].features = [...this.viewingUserFeatures];
                 this.showNotification('Akses fitur dihapus', 'success');
             }
             this.showRemoveFeatureModal = false;
             this.featureToRemove = '';
        },
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
            this.newUser = { nama: '', email: '', password: '', ip_address: '', role: '', features: [] };
            this.showAddModal = true;
        },

        // Delete Notification State
        showDeleteModal: false,
        deleteIndex: null,

        editUser(index) {
            this.isEditing = true;
            this.editIndex = index;
            // Clone object to avoid direct mutation before save
            this.newUser = { ...this.users[index] }; 
            this.showAddModal = true;
        },

        deleteUser(index) {
           this.deleteIndex = index;
           this.showDeleteModal = true;
        },

        confirmDelete() {
            if (this.deleteIndex !== null) {
                this.users.splice(this.deleteIndex, 1);
                this.showNotification('Akun berhasil dihapus', 'success');
                this.showDeleteModal = false;
                this.deleteIndex = null;
            }
        },

        saveUser() {
            if(!this.newUser.nama || !this.newUser.email) return;
            
            if (this.isEditing) {
                // Update existing
                this.users[this.editIndex] = { 
                    ...this.users[this.editIndex], 
                    ...this.newUser 
                };
                this.showNotification('Data akun berhasil diperbarui', 'success');
            } else {
                // Add new
                this.users.push({
                    nama: this.newUser.nama,
                    email: this.newUser.email,
                    password: this.newUser.password,
                    ip_address: this.newUser.ip_address,
                    role: this.newUser.role,
                    features: [...this.newUser.features],
                    last_login: 'Belum Login',
                    status: 'Aktif'
                });
                this.showNotification('Akun baru berhasil ditambahkan', 'success');
            }

            this.showAddModal = false;
            this.showAddModal = false;
            this.newUser = { nama: '', email: '', password: '', ip_address: '', role: '', features: [] };
        },
        toggleFeature(feature) {
             if (this.newUser.features.includes(feature)) {
                 this.newUser.features = this.newUser.features.filter(f => f !== feature);
             } else {
                 this.newUser.features.push(feature);
             }
        },

        toggleStatus(index) {
            const user = this.users[index];
            if (user.status === 'Aktif') {
                user.status = 'Nonaktif';
                this.showNotification(`Akun ${user.nama} dinonaktifkan`, 'error');
            } else {
                user.status = 'Aktif';
                this.showNotification(`Akun ${user.nama} diaktifkan kembali`, 'success');
            }
        },

        init() {
            this.$nextTick(() => { if(window.lucide) window.lucide.createIcons(); });
            this.$watch('users', () => {
                this.$nextTick(() => { if(window.lucide) window.lucide.createIcons(); });
            });
            this.$watch('showAddModal', (value) => {
                // Wait for transition
                setTimeout(() => {
                   if(window.lucide) window.lucide.createIcons();
                }, 100);
            });
        }
    }">
        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <h2 class="text-lg font-semibold text-white" style="font-family: 'Inter';">Daftar Pengguna Sistem</h2>
                <p class="text-sm text-gray-500 mt-1" style="font-family: 'Inter';">Kelola hak akses untuk Admin, Kasir, dan Operator</p>
            </div>
            <button @click="openAddModal()" class="text-xs bg-[#D0B75B] text-black font-semibold px-4 py-2.5 rounded-lg hover:bg-[#e0c86b] transition-colors flex items-center gap-2 self-start" style="font-family: 'Inter';">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Tambah Akun Baru
            </button>
        </div>

        {{-- User Table --}}
        <div class="bg-[#080808] border border-white/5 rounded-2xl overflow-hidden mb-8">
            <div class="overflow-x-auto">
                <table class="w-full text-xs" style="font-family: 'Inter';">
                    <thead>
                        <tr class="text-gray-600 text-[9px] font-black uppercase tracking-[0.25em] bg-black/40">
                            <th class="text-left px-5 py-3">Nama Lengkap</th>
                            <th class="text-left px-5 py-3">Email / Username</th>
                            <th class="text-left px-5 py-3">Password</th>
                            <th class="text-left px-5 py-3">IP Address</th>
                            <th class="text-left px-5 py-3">Peran (Role)</th>
                            <th class="text-left px-5 py-3">Hak Akses</th>
                            <th class="text-left px-5 py-3">Terakhir Login</th>
                            <th class="text-center px-5 py-3">Status</th>
                            <th class="text-center px-4 py-3 sticky right-0 z-20 bg-[#080808] border-l border-white/5 shadow-[-5px_0_10px_rgba(0,0,0,0.5)]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <template x-for="(user, index) in users" :key="index">
                            <tr class="group hover:bg-white/[0.01] transition-colors">
                                <td class="px-5 py-3">
                                    <span class="text-white font-bold" x-text="user.nama"></span>
                                </td>
                                <td class="px-5 py-3 text-gray-400 font-medium" x-text="user.email"></td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-gray-400 font-mono text-[11px]"
                                              x-text="showPasswordIndex === index ? (user.password || '-') : '••••••••'"></span>
                                        <template x-if="user.password">
                                            <button @click="showPasswordIndex = (showPasswordIndex === index ? null : index)" 
                                                    class="text-gray-600 hover:text-[#D0B75B] transition-colors p-0.5 rounded" 
                                                    :title="showPasswordIndex === index ? 'Sembunyikan' : 'Tampilkan'">
                                                <template x-if="showPasswordIndex !== index">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3.5 h-3.5">
                                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                        <circle cx="12" cy="12" r="3"></circle>
                                                    </svg>
                                                </template>
                                                <template x-if="showPasswordIndex === index">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3.5 h-3.5">
                                                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                                        <line x1="1" y1="1" x2="23" y2="23"></line>
                                                    </svg>
                                                </template>
                                            </button>
                                        </template>
                                    </div>
                                </td>
                                <td class="px-5 py-3">
                                    <template x-if="user.ip_address">
                                        <div class="flex items-center gap-1 opacity-70">
                                            <i data-lucide="globe" class="w-3 h-3 text-gray-500"></i>
                                            <span class="text-[10px] font-mono text-gray-300" x-text="user.ip_address"></span>
                                        </div>
                                    </template>
                                     <template x-if="!user.ip_address">
                                        <span class="text-[9px] text-gray-600 italic">-</span>
                                    </template>
                                </td>
                                <td class="px-5 py-3">
                                    <span class="text-[9px] font-black uppercase tracking-widest text-gray-500" 
                                          x-text="user.role">
                                    </span>
                                </td>
                                <td class="px-5 py-3">
                                    <div @click="viewFeaturesList(user, index)" class="flex flex-wrap gap-1 cursor-pointer hover:bg-white/5 p-1 -m-1 rounded-lg transition-colors group">
                                        <template x-if="user.features && user.features.length > 0">
                                            <div class="flex items-center gap-1">
                                                <template x-if="user.features.length === availableFeatures.length">
                                                    <span class="text-[10px] text-[#D0B75B] font-bold bg-[#D0B75B]/10 border border-[#D0B75B]/20 px-2 py-0.5 rounded-md group-hover:bg-[#D0B75B]/20 transition-colors">Semua Fitur</span>
                                                </template>
                                                <template x-if="user.features.length < availableFeatures.length">
                                                    <div class="flex gap-1">
                                                        {{-- Show up to 2 specific features --}}
                                                        <template x-for="(feature, idx) in user.features.slice(0, 2)" :key="idx">
                                                            <span class="text-[9px] text-gray-300 bg-white/5 border border-white/10 px-1.5 py-0.5 rounded group-hover:bg-white/10 transition-colors" x-text="feature === 'Pemesanan' ? 'Reservasi Room' : feature"></span>
                                                        </template>
                                                        {{-- Show count for remaining --}}
                                                        <template x-if="user.features.length > 2">
                                                            <span class="text-[9px] text-gray-500 bg-white/5 px-1.5 py-0.5 rounded group-hover:bg-white/10 transition-colors" x-text="'+' + (user.features.length - 2) + ' Lainnya'"></span>
                                                        </template>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                        <template x-if="!user.features || user.features.length === 0">
                                            <span class="text-[10px] text-gray-600 italic">Tidak ada akses</span>
                                        </template>
                                    </div>
                                </td>
                                <td class="px-5 py-3 text-gray-500 text-[10px] uppercase font-bold tracking-wider" x-text="user.last_login"></td>
                                <td class="px-5 py-3 text-center">
                                    <button @click="toggleStatus(index)" class="group relative inline-flex items-center justify-center gap-1.5 px-3 py-1 rounded-full transition-all duration-300 w-24 border"
                                            :class="user.status === 'Aktif' ? 'bg-green-500/10 hover:bg-green-500/20 border-green-500/20' : 'bg-red-500/10 hover:bg-red-500/20 border-red-500/20'">
                                        
                                        <span class="text-[9px] font-black uppercase tracking-widest transition-colors duration-300"
                                              :class="user.status === 'Aktif' ? 'text-green-400' : 'text-red-400'"
                                              x-text="user.status === 'Aktif' ? 'Aktif' : 'Nonaktif'"></span>
                                        
                                        {{-- Hover Tooltip --}}
                                        <div class="absolute bottom-full mb-2 left-1/2 -translate-x-1/2 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-10">
                                            <span class="text-[9px] bg-black border border-white/10 text-white px-2 py-1 rounded shadow-xl"
                                                  x-text="user.status === 'Aktif' ? 'Nonaktifkan' : 'Aktifkan'"></span>
                                        </div>
                                    </button>
                                </td>
                                <td class="px-4 py-3 text-center sticky right-0 z-10 bg-[#080808] border-l border-white/5 shadow-[-5px_0_10px_rgba(0,0,0,0.5)] group-hover:bg-[#0A0A0A] transition-colors">
                                    <div class="flex items-center justify-center gap-2">
                                        <button @click="editUser(index)" class="w-8 h-8 rounded-lg bg-white/5 text-gray-400 hover:bg-[#D0B75B] hover:text-black transition-all flex items-center justify-center" title="Edit">
                                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                                        </button>

                                        <button @click="deleteUser(index)" class="w-8 h-8 rounded-lg bg-white/5 text-gray-400 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center" title="Hapus">
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

        {{-- Add User Modal --}}
        <template x-teleport="body">
            <div x-show="showAddModal" style="display: none;" 
                 class="fixed inset-0 z-[99] flex items-center justify-center bg-black/80 backdrop-blur-xl p-4" x-transition.opacity>
                <div @click.away="showAddModal = false" class="bg-[#0A0A0A] border border-white/10 rounded-2xl w-full max-w-2xl overflow-hidden relative">
                     <div class="px-6 py-4 border-b border-white/5 bg-zinc-900/20 flex justify-between items-center">
                        <h3 class="text-white font-bold" x-text="isEditing ? 'Edit Akun Pengguna' : 'Tambah Akun Baru'"></h3>
                        <button @click="showAddModal = false"><i data-lucide="x" class="w-5 h-5 text-gray-500 hover:text-white transition-colors"></i></button>
                     </div>
                     <div class="p-6">
                        <div class="flex flex-col md:flex-row gap-6">
                            {{-- Left Column: User Details --}}
                            <div class="flex-1 space-y-4">
                                <div>
                                    <label class="block text-xs uppercase font-bold text-gray-500 mb-2">Nama Lengkap</label>
                                    <input type="text" x-model="newUser.nama" class="w-full bg-zinc-900 border border-white/10 rounded-lg px-4 py-2 text-white focus:border-[#D0B75B] outline-none" placeholder="Contoh: Ahmad">
                                </div>
                                <div>
                                    <label class="block text-xs uppercase font-bold text-gray-500 mb-2">Email / Username</label>
                                    <input type="text" x-model="newUser.email" class="w-full bg-zinc-900 border border-white/10 rounded-lg px-4 py-2 text-white focus:border-[#D0B75B] outline-none" placeholder="Contoh: ahmad@email.com">
                                </div>
                                <div>
                                    <label class="block text-xs uppercase font-bold text-gray-500 mb-2">Password</label>
                                    <input type="password" x-model="newUser.password" class="w-full bg-zinc-900 border border-white/10 rounded-lg px-4 py-2 text-white focus:border-[#D0B75B] outline-none" :placeholder="isEditing ? 'Isi untuk mengganti password' : 'Masukkan password'">
                                </div>
                                <div>
                                    <label class="block text-xs uppercase font-bold text-gray-500 mb-2">IP Address (Opsional)</label>
                                    <input type="text" x-model="newUser.ip_address" class="w-full bg-zinc-900 border border-white/10 rounded-lg px-4 py-2 text-white focus:border-[#D0B75B] outline-none" placeholder="Contoh: 192.168.1.10">
                                </div>
                                <div>
                                    <label class="block text-xs uppercase font-bold text-gray-500 mb-2">Peran (Role)</label>
                                    <input type="text" x-model="newUser.role" class="w-full bg-zinc-900 border border-white/10 rounded-lg px-4 py-2 text-white focus:border-[#D0B75B] outline-none" placeholder="Contoh: Jabatan">
                                </div>
                            </div>

                            {{-- Right Column: Feature Checklist --}}
                            <div class="flex-1 border-l border-white/5 pl-6">
                                <label class="block text-xs uppercase font-bold text-gray-500 mb-3">Hak Akses Fitur</label>
                                <div class="grid grid-cols-2 gap-2 pr-1">
                                    <template x-for="feature in availableFeatures" :key="feature">
                                        <div @click="toggleFeature(feature)" 
                                             class="flex items-center gap-2 p-2 rounded-lg border border-white/5 bg-zinc-900/30 hover:bg-zinc-900 cursor-pointer transition-colors group select-none">
                                            
                                            <div class="relative flex items-center justify-center w-3.5 h-3.5 rounded border transition-all"
                                                 :class="newUser.features.includes(feature) ? 'bg-[#D0B75B] border-[#D0B75B]' : 'border-gray-600 group-hover:border-gray-400 bg-black/50'">
                                                {{-- Inline SVG --}}
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" 
                                                     class="w-2.5 h-2.5 text-black transition-opacity" 
                                                     :class="newUser.features.includes(feature) ? 'opacity-100' : 'opacity-0'">
                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                </svg>
                                            </div>
                                            <span class="text-[10px] text-gray-300 group-hover:text-white font-medium truncate" x-text="feature === 'Pemesanan' ? 'Reservasi Room' : feature"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <div class="pt-6 mt-2 border-t border-white/5">
                             <button @click="saveUser()" class="w-full bg-[#D0B75B] hover:bg-[#e0c86b] text-black font-bold uppercase tracking-widest py-3 rounded-xl transition-all" x-text="isEditing ? 'Simpan Perubahan' : 'Simpan Akun'">
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
                 class="fixed top-4 right-4 z-[100] flex items-center gap-3 px-4 py-3 rounded-xl border shadow-2xl min-w-[300px]"
                 :class="{
                    'bg-[#0A0A0A] border-green-500/20 text-green-500': toastType === 'success',
                    'bg-[#0A0A0A] border-red-500/20 text-red-500': toastType === 'error'
                 }">
                <div class="p-2 rounded-full" 
                     :class="{
                        'bg-green-500/10': toastType === 'success',
                        'bg-red-500/10': toastType === 'error'
                     }">
                    <template x-if="toastType === 'success'">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                    </template>
                    <template x-if="toastType === 'error'">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                    </template>
                </div>
                <div>
                    <h4 class="text-sm font-bold uppercase tracking-wider" x-text="toastType === 'success' ? 'Berhasil' : 'Gagal'"></h4>
                    <p class="text-xs text-gray-400 font-medium mt-0.5" x-text="toastMessage"></p>
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
                        <h3 class="text-white font-bold text-lg mb-2">Hapus Akun?</h3>
                        <p class="text-gray-400 text-sm mb-6">Apakah Anda yakin ingin menghapus akun ini? Tindakan ini tidak dapat dibatalkan.</p>
                        
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

        {{-- Features List Modal --}}
        <template x-teleport="body">
            <div x-show="showFeaturesModal" style="display: none;" 
                 class="fixed inset-0 z-[110] flex items-center justify-center bg-black/80 backdrop-blur-sm p-4" 
                 x-transition.opacity>
                <div @click.away="showFeaturesModal = false" class="bg-[#0A0A0A] border border-white/10 rounded-2xl w-full max-w-sm overflow-hidden relative">
                     <div class="px-5 py-4 border-b border-white/5 bg-zinc-900/20 flex justify-between items-center">
                        <div>
                            <h3 class="text-white font-bold text-sm">Hak Akses Fitur</h3>
                            <p class="text-[10px] text-gray-500" x-text="viewingUserName"></p>
                        </div>
                        <button @click="showFeaturesModal = false"><i data-lucide="x" class="w-4 h-4 text-gray-500 hover:text-white transition-colors"></i></button>
                     </div>
                     <div class="p-5">
                        <div class="grid grid-cols-2 gap-2 max-h-[300px] overflow-y-auto pr-1">
                            <template x-for="feature in viewingUserFeatures" :key="feature">
                                <div class="flex items-center justify-between p-2 rounded-lg border border-white/5 bg-zinc-900/30 transition-all">
                                    <div class="flex items-center gap-2 overflow-hidden">
                                        <div class="w-5 h-5 rounded-full bg-[#D0B75B]/10 flex-shrink-0 flex items-center justify-center border border-[#D0B75B]/20">
                                            {{-- Inline SVG Check --}}
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="w-2.5 h-2.5 text-[#D0B75B]">
                                                <polyline points="20 6 9 17 4 12"></polyline>
                                            </svg>
                                        </div>
                                        <span class="text-[10px] text-gray-300 font-medium truncate" x-text="feature === 'Pemesanan' ? 'Reservasi Room' : feature"></span>
                                    </div>
                                    <button @click="initiateRemoveFeature(feature)" class="text-gray-600 hover:text-red-500 hover:bg-red-500/10 rounded transition-colors p-1 flex-shrink-0" title="Hapus Akses">
                                        {{-- Inline SVG Trash --}}
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3 h-3">
                                            <path d="M3 6h18"></path>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                        </svg>
                                    </button>
                                </div>
                            </template>
                             <template x-if="viewingUserFeatures.length === 0">
                                <p class="col-span-2 text-center text-gray-500 text-xs py-4 italic">Tidak ada akses fitur yang diberikan.</p>
                            </template>
                        </div>
                        <div class="pt-4 mt-2 border-t border-white/5">
                             <button @click="showFeaturesModal = false" class="w-full bg-white/5 hover:bg-white/10 text-white font-bold uppercase tracking-widest py-2.5 rounded-xl transition-all text-[10px]">
                                Tutup
                            </button>
                        </div>
                     </div>
                </div>
            </div>
        </template>

        {{-- Remove Feature Confirmation Modal --}}
        <template x-teleport="body">
            <div x-show="showRemoveFeatureModal" style="display: none;" 
                 class="fixed inset-0 z-[115] flex items-center justify-center bg-black/80 backdrop-blur-sm p-4" x-transition.opacity>
                <div @click.away="showRemoveFeatureModal = false" class="bg-[#0A0A0A] border border-white/10 rounded-2xl w-full max-w-xs overflow-hidden relative text-center">
                     <div class="p-6">
                        <div class="w-12 h-12 rounded-full bg-red-500/10 flex items-center justify-center mx-auto mb-3 border border-red-500/20">
                            {{-- Inline SVG Trash --}}
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6 text-red-500">
                                <path d="M3 6h18"></path>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            </svg>
                        </div>
                        <h3 class="text-white font-bold text-sm mb-1">Hapus Akses Fitur?</h3>
                        <p class="text-gray-400 text-[10px] mb-4">Anda akan menghapus akses <span class="text-white font-bold" x-text="featureToRemove === 'Pemesanan' ? 'Reservasi Room' : featureToRemove"></span> dari <span class="text-white font-bold" x-text="viewingUserName"></span>.</p>
                        
                        <div class="flex gap-2">
                            <button @click="showRemoveFeatureModal = false" class="flex-1 bg-white/5 hover:bg-white/10 text-white font-bold py-2 rounded-lg transition-all uppercase tracking-widest text-[10px]">
                                Batal
                            </button>
                            <button @click="confirmRemoveFeature()" class="flex-1 bg-red-500 hover:bg-red-600 text-white font-bold py-2 rounded-lg transition-all uppercase tracking-widest text-[10px] shadow-lg shadow-red-500/20">
                                Hapus
                            </button>
                        </div>
                     </div>
                </div>
            </div>
        </template>
        
        {{-- Attendance History Modal --}}
        <template x-teleport="body">
            <div x-show="showAttendanceModal" style="display: none;" 
                 class="fixed inset-0 z-[120] flex items-center justify-center bg-black/80 backdrop-blur-sm p-4" x-transition.opacity>
                <div @click.away="showAttendanceModal = false" class="bg-[#0A0A0A] border border-white/10 rounded-2xl w-full max-w-2xl overflow-hidden relative">
                     <div class="px-6 py-4 border-b border-white/5 bg-zinc-900/20 flex justify-between items-center">
                        <div>
                            <h3 class="text-white font-bold text-sm">Riwayat Absensi</h3>
                            <p class="text-[10px] text-gray-500" x-text="attendanceUser ? attendanceUser.nama : ''"></p>
                        </div>
                        <button @click="showAttendanceModal = false"><i data-lucide="x" class="w-4 h-4 text-gray-500 hover:text-white transition-colors"></i></button>
                     </div>
                     <div class="p-6">
                        <div class="bg-black/40 border border-white/5 rounded-xl overflow-hidden">
                            <table class="w-full text-xs text-left">
                                <thead class="text-[9px] text-gray-500 font-bold uppercase tracking-widest bg-white/5 border-b border-white/5">
                                    <tr>
                                        <th class="px-4 py-3">Tanggal</th>
                                        <th class="px-4 py-3">Masuk (Login)</th>
                                        <th class="px-4 py-3">Keluar (Logout)</th>
                                        <th class="px-4 py-3">IP Address</th>
                                        <th class="px-4 py-3 text-center">Status IP</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-white/5">
                                    <template x-for="(log, idx) in attendanceData" :key="idx">
                                        <tr class="hover:bg-white/5 transition-colors">
                                            <td class="px-4 py-3 text-gray-300 font-medium" x-text="log.date"></td>
                                            <td class="px-4 py-3 text-white" x-text="log.checkIn"></td>
                                            <td class="px-4 py-3 text-white" x-text="log.checkOut"></td>
                                            <td class="px-4 py-3 font-mono text-gray-400 text-[10px]" x-text="log.ip"></td>
                                            <td class="px-4 py-3 text-center">
                                                <span class="px-2 py-1 rounded text-[9px] font-bold uppercase tracking-wider border"
                                                      :class="log.isValidIp ? 'bg-green-500/10 text-green-500 border-green-500/20' : 'bg-red-500/10 text-red-500 border-red-500/20'"
                                                      x-text="log.isValidIp ? 'Valid' : 'Invalid'"></span>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                        <div class="pt-6 mt-2 flex justify-end">
                             <button @click="showAttendanceModal = false" class="bg-white/5 hover:bg-white/10 text-white font-bold uppercase tracking-widest py-2.5 px-6 rounded-xl transition-all text-[10px]">
                                Tutup
                            </button>
                        </div>
                     </div>
                </div>
            </div>
        </template>
    </div>
@endsection
