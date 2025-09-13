@extends('layouts.main')

@section('title', 'Панель Заявок на Расходы')
@section('content')
    <div class="container-fluid">
        <h1 class="text-center my-3">Панель Заявок на Расходы</h1>

        <!-- Tabs navigation -->
        <ul class="nav nav-tabs" id="expenseTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button"
                    role="tab" aria-controls="pending" aria-selected="true">
                    В ожидании
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="approved-tab" data-bs-toggle="tab" data-bs-target="#approved" type="button"
                    role="tab" aria-controls="approved" aria-selected="false">
                    Одобренные
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="declined-tab" data-bs-toggle="tab" data-bs-target="#declined" type="button"
                    role="tab" aria-controls="declined" aria-selected="false">
                    Отклоненные
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="issued-tab" data-bs-toggle="tab" data-bs-target="#issued" type="button"
                    role="tab" aria-controls="issued" aria-selected="false">
                    Выполненные
                </button>
            </li>
        </ul>

        <!-- Tabs content -->
        <div class="tab-content" id="expenseTabsContent">
            <!-- Pending Requests Tab -->
            <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                <div class="card mt-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>Заявки в ожидании</h5>
                        <div>
                            <button class="btn btn-sm btn-primary refresh-btn" data-status="pending">
                                <i class="bi bi-arrow-repeat"></i> Обновить
                            </button>
                            <button class="btn btn-sm btn-success export-btn" data-status="pending">
                                <i class="bi bi-file-earmark-spreadsheet"></i> Экспорт CSV
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        @include('company.expense_requests_table', [
                            'status' => 'pending',
                            'columns' => ['Дата', 'Заявитель', 'Описание', 'Сумма', 'Статус'],
                        ])
                    </div>
                </div>
            </div>

            <!-- Approved Requests Tab -->
            <div class="tab-pane fade" id="approved" role="tabpanel" aria-labelledby="approved-tab">
                <div class="card mt-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>Одобренные заявки</h5>
                        <div>
                            <button class="btn btn-sm btn-primary refresh-btn" data-status="approved">
                                <i class="bi bi-arrow-repeat"></i> Обновить
                            </button>
                            <button class="btn btn-sm btn-success export-btn" data-status="approved">
                                <i class="bi bi-file-earmark-spreadsheet"></i> Экспорт CSV
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        @include('company.expense_requests_table', [
                            'status' => 'approved',
                            'columns' => ['Дата', 'Заявитель', 'Описание', 'Сумма', 'Статус'],
                        ])
                    </div>
                </div>
            </div>

            <!-- Declined Requests Tab -->
            <div class="tab-pane fade" id="declined" role="tabpanel" aria-labelledby="declined-tab">
                <div class="card mt-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>Отклоненные заявки</h5>
                        <div>
                            <button class="btn btn-sm btn-primary refresh-btn" data-status="declined">
                                <i class="bi bi-arrow-repeat"></i> Обновить
                            </button>
                            <button class="btn btn-sm btn-success export-btn" data-status="declined">
                                <i class="bi bi-file-earmark-spreadsheet"></i> Экспорт CSV
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        @include('company.expense_requests_table', [
                            'status' => 'declined',
                            'columns' => ['Дата', 'Заявитель', 'Описание', 'Сумма'],
                        ])
                    </div>
                </div>
            </div>

            <!-- Issued Requests Tab -->
            <div class="tab-pane fade" id="issued" role="tabpanel" aria-labelledby="issued-tab">
                <div class="card mt-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>Выполненные заявки</h5>
                        <div>
                            <button class="btn btn-sm btn-primary refresh-btn" data-status="issued">
                                <i class="bi bi-arrow-repeat"></i> Обновить
                            </button>
                            <button class="btn btn-sm btn-success export-btn" data-status="issued">
                                <i class="bi bi-file-earmark-spreadsheet"></i> Экспорт CSV
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        @include('company.expense_requests_table', [
                            'status' => 'issued',
                            'columns' => ['Дата', 'Заявитель', 'Описание', 'Сумма', 'Исполнитель', 'Выдано'],
                        ])
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for expense details -->
    <div class="modal fade" id="expenseDetailsModal" tabindex="-1" aria-labelledby="expenseDetailsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="expenseDetailsModalLabel">Детали заявки</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="expenseDetailsContent">
                    <!-- Details will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Modal -->
    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel">Фильтры</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="filterForm">
                        <div class="mb-3">
                            <label for="dateFrom" class="form-label">Дата от</label>
                            <input type="date" class="form-control" id="dateFrom">
                        </div>
                        <div class="mb-3">
                            <label for="dateTo" class="form-label">Дата до</label>
                            <input type="date" class="form-control" id="dateTo">
                        </div>
                        <div class="mb-3">
                            <label for="amountMin" class="form-label">Сумма от</label>
                            <input type="number" class="form-control" id="amountMin" min="0" step="0.01">
                        </div>
                        <div class="mb-3">
                            <label for="amountMax" class="form-label">Сумма до</label>
                            <input type="number" class="form-control" id="amountMax" min="0" step="0.01">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="button" class="btn btn-primary" id="applyFilters">Применить</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .loading-spinner {
            display: inline-block;
            width: 1rem;
            height: 1rem;
            border: 2px solid rgba(0, 0, 0, 0.1);
            border-left-color: #0d6efd;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .sortable {
            cursor: pointer;
        }

        .sortable:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }

        .sort-indicator::after {
            content: " \2195";
            /* Up/Down arrow */
            opacity: 0.3;
        }

        .sort-indicator.asc::after {
            content: " \2191";
            /* Up arrow */
            opacity: 1;
        }

        .sort-indicator.desc::after {
            content: " \2193";
            /* Down arrow */
            opacity: 1;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Global variables
            let currentCompanyId = {{ $companyId ?? 1 }}; // Use default company ID 1
            let currentFilters = {};
            let currentSort = {};
            let autoRefreshInterval;

            // DOM Elements
            const refreshButtons = document.querySelectorAll('.refresh-btn');
            const exportButtons = document.querySelectorAll('.export-btn');

            // Initialize tabs
            loadAllTabs();

            // Start auto-refresh (every 5 minutes)
            startAutoRefresh();

            // Tab change handler
            const tabTrigger = document.querySelectorAll('[data-bs-toggle="tab"]');
            tabTrigger.forEach(trigger => {
                trigger.addEventListener('shown.bs.tab', function(event) {
                    const target = event.target.getAttribute('data-bs-target');
                    const status = target.substring(1); // Remove #
                    loadExpenseRequests(status, currentCompanyId);
                });
            });

            // Refresh buttons handler
            refreshButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const status = this.getAttribute('data-status');
                    loadExpenseRequests(status, currentCompanyId);
                });
            });

            // Export buttons handler
            exportButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const status = this.getAttribute('data-status');
                    exportExpenses(status, currentCompanyId);
                });
            });

            // Apply filters
            document.getElementById('applyFilters').addEventListener('click', function() {
                currentFilters = {
                    date_from: document.getElementById('dateFrom').value,
                    date_to: document.getElementById('dateTo').value,
                    amount_min: document.getElementById('amountMin').value,
                    amount_max: document.getElementById('amountMax').value
                };

                // Close modal
                bootstrap.Modal.getInstance(document.getElementById('filterModal')).hide();

                // Reload all tabs with new filters
                loadAllTabs();
            });

            // Functions
            function loadAllTabs() {
                const statuses = ['pending', 'approved', 'declined', 'issued'];
                statuses.forEach(status => {
                    loadExpenseRequests(status, currentCompanyId);
                });
            }

            function loadExpenseRequests(status, companyId) {
                const tableBody = document.querySelector(`#${status} .expense-requests-table tbody`);
                const pagination = document.querySelector(`#${status} .pagination-container`);

                // Show loading indicator
                tableBody.innerHTML = `
            <tr>
                <td colspan="10" class="text-center">
                    <div class="loading-spinner"></div> Загрузка...
                </td>
            </tr>
        `;

                // Build query parameters (without company_id as it's now in the URL path)
                const params = new URLSearchParams();

                // Add filters
                Object.keys(currentFilters).forEach(key => {
                    if (currentFilters[key]) {
                        params.append(key, currentFilters[key]);
                    }
                });

                // Add sorting
                if (currentSort[status]) {
                    params.append('sort_by', currentSort[status].column);
                    params.append('sort_direction', currentSort[status].direction);
                }

                // Make API request using company ID in URL path instead of query parameter
                fetch(`{{ url('/company') }}/${companyId}/expenses/${status}?${params.toString()}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            tableBody.innerHTML = `
                        <tr>
                            <td colspan="10" class="text-center text-danger">
                                Ошибка: ${data.error}
                            </td>
                        </tr>
                    `;
                            return;
                        }

                        renderTableData(status, data, tableBody, pagination);
                    })
                    .catch(error => {
                        console.error('Error loading expense requests:', error);
                        tableBody.innerHTML = `
                    <tr>
                        <td colspan="10" class="text-center text-danger">
                            Ошибка загрузки данных
                        </td>
                    </tr>
                `;
                    });
            }

            function renderTableData(status, data, tableBody, pagination) {
                if (!data.data || data.data.length === 0) {
                    tableBody.innerHTML = `
                <tr>
                    <td colspan="10" class="text-center">
                        Нет данных для отображения
                    </td>
                </tr>
            `;
                    if (pagination) {
                        pagination.innerHTML = '';
                    }
                    return;
                }

                // Render table rows
                let rows = '';
                data.data.forEach(item => {
                    rows += renderTableRow(status, item);
                });
                tableBody.innerHTML = rows;

                // Render pagination
                if (pagination && data.pagination) {
                    renderPagination(status, data.pagination, pagination);
                }

                // Add event listeners for view details buttons
                document.querySelectorAll(`#${status} .view-details-btn`).forEach(button => {
                    button.addEventListener('click', function() {
                        const requestId = this.getAttribute('data-id');
                        viewExpenseDetails(requestId, currentCompanyId);
                    });
                });
            }

            function renderTableRow(status, item) {
                let row = `<tr>
            <td>${item.date || 'N/A'}</td>
            <td>${item.requester_name || 'N/A'}</td>
            <td>${item.description || 'N/A'}</td>
            <td>${formatAmount(item.amount)}</td>`;

                switch (status) {
                    case 'pending':
                        row += `<td>В ожиданий</td>`;
                        break;
                    case 'approved':
                        row += `<td>Подтверждён</td>`;
                        break;
                    case 'issued':
                        row += `<td>Выдано</td>
                        <td>${formatAmount(item.issued_amount)}</td>`;
                        break;
                }

                row += `
            </tr>`;

                return row;
            }

            function renderPagination(status, paginationData, paginationContainer) {
                if (paginationData.last_page <= 1) {
                    paginationContainer.innerHTML = '';
                    return;
                }

                let paginationHtml = `
            <nav>
                <ul class="pagination justify-content-center">
        `;

                // Previous button
                if (paginationData.current_page > 1) {
                    paginationHtml += `
                <li class="page-item">
                    <a class="page-link" href="#" data-page="${paginationData.current_page - 1}" data-status="${status}">Предыдущая</a>
                </li>
            `;
                }

                // Page numbers
                for (let i = Math.max(1, paginationData.current_page - 2); i <= Math.min(paginationData.last_page,
                        paginationData.current_page + 2); i++) {
                    const activeClass = i === paginationData.current_page ? 'active' : '';
                    paginationHtml += `
                <li class="page-item ${activeClass}">
                    <a class="page-link" href="#" data-page="${i}" data-status="${status}">${i}</a>
                </li>
            `;
                }

                // Next button
                if (paginationData.current_page < paginationData.last_page) {
                    paginationHtml += `
                <li class="page-item">
                    <a class="page-link" href="#" data-page="${paginationData.current_page + 1}" data-status="${status}">Следующая</a>
                </li>
            `;
                }

                paginationHtml += `
                </ul>
            </nav>
        `;

                paginationContainer.innerHTML = paginationHtml;

                // Add event listeners to pagination links
                paginationContainer.querySelectorAll('.page-link').forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        const page = this.getAttribute('data-page');
                        const status = this.getAttribute('data-status');

                        // Add page parameter to request
                        const params = new URLSearchParams();
                        params.append('page', page);

                        // Add other filters
                        Object.keys(currentFilters).forEach(key => {
                            if (currentFilters[key]) {
                                params.append(key, currentFilters[key]);
                            }
                        });

                        // Add sorting
                        if (currentSort[status]) {
                            params.append('sort_by', currentSort[status].column);
                            params.append('sort_direction', currentSort[status].direction);
                        }

                        // Make API request using company ID in URL path
                        fetch(
                                `{{ url('/company') }}/${currentCompanyId}/expenses/${status}?${params.toString()}`
                            )
                            .then(response => response.json())
                            .then(data => {
                                if (data.error) {
                                    console.error('Error loading expense requests:', data
                                        .error);
                                    return;
                                }

                                const tableBody = document.querySelector(
                                    `#${status} .expense-requests-table tbody`);
                                const pagination = document.querySelector(
                                    `#${status} .pagination-container`);
                                renderTableData(status, data, tableBody, pagination);
                            })
                            .catch(error => {
                                console.error('Error loading expense requests:', error);
                            });
                    });
                });
            }

            function formatAmount(amount) {
                if (amount === undefined || amount === null) return 'N/A';
                return new Intl.NumberFormat('ru-RU', {
                    style: 'currency',
                    currency: 'RUB',
                    minimumFractionDigits: 2
                }).format(amount);
            }

            function viewExpenseDetails(requestId, companyId) {
                const modal = new bootstrap.Modal(document.getElementById('expenseDetailsModal'));
                const modalContent = document.getElementById('expenseDetailsContent');

                // Show loading state
                modalContent.innerHTML = `
            <div class="text-center">
                <div class="loading-spinner"></div>
                <p>Загрузка деталей...</p>
            </div>
        `;

                modal.show();

                // Fetch expense details using company ID in URL path
                fetch(`{{ url('/company') }}/${companyId}/expenses/${requestId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            modalContent.innerHTML = `
                        <div class="alert alert-danger">
                            Ошибка: ${data.error}
                        </div>
                    `;
                            return;
                        }

                        // Render expense details
                        modalContent.innerHTML = renderExpenseDetails(data.data);
                    })
                    .catch(error => {
                        console.error('Error loading expense details:', error);
                        modalContent.innerHTML = `
                    <div class="alert alert-danger">
                        Ошибка загрузки деталей заявки
                    </div>
                `;
                    });
            }

            function renderExpenseDetails(data) {
                return `
            <div class="row">
                <div class="col-md-6">
                    <p><strong>ID:</strong> ${data.id}</p>
                    <p><strong>Дата:</strong> ${data.date}</p>
                    <p><strong>Заявитель:</strong> ${data.requester_name}</p>
                    <p><strong>Описание:</strong> ${data.description}</p>
                    <p><strong>Сумма:</strong> ${formatAmount(data.amount)}</p>
                </div>
                <div class="col-md-6">
                    ${data.status ? `<p><strong>Статус:</strong> ${data.status}</p>` : ''}
                    ${data.issuer_name ? `<p><strong>Исполнитель:</strong> ${data.issuer_name}</p>` : ''}
                    ${data.issued_amount ? `<p><strong>Выдано:</strong> ${formatAmount(data.issued_amount)}</p>` : ''}
                </div>
            </div>
        `;
            }

            function exportExpenses(status, companyId) {
                // Build query parameters (without company_id as it's now in the URL path)
                const params = new URLSearchParams();

                // Add filters
                Object.keys(currentFilters).forEach(key => {
                    if (currentFilters[key]) {
                        params.append(key, currentFilters[key]);
                    }
                });

                // Add sorting
                if (currentSort[status]) {
                    params.append('sort_by', currentSort[status].column);
                    params.append('sort_direction', currentSort[status].direction);
                }

                // Trigger download using company ID in URL path
                const url = `{{ url('/company') }}/${companyId}/expenses/export/${status}?${params.toString()}`;
                window.location.href = url;
            }

            function startAutoRefresh() {
                // Clear any existing interval
                if (autoRefreshInterval) {
                    clearInterval(autoRefreshInterval);
                }

                // Set new interval (5 minutes = 300000 ms)
                autoRefreshInterval = setInterval(() => {
                    const activeTab = document.querySelector('.tab-pane.active').id;
                    loadExpenseRequests(activeTab, currentCompanyId);
                }, 300000);
            }

            // Sorting functionality
            document.querySelectorAll('.sortable').forEach(header => {
                header.addEventListener('click', function() {
                    const status = this.closest('.tab-pane').id;
                    const column = this.getAttribute('data-column');

                    // Update sort direction
                    if (!currentSort[status] || currentSort[status].column !== column) {
                        currentSort[status] = {
                            column: column,
                            direction: 'asc'
                        };
                    } else {
                        currentSort[status].direction = currentSort[status].direction === 'asc' ?
                            'desc' : 'asc';
                    }

                    // Update UI indicators
                    document.querySelectorAll(`#${status} .sortable`).forEach(el => {
                        el.classList.remove('asc', 'desc');
                    });
                    this.classList.add(currentSort[status].direction);

                    // Reload data with sorting
                    loadExpenseRequests(status, currentCompanyId);
                });
            });
        });
    </script>
@endsection
