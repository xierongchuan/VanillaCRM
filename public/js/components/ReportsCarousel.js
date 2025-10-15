/**
 * ReportsCarousel Component
 *
 * Replaces Slick Carousel with Vue-based carousel for company reports
 * Handles sliding between different report types (xlsx, service, caffe, cashier)
 */

const ReportsCarousel = {
  name: 'ReportsCarousel',
  props: {
    companyId: {
      type: [Number, String],
      required: true
    },
    companyName: {
      type: String,
      required: true
    },
    // Array of report slots to display
    // Each slot contains report HTML content
    reportSlots: {
      type: Array,
      default: () => []
    }
  },
  data() {
    return {
      currentSlide: 0,
      isPanelOpen: true
    };
  },
  computed: {
    hasMultipleSlides() {
      return this.reportSlots.length > 1;
    },
    canGoPrev() {
      return this.currentSlide > 0;
    },
    canGoNext() {
      return this.currentSlide < this.reportSlots.length - 1;
    }
  },
  methods: {
    nextSlide() {
      if (this.canGoNext) {
        this.currentSlide++;
      } else if (this.reportSlots.length > 0) {
        // Loop back to first slide
        this.currentSlide = 0;
      }
    },
    prevSlide() {
      if (this.canGoPrev) {
        this.currentSlide--;
      } else if (this.reportSlots.length > 0) {
        // Loop to last slide
        this.currentSlide = this.reportSlots.length - 1;
      }
    },
    togglePanel() {
      this.isPanelOpen = !this.isPanelOpen;
    }
  },
  template: `
    <div class="tw-flex tw-flex-col tw-items-center tw-w-full">
      <div class="lg:tw-w-9/12 tw-w-full tw-bg-gray-200 dark:tw-bg-gray-700 tw-rounded tw-my-2 tw-p-2">
        <!-- Header with company name and controls -->
        <div class="tw-flex tw-justify-between tw-items-center">
          <h1
            class="tw-m-0 tw-mx-1 tw-cursor-pointer tw-text-2xl tw-font-bold tw-text-gray-900 dark:tw-text-white"
            @click="togglePanel">
            {{ companyName }}
          </h1>

          <div class="tw-flex tw-items-center tw-space-x-2">
            <!-- Carousel navigation buttons (only show if multiple slides) -->
            <template v-if="hasMultipleSlides">
              <button
                @click="prevSlide"
                class="tw-px-3 tw-py-2 tw-bg-blue-600 tw-text-white tw-rounded tw-hover:bg-blue-700 tw-transition-colors tw-focus:outline-none tw-focus:ring-2 tw-focus:ring-blue-500"
                :disabled="!canGoPrev && reportSlots.length === 0"
                aria-label="Previous slide">
                <i class="bi bi-chevron-left"></i>
              </button>
              <button
                @click="nextSlide"
                class="tw-px-3 tw-py-2 tw-bg-blue-600 tw-text-white tw-rounded tw-hover:bg-blue-700 tw-transition-colors tw-focus:outline-none tw-focus:ring-2 tw-focus:ring-blue-500"
                :disabled="!canGoNext && reportSlots.length === 0"
                aria-label="Next slide">
                <i class="bi bi-chevron-right"></i>
              </button>
            </template>

            <!-- Panel toggle button -->
            <button
              @click="togglePanel"
              class="tw-px-3 tw-py-2 tw-bg-red-600 tw-text-white tw-rounded tw-hover:bg-red-700 tw-transition-colors tw-focus:outline-none tw-focus:ring-2 tw-focus:ring-red-500"
              :aria-expanded="isPanelOpen ? 'true' : 'false'"
              aria-label="Toggle panel">
              <i class="bi bi-nintendo-switch"></i>
            </button>
          </div>
        </div>

        <!-- Carousel slides container -->
        <transition
          enter-active-class="tw-transition tw-ease-out tw-duration-300"
          enter-from-class="tw-opacity-0 tw-transform tw-scale-95"
          enter-to-class="tw-opacity-100 tw-transform tw-scale-100"
          leave-active-class="tw-transition tw-ease-in tw-duration-200"
          leave-from-class="tw-opacity-100 tw-transform tw-scale-100"
          leave-to-class="tw-opacity-0 tw-transform tw-scale-95">
          <div
            v-show="isPanelOpen"
            class="tw-mt-2 tw-w-full">
            <!-- Render current slide -->
            <div
              v-if="reportSlots.length > 0"
              class="tw-w-full"
              v-html="reportSlots[currentSlide]">
            </div>

            <!-- Empty state -->
            <div
              v-else
              class="tw-w-full tw-bg-gray-100 dark:tw-bg-gray-800 tw-rounded tw-p-4 tw-text-center tw-text-gray-600 dark:tw-text-gray-400">
              <p>Нет доступных отчётов для этой компании</p>
            </div>
          </div>
        </transition>

        <!-- Slide indicator (dots) - only show if multiple slides and panel is open -->
        <div
          v-if="hasMultipleSlides && isPanelOpen && reportSlots.length > 0"
          class="tw-flex tw-justify-center tw-space-x-2 tw-mt-3">
          <button
            v-for="(slot, index) in reportSlots"
            :key="index"
            @click="currentSlide = index"
            :class="[
              'tw-w-2 tw-h-2 tw-rounded-full tw-transition-colors tw-focus:outline-none',
              currentSlide === index
                ? 'tw-bg-blue-600'
                : 'tw-bg-gray-400 dark:tw-bg-gray-600 tw-hover:bg-gray-500'
            ]"
            :aria-label="'Go to slide ' + (index + 1)"
            :aria-current="currentSlide === index ? 'true' : 'false'">
          </button>
        </div>
      </div>
    </div>
  `
};

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
  module.exports = ReportsCarousel;
}

// Make available globally
window.ReportsCarousel = ReportsCarousel;
