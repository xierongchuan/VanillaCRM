/**
 * DailyStatsReport Component
 *
 * Displays daily statistics report with multiple sections:
 * - Date header with upload date and report date
 * - Today's summary (Договора, Оплата, Лизинг, Банк, Доплата, Всего)
 * - Monthly progress (Факт vs План) with progress bars
 * - Managers table with sales data
 * - Realization table (Bank/Leasing breakdown)
 * - Archive reports history
 * - Company fields/links
 *
 * Completely replaces Bootstrap 5 with Tailwind CSS
 */

const DailyStatsReport = {
  name: 'DailyStatsReport',
  props: {
    companyId: {
      type: [Number, String],
      required: true
    },
    companyName: {
      type: String,
      required: true
    },
    // Report data object containing all statistics
    reportData: {
      type: Object,
      required: true
    },
    // URL to the report file
    reportUrl: {
      type: String,
      default: ''
    },
    // Sales data for managers
    salesData: {
      type: Object,
      default: () => ({})
    },
    // Archive reports array
    archiveReports: {
      type: Array,
      default: () => []
    },
    // Company fields/links
    companyFields: {
      type: Array,
      default: () => []
    },
    // Archive route
    archiveRoute: {
      type: String,
      default: ''
    }
  },
  computed: {
    // Format upload date
    uploadDate() {
      if (!this.reportData.UploadDate) return '';
      const date = new Date(this.reportData.UploadDate);
      return date.toLocaleString('ru-RU', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
      });
    },
    // Format report date
    reportDate() {
      if (!this.reportData['Дата']) return '';
      const date = new Date(this.reportData['Дата']);
      return date.toLocaleDateString('ru-RU', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
      });
    },
    // Calculate manager statistics
    managersStats() {
      const managers = [];
      const salesTotal = Object.values(this.salesData).reduce((sum, val) => sum + parseInt(val || 0), 0);

      let todayTotal = 0;
      let monthTotal = 0;

      for (const [userId, monthSales] of Object.entries(this.salesData)) {
        const todaySales = this.reportData.Sales && this.reportData.Sales[userId]
          ? parseInt(this.reportData.Sales[userId])
          : 0;
        const month = parseInt(monthSales || 0);
        const percentage = salesTotal > 0 ? ((month / salesTotal) * 100).toFixed(1) : 0;

        managers.push({
          userId,
          todaySales,
          monthSales: month,
          percentage
        });

        todayTotal += todaySales;
        monthTotal += month;
      }

      return {
        managers,
        todayTotal,
        monthTotal
      };
    },
    // Calculate realization percentages
    realizationStats() {
      const bankSum = parseFloat(this.reportData['5 Через банк сумма'] || 0);
      const leasingSum = parseFloat(this.reportData['5 Через лизинг сумма'] || 0);
      const totalSum = bankSum + leasingSum;

      const bankCount = parseInt(this.reportData['5 Через банк шт'] || 0);
      const leasingCount = parseInt(this.reportData['5 Через лизинг шт'] || 0);
      const totalCount = bankCount + leasingCount;

      return {
        bank: {
          count: bankCount,
          sum: bankSum,
          countPercent: totalCount > 0 ? ((bankCount / totalCount) * 100).toFixed(1) : 0,
          sumPercent: totalSum > 0 ? ((bankSum / totalSum) * 100).toFixed(2) : 0
        },
        leasing: {
          count: leasingCount,
          sum: leasingSum,
          countPercent: totalCount > 0 ? ((leasingCount / totalCount) * 100).toFixed(1) : 0,
          sumPercent: totalSum > 0 ? ((leasingSum / totalSum) * 100).toFixed(2) : 0
        },
        total: {
          count: parseInt(this.reportData['5 Итог шт'] || 0),
          sum: parseFloat(this.reportData['5 Cумма'] || 0)
        }
      };
    }
  },
  methods: {
    // Format number with spaces (Russian format)
    formatNumber(num) {
      return parseInt(num || 0).toLocaleString('ru-RU').replace(/,/g, ' ');
    },
    // Get progress bar color based on percentage
    getProgressColor(percent) {
      const p = parseFloat(percent || 0);
      if (p >= 100) return 'tw-bg-green-500';
      if (p >= 75) return 'tw-bg-yellow-500';
      if (p >= 50) return 'tw-bg-orange-500';
      return 'tw-bg-red-500';
    }
  },
  template: `
    <div class="tw-w-full">
      <!-- Header: Ежедневная статистика -->
      <div class="tw-bg-gray-100 dark:tw-bg-gray-800 tw-mt-2 tw-rounded tw-p-3 tw-pb-2 tw-mb-2">
        <div class="tw-flex tw-flex-wrap tw-justify-center">
          <h2 class="tw-text-2xl tw-font-bold tw-text-gray-900 dark:tw-text-white">Ежедневная статистика</h2>
        </div>
      </div>

      <!-- Dates Section -->
      <div class="tw-bg-gray-100 dark:tw-bg-gray-800 tw-mt-2 tw-rounded tw-p-3 tw-mb-2">
        <div class="tw-flex tw-flex-wrap tw-justify-center tw-items-center tw-gap-2 tw-text-lg">
          <h4 class="tw-text-lg tw-font-semibold tw-text-gray-900 dark:tw-text-white">
            Дата загрузки
            <a v-if="reportUrl" :href="reportUrl" class="tw-text-blue-600 dark:tw-text-blue-400 hover:tw-underline">отчёта</a>
            <span v-else>отчёта</span>:
          </h4>
          <h4 class="tw-text-lg tw-font-semibold tw-text-gray-900 dark:tw-text-white">{{ uploadDate }}</h4>
          <h4 class="tw-text-lg tw-font-semibold tw-text-gray-900 dark:tw-text-white">|</h4>
          <h4 class="tw-text-lg tw-font-semibold tw-text-gray-900 dark:tw-text-white">На дату:</h4>
          <h4 class="tw-text-lg tw-font-semibold tw-text-gray-900 dark:tw-text-white">{{ reportDate }}</h4>
        </div>
      </div>

      <!-- Today's Summary -->
      <div class="tw-bg-gray-100 dark:tw-bg-gray-800 tw-rounded tw-p-3 tw-mb-2">
        <div class="tw-flex tw-flex-col tw-space-y-1">
          <!-- Договора -->
          <div class="md:tw-w-2/3 tw-w-full tw-mx-auto tw-my-1 tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-rounded tw-p-2 tw-flex tw-justify-between tw-text-lg tw-bg-white dark:tw-bg-gray-700">
            <span class="tw-font-bold tw-text-gray-900 dark:tw-text-white">Договора:</span>
            <span class="tw-font-bold tw-text-gray-900 dark:tw-text-white">{{ reportData['Договора'] }}</span>
          </div>

          <!-- Оплата -->
          <div class="md:tw-w-2/3 tw-w-full tw-mx-auto tw-my-1 tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-rounded tw-p-2 tw-flex tw-justify-between tw-text-lg tw-bg-white dark:tw-bg-gray-700">
            <span class="tw-font-bold tw-text-gray-900 dark:tw-text-white">Оплата:</span>
            <span class="tw-font-bold tw-text-gray-900 dark:tw-text-white">{{ reportData['Оплата Кол-во'] }}</span>
          </div>

          <!-- Лизинг -->
          <div class="md:tw-w-2/3 tw-w-full tw-mx-auto tw-my-1 tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-rounded tw-p-2 tw-flex tw-justify-between tw-text-lg tw-bg-white dark:tw-bg-gray-700">
            <span class="tw-font-bold tw-text-gray-900 dark:tw-text-white">Лизинг:</span>
            <span class="tw-font-bold tw-text-gray-900 dark:tw-text-white">{{ formatNumber(reportData['Лизинг']) }}</span>
          </div>

          <div class="tw-my-3"></div>

          <!-- Банк -->
          <div class="md:tw-w-2/3 tw-w-full tw-mx-auto tw-my-1 tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-rounded tw-p-2 tw-flex tw-justify-between tw-text-lg tw-bg-white dark:tw-bg-gray-700">
            <span class="tw-font-bold tw-text-gray-900 dark:tw-text-white">Банк:</span>
            <span class="tw-font-bold tw-text-gray-900 dark:tw-text-white">{{ formatNumber(reportData['Всего']) }}</span>
          </div>

          <!-- Доплата -->
          <div class="md:tw-w-2/3 tw-w-full tw-mx-auto tw-my-1 tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-rounded tw-p-2 tw-flex tw-justify-between tw-text-lg tw-bg-white dark:tw-bg-gray-700">
            <span class="tw-font-bold tw-text-gray-900 dark:tw-text-white">Доплата:</span>
            <span class="tw-font-bold tw-text-gray-900 dark:tw-text-white">{{ formatNumber(reportData['Доплата']) }}</span>
          </div>

          <!-- Всего -->
          <div class="md:tw-w-2/3 tw-w-full tw-mx-auto tw-my-1 tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-rounded tw-p-2 tw-flex tw-justify-between tw-text-lg tw-bg-white dark:tw-bg-gray-700">
            <span class="tw-font-bold tw-text-gray-900 dark:tw-text-white">Всего:</span>
            <span class="tw-font-bold tw-text-gray-900 dark:tw-text-white">{{ formatNumber(reportData['Оплата Сумм']) }}</span>
          </div>
        </div>
      </div>

      <!-- Monthly Progress: Этот месяц -->
      <div class="tw-bg-gray-100 dark:tw-bg-gray-800 tw-rounded tw-p-3 tw-mb-2">
        <div class="tw-flex tw-flex-wrap tw-justify-center tw-mb-4">
          <h2 class="tw-text-2xl tw-font-bold tw-text-gray-900 dark:tw-text-white tw-mb-0">Этот месяц</h2>
        </div>

        <div class="tw-space-y-2">
          <!-- Header Row (Desktop only) -->
          <div class="md:tw-w-3/4 tw-mx-auto tw-flex tw-justify-between tw-text-lg tw-hidden md:tw-flex">
            <span class="tw-w-1/3 tw-p-2 tw-text-center tw-font-semibold tw-text-gray-900 dark:tw-text-white">Факт</span>
            <span class="tw-w-1/3"></span>
            <span class="tw-w-1/3 tw-p-2 tw-text-right tw-font-semibold tw-text-gray-900 dark:tw-text-white">План</span>
          </div>

          <!-- Quantity Progress Bar -->
          <div class="md:tw-w-3/4 tw-mx-auto tw-flex tw-flex-col md:tw-flex-row tw-justify-between tw-gap-2 tw-text-lg">
            <span class="md:tw-w-5/12 tw-p-2 tw-border tw-border-green-500 tw-rounded tw-flex tw-justify-between tw-bg-white dark:tw-bg-gray-700">
              <b class="md:tw-hidden tw-text-gray-900 dark:tw-text-white">Факт:</b>
              <span class="tw-font-bold tw-text-gray-900 dark:tw-text-white">{{ reportData['Факт Кол-во'] }}</span>
            </span>

            <span class="md:tw-w-2/12 tw-p-2 tw-border tw-border-red-500 tw-rounded tw-text-center tw-text-base tw-relative tw-overflow-hidden tw-bg-white dark:tw-bg-gray-700">
              <div
                class="tw-absolute tw-inset-0 tw-opacity-20"
                :class="getProgressColor(reportData['% от кол-во'])"
                :style="{ width: Math.min(parseFloat(reportData['% от кол-во'] || 0), 100) + '%' }">
              </div>
              <b class="tw-relative tw-z-10 tw-text-gray-900 dark:tw-text-white">{{ reportData['% от кол-во'] }} %</b>
            </span>

            <span class="md:tw-w-5/12 tw-p-2 tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-rounded tw-flex tw-justify-between tw-bg-white dark:tw-bg-gray-700">
              <b class="md:tw-hidden tw-text-gray-900 dark:tw-text-white">План:</b>
              <span class="tw-font-bold tw-text-gray-900 dark:tw-text-white tw-ml-auto">{{ reportData['План Кол-во'] }}</span>
            </span>
          </div>

          <div class="tw-my-3 md:tw-hidden"></div>

          <!-- Sum Progress Bar -->
          <div class="md:tw-w-3/4 tw-mx-auto tw-flex tw-flex-col md:tw-flex-row tw-justify-between tw-gap-2 tw-text-lg">
            <span class="md:tw-w-5/12 tw-p-2 tw-border tw-border-green-500 tw-rounded tw-flex tw-justify-between tw-bg-white dark:tw-bg-gray-700">
              <b class="md:tw-hidden tw-text-gray-900 dark:tw-text-white">Факт:</b>
              <span class="tw-font-bold tw-text-gray-900 dark:tw-text-white">{{ formatNumber(reportData['Факт Сумм']) }}</span>
            </span>

            <span class="md:tw-w-2/12 tw-p-2 tw-border tw-border-red-500 tw-rounded tw-text-center tw-text-base tw-relative tw-overflow-hidden tw-bg-white dark:tw-bg-gray-700">
              <div
                class="tw-absolute tw-inset-0 tw-opacity-20"
                :class="getProgressColor(reportData['% от сумм'])"
                :style="{ width: Math.min(parseFloat(reportData['% от сумм'] || 0), 100) + '%' }">
              </div>
              <b class="tw-relative tw-z-10 tw-text-gray-900 dark:tw-text-white">{{ reportData['% от сумм'] }} %</b>
            </span>

            <span class="md:tw-w-5/12 tw-p-2 tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-rounded tw-flex tw-justify-between tw-bg-white dark:tw-bg-gray-700">
              <b class="md:tw-hidden tw-text-gray-900 dark:tw-text-white">План:</b>
              <span class="tw-font-bold tw-text-gray-900 dark:tw-text-white tw-ml-auto">{{ formatNumber(reportData['План Сумм']) }}</span>
            </span>
          </div>

          <!-- Additional stats -->
          <div class="md:tw-w-1/2 tw-mx-auto tw-my-1 tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-rounded tw-p-2 tw-flex tw-justify-between tw-text-base tw-bg-white dark:tw-bg-gray-700">
            <span class="tw-text-gray-900 dark:tw-text-white">Договора</span>
            <span class="tw-font-bold tw-text-gray-900 dark:tw-text-white">{{ reportData['2 Договора'] }} шт</span>
          </div>

          <div class="md:tw-w-1/2 tw-mx-auto tw-my-1 tw-border tw-border-yellow-500 tw-rounded tw-p-2 tw-flex tw-justify-between tw-text-base tw-bg-white dark:tw-bg-gray-700">
            <span class="tw-text-gray-900 dark:tw-text-white">Конверсия (CV)</span>
            <span class="tw-font-bold tw-text-gray-900 dark:tw-text-white">{{ reportData['2 Конверсия'] }} %</span>
          </div>
        </div>
      </div>

      <!-- Payment Details -->
      <div class="tw-bg-gray-100 dark:tw-bg-gray-800 tw-rounded tw-p-3 tw-mb-2">
        <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-2">
          <div class="md:tw-col-span-1 tw-my-1 tw-mx-auto tw-w-full md:tw-w-5/6 tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-rounded tw-p-2 tw-flex tw-justify-between tw-text-base tw-bg-white dark:tw-bg-gray-700">
            <span class="tw-text-gray-900 dark:tw-text-white">Банк</span>
            <span class="tw-font-bold tw-text-gray-900 dark:tw-text-white">{{ formatNumber(reportData['3 Оплата']) }} сум</span>
          </div>

          <div class="md:tw-col-span-1 tw-my-1 tw-mx-auto tw-w-full md:tw-w-5/6 tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-rounded tw-p-2 tw-flex tw-justify-between tw-text-base tw-bg-white dark:tw-bg-gray-700">
            <span class="tw-text-gray-900 dark:tw-text-white">Лизинг</span>
            <span class="tw-font-bold tw-text-gray-900 dark:tw-text-white">{{ formatNumber(reportData['3 Доплата']) }} сум</span>
          </div>

          <div class="md:tw-col-span-1 tw-my-1 tw-mx-auto tw-w-full md:tw-w-5/6 tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-rounded tw-p-2 tw-flex tw-justify-between tw-text-base tw-bg-white dark:tw-bg-gray-700">
            <span class="tw-text-gray-900 dark:tw-text-white">Доплата</span>
            <span class="tw-font-bold tw-text-gray-900 dark:tw-text-white">{{ formatNumber(reportData['3 Лизинг']) }} сум</span>
          </div>

          <div class="md:tw-col-span-1 tw-my-1 tw-mx-auto tw-w-full md:tw-w-5/6 tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-rounded tw-p-2 tw-flex tw-justify-between tw-text-base tw-bg-white dark:tw-bg-gray-700">
            <span class="tw-text-gray-900 dark:tw-text-white">Остаток</span>
            <span class="tw-font-bold tw-text-gray-900 dark:tw-text-white">{{ formatNumber(reportData['3 Остаток']) }} сум</span>
          </div>
        </div>
      </div>

      <!-- Managers Table -->
      <div class="tw-bg-gray-100 dark:tw-bg-gray-800 tw-rounded tw-p-3 tw-mb-2">
        <h2 class="tw-text-2xl tw-font-bold tw-text-gray-900 dark:tw-text-white tw-mb-3">Менеджеры</h2>

        <div class="tw-overflow-x-auto">
          <table class="tw-min-w-full tw-border-collapse">
            <thead>
              <tr class="tw-bg-gray-200 dark:tw-bg-gray-700">
                <th class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-left tw-text-gray-900 dark:tw-text-white">#</th>
                <th class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-left tw-text-gray-900 dark:tw-text-white">Имя</th>
                <th class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-left tw-text-gray-900 dark:tw-text-white">Сегодня</th>
                <th class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-left tw-text-gray-900 dark:tw-text-white">Мес</th>
                <th class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-left tw-text-gray-900 dark:tw-text-white">%</th>
              </tr>
            </thead>
            <tbody class="tw-bg-white dark:tw-bg-gray-800">
              <tr
                v-for="(manager, index) in managersStats.managers"
                :key="manager.userId"
                class="tw-border-b tw-border-gray-200 dark:tw-border-gray-700">
                <th class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-gray-900 dark:tw-text-white">{{ index + 1 }}</th>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-gray-900 dark:tw-text-white">
                  <slot :name="'manager-name-' + manager.userId" :manager="manager">
                    Менеджер {{ manager.userId }}
                  </slot>
                </td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap">
                  {{ manager.todaySales }} шт
                </td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap">
                  {{ manager.monthSales }} шт
                </td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap">
                  {{ manager.percentage }} %
                </td>
              </tr>

              <!-- Total Row -->
              <tr class="tw-bg-gray-100 dark:tw-bg-gray-700 tw-font-bold">
                <th class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-gray-900 dark:tw-text-white">#</th>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-gray-900 dark:tw-text-white">Всего</td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap">
                  {{ managersStats.todayTotal }} шт
                </td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap">
                  {{ managersStats.monthTotal }} шт
                </td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-gray-900 dark:tw-text-white">%</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Realization Table -->
      <div class="tw-bg-gray-100 dark:tw-bg-gray-800 tw-rounded tw-p-3 tw-mb-2">
        <h2 class="tw-text-2xl tw-font-bold tw-text-gray-900 dark:tw-text-white tw-mb-3">Реализация</h2>

        <div class="tw-overflow-x-auto">
          <table class="tw-min-w-full tw-border-collapse">
            <tbody class="tw-bg-white dark:tw-bg-gray-800">
              <tr class="tw-border-b tw-border-gray-200 dark:tw-border-gray-700">
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-gray-900 dark:tw-text-white">DKD/Банк</td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-right tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap">
                  {{ realizationStats.bank.count }}
                </td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-right tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap tw-w-24">
                  {{ realizationStats.bank.countPercent }} %
                </td>
              </tr>
              <tr class="tw-border-b tw-border-gray-200 dark:tw-border-gray-700">
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-gray-900 dark:tw-text-white">Через банк (сумма)</td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-right tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap">
                  {{ formatNumber(realizationStats.bank.sum) }}
                </td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-right tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap">
                  {{ realizationStats.bank.sumPercent }} %
                </td>
              </tr>
              <tr class="tw-border-b tw-border-gray-200 dark:tw-border-gray-700">
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-gray-900 dark:tw-text-white">SKD/Лизинг</td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-right tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap">
                  {{ realizationStats.leasing.count }}
                </td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-right tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap">
                  {{ realizationStats.leasing.countPercent }} %
                </td>
              </tr>
              <tr class="tw-border-b tw-border-gray-200 dark:tw-border-gray-700">
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-gray-900 dark:tw-text-white">Через лизинг (сумма)</td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-right tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap">
                  {{ formatNumber(realizationStats.leasing.sum) }}
                </td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-right tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap">
                  {{ realizationStats.leasing.sumPercent }} %
                </td>
              </tr>
              <tr class="tw-border-b tw-border-gray-200 dark:tw-border-gray-700">
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-gray-900 dark:tw-text-white">Итог (шт)</td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-right tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap">
                  {{ realizationStats.total.count }}
                </td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2"></td>
              </tr>
              <tr>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-gray-900 dark:tw-text-white">Итог (сумма)</td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2 tw-text-right tw-text-gray-900 dark:tw-text-white tw-whitespace-nowrap">
                  {{ formatNumber(realizationStats.total.sum) }}
                </td>
                <td class="tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-px-4 tw-py-2"></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Archive Reports -->
      <div class="tw-bg-gray-100 dark:tw-bg-gray-800 tw-rounded tw-p-3 tw-mb-2">
        <div class="tw-flex tw-justify-between tw-items-center tw-mb-2">
          <h2 class="tw-text-2xl tw-font-bold tw-text-gray-900 dark:tw-text-white">Прошлые месяцы</h2>
          <a
            v-if="archiveRoute"
            :href="archiveRoute"
            class="tw-text-base tw-text-blue-600 dark:tw-text-blue-400 hover:tw-underline">
            Архив
          </a>
        </div>

        <!-- Header Row -->
        <div class="tw-my-1 tw-mx-auto tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-rounded tw-py-2 tw-grid tw-grid-cols-12 tw-gap-2 tw-text-lg tw-bg-gray-200 dark:tw-bg-gray-700">
          <div class="tw-col-span-3 tw-px-2 tw-font-semibold tw-text-gray-900 dark:tw-text-white">Месяц</div>
          <div class="tw-col-span-4 tw-text-right tw-px-2 tw-font-semibold tw-text-gray-900 dark:tw-text-white">Сум</div>
          <div class="tw-col-span-2 tw-text-right tw-px-2 tw-font-semibold tw-text-gray-900 dark:tw-text-white">Шт</div>
          <div class="tw-col-span-3 tw-text-right tw-px-2 tw-font-semibold tw-text-gray-900 dark:tw-text-white">Факт</div>
        </div>

        <!-- Archive Reports List -->
        <template v-if="archiveReports.length > 0">
          <div
            v-for="report in archiveReports"
            :key="report.month"
            class="tw-my-1 tw-mx-auto tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-rounded tw-py-2 tw-grid tw-grid-cols-12 tw-gap-2 tw-text-lg tw-bg-white dark:tw-bg-gray-800">
            <div class="tw-col-span-3 tw-px-2 tw-text-gray-900 dark:tw-text-white">
              <a
                v-if="report.url"
                :href="report.url"
                class="tw-text-blue-600 dark:tw-text-blue-400 hover:tw-underline">
                {{ report.month }}
              </a>
              <span v-else>{{ report.month }}</span>
            </div>
            <div class="tw-col-span-4 tw-text-right tw-px-2 tw-text-gray-900 dark:tw-text-white">
              {{ formatNumber(report.sum) }}
            </div>
            <div class="tw-col-span-2 tw-text-right tw-px-2 tw-text-gray-900 dark:tw-text-white">
              {{ report.quantity }}
            </div>
            <div class="tw-col-span-3 tw-text-right tw-px-2 tw-text-gray-900 dark:tw-text-white">
              {{ report.fact }}
            </div>
          </div>
        </template>

        <!-- No Reports -->
        <div
          v-else
          class="tw-my-1 tw-mx-auto tw-border tw-border-red-500 tw-rounded tw-py-2 tw-text-lg tw-bg-white dark:tw-bg-gray-800">
          <div class="tw-flex tw-justify-center tw-text-gray-900 dark:tw-text-white">
            Отсутствует отчёт
          </div>
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
  module.exports = DailyStatsReport;
}

// Make available globally
window.DailyStatsReport = DailyStatsReport;
