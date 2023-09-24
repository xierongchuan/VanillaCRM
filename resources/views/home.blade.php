@extends('layouts.main')

@section('title', 'Home')

@section('content')

	<h1 class="text-center mt-5">Welcome to {{getenv('APP_NAME')}}</h1>

@endsection
