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
                <label for="link">Ссылка на отчёт: </label>
                <input type="text" class="form-control" name="link" placeholder="https://example.com" required>
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

            <div class="form-group mb-2">
                <label for="saldo">Сальдо:</label>
                <input type="number" class="form-control" name="saldo" id="saldo" placeholder="Сальдо">
            </div>

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

            <div class="d-flex justify-content-center">
                <button type="submit" class="btn btn-primary">Создать</button>
            </div>
        </form>
    </div>
</div>
