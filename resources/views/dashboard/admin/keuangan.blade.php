@extends('layouts.dashboard')

@section('dashboard-role', 'Admin Panel')
@section('dashboard-role-icon', 'shield-check')

@section('title', 'Keuangan - Admin Dashboard')
@section('page-title', 'Keuangan & Pendapatan')

@section('sidebar-nav')
    @include('dashboard.admin._sidebar', ['active' => 'keuangan'])
@endsection

@section('dashboard-content')
    {{-- Chart Section (Replaces Summary Cards) --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>

    <div class="bg-black border border-white/5 rounded-2xl p-4 mb-6 relative">
        
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4 relative z-10">
            <div>
                <h2 class="text-xl font-black text-white tracking-tight flex items-center gap-2">
                    <i data-lucide="bar-chart-2" class="w-5 h-5 text-[#D0B75B]"></i>
                    Analisis Pendapatan
                </h2>
                <p class="text-[10px] text-gray-500 font-medium uppercase tracking-widest mt-1">
                    @if($filterType === 'monthly')
                        Laporan Bulan {{ \Carbon\Carbon::parse($selectedMonth)->translatedFormat('F Y') }}
                    @else
                        Periode {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M') }} - {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}
                    @endif
                </p>
            </div>
            
            <div class="flex items-center gap-2">
                {{-- Filter Controls --}}
                <form action="{{ route('admin.keuangan') }}" method="GET" class="flex items-center gap-2 bg-[#0A0A0A] p-1.5 rounded-xl border border-white/10">
                    <select name="filter_type" onchange="this.form.submit()" 
                            class="bg-transparent text-xs font-bold text-gray-300 px-3 py-2 rounded-lg focus:outline-none focus:bg-white/5 cursor-pointer hover:text-white transition-colors border-none">
                        <option value="monthly" {{ $filterType == 'monthly' ? 'selected' : '' }} class="bg-[#18181b] text-white">Bulanan</option>
                        <option value="custom" {{ $filterType == 'custom' ? 'selected' : '' }} class="bg-[#18181b] text-white">Periode</option>
                    </select>
                    
                    <div class="h-5 w-px bg-white/10"></div>
                    
                    @if($filterType == 'monthly')
                    <input type="month" name="month" value="{{ $selectedMonth }}" onchange="this.form.submit()" style="color-scheme: dark;"
                           class="bg-transparent text-xs font-bold text-white px-3 py-1.5 rounded-lg focus:outline-none focus:bg-white/5 border border-white/10 cursor-pointer hover:bg-white/5 transition-colors">
                    @else
                    <div class="flex items-center gap-2">
                        <input type="date" name="start_date" value="{{ $startDate }}" onchange="this.form.submit()" style="color-scheme: dark;"
                               class="bg-transparent text-xs font-bold text-white px-2 py-1.5 rounded-lg focus:outline-none focus:bg-white/5 border-none outline-none cursor-pointer hover:bg-white/5 transition-colors">
                        <span class="text-gray-500 text-[10px] uppercase font-bold">s/d</span>
                        <input type="date" name="end_date" value="{{ $endDate }}" onchange="this.form.submit()" style="color-scheme: dark;"
                               class="bg-transparent text-xs font-bold text-white px-2 py-1.5 rounded-lg focus:outline-none focus:bg-white/5 border-none outline-none cursor-pointer hover:bg-white/5 transition-colors">
                    </div>
                    @endif
                </form>

                {{-- Export PDF Button --}}
                <button onclick="exportKeuanganPdf()" class="bg-[#D0B75B] text-black font-bold text-[10px] px-3 py-2 rounded-xl hover:bg-[#e0c86b] transition-colors flex items-center gap-1.5 border border-[#D0B75B]/30">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-3.5 h-3.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    Export PDF
                </button>
            </div>
        </div>

        {{-- Chart Container --}}
        <div class="h-[250px] w-full relative z-10">
            <canvas id="revenueChart"></canvas>
        </div>
        
        {{-- Quick Stats Overlay --}}
        <div class="grid grid-cols-3 gap-4 mt-6 border-t border-white/5 pt-6">
            <div class="text-center">
                <p class="text-[9px] text-gray-500 uppercase tracking-widest font-bold mb-1">Total Pendapatan</p>
                <p class="text-lg font-black text-white">
                    Rp {{ number_format($filterType == 'monthly' ? $ringkasanKeuangan['bulan_ini'] : $ringkasanKeuangan['bulan_ini'], 0, ',', '.') }}
                </p>
            </div>
            <div class="text-center border-l border-white/5">
                <p class="text-[9px] text-gray-500 uppercase tracking-widest font-bold mb-1">Rata-rata / Hari</p>
                 @php
                    $daysCount = count($laporanHarian) > 0 ? count($laporanHarian) : 1;
                @endphp
                <p class="text-lg font-bold text-gray-300">
                    Rp {{ number_format($ringkasanKeuangan['bulan_ini'] / $daysCount, 0, ',', '.') }}
                </p>
            </div>
            <div class="text-center border-l border-white/5">
                <p class="text-[9px] text-gray-500 uppercase tracking-widest font-bold mb-1">Pertumbuhan</p>
                @php
                    $growth = $ringkasanKeuangan['growth'];
                    $icon = null;
                    if ($growth > 0) {
                        $color = 'text-green-400';
                        $icon = 'trending-up';
                    } elseif ($growth < 0) {
                        $color = 'text-red-400';
                        $icon = 'trending-down';
                    } else {
                        $color = 'text-white';
                    }
                @endphp
                <p class="text-lg font-bold {{ $color }} flex items-center justify-center gap-1">
                    @if($icon)
                    <i data-lucide="{{ $icon }}" class="w-3 h-3"></i> 
                    @endif
                    {{ abs($growth) }}%
                </p>
            </div>
        </div>
    </div>

    {{-- Laporan Pendapatan Table --}}
    {{-- Laporan Pendapatan Table --}}
    <div class="bg-black border border-white/5 rounded-2xl overflow-hidden mb-6 relative group">
        <div class="px-4 py-4 border-b border-white/5 bg-zinc-900/20 flex items-center justify-between backdrop-blur-sm">
            <div class="flex items-center gap-3">
                 <div class="p-1.5 rounded-lg bg-[#D0B75B]/10 border border-[#D0B75B]/20">
                    <i data-lucide="table" class="w-3.5 h-3.5 text-[#D0B75B]"></i>
                 </div>
                <h2 class="text-[10px] font-bold text-gray-200 uppercase tracking-[0.2em]" style="font-family: 'Inter';">Data Laporan Pendapatan</h2>
            </div>
            
            <div class="flex items-center gap-2">
                {{-- Search Filter --}}
                <div class="relative">
                    <input type="text" id="dateSearchInput" placeholder="Cari Tanggal..." 
                           class="bg-black/50 border border-white/10 text-[10px] text-white rounded-lg pl-8 pr-3 py-1.5 focus:outline-none focus:border-[#D0B75B]/50 placeholder-gray-600 transition-colors w-40">
                    <i data-lucide="search" class="w-3 h-3 text-gray-500 absolute left-2.5 top-1/2 -translate-y-1/2"></i>
                </div>

            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs" style="font-family: 'Inter';">
                <thead>
                    <tr class="text-gray-500 text-[9px] font-black uppercase tracking-[0.2em] bg-black/40 border-b border-white/5">
                        <th class="text-left px-4 py-3">Tanggal</th>
                        <th class="text-center px-4 py-3">Transaksi</th>
                        <th class="text-right px-4 py-3">Ruangan</th>
                        <th class="text-right px-4 py-3">F&B</th>
                        <th class="text-right px-4 py-3">Extend</th>
                        <th class="text-right px-4 py-3">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5" id="laporanTableBody">
                    @forelse($laporanHarian as $laporan)
                    <tr class="hover:bg-white/[0.02] transition-colors group/row">
                        <td class="px-4 py-3 text-white font-medium group-hover/row:text-[#D0B75B] transition-colors">{{ $laporan['tanggal'] }}</td>
                        <td class="px-4 py-3 text-center text-gray-500 font-bold bg-white/[0.01]">{{ $laporan['jumlah_trx'] }}</td>
                        <td class="px-4 py-3 text-right text-gray-400">Rp {{ number_format($laporan['pendapatan_ruangan'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-gray-400">Rp {{ number_format($laporan['pendapatan_fnb'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-gray-400">Rp {{ number_format($laporan['extend'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-white font-black bg-white/[0.01] border-l border-white/5">Rp {{ number_format($laporan['total'], 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-gray-500 italic">Tidak ada data untuk periode ini</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>



    {{-- Script for Chart & Search --}}
    <script>
        // Export PDF Function
        function exportKeuanganPdf() {
            const tableBody = document.getElementById('laporanTableBody');
            const rows = tableBody.querySelectorAll('tr');
            const visibleRows = [];

            rows.forEach(row => {
                if (row.style.display !== 'none' && row.querySelectorAll('td').length >= 6) {
                    const cells = row.querySelectorAll('td');
                    visibleRows.push({
                        tanggal: cells[0].textContent.trim(),
                        transaksi: cells[1].textContent.trim(),
                        ruangan: cells[2].textContent.trim(),
                        fnb: cells[3].textContent.trim(),
                        extend: cells[4].textContent.trim(),
                        total: cells[5].textContent.trim()
                    });
                }
            });

            if (visibleRows.length === 0) {
                alert('Tidak ada data untuk diekspor');
                return;
            }

            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('l', 'mm', 'a4');
            const pw = doc.internal.pageSize.getWidth();
            const ph = doc.internal.pageSize.getHeight();
            const margin = 10;
            const today = new Date();
            const dateStr = today.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
            const timeStr = today.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

            // ── Header Banner ──
            doc.setFillColor(10, 10, 10);
            doc.rect(0, 0, pw, 35, 'F');
            doc.setFillColor(208, 183, 91);
            doc.rect(0, 35, pw, 1.5, 'F');

            // Brand
            doc.setTextColor(208, 183, 91);
            doc.setFontSize(16);
            doc.setFont('helvetica', 'bold');
            doc.text('SGRT KARAOKE', margin, 16);
            doc.setTextColor(180, 180, 180);
            doc.setFontSize(8);
            doc.setFont('helvetica', 'normal');
            doc.text('Sing & Joy Premium Karaoke', margin, 23);

            // Title
            doc.setTextColor(255, 255, 255);
            doc.setFontSize(13);
            doc.setFont('helvetica', 'bold');
            doc.text('LAPORAN KEUANGAN & PENDAPATAN', pw - margin, 16, { align: 'right' });

            // Period info
            const periodText = @json(
                $filterType === 'monthly'
                    ? 'Bulan: ' . \Carbon\Carbon::parse($selectedMonth)->translatedFormat('F Y')
                    : 'Periode: ' . \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') . ' s/d ' . \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y')
            );
            doc.setTextColor(150, 150, 150);
            doc.setFontSize(7);
            doc.setFont('helvetica', 'normal');
            doc.text(periodText, pw - margin, 22, { align: 'right' });
            doc.text('Dicetak: ' + dateStr + ' ' + timeStr + '  |  ' + visibleRows.length + ' hari', pw - margin, 25, { align: 'right' });

            // ── Summary Stats Line ──
            const totalPendapatan = @json($ringkasanKeuangan['bulan_ini']);
            const daysCount = @json(count($laporanHarian) > 0 ? count($laporanHarian) : 1);
            const rataRata = Math.round(totalPendapatan / daysCount);
            const growth = @json($ringkasanKeuangan['growth']);

            const formatRp = (val) => 'Rp ' + new Intl.NumberFormat('id-ID').format(val);

            doc.setDrawColor(255, 255, 255);
            doc.setLineWidth(0.1);
            doc.line(pw - 130, 29, pw - margin, 29);
            doc.setTextColor(180, 180, 180);
            doc.setFontSize(7);
            doc.text('Total: ' + formatRp(totalPendapatan) + '  |  Rata-rata/Hari: ' + formatRp(rataRata) + '  |  Pertumbuhan: ' + (growth >= 0 ? '+' : '') + growth + '%', pw - margin, 33, { align: 'right' });

            // ── Table Data ──
            const tableData = visibleRows.map((row, i) => [
                (i + 1).toString(),
                row.tanggal,
                row.transaksi,
                row.ruangan,
                row.fnb,
                row.extend,
                row.total
            ]);

            doc.autoTable({
                head: [['No', 'Tanggal', 'Transaksi', 'Ruangan', 'F&B', 'Extend', 'Total']],
                body: tableData,
                startY: 40,
                margin: { left: margin, right: margin },
                theme: 'grid',
                styles: {
                    fontSize: 8,
                    cellPadding: 3,
                    textColor: [50, 50, 50],
                    lineColor: [220, 220, 220],
                    lineWidth: 0.2
                },
                headStyles: {
                    fillColor: [30, 30, 30],
                    textColor: [208, 183, 91],
                    fontStyle: 'bold',
                    fontSize: 8,
                    halign: 'center'
                },
                alternateRowStyles: { fillColor: [248, 248, 248] },
                columnStyles: {
                    0: { halign: 'center', cellWidth: 12 },
                    1: { fontStyle: 'bold', cellWidth: 35 },
                    2: { halign: 'center', cellWidth: 25 },
                    3: { halign: 'right', cellWidth: 40 },
                    4: { halign: 'right', cellWidth: 40 },
                    5: { halign: 'right', cellWidth: 35 },
                    6: { halign: 'right', cellWidth: 45, fontStyle: 'bold' }
                },
                didParseCell: function(data) {
                    // Bold + dark color for Total column
                    if (data.column.index === 6 && data.section === 'body') {
                        data.cell.styles.textColor = [20, 20, 20];
                        data.cell.styles.fontStyle = 'bold';
                    }
                },
                didDrawPage: function(d) {
                    // Footer
                    doc.setFillColor(245, 245, 245);
                    doc.rect(0, ph - 12, pw, 12, 'F');
                    doc.setFillColor(208, 183, 91);
                    doc.rect(0, ph - 12, pw, 0.5, 'F');
                    doc.setTextColor(130, 130, 130);
                    doc.setFontSize(6);
                    doc.setFont('helvetica', 'normal');
                    doc.text('SGRT Karaoke - Laporan Keuangan & Pendapatan | Digenerate otomatis oleh sistem', margin, ph - 5);
                    doc.text('Halaman ' + d.pageNumber, pw - margin, ph - 5, { align: 'right' });
                }
            });

            // ── Grand Total Row after table ──
            let grandY = doc.lastAutoTable.finalY + 5;
            // If not enough space, add new page
            if (grandY > ph - 25) {
                doc.addPage();
                grandY = 15;
            }
            doc.setFillColor(30, 30, 30);
            doc.roundedRect(margin, grandY, pw - (margin * 2), 12, 2, 2, 'F');
            doc.setTextColor(208, 183, 91);
            doc.setFontSize(9);
            doc.setFont('helvetica', 'bold');
            doc.text('GRAND TOTAL', margin + 8, grandY + 8);
            doc.setTextColor(255, 255, 255);
            doc.setFontSize(11);
            doc.text(formatRp(totalPendapatan), pw - margin - 8, grandY + 8, { align: 'right' });

            // ── Save ──
            const filterType = @json($filterType);
            let filename = 'laporan_keuangan';
            if (filterType === 'monthly') {
                filename += '_' + @json($selectedMonth);
            } else {
                filename += '_' + @json($startDate) + '_sd_' + @json($endDate);
            }
            doc.save(filename + '.pdf');
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Chart
            const ctx = document.getElementById('revenueChart').getContext('2d');
            const chartConfig = {
                type: 'line',
                data: {
                    labels: @json($chartLabels),
                    datasets: [{
                        label: 'Pendapatan',
                        data: @json($chartValues),
                        borderColor: '#D0B75B',
                        borderWidth: 2,
                        pointBackgroundColor: '#0A0A0A',
                        pointBorderColor: '#D0B75B',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: '#D0B75B',
                        tension: 0.4,
                        fill: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#18181b', borderColor: 'rgba(255,255,255,0.1)', borderWidth: 1, titleColor: '#fff', bodyColor: '#a1a1aa', padding: 12, displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) { label += ': '; }
                                    if (context.parsed.y !== null) { label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(context.parsed.y); }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        y: { beginAtZero: true, grid: { color: 'rgba(255, 255, 255, 0.05)' }, ticks: { color: '#52525b', font: { family: "'Inter', sans-serif", size: 10 }, callback: function(value) { if(value >= 1000000) return 'Rp ' + (value/1000000) + 'jt'; if(value >= 1000) return 'Rp ' + (value/1000) + 'rb'; return value; } }, border: { display: false } },
                        x: { grid: { display: false }, ticks: { color: '#52525b', font: { family: "'Inter', sans-serif", size: 10 }, maxTicksLimit: 10 }, border: { display: false } }
                    },
                    interaction: { mode: 'index', intersect: false },
                }
            };
            new Chart(ctx, chartConfig);

            // Search Logic
            const setupSearch = (inputId, tableBodyId) => {
                const input = document.getElementById(inputId);
                const tableBody = document.getElementById(tableBodyId);
                if(!input || !tableBody) return;

                input.addEventListener('keyup', function() {
                    const filter = input.value.toLowerCase();
                    const rows = tableBody.getElementsByTagName('tr');

                    for (let i = 0; i < rows.length; i++) {
                        const row = rows[i];
                        if(row.innerText.toLowerCase().indexOf(filter) > -1) {
                            row.style.display = "";
                        } else {
                            row.style.display = "none";
                        }
                    }
                });
            };

            setupSearch('dateSearchInput', 'laporanTableBody');
        });
    </script>
@endsection
