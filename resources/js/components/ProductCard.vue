<template>
 <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200">
 <div class="aspect-square relative">
 <img
 v-if="product.product_thumbnail?.file_url"
 :src="product.product_thumbnail.file_url"
 :alt="product.name"
 class="w-full h-full object-cover"
 />
 <div v-else class="flex items-center justify-center w-full h-full bg-gray-100">
 <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
 </svg>
 </div>
 
 <div class="absolute top-2 left-2">
 <span
 :class="{
 'bg-green-100 text-green-800': product.stock_status === 'in_stock',
 'bg-yellow-100 text-yellow-800': product.stock_status === 'low_stock',
 'bg-red-100 text-red-800': product.stock_status === 'out_of_stock'
 }"
 class="px-2 py-1 text-xs font-medium rounded-full"
 >
 {{ stockStatusText }}
 </span>
 </div>
 </div>
 
 <div class="p-4">
 <div class="mb-3">
 <h3 class="text-lg font-medium text-gray-900 line-clamp-1">
 {{ product.name }}
 </h3>
 </div>
 
 <div class="flex items-center justify-between mb-3">
 <div class="text-xl font-bold text-gray-900">
 RM{{ formatPrice(product.price) }}
 </div>
 <div class="text-sm text-gray-500">
 Stock: {{ product.total_stock }}
 </div>
 </div>
 
 <div v-if="product.product_categories && product.product_categories.length > 0" class="flex flex-wrap gap-1 mb-3">
 <span
 v-for="category in product.product_categories.slice(0, 2)"
 :key="category.id"
 class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full"
 >
 {{ category.name }}
 </span>
 <span v-if="product.product_categories.length > 2" class="px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded-full">
 +{{ product.product_categories.length - 2 }} more
 </span>
 </div>
 
 <!-- Variant Selection -->
 <div v-if="product.variant_groups && product.variant_groups.length > 0" class="mb-4 space-y-3">
 <div v-for="variantGroup in product.variant_groups" :key="variantGroup.id">
 <label class="block text-sm font-medium text-gray-700 mb-2">
 {{ variantGroup.name }}
 </label>
 <select
 v-model="selectedVariants[variantGroup.id]"
 @change="updateSelectedSku"
 class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
 >
 <option value="">Select {{ variantGroup.name }}</option>
 <option 
 v-for="variant in variantGroup.variants" 
 :key="variant.id"
 :value="variant.id"
 >
 {{ variant.name }}
 </option>
 </select>
 </div>
 
 <!-- Selected SKU Info -->
 <div v-if="selectedSku" class="text-xs text-gray-600 bg-blue-50 p-2 rounded">
 <div class="font-medium">Selected: {{ formatSkuMatrix(selectedSku.matrix) }}</div>
 <div class="flex justify-between mt-1">
 <span>Price: RM{{ formatPrice(selectedSku.price) }}</span>
 <span>Stock: {{ selectedSku.total_stock }}</span>
 </div>
 </div>
 </div>
 
 <!-- Quantity Selection -->
 <div v-if="canShowQuantity" class="mb-4">
 <label class="block text-sm font-medium text-gray-700 mb-2">
 Quantity
 </label>
 <input
 v-model.number="quantity"
 type="number"
 min="1"
 :max="getAvailableStock"
 class="w-20 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
 />
 </div>
 
 <div class="flex gap-2">
 <button
 @click="handleAddToCart"
 :disabled="!canAddToCart || isAddingToCart"
 :class="{
 'bg-blue-600 hover:bg-blue-700': canAddToCart && !isAddingToCart,
 'bg-green-600': isInCart,
 'bg-gray-300 text-gray-500 cursor-not-allowed': !canAddToCart || isAddingToCart
 }"
 class="flex-1 text-sm font-medium py-3 px-4 rounded-md transition-colors relative"
 >
 <span v-if="isAddingToCart" class="flex items-center justify-center">
 <div class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent mr-2"></div>
 Adding...
 </span>
 <span v-else-if="isInCart && cartQuantity > 0">
 In Cart ({{ cartQuantity }})
 </span>
 <span v-else-if="!product.is_in_stock">
 Out of Stock
 </span>
 <span v-else-if="hasVariants && !hasRequiredVariants">
 Select Options
 </span>
 <span v-else>
 Add to Cart ({{ quantity }})
 </span>
 </button>
 </div>
 </div>
 </div>
</template>

<script setup lang="ts">
import { computed, ref, onMounted } from 'vue'
import { useCart } from '@/composables/useCart'
import { useToast } from '@/composables/useToast'
import { usePage } from '@inertiajs/vue3'

interface Product {
 id: string
 name: string
 description: string
 price: number
 total_stock: number
 is_published: boolean
 created_at: string
 updated_at: string
 product_thumbnail?: {
 id: string
 file_name: string
 file_path: string
 file_url: string
 }
 product_images?: Array<{
 id: string
 file_name: string
 file_path: string
 file_url: string
 }>
 product_categories?: Array<{
 id: string
 name: string
 }>
 product_tags?: Array<{
 id: string
 name: string
 }>
 variant_groups?: Array<{
 id: string
 name: string
 is_active: boolean
 variants: Array<{
 id: string
 name: string
 description: string
 price: number
 total_stock: number
 }>
 }>
 skus?: Array<{
 id: string
 matrix: string
 price: number
 total_stock: number
 }>
 has_variants: boolean
 variant_count: number
 is_in_stock: boolean
 stock_status: 'in_stock' | 'low_stock' | 'out_of_stock'
}

const props = defineProps<{
 product: Product
}>()

const page = usePage()
const isAuthenticated = computed(() => !!page.props.auth.user)

// Toast functionality
const { success, error } = useToast()

// Cart functionality
const {
 addToCart,
 isItemInCart,
 getCartItemQuantity,
 refreshCartSummary,
 formatPrice: formatCartPrice
} = useCart()

const isAddingToCart = ref(false)

// Variant selection state
const selectedVariants = ref<Record<string, string>>({})
const selectedSku = ref<any>(null) // Will hold the matching SKU from product.skus
const quantity = ref(1)

// Initialize cart on component mount if user is authenticated
onMounted(() => {
 if (isAuthenticated.value) {
 refreshCartSummary()
 }
})

const stockStatusText = computed(() => {
 switch (props.product.stock_status) {
 case 'in_stock':
 return 'In Stock'
 case 'low_stock':
 return 'Low Stock'
 case 'out_of_stock':
 return 'Out of Stock'
 default:
 return 'Unknown'
 }
})

const formatPrice = (price: number) => {
 return price.toFixed(2)
}

// Helper function to format SKU matrix
const formatSkuMatrix = (matrix: string) => {
 const parts = matrix.split(':')
 if (parts.length > 1) {
 return parts.slice(1).join(' / ')
 }
 return matrix
}

// Variant selection logic
const hasVariants = computed(() => {
 return props.product.variant_groups && props.product.variant_groups.length > 0
})

const hasRequiredVariants = computed(() => {
 if (!hasVariants.value) return true
 return props.product.variant_groups!.every(vg => selectedVariants.value[vg.id])
})

const getAvailableStock = computed(() => {
 if (selectedSku.value) {
 return selectedSku.value.total_stock
 }
 return props.product.total_stock
})

const canShowQuantity = computed(() => {
 return props.product.is_in_stock && (!hasVariants.value || hasRequiredVariants.value)
})

// Update selected SKU when variants change
const updateSelectedSku = () => {
 if (!hasVariants.value || !props.product.skus) {
 selectedSku.value = null
 return
 }

 if (!hasRequiredVariants.value) {
 selectedSku.value = null
 return
 }

 // Find matching SKU based on selected variants
 const selectedVariantNames = Object.values(selectedVariants.value).map(variantId => {
 for (const vg of props.product.variant_groups || []) {
 const variant = vg.variants.find(v => v.id === variantId)
 if (variant) return variant.name
 }
 return null
 }).filter(Boolean)

 const matchingSku = props.product.skus.find(sku => {
 const skuVariants = sku.matrix.split(':').slice(1) // Remove product name part
 return skuVariants.length === selectedVariantNames.length &&
 skuVariants.every(sv => selectedVariantNames.includes(sv))
 })

 selectedSku.value = matchingSku || null
}

// Cart computed properties
const canAddToCart = computed(() => {
 if (!isAuthenticated.value || !props.product.is_in_stock) return false
 if (hasVariants.value && !hasRequiredVariants.value) return false
 if (selectedSku.value && selectedSku.value.total_stock <= 0) return false
 return true
})

const isInCart = computed(() => {
 const skuMatrix = selectedSku.value ? selectedSku.value.matrix : null
 return isAuthenticated.value && isItemInCart(props.product.id, skuMatrix)
})

const cartQuantity = computed(() => {
 const skuMatrix = selectedSku.value ? selectedSku.value.matrix : null
 return isAuthenticated.value ? getCartItemQuantity(props.product.id, skuMatrix) : 0
})

const handleAddToCart = async () => {
 if (!canAddToCart.value || isAddingToCart.value) return
 
 if (!isAuthenticated.value) {
 // Redirect to login if not authenticated
 window.location.href = '/login'
 return
 }
 
 isAddingToCart.value = true
 
 try {
 const skuMatrix = selectedSku.value ? selectedSku.value.matrix : undefined
 const result = await addToCart(props.product.id, quantity.value, skuMatrix)
 
 if (result.success) {
 // Show success toast
 const variantText = selectedSku.value ? ` (${formatSkuMatrix(selectedSku.value.matrix)})` : ''
 const quantityText = quantity.value > 1 ? ` (${quantity.value})` : ''
 success(`Added ${props.product.name}${variantText}${quantityText} to cart!`)
 
 // Reset quantity after successful add
 quantity.value = 1
 } else {
 // Show error toast
 error(result.message)
 }
 } catch (error: any) {
 // Show error toast for exceptions
 error(error.message || 'Failed to add item to cart')
 } finally {
 isAddingToCart.value = false
 }
}
</script>

<style scoped>
.line-clamp-1 {
 display: -webkit-box;
 -webkit-line-clamp: 1;
 -webkit-box-orient: vertical;
 overflow: hidden;
}

.line-clamp-2 {
 display: -webkit-box;
 -webkit-line-clamp: 2;
 -webkit-box-orient: vertical;
 overflow: hidden;
}
</style>