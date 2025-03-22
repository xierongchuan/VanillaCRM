<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - @yield('title')</title>
    <link rel="icon" href="/icon.png" type="image/png">

    {{-- Import Styles --}}
    @vite(['resources/sass/app.scss'])

    @yield('includes')

    <style>
        body,
        html {
            height: 100%;
            margin: 0;
        }

        .overlay-f {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: repeating-linear-gradient(45deg,
                    yellow,
                    yellow 10px,
                    black 10px,
                    black 20px);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            color: white;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
        }

        .bg-body-test {
            background: repeating-linear-gradient(45deg,
                    rgb(109, 109, 0),
                    rgb(109, 109, 0) 10px,
                    black 10px,
                    black 20px);
        }

        /* Custom column sizes */
        .col-1-5 {
            flex: 0 0 12.5%;
            max-width: 12.5%;
        }

        .col-2-5 {
            flex: 0 0 20.833333%;
            max-width: 20.833333%;
        }

        .progress-bar-span {
            display: inline-block;
            /* Важно для корректного заполнения фона */
            padding: 0.5rem;
            /* Можно настроить */
            border: 1px solid red;
            /* Как у вас */
            border-radius: 0.25rem;
            /* Как у вас */
            text-align: center;
            position: relative;
            /* Для позиционирования псевдоэлемента */
            overflow: hidden;
            /* Чтобы фон не выходил за границы */
        }

        .progress-bar-span::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            background-color: rgba(255, 0, 0, 0.3);
            /* Цвет и прозрачность прогресса (можно менять) */
            z-index: -1;
            transition: width 0.3s ease-in-out;
            /* Анимация изменения ширины */
        }

        .bg-success-tr {
            background-color: #1a9e6199 !important;
        }

        .bg-danger-tr {
            background-color: #e74354bb !important;
        }

        .responsive-text-vir .responsive-text-ras .responsive-text-ost .responsive-text-safe {
            white-space: nowrap;
            /* Предотвращаем перенос текста */
        }

        /* Стандартный экран (больше 768px) */
        .responsive-text-vir::before {
            content: "Выручка";
            /* На большом экране отображается полностью */
        }

        .responsive-text-ras::before {
            content: "Расходы";
            /* На большом экране отображается полностью */
        }

        .responsive-text-ost::before {
            content: "Остаток";
            /* На большом экране отображается полностью */
        }

        .responsive-text-safe::before {
            content: "Сейф";
            /* На большом экране отображается полностью */
        }

        /* Мобильные экраны (ширина до 768px) */
        @media (max-width: 768px) {
            .responsive-text-vir::before {
                content: "Выр";
                /* На маленьком экране отображается сокращенно */
            }

            .responsive-text-ras::before {
                content: "Рас";
                /* На маленьком экране отображается сокращенно */
            }

            .responsive-text-ost::before {
                content: "Ост";
                /* На маленьком экране отображается сокращенно */
            }

            .responsive-text-safe::before {
                content: "Сф";
                /* На маленьком экране отображается сокращенно */
            }

            .responsive-text-vir .responsive-text-ras .responsive-text-ost {
                width: auto !important;
                /* Удаляем фиксированную ширину */
            }

        }
    </style>

</head>

<body data-bs-theme="{{ session('theme') ?? 'light' }}">

    <header>
        <nav class="navbar navbar-expand-lg {{ config('app.debug') ? 'bg-body-test' : 'bg-body-secondary' }} px-2h">
            <div class="container">
                <a class="navbar-brand" href="{{ route('home.index') }}"> {{ config('app.name') }}</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="
								nav-link

								@if (Route::currentRouteName() == 'home.index') active @endif

								"
                                aria-current="page" href="{{ route('home.index') }}">Главная</a>
                        </li>


                        @if (@Auth::user()->role === 'admin')
                            <li class="nav-item">
                                <a class="
								nav-link

								@if (Route::currentRouteName() == 'admin.index') active @endif

								"
                                    aria-current="page" href="{{ route('admin.index') }}">Администраторы</a>
                            </li>

                            <li class="nav-item">
                                <a class="
								nav-link

								@if (Route::currentRouteName() == 'company.list') active @endif

								"
                                    aria-current="page" href="{{ route('company.list') }}">Настройки</a>
                            </li>

                            <li class="nav-item">
                                <a class="
								nav-link

								@if (Route::currentRouteName() == 'stat.index') active @endif

								"
                                    aria-current="page" href="{{ route('stat.index') }}">Статистика</a>
                            </li>
                        @endif

                        @if (@Auth::user()->role === 'user')
                            <li class="nav-item">
                                <a class="
								nav-link

								@if (Route::currentRouteName() == 'user.permission') active @endif

								"
                                    aria-current="page" href="{{ route('user.permission') }}">Задачи</a>
                            </li>
                        @endif

                    </ul>


                    <ul class="d-flex navbar-nav mb-2 mb-lg-0">
                        @yield('nav_right')

                        @hasSection('nav_right')
                            <div class="vr mx-1  mr-2 d-none d-lg-block"></div>
                        @endif

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle fs-5 p-0" style="padding-top: 0.38rem!important;"
                                href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                @if (session('theme') == 'light')
                                    <i class="bi bi-lightbulb-fill"></i>
                                @elseif (session('theme') == 'dark')
                                    <i class="bi bi-cloud-haze2"></i>
                                @else
                                    <i class="bi bi-palette2"></i>
                                @endif
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('theme.switch', 'light') }}"><i
                                            class="bi bi-lightbulb-fill"></i> Светлая</a></li>
                                <li><a class="dropdown-item" href="{{ route('theme.switch', 'dark') }}"><i
                                            class="bi bi-cloud-haze2"></i> Тёмная</a></li>
                            </ul>
                        </li>

                        @if (!Auth::check())
                            <li class="nav-item">
                                <a class="
									nav-link

									@if (Route::currentRouteName() == 'auth.sign_in') active @endif

									"
                                    aria-current="page" href="{{ route('auth.sign_in') }}">Войти</a>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="nav-link" aria-current="page" href="{{ route('auth.logout') }}">Выйти</a>
                            </li>
                        @endif

                    </ul>

                </div>
            </div>
        </nav>
    </header>

    <main class="container">

        @if (Session::has('success'))
            <div class="alert alert-success mt-3">
                <ul>
                    <li>{{ Session::get('success') }}</li>
                </ul>
            </div>
        @endif

        @if (Session::has('warning'))
            <div class="alert alert-warning mt-3">
                <ul>
                    <li>{{ Session::get('warning') }}</li>
                </ul>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger mt-3">
                <ul class="m-0 my-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')

    </main>

    <span class="m-5 w-full"></span>

    <footer>

    </footer>

    {{-- Import JavaScript --}}
    @if (@Auth::user()->role === 'admin')
        @vite(['resources/js/admin.js'])
    @elseif(@Auth::user()->role === 'user')
        @vite(['resources/js/user.js'])
    @else
        @vite(['resources/js/default.js'])
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const progressSpans = document.querySelectorAll('.progress-bar-span');

            progressSpans.forEach(span => {
                const progressValue = parseFloat(span.getAttribute('data-progress'));
                console.log("Значение атрибута data-progress:", progressValue);
                if (!isNaN(progressValue) && progressValue >= 0 && progressValue <= 100) {
                    span.style.backgroundImage =
                        `linear-gradient(to right, rgba(30, 256, 30, 0.4) ${progressValue}%, transparent ${progressValue}%)`;
                } else {
                    console.error("Некорректное значение progress:", progressValue, "У элемента", span);
                }
            });
        });
    </script>
</body>

</html>
