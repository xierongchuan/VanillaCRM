/**
 * CashierReport Component
 *
 * Displays cashier report with:
 * - Date header with upload date and report date
 * - Two main tables:
 *   1. Main stats: Link, Оборот (+/-), Сальдо
 *   2. Detailed breakdown: Наличка, Р/С, Пластик, Скидки, Сдано
 * - Button to view expense requests history
 *
 * Completely replaces Bootstrap 5 with Tailwind CSS
 */

const CashierReport = {
  name: 'CashierReport',
  props: {
    companyId: {
      type: [Number, String],
      required: true
    },
    companyName: {
      type: String,
      required: true
    },
    // Cashier report data object
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
    },
    // Expense requests URL
    expenseRequestsUrl: {
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
    },
    hasLink() {
      return this.reportData.link && this.reportData.link.trim() !== '';
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
      <!-- Header: Отчёт Кассир -->
      <div class="tw-bg-gray-100 dark:tw-bg-gray-800 tw-mt-2 tw-rounded tw-p-3 tw-pb-2 tw-mb-2">
        <div class="tw-flex tw-flex-wrap tw-justify-center">
          <h2 class="tw-text-2xl tw-font-bold tw-text-gray-900 dark:tw-text-white">Отчёт Кассир</h2>
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

      <!-- Main Statistics Table -->
      <div class="tw-bg-gray-100 dark:tw-bg-gray-800 tw-rounded tw-p-3 tw-mb-2">
        <div class="tw-overflow-x-auto tw-mb-3">
          <table class="tw-min-w-full tw-border-collapse">
            <thead>
              <tr class="tw-bg-gray-200 dark:tw-bg-gray-700">
                <th class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-left tw-text-gray-900 dark:tw-text-white tw-w-1/3">
                  Параметр
                </th>
                <th class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-left tw-text-gray-900 dark:tw-text-white tw-w-1/3">
                  Значение
                </th>
                <th class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-left tw-text-gray-900 dark:tw-text-white tw-w-1/3">
                  Сумма за месяц
                </th>
              </tr>
            </thead>
            <tbody class="tw-bg-white dark:tw-bg-gray-800">
              <!-- Link Row -->
              <tr class="tw-border-b tw-border-gray-200 dark:tw-border-gray-700">
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-gray-900 dark:tw-text-white">
                  Ссылка на отчёт
                </td>
                <td
                  colspan="2"
                  class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-gray-900 dark:tw-text-white">
                  <a
                    v-if="hasLink"
                    :href="reportData.link"
                    target="_blank"
                    class="tw-text-blue-600 dark:tw-text-blue-400 hover:tw-underline">
                    Открыть отчёт
                  </a>
                  <span v-else>Нет ссылки</span>
                </td>
              </tr>

              <!-- Оборот (Turnover) Row with +/- -->
              <tr class="tw-border-b tw-border-gray-200 dark:tw-border-gray-700">
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-gray-900 dark:tw-text-white">
                  Оборот
                </td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-right">
                  <div class="tw-flex tw-justify-end tw-flex-col md:tw-flex-row tw-items-end tw-gap-1 md:tw-gap-3">
                    <div class="tw-whitespace-nowrap tw-text-green-600 dark:tw-text-green-400">
                      {{ formatNumber(reportData.oborot_plus) }}
                    </div>
                    <div class="tw-whitespace-nowrap tw-hidden md:tw-block tw-text-gray-900 dark:tw-text-white">|</div>
                    <div class="tw-whitespace-nowrap tw-text-red-600 dark:tw-text-red-400">
                      {{ formatNumber(reportData.oborot_minus) }}
                    </div>
                  </div>
                </td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-right">
                  <div class="tw-flex tw-justify-end tw-flex-col md:tw-flex-row tw-items-end tw-gap-1 md:tw-gap-3">
                    <div class="tw-whitespace-nowrap tw-text-green-600 dark:tw-text-green-400">
                      {{ formatNumber(reportData.oborot_plus_sum) }}
                    </div>
                    <div class="tw-whitespace-nowrap tw-hidden md:tw-block tw-text-gray-900 dark:tw-text-white">|</div>
                    <div class="tw-whitespace-nowrap tw-text-red-600 dark:tw-text-red-400">
                      {{ formatNumber(reportData.oborot_minus_sum) }}
                    </div>
                  </div>
                </td>
              </tr>

              <!-- Сальдо (Balance) Row -->
              <tr>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-gray-900 dark:tw-text-white">
                  Сальдо
                </td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-right tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap">
                  {{ formatNumber(reportData.saldo) }}
                </td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-right tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap">
                  {{ formatNumber(reportData.saldo_sum) }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Detailed Breakdown Table -->
        <div class="tw-overflow-x-auto">
          <table class="tw-min-w-full tw-border-collapse">
            <colgroup>
              <col class="tw-w-1/3">
              <col class="tw-w-1/3">
              <col class="tw-w-1/3">
            </colgroup>
            <tbody class="tw-bg-white dark:tw-bg-gray-800">
              <!-- Наличка (Cash) -->
              <tr class="tw-border-b tw-border-gray-200 dark:tw-border-gray-700">
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-gray-900 dark:tw-text-white">
                  Наличка
                </td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-right tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap">
                  {{ formatNumber(reportData.nalichka) }}
                </td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-right tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap">
                  {{ formatNumber(reportData.nalichka_sum) }}
                </td>
              </tr>

              <!-- Р/С (Bank Account) -->
              <tr class="tw-border-b tw-border-gray-200 dark:tw-border-gray-700">
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-gray-900 dark:tw-text-white">
                  Р/С
                </td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-right tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap">
                  {{ formatNumber(reportData.rs) }}
                </td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-right tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap">
                  {{ formatNumber(reportData.rs_sum) }}
                </td>
              </tr>

              <!-- Пластик (Card) -->
              <tr class="tw-border-b tw-border-gray-200 dark:tw-border-gray-700">
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-gray-900 dark:tw-text-white">
                  Пластик
                </td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-right tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap">
                  {{ formatNumber(reportData.plastic) }}
                </td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-right tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap">
                  {{ formatNumber(reportData.plastic_sum) }}
                </td>
              </tr>

              <!-- Скидки (Discounts) -->
              <tr class="tw-border-b tw-border-gray-200 dark:tw-border-gray-700">
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-gray-900 dark:tw-text-white">
                  Скидки
                </td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-right tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap">
                  {{ formatNumber(reportData.skidki) }}
                </td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-right tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap">
                  {{ formatNumber(reportData.skidki_sum) }}
                </td>
              </tr>

              <!-- Сдано (Returned/Given) -->
              <tr>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-gray-900 dark:tw-text-white">
                  Сдано
                </td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-right tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap">
                  {{ formatNumber(reportData.sdano) }}
                </td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-right tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap">
                  {{ formatNumber(reportData.sdano_sum) }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Expense Requests Button -->
      <div v-if="expenseRequestsUrl" class="tw-bg-gray-100 dark:tw-bg-gray-800 tw-mt-2 tw-rounded tw-p-3 tw-mb-2">
        <div class="tw-flex tw-flex-wrap tw-justify-center">
          <a
            :href="expenseRequestsUrl"
            class="tw-px-4 tw-py-2 tw-bg-blue-600 tw-text-white tw-rounded tw-hover:bg-blue-700 tw-transition-colors tw-focus:outline-none tw-focus:ring-2 tw-focus:ring-blue-500">
            История запросов
          </a>
        </div>
      </div>
    </div>
  `
};

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
  module.exports = CashierReport;
}

// Make available globally
window.CashierReport = CashierReport;
