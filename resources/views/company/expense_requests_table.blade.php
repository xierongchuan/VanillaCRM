<div class="table-responsive">
    <table class="table table-striped table-hover expense-requests-table">
        <thead>
            <tr>
                @foreach ($columns as $column)
                    <th class="sortable" data-column="{{ strtolower(str_replace(' ', '_', $column)) }}">
                        {{ $column }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            <!-- Data will be loaded here via JavaScript -->
            <tr>
                <td colspan="{{ count($columns) + 1 }}" class="text-center">
                    <div class="loading-spinner"></div> Загрузка данных...
                </td>
            </tr>
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-between align-items-center mt-3">
    <div>
        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#filterModal">
            <i class="bi bi-funnel"></i> Фильтры
        </button>
    </div>
    <div class="pagination-container">
        <!-- Pagination will be loaded here via JavaScript -->
    </div>
    <div>
        <select class="form-select form-select-sm d-inline-block w-auto" id="perPageSelect">
            <option value="15">15 на странице</option>
            <option value="30">30 на странице</option>
            <option value="50">50 на странице</option>
            <option value="100">100 на странице</option>
        </select>
    </div>
</div>
