/**
 * DataTable.js - Vue 3 Component
 *
 * Reusable data table component with sorting, pagination, and filtering.
 * Replaces Bootstrap tables with Tailwind-styled Vue components.
 *
 * Features:
 * - Client-side sorting (click column headers)
 * - Search/filter functionality
 * - Responsive design
 * - Dark mode support
 * - Empty state handling
 * - Customizable columns
 * - Pagination support (optional)
 *
 * Usage:
 * <data-table
 *   :columns="[
 *     { key: 'name', label: 'Имя', sortable: true },
 *     { key: 'value', label: 'Значение', sortable: true, align: 'right' }
 *   ]"
 *   :rows="items"
 *   :searchable="true"
 *   :paginate="false">
 * </data-table>
 *
 * Props:
 * - columns: Array of column definitions
 * - rows: Array of data rows
 * - searchable: Enable search functionality
 * - paginate: Enable pagination
 * - perPage: Items per page
 * - striped: Alternating row colors
 * - hover: Hover effect on rows
 *
 * @component
 * @author Claude Code
 */

const { ref, computed, watch } = Vue;

const DataTable = {
    name: 'DataTable',
    props: {
        /**
         * Array of column definitions
         * Each column: { key, label, sortable, align, width, formatter }
         */
        columns: {
            type: Array,
            required: true,
            validator: (cols) => cols.every(col => col.key && col.label)
        },
        /**
         * Array of data rows
         */
        rows: {
            type: Array,
            default: () => []
        },
        /**
         * Enable search/filter functionality
         */
        searchable: {
            type: Boolean,
            default: false
        },
        /**
         * Search placeholder text
         */
        searchPlaceholder: {
            type: String,
            default: 'Поиск...'
        },
        /**
         * Enable pagination
         */
        paginate: {
            type: Boolean,
            default: false
        },
        /**
         * Number of items per page
         */
        perPage: {
            type: Number,
            default: 10
        },
        /**
         * Striped rows (alternating colors)
         */
        striped: {
            type: Boolean,
            default: true
        },
        /**
         * Hover effect on rows
         */
        hover: {
            type: Boolean,
            default: true
        },
        /**
         * Empty state text
         */
        emptyText: {
            type: String,
            default: 'Нет данных для отображения'
        },
        /**
         * Additional table classes
         */
        tableClass: {
            type: String,
            default: ''
        }
    },
    setup(props) {
        // State
        const searchQuery = ref('');
        const sortColumn = ref(null);
        const sortDirection = ref('asc'); // 'asc' or 'desc'
        const currentPage = ref(1);

        /**
         * Filtered rows based on search query
         */
        const filteredRows = computed(() => {
            if (!props.searchable || !searchQuery.value) {
                return props.rows;
            }

            const query = searchQuery.value.toLowerCase();

            return props.rows.filter(row => {
                return props.columns.some(col => {
                    const value = row[col.key];
                    if (value === null || value === undefined) return false;
                    return String(value).toLowerCase().includes(query);
                });
            });
        });

        /**
         * Sorted rows
         */
        const sortedRows = computed(() => {
            if (!sortColumn.value) {
                return filteredRows.value;
            }

            const rows = [...filteredRows.value];
            const column = sortColumn.value;

            rows.sort((a, b) => {
                let aVal = a[column];
                let bVal = b[column];

                // Handle null/undefined
                if (aVal === null || aVal === undefined) aVal = '';
                if (bVal === null || bVal === undefined) bVal = '';

                // Convert to strings for comparison
                aVal = String(aVal);
                bVal = String(bVal);

                // Numeric comparison if both are numbers
                const aNum = parseFloat(aVal.replace(/[^\d.-]/g, ''));
                const bNum = parseFloat(bVal.replace(/[^\d.-]/g, ''));

                if (!isNaN(aNum) && !isNaN(bNum)) {
                    return sortDirection.value === 'asc' ? aNum - bNum : bNum - aNum;
                }

                // String comparison
                const comparison = aVal.localeCompare(bVal, 'ru');
                return sortDirection.value === 'asc' ? comparison : -comparison;
            });

            return rows;
        });

        /**
         * Paginated rows
         */
        const paginatedRows = computed(() => {
            if (!props.paginate) {
                return sortedRows.value;
            }

            const start = (currentPage.value - 1) * props.perPage;
            const end = start + props.perPage;
            return sortedRows.value.slice(start, end);
        });

        /**
         * Total pages
         */
        const totalPages = computed(() => {
            if (!props.paginate) return 1;
            return Math.ceil(sortedRows.value.length / props.perPage);
        });

        /**
         * Handle column sort
         */
        const handleSort = (column) => {
            if (!column.sortable) return;

            if (sortColumn.value === column.key) {
                // Toggle direction
                sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
            } else {
                // New column
                sortColumn.value = column.key;
                sortDirection.value = 'asc';
            }
        };

        /**
         * Get sort icon for column
         */
        const getSortIcon = (column) => {
            if (!column.sortable) return '';
            if (sortColumn.value !== column.key) return 'bi-arrow-down-up';
            return sortDirection.value === 'asc' ? 'bi-arrow-up' : 'bi-arrow-down';
        };

        /**
         * Get column alignment class
         */
        const getAlignClass = (align) => {
            const alignMap = {
                'left': 'tw-text-left',
                'center': 'tw-text-center',
                'right': 'tw-text-right'
            };
            return alignMap[align] || 'tw-text-left';
        };

        /**
         * Format cell value
         */
        const formatValue = (row, column) => {
            const value = row[column.key];

            // Custom formatter
            if (column.formatter && typeof column.formatter === 'function') {
                return column.formatter(value, row);
            }

            // Default formatting
            if (value === null || value === undefined) {
                return '—';
            }

            return value;
        };

        /**
         * Go to page
         */
        const goToPage = (page) => {
            if (page < 1 || page > totalPages.value) return;
            currentPage.value = page;
        };

        /**
         * Reset to first page when search changes
         */
        watch(searchQuery, () => {
            currentPage.value = 1;
        });

        /**
         * Check if table is empty
         */
        const isEmpty = computed(() => {
            return paginatedRows.value.length === 0;
        });

        return {
            searchQuery,
            sortColumn,
            sortDirection,
            currentPage,
            paginatedRows,
            totalPages,
            handleSort,
            getSortIcon,
            getAlignClass,
            formatValue,
            goToPage,
            isEmpty
        };
    },
    template: `
        <div class="tw-w-full">
            <!-- Search bar -->
            <div v-if="searchable" class="tw-mb-4">
                <div class="tw-relative">
                    <input
                        v-model="searchQuery"
                        type="text"
                        :placeholder="searchPlaceholder"
                        class="tw-w-full tw-px-4 tw-py-2 tw-pl-10 tw-border tw-border-gray-300 dark:tw-border-gray-600 tw-rounded-lg tw-bg-white dark:tw-bg-gray-800 tw-text-gray-900 dark:tw-text-gray-100 focus:tw-ring-2 focus:tw-ring-blue-500 focus:tw-outline-none tw-transition-colors"
                    />
                    <i class="bi bi-search tw-absolute tw-left-3 tw-top-3 tw-text-gray-400"></i>
                </div>
            </div>

            <!-- Table -->
            <div class="tw-overflow-x-auto tw-rounded-lg tw-shadow-md">
                <table
                    class="tw-min-w-full tw-divide-y tw-divide-gray-200 dark:tw-divide-gray-700"
                    :class="tableClass"
                >
                    <!-- Table head -->
                    <thead class="tw-bg-gray-50 dark:tw-bg-gray-800">
                        <tr>
                            <th
                                v-for="column in columns"
                                :key="column.key"
                                :style="column.width ? { width: column.width } : {}"
                                :class="[
                                    'tw-px-6 tw-py-3 tw-text-xs tw-font-medium tw-text-gray-700 dark:tw-text-gray-300 tw-uppercase tw-tracking-wider',
                                    getAlignClass(column.align),
                                    column.sortable ? 'tw-cursor-pointer hover:tw-bg-gray-100 dark:hover:tw-bg-gray-700 tw-select-none' : ''
                                ]"
                                @click="handleSort(column)"
                            >
                                <div class="tw-flex tw-items-center tw-gap-2" :class="getAlignClass(column.align)">
                                    <span>{{ column.label }}</span>
                                    <i
                                        v-if="column.sortable"
                                        :class="['bi', getSortIcon(column), 'tw-text-sm']"
                                    ></i>
                                </div>
                            </th>
                        </tr>
                    </thead>

                    <!-- Table body -->
                    <tbody
                        class="tw-bg-white dark:tw-bg-gray-900 tw-divide-y tw-divide-gray-200 dark:tw-divide-gray-700"
                    >
                        <!-- Empty state -->
                        <tr v-if="isEmpty">
                            <td
                                :colspan="columns.length"
                                class="tw-px-6 tw-py-8 tw-text-center tw-text-gray-500 dark:tw-text-gray-400"
                            >
                                <i class="bi bi-inbox tw-text-3xl tw-mb-2 tw-block"></i>
                                {{ emptyText }}
                            </td>
                        </tr>

                        <!-- Data rows -->
                        <tr
                            v-for="(row, index) in paginatedRows"
                            :key="row.id || index"
                            :class="[
                                striped && index % 2 === 1 ? 'tw-bg-gray-50 dark:tw-bg-gray-800' : '',
                                hover ? 'hover:tw-bg-gray-100 dark:hover:tw-bg-gray-700 tw-transition-colors' : ''
                            ]"
                        >
                            <td
                                v-for="column in columns"
                                :key="column.key"
                                :class="[
                                    'tw-px-6 tw-py-4 tw-whitespace-nowrap tw-text-sm tw-text-gray-900 dark:tw-text-gray-100',
                                    getAlignClass(column.align)
                                ]"
                            >
                                {{ formatValue(row, column) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="paginate && totalPages > 1" class="tw-mt-4 tw-flex tw-justify-between tw-items-center">
                <div class="tw-text-sm tw-text-gray-700 dark:tw-text-gray-300">
                    Страница {{ currentPage }} из {{ totalPages }}
                </div>
                <div class="tw-flex tw-gap-2">
                    <button
                        @click="goToPage(currentPage - 1)"
                        :disabled="currentPage === 1"
                        class="tw-px-3 tw-py-2 tw-rounded tw-bg-gray-200 dark:tw-bg-gray-700 tw-text-gray-700 dark:tw-text-gray-300 hover:tw-bg-gray-300 dark:hover:tw-bg-gray-600 disabled:tw-opacity-50 disabled:tw-cursor-not-allowed tw-transition-colors"
                    >
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <button
                        @click="goToPage(currentPage + 1)"
                        :disabled="currentPage === totalPages"
                        class="tw-px-3 tw-py-2 tw-rounded tw-bg-gray-200 dark:tw-bg-gray-700 tw-text-gray-700 dark:tw-text-gray-300 hover:tw-bg-gray-300 dark:hover:tw-bg-gray-600 disabled:tw-opacity-50 disabled:tw-cursor-not-allowed tw-transition-colors"
                    >
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    `
};

// Register component globally
if (window.Vue) {
    window.DataTable = DataTable;
    console.log('DataTable component registered');
}

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = DataTable;
}
