<script setup lang="ts">
interface Props {
 class?: string
}

const props = withDefaults(defineProps<Props>(), {
 class: ''
})
</script>

<template>
 <td
 :class="[
 'p-4 align-middle [&:has([role=checkbox])]:pr-0',
 props.class
 ]"
 >
 <slot />
 </td>
</template>