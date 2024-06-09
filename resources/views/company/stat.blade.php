@extends('layouts.main')

@section('title', 'Статистика')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="m-2"></div>

    <div class="bg-transparent mt-2 rounded p-3 pb-2 mb-0">
        <div class="d-flex flex-wrap justify-content-center">
            <h2>Статистика менеджеров</h2>
        </div>
    </div>

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
            document.addEventListener('DOMContentLoaded', function() {
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
            });
        </script>
    @endforeach

@endsection
