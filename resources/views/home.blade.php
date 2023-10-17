@extends('layouts.main')

@section('title', 'Home')

@section('content')

	<h1 class="text-center mt-5">
		Welcome
		@if(@Auth::user()->role === 'admin')
			Admin
		@elseif(@Auth::user()->role === 'user')
			<b>{{Company::find(Auth::user() -> com_id) -> name}}</b> Worker
		@endif
	</h1>

	@if(Auth::check())

		@foreach($companies as $company)

			<div class="row flex-column align-items-center">
				<div class="col-lg-9 bg-body-secondary rounded mt-3 p-2">
					<span class="d-flex justify-content-between">
						<h2 class="perm_panel_switch" panel="perm_panel_{{$company->id}}"><b>{{$company -> name}}</b></h2>
						<button class="lead perm_panel_switch" panel="perm_panel_{{$company->id}}">Switch</button>
					</span>
					<div id="perm_panel_{{$company->id}}" class="perm-panel w-100">
						<div class="bg-body-tertiary rounded p-3 mb-2">
							<div class="row">
								<div class="col-md-5 my-1 m-auto border rounded p-2 d-flex justify-content-between h4">
									<span>
										Договора
									</span>

										<span>
										8 шт
									</span>
								</div>

								<div class="col-md-5 my-1 m-auto border rounded p-2 d-flex justify-content-between h4">
									<span>
										Оплата
									</span>

									<span>
										6 шт
									</span>

									<span>
										<b>50 000 000</b> сум
									</span>
								</div>

								<div class="col-md-5 my-1 m-auto border rounded p-2 d-flex justify-content-between h4">
									<span>
										Доплата
									</span>

									<span>
										<b>50 000 000</b> сум
									</span>
								</div>

							</div>
						</div>

						<div class="bg-body-tertiary rounded p-3 mb-2">
							<div class="row">

								<div class="col-md-5 my-1 m-auto border rounded p-2">
									<div class="my-1 m-auto p-2 d-flex justify-content-between h3">
										<span>
											План
										</span>

											<span>
											<b>130</b> шт
										</span>
									</div>
									<div class="my-1 m-auto p-2 d-flex justify-content-between h3">
										<span class="mx-auto">
											<b>130 000 000</b> сум
										</span>
									</div>
								</div>

								<div class="col-md-5 my-1 m-auto border rounded p-2">
									<div class="my-1 m-auto p-2 d-flex justify-content-between h3">
										<span>
											Факт
										</span>

										<span>
											<b>31</b> шт
										</span>
									</div>
									<div class="my-1 m-auto p-2 d-flex justify-content-between h3">
										<span class="mx-auto">
											<b>45 000 000</b> сум
										</span>
									</div>
								</div>

								<div class="col-md-5 my-1 m-auto border rounded p-2 d-flex justify-content-between lead">
									<span>
										Договора
									</span>

									<span>
										<b>40</b> шт
									</span>
								</div>

								<div class="col-md-5 my-1 m-auto border rounded p-2 d-flex justify-content-between lead">
									<span>
										Конверсия
									</span>

									<span>
										<b>77.5</b> %
									</span>
								</div>

								<div class="col-md-5 my-1 m-auto border rounded p-2 d-flex justify-content-between lead">
									<span>
										% от кол-во
									</span>

									<span>
										<b>23.8</b> %
									</span>
								</div>

								<div class="col-md-5 my-1 m-auto border rounded p-2 d-flex justify-content-between lead">
									<span>
										% от суммы
									</span>

									<span>
										<b>23.9</b> %
									</span>
								</div>


							</div>
						</div>

						<div class="bg-body-tertiary rounded p-3 mb-2">
							<div class="row">

								<div class="col-md-5 my-1 m-auto border rounded p-2 d-flex justify-content-between lead">
									<span>
										Оплата
									</span>

									<span>
										<b>12 000 000 000</b> сум
									</span>
								</div>

								<div class="col-md-5 my-1 m-auto border rounded p-2 d-flex justify-content-between lead">
									<span>
										Лизинг
									</span>

									<span>
										<b>880 000</b> сум
									</span>
								</div>

								<div class="col-md-5 my-1 m-auto border rounded p-2 d-flex justify-content-between lead">
									<span>
										Доплата
									</span>

									<span>
										<b>12 000 000</b> сум
									</span>
								</div>

								<div class="col-md-5 my-1 m-auto border rounded p-2 d-flex justify-content-between lead">
									<span>
										Лизинг
									</span>

									<span>
										<b>880 000</b> сум
									</span>
								</div>

							</div>
						</div>

						<div class="bg-body-tertiary rounded p-3 mb-2">

						</div>

						<div class="bg-body-tertiary rounded p-3 mb-2">

						</div>

						<div class="bg-body-tertiary rounded p-3 mb-2">

						</div>

					</div>
				</div>
			</div>


		@endforeach

	@endif

@endsection
