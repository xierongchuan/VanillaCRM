/**
 * FormInput Component
 *
 * Reusable form input component with Tailwind styling
 * Replaces Bootstrap form-control inputs
 *
 * Props:
 * - label: Input label text
 * - name: Input name attribute (for form submission)
 * - type: Input type (text, password, email, etc.) - default: 'text'
 * - modelValue: v-model value
 * - required: Whether field is required - default: false
 * - placeholder: Input placeholder
 * - autocomplete: Autocomplete attribute
 * - oldValue: Laravel old() value for validation errors
 *
 * Events:
 * - update:modelValue: Emitted on input change (for v-model)
 *
 * Usage:
 * <form-input
 *   label="Username"
 *   name="username"
 *   v-model="formData.username"
 *   required
 * ></form-input>
 */

export default {
  name: 'FormInput',
  props: {
    label: {
      type: String,
      required: true
    },
    name: {
      type: String,
      required: true
    },
    type: {
      type: String,
      default: 'text'
    },
    modelValue: {
      type: [String, Number],
      default: ''
    },
    required: {
      type: Boolean,
      default: false
    },
    placeholder: {
      type: String,
      default: ''
    },
    autocomplete: {
      type: String,
      default: 'off'
    },
    oldValue: {
      type: String,
      default: ''
    }
  },
  emits: ['update:modelValue'],
  data() {
    return {
      internalValue: ''
    };
  },
  mounted() {
    // Initialize with oldValue (for validation errors) or modelValue
    this.internalValue = this.oldValue || this.modelValue;
  },
  computed: {
    inputValue() {
      // Use internal value which tracks user input
      return this.internalValue;
    }
  },
  watch: {
    modelValue(newVal) {
      // Only update if there's no oldValue (oldValue takes precedence on initial load only)
      if (!this.oldValue) {
        this.internalValue = newVal;
      }
    }
  },
  methods: {
    onInput(event) {
      this.internalValue = event.target.value;
      this.$emit('update:modelValue', event.target.value);
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
      <input
        :type="type"
        :id="name"
        :name="name"
        :value="inputValue"
        @input="onInput"
        :required="required"
        :placeholder="placeholder"
        :autocomplete="autocomplete"
        class="tw-w-full tw-px-3 tw-py-2 tw-border tw-border-gray-300 dark:tw-border-gray-600
               tw-rounded-lg tw-bg-white dark:tw-bg-gray-800
               tw-text-gray-900 dark:tw-text-gray-100
               focus:tw-outline-none focus:tw-ring-2 focus:tw-ring-blue-500 focus:tw-border-transparent
               placeholder:tw-text-gray-400 dark:placeholder:tw-text-gray-500
               tw-transition-colors tw-duration-200"
      />
    </div>
  `
};
