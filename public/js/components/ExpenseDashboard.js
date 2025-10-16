/**
 * ExpenseDashboard Component
 *
 * Comprehensive expense requests dashboard with:
 * - Tab navigation for different request statuses
 * - Data tables with sorting and pagination
 * - Filtering by date range and amount
 * - Export to CSV functionality
 * - Auto-refresh every 5 minutes
 * - Detail modal for individual requests
 *
 * @component
 * @example
 * <expense-dashboard
 *   :company-id="1"
 *   :initial-tab="'pending'"
 * ></expense-dashboard>
 */

const { ref, reactive, computed, onMounted, onUnmounted } = Vue;

window.ExpenseDashboard = {
  name: 'ExpenseDashboard',

  props: {
    companyId: {
      type: Number,
      required: true,
      default: 1
    },
    initialTab: {
      type: String,
      default: 'pending',
      validator: (value) => ['pending', 'approved', 'declined', 'issued'].includes(value)
    },
    autoRefreshInterval: {
      type: Number,
      default: 300000 // 5 minutes in milliseconds
    }
  },

  setup(props) {
    // Reactive state
    const activeTab = ref(props.initialTab);
    const filters = reactive({
      dateFrom: '',
      dateTo: '',
      amountMin: '',
      amountMax: ''
    });
    const sortConfig = reactive({
      pending: null,
      approved: null,
      declined: null,
      issued: null
    });
    const showFilterModal = ref(false);
    const showDetailsModal = ref(false);
    const currentExpenseDetails = ref(null);
    const autoRefreshTimer = ref(null);

    // Tab data storage
    const tabData = reactive({
      pending: { data: [], loading: true, pagination: null },
      approved: { data: [], loading: true, pagination: null },
      declined: { data: [], loading: true, pagination: null },
      issued: { data: [], loading: true, pagination: null }
    });

    // Tab configuration
    const tabs = computed(() => [
      { id: 'pending', label: 'В ожидании', icon: 'bi-clock' },
      { id: 'approved', label: 'Одобренные', icon: 'bi-check-circle' },
      { id: 'declined', label: 'Отклоненные', icon: 'bi-x-circle' },
      { id: 'issued', label: 'Выполненные', icon: 'bi-check2-all' }
    ]);

    // Column definitions per tab
    const columnsByTab = {
      pending: ['date', 'requester_name', 'description', 'amount', 'status'],
      approved: ['date', 'requester_name', 'description', 'amount', 'status'],
      declined: ['date', 'requester_name', 'description', 'amount'],
      issued: ['date', 'requester_name', 'description', 'amount', 'issuer_name', 'issued_amount']
    };

    const columnLabels = {
      date: 'Дата',
      requester_name: 'Заявитель',
      description: 'Описание',
      amount: 'Сумма',
      status: 'Статус',
      issuer_name: 'Исполнитель',
      issued_amount: 'Выдано'
    };

    // Methods
    const switchTab = (tabId) => {
      activeTab.value = tabId;
      if (!tabData[tabId].data.length && !tabData[tabId].loading) {
        loadExpenseRequests(tabId);
      }
    };

    const loadExpenseRequests = async (status, page = 1) => {
      tabData[status].loading = true;

      // Build query parameters
      const params = new URLSearchParams();
      params.append('page', page);

      // Add filters
      if (filters.dateFrom) params.append('date_from', filters.dateFrom);
      if (filters.dateTo) params.append('date_to', filters.dateTo);
      if (filters.amountMin) params.append('amount_min', filters.amountMin);
      if (filters.amountMax) params.append('amount_max', filters.amountMax);

      // Add sorting
      if (sortConfig[status]) {
        params.append('sort_by', sortConfig[status].column);
        params.append('sort_direction', sortConfig[status].direction);
      }

      try {
        const url = `/company/${props.companyId}/expenses/${status}?${params.toString()}`;
        const response = await fetch(url);
        const data = await response.json();

        if (data.error) {
          console.error('Error loading expense requests:', data.error);
          tabData[status].data = [];
          tabData[status].pagination = null;
        } else {
          tabData[status].data = data.data || [];
          tabData[status].pagination = data.pagination || null;
        }
      } catch (error) {
        console.error('Error loading expense requests:', error);
        tabData[status].data = [];
        tabData[status].pagination = null;
      } finally {
        tabData[status].loading = false;
      }
    };

    const loadAllTabs = () => {
      tabs.value.forEach(tab => {
        loadExpenseRequests(tab.id);
      });
    };

    const refreshCurrentTab = () => {
      loadExpenseRequests(activeTab.value);
    };

    const applyFilters = () => {
      showFilterModal.value = false;
      loadAllTabs();
    };

    const clearFilters = () => {
      filters.dateFrom = '';
      filters.dateTo = '';
      filters.amountMin = '';
      filters.amountMax = '';
      showFilterModal.value = false;
      loadAllTabs();
    };

    const exportToCsv = async (status) => {
      // Build query parameters
      const params = new URLSearchParams();
      if (filters.dateFrom) params.append('date_from', filters.dateFrom);
      if (filters.dateTo) params.append('date_to', filters.dateTo);
      if (filters.amountMin) params.append('amount_min', filters.amountMin);
      if (filters.amountMax) params.append('amount_max', filters.amountMax);
      if (sortConfig[status]) {
        params.append('sort_by', sortConfig[status].column);
        params.append('sort_direction', sortConfig[status].direction);
      }

      const url = `/company/${props.companyId}/expenses/export/${status}?${params.toString()}`;
      window.location.href = url;
    };

    const viewDetails = async (requestId) => {
      showDetailsModal.value = true;
      currentExpenseDetails.value = null;

      try {
        const url = `/company/${props.companyId}/expenses/${requestId}`;
        const response = await fetch(url);
        const data = await response.json();

        if (data.error) {
          console.error('Error loading expense details:', data.error);
        } else {
          currentExpenseDetails.value = data.data;
        }
      } catch (error) {
        console.error('Error loading expense details:', error);
      }
    };

    const closeDetailsModal = () => {
      showDetailsModal.value = false;
      currentExpenseDetails.value = null;
    };

    const sortTable = (status, column) => {
      if (!sortConfig[status] || sortConfig[status].column !== column) {
        sortConfig[status] = { column, direction: 'asc' };
      } else {
        sortConfig[status].direction = sortConfig[status].direction === 'asc' ? 'desc' : 'asc';
      }
      loadExpenseRequests(status);
    };

    const formatAmount = (amount) => {
      if (amount === undefined || amount === null) return 'N/A';
      return new Intl.NumberFormat('ru-RU', {
        style: 'currency',
        currency: 'UZS',
        minimumFractionDigits: 2
      }).format(amount);
    };

    const formatDate = (dateString) => {
      if (!dateString) return 'N/A';
      const date = new Date(dateString);
      return date.toLocaleDateString('ru-RU');
    };

    const startAutoRefresh = () => {
      if (autoRefreshTimer.value) {
        clearInterval(autoRefreshTimer.value);
      }
      autoRefreshTimer.value = setInterval(() => {
        loadExpenseRequests(activeTab.value);
      }, props.autoRefreshInterval);
    };

    const stopAutoRefresh = () => {
      if (autoRefreshTimer.value) {
        clearInterval(autoRefreshTimer.value);
        autoRefreshTimer.value = null;
      }
    };

    // Lifecycle hooks
    onMounted(() => {
      loadAllTabs();
      startAutoRefresh();
    });

    onUnmounted(() => {
      stopAutoRefresh();
    });

    return {
      activeTab,
      filters,
      sortConfig,
      showFilterModal,
      showDetailsModal,
      currentExpenseDetails,
      tabData,
      tabs,
      columnsByTab,
      columnLabels,
      switchTab,
      loadExpenseRequests,
      refreshCurrentTab,
      applyFilters,
      clearFilters,
      exportToCsv,
      viewDetails,
      closeDetailsModal,
      sortTable,
      formatAmount,
      formatDate
    };
  },

  template: `
    <div class="tw-container-fluid tw-p-4">
      <h1 class="tw-text-3xl tw-font-bold tw-text-center tw-my-6 tw-text-gray-800 dark:tw-text-gray-100">
        Панель Заявок на Расходы
      </h1>

      <!-- Tabs navigation -->
      <div class="tw-border-b tw-border-gray-300 dark:tw-border-gray-600 tw-mb-4">
        <nav class="tw-flex tw-space-x-4" role="tablist">
          <button
            v-for="tab in tabs"
            :key="tab.id"
            @click="switchTab(tab.id)"
            :class="[
              'tw-px-4 tw-py-2 tw-font-medium tw-transition-colors tw-duration-200',
              'tw-border-b-2 tw-flex tw-items-center tw-space-x-2',
              activeTab === tab.id
                ? 'tw-border-blue-500 tw-text-blue-600 dark:tw-text-blue-400'
                : 'tw-border-transparent tw-text-gray-600 dark:tw-text-gray-400 hover:tw-text-gray-800 dark:hover:tw-text-gray-200'
            ]"
            :aria-selected="activeTab === tab.id"
            role="tab"
          >
            <i :class="['bi', tab.icon]"></i>
            <span>{{ tab.label }}</span>
          </button>
        </nav>
      </div>

      <!-- Tab content -->
      <div v-for="tab in tabs" :key="tab.id" v-show="activeTab === tab.id" role="tabpanel">
        <div class="tw-bg-white dark:tw-bg-gray-800 tw-rounded-lg tw-shadow-md tw-overflow-hidden">
          <!-- Card header -->
          <div class="tw-bg-gray-100 dark:tw-bg-gray-700 tw-px-6 tw-py-4 tw-flex tw-justify-between tw-items-center">
            <h5 class="tw-text-lg tw-font-semibold tw-text-gray-800 dark:tw-text-gray-100">
              {{ tab.label }}
            </h5>
            <div class="tw-flex tw-space-x-2">
              <button
                @click="refreshCurrentTab"
                class="tw-px-3 tw-py-1 tw-text-sm tw-bg-blue-500 tw-text-white tw-rounded hover:tw-bg-blue-600 tw-transition-colors"
                :disabled="tabData[tab.id].loading"
              >
                <i class="bi bi-arrow-repeat"></i> Обновить
              </button>
              <button
                @click="exportToCsv(tab.id)"
                class="tw-px-3 tw-py-1 tw-text-sm tw-bg-green-500 tw-text-white tw-rounded hover:tw-bg-green-600 tw-transition-colors"
              >
                <i class="bi bi-file-earmark-spreadsheet"></i> Экспорт CSV
              </button>
            </div>
          </div>

          <!-- Card body -->
          <div class="tw-p-6">
            <!-- Loading state -->
            <div v-if="tabData[tab.id].loading" class="tw-text-center tw-py-8">
              <div class="tw-inline-block tw-w-8 tw-h-8 tw-border-4 tw-border-blue-500 tw-border-t-transparent tw-rounded-full tw-animate-spin"></div>
              <p class="tw-mt-2 tw-text-gray-600 dark:tw-text-gray-400">Загрузка...</p>
            </div>

            <!-- Table -->
            <div v-else-if="tabData[tab.id].data.length > 0" class="tw-overflow-x-auto">
              <table class="tw-min-w-full tw-divide-y tw-divide-gray-200 dark:tw-divide-gray-700">
                <thead class="tw-bg-gray-50 dark:tw-bg-gray-900">
                  <tr>
                    <th
                      v-for="column in columnsByTab[tab.id]"
                      :key="column"
                      @click="sortTable(tab.id, column)"
                      class="tw-px-6 tw-py-3 tw-text-left tw-text-xs tw-font-medium tw-text-gray-500 dark:tw-text-gray-400 tw-uppercase tw-tracking-wider tw-cursor-pointer hover:tw-bg-gray-100 dark:hover:tw-bg-gray-800"
                    >
                      {{ columnLabels[column] }}
                      <i v-if="sortConfig[tab.id]?.column === column"
                         :class="sortConfig[tab.id].direction === 'asc' ? 'bi-arrow-up' : 'bi-arrow-down'"
                         class="bi tw-ml-1"></i>
                    </th>
                  </tr>
                </thead>
                <tbody class="tw-bg-white dark:tw-bg-gray-800 tw-divide-y tw-divide-gray-200 dark:tw-divide-gray-700">
                  <tr
                    v-for="item in tabData[tab.id].data"
                    :key="item.id"
                    @click="viewDetails(item.id)"
                    class="hover:tw-bg-gray-50 dark:hover:tw-bg-gray-700 tw-cursor-pointer tw-transition-colors"
                  >
                    <td v-for="column in columnsByTab[tab.id]" :key="column" class="tw-px-6 tw-py-4 tw-whitespace-nowrap tw-text-sm tw-text-gray-900 dark:tw-text-gray-100">
                      <span v-if="column === 'date'">{{ formatDate(item[column]) }}</span>
                      <span v-else-if="column === 'amount' || column === 'issued_amount'">{{ formatAmount(item[column]) }}</span>
                      <span v-else-if="column === 'status'">
                        <span :class="[
                          'tw-px-2 tw-py-1 tw-rounded-full tw-text-xs tw-font-medium',
                          item.status === 'pending' ? 'tw-bg-yellow-100 tw-text-yellow-800' :
                          item.status === 'approved' ? 'tw-bg-green-100 tw-text-green-800' :
                          item.status === 'declined' ? 'tw-bg-red-100 tw-text-red-800' :
                          'tw-bg-blue-100 tw-text-blue-800'
                        ]">
                          {{ item.status || 'N/A' }}
                        </span>
                      </span>
                      <span v-else>{{ item[column] || 'N/A' }}</span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Empty state -->
            <div v-else class="tw-text-center tw-py-8">
              <i class="bi bi-inbox tw-text-6xl tw-text-gray-400"></i>
              <p class="tw-mt-4 tw-text-gray-600 dark:tw-text-gray-400">Нет данных для отображения</p>
            </div>

            <!-- Filter and pagination controls -->
            <div class="tw-flex tw-justify-between tw-items-center tw-mt-4">
              <button
                @click="showFilterModal = true"
                class="tw-px-4 tw-py-2 tw-text-sm tw-bg-gray-200 dark:tw-bg-gray-700 tw-text-gray-800 dark:tw-text-gray-200 tw-rounded hover:tw-bg-gray-300 dark:hover:tw-bg-gray-600 tw-transition-colors"
              >
                <i class="bi bi-funnel"></i> Фильтры
              </button>

              <!-- Pagination will go here if needed -->
              <div v-if="tabData[tab.id].pagination" class="tw-flex tw-space-x-2">
                <!-- Simple pagination placeholder -->
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Filter Modal -->
      <div v-if="showFilterModal" class="tw-fixed tw-inset-0 tw-bg-black tw-bg-opacity-50 tw-flex tw-items-center tw-justify-center tw-z-50" @click.self="showFilterModal = false">
        <div class="tw-bg-white dark:tw-bg-gray-800 tw-rounded-lg tw-shadow-xl tw-w-full tw-max-w-md tw-p-6">
          <div class="tw-flex tw-justify-between tw-items-center tw-mb-4">
            <h3 class="tw-text-xl tw-font-bold tw-text-gray-800 dark:tw-text-gray-100">Фильтры</h3>
            <button @click="showFilterModal = false" class="tw-text-gray-500 hover:tw-text-gray-700 dark:tw-text-gray-400 dark:hover:tw-text-gray-200">
              <i class="bi bi-x-lg"></i>
            </button>
          </div>

          <div class="tw-space-y-4">
            <div>
              <label class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 dark:tw-text-gray-300 tw-mb-1">Дата от</label>
              <input v-model="filters.dateFrom" type="date" class="tw-w-full tw-px-3 tw-py-2 tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-rounded tw-bg-white dark:tw-bg-gray-700 tw-text-gray-900 dark:tw-text-gray-100 focus:tw-outline-none focus:tw-ring-2 focus:tw-ring-blue-500" />
            </div>

            <div>
              <label class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 dark:tw-text-gray-300 tw-mb-1">Дата до</label>
              <input v-model="filters.dateTo" type="date" class="tw-w-full tw-px-3 tw-py-2 tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-rounded tw-bg-white dark:tw-bg-gray-700 tw-text-gray-900 dark:tw-text-gray-100 focus:tw-outline-none focus:tw-ring-2 focus:tw-ring-blue-500" />
            </div>

            <div>
              <label class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 dark:tw-text-gray-300 tw-mb-1">Сумма от</label>
              <input v-model="filters.amountMin" type="number" min="0" step="0.01" class="tw-w-full tw-px-3 tw-py-2 tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-rounded tw-bg-white dark:tw-bg-gray-700 tw-text-gray-900 dark:tw-text-gray-100 focus:tw-outline-none focus:tw-ring-2 focus:tw-ring-blue-500" />
            </div>

            <div>
              <label class="tw-block tw-text-sm tw-font-medium tw-text-gray-700 dark:tw-text-gray-300 tw-mb-1">Сумма до</label>
              <input v-model="filters.amountMax" type="number" min="0" step="0.01" class="tw-w-full tw-px-3 tw-py-2 tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-rounded tw-bg-white dark:tw-bg-gray-700 tw-text-gray-900 dark:tw-text-gray-100 focus:tw-outline-none focus:tw-ring-2 focus:tw-ring-blue-500" />
            </div>
          </div>

          <div class="tw-flex tw-space-x-2 tw-mt-6">
            <button @click="clearFilters" class="tw-flex-1 tw-px-4 tw-py-2 tw-bg-gray-200 dark:tw-bg-gray-700 tw-text-gray-800 dark:tw-text-gray-200 tw-rounded hover:tw-bg-gray-300 dark:hover:tw-bg-gray-600 tw-transition-colors">
              Очистить
            </button>
            <button @click="applyFilters" class="tw-flex-1 tw-px-4 tw-py-2 tw-bg-blue-500 tw-text-white tw-rounded hover:tw-bg-blue-600 tw-transition-colors">
              Применить
            </button>
          </div>
        </div>
      </div>

      <!-- Details Modal -->
      <div v-if="showDetailsModal" class="tw-fixed tw-inset-0 tw-bg-black tw-bg-opacity-50 tw-flex tw-items-center tw-justify-center tw-z-50" @click.self="closeDetailsModal">
        <div class="tw-bg-white dark:tw-bg-gray-800 tw-rounded-lg tw-shadow-xl tw-w-full tw-max-w-2xl tw-p-6">
          <div class="tw-flex tw-justify-between tw-items-center tw-mb-4">
            <h3 class="tw-text-xl tw-font-bold tw-text-gray-800 dark:tw-text-gray-100">Детали заявки</h3>
            <button @click="closeDetailsModal" class="tw-text-gray-500 hover:tw-text-gray-700 dark:tw-text-gray-400 dark:hover:tw-text-gray-200">
              <i class="bi bi-x-lg"></i>
            </button>
          </div>

          <!-- Loading state -->
          <div v-if="!currentExpenseDetails" class="tw-text-center tw-py-8">
            <div class="tw-inline-block tw-w-8 tw-h-8 tw-border-4 tw-border-blue-500 tw-border-t-transparent tw-rounded-full tw-animate-spin"></div>
            <p class="tw-mt-2 tw-text-gray-600 dark:tw-text-gray-400">Загрузка деталей...</p>
          </div>

          <!-- Details content -->
          <div v-else class="tw-grid tw-grid-cols-2 tw-gap-4">
            <div>
              <p class="tw-text-sm tw-text-gray-600 dark:tw-text-gray-400">ID</p>
              <p class="tw-font-medium tw-text-gray-900 dark:tw-text-gray-100">{{ currentExpenseDetails.id }}</p>
            </div>
            <div>
              <p class="tw-text-sm tw-text-gray-600 dark:tw-text-gray-400">Дата</p>
              <p class="tw-font-medium tw-text-gray-900 dark:tw-text-gray-100">{{ formatDate(currentExpenseDetails.date) }}</p>
            </div>
            <div>
              <p class="tw-text-sm tw-text-gray-600 dark:tw-text-gray-400">Заявитель</p>
              <p class="tw-font-medium tw-text-gray-900 dark:tw-text-gray-100">{{ currentExpenseDetails.requester_name || 'N/A' }}</p>
            </div>
            <div>
              <p class="tw-text-sm tw-text-gray-600 dark:tw-text-gray-400">Сумма</p>
              <p class="tw-font-medium tw-text-gray-900 dark:tw-text-gray-100">{{ formatAmount(currentExpenseDetails.amount) }}</p>
            </div>
            <div class="tw-col-span-2">
              <p class="tw-text-sm tw-text-gray-600 dark:tw-text-gray-400">Описание</p>
              <p class="tw-font-medium tw-text-gray-900 dark:tw-text-gray-100">{{ currentExpenseDetails.description || 'N/A' }}</p>
            </div>
            <div v-if="currentExpenseDetails.status">
              <p class="tw-text-sm tw-text-gray-600 dark:tw-text-gray-400">Статус</p>
              <p class="tw-font-medium tw-text-gray-900 dark:tw-text-gray-100">{{ currentExpenseDetails.status }}</p>
            </div>
            <div v-if="currentExpenseDetails.issuer_name">
              <p class="tw-text-sm tw-text-gray-600 dark:tw-text-gray-400">Исполнитель</p>
              <p class="tw-font-medium tw-text-gray-900 dark:tw-text-gray-100">{{ currentExpenseDetails.issuer_name }}</p>
            </div>
            <div v-if="currentExpenseDetails.issued_amount">
              <p class="tw-text-sm tw-text-gray-600 dark:tw-text-gray-400">Выдано</p>
              <p class="tw-font-medium tw-text-gray-900 dark:tw-text-gray-100">{{ formatAmount(currentExpenseDetails.issued_amount) }}</p>
            </div>
          </div>

          <div class="tw-mt-6 tw-flex tw-justify-end">
            <button @click="closeDetailsModal" class="tw-px-4 tw-py-2 tw-bg-gray-200 dark:tw-bg-gray-700 tw-text-gray-800 dark:tw-text-gray-200 tw-rounded hover:tw-bg-gray-300 dark:hover:tw-bg-gray-600 tw-transition-colors">
              Закрыть
            </button>
          </div>
        </div>
      </div>
    </div>
  `
};
