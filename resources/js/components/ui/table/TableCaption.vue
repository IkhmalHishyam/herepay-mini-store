<script setup lang="ts">
interface Props {
 class?: string
}

const props = withDefaults(defineProps<Props>(), {
 class: ''
})
</script>

<template>
 <caption
 :class="[
 'mt-4 text-sm text-muted-foreground',
 props.class
 ]"
 >
 <slot />
 </caption>
</template>