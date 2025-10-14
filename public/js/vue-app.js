/**
 * VanillaCRM - Vue 3 App Initialization
 *
 * This file initializes the main Vue 3 application for VanillaCRM.
 * It provides global state management and component registration.
 */

// Wait for DOM to be ready
document.addEventListener('DOMContentLoaded', function() {
  // Check if Vue is available before destructuring
  if (typeof Vue === 'undefined') {
    console.error('Vue 3 is not loaded! Please check CDN connection.');
    return;
  }

  // Destructure Vue safely after checking availability
  const { createApp, ref, onMounted } = Vue;

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
      // Flash messages are passed from Blade template via window.__FLASH_MESSAGES__
      const flashData = window.__FLASH_MESSAGES__ || {};
      if (flashData.success) {
        flashMessages.value.success = flashData.success;
      }
      if (flashData.warning) {
        flashMessages.value.warning = flashData.warning;
      }
      if (flashData.errors && flashData.errors.length > 0) {
        flashMessages.value.errors = flashData.errors;
      }

      // Log Vue initialization
      console.log('Vue 3 App Initialized', {
        theme: theme.value,
        hasFlashMessages: !!(flashData.success || flashData.warning || flashData.errors?.length)
      });
    });

    // Methods
    const clearFlash = () => {
      flashMessages.value = { success: null, warning: null, errors: [] };
    };

    const setTheme = (newTheme) => {
      theme.value = newTheme;
      document.body.setAttribute('data-bs-theme', newTheme);
    };

      return {
        user,
        theme,
        flashMessages,
        clearFlash,
        setTheme
      };
    }
  };

  // Create Vue app instance
  const app = createApp(App);

  // Register global components
  // Stage 2: Header Navigation
  if (typeof window.HeaderNav !== 'undefined') {
    app.component('header-nav', window.HeaderNav);
    console.log('HeaderNav component registered');
  }

  // Future components will be registered here:
  // app.component('flash-messages', FlashMessages);
  // app.component('reports-carousel', ReportsCarousel);
  // etc.

  // Mount the app
  try {
    app.mount('#app');
    console.log('Vue app successfully mounted to #app');
  } catch (error) {
    console.error('Failed to mount Vue app:', error);
  }
});
