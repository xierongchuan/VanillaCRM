@extends('layouts.main')

@section('title', 'Архив «' . $company->name . '»')

@section('nav_right')
    <li class="nav-item">
        <a class="btn btn-danger" aria-current="page" href="{{ route('company.remove_last_report', compact('company')) }}">
            Удалить отчёт на последнюю дату
        </a>
    </li>
@endsection

@section('content')
    <h1 class="text-center my-4">
        Архив <b>{{ $company->name }}</b>
    </h1>

    <div class="">
        <div class="accordion" id="reportsAccordion">
            @foreach ($groupedReports as $month => $reports)
                <div class="card mb-3 overflow-hidden">
                    <div class="card-header p-2" id="heading-{{ $month }}">
                        <h2 class="mb-0 d-flex row justify-content-between align-items-center">
                            <div class="h3 my-1 col-lg-2 col-5" data-bs-toggle="collapse" aria-expanded="true"
                                aria-controls="collapse-{{ $month }}" data-bs-target="#collapse-{{ $month }}">
                                <b>{{ $month }}</b>
                            </div>
                            <div class="d-none d-lg-flex justify-content-between align-items-center col-lg-7">
                                <div class="text-nowrap mb-0 pb-0 col-3 text-end h4">
                                    {{ number_format(reset($reports)['sum'], 0, '', ' ') }} Сум
                                </div>
                                <div class="text-nowrap mb-0 pb-0 col-2 text-end h4 text-end">
                                    {{ reset($reports)['quantity'] }} Шт
                                </div>
                                <div class="text-nowrap mb-0 pb-0 col-2 text-end h4 text-end">
                                    {{ reset($reports)['fact'] }} Факт
                                </div>
                            </div>
                            <span class="col-lg-3 col-7 text-end">
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
                        <div class="card-body p-lg-0">

                            <div class="m-1 border rounded p-2 py-3 px-0 row">
                                <!-- Для больших экранов -->
                                <div class="mb-0 pb-0 col-2 h5 px-3 d-none d-md-block">
                                    День
                                </div>
                                <!-- Для мобильных устройств -->
                                <div class="mb-0 pb-0 col-2 h5 px-3 d-block d-md-none">
                                    <i class="bi bi-calendar-day"></i>
                                </div>

                                <div class="mb-0 pb-0 col-3 text-end h5 px-0 text-end">
                                    Сум
                                </div>

                                <div class="mb-0 pb-0 col-2 text-end h5 px-0 text-end">
                                    Шт
                                </div>

                                <div class="mb-0 pb-0 col-2 text-end h5 px-0 text-end">
                                    Факт
                                </div>

                                <div class="mb-0 pb-0 col-3 px-lg-0d px-2 text-end">
                                    {{-- Пустой --}}
                                </div>
                            </div>

                            <div class="accordion" id="dayReportsAccordion-{{ $month }}">

                                @foreach ($reports as $reportData)
                                    <div class="card overflow-hidden m-1">
                                        <div class="card-header p-2 px-0"
                                            id="heading-{{ $reportData['report']->for_date }}">
                                            <h2 class="row m-0 d-flex align-items-center">
                                                <div class="mb-0 pb-0 col-2 h5 px-3">
                                                    {{ (int) explode('-', $reportData['report']->for_date)[2] }}
                                                </div>

                                                <!-- Для больших экранов -->
                                                <div class="mb-0 pb-0 col-3 text-end h5 px-0 text-end d-none d-md-block">
                                                    {{ number_format($reportData['sum'], 0, '', ' ') }}
                                                </div>
                                                <!-- Для мобильных устройств -->
                                                <div class="mb-0 pb-0 col-3 text-end h5 px-0 text-end d-block d-md-none">
                                                    {{ number_format((int) $reportData['sum'] / 1000000000, 1, '.', ' ') }}
                                                    млн
                                                </div>

                                                <div class="mb-0 pb-0 col-2 text-end h5 px-0 text-end">
                                                    {{ $reportData['quantity'] }}
                                                </div>
                                                <div class="mb-0 pb-0 col-2 text-end h5 px-0 text-end">
                                                    {{ $reportData['fact'] }}
                                                </div>
                                                <span class="mb-0 pb-0 col-3 px-lg-0d px-2 text-end">
                                                    <a class="btn btn-success my-lg-0 my-1"
                                                        href="{{ $reportData['url'] }}">
                                                        <i class="bi bi-download"></i>
                                                    </a>

                                                    <button class="btn btn-primary my-lg-0 my-1" type="button"
                                                        data-bs-toggle="collapse"
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
                                            <div class="card-body p-lg-3 p-0">

                                                {{-- Начало превью отчёта --}}

                                                @php
                                                    $data = (array) json_decode($reportData['report']['data']);
                                                @endphp

                                                <div class="flex-column align-items-center">

                                                    <div class="bg-body-tertiary rounded p-3 mb-2">

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
                                                            <h2 class="mb-0">Месяц</h2>
                                                        </div>

                                                        <div class="row">

                                                            <div class="mb-2">

                                                                <div
                                                                    class="col-md-9 mt-1 row m-auto justify-content-between h4">

                                                                    <span class="col-md-4 p-2 m-auto h4 d-none d-md-block">
                                                                        Факт
                                                                    </span>

                                                                    <span class="col-md-4">

                                                                    </span>


                                                                    <span
                                                                        class="col-md-4 p-2 text-md-end d-none d-md-block">
                                                                        План
                                                                    </span>

                                                                </div>


                                                                <div
                                                                    class="col-md-9 my-1 row m-auto justify-content-between h4">
                                                                    <span
                                                                        class="col-md-5 p-2 border border-success rounded d-flex justify-content-between">
                                                                        <b class="d-block d-md-none">Факт:</b>
                                                                        <span><b>{{ $data['Факт Кол-во'] }}</b>
                                                                        </span>
                                                                    </span>

                                                                    <span
                                                                        class="col-md-2 p-2 m-auto h6 border border-danger rounded text-center">
                                                                        <b>{{ $data['% от кол-во'] }}</b> %
                                                                    </span>


                                                                    <span
                                                                        class="col-md-5 p-2 border rounded d-flex justify-content-md-end justify-content-between">
                                                                        <b class="d-block d-md-none">План:</b>
                                                                        <span><b>{{ $data['План Кол-во'] }}</b>
                                                                        </span>
                                                                    </span>

                                                                </div>

                                                                <div class="m-3 d-block d-md-none"></div>

                                                                <div
                                                                    class="col-md-9 my-1 row m-auto justify-content-between h4">
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
                                                                    <b>{{ number_format((int) $data['3 Оплата'], 0, '', ' ') }}</b>
                                                                    сум
                                                                </span>
                                                            </div>

                                                            <div
                                                                class="col-md-5 my-1 m-auto border rounded p-2 d-flex justify-content-between lead">
                                                                <span>
                                                                    Лизинг
                                                                </span>

                                                                <span>
                                                                    <b>{{ number_format((int) $data['3 Доплата'], 0, '', ' ') }}</b>
                                                                    сум
                                                                </span>
                                                            </div>

                                                            <div
                                                                class="col-md-5 my-1 m-auto border rounded p-2 d-flex justify-content-between lead">
                                                                <span>
                                                                    Доплата
                                                                </span>

                                                                <span>
                                                                    <b>{{ number_format((int) $data['3 Лизинг'], 0, '', ' ') }}</b>
                                                                    сум
                                                                </span>
                                                            </div>

                                                            <div
                                                                class="col-md-5 my-1 m-auto border rounded p-2 d-flex justify-content-between lead">
                                                                <span>
                                                                    Остаток
                                                                </span>

                                                                <span>
                                                                    <b>{{ number_format((int) $data['3 Остаток'], 0, '', ' ') }}</b>
                                                                    сум
                                                                </span>
                                                            </div>

                                                        </div>
                                                    </div>


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

                                                        <h2>Менеджеры</h2>

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
                                                                        $worker = App\Models\User::where(
                                                                            'id',
                                                                            $id,
                                                                        )->first();
                                                                    @endphp

                                                                    <tr>
                                                                        <th scope="row">
                                                                            {{ $loop->iteration }}</th>
                                                                        <td>{{ $worker->full_name }}</td>
                                                                        <td class="text-nowrap overflow-hidden">
                                                                            {{ $sales[$id] }}
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
                                                                    <td class="text-nowrap overflow-hidden">
                                                                        {{ $now_men }}
                                                                        шт
                                                                    </td>
                                                                    <td class="text-nowrap overflow-hidden">%
                                                                    </td>
                                                                </tr>

                                                            </tbody>
                                                        </table>

                                                    </div>


                                                    <div class="bg-body-tertiary rounded p-3 mb-2">

                                                        @php
                                                            $sums = [
                                                                $data['5 Через банк сумма'],
                                                                $data['5 Через лизинг сумма'],
                                                            ];
                                                            $totalSumSums = array_sum($sums);

                                                            $sums_per = [];
                                                            foreach ($sums as $key => $sum) {
                                                                $percentage = 0;
                                                                if ($sum != 0 && $totalSumSums != 0) {
                                                                    $percentage = ($sum / $totalSumSums) * 100;
                                                                }
                                                                $sums_per[$key] = round($percentage, 2);
                                                            }

                                                            $counts = [
                                                                $data['5 Через банк шт'],
                                                                $data['5 Через лизинг шт'],
                                                            ];
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
                                                                    <td>Через банк (шт)</td>
                                                                    <td class="text-nowrap overflow-hidden text-end">
                                                                        {{ $data['5 Через банк шт'] }} </td>
                                                                    <td class="text-nowrap overflow-hidden text-end"
                                                                        style="width:4rem;">
                                                                        {{ $count_per[0] }} %</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Через банк (сумма)</td>
                                                                    <td class="text-nowrap overflow-hidden text-end">
                                                                        {{ number_format((int) $data['5 Через банк сумма'], 0, '', ' ') }}
                                                                    </td>
                                                                    <td class="text-nowrap overflow-hidden text-end"
                                                                        style="width:4rem;">
                                                                        {{ $sums_per[0] }} %</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Через лизинг (шт)</td>
                                                                    <td class="text-nowrap overflow-hidden text-end">
                                                                        {{ $data['5 Через лизинг шт'] }} </td>
                                                                    <td class="text-nowrap overflow-hidden text-end"
                                                                        style="width:4rem;">
                                                                        {{ $count_per[1] }} %</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Через лизинг (сумма)</td>
                                                                    <td class="text-nowrap overflow-hidden text-end">
                                                                        {{ number_format((int) $data['5 Через лизинг сумма'], 0, '', ' ') }}
                                                                    </td>
                                                                    <td class="text-nowrap overflow-hidden text-end"
                                                                        style="width:4rem;">
                                                                        {{ $sums_per[1] }} %</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Итог (шт)</td>
                                                                    <td class="text-nowrap overflow-hidden text-end">
                                                                        {{ $data['5 Итог шт'] }}
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Итог (сумма)</td>
                                                                    <td class="text-nowrap overflow-hidden text-end">
                                                                        {{ number_format((int) $data['5 Cумма'], 0, '', ' ') }}
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>

                                                    </div>


                                                    {{-- Конец превью отчёта --}}

                                                </div>

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
                    <div class="col-3 h5">
                        Дата
                    </div>
                    <div class="col-3 h5 text-end">
                        Сум
                    </div>
                    <div class="col-3 h5 text-end">
                        Шт
                    </div>
                    <div class="col-3 h5 text-end">
                        Факт
                    </div>
                </div>

                @foreach ($files_data as $file)
                    <div class="m-1 border rounded p-2 pt-3 row">
                        <div class="col-3 h5">
                            <a href="{{ $file->url }}">{{ $file->date }}</a>
                        </div>
                        <!-- Для больших экранов -->
                        <div class="col-3 text-end h5 text-end d-none d-md-block format-full">
                            {{ $file->sum }}
                        </div>
                        <!-- Для мобильных устройств -->
                        <div class="col-3 text-end h5 text-end d-block d-md-none format-short">
                            {{ $file->sum }}
                        </div>
                        <div class="col-3 h5 text-end">
                            {{ $file->count }}
                        </div>
                        <div class="col-3 h5 text-end">
                            {{ $file->fakt }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

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
                const sumValue = {{ @$reportData['sum'] }}; // Получаем значение из Blade
                const formattedSum = formatNumber(sumValue);
                element.textContent = formattedSum;
            });
        });
    </script>
@endsection
