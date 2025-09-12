@extends('layouts.main')

@section('title', 'История Заявок на Расходы')

@section('content')
    <h1 class="text-center my-2">
        История Заявок на Расходы {{ $company->name }}
    </h1>

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
            <button class="nav-link" id="rejected-tab" data-bs-toggle="tab" data-bs-target="#rejected" type="button"
                role="tab" aria-controls="rejected" aria-selected="false">
                Отклоненные
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed" type="button"
                role="tab" aria-controls="completed" aria-selected="false">
                Выполненные
            </button>
        </li>
    </ul>

    <!-- Tabs content -->
    <div class="tab-content" id="expenseTabsContent">
        <!-- Pending Requests Table -->
        <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="pending-tab">
            <div class="flex-column align-items-center">
                <div class="bg-body-tertiary rounded p-3 mb-2">
                    <div class="my-1 m-auto border rounded py-2 row h4">
                        <div class="col-3">Дата</div>
                        <div class="col-3">Сумма</div>
                        <div class="col-3">Категория</div>
                        <div class="col-3 text-end">Действия</div>
                    </div>

                    @if (isset($pendingRequests) && count($pendingRequests) > 0)
                        @foreach ($pendingRequests as $request)
                            <div class="my-1 m-auto border rounded py-2 row h4">
                                <div class="col-3 h5">{{ $request->date ?? 'N/A' }}</div>
                                <div class="col-3">{{ number_format($request->amount ?? 0, 0, '', ' ') }}</div>
                                <div class="col-3">{{ $request->category ?? 'N/A' }}</div>
                                <div class="col-3 text-end">
                                    <a href="#" class="btn btn-sm btn-primary">Просмотр</a>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="my-1 m-auto border rounded py-2 row h4 text-center">
                            <div class="col-12">
                                Нет заявок в ожидании
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Approved Requests Table -->
        <div class="tab-pane fade" id="approved" role="tabpanel" aria-labelledby="approved-tab">
            <div class="flex-column align-items-center">
                <div class="bg-body-tertiary rounded p-3 mb-2">
                    <div class="my-1 m-auto border rounded py-2 row h4">
                        <div class="col-3">Дата</div>
                        <div class="col-3">Сумма</div>
                        <div class="col-3">Категория</div>
                        <div class="col-3 text-end">Одобрен</div>
                    </div>

                    @if (isset($approvedRequests) && count($approvedRequests) > 0)
                        @foreach ($approvedRequests as $request)
                            <div class="my-1 m-auto border rounded py-2 row h4">
                                <div class="col-3 h5">{{ $request->date ?? 'N/A' }}</div>
                                <div class="col-3">{{ number_format($request->amount ?? 0, 0, '', ' ') }}</div>
                                <div class="col-3">{{ $request->category ?? 'N/A' }}</div>
                                <div class="col-3 text-end">{{ $request->approved_date ?? 'N/A' }}</div>
                            </div>
                        @endforeach
                    @else
                        <div class="my-1 m-auto border rounded py-2 row h4 text-center">
                            <div class="col-12">
                                Нет одобренных заявок
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Rejected Requests Table -->
        <div class="tab-pane fade" id="rejected" role="tabpanel" aria-labelledby="rejected-tab">
            <div class="flex-column align-items-center">
                <div class="bg-body-tertiary rounded p-3 mb-2">
                    <div class="my-1 m-auto border rounded py-2 row h4">
                        <div class="col-3">Дата</div>
                        <div class="col-3">Сумма</div>
                        <div class="col-3">Категория</div>
                        <div class="col-3 text-end">Причина</div>
                    </div>

                    @if (isset($rejectedRequests) && count($rejectedRequests) > 0)
                        @foreach ($rejectedRequests as $request)
                            <div class="my-1 m-auto border rounded py-2 row h4">
                                <div class="col-3 h5">{{ $request->date ?? 'N/A' }}</div>
                                <div class="col-3">{{ number_format($request->amount ?? 0, 0, '', ' ') }}</div>
                                <div class="col-3">{{ $request->category ?? 'N/A' }}</div>
                                <div class="col-3 text-end">{{ $request->rejection_reason ?? 'N/A' }}</div>
                            </div>
                        @endforeach
                    @else
                        <div class="my-1 m-auto border rounded py-2 row h4 text-center">
                            <div class="col-12">
                                Нет отклоненных заявок
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Completed Requests Table -->
        <div class="tab-pane fade" id="completed" role="tabpanel" aria-labelledby="completed-tab">
            <div class="flex-column align-items-center">
                <div class="bg-body-tertiary rounded p-3 mb-2">
                    <div class="my-1 m-auto border rounded py-2 row h4">
                        <div class="col-3">Дата</div>
                        <div class="col-3">Сумма</div>
                        <div class="col-3">Категория</div>
                        <div class="col-3 text-end">Выполнено</div>
                    </div>

                    @if (isset($completedRequests) && count($completedRequests) > 0)
                        @foreach ($completedRequests as $request)
                            <div class="my-1 m-auto border rounded py-2 row h4">
                                <div class="col-3 h5">{{ $request->date ?? 'N/A' }}</div>
                                <div class="col-3">{{ number_format($request->amount ?? 0, 0, '', ' ') }}</div>
                                <div class="col-3">{{ $request->category ?? 'N/A' }}</div>
                                <div class="col-3 text-end">{{ $request->completion_date ?? 'N/A' }}</div>
                            </div>
                        @endforeach
                    @else
                        <div class="my-1 m-auto border rounded py-2 row h4 text-center">
                            <div class="col-12">
                                Нет выполненных заявок
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
