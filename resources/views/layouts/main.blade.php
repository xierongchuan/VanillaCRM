<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="format-detection" content="telephone=no, date=no, address=no, email=no">
    <title>{{ config('app.name') }} - @yield('title')</title>
    <link rel="icon" href="/icon.png" type="image/png">

    {{-- Import Styles --}}
    @vite(['resources/sass/app.scss'])

    {{-- Tailwind CSS CDN (Play CDN - Development/Prototyping)
         Note: SRI (Subresource Integrity) cannot be used with cdn.tailwindcss.com
         because it's a JIT (Just-In-Time) compiler that dynamically generates CSS
         based on your markup. For production with SRI, use cdnjs.cloudflare.com instead. --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Tailwind config for dark mode and prefix to avoid Bootstrap conflicts
        tailwind.config = {
            prefix: 'tw-',
            darkMode: 'class',
            theme: {
                extend: {}
            },
            corePlugins: {
                preflight: false  // Disable Tailwind's reset to prevent conflicts with Bootstrap
            }
        }
    </script>

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

        /* Vue Transition Animations for Flash Messages */
        .fade-slide-enter-active,
        .fade-slide-leave-active {
            transition: all 0.3s ease;
        }

        .fade-slide-enter-from {
            opacity: 0;
            transform: translateY(-10px);
        }

        .fade-slide-leave-to {
            opacity: 0;
            transform: translateY(-10px);
        }
    </style>

</head>

<body data-bs-theme="{{ session('theme') ?? 'light' }}">

    <div id="app">
    {{-- Vue Header Navigation Component --}}
    <header-nav
        app-name="{{ config('app.name') }}"
        :is-authenticated="{{ Auth::check() ? 'true' : 'false' }}"
        user-role="{{ @Auth::user()->role ?? 'guest' }}"
        current-route="{{ Route::currentRouteName() }}"
        theme="{{ session('theme') ?? 'light' }}"
        :nav-right-buttons='@json($__navRightButtons ?? [])'
    ></header-nav>

    <main class="container">

        {{-- Vue Flash Messages Component --}}
        <flash-messages
            :messages="flashMessages"
            :auto-dismiss="true"
            :dismiss-delay="5000"
        ></flash-messages>

        @yield('content')

    </main>

    <span class="m-5 w-full"></span>

    <footer>

    </footer>

    </div>{{-- End #app --}}

    {{-- Vue 3 CDN (pinned to v3.5.22 for SRI) --}}
    <script src="https://unpkg.com/vue@3.5.22/dist/vue.global.prod.js"
            integrity="sha256-2unBeOhuCSQOWHIc20aoGslq4dxqhw0bG7n/ruPG0/4="
            crossorigin="anonymous"></script>

    {{-- Helpers for CSRF and fetch --}}
    <script src="/js/helpers.js"></script>

    {{-- Vue Components (must load before vue-app.js) --}}
    <script src="/js/components/HeaderNav.js"></script>
    <script src="/js/components/FlashMessages.js"></script>
    <script src="/js/components/ReportsCarousel.js"></script>

    {{-- Vue App Initialization (must load last) --}}
    <script src="/js/vue-app.js" defer></script>

    {{-- Pass flash messages to JavaScript --}}
    <script>
        window.__FLASH_MESSAGES__ = {
            success: @json(Session::get('success')),
            warning: @json(Session::get('warning')),
            errors: @json($errors->all())
        };
    </script>

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
