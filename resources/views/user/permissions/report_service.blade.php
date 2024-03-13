<div class="row flex-column align-items-center">
	<div class="col-lg-9 bg-body-secondary rounded mt-3 p-2">
		<span class="d-flex justify-content-between">
			<h2 class="perm_panel_switch mx-1" panel="perm_panel_create_worker">Отчёт сервис в <b>{{$data -> company -> name}}</b></h2>
			<button class="lead perm_panel_switch m-1" panel="perm_panel_create_worker"><i class="bi bi-nintendo-switch"></i></button>
		</span>
		<form id="perm_panel_create_worker" action="{{route('mod.report_service', $data -> company -> id)}}" method="post" class="perm-panel-list perm-panel bg-body-tertiary rounded p-3" style="display: block">
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

            <!-- Вывод суммы -->
            <div class="form-group mb-2">
                <label for="total">Итого:</label>
                <p id="total" class="form-control-static">0.00</p>
            </div>


            <div class="form-group mb-2">
                <label for="zap">Запчасть:</label>
                <input type="number" min="0" class="form-control" id="zap" name="zap" value="{{old('zap')}}" required>
            </div>

            <div class="form-group mb-2">
                <label for="srv">Сервис:</label>
                <input type="number" min="0" class="form-control" id="srv" name="srv" value="{{old('srv')}}" required>
            </div>

            <!-- Вывод суммы -->
            <div class="form-group mb-2">
                <label for="total2">Итого:</label>
                <p id="total2" class="form-control-static">0.00</p>
            </div>

			<div class="d-flex justify-content-center">
				<button type="submit" class="btn btn-primary">Создать</button>
			</div>
		</form>
	</div>
</div>

<script>
    // Обработчик изменения значений в инпутах
    document.addEventListener("input", function () {
        // Получаем значения из инпутов
        var dopValue = parseFloat(document.getElementById("dop").value) || 0;
        var nowValue = parseFloat(document.getElementById("now").value) || 0;
        var toValue = parseFloat(document.getElementById("to").value) || 0;
        var kuzValue = parseFloat(document.getElementById("kuz").value) || 0;
        var storeValue = parseFloat(document.getElementById("store").value) || 0;

        // Считаем сумму
        var totalValue = dopValue + nowValue + toValue + kuzValue + storeValue;

        // Записываем сумму в элемент "Итого"
        document.getElementById("total").textContent = totalValue.toFixed(2);



        var zapValue = parseFloat(document.getElementById("zap").value) || 0;
        var srvValue = parseFloat(document.getElementById("srv").value) || 0;

        // Считаем сумму
        var totalValue2 = zapValue + srvValue;

        // Записываем сумму в элемент "Итого"
        document.getElementById("total2").textContent = totalValue2.toFixed(2);
    });
</script>
