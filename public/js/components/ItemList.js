/**
 * ItemList.js - Vue 3 Component
 *
 * Reusable component for displaying lists of items with action buttons.
 * Replaces Bootstrap card lists with Tailwind-styled Vue components.
 *
 * Features:
 * - Dynamic item rendering with slots
 * - Configurable action buttons (edit, delete, custom actions)
 * - Dark mode support
 * - Responsive design
 * - Empty state handling
 *
 * Usage:
 * <item-list
 *   :items="users"
 *   :actions="[
 *     { icon: 'bi-pencil', variant: 'warning', href: (item) => `/edit/${item.id}` },
 *     { icon: 'bi-trash', variant: 'danger', href: (item) => `/delete/${item.id}`, confirm: true }
 *   ]"
 *   empty-text="Нет элементов">
 *   <template #item="{ item }">
 *     <span>{{ item.name }}</span>
 *   </template>
 * </item-list>
 *
 * Props:
 * - items: Array of items to display
 * - actions: Array of action button configurations
 * - emptyText: Text to display when no items
 * - itemClass: Additional CSS classes for item container
 *
 * @component
 * @author Claude Code
 */

const { ref, computed } = Vue;

const ItemList = {
    name: 'ItemList',
    props: {
        /**
         * Array of items to display
         */
        items: {
            type: Array,
            default: () => []
        },
        /**
         * Array of action button configurations
         * Each action: { icon, variant, href, confirm, tooltip }
         */
        actions: {
            type: Array,
            default: () => []
        },
        /**
         * Text to show when items array is empty
         */
        emptyText: {
            type: String,
            default: 'Нет элементов для отображения'
        },
        /**
         * Additional CSS classes for each item container
         */
        itemClass: {
            type: String,
            default: ''
        },
        /**
         * Show action buttons
         */
        showActions: {
            type: Boolean,
            default: true
        }
    },
    setup(props, { slots }) {
        /**
         * Check if items list is empty
         */
        const isEmpty = computed(() => {
            return !props.items || props.items.length === 0;
        });

        /**
         * Get button CSS classes based on variant
         */
        const getButtonClass = (variant) => {
            const baseClasses = 'tw-px-3 tw-py-2 tw-rounded tw-text-white tw-transition-colors tw-duration-200 tw-flex tw-items-center tw-justify-center';

            const variantClasses = {
                'primary': 'tw-bg-blue-600 hover:tw-bg-blue-700 dark:tw-bg-blue-500 dark:hover:tw-bg-blue-600',
                'secondary': 'tw-bg-gray-600 hover:tw-bg-gray-700 dark:tw-bg-gray-500 dark:hover:tw-bg-gray-600',
                'success': 'tw-bg-green-600 hover:tw-bg-green-700 dark:tw-bg-green-500 dark:hover:tw-bg-green-600',
                'danger': 'tw-bg-red-600 hover:tw-bg-red-700 dark:tw-bg-red-500 dark:hover:tw-bg-red-600',
                'warning': 'tw-bg-yellow-500 hover:tw-bg-yellow-600 dark:tw-bg-yellow-400 dark:hover:tw-bg-yellow-500',
                'info': 'tw-bg-cyan-600 hover:tw-bg-cyan-700 dark:tw-bg-cyan-500 dark:hover:tw-bg-cyan-600'
            };

            return `${baseClasses} ${variantClasses[variant] || variantClasses['primary']}`;
        };

        /**
         * Handle action button click
         */
        const handleAction = (action, item, event) => {
            // If action requires confirmation
            if (action.confirm) {
                const confirmMessage = action.confirmMessage || `Вы уверены, что хотите выполнить это действие?`;
                if (!confirm(confirmMessage)) {
                    event.preventDefault();
                    return false;
                }
            }

            // If action has custom onClick handler
            if (action.onClick && typeof action.onClick === 'function') {
                event.preventDefault();
                action.onClick(item);
                return false;
            }

            // Otherwise, let the link navigate normally
            return true;
        };

        /**
         * Get href for action button
         */
        const getActionHref = (action, item) => {
            if (typeof action.href === 'function') {
                return action.href(item);
            }
            return action.href || '#';
        };

        return {
            isEmpty,
            getButtonClass,
            handleAction,
            getActionHref
        };
    },
    template: `
        <div class="tw-space-y-2">
            <!-- Empty state -->
            <div v-if="isEmpty" class="tw-text-center tw-py-8 tw-text-gray-500 dark:tw-text-gray-400">
                <i class="bi bi-inbox tw-text-4xl tw-mb-2"></i>
                <p class="tw-text-lg">{{ emptyText }}</p>
            </div>

            <!-- Items list -->
            <div
                v-for="(item, index) in items"
                :key="item.id || index"
                class="tw-flex tw-justify-between tw-items-center tw-rounded-lg tw-bg-white dark:tw-bg-gray-800 tw-shadow-md tw-p-3 tw-transition-shadow hover:tw-shadow-lg"
                :class="itemClass"
            >
                <!-- Item content (slot) -->
                <div class="tw-flex-1 tw-text-gray-900 dark:tw-text-gray-100">
                    <slot name="item" :item="item" :index="index">
                        {{ item.name || item.title || 'Элемент ' + (index + 1) }}
                    </slot>
                </div>

                <!-- Action buttons -->
                <div
                    v-if="showActions && actions.length > 0"
                    class="tw-flex tw-gap-1 tw-ml-3"
                    role="group"
                    aria-label="Действия"
                >
                    <a
                        v-for="(action, actionIndex) in actions"
                        :key="actionIndex"
                        :href="getActionHref(action, item)"
                        :class="getButtonClass(action.variant || 'primary')"
                        :title="action.tooltip || ''"
                        @click="handleAction(action, item, $event)"
                    >
                        <i :class="action.icon"></i>
                    </a>
                </div>
            </div>
        </div>
    `
};

// Register component globally
if (window.Vue) {
    window.ItemList = ItemList;
    console.log('ItemList component registered');
}

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ItemList;
}
