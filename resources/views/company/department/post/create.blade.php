@extends('layouts.main')

@section('title', 'Создать Должность')

@section('content')

	@php
		// Preserve selected permissions after validation errors
		$oldPermissions = old('permission', []);
	@endphp

	{{-- Stage 7: Migrated to Vue form components with Tailwind --}}
	<div class="tw-flex tw-justify-center">
		<div class="tw-w-full lg:tw-w-2/3 xl:tw-w-1/2">
			<h2 class="tw-text-center tw-mt-6 tw-mb-4 tw-text-2xl tw-font-bold tw-text-gray-800 dark:tw-text-gray-100">
				Создать должность в <b>{{$department -> name}}</b>
			</h2>
			<form action="{{route('company.department.post.store', compact('company', 'department'))}}" method="post"
				class="tw-bg-gray-100 dark:tw-bg-gray-800 tw-rounded-lg tw-p-6 tw-shadow-md">
				@csrf

				<form-input
					label="Название"
					name="name"
					type="text"
					:required="true"
					placeholder="Введите название должности"
					old-value="{{ old('name') }}"
				></form-input>

				<form-multi-select
					label="Права доступа"
					name="permission[]"
					:options='@json(array_map(function($p) use ($oldPermissions) { return ["value" => (string)$p->id, "label" => $p->name, "selected" => in_array((string)$p->id, $oldPermissions)]; }, $permissions->all()))'
					:size="8"
					help-text="Выберите несколько прав доступа (удерживайте Ctrl/Cmd)"
				></form-multi-select>

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
