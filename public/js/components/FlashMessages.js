/**
 * FlashMessages Component
 *
 * Displays success, warning, and error messages from Laravel session.
 * Supports auto-dismiss functionality and smooth animations.
 *
 * Props:
 * - messages: Object with success, warning, and errors arrays
 * - autoDismiss: Boolean (default: true)
 * - dismissDelay: Number in milliseconds (default: 5000)
 *
 * Usage:
 * <flash-messages :messages="flashMessages" :auto-dismiss="true"></flash-messages>
 */

const FlashMessages = {
  name: 'FlashMessages',
  props: {
    messages: {
      type: Object,
      default: () => ({
        success: null,
        warning: null,
        errors: []
      })
    },
    autoDismiss: {
      type: Boolean,
      default: true
    },
    dismissDelay: {
      type: Number,
      default: 5000
    }
  },
  data() {
    return {
      localMessages: {
        success: this.messages.success,
        warning: this.messages.warning,
        errors: [...(this.messages.errors || [])]
      },
      dismissTimeouts: {
        success: null,
        warning: null,
        errors: null
      }
    };
  },
  watch: {
    messages: {
      handler(newMessages) {
        this.localMessages.success = newMessages.success;
        this.localMessages.warning = newMessages.warning;
        this.localMessages.errors = [...(newMessages.errors || [])];

        // Start auto-dismiss timers
        if (this.autoDismiss) {
          this.startAutoDismissTimers();
        }
      },
      deep: true,
      immediate: true
    }
  },
  computed: {
    hasMessages() {
      return this.localMessages.success ||
             this.localMessages.warning ||
             (this.localMessages.errors && this.localMessages.errors.length > 0);
    }
  },
  methods: {
    dismissSuccess() {
      this.localMessages.success = null;
      if (this.dismissTimeouts.success) {
        clearTimeout(this.dismissTimeouts.success);
        this.dismissTimeouts.success = null;
      }
    },
    dismissWarning() {
      this.localMessages.warning = null;
      if (this.dismissTimeouts.warning) {
        clearTimeout(this.dismissTimeouts.warning);
        this.dismissTimeouts.warning = null;
      }
    },
    dismissErrors() {
      this.localMessages.errors = [];
      if (this.dismissTimeouts.errors) {
        clearTimeout(this.dismissTimeouts.errors);
        this.dismissTimeouts.errors = null;
      }
    },
    startAutoDismissTimers() {
      // Clear any existing timers
      this.clearTimeouts();

      // Set new timers for each message type
      if (this.localMessages.success) {
        this.dismissTimeouts.success = setTimeout(() => {
          this.dismissSuccess();
        }, this.dismissDelay);
      }

      if (this.localMessages.warning) {
        this.dismissTimeouts.warning = setTimeout(() => {
          this.dismissWarning();
        }, this.dismissDelay);
      }

      if (this.localMessages.errors && this.localMessages.errors.length > 0) {
        this.dismissTimeouts.errors = setTimeout(() => {
          this.dismissErrors();
        }, this.dismissDelay);
      }
    },
    clearTimeouts() {
      // Clear all timers (used when component unmounts or when resetting all timers)
      if (this.dismissTimeouts.success) {
        clearTimeout(this.dismissTimeouts.success);
        this.dismissTimeouts.success = null;
      }
      if (this.dismissTimeouts.warning) {
        clearTimeout(this.dismissTimeouts.warning);
        this.dismissTimeouts.warning = null;
      }
      if (this.dismissTimeouts.errors) {
        clearTimeout(this.dismissTimeouts.errors);
        this.dismissTimeouts.errors = null;
      }
    }
  },
  mounted() {
    // Start auto-dismiss on mount if enabled
    if (this.autoDismiss && this.hasMessages) {
      this.startAutoDismissTimers();
    }
  },
  beforeUnmount() {
    // Clean up timers when component is unmounted
    this.clearTimeouts();
  },
  template: `
    <div class="flash-messages-container">
      <!-- Success Message -->
      <transition name="fade-slide">
        <div v-if="localMessages.success"
             class="tw-p-4 tw-mb-4 tw-mt-3 tw-text-green-800 tw-bg-green-100 tw-rounded-lg tw-shadow-md tw-border tw-border-green-200 dark:tw-bg-green-900 dark:tw-text-green-200 dark:tw-border-green-800 tw-flex tw-items-start tw-justify-between tw-transition-all tw-duration-300">
          <div class="tw-flex tw-items-start tw-flex-1">
            <i class="bi bi-check-circle-fill tw-text-xl tw-mr-3 tw-mt-0.5"></i>
            <ul class="tw-m-0 tw-list-disc tw-pl-5">
              <li>{{ localMessages.success }}</li>
            </ul>
          </div>
          <button @click="dismissSuccess"
                  class="tw-ml-4 tw-text-green-800 dark:tw-text-green-200 hover:tw-text-green-600 dark:hover:tw-text-green-400 tw-transition-colors tw-flex-shrink-0"
                  aria-label="Close">
            <i class="bi bi-x-lg tw-text-xl"></i>
          </button>
        </div>
      </transition>

      <!-- Warning Message -->
      <transition name="fade-slide">
        <div v-if="localMessages.warning"
             class="tw-p-4 tw-mb-4 tw-mt-3 tw-text-yellow-800 tw-bg-yellow-100 tw-rounded-lg tw-shadow-md tw-border tw-border-yellow-200 dark:tw-bg-yellow-900 dark:tw-text-yellow-200 dark:tw-border-yellow-800 tw-flex tw-items-start tw-justify-between tw-transition-all tw-duration-300">
          <div class="tw-flex tw-items-start tw-flex-1">
            <i class="bi bi-exclamation-triangle-fill tw-text-xl tw-mr-3 tw-mt-0.5"></i>
            <ul class="tw-m-0 tw-list-disc tw-pl-5">
              <li>{{ localMessages.warning }}</li>
            </ul>
          </div>
          <button @click="dismissWarning"
                  class="tw-ml-4 tw-text-yellow-800 dark:tw-text-yellow-200 hover:tw-text-yellow-600 dark:hover:tw-text-yellow-400 tw-transition-colors tw-flex-shrink-0"
                  aria-label="Close">
            <i class="bi bi-x-lg tw-text-xl"></i>
          </button>
        </div>
      </transition>

      <!-- Error Messages -->
      <transition name="fade-slide">
        <div v-if="localMessages.errors && localMessages.errors.length > 0"
             class="tw-p-4 tw-mb-4 tw-mt-3 tw-text-red-800 tw-bg-red-100 tw-rounded-lg tw-shadow-md tw-border tw-border-red-200 dark:tw-bg-red-900 dark:tw-text-red-200 dark:tw-border-red-800 tw-flex tw-items-start tw-justify-between tw-transition-all tw-duration-300">
          <div class="tw-flex tw-items-start tw-flex-1">
            <i class="bi bi-x-circle-fill tw-text-xl tw-mr-3 tw-mt-0.5"></i>
            <ul class="tw-m-0 tw-list-disc tw-pl-5">
              <li v-for="(error, index) in localMessages.errors" :key="index">
                {{ error }}
              </li>
            </ul>
          </div>
          <button @click="dismissErrors"
                  class="tw-ml-4 tw-text-red-800 dark:tw-text-red-200 hover:tw-text-red-600 dark:hover:tw-text-red-400 tw-transition-colors tw-flex-shrink-0"
                  aria-label="Close">
            <i class="bi bi-x-lg tw-text-xl"></i>
          </button>
        </div>
      </transition>
    </div>
  `
};

// Export for use in vue-app.js
if (typeof window !== 'undefined') {
  window.FlashMessages = FlashMessages;
}
