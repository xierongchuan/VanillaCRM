# VanillaCRM Frontend Migration Plan: Vue 3 + Tailwind CSS

## Отчёт об анализе репозитория

### Текущая структура проекта

#### Frontend файлы:
- **Главный layout**: `resources/views/layouts/main.blade.php`
- **Шаблонизатор**: Laravel Blade
- **JS файлы**:
  - `resources/js/bootstrap.js` - базовая инициализация axios
  - `resources/js/default.js` - для неавторизованных пользователей
  - `resources/js/admin.js` - для администраторов (jQuery, AJAX, Slick Carousel)
  - `resources/js/user.js` - для обычных пользователей (jQuery, расчёты)
  - `resources/js/genmanager.js` - дополнительный функционал
- **CSS**: `resources/sass/app.scss` + `resources/sass/_variables.scss`
- **Сборщик**: Vite (Laravel Vite Plugin)
- **Текущие зависимости**: Bootstrap 5.3.2, jQuery 3.7.1, Slick Carousel, Bootstrap Icons

#### Views структура:
- **Auth**: `resources/views/auth/sign_in.blade.php`
- **User pages**: `resources/views/user/` (разрешения, отчёты)
- **Company pages**: `resources/views/company/` (списки, создание, обновление, архивы, расходы)
- **Admin pages**: `resources/views/admin/index.blade.php`
- **Home**: `resources/views/home.blade.php` (~2016 строк, сложные отчёты с Slick Carousel)

#### Используемые паттерны:
- jQuery AJAX для динамической загрузки постов по департаментам
- jQuery для slideToggle панелей разрешений
- Slick Carousel для слайдеров отчётов
- Bootstrap 5 компоненты (navbar, dropdown, alerts)
- Встроенные inline скрипты для progress bars

---

## Полный план миграции

### Этап 0: Подготовка (Pre-flight)
**Цель**: Подготовить инфраструктуру для миграции без изменения функционала

**Задачи**:
- ✅ Анализ структуры проекта
- ✅ Документирование текущего состояния
- ✅ Создание плана миграции

---

### Этап 1: Scaffold - Базовая инфраструктура
**Branch**: `crm2`
**Commit**: `feat(frontend): add Vue 3 and Tailwind CSS CDN infrastructure`

#### Что делаем:
1. Добавляем CDN для Tailwind CSS и Vue 3 в `resources/views/layouts/main.blade.php`
2. Создаём корневой элемент `<div id="app">` вокруг основного контента
3. Создаём базовый файл `public/js/vue-app.js` с инициализацией Vue
4. Создаём файл `public/js/helpers.js` с утилитами для работы с CSRF и fetch API
5. Добавляем документацию `FRONTEND/README.md`

#### Изменяемые файлы:
- `resources/views/layouts/main.blade.php` (добавление CDN, обёртка #app)
- `public/js/vue-app.js` (новый файл)
- `public/js/helpers.js` (новый файл)
- `FRONTEND/README.md` (новый файл)

#### Риски:
- **Минимальные**: добавляем только CDN скрипты и пустую инициализацию Vue, не меняем существующий функционал
- Размер страницы увеличится на ~70-100 KB (CDN библиотеки)

#### Тестирование:
```bash
npm run start
# Открыть http://localhost:8000
# Проверить в консоли браузера: window.Vue должен быть определён
# Проверить что все страницы загружаются без ошибок
```

#### Ожидаемый результат:
- Vue 3 доступен глобально через CDN
- Tailwind CSS подключен через CDN
- Базовое Vue приложение инициализировано (пока пустое)
- Все существующие функции работают как раньше

---

### Этап 2: Layout Components - Преобразование Header и Nav
**Branch**: `crm2`
**Commit**: `feat(frontend): migrate header navigation to Vue 3`

#### Что делаем:
1. Создаём Vue компонент для Header/Navigation в `public/js/components/HeaderNav.js`
2. Переносим логику навигации из Blade шаблона в Vue компонент
3. Стилизуем с помощью Tailwind CSS вместо Bootstrap классов
4. Сохраняем CSRF токен и Laravel session интеграцию

#### Изменяемые файлы:
- `resources/views/layouts/main.blade.php` (рефакторинг header секции)
- `public/js/components/HeaderNav.js` (новый файл)
- `public/js/vue-app.js` (регистрация компонента)

#### Tailwind классы (примерный маппинг):
- `navbar navbar-expand-lg` → `flex items-center justify-between p-4 bg-gray-100 dark:bg-gray-800`
- `nav-link` → `px-3 py-2 text-gray-700 hover:text-blue-600 dark:text-gray-300`
- `btn btn-primary` → `px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700`
- `dropdown` → Vue-driven с `v-if` для показа/скрытия

#### Риски:
- **Средние**: изменение структуры навигации может повлиять на мобильную версию
- Необходимо тщательно протестировать responsive поведение

#### Тестирование:
- Проверить все ссылки навигации
- Проверить dropdown темы (light/dark)
- Проверить mobile menu (toggle)
- Проверить активные состояния ссылок

---

### Этап 3: Flash Messages - Система уведомлений
**Branch**: `crm2`
**Commit**: `feat(frontend): implement Vue-based flash messages component`

#### Что делаем:
1. Создаём Vue компонент `FlashMessages.js` для отображения алертов
2. Переносим Laravel session flash messages в Vue reactive state
3. Добавляем Tailwind стили для success/warning/danger alerts
4. Добавляем auto-dismiss функционал (опционально)

#### Изменяемые файлы:
- `resources/views/layouts/main.blade.php` (замена Blade @if на Vue компонент)
- `public/js/components/FlashMessages.js` (новый файл)
- `public/js/vue-app.js` (регистрация компонента)

#### Tailwind классы:
- `alert alert-success` → `p-4 mb-4 text-green-800 bg-green-100 rounded-lg dark:bg-green-900 dark:text-green-200`
- `alert alert-warning` → `p-4 mb-4 text-yellow-800 bg-yellow-100 rounded-lg`
- `alert alert-danger` → `p-4 mb-4 text-red-800 bg-red-100 rounded-lg`

#### Риски:
- **Минимальные**: простая замена статических alerts на Vue компонент

---

### Этап 4: Home Page Reports - Система отчётов (Сложный!)
**Branch**: `crm2`
**Commit**: `feat(frontend): migrate reports dashboard to Vue 3 with Tailwind`

#### Что делаем:
1. Создаём Vue компонент `ReportsCarousel.js` для замены Slick Carousel
2. Переносим данные из Blade `@foreach` в Vue reactive data
3. Реализуем carousel логику на Vue (prev/next buttons)
4. Переносим panel toggle логику из jQuery в Vue
5. Стилизуем с Tailwind CSS

#### Изменяемые файлы:
- `resources/views/home.blade.php` (рефакторинг в Vue компоненты)
- `public/js/components/ReportsCarousel.js` (новый файл)
- `public/js/components/ReportCard.js` (новый файл)
- `public/js/vue-app.js` (регистрация компонентов)
- `resources/js/admin.js` (удаление Slick Carousel инициализации)

#### Vue реализация carousel:
```javascript
const currentSlide = ref(0);
const nextSlide = () => {
  currentSlide.value = (currentSlide.value + 1) % slides.length;
};
const prevSlide = () => {
  currentSlide.value = (currentSlide.value - 1 + slides.length) % slides.length;
};
```

#### Риски:
- **Высокие**: `home.blade.php` очень большой файл (2016 строк)
- Много встроенной логики и данных из backend
- Потребуется осторожная работа с Laravel Blade переменными
- Необходимо сохранить все progress bar расчёты

#### Тестирование:
- Проверить загрузку всех отчётов
- Проверить carousel навигацию (prev/next)
- Проверить toggle панелей
- Проверить progress bars и расчёты процентов
- Проверить форматирование дат

---

### Этап 5: Admin Department/Post AJAX Forms
**Branch**: `crm2`
**Commit**: `feat(frontend): replace jQuery AJAX with Vue fetch API`

#### Что делаем:
1. Переносим логику из `resources/js/admin.js` в Vue композицию
2. Заменяем jQuery AJAX на fetch API с помощью `helpers.js`
3. Создаём Vue компонент для department/post selector
4. Добавляем loading states и error handling

#### Изменяемые файлы:
- `resources/js/admin.js` (удаление jQuery AJAX)
- `public/js/components/DepartmentPostSelector.js` (новый файл)
- `resources/views/company/user/create.blade.php` (интеграция Vue компонента)
- `resources/views/company/user/update.blade.php` (интеграция Vue компонента)

#### Пример fetch замены:
```javascript
// Было (jQuery):
$.ajax({
  url: '/company/' + companyId + '/department/' + depValue + '/posts',
  type: 'POST',
  headers: { 'X-CSRF-TOKEN': token }
});

// Станет (Vue + fetch):
const posts = await apiFetch(`/company/${companyId}/department/${depValue}/posts`, {
  method: 'POST'
});
```

#### Риски:
- **Средние**: изменение AJAX логики может повлиять на submit форм
- Необходимо сохранить CSRF токены

---

### Этап 6: User Panel Toggles and Calculations
**Branch**: `crm2`
**Commit**: `feat(frontend): migrate user permission panels to Vue`

#### Что делаем:
1. Переносим логику из `resources/js/user.js` в Vue
2. Заменяем jQuery slideToggle на Vue transitions
3. Переносим расчёт процентов для XLSX отчётов в Vue computed properties
4. Создаём Vue компоненты для permission panels

#### Изменяемые файлы:
- `resources/js/user.js` (удаление jQuery)
- `public/js/components/PermissionPanel.js` (новый файл)
- `public/js/components/XlsxReportForm.js` (новый файл)
- `resources/views/user/permission.blade.php` (интеграция компонентов)

#### Vue transitions замена:
```javascript
// Было (jQuery):
$('#panelId').slideToggle();

// Станет (Vue):
<Transition name="slide">
  <div v-show="isOpen">...</div>
</Transition>
```

#### Риски:
- **Минимальные**: простая замена jQuery на Vue

---

### Этап 7: Forms - Create/Update Components
**Branch**: `crm2`
**Commit**: `feat(frontend): migrate CRUD forms to Vue with validation`

#### Что делаем:
1. Создаём переиспользуемые Vue компоненты для форм
2. Используем `v-model` для two-way binding
3. Добавляем client-side валидацию
4. Обрабатываем submit через fetch API с CSRF
5. Стилизуем с Tailwind CSS

#### Изменяемые файлы:
- `resources/views/company/create.blade.php`
- `resources/views/company/update.blade.php`
- `resources/views/company/user/create.blade.php`
- `resources/views/company/user/update.blade.php`
- `resources/views/company/field/create.blade.php`
- `resources/views/company/field/update.blade.php`
- `resources/views/company/permission/create.blade.php`
- `resources/views/company/permission/update.blade.php`
- `resources/views/company/department/create.blade.php`
- `resources/views/company/department/update.blade.php`
- `resources/views/company/department/post/create.blade.php`
- `resources/views/company/department/post/update.blade.php`
- `public/js/components/CrudForm.js` (новый файл)

#### Tailwind form классы:
- `form-control` → `w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500`
- `btn btn-primary` → `px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700`
- `form-label` → `block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300`

#### Риски:
- **Средние**: много форм, требуется системный подход
- Необходимо сохранить Laravel валидацию на backend
- Обработка ошибок валидации от сервера

---

### Этап 8: Tables - Lists and Archives
**Branch**: `crm2`
**Commit**: `feat(frontend): implement Vue tables with sorting and pagination`

#### Что делаем:
1. Создаём Vue компонент для таблиц с данными
2. Добавляем client-side sorting
3. Интегрируем Laravel pagination через fetch API
4. Добавляем фильтрацию и поиск
5. Стилизуем с Tailwind CSS

#### Изменяемые файлы:
- `resources/views/company/list.blade.php`
- `resources/views/company/archive.blade.php`
- `resources/views/company/cashier_archive.blade.php`
- `resources/views/company/service_archive.blade.php`
- `resources/views/company/caffe_archive.blade.php`
- `public/js/components/DataTable.js` (новый файл)

#### Tailwind table классы:
- `table` → `min-w-full divide-y divide-gray-200 dark:divide-gray-700`
- `thead` → `bg-gray-50 dark:bg-gray-800`
- `th` → `px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider`
- `td` → `px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100`

#### Риски:
- **Средние**: сложная логика pagination и сортировки
- Необходимо сохранить backend endpoints для данных

---

### Этап 9: Expense Requests Dashboard
**Branch**: `crm2`
**Commit**: `feat(frontend): migrate expense requests to Vue components`

#### Что делаем:
1. Создаём Vue компоненты для expense dashboard
2. Реализуем real-time обновление данных (опционально)
3. Добавляем фильтры по статусам
4. Стилизуем с Tailwind CSS

#### Изменяемые файлы:
- `resources/views/company/expense_requests_dashboard.blade.php`
- `resources/views/company/expense_requests_table.blade.php`
- `public/js/components/ExpenseDashboard.js` (новый файл)
- `public/js/components/ExpenseTable.js` (новый файл)

#### Риски:
- **Средние**: интеграция с VanillaFlow API
- Необходимо сохранить backend logic без изменений

---

### Этап 10: Statistics Page
**Branch**: `crm2`
**Commit**: `feat(frontend): implement statistics dashboard with Vue and charts`

#### Что делаем:
1. Создаём Vue компоненты для статистики
2. Добавляем charts (можно Chart.js через CDN)
3. Реализуем filters и date pickers
4. Стилизуем с Tailwind CSS

#### Изменяемые файлы:
- `resources/views/company/stat.blade.php`
- `public/js/components/StatsDashboard.js` (новый файл)

#### Риски:
- **Низкие**: в основном визуализация данных

---

### Этап 11: Auth Page - Sign In
**Branch**: `crm2`
**Commit**: `feat(frontend): migrate sign in page to Vue with Tailwind`

#### Что делаем:
1. Создаём Vue компонент для login формы
2. Добавляем валидацию
3. Обрабатываем submit через fetch
4. Стилизуем с Tailwind CSS

#### Изменяемые файлы:
- `resources/views/auth/sign_in.blade.php`
- `public/js/components/LoginForm.js` (новый файл)

#### Риски:
- **Высокие**: критическая функция, нужно тщательное тестирование
- CSRF токены обязательны
- Обработка ошибок аутентификации

---

### Этап 12: Admin Panel - User Management
**Branch**: `crm2`
**Commit**: `feat(frontend): migrate admin panel to Vue components`

#### Что делаем:
1. Создаём Vue компоненты для admin dashboard
2. Переносим delete confirmations из jQuery в Vue
3. Стилизуем с Tailwind CSS

#### Изменяемые файлы:
- `resources/views/admin/index.blade.php`
- `public/js/components/AdminDashboard.js` (новый файл)
- `resources/js/admin.js` (удаление jQuery delete handlers)

#### Риски:
- **Средние**: важная админская функциональность

---

### Этап 13: Cleanup - Удаление старых зависимостей
**Branch**: `crm2`
**Commit**: `refactor(frontend): remove jQuery and Bootstrap dependencies`

#### Что делаем:
1. Удаляем jQuery из `package.json`
2. Удаляем Bootstrap из `package.json`
3. Удаляем Slick Carousel из `package.json`
4. Удаляем старые JS файлы (`admin.js`, `user.js`, `default.js`)
5. Очищаем `resources/sass/app.scss` от Bootstrap импортов

#### Изменяемые файлы:
- `package.json`
- `resources/js/admin.js` (удалить)
- `resources/js/user.js` (удалить)
- `resources/js/default.js` (удалить)
- `resources/sass/app.scss` (упростить)
- `vite.config.js` (обновить inputs)

#### Риски:
- **Высокие**: может сломать что-то упущенное
- Необходимо полное regression тестирование

---

### Этап 14: Custom CSS to Tailwind Migration
**Branch**: `crm2`
**Commit**: `refactor(frontend): migrate custom CSS to Tailwind utilities`

#### Что делаем:
1. Переносим inline styles из `main.blade.php` в Tailwind классы
2. Создаём Tailwind config для кастомных цветов/размеров
3. Минимизируем использование custom CSS

#### Изменяемые файлы:
- `resources/views/layouts/main.blade.php` (убрать inline styles)
- `public/css/custom.css` (новый файл для оставшихся custom styles)
- `tailwind.config.js` (новый файл, опционально для CDN config)

#### Custom классы для миграции:
- `.overlay-f` (warning overlay)
- `.col-1-5`, `.col-2-5` (custom columns)
- `.progress-bar-span` (progress bars)
- `.bg-success-tr`, `.bg-danger-tr` (transparent backgrounds)
- `.responsive-text-*` (responsive text)

#### Риски:
- **Средние**: может повлиять на визуальную консистентность

---

### Этап 15: Responsive & Mobile Testing
**Branch**: `crm2`
**Commit**: `fix(frontend): improve responsive design and mobile experience`

#### Что делаем:
1. Тестируем все страницы на mobile devices
2. Улучшаем responsive breakpoints
3. Оптимизируем touch interactions
4. Проверяем accessibility

#### Изменяемые файлы:
- Различные компоненты по необходимости

#### Риски:
- **Средние**: могут обнаружиться проблемы на разных устройствах

---

### Этап 16: Performance Optimization
**Branch**: `crm2`
**Commit**: `perf(frontend): optimize Vue components and lazy loading`

#### Что делаем:
1. Добавляем lazy loading для компонентов
2. Оптимизируем re-renders с `v-memo`
3. Добавляем virtualization для длинных списков (опционально)
4. Минимизируем bundle size

#### Изменяемые файлы:
- `public/js/vue-app.js`
- Различные компоненты

#### Риски:
- **Низкие**: улучшения производительности

---

### Этап 17: Documentation & Final Review
**Branch**: `crm2`
**Commit**: `docs(frontend): add comprehensive frontend documentation`

#### Что делаем:
1. Обновляем `FRONTEND/README.md` с финальными инструкциями
2. Добавляем комментарии в код
3. Создаём troubleshooting guide
4. Документируем API endpoints используемые frontend

#### Новые файлы:
- `FRONTEND/COMPONENTS.md` (описание всех Vue компонентов)
- `FRONTEND/API.md` (документация API endpoints)
- `FRONTEND/MIGRATION.md` (история миграции)
- `FRONTEND/ROLLBACK.md` (инструкции отката)

#### Риски:
- **Минимальные**: только документация

---

### Этап 18: Final Testing & QA
**Branch**: `crm2`
**Commit**: `test(frontend): comprehensive end-to-end testing`

#### Что делаем:
1. Полное regression тестирование всех функций
2. Проверка всех user flows:
   - Login/Logout
   - Создание/редактирование/удаление компаний
   - Управление пользователями
   - Отчёты (просмотр, экспорт)
   - Expense requests workflow
   - Statistics dashboard
3. Cross-browser testing (Chrome, Firefox, Safari)
4. Mobile devices testing
5. Performance testing (Lighthouse)

#### Чек-лист QA:
- [ ] Все формы отправляются на правильные endpoints
- [ ] CSRF токены работают везде
- [ ] Нет изменений в backend файлах
- [ ] Все AJAX запросы работают
- [ ] Pagination работает
- [ ] Sorting/filtering работают
- [ ] Flash messages отображаются
- [ ] Theme switcher работает
- [ ] Mobile responsive
- [ ] Нет JavaScript ошибок в консоли
- [ ] Нет 404 ошибок для ассетов
- [ ] Время загрузки страниц приемлемо
- [ ] Все иконки отображаются
- [ ] Print-friendly (если было)

---

## Технические детали и шаблоны кода

### Структура файлов после миграции

```
/tmp/gh-issue-solver-1760426899403/
├── public/
│   ├── js/
│   │   ├── vue-app.js              # Главная инициализация Vue
│   │   ├── helpers.js              # CSRF, fetch helpers
│   │   └── components/
│   │       ├── HeaderNav.js
│   │       ├── FlashMessages.js
│   │       ├── ReportsCarousel.js
│   │       ├── ReportCard.js
│   │       ├── DepartmentPostSelector.js
│   │       ├── PermissionPanel.js
│   │       ├── XlsxReportForm.js
│   │       ├── CrudForm.js
│   │       ├── DataTable.js
│   │       ├── ExpenseDashboard.js
│   │       ├── ExpenseTable.js
│   │       ├── StatsDashboard.js
│   │       ├── LoginForm.js
│   │       └── AdminDashboard.js
│   └── css/
│       └── custom.css              # Минимальные custom styles
├── resources/
│   ├── views/                       # Blade templates (упрощённые)
│   └── sass/                        # Минимальный SASS (если нужен)
├── PLANS/
│   └── plan-vue3-tailwind.md       # Этот файл
└── FRONTEND/
    ├── README.md                    # Главная документация
    ├── COMPONENTS.md                # Описание компонентов
    ├── API.md                       # API документация
    ├── MIGRATION.md                 # История миграции
    └── ROLLBACK.md                  # Инструкции отката
```

### Шаблон helpers.js

```javascript
// public/js/helpers.js

/**
 * Get CSRF token from meta tag
 */
export function csrfToken() {
  const el = document.querySelector('meta[name="csrf-token"]');
  return el ? el.getAttribute('content') : '';
}

/**
 * Wrapper for fetch API with CSRF and error handling
 * Note: Content-Type is only added for non-FormData payloads
 * to support file uploads properly
 */
export async function apiFetch(url, options = {}) {
  const token = csrfToken();

  // Build default headers
  const defaultHeaders = {
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN': token,
    'Accept': 'application/json'
  };

  // Only add Content-Type for JSON if body is not FormData
  // FormData needs to set its own Content-Type with boundary
  let shouldAddContentType = false;
  if (options.body && !(options.body instanceof FormData)) {
    if (typeof options.body === 'string' || typeof options.body === 'object') {
      shouldAddContentType = true;
    }
  }

  // Merge headers: custom headers should NOT overwrite essential headers like CSRF
  // First apply custom headers, then override with essentials
  const customHeaders = options.headers || {};
  const headers = Object.assign({}, customHeaders, defaultHeaders);

  // Only add Content-Type if caller didn't provide one and body needs it
  if (shouldAddContentType && !customHeaders['Content-Type']) {
    headers['Content-Type'] = 'application/json';
  }

  const config = Object.assign({
    credentials: 'same-origin',
    headers
  }, options);

  try {
    const res = await fetch(url, config);

    if (!res.ok) {
      const text = await res.text();
      throw new Error(`HTTP ${res.status}: ${text}`);
    }

    return await res.json();
  } catch (error) {
    console.error('API Fetch Error:', error);
    throw error;
  }
}

/**
 * Format date for display
 */
export function formatDate(dateString, format = 'dd.mm.yyyy') {
  const date = new Date(dateString);
  const day = String(date.getDate()).padStart(2, '0');
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const year = date.getFullYear();
  const hours = String(date.getHours()).padStart(2, '0');
  const minutes = String(date.getMinutes()).padStart(2, '0');
  const seconds = String(date.getSeconds()).padStart(2, '0');

  if (format === 'dd.mm.yyyy') {
    return `${day}.${month}.${year}`;
  } else if (format === 'dd.mm.yyyy HH:ii:ss') {
    return `${day}.${month}.${year} ${hours}:${minutes}:${seconds}`;
  }

  return dateString;
}
```

### Шаблон vue-app.js

```javascript
// public/js/vue-app.js

const { createApp, ref, reactive, computed, onMounted } = Vue;

// Import all components (will be added in later stages)

const App = {
  setup() {
    // Global state
    const user = ref(null);
    const theme = ref(document.body.getAttribute('data-bs-theme') || 'light');
    const flashMessages = ref({
      success: null,
      warning: null,
      errors: []
    });

    // Initialize flash messages from Laravel session
    onMounted(() => {
      // Flash messages will be passed from Blade template
      const flashData = window.__FLASH_MESSAGES__ || {};
      if (flashData.success) flashMessages.value.success = flashData.success;
      if (flashData.warning) flashMessages.value.warning = flashData.warning;
      if (flashData.errors) flashMessages.value.errors = flashData.errors;
    });

    // Methods
    const clearFlash = () => {
      flashMessages.value = { success: null, warning: null, errors: [] };
    };

    return {
      user,
      theme,
      flashMessages,
      clearFlash
    };
  }
};

// Create and mount Vue app
const app = createApp(App);

// Register global components here
// app.component('header-nav', HeaderNav);
// app.component('flash-messages', FlashMessages);
// etc.

app.mount('#app');
```

### Пример компонента (HeaderNav.js)

```javascript
// public/js/components/HeaderNav.js

export default {
  name: 'HeaderNav',
  props: {
    appName: String,
    isAuthenticated: Boolean,
    userRole: String,
    currentRoute: String,
    theme: String
  },
  data() {
    return {
      isMobileMenuOpen: false,
      isThemeDropdownOpen: false
    };
  },
  computed: {
    navItems() {
      if (this.userRole === 'admin') {
        return [
          { name: 'Главная', route: 'home.index' },
          { name: 'Администраторы', route: 'admin.index' },
          { name: 'Настройки', route: 'company.list' },
          { name: 'Статистика', route: 'stat.index' }
        ];
      } else if (this.userRole === 'user') {
        return [
          { name: 'Главная', route: 'home.index' },
          { name: 'Задачи', route: 'user.permission' }
        ];
      }
      return [{ name: 'Главная', route: 'home.index' }];
    }
  },
  methods: {
    toggleMobileMenu() {
      this.isMobileMenuOpen = !this.isMobileMenuOpen;
    },
    toggleThemeDropdown() {
      this.isThemeDropdownOpen = !this.isThemeDropdownOpen;
    },
    isActive(route) {
      return this.currentRoute === route;
    }
  },
  template: `
    <header>
      <nav class="flex items-center justify-between p-4 bg-gray-100 dark:bg-gray-800">
        <div class="container mx-auto flex items-center justify-between">
          <a :href="route('home.index')" class="text-xl font-bold text-gray-800 dark:text-white">
            {{ appName }}
          </a>

          <!-- Mobile menu button -->
          <button @click="toggleMobileMenu" class="lg:hidden">
            <i class="bi bi-list text-2xl"></i>
          </button>

          <!-- Desktop nav -->
          <div class="hidden lg:flex items-center space-x-4">
            <a v-for="item in navItems"
               :key="item.route"
               :href="route(item.route)"
               :class="['px-3 py-2 rounded', isActive(item.route) ? 'bg-blue-600 text-white' : 'text-gray-700 hover:text-blue-600 dark:text-gray-300']">
              {{ item.name }}
            </a>

            <!-- Theme switcher -->
            <div class="relative">
              <button @click="toggleThemeDropdown" class="text-xl">
                <i v-if="theme === 'light'" class="bi bi-lightbulb-fill"></i>
                <i v-else-if="theme === 'dark'" class="bi bi-cloud-haze2"></i>
                <i v-else class="bi bi-palette2"></i>
              </button>
              <div v-show="isThemeDropdownOpen" class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-700 rounded-lg shadow-lg">
                <a :href="route('theme.switch', 'light')" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600">
                  <i class="bi bi-lightbulb-fill"></i> Светлая
                </a>
                <a :href="route('theme.switch', 'dark')" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600">
                  <i class="bi bi-cloud-haze2"></i> Тёмная
                </a>
              </div>
            </div>

            <!-- Auth links -->
            <a v-if="!isAuthenticated" :href="route('auth.sign_in')" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
              Войти
            </a>
            <a v-else :href="route('auth.logout')" class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:text-blue-600">
              Выйти
            </a>
          </div>
        </div>
      </nav>
    </header>
  `
};
```

---

## Контроль качества

### Чек-лист для каждого PR:

- [ ] **Не изменены backend файлы** (app/, routes/, database/, migrations/, composer.json)
- [ ] **UI логика сохранена** (кнопки, формы, списки используют те же endpoints)
- [ ] **CSRF токены работают** во всех формах и AJAX запросах
- [ ] **Пагинация и фильтры работают** как раньше
- [ ] **Нет jQuery зависимостей** в новом коде (после Этапа 13)
- [ ] **Responsive дизайн работает** на mobile/tablet/desktop
- [ ] **Нет JavaScript ошибок** в консоли браузера
- [ ] **Все тесты проходят** (если есть frontend тесты)
- [ ] **Документация обновлена** для изменённых компонентов
- [ ] **Commit message соответствует** формату `feat(frontend):` / `refactor(frontend):` / `fix(frontend):`
- [ ] **Branch name**: `crm2`
- [ ] **Инструкция по тестированию** добавлена в описание PR

### Инструкции по откату:

Если что-то пошло не так, откат можно сделать следующим образом:

```bash
# 1. Вернуться к последнему рабочему коммиту
git log --oneline  # найти хороший коммит
git revert <commit-hash>

# 2. Или удалить ветку и начать заново
git checkout main
git branch -D crm2
git checkout -b crm2

# 3. Или восстановить конкретные файлы
git checkout <commit-hash> -- path/to/file
```

---

## Риски и митигация

### Общие риски:

1. **Размер проекта**: Более 2000 строк в некоторых Blade файлах
   - **Митигация**: Постепенная миграция, тестирование после каждого шага

2. **jQuery зависимости**: Много кода на jQuery
   - **Митигация**: Сначала добавляем Vue, потом постепенно убираем jQuery

3. **Slick Carousel**: Используется для критических отчётов
   - **Митигация**: Сначала реализуем Vue carousel, тестируем, потом удаляем Slick

4. **AJAX запросы**: Много мест с jQuery AJAX
   - **Митигация**: Создаём helper функцию `apiFetch()`, используем везде одинаково

5. **Bootstrap зависимости**: Глубокая интеграция Bootstrap 5
   - **Митигация**: Tailwind CDN позволяет сосуществовать с Bootstrap, убираем Bootstrap в конце

6. **CSRF токены**: Критически важны для безопасности
   - **Митигация**: Тестируем CSRF в каждой форме, используем единый helper

7. **Responsive дизайн**: Custom CSS для responsive текста
   - **Митигация**: Сохраняем custom CSS до финального этапа, тестируем на mobile

### Критические точки внимания:

- ✅ **Login форма** (Этап 11) - критическая функция, требует тщательного тестирования
- ✅ **Home page отчёты** (Этап 4) - самый большой и сложный файл
- ✅ **AJAX forms** (Этап 5) - много бизнес-логики
- ✅ **Admin delete** (Этап 12) - критическая админская функция

---

## Timeline (примерный)

| Этап | Название | Сложность | Время | Риск |
|------|----------|-----------|-------|------|
| 0 | Подготовка | Низкая | 1ч | Минимальный |
| 1 | Scaffold | Низкая | 2ч | Минимальный |
| 2 | Header/Nav | Средняя | 4ч | Средний |
| 3 | Flash Messages | Низкая | 2ч | Минимальный |
| 4 | Reports (Home) | **Высокая** | 12ч | **Высокий** |
| 5 | AJAX Forms | Средняя | 6ч | Средний |
| 6 | User Panels | Низкая | 4ч | Минимальный |
| 7 | CRUD Forms | Средняя | 10ч | Средний |
| 8 | Tables | Средняя | 8ч | Средний |
| 9 | Expense Dashboard | Средняя | 6ч | Средний |
| 10 | Statistics | Низкая | 4ч | Низкий |
| 11 | Login | **Высокая** | 4ч | **Высокий** |
| 12 | Admin Panel | Средняя | 4ч | Средний |
| 13 | Cleanup | Средняя | 4ч | Высокий |
| 14 | CSS Migration | Средняя | 6ч | Средний |
| 15 | Responsive Testing | Средняя | 6ч | Средний |
| 16 | Performance | Низкая | 4ч | Низкий |
| 17 | Documentation | Низкая | 4ч | Минимальный |
| 18 | Final QA | Высокая | 8ч | Высокий |
| **TOTAL** | | | **~99ч** | |

**Примечание**: Это оценки для одного разработчика. С автоматизацией (агент) может быть быстрее.

---

## Следующие шаги

Согласно инструкции, после создания этого плана я автоматически перехожу к **Этапу 1 - Scaffold**:

1. ✅ Добавлю CDN для Tailwind и Vue 3 в `resources/views/layouts/main.blade.php`
2. ✅ Создам `<div id="app">` обёртку
3. ✅ Создам `public/js/vue-app.js` с базовой инициализацией
4. ✅ Создам `public/js/helpers.js` с CSRF и fetch helpers
5. ✅ Создам `FRONTEND/README.md` с документацией
6. ✅ Сделаю коммит в ветку `crm2`
7. ✅ Обновлю PR #6

---

## Заключение

Этот план предоставляет детальную дорожную карту для полной миграции frontend VanillaCRM с Bootstrap 5 + jQuery на Tailwind CSS + Vue 3. Миграция будет выполняться итеративно, с маленькими атомарными коммитами, без изменения backend логики.

**Главные принципы**:
- ✅ Не менять backend
- ✅ Сохранять все эндпойнты
- ✅ Маленькие итерации
- ✅ Тестирование после каждого шага
- ✅ Документирование всех изменений
- ✅ Возможность отката

**Готов к началу Этапа 1!**
