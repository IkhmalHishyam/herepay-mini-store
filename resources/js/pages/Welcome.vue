<template>
  <Head title="Herepay Mini Store">
    <link rel="preconnect" href="https://rsms.me/" />
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
  </Head>
  
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <div class="flex items-center">
            <h1 class="text-2xl font-bold text-gray-900">
              Herepay Mini Store
            </h1>
          </div>
          
          <nav class="flex items-center space-x-4">
            <CartIcon v-if="isAuthenticated" />
            <template v-if="$page.props.auth.user">
              <Link
                :href="dashboard()"
                class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium"
              >
                Dashboard
              </Link>
              <Link
                :href="logout()"
                method="post"
                as="button"
                class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded-md text-sm font-medium"
              >
                Logout
              </Link>
            </template>
            <template v-else>
              <Link
                :href="login()"
                class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium"
              >
                Login
              </Link>
              <Link
                :href="register()"
                class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-md text-sm font-medium text-white"
              >
                Register
              </Link>
            </template>
          </nav>
        </div>
      </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
             <!-- Search and Filters -->
       <div class="mb-12 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
         <div class="flex flex-col lg:flex-row gap-6">
           <!-- Search Bar -->
           <div class="flex-1">
             <div class="relative">
               <Search v-if="!isSearching" class="absolute left-4 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" />
               <div v-else class="absolute left-4 top-1/2 transform -translate-y-1/2">
                 <div class="animate-spin rounded-full h-5 w-5 border-2 border-gray-300 border-t-blue-600"></div>
               </div>
               <input
                 v-model="searchQuery"
                 type="text"
                 placeholder="Search products... (min 2 characters)"
                 :class="{
                   'border-amber-300 focus:ring-amber-500 focus:border-amber-500': searchQuery.length > 0 && searchQuery.length < 2,
                   'border-gray-300 focus:ring-blue-500 focus:border-blue-500': !(searchQuery.length > 0 && searchQuery.length < 2)
                 }"
                 class="w-full pl-12 pr-12 py-4 border-2 rounded-xl transition-all text-base focus:outline-none"
                 @input="debouncedSearch"
               />
               <!-- Clear search button -->
               <button
                 v-if="searchQuery"
                 @click="clearSearch"
                 class="absolute right-4 top-1/2 transform -translate-y-1/2 hover:text-gray-600 transition-colors p-1 rounded-full hover:bg-gray-100"
               >
                 <X class="h-4 w-4" />
               </button>
             </div>
             <!-- Search helper text -->
             <div v-if="searchQuery.length > 0 && searchQuery.length < 2" class="mt-2">
               <p class="text-sm text-amber-600 bg-amber-50 px-3 py-2 rounded-lg border border-amber-200">
                 Please enter at least 2 characters to search
               </p>
             </div>
           </div>
          
                     <!-- Sort Dropdown -->
           <div class="w-full lg:w-48">
             <select
               v-model="sortBy"
               class="w-full px-4 py-4 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-base focus:outline-none bg-white"
               @change="applyFilters"
             >
               <option value="">Sort by</option>
               <option value="newest">Newest First</option>
               <option value="oldest">Oldest First</option>
               <option value="price_asc">Price: Low to High</option>
               <option value="price_desc">Price: High to Low</option>
               <option value="name_asc">Name: A-Z</option>
               <option value="name_desc">Name: Z-A</option>
             </select>
           </div>
           
           <!-- Filter Toggle Button -->
           <button
             @click="showFilters = !showFilters"
             class="flex items-center justify-center px-6 py-4 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all duration-200 transform hover:scale-[1.02] shadow-md"
           >
             <Filter class="h-5 w-5 mr-2" />
             Filters
             <ChevronDown :class="{ 'rotate-180': showFilters}" class="ml-2 h-4 w-4 transition-transform" />
           </button>
        </div>
        
                 <!-- Filter Panel -->
         <div v-if="showFilters" class="mt-8 p-8 bg-white rounded-xl border border-gray-200 shadow-lg">
           <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Category Filter -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Categories
              </label>
                           <div class="space-y-3 max-h-32 overflow-y-auto p-2 bg-gray-50 rounded-lg">
               <label v-for="category in filters.categories" :key="category.id" class="flex items-center p-2 hover:bg-white rounded-md transition-colors cursor-pointer">
                 <input
                   v-model="selectedCategories"
                   :value="category.id"
                   type="checkbox"
                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 h-4 w-4"
                   @change="applyFilters"
                 />
                 <span class="ml-3 text-sm text-gray-900 font-medium">{{ category.name }}</span>
               </label>
             </div>
            </div>
            
            <!-- Tags Filter -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Tags
              </label>
                           <div class="space-y-3 max-h-32 overflow-y-auto p-2 bg-gray-50 rounded-lg">
               <label v-for="tag in filters.tags" :key="tag.id" class="flex items-center p-2 hover:bg-white rounded-md transition-colors cursor-pointer">
                 <input
                   v-model="selectedTags"
                   :value="tag.id"
                   type="checkbox"
                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 h-4 w-4"
                   @change="applyFilters"
                 />
                 <span class="ml-3 text-sm text-gray-900 font-medium">{{ tag.name }}</span>
               </label>
             </div>
            </div>
            
            <!-- Price Range -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Price Range (RM)
              </label>
                             <div class="space-y-3">
                 <input
                   v-model.number="minPrice"
                   type="number"
                   placeholder="Min price"
                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base transition-all focus:outline-none"
                   @input="debouncedFilter"
                 />
                 <input
                   v-model.number="maxPrice"
                   type="number"
                   placeholder="Max price"
                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base transition-all focus:outline-none"
                   @input="debouncedFilter"
                 />
               </div>
            </div>
            
            <!-- Stock Status -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Stock Status
              </label>
                             <select
                 v-model="stockStatus"
                 class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base transition-all focus:outline-none"
                 @change="applyFilters"
               >
                <option value="">All</option>
                <option value="in_stock">In Stock</option>
                <option value="low_stock">Low Stock</option>
                <option value="out_of_stock">Out of Stock</option>
              </select>
            </div>
          </div>
          
                     <!-- Clear Filters -->
           <div class="mt-8 flex justify-end">
             <button
               @click="clearFilters"
               class="px-6 py-3 text-sm text-gray-600 hover:text-gray-800 bg-gray-100 hover:bg-gray-200 rounded-lg transition-all duration-200 font-medium"
             >
               Clear All Filters
             </button>
           </div>
        </div>
      </div>

               <!-- Products Section -->
         <div class="mb-16">
           <div class="flex justify-between items-center mb-8">
             <h2 class="text-3xl font-bold text-gray-900">Our Products</h2>
             <div v-if="products.data" class="text-sm text-gray-600 bg-gray-100 px-3 py-2 rounded-lg">
               Showing {{ products.from || 0 }}-{{ products.to || 0 }} of {{ products.total || 0 }} products
             </div>
           </div>
           
           <!-- Product Grid -->
           <div v-if="products && products.data && products.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
             <ProductCard
               v-for="product in products.data"
               :key="product.id"
               :product="product"
             />
           </div>
        
        <!-- No Products -->
        <div v-else class="text-center py-12">
          <Package class="mx-auto h-16 w-16 mb-4" />
          <h3 class="text-lg font-medium text-gray-900 mb-2">No products available</h3>
          <p class="text-gray-600">Check back later for new products.</p>
        </div>
        
        <!-- Pagination -->
        <div v-if="products && products.data && products.last_page > 1" class="mt-8 flex justify-center">
          <nav class="flex items-center space-x-2">
            <!-- Previous Page -->
            <button
              @click="goToPage(products.current_page - 1)"
              :disabled="products.current_page <= 1"
              class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <ChevronLeft class="h-4 w-4" />
            </button>
            
            <!-- Page Numbers -->
            <template v-for="page in getPageNumbers()" :key="page">
              <button
                v-if="page === '...'"
                disabled
                class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md"
              >
                ...
              </button>
              <button
                v-else
                @click="goToPage(page)"
                :class="{
                  'bg-blue-600 border-blue-600': page === products.current_page,
                  'text-gray-500 bg-white border-gray-300 hover:bg-gray-50': page !== products.current_page
                }"
                class="px-3 py-2 text-sm font-medium border rounded-md"
              >
                {{ page }}
              </button>
            </template>
            
            <!-- Next Page -->
            <button
              @click="goToPage(products.current_page + 1)"
              :disabled="products.current_page >= products.last_page"
              class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <ChevronRight class="h-4 w-4" />
            </button>
          </nav>
        </div>
      </div>
      
             <!-- Shopping Cart & Order Form -->
       <div v-if="hasCartItems && isAuthenticated" data-cart-section class="bg-white rounded-lg shadow-lg border border-gray-100 p-8">
         <h3 class="text-2xl font-bold text-gray-900 mb-8">Your Order</h3>
        
                 <!-- Cart Items -->
         <div class="mb-8">
           <h4 class="text-lg font-semibold text-gray-900 mb-6">Items</h4>
           <div v-if="cart" class="space-y-4">
             <div 
               v-for="item in cart.items" 
               :key="`${item.product.id}-${item.sku_matrix || 'default'}`"
               class="flex items-center justify-between p-6 border border-gray-200 rounded-xl bg-gray-50"
             >
              <div class="flex items-center space-x-4">
                <img
                  v-if="item.product.thumbnail"
                  :src="item.product.thumbnail"
                  :alt="item.product.name"
                  class="h-16 w-16 object-cover rounded"
                />
                <Package v-else class="h-16 w-16" />
                
                <div>
                  <h5 class="font-semibold text-gray-900">{{ item.name }}</h5>
                  <p v-if="item.sku_matrix" class="text-sm text-gray-600">
                    {{ formatSkuMatrix(item.sku_matrix) }}
                  </p>
                  <p class="text-lg font-bold text-blue-600">
                    RM{{ formatPrice(item.price_per_unit) }}
                  </p>
                </div>
              </div>
              
              <div class="flex items-center space-x-4">
                <!-- Quantity Controls -->
                <div class="flex items-center space-x-2">
                  <button
                    @click="updateItemQuantity(item.product.id, item.sku_matrix, item.quantity - 1)"
                    :disabled="isCartLoading || item.quantity <= 1"
                    class="w-8 h-8 flex items-center justify-center bg-gray-200 text-gray-700 hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed rounded"
                  >
                    -
                  </button>
                  <span class="w-12 text-center text-gray-600 font-medium">{{ item.quantity }}</span>
                  <button
                    @click="updateItemQuantity(item.product.id, item.sku_matrix, item.quantity + 1)"
                    :disabled="isCartLoading"
                    class="w-8 h-8 flex items-center justify-center bg-gray-200 text-gray-700 hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed rounded"
                  >
                    +
                  </button>
                </div>
                <button
                  @click="removeItemFromCart(item.product.id, item.sku_matrix)"
                  :disabled="isCartLoading"
                  class="text-red-600 hover:text-red-800 disabled:text-gray-400 p-2"
                >
                  <div v-if="isCartLoading" class="w-4 h-4 animate-spin rounded-full border-2 border-red-600 border-t-transparent"></div>
                  <Trash2 v-else class="h-4 w-4" />
                </button>
              </div>
            </div>
          </div>
          
                     <!-- Total -->
           <div class="mt-8 pt-6 border-t-2 border-gray-200 bg-blue-50 p-4 rounded-lg">
             <div class="flex justify-between items-center text-2xl font-bold">
               <span class="text-gray-900">Total:</span>
               <span class="text-blue-700">RM{{ cart ? formatPrice(cart.total_price) : '0.00' }}</span>
             </div>
           </div>
        </div>
        
                 <!-- Customer Details Form -->
         <div class="space-y-8">
           <h4 class="text-lg font-semibold text-gray-900 mb-6">Customer Details</h4>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Full Name *
              </label>
              <input
                v-model="customerForm.customer_name"
                type="text"
                required
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
              />
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Email *
              </label>
              <input
                v-model="customerForm.customer_email"
                type="email"
                required
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
              />
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Phone *
              </label>
              <input
                v-model="customerForm.customer_phone"
                type="tel"
                required
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
              />
            </div>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Address Line 1 *
            </label>
            <input
              v-model="customerForm.address_1"
              type="text"
              required
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
            />
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Address Line 2
            </label>
            <input
              v-model="customerForm.address_2"
              type="text"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
            />
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                City *
              </label>
              <input
                v-model="customerForm.city"
                type="text"
                required
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
              />
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                State *
              </label>
              <input
                v-model="customerForm.state"
                type="text"
                required
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
              />
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Postcode *
              </label>
              <input
                v-model="customerForm.postcode"
                type="text"
                required
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
              />
            </div>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Country *
            </label>
            <input
              v-model="customerForm.country"
              type="text"
              required
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
            />
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Order Notes
            </label>
            <textarea
              v-model="customerForm.order_notes"
              rows="3"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
              placeholder="Any special instructions..."
            ></textarea>
          </div>
          
          <!-- Submit Buttons -->
          <div class="pt-6 space-y-4">
            <div class="text-sm text-gray-600 text-center mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
              Choose order scenario for testing:
            </div>
            
            <!-- Success Scenario Button -->
            <button
              type="button"
              @click="submitOrder(true)"
              :disabled="isSubmitting || !hasCartItems"
              :class="{
                'bg-green-600 hover:bg-green-700 shadow-lg': !isSubmitting && hasCartItems,
                'bg-gray-300 text-gray-500 cursor-not-allowed': isSubmitting || !hasCartItems
              }"
              class="w-full px-6 py-4 rounded-lg text-lg font-medium transition-all duration-200 transform hover:scale-[1.02]"
            >
              <span v-if="isSubmitting" class="flex items-center justify-center">
                <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-white mr-2"></div>
                Processing Order...
              </span>
              <span v-else>
                ✅ Success Scenario - RM{{ cart ? formatPrice(cart.total_price) : '0.00' }}
              </span>
            </button>
            
            <!-- Failure Scenario Button -->
            <button
              type="button"
              @click="submitOrder(false)"
              :disabled="isSubmitting || !hasCartItems"
              :class="{
                'bg-red-600 hover:bg-red-700 shadow-lg': !isSubmitting && hasCartItems,
                'bg-gray-300 text-gray-500 cursor-not-allowed': isSubmitting || !hasCartItems
              }"
              class="w-full px-6 py-4 rounded-lg text-lg font-medium transition-all duration-200 transform hover:scale-[1.02]"
            >
              <span v-if="isSubmitting" class="flex items-center justify-center">
                <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-white mr-2"></div>
                Processing Order...
              </span>
              <span v-else>
                ❌ Failure Scenario - RM{{ cart ? formatPrice(cart.total_price) : '0.00' }}
              </span>
            </button>
          </div>
        </div>
      </div>
    </main>
    
    <ToastProvider />
  </div>
</template>

<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { ref, reactive, onMounted, computed, watch } from 'vue'
import { Package, Trash2, Search, Filter, ChevronDown, ChevronLeft, ChevronRight, X } from 'lucide-vue-next'
import ToastProvider from '@/components/ToastProvider.vue'
import CartIcon from '@/components/CartIcon.vue'
import ProductCard from '@/components/ProductCard.vue'
import { dashboard, login, register, logout } from '@/routes'
import { sanitizeHtmlForCard } from '@/utils/sanitizeHtml'
import { useCart } from '@/composables/useCart'
import { useToast } from '@/composables/useToast'

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
 has_variants: boolean
 variant_count: number
 is_in_stock: boolean
 stock_status: 'in_stock' | 'low_stock' | 'out_of_stock'
}


interface Props {
 products: {
 data: Product[]
 current_page: number
 last_page: number
 per_page: number
 total: number
 from: number
 to: number
 }
 filters: {
 categories: Array<{id: string, name: string}>
 tags: Array<{id: string, name: string}>
 search: string
 category_ids: string[]
 tag_ids: string[]
 min_price?: number
 max_price?: number
 stock_status?: string
 order_by?: string
 }
}

const props = defineProps<Props>()
const page = usePage()

console.log('Welcome.vue loaded, products:', props.products)

// Search and Filter State
const searchQuery = ref(props.filters.search || '')
const isSearching = ref(false)
const showFilters = ref(false)
const selectedCategories = ref<string[]>(props.filters.category_ids || [])
const selectedTags = ref<string[]>(props.filters.tag_ids || [])
const minPrice = ref<number | undefined>(props.filters.min_price)
const maxPrice = ref<number | undefined>(props.filters.max_price)
const stockStatus = ref(props.filters.stock_status || '')
const sortBy = ref(props.filters.order_by || '')

// Authentication
const isAuthenticated = computed(() => !!page.props.auth.user)

// Toast functionality
const { success, error } = useToast()

// Cart functionality
const {
 cart,
 hasCartItems,
 cartTotal,
 removeFromCart,
 updateCartItem,
 refreshCart,
 initializeCart,
 isLoading: isCartLoading
} = useCart()

const isSubmitting = ref(false)

const customerForm = reactive({
 customer_name: '',
 customer_email: '',
 customer_phone: '',
 address_1: '',
 address_2: '',
 city: '',
 state: '',
 postcode: '',
 country: 'Malaysia',
 order_notes: '',
 is_success: true
})


// Initialize cart on mount
onMounted(async () => {
 if (isAuthenticated.value) {
 await initializeCart()
 await refreshCart()
 }
})

// Helper functions
const formatPrice = (price: number) => {
 return price.toFixed(2)
}


const removeItemFromCart = async (productId: string, skuMatrix?: string) => {
 try {
 const result = await removeFromCart(productId, skuMatrix)
 if (result.success) {
 // Optionally show success message
 console.log('Item removed from cart:', result.message)
 }
 } catch (error) {
 console.error('Error removing item from cart:', error)
 }
}

const updateItemQuantity = async (productId: string, skuMatrix: string | undefined, newQuantity: number) => {
 if (newQuantity < 1) return
 
 try {
 const result = await updateCartItem(productId, newQuantity, skuMatrix)
 if (result.success) {
 // Find the item to get its name and variant info for toast
 const cartItem = cart.value?.items.find(item => 
 item.product.id === productId && item.sku_matrix === skuMatrix
 )
 
 const itemName = cartItem?.name || 'Item'
 const variantText = skuMatrix ? ` (${formatSkuMatrix(skuMatrix)})` : ''
 
 success(`Updated ${itemName}${variantText} quantity to ${newQuantity}`)
 } else {
 error(result.message)
 }
 } catch (err: any) {
 error(err.response?.data?.message || err.message || 'Failed to update quantity')
 }
}

const formatSkuMatrix = (matrix: string) => {
 // Remove product name and format variants nicely
 const parts = matrix.split(':')
 if (parts.length > 1) {
 return parts.slice(1).join(' / ')
 }
 return matrix
}

const submitOrder = async (isSuccess: boolean) => {
 if (isSubmitting.value || !hasCartItems.value || !cart.value) return
 
 // Check if user is authenticated
 if (!isAuthenticated.value) {
 error('Please login to submit an order')
 return
 }
 
 isSubmitting.value = true
 
 // Update the form with the success/failure scenario
 customerForm.is_success = isSuccess
 
 const orderData = {
 ...customerForm,
 order_items: cart.value.items.map(item => ({
 product_id: item.product.id,
 sku_matrix: item.sku_matrix,
 quantity: item.quantity
 }))
 }
 
 router.post('/orders/submit', orderData, {
 onSuccess: () => {
 // Clear form - cart will be cleared server-side
 Object.assign(customerForm, {
 customer_name: '',
 customer_email: '',
 customer_phone: '',
 address_1: '',
 address_2: '',
 city: '',
 state: '',
 postcode: '',
 country: 'Malaysia',
 order_notes: ''
 })
 // Refresh cart to reflect server-side clearing
 refreshCart()
 isSubmitting.value = false
 success('Order submitted successfully!')
 },
 onError: (errors) => {
 isSubmitting.value = false
 // Handle specific authentication errors
 if (errors.response?.status === 401) {
 error('Please login to submit an order')
 } else if (errors.response?.status === 403) {
 error('Access denied. Please check your permissions.')
 } else {
 error(errors.response?.data?.message || 'Failed to submit order. Please try again.')
 }
 }
 })
}

// Search and Filter Functions
let searchTimeout: NodeJS.Timeout | null = null
let currentSearchRequest: any = null

const debouncedSearch = () => {
 // Cancel any existing timeout
 if (searchTimeout) {
 clearTimeout(searchTimeout)
 }
 
 // Cancel any ongoing request
 if (currentSearchRequest) {
 currentSearchRequest.cancel()
 currentSearchRequest = null
 }
 
 // Don't search if less than 2 characters (but allow empty to clear search)
 if (searchQuery.value.length > 0 && searchQuery.value.length < 2) {
 return
 }
 
 // Set loading state if we're about to search
 if (searchQuery.value.length >= 2) {
 isSearching.value = true
 }
 
 // Debounce the search - increased to 800ms for smoother UX
 searchTimeout = setTimeout(() => {
 performSearch()
 }, 800)
}

const performSearch = () => {
 const params: Record<string, any> = {}
 
 // Add search query if it meets minimum length
 if (searchQuery.value && searchQuery.value.length >= 2) {
 params.search = searchQuery.value
 }
 
 // Preserve current filters
 if (selectedCategories.value.length > 0) {
 params.category_ids = selectedCategories.value
 }
 
 if (selectedTags.value.length > 0) {
 params.tag_ids = selectedTags.value
 }
 
 if (minPrice.value !== undefined && minPrice.value !== null) {
 params.min_price = minPrice.value
 }
 
 if (maxPrice.value !== undefined && maxPrice.value !== null) {
 params.max_price = maxPrice.value
 }
 
 if (stockStatus.value) {
 params.stock_status = stockStatus.value
 }
 
 if (sortBy.value) {
 params.order_by = sortBy.value
 }
 
 // Reset to page 1 when searching
 params.page = 1
 
 // Make the request and store it so we can cancel if needed
 currentSearchRequest = router.get(window.location.pathname, params, {
 preserveState: false,
 replace: false,
 onFinish: () => {
 isSearching.value = false
 currentSearchRequest = null
 },
 onError: () => {
 isSearching.value = false
 currentSearchRequest = null
 }
 })
}

const clearSearch = () => {
 searchQuery.value = ''
 // Cancel any pending search
 if (searchTimeout) {
 clearTimeout(searchTimeout)
 }
 if (currentSearchRequest) {
 currentSearchRequest.cancel()
 currentSearchRequest = null
 }
 isSearching.value = false
 
 // Trigger immediate search with empty query
 performSearch()
}

let filterTimeout: NodeJS.Timeout | null = null
const debouncedFilter = () => {
 if (filterTimeout) {
 clearTimeout(filterTimeout)
 }
 filterTimeout = setTimeout(() => {
 applyFilters()
 }, 500)
}

const applyFilters = () => {
 const params: Record<string, any> = {}
 
 // Only include search if it's valid (2+ chars or empty)
 if (searchQuery.value && searchQuery.value.length >= 2) {
 params.search = searchQuery.value
 }
 
 if (selectedCategories.value.length > 0) {
 params.category_ids = selectedCategories.value
 }
 
 if (selectedTags.value.length > 0) {
 params.tag_ids = selectedTags.value
 }
 
 if (minPrice.value !== undefined && minPrice.value !== null) {
 params.min_price = minPrice.value
 }
 
 if (maxPrice.value !== undefined && maxPrice.value !== null) {
 params.max_price = maxPrice.value
 }
 
 if (stockStatus.value) {
 params.stock_status = stockStatus.value
 }
 
 if (sortBy.value) {
 params.order_by = sortBy.value
 }
 
 // Reset to page 1 when applying filters
 params.page = 1
 
 router.get(window.location.pathname, params, {
 preserveState: false,
 replace: false
 })
}

const clearFilters = () => {
 // Cancel any pending operations
 if (searchTimeout) {
 clearTimeout(searchTimeout)
 }
 if (currentSearchRequest) {
 currentSearchRequest.cancel()
 currentSearchRequest = null
 }
 
 // Clear all filter values
 searchQuery.value = ''
 selectedCategories.value = []
 selectedTags.value = []
 minPrice.value = undefined
 maxPrice.value = undefined
 stockStatus.value = ''
 sortBy.value = ''
 isSearching.value = false
 
 // Navigate to clean state
 router.get(window.location.pathname, {}, {
 preserveState: false,
 replace: false
 })
}

// Pagination Functions
const goToPage = (page: number) => {
 if (page < 1 || page > props.products.last_page) return
 
 const params: Record<string, any> = { page }
 
 // Preserve current filters
 if (searchQuery.value) params.search = searchQuery.value
 if (selectedCategories.value.length > 0) params.category_ids = selectedCategories.value
 if (selectedTags.value.length > 0) params.tag_ids = selectedTags.value
 if (minPrice.value !== undefined) params.min_price = minPrice.value
 if (maxPrice.value !== undefined) params.max_price = maxPrice.value
 if (stockStatus.value) params.stock_status = stockStatus.value
 if (sortBy.value) params.order_by = sortBy.value
 
 router.get(window.location.pathname, params, {
 preserveState: false,
 replace: false
 })
}

const getPageNumbers = () => {
 const current = props.products.current_page
 const last = props.products.last_page
 const pages: (number | string)[] = []
 
 if (last <= 7) {
 // Show all pages if 7 or fewer
 for (let i = 1; i <= last; i++) {
 pages.push(i)
 }
 } else {
 // Show first page
 pages.push(1)
 
 if (current > 4) {
 pages.push('...')
 }
 
 // Show pages around current
 const start = Math.max(2, current - 1)
 const end = Math.min(last - 1, current + 1)
 
 for (let i = start; i <= end; i++) {
 pages.push(i)
 }
 
 if (current < last - 3) {
 pages.push('...')
 }
 
 // Show last page
 if (last > 1) {
 pages.push(last)
 }
 }
 
 return pages
}
</script>

<style scoped>
.line-clamp-2 {
 display: -webkit-box;
 -webkit-line-clamp: 2;
 line-clamp: 2;
 -webkit-box-orient: vertical;
 overflow: hidden;
}

.line-clamp-3 {
 display: -webkit-box;
 -webkit-line-clamp: 3;
 line-clamp: 3;
 -webkit-box-orient: vertical;
 overflow: hidden;
}

/* Rich text content styling for product cards */
.rich-text-content {
 line-height: 1.5;
}

.rich-text-content :deep(h1),
.rich-text-content :deep(h2),
.rich-text-content :deep(h3) {
 font-weight: 600;
 margin-bottom: 0.25rem;
}

.rich-text-content :deep(h1) {
 font-size: 1rem;
}

.rich-text-content :deep(h2) {
 font-size: 0.875rem;
}

.rich-text-content :deep(h3) {
 font-size: 0.875rem;
}

.rich-text-content :deep(p) {
 margin-bottom: 0.25rem;
}

.rich-text-content :deep(ul),
.rich-text-content :deep(ol) {
 list-style-position: inside;
 margin-bottom: 0.25rem;
}

.rich-text-content :deep(ul) {
 list-style-type: disc;
}

.rich-text-content :deep(ol) {
 list-style-type: decimal;
}

.rich-text-content :deep(li) {
 font-size: 0.875rem;
}

.rich-text-content :deep(strong) {
 font-weight: 600;
}

.rich-text-content :deep(em) {
 font-style: italic;
}

.rich-text-content :deep(a) {
 color: #2563eb;
 text-decoration: underline;
}

.dark .rich-text-content :deep(a) {
 color: #60a5fa;
}

.rich-text-content :deep(blockquote) {
 border-left: 2px solid #d1d5db;
 padding-left: 0.5rem;
 font-style: italic;
}

.dark .rich-text-content :deep(blockquote) {
 border-left-color: #4b5563;
}
</style>