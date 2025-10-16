/**
 * StatsDashboard Component
 *
 * Vue 3 component for displaying statistics dashboard with charts
 * Replaces Bootstrap collapse panels with Vue-based collapsible sections
 * Integrates Chart.js for line charts showing sales, reports, payments, etc.
 *
 * Stage 10: Statistics Page - Stats Dashboard
 *
 * Features:
 * - Two collapsible sections (Manager Stats, Report Stats)
 * - Multiple Chart.js line charts per company
 * - Automatic chart initialization after mount
 * - Responsive design with Tailwind CSS
 * - Dark mode support
 * - Color generation for chart lines
 *
 * Props:
 * @prop {Object} salesData - Manager sales statistics { companyId: { date: { managerName: value } } }
 * @prop {Object} growthData - Report growth statistics { companyId: { date: { metric: value } } }
 *
 * Usage:
 * <stats-dashboard
 *   :sales-data="{{ json_encode($sales) }}"
 *   :growth-data="{{ json_encode($growthStatistics) }}"
 * ></stats-dashboard>
 */

window.StatsDashboard = {
  name: 'StatsDashboard',

  props: {
    salesData: {
      type: Object,
      default: () => ({})
    },
    growthData: {
      type: Object,
      default: () => ({})
    },
    companyNames: {
      type: Object,
      default: () => ({})
    }
  },

  data() {
    return {
      // Collapsible section states
      salesCollapsed: true,
      reportsCollapsed: true,

      // Chart instances (to destroy on unmount)
      chartInstances: [],

      // Loading state
      isLoading: false
    };
  },

  computed: {
    /**
     * Check if sales data exists
     */
    hasSalesData() {
      return Object.keys(this.salesData).length > 0;
    },

    /**
     * Check if growth data exists
     */
    hasGrowthData() {
      return Object.keys(this.growthData).length > 0;
    },

    /**
     * Get company names for sales data
     */
    salesCompanies() {
      return Object.keys(this.salesData);
    },

    /**
     * Get company names for growth data
     */
    growthCompanies() {
      return Object.keys(this.growthData);
    }
  },

  methods: {
    /**
     * Toggle sales section collapse
     */
    toggleSales() {
      this.salesCollapsed = !this.salesCollapsed;

      // Initialize charts when opening section
      if (!this.salesCollapsed && this.hasSalesData) {
        this.$nextTick(() => {
          this.initializeSalesCharts();
        });
      }
    },

    /**
     * Toggle reports section collapse
     */
    toggleReports() {
      this.reportsCollapsed = !this.reportsCollapsed;

      // Initialize charts when opening section
      if (!this.reportsCollapsed && this.hasGrowthData) {
        this.$nextTick(() => {
          this.initializeReportsCharts();
        });
      }
    },

    /**
     * Generate random color for chart lines
     */
    getRandomColor() {
      const letters = '0123456789ABCDEF';
      let color = '#';
      for (let i = 0; i < 6; i++) {
        color += letters[Math.floor(Math.random() * 16)];
      }
      return color;
    },

    /**
     * Get company name by ID from companyNames prop
     */
    getCompanyName(companyId) {
      return this.companyNames[companyId] || `Company ${companyId}`;
    },

    /**
     * Initialize all sales charts
     */
    initializeSalesCharts() {
      this.salesCompanies.forEach(companyId => {
        this.initializeManagerChart(companyId);
      });
    },

    /**
     * Initialize manager statistics chart for a company
     */
    initializeManagerChart(companyId) {
      const canvasId = `managerStatsChart-${companyId}`;
      const canvas = document.getElementById(canvasId);

      if (!canvas) {
        console.warn(`Canvas ${canvasId} not found`);
        return;
      }

      // Destroy existing chart if it exists to avoid "Canvas is already in use" error
      const existingChart = Chart.getChart(canvas);
      if (existingChart) {
        existingChart.destroy();
      }

      const data = this.salesData[companyId];
      const labels = Object.keys(data).sort(); // Dates sorted ascending

      // Collect all manager names across all dates
      const managerNamesSet = new Set();
      labels.forEach(date => {
        Object.keys(data[date]).forEach(managerName => {
          managerNamesSet.add(managerName);
        });
      });
      const managerNames = Array.from(managerNamesSet);

      // Create datasets for each manager
      const datasets = managerNames.map(managerName => {
        return {
          label: managerName,
          data: labels.map(date => data[date][managerName] || 0),
          fill: false,
          borderColor: this.getRandomColor(),
          tension: 0.1
        };
      });

      const ctx = canvas.getContext('2d');
      const chart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: labels,
          datasets: datasets
        },
        options: {
          responsive: true,
          maintainAspectRatio: true,
          scales: {
            x: {
              title: {
                display: true,
                text: 'Дата'
              }
            },
            y: {
              title: {
                display: true,
                text: 'Продажи'
              }
            }
          }
        }
      });

      // Store chart instance for cleanup
      this.chartInstances.push(chart);
    },

    /**
     * Initialize all reports charts
     */
    initializeReportsCharts() {
      this.growthCompanies.forEach(companyId => {
        this.initializeReportChartsForCompany(companyId);
      });
    },

    /**
     * Initialize all 4 report charts for a company
     */
    initializeReportChartsForCompany(companyId) {
      const data = this.growthData[companyId];
      const labels = Object.keys(data).sort();

      // 1. Daily quantity (contracts, payments)
      this.createChart(
        `repsEveryDayPcStatsChart-${companyId}`,
        labels,
        data,
        {
          'contracts': 'Договора',
          'payment_quantity': 'Оплата'
        },
        'Дата',
        'Шт'
      );

      // 2. Daily sum (total, additional_payment, payment_sum, leasing)
      this.createChart(
        `repsEveryDaySumStatsChart-${companyId}`,
        labels,
        data,
        {
          'total': 'Банк',
          'additional_payment': 'Доплата',
          'payment_sum': 'Всего',
          'leasing': 'Лизинг'
        },
        'Дата',
        'Сумм'
      );

      // 3. Monthly quantity (actual, plan)
      this.createChart(
        `repsOfMonthPcStatsChart-${companyId}`,
        labels,
        data,
        {
          'actual_quantity': 'Факт',
          'plan_quantity': 'План'
        },
        'Дата',
        'Шт'
      );

      // 4. Monthly sum (actual_sum, plan_sum, payment_3, etc.)
      this.createChart(
        `repsOfMonthSumStatsChart-${companyId}`,
        labels,
        data,
        {
          'actual_sum': 'Факт',
          'plan_sum': 'План',
          'payment_3': 'Банк',
          'additional_payment_3': 'Лизинг',
          'leasing_3': 'Доплата',
          'balance_3': 'Остаток'
        },
        'Дата',
        'Сумм'
      );
    },

    /**
     * Create a chart with specified configuration
     */
    createChart(canvasId, labels, data, metricLabels, xAxisLabel, yAxisLabel) {
      const canvas = document.getElementById(canvasId);

      if (!canvas) {
        console.warn(`Canvas ${canvasId} not found`);
        return;
      }

      // Destroy existing chart if it exists to avoid "Canvas is already in use" error
      const existingChart = Chart.getChart(canvas);
      if (existingChart) {
        existingChart.destroy();
      }

      // Create datasets for specified metrics
      // Iterate metricLabels keys instead of data keys to ensure all configured metrics render
      const datasets = Object.keys(metricLabels).map(name => {
        return {
          label: metricLabels[name],
          data: labels.map(date => data[date] && data[date][name] !== undefined ? data[date][name] : 0),
          fill: false,
          borderColor: this.getRandomColor(),
          tension: 0.1
        };
      });

      const ctx = canvas.getContext('2d');
      const chart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: labels,
          datasets: datasets
        },
        options: {
          responsive: true,
          maintainAspectRatio: true,
          scales: {
            x: {
              title: {
                display: true,
                text: xAxisLabel
              }
            },
            y: {
              title: {
                display: true,
                text: yAxisLabel
              }
            }
          }
        }
      });

      // Store chart instance for cleanup
      this.chartInstances.push(chart);
    }
  },

  mounted() {
    console.log('StatsDashboard component mounted', {
      hasSalesData: this.hasSalesData,
      hasGrowthData: this.hasGrowthData,
      salesCompanies: this.salesCompanies.length,
      growthCompanies: this.growthCompanies.length
    });
  },

  beforeUnmount() {
    // Destroy all chart instances to prevent memory leaks
    this.chartInstances.forEach(chart => {
      if (chart && typeof chart.destroy === 'function') {
        chart.destroy();
      }
    });
    this.chartInstances = [];
  },

  template: `
    <div class="tw-p-2">

      <!-- Manager Statistics Section -->
      <div class="tw-mt-2">
        <div class="tw-bg-transparent tw-rounded tw-p-3 tw-pb-2 tw-mb-0">
          <div class="tw-flex tw-flex-wrap tw-justify-center tw-items-center tw-gap-2">
            <h2 class="tw-text-2xl tw-font-bold tw-text-gray-800 dark:tw-text-gray-100 tw-m-0">
              Статистика менеджеров
            </h2>
            <button
              @click="toggleSales"
              class="tw-px-3 tw-py-1 tw-bg-gray-600 dark:tw-bg-gray-700 tw-text-white tw-rounded hover:tw-bg-gray-700 dark:hover:tw-bg-gray-600 tw-transition-colors"
              :aria-expanded="!salesCollapsed"
              aria-controls="collapse-sales"
            >
              <i class="bi bi-nintendo-switch"></i>
            </button>
          </div>
        </div>

        <!-- Sales Charts (Collapsible) -->
        <transition name="tw-slide">
          <div v-show="!salesCollapsed" id="collapse-sales" class="tw-mt-2">
            <div v-if="!hasSalesData" class="tw-text-center tw-p-8 tw-text-gray-500 dark:tw-text-gray-400">
              <i class="bi bi-bar-chart tw-text-4xl"></i>
              <p class="tw-mt-2">Нет данных для отображения</p>
            </div>

            <div v-for="companyId in salesCompanies" :key="'sales-' + companyId" class="tw-flex tw-justify-center tw-my-2">
              <div class="tw-w-full lg:tw-w-3/4 tw-bg-gray-100 dark:tw-bg-gray-800 tw-rounded tw-border tw-border-gray-300 dark:tw-border-gray-700 tw-p-4">

                <!-- Company Name Header -->
                <div class="tw-bg-gray-200 dark:tw-bg-gray-700 tw-rounded tw-p-3 tw-mb-4">
                  <div class="tw-flex tw-flex-wrap tw-justify-center">
                    <h3 class="tw-text-xl tw-font-semibold tw-text-gray-800 dark:tw-text-gray-100 tw-m-0">
                      {{ getCompanyName(companyId) }}
                    </h3>
                  </div>
                </div>

                <!-- Chart Canvas -->
                <div class="tw-w-full tw-my-2 tw-p-2">
                  <canvas :id="'managerStatsChart-' + companyId"></canvas>
                </div>
              </div>
            </div>
          </div>
        </transition>
      </div>

      <hr class="tw-my-4 tw-border-gray-300 dark:tw-border-gray-700">

      <!-- Report Statistics Section -->
      <div class="tw-mt-2">
        <div class="tw-bg-transparent tw-rounded tw-p-3 tw-pb-2 tw-mb-0">
          <div class="tw-flex tw-flex-wrap tw-justify-center tw-items-center tw-gap-2">
            <h2 class="tw-text-2xl tw-font-bold tw-text-gray-800 dark:tw-text-gray-100 tw-m-0">
              Статистика отчётов
            </h2>
            <button
              @click="toggleReports"
              class="tw-px-3 tw-py-1 tw-bg-gray-600 dark:tw-bg-gray-700 tw-text-white tw-rounded hover:tw-bg-gray-700 dark:hover:tw-bg-gray-600 tw-transition-colors"
              :aria-expanded="!reportsCollapsed"
              aria-controls="collapse-reports"
            >
              <i class="bi bi-nintendo-switch"></i>
            </button>
          </div>
        </div>

        <!-- Reports Charts (Collapsible) -->
        <transition name="tw-slide">
          <div v-show="!reportsCollapsed" id="collapse-reports" class="tw-mt-2">
            <div v-if="!hasGrowthData" class="tw-text-center tw-p-8 tw-text-gray-500 dark:tw-text-gray-400">
              <i class="bi bi-bar-chart tw-text-4xl"></i>
              <p class="tw-mt-2">Нет данных для отображения</p>
            </div>

            <div v-for="companyId in growthCompanies" :key="'growth-' + companyId" class="tw-flex tw-justify-center tw-my-2">
              <div class="tw-w-full lg:tw-w-3/4 tw-bg-gray-100 dark:tw-bg-gray-800 tw-rounded tw-border tw-border-gray-300 dark:tw-border-gray-700 tw-p-4">

                <!-- Company Name Header -->
                <div class="tw-bg-gray-200 dark:tw-bg-gray-700 tw-rounded tw-p-3 tw-mb-4">
                  <div class="tw-flex tw-flex-wrap tw-justify-center">
                    <h3 class="tw-text-xl tw-font-semibold tw-text-gray-800 dark:tw-text-gray-100 tw-m-0">
                      {{ getCompanyName(companyId) }}
                    </h3>
                  </div>
                </div>

                <!-- Daily Statistics -->
                <div class="tw-w-full tw-my-2 tw-p-2 tw-border tw-border-gray-300 dark:tw-border-gray-700 tw-rounded">
                  <div class="tw-flex tw-flex-wrap tw-justify-center tw-mb-2">
                    <h4 class="tw-text-lg tw-font-medium tw-text-gray-800 dark:tw-text-gray-100 tw-m-0">Ежедневная</h4>
                  </div>

                  <canvas :id="'repsEveryDayPcStatsChart-' + companyId"></canvas>

                  <hr class="tw-my-2 tw-border-gray-300 dark:tw-border-gray-600">

                  <canvas :id="'repsEveryDaySumStatsChart-' + companyId"></canvas>
                </div>

                <!-- Monthly Statistics -->
                <div class="tw-w-full tw-my-2 tw-p-2 tw-border tw-border-gray-300 dark:tw-border-gray-700 tw-rounded">
                  <div class="tw-flex tw-flex-wrap tw-justify-center tw-mb-2">
                    <h4 class="tw-text-lg tw-font-medium tw-text-gray-800 dark:tw-text-gray-100 tw-m-0">Месяц</h4>
                  </div>

                  <canvas :id="'repsOfMonthPcStatsChart-' + companyId"></canvas>

                  <hr class="tw-my-2 tw-border-gray-300 dark:tw-border-gray-600">

                  <canvas :id="'repsOfMonthSumStatsChart-' + companyId"></canvas>
                </div>
              </div>
            </div>
          </div>
        </transition>
      </div>

    </div>
  `
};
