@extends('layouts.main')

@section('title', 'Home')

@section('content')

	<h1 class="text-center mt-5">
		Welcome
		@if(@Auth::user()->role === 'admin')
			Admin
		@elseif(@Auth::user()->role === 'user')
			<b>{{$company -> name}}</b> Worker
		@endif
	</h1>

	@if(isset($companies))

		@foreach($companies as $company)

			@php

				$data = (array)json_decode($company -> data);

			@endphp

			@if(!empty($data))
				<div class="row flex-column align-items-center">
					<div class="col-lg-9 bg-body-secondary rounded my-2 p-2">
					<span class="d-flex justify-content-between">
						 <h1 class="m-0 perm_panel_switch" panel="perm_panel_{{$company->id}}"><b>{{$company -> name}}</b></h1>
						<button class="lead perm_panel_switch" panel="perm_panel_{{$company->id}}">Switch</button>
					</span>
						<div id="perm_panel_{{$company->id}}" class="perm-panel w-100">
							<div class="bg-body-tertiary rounded p-3 mb-2">
								<div class="d-flex flex-wrap justify-content-center">
									<h4>Дата загрузки <a href="{{$last_repor_urls[$loop->index]}}">отчёта</a>:</h4>
									<div class="mx-sm-1"></div>
									<h4>{{date("d-m-Y H:i:s", strtotime($data['Дата']))}}</h4>
								</div>

								<div class="col-md-5 my-1 m-auto border rounded p-2">
									<div class="my-1 m-auto p-2 d-flex justify-content-between h3">

											<span>
												План
											</span>

										<span>
												<b>{{$data['План Кол-во']}}</b> шт
											</span>

									</div>

									<div class="my-1 m-auto p-2 d-flex justify-content-between h3">
											<span class="mx-auto">
												<b>{{number_format((int)$data['План Сумм'], 0, '', ' ')}}</b> сум
											</span>
									</div>
								</div>

							</div>

							<div class="bg-body-tertiary rounded p-3 mb-2">
								<div class="d-flex flex-wrap justify-content-center">
									<h2>Сегодня</h2>
								</div>
								<div class="row">
									<div class="col-md-5 my-1 m-auto border rounded p-2 d-flex justify-content-between h4">
									<span>
										Договора
									</span>

										<span>
										{{$data['Договора']}} шт
									</span>
									</div>

									<div class="col-md-5 my-1 m-auto border rounded p-2 d-flex justify-content-between h4">
									<span>
										Оплата
									</span>

									<span>
										{{$data['Оплата Кол-во']}} шт
									</span>

									<span>
										<b>{{number_format((int)$data['Оплата Сумм'], 0, '', ' ')}}</b> сум
									</span>
									</div>

									<div class="col-md-5 my-1 m-auto border rounded p-2 d-flex justify-content-between h4">
									<span>
										Доплата
									</span>

									<span>
										<b>{{number_format((int)$data['Доплата'], 0, '', ' ')}}</b> сум
									</span>
									</div>

								</div>
							</div>

							<div class="bg-body-tertiary rounded p-3 mb-2">
								<div class="row">

{{--									<div class="col-md-5 my-1 m-auto border rounded p-2">--}}
{{--										<div class="my-1 m-auto p-2 d-flex justify-content-between h3">--}}

{{--											<span>--}}
{{--												План--}}
{{--											</span>--}}

{{--											<span>--}}
{{--												<b>{{$data['План Кол-во']}}</b> шт--}}
{{--											</span>--}}

{{--										</div>--}}

{{--										<div class="my-1 m-auto p-2 d-flex justify-content-between h3">--}}
{{--											<span class="mx-auto">--}}
{{--												<b>{{number_format((int)$data['План Сумм'], 0, '', ' ')}}</b> сум--}}
{{--											</span>--}}
{{--										</div>--}}
{{--									</div>--}}

									<div class="col-md-5 my-1 m-auto border rounded p-2">
										<div class="my-1 m-auto p-2 d-flex justify-content-between h3">
										<span>
											Факт
										</span>

											<span>
											<b>{{$data['Факт Кол-во']}}</b> шт
										</span>
										</div>
										<div class="my-1 m-auto p-2 d-flex justify-content-between h3">
										<span class="mx-auto">
											<b>{{number_format((int)$data['Факт Сумм'], 0, '', ' ')}}</b> сум
										</span>
										</div>
									</div>

									<div class="col-md-5 my-1 m-auto border rounded p-2 d-flex justify-content-between lead">
									<span>
										Договора
									</span>

										<span>
										<b>{{$data['2 Договора']}}</b> шт
									</span>
									</div>

									<div class="col-md-5 my-1 m-auto border rounded p-2 d-flex justify-content-between lead">
									<span>
										Конверсия (CV)
									</span>

										<span>
										<b>{{$data['2 Конверсия']}}</b> %
									</span>
									</div>

									<div class="col-md-5 my-1 m-auto border rounded p-2 d-flex justify-content-between lead">
									<span>
										% от кол-во
									</span>

										<span>
										<b>{{$data['% от кол-во']}}</b> %
									</span>
									</div>

									<div class="col-md-5 my-1 m-auto border rounded p-2 d-flex justify-content-between lead">
									<span>
										% от суммы
									</span>

										<span>
										<b>{{$data['% от сумм']}}</b> %
									</span>
									</div>


								</div>
							</div>

							<div class="bg-body-tertiary rounded p-3 mb-2">
								<div class="row">

									<div class="col-md-5 my-1 m-auto border rounded p-2 d-flex justify-content-between lead">
									<span>
										Банк
									</span>

										<span>
										<b>{{number_format((int)$data['3 Оплата'], 0, '', ' ')}}</b> сум
									</span>
									</div>

									<div class="col-md-5 my-1 m-auto border rounded p-2 d-flex justify-content-between lead">
									<span>
										Лизинг
									</span>

										<span>
										<b>{{number_format((int)$data['3 Доплата'], 0, '', ' ')}}</b> сум
									</span>
									</div>

									<div class="col-md-5 my-1 m-auto border rounded p-2 d-flex justify-content-between lead">
									<span>
										Доплата
									</span>

										<span>
										<b>{{number_format((int)$data['3 Лизинг'], 0, '', ' ')}}</b> сум
									</span>
									</div>

									<div class="col-md-5 my-1 m-auto border rounded p-2 d-flex justify-content-between lead">
									<span>
										Остаток
									</span>

										<span>
										<b>{{number_format((int)$data['3 Остаток'], 0, '', ' ')}}</b> сум
									</span>
									</div>

								</div>
							</div>

							<div class="bg-body-tertiary rounded p-3 mb-2">

								@php

									$managers = $data['Продажи'];
									$totalSum = 0;
									foreach ($managers as $manager) {
										$totalSum += (int)$manager -> month;
									}

									$percentages = [];
									foreach ($managers as $key => $manager) {
										$percentage = ($manager -> month / $totalSum) * 100;
										$percentages[$key] = round($percentage, 1);
									}

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

									@foreach($managers as $key => $manager)

										<tr>
											<th scope="row">{{$loop -> iteration}}</th>
											<td>{{$manager -> name}}</td>
											<td>{{$manager -> sold}} шт</td>
											<td>{{$manager -> month}} шт</td>
											<td>{{$percentages[$key]}} %</td>
										</tr>

									@endforeach

									</tbody>
								</table>

							</div>

							<div class="bg-body-tertiary rounded p-3 mb-2">

								@php
									$sums = [$data['5 Через банк сумма'], $data['5 Через лизинг сумма']];
									$totalSumSums = array_sum($sums);

									$sums_per = [];
									foreach ($sums as $key => $sum) {
										$percentage = ($sum / $totalSumSums) * 100;
										$sums_per[$key] = round($percentage, 1);
									}

									$counts = [$data['5 Через банк шт'], $data['5 Через лизинг шт']];
									$totalSumCounts = array_sum($counts);

									$count_per = [];
									foreach ($counts as $key => $sum) {
										$percentage = ($sum / $totalSumCounts) * 100;
										$count_per[$key] = round($percentage, 1);
									}
								@endphp

								<h2>Реализация</h2>

								<table class="table mb-1 rounded overflow-hidden">
									{{--								<thead>--}}
									{{--								<tr>--}}
									{{--									<th scope="col"></th>--}}
									{{--									<th scope="col">Продал сегодня</th>--}}
									{{--									<th scope="col">Продал в мес</th>--}}
									{{--								</tr>--}}
									{{--								</thead>--}}
									<tbody>
									<tr>
										<td>Через банк шт</td>
										<td>{{$data['5 Через банк шт']}} шт</td>
										<td class="text-nowrap overflow-hidden">{{$count_per[0]}} %</td>
									</tr>
									<tr>
										<td>Через банк сумма</td>
										<td class="text-nowrap overflow-hidden">{{number_format((int)$data['5 Через банк сумма'], 0, '', ' ')}} сум</td>
										<td class="text-nowrap overflow-hidden">{{$sums_per[0]}} %</td>
									</tr>
									<tr>
										<td>Через лизинг шт</td>
										<td>{{$data['5 Через лизинг шт']}} шт</td>
										<td class="text-nowrap overflow-hidden">{{$count_per[1]}} %</td>
									</tr>
									<tr>
										<td>Через лизинг сумма</td>
										<td class="text-nowrap overflow-hidden">{{number_format((int)$data['5 Через лизинг сумма'], 0, '', ' ')}} сум</td>
										<td class="text-nowrap overflow-hidden">{{$sums_per[1]}} %</td>
									</tr>
									<tr>
										<td>Итог шт</td>
										<td>{{$data['5 Итог шт']}} шт</td>
										<td></td>
									</tr>
									<tr>
										<td>Итог сумма</td>
										<td class="text-nowrap overflow-hidden">{{number_format((int)$data['5 Cумма'], 0, '', ' ')}} сум</td>
										<td></td>
									</tr>
									</tbody>
								</table>

							</div>

							<div class="bg-body-tertiary rounded p-3 mb-2">

								<div class="my-1 m-auto p-0 d-flex justify-content-between">
									<h2>Прошлые месяцы</h2>

									<a href="{{route('company.archive', compact('company'))}}" class="lead">Архив</a>
								</div>

								<div class="my-1 m-auto border rounded py-2 row h4">

									<div class="col-3">
										Мес
									</div>

									<div class="col-7 text-end">
										Сум
									</div>

									<div class="col-2 text-end">
										Шт
									</div>
								</div>

								@foreach($files_data as $file)
									@if($file -> company != $company -> name)
										@continue
									@endif
									@if($loop -> count > 4)
										@break
									@endif

									<div class="my-1 m-auto border rounded py-2 row h4">

										<div class="col-3 h5">
											<a href="{{$file -> url}}">{{$file -> date}}</a>
										</div>

										<div class="col-7 text-end">
											{{$file -> sum}}
										</div>

										<div class="col-2 text-end">
											{{$file -> count}}
										</div>
									</div>

								@endforeach

							</div>

						</div>
					</div>
				</div>
			@endif


		@endforeach

	@endif

@endsection
