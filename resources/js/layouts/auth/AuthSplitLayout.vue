<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { home } from '@/routes';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();
const name = page.props.name;
const quote = page.props.quote;

defineProps<{
 title?: string;
 description?: string;
}>();
</script>

<template>
 <div class="relative grid h-dvh flex-col items-center justify-center px-8 sm:px-0 lg:max-w-none lg:grid-cols-2lg:px-0">
 <div class="relative hidden h-full flex-col bg-muted p-10 lg:flexborder-r">
 <div class="absolute inset-0bg-zinc-900" />
 <Link :href="home()" class="relative z-20 flex items-center text-lgfont-medium">
 <AppLogoIcon class="mr-2 size-8 fill-current" />
 {{ name }}
 </Link>
 <div v-if="quote" class="relative z-20mt-auto">
 <blockquote class="space-y-2">
 <p class="text-lg">&ldquo;{{ quote.message }}&rdquo;</p>
 <footer class="text-smtext-neutral-300">{{ quote.author }}</footer>
 </blockquote>
 </div>
 </div>
 <div class="lg:p-8">
 <div class="mx-auto flex w-full flex-col justify-center space-y-6sm:w-[350px]">
 <div class="flex flex-col space-y-2text-center">
 <h1 class="text-xl font-mediumtracking-tight" v-if="title">{{ title }}</h1>
 <p class="text-smtext-muted-foreground" v-if="description">{{ description }}</p>
 </div>
 <slot />
 </div>
 </div>
 </div>
</template>
