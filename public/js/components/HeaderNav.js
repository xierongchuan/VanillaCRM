/**
 * HeaderNav Component
 *
 * Main navigation header with mobile support and theme switcher
 * Replaces Bootstrap navbar with Tailwind CSS
 */

const HeaderNav = {
  name: 'HeaderNav',
  props: {
    appName: {
      type: String,
      required: true
    },
    isAuthenticated: {
      type: Boolean,
      default: false
    },
    userRole: {
      type: String,
      default: null
    },
    currentRoute: {
      type: String,
      default: ''
    },
    theme: {
      type: String,
      default: 'light'
    },
    // Additional navigation buttons from page-specific @section('nav_right')
    navRightButtons: {
      type: Array,
      default: () => []
    }
  },
  data() {
    return {
      isMobileMenuOpen: false
    };
  },
  computed: {
    navItems() {
      const items = [
        { name: 'Главная', route: 'home.index', roles: ['admin', 'user', 'guest'] }
      ];

      if (this.userRole === 'admin') {
        items.push(
          { name: 'Администраторы', route: 'admin.index', roles: ['admin'] },
          { name: 'Настройки', route: 'company.list', roles: ['admin'] },
          { name: 'Статистика', route: 'stat.index', roles: ['admin'] }
        );
      }

      if (this.userRole === 'user') {
        items.push(
          { name: 'Задачи', route: 'user.permission', roles: ['user'] }
        );
      }

      return items;
    },
    themeIcon() {
      switch (this.theme) {
        case 'light':
          return 'bi-lightbulb-fill';
        case 'dark':
          return 'bi-cloud-haze2';
        default:
          return 'bi-palette2';
      }
    }
  },
  methods: {
    toggleMobileMenu() {
      this.isMobileMenuOpen = !this.isMobileMenuOpen;
    },
    closeMobileMenu() {
      this.isMobileMenuOpen = false;
    },
    isActive(route) {
      return this.currentRoute === route;
    },
    getRouteUrl(routeName, params = {}) {
      // Always use the route helper from helpers.js
      // This ensures a single source of truth for routing
      if (typeof window.route === 'function') {
        return window.route(routeName, params);
      }

      // Fallback if route helper is not loaded (should not happen in production)
      console.warn(`Route helper not available for ${routeName}. Loading helpers.js is required.`);
      return '#';
    }
  },
  template: `
    <header class="tw-bg-gray-100 tw-dark:bg-gray-800 tw-shadow-sm">
      <nav class="tw-container tw-mx-auto tw-px-4 tw-py-3">
        <div class="tw-flex tw-items-center tw-justify-between">
          <!-- Brand/Logo -->
          <a :href="getRouteUrl('home.index')"
             class="tw-text-xl tw-font-bold tw-text-gray-900 tw-dark:text-white tw-hover:text-blue-600 tw-dark:hover:text-blue-400 tw-transition-colors tw-no-underline">
            {{ appName }}
          </a>

          <!-- Mobile menu button -->
          <button
            @click="toggleMobileMenu"
            class="lg:tw-hidden tw-p-2 tw-text-gray-600 tw-dark:text-gray-300 tw-hover:text-gray-900 tw-dark:hover:text-white tw-focus:outline-none tw-focus:ring-2 tw-focus:ring-blue-500 tw-rounded"
            aria-label="Toggle navigation"
            aria-expanded="false">
            <svg class="tw-w-6 tw-h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path v-if="!isMobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
              <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>

          <!-- Desktop Navigation -->
          <div class="tw-hidden lg:tw-flex lg:tw-items-center lg:tw-space-x-6">
            <!-- Nav Links -->
            <div class="tw-flex tw-items-center tw-space-x-1">
              <a v-for="item in navItems"
                 :key="item.route"
                 :href="getRouteUrl(item.route)"
                 :class="[ 'tw-px-3 tw-py-2 tw-rounded-md tw-text-sm tw-font-medium tw-transition-colors tw-no-underline', isActive(item.route) ? 'tw-bg-blue-600 tw-text-white' : 'tw-text-gray-700 tw-dark:text-gray-300 tw-hover:bg-gray-200 tw-dark:hover:bg-gray-700 tw-hover:text-blue-600 tw-dark:hover:text-blue-400' ]">
                {{ item.name }}
              </a>
            </div>

            <!-- Right side navigation -->
            <div class="tw-flex tw-items-center tw-space-x-4 tw-border-l tw-border-gray-300 tw-dark:border-gray-600 tw-pl-4">
              <!-- Page-specific action buttons (replaces @yield('nav_right')) -->
              <a v-for="(button, index) in navRightButtons"
                 :key="index"
                 :href="button.href"
                 :class="button.class || 'tw-px-3 tw-py-2 tw-rounded-md tw-text-sm tw-font-medium tw-bg-green-600 tw-text-white tw-hover:bg-green-700 tw-transition-colors tw-no-underline'">
                {{ button.text }}
              </a>

              <!-- Theme Switcher Toggle -->
              <a :href="getRouteUrl('theme.switch', theme === 'light' ? 'dark' : 'light')"
                 class="tw-p-2 tw-text-gray-600 tw-dark:text-gray-300 tw-hover:text-blue-600 tw-dark:hover:text-blue-400 tw-focus:outline-none tw-focus:ring-2 tw-focus:ring-blue-500 tw-rounded tw-transition-colors tw-no-underline"
                 aria-label="Switch theme"
                 title="Переключить тему">
                <i :class="['bi', themeIcon, 'tw-text-xl']"></i>
              </a>

              <!-- Auth Links -->
              <a v-if="!isAuthenticated"
                 :href="getRouteUrl('auth.sign_in')"
                 :class="[ 'tw-px-3 tw-py-2 tw-rounded-md tw-text-sm tw-font-medium tw-transition-colors tw-no-underline', isActive('auth.sign_in') ? 'tw-bg-blue-600 tw-text-white' : 'tw-text-gray-700 tw-dark:text-gray-300 tw-hover:bg-gray-200 tw-dark:hover:bg-gray-700 tw-hover:text-blue-600 tw-dark:hover:text-blue-400' ]">
                Войти
              </a>
              <a v-else
                 :href="getRouteUrl('auth.logout')"
                 class="tw-px-3 tw-py-2 tw-rounded-md tw-text-sm tw-font-medium tw-text-gray-700 tw-dark:text-gray-300 tw-hover:bg-gray-200 tw-dark:hover:bg-gray-700 tw-hover:text-red-600 tw-dark:hover:text-red-400 tw-transition-colors tw-no-underline">
                Выйти
              </a>
            </div>
          </div>
        </div>

        <!-- Mobile Navigation -->
        <transition
          enter-active-class="tw-transition tw-ease-out tw-duration-200"
          enter-from-class="tw-opacity-0 tw--translate-y-1"
          enter-to-class="tw-opacity-100 tw-translate-y-0"
          leave-active-class="tw-transition tw-ease-in tw-duration-150"
          leave-from-class="tw-opacity-100 tw-translate-y-0"
          leave-to-class="tw-opacity-0 tw--translate-y-1">
          <div v-show="isMobileMenuOpen" class="lg:tw-hidden tw-mt-4 tw-pb-3 tw-space-y-1">
            <!-- Mobile Nav Links -->
            <a v-for="item in navItems"
               :key="item.route"
               :href="getRouteUrl(item.route)"
               @click="closeMobileMenu"
               :class="[ 'tw-block tw-px-3 tw-py-2 tw-rounded-md tw-text-base tw-font-medium tw-transition-colors tw-no-underline', isActive(item.route) ? 'tw-bg-blue-600 tw-text-white' : 'tw-text-gray-700 tw-dark:text-gray-300 tw-hover:bg-gray-200 tw-dark:hover:bg-gray-700 tw-hover:text-blue-600 tw-dark:hover:text-blue-400' ]">
              {{ item.name }}
            </a>

            <!-- Mobile Page-specific action buttons -->
            <div v-if="navRightButtons.length > 0" class="tw-border-t tw-border-gray-300 tw-dark:border-gray-600 tw-pt-3 tw-mt-3">
              <a v-for="(button, index) in navRightButtons"
                 :key="index"
                 :href="button.href"
                 @click="closeMobileMenu"
                 :class="button.class || 'tw-block tw-px-3 tw-py-2 tw-rounded-md tw-text-base tw-font-medium tw-bg-green-600 tw-text-white tw-hover:bg-green-700 tw-transition-colors tw-no-underline'">
                {{ button.text }}
              </a>
            </div>

            <!-- Mobile Theme Switcher -->
            <div class="tw-border-t tw-border-gray-300 tw-dark:border-gray-600 tw-pt-3 tw-mt-3">
              <a :href="getRouteUrl('theme.switch', theme === 'light' ? 'dark' : 'light')"
                 @click="closeMobileMenu"
                 class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-rounded-md tw-text-base tw-font-medium tw-text-gray-700 tw-dark:text-gray-300 tw-hover:bg-gray-200 tw-dark:hover:bg-gray-700 tw-transition-colors tw-no-underline">
                <i :class="['bi', themeIcon, 'tw-mr-2']"></i>
                <span v-if="theme === 'light'">Переключить на тёмную</span>
                <span v-else>Переключить на светлую</span>
              </a>
            </div>

            <!-- Mobile Auth -->
            <div class="tw-border-t tw-border-gray-300 tw-dark:border-gray-600 tw-pt-3 tw-mt-3">
              <a v-if="!isAuthenticated"
                 :href="getRouteUrl('auth.sign_in')"
                 @click="closeMobileMenu"
                 :class="[ 'tw-block tw-px-3 tw-py-2 tw-rounded-md tw-text-base tw-font-medium tw-transition-colors tw-no-underline', isActive('auth.sign_in') ? 'tw-bg-blue-600 tw-text-white' : 'tw-text-gray-700 tw-dark:text-gray-300 tw-hover:bg-gray-200 tw-dark:hover:bg-gray-700 tw-hover:text-blue-600 tw-dark:hover:text-blue-400' ]">
                Войти
              </a>
              <a v-else
                 :href="getRouteUrl('auth.logout')"
                 @click="closeMobileMenu"
                 class="tw-block tw-px-3 tw-py-2 tw-rounded-md tw-text-base tw-font-medium tw-text-gray-700 tw-dark:text-gray-300 tw-hover:bg-gray-200 tw-dark:hover:bg-gray-700 tw-hover:text-red-600 tw-dark:hover:text-red-400 tw-transition-colors tw-no-underline">
                Выйти
              </a>
            </div>
          </div>
        </transition>
      </nav>
    </header>
  `
};

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
  module.exports = HeaderNav;
}

// Make available globally
window.HeaderNav = HeaderNav;
