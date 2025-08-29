<script setup lang="ts">
interface Props {
 class?: string
}

const props = withDefaults(defineProps<Props>(), {
 class: ''
})
</script>

<template>
 <tbody
 :class="[
 '[&_tr:last-child]:border-0',
 props.class
 ]"
 >
 <slot />
 </tbody>
</template>