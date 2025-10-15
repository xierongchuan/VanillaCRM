/**
 * FormTextarea Component
 *
 * Reusable textarea component with Tailwind styling
 * Replaces Bootstrap form-control textarea
 *
 * Props:
 * - label: Textarea label text
 * - name: Textarea name attribute (for form submission)
 * - modelValue: v-model value
 * - required: Whether field is required - default: false
 * - placeholder: Textarea placeholder
 * - rows: Number of rows - default: 10
 * - cols: Number of cols - default: 30
 * - oldValue: Laravel old() value for validation errors
 *
 * Events:
 * - update:modelValue: Emitted on input change (for v-model)
 *
 * Usage:
 * <form-textarea
 *   label="Description"
 *   name="description"
 *   v-model="formData.description"
 *   :rows="5"
 * ></form-textarea>
 */

export default {
  name: 'FormTextarea',
  props: {
    label: {
      type: String,
      required: true
    },
    name: {
      type: String,
      required: true
    },
    modelValue: {
      type: String,
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
    rows: {
      type: Number,
      default: 10
    },
    cols: {
      type: Number,
      default: 30
    },
    oldValue: {
      type: String,
      default: ''
    }
  },
  emits: ['update:modelValue'],
  computed: {
    textareaValue() {
      // Priority: oldValue (from Laravel validation) > modelValue (from Vue)
      return this.oldValue || this.modelValue;
    }
  },
  methods: {
    onInput(event) {
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
      <textarea
        :id="name"
        :name="name"
        :value="textareaValue"
        @input="onInput"
        :required="required"
        :placeholder="placeholder"
        :rows="rows"
        :cols="cols"
        class="tw-w-full tw-px-3 tw-py-2 tw-border tw-border-gray-300 dark:tw-border-gray-600
               tw-rounded-lg tw-bg-white dark:tw-bg-gray-800
               tw-text-gray-900 dark:tw-text-gray-100
               focus:tw-outline-none focus:tw-ring-2 focus:tw-ring-blue-500 focus:tw-border-transparent
               placeholder:tw-text-gray-400 dark:placeholder:tw-text-gray-500
               tw-transition-colors tw-duration-200 tw-resize-y"
      ></textarea>
    </div>
  `
};
