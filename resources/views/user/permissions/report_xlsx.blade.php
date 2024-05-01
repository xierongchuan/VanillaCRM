@php
	use App\Models\User;

	$workers = User::where('dep_id', \Illuminate\Support\Facades\Auth::user() -> dep_id) -> get();

@endphp

<div class="row flex-column align-items-center">
	<div class="col-lg-9 bg-body-secondary rounded mt-3 p-2">
		<span class="d-flex justify-content-between">
			<h2 class="perm_panel_switch mx-1" panel="perm_panel_report_xlsx">Отправить отчёт в <b>{{$data -> company -> name}}</b></h2>
			<button class="lead perm_panel_switch m-1" panel="perm_panel_report_xlsx"><i class="bi bi-nintendo-switch"></i></button>
		</span>
		<form id="perm_panel_report_xlsx" action="{{route('mod.report_xlsx', $data -> company -> id)}}" method="post" enctype="multipart/form-data" class="perm-panel bg-body-tertiary rounded p-3">
			@csrf

			<div class="form-group mb-2">
				<label for="file">Файл отчёта: </label>
				<input type="file" class="form-control repost_xlsx_required_inputs" name="file" required>
			</div>
			<div class="my-2"></div>
			<div class="form-group mb-2 d-none">
				<label for="note">Заметка:</label>
				<textarea name="note" class="form-control w-100" placeholder="Написать заметки" style="height: 100px">{{old('note')}}</textarea>
			</div>
			<label for="">Продажи: </label>
			@foreach($workers as $worker)
				<input type="hidden" name="worker_name_{{$loop->iteration}}" value="{{$worker -> full_name}}">
				<div class="input-group mb-2">
					<span class="input-group-text col-8">{{$worker -> full_name}}</span>
					<input type="number" class="form-control col-4 repost_xlsx_required_inputs" name="worker_sold_{{$loop->iteration}}" placeholder="Sold" value="{{old('worker_sold_'.$loop->iteration, '0')}}" aria-label="Sold" required>
				</div>
			@endforeach

			<div class="form-check">
				<input class="form-check-input" type="checkbox" value="1" id="repost_xlsx_checkbox" name="close_month">
				<label class="form-check-label" for="repost_xlsx_checkbox">
					Закрыть месяц
				</label>
			</div>

			<div class="d-flex justify-content-center">
				<button type="submit" class="btn btn-primary">Отправить</button>
			</div>
		</form>
	</div>
</div>
