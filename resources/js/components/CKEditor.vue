<template>
  <div class="ckeditor-wrapper">
    <label v-if="label" class="block text-sm font-medium text-gray-700 mb-2">
      {{ label }}
      <span v-if="required" class="text-red-500">*</span>
    </label>
    <div class="border border-gray-300 rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-blue-500">
      <Ckeditor
        v-model="localValue"
        :editor="editor"
        :config="editorConfig"
        @ready="onReady"
        @focus="onFocus"
        @blur="onBlur"
      />
    </div>
    <p v-if="error" class="mt-1 text-sm text-red-600">{{ error }}</p>
    <p v-if="hint" class="mt-1 text-sm text-gray-500">{{ hint }}</p>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { Ckeditor } from '@ckeditor/ckeditor5-vue'
import ClassicEditor from '@ckeditor/ckeditor5-build-classic'

interface Props {
  modelValue: string
  label?: string
  required?: boolean
  error?: string
  hint?: string
  placeholder?: string
  disabled?: boolean
  minHeight?: string
}

interface Emits {
  (e: 'update:modelValue', value: string): void
  (e: 'focus'): void
  (e: 'blur'): void
}

const props = withDefaults(defineProps<Props>(), {
  modelValue: '',
  minHeight: '150px'
})

const emit = defineEmits<Emits>()

const editor = ClassicEditor
const localValue = ref(props.modelValue)

// Editor configuration
const editorConfig = computed(() => ({
  toolbar: {
    items: [
      'heading',
      '|',
      'bold',
      'italic',
      'underline',
      '|',
      'bulletedList',
      'numberedList',
      '|',
      'outdent',
      'indent',
      '|',
      'link',
      '|',
      'blockQuote',
      'insertTable',
      '|',
      'undo',
      'redo'
    ]
  },
  placeholder: props.placeholder || 'Enter product description...',
  heading: {
    options: [
      { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
      { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
      { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
      { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' }
    ]
  },
  table: {
    contentToolbar: [
      'tableColumn',
      'tableRow',
      'mergeTableCells'
    ]
  },
  link: {
    decorators: {
      openInNewTab: {
        mode: 'manual',
        label: 'Open in a new tab',
        attributes: {
          target: '_blank',
          rel: 'noopener noreferrer'
        }
      }
    }
  },
  // Custom styles for the editor
  extraPlugins: [],
  removePlugins: ['MediaEmbed'], // Disable media embed for security
}))

// Watch for external changes to modelValue
watch(() => props.modelValue, (newValue) => {
  if (newValue !== localValue.value) {
    localValue.value = newValue
  }
})

// Watch for internal changes and emit
watch(localValue, (newValue) => {
  emit('update:modelValue', newValue)
})

// Event handlers
const onReady = (editor: any) => {
  // Set minimum height
  if (props.minHeight) {
    const view = editor.editing.view
    view.change((writer: any) => {
      writer.setStyle('min-height', props.minHeight, view.document.getRoot())
    })
  }
}

const onFocus = () => {
  emit('focus')
}

const onBlur = () => {
  emit('blur')
}
</script>

<style scoped>
.ckeditor-wrapper {
  width: 100%;
}

/* Custom CKEditor styling */
.ckeditor-wrapper :deep(.ck-editor) {
  border: 0;
}

.ckeditor-wrapper :deep(.ck-editor__top) {
  border-bottom: 1px solid #e5e7eb;
}

.dark .ckeditor-wrapper :deep(.ck-editor__top) {
  border-bottom-color: #4b5563;
}

.ckeditor-wrapper :deep(.ck-toolbar) {
  background-color: #f9fafb;
  border: 0;
}

.dark .ckeditor-wrapper :deep(.ck-toolbar) {
  background-color: #1f2937;
}

.ckeditor-wrapper :deep(.ck-button) {
  color: #4b5563;
}

.dark .ckeditor-wrapper :deep(.ck-button) {
  color: #d1d5db;
}

.ckeditor-wrapper :deep(.ck-button:hover) {
  background-color: #f3f4f6;
}

.dark .ckeditor-wrapper :deep(.ck-button:hover) {
  background-color: #374151;
}

.ckeditor-wrapper :deep(.ck-button.ck-on) {
  background-color: #dbeafe;
  color: #2563eb;
}

.dark .ckeditor-wrapper :deep(.ck-button.ck-on) {
  background-color: #1e3a8a;
  color: #93c5fd;
}

.ckeditor-wrapper :deep(.ck-content) {
  background-color: #ffffff;
  color: #111827;
  padding: 1rem;
  min-height: 150px;
}

.dark .ckeditor-wrapper :deep(.ck-content) {
  background-color: #111827;
  color: #f9fafb;
}

.ckeditor-wrapper :deep(.ck-content h1) {
  font-size: 1.5rem;
  font-weight: 700;
  margin-bottom: 1rem;
}

.ckeditor-wrapper :deep(.ck-content h2) {
  font-size: 1.25rem;
  font-weight: 700;
  margin-bottom: 0.75rem;
}

.ckeditor-wrapper :deep(.ck-content h3) {
  font-size: 1.125rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
}

.ckeditor-wrapper :deep(.ck-content ul) {
  list-style-type: disc;
  list-style-position: inside;
  margin-bottom: 1rem;
}

.ckeditor-wrapper :deep(.ck-content ol) {
  list-style-type: decimal;
  list-style-position: inside;
  margin-bottom: 1rem;
}

.ckeditor-wrapper :deep(.ck-content blockquote) {
  border-left: 4px solid #d1d5db;
  padding-left: 1rem;
  font-style: italic;
  margin: 1rem 0;
}

.dark .ckeditor-wrapper :deep(.ck-content blockquote) {
  border-left-color: #4b5563;
}

.ckeditor-wrapper :deep(.ck-content table) {
  border-collapse: collapse;
  border: 1px solid #d1d5db;
  width: 100%;
  margin-bottom: 1rem;
}

.dark .ckeditor-wrapper :deep(.ck-content table) {
  border-color: #4b5563;
}

.ckeditor-wrapper :deep(.ck-content table td,
.ckeditor-wrapper :deep(.ck-content table th)) {
  border: 1px solid #d1d5db;
  padding: 0.5rem;
}

.dark .ckeditor-wrapper :deep(.ck-content table td,
.dark .ckeditor-wrapper :deep(.ck-content table th)) {
  border-color: #4b5563;
}

.ckeditor-wrapper :deep(.ck-content table th) {
  background-color: #f3f4f6;
  font-weight: 700;
}

.dark .ckeditor-wrapper :deep(.ck-content table th) {
  background-color: #1f2937;
}
</style>