@php
	use App\Models\User;

	$workers = User::where('dep_id', \Illuminate\Support\Facades\Auth::user() -> dep_id) -> get();

@endphp

<div class="row flex-column align-items-center">
	<div class="col-lg-9 bg-body-secondary rounded mt-3 p-2">
		<span class="d-flex justify-content-between">
			<h2 class="perm_panel_switch" panel="perm_panel_report_xlsx">Send <b>{{$data -> company -> name}}</b> report</h2>
			<button class="lead perm_panel_switch" panel="perm_panel_report_xlsx">Switch</button>
		</span>
		<form id="perm_panel_report_xlsx" action="{{route('mod.report_xlsx', $data -> company -> id)}}" method="post" enctype="multipart/form-data" class="perm-panel bg-body-tertiary rounded p-3">
			@csrf

			<div class="form-check">
				<input class="form-check-input" type="checkbox" value="1" id="flexCheckDefault" name="close_month">
				<label class="form-check-label" for="flexCheckDefault">
					Закрыть месяц
				</label>
			</div>

			<div class="form-group mb-2">
				<label for="file">File: </label>
				<input type="file" class="form-control" name="file" required>
			</div>
			<div class="form-group mb-2">
				<label for="note">Note:</label>
				<textarea name="note" class="form-control w-100" placeholder="Write a note" style="height: 100px">{{old('note')}}</textarea>
			</div>
			@foreach($workers as $worker)
				<input type="hidden" name="worker_name_{{$loop->iteration}}" value="{{$worker -> full_name}}">
				<div class="input-group mb-2">
					<span class="input-group-text">{{$worker -> full_name}}</span>
					<input type="number" class="form-control" name="worker_sold_{{$loop->iteration}}" placeholder="Sold" value="{{old('worker_sold_'.$loop->iteration, '0')}}" aria-label="Sold" required>
				</div>
			@endforeach

			<div class="d-flex justify-content-center">
				<button type="submit" class="btn btn-primary">Send</button>
			</div>
		</form>
	</div>
</div>
