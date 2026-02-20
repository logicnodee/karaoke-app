@extends('layouts.dashboard')

@section('dashboard-role', 'Admin Panel')
@section('dashboard-role-icon', 'shield-check')

@section('title', 'Manajemen Absensi - Admin Dashboard')
@section('page-title', 'Manajemen Absensi')

@section('sidebar-nav')
    @include('dashboard.admin._sidebar', ['active' => 'manajemen-absensi'])
@endsection

@section('dashboard-content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
    <div x-data="{
        showModal: false,
        isEditing: false,
        activeTab: 'summary',
        
        // Filter & Pagination for Attendance
        filterMonth: '2026-02',
        filterDay: '',
        filterStatus: '',
        searchHistory: '',
        attPage: 1,
        attPerPage: 10,

        // Attendance History Data - Monthly (Generated)
        attendanceList: (function() {
            const emps = [
                { nama: 'Admin Utama', role: 'Admin', shift: 'Pagi', ip: '192.168.1.10' },
                { nama: 'Sari Putri', role: 'Kasir', shift: 'Pagi', ip: '192.168.1.15' },
                { nama: 'Budi Santoso', role: 'Operator', shift: 'Siang', ip: '192.168.1.22' },
                { nama: 'Rahmawati', role: 'Kasir', shift: 'Malam', ip: '192.168.1.30' },
                { nama: 'Dedi Kurniawan', role: 'Operator', shift: 'Pagi', ip: '192.168.1.25' },
                { nama: 'Siti Aminah', role: 'Kasir', shift: 'Siang', ip: '192.168.1.18' }
            ];
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            const list = [];
            const shiftTimes = { 'Pagi': ['08','16'], 'Siang': ['14','22'], 'Malam': ['22','06'] };

            // Feb 2026 - hari 1-17
            for (let d = 17; d >= 1; d--) {
                const dayOfWeek = new Date(2026, 1, d).getDay();
                if (dayOfWeek === 0) continue; // Skip Minggu
                emps.forEach(emp => {
                    const rand = Math.random();
                    let status, statusColor, statusBg, checkIn, checkOut;
                    const st = shiftTimes[emp.shift];

                    if (rand < 0.70) {
                        status = 'Hadir';
                        statusColor = 'text-green-500';
                        statusBg = 'bg-green-500/10 border-green-500/20';
                        const m = String(Math.floor(Math.random() * 10)).padStart(2, '0');
                        checkIn = st[0] + ':' + m;
                        const m2 = String(Math.floor(Math.random() * 30)).padStart(2, '0');
                        checkOut = st[1] + ':' + m2;
                    } else if (rand < 0.85) {
                        status = 'Terlambat';
                        statusColor = 'text-yellow-500';
                        statusBg = 'bg-yellow-500/10 border-yellow-500/20';
                        const m = String(15 + Math.floor(Math.random() * 45)).padStart(2, '0');
                        checkIn = st[0] + ':' + m;
                        const m2 = String(Math.floor(Math.random() * 30)).padStart(2, '0');
                        checkOut = st[1] + ':' + m2;
                    } else {
                        status = 'Absen';
                        statusColor = 'text-red-500';
                        statusBg = 'bg-red-500/10 border-red-500/20';
                        checkIn = '-';
                        checkOut = '-';
                    }

                    list.push({
                        nama: emp.nama,
                        role: emp.role,
                        date: d + ' Feb 2026',
                        dateKey: '2026-02',
                        dateDay: d,
                        shift: emp.shift,
                        check_in: checkIn,
                        check_out: checkOut,
                        ip: status === 'Absen' ? '-' : emp.ip,
                        status: status,
                        status_color: statusColor,
                        status_bg: statusBg
                    });
                });
            }

            // Jan 2026 - hari 26-31
            for (let d = 31; d >= 26; d--) {
                emps.forEach(emp => {
                    const rand = Math.random();
                    let status, statusColor, statusBg, checkIn, checkOut;
                    const st = shiftTimes[emp.shift];
                    if (rand < 0.75) {
                        status = 'Hadir'; statusColor = 'text-green-500'; statusBg = 'bg-green-500/10 border-green-500/20';
                        checkIn = st[0] + ':0' + Math.floor(Math.random()*9); checkOut = st[1] + ':' + String(Math.floor(Math.random()*30)).padStart(2,'0');
                    } else if (rand < 0.88) {
                        status = 'Terlambat'; statusColor = 'text-yellow-500'; statusBg = 'bg-yellow-500/10 border-yellow-500/20';
                        checkIn = st[0] + ':' + String(20+Math.floor(Math.random()*30)).padStart(2,'0'); checkOut = st[1] + ':' + String(Math.floor(Math.random()*30)).padStart(2,'0');
                    } else {
                        status = 'Absen'; statusColor = 'text-red-500'; statusBg = 'bg-red-500/10 border-red-500/20';
                        checkIn = '-'; checkOut = '-';
                    }
                    list.push({ nama: emp.nama, role: emp.role, date: d + ' Jan 2026', dateKey: '2026-01', dateDay: d, shift: emp.shift, check_in: checkIn, check_out: checkOut, ip: status === 'Absen' ? '-' : emp.ip, status, status_color: statusColor, status_bg: statusBg });
                });
            }
            return list;
        })(),

        // Computed: available months from data
        get monthOptions() {
             // Create unique list of 'YYYY-MM' from data
             const keys = [...new Set(this.attendanceList.map(l => l.dateKey))];
             // Return formatted objects or just keys, here we return keys sorted descending
             return keys.sort().reverse(); 
        },

        // Computed: available days for selected month (or all if no month selected)
        get dayOptions() {
            let list = this.attendanceList;
            if (this.filterMonth) {
                list = list.filter(l => l.dateKey === this.filterMonth);
            }
            const days = [...new Set(list.map(l => l.dateDay))];
            return days.sort((a, b) => a - b);
        },

        // Calendar Logic
        calendarOpen: false,
        viewYear: 2026,
        viewMonth: 1, // 0-indexed (1 = Feb)
        
        get formattedFilterDate() {
            if (!this.filterMonth) return 'Semua Waktu';
            
            const [y, m] = this.filterMonth.split('-').map(Number);
            const date = new Date(y, m - 1);
            const monthName = date.toLocaleDateString('id-ID', { month: 'long', year: 'numeric' });
            
            if (this.filterDay) {
                return this.filterDay + ' ' + monthName;
            }
            return monthName;
        },

        get calendarDays() {
            const year = this.viewYear;
            const month = this.viewMonth; // 0-indexed
            
            const firstDay = new Date(year, month, 1).getDay(); // 0 = Sunday
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            const daysInPrevMonth = new Date(year, month, 0).getDate();
            
            let days = [];
            
            // Previous month padding
            for (let i = firstDay; i > 0; i--) {
                days.push({ day: daysInPrevMonth - i + 1, type: 'prev' });
            }
            
            // Current month days
            for (let i = 1; i <= daysInMonth; i++) {
                days.push({ day: i, type: 'current' });
            }
            
            // Next month padding (to fill 42 grid)
            const remaining = 42 - days.length;
            for (let i = 1; i <= remaining; i++) {
                days.push({ day: i, type: 'next' });
            }
            
            return days;
        },

        initCalendar() {
            // Sync view with current filter or today
            if (this.filterMonth) {
                const [y, m] = this.filterMonth.split('-');
                this.viewYear = parseInt(y);
                this.viewMonth = parseInt(m) - 1;
            } else {
                const now = new Date();
                this.viewYear = now.getFullYear();
                this.viewMonth = now.getMonth();
            }
        },

        prevMonth() {
            this.viewMonth--;
            if (this.viewMonth < 0) {
                this.viewMonth = 11;
                this.viewYear--;
            }
            this.updateFilterMonthFromView();
        },

        nextMonth() {
            this.viewMonth++;
            if (this.viewMonth > 11) {
                this.viewMonth = 0;
                this.viewYear++;
            }
            this.updateFilterMonthFromView();
        },

        updateFilterMonthFromView() {
            const m = String(this.viewMonth + 1).padStart(2, '0');
            this.filterMonth = `${this.viewYear}-${m}`;
            this.filterDay = ''; // Reset day when changing month
        },

        selectDate(day) {
            this.filterDay = day;
            this.calendarOpen = false;
        },

        clearDateFilter() {
             this.filterDay = '';
             this.calendarOpen = false;
        },

        setToday() {
            const now = new Date();
            this.viewYear = now.getFullYear();
            this.viewMonth = now.getMonth();
            this.filterMonth = `${this.viewYear}-${String(this.viewMonth+1).padStart(2, '0')}`;
            this.filterDay = now.getDate();
            this.calendarOpen = false;
        },

        // Computed: filter by month only (for stats cards)
        get monthFilteredList() {
            let r = this.attendanceList;
            if (this.filterMonth) r = r.filter(l => l.dateKey === this.filterMonth);
            return r;
        },

        // Computed: full filter (month + day + status + search)
        get filteredAttendance() {
            let result = this.monthFilteredList;
            if (this.filterDay) result = result.filter(l => l.dateDay == this.filterDay);
            if (this.filterStatus) result = result.filter(l => l.status === this.filterStatus);
            if (this.searchHistory.trim()) {
                const q = this.searchHistory.toLowerCase();
                result = result.filter(l => l.nama.toLowerCase().includes(q) || l.role.toLowerCase().includes(q));
            }
            return result;
        },

        get attTotalPages() { return Math.max(1, Math.ceil(this.filteredAttendance.length / this.attPerPage)); },
        get paginatedAttendance() { return this.filteredAttendance.slice((this.attPage-1)*this.attPerPage, this.attPage*this.attPerPage); },

        exportPdf() {
            const data = this.filteredAttendance;
            if (data.length === 0) return;

            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('l', 'mm', 'a4');
            const pw = doc.internal.pageSize.getWidth();
            const ph = doc.internal.pageSize.getHeight();
            const today = new Date();
            const dateStr = today.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
            const timeStr = today.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

            // Header
            const margin = 10;
            doc.setFillColor(10, 10, 10);
            doc.rect(0, 0, pw, 35, 'F');
            doc.setFillColor(208, 183, 91);
            doc.rect(0, 35, pw, 1.5, 'F');

            doc.setTextColor(208, 183, 91);
            doc.setFontSize(16);
            doc.setFont('helvetica', 'bold');
            doc.text('SGRT KARAOKE', margin, 16);
            doc.setTextColor(180, 180, 180);
            doc.setFontSize(8);
            doc.setFont('helvetica', 'normal');
            doc.text('Sing & Joy Premium Karaoke', margin, 23);

            doc.setTextColor(255, 255, 255);
            doc.setFontSize(13);
            doc.setFont('helvetica', 'bold');
            doc.text('LAPORAN ABSENSI BULANAN', pw - margin, 16, { align: 'right' });
            
            // Filter Info Header
            let filterTexts = [];
            if (this.filterMonth) filterTexts.push('Bulan: ' + this.filterMonth);
            if (this.filterDay) filterTexts.push('Tanggal: ' + this.filterDay);
            if (this.filterStatus) filterTexts.push('Status: ' + this.filterStatus);
            if (this.searchHistory) filterTexts.push('Pencarian: ' + this.searchHistory);
            
            const filterString = filterTexts.length ? filterTexts.join(' | ') : 'Semua Data';

            doc.setTextColor(150, 150, 150);
            doc.setFontSize(7);
            doc.setFont('helvetica', 'normal');
            doc.text('Filter: ' + filterString, pw - margin, 22, { align: 'right' });
            doc.text('Dicetak: ' + dateStr + ' ' + timeStr + '  |  Total: ' + data.length + ' record', pw - margin, 25, { align: 'right' });

            const hadir = data.filter(l => l.status === 'Hadir').length;
            const terlambat = data.filter(l => l.status === 'Terlambat').length;
            const absen = data.filter(l => l.status === 'Absen').length;
            
            // Stats Box
            doc.setDrawColor(255, 255, 255);
            doc.setLineWidth(0.1);
            doc.line(pw - 80, 29, pw - margin, 29);
            doc.text('Hadir: ' + hadir + '  |  Terlambat: ' + terlambat + '  |  Absen: ' + absen, pw - margin, 33, { align: 'right' });

            const tableData = data.map((log, i) => [
                (i + 1).toString(),
                log.nama,
                log.role,
                log.date,
                log.shift,
                log.check_in,
                log.check_out,
                log.ip,
                log.status
            ]);

            doc.autoTable({
                head: [['No', 'Karyawan', 'Role', 'Tanggal', 'Shift', 'Masuk', 'Keluar', 'IP Address', 'Status']],
                body: tableData,
                startY: 40,
                margin: { left: margin, right: margin },
                theme: 'grid',
                styles: { fontSize: 8, cellPadding: 3, textColor: [50, 50, 50], lineColor: [220, 220, 220], lineWidth: 0.2 },
                headStyles: { fillColor: [30, 30, 30], textColor: [208, 183, 91], fontStyle: 'bold', fontSize: 8, halign: 'center' },
                alternateRowStyles: { fillColor: [248, 248, 248] },
                columnStyles: {
                    0: { halign: 'center', cellWidth: 10 },
                    1: { fontStyle: 'bold' }, // Auto width
                    2: { cellWidth: 25 },
                    3: { cellWidth: 25 },
                    4: { cellWidth: 15, halign: 'center' },
                    5: { cellWidth: 20, halign: 'center', font: 'courier' },
                    6: { cellWidth: 20, halign: 'center', font: 'courier' },
                    7: { cellWidth: 35, halign: 'center', fontSize: 7, font: 'courier' },
                    8: { cellWidth: 22, halign: 'center', fontStyle: 'bold' }
                },
                didDrawPage: function(d) {
                    doc.setFillColor(245, 245, 245);
                    doc.rect(0, ph - 12, pw, 12, 'F');
                    doc.setFillColor(208, 183, 91);
                    doc.rect(0, ph - 12, pw, 0.5, 'F');
                    doc.setTextColor(130, 130, 130);
                    doc.setFontSize(6);
                    doc.setFont('helvetica', 'normal');
                    doc.text('SGRT Karaoke - Laporan Absensi Bulanan | Digenerate otomatis oleh sistem', margin, ph - 5);
                    doc.text('Halaman ' + d.pageNumber, pw - margin, ph - 5, { align: 'right' });
                }
            });

            // Dynamic Filename
            let filenameParts = ['laporan_absensi'];
            if (this.filterMonth) filenameParts.push(this.filterMonth);
            if (this.filterDay) filenameParts.push('tgl' + this.filterDay);
            if (this.searchHistory) filenameParts.push(this.searchHistory.replace(/\s+/g, '_').toLowerCase());
            
            doc.save(filenameParts.join('_') + '.pdf');
        },

        // Dummy Shifts
        shifts: [
            { id: 1, nama: 'Pagi', mulai: '08:00', selesai: '16:00', toleransi: 15 },
            { id: 2, nama: 'Siang', mulai: '14:00', selesai: '22:00', toleransi: 15 },
            { id: 3, nama: 'Malam', mulai: '22:00', selesai: '06:00', toleransi: 15 }
        ],
        
        // Dummy Employee Assignments
        assignments: [
            { nama: 'Admin Utama', shift: 'Pagi', check_in: '08:00', check_out: '16:00', toleransi: 15 },
            { nama: 'Sari Putri', shift: 'Pagi', check_in: '08:00', check_out: '16:00', toleransi: 15 },
            { nama: 'Budi Santoso', shift: 'Siang', check_in: '14:00', check_out: '22:00', toleransi: 15 },
            { nama: 'Rahmawati', shift: 'Malam', check_in: '22:00', check_out: '06:00', toleransi: 15 },
            { nama: 'Dedi Kurniawan', shift: 'Pagi', check_in: '08:00', check_out: '16:00', toleransi: 15 },
            { nama: 'Siti Aminah', shift: 'Siang', check_in: '14:00', check_out: '22:00', toleransi: 15 },
        ],
        
        newShift: { id: null, nama: '', mulai: '', selesai: '', toleransi: 15 },
        employees: ['Admin Utama', 'Sari Putri', 'Budi Santoso', 'Rahmawati', 'Dedi Kurniawan', 'Siti Aminah'],

        openAddShiftModal() {
            this.isEditing = false;
            this.newShift = { id: null, nama: '', mulai: '08:00', selesai: '16:00', toleransi: 15 };
            this.showModal = true;
        },

        editShift(shift) {
            this.isEditing = true;
            this.newShift = { ...shift };
            this.showModal = true;
        },

        saveShift() {
            if (this.isEditing) {
                const index = this.shifts.findIndex(s => s.id === this.newShift.id);
                if (index !== -1) {
                    this.shifts[index] = { ...this.newShift };
                }
            } else {
                this.shifts.push({ ...this.newShift, id: Date.now() });
            }
            this.showModal = false;
        },
        
        // Assignment State
        showAssignModal: false,
        assignForm: { nama: '', shift_id: '' },
        
        openAssignModal(existingAssignment = null) {
            if (existingAssignment) {
                this.assignForm = { 
                    nama: existingAssignment.nama, 
                    shift_id: this.shifts.find(s => s.nama === existingAssignment.shift)?.id || ''
                };
            } else {
                this.assignForm = { nama: '', shift_id: '' };
            }
            this.showAssignModal = true;
        },

        saveAssignment() {
            const selectedShift = this.shifts.find(s => s.id == this.assignForm.shift_id);
            if (!selectedShift || !this.assignForm.nama) return;

            const idx = this.assignments.findIndex(a => a.nama === this.assignForm.nama);
            
            const newAssignment = {
                nama: this.assignForm.nama,
                shift: selectedShift.nama,
                check_in: selectedShift.mulai,
                check_out: selectedShift.selesai,
                toleransi: selectedShift.toleransi
            };

            if (idx !== -1) {
                this.assignments[idx] = newAssignment;
            } else {
                this.assignments.push(newAssignment);
            }
            
            this.showAssignModal = false;
        },

        init() {
            this.$watch('filterMonth', () => { this.attPage = 1; }); // remove this.filterDay reset here as it's handled in prevMonth/nextMonth or init
            this.$watch('filterDay', () => { this.attPage = 1; });
            this.$watch('filterStatus', () => { this.attPage = 1; });
            this.$watch('searchHistory', () => { this.attPage = 1; });
            this.$watch('attPerPage', () => { this.attPage = 1; });
            
            this.initCalendar();
        }
    }">
        {{-- Navigation Tabs --}}
        <div class="flex items-center gap-4 mb-6 border-b border-white/10 pb-4 overflow-x-auto">
            <button @click="activeTab = 'summary'" 
                   :class="activeTab === 'summary' ? 'text-[#D0B75B] border-b-2 border-[#D0B75B]' : 'text-gray-400 hover:text-white'"
                   class="pb-1 text-sm font-semibold transition-colors px-2 whitespace-nowrap">
                Ringkasan & Riwayat
            </button>
            <button @click="activeTab = 'shift'" 
                   :class="activeTab === 'shift' ? 'text-[#D0B75B] border-b-2 border-[#D0B75B]' : 'text-gray-400 hover:text-white'"
                   class="pb-1 text-sm font-semibold transition-colors px-2 whitespace-nowrap">
                Jadwal Shift
            </button>
            <button @click="activeTab = 'karyawan'" 
                   :class="activeTab === 'karyawan' ? 'text-[#D0B75B] border-b-2 border-[#D0B75B]' : 'text-gray-400 hover:text-white'"
                   class="pb-1 text-sm font-semibold transition-colors px-2 whitespace-nowrap">
                Penugasan Karyawan
            </button>
        </div>

        {{-- Summary & History Tab --}}
        <div x-show="activeTab === 'summary'" x-transition>
            {{-- Stats Cards - Computed from filtered data --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
                <div class="bg-[#080808] border border-white/5 rounded-xl p-3">
                    <div class="text-[10px] text-gray-500 uppercase font-bold tracking-widest mb-1">Total Karyawan</div>
                    <div class="flex items-end gap-1.5">
                        <h3 class="text-xl font-black text-white" x-text="employees.length"></h3>
                        <span class="text-[10px] text-gray-400 mb-0.5">orang</span>
                    </div>
                </div>
                <div class="bg-[#080808] border border-white/5 rounded-xl p-3">
                    <div class="text-[10px] text-gray-500 uppercase font-bold tracking-widest mb-1">Total Hadir</div>
                    <div class="flex items-end gap-1.5">
                        <h3 class="text-xl font-black text-green-500" x-text="monthFilteredList.filter(l => l.status === 'Hadir').length"></h3>
                        <span class="text-[10px] text-gray-400 mb-0.5">record</span>
                    </div>
                </div>
                <div class="bg-[#080808] border border-white/5 rounded-xl p-3">
                    <div class="text-[10px] text-gray-500 uppercase font-bold tracking-widest mb-1">Tidak Hadir</div>
                    <div class="flex items-end gap-1.5">
                        <h3 class="text-xl font-black text-red-500" x-text="monthFilteredList.filter(l => l.status === 'Absen').length"></h3>
                        <span class="text-[10px] text-gray-400 mb-0.5">record</span>
                    </div>
                </div>
                 <div class="bg-[#080808] border border-white/5 rounded-xl p-3">
                    <div class="text-[10px] text-gray-500 uppercase font-bold tracking-widest mb-1">Terlambat</div>
                    <div class="flex items-end gap-1.5">
                        <h3 class="text-xl font-black text-yellow-500" x-text="monthFilteredList.filter(l => l.status === 'Terlambat').length"></h3>
                        <span class="text-[10px] text-gray-400 mb-0.5">record</span>
                    </div>
                </div>
            </div>

            <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 mb-3">
                <div class="flex items-center gap-3">
                    <div>
                        <h3 class="text-white font-bold text-sm">Log Absensi Bulanan</h3>
                        <p class="text-[10px] text-gray-500 mt-0.5">Pantau kehadiran karyawan per bulan</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-wrap">

                    {{-- New Calendar Date Picker --}}
                    <div class="relative" @click.outside="calendarOpen = false">
                        <button @click="calendarOpen = !calendarOpen" 
                                class="bg-[#080808] border border-white/10 rounded-lg px-3 py-1.5 text-[10px] text-white hover:border-[#D0B75B] transition-colors flex items-center gap-2 min-w-[140px] justify-between">
                            <div class="flex items-center gap-2">
                                <i data-lucide="calendar" class="w-3.5 h-3.5 text-[#D0B75B]"></i>
                                <span x-text="formattedFilterDate"></span>
                            </div>
                            <i data-lucide="chevron-down" class="w-3 h-3 text-gray-500 transition-transform" :class="calendarOpen ? 'rotate-180' : ''"></i>
                        </button>

                        {{-- Calendar Dropdown --}}
                        <div x-show="calendarOpen" x-transition.origin.top.left
                             class="absolute top-full left-0 mt-2 bg-[#1A1A1A] border border-white/10 rounded-xl shadow-2xl p-4 w-[280px] z-50">
                            
                            {{-- Header --}}
                            <div class="flex items-center justify-between mb-4">
                                <button @click="prevMonth()" class="text-gray-400 hover:text-white p-1 rounded-full hover:bg-white/10 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                                </button>
                                <span class="text-white font-bold text-sm" x-text="new Date(viewYear, viewMonth).toLocaleDateString('en-US', { month: 'long', year: 'numeric' })"></span>
                                <button @click="nextMonth()" class="text-gray-400 hover:text-white p-1 rounded-full hover:bg-white/10 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                                </button>
                            </div>

                            {{-- Days Header --}}
                            <div class="grid grid-cols-7 mb-2">
                                <template x-for="d in ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa']">
                                    <div class="text-center text-[10px] font-bold text-gray-500" x-text="d"></div>
                                </template>
                            </div>

                            {{-- Dates Grid --}}
                            <div class="grid grid-cols-7 gap-1 mb-4">
                                <template x-for="(d, idx) in calendarDays" :key="idx">
                                    <button 
                                        :disabled="d.type !== 'current'"
                                        @click="d.type === 'current' && selectDate(d.day)"
                                        class="w-full aspect-square flex items-center justify-center rounded-lg text-xs transition-all relative group"
                                        :class="{
                                            'text-gray-600 cursor-default': d.type !== 'current',
                                            'text-white hover:bg-white/10': d.type === 'current' && filterDay != d.day,
                                            'bg-[#60a5fa] text-white font-bold shadow-lg shadow-blue-500/20': d.type === 'current' && filterDay == d.day
                                        }"
                                    >
                                        <span x-text="d.day"></span>
                                        {{-- Today Indicator Dot --}}
                                        <template x-if="d.type === 'current' && d.day === new Date().getDate() && viewMonth === new Date().getMonth() && viewYear === new Date().getFullYear() && filterDay != d.day">
                                             <div class="absolute bottom-1 w-1 h-1 bg-[#60a5fa] rounded-full"></div>
                                        </template>
                                    </button>
                                </template>
                            </div>

                            {{-- Footer --}}
                            <div class="flex items-center justify-between pt-3 border-t border-white/10">
                                <button @click="clearDateFilter()" class="text-[10px] text-gray-400 hover:text-white transition-colors">
                                    Clear
                                </button>
                                <button @click="setToday()" class="text-[10px] text-[#60a5fa] font-bold hover:text-blue-400 transition-colors">
                                    Today
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Status Filter --}}
                    <select x-model="filterStatus" class="bg-[#080808] border border-white/10 rounded-lg px-3 py-1.5 text-[10px] text-white focus:border-[#D0B75B] outline-none appearance-none cursor-pointer pr-7">
                        <option value="">Semua Status</option>
                        <option value="Hadir">Hadir</option>
                        <option value="Terlambat">Terlambat</option>
                        <option value="Absen">Absen</option>
                    </select>

                    {{-- Search --}}
                    <div class="relative">
                        <input type="text" x-model="searchHistory" placeholder="Cari karyawan..." 
                               class="bg-[#080808] border border-white/10 rounded-lg pl-8 pr-3 py-1.5 text-[10px] text-white focus:border-[#D0B75B] outline-none w-full md:w-40 transition-all">
                        <i data-lucide="search" class="w-3 h-3 text-gray-500 absolute left-2.5 top-1/2 -translate-y-1/2"></i>
                    </div>
                    
                    {{-- Export PDF --}}
                    <button @click="exportPdf()" class="bg-[#D0B75B] text-black font-bold text-[10px] px-3 py-1.5 rounded-lg hover:bg-[#e0c86b] transition-colors flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-3.5 h-3.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                        Export PDF
                    </button>
                </div>
            </div>

            <div class="bg-[#080808] border border-white/5 rounded-2xl overflow-hidden mb-8">
                <div class="overflow-x-auto">
                    <table class="w-full text-[10px] text-left">
                        <thead class="text-[9px] text-gray-500 font-bold uppercase tracking-widest bg-white/5 border-b border-white/5">
                            <tr>
                                <th class="px-3 py-2">Karyawan</th>
                                <th class="px-3 py-2">Tanggal</th>
                                <th class="px-3 py-2">Shift</th>
                                <th class="px-3 py-2">Masuk (Login)</th>
                                <th class="px-3 py-2">Keluar (Logout)</th>
                                <th class="px-3 py-2">IP Address</th>
                                <th class="px-3 py-2 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            <template x-for="(log, index) in paginatedAttendance" :key="index">
                                <tr class="hover:bg-white/[0.01] transition-colors">
                                    <td class="px-3 py-2">
                                        <div>
                                            <div class="text-white font-bold" x-text="log.nama"></div>
                                            <div class="text-[9px] text-gray-500 uppercase tracking-wider scale-90 origin-left" x-text="log.role"></div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-2 text-gray-400 font-medium" x-text="log.date"></td>
                                    <td class="px-3 py-2 text-gray-300 font-medium" x-text="log.shift || '-'"></td>
                                    <td class="px-3 py-2 text-white font-mono" x-text="log.check_in"></td>
                                    <td class="px-3 py-2 text-white font-mono" x-text="log.check_out"></td>
                                    <td class="px-3 py-2">
                                         <div class="flex items-center gap-1 opacity-70">
                                            <span class="text-[9px] font-mono text-gray-300" x-text="log.ip"></span>
                                        </div>
                                    </td>
                                    <td class="px-3 py-2 text-center">
                                        <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase tracking-widest border"
                                              :class="log.status_bg + ' ' + log.status_color"
                                              x-text="log.status">
                                        </span>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="filteredAttendance.length === 0">
                                <tr>
                                    <td colspan="7" class="px-3 py-8 text-center text-gray-500 text-xs">Tidak ada data absensi ditemukan</td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
                {{-- Pagination --}}
                <div class="px-4 py-3 bg-black/40 border-t border-white/5 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        {{-- Rows per page --}}
                        <div class="flex items-center gap-1.5">
                            <span class="text-[9px] text-gray-500">Baris:</span>
                            <select x-model.number="attPerPage" class="bg-black/50 border border-white/10 rounded px-2 py-0.5 text-[9px] text-white outline-none appearance-none cursor-pointer pr-5 focus:border-[#D0B75B]">
                                <option value="10">10</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                        <span class="text-[9px] text-gray-500" x-text="'Menampilkan ' + paginatedAttendance.length + ' dari ' + filteredAttendance.length + ' record'"></span>
                    </div>
                    <div class="flex gap-1" x-show="attTotalPages > 1">
                        <button @click="attPage > 1 && (attPage--)" :disabled="attPage === 1"
                                class="w-6 h-6 flex items-center justify-center rounded-lg bg-white/5 text-gray-400 hover:bg-white/10 hover:text-white transition-colors disabled:opacity-30 disabled:cursor-not-allowed">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-3 h-3"><polyline points="15 18 9 12 15 6"></polyline></svg>
                        </button>
                        <template x-for="p in attTotalPages" :key="p">
                            <button @click="attPage = p" class="w-6 h-6 rounded-lg font-bold text-[9px] transition-colors"
                                    :class="attPage === p ? 'bg-[#D0B75B] text-black' : 'bg-transparent text-gray-400 hover:text-white'"
                                    x-text="p"></button>
                        </template>
                        <button @click="attPage < attTotalPages && (attPage++)" :disabled="attPage === attTotalPages"
                                class="w-6 h-6 flex items-center justify-center rounded-lg bg-white/5 text-gray-400 hover:bg-white/10 hover:text-white transition-colors disabled:opacity-30 disabled:cursor-not-allowed">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-3 h-3"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Shift Management Tab --}}
        <div x-show="activeTab === 'shift'" x-transition style="display: none;">
            <div class="flex justify-between items-center mb-6">
                <div>
                     <h3 class="text-white font-bold text-lg">Daftar Jadwal Shift</h3>
                     <p class="text-xs text-gray-500 mt-1">Atur jam kerja dan toleransi keterlambatan</p>
                </div>
                <button @click="openAddShiftModal()" class="bg-[#D0B75B] text-black font-bold text-xs px-4 py-2.5 rounded-lg hover:bg-[#e0c86b] transition-colors flex items-center gap-2">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Tambah Shift
                </button>
            </div>

            <div class="bg-[#080808] border border-white/5 rounded-2xl overflow-hidden">
                <table class="w-full text-xs text-left">
                    <thead class="text-[9px] text-gray-500 font-bold uppercase tracking-widest bg-white/5 border-b border-white/5">
                        <tr>
                            <th class="px-5 py-4">Nama Shift</th>
                            <th class="px-5 py-4">Jam Mulai</th>
                            <th class="px-5 py-4">Jam Selesai</th>
                            <th class="px-5 py-4">Toleransi (Menit)</th>
                            <th class="px-5 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <template x-for="shift in shifts" :key="shift.id">
                            <tr class="hover:bg-white/[0.01] transition-colors">
                                <td class="px-5 py-4 font-bold text-white" x-text="shift.nama"></td>
                                <td class="px-5 py-4 text-gray-300 font-mono" x-text="shift.mulai"></td>
                                <td class="px-5 py-4 text-gray-300 font-mono" x-text="shift.selesai"></td>
                                <td class="px-5 py-4">
                                    <span class="text-yellow-500 font-mono font-bold" x-text="shift.toleransi + ' Menit'"></span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <button @click="editShift(shift)" class="text-gray-400 hover:text-[#D0B75B] transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                                            <path d="M12 20h9"></path>
                                            <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Employee Assignment Tab --}}
        <div x-show="activeTab === 'karyawan'" x-transition style="display: none;">
             <div class="flex justify-between items-center mb-6">
                <div>
                     <h3 class="text-white font-bold text-lg">Penugasan Karyawan</h3>
                     <p class="text-xs text-gray-500 mt-1">Tentukan shift kerja untuk setiap akun karyawan</p>
                </div>

                <button @click="openAssignModal()" class="bg-[#D0B75B] text-black font-bold text-xs px-4 py-2.5 rounded-lg hover:bg-[#e0c86b] transition-colors flex items-center gap-2">
                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                    Atur Penugasan
                </button>
            </div>
            
            <div class="bg-[#080808] border border-white/5 rounded-2xl overflow-hidden">
                <table class="w-full text-xs text-left">
                    <thead class="text-[9px] text-gray-500 font-bold uppercase tracking-widest bg-white/5 border-b border-white/5">
                        <tr>
                            <th class="px-5 py-4">Nama Karyawan</th>
                            <th class="px-5 py-4">Shift Ditugaskan</th>
                            <th class="px-5 py-4">Target Check-In</th>
                            <th class="px-5 py-4">Target Check-Out</th>
                            <th class="px-5 py-4">Toleransi</th>
                            <th class="px-5 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <template x-for="(assign, idx) in assignments" :key="idx">
                            <tr class="hover:bg-white/[0.01] transition-colors">
                                <td class="px-5 py-4 font-bold text-white" x-text="assign.nama"></td>
                                <td class="px-5 py-4">
                                    <span class="bg-white/5 border border-white/10 px-2 py-1 rounded text-[10px] text-gray-300 font-bold" x-text="assign.shift"></span>
                                </td>
                                <td class="px-5 py-4 text-gray-400 font-mono" x-text="assign.check_in"></td>
                                <td class="px-5 py-4 text-gray-400 font-mono" x-text="assign.check_out"></td>
                                <td class="px-5 py-4 text-yellow-500 font-mono font-bold" x-text="'+' + assign.toleransi + 'm'"></td>
                                <td class="px-5 py-4 text-center">
                                    <button @click="openAssignModal(assign)" class="text-gray-400 hover:text-[#D0B75B] transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                                            <path d="M12 20h9"></path>
                                            <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Add/Edit Shift Modal --}}
        <template x-teleport="body">
            <div x-show="showModal" style="display: none;" 
                 class="fixed inset-0 z-[99] flex items-center justify-center bg-black/80 backdrop-blur-sm p-4" x-transition.opacity>
                <div @click.away="showModal = false" class="bg-[#0A0A0A] border border-white/10 rounded-2xl w-full max-w-lg overflow-hidden relative">
                    <div class="px-6 py-4 border-b border-white/5 bg-zinc-900/20 flex justify-between items-center">
                        <h3 class="text-white font-bold" x-text="isEditing ? 'Edit Shift' : 'Tambah Shift Baru'"></h3>
                        <button @click="showModal = false"><i data-lucide="x" class="w-5 h-5 text-gray-500 hover:text-white transition-colors"></i></button>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-xs uppercase font-bold text-gray-500 mb-2">Nama Shift</label>
                            <input type="text" x-model="newShift.nama" class="w-full bg-zinc-900 border border-white/10 rounded-lg px-4 py-2 text-white focus:border-[#D0B75B] outline-none" placeholder="Contoh: Shift Pagi">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs uppercase font-bold text-gray-500 mb-2">Jam Mulai</label>
                                <input type="time" x-model="newShift.mulai" class="w-full bg-zinc-900 border border-white/10 rounded-lg px-4 py-2 text-white focus:border-[#D0B75B] outline-none">
                            </div>
                            <div>
                                <label class="block text-xs uppercase font-bold text-gray-500 mb-2">Jam Selesai</label>
                                <input type="time" x-model="newShift.selesai" class="w-full bg-zinc-900 border border-white/10 rounded-lg px-4 py-2 text-white focus:border-[#D0B75B] outline-none">
                            </div>
                        </div>
                        <div>
                             <label class="block text-xs uppercase font-bold text-gray-500 mb-2">Toleransi Keterlambatan (Menit)</label>
                             <div class="relative">
                                 <input type="number" x-model="newShift.toleransi" class="w-full bg-zinc-900 border border-white/10 rounded-lg px-4 py-2 text-white focus:border-[#D0B75B] outline-none pl-4">
                                 <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 text-xs font-bold">Menit</span>
                             </div>
                             <p class="text-[10px] text-gray-500 mt-1">Karyawan dianggap terlambat jika check-in melebihi batas toleransi ini.</p>
                        </div>
                        
                        <div class="pt-4 border-t border-white/5 mt-2">
                             <button @click="saveShift()" class="w-full bg-[#D0B75B] hover:bg-[#e0c86b] text-black font-bold uppercase tracking-widest py-3 rounded-xl transition-all">
                                Simpan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        {{-- Assign Shift Modal --}}
        <template x-teleport="body">
            <div x-show="showAssignModal" style="display: none;" 
                 class="fixed inset-0 z-[99] flex items-center justify-center bg-black/80 backdrop-blur-sm p-4" x-transition.opacity>
                <div @click.away="showAssignModal = false" class="bg-[#0A0A0A] border border-white/10 rounded-2xl w-full max-w-lg overflow-hidden relative">
                    <div class="px-6 py-4 border-b border-white/5 bg-zinc-900/20 flex justify-between items-center">
                        <h3 class="text-white font-bold">Atur Penugasan Shift</h3>
                        <button @click="showAssignModal = false"><i data-lucide="x" class="w-5 h-5 text-gray-500 hover:text-white transition-colors"></i></button>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-xs uppercase font-bold text-gray-500 mb-2">Pilih Karyawan</label>
                            <select x-model="assignForm.nama" class="w-full bg-zinc-900 border border-white/10 rounded-lg px-4 py-2 text-white focus:border-[#D0B75B] outline-none">
                                <option value="">-- Pilih Karyawan --</option>
                                <template x-for="emp in employees" :key="emp">
                                    <option :value="emp" x-text="emp"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs uppercase font-bold text-gray-500 mb-2">Pilih Shift</label>
                            <select x-model="assignForm.shift_id" class="w-full bg-zinc-900 border border-white/10 rounded-lg px-4 py-2 text-white focus:border-[#D0B75B] outline-none">
                                <option value="">-- Pilih Shift --</option>
                                <template x-for="shift in shifts" :key="shift.id">
                                    <option :value="shift.id" x-text="shift.nama + ' (' + shift.mulai + ' - ' + shift.selesai + ')'"></option>
                                </template>
                            </select>
                        </div>
                        
                        <div class="pt-4 border-t border-white/5 mt-2">
                             <button @click="saveAssignment()" class="w-full bg-[#D0B75B] hover:bg-[#e0c86b] text-black font-bold uppercase tracking-widest py-3 rounded-xl transition-all">
                                Simpan Penugasan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
@endsection
