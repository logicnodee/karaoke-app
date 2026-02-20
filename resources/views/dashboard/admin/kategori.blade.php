@extends('layouts.dashboard')

@section('dashboard-role', 'Admin Panel')
@section('dashboard-role-icon', 'shield-check')

@section('title', 'Manajemen Kategori - Admin Dashboard')
@section('page-title', 'Manajemen Kategori')

@section('sidebar-nav')
    @include('dashboard.admin._sidebar', ['active' => 'kategori'])
@endsection

@section('dashboard-content')
    <div class="flex flex-col h-[calc(100vh-5rem)]" 
         x-data="{ 
            showToast: false, 
            toastMessage: '', 
            toastType: 'success',
            search: '',
            categories: {{ Js::from($categories) }},
            selectedCategory: null,
            isEditModalOpen: false,
            
            showNotification(message, type = 'success') {
                this.toastMessage = message;
                this.toastType = type;
                this.showToast = true;
                setTimeout(() => this.showToast = false, 3000);
            },
            
            openEditModal(category) {
                this.selectedCategory = JSON.parse(JSON.stringify(category)); // Deep copy
                this.isEditModalOpen = true;
                this.$nextTick(() => lucide.createIcons());
            },
            
            openAddModal() {
                this.selectedCategory = {
                    id: Date.now(), // Generate unique temp ID
                    name: '',
                    description: '',
                    banner_url: '',
                    banner_type: 'image',
                    sub_categories: []
                };
                this.isEditModalOpen = true;
                this.$nextTick(() => lucide.createIcons());
            },
            
            triggerFileInput() {
                this.$refs.bannerInput.click();
            },

            handleFileChange(event) {
                const file = event.target.files[0];
                if (file) {
                    // Create a fake URL for preview purposes
                    this.selectedCategory.banner_url = URL.createObjectURL(file);
                    this.selectedCategory.banner_type = file.type.startsWith('video/') ? 'video' : 'image';
                }
            },

            saveCategory() {
                if (!this.selectedCategory.name) return; // Basic validation

                // Check if existing
                const index = this.categories.findIndex(c => c.id === this.selectedCategory.id);
                
                if (index !== -1) {
                    // Update existing
                    this.categories[index] = this.selectedCategory;
                    this.showNotification('Kategori ' + this.selectedCategory.name + ' berhasil diperbarui');
                } else {
                    // Add new
                    this.categories.push(this.selectedCategory);
                    this.showNotification('Kategori ' + this.selectedCategory.name + ' berhasil ditambahkan');
                }
                this.isEditModalOpen = false;
                this.$nextTick(() => lucide.createIcons());
            },

            addSubCategory() {
                if (!this.selectedCategory.sub_categories) this.selectedCategory.sub_categories = [];
                this.selectedCategory.sub_categories.push('New Genre');
                this.$nextTick(() => lucide.createIcons());
            },

            removeSubCategory(index) {
                this.selectedCategory.sub_categories.splice(index, 1);
            }
         }">
        
        {{-- Top Bar Actions --}}
        <div class="flex items-center justify-between mb-6">
            <div class="relative w-64">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500"></i>
                <input type="text" x-model="search" placeholder="Cari kategori..." 
                       class="w-full bg-[#080808] border border-white/10 rounded-lg pl-10 pr-4 py-2 text-sm text-white focus:border-[#D0B75B] focus:outline-none placeholder-gray-600 transition-colors">
            </div>
            
            <button @click="openAddModal()" class="bg-[#D0B75B] text-black px-4 py-2 rounded-lg text-sm font-bold flex items-center gap-2 hover:bg-[#bfa853] transition-colors">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Tambah Kategori
            </button>
        </div>

        {{-- Category List --}}
        <div class="bg-[#080808] border border-white/5 rounded-2xl overflow-hidden flex-1 flex flex-col">
             <div class="px-6 py-4 border-b border-white/5 bg-black/20 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-3">
                    <i data-lucide="layers" class="w-5 h-5 text-[#D0B75B]"></i> 
                    <h2 class="text-sm font-bold text-gray-200 uppercase tracking-widest" style="font-family: 'Inter';">Daftar Kategori Utama</h2>
                </div>
                <span class="text-[10px] text-gray-500 font-mono" x-text="categories.length + ' kategori'"></span>
            </div>
            
            <div class="overflow-x-auto flex-1 p-6" x-effect="categories; $nextTick(() => lucide.createIcons())">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <template x-for="cat in categories" :key="cat.id">
                        <div x-show="cat.name.toLowerCase().includes(search.toLowerCase())" 
                             class="bg-[#141414] border border-white/5 rounded-xl overflow-hidden group hover:border-white/20 transition-all">
                            
                            {{-- Banner Preview --}}
                            <div class="h-32 bg-gray-800 relative overflow-hidden">
                                <template x-if="cat.banner_url">
                                    <div class="w-full h-full">
                                        <template x-if="cat.banner_type === 'video'">
                                            <div class="w-full h-full relative">
                                                <div class="absolute inset-0 flex items-center justify-center bg-black/50 z-10">
                                                    <i data-lucide="video" class="w-6 h-6 text-white/50"></i>
                                                </div>
                                                <video :src="cat.banner_url" class="w-full h-full object-cover opacity-60"></video>
                                            </div>
                                        </template>
                                        <template x-if="cat.banner_type === 'image'">
                                            <img :src="cat.banner_url" class="w-full h-full object-cover opacity-60">
                                        </template>
                                    </div>
                                </template>
                                <template x-if="!cat.banner_url">
                                    <div class="w-full h-full flex items-center justify-center bg-[#202020]">
                                        <span class="text-xs text-gray-600">No Banner</span>
                                    </div>
                                </template>

                                <div class="absolute inset-0 bg-gradient-to-t from-[#141414] to-transparent"></div>
                                
                                <div class="absolute bottom-3 left-3 z-20">
                                    <h3 class="text-xl font-black text-white uppercase tracking-tight" x-text="cat.name || 'New Category'"></h3>
                                    <p class="text-[10px] text-gray-400" x-text="(cat.sub_categories ? cat.sub_categories.length : 0) + ' Sub-kategori'"></p>
                                </div>

                                <button @click.stop="openEditModal(cat)" class="absolute top-2 right-2 p-2 bg-black/50 hover:bg-[#D0B75B] hover:text-black rounded-lg text-white transition-all duration-300 backdrop-blur-sm opacity-0 group-hover:opacity-100 z-50 transform translate-y-2 group-hover:translate-y-0">
                                    <i data-lucide="edit-2" class="w-4 h-4"></i>
                                </button>
                            </div>

                            {{-- Sub Categories --}}
                            <div class="p-4">
                                <p class="text-[10px] text-gray-500 font-bold uppercase mb-2">Sub-Kategori (Genre)</p>
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="sub in cat.sub_categories">
                                        <span class="px-2 py-1 rounded-md bg-white/5 border border-white/10 text-[10px] text-gray-300" x-text="sub"></span>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- Edit Modal --}}
        <div x-show="isEditModalOpen" 
             style="display: none;"
             class="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 backdrop-blur-sm p-4"
             x-transition.opacity>
            <div @click.outside="isEditModalOpen = false" class="bg-[#141414] border border-white/10 rounded-2xl w-full max-w-lg overflow-hidden shadow-2xl flex flex-col max-h-[90vh]">
                
                <div class="px-6 py-4 border-b border-white/10 flex items-center justify-between bg-black/20">
                    <h3 class="font-bold text-white">Edit Kategori</h3>
                    <button @click="isEditModalOpen = false" class="text-gray-500 hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
                </div>

                <div class="p-6 overflow-y-auto space-y-4">
                    <template x-if="selectedCategory">
                        <div class="space-y-4">
                            {{-- Name --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Nama Kategori</label>
                                <input type="text" x-model="selectedCategory.name" placeholder="Masukkan nama kategori..." class="w-full bg-black/40 border border-white/10 rounded p-2 text-white text-sm focus:border-[#D0B75B] outline-none placeholder-gray-600">
                            </div>

                            {{-- Description --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Deskripsi</label>
                                <textarea x-model="selectedCategory.description" rows="3" placeholder="Deskripsi singkat kategori..." class="w-full bg-black/40 border border-white/10 rounded p-2 text-white text-sm focus:border-[#D0B75B] outline-none placeholder-gray-600 resize-none"></textarea>
                            </div>

                            {{-- Banner Upload --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Banner (Video/Image)</label>
                                
                                {{-- Preview Area --}}
                                <div class="mb-2 h-32 bg-black/40 border border-white/10 rounded overflow-hidden relative group/banner cursor-pointer" @click="triggerFileInput">
                                    <template x-if="selectedCategory.banner_url">
                                        <div class="w-full h-full">
                                            <template x-if="selectedCategory.banner_type === 'video'">
                                                <video :src="selectedCategory.banner_url" class="w-full h-full object-cover opacity-80"></video>
                                            </template>
                                            <template x-if="selectedCategory.banner_type === 'image'">
                                                <img :src="selectedCategory.banner_url" class="w-full h-full object-cover opacity-80">
                                            </template>
                                        </div>
                                    </template>
                                    <div class="absolute inset-0 flex flex-col items-center justify-center bg-black/50 transition-opacity"
                                         :class="selectedCategory.banner_url ? 'opacity-0 group-hover/banner:opacity-100' : 'opacity-100'">
                                        <i data-lucide="upload-cloud" class="w-8 h-8 text-gray-400 mb-2"></i>
                                        <span class="text-xs text-gray-300 font-bold">Klik untuk Upload Banner</span>
                                        <span class="text-[10px] text-gray-500">Video MP4 atau Gambar JPG/PNG</span>
                                    </div>
                                </div>
                                
                                {{-- Hidden File Input --}}
                                <input type="file" x-ref="bannerInput" @change="handleFileChange" class="hidden" accept="image/*,video/mp4">
                            </div>

                            {{-- Sub Categories --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Sub Kategori (Navbar)</label>
                                <div class="space-y-2">
                                    <template x-for="(sub, index) in selectedCategory.sub_categories" :key="index">
                                        <div class="flex gap-2">
                                            <input type="text" x-model="selectedCategory.sub_categories[index]" class="flex-1 bg-black/40 border border-white/10 rounded p-2 text-white text-sm focus:border-[#D0B75B] outline-none">
                                            <button @click="removeSubCategory(index)" class="text-red-500 hover:text-red-400 p-2 hover:bg-white/5 rounded"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                                        </div>
                                    </template>
                                    <button @click="addSubCategory" class="w-full py-2 border border-dashed border-white/20 text-gray-400 text-xs rounded hover:border-[#D0B75B] hover:text-[#D0B75B] transition-colors flex items-center justify-center gap-2">
                                        <i data-lucide="plus" class="w-3 h-3"></i> Tambah Sub-Kategori
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="px-6 py-4 border-t border-white/10 bg-black/20 flex justify-end gap-3">
                    <button @click="isEditModalOpen = false" class="px-4 py-2 text-sm text-gray-400 hover:text-white font-medium">Batal</button>
                    <button @click="saveCategory" class="px-4 py-2 bg-[#D0B75B] text-black text-sm font-bold rounded hover:bg-[#bfa853]">Simpan Perubahan</button>
                </div>
            </div>
        </div>

        {{-- Toast Notification --}}
        <div x-show="showToast" 
             style="display: none;"
             class="fixed top-4 right-4 z-[200] flex items-center gap-3 px-4 py-3 rounded-xl border shadow-2xl min-w-[300px] bg-[#0A0A0A]"
             :class="{
                'border-green-500/20 text-green-500': toastType === 'success',
                'border-blue-500/20 text-blue-500': toastType === 'info',
                'border-red-500/20 text-red-500': toastType === 'error'
             }"
             x-transition>
             <div class="p-2 rounded-full bg-white/10">
                <i data-lucide="check" class="w-4 h-4"></i>
             </div>
             <div>
                 <h4 class="text-sm font-bold uppercase tracking-wider">Berhasil</h4>
                 <p class="text-xs text-gray-400 font-medium mt-0.5" x-text="toastMessage"></p>
             </div>
        </div>
    </div>
@endsection
