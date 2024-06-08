@extends('layouts.main')

@section('title', 'Архив «' . $company->name . '»')

@section('nav_right')
    <li class="nav-item">
        <a class="btn btn-danger" aria-current="page" href="{{ route('company.remove_last_report', compact('company')) }}">
            Удалить последний отчёт
        </a>
    </li>
@endsection

@section('content')
    <h1 class="text-center my-4">
        Архив <b>{{ $company->name }}</b>
    </h1>

    <div class="container">
        <div class="accordion" id="reportsAccordion">
            @foreach ($groupedReports as $month => $reports)
                <div class="card mb-3 overflow-hidden">
                    <div class="card-header p-2" id="heading-{{ $month }}">
                        <h2 class="mb-0 d-flex justify-content-between align-items-center">
                            <div class="h3 mx-2 my-1" data-bs-toggle="collapse" aria-expanded="true"
                                aria-controls="collapse-{{ $month }}" data-bs-target="#collapse-{{ $month }}">
                                {{ $month }}</div>
                            <span>
                                <span class="badge bg-secondary text-light p-2 px-3 mx-2">{{ count($reports) }} </span>

                                <a class="btn btn-success" href="{{ reset($reports)['url'] }}">
                                    <i class="bi bi-download"></i>
                                </a>

                                <button class="btn btn-primary" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapse-{{ $month }}" aria-expanded="true"
                                    aria-controls="collapse-{{ $month }}">
                                    <i class="bi bi-nintendo-switch"></i>
                                </button>
                            </span>
                        </h2>
                    </div>

                    <div id="collapse-{{ $month }}" class="collapse" aria-labelledby="heading-{{ $month }}"
                        data-bs-parent="#reportsAccordion">
                        <div class="card-body">

                            <div class="m-1 border rounded p-2 pt-3 row">
                                <div class="col-2 h4">
                                    День
                                </div>
                                <div class="col-3 h4 text-end">
                                    Сум
                                </div>
                                <div class="col-2 h4 text-end">
                                    Шт
                                </div>
                                <div class="col-2 h4 text-end">
                                    Факт
                                </div>
                                <div class="col-3 h4 text-end">

                                </div>
                            </div>

                            <div class="accordion" id="dayReportsAccordion-{{ $month }}">

                            @foreach ($reports as $reportData)
                                <div class="card overflow-hidden m-1">
                                    <div class="card-header p-2 " id="heading-{{ $reportData['report']->for_date }}">
                                        {{-- <h2 class="mb-0 d-flex justify-content-between align-items-center"> --}}
                                        <h2 class="row m-0 d-flex align-items-center">
                                            <div class="mb-0 pb-0 col-2 h4">
                                                {{ (int) explode('-', $reportData['report']->for_date)[2] }}
                                            </div>
                                            <div class="mb-0 pb-0 col-3 text-end h4 text-end">
                                                {{ number_format($reportData['sum'], 0, '', ' ') }}
                                            </div>
                                            <div class="mb-0 pb-0 col-2 text-end h4 text-end">
                                                {{ $reportData['quantity'] }}
                                            </div>
                                            <div class="mb-0 pb-0 col-2 text-end h4 text-end">
                                                {{ $reportData['fact'] }}
                                            </div>
                                            <span class="mb-0 pb-0 col-3 text-end">
                                                <a class="btn btn-success" href="{{ $reportData['url'] }}">
                                                    <i class="bi bi-download"></i>
                                                </a>

                                                <button class="btn btn-primary" type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#collapse-{{ $reportData['report']->for_date }}"
                                                    aria-expanded="true"
                                                    aria-controls="collapse-{{ $reportData['report']->for_date }}">
                                                    <i class="bi bi-nintendo-switch"></i>
                                                </button>
                                            </span>
                                        </h2>
                                    </div>

                                    <div id="collapse-{{ $reportData['report']->for_date }}" class="collapse"
                                        aria-labelledby="heading-{{ $reportData['report']->for_date }}"
                                        data-bs-parent="#dayReportsAccordion-{{ $month }}">
                                        <div class="card-body">

                                            {{-- Начало превью отчёта --}}

                                            <div class="bg-body-tertiary rounded p-3 mb-2">

                                                @php
                                                    $sales = (array) ((array) json_decode(
                                                        $reportData['report']['data'],
                                                    ))['Sales'];

                                                    $totalSum = array_sum($sales);

                                                    $percentages = [];
                                                    foreach ($sales as $id => $sale) {
                                                        if ((int) $sale == 0) {
                                                            $percentages[$id] = 0;
                                                            continue;
                                                        }

                                                        $percentage = ($sale / $totalSum) * 100;
                                                        $percentages[$id] = round($percentage, 1);
                                                    }

                                                    $now_men = 0;
                                                    $mon_men = 0;

                                                @endphp

                                                <h2>Продажи</h2>

                                                <table class="table mb-1 overflow-hidden">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col">#</th>
                                                            <th scope="col">Имя</th>
                                                            <th scope="col">Сегодня</th>
                                                            <th scope="col">%</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>

                                                        @foreach ($sales as $id => $sale)
                                                            @php
                                                                $worker = App\Models\User::where('id', $id)->first();
                                                            @endphp

                                                            <tr>
                                                                <th scope="row">{{ $loop->iteration }}</th>
                                                                <td>{{ $worker->full_name }}</td>
                                                                <td class="text-nowrap overflow-hidden">{{ $sales[$id] }}
                                                                    шт</td>
                                                                <td class="text-nowrap overflow-hidden">
                                                                    {{ $percentages[$id] }} %</td>
                                                            </tr>

                                                            @php
                                                                $now_men += (int) $sales[$id];
                                                            @endphp
                                                        @endforeach

                                                        <tr>
                                                            <th scope="row">#</th>
                                                            <td><b>Всего</b></td>
                                                            <td class="text-nowrap overflow-hidden">{{ $now_men }} шт
                                                            </td>
                                                            <td class="text-nowrap overflow-hidden">%</td>
                                                        </tr>

                                                    </tbody>
                                                </table>

                                            </div>

                                            {{-- Конец превью отчёта --}}

                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="card mt-5">
            <div class="card-header">
                <h2 class="mb-0">
                    Старый архив
                </h2>
            </div>
            <div class="card-body">

                <div class="m-1 border rounded p-2 pt-3 row">
                    <div class="col-3 h4">
                        Дата
                    </div>
                    <div class="col-3 h4 text-end">
                        Сум
                    </div>
                    <div class="col-3 h4 text-end">
                        Шт
                    </div>
                    <div class="col-3 h4 text-end">
                        Факт
                    </div>
                </div>

                @foreach ($files_data as $file)
                    <div class="m-1 border rounded p-2 pt-3 row">
                        <div class="col-3 h4">
                            <a href="{{ $file->url }}">{{ $file->date }}</a>
                        </div>
                        <div class="col-3 h4 text-end">
                            {{ $file->sum }}
                        </div>
                        <div class="col-3 h4 text-end">
                            {{ $file->count }}
                        </div>
                        <div class="col-3 h4 text-end">
                            {{ $file->fakt }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
