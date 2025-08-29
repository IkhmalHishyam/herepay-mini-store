<script setup lang="ts">
import { Toaster } from 'vue-sonner'
import { computed, nextTick, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { useToast } from '@/composables/useToast'

// Get current theme for toast styling
const page = usePage()
const { success, error, warning, info } = useToast()

// Watch for flash messages from Laravel and display them as toasts
// This will trigger on every page navigation/form submission
watch(
  () => page.props.flash,
  async (flash: any) => {
    if (!flash) return

    // Ensure the Toaster component is fully mounted before showing toasts
    await nextTick()
    
    // Add a small delay to ensure vue-sonner is fully initialized
    setTimeout(() => {
      if (flash?.success) {
        success(flash.success)
      }
      
      if (flash?.error) {
        error(flash.error)
      }
      
      if (flash?.warning) {
        warning(flash.warning)
      }
      
      if (flash?.info) {
        info(flash.info)
      }

      if (flash?.message) {
        info(flash.message)
      }
    }, 50) // Small delay to ensure toaster is ready
  },
  { immediate: true, deep: true }
)

// Always use light theme
const theme = 'light'
</script>

<template>
  <Toaster
    position="top-right"
    :theme="theme"
    :rich-colors="true"
    :close-button="true"
    :duration="4000"
  />
</template>

<style>
/* Import vue-sonner base styles */
@import 'vue-sonner/style.css';

/* Custom toast styling */
:root {
  --normal-bg: hsl(0 0% 100%);
  --normal-border: hsl(0 0% 92.8%);
  --normal-text: hsl(0 0% 3.9%);
  --normal-description: hsl(0 0% 45.1%);
  
  --success-bg: hsl(0 0% 100%);
  --success-border: hsl(142 76% 36%);
  --success-text: hsl(0 0% 3.9%);
  
  --error-bg: hsl(0 0% 100%);
  --error-border: hsl(0 84.2% 60.2%);
  --error-text: hsl(0 0% 3.9%);
  
  --warning-bg: hsl(0 0% 100%);
  --warning-border: hsl(43 74% 66%);
  --warning-text: hsl(0 0% 3.9%);
  
  --info-bg: hsl(0 0% 100%);
  --info-border: hsl(217.2 91.2% 59.8%);
  --info-text: hsl(0 0% 3.9%);
}

.dark {
  --normal-bg: hsl(0 0% 3.9%);
  --normal-border: hsl(0 0% 14.9%);
  --normal-text: hsl(0 0% 98%);
  --normal-description: hsl(0 0% 63.9%);
  
  --success-bg: hsl(0 0% 3.9%);
  --success-border: hsl(142 76% 36%);
  --success-text: hsl(0 0% 98%);
  
  --error-bg: hsl(0 0% 3.9%);
  --error-border: hsl(0 84% 60%);
  --error-text: hsl(0 0% 98%);
  
  --warning-bg: hsl(0 0% 3.9%);
  --warning-border: hsl(43 74% 66%);
  --warning-text: hsl(0 0% 98%);
  
  --info-bg: hsl(0 0% 3.9%);
  --info-border: hsl(217.2 91.2% 59.8%);
  --info-text: hsl(0 0% 98%);
}

/* Base toast styling */
[data-sonner-toaster] {
  z-index: 9999 !important;
}

[data-sonner-toast] {
  background: var(--normal-bg) !important;
  border: 1px solid var(--normal-border) !important;
  color: var(--normal-text) !important;
  border-radius: 8px !important;
  box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1) !important;
  padding: 12px 16px !important;
  font-family: inherit !important;
  font-size: 14px !important;
  line-height: 1.4 !important;
  max-width: 400px !important;
  pointer-events: auto !important;
}

[data-sonner-toast] [data-description] {
  color: var(--normal-description) !important;
  font-size: 13px !important;
  margin-top: 4px !important;
}

/* Success styling */
[data-sonner-toast][data-type="success"] {
  background: var(--success-bg) !important;
  border-color: var(--success-border) !important;
  color: var(--success-text) !important;
}

[data-sonner-toast][data-type="success"] [data-icon] {
  color: hsl(142 76% 36%) !important;
}

/* Error styling */
[data-sonner-toast][data-type="error"] {
  background: var(--error-bg) !important;
  border-color: var(--error-border) !important;
  color: var(--error-text) !important;
}

[data-sonner-toast][data-type="error"] [data-icon] {
  color: hsl(0 84.2% 60.2%) !important;
}

/* Warning styling */
[data-sonner-toast][data-type="warning"] {
  background: var(--warning-bg) !important;
  border-color: var(--warning-border) !important;
  color: var(--warning-text) !important;
}

[data-sonner-toast][data-type="warning"] [data-icon] {
  color: hsl(43 74% 66%) !important;
}

/* Info styling */
[data-sonner-toast][data-type="info"] {
  background: var(--info-bg) !important;
  border-color: var(--info-border) !important;
  color: var(--info-text) !important;
}

[data-sonner-toast][data-type="info"] [data-icon] {
  color: hsl(217.2 91.2% 59.8%) !important;
}

/* Loading styling */
[data-sonner-toast][data-type="loading"] {
  background: var(--normal-bg) !important;
  border-color: var(--normal-border) !important;
  color: var(--normal-text) !important;
}

[data-sonner-toast][data-type="loading"] [data-icon] {
  color: hsl(217.2 91.2% 59.8%) !important;
}

/* Close button styling */
[data-sonner-toast] [data-close-button] {
  background: transparent !important;
  border: none !important;
  color: var(--normal-description) !important;
  font-size: 16px !important;
  padding: 4px !important;
  border-radius: 4px !important;
  opacity: 0.7 !important;
  transition: opacity 0.2s ease !important;
}

[data-sonner-toast] [data-close-button]:hover {
  opacity: 1 !important;
  background: hsl(0 0% 0% / 0.1) !important;
}

.dark [data-sonner-toast] [data-close-button]:hover {
  background: hsl(0 0% 100% / 0.1) !important;
}

/* Animation improvements */
[data-sonner-toast][data-mounted="true"] {
  animation: toast-slide-in 0.3s ease-out forwards !important;
}

[data-sonner-toast][data-removed="true"] {
  animation: toast-slide-out 0.2s ease-in forwards !important;
}

@keyframes toast-slide-in {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

@keyframes toast-slide-out {
  from {
    transform: translateX(0);
    opacity: 1;
  }
  to {
    transform: translateX(100%);
    opacity: 0;
  }
}
</style>