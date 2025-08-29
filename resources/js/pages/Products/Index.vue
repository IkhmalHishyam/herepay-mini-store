<template>
  <AppSidebarLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
      <!-- Header Section -->
      <div class="rounded-xl border border-sidebar-border/70 bg-card p-6 border-sidebar-border">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-2xl font-bold tracking-tight">Products</h1>
            <p class="text-sm text-muted-foreground">Manage your product catalog</p>
          </div>
          <div class="flex items-center gap-3">
            <!-- Per Page Selector -->
            <select
              v-model="perPage"
              @change="handlePerPageChange(perPage)"
              class="rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:ring-2 focus:ring-ring focus:ring-offset-2"
            >
              <option value="10">10 per page</option>
              <option value="25">25 per page</option>
              <option value="50">50 per page</option>
              <option value="100">100 per page</option>
            </select>
            
            <!-- Search Input -->
            <div class="relative">
              <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
              <Input
                v-model="search"
                placeholder="Search products..."
                class="pl-9 w-64"
                @input="handleSearch"
              />
            </div>
            
            <!-- Create Product Button -->
            <Button @click="router.visit(admin.products.create())">
              <Plus class="h-4 w-4 mr-2" />
              Add Product
            </Button>
          </div>
        </div>
      </div>

      <!-- Products Table -->
      <div v-if="products.data.length > 0" class="rounded-xl border border-sidebar-border/70 bg-card border-sidebar-border overflow-hidden">
        <div class="overflow-x-auto">
          <Table class="min-w-[800px]">
            <TableHeader>
              <TableRow>
                <TableHead class="min-w-[60px]">No.</TableHead>
                <TableHead class="min-w-[280px]">
                  <Button 
                    variant="ghost" 
                    size="sm" 
                    class="-ml-4 h-8 text-left justify-start hover:bg-transparent"
                    @click="handleSort('name')"
                  >
                    Product
                    <component :is="getSortIcon('name')" class="ml-2 h-4 w-4" />
                  </Button>
                </TableHead>
                <TableHead class="min-w-[100px]">
                  <Button 
                    variant="ghost" 
                    size="sm" 
                    class="-ml-4 h-8 text-left justify-start hover:bg-transparent"
                    @click="handleSort('price')"
                  >
                    Base Price
                    <component :is="getSortIcon('price')" class="ml-2 h-4 w-4" />
                  </Button>
                </TableHead>
                <TableHead class="min-w-[120px]">
                  <Button 
                    variant="ghost" 
                    size="sm" 
                    class="-ml-4 h-8 text-left justify-start hover:bg-transparent"
                    @click="handleSort('total_stock')"
                  >
                    Inventory
                    <component :is="getSortIcon('total_stock')" class="ml-2 h-4 w-4" />
                  </Button>
                </TableHead>
                <TableHead class="hidden lg:table-cell min-w-[120px]">
                  Categories
                </TableHead>
                <TableHead class="min-w-[100px]">Status</TableHead>
                <TableHead class="hidden xl:table-cell min-w-[120px]">
                  <Button 
                    variant="ghost" 
                    size="sm" 
                    class="-ml-4 h-8 text-left justify-start hover:bg-transparent"
                    @click="handleSort('created_at')"
                  >
                    Created
                    <component :is="getSortIcon('created_at')" class="ml-2 h-4 w-4" />
                  </Button>
                </TableHead>
                <TableHead class="w-12">Actions</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow
                v-for="(product, index) in products.data"
                :key="product.id"
                class="hover:bg-muted/50"
              >
                <!-- Index Number -->
                <TableCell class="py-3">
                  <div class="font-medium text-sm">{{ (products.current_page - 1) * products.per_page + index + 1 }}</div>
                </TableCell>
                
                <!-- Combined Product (Image + Name + Description) -->
                <TableCell class="py-3">
                  <div class="flex items-start gap-3">
                    <div class="w-12 h-12 rounded-lg overflow-hidden bg-gray-100 flex items-center justify-center flex-shrink-0">
                      <img
                        v-if="product.product_thumbnail?.file_url"
                        :src="product.product_thumbnail.file_url"
                        :alt="product.name"
                        class="w-full h-full object-cover"
                      />
                      <Package v-else class="w-6 h-6" />
                    </div>
                    <div class="flex flex-col min-w-0">
                      <Button 
                        variant="link" 
                        class="font-semibold p-0 h-auto text-foreground hover:text-primary text-left justify-start"
                        @click="router.visit(admin.products.show.url(product.id))"
                      >
                        {{ product.name }}
                      </Button>
                      <div v-if="product.description" class="text-sm text-muted-foreground mt-1 line-clamp-2">
                        {{ product.description }}
                      </div>
                    </div>
                  </div>
                </TableCell>
                
                <!-- Base Price -->
                <TableCell>
                  <div class="font-semibold text-lg">RM{{ formatPrice(product.price) }}</div>
                </TableCell>
                
                <!-- Inventory (Stock + Status) -->
                <TableCell>
                  <div class="flex flex-col gap-1">
                    <span class="font-medium text-sm">{{ getTotalStock(product) }} units</span>
                    <span
                      :class="{
                        'bg-green-100 text-green-800': getStockStatus(product) === 'in_stock',
                        'bg-yellow-100 text-yellow-800': getStockStatus(product) === 'low_stock',
                        'bg-red-100 text-red-800': getStockStatus(product) === 'out_of_stock'
                      }"
                      class="px-2 py-0.5 text-xs font-medium rounded-full w-fit"
                    >
                      {{ getStockStatusText(product) }}
                    </span>
                  </div>
                </TableCell>
                
                <!-- Categories -->
                <TableCell class="hidden lg:table-cell">
                  <div v-if="product.product_categories && product.product_categories.length > 0" class="flex flex-wrap gap-1">
                    <span
                      v-for="category in product.product_categories.slice(0, 2)"
                      :key="category.id"
                      class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full"
                    >
                      {{ category.name }}
                    </span>
                    <span v-if="product.product_categories.length > 2" class="px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded-full">
                      +{{ product.product_categories.length - 2 }}
                    </span>
                  </div>
                  <span v-else class="text-sm text-muted-foreground">—</span>
                </TableCell>
                
                <!-- Status (Published Status) -->
                <TableCell>
                  <span
                    :class="{
                      'bg-green-100 text-green-800': product.is_published,
                      'bg-gray-100 text-gray-800': !product.is_published
                    }"
                    class="px-2 py-1 text-xs font-medium rounded-full"
                  >
                    {{ product.is_published ? 'Live' : 'Draft' }}
                  </span>
                </TableCell>
                
                <!-- Created Date -->
                <TableCell class="hidden xl:table-cell">
                  <div class="text-sm text-muted-foreground">
                    {{ formatDate(product.created_at) }}
                  </div>
                </TableCell>
                <TableCell>
                  <DropdownMenu>
                    <DropdownMenuTrigger as-child>
                      <Button variant="ghost" size="sm">
                        <MoreHorizontal class="h-4 w-4" />
                        <span class="sr-only">Open menu</span>
                      </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                      <DropdownMenuItem @click="router.visit(admin.products.show.url(product.id))">
                        <Eye class="mr-2 h-4 w-4" />
                        View
                      </DropdownMenuItem>
                      <DropdownMenuItem @click="router.visit(admin.products.edit.url({ product_id: product.id }))">
                        <Edit class="mr-2 h-4 w-4" />
                        Edit
                      </DropdownMenuItem>
                      <DropdownMenuItem @click="handleTogglePublish(product.id, product.is_published)">
                        <component :is="product.is_published ? EyeOff : Eye" class="mr-2 h-4 w-4" />
                        {{ product.is_published ? 'Unpublish' : 'Publish' }}
                      </DropdownMenuItem>
                      <DropdownMenuSeparator />
                      <DropdownMenuItem class="text-red-600" @click="handleDelete(product.id, product.name)">
                        <Trash2 class="mr-2 h-4 w-4" />
                        Delete
                      </DropdownMenuItem>
                    </DropdownMenuContent>
                  </DropdownMenu>
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="products.data.length > 0 && products.last_page > 1" class="rounded-xl border border-sidebar-border/70 bg-card p-4 border-sidebar-border">
        <div class="flex items-center justify-between">
          <div class="text-sm text-muted-foreground">
            Showing {{ products.from }} to {{ products.to }} of {{ products.total }} products
          </div>
          <div class="flex items-center space-x-2">
            <Button
              variant="outline"
              size="sm"
              :disabled="!products.prev_page_url"
              @click="goToPage(products.current_page - 1)"
            >
              <ChevronLeft class="h-4 w-4 mr-1" />
              Previous
            </Button>
            
            <div class="flex items-center space-x-1">
              <template v-for="page in paginationPages" :key="page">
                <Button
                  v-if="page !== '...'"
                  variant="outline"
                  size="sm"
                  :class="{
                    'bg-primary text-primary-foreground': page === products.current_page
                  }"
                  @click="goToPage(typeof page === 'number' ? page : products.current_page)"
                >
                  {{ page }}
                </Button>
                <span v-else class="px-2 text-muted-foreground">...</span>
              </template>
            </div>

            <Button
              variant="outline"
              size="sm"
              :disabled="!products.next_page_url"
              @click="goToPage(products.current_page + 1)"
            >
              Next
              <ChevronRight class="h-4 w-4 ml-1" />
            </Button>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else-if="products.data.length === 0" class="rounded-xl border border-sidebar-border/70 bg-card p-12 border-sidebar-border text-center">
        <Package class="mx-auto h-12 w-12 text-muted-foreground mb-4" />
        <h3 class="text-lg font-medium mb-2">No products found</h3>
        <p class="text-muted-foreground mb-4">
          {{ search ? 'No products match your search criteria.' : 'Get started by creating your first product.' }}
        </p>
        <Button v-if="!search" @click="router.visit(admin.products.create())">
          <Plus class="h-4 w-4 mr-2" />
          Create your first product
        </Button>
      </div>
    </div>
  </AppSidebarLayout>
</template>

<script setup lang="ts">
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import { Search, Plus, Package, ChevronLeft, ChevronRight, MoreHorizontal, Edit, Trash2, Eye, EyeOff, ArrowUpDown, ArrowUp, ArrowDown } from 'lucide-vue-next'
import { computed, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import type { BreadcrumbItemType } from '@/types'
import admin from '@/routes/admin'

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
  tags?: Array<{
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

interface PaginatedProducts {
  data: Product[]
  current_page: number
  last_page: number
  per_page: number
  total: number
  from: number
  to: number
  prev_page_url: string | null
  next_page_url: string | null
}

interface Props {
  products: PaginatedProducts
  filters: {
    search?: string
    order_by?: string
    order_direction?: 'asc' | 'desc'
    per_page?: number
  }
}

const props = withDefaults(defineProps<Props>(), {
  filters: () => ({})
})

const search = ref(props.filters.search || '')
const orderBy = ref<string>(props.filters.order_by || '')
const orderDirection = ref<'asc' | 'desc'>(props.filters.order_direction || 'asc')
const perPage = ref<number>(props.filters.per_page || 10)

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Products', href: '/product' }
]

const paginationPages = computed(() => {
  const pages: (number | string)[] = []
  const current = props.products.current_page
  const last = props.products.last_page
  
  // Always show first page
  pages.push(1)
  
  if (current > 3) {
    pages.push('...')
  }
  
  // Show pages around current page
  for (let i = Math.max(2, current - 1); i <= Math.min(last - 1, current + 1); i++) {
    if (!pages.includes(i)) {
      pages.push(i)
    }
  }
  
  if (current < last - 2) {
    if (!pages.includes('...')) {
      pages.push('...')
    }
  }
  
  // Always show last page if there's more than one page
  if (last > 1 && !pages.includes(last)) {
    pages.push(last)
  }
  
  return pages
})

const handleSearch = () => {
  router.get('/product', { 
    search: search.value,
    order_by: orderBy.value,
    order_direction: orderDirection.value,
    per_page: perPage.value
  }, {
    preserveState: true,
    replace: true
  })
}

const goToPage = (page: number) => {
  router.get('/product', { 
    page, 
    search: search.value,
    order_by: orderBy.value,
    order_direction: orderDirection.value,
    per_page: perPage.value
  }, {
    preserveState: true,
    replace: true
  })
}

const handlePerPageChange = (newPerPage: number) => {
  perPage.value = newPerPage
  router.get('/product', { 
    search: search.value,
    order_by: orderBy.value,
    order_direction: orderDirection.value,
    per_page: perPage.value
  }, {
    preserveState: true,
    replace: true
  })
}

const formatPrice = (price: number) => {
  return price.toFixed(2)
}

const formatDate = (dateString: string) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const getTotalStock = (product: Product) => {
  if (!product.skus?.length) return 0
  
  return product.skus.reduce((total, sku) => {
    return total + sku.total_stock
  }, 0)
}

const getStockStatus = (product: Product) => {
  const totalStock = getTotalStock(product)
  if (totalStock === 0) return 'out_of_stock'
  if (totalStock <= 10) return 'low_stock'
  return 'in_stock'
}

const getStockStatusText = (product: Product) => {
  const status = getStockStatus(product)
  switch (status) {
    case 'in_stock':
      return 'In Stock'
    case 'low_stock':
      return 'Low Stock'
    case 'out_of_stock':
      return 'Out of Stock'
    default:
      return 'Unknown'
  }
}

const handleSort = (field: string) => {
  if (orderBy.value === field) {
    orderDirection.value = orderDirection.value === 'asc' ? 'desc' : 'asc'
  } else {
    orderBy.value = field
    orderDirection.value = 'asc'
  }
  
  router.get('/product', { 
    search: search.value, 
    order_by: orderBy.value, 
    order_direction: orderDirection.value,
    per_page: perPage.value
  }, {
    preserveState: true,
    replace: true
  })
}

const getSortIcon = (field: string) => {
  if (orderBy.value !== field) return ArrowUpDown
  return orderDirection.value === 'asc' ? ArrowUp : ArrowDown
}

const handleTogglePublish = (productId: string, isCurrentlyPublished: boolean) => {
  const actionText = isCurrentlyPublished ? 'unpublish' : 'publish'
  
  if (confirm(`Are you sure you want to ${actionText} this product?`)) {
    const endpoint = isCurrentlyPublished 
      ? admin.products.unpublish.url(productId)
      : admin.products.publish.url(productId)
    
    router.post(endpoint, {}, {
      onError: (errors) => {
        console.error(`Failed to ${actionText} product:`, errors)
        alert(`Failed to ${actionText} product. Please try again.`)
      }
    })
  }
}

const handleDelete = (productId: string, productName: string) => {
  if (confirm(`Are you sure you want to delete "${productName}"? This action cannot be undone.`)) {
    router.delete(admin.products.destroy.url(productId), {
      onError: (errors) => {
        console.error('Failed to delete product:', errors)
        alert('Failed to delete product. Please try again.')
      }
    })
  }
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
</style>