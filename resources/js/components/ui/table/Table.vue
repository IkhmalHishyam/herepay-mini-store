<script setup lang="ts">
interface Props {
 class?: string
}

const props = withDefaults(defineProps<Props>(), {
 class: ''
})
</script>

<template>
 <table
 :class="[
 'w-full caption-bottom text-sm min-w-full',
 props.class
 ]"
 >
 <slot />
 </table>
</template>