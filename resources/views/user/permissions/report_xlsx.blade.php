@php
    use App\Services\PermissionService;

    $permissionService = new PermissionService();
    $workers = $permissionService->getUsersWithPermission(
        'sales_consultant',
        \Illuminate\Support\Facades\Auth::user()->dep_id,
    );
@endphp

<div class="row flex-column align-items-center">
    <div class="col-lg-9 bg-body-secondary rounded mt-3 p-2">
        <span class="d-flex justify-content-between">
            <h2 class="perm_panel_switch mx-1" panel="perm_panel_report_xlsx">Отправить отчёт в
                <b>{{ $data->company->name }}</b>
            </h2>
            <button class="lead perm_panel_switch m-1" panel="perm_panel_report_xlsx"><i
                    class="bi bi-nintendo-switch"></i></button>
        </span>
        <form id="perm_panel_report_xlsx" action="{{ route('mod.report_xlsx', $data->company->id) }}" method="post"
            enctype="multipart/form-data" class="perm-panel bg-body-tertiary rounded p-3">
            @csrf

            <div class="form-group mb-2">
                <label for="for_date">Дата:</label>
                <input type="date" class="form-control" id="for_date" name="for_date"
                    value="{{ old('date') ?? date('Y-m-d') }}" required>
            </div>

            <div class="form-group mb-2">
                <label for="file">Файл отчёта: </label>
                <input type="file" class="form-control repost_xlsx_required_inputs" name="file" required>
            </div>
            <div class="my-2"></div>
            <div class="form-group mb-2 d-none">
                <label for="note">Заметка:</label>
                <textarea name="note" class="form-control w-100" placeholder="Написать заметки" style="height: 100px">{{ old('note') }}</textarea>
            </div>
            <label for="">Продажи: </label>
            @foreach ($workers as $worker)
                <input type="hidden" name="worker_name_{{ $worker->id }}" value="{{ $worker->full_name }}">
                <div class="input-group mb-2">
                    <span class="input-group-text col-8">{{ $worker->full_name }}</span>
                    <input type="number" class="form-control col-4 repost_xlsx_required_inputs"
                        name="worker_sold_{{ $worker->id }}" placeholder="Sold"
                        value="{{ old('worker_sold_' . $worker->id, '0') }}" aria-label="Sold" required>
                </div>
            @endforeach

            <div class="d-flex justify-content-center">
                <button type="submit" class="btn btn-primary">Отправить</button>
            </div>
        </form>
    </div>
</div>
