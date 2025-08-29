<script setup lang="ts">
import { ShoppingCart } from 'lucide-vue-next';
import type { HTMLAttributes } from 'vue';

defineOptions({
  inheritAttrs: false,
});

interface Props {
  className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
  <ShoppingCart :class="className" v-bind="$attrs" />
</template>
