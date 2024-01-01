@extends('layouts.main')

@section('title', 'Создать Сотрудника')

@section('nav_right')
	<li class="nav-item">
		<a class="btn btn-danger" aria-current="page" href="{{route('company.remove_last_report', compact('company'))}}">Удалить последний отчёт</a>
	</li>
@endsection

@section('content')
	<h1 class="text-center my-2">
		Архив {{$company -> name}}
	</h1>

	<div class="flex-column align-items-center">
		<div class="bg-body-tertiary rounded p-3 mb-2">
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

			@foreach($files_data as $file)

				<div class="my-1 m-auto border rounded py-2 row h4">

					<div class="col-3 h5">
						<a href="{{$file -> url}}">{{$file -> date}}</a>
					</div>


					<div class="col-4 text-end">
						{{$file -> sum}}
					</div>

					<div class="col-2 text-end">
						{{$file -> count}}
					</div>

					<div class="col-3 text-end">
						{{$file -> fakt}}
					</div>
				</div>

			@endforeach

		</div>
	</div>

@endsection
