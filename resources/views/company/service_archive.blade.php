@extends('layouts.main')

@section('title', 'Архив Сервис')

@php
    // Pass action buttons to header navigation component
    // This replaces the old @section('nav_right') pattern
    $__navRightButtons = [
        [
            'text' => 'Удалить последний отчёт',
            'href' => route('company.service.remove_last_report', compact('company')),
            'class' => 'tw-px-3 tw-py-2 tw-rounded-md tw-text-sm tw-font-medium tw-bg-red-600 tw-text-white tw-hover:bg-red-700 tw-transition-colors tw-no-underline'
        ]
    ];
@endphp

@section('content')
    <h1 class="text-center my-2">
        Архив Сервис {{ $company->name }}
    </h1>

    <div class="flex-column align-items-center">
        <div class="bg-body-tertiary rounded p-3 mb-2">

            <div class="my-1 m-auto border rounded py-2 row h4">

                <div class="col-6">
                    Дата
                </div>

                <div class="col-6 text-end">
                    Всего
                </div>

            </div>

            @foreach ($reports as $key => $val)
                <div class="my-1 m-auto border rounded py-2 row h4">

                    <div class="col-6 h5">
                        <a href="{{ route('company.service.archive', [$company, $val[0]]) }}">{{ $key }}</a>
                    </div>


                    <div class="col-6 text-end">
                        {{ number_format((int) $val[1], 0, '', ' ') }}
                    </div>
                </div>
            @endforeach

        </div>
    </div>

@endsection
