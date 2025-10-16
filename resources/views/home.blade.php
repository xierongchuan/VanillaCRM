@extends('layouts.main')

@section('title', 'Отчёты')

@section('content')

    <div class="tw-m-2"></div>

    {{-- ADMIN VIEW: All companies with carousel for report types --}}
    @if (isset($companies))

        {{-- Page Header --}}
        <div class="tw-bg-transparent tw-mt-2 tw-rounded tw-p-3 tw-pb-2 tw-mb-0">
            <div class="tw-flex tw-flex-wrap tw-justify-center">
                <h2 class="tw-text-2xl tw-font-bold tw-text-gray-900 dark:tw-text-white">Отчёты</h2>
            </div>
        </div>

        @foreach ($companies as $company)
            @php
                // Prepare data for each report type
                $dailyReportData = isset($coms_data[$company->id]) ? (array) json_decode($coms_data[$company->id]) : [];
                $serviceReportData = $srv_reps[$company->id] ?? [];
                $cafeReportData = $caffe_reps[$company->id] ?? [];
                $cashierReportData = $cashier_reps[$company->id] ?? [];
                $salesData = $sales_data[$company->id] ?? [];
                $companyPermissions = $coms_perms[$company->id] ?? [];
                $reportUrl = $last_repor_urls[$company->id] ?? '';

                // Archive data
                $archiveList = [];
                if (isset($archiveReports[$company->id]) && is_array($archiveReports[$company->id])) {
                    foreach ($archiveReports[$company->id] as $month => $report) {
                        $archiveList[] = [
                            'month' => $month,
                            'url' => isset($report->url) ? $report->url : '',
                            'sum' => isset($report->sum) ? $report->sum : 0,
                            'quantity' => isset($report->count) ? $report->count : 0,
                            'fact' => isset($report->fakt) ? $report->fakt : 0
                        ];
                    }
                }

                // Company fields
                $companyFields = [];
                if (isset($company->fields) && $company->fields) {
                    foreach ($company->fields as $field) {
                        $companyFields[] = [
                            'id' => $field->id,
                            'title' => $field->title,
                            'link' => $field->link
                        ];
                    }
                }

                // Collect available report slides
                $slides = [];

                if (in_array('report_xlsx', $companyPermissions) && !empty($dailyReportData)) {
                    $slides[] = [
                        'type' => 'daily-stats',
                        'permission' => 'report_xlsx',
                        'component' => 'daily-stats-report',
                        'props' => [
                            'companyId' => $company->id,
                            'companyName' => $company->name,
                            'reportData' => $dailyReportData,
                            'reportUrl' => $reportUrl,
                            'salesData' => $salesData,
                            'archiveReports' => $archiveList,
                            'companyFields' => $companyFields,
                            'archiveRoute' => route('company.archive', compact('company'))
                        ]
                    ];
                }

                if (in_array('report_service', $companyPermissions)) {
                    $slides[] = [
                        'type' => 'service',
                        'permission' => 'report_service',
                        'component' => 'service-report',
                        'props' => [
                            'companyId' => $company->id,
                            'companyName' => $company->name,
                            'reportData' => $serviceReportData,
                            'companyFields' => $companyFields,
                            'archiveUrl' => !empty($serviceReportData['updated_at']) ? route('company.service.archive', [$company, $serviceReportData['updated_at']]) : '',
                            'archiveListUrl' => route('company.service.archive.list', compact('company'))
                        ]
                    ];
                }

                if (in_array('report_caffe', $companyPermissions)) {
                    $slides[] = [
                        'type' => 'cafe',
                        'permission' => 'report_caffe',
                        'component' => 'cafe-report',
                        'props' => [
                            'companyId' => $company->id,
                            'companyName' => $company->name,
                            'reportData' => $cafeReportData,
                            'companyFields' => $companyFields,
                            'archiveUrl' => !empty($cafeReportData['updated_at']) ? route('company.caffe.archive', [$company, $cafeReportData['updated_at']]) : '',
                            'archiveListUrl' => route('company.caffe.archive.list', compact('company'))
                        ]
                    ];
                }

                if (in_array('report_cashier', $companyPermissions)) {
                    $slides[] = [
                        'type' => 'cashier',
                        'permission' => 'report_cashier',
                        'component' => 'cashier-report',
                        'props' => [
                            'companyId' => $company->id,
                            'companyName' => $company->name,
                            'reportData' => $cashierReportData,
                            'companyFields' => $companyFields,
                            'archiveUrl' => !empty($cashierReportData['updated_at']) ? route('company.cashier.archive', [$company, $cashierReportData['updated_at']]) : '',
                            'archiveListUrl' => route('company.cashier.archive.list', compact('company')),
                            'reportUrl' => isset($cashierReportData['link']) ? $cashierReportData['link'] : ''
                        ]
                    ];
                }
            @endphp

            {{-- Company Reports Carousel --}}
            @if (count($slides) > 0)
                <reports-carousel
                    :company-id="{{ $company->id }}"
                    company-name="{{ $company->name }}"
                    :slides="{{ json_encode($slides) }}"
                ></reports-carousel>
            @endif

        @endforeach

    {{-- USER VIEW: Single company with carousel for report types --}}
    @elseif (isset($company) && isset($data))

        {{-- Page Header --}}
        <div class="tw-bg-transparent tw-mt-2 tw-rounded tw-p-3 tw-pb-2 tw-mb-0">
            <div class="tw-flex tw-flex-wrap tw-justify-center">
                <h2 class="tw-text-2xl tw-font-bold tw-text-gray-900 dark:tw-text-white">Отчёты</h2>
            </div>
        </div>

        @php
            // Prepare data for each report type
            $dailyReportData = isset($data->com_data) ? (array) json_decode($data->com_data) : [];
            $serviceReportData = $srv_rep ?? [];
            $cafeReportData = isset($caffe_reps[$company->id]) ? $caffe_reps[$company->id] : [];
            $cashierReportData = $cashier_rep ?? [];
            $salesData = $data->sales_data ?? [];
            $userPermissions = $data->perm ?? [];

            // Company fields
            $companyFields = [];
            if (isset($company->fields) && $company->fields) {
                foreach ($company->fields as $field) {
                    $companyFields[] = [
                        'id' => $field->id,
                        'title' => $field->title,
                        'link' => $field->link
                    ];
                }
            }

            // Get report URL for daily stats
            $reportUrl = '';
            if (!empty($dailyReportData) && isset($dailyReportData['File'])) {
                $reportUrl = asset('storage/tmp/' . $dailyReportData['File']);
            }

            // Archive data (for daily stats)
            $archiveList = [];

            // Collect available report slides
            $slides = [];

            if (in_array('report_xlsx', $userPermissions) && !empty($dailyReportData)) {
                $slides[] = [
                    'type' => 'daily-stats',
                    'permission' => 'report_xlsx',
                    'component' => 'daily-stats-report',
                    'props' => [
                        'companyId' => $company->id,
                        'companyName' => $company->name,
                        'reportData' => $dailyReportData,
                        'reportUrl' => $reportUrl,
                        'salesData' => $salesData,
                        'archiveReports' => $archiveList,
                        'companyFields' => $companyFields,
                        'archiveRoute' => route('company.archive', compact('company'))
                    ]
                ];
            }

            if (in_array('report_service', $userPermissions)) {
                $slides[] = [
                    'type' => 'service',
                    'permission' => 'report_service',
                    'component' => 'service-report',
                    'props' => [
                        'companyId' => $company->id,
                        'companyName' => $company->name,
                        'reportData' => $serviceReportData,
                        'companyFields' => $companyFields,
                        'archiveUrl' => !empty($serviceReportData['updated_at']) ? route('company.service.archive', [$company, $serviceReportData['updated_at']]) : '',
                        'archiveListUrl' => route('company.service.archive.list', compact('company'))
                    ]
                ];
            }

            if (in_array('report_caffe', $userPermissions)) {
                $slides[] = [
                    'type' => 'cafe',
                    'permission' => 'report_caffe',
                    'component' => 'cafe-report',
                    'props' => [
                        'companyId' => $company->id,
                        'companyName' => $company->name,
                        'reportData' => $cafeReportData,
                        'companyFields' => $companyFields,
                        'archiveUrl' => !empty($cafeReportData['updated_at']) ? route('company.caffe.archive', [$company, $cafeReportData['updated_at']]) : '',
                        'archiveListUrl' => route('company.caffe.archive.list', compact('company'))
                    ]
                ];
            }

            if (in_array('report_cashier', $userPermissions)) {
                $slides[] = [
                    'type' => 'cashier',
                    'permission' => 'report_cashier',
                    'component' => 'cashier-report',
                    'props' => [
                        'companyId' => $company->id,
                        'companyName' => $company->name,
                        'reportData' => $cashierReportData,
                        'companyFields' => $companyFields,
                        'archiveUrl' => !empty($cashierReportData['updated_at']) ? route('company.cashier.archive', [$company, $cashierReportData['updated_at']]) : '',
                        'archiveListUrl' => route('company.cashier.archive.list', compact('company')),
                        'reportUrl' => isset($cashierReportData['link']) ? $cashierReportData['link'] : ''
                    ]
                ];
            }
        @endphp

        {{-- Company Reports Carousel --}}
        @if (count($slides) > 0)
            <reports-carousel
                :company-id="{{ $company->id }}"
                company-name="{{ $company->name }}"
                :slides="{{ json_encode($slides) }}"
            ></reports-carousel>
        @endif

    @else
        {{-- Guest view: Welcome message --}}
        <div class="tw-bg-gray-100 dark:tw-bg-gray-800 tw-mt-2 tw-rounded tw-p-6 tw-text-center">
            <h1 class="tw-text-3xl tw-font-bold tw-text-gray-900 dark:tw-text-white tw-mb-4">
                Добро пожаловать в VanillaCRM
            </h1>
            <p class="tw-text-lg tw-text-gray-700 dark:tw-text-gray-300">
                Пожалуйста, войдите в систему, чтобы просмотреть отчёты
            </p>
        </div>
    @endif

@endsection
