@extends('layouts.main')

@section('title', 'User Create')

@section('content')
	<h1 class="text-center my-2">
		Archive {{$company -> name}}
	</h1>

	<div class="flex-column align-items-center">
		<div class="bg-body-tertiary rounded p-3 mb-2">
			@foreach($files_data as $file)

				<div class="my-1 m-auto border rounded p-2 d-flex justify-content-between h4">

					<span>
						<a href="{{$file -> url}}">{{$file -> date}}</a>
					</span>

					<span>
						{{$file -> sum}} сум
					</span>

					<span>
						{{$file -> count}} шт
					</span>
				</div>

			@endforeach

		</div>
	</div>

@endsection
