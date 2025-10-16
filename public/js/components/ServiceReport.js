/**
 * ServiceReport Component
 *
 * Displays service report with:
 * - Date header with upload date and report date
 * - Service statistics table with columns: Название, Сегодня, За месяц
 * - Rows: Доп, Текущий, ТО, Кузовной, Магазин, Всего, Запчасти, Сервис
 * - Link to archive
 *
 * Completely replaces Bootstrap 5 with Tailwind CSS
 */

const ServiceReport = {
  name: 'ServiceReport',
  props: {
    companyId: {
      type: [Number, String],
      required: true
    },
    companyName: {
      type: String,
      required: true
    },
    // Service report data object
    reportData: {
      type: Object,
      required: true
    },
    // Archive URL
    archiveUrl: {
      type: String,
      default: ''
    },
    // Archive list URL
    archiveListUrl: {
      type: String,
      default: ''
    },
    // Report URL (for current report download)
    reportUrl: {
      type: String,
      default: ''
    }
  },
  computed: {
    hasReport() {
      return this.reportData && this.reportData.have;
    },
    updatedAt() {
      return this.reportData.updated_at || 'Отчёта нету.';
    },
    forDate() {
      return this.reportData.for_date || '';
    }
  },
  methods: {
    // Format number with spaces (Russian format)
    formatNumber(num) {
      return parseInt(num || 0).toLocaleString('ru-RU').replace(/,/g, ' ');
    }
  },
  template: `
    <div class="tw-w-full">
      <!-- Header: Отчёт сервис -->
      <div class="tw-bg-gray-100 dark:tw-bg-gray-800 tw-mt-2 tw-rounded tw-p-3 tw-pb-2 tw-mb-2">
        <div class="tw-flex tw-flex-wrap tw-justify-center">
          <h2 class="tw-text-2xl tw-font-bold tw-text-gray-900 dark:tw-text-white">Отчёт сервис</h2>
        </div>
      </div>

      <!-- Dates Section -->
      <div class="tw-bg-gray-100 dark:tw-bg-gray-800 tw-mt-2 tw-rounded tw-p-3 tw-mb-2">
        <div class="tw-flex tw-flex-wrap tw-justify-center tw-items-center tw-gap-2 tw-text-lg">
          <template v-if="reportData.updated_at">
            <h4 class="tw-text-lg tw-font-semibold tw-text-gray-900 dark:tw-text-white">
              Дата загрузки
              <a
                v-if="reportUrl"
                :href="reportUrl"
                class="tw-text-blue-600 dark:tw-text-blue-400 hover:tw-underline">
                отчёта
              </a>
              <span v-else>отчёта</span>:
            </h4>
          </template>

          <h4 class="tw-text-lg tw-font-semibold tw-text-gray-900 dark:tw-text-white">{{ updatedAt }}</h4>

          <template v-if="hasReport">
            <h4 class="tw-text-lg tw-font-semibold tw-text-gray-900 dark:tw-text-white">|</h4>
            <h4 class="tw-text-lg tw-font-semibold tw-text-gray-900 dark:tw-text-white">На дату:</h4>
            <h4 class="tw-text-lg tw-font-semibold tw-text-gray-900 dark:tw-text-white">{{ forDate }}</h4>
          </template>

          <a
            v-if="archiveListUrl"
            :href="archiveListUrl"
            class="tw-text-base tw-text-blue-600 dark:tw-text-blue-400 hover:tw-underline">
            Архив
          </a>
        </div>
      </div>

      <!-- Service Statistics Table -->
      <div class="tw-bg-gray-100 dark:tw-bg-gray-800 tw-rounded tw-p-3 tw-mb-2">
        <div class="tw-overflow-x-auto">
          <table class="tw-min-w-full tw-border-collapse">
            <thead>
              <tr class="tw-bg-gray-200 dark:tw-bg-gray-700">
                <th class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-left tw-text-gray-900 dark:tw-text-white">
                  Навзания
                </th>
                <th class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-left tw-text-gray-900 dark:tw-text-white">
                  Сегодня
                </th>
                <th class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-left tw-text-gray-900 dark:tw-text-white">
                  За месяц
                </th>
              </tr>
            </thead>
            <tbody class="tw-bg-white dark:tw-bg-gray-800">
              <!-- Доп -->
              <tr class="tw-border-b tw-border-gray-200 dark:tw-border-gray-700">
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-gray-900 dark:tw-text-white">
                  Доп
                </td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-right tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap">
                  {{ formatNumber(reportData.dop) }}
                </td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-right tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap">
                  {{ formatNumber(reportData.dop_sum) }}
                </td>
              </tr>

              <!-- Текущий -->
              <tr class="tw-border-b tw-border-gray-200 dark:tw-border-gray-700">
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-gray-900 dark:tw-text-white">
                  Текущий
                </td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-right tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap">
                  {{ formatNumber(reportData.now) }}
                </td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-right tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap">
                  {{ formatNumber(reportData.now_sum) }}
                </td>
              </tr>

              <!-- ТО -->
              <tr class="tw-border-b tw-border-gray-200 dark:tw-border-gray-700">
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-gray-900 dark:tw-text-white">
                  ТО
                </td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-right tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap">
                  {{ formatNumber(reportData.to) }}
                </td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-right tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap">
                  {{ formatNumber(reportData.to_sum) }}
                </td>
              </tr>

              <!-- Кузовной -->
              <tr class="tw-border-b tw-border-gray-200 dark:tw-border-gray-700">
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-gray-900 dark:tw-text-white">
                  Кузовной
                </td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-right tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap">
                  {{ formatNumber(reportData.kuz) }}
                </td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-right tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap">
                  {{ formatNumber(reportData.kuz_sum) }}
                </td>
              </tr>

              <!-- Магазин -->
              <tr class="tw-border-b tw-border-gray-200 dark:tw-border-gray-700">
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-gray-900 dark:tw-text-white">
                  Магазин
                </td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-right tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap">
                  {{ formatNumber(reportData.store) }}
                </td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-right tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap">
                  {{ formatNumber(reportData.store_sum) }}
                </td>
              </tr>

              <!-- Всего (Subtotal) -->
              <tr class="tw-border-b tw-border-gray-200 dark:tw-border-gray-700 tw-font-bold tw-bg-gray-100 dark:tw-bg-gray-700">
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-gray-900 dark:tw-text-white">
                  Всего
                </td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-right tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap">
                  {{ formatNumber(reportData.SUM) }}
                </td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-right tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap">
                  {{ formatNumber(reportData.SUM_sum) }}
                </td>
              </tr>

              <!-- Запчасти (with border spacing) -->
              <tr class="tw-border-b tw-border-gray-200 dark:tw-border-gray-700 tw-border-t-4 tw-border-t-white dark:tw-border-t-gray-900">
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-gray-900 dark:tw-text-white">
                  Запчасти
                </td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-right tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap">
                  {{ formatNumber(reportData.zap) }}
                </td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-right tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap">
                  {{ formatNumber(reportData.zap_sum) }}
                </td>
              </tr>

              <!-- Сервис (with border spacing) -->
              <tr class="tw-border-b tw-border-gray-200 dark:tw-border-gray-700 tw-border-t-4 tw-border-t-white dark:tw-border-t-gray-900">
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-gray-900 dark:tw-text-white">
                  Сервис
                </td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-right tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap">
                  {{ formatNumber(reportData.srv) }}
                </td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-right tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap">
                  {{ formatNumber(reportData.srv_sum) }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  `
};

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
  module.exports = ServiceReport;
}

// Make available globally
window.ServiceReport = ServiceReport;
