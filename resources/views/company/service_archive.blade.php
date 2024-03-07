@extends('layouts.main')

@section('title', 'Архив Сервис')

@section('nav_right')
    <li class="nav-item">
        <a class="btn btn-danger" aria-current="page"
           href="{{route('company.service.remove_last_report', compact('company'))}}">Удалить последний отчёт</a>
    </li>
@endsection

@section('content')
    <h1 class="text-center my-2">
        Архив Сервис {{$company -> name}}
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

            @foreach($reports as $key => $val)

                <div class="my-1 m-auto border rounded py-2 row h4">

                    <div class="col-6 h5">
                        <a href="{{ route('company.service.archive', [$company, $val[0]]) }}">{{$key}}</a>
                    </div>


                    <div class="col-6 text-end">
                        {{$val[1]}}
                    </div>
                </div>

            @endforeach

        </div>
    </div>

@endsection
