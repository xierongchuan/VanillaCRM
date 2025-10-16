/**
 * CafeReport Component
 *
 * Displays cafe report with:
 * - Date header with upload date and report date
 * - Three main tables:
 *   1. Today statistics (Выручка, Расходы, Остаток) with Нал/Без Нал columns
 *   2. Monthly statistics (same structure)
 *   3. Safe (Сейф) table
 * - Company fields/links section
 *
 * Completely replaces Bootstrap 5 with Tailwind CSS
 */

const CafeReport = {
  name: 'CafeReport',
  props: {
    companyId: {
      type: [Number, String],
      required: true
    },
    companyName: {
      type: String,
      required: true
    },
    // Cafe report data object
    reportData: {
      type: Object,
      required: true
    },
    // Company fields/links
    companyFields: {
      type: Array,
      default: () => []
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
    // Format number (no formatting needed as per original code)
    formatValue(val) {
      return val || 0;
    }
  },
  template: `
    <div class="tw-w-full">
      <!-- Header: Отчёт кафе -->
      <div class="tw-bg-gray-100 dark:tw-bg-gray-800 tw-mt-2 tw-rounded tw-p-3 tw-pb-2 tw-mb-2">
        <div class="tw-flex tw-flex-wrap tw-justify-center">
          <h2 class="tw-text-2xl tw-font-bold tw-text-gray-900 dark:tw-text-white">Отчёт кафе</h2>
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

      <!-- Statistics Tables Container -->
      <div class="tw-bg-gray-100 dark:tw-bg-gray-800 tw-rounded tw-p-3 tw-mb-2 tw-overflow-x-auto">
        <!-- Table 1: Today Statistics -->
        <div class="tw-mb-4">
          <table class="tw-min-w-full tw-border-collapse">
            <thead>
              <tr class="tw-bg-gray-200 dark:tw-bg-gray-700">
                <th
                  rowspan="2"
                  class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-center tw-text-gray-900 dark:tw-text-white tw-w-40">
                </th>
                <th
                  colspan="3"
                  class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-center tw-text-gray-900 dark:tw-text-white">
                  Сегодня
                </th>
              </tr>
              <tr class="tw-bg-gray-200 dark:tw-bg-gray-700">
                <th class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-gray-900 dark:tw-text-white">
                  Нал
                </th>
                <th class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-gray-900 dark:tw-text-white">
                  Без Нал
                </th>
                <th class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-gray-900 dark:tw-text-white tw-border-l-4 tw-border-l-white dark:tw-border-l-gray-900">
                  Всего
                </th>
              </tr>
            </thead>
            <tbody class="tw-bg-white dark:tw-bg-gray-800">
              <!-- Выручка (Profit) - Green -->
              <tr class="tw-border-b tw-border-gray-200 dark:tw-border-gray-700">
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-gray-900 dark:tw-text-white tw-w-40">
                  <span class="tw-hidden md:tw-inline">Выручка</span>
                  <span class="tw-inline md:tw-hidden">Вир</span>
                </td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-right tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap tw-bg-green-100 dark:tw-bg-green-900/20">
                  {{ formatValue(reportData.profit_nal) }}
                </td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-right tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap tw-bg-green-100 dark:tw-bg-green-900/20">
                  {{ formatValue(reportData.profit_bez_nal) }}
                </td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-right tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap tw-bg-green-100 dark:tw-bg-green-900/20 tw-border-l-4 tw-border-l-white dark:tw-border-l-gray-900">
                  {{ formatValue(reportData.profit_SUM) }}
                </td>
              </tr>

              <!-- Расходы (Waste) - Red -->
              <tr class="tw-border-b tw-border-gray-200 dark:tw-border-gray-700">
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-gray-900 dark:tw-text-white tw-w-40">
                  <span class="tw-hidden md:tw-inline">Расходы</span>
                  <span class="tw-inline md:tw-hidden">Рас</span>
                </td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-right tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap tw-bg-red-100 dark:tw-bg-red-900/20">
                  {{ formatValue(reportData.waste_nal) }}
                </td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-right tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap tw-bg-red-100 dark:tw-bg-red-900/20">
                  {{ formatValue(reportData.waste_bez_nal) }}
                </td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-right tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap tw-bg-red-100 dark:tw-bg-red-900/20 tw-border-l-4 tw-border-l-white dark:tw-border-l-gray-900">
                  {{ formatValue(reportData.waste_SUM) }}
                </td>
              </tr>

              <!-- Остаток (Remains) -->
              <tr>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-gray-900 dark:tw-text-white tw-w-40">
                  <span class="tw-hidden md:tw-inline">Остаток</span>
                  <span class="tw-inline md:tw-hidden">Ост</span>
                </td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-right tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap">
                  {{ formatValue(reportData.remains_nal) }}
                </td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-right tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap">
                  {{ formatValue(reportData.remains_bez_nal) }}
                </td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-right tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap tw-border-l-4 tw-border-l-white dark:tw-border-l-gray-900">
                  {{ formatValue(reportData.remains_SUM) }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Table 2: Monthly Statistics -->
        <div class="tw-mb-4">
          <table class="tw-min-w-full tw-border-collapse">
            <thead>
              <tr class="tw-bg-gray-200 dark:tw-bg-gray-700">
                <th
                  rowspan="2"
                  class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-center tw-text-gray-900 dark:tw-text-white tw-w-40">
                </th>
                <th
                  colspan="3"
                  class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-center tw-text-gray-900 dark:tw-text-white">
                  За Месяц
                </th>
              </tr>
              <tr class="tw-bg-gray-200 dark:tw-bg-gray-700">
                <th class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-gray-900 dark:tw-text-white">
                  Нал
                </th>
                <th class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-gray-900 dark:tw-text-white">
                  Без Нал
                </th>
                <th class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-gray-900 dark:tw-text-white tw-border-l-4 tw-border-l-white dark:tw-border-l-gray-900">
                  Всего
                </th>
              </tr>
            </thead>
            <tbody class="tw-bg-white dark:tw-bg-gray-800">
              <!-- Выручка (Profit) - Green -->
              <tr class="tw-border-b tw-border-gray-200 dark:tw-border-gray-700">
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-gray-900 dark:tw-text-white tw-w-40">
                  <span class="tw-hidden md:tw-inline">Выручка</span>
                  <span class="tw-inline md:tw-hidden">Вир</span>
                </td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-right tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap tw-bg-green-100 dark:tw-bg-green-900/20">
                  {{ formatValue(reportData.profit_nal_sum) }}
                </td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-right tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap tw-bg-green-100 dark:tw-bg-green-900/20">
                  {{ formatValue(reportData.profit_bez_nal_sum) }}
                </td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-right tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap tw-bg-green-100 dark:tw-bg-green-900/20 tw-border-l-4 tw-border-l-white dark:tw-border-l-gray-900">
                  {{ formatValue(reportData.profit_SUM_sum) }}
                </td>
              </tr>

              <!-- Расходы (Waste) - Red -->
              <tr class="tw-border-b tw-border-gray-200 dark:tw-border-gray-700">
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-gray-900 dark:tw-text-white tw-w-40">
                  <span class="tw-hidden md:tw-inline">Расходы</span>
                  <span class="tw-inline md:tw-hidden">Рас</span>
                </td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-right tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap tw-bg-red-100 dark:tw-bg-red-900/20">
                  {{ formatValue(reportData.waste_nal_sum) }}
                </td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-right tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap tw-bg-red-100 dark:tw-bg-red-900/20">
                  {{ formatValue(reportData.waste_bez_nal_sum) }}
                </td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-right tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap tw-bg-red-100 dark:tw-bg-red-900/20 tw-border-l-4 tw-border-l-white dark:tw-border-l-gray-900">
                  {{ formatValue(reportData.waste_SUM_sum) }}
                </td>
              </tr>

              <!-- Остаток (Remains) -->
              <tr>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-gray-900 dark:tw-text-white tw-w-40">
                  <span class="tw-hidden md:tw-inline">Остаток</span>
                  <span class="tw-inline md:tw-hidden">Ост</span>
                </td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-right tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap">
                  {{ formatValue(reportData.remains_nal_sum) }}
                </td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-right tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap">
                  {{ formatValue(reportData.remains_bez_nal_sum) }}
                </td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-right tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap tw-border-l-4 tw-border-l-white dark:tw-border-l-gray-900">
                  {{ formatValue(reportData.remains_SUM_sum) }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Table 3: Safe (Сейф) -->
        <div class="tw-mb-2">
          <table class="tw-min-w-full tw-border-collapse">
            <thead>
              <tr class="tw-bg-gray-200 dark:tw-bg-gray-700">
                <th class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-gray-900 dark:tw-text-white"></th>
                <th class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-gray-900 dark:tw-text-white">Нал</th>
                <th class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-gray-900 dark:tw-text-white">Без Нал</th>
                <th class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-gray-900 dark:tw-text-white tw-border-l-4 tw-border-l-white dark:tw-border-l-gray-900">Всего</th>
              </tr>
            </thead>
            <tbody class="tw-bg-white dark:tw-bg-gray-800">
              <tr>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-gray-900 dark:tw-text-white tw-w-40">
                  <span class="tw-hidden md:tw-inline">Сейф</span>
                  <span class="tw-inline md:tw-hidden">Сф</span>
                </td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-right tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap">
                  {{ formatValue(reportData.safe_nal) }}
                </td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-right tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap">
                  {{ formatValue(reportData.safe_bez_nal) }}
                </td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-right tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap tw-border-l-4 tw-border-l-white dark:tw-border-l-gray-900">
                  {{ formatValue(reportData.safe_SUM) }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Company Fields/Links -->
      <div v-if="companyFields.length > 0" class="tw-bg-gray-100 dark:tw-bg-gray-800 tw-rounded tw-p-3 tw-mb-2">
        <div class="tw-mb-2">
          <h2 class="tw-text-2xl tw-font-bold tw-text-gray-900 dark:tw-text-white">Ссылки</h2>
        </div>

        <div
          v-for="field in companyFields"
          :key="field.id"
          class="tw-my-1 tw-mx-auto tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-rounded tw-py-2 tw-grid tw-grid-cols-2 tw-gap-2 tw-text-lg tw-bg-white dark:tw-bg-gray-800">
          <div class="tw-col-span-1 tw-px-2 tw-text-gray-900 dark:tw-text-white">
            {{ field.title }}
          </div>
          <div class="tw-col-span-1 tw-text-right tw-px-2">
            <a
              :href="field.link"
              class="tw-text-blue-600 dark:tw-text-blue-400 hover:tw-underline"
              target="_blank">
              Открыть
            </a>
          </div>
        </div>
      </div>
    </div>
  `
};

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
  module.exports = CafeReport;
}

// Make available globally
window.CafeReport = CafeReport;
