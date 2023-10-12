@extends('layouts.main')

@section('title', 'Home')

@section('content')

	<h1 class="text-center mt-5">
		Welcome
		@if(@Auth::user()->role === 'admin')
			Admin
		@elseif(@Auth::user()->role === 'user')
			<b>{{\App\Models\Company::find(Auth::user() -> com_id) -> name}}</b> Worker
		@endif
	</h1>

@endsection
