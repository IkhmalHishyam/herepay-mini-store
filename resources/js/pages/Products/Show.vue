<template>
  <AppSidebarLayout title="Product Details" :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
      <!-- Header Actions -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-semibold">{{ product.name }}</h1>
          <div 
            v-if="product.description" 
            class="text-muted-foreground mt-2 rich-text-content"
            v-html="sanitizeHtml(product.description)"
          ></div>
          <p v-else class="text-muted-foreground italic">No description provided</p>
        </div>
        <div class="flex items-center gap-2">
          <Button variant="outline" @click="router.visit(admin.products.index())">
            <ArrowLeft class="h-4 w-4 mr-2" />
            Back to Products
          </Button>
          <DropdownMenu>
            <DropdownMenuTrigger as-child>
              <Button variant="outline">
                <MoreHorizontal class="h-4 w-4" />
                <span class="sr-only">More actions</span>
              </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end">
              <DropdownMenuItem @click="router.visit(admin.products.edit.url({ product_id: product.id }))">
                <Edit class="mr-2 h-4 w-4" />
                Edit Product
              </DropdownMenuItem>
              <DropdownMenuItem @click="handleTogglePublish">
                <component :is="product.is_published ? EyeOff : Eye" class="mr-2 h-4 w-4" />
                {{ product.is_published ? 'Unpublish' : 'Publish' }}
              </DropdownMenuItem>
              <DropdownMenuSeparator />
              <DropdownMenuItem class="text-red-600" @click="handleDelete">
                <Trash2 class="mr-2 h-4 w-4" />
                Delete Product
              </DropdownMenuItem>
            </DropdownMenuContent>
          </DropdownMenu>
        </div>
      </div>

      <!-- Product Overview Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Status -->
        <div class="rounded-xl border border-sidebar-border/70 bg-card p-4 border-sidebar-border">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-muted-foreground">Status</p>
              <div class="flex items-center gap-2 mt-1">
                <div 
                  :class="{
                    'bg-green-100 text-green-800': product.is_published,
                    'bg-gray-100 text-gray-800': !product.is_published
                  }"
                  class="px-2 py-1 text-xs font-medium rounded-full"
                >
                  {{ product.is_published ? 'Published' : 'Draft' }}
                </div>
              </div>
            </div>
            <component :is="product.is_published ? Eye : EyeOff" class="h-8 w-8 text-muted-foreground" />
          </div>
        </div>

        <!-- Price -->
        <div class="rounded-xl border border-sidebar-border/70 bg-card p-4 border-sidebar-border">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-muted-foreground">Price</p>
              <p class="text-2xl font-semibold">RM{{ formatPrice(product.price) }}</p>
            </div>
            <DollarSign class="h-8 w-8 text-muted-foreground" />
          </div>
        </div>

        <!-- Stock -->
        <div class="rounded-xl border border-sidebar-border/70 bg-card p-4 border-sidebar-border">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-muted-foreground">Total Stock</p>
              <div class="flex items-center gap-2 mt-1">
                <p class="text-2xl font-semibold">{{ getTotalStock() }}</p>
                <div 
                  :class="{
                    'bg-green-100 text-green-800': getStockStatus() === 'in_stock',
                    'bg-yellow-100 text-yellow-800': getStockStatus() === 'low_stock',
                    'bg-red-100 text-red-800': getStockStatus() === 'out_of_stock'
                  }"
                  class="px-2 py-1 text-xs font-medium rounded-full"
                >
                  {{ getStockStatusLabel(getStockStatus()) }}
                </div>
              </div>
            </div>
            <Package class="h-8 w-8 text-muted-foreground" />
          </div>
        </div>

        <!-- SKUs -->
        <div class="rounded-xl border border-sidebar-border/70 bg-card p-4 border-sidebar-border">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-muted-foreground">SKU Combinations</p>
              <p class="text-2xl font-semibold">{{ product.skus?.length || 0 }}</p>
              <p class="text-xs text-muted-foreground">{{ product.variant_groups?.length || 0 }} variant groups</p>
            </div>
            <Grid3X3 class="h-8 w-8 text-muted-foreground" />
          </div>
        </div>
      </div>

      <!-- Main Content Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Product Images -->
        <div class="rounded-xl border border-sidebar-border/70 bg-card p-6 border-sidebar-border">
          <h3 class="text-lg font-semibold mb-4">Product Images</h3>
          
          <!-- Thumbnail -->
          <div v-if="product.product_thumbnail" class="mb-4">
            <p class="text-sm text-muted-foreground mb-2">Thumbnail</p>
            <div class="w-32 h-32 rounded-lg overflow-hidden bg-gray-100 flex items-center justify-center">
              <img
                :src="product.product_thumbnail.file_url"
                :alt="product.name"
                class="w-full h-full object-cover"
              />
            </div>
          </div>

          <!-- Additional Images -->
          <div v-if="product.product_images?.length">
            <p class="text-sm text-muted-foreground mb-2">Additional Images ({{ product.product_images.length }})</p>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
              <div 
                v-for="image in product.product_images" 
                :key="image.id"
                class="aspect-square rounded-lg overflow-hidden bg-gray-100"
              >
                <img
                  :src="image.file_url"
                  :alt="product.name"
                  class="w-full h-full object-cover"
                />
              </div>
            </div>
          </div>

          <div v-else-if="!product.product_thumbnail" class="flex flex-col items-center justify-center py-8 text-muted-foreground">
            <ImageIcon class="h-12 w-12 mb-2" />
            <p class="text-sm">No images uploaded</p>
          </div>
        </div>

        <!-- Product Details -->
        <div class="space-y-6">
          <!-- Categories & Tags -->
          <div class="rounded-xl border border-sidebar-border/70 bg-card p-6 border-sidebar-border">
            <h3 class="text-lg font-semibold mb-4">Categories & Tags</h3>
            
            <!-- Categories -->
            <div class="mb-4">
              <p class="text-sm text-muted-foreground mb-2">Categories</p>
              <div v-if="product.product_categories?.length" class="flex flex-wrap gap-2">
                <div 
                  v-for="category in product.product_categories"
                  :key="category.id"
                  class="px-3 py-1 bg-blue-100 text-blue-800 text-sm rounded-full"
                >
                  {{ category.name }}
                </div>
              </div>
              <p v-else class="text-sm text-muted-foreground">No categories assigned</p>
            </div>

            <!-- Tags -->
            <div>
              <p class="text-sm text-muted-foreground mb-2">Tags</p>
              <div v-if="product.product_tags?.length" class="flex flex-wrap gap-2">
                <div 
                  v-for="tag in product.product_tags"
                  :key="tag.id"
                  class="px-3 py-1 bg-purple-100 text-purple-800 text-sm rounded-full"
                >
                  {{ tag.name }}
                </div>
              </div>
              <p v-else class="text-sm text-muted-foreground">No tags assigned</p>
            </div>
          </div>

          <!-- Product Statistics -->
          <div class="rounded-xl border border-sidebar-border/70 bg-card p-6 border-sidebar-border">
            <h3 class="text-lg font-semibold mb-4">Sales Statistics</h3>
            
            <div v-if="product.product_stat" class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <p class="text-sm text-muted-foreground">Total Revenue</p>
                <p class="text-xl font-semibold text-green-600">RM{{ formatPrice(product.product_stat.total_revenue || 0) }}</p>
              </div>
              <div>
                <p class="text-sm text-muted-foreground">Units Sold</p>
                <p class="text-xl font-semibold text-blue-600">{{ product.product_stat.total_sold_in_units || 0 }}</p>
              </div>
              <div>
                <p class="text-sm text-muted-foreground">Last Sold</p>
                <p class="text-lg font-medium">{{ product.product_stat.last_sold_at ? formatDate(product.product_stat.last_sold_at) : 'Never' }}</p>
              </div>
            </div>
            
            <div v-else class="flex flex-col items-center justify-center py-8 text-muted-foreground">
              <DollarSign class="h-12 w-12 mb-2" />
              <p class="text-sm">No sales data yet</p>
              <p class="text-xs">Statistics will appear after the first sale</p>
            </div>
          </div>
        </div>
      </div>

      <!-- SKU Combinations -->
      <div v-if="product.skus?.length" class="rounded-xl border border-sidebar-border/70 bg-card p-6 border-sidebar-border">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold">SKU Combinations</h3>
          <div class="text-sm text-muted-foreground">
            {{ product.skus.length }} combination{{ product.skus.length !== 1 ? 's' : '' }}
          </div>
        </div>
        
        <div class="space-y-3">
          <div 
            v-for="sku in product.skus"
            :key="sku.id"
            class="grid grid-cols-1 lg:grid-cols-4 gap-4 p-4 border rounded-lg bg-gray-50"
          >
            <!-- Matrix -->
            <div class="lg:col-span-2">
              <p class="text-xs font-medium text-muted-foreground mb-1">Matrix</p>
              <div class="font-mono text-sm bg-white px-3 py-2 border rounded">
                {{ sku.matrix }}
              </div>
            </div>
            
            <!-- Price -->
            <div>
              <p class="text-xs font-medium text-muted-foreground mb-1">Price</p>
              <p class="text-lg font-semibold">RM{{ formatPrice(sku.price) }}</p>
            </div>
            
            <!-- Stock -->
            <div>
              <p class="text-xs font-medium text-muted-foreground mb-1">Stock</p>
              <div class="flex items-center gap-2">
                <p class="text-lg font-semibold">{{ sku.total_stock }}</p>
                <div 
                  :class="{
                    'bg-green-100 text-green-800': sku.total_stock > 10,
                    'bg-yellow-100 text-yellow-800': sku.total_stock > 0 && sku.total_stock <= 10,
                    'bg-red-100 text-red-800': sku.total_stock === 0
                  }"
                  class="px-2 py-1 text-xs font-medium rounded-full"
                >
                  {{ sku.total_stock === 0 ? 'Out' : sku.total_stock <= 10 ? 'Low' : 'In Stock' }}
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Variant Groups Summary -->
        <div v-if="product.variant_groups?.length" class="mt-6 p-4 bg-blue-50 rounded-lg">
          <h4 class="font-medium mb-3 text-blue-900">Variant Groups</h4>
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div 
              v-for="variantGroup in product.variant_groups"
              :key="variantGroup.id"
              class="bg-white p-3 rounded border"
            >
              <div class="flex items-center gap-2 mb-2">
                <h5 class="font-medium">{{ variantGroup.name }}</h5>
                <span class="text-xs bg-blue-100 text-blue-800 px-2 py-0.5 rounded">
                  Index {{ variantGroup.index }}
                </span>
              </div>
              <div class="flex flex-wrap gap-1">
                <div 
                  v-for="variant in variantGroup.variants"
                  :key="variant.id"
                  class="text-xs bg-gray-100 px-2 py-1 rounded"
                >
                  {{ variant.name }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Product Meta Information -->
      <div class="rounded-xl border border-sidebar-border/70 bg-card p-6 border-sidebar-border">
        <h3 class="text-lg font-semibold mb-4">Product Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
          <div>
            <span class="text-muted-foreground">Product ID:</span>
            <span class="ml-2 font-mono">{{ product.id }}</span>
          </div>
          <div>
            <span class="text-muted-foreground">Created:</span>
            <span class="ml-2">{{ formatDate(product.created_at) }}</span>
          </div>
          <div>
            <span class="text-muted-foreground">Last Updated:</span>
            <span class="ml-2">{{ formatDate(product.updated_at) }}</span>
          </div>
        </div>
      </div>
    </div>
  </AppSidebarLayout>
</template>

<script setup lang="ts">
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue'
import { Button } from '@/components/ui/button'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import { 
  ArrowLeft, 
  MoreHorizontal, 
  Edit, 
  Trash2, 
  Eye, 
  EyeOff, 
  Package, 
  DollarSign,
  Grid3X3,
  ImageIcon
} from 'lucide-vue-next'
import { router } from '@inertiajs/vue3'
import type { BreadcrumbItemType } from '@/types'
import admin from '@/routes/admin'
import { sanitizeHtml } from '@/utils/sanitizeHtml'

interface ProductStat {
  id: string
  total_revenue: number
  total_sold_in_units: number
  last_sold_at: string | null
}

interface ProductImage {
  id: string
  file_url: string
  file_name: string
}

interface ProductCategory {
  id: string
  name: string
}

interface ProductTag {
  id: string
  name: string
}

interface Variant {
  id: string
  name: string
}

interface VariantGroup {
  id: string
  name: string
  index: number
  variants?: Variant[]
}

interface Sku {
  id: string
  matrix: string
  price: number
  total_stock: number
}

interface Product {
  id: string
  name: string
  description: string
  price: number
  is_published: boolean
  product_thumbnail?: ProductImage
  product_images?: ProductImage[]
  product_categories?: ProductCategory[]
  product_tags?: ProductTag[]
  variant_groups?: VariantGroup[]
  skus?: Sku[]
  product_stat?: ProductStat
  created_at: string
  updated_at: string
}

const props = defineProps<{
  product: Product
}>()

const breadcrumbs: BreadcrumbItemType[] = [
  {
    label: 'Products',
    href: admin.products.index.url()
  },
  {
    label: props.product.name,
    href: admin.products.show.url(props.product.id)
  }
]

const formatPrice = (price: number) => {
  return price.toFixed(2)
}

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const getStockStatusLabel = (status: string) => {
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

const getTotalStock = () => {
  if (!props.product.skus?.length) return 0
  
  return props.product.skus.reduce((total, sku) => {
    return total + sku.total_stock
  }, 0)
}

const getStockStatus = () => {
  const totalStock = getTotalStock()
  
  if (totalStock === 0) return 'out_of_stock'
  if (totalStock <= 10) return 'low_stock'
  return 'in_stock'
}

const handleTogglePublish = () => {
  const actionText = props.product.is_published ? 'unpublish' : 'publish'
  
  if (confirm(`Are you sure you want to ${actionText} this product?`)) {
    const endpoint = props.product.is_published 
      ? admin.products.unpublish.url(props.product.id)
      : admin.products.publish.url(props.product.id)
    
    router.post(endpoint, {}, {
      onError: (errors) => {
        console.error(`Failed to ${actionText} product:`, errors)
        alert(`Failed to ${actionText} product. Please try again.`)
      }
    })
  }
}

const handleDelete = () => {
  if (confirm(`Are you sure you want to delete "${props.product.name}"? This action cannot be undone.`)) {
    router.delete(admin.products.destroy.url(props.product.id), {
      onSuccess: () => {
        router.visit(admin.products.index.url())
      },
      onError: (errors) => {
        console.error('Failed to delete product:', errors)
        alert('Failed to delete product. Please try again.')
      }
    })
  }
}
</script>

<style scoped>
.rich-text-content {
  line-height: 1.6;
}

.rich-text-content :deep(h1) {
  font-size: 1.5rem;
  font-weight: 700;
  margin-bottom: 1rem;
  color: hsl(var(--foreground));
}

.rich-text-content :deep(h2) {
  font-size: 1.25rem;
  font-weight: 700;
  margin-bottom: 0.75rem;
  color: hsl(var(--foreground));
}

.rich-text-content :deep(h3) {
  font-size: 1.125rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
  color: hsl(var(--foreground));
}

.rich-text-content :deep(p) {
  margin-bottom: 0.75rem;
  line-height: 1.625;
}

.rich-text-content :deep(ul) {
  list-style-type: disc;
  list-style-position: inside;
  margin-bottom: 1rem;
}

.rich-text-content :deep(ul > li) {
  margin-bottom: 0.25rem;
}

.rich-text-content :deep(ol) {
  list-style-type: decimal;
  list-style-position: inside;
  margin-bottom: 1rem;
}

.rich-text-content :deep(ol > li) {
  margin-bottom: 0.25rem;
}

.rich-text-content :deep(li) {
  line-height: 1.625;
}

.rich-text-content :deep(strong) {
  font-weight: 600;
  color: hsl(var(--foreground));
}

.rich-text-content :deep(em) {
  font-style: italic;
}

.rich-text-content :deep(a) {
  color: #2563eb;
  text-decoration: underline;
  transition: color 0.15s ease-in-out;
}

.rich-text-content :deep(a):hover {
  color: #1e40af;
}

.dark .rich-text-content :deep(a) {
  color: #60a5fa;
}

.dark .rich-text-content :deep(a):hover {
  color: #93c5fd;
}

.rich-text-content :deep(blockquote) {
  border-left: 4px solid #d1d5db;
  padding-left: 1rem;
  font-style: italic;
  margin: 1rem 0;
  color: #4b5563;
}

.dark .rich-text-content :deep(blockquote) {
  border-left-color: #4b5563;
  color: #9ca3af;
}

.rich-text-content :deep(table) {
  border-collapse: collapse;
  border: 1px solid #d1d5db;
  width: 100%;
  margin-bottom: 1rem;
}

.dark .rich-text-content :deep(table) {
  border-color: #4b5563;
}

.rich-text-content :deep(table td),
.rich-text-content :deep(table th) {
  border: 1px solid #d1d5db;
  padding: 0.5rem;
  text-align: left;
}

.dark .rich-text-content :deep(table td),
.dark .rich-text-content :deep(table th) {
  border-color: #4b5563;
}

.rich-text-content :deep(table th) {
  background-color: #f3f4f6;
  font-weight: 600;
  color: hsl(var(--foreground));
}

.dark .rich-text-content :deep(table th) {
  background-color: #1f2937;
}

.rich-text-content :deep(code) {
  background-color: #f3f4f6;
  padding: 0.125rem 0.25rem;
  border-radius: 0.25rem;
  font-size: 0.875rem;
  font-family: ui-monospace, SFMono-Regular, 'Cascadia Code', 'Roboto Mono', Menlo, 'Liberation Mono', 'Consolas', monospace;
}

.dark .rich-text-content :deep(code) {
  background-color: #1f2937;
}
</style>