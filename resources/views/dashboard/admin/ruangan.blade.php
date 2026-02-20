@extends('layouts.dashboard')

@section('dashboard-role', 'Admin Panel')
@section('dashboard-role-icon', 'shield-check')

@section('title', 'Manajemen Ruangan - Admin Dashboard')
@section('page-title', 'Manajemen Ruangan')

@section('sidebar-nav')
    @include('dashboard.admin._sidebar', ['active' => 'ruangan'])
@endsection

@section('dashboard-content')
    <div x-data="{
        rooms: {{ json_encode($daftarRuangan) }},
        packages: {{ json_encode($paketHarga) }},
        
        // Modal Overlay for Audio Init
        initAudioOverlay: true,
        showAddModal: false,
        showAddPackageModal: false,
        showControlModal: false,
        showBillModal: false,
        showReceiptPreview: false,
        selectedRoom: null,
        activeBill: null,
        showStartReceiptPreview: false,
        sessionReceiptData: null,
        newRoom: { nama: '', lantai: 1, tipe: 'Regular', kapasitas: 4, harga_weekday: 50000, harga_weekend: 75000 },
        newPackage: { nama: '', durasi: '', harga_weekday: 0, harga_weekend: 0 },
        showEditPackageModal: false,
        editPackageIndex: null,
        editPackage: { nama: '', durasi: '', harga_weekday: 0, harga_weekend: 0 },
        bookingForm: { tamu: '', mode: 'paket', durasi: 1, selectedPackage: null },
        currentTime: new Date().getTime(),
        showExtendModal: false,
        extendForm: { durasi: 1 },
        extendReceiptData: null,
        showExtendReceiptPreview: false,
        
        playWarningBeeps(room) {
            // Encode filenames to handle spaces and special characters
            const audio1Url = '/assets/sound%20effect/' + encodeURIComponent('Announcement sound effect.mp3');
            const audio2Url = '/assets/sound%20effect/' + encodeURIComponent('waktu anda tersisa 10menit sound effect (warnet).mp3');

            const audio1 = new Audio(audio1Url);
            const audio2 = new Audio(audio2Url);
            
            // Preload
            audio1.load();
            audio2.load();

            const playChain = () => {
                audio1.play()
                    .then(() => {
                        console.log('Audio 1 playing');
                    })
                    .catch(e => {
                        console.warn('Audio Autoplay Blocked:', e);
                        // Fallback: If blocked, wait for ONE interaction then play immediately
                        const forcePlay = () => {
                            audio1.play().catch(e => console.error('Force play failed', e));
                            document.removeEventListener('click', forcePlay);
                            document.removeEventListener('keydown', forcePlay);
                        };
                        document.addEventListener('click', forcePlay);
                        document.addEventListener('keydown', forcePlay);
                    });

                audio1.onended = () => {
                    audio2.play().catch(e => console.warn('Audio 2 play failed:', e));
                };
            };

            playChain();
        },

        init() {
            // Initialize icons immediately
            this.$nextTick(() => {
                if (window.lucide) window.lucide.createIcons();
            });

            // Watch for changes in rooms to re-init icons
            this.$watch('rooms', () => {
                this.$nextTick(() => {
                    if (window.lucide) window.lucide.createIcons();
                });
            });

            // Helper to parse HH:MM:SS safely
            const parseTimeSafe = (str) => {
                if (!str || typeof str !== 'string') return 0;
                const parts = str.split(':').map(p => parseInt(p, 10)); // clear radix
                if (parts.length !== 3 || parts.some(isNaN)) return 0;
                return (parts[0] * 3600) + (parts[1] * 60) + parts[2];
            };

            // Hydrate static server data into dynamic state
            // Hydrate static server data into dynamic state
            this.rooms.forEach(room => {
                if (room.status === 'Digunakan' && !room.booking_start) {
                    // Scenario 1: Package Mode (Has Remaining Time)
                    if (room.sisa_waktu) {
                         const seconds = parseTimeSafe(room.sisa_waktu);
                         if (seconds > 0) {
                            room.billing_mode = 'paket';
                            room.sisa_detik = seconds;
                            
                            // Estimate duration (ceil to nearest hour)
                            let estimatedDuration = Math.ceil(seconds / 3600);
                            if (estimatedDuration < 1) estimatedDuration = 1;
                            if (estimatedDuration * 3600 < seconds) estimatedDuration += 1; 

                            room.booking_duration = estimatedDuration;
                            
                            // Back-calculate Start = Now - (Total - Remaining)
                            const elapsed = (estimatedDuration * 3600) - seconds;
                            room.booking_start = new Date(Date.now() - (elapsed * 1000)).toISOString();
                         }
                    } 
                    // Scenario 2: Open Billing Mode (Has Elapsed Time/Usage Duration)
                    else if (room.durasi_pakai) {
                         const elapsedSeconds = parseTimeSafe(room.durasi_pakai);
                         room.billing_mode = 'open';
                         room.durasi_berjalan = elapsedSeconds;
                         // Back-calculate Start = Now - Elapsed
                         room.booking_start = new Date(Date.now() - (elapsedSeconds * 1000)).toISOString();
                    }
                    // Scenario 3: Fallback (Just Started)
                    else {
                         room.billing_mode = 'open';
                         room.booking_start = new Date().toISOString();
                         room.durasi_berjalan = 0;
                    }
                }
            });

            // Update timers every second
            setInterval(() => {
                this.currentTime = new Date().getTime();
                this.rooms.forEach(room => {
                    if (room.status === 'Digunakan' && room.booking_start) {
                        const start = new Date(room.booking_start).getTime();
                        const now = this.currentTime;
                        const elapsed = Math.floor((now - start) / 1000); // Elapsed execution time
                        
                        if (room.billing_mode === 'open') {
                            room.durasi_berjalan = elapsed;
                        } else if (room.billing_mode === 'paket' || room.billing_mode === 'khusus') {
                            const totalSeconds = (room.booking_duration || 0) * 3600;
                            let remaining = totalSeconds - elapsed;
                            // Auto-play warning sound at 18 seconds remaining (Safe Range)
                            if (remaining <= 18 && remaining >= 17) {
                                if (!room.hasPlayedWarning) {
                                    this.playWarningBeeps(room);
                                    room.hasPlayedWarning = true;
                                }
                            } else {
                                // Reset flag if time is added/changed significantly above 18s
                                if (remaining > 22) room.hasPlayedWarning = false;
                            }

                            if (remaining <= 0) {
                                remaining = 0;
                                if (room.status === 'Digunakan' && !room.hasNotifiedExpired) {
                                    room.hasNotifiedExpired = true;
                                    this.showNotification(`Waktu ${room.nama} Telah Habis! Menunggu tindakan...`, 'warning');
                                }
                            }
                            room.sisa_detik = remaining;
                            room.hampir_habis = remaining < 900; 
                        }
                    }
                });
            }, 1000);
        },

        parseTime(timeString) {
             if (!timeString) return 0;
             const parts = timeString.split(':');
             if (parts.length !== 3) return 0;
             return (parseInt(parts[0]) * 3600) + (parseInt(parts[1]) * 60) + parseInt(parts[2]);
        },

        formatMoney(amount) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(amount).slice(0, -3);
        },

        formatTimeArray(seconds) {
            if (!seconds || isNaN(seconds)) seconds = 0;
            const h = Math.floor(seconds / 3600);
            const m = Math.floor((seconds % 3600) / 60);
            const s = seconds % 60;
            return `${h.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
        },

        calculateCurrentPrice(room) {
            if (!room.booking_start) return this.formatMoney(0);
            const start = new Date(room.booking_start).getTime();
            const now = this.currentTime || Date.now(); 
            const elapsedHours = (now - start) / 1000 / 3600;
            const pricePerHour = room.harga_weekday || 0;

            let billAmount = 0;
            if (room.billing_mode === 'paket' || room.billing_mode === 'khusus') {
                billAmount = (room.booking_duration || 0) * pricePerHour;
            } else {
                // Open billing: Charge per minute
                const elapsedMinutes = Math.ceil((now - start) / 1000 / 60);
                const pricePerMinute = pricePerHour / 60;
                billAmount = elapsedMinutes * pricePerMinute;
            }
            return this.formatMoney(billAmount);
        },

        addRoom() {
            this.rooms.push({
                nama: this.newRoom.nama,
                lantai: this.newRoom.lantai,
                tipe: this.newRoom.tipe,
                kapasitas: this.newRoom.kapasitas,
                harga_weekday: this.newRoom.harga_weekday,
                harga_weekend: this.newRoom.harga_weekend,
                status: 'Kosong',
                tamu: null,
                sisa_waktu: null,
                hampir_habis: false
            });
            this.showAddModal = false;
            this.newRoom = { nama: '', lantai: 1, tipe: 'Regular', kapasitas: 4, harga_weekday: 50000, harga_weekend: 75000 };
            this.showAddModal = false;
            this.newRoom = { nama: '', lantai: 1, tipe: 'Regular', kapasitas: 4, harga_weekday: 50000, harga_weekend: 75000 };
            this.showNotification('Ruangan berhasil ditambahkan!', 'success');
        },

        addPackage() {
            if (!this.newPackage.nama || !this.newPackage.durasi) {
                this.showNotification('Nama paket dan durasi wajib diisi!', 'error');
                return;
            }
            this.packages.push({
                nama: this.newPackage.nama,
                durasi: this.newPackage.durasi + ' Jam',
                harga_weekday: parseInt(this.newPackage.harga_weekday),
                harga_weekend: parseInt(this.newPackage.harga_weekend)
            });
            this.showAddPackageModal = false;
            this.newPackage = { nama: '', durasi: '', harga_weekday: 0, harga_weekend: 0 };
            this.showNotification('Paket berhasil ditambahkan!', 'success');
        },

        openEditPackage(index) {
            this.editPackageIndex = index;
            const p = this.packages[index];
            this.editPackage = {
                nama: p.nama,
                durasi: p.durasi.replace(/[^0-9]/g, ''),
                harga_weekday: p.harga_weekday,
                harga_weekend: p.harga_weekend
            };
            this.showEditPackageModal = true;
        },

        updatePackage() {
            if (!this.editPackage.nama || !this.editPackage.durasi) {
                this.showNotification('Nama paket dan durasi wajib diisi!', 'error');
                return;
            }
            this.packages[this.editPackageIndex] = {
                nama: this.editPackage.nama,
                durasi: this.editPackage.durasi + ' Jam',
                harga_weekday: parseInt(this.editPackage.harga_weekday),
                harga_weekend: parseInt(this.editPackage.harga_weekend)
            };
            this.showEditPackageModal = false;
            this.showNotification('Paket berhasil diperbarui!', 'success');
        },

        deletePackage(index) {
            if (confirm('Yakin ingin menghapus paket ini?')) {
                this.packages.splice(index, 1);
                this.showNotification('Paket berhasil dihapus!', 'success');
            }
        },

        openControl(room) {
            this.selectedRoom = room;
            this.bookingForm = { tamu: '', mode: 'paket', durasi: 1, selectedPackage: null };
            this.showControlModal = true;
        },

        prepareSessionData() {
             if (!this.bookingForm.tamu) {
                 this.showNotification('Nama tamu wajib diisi!', 'error');
                 return false;
             }
             
             const now = new Date();
             const startTimeStr = now.toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'});
             let endTimeStr = '';
             let durasiText = '';
             let estimasi = '-';

             if (this.bookingForm.mode === 'paket') {
                 const durasi = parseInt(this.bookingForm.durasi);
                 const endTime = new Date(now.getTime() + durasi * 3600 * 1000);
                 endTimeStr = endTime.toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'});
                 durasiText = durasi + ' Jam (Per Jam)';
                 estimasi = this.formatMoney(durasi * this.selectedRoom.harga_weekday);
             } else if (this.bookingForm.mode === 'khusus') {
                 if (this.bookingForm.selectedPackage === null) {
                     this.showNotification('Pilih paket terlebih dahulu!', 'error');
                     return false;
                 }
                 const pkg = this.packages[this.bookingForm.selectedPackage];
                 const durasi = parseInt(pkg.durasi);
                 const endTime = new Date(now.getTime() + durasi * 3600 * 1000);
                 endTimeStr = endTime.toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'});
                 durasiText = pkg.nama + ' (' + durasi + ' Jam)';
                 estimasi = this.formatMoney(pkg.harga_weekday);
             } else {
                 endTimeStr = 'Open Billing';
                 durasiText = 'Open Billing';
             }

             this.sessionReceiptData = {
                 roomName: this.selectedRoom.nama,
                 tamu: this.bookingForm.tamu,
                 masuk: startTimeStr,
                 keluar: endTimeStr,
                 durasi: durasiText,
                 tanggal: now.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' }),
                 estimasi: estimasi,
                 mode: this.bookingForm.mode,
                 rawDuration: this.bookingForm.mode === 'khusus' ? parseInt(this.packages[this.bookingForm.selectedPackage].durasi) : parseInt(this.bookingForm.durasi)
             };
             return true;
        },

        handleStartButton() {
            if (!this.prepareSessionData()) return;

            if (this.bookingForm.mode === 'open') {
                // Open Billing -> Start Immediately (No Print)
                this.executeStartSession(false);
            } else {
                // Paket / Khusus -> Show Preview
                this.showStartReceiptPreview = true;
            }
        },

        executeStartSession(shouldPrint) {
            const now = new Date();
            this.selectedRoom.status = 'Digunakan';
            this.selectedRoom.tamu = this.bookingForm.tamu;
            this.selectedRoom.booking_start = now.toISOString();
            this.selectedRoom.billing_mode = this.bookingForm.mode;

            // Auto-generate Key
            this.selectedRoom.key = this.generateKey(this.selectedRoom.nama);

            if (this.bookingForm.mode === 'paket' || this.bookingForm.mode === 'khusus') {
                const durasi = this.sessionReceiptData.rawDuration;
                this.selectedRoom.booking_duration = durasi;
                this.selectedRoom.sisa_detik = durasi * 3600;
            } else {
                this.selectedRoom.durasi_berjalan = 0;
            }

            if (shouldPrint) {
                this.printSessionReceipt(this.sessionReceiptData);
            }

            this.showControlModal = false;
            this.showStartReceiptPreview = false;
            this.showNotification('Sesi dimulai' + (shouldPrint ? ' & struk dicetak!' : '!') + ' | Key: ' + this.selectedRoom.key, 'success');
        },

        generateKey(roomName) {
            const name = roomName.toUpperCase();
            let prefix = '';
            let num = '';
            
            if (name.startsWith('VVIP')) {
                prefix = 'VV';
                num = name.replace(/[^0-9]/g, '');
            } else if (name.startsWith('VIP')) {
                prefix = 'V';
                num = name.replace(/[^0-9]/g, '');
            } else if (name.startsWith('SUITE')) {
                prefix = 'S';
                num = name.replace(/[^0-9]/g, '');
            } else if (name.startsWith('PARTY')) {
                prefix = 'P';
                num = name.replace(/[^0-9]/g, '');
            } else {
                // Room 101, Room 202, etc - just use the number
                prefix = '';
                num = name.replace(/[^0-9]/g, '');
            }
            
            return 'K-' + prefix + num.padStart(prefix ? 2 : 3, '0');
        },

        printSessionReceipt(data) {
            const printContent = `
                <html>
                    <head>
                        <title>Struk Sesi - ${data.roomName}</title>
                        <style>
                            body { font-family: 'Courier New', monospace; padding: 20px; max-width: 300px; margin: 0 auto; background: #fff; color: #000; }
                            .header { text-align: center; margin-bottom: 15px; border-bottom: 2px dashed black; padding-bottom: 10px; }
                            .header h3 { margin: 0 0 5px 0; font-size: 18px; }
                            .header p { margin: 2px 0; font-size: 10px; }
                            .section-title { text-align: center; font-size: 12px; font-weight: bold; margin: 10px 0; text-transform: uppercase; letter-spacing: 2px; background: #f0f0f0; padding: 5px; }
                            .item { display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 12px; }
                            .divider { border-top: 1px dashed black; margin: 10px 0; }
                            .estimate { display: flex; justify-content: space-between; font-weight: bold; font-size: 14px; margin-top: 5px; }
                            .footer { text-align: center; margin-top: 15px; font-size: 10px; border-top: 2px dashed black; padding-top: 10px; }
                            .time-highlight { font-size: 20px; text-align: center; font-weight: bold; margin: 10px 0; letter-spacing: 3px; }
                        </style>
                    </head>
                    <body>
                        <div class='header'>
                            <h3>SGRT KARAOKE</h3>
                            <p>Jl. Hiburan Malam No. 99</p>
                            <p>Telp: 021-555-999</p>
                        </div>
                        <div class='section-title'>Struk Mulai Sesi</div>
                        <div class='item'><span>Tanggal:</span> <span>${data.tanggal}</span></div>
                        <div class='item'><span>Ruangan:</span> <span>${data.roomName}</span></div>
                        <div class='item'><span>Tamu:</span> <span>${data.tamu}</span></div>
                        <div class='divider'></div>
                        <div class='time-highlight'>${data.masuk} &rarr; ${data.keluar}</div>
                        <div class='item'><span>Jam Masuk:</span> <span>${data.masuk}</span></div>
                        <div class='item'><span>Jam Keluar:</span> <span>${data.keluar}</span></div>
                        <div class='item'><span>Durasi:</span> <span>${data.durasi}</span></div>
                        <div class='divider'></div>
                        <div class='estimate'>
                            <span>Estimasi</span>
                            <span>${data.estimasi}</span>
                        </div>
                        <div class='footer'>
                            <p>Selamat Berkaraoke!</p>
                            <p>--- Simpan struk ini sebagai bukti ---</p>
                        </div>
                    </body>
                </html>
            `;

            const printWindow = window.open('', '', 'height=600,width=400');
            printWindow.document.write(printContent);
            printWindow.document.close();
            printWindow.focus();
            setTimeout(() => {
                printWindow.print();
                printWindow.close();
            }, 500);
        },

        checkout() {
            const room = this.selectedRoom;
            const now = new Date();
            const start = new Date(room.booking_start);

            if (room.billing_mode === 'open') {
                // Open billing: Calculate bill at checkout
                const pricePerHour = room.harga_weekday;
                const elapsedMinutes = Math.ceil((now - start) / 1000 / 60);
                const pricePerMinute = pricePerHour / 60;
                const billAmount = elapsedMinutes * pricePerMinute;
                const durasiText = this.formatTimeArray(Math.floor((now - start)/1000)) + ' (Open)';

                this.activeBill = {
                    roomName: room.nama,
                    tamu: room.tamu,
                    masuk: start.toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'}),
                    keluar: now.toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'}),
                    durasi: durasiText,
                    total: billAmount
                };

                // Reset Room
                room.status = 'Kosong';
                room.tamu = null;
                room.booking_start = null;
                room.billing_mode = null;
                room.sisa_detik = null;
                room.durasi_berjalan = null;
                room.key = null;
                room.hasNotifiedExpired = false;

                this.showControlModal = false;
                this.showBillModal = true;
            } else {
                // Paket/Khusus: Already paid, just end session
                const roomName = room.nama;
                room.status = 'Kosong';
                room.tamu = null;
                room.booking_start = null;
                room.billing_mode = null;
                room.sisa_detik = null;
                room.durasi_berjalan = null;
                room.key = null;
                room.hasNotifiedExpired = false;

                this.showControlModal = false;
                this.showNotification(roomName + ' - Sesi selesai. Sudah dibayar di awal.', 'success');
            }
        },

        setRoomCleaning() {
            if (!this.selectedRoom) return;
            this.selectedRoom.status = 'Cleaning';
            this.selectedRoom.tamu = null;
            this.selectedRoom.billing_mode = null;
            this.selectedRoom.booking_start = null;
            this.selectedRoom.key = null;
            this.selectedRoom.sisa_detik = null;
            this.selectedRoom.durasi_berjalan = null;
            this.selectedRoom.hasNotifiedExpired = false;
            this.showControlModal = false;
            this.showNotification(this.selectedRoom.nama + ' - Status: Cleaning', 'info');
        },

        prepareExtendData() {
            if (!this.selectedRoom) return false;
            const durasi = parseInt(this.extendForm.durasi);
            if (!durasi || durasi < 1) {
                this.showNotification('Durasi minimal 1 jam!', 'error');
                return false;
            }
            const now = new Date();
            const biaya = durasi * this.selectedRoom.harga_weekday;

            this.extendReceiptData = {
                roomName: this.selectedRoom.nama,
                tamu: this.selectedRoom.tamu,
                tanggal: now.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' }),
                waktu: now.toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'}),
                durasi: durasi + ' Jam',
                biaya: this.formatMoney(biaya),
                rawDuration: durasi,
                rawBiaya: biaya
            };
            return true;
        },

        handleExtendButton() {
            if (!this.prepareExtendData()) return;
            this.showExtendReceiptPreview = true;
        },

        executeExtendSession(shouldPrint) {
            const durasi = this.extendReceiptData.rawDuration;
            this.selectedRoom.sisa_detik += durasi * 3600;
            this.selectedRoom.hasNotifiedExpired = false;

            if (shouldPrint) {
                this.printExtendReceipt(this.extendReceiptData);
            }

            this.showExtendModal = false;
            this.showExtendReceiptPreview = false;
            this.showControlModal = false;
            this.showNotification('Waktu ditambah ' + durasi + ' jam untuk ' + this.selectedRoom.nama + '!', 'success');
        },

        printExtendReceipt(data) {
            const printContent = `
                <html>
                    <head>
                        <title>Struk Tambah Waktu - ${data.roomName}</title>
                        <style>
                            body { font-family: 'Courier New', monospace; padding: 20px; max-width: 300px; margin: 0 auto; background: #fff; color: #000; }
                            .header { text-align: center; margin-bottom: 15px; border-bottom: 2px dashed black; padding-bottom: 10px; }
                            .header h3 { margin: 0 0 5px 0; font-size: 18px; }
                            .header p { margin: 2px 0; font-size: 10px; }
                            .section-title { text-align: center; font-size: 12px; font-weight: bold; margin: 10px 0; text-transform: uppercase; letter-spacing: 2px; background: #f0f0f0; padding: 5px; }
                            .item { display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 12px; }
                            .divider { border-top: 1px dashed black; margin: 10px 0; }
                            .total { display: flex; justify-content: space-between; font-weight: bold; font-size: 14px; margin-top: 5px; }
                            .footer { text-align: center; margin-top: 15px; font-size: 10px; border-top: 2px dashed black; padding-top: 10px; }
                        </style>
                    </head>
                    <body>
                        <div class='header'>
                            <h3>SGRT KARAOKE</h3>
                            <p>Jl. Hiburan Malam No. 99</p>
                            <p>Telp: 021-555-999</p>
                        </div>
                        <div class='section-title'>Struk Tambah Waktu</div>
                        <div class='item'><span>Tanggal:</span> <span>${data.tanggal}</span></div>
                        <div class='item'><span>Waktu:</span> <span>${data.waktu}</span></div>
                        <div class='item'><span>Ruangan:</span> <span>${data.roomName}</span></div>
                        <div class='item'><span>Tamu:</span> <span>${data.tamu}</span></div>
                        <div class='divider'></div>
                        <div class='item'><span>Tambah Durasi:</span> <span>${data.durasi}</span></div>
                        <div class='divider'></div>
                        <div class='total'>
                            <span>Biaya</span>
                            <span>${data.biaya}</span>
                        </div>
                        <div class='footer'>
                            <p>Selamat Berkaraoke!</p>
                            <p>--- Simpan struk ini sebagai bukti ---</p>
                        </div>
                    </body>
                </html>
            `;

            const printWindow = window.open('', '', 'height=600,width=400');
            printWindow.document.write(printContent);
            printWindow.document.close();
            printWindow.focus();
            setTimeout(() => {
                printWindow.print();
                printWindow.close();
            }, 500);
        },

        printReceipt() {
            const bill = this.activeBill;
            if (!bill) return;

            const printContent = `
                <html>
                    <head>
                        <title>Struk Pembayaran - ${bill.roomName}</title>
                        <style>
                            body { font-family: 'Courier New', monospace; padding: 20px; max-width: 300px; margin: 0 auto; background: #fff; color: #000; }
                            .header { text-align: center; margin-bottom: 20px; border-bottom: 1px dashed black; padding-bottom: 10px; }
                            .item { display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 12px; }
                            .total { border-top: 1px dashed black; margin-top: 10px; padding-top: 10px; font-weight: bold; font-size: 14px; display: flex; justify-content: space-between; }
                            .footer { text-align: center; margin-top: 20px; font-size: 10px; }
                        </style>
                    </head>
                    <body>
                        <div class='header'>
                            <h3>SGRT KARAOKE</h3>
                            <p>Jl. Hiburan Malam No. 99</p>
                            <p>Telp: 021-555-999</p>
                        </div>
                        <div class='item'><span>Ruangan:</span> <span>${bill.roomName}</span></div>
                        <div class='item'><span>Tamu:</span> <span>${bill.tamu}</span></div>
                        <div class='item'><span>Masuk:</span> <span>${bill.masuk}</span></div>
                        <div class='item'><span>Keluar:</span> <span>${bill.keluar}</span></div>
                        <div class='item'><span>Durasi:</span> <span>${bill.durasi}</span></div>
                        <div class='total'>
                            <span>TOTAL</span>
                            <span>${this.formatMoney(bill.total)}</span>
                        </div>
                        <div class='footer'>
                            <p>Terima Kasih atas Kunjungan Anda!</p>
                            <p>--- Simpan struk ini sebagai bukti ---</p>
                        </div>
                    </body>
                </html>
            `;

            const printWindow = window.open('', '', 'height=600,width=400');
            printWindow.document.write(printContent);
            printWindow.document.close();
            printWindow.focus();
            setTimeout(() => {
                printWindow.print();
                printWindow.close();
            }, 500);
        },
        filterType: 'all',
        filterFloor: 'all',
        searchQuery: '',
        
        // Toast Notification State
        showToast: false,
        toastMessage: '',
        toastType: 'success', // success, error, info
        
        showNotification(message, type = 'success') {
            this.toastMessage = message;
            this.toastType = type;
            this.showToast = true;
            setTimeout(() => {
                this.showToast = false;
            }, 3000);
        }
    }" class="space-y-8">
    
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
                'bg-[#0A0A0A] border-red-500/20 text-red-500': toastType === 'error',
                'bg-[#0A0A0A] border-[#D0B75B]/20 text-[#D0B75B]': toastType === 'info',
                'bg-[#0A0A0A] border-yellow-500/20 text-yellow-500': toastType === 'warning'
             }">
             
            <div class="p-2 rounded-full" 
                 :class="{
                    'bg-green-500/10': toastType === 'success',
                    'bg-red-500/10': toastType === 'error',
                    'bg-[#D0B75B]/10': toastType === 'info',
                    'bg-yellow-500/10': toastType === 'warning'
                 }">
                <template x-if="toastType === 'success'">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/><path d="m9 12 2 2 4-4"/></svg>
                </template>
                <template x-if="toastType === 'error'">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>
                </template>
                <template x-if="toastType === 'info'">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </template>
                <template x-if="toastType === 'warning'">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" x2="12" y1="9" y2="13"/><line x1="12" x2="12.01" y1="17" y2="17"/></svg>
                </template>
            </div>
            
            <div>
                <h4 class="text-sm font-bold uppercase tracking-wider" x-text="toastType === 'success' ? 'Berhasil' : (toastType === 'error' ? 'Gagal' : (toastType === 'warning' ? 'Peringatan' : 'Pemberitahuan'))"></h4>
                <p class="text-xs text-gray-400 font-medium mt-0.5" x-text="toastMessage"></p>
            </div>
            
            <button @click="showToast = false" class="ml-auto text-gray-500 hover:text-white transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
        </div>
    </template>
        
        {{-- Monitoring Status Ruangan Grid --}}
        <div>
            <div class="flex items-center gap-3 mb-6">
                <div class="p-2 bg-[#D0B75B]/10 rounded-lg">
                    <i data-lucide="monitor" class="w-5 h-5 text-[#D0B75B]"></i> 
                </div>
                <h2 class="text-xl font-black text-white tracking-tight" style="font-family: 'Inter';">Monitoring Status Real-time</h2>
            </div>

            {{-- Lantai 1 --}}
            <div class="mb-8">
                <h3 class="text-white font-bold text-sm uppercase tracking-widest mb-4 border-l-4 border-[#D0B75B] pl-3">Lantai 1</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-5 gap-3">
                    <template x-for="room in rooms.filter(r => r.lantai == 1)" :key="room.nama">
                        <div @click="openControl(room)" 
                             class="bg-[#080808] border rounded-xl p-3 text-center transition-all duration-300 hover:scale-[1.02] group relative overflow-hidden cursor-pointer h-full flex flex-col"
                             :class="room.status === 'Digunakan' ? (room.billing_mode === 'open' ? 'border-blue-500/30 hover:border-blue-500' : 'border-[#D0B75B]/30 hover:border-[#D0B75B]') : (room.status === 'Kosong' ? 'border-green-500/30 hover:border-green-500' : 'border-gray-600/30 hover:border-gray-500')">
                            
                            {{-- Background Glow --}}
                            <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-500"
                                 :class="room.status === 'Digunakan' ? (room.billing_mode === 'open' ? 'bg-blue-500/5' : 'bg-[#D0B75B]/5') : (room.status === 'Kosong' ? 'bg-green-500/5' : 'bg-gray-500/5')"></div>
    
                            <div class="relative z-10 flex flex-col h-full">
                                {{-- Icon --}}
                                <div class="w-10 h-10 mx-auto mb-2 flex items-center justify-center transition-all duration-300 group-hover:-translate-y-0.5"
                                     :class="room.status === 'Digunakan' ? (room.billing_mode === 'open' ? 'text-blue-500' : 'text-[#D0B75B]') : (room.status === 'Kosong' ? 'text-green-500' : 'text-gray-700')">
                                    <template x-if="room.status === 'Digunakan'"><div><i data-lucide="mic-2" class="w-6 h-6"></i></div></template>
                                    <template x-if="room.status === 'Kosong'"><div><i data-lucide="check-circle-2" class="w-6 h-6"></i></div></template>
                                    <template x-if="room.status === 'Cleaning'"><div><i data-lucide="sparkles" class="w-6 h-6 animate-pulse"></i></div></template>
                                </div>
                                
                                <p class="text-white text-sm font-black mb-1 group-hover:text-[#D0B75B] transition-colors" style="font-family: 'Inter';" x-text="room.nama"></p>
                                
                                {{-- Key Display --}}
                                <div class="flex items-center justify-center gap-1.5 mb-2 opacity-60">
                                    <i data-lucide="key" class="w-3 h-3 text-[#D0B75B]"></i>
                                    <span class="text-[10px] text-gray-300 font-mono tracking-wider" x-text="room.key || '-'"></span>
                                </div>
                                
                                <template x-if="room.status === 'Digunakan'">
                                    <div class="mt-2">
                                        <p class="text-[9px] text-gray-400 uppercase tracking-widest font-bold mb-0.5 truncate" x-text="room.tamu || 'Tamu'"></p>
                                        <div class="text-sm font-mono font-black" 
                                             :class="room.billing_mode === 'paket' ? (room.hampir_habis ? 'text-red-500 animate-pulse' : 'text-white') : 'text-blue-500'">
                                             <span x-text="room.billing_mode === 'paket' ? formatTimeArray(room.sisa_detik) : formatTimeArray(room.durasi_berjalan)"></span>
                                        </div>
                                    </div>
                                </template>
    
                                <template x-if="room.status === 'Kosong'">
                                    <div class="mt-2">
                                        <div class="text-sm font-black text-white">
                                            <span x-text="room.kapasitas"></span> <span class="text-[9px] text-gray-500 font-normal">Orang</span>
                                        </div>
                                        <p class="text-[8px] text-green-500 uppercase tracking-widest mt-0.5 font-bold">Siap</p>
                                    </div>
                                </template>
                                
                                 <template x-if="room.status === 'Cleaning'">
                                    <div class="mt-2">
                                        <div class="text-[10px] font-black text-gray-400">CLEANING</div>
                                    </div>
                                </template>

                                {{-- Action Button Mockup --}}
                                <div class="mt-auto pt-2 border-t border-white/5 opacity-80 group-hover:opacity-100 transition-opacity">
                                    <span class="text-[9px] font-bold uppercase tracking-widest flex items-center justify-center gap-1"
                                          :class="room.status === 'Digunakan' ? (room.billing_mode === 'open' ? 'text-blue-500' : 'text-[#D0B75B]') : (room.status === 'Kosong' ? 'text-green-500' : 'text-gray-500')">
                                        <i data-lucide="settings-2" class="w-3 h-3"></i> Kelola
                                    </span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Lantai 2 --}}
            <div>
                 <h3 class="text-white font-bold text-sm uppercase tracking-widest mb-4 border-l-4 border-[#D0B75B] pl-3">Lantai 2</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-5 gap-3">
                    <template x-for="room in rooms.filter(r => r.lantai == 2)" :key="room.nama">
                        <div @click="openControl(room)" 
                             class="bg-[#080808] border rounded-xl p-3 text-center transition-all duration-300 hover:scale-[1.02] group relative overflow-hidden cursor-pointer h-full flex flex-col"
                             :class="room.status === 'Digunakan' ? (room.billing_mode === 'open' ? 'border-blue-500/30 hover:border-blue-500' : 'border-[#D0B75B]/30 hover:border-[#D0B75B]') : (room.status === 'Kosong' ? 'border-green-500/30 hover:border-green-500' : 'border-gray-600/30 hover:border-gray-500')">
                            
                             <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-500"
                                  :class="room.status === 'Digunakan' ? (room.billing_mode === 'open' ? 'bg-blue-500/5' : 'bg-[#D0B75B]/5') : (room.status === 'Kosong' ? 'bg-green-500/5' : 'bg-gray-500/5')"></div>
    
                            <div class="relative z-10 flex flex-col h-full">
                                <div class="w-10 h-10 mx-auto mb-2 flex items-center justify-center transition-all duration-300 group-hover:-translate-y-0.5"
                                     :class="room.status === 'Digunakan' ? (room.billing_mode === 'open' ? 'text-blue-500' : 'text-[#D0B75B]') : (room.status === 'Kosong' ? 'text-green-500' : 'text-gray-700')">
                                    <template x-if="room.status === 'Digunakan'"><div><i data-lucide="mic-2" class="w-6 h-6"></i></div></template>
                                    <template x-if="room.status === 'Kosong'"><div><i data-lucide="check-circle-2" class="w-6 h-6"></i></div></template>
                                    <template x-if="room.status === 'Cleaning'"><div><i data-lucide="sparkles" class="w-6 h-6 animate-pulse"></i></div></template>
                                </div>
                                
                                <p class="text-white text-sm font-black mb-1 group-hover:text-[#D0B75B] transition-colors" style="font-family: 'Inter';" x-text="room.nama"></p>
                                
                                {{-- Key Display --}}
                                <div class="flex items-center justify-center gap-1.5 mb-2 opacity-60">
                                    <i data-lucide="key" class="w-3 h-3 text-[#D0B75B]"></i>
                                    <span class="text-[10px] text-gray-300 font-mono tracking-wider" x-text="room.key || '-'"></span>
                                </div>

                                <template x-if="room.status === 'Digunakan'">
                                    <div class="mt-2">
                                        <p class="text-[9px] text-gray-400 uppercase tracking-widest font-bold mb-0.5 truncate" x-text="room.tamu || 'Tamu'"></p>
                                        <div class="text-sm font-mono font-black" 
                                             :class="room.billing_mode === 'paket' ? (room.hampir_habis ? 'text-red-500 animate-pulse' : 'text-white') : 'text-blue-500'">
                                             <span x-text="room.billing_mode === 'paket' ? formatTimeArray(room.sisa_detik) : formatTimeArray(room.durasi_berjalan)"></span>
                                        </div>
                                    </div>
                                </template>
    
                                <template x-if="room.status === 'Kosong'">
                                    <div class="mt-2">
                                        <div class="text-sm font-black text-white">
                                            <span x-text="room.kapasitas"></span> <span class="text-[9px] text-gray-500 font-normal">Orang</span>
                                        </div>
                                        <p class="text-[8px] text-green-500 uppercase tracking-widest mt-0.5 font-bold">Siap</p>
                                    </div>
                                </template>
                                
                                 <template x-if="room.status === 'Cleaning'">
                                    <div class="mt-2">
                                        <div class="text-[10px] font-black text-gray-400">CLEANING</div>
                                    </div>
                                </template>

                                <div class="mt-auto pt-2 border-t border-white/5 opacity-80 group-hover:opacity-100 transition-opacity">
                                    <span class="text-[9px] font-bold uppercase tracking-widest flex items-center justify-center gap-1"
                                          :class="room.status === 'Digunakan' ? (room.billing_mode === 'open' ? 'text-blue-500' : 'text-[#D0B75B]') : (room.status === 'Kosong' ? 'text-green-500' : 'text-gray-500')">
                                        <i data-lucide="settings-2" class="w-3 h-3"></i> Kelola
                                    </span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>


        <div class="bg-[#080808] border border-white/5 rounded-2xl overflow-hidden">
            <div class="px-8 py-6 border-b border-white/5 bg-black/20 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <i data-lucide="home" class="w-5 h-5 text-[#D0B75B]"></i> 
                    <h2 class="text-sm font-bold text-gray-200 uppercase tracking-widest" style="font-family: 'Inter';">Daftar Ruangan</h2>
                </div>
                <div class="flex items-center gap-3">
                    {{-- Search Input --}}
                    <div class="relative">
                        <input type="text" x-model="searchQuery" placeholder="Cari ruangan..." 
                               class="bg-black border border-white/10 text-white text-xs font-bold rounded-lg pl-9 pr-4 py-2.5 outline-none focus:border-[#D0B75B] w-48 placeholder:text-gray-600">
                        <i data-lucide="search" class="w-4 h-4 text-gray-500 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                    </div>

                    {{-- Filter Tipe --}}
                    <div class="relative">
                        <select x-model="filterType" class="bg-black border border-white/10 text-white text-xs font-bold uppercase tracking-wider rounded-lg px-4 py-2.5 outline-none focus:border-[#D0B75B] appearance-none cursor-pointer pr-10">
                            <option value="all">Semua Tipe</option>
                            <option value="Regular">Regular</option>
                            <option value="VIP">VIP</option>
                            <option value="VVIP">VVIP</option>
                            <option value="Suite">Suite</option>
                            <option value="Party">Party</option>
                        </select>
                        <i data-lucide="chevron-down" class="w-4 h-4 text-gray-500 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                    </div>

                    {{-- Filter Lantai --}}
                    <div class="relative">
                        <select x-model="filterFloor" class="bg-black border border-white/10 text-white text-xs font-bold uppercase tracking-wider rounded-lg px-4 py-2.5 outline-none focus:border-[#D0B75B] appearance-none cursor-pointer pr-10">
                            <option value="all">Semua Lantai</option>
                            <option value="1">Lantai 1</option>
                            <option value="2">Lantai 2</option>
                        </select>
                        <i data-lucide="chevron-down" class="w-4 h-4 text-gray-500 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                    </div>

                    <button @click="showAddModal = true" class="text-[10px] font-black uppercase tracking-[0.2em] bg-[#D0B75B] text-black px-4 py-2 rounded-lg hover:bg-[#e0c86b] transition-all flex items-center gap-2">
                        <i data-lucide="plus" class="w-4 h-4"></i> Tambah Ruangan
                    </button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs" style="font-family: 'Inter';">
                    <thead>
                         <tr class="text-gray-600 text-[9px] font-black uppercase tracking-[0.25em] bg-black/40">
                            <th class="text-left px-8 py-4">Ruangan</th>
                            <th class="text-left px-8 py-4">Lantai</th>
                            <th class="text-left px-8 py-4">Tipe</th>
                            <th class="text-center px-8 py-4">Kapasitas</th>
                            <th class="text-right px-8 py-4">Harga Weekday</th>
                            <th class="text-right px-8 py-4">Harga Weekend</th>
                            <th class="text-center px-8 py-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <template x-for="(room, index) in rooms.filter(r => (filterType === 'all' || r.tipe === filterType) && (filterFloor === 'all' || r.lantai == filterFloor) && (searchQuery === '' || r.nama.toLowerCase().includes(searchQuery.toLowerCase())))" :key="index">
                            <tr class="hover:bg-white/[0.01] transition-colors group">
                                <td class="px-8 py-5">
                                    <div class="flex flex-col">
                                        <span class="text-white font-black text-sm" x-text="room.nama"></span>
                                        <span class="text-[9px] text-gray-500 uppercase tracking-widest">ID: #<span x-text="(index+1).toString().padStart(3, '0')"></span></span>
                                    </div>
                                </td>
                                <td class="px-8 py-5">
                                    <span class="text-gray-400 font-bold" x-text="'Lantai ' + room.lantai"></span>
                                </td>
                                <td class="px-8 py-5">
                                    <span class="bg-white/5 px-3 py-1 rounded-lg text-gray-300 font-bold border border-white/5" x-text="room.tipe"></span>
                                </td>
                                <td class="px-8 py-5 text-center text-gray-400">
                                    <span x-text="room.kapasitas"></span> Orang
                                </td>
                                <td class="px-8 py-5 text-right font-mono text-gray-300" x-text="formatMoney(room.harga_weekday)"></td>
                                <td class="px-8 py-5 text-right font-mono text-[#D0B75B]" x-text="formatMoney(room.harga_weekend)"></td>
                                <td class="px-8 py-5 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button @click="openControl(room)" class="w-8 h-8 rounded-lg bg-white/5 text-gray-400 hover:bg-[#D0B75B] hover:text-black transition-all flex items-center justify-center">
                                            <i data-lucide="settings-2" class="w-4 h-4"></i>
                                        </button>
                                        <button class="w-8 h-8 rounded-lg bg-white/5 text-gray-400 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center">
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

        {{-- Add Room Modal --}}
        <template x-teleport="body">
            <div x-show="showAddModal" style="display: none;" 
                 class="fixed inset-0 z-[99] flex items-center justify-center bg-black/80 backdrop-blur-xl p-4" x-transition.opacity>
                <div @click.away="showAddModal = false" class="bg-[#0A0A0A] border border-white/10 rounded-2xl w-full max-w-md overflow-hidden relative">
                     <div class="px-6 py-4 border-b border-white/5 bg-black/20 flex justify-between items-center">
                        <h3 class="text-white font-bold">Tambah Ruangan Baru</h3>
                        <button @click="showAddModal = false"><i data-lucide="x" class="w-5 h-5 text-gray-500 hover:text-white transition-colors"></i></button>
                     </div>
                     <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-xs uppercase font-bold text-gray-500 mb-2">Nama Ruangan</label>
                            <input type="text" x-model="newRoom.nama" class="w-full bg-zinc-900 border border-white/10 rounded-lg px-4 py-2 text-white focus:border-[#D0B75B] outline-none" placeholder="Contoh: VIP 05">
                        </div>
                        <div>
                            <label class="block text-xs uppercase font-bold text-gray-500 mb-2">Tipe</label>
                            <select x-model="newRoom.tipe" class="w-full bg-zinc-900 border border-white/10 rounded-lg px-4 py-2 text-white focus:border-[#D0B75B] outline-none">
                                <option>Regular</option>
                                <option>VIP</option>
                                <option>VVIP</option>
                                <option>Suite</option>
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                             <div>
                                <label class="block text-xs uppercase font-bold text-gray-500 mb-2">Kapasitas</label>
                                <input type="number" x-model="newRoom.kapasitas" class="w-full bg-zinc-900 border border-white/10 rounded-lg px-4 py-2 text-white focus:border-[#D0B75B] outline-none">
                            </div>
                            <div>
                                <label class="block text-xs uppercase font-bold text-gray-500 mb-2">Lantai</label>
                                <select x-model="newRoom.lantai" class="w-full bg-zinc-900 border border-white/10 rounded-lg px-4 py-2 text-white focus:border-[#D0B75B] outline-none">
                                    <option value="1">Lantai 1</option>
                                    <option value="2">Lantai 2</option>
                                </select>
                            </div>
                        </div>
                        <div>
                             <label class="block text-xs uppercase font-bold text-gray-500 mb-2">Harga / Jam</label>
                             <input type="number" x-model="newRoom.harga_weekday" class="w-full bg-zinc-900 border border-white/10 rounded-lg px-4 py-2 text-white focus:border-[#D0B75B] outline-none">
                        </div>
                     </div>
                     <div class="px-6 py-4 border-t border-white/5 bg-zinc-900/30 flex justify-end">
                        <button @click="addRoom" class="bg-[#D0B75B] text-black font-bold px-6 py-2 rounded-lg hover:bg-[#e0c86b] transition-all">Simpan</button>
                     </div>
                </div>
            </div>
        </template>

        {{-- Control Modal (Check In / Checkout) --}}
        <template x-teleport="body">
            <div x-show="showControlModal" style="display: none;" 
                 class="fixed inset-0 z-[99] flex items-center justify-center bg-black/80 backdrop-blur-xl p-4" x-transition.opacity>
                <div @click.away="showControlModal = false" class="bg-[#0A0A0A] border border-white/10 rounded-2xl w-full max-w-lg overflow-hidden relative">
                 <template x-if="selectedRoom">
                     <div>
                        {{-- Header --}}
                        <div class="px-6 py-6 border-b border-white/5 bg-black/20 flex justify-between items-start">
                             <div>
                                <div class="flex items-center gap-3 mb-1">
                                    <h3 class="text-2xl font-black text-white" x-text="selectedRoom.nama"></h3>
                                    <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest border"
                                          :class="selectedRoom.status === 'Kosong' ? 'bg-white/10 text-gray-300 border-white/10' : 'bg-[#D0B75B]/10 text-[#D0B75B] border-[#D0B75B]/20'"
                                          x-text="selectedRoom.status">
                                    </span>
                                </div>
                                <p class="text-gray-500 text-xs uppercase tracking-wider font-bold" x-text="selectedRoom.tipe + ' Room'"></p>
                             </div>
                             <button @click="showControlModal = false"><i data-lucide="x" class="w-6 h-6 text-gray-500 hover:text-white"></i></button>
                        </div>

                        {{-- Body: Check In Form --}}
                        <div x-show="selectedRoom.status === 'Kosong'" class="p-6 space-y-6">
                            {{-- Billing Mode Selection --}}
                            <div class="grid grid-cols-3 gap-2 p-1 bg-[#080808] border border-white/5 rounded-xl">
                                <button @click="bookingForm.mode = 'paket'; bookingForm.selectedPackage = null" 
                                        class="py-2.5 rounded-lg text-[10px] font-bold uppercase tracking-wider transition-all"
                                        :class="bookingForm.mode === 'paket' ? 'bg-[#D0B75B] text-black' : 'text-gray-500 hover:text-white'">
                                    Paket Per Jam
                                </button>
                                <button @click="bookingForm.mode = 'khusus'; bookingForm.selectedPackage = null" 
                                        class="py-2.5 rounded-lg text-[10px] font-bold uppercase tracking-wider transition-all"
                                        :class="bookingForm.mode === 'khusus' ? 'bg-[#D0B75B] text-black' : 'text-gray-500 hover:text-white'">
                                    Paket Khusus
                                </button>
                                <button @click="bookingForm.mode = 'open'" 
                                        class="py-2.5 rounded-lg text-[10px] font-bold uppercase tracking-wider transition-all"
                                        :class="bookingForm.mode === 'open' ? 'bg-[#D0B75B] text-black' : 'text-gray-500 hover:text-white'">
                                    Open Billing
                                </button>
                            </div>

                            {{-- Form Fields --}}
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-[10px] uppercase font-bold text-gray-500 mb-1.5 tracking-wider">Nama Tamu</label>
                                    <div class="relative">
                                        <i data-lucide="user" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500"></i>
                                        <input type="text" x-model="bookingForm.tamu" class="w-full bg-black/40 border border-white/10 rounded-xl pl-10 pr-4 py-3 text-white focus:border-[#D0B75B] outline-none placeholder-gray-700" placeholder="Masukkan nama tamu...">
                                    </div>
                                </div>

                                {{-- Main Content Area with Smooth Crossfade --}}
                                <div class="relative grid grid-cols-1">
                                    
                                    {{-- Paket Per Jam --}}
                                    <div class="col-start-1 row-start-1 transition-all duration-300 ease-in-out"
                                         :class="bookingForm.mode === 'paket' ? 'opacity-100 z-10 translate-x-0' : 'opacity-0 pointer-events-none z-0 -translate-x-4'">
                                        <label class="block text-[10px] uppercase font-bold text-gray-500 mb-1.5 tracking-wider">Durasi (Jam)</label>
                                        <div class="grid grid-cols-4 gap-2">
                                            <button @click="bookingForm.durasi = 1" class="border border-white/10 rounded-lg py-2 text-xs hover:border-[#D0B75B] hover:text-[#D0B75B] transition-colors" :class="bookingForm.durasi == 1 ? 'border-[#D0B75B] text-[#D0B75B] bg-[#D0B75B]/10' : 'text-gray-400'">1 Jam</button>
                                            <button @click="bookingForm.durasi = 2" class="border border-white/10 rounded-lg py-2 text-xs hover:border-[#D0B75B] hover:text-[#D0B75B] transition-colors" :class="bookingForm.durasi == 2 ? 'border-[#D0B75B] text-[#D0B75B] bg-[#D0B75B]/10' : 'text-gray-400'">2 Jam</button>
                                            <button @click="bookingForm.durasi = 3" class="border border-white/10 rounded-lg py-2 text-xs hover:border-[#D0B75B] hover:text-[#D0B75B] transition-colors" :class="bookingForm.durasi == 3 ? 'border-[#D0B75B] text-[#D0B75B] bg-[#D0B75B]/10' : 'text-gray-400'">3 Jam</button>
                                            <input type="number" x-model="bookingForm.durasi" class="bg-black/40 border border-white/10 rounded-lg text-center text-white text-xs focus:border-[#D0B75B] outline-none" placeholder="Custom">
                                        </div>
                                        
                                        {{-- Estimated Price Display --}}
                                        <div class="mt-3 p-3 bg-[#D0B75B]/10 border border-[#D0B75B]/20 rounded-lg flex justify-between items-center">
                                            <span class="text-[#D0B75B] text-xs font-bold uppercase">Estimasi Harga</span>
                                            <span class="text-white font-black text-lg" x-text="formatMoney(bookingForm.durasi * selectedRoom.harga_weekday)"></span>
                                        </div>
                                    </div>

                                    {{-- Paket Khusus --}}
                                    <div class="col-start-1 row-start-1 transition-all duration-300 ease-in-out"
                                         :class="bookingForm.mode === 'khusus' ? 'opacity-100 z-10 translate-x-0' : 'opacity-0 pointer-events-none z-0 translate-x-4'">
                                        <label class="block text-[10px] uppercase font-bold text-gray-500 mb-1.5 tracking-wider">Pilih Paket</label>
                                        <div class="space-y-2 max-h-[200px] overflow-y-auto pr-1">
                                            <template x-for="(paket, idx) in packages" :key="paket.nama">
                                                <button @click="bookingForm.selectedPackage = idx; bookingForm.durasi = parseInt(paket.durasi)" 
                                                        class="w-full text-left p-3 rounded-xl border transition-all"
                                                        :class="bookingForm.selectedPackage === idx ? 'border-[#D0B75B] bg-[#D0B75B]/10' : 'border-white/10 hover:border-white/20 bg-black/20'">
                                                    <div class="flex justify-between items-center">
                                                        <div>
                                                            <p class="text-white font-bold text-sm" x-text="paket.nama"></p>
                                                            <p class="text-gray-500 text-[10px] font-bold" x-text="paket.durasi"></p>
                                                        </div>
                                                        <div class="text-right">
                                                            <p class="text-[#D0B75B] font-black text-sm" x-text="formatMoney(paket.harga_weekday)"></p>
                                                            <p class="text-gray-500 text-[9px]">weekday</p>
                                                        </div>
                                                    </div>
                                                </button>
                                            </template>
                                        </div>
                                        
                                        {{-- Estimated Price Display --}}
                                        <div x-show="bookingForm.selectedPackage !== null" class="mt-3 p-3 bg-[#D0B75B]/10 border border-[#D0B75B]/20 rounded-lg flex justify-between items-center">
                                            <span class="text-[#D0B75B] text-xs font-bold uppercase">Estimasi Harga</span>
                                            <span class="text-white font-black text-lg" x-text="bookingForm.selectedPackage !== null ? formatMoney(packages[bookingForm.selectedPackage].harga_weekday) : ''"></span>
                                        </div>
                                    </div>

                                    {{-- Open Billing --}}
                                    <div class="col-start-1 row-start-1 transition-all duration-300 ease-in-out"
                                         :class="bookingForm.mode === 'open' ? 'opacity-100 z-10 translate-x-0' : 'opacity-0 pointer-events-none z-0 translate-x-4'">
                                        <div class="p-4 bg-blue-500/10 border border-blue-500/20 rounded-xl h-full flex items-center justify-center min-h-[120px]">
                                            <div class="text-center">
                                                 <div class="w-10 h-10 bg-blue-500/20 rounded-full flex items-center justify-center mx-auto mb-2">
                                                    <i data-lucide="infinity" class="w-5 h-5 text-blue-400"></i>
                                                 </div>
                                                 <p class="text-blue-400 font-bold text-sm">Open Billing Mode</p>
                                                 <p class="text-blue-400/60 text-[10px] mt-1">Billing akan berjalan terus hingga di-stop.</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>

                            <button @click="handleStartButton" class="w-full bg-[#D0B75B] text-black font-black uppercase tracking-widest py-4 rounded-xl hover:bg-[#e0c86b] mt-4 flex items-center justify-center gap-2">
                                <template x-if="bookingForm.mode === 'paket' || bookingForm.mode === 'khusus'">
                                    <div class="flex items-center gap-2">
                                         <i data-lucide="file-text" class="w-5 h-5"></i>
                                         <span>Preview Struk</span>
                                    </div>
                                </template>
                                <template x-if="bookingForm.mode === 'open'">
                                    <div class="flex items-center gap-2">
                                         <i data-lucide="play" class="w-5 h-5"></i>
                                         <span>Mulai Sesi</span>
                                    </div>
                                </template>
                            </button>
                        </div>

                        {{-- Body: Active Session Control --}}
                        <div x-show="selectedRoom.status === 'Digunakan'" class="p-6 space-y-6">
                            <div class="flex items-center justify-between p-4 bg-zinc-900/50 rounded-xl border border-white/5">
                                <div>
                                    <p class="text-[10px] text-gray-500 uppercase tracking-widest font-bold mb-1">Billing Mode</p>
                                    <p class="text-white font-bold" x-text="selectedRoom.billing_mode === 'paket' ? 'Paket Per Jam' : selectedRoom.billing_mode === 'khusus' ? 'Paket Khusus' : 'Open Billing'"></p>
                                </div>
                                <div class="text-right">
                                    <p class="text-[10px] text-gray-500 uppercase tracking-widest font-bold mb-1">Durasi</p>
                                    <p class="font-mono font-bold text-lg" 
                                       :class="(selectedRoom.billing_mode === 'paket' || selectedRoom.billing_mode === 'khusus') && selectedRoom.sisa_detik <= 0 ? 'text-red-500 animate-pulse' : 'text-[#D0B75B]'"
                                       x-text="(selectedRoom.billing_mode === 'paket' || selectedRoom.billing_mode === 'khusus') ? formatTimeArray(selectedRoom.sisa_detik) : formatTimeArray(selectedRoom.durasi_berjalan)"></p>
                                </div>
                            </div>

                            <template x-if="selectedRoom.billing_mode === 'paket' || selectedRoom.billing_mode === 'khusus'">
                                <div class="grid grid-cols-2 gap-3">
                                    <button @click="showExtendModal = true; extendForm.durasi = 1;" class="bg-[#D0B75B] text-black font-black uppercase tracking-widest py-4 rounded-xl hover:bg-[#c4a94f] flex items-center justify-center gap-2">
                                        <i data-lucide="plus-circle" class="w-5 h-5"></i> Tambah Waktu
                                    </button>
                                    <button @click="checkout" class="bg-red-500 text-white font-black uppercase tracking-widest py-4 rounded-xl hover:bg-red-600 flex items-center justify-center gap-2">
                                        <i data-lucide="log-out" class="w-5 h-5"></i> Checkout
                                    </button>
                                </div>
                            </template>
                            <template x-if="selectedRoom.billing_mode === 'open'">
                                <button @click="checkout" class="w-full bg-red-500 text-white font-black uppercase tracking-widest py-4 rounded-xl hover:bg-red-600">
                                    Stop Sesi &amp; Checkout
                                </button>
                            </template>
                        </div>
                     </div>
                 </template>
            </div>
        </div>
        </template>

        {{-- Bill Summary Modal --}}
        <template x-teleport="body">
            <div x-show="showBillModal" style="display: none;" 
                 class="fixed inset-0 z-[100] flex items-center justify-center bg-black/90 backdrop-blur-xl p-4" x-transition.opacity>
            <div @click.away="showBillModal = false" class="bg-white text-black rounded-3xl w-full max-w-sm overflow-hidden relative">
                 <div class="p-8 text-center bg-[#D0B75B]">
                     <div class="w-16 h-16 bg-black rounded-full flex items-center justify-center mx-auto mb-4">
                         <i data-lucide="check" class="w-8 h-8 text-[#D0B75B]"></i>
                     </div>
                     <h2 class="text-2xl font-black uppercase tracking-tight">Pembayaran</h2>
                     <p class="opacity-70 font-medium">Transaksi Berhasil</p>
                 </div>
                 <div class="p-8 space-y-4 bg-gray-50">
                     <template x-if="activeBill">
                         <div>
                             <div class="flex justify-between py-2 border-b border-gray-200">
                                 <span class="text-gray-500 text-xs font-bold uppercase">Ruangan</span>
                                 <span class="font-bold" x-text="activeBill.roomName"></span>
                             </div>
                             <div class="flex justify-between py-2 border-b border-gray-200">
                                 <span class="text-gray-500 text-xs font-bold uppercase">Tamu</span>
                                 <span class="font-bold" x-text="activeBill.tamu"></span>
                             </div>
                             <div class="flex justify-between py-2 border-b border-gray-200">
                                 <span class="text-gray-500 text-xs font-bold uppercase">Waktu</span>
                                 <span class="font-bold text-xs"><span x-text="activeBill.masuk"></span> - <span x-text="activeBill.keluar"></span></span>
                             </div>
                             <div class="flex justify-between py-2 border-b border-gray-200">
                                 <span class="text-gray-500 text-xs font-bold uppercase">Durasi</span>
                                 <span class="font-bold" x-text="activeBill.durasi"></span>
                             </div>
                             <div class="flex justify-between py-4 mt-2">
                                 <span class="text-gray-900 font-black text-lg">TOTAL</span>
                                 <span class="text-[#D0B75B] font-black text-2xl" x-text="formatMoney(activeBill.total)"></span>
                             </div>
                         </div>
                     </template>
                     <div class="grid grid-cols-2 gap-3 mt-4">
                        <button @click="showBillModal = false" class="w-full bg-gray-200 text-gray-800 font-bold py-3 rounded-xl hover:bg-gray-300">
                            Tutup
                        </button>
                        <button @click="showReceiptPreview = true" class="w-full bg-black text-white font-bold py-3 rounded-xl hover:bg-gray-900 flex items-center justify-center gap-2">
                            <i data-lucide="printer" class="w-4 h-4"></i> Cetak Struk
                        </button>
                    </div>
                 </div>
            </div>
        </div>
    </template>

    {{-- Receipt Preview Modal --}}
    <template x-teleport="body">
        <div x-show="showReceiptPreview" style="display: none;" 
             class="fixed inset-0 z-[110] flex items-center justify-center bg-black/90 backdrop-blur-xl p-4" x-transition.opacity>
            <div class="bg-white text-black p-6 rounded-sm w-full max-w-[320px] shadow-2xl relative">
                <div class="text-center font-mono text-sm leading-tight border-b border-dashed border-gray-400 pb-4 mb-4">
                    <h3 class="font-bold text-lg mb-1">SGRT KARAOKE</h3>
                    <p class="text-xs">Jl. Hiburan Malam No. 99</p>
                    <p class="text-xs">Telp: 021-555-999</p>
                </div>
                
                <template x-if="activeBill">
                    <div class="space-y-2 font-mono text-xs">
                        <div class="flex justify-between"><span>Ruangan:</span> <span x-text="activeBill.roomName"></span></div>
                        <div class="flex justify-between"><span>Tamu:</span> <span x-text="activeBill.tamu"></span></div>
                        <div class="flex justify-between"><span>Masuk:</span> <span x-text="activeBill.masuk"></span></div>
                        <div class="flex justify-between"><span>Keluar:</span> <span x-text="activeBill.keluar"></span></div>
                        <div class="flex justify-between"><span>Durasi:</span> <span x-text="activeBill.durasi"></span></div>
                        
                        <div class="border-t border-dashed border-gray-400 my-4 pt-4 flex justify-between font-bold text-sm">
                            <span>TOTAL</span>
                            <span x-text="formatMoney(activeBill.total)"></span>
                        </div>
                    </div>
                </template>

                <div class="text-center font-mono text-[10px] mt-6 border-t border-dashed border-gray-400 pt-4 opacity-70">
                    <p>Terima Kasih atas Kunjungan Anda!</p>
                    <p>--- Simpan struk ini sebagai bukti ---</p>
                </div>

                <div class="mt-8 grid grid-cols-2 gap-3">
                    <button @click="showReceiptPreview = false" class="bg-gray-200 text-gray-800 text-xs font-bold py-2 rounded hover:bg-gray-300">Tutup</button>
                    <button @click="printReceipt()" class="bg-black text-white text-xs font-bold py-2 rounded hover:bg-gray-900 flex items-center justify-center gap-2">
                        <i data-lucide="printer" class="w-3 h-3"></i> Cetak
                    </button>
                </div>
            </div>
        </div>
    </template>

    {{-- Start Session Receipt Preview Modal --}}
    <template x-teleport="body">
        <div x-show="showStartReceiptPreview" style="display: none;" 
             class="fixed inset-0 z-[110] flex items-center justify-center bg-black/90 backdrop-blur-xl p-4" x-transition.opacity>
            <div class="bg-white text-black p-6 rounded-sm w-full max-w-[320px] shadow-2xl relative">
                <div class="text-center font-mono text-sm leading-tight border-b border-dashed border-gray-400 pb-4 mb-4">
                    <h3 class="font-bold text-lg mb-1">SGRT KARAOKE</h3>
                    <p class="text-xs">Jl. Hiburan Malam No. 99</p>
                    <p class="text-xs">Telp: 021-555-999</p>
                </div>
                
                <template x-if="sessionReceiptData">
                    <div class="space-y-2 font-mono text-xs">
                        <div class="text-center font-bold uppercase mb-2">Struk Mulai Sesi</div>
                        <div class="flex justify-between"><span>Tanggal:</span> <span x-text="sessionReceiptData.tanggal"></span></div>
                        <div class="flex justify-between"><span>Ruangan:</span> <span x-text="sessionReceiptData.roomName"></span></div>
                        <div class="flex justify-between"><span>Tamu:</span> <span x-text="sessionReceiptData.tamu"></span></div>
                        
                        <div class="border-t border-dashed border-gray-400 my-2"></div>
                        <div class="text-center font-bold text-lg my-2" x-text="sessionReceiptData.masuk + ' -> ' + sessionReceiptData.keluar"></div>
                        
                        <div class="flex justify-between"><span>Durasi:</span> <span x-text="sessionReceiptData.durasi"></span></div>
                        
                        <div class="border-t border-dashed border-gray-400 my-4 pt-4 flex justify-between font-bold text-sm">
                            <span>Estimasi</span>
                            <span x-text="sessionReceiptData.estimasi"></span>
                        </div>
                    </div>
                </template>

                <div class="text-center font-mono text-[10px] mt-6 border-t border-dashed border-gray-400 pt-4 opacity-70">
                    <p>Selamat Berkaraoke!</p>
                    <p>--- Simpan struk ini sebagai bukti ---</p>
                </div>

                <div class="mt-8 grid grid-cols-2 gap-3">
                    <button @click="showStartReceiptPreview = false" class="bg-gray-200 text-gray-800 text-xs font-bold py-2 rounded hover:bg-gray-300">Batal</button>
                    <button @click="executeStartSession(true)" class="bg-[#D0B75B] text-black text-xs font-bold py-2 rounded hover:bg-[#e0c86b] flex items-center justify-center gap-2">
                        <i data-lucide="printer" class="w-3 h-3"></i> Data Benar, Cetak
                    </button>
                </div>
            </div>
        </div>
    </template>

    {{-- Manajemen Paket --}}
        <div class="bg-[#080808] border border-white/5 rounded-2xl overflow-hidden">
            <div class="px-8 py-6 border-b border-white/5 bg-black/20 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <i data-lucide="package" class="w-5 h-5 text-[#D0B75B]"></i> 
                    <h2 class="text-sm font-bold text-gray-200 uppercase tracking-widest" style="font-family: 'Inter';">Manajemen Paket & Harga</h2>
                </div>
                <div class="flex items-center gap-3">
                     <button @click="showAddPackageModal = true" class="text-[10px] font-black uppercase tracking-[0.2em] bg-[#D0B75B] text-black px-4 py-2 rounded-lg hover:bg-[#e0c86b] transition-all flex items-center gap-2">
                        <i data-lucide="plus" class="w-4 h-4"></i> Tambah Paket
                    </button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs" style="font-family: 'Inter';">
                    <thead>
                         <tr class="text-gray-600 text-[9px] font-black uppercase tracking-[0.25em] bg-black/40">
                            <th class="text-left px-8 py-4">Nama Paket</th>
                            <th class="text-left px-8 py-4">Durasi</th>
                            <th class="text-right px-8 py-4">Harga Weekday</th>
                            <th class="text-right px-8 py-4">Harga Weekend</th>
                            <th class="text-center px-8 py-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <template x-for="(paket, index) in packages" :key="paket.nama">
                        <tr class="hover:bg-white/[0.01] transition-colors">
                            <td class="px-8 py-5 text-white font-black" x-text="paket.nama"></td>
                            <td class="px-8 py-5 text-gray-400 font-bold italic" x-text="paket.durasi"></td>
                            <td class="px-8 py-5 text-right font-mono text-gray-300" x-text="formatMoney(paket.harga_weekday)"></td>
                            <td class="px-8 py-5 text-right font-mono text-[#D0B75B] font-black" x-text="formatMoney(paket.harga_weekend)"></td>
                            <td class="px-8 py-5 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button @click="openEditPackage(index)" class="w-8 h-8 rounded-lg bg-white/5 text-gray-400 hover:bg-[#D0B75B] hover:text-black transition-all flex items-center justify-center" title="Edit">
                                        <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                    </button>
                                    <button @click="deletePackage(index)" class="w-8 h-8 rounded-lg bg-white/5 text-gray-400 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center" title="Hapus">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        </template>
                    </tbody>
                </table>
            </div>
            </div>

        {{-- Add Package Modal --}}
        <template x-teleport="body">
            <div x-show="showAddPackageModal" style="display: none;" 
                 class="fixed inset-0 z-[99] flex items-center justify-center bg-black/80 backdrop-blur-xl p-4" x-transition.opacity>
                <div @click.away="showAddPackageModal = false" class="bg-[#0A0A0A] border border-white/10 rounded-2xl w-full max-w-lg overflow-hidden relative">
                     <div class="px-6 py-4 border-b border-white/5 bg-black/20 flex justify-between items-center">
                        <h3 class="text-white font-bold">Tambah Paket Baru</h3>
                        <button @click="showAddPackageModal = false"><i data-lucide="x" class="w-5 h-5 text-gray-500 hover:text-white transition-colors"></i></button>
                     </div>
                     <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-xs uppercase font-bold text-gray-500 mb-2">Nama Paket</label>
                            <input type="text" x-model="newPackage.nama" class="w-full bg-zinc-900 border border-white/10 rounded-lg px-4 py-2 text-white focus:border-[#D0B75B] outline-none" placeholder="Contoh: Paket Malam Minggu">
                        </div>
                         <div>
                            <label class="block text-xs uppercase font-bold text-gray-500 mb-2">Durasi (Jam)</label>
                            <input type="number" x-model="newPackage.durasi" class="w-full bg-zinc-900 border border-white/10 rounded-lg px-4 py-2 text-white focus:border-[#D0B75B] outline-none" placeholder="Contoh: 2">
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs uppercase font-bold text-gray-500 mb-2">Harga Weekday</label>
                                <input type="number" x-model="newPackage.harga_weekday" class="w-full bg-zinc-900 border border-white/10 rounded-lg px-4 py-2 text-white focus:border-[#D0B75B] outline-none" placeholder="Contoh: 90000">
                            </div>
                            <div>
                                <label class="block text-xs uppercase font-bold text-gray-500 mb-2">Harga Weekend</label>
                                <input type="number" x-model="newPackage.harga_weekend" class="w-full bg-zinc-900 border border-white/10 rounded-lg px-4 py-2 text-white focus:border-[#D0B75B] outline-none" placeholder="Contoh: 130000">
                            </div>
                        </div>

                     </div>
                     <div class="px-6 py-4 border-t border-white/5 bg-zinc-900/30 flex justify-end">
                        <button @click="addPackage" class="bg-[#D0B75B] text-black font-bold px-6 py-2 rounded-lg hover:bg-[#e0c86b] transition-all">Simpan Paket</button>
                     </div>
                </div>
            </div>
        </template>

        {{-- Edit Package Modal --}}
        <template x-teleport="body">
            <div x-show="showEditPackageModal" style="display: none;" 
                 class="fixed inset-0 z-[99] flex items-center justify-center bg-black/80 backdrop-blur-xl p-4" x-transition.opacity>
                <div @click.away="showEditPackageModal = false" class="bg-[#0A0A0A] border border-white/10 rounded-2xl w-full max-w-lg overflow-hidden relative">
                     <div class="px-6 py-4 border-b border-white/5 bg-black/20 flex justify-between items-center">
                        <h3 class="text-white font-bold">Edit Paket</h3>
                        <button @click="showEditPackageModal = false"><i data-lucide="x" class="w-5 h-5 text-gray-500 hover:text-white transition-colors"></i></button>
                     </div>
                     <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-xs uppercase font-bold text-gray-500 mb-2">Nama Paket</label>
                            <input type="text" x-model="editPackage.nama" class="w-full bg-zinc-900 border border-white/10 rounded-lg px-4 py-2 text-white focus:border-[#D0B75B] outline-none">
                        </div>
                         <div>
                            <label class="block text-xs uppercase font-bold text-gray-500 mb-2">Durasi (Jam)</label>
                            <input type="number" x-model="editPackage.durasi" class="w-full bg-zinc-900 border border-white/10 rounded-lg px-4 py-2 text-white focus:border-[#D0B75B] outline-none">
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs uppercase font-bold text-gray-500 mb-2">Harga Weekday</label>
                                <input type="number" x-model="editPackage.harga_weekday" class="w-full bg-zinc-900 border border-white/10 rounded-lg px-4 py-2 text-white focus:border-[#D0B75B] outline-none">
                            </div>
                            <div>
                                <label class="block text-xs uppercase font-bold text-gray-500 mb-2">Harga Weekend</label>
                                <input type="number" x-model="editPackage.harga_weekend" class="w-full bg-zinc-900 border border-white/10 rounded-lg px-4 py-2 text-white focus:border-[#D0B75B] outline-none">
                            </div>
                        </div>

                     </div>
                     <div class="px-6 py-4 border-t border-white/5 bg-zinc-900/30 flex justify-end">
                        <button @click="updatePackage" class="bg-[#D0B75B] text-black font-bold px-6 py-2 rounded-lg hover:bg-[#e0c86b] transition-all">Update Paket</button>
                     </div>
                </div>
            </div>
        </template>

        {{-- Extend Time Modal --}}
        <template x-teleport="body">
            <div x-show="showExtendModal" style="display: none;" 
                 class="fixed inset-0 z-[100] flex items-center justify-center bg-black/90 backdrop-blur-xl p-4" x-transition.opacity>
                <div @click.away="showExtendModal = false; showExtendReceiptPreview = false;" class="bg-[#0A0A0A] border border-white/10 rounded-2xl w-full max-w-md overflow-hidden relative">
                    
                    {{-- Header --}}
                    <div class="px-6 py-4 border-b border-white/5 bg-black/20 flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-[#D0B75B]/20 rounded-lg flex items-center justify-center">
                                <i data-lucide="plus-circle" class="w-5 h-5 text-[#D0B75B]"></i>
                            </div>
                            <div>
                                <h3 class="text-white font-bold">Tambah Waktu</h3>
                                <p class="text-xs text-gray-500" x-text="selectedRoom ? selectedRoom.nama : ''"></p>
                            </div>
                        </div>
                        <button @click="showExtendModal = false; showExtendReceiptPreview = false;"><i data-lucide="x" class="w-5 h-5 text-gray-500 hover:text-white transition-colors"></i></button>
                    </div>

                    {{-- Body: Duration Picker --}}
                    <div x-show="!showExtendReceiptPreview" class="p-6 space-y-5">
                        <div class="bg-zinc-900/50 p-4 rounded-xl border border-white/5">
                            <p class="text-[10px] text-gray-500 uppercase tracking-widest font-bold mb-1">Sisa Waktu Saat Ini</p>
                            <p class="text-[#D0B75B] font-mono font-bold text-2xl" 
                               :class="selectedRoom && selectedRoom.sisa_detik <= 0 ? 'text-red-500 animate-pulse' : 'text-[#D0B75B]'"
                               x-text="selectedRoom ? formatTimeArray(selectedRoom.sisa_detik) : '00:00:00'"></p>
                        </div>

                        <div>
                            <label class="block text-xs uppercase font-bold text-gray-500 mb-2">Tambah Durasi (Jam)</label>
                            <div class="flex items-center gap-3">
                                <button @click="extendForm.durasi = Math.max(1, extendForm.durasi - 1)" class="w-12 h-12 bg-zinc-800 border border-white/10 rounded-xl text-white font-bold text-xl hover:bg-zinc-700">-</button>
                                <input type="number" x-model.number="extendForm.durasi" min="1" max="12" class="flex-1 bg-zinc-900 border border-white/10 rounded-xl px-4 py-3 text-white text-center text-2xl font-mono font-bold focus:border-[#D0B75B] outline-none">
                                <button @click="extendForm.durasi = Math.min(12, extendForm.durasi + 1)" class="w-12 h-12 bg-zinc-800 border border-white/10 rounded-xl text-white font-bold text-xl hover:bg-zinc-700">+</button>
                            </div>
                        </div>

                        <div class="bg-zinc-900/50 p-4 rounded-xl border border-white/5">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400 text-sm">Biaya Tambahan</span>
                                <span class="text-[#D0B75B] font-black text-xl" x-text="selectedRoom ? formatMoney(extendForm.durasi * selectedRoom.harga_weekday) : 'Rp 0'"></span>
                            </div>
                        </div>

                        <button @click="handleExtendButton()" class="w-full bg-[#D0B75B] text-black font-black uppercase tracking-widest py-4 rounded-xl hover:bg-[#c4a94f] flex items-center justify-center gap-2">
                            <i data-lucide="receipt" class="w-5 h-5"></i> Preview Struk
                        </button>
                    </div>
                </div>
            </div>
        </template>

        {{-- Extend Receipt Preview Modal (separate full-screen like Start Session Receipt) --}}
        <template x-teleport="body">
            <div x-show="showExtendReceiptPreview" style="display: none;" 
                 class="fixed inset-0 z-[110] flex items-center justify-center bg-black/90 backdrop-blur-xl p-4" x-transition.opacity>
                <div class="bg-white text-black p-6 rounded-sm w-full max-w-[320px] shadow-2xl relative">
                    <div class="text-center font-mono text-sm leading-tight border-b border-dashed border-gray-400 pb-4 mb-4">
                        <h3 class="font-bold text-lg mb-1">SGRT KARAOKE</h3>
                        <p class="text-xs">Jl. Hiburan Malam No. 99</p>
                        <p class="text-xs">Telp: 021-555-999</p>
                    </div>
                    
                    <template x-if="extendReceiptData">
                        <div class="space-y-2 font-mono text-xs">
                            <div class="text-center font-bold uppercase mb-2">Struk Tambah Waktu</div>
                            <div class="flex justify-between"><span>Tanggal:</span> <span x-text="extendReceiptData.tanggal"></span></div>
                            <div class="flex justify-between"><span>Ruangan:</span> <span x-text="extendReceiptData.roomName"></span></div>
                            <div class="flex justify-between"><span>Tamu:</span> <span x-text="extendReceiptData.tamu"></span></div>
                            
                            <div class="border-t border-dashed border-gray-400 my-2"></div>
                            
                            <div class="flex justify-between"><span>Tambah Durasi:</span> <span x-text="extendReceiptData.durasi"></span></div>
                            
                            <div class="border-t border-dashed border-gray-400 my-4 pt-4 flex justify-between font-bold text-sm">
                                <span>Biaya</span>
                                <span x-text="extendReceiptData.biaya"></span>
                            </div>
                        </div>
                    </template>

                    <div class="text-center font-mono text-[10px] mt-6 border-t border-dashed border-gray-400 pt-4 opacity-70">
                        <p>Selamat Berkaraoke!</p>
                        <p>--- Simpan struk ini sebagai bukti ---</p>
                    </div>

                    <div class="mt-8 grid grid-cols-2 gap-3">
                        <button @click="showExtendReceiptPreview = false" class="bg-gray-200 text-gray-800 text-xs font-bold py-2 rounded hover:bg-gray-300">Batal</button>
                        <button @click="executeExtendSession(true)" class="bg-[#D0B75B] text-black text-xs font-bold py-2 rounded hover:bg-[#e0c86b] flex items-center justify-center gap-2">
                            <i data-lucide="printer" class="w-3 h-3"></i> Data Benar, Cetak
                        </button>
                    </div>
                </div>
            </div>
        </template>

    </div>
@endsection
