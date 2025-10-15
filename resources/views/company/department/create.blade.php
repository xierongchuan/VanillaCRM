@extends('layouts.main')

@section('title', 'Department Create')

@section('content')

	{{-- Stage 7: Migrated to Vue form components with Tailwind --}}
	<div class="tw-flex tw-justify-center">
		<div class="tw-w-full lg:tw-w-2/3 xl:tw-w-1/2">
			<h2 class="tw-text-center tw-mt-6 tw-mb-4 tw-text-2xl tw-font-bold tw-text-gray-800 dark:tw-text-gray-100">
				Создать отдел в <b>{{$company -> name}}</b>
			</h2>
			<form action="{{route('company.department.store', $company -> id)}}" method="post"
				class="tw-bg-gray-100 dark:tw-bg-gray-800 tw-rounded-lg tw-p-6 tw-shadow-md">
				@csrf

				<form-input
					label="Название"
					name="name"
					type="text"
					:required="true"
					placeholder="Введите название отдела"
					old-value="{{ old('name') }}"
				></form-input>

				<div class="tw-flex tw-justify-center">
					<button type="submit"
						class="tw-px-6 tw-py-3 tw-bg-blue-600 tw-text-white tw-rounded-lg
							   tw-font-medium tw-transition-colors tw-duration-200
							   hover:tw-bg-blue-700 focus:tw-outline-none focus:tw-ring-2
							   focus:tw-ring-blue-500 focus:tw-ring-offset-2">
						Создать
					</button>
				</div>
			</form>
		</div>
	</div>

@endsection
