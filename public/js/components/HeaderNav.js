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
    }
  },
  data() {
    return {
      isMobileMenuOpen: false,
      isThemeDropdownOpen: false
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
    toggleThemeDropdown() {
      this.isThemeDropdownOpen = !this.isThemeDropdownOpen;
    },
    closeMobileMenu() {
      this.isMobileMenuOpen = false;
    },
    closeThemeDropdown() {
      this.isThemeDropdownOpen = false;
    },
    isActive(route) {
      return this.currentRoute === route;
    },
    getRouteUrl(routeName, params = {}) {
      // Use Laravel route helper if available
      if (typeof window.route === 'function') {
        return window.route(routeName, params);
      }
      // Fallback: construct URL manually
      // This is a simple fallback, may need adjustment based on actual routes
      const routeMap = {
        'home.index': '/',
        'admin.index': '/admin',
        'company.list': '/company/list',
        'stat.index': '/stat',
        'user.permission': '/user/permission',
        'auth.sign_in': '/auth/sign_in',
        'auth.logout': '/auth/logout',
        'theme.switch': '/theme/switch/'
      };
      return routeMap[routeName] || '/';
    }
  },
  mounted() {
    // Close dropdowns when clicking outside
    document.addEventListener('click', (event) => {
      const target = event.target;
      if (this.isThemeDropdownOpen && !target.closest('.theme-dropdown')) {
        this.closeThemeDropdown();
      }
    });
  },
  template: `
    <header class="tw-bg-gray-100 tw-dark:bg-gray-800 tw-shadow-sm">
      <nav class="tw-container tw-mx-auto tw-px-4 tw-py-3">
        <div class="tw-flex tw-items-center tw-justify-between">
          <!-- Brand/Logo -->
          <a :href="getRouteUrl('home.index')"
             class="tw-text-xl tw-font-bold tw-text-gray-900 tw-dark:text-white tw-hover:text-blue-600 tw-dark:hover:text-blue-400 tw-transition-colors">
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
                 :class="[ 'tw-px-3 tw-py-2 tw-rounded-md tw-text-sm tw-font-medium tw-transition-colors', isActive(item.route) ? 'tw-bg-blue-600 tw-text-white' : 'tw-text-gray-700 tw-dark:text-gray-300 tw-hover:bg-gray-200 tw-dark:hover:bg-gray-700 tw-hover:text-blue-600 tw-dark:hover:text-blue-400' ]">
                {{ item.name }}
              </a>
            </div>

            <!-- Right side navigation -->
            <div class="tw-flex tw-items-center tw-space-x-4 tw-border-l tw-border-gray-300 tw-dark:border-gray-600 tw-pl-4">
              <!-- Theme Switcher Dropdown -->
              <div class="tw-relative theme-dropdown">
                <button
                  @click="toggleThemeDropdown"
                  class="tw-p-2 tw-text-gray-600 tw-dark:text-gray-300 tw-hover:text-blue-600 tw-dark:hover:text-blue-400 tw-focus:outline-none tw-focus:ring-2 tw-focus:ring-blue-500 tw-rounded tw-transition-colors"
                  aria-label="Switch theme">
                  <i :class="['bi', themeIcon, 'tw-text-xl']"></i>
                </button>
                <div v-show="isThemeDropdownOpen"
                     class="tw-absolute right-0 tw-mt-2 tw-w-48 tw-bg-white tw-dark:bg-gray-700 tw-rounded-lg tw-shadow-lg tw-py-1 tw-z-50 tw-border tw-border-gray-200 tw-dark:border-gray-600">
                  <a :href="getRouteUrl('theme.switch', 'light')"
                     class="tw-flex tw-items-center tw-px-4 tw-py-2 tw-text-sm tw-text-gray-700 tw-dark:text-gray-300 tw-hover:bg-gray-100 tw-dark:hover:bg-gray-600 tw-transition-colors">
                    <i class="bi bi-lightbulb-fill tw-mr-2"></i> Светлая
                  </a>
                  <a :href="getRouteUrl('theme.switch', 'dark')"
                     class="tw-flex tw-items-center tw-px-4 tw-py-2 tw-text-sm tw-text-gray-700 tw-dark:text-gray-300 tw-hover:bg-gray-100 tw-dark:hover:bg-gray-600 tw-transition-colors">
                    <i class="bi bi-cloud-haze2 tw-mr-2"></i> Тёмная
                  </a>
                </div>
              </div>

              <!-- Auth Links -->
              <a v-if="!isAuthenticated"
                 :href="getRouteUrl('auth.sign_in')"
                 :class="[ 'tw-px-3 tw-py-2 tw-rounded-md tw-text-sm tw-font-medium tw-transition-colors', isActive('auth.sign_in') ? 'tw-bg-blue-600 tw-text-white' : 'tw-text-gray-700 tw-dark:text-gray-300 tw-hover:bg-gray-200 tw-dark:hover:bg-gray-700 tw-hover:text-blue-600 tw-dark:hover:text-blue-400' ]">
                Войти
              </a>
              <a v-else
                 :href="getRouteUrl('auth.logout')"
                 class="tw-px-3 tw-py-2 tw-rounded-md tw-text-sm tw-font-medium tw-text-gray-700 tw-dark:text-gray-300 tw-hover:bg-gray-200 tw-dark:hover:bg-gray-700 tw-hover:text-red-600 tw-dark:hover:text-red-400 tw-transition-colors">
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
               :class="[ 'tw-block tw-px-3 tw-py-2 tw-rounded-md tw-text-base tw-font-medium tw-transition-colors', isActive(item.route) ? 'tw-bg-blue-600 tw-text-white' : 'tw-text-gray-700 tw-dark:text-gray-300 tw-hover:bg-gray-200 tw-dark:hover:bg-gray-700 tw-hover:text-blue-600 tw-dark:hover:text-blue-400' ]">
              {{ item.name }}
            </a>

            <!-- Mobile Theme Switcher -->
            <div class="tw-border-t tw-border-gray-300 tw-dark:border-gray-600 tw-pt-3 tw-mt-3">
              <div class="tw-px-3 tw-py-2 tw-text-xs tw-font-semibold tw-text-gray-500 tw-dark:text-gray-400 tw-uppercase tw-tracking-wider">
                Тема
              </div>
              <a :href="getRouteUrl('theme.switch', 'light')"
                 @click="closeMobileMenu"
                 class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-rounded-md tw-text-base tw-font-medium tw-text-gray-700 tw-dark:text-gray-300 tw-hover:bg-gray-200 tw-dark:hover:bg-gray-700 tw-transition-colors">
                <i class="bi bi-lightbulb-fill tw-mr-2"></i> Светлая
              </a>
              <a :href="getRouteUrl('theme.switch', 'dark')"
                 @click="closeMobileMenu"
                 class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-rounded-md tw-text-base tw-font-medium tw-text-gray-700 tw-dark:text-gray-300 tw-hover:bg-gray-200 tw-dark:hover:bg-gray-700 tw-transition-colors">
                <i class="bi bi-cloud-haze2 tw-mr-2"></i> Тёмная
              </a>
            </div>

            <!-- Mobile Auth -->
            <div class="tw-border-t tw-border-gray-300 tw-dark:border-gray-600 tw-pt-3 tw-mt-3">
              <a v-if="!isAuthenticated"
                 :href="getRouteUrl('auth.sign_in')"
                 @click="closeMobileMenu"
                 :class="[ 'tw-block tw-px-3 tw-py-2 tw-rounded-md tw-text-base tw-font-medium tw-transition-colors', isActive('auth.sign_in') ? 'tw-bg-blue-600 tw-text-white' : 'tw-text-gray-700 tw-dark:text-gray-300 tw-hover:bg-gray-200 tw-dark:hover:bg-gray-700 tw-hover:text-blue-600 tw-dark:hover:text-blue-400' ]">
                Войти
              </a>
              <a v-else
                 :href="getRouteUrl('auth.logout')"
                 @click="closeMobileMenu"
                 class="tw-block tw-px-3 tw-py-2 tw-rounded-md tw-text-base tw-font-medium tw-text-gray-700 tw-dark:text-gray-300 tw-hover:bg-gray-200 tw-dark:hover:bg-gray-700 tw-hover:text-red-600 tw-dark:hover:text-red-400 tw-transition-colors">
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
