@extends('layouts.main')

@section('title', 'Permissions')

@section('content')

	@if(in_array('report_xlsx', $data -> perm))
		@include('user.permissions.report_xlsx')
	@endif

	@if(in_array('report_service', $data -> perm))
		@include('user.permissions.report_service')
	@endif

	@if(in_array('create_worker', $data -> perm))
		@include('user.permissions.create_worker')
	@endif

@endsection
