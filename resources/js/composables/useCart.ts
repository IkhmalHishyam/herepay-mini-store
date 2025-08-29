import { ref, computed, reactive } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'

interface CartItem {
    id: string
    name: string
    sku_matrix: string | null
    price_per_unit: number
    quantity: number
    total_price: number
    product: {
        id: string
        name: string
        thumbnail: string | null
        images: string[]
    }
    added_at: string
    updated_at: string
}

interface Cart {
    id: string
    items: CartItem[]
    items_count: number
    total_price: number
    created_at: string
    updated_at: string
}

interface CartSummary {
    items_count: number
    total_price: number
    has_items: boolean
}

const cart = ref<Cart | null>(null)
const cartSummary = ref<CartSummary>({ items_count: 0, total_price: 0, has_items: false })
const isLoading = ref(false)
const lastError = ref<string | null>(null)

export const useCart = () => {
    const addToCart = async (productId: string, quantity: number = 1, skuMatrix?: string) => {
        isLoading.value = true
        lastError.value = null
        
        try {
            const response = await axios.post('/cart/add', {
                product_id: productId,
                quantity,
                sku_matrix: skuMatrix
            })

            if (response.data.success) {
                await refreshCart()
                return {
                    success: true,
                    message: response.data.message
                }
            } else {
                throw new Error(response.data.message || 'Failed to add to cart')
            }
        } catch (error: any) {
            lastError.value = error.response?.data?.message || error.message || 'Failed to add to cart'
            return {
                success: false,
                message: lastError.value
            }
        } finally {
            isLoading.value = false
        }
    }

    const updateCartItem = async (productId: string, quantity: number, skuMatrix?: string) => {
        isLoading.value = true
        lastError.value = null
        
        try {
            const response = await axios.put('/cart/update', {
                product_id: productId,
                quantity,
                sku_matrix: skuMatrix
            })

            if (response.data.success) {
                await refreshCart()
                return {
                    success: true,
                    message: response.data.message
                }
            } else {
                throw new Error(response.data.message || 'Failed to update cart')
            }
        } catch (error: any) {
            lastError.value = error.response?.data?.message || error.message || 'Failed to update cart'
            return {
                success: false,
                message: lastError.value
            }
        } finally {
            isLoading.value = false
        }
    }

    const removeFromCart = async (productId: string, skuMatrix?: string) => {
        isLoading.value = true
        lastError.value = null
        
        try {
            const response = await axios.delete('/cart/item', {
                data: {
                    product_id: productId,
                    sku_matrix: skuMatrix
                }
            })

            if (response.data.success) {
                await refreshCart()
                return {
                    success: true,
                    message: response.data.message
                }
            } else {
                throw new Error(response.data.message || 'Failed to remove from cart')
            }
        } catch (error: any) {
            lastError.value = error.response?.data?.message || error.message || 'Failed to remove from cart'
            return {
                success: false,
                message: lastError.value
            }
        } finally {
            isLoading.value = false
        }
    }

    const clearCart = async () => {
        isLoading.value = true
        lastError.value = null
        
        try {
            const response = await axios.delete('/cart/clear')

            if (response.data.success) {
                await refreshCart()
                return {
                    success: true,
                    message: response.data.message
                }
            } else {
                throw new Error(response.data.message || 'Failed to clear cart')
            }
        } catch (error: any) {
            lastError.value = error.response?.data?.message || error.message || 'Failed to clear cart'
            return {
                success: false,
                message: lastError.value
            }
        } finally {
            isLoading.value = false
        }
    }

    const refreshCart = async () => {
        try {
            const response = await axios.get('/cart')
            
            if (response.data.success) {
                cart.value = response.data.cart
                
                // Update summary
                if (response.data.cart) {
                    cartSummary.value = {
                        items_count: response.data.cart.items_count,
                        total_price: response.data.cart.total_price,
                        has_items: response.data.cart.items_count > 0
                    }
                } else {
                    cartSummary.value = { items_count: 0, total_price: 0, has_items: false }
                }
            }
        } catch (error: any) {
            // Silently fail for cart refresh
            console.error('Failed to refresh cart:', error.response?.data?.message || error.message)
        }
    }

    const refreshCartSummary = async () => {
        try {
            const response = await axios.get('/cart/summary')
            
            if (response.data.success) {
                cartSummary.value = response.data.summary
            }
        } catch (error: any) {
            // Silently fail for summary refresh
            console.error('Failed to refresh cart summary:', error.response?.data?.message || error.message)
        }
    }

    const formatPrice = (price: number) => {
        return price.toFixed(2)
    }

    // Computed properties
    const cartItemsCount = computed(() => cartSummary.value.items_count)
    const cartTotal = computed(() => cartSummary.value.total_price)
    const hasCartItems = computed(() => cartSummary.value.has_items)

    const isItemInCart = (productId: string, skuMatrix?: string) => {
        if (!cart.value) return false
        
        return cart.value.items.some(item => 
            item.product.id === productId && 
            (item.sku_matrix === skuMatrix || (!item.sku_matrix && !skuMatrix))
        )
    }

    const getCartItemQuantity = (productId: string, skuMatrix?: string) => {
        if (!cart.value) return 0
        
        const item = cart.value.items.find(item => 
            item.product.id === productId && 
            (item.sku_matrix === skuMatrix || (!item.sku_matrix && !skuMatrix))
        )
        
        return item ? item.quantity : 0
    }

    // Initialize cart on first use
    const initializeCart = async () => {
        await refreshCartSummary()
    }

    return {
        // State
        cart,
        cartSummary,
        isLoading,
        lastError,
        
        // Actions
        addToCart,
        updateCartItem,
        removeFromCart,
        clearCart,
        refreshCart,
        refreshCartSummary,
        initializeCart,
        
        // Computed
        cartItemsCount,
        cartTotal,
        hasCartItems,
        
        // Helpers
        isItemInCart,
        getCartItemQuantity,
        formatPrice
    }
}