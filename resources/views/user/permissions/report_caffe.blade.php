<div class="row flex-column align-items-center">
    <div class="col-lg-9 bg-body-secondary rounded mt-3 p-2">
        <span class="d-flex justify-content-between">
            <h2 class="perm_panel_switch mx-1" panel="perm_panel_create_worker">Отчёт кафе в
                <b>{{ $data->company->name }}</b></h2>
            <button class="lead perm_panel_switch m-1" panel="perm_panel_create_worker"><i
                    class="bi bi-nintendo-switch"></i></button>
        </span>
        <form id="perm_panel_create_worker" action="{{ route('mod.report_caffe', $data->company->id) }}"
            method="post" class="perm-panel-list perm-panel bg-body-tertiary rounded p-3" style="display: block">
            @csrf

            <div class="form-group mb-2">
                <label for="date">Дата:</label>
                <input type="date" class="form-control" id="date" name="date"
                    value="{{ old('date') ?? date('Y-m-d') }}" required>
            </div>
            <div class="form-group mb-2">
                <label for="profit_nal">Выручка:</label>
                <div class="input-group mb-1">
                    <input type="number" class="form-control" name="profit_nal" id="profit_nal" placeholder="Нал" aria-label="Нал">
                    <input type="number" class="form-control" name="profit_bez_nal" id="profit_bez_nal" placeholder="Без Нал" aria-label="Без Нал">
                </div>
                <div class="input-group mb-3">
                    <span class="input-group-text">Сумма:</span>
                    <span class="input-group-text" id="profit">0.00</span>
                </div>
            </div>
            <div class="form-group mb-2">
                <label for="dowaste_nalp">Расходы:</label>
                <div class="input-group mb-1">
                    <input type="number" class="form-control" name="waste_nal" id="waste_nal" placeholder="Нал" aria-label="Нал">
                    <input type="number" class="form-control" name="waste_bez_nal" id="waste_bez_nal" placeholder="Без Нал" aria-label="Без Нал">
                </div>
                <div class="input-group mb-3">
                    <span class="input-group-text">Сумма:</span>
                    <span class="input-group-text" id="waste">0.00</span>
                </div>
            </div>
            <div class="form-group mb-2">
                <label for="remains_nal">Остаток:</label>
                <div class="input-group mb-3">
                    <input type="number" class="form-control" name="remains_nal" id="remains_nal" placeholder="Нал" aria-label="Нал">
                    <input type="number" class="form-control" name="remains_bez_nal" id="remains_bez_nal" placeholder="Без Нал" aria-label="Без Нал">
                </div>
                <div class="input-group mb-3">
                    <span class="input-group-text">Сумма:</span>
                    <span class="input-group-text" id="remains">0.00</span>
                </div>
            </div>
            <div class="form-group mt-2">
                <div class="input-group mb-3">
                    <span class="input-group-text fw-bold">ОСТ:</span>
                    <span class="input-group-text fw-bold" id="remains_calc">0.00</span>
                </div>
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
        var profit_nal = parseFloat(document.getElementById("profit_nal").value) || 0;
        var profit_bez_nal = parseFloat(document.getElementById("profit_bez_nal").value) || 0;
        var waste_nal = parseFloat(document.getElementById("waste_nal").value) || 0;
        var waste_bez_nal = parseFloat(document.getElementById("waste_bez_nal").value) || 0;
        var remains_nal = parseFloat(document.getElementById("remains_nal").value) || 0;
        var remains_bez_nal = parseFloat(document.getElementById("remains_bez_nal").value) || 0;

        var profitVal = profit_nal + profit_bez_nal;
        var wasteVal = waste_nal + waste_bez_nal;
        var remainsVal = remains_nal + remains_bez_nal;
        var remainsCalcVal = profitVal - wasteVal;

        // Записываем сумму в элемент "Итого"
        document.getElementById("profit").textContent = profitVal.toFixed(2);
        document.getElementById("waste").textContent = wasteVal.toFixed(2);
        document.getElementById("remains").textContent = remainsVal.toFixed(2);
        document.getElementById("remains_calc").textContent = remainsCalcVal.toFixed(2);
    });
</script>
