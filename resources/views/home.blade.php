@extends('layouts.main')

@section('title', 'Отчёты')

@section('content')

    <div class="m-2"></div>

    @if (isset($companies))

        @foreach ($companies as $company)
            @php

                $data = (array) json_decode($company->data);
            @endphp

            @if (!empty($data))
                <div class="row flex-column align-items-center">
                    <div class="col-lg-9 bg-body-secondary rounded my-2 p-2">
                        <span class="d-flex justify-content-between">
                            <h1 class="m-0 mx-1 perm_panel_switch" panel="perm_panel_{{ $company->id }}">
                                <b>{{ $company->name }}</b>
                            </h1>
                            <div class="order-last lead">
                                <button class="btn btn-primary slider_prev_button" section-id="{{ $company->id }}">
                                    < </button>
                                        <button class="btn btn-primary slider_next_button"
                                                section-id="{{ $company->id }}">
                                            > </button>
                                        <button class="btn btn-danger perm_panel_switch" data-bs-toggle="collapse"
                                                panel="perm_panel_{{ $company->id }}"><i
                                                class="bi bi-nintendo-switch"></i></button>
                            </div>
                            {{-- <button class="lead perm_panel_switch order-last" panel="perm_panel_{{$company->id}}"><i class="bi bi-nintendo-switch"></i></button> --}}
                        </span>

                        <span id="perm_panel_{{ $company->id }}">
                            <div id="stat_perm_panel_{{ $company->id }}" class="collapse show perm-panel w-100">
                                <div class="bg-body-tertiary mt-2 rounded p-3 pb-2 mb-2">
                                    <div class="d-flex flex-wrap justify-content-center">
                                        <h2>Ежедневная статистика</h2>
                                    </div>
                                </div>

                                <div class="bg-body-tertiary mt-2 rounded p-3 mb-2">
                                    <div class="d-flex flex-wrap justify-content-center">
                                        <h4>Дата загрузки <a
                                                href="{{ $last_repor_urls[$loop->index] }}">отчёта</a>:</h4>
                                        <div class="mx-sm-1"></div>
                                        <h4>{{ date('d.m.Y H:i', strtotime($data['Дата'])) }}</h4>
                                    </div>
                                </div>

                                <div class="bg-body-tertiary rounded p-3 mb-2">
                                    <div class="d-flex flex-wrap justify-content-center">
                                        @if ($data['Clear Sales'])
                                            <h2>Месяц Закрыт</h2>
                                        @else
                                            <h2>Сегодня</h2>
                                        @endif
                                    </div>
                                    <div class="col">
                                        <div
                                            class="col-md-8 my-1 m-auto border rounded p-2 d-flex justify-content-between h4">
                                            <span>
                                                <b>Договора</b>:
                                            </span>

                                            <span>
                                                <b>{{ $data['Договора'] }}</b>
                                            </span>
                                        </div>

                                        <div
                                            class="col-md-8 my-1 m-auto border rounded p-2 d-flex justify-content-between h4">
                                            <span>
                                                <b>Оплата</b>:
                                            </span>

                                            <span>
                                                <b>{{ $data['Оплата Кол-во'] }}</b>
                                            </span>

                                        </div>

                                        <div
                                            class="col-md-8 my-1 m-auto border rounded p-2 d-flex justify-content-between h4">
                                            <span>
                                                <b>Лизинг</b>:
                                            </span>

                                            <span>
                                                <b>{{ $data['Лизинг'] }}</b>
                                            </span>

                                        </div>

                                        <div class="m-3"></div>

                                        <div
                                            class="col-md-8 my-1 m-auto border rounded p-2 d-flex justify-content-between h4">
                                            <span>
                                                <b>Банк</b>:
                                            </span>

                                            <span>
                                                <b>{{ number_format((int) $data['Всего'], 0, '', ' ') }}</b>
                                            </span>
                                        </div>

                                        <div
                                            class="col-md-8 my-1 m-auto border rounded p-2 d-flex justify-content-between h4">
                                            <span>
                                                <b>Доплата</b>:
                                            </span>

                                            <span>
                                                <b>{{ number_format((int) $data['Доплата'], 0, '', ' ') }}</b>
                                            </span>
                                        </div>

                                        <div
                                            class="col-md-8 my-1 m-auto border rounded p-2 d-flex justify-content-between h4">
                                            <span>
                                                <b>Всего</b>:
                                            </span>

                                            <span>
                                                <b>{{ number_format((int) $data['Оплата Сумм'], 0, '', ' ') }}</b>
                                            </span>
                                        </div>

                                    </div>
                                </div>

                                <div class="bg-body-tertiary rounded p-3 mb-2">
                                    <div class="d-flex flex-wrap justify-content-center">
                                        <h2 class="mb-0">Этот месяц</h2>
                                    </div>

                                    <div class="row">

                                        <div class="mb-2">

                                            <div class="col-md-9 mt-1 row m-auto justify-content-between h4">

                                                <span class="col-md-4 p-2 m-auto h4 d-none d-md-block">
                                                    Факт
                                                </span>

                                                <span class="col-md-4">

                                                </span>


                                                <span class="col-md-4 p-2 text-md-end d-none d-md-block">
                                                    План
                                                </span>

                                            </div>


                                            <div class="col-md-9 my-1 row m-auto justify-content-between h4">
                                                <span
                                                    class="col-md-5 p-2 border border-success rounded d-flex justify-content-between">
                                                    <b class="d-block d-md-none">Факт:</b>
                                                    <span><b>{{ $data['Факт Кол-во'] }}</b> </span>
                                                </span>

                                                <span
                                                    class="col-md-2 p-2 m-auto h6 border border-danger rounded text-center">
                                                    <b>{{ $data['% от кол-во'] }}</b> %
                                                </span>


                                                <span
                                                    class="col-md-5 p-2 border rounded d-flex justify-content-md-end justify-content-between">
                                                    <b class="d-block d-md-none">План:</b>
                                                    <span><b>{{ $data['План Кол-во'] }}</b> </span>
                                                </span>

                                            </div>

                                            <div class="m-3 d-block d-md-none"></div>

                                            <div class="col-md-9 my-1 row m-auto justify-content-between h4">
                                                <span
                                                    class="col-md-5 p-2 border border-success rounded d-flex justify-content-between">
                                                    <b class="d-block d-md-none">Факт:</b>
                                                    <span><b>{{ number_format((int) $data['Факт Сумм'], 0, '', ' ') }}</b>
                                                    </span>
                                                </span>

                                                <span
                                                    class="col-md-2 p-2 m-auto h6 border border-danger rounded text-center">
                                                    <b>{{ $data['% от сумм'] }}</b> %
                                                </span>


                                                <span
                                                    class="col-md-5 p-2 border rounded d-flex justify-content-md-end justify-content-between">
                                                    <b class="d-block d-md-none">План:</b>
                                                    <span><b>{{ number_format((int) $data['План Сумм'], 0, '', ' ') }}</b>
                                                    </span>
                                                </span>

                                            </div>


                                        </div>

                                        <div
                                            class="col-md-5 my-1 m-auto border rounded p-2 d-flex justify-content-between lead">
                                            <span>
                                                Договора
                                            </span>

                                            <span>
                                                <b>{{ $data['2 Договора'] }}</b> шт
                                            </span>
                                        </div>

                                        <div
                                            class="col-md-5 my-1 m-auto border border-warning rounded p-2 d-flex justify-content-between lead">
                                            <span>
                                                Конверсия (CV)
                                            </span>

                                            <span>
                                                <b>{{ $data['2 Конверсия'] }}</b> %
                                            </span>
                                        </div>

                                    </div>
                                </div>

                                <div class="bg-body-tertiary rounded p-3 mb-2">
                                    <div class="row">

                                        <div
                                            class="col-md-5 my-1 m-auto border rounded p-2 d-flex justify-content-between lead ">
                                            <span>
                                                Банк
                                            </span>

                                            <span>
                                                <b>{{ number_format((int) $data['3 Оплата'], 0, '', ' ') }}</b> сум
                                            </span>
                                        </div>

                                        <div
                                            class="col-md-5 my-1 m-auto border rounded p-2 d-flex justify-content-between lead">
                                            <span>
                                                Лизинг
                                            </span>

                                            <span>
                                                <b>{{ number_format((int) $data['3 Доплата'], 0, '', ' ') }}</b> сум
                                            </span>
                                        </div>

                                        <div
                                            class="col-md-5 my-1 m-auto border rounded p-2 d-flex justify-content-between lead">
                                            <span>
                                                Доплата
                                            </span>

                                            <span>
                                                <b>{{ number_format((int) $data['3 Лизинг'], 0, '', ' ') }}</b> сум
                                            </span>
                                        </div>

                                        <div
                                            class="col-md-5 my-1 m-auto border rounded p-2 d-flex justify-content-between lead">
                                            <span>
                                                Остаток
                                            </span>

                                            <span>
                                                <b>{{ number_format((int) $data['3 Остаток'], 0, '', ' ') }}</b> сум
                                            </span>
                                        </div>

                                    </div>
                                </div>

                                <div class="bg-body-tertiary rounded p-3 mb-2">

                                    @php

                                        $managers = $data['Продажи'];
                                        $totalSum = 0;
                                        foreach ($managers as $manager) {
                                            $totalSum += (int) $manager->month;
                                        }

                                        $percentages = [];
                                        foreach ($managers as $key => $manager) {
                                            if ((int) $manager->month == 0) {
                                                $percentages[$key] = 0;
                                                continue;
                                            }

                                            $percentage = ($manager->month / $totalSum) * 100;
                                            $percentages[$key] = round($percentage, 1);
                                        }

                                        $now_men = 0;
                                        $mon_men = 0;

                                    @endphp

                                    <h2>Менеджеры</h2>

                                    <table class="table mb-1 rounded overflow-hidden">
                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Имя</th>
                                                <th scope="col">Сегодня</th>
                                                <th scope="col">Мес</th>
                                                <th scope="col">%</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            @foreach ($managers as $key => $manager)
                                                <tr>
                                                    <th scope="row">{{ $loop->iteration }}</th>
                                                    <td>{{ $manager->name }}</td>
                                                    <td class="text-nowrap overflow-hidden">{{ $manager->sold }} шт</td>
                                                    <td class="text-nowrap overflow-hidden">{{ $manager->month }} шт</td>
                                                    <td class="text-nowrap overflow-hidden">{{ $percentages[$key] }} %</td>
                                                </tr>

                                                @php
                                                    $now_men += (int)$manager->sold;
                                                    $mon_men += (int)$manager->month;
                                                @endphp

                                            @endforeach

                                            <tr>
                                                <th scope="row">#</th>
                                                <td><b>Всего</b></td>
                                                <td class="text-nowrap overflow-hidden">{{ $now_men }} шт</td>
                                                <td class="text-nowrap overflow-hidden">{{ $mon_men }} шт</td>
                                                <td class="text-nowrap overflow-hidden">%</td>
                                            </tr>

                                        </tbody>
                                    </table>

                                </div>

                                <div class="bg-body-tertiary rounded p-3 mb-2">

                                    @php
                                        $sums = [$data['5 Через банк сумма'], $data['5 Через лизинг сумма']];
                                        $totalSumSums = array_sum($sums);

                                        $sums_per = [];
                                        foreach ($sums as $key => $sum) {
                                            $percentage = 0;
                                            if ($sum != 0 && $totalSumSums != 0) {
                                                $percentage = ($sum / $totalSumSums) * 100;
                                            }
                                            $sums_per[$key] = round($percentage, 2);
                                        }

                                        $counts = [$data['5 Через банк шт'], $data['5 Через лизинг шт']];
                                        $totalSumCounts = array_sum($counts);

                                        $count_per = [];
                                        foreach ($counts as $key => $sum) {
                                            $percentage = 0;
                                            if ($sum != 0 && $totalSumCounts != 0) {
                                                $percentage = ($sum / $totalSumCounts) * 100;
                                            }
                                            $count_per[$key] = round($percentage, 1);
                                        }
                                    @endphp

                                    <h2>Реализация</h2>

                                    <table class="table mb-1 rounded overflow-hidden">

                                        <tbody>
                                            <tr>
                                                <td>Через банк (шт)</td>
                                                <td class="text-nowrap overflow-hidden text-end">
                                                    {{ $data['5 Через банк шт'] }} </td>
                                                <td class="text-nowrap overflow-hidden text-end" style="width:4rem;">
                                                    {{ $count_per[0] }} %</td>
                                            </tr>
                                            <tr>
                                                <td>Через банк (сумма)</td>
                                                <td class="text-nowrap overflow-hidden text-end">
                                                    {{ number_format((int) $data['5 Через банк сумма'], 0, '', ' ') }}
                                                </td>
                                                <td class="text-nowrap overflow-hidden text-end" style="width:4rem;">
                                                    {{ $sums_per[0] }} %</td>
                                            </tr>
                                            <tr>
                                                <td>Через лизинг (шт)</td>
                                                <td class="text-nowrap overflow-hidden text-end">
                                                    {{ $data['5 Через лизинг шт'] }} </td>
                                                <td class="text-nowrap overflow-hidden text-end" style="width:4rem;">
                                                    {{ $count_per[1] }} %</td>
                                            </tr>
                                            <tr>
                                                <td>Через лизинг (сумма)</td>
                                                <td class="text-nowrap overflow-hidden text-end">
                                                    {{ number_format((int) $data['5 Через лизинг сумма'], 0, '', ' ') }}
                                                </td>
                                                <td class="text-nowrap overflow-hidden text-end" style="width:4rem;">
                                                    {{ $sums_per[1] }} %</td>
                                            </tr>
                                            <tr>
                                                <td>Итог (шт)</td>
                                                <td class="text-nowrap overflow-hidden text-end">{{ $data['5 Итог шт'] }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Итог (сумма)</td>
                                                <td class="text-nowrap overflow-hidden text-end">
                                                    {{ number_format((int) $data['5 Cумма'], 0, '', ' ') }} </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                </div>

                                <div class="bg-body-tertiary rounded p-3 mb-2">

                                    <div class="my-1 m-auto p-0 d-flex justify-content-between">
                                        <h2>Прошлые месяцы</h2>

                                        <a href="{{ route('company.archive', compact('company')) }}"
                                           class="lead">Архив</a>
                                    </div>

                                    <div class="my-1 m-auto border rounded py-2 row h4">

                                        <div class="col-3">
                                            Месяц
                                        </div>

                                        <div class="col-4 text-end">
                                            Сум
                                        </div>

                                        <div class="col-2 text-end">
                                            Шт
                                        </div>

                                        <div class="col-3 text-end">
                                            Факт
                                        </div>
                                    </div>

                                    @foreach ($files_data as $file)
                                        @if ($file->company != $company->name)
                                            @continue
                                        @endif

                                        <div class="my-1 m-auto border rounded py-2 row h4">

                                            <div class="col-3 h4 m-0">
                                                <a href="{{ $file->url }}">{{ $file->date }}</a>
                                            </div>

                                            <div class="col-4 text-end">
                                                {{ $file->sum }}
                                            </div>

                                            <div class="col-2 text-end">
                                                {{ $file->count }}
                                            </div>

                                            <div class="col-3 text-end">
                                                {{ $file->fakt }}
                                            </div>
                                        </div>

                                        @if ($file->company == $company->name)
                                            @break
                                        @endif
                                    @endforeach

                            </div>

                            <div class="bg-body-tertiary rounded p-3 mb-2">

                                <div class="my-1 m-auto p-0 d-flex justify-content-between">
                                    <h2>Ссылки</h2>
                                </div>

                                @foreach ($company->fields as $field)
                                    <div class="my-1 m-auto border rounded py-2 row h4">

                                        <div class="col-6 h4 m-0">
                                            {{ $field->title }}
                                        </div>

                                        <div class="col-6 text-end">
                                            <a href="{{ $field->link }}">Открыть</a>
                                        </div>
                                    </div>
                                @endforeach

                            </div>


                        </div>

                        <div id="serv_perm_panel_{{ $company->id }}" class="collapse perm-panel w-100">
                            <div class="bg-body-tertiary mt-2 rounded p-3 pb-2 mb-2">
                                <div class="d-flex flex-wrap justify-content-center">
                                    <h2>Отчёт сервис</h2>
                                </div>
                            </div>

                            <div class="bg-body-tertiary mt-2 rounded p-3 mb-2">
                                <div class="d-flex flex-wrap justify-content-center">
                                    @if ($srv_reps[$company->id]['created_at'])
                                        <h4>Дата и время загрузки <a
                                                href="{{ route('company.service.archive', [$company, $srv_reps[$company->id]['created_at']]) }}">отчёта</a>:
                                        </h4>
                                        <div class="mx-sm-1"></div>
                                    @endif

                                    <h4>{{ $srv_reps[$company->id]['created_at'] ?? 'Отчёта нету.' }}</h4>

                                    @if ($srv_reps[$company->id]['have'])
                                        <div class="mx-1"></div>
                                        <h4> | На дату: </h4>
                                        <div class="mx-1"></div>

                                        <h4>{{ $srv_reps[$company->id]['for_date']}}</h4>
                                    @endif
                                        <div class="mx-1"></div>

                                    <a href="{{ route('company.service.archive.list', compact('company')) }}"
                                       class="lead">Архив</a>
                                </div>
                            </div>

                            <div class="bg-body-tertiary rounded p-3 mb-2">


                                <table class="table mb-1 rounded overflow-hidden">
                                    <thead>
                                        <tr>
                                            <th scope="col">Навзания</th>
                                            <th scope="col">Сегодня</th>
                                            <th scope="col">За месяц</th>
                                        </tr>

                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Доп</td>
                                            <td class="text-nowrap overflow-hidden text-end">
                                                {{ number_format($srv_reps[$company->id]['dop'], 0, '', ' ') }}
                                            </td>
                                            <td class="text-nowrap overflow-hidden text-end">
                                                {{ number_format($srv_reps[$company->id]['dop_sum'], 0, '', ' ') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Текущий</td>
                                            <td class="text-nowrap overflow-hidden text-end">
                                                {{ number_format($srv_reps[$company->id]['now'], 0, '', ' ') }}
                                            </td>
                                            <td class="text-nowrap overflow-hidden text-end">
                                                {{ number_format($srv_reps[$company->id]['now_sum'], 0, '', ' ') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>ТО</td>
                                            <td class="text-nowrap overflow-hidden text-end">
                                                {{ number_format($srv_reps[$company->id]['to'], 0, '', ' ') }}
                                            </td>
                                            <td class="text-nowrap overflow-hidden text-end">
                                                {{ number_format($srv_reps[$company->id]['to_sum'], 0, '', ' ') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Кузовной</td>
                                            <td class="text-nowrap overflow-hidden text-end">
                                                {{ number_format($srv_reps[$company->id]['kuz'], 0, '', ' ') }}
                                            </td>
                                            <td class="text-nowrap overflow-hidden text-end">
                                                {{ number_format($srv_reps[$company->id]['kuz_sum'], 0, '', ' ') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Магазин</td>
                                            <td class="text-nowrap overflow-hidden text-end">
                                                {{ number_format($srv_reps[$company->id]['store'], 0, '', ' ') }}
                                            </td>
                                            <td class="text-nowrap overflow-hidden text-end">
                                                {{ number_format($srv_reps[$company->id]['store_sum'], 0, '', ' ') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Всего</td>
                                            <td class="text-nowrap overflow-hidden text-end">
                                                {{ number_format($srv_reps[$company->id]['SUM'], 0, '', ' ') }}
                                            </td>
                                            <td class="text-nowrap overflow-hidden text-end">
                                                {{ number_format($srv_reps[$company->id]['SUM_sum'], 0, '', ' ') }}
                                            </td>
                                        </tr>

                                        <tr class="border-3 border-white">
                                            <td>Запчасти</td>
                                            <td class="text-nowrap overflow-hidden text-end">
                                                {{ number_format($srv_reps[$company->id]['zap'], 0, '', ' ') }}
                                            </td>
                                            <td class="text-nowrap overflow-hidden text-end">
                                                {{ number_format($srv_reps[$company->id]['zap_sum'], 0, '', ' ') }}
                                            </td>
                                        </tr>

                                        <tr class="border-3 border-white">
                                            <td>Сервис</td>
                                            <td class="text-nowrap overflow-hidden text-end">
                                                {{ number_format($srv_reps[$company->id]['srv'], 0, '', ' ') }}
                                            </td>
                                            <td class="text-nowrap overflow-hidden text-end">
                                                {{ number_format($srv_reps[$company->id]['srv_sum'], 0, '', ' ') }}
                                            </td>
                                        </tr>
                                    </tbody>

                                </table>

                            </div>


                        </div>
                    </span>
                    </div>
                </div>
            @endif
        @endforeach

    @endif


    @if (@Auth::user()->role === 'user' && !empty($company->data))
        @if (in_array('report_xlsx', $data->perm))
            @php
                $data_com = (array) json_decode(@$company->data);
                $workers = $data_com['Продажи'] ?? [];
            @endphp

            <div class="row flex-column align-items-center">
                <div class="col-lg-9 bg-body-secondary rounded mt-3 p-2">
                <span class="d-flex justify-content-between">
                    <h2 class="perm_panel_switch mb-1" panel="perm_panel_report_xlsx_sales"
                        style="font-size: calc(1.105rem + .66vw);margin-top: 0.1rem;">Продажи менеджеров
                        <b>{{ $company->name }}</b>
                    </h2>
                    <button class="lead perm_panel_switch m-1" panel="perm_panel_report_xlsx_sales"><i
                            class="bi bi-nintendo-switch"></i></button>
                </span>
                    <form id="perm_panel_report_xlsx_sales" action="{{ route('mod.report_xlsx_sales', $company) }}"
                          method="post" enctype="multipart/form-data" class="perm-panel bg-body-tertiary rounded p-3">
                        @csrf
                        @foreach ($workers as $worker)
                            <input type="hidden" name="worker_name_{{ $loop->iteration }}"
                                   value="{{ $worker->name }}">
                            <div class="input-group mb-2">
                                <span class="input-group-text col-7 col-md-9">{{ $worker->name }}</span>
                                <input type="number" class="col-4 col-md-2 form-control repost_xlsx_required_inputs"
                                       name="worker_month_{{ $loop->iteration }}"
                                       id="worker_month_{{ $loop->iteration }}"
                                       placeholder="Sold" value="{{ $worker->month }}" aria-label="Sold" required>
                                <span class="input-group-text px-1 px-md-2 col-2 col-md-1"
                                      id="report_worker_percent_{{ $loop->iteration }}">99.9 %</span>
                            </div>
                        @endforeach

                        <div class="d-flex justify-content-center">
                            <button type="submit" class="btn btn-primary">Изменить</button>
                        </div>
                    </form>
                </div>
            </div>

        @endif

        @if (in_array('report_service', $data->perm))
            @php
                $data_com = (array) json_decode(@$company->data);
                $workers = $data_com['Продажи'] ?? [];
            @endphp

            <div class="row flex-column align-items-center">
                <div class="w-100">
                    <div class="bg-body-tertiary mt-2 rounded p-3 pb-2 mb-2">
                        <div class="d-flex flex-wrap justify-content-center">
                            <h2>Отчёт сервис</h2>
                        </div>
                    </div>

                    <div class="bg-body-tertiary mt-2 rounded p-3 mb-2">
                        <div class="d-flex flex-wrap justify-content-center">
                            @if ($srv_rep['created_at'])
                                <h4>Дата и время загрузки отчёта:
                                </h4>
                                <div class="mx-sm-1"></div>
                            @endif

                            <h4>{{ $srv_rep['created_at'] ?? 'Отчёта нету.' }}</h4>

                            @if ($srv_rep['have'])
                                <div class="mx-sm-1"></div>
                                <h4>| На дату: </h4>
                                <div class="mx-sm-1"></div>

                                <h4>{{ $srv_rep['for_date']}}</h4>
                            @endif
                        </div>
                    </div>

                    <div class="bg-body-tertiary rounded p-3 mb-2">


                        <table class="table mb-1 rounded overflow-hidden">
                            <thead>
                            <tr>
                                <th scope="col">Навзания</th>
                                <th scope="col">Сегодня</th>
                                <th scope="col">За мес</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>Доп</td>
                                <td class="text-nowrap overflow-hidden text-end">
                                    {{ number_format($srv_rep['dop'], 0, '', ' ') }}
                                </td>
                                <td class="text-nowrap overflow-hidden text-end">
                                    {{ number_format($srv_rep['dop_sum'], 0, '', ' ') }}
                                </td>
                            </tr>
                            <tr>
                                <td>Текущий</td>
                                <td class="text-nowrap overflow-hidden text-end">
                                    {{ number_format($srv_rep['now'], 0, '', ' ') }}
                                </td>
                                <td class="text-nowrap overflow-hidden text-end">
                                    {{ number_format($srv_rep['now_sum'], 0, '', ' ') }}
                                </td>
                            </tr>
                            <tr>
                                <td>ТО</td>
                                <td class="text-nowrap overflow-hidden text-end">
                                    {{ number_format($srv_rep['to'], 0, '', ' ') }}
                                </td>
                                <td class="text-nowrap overflow-hidden text-end">
                                    {{ number_format($srv_rep['to_sum'], 0, '', ' ') }}
                                </td>
                            </tr>
                            <tr>
                                <td>Кузовной</td>
                                <td class="text-nowrap overflow-hidden text-end">
                                    {{ number_format($srv_rep['kuz'], 0, '', ' ') }}
                                </td>
                                <td class="text-nowrap overflow-hidden text-end">
                                    {{ number_format($srv_rep['kuz_sum'], 0, '', ' ') }}
                                </td>
                            </tr>
                            <tr>
                                <td>Магазин</td>
                                <td class="text-nowrap overflow-hidden text-end">
                                    {{ number_format($srv_rep['store'], 0, '', ' ') }}
                                </td>
                                <td class="text-nowrap overflow-hidden text-end">
                                    {{ number_format($srv_rep['store_sum'], 0, '', ' ') }}
                                </td>
                            </tr>
                            <tr>
                                <td>Всего</td>
                                <td class="text-nowrap overflow-hidden text-end">
                                    {{ number_format($srv_rep['SUM'], 0, '', ' ') }}
                                </td>
                                <td class="text-nowrap overflow-hidden text-end">
                                    {{ number_format($srv_rep['SUM_sum'], 0, '', ' ') }}
                                </td>
                            </tr>
                            <tr class="border-3 border-white">
                                <td>Запчасти</td>
                                <td class="text-nowrap overflow-hidden text-end">
                                    {{ number_format($srv_rep['zap'], 0, '', ' ') }}
                                </td>
                                <td class="text-nowrap overflow-hidden text-end">
                                    {{ number_format($srv_rep['zap_sum'], 0, '', ' ') }}
                                </td>
                            </tr>

                            <tr class="border-3 border-white">
                                <td>Сервис</td>
                                <td class="text-nowrap overflow-hidden text-end">
                                    {{ number_format($srv_rep['srv'], 0, '', ' ') }}
                                </td>
                                <td class="text-nowrap overflow-hidden text-end">
                                    {{ number_format($srv_rep['srv_sum'], 0, '', ' ') }}
                                </td>
                            </tr>
                            </tbody>

                        </table>

                    </div>


                </div>
            </div>

        @endif

    @endif

@endsection
