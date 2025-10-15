@php
    use App\Services\PermissionService;

    $workers = PermissionService::getUsersWithPermission(
        'sales_consultant',
        \Illuminate\Support\Facades\Auth::user()->com_id,
    );
@endphp

{{-- Vue 3 Component Integration (Stage 6) --}}
<permission-panel
    panel-id="perm_panel_report_xlsx"
    title="Отправить отчёт"
    company-name="{{ $data->company->name }}"
    :initially-open="false"
>
    <template #content>
        <xlsx-report-form
            :workers='@json($workers)'
            :company-id="{{ $data->company->id }}"
            :old-values='@json(old())'
        ></xlsx-report-form>
    </template>
</permission-panel>
