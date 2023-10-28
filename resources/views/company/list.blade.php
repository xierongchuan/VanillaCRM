@extends('layouts.main')

@section('title', 'Настройки')

@section('nav_right')
	<li class="nav-item">
		<a class="btn btn-success" aria-current="page" href="{{route('company.create')}}">Создать Компанию</a>
	</li>
@endsection

@section('content')

	<div class="row flex-column align-items-center">
		@foreach($companies as $company)
			<div class="col-lg-9 bg-body-secondary rounded mt-3 p-2">
				<div class="d-flex justify-content-between mx-2">
					<h2 class="perm_panel_switch" panel="perm_panel_{{$company->id}}" style="font-size: calc(1.605rem + .66vw);margin-top: 0.1rem;"><b>{{$company -> name}}</b></h2>

					<div class="btn-group align-items-center col-lg-3 p-0 mb-2" role="group" aria-label="Basic mixed styles example">
						<a href="{{route('company.update', $company -> id)}}" type="button" class="btn btn-warning"><i class="bi bi-pencil"></i></a>
						<a href="{{route('company.delete', $company -> id)}}" type="button" class="btn btn-danger"><i class="bi bi-trash"></i></a>
						<button class="btn btn-primary perm_panel_switch" panel="perm_panel_{{$company->id}}"><i class="bi bi-nintendo-switch"></i></button>
					</div>
				</div>

				<div id="perm_panel_{{$company->id}}" class="perm-panel-list w-100">
					<div class="p-2 border border-2 rounded">
						<span class="lead">Отделы: </span><br>
						@foreach($company -> departments as $department)
							<span>
								<div class="d-flex justify-content-between rounded bg-body shadow gx-1 my-1 p-2 pb-0">
									<div class="col-lg-9 lead"><a href="{{route('company.department.index', compact('company', 'department'))}}" class="nav-link">{{$department -> name}}</a></div>

									<div class="btn-group col-lg-3 p-0 mb-2" role="group" aria-label="Basic mixed styles example">
										<a href="{{route('company.department.update', ['company' => $company, 'department' => $department])}}" type="button" class="btn btn-warning"><i class="bi bi-pencil"></i></a>
										<a href="{{route('company.department.delete', ['company' =>  $company, 'department' => $department])}}" type="button" class="btn btn-danger"><i class="bi bi-trash"></i></a>
									</div>
								</div>
							</span>
						@endforeach

						<a href="{{route('company.department.create', $company -> id)}}" class="btn btn-success w-100 mt-1">Создать <i class="bi bi-people"></i></a>

					</div>

					<div class="p-2 border border-2 rounded mt-2">
						<span class="lead">Доступы: </span><br>
						@foreach($company -> permissions as $permission)
								<span>
							<div class="d-flex justify-content-between rounded bg-body shadow gx-1 my-1 p-2 pb-0">
										<div class="col-lg-9 lead"><a href="#" class="nav-link">{{$permission -> name}} ({{$permission -> value}})</a></div>

										<div class="btn-group col-lg-3 p-0 mb-2" role="group" aria-label="Basic mixed styles example">
											<a href="{{route('company.permission.update', compact('company', 'permission'))}}" type="button" class="btn btn-warning"><i class="bi bi-pencil"></i></a>
											@if(config('app.debug'))
												<a href="{{route('company.permission.delete', compact('company', 'permission'))}}" type="button" class="btn btn-danger"><i class="bi bi-trash"></i></a>
											@endif
										</div>
									</div>
							</span>
						@endforeach


						<a href="{{route('company.permission.create', $company -> id)}}" class="btn btn-success w-100 mt-1">Создать <i class="bi bi-person-vcard"></i></a>
					</div>

					<div class="p-2 border border-2 rounded mt-2">
					<span class="lead">Пользователи: </span><br>
					@foreach($company -> users as $user)
						<span>
							<div class="d-flex justify-content-between rounded bg-body shadow gx-1 my-1 p-2 pb-0">
								<div class="col-lg-9 lead"><a href="{{route('company.user.update', compact('company', 'user'))}}" class="nav-link">{{$user -> full_name}} ({{$user -> login}})</a></div>

								<div class="btn-group col-lg-3 p-0 mb-2" role="group" aria-label="Basic mixed styles example">
									<a href="{{route('company.user.update', compact('company', 'user'))}}" type="button" class="btn btn-warning"><i class="bi bi-pencil"></i></a>
								<a href="{{route('company.user.delete', compact('company', 'user'))}}" type="button" data-id="{{$user->id}}" data-name="{{$user->name}}"class="btn btn-danger delete-user"><i class="bi bi-trash"></i></a>
								</div>
							</div>
					</span>
					@endforeach

					<a href="{{route('company.user.create', compact('company'))}}" class="btn btn-success w-100 mt-1">Создать <i class="bi bi-person"></i></a>
				</div>

{{--					<script>--}}
{{--						document.querySelectorAll('.delete-user').forEach(item => {--}}
{{--							item.addEventListener('click', function(event) {--}}
{{--								event.preventDefault();--}}
{{--								event.stopPropagation(); // Добавлено здесь--}}

{{--								var userId = this.getAttribute('data-id');--}}
{{--								var userName = this.getAttribute('data-name');--}}
{{--								var link = this.getAttribute('href');--}}

{{--								var confirmed = confirm("Вы точно хотите удалить " + userName + "?");--}}

{{--								if (confirmed) {--}}
{{--									window.location.href = link;--}}
{{--								}--}}
{{--							});--}}
{{--						});--}}
{{--					</script>--}}
				</div>
			</div>
		@endforeach
	</div>

@endsection
