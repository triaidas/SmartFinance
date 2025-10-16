<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Monthly Overview -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">{{ __('Monthly Overview') }}</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Income') }}</p>
                            <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($monthlyIncome, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Expenses') }}</p>
                            <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ number_format(abs($monthlyExpenses), 2) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Transaction Trends Chart -->
                        <div>
                            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">{{ __('Transaction Trends') }}</h3>
                            <div id="transactionTrendsChart" class="w-full" style="min-height: 300px;"></div>
                        </div>
                        <!-- Distribution Chart -->
                        <div>
                            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">{{ __('Income vs Expenses') }}</h3>
                            <div id="distributionChart" class="w-full" style="min-height: 300px;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">{{ __('Recent Transactions') }}</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Date') }}</th>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Type') }}</th>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Category') }}</th>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Payment Method') }}</th>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Amount') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                @forelse ($recentTransactions as $transaction)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                            {{ $transaction->date->format('Y-m-d') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm {{ $transaction->type === 'income' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }} font-medium">
                                            {{ __(ucfirst($transaction->type)) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                            {{ $transaction->category }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                            {{ $transaction->payment_method }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right {{ $transaction->type === 'income' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                            {{ number_format($transaction->amount, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                            {{ __('No recent transactions') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const trends = @json($transactionTrends);
            const distribution = @json($distributionData);

            // Line chart for trends
            const trendOptions = {
                chart: {
                    type: 'line',
                    height: 300,
                    toolbar: {
                        show: false
                    },
                    animations: {
                        enabled: true
                    }
                },
                series: [{
                    name: 'Balance',
                    data: trends.map(t => t.total)
                }],
                xaxis: {
                    categories: trends.map(t => t.month),
                    labels: {
                        style: {
                            colors: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#4b5563'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#4b5563'
                        }
                    }
                },
                colors: ['#0EA5E9'],
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                theme: {
                    mode: document.documentElement.classList.contains('dark') ? 'dark' : 'light'
                },
                grid: {
                    borderColor: document.documentElement.classList.contains('dark') ? '#374151' : '#e5e7eb'
                }
            };

            // Pie chart for income vs expenses distribution
            const distributionOptions = {
                chart: {
                    type: 'pie',
                    height: 300,
                    animations: {
                        enabled: true
                    }
                },
                series: distribution.map(item => item.value),
                labels: distribution.map(item => item.label),
                colors: ['#22C55E', '#EF4444'],  // Green for income, red for expenses
                theme: {
                    mode: document.documentElement.classList.contains('dark') ? 'dark' : 'light'
                },
                legend: {
                    position: 'bottom',
                    labels: {
                        colors: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#4b5563'
                    }
                },
                plotOptions: {
                    pie: {
                        expandOnClick: true,
                        donut: {
                            size: '65%'
                        }
                    }
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            height: 250
                        }
                    }
                }]
            };

            try {
                const trendsChart = new ApexCharts(document.querySelector("#transactionTrendsChart"), trendOptions);
                const distributionChart = new ApexCharts(document.querySelector("#distributionChart"), distributionOptions);

                trendsChart.render();
                distributionChart.render();
            } catch (error) {
                console.error('Error initializing charts:', error);
            }
        });
    </script>
    @endpush
</x-app-layout>
