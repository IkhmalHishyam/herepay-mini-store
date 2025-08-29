<script setup lang="ts">
interface Props {
 class?: string
}

const props = withDefaults(defineProps<Props>(), {
 class: ''
})
</script>

<template>
 <thead
 :class="[
 '[&_tr]:border-b',
 props.class
 ]"
 >
 <slot />
 </thead>
</template>