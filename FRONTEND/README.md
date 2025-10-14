# VanillaCRM Frontend Documentation

## Overview

VanillaCRM is migrating from Bootstrap 5 + jQuery to **Tailwind CSS + Vue 3**. This document provides instructions for developers working with the new frontend architecture.

## Current Status: Stage 1 - Scaffold Complete ✅

### What's Implemented

- ✅ **Vue 3** - Loaded via CDN (`https://unpkg.com/vue@3/dist/vue.global.prod.js`)
- ✅ **Tailwind CSS** - Loaded via CDN (`https://cdn.tailwindcss.com`)
- ✅ **Base Vue App** - Initialized in `public/js/vue-app.js`
- ✅ **Helper Functions** - CSRF and fetch utilities in `public/js/helpers.js`
- ✅ **Main Layout** - Updated `resources/views/layouts/main.blade.php` with Vue mount point

### What's NOT Yet Migrated

- ⏳ Header/Navigation (still Bootstrap + Blade)
- ⏳ Flash Messages (still Bootstrap + Blade)
- ⏳ All page components (still using jQuery)
- ⏳ Forms (still using traditional HTML forms)
- ⏳ Tables (still using Blade loops)
- ⏳ Reports/Carousel (still using Slick Carousel + jQuery)

## Tech Stack

### Frontend
- **Framework**: Vue 3 (Composition API)
- **CSS**: Tailwind CSS 3.x
- **Icons**: Bootstrap Icons (kept from original)
- **Build Tool**: Vite (Laravel Vite Plugin)

### Backend (Unchanged)
- **Framework**: Laravel 12
- **PHP**: 8.4
- **Database**: MariaDB/MySQL
- **Template Engine**: Blade

## Project Structure

```
VanillaCRM/
├── public/
│   ├── js/
│   │   ├── vue-app.js           # Main Vue 3 app initialization
│   │   ├── helpers.js            # Utility functions (CSRF, fetch, formatting)
│   │   └── components/           # Vue components (to be added in future stages)
│   └── css/
│       └── custom.css            # Custom styles (to be added if needed)
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   └── main.blade.php    # Main layout with Vue mount point
│   │   ├── auth/                 # Authentication views
│   │   ├── company/              # Company management views
│   │   ├── user/                 # User views
│   │   └── admin/                # Admin views
│   ├── js/                       # Legacy JS (to be phased out)
│   │   ├── admin.js             # Admin jQuery code (legacy)
│   │   ├── user.js              # User jQuery code (legacy)
│   │   └── default.js           # Default jQuery code (legacy)
│   └── sass/                     # SASS styles (still using Bootstrap)
│       └── app.scss
├── PLANS/
│   └── plan-vue3-tailwind.md    # Detailed migration plan
└── FRONTEND/                     # Frontend documentation
    └── README.md                 # This file
```

## Development Setup

### Prerequisites

- Node.js 18+ and npm
- PHP 8.4
- Composer
- MariaDB/MySQL

### Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd VanillaCRM
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node dependencies**
   ```bash
   npm install
   ```

4. **Configure environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Run migrations**
   ```bash
   php artisan migrate
   ```

### Running the Application

#### Development Mode (recommended)
```bash
# Starts both Laravel server (port 8000) and Vite dev server (port 5173)
npm run start
```

This will:
- Start PHP development server on `http://localhost:8000`
- Start Vite dev server with hot reload
- Watch for file changes

#### Separate Processes
```bash
# Terminal 1: Laravel server
php artisan serve

# Terminal 2: Vite dev server
npm run dev
```

#### Production Build
```bash
# Build assets for production
npm run build

# Serve with production settings
php artisan serve --env=production
```

### Docker Environment (Alternative)

```bash
# Start Docker containers
docker-compose up -d

# Access the application
# App: http://localhost:8002
# phpMyAdmin: http://localhost:8080
```

## Working with Vue 3

### App Structure

The main Vue app is initialized in `public/js/vue-app.js` and mounted to `<div id="app">` in the main layout.

#### Global State

The root Vue app provides global reactive state:

```javascript
{
  user: null,              // Current user (to be populated)
  theme: 'light',          // Current theme (light/dark)
  flashMessages: {         // Flash messages from Laravel
    success: null,
    warning: null,
    errors: []
  }
}
```

#### Accessing Global State in Components

```javascript
// In a component
export default {
  inject: ['user', 'theme', 'flashMessages'],
  setup() {
    // Use injected values
  }
}
```

### Helper Functions

#### CSRF Token

```javascript
// Get CSRF token
const token = csrfToken();
```

#### Fetch API Wrapper

```javascript
// GET request
const data = await apiFetch('/api/customers');

// POST request
const result = await apiFetch('/api/customers', {
  method: 'POST',
  body: JSON.stringify({ name: 'John', email: 'john@example.com' })
});

// With error handling
try {
  const data = await apiFetch('/api/endpoint');
} catch (error) {
  console.error('Request failed:', error.message);
}
```

#### Date Formatting

```javascript
// Format date as dd.mm.yyyy
const formatted = formatDate('2025-01-15');  // "15.01.2025"

// Format date with time
const formatted = formatDate('2025-01-15 14:30:00', 'dd.mm.yyyy HH:ii:ss');  // "15.01.2025 14:30:00"
```

#### Number Formatting

```javascript
// Format number with thousands separator
const formatted = formatNumber(1234567.89, 2);  // "1 234 567,89" (Russian locale)
```

#### Debounce

```javascript
// Debounce search input
const searchDebounced = debounce((query) => {
  // Search logic
}, 500);
```

## Working with Tailwind CSS

### Using Tailwind Classes

Tailwind CSS is loaded via CDN and supports dark mode out of the box.

#### Example Conversions

| Bootstrap 5 | Tailwind CSS |
|-------------|--------------|
| `btn btn-primary` | `px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700` |
| `container` | `container mx-auto` |
| `row` | `flex flex-wrap` |
| `col-md-6` | `w-full md:w-1/2` |
| `d-flex justify-content-between` | `flex justify-between` |
| `text-center` | `text-center` |
| `alert alert-success` | `p-4 mb-4 text-green-800 bg-green-100 rounded-lg` |
| `form-control` | `w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500` |

#### Dark Mode

Tailwind is configured to use class-based dark mode. Add `dark:` prefix for dark mode styles:

```html
<div class="bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
  Content adapts to theme
</div>
```

### Custom Tailwind Configuration

Tailwind is configured inline in `main.blade.php`:

```javascript
tailwind.config = {
  darkMode: 'class',
  theme: {
    extend: {
      // Add custom colors, spacing, etc. here
    }
  }
}
```

## Form Handling with Vue

### Basic Form Example

```javascript
export default {
  setup() {
    const form = reactive({
      name: '',
      email: '',
      phone: ''
    });

    const loading = ref(false);
    const errors = ref({});

    const submit = async () => {
      loading.value = true;
      errors.value = {};

      try {
        const result = await apiFetch('/api/customers', {
          method: 'POST',
          body: JSON.stringify(form)
        });

        // Handle success
        alert('Customer created!');
      } catch (error) {
        // Handle validation errors
        if (error.message.includes('422')) {
          errors.value = JSON.parse(error.message);
        }
      } finally {
        loading.value = false;
      }
    };

    return { form, loading, errors, submit };
  }
}
```

### Form Template

```html
<form @submit.prevent="submit">
  <div class="mb-4">
    <label class="block mb-2 text-sm font-medium">Name</label>
    <input
      v-model="form.name"
      type="text"
      class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
      :class="{ 'border-red-500': errors.name }"
    />
    <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name }}</p>
  </div>

  <button
    type="submit"
    :disabled="loading"
    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:opacity-50"
  >
    {{ loading ? 'Saving...' : 'Save' }}
  </button>
</form>
```

## Testing

### Manual Testing

After starting the development server:

1. **Verify Vue 3 is loaded**
   - Open browser console: `http://localhost:8000`
   - Type `Vue` - should show Vue object
   - Check for message: "Vue 3 App Initialized"

2. **Verify Tailwind CSS is loaded**
   - Inspect any element
   - Tailwind utility classes should work
   - Try adding `class="bg-red-500 text-white p-4"` to test

3. **Verify helpers are available**
   - Open browser console
   - Type `csrfToken()` - should return token
   - Type `apiFetch` - should show function

### Browser Console Checks

```javascript
// Check Vue
console.log(Vue);  // Should show Vue object

// Check CSRF token
console.log(csrfToken());  // Should return token string

// Check helper functions
console.log(typeof apiFetch);  // "function"
console.log(typeof formatDate);  // "function"

// Check flash messages
console.log(window.__FLASH_MESSAGES__);  // Should show flash data
```

## Migration Checklist

### Stage 1: Scaffold ✅ (Current)
- [x] Add Vue 3 CDN to main layout
- [x] Add Tailwind CSS CDN to main layout
- [x] Create Vue app initialization
- [x] Create helper functions
- [x] Add `#app` mount point
- [x] Pass flash messages to JavaScript
- [x] Create FRONTEND/README.md

### Stage 2: Layout Components (Next)
- [ ] Migrate Header/Navigation to Vue
- [ ] Convert Bootstrap navbar to Tailwind
- [ ] Create HeaderNav component

### Stage 3: Flash Messages
- [ ] Create FlashMessages Vue component
- [ ] Style with Tailwind
- [ ] Auto-dismiss functionality

### Stage 4-18: See PLANS/plan-vue3-tailwind.md

## Troubleshooting

### Vue Not Mounting

**Error**: `Failed to mount Vue app`

**Solutions**:
1. Check browser console for errors
2. Verify `#app` element exists in DOM
3. Ensure Vue CDN is loading (check Network tab)
4. Clear browser cache

### CSRF Token Missing

**Error**: `419 CSRF token mismatch`

**Solutions**:
1. Verify `<meta name="csrf-token">` exists in layout
2. Check `csrfToken()` returns a value
3. Ensure session is working
4. Clear Laravel cache: `php artisan cache:clear`

### Tailwind Classes Not Working

**Solutions**:
1. Check Tailwind CDN is loading in Network tab
2. Verify no CSS conflicts with Bootstrap
3. Try using `!important` prefix: `!bg-red-500`
4. Clear browser cache

### JavaScript Errors

**Error**: `Uncaught ReferenceError: X is not defined`

**Solutions**:
1. Check script load order in `main.blade.php`
2. Ensure helpers.js loads before vue-app.js
3. Check for typos in function names
4. Verify CDN scripts are loading

## Best Practices

### DO ✅

- Use Vue 3 Composition API for new components
- Use Tailwind utility classes instead of custom CSS
- Use `apiFetch()` for all AJAX requests (includes CSRF automatically)
- Add `@submit.prevent` to forms to prevent default submission
- Use `v-model` for two-way binding on form inputs
- Add loading states to async operations
- Handle errors gracefully with try-catch
- Test on mobile/tablet/desktop viewports
- Keep components small and focused
- Document complex logic with comments

### DON'T ❌

- Don't modify backend files (routes, controllers, migrations)
- Don't use jQuery in new code (being phased out)
- Don't create global variables (use Vue state or props)
- Don't write inline styles (use Tailwind classes)
- Don't ignore CSRF tokens (security risk!)
- Don't forget to test dark mode
- Don't skip responsive testing
- Don't remove functionality during migration
- Don't mix Bootstrap and Tailwind for same elements

## Resources

### Documentation
- [Vue 3 Docs](https://vuejs.org/guide/introduction.html)
- [Tailwind CSS Docs](https://tailwindcss.com/docs)
- [Laravel Blade Docs](https://laravel.com/docs/12.x/blade)
- [Vite Docs](https://vitejs.dev/)

### Migration Plan
- See `PLANS/plan-vue3-tailwind.md` for detailed migration roadmap

## Support

For questions or issues:
1. Check this README
2. Review `PLANS/plan-vue3-tailwind.md`
3. Check browser console for errors
4. Review Laravel logs: `storage/logs/laravel.log`

## Version History

### v1.0.0 - Stage 1 Scaffold (2025-10-14)
- Initial Vue 3 + Tailwind CSS infrastructure
- Base Vue app initialization
- Helper functions for CSRF and fetch
- Documentation created

---

**Status**: 🚧 Active Development
**Branch**: `crm2`
**Last Updated**: 2025-10-14
