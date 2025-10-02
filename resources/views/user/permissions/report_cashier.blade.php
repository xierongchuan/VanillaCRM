<div class="row flex-column align-items-center">
    <div class="col-lg-9 bg-body-secondary rounded mt-3 p-2">
        <span class="d-flex justify-content-between">
            <h2 class="perm_panel_switch mx-1" panel="perm_panel_report_cashier">Отчёт Кассир в
                <b>{{ $data->company->name }}</b>
            </h2>
            <button class="lead perm_panel_switch m-1" panel="perm_panel_report_cashier"><i
                    class="bi bi-nintendo-switch"></i></button>
        </span>
        <form id="perm_panel_report_cashier" action="{{ route('mod.report_cashier', $data->company->id) }}" method="post"
            class="perm-panel-list perm-panel bg-body-tertiary rounded p-3" enctype="multipart/form-data">
            @csrf

            <div class="form-group mb-2">
                <label for="date">Дата:</label>
                <input type="date" class="form-control" id="date" name="date"
                    value="{{ old('date') ?? date('Y-m-d') }}" required>
            </div>

            <div class="form-group mb-2">
                <label for="file">Файл отчёта: </label>
                <input type="file" class="form-control" name="file" required>
            </div>


            <div class="form-group mb-2">
                <label for="oborot_nal">Оборот:</label>
                <div class="input-group mb-1">
                    <input type="number" class="form-control" name="oborot_plus" id="oborot_plus" placeholder="Плюс"
                        aria-label="Плюс">
                    <input type="number" class="form-control" name="oborot_minus" id="oborot_minus" placeholder="Минус"
                        aria-label="Минус">
                </div>
            </div>

            <!-- Вывод суммы -->
            <div class="form-group mb-2">
                <label for="total2">Сальдо:</label>
                <p id="total2" class="form-control-static">0.00</p>
            </div>

            <hr>

            <div class="form-group mb-2">
                <label for="nalichka">Наличка:</label>
                <input type="number" class="form-control" name="nalichka" id="nalichka" placeholder="Наличка">
            </div>

            <div class="form-group mb-2">
                <label for="rs">Р/С:</label>
                <input type="number" class="form-control" name="rs" id="rs" placeholder="Р/С">
            </div>

            <div class="form-group mb-2">
                <label for="plastic">Пластик:</label>
                <input type="number" class="form-control" name="plastic" id="plastic" placeholder="Пластик">
            </div>

            <div class="form-group mb-2">
                <label for="skidki">Скидки:</label>
                <input type="number" class="form-control" name="skidki" id="skidki" placeholder="Скидки">
            </div>

            <!-- Вывод суммы -->
            <div class="form-group mb-2">
                <label for="total">Итого: </label>
                <p id="total" class="form-control-static">0.00</p>
            </div>

            <!-- Вывод к сдаче -->
            <div class="form-group mb-2">
                <label for="sdacha">К сдаче: </label>
                <p id="sdacha" class="form-control-static">0.00</p>
            </div>

            <div class="d-flex justify-content-center">
                <button type="submit" class="btn btn-primary">Создать</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Обработчик изменения значений в инпутах
    document.addEventListener("input", function() {
        var oborot_plus = parseFloat(document.getElementById("oborot_plus").value) || 0;
        var oborot_minus = parseFloat(document.getElementById("oborot_minus").value) || 0;

        // Считаем сумму
        var totalValue2 = oborot_plus - oborot_minus;

        // Записываем сумму в элемент "Итого"
        document.getElementById("total2").textContent = totalValue2
            .toLocaleString("ru-RU", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });


        // Получаем значения из инпутов
        var nalichka = parseFloat(document.getElementById("nalichka").value) || 0;
        var rs = parseFloat(document.getElementById("rs").value) || 0;
        var plastic = parseFloat(document.getElementById("plastic").value) || 0;
        var skidki = parseFloat(document.getElementById("skidki").value) || 0;

        // Считаем сумму
        var totalValue = nalichka + rs + plastic + skidki;

        // Записываем сумму в элемент "Итого"
        document.getElementById("total").textContent = totalValue
            .toLocaleString("ru-RU", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

        // Считаем сдачу
        var sdacha = nalichka - oborot_minus;
        // Записываем сумму в элемент "К сдаче"
        document.getElementById("sdacha").textContent = sdacha
            .toLocaleString("ru-RU", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

    });
</script>
