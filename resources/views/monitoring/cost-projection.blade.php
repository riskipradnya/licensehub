<x-app-layout>
    <x-slot name="title">Proyeksi Biaya Lisensi</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6 text-slate-200">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Proyeksi Biaya (Cost Projection)</h1>
                <p class="text-sm mt-1 text-slate-500 dark:text-slate-400">Pantau perkiraan biaya perpanjangan lisensi IT di masa depan.</p>
            </div>
            <div class="flex flex-col xl:flex-row items-center gap-3 w-full sm:w-auto">
                <!-- Hybrid Filter Form -->
                <form x-data="{ filterType: '{{ $periode }}' }" method="GET" action="{{ route('cost-projection.index') }}" class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
                    <div class="flex items-center gap-2">
                        <label for="periodeFilter" class="hidden md:block text-sm font-medium text-slate-500 dark:text-slate-400">Periode:</label>
                        <select name="periode" id="periodeFilter" x-model="filterType" class="bg-white dark:bg-[#1E293B] text-slate-900 dark:text-slate-200 text-sm py-2 pl-4 pr-10 rounded-lg shadow-sm border border-slate-300 dark:border-slate-700 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all">
                            <option value="3">3 Bulan</option>
                            <option value="6">6 Bulan</option>
                            <option value="12">1 Tahun</option>
                            <option value="custom">Custom Range</option>
                        </select>
                    </div>

                    <div x-show="filterType === 'custom'" x-cloak class="flex flex-col sm:flex-row items-center gap-2 w-full sm:w-auto">
                        <input type="date" name="start_date" value="{{ request('start_date', $startDate->format('Y-m-d')) }}" class="w-full sm:w-auto bg-white dark:bg-[#1E293B] text-slate-900 dark:text-white border border-slate-300 dark:border-slate-700 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 px-3 py-2 outline-none transition-all">
                        <span class="text-slate-500 dark:text-slate-400 font-medium text-sm">to</span>
                        <input type="date" name="end_date" value="{{ request('end_date', $endDate->format('Y-m-d')) }}" class="w-full sm:w-auto bg-white dark:bg-[#1E293B] text-slate-900 dark:text-white border border-slate-300 dark:border-slate-700 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 px-3 py-2 outline-none transition-all">
                    </div>

                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold py-2 px-4 rounded-lg shadow-sm transition-colors flex items-center gap-1.5 w-full sm:w-auto justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Filter
                    </button>
                </form>

                <!-- Export Dropdown -->
                <div x-data="{ openExport: false }" class="relative w-full sm:w-auto">
                    <button @click="openExport = !openExport" @click.away="openExport = false" class="bg-slate-100 hover:bg-slate-200 dark:bg-[#1E293B] dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 text-sm font-semibold py-2 px-4 rounded-lg shadow-sm border border-slate-300 dark:border-slate-700 transition-colors flex items-center justify-between gap-2 w-full">
                        <span class="flex items-center gap-1.5"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg> Export Data</span>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': openExport }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    
                    <div x-show="openExport" x-transition.opacity x-cloak class="absolute right-0 mt-2 w-48 bg-white dark:bg-[#1E293B] rounded-lg shadow-xl border border-slate-200 dark:border-slate-700/50 overflow-hidden z-50">
                        <a href="{{ route('cost-projection.export', array_merge(request()->query(), ['format' => 'pdf'])) }}" class="block px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-red-50 dark:hover:bg-red-500/10 hover:text-red-600 dark:hover:text-red-400 transition-colors flex items-center gap-2">
                            📄 Export PDF
                        </a>
                        <a href="{{ route('cost-projection.export', array_merge(request()->query(), ['format' => 'excel'])) }}" class="block px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-emerald-50 dark:hover:bg-emerald-500/10 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors flex items-center gap-2 border-t border-slate-100 dark:border-slate-700/50">
                            📊 Export Excel
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Key Metrics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Total Projected Cost -->
            <div class="bg-white dark:bg-[#1E293B] border border-slate-200 dark:border-slate-700/50 rounded-xl shadow-lg p-6 flex items-center gap-4 transition-transform hover:-translate-y-1 duration-300">
                <div class="p-3 rounded-xl bg-indigo-500/20 text-indigo-400">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Total Proyeksi Biaya</p>
                    <p class="text-2xl font-bold text-slate-900 dark:text-white mt-1">Rp {{ number_format($totalProjectedCost, 0, ',', '.') }}</p>
                </div>
            </div>

            <!-- Licenses Due -->
            <div class="bg-white dark:bg-[#1E293B] border border-slate-200 dark:border-slate-700/50 rounded-xl shadow-lg p-6 flex items-center gap-4 transition-transform hover:-translate-y-1 duration-300">
                <div class="p-3 rounded-xl bg-amber-500/20 text-amber-400">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Lisensi Jatuh Tempo</p>
                    <p class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ $licensesDue }} Lisensi</p>
                </div>
            </div>

            <!-- Average Monthly Cost -->
            <div class="bg-white dark:bg-[#1E293B] border border-slate-200 dark:border-slate-700/50 rounded-xl shadow-lg p-6 flex items-center gap-4 transition-transform hover:-translate-y-1 duration-300">
                <div class="p-3 rounded-xl bg-emerald-500/20 text-emerald-400">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Rata-rata per Bulan</p>
                    <p class="text-2xl font-bold text-slate-900 dark:text-white mt-1">Rp {{ number_format($avgMonthlyCost, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="bg-white dark:bg-[#1E293B] border border-slate-200 dark:border-slate-700/50 rounded-xl p-6 w-full shadow-lg">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Tren Proyeksi Biaya Bulanan</h3>
            </div>
            <div class="relative h-80 w-full">
                <canvas id="costChart"></canvas>
            </div>
        </div>

        <!-- Breakdown Table -->
        <div class="bg-white dark:bg-[#1E293B] border border-slate-200 dark:border-slate-700/50 rounded-xl shadow-lg overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700/50 bg-slate-50 dark:bg-[#0F172A]/50">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Rincian Jatuh Tempo Lisensi</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-slate-50 dark:bg-[#1E293B] text-slate-700 dark:text-slate-400 border-b border-slate-200 dark:border-slate-700/50">
                        <tr>
                            <th class="px-6 py-4 font-semibold text-center w-16">No</th>
                            <th class="px-6 py-4 font-semibold">Nama Lisensi</th>
                            <th class="px-6 py-4 font-semibold">Vendor</th>
                            <th class="px-6 py-4 font-semibold text-center">Jatuh Tempo</th>
                            <th class="px-6 py-4 font-semibold text-center">Sisa Hari</th>
                            <th class="px-6 py-4 font-semibold text-right">Biaya (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700/50">
                        @forelse($expiringLicenses as $index => $license)
                            @php
                                $daysLeft = $license->days_until_expiry;
                                
                                // Modern Dark Theme Badge color logic
                                if ($daysLeft <= 0) {
                                    $badgeClass = 'bg-red-500/10 text-red-600 dark:text-red-400 border-red-500/20';
                                } elseif ($daysLeft <= 14) {
                                    $badgeClass = 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20';
                                } elseif ($daysLeft <= 31) {
                                    $badgeClass = 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border-blue-500/20';
                                } else {
                                    $badgeClass = 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20';
                                }
                            @endphp
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors duration-200">
                                <td class="px-6 py-4 text-center text-slate-600 dark:text-slate-500">{{ $index + 1 }}</td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-slate-900 dark:text-slate-200">
                                        <a href="{{ route('licenses.show', $license->id) }}" class="hover:text-indigo-500 dark:hover:text-indigo-400 transition-colors">
                                            {{ $license->name }}
                                        </a>
                                    </div>
                                    <div class="text-xs text-slate-500 mt-1">{{ ucfirst($license->billing_cycle) }}</div>
                                </td>
                                <td class="px-6 py-4 text-slate-700 dark:text-slate-400">{{ $license->vendor->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-center text-slate-800 dark:text-slate-300">{{ $license->expiry_date->format('d M Y') }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-3 py-1.5 text-xs font-semibold rounded-md border {{ $badgeClass }}">
                                        @if($daysLeft < 0)
                                            Terlewat {{ abs($daysLeft) }} Hari
                                        @elseif($daysLeft == 0)
                                            Hari Ini
                                        @else
                                            {{ $daysLeft }} Hari
                                        @endif
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right font-medium text-slate-900 dark:text-slate-200">
                                    {{ number_format($license->cost, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-slate-500">
                                        <svg class="w-16 h-16 mb-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        <p class="font-medium text-lg text-slate-300">Tidak ada lisensi yang jatuh tempo</p>
                                        <p class="text-sm mt-1">Dalam rentang {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($expiringLicenses->isNotEmpty())
                    <tfoot class="bg-slate-50 dark:bg-[#0F172A]/80 border-t border-slate-200 dark:border-slate-700/50">
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-right font-bold text-slate-700 dark:text-slate-400 tracking-wider text-sm">TOTAL PROYEKSI:</td>
                            <td class="px-6 py-4 text-right font-bold text-indigo-600 dark:text-indigo-400 text-lg">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
                <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700/50 bg-white dark:bg-[#1E293B]">
                    {{ $expiringLicenses->links() }}
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('costChart').getContext('2d');
            
            const labels = @json($chartLabels);
            const data = @json($chartValues);

            const formatRupiah = (number) => {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                }).format(number);
            };
            
            // Mengatur default font color untuk legend/axis text di tema gelap
            Chart.defaults.color = '#94a3b8'; // text-slate-400
            Chart.defaults.font.family = "'Inter', 'sans-serif'";

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Proyeksi Biaya (Rp)',
                        data: data,
                        backgroundColor: 'rgba(99, 102, 241, 0.8)', // Primary indigo
                        borderColor: 'rgba(99, 102, 241, 1)',
                        borderWidth: 1,
                        borderRadius: 6,
                        hoverBackgroundColor: 'rgba(79, 70, 229, 0.9)'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(15, 23, 42, 0.9)', // bg-slate-900
                            titleColor: '#f8fafc',
                            bodyColor: '#e2e8f0',
                            padding: 12,
                            borderColor: 'rgba(51, 65, 85, 0.8)',
                            borderWidth: 1,
                            callbacks: {
                                label: function(context) {
                                    return formatRupiah(context.raw);
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    if (value >= 1000000) {
                                        return 'Rp ' + (value / 1000000) + ' Jt';
                                    } else if (value >= 1000) {
                                        return 'Rp ' + (value / 1000) + ' Rb';
                                    }
                                    return 'Rp ' + value;
                                }
                            },
                            grid: {
                                color: 'rgba(51, 65, 85, 0.3)', // slate-700 dengan opacity rendah
                                drawBorder: false
                            }
                        },
                        x: {
                            grid: {
                                display: false,
                                drawBorder: false
                            }
                        }
                    }
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
