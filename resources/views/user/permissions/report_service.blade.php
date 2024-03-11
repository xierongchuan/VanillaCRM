<div class="row flex-column align-items-center">
	<div class="col-lg-9 bg-body-secondary rounded mt-3 p-2">
		<span class="d-flex justify-content-between">
			<h2 class="perm_panel_switch mx-1" panel="perm_panel_create_worker">Отчёт сервис в <b>{{$data -> company -> name}}</b></h2>
			<button class="lead perm_panel_switch m-1" panel="perm_panel_create_worker"><i class="bi bi-nintendo-switch"></i></button>
		</span>
		<form id="perm_panel_create_worker" action="{{route('mod.report_service', $data -> company -> id)}}" method="post" class="perm-panel-list perm-panel bg-body-tertiary rounded p-3">
			@csrf

			<div class="form-group mb-2">
				<label for="date">Дата:</label>
				<input type="date" class="form-control" id="date" name="date" value="{{old('date') ?? date("Y-m-d")}}" required>
			</div>
			<div class="form-group mb-2">
				<label for="dop">Доп:</label>
				<input type="number" min="0" class="form-control" id="dop" name="dop" value="{{old('dop')}}" required>
			</div>
			<div class="form-group mb-2">
				<label for="now">Текущий:</label>
				<input type="number" min="0" class="form-control" id="now" name="now" value="{{old('now')}}" required>
			</div>
			<div class="form-group mb-2">
				<label for="to">ТО:</label>
				<input type="number" min="0" class="form-control" id="to" name="to" value="{{old('to')}}" required>
			</div>

			<div class="form-group mb-2">
				<label for="kuz">Кузовной:</label>
				<input type="number" min="0" class="form-control" id="kuz" name="kuz" value="{{old('kuz')}}" required>
			</div>

			<div class="form-group mb-2">
				<label for="store">Магазин:</label>
				<input type="number" min="0" class="form-control" id="store" name="store" value="{{old('store')}}" required>
			</div>

            <div class="form-group mb-2">
                <label for="zap">Запчасть:</label>
                <input type="number" min="0" class="form-control" id="zap" name="zap" value="{{old('zap')}}" required>
            </div>

            <div class="form-group mb-2">
                <label for="srv">Сервис:</label>
                <input type="number" min="0" class="form-control" id="srv" name="srv" value="{{old('srv')}}" required>
            </div>
			<div class="d-flex justify-content-center">
				<button type="submit" class="btn btn-primary">Создать</button>
			</div>
		</form>
	</div>
</div>
