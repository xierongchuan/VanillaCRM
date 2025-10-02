@extends('layouts.main')

@section('title', 'Отчёты')

@section('content')

    <div class="m-2"></div>

    @if (isset($companies))

        <div class="bg-transparent mt-2 rounded p-3 pb-2 mb-0">
            <div class="d-flex flex-wrap justify-content-center">
                <h2>Отчёты</h2>
            </div>
        </div>

        @foreach ($companies as $company)
            @php
                $data = (array) json_decode(@$coms_data[$company->id]);
            @endphp

            <div class="row flex-column align-items-center">
                <div class="col-lg-9 bg-body-secondary rounded my-2 p-2">
                    <span class="d-flex justify-content-between">
                        <h1 class="m-0 mx-1 perm_panel_switch" panel="perm_panel_{{ $company->id }}">
                            <b>{{ $company->name }}</b>
                        </h1>
                        <div class="order-last lead my-auto">
                            <button class="btn btn-primary slider_prev_button" section-id="{{ $company->id }}">
                                <i class="bi bi-chevron-left"></i>
                            </button>
                            <button class="btn btn-primary slider_next_button" section-id="{{ $company->id }}">
                                <i class="bi bi-chevron-right"></i>
                            </button>
                            <button class="btn btn-danger perm_panel_switch" data-bs-toggle="collapse"
                                panel="perm_panel_{{ $company->id }}"><i class="bi bi-nintendo-switch"></i></button>
                        </div>
                    </span>

                    {{-- @dd($coms_perms) --}}

                    <span id="perm_panel_{{ $company->id }}">
                        @if (in_array('report_xlsx', $coms_perms[$company->id]) && !empty($coms_data[$company->id]))
                            {{-- Ежедневный отчёт --}}
                            <div id="stat_perm_panel_{{ $company->id }}" class="collapse show perm-panel w-100">
                                <div class="bg-body-tertiary mt-2 rounded p-3 pb-2 mb-2">
                                    <div class="d-flex flex-wrap justify-content-center">
                                        <h2>Ежедневная статистика</h2>
                                    </div>
                                </div>

                                <div class="bg-body-tertiary mt-2 rounded p-3 mb-2">
                                    <div class="d-flex flex-wrap justify-content-center">
                                        <h4>Дата загрузки <a href="{{ @$last_repor_urls[$company->id] }}">отчёта</a>:</h4>
                                        <div class="mx-sm-1"></div>
                                        <h4>{{ date('d.m.Y H:i:s', strtotime($data['UploadDate'])) }}</h4>

                                        <div class="mx-1"></div>
                                        <h4> | На дату: </h4>
                                        <div class="mx-1"></div>

                                        <h4>{{ date('d.m.Y', strtotime($data['Дата'])) }}</h4>
                                    </div>
                                </div>

                                <div class="bg-body-tertiary rounded p-3 mb-2">
                                    {{-- <div class="d-flex flex-wrap justify-content-center">
                                            @if ($data['Clear Sales'])
                                                <h2>Месяц Закрыт</h2>
                                            @else
                                                <h2>Сегодня</h2>
                                            @endif
                                        </div> --}}
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
                                                <b>{{ number_format((int) $data['Лизинг'], 0, '', ' ') }}</b>
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
                                                    class="col-md-2 p-2 m-auto h6 border border-danger rounded text-center progress-bar-span"
                                                    data-progress="{{ $data['% от кол-во'] }}">
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
                                                    class="col-md-2 p-2 m-auto h6 border border-danger rounded text-center progress-bar-span"
                                                    data-progress="{{ $data['% от сумм'] }}">
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

                                        $sales = [];

                                        if (isset($sales_data[$company->id])) {
                                            $sales = $sales_data[$company->id];
                                        }

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

                                    <h2>Менеджеры</h2>

                                    <table class="table mb-1 overflow-hidden">
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

                                            @foreach (@$sales as $id => $sale)
                                                @php
                                                    $worker = App\Models\User::where('id', $id)->first();
                                                @endphp

                                                <tr>
                                                    <th scope="row">{{ $loop->iteration }}</th>
                                                    <td>{{ $worker->full_name }}</td>
                                                    <td class="text-nowrap overflow-hidden">
                                                        {{ isset(((array) $data['Sales'])[$id]) ? ((array) $data['Sales'])[$id] : 0 }}
                                                        шт</td>
                                                    <td class="text-nowrap overflow-hidden">
                                                        {{ isset($sales[$id]) ? $sales[$id] : 0 }} шт</td>
                                                    <td class="text-nowrap overflow-hidden">{{ $percentages[$id] }} %</td>
                                                </tr>

                                                @php
                                                    $now_men += (int) (isset(((array) $data['Sales'])[$id])
                                                        ? ((array) $data['Sales'])[$id]
                                                        : 0);
                                                    $mon_men += (int) $sales[$id];
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

                                    <table class="table mb-1 overflow-hidden">

                                        <tbody>
                                            <tr>
                                                <td>DKD/Банк</td>
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
                                                <td>SKD/Лизинг</td>
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
                                    @if (isset(reset($archiveReports[$company->id])->url))
                                        @foreach ($archiveReports[$company->id] as $month => $archiveReport)
                                            <div class="my-1 m-auto border rounded py-2 row h4">

                                                <div class="col-3 h4 m-0">
                                                    <a href="{{ $archiveReport->url }}">{{ $month }}</a>
                                                </div>

                                                <div class="col-4 text-end">
                                                    {{ number_format($archiveReport->sum, 0, '', ' ') }}
                                                </div>

                                                <div class="col-2 text-end">
                                                    {{ $archiveReport->quantity }}
                                                </div>

                                                <div class="col-3 text-end">
                                                    {{ $archiveReport->fact }}
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="my-1 m-auto border border-danger rounded py-2 row h4">

                                            <div class="col-12 d-flex justify-content-center">
                                                Отсутствует отчёт
                                            </div>
                                        </div>
                                    @endif


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
                        @endif

                        @if (in_array('report_service', $coms_perms[$company->id]))
                            {{-- Отчёт сервис --}}
                            <div id="serv_perm_panel_{{ $company->id }}" class="collapse perm-panel w-100">
                                <div class="bg-body-tertiary mt-2 rounded p-3 pb-2 mb-2">
                                    <div class="d-flex flex-wrap justify-content-center">
                                        <h2>Отчёт сервис</h2>
                                    </div>
                                </div>

                                <div class="bg-body-tertiary mt-2 rounded p-3 mb-2">
                                    <div class="d-flex flex-wrap justify-content-center">
                                        @if ($srv_reps[$company->id]['updated_at'])
                                            <h4>Дата загрузки <a
                                                    href="{{ route('company.service.archive', [$company, $srv_reps[$company->id]['updated_at']]) }}">отчёта</a>:
                                            </h4>
                                            <div class="mx-sm-1"></div>
                                        @endif

                                        <h4>{{ $srv_reps[$company->id]['updated_at'] ?? 'Отчёта нету.' }}</h4>

                                        @if ($srv_reps[$company->id]['have'])
                                            <div class="mx-1"></div>
                                            <h4> | На дату: </h4>
                                            <div class="mx-1"></div>

                                            <h4>{{ $srv_reps[$company->id]['for_date'] }}</h4>
                                        @endif
                                        <div class="mx-1"></div>

                                        <a href="{{ route('company.service.archive.list', compact('company')) }}"
                                            class="lead">Архив</a>
                                    </div>
                                </div>

                                <div class="bg-body-tertiary rounded p-3 mb-2">


                                    <table class="table mb-1 overflow-hidden">
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
                        @endif

                        @if (in_array('report_caffe', $coms_perms[$company->id]))
                            {{-- Отчёт кафе --}}
                            <div id="serv_perm_panel_{{ $company->id }}" class="collapse perm-panel w-100">
                                <div class="bg-body-tertiary mt-2 rounded p-3 pb-2 mb-2">
                                    <div class="d-flex flex-wrap justify-content-center">
                                        <h2>Отчёт кафе</h2>
                                    </div>
                                </div>

                                <div class="bg-body-tertiary mt-2 rounded p-3 mb-2">
                                    <div class="d-flex flex-wrap justify-content-center">
                                        @if ($caffe_reps[$company->id]['updated_at'])
                                            <h4>Дата загрузки <a
                                                    href="{{ route('company.caffe.archive', [$company, $caffe_reps[$company->id]['updated_at']]) }}">отчёта</a>:
                                            </h4>
                                            <div class="mx-sm-1"></div>
                                        @endif

                                        <h4>{{ $caffe_reps[$company->id]['updated_at'] ?? 'Отчёта нету.' }}</h4>

                                        @if ($caffe_reps[$company->id]['have'])
                                            <div class="mx-1"></div>
                                            <h4> | На дату: </h4>
                                            <div class="mx-1"></div>

                                            <h4>{{ $caffe_reps[$company->id]['for_date'] }}</h4>
                                        @endif
                                        <div class="mx-1"></div>

                                        <a href="{{ route('company.caffe.archive.list', compact('company')) }}"
                                            class="lead">Архив</a>
                                    </div>
                                </div>

                                <div class="bg-body-tertiary rounded p-3 mb-2" style="overflow-x: auto;">


                                    <table class="table mb-1 overflow-hidden">
                                        <thead>
                                            <tr>
                                                <th scope="col" rowspan="2" style="width: 160px;"
                                                    class="text-center"></th>
                                                <th scope="col" colspan="3" class="text-center">Сегодня</th>
                                            </tr>
                                            <tr>
                                                <th scope="col">Нал</th>
                                                <th scope="col">Без Нал</th>
                                                <th scope="col" class="border-3 border-white">Всего</th>
                                            </tr>

                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td style="width: 160px;" class="responsive-text-vir">
                                                    {{-- Выручка --}}</td>

                                                <td class="format-full text-nowrap overflow-hidden text-end bg-success-tr">
                                                    {{ $caffe_reps[$company->id]['profit_nal'] }}
                                                </td>
                                                <td class="format-full text-nowrap overflow-hidden text-end bg-success-tr">
                                                    {{ $caffe_reps[$company->id]['profit_bez_nal'] }}
                                                </td>
                                                <td
                                                    class="format-full text-nowrap overflow-hidden text-end bg-success-tr border-3 border-white">
                                                    {{ $caffe_reps[$company->id]['profit_SUM'] }}
                                                </td>
                                            </tr>

                                            <tr>
                                                <td style="width: 160px;" class="responsive-text-ras">
                                                    {{-- Расходы --}}</td>

                                                <td class="format-full text-nowrap overflow-hidden text-end bg-danger-tr">
                                                    {{ $caffe_reps[$company->id]['waste_nal'] }}
                                                </td>
                                                <td class="format-full text-nowrap overflow-hidden text-end bg-danger-tr">
                                                    {{ $caffe_reps[$company->id]['waste_bez_nal'] }}
                                                </td>
                                                <td
                                                    class="format-full text-nowrap overflow-hidden text-end bg-danger-tr border-3 border-white">
                                                    {{ $caffe_reps[$company->id]['waste_SUM'] }}
                                                </td>
                                            </tr>

                                            <tr>
                                                <td style="width: 160px;" class="responsive-text-ost">
                                                    {{-- Остаток --}}</td>

                                                <td class="format-full text-nowrap overflow-hidden text-end">
                                                    {{ $caffe_reps[$company->id]['remains_nal'] }}
                                                </td>
                                                <td class="format-full text-nowrap overflow-hidden text-end">
                                                    {{ $caffe_reps[$company->id]['remains_bez_nal'] }}
                                                </td>
                                                <td
                                                    class="format-full text-nowrap overflow-hidden text-end border-3 border-white">
                                                    {{ $caffe_reps[$company->id]['remains_SUM'] }}
                                                </td>
                                            </tr>
                                        </tbody>

                                    </table>

                                    <table class="table mb-1 overflow-hidden">
                                        <thead>
                                            <tr>
                                                <th scope="col" rowspan="2" style="width: 160px;"
                                                    class="text-center"></th>
                                                <th scope="col" colspan="3" class="text-center">За Месяц</th>
                                            </tr>
                                            <tr>
                                                <th scope="col">Нал</th>
                                                <th scope="col">Без Нал</th>
                                                <th scope="col" class="border-3 border-white">Всего</th>
                                            </tr>

                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td style="width: 160px;" class="responsive-text-vir">
                                                    {{-- Выручка --}}</td>

                                                <td class="format-full text-nowrap overflow-hidden text-end bg-success-tr">
                                                    {{ $caffe_reps[$company->id]['profit_nal_sum'] }}
                                                </td>
                                                <td class="format-full text-nowrap overflow-hidden text-end bg-success-tr">
                                                    {{ $caffe_reps[$company->id]['profit_bez_nal_sum'] }}
                                                </td>
                                                <td
                                                    class="format-full text-nowrap overflow-hidden text-end bg-success-tr border-3 border-white">
                                                    {{ $caffe_reps[$company->id]['profit_SUM_sum'] }}
                                                </td>
                                            </tr>

                                            <tr>
                                                <td style="width: 160px;" class="responsive-text-ras">
                                                    {{-- Расходы --}}</td>

                                                <td class="format-full text-nowrap overflow-hidden text-end bg-danger-tr">
                                                    {{ $caffe_reps[$company->id]['waste_nal_sum'] }}
                                                </td>
                                                <td class="format-full text-nowrap overflow-hidden text-end bg-danger-tr">
                                                    {{ $caffe_reps[$company->id]['waste_bez_nal_sum'] }}
                                                </td>
                                                <td
                                                    class="format-full text-nowrap overflow-hidden text-end bg-danger-tr border-3 border-white">
                                                    {{ $caffe_reps[$company->id]['waste_SUM_sum'] }}
                                                </td>
                                            </tr>

                                            <tr>
                                                <td style="width: 160px;" class="responsive-text-ost">
                                                    {{-- Остаток --}}</td>

                                                <td class="format-full text-nowrap overflow-hidden text-end">
                                                    {{ $caffe_reps[$company->id]['remains_nal_sum'] }}
                                                </td>
                                                <td class="format-full text-nowrap overflow-hidden text-end">
                                                    {{ $caffe_reps[$company->id]['remains_bez_nal_sum'] }}
                                                </td>
                                                <td
                                                    class="format-full text-nowrap overflow-hidden text-end border-3 border-white">
                                                    {{ $caffe_reps[$company->id]['remains_SUM_sum'] }}
                                                </td>
                                            </tr>
                                        </tbody>

                                    </table>

                                    <table class="table mb-1 overflow-hidden">
                                        <thead>
                                            <tr>
                                                <th scope="col"></th>
                                                <th scope="col">Нал</th>
                                                <th scope="col">Без Нал</th>
                                                <th scope="col" class="border-3 border-white">Всего</th>
                                            </tr>

                                        </thead>
                                        <tbody>

                                            <tr>
                                                <td style="width: 160px;" class="responsive-text-safe">
                                                    {{-- Сейф --}}</td>

                                                <td class="format-full text-nowrap overflow-hidden text-end">
                                                    {{ $caffe_reps[$company->id]['safe_nal'] }}
                                                </td>
                                                <td class="format-full text-nowrap overflow-hidden text-end">
                                                    {{ $caffe_reps[$company->id]['safe_bez_nal'] }}
                                                </td>
                                                <td
                                                    class="format-full text-nowrap overflow-hidden text-end border-3 border-white">
                                                    {{ $caffe_reps[$company->id]['safe_SUM'] }}
                                                </td>
                                            </tr>
                                        </tbody>

                                    </table>

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
                        @endif

                        @if (in_array('report_cashier', $coms_perms[$company->id]))
                            {{-- Отчёт кассир --}}
                            <div id="cashier_perm_panel_{{ $company->id }}" class="collapse perm-panel w-100">
                                <div class="bg-body-tertiary mt-2 rounded p-3 pb-2 mb-2">
                                    <div class="d-flex flex-wrap justify-content-center">
                                        <h2>Отчёт Кассир</h2>
                                    </div>
                                </div>

                                <div class="bg-body-tertiary mt-2 rounded p-3 mb-2">
                                    <div class="d-flex flex-wrap justify-content-center">
                                        @if (isset($cashier_reps[$company->id]['updated_at']) && $cashier_reps[$company->id]['updated_at'])
                                            <h4>Дата загрузки <a
                                                    href="{{ route('company.cashier.archive', [$company, $cashier_reps[$company->id]['updated_at']]) }}">отчёта</a>:
                                            </h4>
                                            <div class="mx-sm-1"></div>
                                        @endif

                                        <h4>{{ $cashier_reps[$company->id]['updated_at'] ?? 'Отчёта нету.' }}</h4>

                                        @if (isset($cashier_reps[$company->id]['have']) && $cashier_reps[$company->id]['have'])
                                            <div class="mx-1"></div>
                                            <h4> | На дату: </h4>
                                            <div class="mx-1"></div>

                                            <h4>{{ $cashier_reps[$company->id]['for_date'] }}</h4>
                                        @endif
                                        <div class="mx-1"></div>

                                        <a href="{{ route('company.cashier.archive.list', compact('company')) }}"
                                            class="lead">Архив</a>
                                    </div>
                                </div>

                                <div class="bg-body-tertiary rounded p-3 mb-2">
                                    <table class="table mb-3 overflow-hidden">
                                        <tr>
                                            <th scope="col" style="width: 33.33%;">Параметр</th>
                                            <th scope="col" style="width: 33.33%;">Значение</th>
                                            <th scope="col" style="width: 33.33%;">Сумма за месяц</th>
                                        </tr>
                                        <tbody>
                                            <tr>
                                                <td>Ссылка на отчёт</td>
                                                <td colspan="2">
                                                    @if (!empty($cashier_reps[$company->id]['link']))
                                                        <a href="{{ $cashier_reps[$company->id]['link'] }}"
                                                            target="_blank">Открыть отчёт</a>
                                                    @else
                                                        Нет ссылки
                                                    @endif
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>Оборот</td>

                                                <td class="text-end">
                                                    <div
                                                        class="d-flex justify-content-end flex-column flex-md-row align-items-end gap-1 gap-md-3">
                                                        <div class="text-nowrap text-success">
                                                            {{ number_format($cashier_reps[$company->id]['oborot_plus'], 0, '', ' ') }}
                                                        </div>
                                                        <div class="text-nowrap d-none d-md-block">|</div>

                                                        <div class="text-nowrap text-danger">
                                                            {{ number_format($cashier_reps[$company->id]['oborot_minus'], 0, '', ' ') }}
                                                        </div>
                                                    </div>
                                                </td>

                                                <td class="text-end">
                                                    <div
                                                        class="d-flex justify-content-end flex-column flex-md-row align-items-end gap-1 gap-md-3">
                                                        <div class="text-nowrap text-success">
                                                            {{ number_format($cashier_reps[$company->id]['oborot_plus_sum'], 0, '', ' ') }}
                                                        </div>
                                                        <div class="text-nowrap d-none d-md-block">|</div>

                                                        <div class="text-nowrap text-danger">
                                                            {{ number_format($cashier_reps[$company->id]['oborot_minus_sum'], 0, '', ' ') }}
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>Сальдо</td>
                                                <td class="text-nowrap overflow-hidden text-end">
                                                    {{ number_format($cashier_reps[$company->id]['saldo'], 0, '', ' ') }}
                                                </td>
                                                <td class="text-nowrap overflow-hidden text-end">
                                                    {{ number_format($cashier_reps[$company->id]['saldo_sum'], 0, '', ' ') }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <table class="table mb-1 overflow-hidden">
                                        <colgroup>
                                            <col style="width: 33.33%;">
                                            <col style="width: 33.33%;">
                                            <col style="width: 33.33%;">
                                        </colgroup>
                                        <tbody>
                                            <tr>
                                                <td>Наличка</td>
                                                <td class="text-nowrap overflow-hidden text-end">
                                                    {{ number_format($cashier_reps[$company->id]['nalichka'], 0, '', ' ') }}
                                                </td>
                                                <td class="text-nowrap overflow-hidden text-end">
                                                    {{ number_format($cashier_reps[$company->id]['nalichka_sum'], 0, '', ' ') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Р/С</td>
                                                <td class="text-nowrap overflow-hidden text-end">
                                                    {{ number_format($cashier_reps[$company->id]['rs'], 0, '', ' ') }}
                                                </td>
                                                <td class="text-nowrap overflow-hidden text-end">
                                                    {{ number_format($cashier_reps[$company->id]['rs_sum'], 0, '', ' ') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Пластик</td>
                                                <td class="text-nowrap overflow-hidden text-end">
                                                    {{ number_format($cashier_reps[$company->id]['plastic'], 0, '', ' ') }}
                                                </td>
                                                <td class="text-nowrap overflow-hidden text-end">
                                                    {{ number_format($cashier_reps[$company->id]['plastic_sum'], 0, '', ' ') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Скидки</td>
                                                <td class="text-nowrap overflow-hidden text-end">
                                                    {{ number_format($cashier_reps[$company->id]['skidki'], 0, '', ' ') }}
                                                </td>
                                                <td class="text-nowrap overflow-hidden text-end">
                                                    {{ number_format($cashier_reps[$company->id]['skidki_sum'], 0, '', ' ') }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="bg-body-tertiary mt-2 rounded p-3 mb-2">
                                    <div class="d-flex flex-wrap justify-content-center">
                                        <a href="{{ route('company.expense.requests', compact('company')) }}"
                                            class="btn btn-primary">История запросов</a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </span>
                </div>
            </div>
        @endforeach
    @endif


    @if (@Auth::user()->role === 'user')
        @if (in_array('report_xlsx', $data->perm) && !empty($data->com_data))

            @php
                $dataCom = (array) json_decode($data->com_data);
                $sales_data = (array) $data->sales_data;

            @endphp

            <div class="row flex-column align-items-center">
                <div class="col-lg-9 bg-body-secondary rounded my-2 p-2">
                    <span class="d-flex justify-content-center">
                        <h1 class="m-0 mx-1 perm_panel_switch" panel="perm_panel_{{ $company->id }}">
                            <b>{{ $company->name }}</b>
                        </h1>
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
                                    <h4>Дата загрузки <a href="{{ @$last_repor_urls[$loop->index] }}">отчёта</a>:</h4>
                                    <div class="mx-sm-1"></div>
                                    <h4>{{ date('d.m.Y H:i:s', strtotime($dataCom['UploadDate'])) }}</h4>

                                    <div class="mx-1"></div>
                                    <h4> | На дату: </h4>
                                    <div class="mx-1"></div>

                                    <h4>{{ date('d.m.Y', strtotime($dataCom['Дата'])) }}</h4>
                                </div>
                            </div>

                            <div class="bg-body-tertiary rounded p-3 mb-2">
                                {{-- <div class="d-flex flex-wrap justify-content-center">
                                        @if ($dataCom['Clear Sales'])
                                            <h2>Месяц Закрыт</h2>
                                        @else
                                            <h2>Сегодня</h2>
                                        @endif
                                    </div> --}}
                                <div class="col">
                                    <div class="col-md-8 my-1 m-auto border rounded p-2 d-flex justify-content-between h4">
                                        <span>
                                            <b>Договора</b>:
                                        </span>

                                        <span>
                                            <b>{{ $dataCom['Договора'] }}</b>
                                        </span>
                                    </div>

                                    <div class="col-md-8 my-1 m-auto border rounded p-2 d-flex justify-content-between h4">
                                        <span>
                                            <b>Оплата</b>:
                                        </span>

                                        <span>
                                            <b>{{ $dataCom['Оплата Кол-во'] }}</b>
                                        </span>

                                    </div>

                                    <div class="col-md-8 my-1 m-auto border rounded p-2 d-flex justify-content-between h4">
                                        <span>
                                            <b>Лизинг</b>:
                                        </span>

                                        <span>
                                            <b>{{ number_format((int) $dataCom['Лизинг'], 0, '', ' ') }}</b>
                                        </span>

                                    </div>

                                    <div class="m-3"></div>

                                    <div class="col-md-8 my-1 m-auto border rounded p-2 d-flex justify-content-between h4">
                                        <span>
                                            <b>Банк</b>:
                                        </span>

                                        <span>
                                            <b>{{ number_format((int) $dataCom['Всего'], 0, '', ' ') }}</b>
                                        </span>
                                    </div>

                                    <div class="col-md-8 my-1 m-auto border rounded p-2 d-flex justify-content-between h4">
                                        <span>
                                            <b>Доплата</b>:
                                        </span>

                                        <span>
                                            <b>{{ number_format((int) $dataCom['Доплата'], 0, '', ' ') }}</b>
                                        </span>
                                    </div>

                                    <div class="col-md-8 my-1 m-auto border rounded p-2 d-flex justify-content-between h4">
                                        <span>
                                            <b>Всего</b>:
                                        </span>

                                        <span>
                                            <b>{{ number_format((int) $dataCom['Оплата Сумм'], 0, '', ' ') }}</b>
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
                                                <span><b>{{ $dataCom['Факт Кол-во'] }}</b> </span>
                                            </span>

                                            <span
                                                class="col-md-2 p-2 m-auto h6 border border-danger rounded text-center progress-bar-span"
                                                data-progress="{{ $dataCom['% от кол-во'] }}">
                                                <b>{{ $dataCom['% от кол-во'] }}</b> %
                                            </span>


                                            <span
                                                class="col-md-5 p-2 border rounded d-flex justify-content-md-end justify-content-between">
                                                <b class="d-block d-md-none">План:</b>
                                                <span><b>{{ $dataCom['План Кол-во'] }}</b> </span>
                                            </span>

                                        </div>

                                        <div class="m-3 d-block d-md-none"></div>

                                        <div class="col-md-9 my-1 row m-auto justify-content-between h4">
                                            <span
                                                class="col-md-5 p-2 border border-success rounded d-flex justify-content-between">
                                                <b class="d-block d-md-none">Факт:</b>
                                                <span><b>{{ number_format((int) $dataCom['Факт Сумм'], 0, '', ' ') }}</b>
                                                </span>
                                            </span>

                                            <span
                                                class="col-md-2 p-2 m-auto h6 border border-danger rounded text-center progress-bar-span"
                                                data-progress="{{ $dataCom['% от сумм'] }}">
                                                <b>{{ $dataCom['% от сумм'] }}</b> %
                                            </span>


                                            <span
                                                class="col-md-5 p-2 border rounded d-flex justify-content-md-end justify-content-between">
                                                <b class="d-block d-md-none">План:</b>
                                                <span><b>{{ number_format((int) $dataCom['План Сумм'], 0, '', ' ') }}</b>
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
                                            <b>{{ $dataCom['2 Договора'] }}</b> шт
                                        </span>
                                    </div>

                                    <div
                                        class="col-md-5 my-1 m-auto border border-warning rounded p-2 d-flex justify-content-between lead">
                                        <span>
                                            Конверсия (CV)
                                        </span>

                                        <span>
                                            <b>{{ $dataCom['2 Конверсия'] }}</b> %
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
                                            <b>{{ number_format((int) $dataCom['3 Оплата'], 0, '', ' ') }}</b> сум
                                        </span>
                                    </div>

                                    <div
                                        class="col-md-5 my-1 m-auto border rounded p-2 d-flex justify-content-between lead">
                                        <span>
                                            Лизинг
                                        </span>

                                        <span>
                                            <b>{{ number_format((int) $dataCom['3 Доплата'], 0, '', ' ') }}</b> сум
                                        </span>
                                    </div>

                                    <div
                                        class="col-md-5 my-1 m-auto border rounded p-2 d-flex justify-content-between lead">
                                        <span>
                                            Доплата
                                        </span>

                                        <span>
                                            <b>{{ number_format((int) $dataCom['3 Лизинг'], 0, '', ' ') }}</b> сум
                                        </span>
                                    </div>

                                    <div
                                        class="col-md-5 my-1 m-auto border rounded p-2 d-flex justify-content-between lead">
                                        <span>
                                            Остаток
                                        </span>

                                        <span>
                                            <b>{{ number_format((int) $dataCom['3 Остаток'], 0, '', ' ') }}</b> сум
                                        </span>
                                    </div>

                                </div>
                            </div>

                            <div class="bg-body-tertiary rounded p-3 mb-2">

                                @php
                                    $sales = $sales_data;
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

                                <h2>Менеджеры</h2>

                                <table class="table mb-1 overflow-hidden">
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

                                        @foreach ($sales as $id => $sale)
                                            @php
                                                $worker = App\Models\User::where('id', $id)->first();
                                            @endphp

                                            <tr>
                                                <th scope="row">{{ $loop->iteration }}</th>
                                                <td>{{ $worker->full_name }}</td>
                                                <td class="text-nowrap overflow-hidden">
                                                    {{ isset(((array) $dataCom['Sales'])[$id]) ? ((array) $dataCom['Sales'])[$id] : 0 }}
                                                    шт</td>
                                                <td class="text-nowrap overflow-hidden">
                                                    {{ isset($sales[$id]) ? $sales[$id] : 0 }} шт</td>
                                                <td class="text-nowrap overflow-hidden">{{ $percentages[$id] }} %</td>
                                            </tr>

                                            @php
                                                $now_men += (int) (isset(((array) $dataCom['Sales'])[$id])
                                                    ? ((array) $dataCom['Sales'])[$id]
                                                    : 0);
                                                $mon_men += (int) $sales[$id];
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
                                    $sums = [$dataCom['5 Через банк сумма'], $dataCom['5 Через лизинг сумма']];
                                    $totalSumSums = array_sum($sums);

                                    $sums_per = [];
                                    foreach ($sums as $key => $sum) {
                                        $percentage = 0;
                                        if ($sum != 0 && $totalSumSums != 0) {
                                            $percentage = ($sum / $totalSumSums) * 100;
                                        }
                                        $sums_per[$key] = round($percentage, 2);
                                    }

                                    $counts = [$dataCom['5 Через банк шт'], $dataCom['5 Через лизинг шт']];
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

                                <table class="table mb-1 overflow-hidden">

                                    <tbody>
                                        <tr>
                                            <td>DKD</td>
                                            <td class="text-nowrap overflow-hidden text-end">
                                                {{ $dataCom['5 Через банк шт'] }} </td>
                                            <td class="text-nowrap overflow-hidden text-end" style="width:4rem;">
                                                {{ $count_per[0] }} %</td>
                                        </tr>
                                        <tr>
                                            <td>Через банк (сумма)</td>
                                            <td class="text-nowrap overflow-hidden text-end">
                                                {{ number_format((int) $dataCom['5 Через банк сумма'], 0, '', ' ') }}
                                            </td>
                                            <td class="text-nowrap overflow-hidden text-end" style="width:4rem;">
                                                {{ $sums_per[0] }} %</td>
                                        </tr>
                                        <tr>
                                            <td>SKD</td>
                                            <td class="text-nowrap overflow-hidden text-end">
                                                {{ $dataCom['5 Через лизинг шт'] }} </td>
                                            <td class="text-nowrap overflow-hidden text-end" style="width:4rem;">
                                                {{ $count_per[1] }} %</td>
                                        </tr>
                                        <tr>
                                            <td>Через лизинг (сумма)</td>
                                            <td class="text-nowrap overflow-hidden text-end">
                                                {{ number_format((int) $dataCom['5 Через лизинг сумма'], 0, '', ' ') }}
                                            </td>
                                            <td class="text-nowrap overflow-hidden text-end" style="width:4rem;">
                                                {{ $sums_per[1] }} %</td>
                                        </tr>
                                        <tr>
                                            <td>Итог (шт)</td>
                                            <td class="text-nowrap overflow-hidden text-end">{{ $dataCom['5 Итог шт'] }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Итог (сумма)</td>
                                            <td class="text-nowrap overflow-hidden text-end">
                                                {{ number_format((int) $dataCom['5 Cумма'], 0, '', ' ') }} </td>
                                        </tr>
                                    </tbody>
                                </table>

                            </div>
                        </div>

                    </span>
                </div>
            </div>

        @endif

        @if (in_array('report_service', $data->perm))

            <div class="row flex-column align-items-center">
                <div class="w-100">
                    <div class="bg-body-tertiary mt-2 rounded p-3 pb-2 mb-2">
                        <div class="d-flex flex-wrap justify-content-center">
                            <h2>Отчёт сервис</h2>
                        </div>
                    </div>

                    <div class="bg-body-tertiary mt-2 rounded p-3 mb-2">
                        <div class="d-flex flex-wrap justify-content-center">
                            @if ($srv_rep['updated_at'])
                                <h4>Дата и время загрузки отчёта:
                                </h4>
                                <div class="mx-sm-1"></div>
                            @endif

                            <h4>{{ $srv_rep['updated_at'] ?? 'Отчёта нету.' }}</h4>

                            @if ($srv_rep['have'])
                                <div class="mx-sm-1"></div>
                                <h4>| На дату: </h4>
                                <div class="mx-sm-1"></div>

                                <h4>{{ $srv_rep['for_date'] }}</h4>
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

        @if (in_array('report_caffe', $data->perm))

            <div class="row flex-column align-items-center">
                <div class="w-100">
                    <div class="bg-body-tertiary mt-2 rounded p-3 pb-2 mb-2">
                        <div class="d-flex flex-wrap justify-content-center">
                            <h2>Отчёт кафе</h2>
                        </div>
                    </div>

                    <div class="bg-body-tertiary mt-2 rounded p-3 mb-2">
                        <div class="d-flex flex-wrap justify-content-center">
                            @if ($caffe_reps[$company->id]['updated_at'])
                                <h4>Дата загрузки отчёта:
                                </h4>
                                <div class="mx-sm-1"></div>
                            @endif

                            <h4>{{ $caffe_reps[$company->id]['updated_at'] ?? 'Отчёта нету.' }}</h4>

                            @if ($caffe_reps[$company->id]['have'])
                                <div class="mx-1"></div>
                                <h4> | На дату: </h4>
                                <div class="mx-1"></div>

                                <h4>{{ $caffe_reps[$company->id]['for_date'] }}</h4>
                            @endif
                        </div>
                    </div>

                    <div class="bg-body-tertiary rounded p-3 mb-2">


                        <table class="table mb-1 overflow-hidden">
                            <thead>
                                <tr>
                                    <th scope="col" rowspan="2" style="width: 160px;" class="text-center"></th>
                                    <th scope="col" colspan="3" class="text-center">Сегодня</th>
                                </tr>
                                <tr>
                                    <th scope="col">Нал</th>
                                    <th scope="col">Без Нал</th>
                                    <th scope="col" class="border-3 border-white">Всего</th>
                                </tr>

                            </thead>
                            <tbody>
                                <tr>
                                    <td style="width: 160px;" class="responsive-text-vir">{{-- Выручка --}}</td>

                                    <td class="format-full text-nowrap overflow-hidden text-end bg-success">
                                        {{ $caffe_reps[$company->id]['profit_nal'] }}
                                    </td>
                                    <td class="format-full text-nowrap overflow-hidden text-end bg-success">
                                        {{ $caffe_reps[$company->id]['profit_bez_nal'] }}
                                    </td>
                                    <td
                                        class="format-full text-nowrap overflow-hidden text-end bg-success border-3 border-white">
                                        {{ $caffe_reps[$company->id]['profit_SUM'] }}
                                    </td>
                                </tr>

                                <tr>
                                    <td style="width: 160px;" class="responsive-text-ras">{{-- Расходы --}}</td>

                                    <td class="format-full text-nowrap overflow-hidden text-end bg-danger">
                                        {{ $caffe_reps[$company->id]['waste_nal'] }}
                                    </td>
                                    <td class="format-full text-nowrap overflow-hidden text-end bg-danger">
                                        {{ $caffe_reps[$company->id]['waste_bez_nal'] }}
                                    </td>
                                    <td
                                        class="format-full text-nowrap overflow-hidden text-end bg-danger border-3 border-white">
                                        {{ $caffe_reps[$company->id]['waste_SUM'] }}
                                    </td>
                                </tr>

                                <tr>
                                    <td style="width: 160px;" class="responsive-text-ost">{{-- Остаток --}}</td>

                                    <td class="format-full text-nowrap overflow-hidden text-end">
                                        {{ $caffe_reps[$company->id]['remains_nal'] }}
                                    </td>
                                    <td class="format-full text-nowrap overflow-hidden text-end">
                                        {{ $caffe_reps[$company->id]['remains_bez_nal'] }}
                                    </td>
                                    <td class="format-full text-nowrap overflow-hidden text-end border-3 border-white">
                                        {{ $caffe_reps[$company->id]['remains_SUM'] }}
                                    </td>
                                </tr>
                            </tbody>

                        </table>

                        <table class="table mb-1 overflow-hidden">
                            <thead>
                                <tr>
                                    <th scope="col" rowspan="2" style="width: 160px;" class="text-center"></th>
                                    <th scope="col" colspan="3" class="text-center">За Месяц</th>
                                </tr>
                                <tr>
                                    <th scope="col">Нал</th>
                                    <th scope="col">Без Нал</th>
                                    <th scope="col" class="border-3 border-white">Всего</th>
                                </tr>

                            </thead>
                            <tbody>
                                <tr>
                                    <td style="width: 160px;" class="responsive-text-vir">{{-- Выручка --}}</td>

                                    <td class="format-full text-nowrap overflow-hidden text-end bg-success">
                                        {{ $caffe_reps[$company->id]['profit_nal_sum'] }}
                                    </td>
                                    <td class="format-full text-nowrap overflow-hidden text-end bg-success">
                                        {{ $caffe_reps[$company->id]['profit_bez_nal_sum'] }}
                                    </td>
                                    <td
                                        class="format-full text-nowrap overflow-hidden text-end bg-success border-3 border-white">
                                        {{ $caffe_reps[$company->id]['profit_SUM_sum'] }}
                                    </td>
                                </tr>

                                <tr>
                                    <td style="width: 160px;" class="responsive-text-ras">{{-- Расходы --}}</td>

                                    <td class="format-full text-nowrap overflow-hidden text-end bg-danger">
                                        {{ $caffe_reps[$company->id]['waste_nal_sum'] }}
                                    </td>
                                    <td class="format-full text-nowrap overflow-hidden text-end bg-danger">
                                        {{ $caffe_reps[$company->id]['waste_bez_nal_sum'] }}
                                    </td>
                                    <td
                                        class="format-full text-nowrap overflow-hidden text-end bg-danger border-3 border-white">
                                        {{ $caffe_reps[$company->id]['waste_SUM_sum'] }}
                                    </td>
                                </tr>

                                <tr>
                                    <td style="width: 160px;" class="responsive-text-ost">{{-- Остаток --}}</td>

                                    <td class="format-full text-nowrap overflow-hidden text-end">
                                        {{ $caffe_reps[$company->id]['remains_nal_sum'] }}
                                    </td>
                                    <td class="format-full text-nowrap overflow-hidden text-end">
                                        {{ $caffe_reps[$company->id]['remains_bez_nal_sum'] }}
                                    </td>
                                    <td class="format-full text-nowrap overflow-hidden text-end border-3 border-white">
                                        {{ $caffe_reps[$company->id]['remains_SUM_sum'] }}
                                    </td>
                                </tr>
                            </tbody>

                        </table>

                        <table class="table mb-1 overflow-hidden">
                            <thead>
                                <tr>
                                    <th scope="col"></th>
                                    <th scope="col">Нал</th>
                                    <th scope="col">Без Нал</th>
                                    <th scope="col" class="border-3 border-white">Всего</th>
                                </tr>

                            </thead>
                            <tbody>

                                <tr>
                                    <td style="width: 160px;" class="responsive-text-safe">
                                        {{-- Сейф --}}</td>

                                    <td class="format-full text-nowrap overflow-hidden text-end">
                                        {{ $caffe_reps[$company->id]['safe_nal'] }}
                                    </td>
                                    <td class="format-full text-nowrap overflow-hidden text-end">
                                        {{ $caffe_reps[$company->id]['safe_bez_nal'] }}
                                    </td>
                                    <td class="format-full text-nowrap overflow-hidden text-end border-3 border-white">
                                        {{ $caffe_reps[$company->id]['safe_SUM'] }}
                                    </td>
                                </tr>
                            </tbody>

                        </table>


                    </div>


                </div>
            </div>

        @endif

        @if (in_array('report_cashier', $data->perm))

            <div class="row flex-column align-items-center">
                <div class="col-lg-9 bg-body-secondary rounded my-2 p-2">
                    <div class="w-100">
                        <div class="bg-body-tertiary mt-2 rounded p-3 pb-2 mb-2">
                            <div class="d-flex flex-wrap justify-content-center">
                                <h2>Отчёт Кассир</h2>
                            </div>
                        </div>

                        <div class="bg-body-tertiary mt-2 rounded p-3 mb-2">
                            <div class="d-flex flex-wrap justify-content-center">
                                @if ($cashier_rep['updated_at'])
                                    <h4>Дата загрузки отчёта:
                                    </h4>
                                    <div class="mx-sm-1"></div>
                                @endif

                                <h4>{{ $cashier_rep['updated_at'] ?? 'Отчёта нету.' }}</h4>

                                @if ($cashier_rep['have'])
                                    <div class="mx-1"></div>
                                    <h4> | На дату: </h4>
                                    <div class="mx-1"></div>

                                    <h4>{{ $cashier_rep['for_date'] }}</h4>
                                @endif
                            </div>
                        </div>

                        <div class="bg-body-tertiary rounded p-3 mb-2">
                            <table class="table mb-3 overflow-hidden">
                                <thead>
                                    <tr>
                                        <th scope="col" style="width: 33.33%;">Параметр</th>
                                        <th scope="col" style="width: 33.33%;">Значение</th>
                                        <th scope="col" style="width: 33.33%;">Сумма за месяц</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Ссылка на отчёт</td>
                                        <td colspan="2">
                                            @if (!empty($cashier_rep['link']))
                                                <a href="{{ $cashier_rep['link'] }}" target="_blank">Открыть отчёт</a>
                                            @else
                                                Нет ссылки
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Оборот</td>

                                        <td class="text-end">
                                            <div
                                                class="d-flex justify-content-end flex-column flex-md-row align-items-end gap-1 gap-md-3">
                                                <div class="text-nowrap text-success">
                                                    {{ number_format($cashier_rep['oborot_plus'], 0, '', ' ') }}
                                                </div>
                                                <div class="text-nowrap d-none d-md-block">|</div>
                                                <div class="text-nowrap text-danger">
                                                    {{ number_format($cashier_rep['oborot_minus'], 0, '', ' ') }}
                                                </div>
                                            </div>
                                        </td>

                                        <td class="text-end">
                                            <div
                                                class="d-flex justify-content-end flex-column flex-md-row align-items-end gap-1 gap-md-3">
                                                <div class="text-nowrap text-success">
                                                    {{ number_format($cashier_rep['oborot_plus_sum'], 0, '', ' ') }}
                                                </div>
                                                <div class="text-nowrap d-none d-md-block">|</div>

                                                <div class="text-nowrap text-danger">
                                                    {{ number_format($cashier_rep['oborot_minus_sum'], 0, '', ' ') }}
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Сальдо</td>
                                        <td class="text-nowrap overflow-hidden text-end">
                                            {{ number_format($cashier_rep['saldo'], 0, '', ' ') }}
                                        </td>
                                        <td class="text-nowrap overflow-hidden text-end">
                                            {{ number_format($cashier_rep['saldo_sum'], 0, '', ' ') }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <table class="table mb-1 overflow-hidden">
                                <colgroup>
                                    <col style="width: 33.33%;">
                                    <col style="width: 33.33%;">
                                    <col style="width: 33.33%;">
                                </colgroup>
                                <tbody>
                                    <tr>
                                        <td>Наличка</td>
                                        <td class="text-nowrap overflow-hidden text-end">
                                            {{ number_format($cashier_rep['nalichka'], 0, '', ' ') }}
                                        </td>
                                        <td class="text-nowrap overflow-hidden text-end">
                                            {{ number_format($cashier_rep['nalichka_sum'], 0, '', ' ') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Р/С</td>
                                        <td class="text-nowrap overflow-hidden text-end">
                                            {{ number_format($cashier_rep['rs'], 0, '', ' ') }}
                                        </td>
                                        <td class="text-nowrap overflow-hidden text-end">
                                            {{ number_format($cashier_rep['rs_sum'], 0, '', ' ') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Пластик</td>
                                        <td class="text-nowrap overflow-hidden text-end">
                                            {{ number_format($cashier_rep['plastic'], 0, '', ' ') }}
                                        </td>
                                        <td class="text-nowrap overflow-hidden text-end">
                                            {{ number_format($cashier_rep['plastic_sum'], 0, '', ' ') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Скидки</td>
                                        <td class="text-nowrap overflow-hidden text-end">
                                            {{ number_format($cashier_rep['skidki'], 0, '', ' ') }}
                                        </td>
                                        <td class="text-nowrap overflow-hidden text-end">
                                            {{ number_format($cashier_rep['skidki_sum'], 0, '', ' ') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Сдано</td>
                                        <td class="text-nowrap overflow-hidden text-end">
                                            {{ number_format(@$cashier_rep['sdano'], 0, '', ' ') }}
                                        </td>
                                        <td class="text-nowrap overflow-hidden text-end">
                                            {{ number_format(@$cashier_rep['sdano_sum'], 0, '', ' ') }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        @endif

    @endif

    @if (@Auth::user()->role !== 'user' && @Auth::user()->role !== 'admin' && config('app.debug'))
        {{-- <div class="overlay-f">
            <h1 class="display-1">Сайт в разработке!</h1>
        </div> --}}
    @endif

    <script>
        function formatNumberWithSpaces(value) {
            return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
        }

        function formatNumber(value) {
            const suffixes = ['', 'тыс', 'млн', 'млрд', 'трлн'];
            let suffixIndex = 0;
            let formattedValue = value;

            while (formattedValue >= 1000 && suffixIndex < suffixes.length - 1) {
                formattedValue /= 1000;
                suffixIndex++;
            }

            return `${formattedValue.toFixed(1)} ${suffixes[suffixIndex]}`;
        }

        document.addEventListener('DOMContentLoaded', () => {
            const fullElements = document.querySelectorAll('.format-full');
            const shortElements = document.querySelectorAll('.format-short');

            fullElements.forEach(element => {
                const sumValue = element.textContent.replace(/\s/g,
                    ''); // Удаляем пробелы для преобразования в число
                const formattedSumWithSpaces = formatNumberWithSpaces(sumValue);
                element.textContent = formattedSumWithSpaces;
            });

            shortElements.forEach(element => {
                const sumValue = element.textContent.replace(/\s/g,
                    ''); // Удаляем пробелы для преобразования в число
                const formattedSum = formatNumber(Number(sumValue)); // Преобразуем строку в число
                element.textContent = formattedSum;
            });
        });
    </script>

@endsection
