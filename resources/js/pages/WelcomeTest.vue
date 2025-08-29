<template>
 <Head title="Test Page" />
 <div class="min-h-screenbg-gray-50">
 <h1>Test Page Working</h1>
 <p>Products count: {{ products.length }}</p>
 <div v-for="product in products" :key="product.id">
 <p>{{ product.name }} - RM{{ product.price }}</p>
 </div>
 </div>
</template>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3'

interface Product {
 id: string
 name: string
 price: number
}

interface Props {
 products: Product[]
}

const props = defineProps<Props>()
console.log('Test page loaded with products:', props.products)
</script>