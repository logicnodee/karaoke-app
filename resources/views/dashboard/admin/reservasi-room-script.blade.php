
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('bookingSystem', () => ({
            bookings: @json($daftarBooking),
            showModal: false,
            newBooking: { tamu: '', ruangan: 'VIP 01', tanggal: '', jam: '', durasi: 1, kontak: '', status: 'Menunggu' },
            showReceiptPreview: false,
            receiptData: null,
            showToast: false,
            toastMessage: '',
            toastType: 'success',
            
            // Filter State
            filters: {
                date: '',
                room: 'Semua Ruangan',
                status: 'Semua Status'
            },

            // Computed Filtered List
            get filteredBookings() {
                return this.bookings.filter(booking => {
                    // Filter by Room
                    if (this.filters.room !== 'Semua Ruangan' && booking.ruangan !== this.filters.room) {
                        return false;
                    }

                    // Filter by Status
                    if (this.filters.status !== 'Semua Status' && booking.status !== this.filters.status) {
                        return false;
                    }

                    // Filter by Date
                    if (this.filters.date) {
                        // Compare YYYY-MM-DD
                        // booking.raw_date should be available from controller
                        if (booking.raw_date && booking.raw_date !== this.filters.date) {
                            return false;
                        }
                    }

                    return true;
                });
            },

            showNotification(message, type = 'success') {
                this.toastMessage = message;
                this.toastType = type;
                this.showToast = true;
                setTimeout(() => this.showToast = false, 3000);
            },

            prepareBooking() {
                if(!this.newBooking.tamu || !this.newBooking.tanggal || !this.newBooking.jam) {
                    this.showNotification('Data tamu, tanggal, dan jam wajib diisi!', 'error');
                    return;
                }

                const rawDate = new Date(this.newBooking.tanggal);
                const formattedDate = rawDate.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });

                this.receiptData = {
                    tamu: this.newBooking.tamu,
                    ruangan: this.newBooking.ruangan,
                    tanggal: formattedDate,
                    jam: this.newBooking.jam,
                    durasi: this.newBooking.durasi + ' Jam',
                    kontak: this.newBooking.kontak || '-'
                };

                this.showReceiptPreview = true;
            },

            processBooking(shouldPrint) {
                const rawDateObj = new Date(this.newBooking.tanggal);
                const formattedDate = rawDateObj.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
                
                // Format YYYY-MM-DD for raw_date consistency
                const year = rawDateObj.getFullYear();
                const month = String(rawDateObj.getMonth() + 1).padStart(2, '0');
                const day = String(rawDateObj.getDate()).padStart(2, '0');
                const rawDateString = `${year}-${month}-${day}`;

                this.bookings.push({
                    tamu: this.newBooking.tamu,
                    ruangan: this.newBooking.ruangan,
                    tanggal: formattedDate,
                    raw_date: this.newBooking.tanggal, // Input is already YYYY-MM-DD
                    jam: this.newBooking.jam,
                    durasi: this.newBooking.durasi,
                    kontak: this.newBooking.kontak || '-',
                    status: 'Menunggu'
                });

                if (shouldPrint) {
                    this.printReceipt(this.receiptData);
                }

                this.showNotification('Reservasi berhasil disimpan' + (shouldPrint ? ' & struk dicetak!' : '!'), 'success');
                this.showModal = false;
                this.showReceiptPreview = false;
                this.newBooking = { tamu: '', ruangan: 'VIP 01', tanggal: '', jam: '', durasi: 1, kontak: '', status: 'Menunggu' };
            },

            printReceipt(data) {
                const printContent = `
                    <html>
                        <head>
                            <title>Bukti Reservasi - ${data.tamu}</title>
                            <style>
                                body { font-family: 'Courier New', monospace; padding: 20px; max-width: 300px; margin: 0 auto; background: #fff; color: #000; }
                                .header { text-align: center; margin-bottom: 15px; border-bottom: 2px dashed black; padding-bottom: 10px; }
                                .header h3 { margin: 0 0 5px 0; font-size: 18px; }
                                .header p { margin: 2px 0; font-size: 10px; }
                                .section-title { text-align: center; font-size: 12px; font-weight: bold; margin: 10px 0; text-transform: uppercase; letter-spacing: 2px; background: #f0f0f0; padding: 5px; }
                                .item { display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 12px; }
                                .divider { border-top: 1px dashed black; margin: 10px 0; }
                                .footer { text-align: center; margin-top: 15px; font-size: 10px; border-top: 2px dashed black; padding-top: 10px; }
                                .code { text-align: center; font-size: 16px; font-weight: bold; margin: 10px 0; letter-spacing: 3px; }
                            </style>
                        </head>
                        <body>
                            <div class='header'>
                                <h3>SGRT KARAOKE</h3>
                                <p>Jl. Hiburan Malam No. 99</p>
                                <p>Telp: 021-555-999</p>
                            </div>
                            <div class='section-title'>BUKTI RESERVASI</div>
                            <div class='item'><span>Tanggal:</span> <span>${data.tanggal}</span></div>
                            <div class='item'><span>Ruangan:</span> <span>${data.ruangan}</span></div>
                            <div class='divider'></div>
                            <div class='item'><span>Nama Tamu:</span> <span>${data.tamu}</span></div>
                            <div class='item'><span>Kontak:</span> <span>${data.kontak}</span></div>
                            <div class='divider'></div>
                            <div class='code'>${data.jam}</div>
                            <div class='item' style="justify-content: center;"><span>(Durasi: ${data.durasi})</span></div>
                            <div class='footer'>
                                <p>Harap datang 10 menit sebelum jadwal</p>
                                <p>--- Tunjukkan struk ini saat check-in ---</p>
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

            updateStatus(booking, status) {
                // Pass booking object directly instead of index
                booking.status = status;
                const message = status === 'Terkonfirmasi' ? 'Reservasi berhasil dikonfirmasi (Check-in).' : 'Reservasi dibatalkan.';
                const type = status === 'Terkonfirmasi' ? 'success' : 'error';
                this.showNotification(message, type);
            },

            init() {
                this.$nextTick(() => { if(window.lucide) window.lucide.createIcons(); });
                this.$watch('bookings', () => {
                    this.$nextTick(() => { if(window.lucide) window.lucide.createIcons(); });
                });
                this.$watch('filters', () => {
                     // Since we use a computed property in x-for, no manual re-filter needed, 
                     // but might want to re-init icons if list changes drastically? 
                     // Alpine handles DOM diffing, so icons might persist or not. 
                     // Safest to re-init icons if needed.
                     this.$nextTick(() => { if(window.lucide) window.lucide.createIcons(); });
                });
            }
        }));
    });
</script>
