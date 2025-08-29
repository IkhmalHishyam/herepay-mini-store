<template>
 <div class="relative">
 <button
 @click="toggleCart"
 class="relative p-2 text-gray-700 hover:text-gray-900 transition-colors"
 >
 <ShoppingCart class="h-6 w-6" />
 <span
 v-if="cartItemsCount > 0"
 class="absolute -top-2 -right-2 inline-flex items-center justify-center min-w-[20px] h-5 text-xs font-bold leading-none text-white bg-red-500 rounded-full shadow-sm border-2 border-white"
 >
 {{ cartItemsCount }}
 </span>
 </button>
 
 <!-- Mini Cart Dropdown -->
 <div
 v-if="showCart"
 class="absolute right-0 mt-2 w-80 bg-white border border-gray-200 rounded-lg shadow-lg z-50"
 >
 <div class="p-4">
 <h3 class="text-lg font-semibold text-gray-900 mb-4">
 Shopping Cart
 </h3>
 
 <!-- Loading State -->
 <div v-if="isLoading" class="flex justify-center py-4">
 <div class="animate-spin rounded-full h-6 w-6 border-2 border-blue-600 border-t-transparent"></div>
 </div>
 
 <!-- Empty Cart -->
 <div v-else-if="!cart || cartItemsCount === 0" class="text-center py-8 text-gray-500">
 <ShoppingCart class="h-12 w-12 mx-auto mb-4 opacity-50" />
 <p>Your cart is empty</p>
 </div>
 
 <!-- Cart Items -->
 <div v-else class="space-y-3 max-h-64 overflow-y-auto">
 <div
 v-for="item in cart.items.slice(0, 3)"
 :key="`${item.product.id}-${item.sku_matrix || 'default'}`"
 class="flex items-center space-x-3 py-2"
 >
 <img
 v-if="item.product.thumbnail"
 :src="item.product.thumbnail"
 :alt="item.product.name"
 class="h-10 w-10 object-coverrounded"
 />
 <Package v-else class="h-10 w-10" />
 
 <div class="flex-1 min-w-0">
 <p class="text-sm font-medium text-gray-900 truncate">
 {{ item.name }}
 </p>
 <p class="text-xs text-gray-500">
 RM{{ formatPrice(item.price_per_unit) }} each
 </p>
 </div>
 
 <div class="flex items-center space-x-2">
 <div class="flex items-center space-x-1">
 <button
 @click="updateQuantity(item, item.quantity - 1)"
 :disabled="isLoading || item.quantity <= 1"
 class="w-6 h-6 flex items-center justify-center bg-gray-200 text-gray-700 hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed rounded text-xs"
 >
 -
 </button>
 <span class="w-8 text-center text-xs font-medium">{{ item.quantity }}</span>
 <button
 @click="updateQuantity(item, item.quantity + 1)"
 :disabled="isLoading"
 class="w-6 h-6 flex items-center justify-center bg-gray-200 text-gray-700 hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed rounded text-xs"
 >
 +
 </button>
 </div>
 <button
 @click="handleRemoveItem(item)"
 :disabled="isLoading"
 class="text-red-500 hover:text-red-700 disabled:opacity-50"
 >
 <X class="h-4 w-4" />
 </button>
 </div>
 </div>
 
 <div v-if="cart.items.length > 3" class="text-center py-2">
 <p class="text-sm text-gray-500">
 +{{ cart.items.length - 3 }} more items
 </p>
 </div>
 </div>
 
 <!-- Cart Summary -->
 <div v-if="cart && cartItemsCount > 0" class="border-t border-gray-200 pt-4 mt-4">
 <div class="flex justify-between items-center mb-4">
 <span class="font-semibold text-gray-900">Total:</span>
 <span class="font-bold text-blue-600">
 RM{{ formatPrice(cart.total_price) }}
 </span>
 </div>
 
 <div class="space-y-2">
 <button
 @click="scrollToCart"
 class="w-full bg-blue-600 hover:bg-blue-700 py-2 px-4 rounded-md text-sm font-medium transition-colors"
 >
 View Cart
 </button>
 <button
 @click="handleClearCart"
 :disabled="isLoading"
 class="w-full bg-gray-500 disabled:bg-gray-300 py-2 px-4 rounded-md text-sm font-medium transition-colors"
 >
 <span v-if="isLoading">Clearing...</span>
 <span v-else>Clear Cart</span>
 </button>
 </div>
 </div>
 </div>
 </div>
 
 <!-- Overlay -->
 <div
 v-if="showCart"
 @click="closeCart"
 class="fixed inset-0 z-40"
 ></div>
 </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import { ShoppingCart, Package, X } from 'lucide-vue-next'
import { useCart } from '@/composables/useCart'
import { useToast } from '@/composables/useToast'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const showCart = ref(false)

// Toast functionality
const { success, error } = useToast()

const {
 cart,
 cartItemsCount,
 removeFromCart,
 updateCartItem,
 clearCart: clearCartAction,
 refreshCart,
 initializeCart,
 isLoading,
 formatPrice
} = useCart()

// Initialize cart on mount if user is authenticated
onMounted(() => {
 if (page.props.auth.user) {
 initializeCart()
 refreshCart()
 }
})

const toggleCart = () => {
 showCart.value = !showCart.value
}

const closeCart = () => {
 showCart.value = false
}

const scrollToCart = () => {
 const cartElement = document.querySelector('[data-cart-section]')
 if (cartElement) {
 cartElement.scrollIntoView({ behavior: 'smooth', block: 'start' })
 }
 closeCart()
}

const handleRemoveItem = async (item: any) => {
 try {
 const result = await removeFromCart(item.product.id, item.sku_matrix)
 if (result.success) {
 const variantText = item.sku_matrix ? ` (${item.sku_matrix.split(':').slice(1).join(' / ')})` : ''
 success(`Removed ${item.name}${variantText} from cart`)
 } else {
 error(result.message)
 }
 } catch (err: any) {
 error(err.message || 'Failed to remove item from cart')
 }
}

const handleClearCart = async () => {
 try {
 const result = await clearCartAction()
 if (result.success) {
 success('Cart cleared successfully')
 closeCart()
 } else {
 error(result.message)
 }
 } catch (err: any) {
 error(err.message || 'Failed to clear cart')
 }
}

const updateQuantity = async (item: any, newQuantity: number) => {
 if (newQuantity < 1) return
 
 try {
 const result = await updateCartItem(item.product.id, newQuantity, item.sku_matrix)
 if (result.success) {
 const variantText = item.sku_matrix ? ` (${item.sku_matrix.split(':').slice(1).join(' / ')})` : ''
 success(`Updated ${item.name}${variantText} quantity to ${newQuantity}`)
 } else {
 error(result.message)
 }
 } catch (err: any) {
 error(err.message || 'Failed to update quantity')
 }
}

// Close cart on escape key
const handleEscape = (e: KeyboardEvent) => {
 if (e.key === 'Escape' && showCart.value) {
 closeCart()
 }
}

onMounted(() => {
 document.addEventListener('keydown', handleEscape)
})

onUnmounted(() => {
 document.removeEventListener('keydown', handleEscape)
})
</script>

<style scoped>
/* Custom scrollbar for cart items */
.overflow-y-auto::-webkit-scrollbar {
 width: 4px;
}

.overflow-y-auto::-webkit-scrollbar-track {
 background: transparent;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
 background-color: rgba(156, 163, 175, 0.5);
 border-radius: 2px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
 background-color: rgba(156, 163, 175, 0.7);
}
</style>