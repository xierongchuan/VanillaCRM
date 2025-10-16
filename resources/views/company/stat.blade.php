@extends('layouts.main')

@section('title', 'Статистика')

@section('includes')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection

@section('content')

    {{-- Vue 3 Statistics Dashboard Component (Stage 10) --}}
    <stats-dashboard
        :sales-data="{{ json_encode($sales) }}"
        :growth-data="{{ json_encode($growthStatistics) }}"
        :company-names="{{ json_encode($companyNames) }}"
    ></stats-dashboard>

    {{--
    ============================================================
    ORIGINAL IMPLEMENTATION (PRESERVED FOR ROLLBACK)
    ============================================================
    Below is the original Bootstrap + inline scripts implementation.
    To rollback to the old version, uncomment this section and remove
    the <stats-dashboard> component above.
    ============================================================

    <div class="m-2"></div>

    <span id="repsAccordion" class="accordion">

        <div class="bg-transparent mt-2 rounded p-3 pb-2 mb-0">
            <div class="d-flex flex-wrap justify-content-center">
                <h2>Статистика менеджеров</h2>
                <button class="btn btn-secondary mx-2 py-0" data-bs-toggle="collapse" data-bs-target="#collapse-sales"
                    aria-expanded="false" aria-controls="collapse-sales">
                    <i class="bi bi-nintendo-switch"></i>
                </button>
            </div>
        </div>


        <div id="collapse-sales" class="collapse" aria-labelledby="heading-sales" data-bs-parent="#salesAccordion">

            @foreach ($sales as $companyId => $sale)
                <div class="row justify-content-center">
                    <div class="col-lg-9 justify-content-center bg-body-secondary rounded border my-2 p-2">
                        @php
                            $company = App\Models\Company::where('id', $companyId)->first();
                        @endphp

                        <div class="bg-body-tertiary rounded p-3 pb-2 mb-2">
                            <div class="d-flex flex-wrap justify-content-center">
                                <h3>{{ $company->name }}</h3>
                            </div>
                        </div>

                        <div class="col-lg-12 my-2 p-2">
                            <canvas id="managerStatsChart-{{ $companyId }}"></canvas>
                        </div>
                    </div>
                </div>

                <script>
                    // Initialize chart after Vue mounts (use immediate timeout to defer execution)
                    setTimeout(function() {
                        const data = @json($sale);
                        const labels = Object.keys(data).sort(); // Даты в порядке возрастания

                        const datasets = Object.keys(data[Object.keys(data)[0]]).map(managerName => {
                            return {
                                label: managerName,
                                data: labels.map(date => data[date][managerName]),
                                fill: false,
                                borderColor: getRandomColor(),
                                tension: 0.1
                            };
                        });

                        const ctx = document.getElementById('managerStatsChart-{{ $companyId }}').getContext('2d');
                        const managerStatsChart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: labels,
                                datasets: datasets
                            },
                            options: {
                                responsive: true,
                                scales: {
                                    x: {
                                        title: {
                                            display: true,
                                            text: 'Дата'
                                        }
                                    },
                                    y: {
                                        title: {
                                            display: true,
                                            text: 'Продажи'
                                        }
                                    }
                                }
                            }
                        });

                        function getRandomColor() {
                            const letters = '0123456789ABCDEF';
                            let color = '#';
                            for (let i = 0; i < 6; i++) {
                                color += letters[Math.floor(Math.random() * 16)];
                            }
                            return color;
                        }
                    }, 100);
                </script>
            @endforeach

        </div>

        <hr>

        <div class="bg-transparent mt-2 rounded p-3 pb-2 mb-0">
            <div class="d-flex flex-wrap justify-content-center">
                <h2>Статистика отчётов</h2>
                <button class="btn btn-secondary mx-2 py-0" data-bs-toggle="collapse" data-bs-target="#collapse-reps"
                    aria-expanded="false" aria-controls="collapse-reps">
                    <i class="bi bi-nintendo-switch"></i>
                </button>
            </div>
        </div>

        <div id="collapse-reps" class="collapse" aria-labelledby="heading-reps" data-bs-parent="#repsAccordion">

            @foreach ($growthStatistics as $companyId => $data)
                <div class="row justify-content-center">
                    <div class="col-lg-9 justify-content-center bg-body-secondary rounded border my-2 p-2">
                        @php
                            $company = App\Models\Company::where('id', $companyId)->first();
                        @endphp

                        <div class="bg-body-tertiary rounded p-3 pb-2 mb-2">
                            <div class="d-flex flex-wrap justify-content-center">
                                <h3>{{ $company->name }}</h3>
                            </div>
                        </div>

                        <div class="col-lg-12 my-2 p-2 border rounded">
                            <div class="d-flex flex-wrap justify-content-center">
                                <h4>Ежедневная</h3>
                            </div>

                            <canvas id="repsEveryDayPcStatsChart-{{ $companyId }}"></canvas>

                            <hr class="my-2">

                            <canvas id="repsEveryDaySumStatsChart-{{ $companyId }}"></canvas>
                        </div>

                        <div class="col-lg-12 my-2 p-2 border rounded">
                            <div class="d-flex flex-wrap justify-content-center">
                                <h4>Месяц</h3>
                            </div>

                            <canvas id="repsOfMonthPcStatsChart-{{ $companyId }}"></canvas>

                            <hr class="my-2">

                            <canvas id="repsOfMonthSumStatsChart-{{ $companyId }}"></canvas>
                        </div>
                    </div>
                </div>

                <script>

                    // 1

                    setTimeout(function() {
                        const data = @json($data);
                        const labels = Object.keys(data).sort(); // Даты в порядке возрастания

                        const reportData = {
                            'contracts': 'Договора',
                            'payment_quantity': 'Оплата',
                        };


                        const datasets = Object.keys(data[Object.keys(data)[0]])
                            .map(name => {
                                if (name in reportData) {
                                    return {
                                        label: reportData[name],
                                        data: labels.map(date => data[date][name]),
                                        fill: false,
                                        borderColor: getRandomColor(),
                                        tension: 0.1
                                    };
                                }
                            })
                            .filter(dataset => dataset !== undefined); // Удаляем undefined значения


                        const ctx = document.getElementById('repsEveryDayPcStatsChart-{{ $companyId }}').getContext('2d');
                        const managerStatsChart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: labels,
                                datasets: datasets
                            },
                            options: {
                                responsive: true,
                                scales: {
                                    x: {
                                        title: {
                                            display: true,
                                            text: 'Дата'
                                        }
                                    },
                                    y: {
                                        title: {
                                            display: true,
                                            text: 'Шт'
                                        }
                                    }
                                }
                            }
                        });

                        function getRandomColor() {
                            const letters = '0123456789ABCDEF';
                            let color = '#';
                            for (let i = 0; i < 6; i++) {
                                color += letters[Math.floor(Math.random() * 16)];
                            }
                            return color;
                        }
                    }, 100);
                    setTimeout(function() {
                        const data = @json($data);
                        const labels = Object.keys(data).sort(); // Даты в порядке возрастания

                        const reportData = {
                            'total': 'Банк',
                            'additional_payment': 'Доплата',
                            'payment_sum': 'Всего',
                            'leasing': 'Лизинг',
                        };


                        const datasets = Object.keys(data[Object.keys(data)[0]])
                            .map(name => {
                                if (name in reportData) {
                                    return {
                                        label: reportData[name],
                                        data: labels.map(date => data[date][name]),
                                        fill: false,
                                        borderColor: getRandomColor(),
                                        tension: 0.1
                                    };
                                }
                            })
                            .filter(dataset => dataset !== undefined); // Удаляем undefined значения


                        const ctx = document.getElementById('repsEveryDaySumStatsChart-{{ $companyId }}').getContext('2d');
                        const managerStatsChart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: labels,
                                datasets: datasets
                            },
                            options: {
                                responsive: true,
                                scales: {
                                    x: {
                                        title: {
                                            display: true,
                                            text: 'Дата'
                                        }
                                    },
                                    y: {
                                        title: {
                                            display: true,
                                            text: 'Сумм'
                                        }
                                    }
                                }
                            }
                        });

                        function getRandomColor() {
                            const letters = '0123456789ABCDEF';
                            let color = '#';
                            for (let i = 0; i < 6; i++) {
                                color += letters[Math.floor(Math.random() * 16)];
                            }
                            return color;
                        }
                    }, 100);

                    // 2

                    setTimeout(function() {
                        const data = @json($data);
                        const labels = Object.keys(data).sort(); // Даты в порядке возрастания

                        const reportData = {
                            'actual_quantity': 'Факт',
                            'plan_quantity': 'План',
                            // 'contracts_2': 'Договора',
                        };


                        const datasets = Object.keys(data[Object.keys(data)[0]])
                            .map(name => {
                                if (name in reportData) {
                                    return {
                                        label: reportData[name],
                                        data: labels.map(date => data[date][name]),
                                        fill: false,
                                        borderColor: getRandomColor(),
                                        tension: 0.1
                                    };
                                }
                            })
                            .filter(dataset => dataset !== undefined); // Удаляем undefined значения


                        const ctx = document.getElementById('repsOfMonthPcStatsChart-{{ $companyId }}').getContext('2d');
                        const managerStatsChart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: labels,
                                datasets: datasets
                            },
                            options: {
                                responsive: true,
                                scales: {
                                    x: {
                                        title: {
                                            display: true,
                                            text: 'Дата'
                                        }
                                    },
                                    y: {
                                        title: {
                                            display: true,
                                            text: 'Шт'
                                        }
                                    }
                                }
                            }
                        });

                        function getRandomColor() {
                            const letters = '0123456789ABCDEF';
                            let color = '#';
                            for (let i = 0; i < 6; i++) {
                                color += letters[Math.floor(Math.random() * 16)];
                            }
                            return color;
                        }
                    }, 100);
                    setTimeout(function() {
                        const data = @json($data);
                        const labels = Object.keys(data).sort(); // Даты в порядке возрастания

                        const reportData = {
                            'actual_sum': 'Факт',
                            'plan_sum': 'План',
                            'payment_3': 'Банк',
                            'additional_payment_3': 'Лизинг',
                            'leasing_3': 'Доплата',
                            'balance_3': 'Остаток',
                        };


                        const datasets = Object.keys(data[Object.keys(data)[0]])
                            .map(name => {
                                if (name in reportData) {
                                    return {
                                        label: reportData[name],
                                        data: labels.map(date => data[date][name]),
                                        fill: false,
                                        borderColor: getRandomColor(),
                                        tension: 0.1
                                    };
                                }
                            })
                            .filter(dataset => dataset !== undefined); // Удаляем undefined значения


                        const ctx = document.getElementById('repsOfMonthSumStatsChart-{{ $companyId }}').getContext('2d');
                        const managerStatsChart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: labels,
                                datasets: datasets
                            },
                            options: {
                                responsive: true,
                                scales: {
                                    x: {
                                        title: {
                                            display: true,
                                            text: 'Дата'
                                        }
                                    },
                                    y: {
                                        title: {
                                            display: true,
                                            text: 'Сумм'
                                        }
                                    }
                                }
                            }
                        });

                        function getRandomColor() {
                            const letters = '0123456789ABCDEF';
                            let color = '#';
                            for (let i = 0; i < 6; i++) {
                                color += letters[Math.floor(Math.random() * 16)];
                            }
                            return color;
                        }
                    }, 100);
                </script>
            @endforeach

        </div>

    </span>

    ============================================================
    END OF ORIGINAL IMPLEMENTATION
    ============================================================
    --}}

@endsection
