<div class="row flex-column align-items-center">
	<div class="col-lg-9 bg-body-secondary rounded mt-3 p-2">
		<span class="d-flex justify-content-between">
			<h2 class="perm_panel_switch mx-1" panel="perm_panel_create_worker">Добавить сотрудника в <b>{{$data -> company -> name}}</b></h2>
			<button class="lead perm_panel_switch m-1" panel="perm_panel_create_worker"><i class="bi bi-nintendo-switch"></i></button>
		</span>
		<form id="perm_panel_create_worker" action="{{route('mod.create_worker', $data -> company -> id)}}" method="post" class="perm-panel-list bg-body-tertiary rounded p-3">
			@csrf

			<div class="form-group mb-2">
				<label for="login">Login:</label>
				<input type="text" class="form-control" id="login" name="login" required>
			</div>
			<div class="form-group mb-2">
				<label for="password">Password:</label>
				<input type="password" autocomplete="off" class="form-control" id="password" name="password" required>
			</div>
			<div class="form-group mb-2">
				<label for="full_name">Name:</label>
				<input type="text" class="form-control" id="full_name" name="full_name" required>
			</div>
			<div class="form-group mb-2">
				<label for="phone_number">Phone:</label>
				<input type="text" class="form-control" id="phone_number" name="phone_number" required>
			</div>
			<div class="d-flex justify-content-center">
				<button type="submit" class="btn btn-primary">Create</button>
			</div>
		</form>
	</div>
</div>
