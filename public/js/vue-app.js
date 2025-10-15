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

  // Stage 3: Flash Messages
  if (typeof window.FlashMessages !== 'undefined') {
    app.component('flash-messages', window.FlashMessages);
    console.log('FlashMessages component registered');
  }

  // Stage 4: Reports Carousel
  if (typeof window.ReportsCarousel !== 'undefined') {
    app.component('reports-carousel', window.ReportsCarousel);
    console.log('ReportsCarousel component registered');
  }

  // Stage 5: Department/Post Selector (AJAX Forms)
  if (typeof window.DepartmentPostSelector !== 'undefined') {
    app.component('department-post-selector', window.DepartmentPostSelector);
    console.log('DepartmentPostSelector component registered');
  }

  // Stage 6: User Panels (Permission Panels & XLSX Forms)
  if (typeof window.PermissionPanel !== 'undefined') {
    app.component('permission-panel', window.PermissionPanel);
    console.log('PermissionPanel component registered');
  }

  if (typeof window.XlsxReportForm !== 'undefined') {
    app.component('xlsx-report-form', window.XlsxReportForm);
    console.log('XlsxReportForm component registered');
  }

  // Stage 7: CRUD Form Components
  if (typeof window.FormInput !== 'undefined') {
    app.component('form-input', window.FormInput);
    console.log('FormInput component registered');
  }

  if (typeof window.FormTextarea !== 'undefined') {
    app.component('form-textarea', window.FormTextarea);
    console.log('FormTextarea component registered');
  }

  if (typeof window.FormRadioGroup !== 'undefined') {
    app.component('form-radio-group', window.FormRadioGroup);
    console.log('FormRadioGroup component registered');
  }

  if (typeof window.FormMultiSelect !== 'undefined') {
    app.component('form-multi-select', window.FormMultiSelect);
    console.log('FormMultiSelect component registered');
  }

  // Future components will be registered here:
  // etc.

  // Mount the app
  try {
    app.mount('#app');
    console.log('Vue app successfully mounted to #app');
  } catch (error) {
    console.error('Failed to mount Vue app:', error);
  }
});
