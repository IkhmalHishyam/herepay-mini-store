<script setup lang="ts">
interface Props {
 class?: string
}

const props = withDefaults(defineProps<Props>(), {
 class: ''
})
</script>

<template>
 <tr
 :class="[
 'border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted',
 props.class
 ]"
 >
 <slot />
 </tr>
</template>