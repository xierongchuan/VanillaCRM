/**
 * FormRadioGroup Component
 *
 * Reusable radio button group component with Tailwind styling
 * Replaces Bootstrap btn-group radio buttons
 *
 * Props:
 * - label: Group label text
 * - name: Input name attribute (for form submission)
 * - options: Array of option objects [{value: 'val', label: 'Label', icon: 'bi-icon', color: 'primary'}]
 * - modelValue: v-model value (selected option value)
 * - required: Whether field is required - default: false
 * - helpText: Help text shown below the options
 * - oldValue: Laravel old() value for validation errors
 *
 * Events:
 * - update:modelValue: Emitted on selection change (for v-model)
 *
 * Usage:
 * <form-radio-group
 *   label="Role"
 *   name="role"
 *   v-model="formData.role"
 *   :options="[
 *     {value: 'user', label: 'User', icon: 'bi-person', color: 'primary'},
 *     {value: 'admin', label: 'Admin', icon: 'bi-shield', color: 'success'}
 *   ]"
 *   help-text="Select appropriate role"
 *   required
 * ></form-radio-group>
 */

export default {
  name: 'FormRadioGroup',
  props: {
    label: {
      type: String,
      required: true
    },
    name: {
      type: String,
      required: true
    },
    options: {
      type: Array,
      required: true,
      // Each option: { value, label, icon?, color? }
    },
    modelValue: {
      type: String,
      default: ''
    },
    required: {
      type: Boolean,
      default: false
    },
    helpText: {
      type: String,
      default: ''
    },
    oldValue: {
      type: String,
      default: ''
    }
  },
  emits: ['update:modelValue'],
  data() {
    return {
      selectedValue: this.oldValue || this.modelValue || ''
    };
  },
  watch: {
    modelValue(newVal) {
      this.selectedValue = newVal;
    },
    oldValue(newVal) {
      if (newVal) {
        this.selectedValue = newVal;
      }
    }
  },
  methods: {
    selectOption(value) {
      this.selectedValue = value;
      this.$emit('update:modelValue', value);
    },
    getColorClasses(option) {
      const isSelected = this.selectedValue === option.value;
      const colorMap = {
        primary: isSelected
          ? 'tw-bg-blue-600 tw-text-white tw-border-blue-600'
          : 'tw-bg-white dark:tw-bg-gray-800 tw-text-blue-600 dark:tw-text-blue-400 tw-border-blue-600 dark:tw-border-blue-400 hover:tw-bg-blue-50 dark:hover:tw-bg-gray-700',
        secondary: isSelected
          ? 'tw-bg-gray-600 tw-text-white tw-border-gray-600'
          : 'tw-bg-white dark:tw-bg-gray-800 tw-text-gray-600 dark:tw-text-gray-400 tw-border-gray-600 dark:tw-border-gray-400 hover:tw-bg-gray-50 dark:hover:tw-bg-gray-700',
        success: isSelected
          ? 'tw-bg-green-600 tw-text-white tw-border-green-600'
          : 'tw-bg-white dark:tw-bg-gray-800 tw-text-green-600 dark:tw-text-green-400 tw-border-green-600 dark:tw-border-green-400 hover:tw-bg-green-50 dark:hover:tw-bg-gray-700',
        danger: isSelected
          ? 'tw-bg-red-600 tw-text-white tw-border-red-600'
          : 'tw-bg-white dark:tw-bg-gray-800 tw-text-red-600 dark:tw-text-red-400 tw-border-red-600 dark:tw-border-red-400 hover:tw-bg-red-50 dark:hover:tw-bg-gray-700',
        warning: isSelected
          ? 'tw-bg-yellow-600 tw-text-white tw-border-yellow-600'
          : 'tw-bg-white dark:tw-bg-gray-800 tw-text-yellow-600 dark:tw-text-yellow-400 tw-border-yellow-600 dark:tw-border-yellow-400 hover:tw-bg-yellow-50 dark:hover:tw-bg-gray-700',
        info: isSelected
          ? 'tw-bg-cyan-600 tw-text-white tw-border-cyan-600'
          : 'tw-bg-white dark:tw-bg-gray-800 tw-text-cyan-600 dark:tw-text-cyan-400 tw-border-cyan-600 dark:tw-border-cyan-400 hover:tw-bg-cyan-50 dark:hover:tw-bg-gray-700'
      };

      return colorMap[option.color] || colorMap.primary;
    }
  },
  template: `
    <div class="tw-mb-4">
      <label class="tw-block tw-mb-2 tw-text-sm tw-font-bold tw-text-gray-700 dark:tw-text-gray-300">
        {{ label }}{{ required ? ' *' : '' }}
      </label>
      <div class="tw-flex tw-flex-wrap tw-gap-2" role="group" :aria-label="label">
        <div
          v-for="(option, index) in options"
          :key="option.value"
          class="tw-flex-1 tw-min-w-[120px]"
        >
          <!-- Hidden radio input for form submission -->
          <input
            type="radio"
            :id="name + '_' + option.value"
            :name="name"
            :value="option.value"
            :checked="selectedValue === option.value"
            :required="required && index === 0"
            class="tw-sr-only"
            @change="selectOption(option.value)"
          />
          <!-- Styled label button -->
          <label
            :for="name + '_' + option.value"
            :class="[
              'tw-block tw-w-full tw-py-3 tw-px-4 tw-text-center tw-rounded-lg',
              'tw-border-2 tw-font-medium tw-cursor-pointer',
              'tw-transition-all tw-duration-200',
              getColorClasses(option)
            ]"
          >
            <i v-if="option.icon" :class="option.icon + ' tw-mr-2'"></i>
            {{ option.label }}
          </label>
        </div>
      </div>
      <div v-if="helpText" class="tw-text-sm tw-text-gray-500 dark:tw-text-gray-400 tw-mt-2">
        {{ helpText }}
      </div>
    </div>
  `
};
