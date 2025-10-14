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
    <header class="bg-gray-100 dark:bg-gray-800 shadow-sm">
      <nav class="container mx-auto px-4 py-3">
        <div class="flex items-center justify-between">
          <!-- Brand/Logo -->
          <a :href="getRouteUrl('home.index')"
             class="text-xl font-bold text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
            {{ appName }}
          </a>

          <!-- Mobile menu button -->
          <button
            @click="toggleMobileMenu"
            class="lg:hidden p-2 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 rounded"
            aria-label="Toggle navigation"
            aria-expanded="false">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path v-if="!isMobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
              <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>

          <!-- Desktop Navigation -->
          <div class="hidden lg:flex lg:items-center lg:space-x-6">
            <!-- Nav Links -->
            <div class="flex items-center space-x-1">
              <a v-for="item in navItems"
                 :key="item.route"
                 :href="getRouteUrl(item.route)"
                 :class="[
                   'px-3 py-2 rounded-md text-sm font-medium transition-colors',
                   isActive(item.route)
                     ? 'bg-blue-600 text-white'
                     : 'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400'
                 ]">
                {{ item.name }}
              </a>
            </div>

            <!-- Right side navigation -->
            <div class="flex items-center space-x-4 border-l border-gray-300 dark:border-gray-600 pl-4">
              <!-- Theme Switcher Dropdown -->
              <div class="relative theme-dropdown">
                <button
                  @click="toggleThemeDropdown"
                  class="p-2 text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded transition-colors"
                  aria-label="Switch theme">
                  <i :class="['bi', themeIcon, 'text-xl']"></i>
                </button>
                <div v-show="isThemeDropdownOpen"
                     class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-700 rounded-lg shadow-lg py-1 z-50 border border-gray-200 dark:border-gray-600">
                  <a :href="getRouteUrl('theme.switch', 'light') + 'light'"
                     class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                    <i class="bi bi-lightbulb-fill mr-2"></i> Светлая
                  </a>
                  <a :href="getRouteUrl('theme.switch', 'dark') + 'dark'"
                     class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                    <i class="bi bi-cloud-haze2 mr-2"></i> Тёмная
                  </a>
                </div>
              </div>

              <!-- Auth Links -->
              <a v-if="!isAuthenticated"
                 :href="getRouteUrl('auth.sign_in')"
                 :class="[
                   'px-3 py-2 rounded-md text-sm font-medium transition-colors',
                   isActive('auth.sign_in')
                     ? 'bg-blue-600 text-white'
                     : 'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400'
                 ]">
                Войти
              </a>
              <a v-else
                 :href="getRouteUrl('auth.logout')"
                 class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 hover:text-red-600 dark:hover:text-red-400 transition-colors">
                Выйти
              </a>
            </div>
          </div>
        </div>

        <!-- Mobile Navigation -->
        <transition
          enter-active-class="transition ease-out duration-200"
          enter-from-class="opacity-0 -translate-y-1"
          enter-to-class="opacity-100 translate-y-0"
          leave-active-class="transition ease-in duration-150"
          leave-from-class="opacity-100 translate-y-0"
          leave-to-class="opacity-0 -translate-y-1">
          <div v-show="isMobileMenuOpen" class="lg:hidden mt-4 pb-3 space-y-1">
            <!-- Mobile Nav Links -->
            <a v-for="item in navItems"
               :key="item.route"
               :href="getRouteUrl(item.route)"
               @click="closeMobileMenu"
               :class="[
                 'block px-3 py-2 rounded-md text-base font-medium transition-colors',
                 isActive(item.route)
                   ? 'bg-blue-600 text-white'
                   : 'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400'
               ]">
              {{ item.name }}
            </a>

            <!-- Mobile Theme Switcher -->
            <div class="border-t border-gray-300 dark:border-gray-600 pt-3 mt-3">
              <div class="px-3 py-2 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                Тема
              </div>
              <a :href="getRouteUrl('theme.switch', 'light') + 'light'"
                 @click="closeMobileMenu"
                 class="flex items-center px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                <i class="bi bi-lightbulb-fill mr-2"></i> Светлая
              </a>
              <a :href="getRouteUrl('theme.switch', 'dark') + 'dark'"
                 @click="closeMobileMenu"
                 class="flex items-center px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                <i class="bi bi-cloud-haze2 mr-2"></i> Тёмная
              </a>
            </div>

            <!-- Mobile Auth -->
            <div class="border-t border-gray-300 dark:border-gray-600 pt-3 mt-3">
              <a v-if="!isAuthenticated"
                 :href="getRouteUrl('auth.sign_in')"
                 @click="closeMobileMenu"
                 :class="[
                   'block px-3 py-2 rounded-md text-base font-medium transition-colors',
                   isActive('auth.sign_in')
                     ? 'bg-blue-600 text-white'
                     : 'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400'
                 ]">
                Войти
              </a>
              <a v-else
                 :href="getRouteUrl('auth.logout')"
                 @click="closeMobileMenu"
                 class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 hover:text-red-600 dark:hover:text-red-400 transition-colors">
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
