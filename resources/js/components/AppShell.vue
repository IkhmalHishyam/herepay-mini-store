<script setup lang="ts">
import { SidebarProvider } from '@/components/ui/sidebar';
import ToastProvider from '@/components/ToastProvider.vue';
import { usePage } from '@inertiajs/vue3';

interface Props {
  variant?: 'header' | 'sidebar';
}

defineProps<Props>();

const isOpen = usePage().props.sidebarOpen;
</script>

<template>
  <div v-if="variant === 'header'" class="flex min-h-screen w-full flex-col">
    <slot />
    <ToastProvider />
  </div>
  <SidebarProvider v-else :default-open="isOpen">
    <slot />
    <ToastProvider />
  </SidebarProvider>
</template>
