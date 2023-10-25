@extends('layouts.main')

@section('title', 'User Create')

@section('content')
	<h1 class="text-center my-2">
		Archive {{$company -> name}}
	</h1>

	<div class="flex-column align-items-center">
		<div class="bg-body-tertiary rounded p-3 mb-2">
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

@endsection
