/**
 * FormMultiSelect Component
 *
 * Reusable multi-select component with Tailwind styling
 * Replaces Bootstrap form-select multiple
 *
 * Props:
 * - label: Select label text
 * - name: Select name attribute (for form submission) - should end with [] for multiple
 * - options: Array of option objects [{value: 'val', label: 'Label', selected: false}]
 * - modelValue: v-model value (array of selected values)
 * - required: Whether field is required - default: false
 * - size: Number of visible options - default: 8
 * - helpText: Help text shown below the select
 *
 * Events:
 * - update:modelValue: Emitted on selection change (for v-model)
 *
 * Usage:
 * <form-multi-select
 *   label="Permissions"
 *   name="permission[]"
 *   v-model="formData.permissions"
 *   :options="[
 *     {value: '1', label: 'Permission 1', selected: true},
 *     {value: '2', label: 'Permission 2', selected: false}
 *   ]"
 *   :size="8"
 *   help-text="Select multiple permissions"
 * ></form-multi-select>
 */

export default {
  name: 'FormMultiSelect',
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
      // Each option: { value, label, selected? }
    },
    modelValue: {
      type: Array,
      default: () => []
    },
    required: {
      type: Boolean,
      default: false
    },
    size: {
      type: Number,
      default: 8
    },
    helpText: {
      type: String,
      default: ''
    }
  },
  emits: ['update:modelValue'],
  data() {
    return {
      selectedValues: []
    };
  },
  mounted() {
    // Initialize selected values from options or modelValue
    if (this.modelValue && this.modelValue.length > 0) {
      this.selectedValues = [...this.modelValue];
    } else {
      this.selectedValues = this.options
        .filter(opt => opt.selected)
        .map(opt => String(opt.value));
    }
  },
  watch: {
    modelValue(newVal) {
      if (newVal) {
        this.selectedValues = [...newVal];
      }
    }
  },
  methods: {
    onSelectionChange(event) {
      // Get all selected options
      const selected = Array.from(event.target.selectedOptions).map(opt => opt.value);
      this.selectedValues = selected;
      this.$emit('update:modelValue', selected);
    },
    isSelected(value) {
      return this.selectedValues.includes(String(value));
    }
  },
  template: `
    <div class="tw-mb-4">
      <label
        :for="name"
        class="tw-block tw-mb-2 tw-text-sm tw-font-medium tw-text-gray-700 dark:tw-text-gray-300"
      >
        {{ label }}{{ required ? ' *' : '' }}
      </label>
      <select
        :id="name"
        :name="name"
        multiple
        :size="size"
        :required="required"
        @change="onSelectionChange"
        class="tw-w-full tw-px-3 tw-py-2 tw-border tw-border-gray-300 dark:tw-border-gray-600
               tw-rounded-lg tw-bg-white dark:tw-bg-gray-800
               tw-text-gray-900 dark:tw-text-gray-100
               focus:tw-outline-none focus:tw-ring-2 focus:tw-ring-blue-500 focus:tw-border-transparent
               tw-transition-colors tw-duration-200"
      >
        <option
          v-for="option in options"
          :key="option.value"
          :value="option.value"
          :selected="isSelected(option.value)"
          class="tw-py-1 hover:tw-bg-blue-50 dark:hover:tw-bg-gray-700"
        >
          {{ option.label }}
        </option>
      </select>
      <div v-if="helpText" class="tw-text-sm tw-text-gray-500 dark:tw-text-gray-400 tw-mt-2">
        {{ helpText }}
      </div>
    </div>
  `
};
