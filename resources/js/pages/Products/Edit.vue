<template>
  <AppSidebarLayout title="Edit Product" :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-semibold">Edit Product: {{ product.name }}</h1>
          <p class="text-muted-foreground">Update the product details</p>
        </div>
        <div class="flex gap-2">
          <Button variant="outline" @click="router.visit(admin.products.show.url(product.id))">
            <Eye class="h-4 w-4 mr-2" />
            View Product
          </Button>
          <Button variant="outline" @click="router.visit(admin.products.index())">
            <ArrowLeft class="h-4 w-4 mr-2" />
            Back to Products
          </Button>
        </div>
      </div>

      <!-- Form -->
      <form @submit.prevent="handleSubmit" class="space-y-6">
        <!-- Basic Information -->
        <div class="rounded-xl border border-sidebar-border/70 bg-card p-6 border-sidebar-border">
          <h3 class="text-lg font-semibold mb-4">Basic Information</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Product Name -->
            <div class="md:col-span-2">
              <label for="name" class="block text-sm font-medium mb-2">Product Name *</label>
              <Input
                id="name"
                v-model="form.name"
                placeholder="Enter product name"
                :class="{ 'border-red-500': errors.name}"
                required
              />
              <p v-if="errors.name" class="text-sm text-red-600 mt-1">{{ errors.name }}</p>
            </div>

            <!-- Price -->
            <div>
              <label for="price" class="block text-sm font-medium mb-2">Price (RM) *</label>
              <Input
                id="price"
                v-model="form.price"
                type="number"
                step="0.01"
                min="0"
                placeholder="0.00"
                :class="{ 'border-red-500': errors.price}"
                required
              />
              <p v-if="errors.price" class="text-sm text-red-600 mt-1">{{ errors.price }}</p>
            </div>

            <!-- Published Status -->
            <div class="md:col-span-2">
              <div class="flex items-center space-x-2">
                <input
                  type="checkbox"
                  id="is_published"
                  v-model="form.is_published"
                  class="rounded border-gray-300 text-primary focus:border-primary focus:ring-primary"
                />
                <label for="is_published" class="text-sm font-medium">Published</label>
              </div>
              <p v-if="errors.is_published" class="text-sm text-red-600 mt-1">{{ errors.is_published }}</p>
            </div>

            <!-- Description -->
            <div class="md:col-span-2">
              <CKEditor
                v-model="form.description"
                label="Description"
                placeholder="Enter product description with rich formatting..."
                :error="errors.description"
                hint="You can format text, add lists, links, and structure your product description for better presentation"
                min-height="200px"
              />
            </div>
          </div>
        </div>

        <!-- Product Images -->
        <div class="rounded-xl border border-sidebar-border/70 bg-card p-6 border-sidebar-border">
          <h3 class="text-lg font-semibold mb-4">Product Images</h3>
          
          <!-- Current Thumbnail -->
          <div class="mb-6">
            <label class="block text-sm font-medium mb-2">Current Thumbnail</label>
            <div class="flex items-start space-x-4">
              <div v-if="product.product_thumbnail && !deletedFileIds.includes(product.product_thumbnail.id)" class="relative w-24 h-24 rounded-lg overflow-hidden border">
                <img :src="product.product_thumbnail.file_url" :alt="product.name" class="w-full h-full object-cover" />
                <button
                  type="button"
                  @click="markFileForDeletion(product.product_thumbnail.id)"
                  class="absolute top-1 right-1 bg-red-500 rounded-full p-1 hover:bg-red-600 transition-colors"
                >
                  <X class="h-3 w-3" />
                </button>
              </div>
              <div v-else class="w-24 h-24 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center">
                <div class="text-center">
                  <ImageIcon class="h-6 w-6 mx-auto mb-1" />
                  <span class="text-xs text-gray-500">No thumbnail</span>
                </div>
              </div>
            </div>
            
            <!-- New Thumbnail Upload -->
            <div class="mt-4">
              <label class="block text-sm font-medium mb-2">Upload New Thumbnail</label>
              <div 
                class="w-24 h-24 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center cursor-pointer hover:border-gray-400 transition-colors"
                @click="thumbnailInputRef?.click()"
              >
                <div v-if="thumbnailPreview" class="w-full h-full rounded-lg overflow-hidden">
                  <img :src="thumbnailPreview" alt="New thumbnail preview" class="w-full h-full object-cover" />
                </div>
                <div v-else class="text-center">
                  <ImageIcon class="h-6 w-6 mx-auto mb-1" />
                  <span class="text-xs text-gray-500">Upload</span>
                </div>
              </div>
              <input
                ref="thumbnailInputRef"
                type="file"
                accept="image/*"
                @change="handleThumbnailChange"
                class="hidden"
              />
            </div>
          </div>

          <!-- Current Product Images -->
          <div class="mb-4">
            <label class="block text-sm font-medium mb-2">Current Images</label>
            <div v-if="product.product_images?.length" class="grid grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-2">
              <div
                v-for="image in product.product_images.filter(img => !deletedFileIds.includes(img.id))"
                :key="image.id"
                class="relative group w-16 h-16 rounded-lg overflow-hidden border"
              >
                <img :src="image.file_url" :alt="product.name" class="w-full h-full object-cover" />
                <button
                  type="button"
                  @click="markFileForDeletion(image.id)"
                  class="absolute top-1 right-1 bg-red-500 rounded-full p-1 hover:bg-red-600 transition-colors opacity-0 group-hover:opacity-100"
                >
                  <X class="h-3 w-3" />
                </button>
              </div>
            </div>
            <p v-else-if="!product.product_images?.length || product.product_images.filter(img => !deletedFileIds.includes(img.id)).length === 0" class="text-sm text-muted-foreground">No images uploaded</p>
          </div>

          <!-- New Images Upload -->
          <div>
            <label class="block text-sm font-medium mb-2">Add New Images</label>
            <div 
              class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer hover:border-gray-400 transition-colors"
              @click="imagesInputRef?.click()"
            >
              <ImageIcon class="h-8 w-8 mx-auto mb-2" />
              <p class="text-sm text-gray-600">Click to upload new images</p>
              <p class="text-xs text-gray-500 mt-1">PNG, JPG up to 5MB each</p>
            </div>
            <input
              ref="imagesInputRef"
              type="file"
              accept="image/*"
              multiple
              @change="handleImagesChange"
              class="hidden"
            />

            <!-- New Images Preview -->
            <div v-if="imagesPreviews.length" class="mt-4">
              <h4 class="text-sm font-medium mb-2">New Images to Upload</h4>
              <div class="grid grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-2">
                <div
                  v-for="(preview, index) in imagesPreviews"
                  :key="index"
                  class="relative group w-16 h-16 rounded-lg overflow-hidden border"
                >
                  <img :src="preview" :alt="`New image ${index + 1}`" class="w-full h-full object-cover" />
                  <button
                    type="button"
                    @click="removeNewImage(index)"
                    class="absolute top-1 right-1 bg-red-500 rounded-full p-1 hover:bg-red-600 transition-colors opacity-0 group-hover:opacity-100"
                  >
                    <X class="h-3 w-3" />
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Categories and Tags -->
        <div class="rounded-xl border border-sidebar-border/70 bg-card p-6 border-sidebar-border">
          <h3 class="text-lg font-semibold mb-4">Categories & Tags</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Categories -->
            <div>
              <label class="block text-sm font-medium mb-2">Categories</label>
              <div class="space-y-2">
                <div v-for="(category, index) in form.categories" :key="index" class="flex items-center space-x-2">
                  <Input
                    v-model="form.categories[index]"
                    placeholder="Category name"
                    class="flex-1"
                  />
                  <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    @click="removeCategory(index)"
                    class="text-red-600 hover:text-red-700"
                  >
                    <X class="h-4 w-4" />
                  </Button>
                </div>
                <Button
                  type="button"
                  variant="outline"
                  size="sm"
                  @click="addCategory"
                >
                  <Plus class="h-4 w-4 mr-2" />
                  Add Category
                </Button>
              </div>
            </div>

            <!-- Tags -->
            <div>
              <label class="block text-sm font-medium mb-2">Tags</label>
              <div class="space-y-2">
                <div v-for="(tag, index) in form.tags" :key="index" class="flex items-center space-x-2">
                  <Input
                    v-model="form.tags[index]"
                    placeholder="Tag name"
                    class="flex-1"
                  />
                  <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    @click="removeTag(index)"
                    class="text-red-600 hover:text-red-700"
                  >
                    <X class="h-4 w-4" />
                  </Button>
                </div>
                <Button
                  type="button"
                  variant="outline"
                  size="sm"
                  @click="addTag"
                >
                  <Plus class="h-4 w-4 mr-2" />
                  Add Tag
                </Button>
              </div>
            </div>
          </div>
        </div>

        <!-- Variant Groups -->
        <div class="rounded-xl border border-sidebar-border/70 bg-card p-6 border-sidebar-border">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Product Variants</h3>
            <Button
              type="button"
              variant="outline"
              size="sm"
              @click="addVariantGroup"
            >
              <Plus class="h-4 w-4 mr-2" />
              Add Variant Group
            </Button>
          </div>

          <div v-if="form.variant_groups.length === 0" class="text-center py-8 text-muted-foreground">
            <Package class="h-8 w-8 mx-auto mb-2" />
            <p>No variant groups added yet</p>
            <p class="text-sm">Add variant groups like size, color, etc.</p>
          </div>

          <div v-else class="space-y-6">
            <div
              v-for="(group, groupIndex) in form.variant_groups"
              :key="groupIndex"
              class="border border-gray-200 rounded-lg p-4"
            >
              <div class="flex justify-between items-center mb-4">
                <div class="flex-1 max-w-md">
                  <label class="block text-sm font-medium mb-2">Variant Group Name *</label>
                  <Input
                    v-model="group.name"
                    placeholder="e.g., Size, Color, Material"
                    :class="{ 'border-red-500': errors[`variant_groups.${groupIndex}.name`]}"
                    required
                  />
                  <p v-if="errors[`variant_groups.${groupIndex}.name`]" class="text-xs text-red-600 mt-1">
                    {{ errors[`variant_groups.${groupIndex}.name`] }}
                  </p>
                </div>
                <div class="ml-4">
                  <label class="block text-xs font-medium mb-1">Index</label>
                  <Input
                    v-model="group.index"
                    type="number"
                    min="0"
                    class="w-20 text-sm"
                    :class="{ 'border-red-500': errors[`variant_groups.${groupIndex}.index`]}"
                    required
                  />
                  <p v-if="errors[`variant_groups.${groupIndex}.index`]" class="text-xs text-red-600 mt-1">
                    {{ errors[`variant_groups.${groupIndex}.index`] }}
                  </p>
                </div>
                <Button
                  type="button"
                  variant="outline"
                  size="sm"
                  @click="removeVariantGroup(groupIndex)"
                  class="ml-2 text-red-600 hover:text-red-700"
                >
                  <Trash2 class="h-4 w-4" />
                </Button>
              </div>

              <!-- Variants -->
              <div class="space-y-3">
                <div class="flex justify-between items-center">
                  <label class="block text-sm font-medium">Variants</label>
                  <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    @click="addVariant(groupIndex)"
                  >
                    <Plus class="h-4 w-4 mr-2" />
                    Add Variant
                  </Button>
                </div>
                
                <div v-if="group.variants.length === 0" class="text-center py-4 text-muted-foreground">
                  <p class="text-sm">No variants added yet</p>
                </div>
                
                <div v-else class="space-y-2">
                  <div
                    v-for="(variant, variantIndex) in group.variants"
                    :key="variantIndex"
                    class="flex items-center space-x-2"
                  >
                    <Input
                      v-model="variant.name"
                      placeholder="Variant name"
                      class="flex-1"
                      :class="{ 'border-red-500': errors[`variant_groups.${groupIndex}.variants.${variantIndex}.name`]}"
                      required
                    />
                    <Button
                      type="button"
                      variant="outline"
                      size="sm"
                      @click="removeVariant(groupIndex, variantIndex)"
                      class="text-red-600 hover:text-red-700"
                    >
                      <X class="h-4 w-4" />
                    </Button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- SKUs -->
        <div class="rounded-xl border border-sidebar-border/70 bg-card p-6 border-sidebar-border">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">SKU Combinations</h3>
            <Button
              type="button"
              variant="outline"
              size="sm"
              @click="generateSkus"
              :disabled="form.variant_groups.length === 0"
            >
              <RefreshCw class="h-4 w-4 mr-2" />
              Regenerate SKUs
            </Button>
          </div>

          <div v-if="form.skus.length === 0" class="text-center py-8 text-muted-foreground">
            <Package class="h-8 w-8 mx-auto mb-2" />
            <p>No SKUs generated yet</p>
            <p class="text-sm">Add variant groups and click "Regenerate SKUs" to create combinations</p>
          </div>

          <div v-else class="space-y-4">
            <div
              v-for="(sku, skuIndex) in form.skus"
              :key="skuIndex"
              class="border border-gray-200 rounded-lg p-4"
            >
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                  <label class="block text-xs font-medium mb-1">Matrix *</label>
                  <Input
                    v-model="sku.matrix"
                    placeholder="e.g., Small-Red"
                    class="text-sm"
                    :class="{ 'border-red-500': errors[`skus.${skuIndex}.matrix`]}"
                    required
                  />
                  <p v-if="errors[`skus.${skuIndex}.matrix`]" class="text-xs text-red-600 mt-1">
                    {{ errors[`skus.${skuIndex}.matrix`] }}
                  </p>
                </div>
                <div>
                  <label class="block text-xs font-medium mb-1">Price (RM) *</label>
                  <Input
                    v-model="sku.price"
                    type="number"
                    step="0.01"
                    min="0"
                    placeholder="0.00"
                    class="text-sm"
                    :class="{ 'border-red-500': errors[`skus.${skuIndex}.price`]}"
                    required
                  />
                  <p v-if="errors[`skus.${skuIndex}.price`]" class="text-xs text-red-600 mt-1">
                    {{ errors[`skus.${skuIndex}.price`] }}
                  </p>
                </div>
                <div>
                  <label class="block text-xs font-medium mb-1">Stock *</label>
                  <Input
                    v-model="sku.total_stock"
                    type="number"
                    min="0"
                    placeholder="0"
                    class="text-sm"
                    :class="{ 'border-red-500': errors[`skus.${skuIndex}.total_stock`]}"
                    required
                  />
                  <p v-if="errors[`skus.${skuIndex}.total_stock`]" class="text-xs text-red-600 mt-1">
                    {{ errors[`skus.${skuIndex}.total_stock`] }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Submit Section -->
        <div class="flex justify-end space-x-3 pt-6 border-t">
          <Button
            type="button"
            variant="outline"
            @click="router.visit(admin.products.show.url(product.id))"
            :disabled="isSubmitting"
          >
            Cancel
          </Button>
          <Button
            type="submit"
            :disabled="isSubmitting"
            class="min-w-[120px]"
          >
            <span v-if="isSubmitting" class="flex items-center">
              <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
              Updating...
            </span>
            <span v-else>Update Product</span>
          </Button>
        </div>
      </form>
    </div>
  </AppSidebarLayout>
</template>

<script setup lang="ts">
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import CKEditor from '@/components/CKEditor.vue'
import { ArrowLeft, Eye, X, ImageIcon, Plus, Package, Trash2, RefreshCw, AlertCircle } from 'lucide-vue-next'
import { ref, reactive, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import type { BreadcrumbItemType } from '@/types'
import admin from '@/routes/admin'

interface Variant {
 id?: string
 name: string
}

interface VariantGroup {
 id?: string
 name: string
 index: number
 variants: Variant[]
}

interface SKU {
 id?: string
 matrix: string
 price: string | number
 total_stock: string | number
}

interface ProductFile {
 id: string
 file_name: string
 file_path: string
 file_url: string
}

interface ProductCategory {
 id: string
 name: string
}

interface ProductTag {
 id: string
 name: string
}

interface Product {
 id: string
 name: string
 description: string
 price: number
 is_published: boolean
 product_thumbnail?: ProductFile
 product_images?: ProductFile[]
 product_categories?: ProductCategory[]
 product_tags?: ProductTag[]
 variant_groups?: Array<{
 id: string
 name: string
 index: number
 variants: Array<{
 id: string
 name: string
 }>
 }>
 skus?: Array<{
 id: string
 matrix: string
 price: number
 total_stock: number
 }>
}

interface Props {
 product: Product
}

const props = defineProps<Props>()

const breadcrumbs: BreadcrumbItemType[] = [
 { title: 'Dashboard', href: '/dashboard' },
 { title: 'Products', href: '/product' },
 { title: props.product.name, href: `/product/${props.product.id}` },
 { title: 'Edit', href: `/product/${props.product.id}/edit` }
]

const form = reactive({
 name: props.product.name,
 description: props.product.description || '',
 price: props.product.price,
 is_published: props.product.is_published,
 categories: props.product.product_categories?.map(c => c.name) || [],
 tags: props.product.product_tags?.map(t => t.name) || [],
 variant_groups: props.product.variant_groups?.map(vg => ({
 id: vg.id,
 name: vg.name,
 index: vg.index,
 variants: vg.variants.map(v => ({ id: v.id, name: v.name }))
 })) || [],
 skus: props.product.skus?.map(sku => ({
 id: sku.id,
 matrix: sku.matrix,
 price: sku.price,
 total_stock: sku.total_stock
 })) || []
})

const errors = ref<Record<string, string>>({})
const isSubmitting = ref(false)

// File handling
const thumbnailInputRef = ref<HTMLInputElement | null>(null)
const imagesInputRef = ref<HTMLInputElement | null>(null)
const thumbnailPreview = ref<string | null>(null)
const imagesPreviews = ref<string[]>([])
const thumbnailFile = ref<File | null>(null)
const imageFiles = ref<File[]>([])
const deletedFileIds = ref<string[]>([])
const deletedVariantGroupIds = ref<string[]>([])
const deletedSkuIds = ref<string[]>([])
const deletedVariantIds = ref<string[]>([])

const markFileForDeletion = (fileId: string) => {
 if (!deletedFileIds.value.includes(fileId)) {
 deletedFileIds.value.push(fileId)
 }
}

const markVariantGroupForDeletion = (variantGroupId: string) => {
 if (!deletedVariantGroupIds.value.includes(variantGroupId)) {
 deletedVariantGroupIds.value.push(variantGroupId)
 }
}

const markSkuForDeletion = (skuId: string) => {
 if (!deletedSkuIds.value.includes(skuId)) {
 deletedSkuIds.value.push(skuId)
 }
}

const markVariantForDeletion = (variantId: string) => {
 if (!deletedVariantIds.value.includes(variantId)) {
 deletedVariantIds.value.push(variantId)
 }
}

const handleThumbnailChange = (event: Event) => {
 const target = event.target as HTMLInputElement
 const file = target.files?.[0]
 if (file) {
 thumbnailFile.value = file
 const reader = new FileReader()
 reader.onload = (e) => {
 thumbnailPreview.value = e.target?.result as string
 }
 reader.readAsDataURL(file)
 }
}

const handleImagesChange = (event: Event) => {
 const target = event.target as HTMLInputElement
 const files = Array.from(target.files || [])
 
 imageFiles.value = files
 imagesPreviews.value = []
 
 files.forEach(file => {
 const reader = new FileReader()
 reader.onload = (e) => {
 imagesPreviews.value.push(e.target?.result as string)
 }
 reader.readAsDataURL(file)
 })
}

const removeNewImage = (index: number) => {
 imageFiles.value.splice(index, 1)
 imagesPreviews.value.splice(index, 1)
}

// Categories and Tags
const addCategory = () => {
 form.categories.push('')
}

const removeCategory = (index: number) => {
 form.categories.splice(index, 1)
}

const addTag = () => {
 form.tags.push('')
}

const removeTag = (index: number) => {
 form.tags.splice(index, 1)
}

// Variant Groups
const addVariantGroup = () => {
 form.variant_groups.push({
 name: '',
 index: form.variant_groups.length,
 variants: []
 })
}

const removeVariantGroup = (index: number) => {
 const variantGroup = form.variant_groups[index]
 
 // If it's an existing variant group with an ID, mark it for deletion
 if (variantGroup.id) {
 markVariantGroupForDeletion(variantGroup.id)
 
 // Also mark all its variants for deletion
 variantGroup.variants.forEach(variant => {
 if (variant.id) {
 markVariantForDeletion(variant.id)
 }
 })
 }
 
 form.variant_groups.splice(index, 1)
 // Reindex remaining groups
 form.variant_groups.forEach((group, idx) => {
 group.index = idx
 })
 generateSkus()
}

const addVariant = (groupIndex: number) => {
 form.variant_groups[groupIndex].variants.push({ name: '' })
}

const removeVariant = (groupIndex: number, variantIndex: number) => {
 const variant = form.variant_groups[groupIndex].variants[variantIndex]
 
 // If it's an existing variant with an ID, mark it for deletion
 if (variant.id) {
 markVariantForDeletion(variant.id)
 }
 
 form.variant_groups[groupIndex].variants.splice(variantIndex, 1)
 generateSkus()
}

// SKU Generation
const generateSkus = () => {
 if (form.variant_groups.length === 0) {
 form.skus = []
 return
 }

 // Get all variant combinations
 const validGroups = form.variant_groups.filter(group => 
 group.name && group.variants.some(variant => variant.name)
 )

 if (validGroups.length === 0) {
 form.skus = []
 return
 }

 const combinations: string[][] = []
 
 const generateCombinations = (groupIndex: number, currentCombination: string[]) => {
 if (groupIndex === validGroups.length) {
 combinations.push([...currentCombination])
 return
 }

 const group = validGroups[groupIndex]
 const validVariants = group.variants.filter(variant => variant.name)
 
 for (const variant of validVariants) {
 generateCombinations(groupIndex + 1, [...currentCombination, variant.name])
 }
 }

 generateCombinations(0, [])

 // Create sorted combinations and preserve existing SKU data
 const existingSkus = new Map(form.skus.map(sku => [sku.matrix, sku]))
 const newMatrices = new Set<string>()
 
 const newSkus = combinations.map(combination => {
 const sortedGroups = validGroups
 .map((group, index) => ({ group, variant: combination[index] }))
 .sort((a, b) => a.group.index - b.group.index)
 
 const sortedCombination = sortedGroups.map(item => item.variant)
 const matrix = `${form.name || 'product'}:${sortedCombination.join(':')}`
 newMatrices.add(matrix)
 
 const existing = existingSkus.get(matrix)
 
 return {
 id: existing?.id,
 matrix,
 price: existing?.price || form.price || '',
 total_stock: existing?.total_stock || ''
 }
 })
 
 // Mark existing SKUs that are no longer needed for deletion
 form.skus.forEach(sku => {
 if (sku.id && !newMatrices.has(sku.matrix)) {
 markSkuForDeletion(sku.id)
 }
 })
 
 form.skus = newSkus
}

// Form submission
const handleSubmit = () => {
 if (isSubmitting.value) return

 isSubmitting.value = true
 errors.value = {}

 // Create FormData
 const formData = new FormData()
 
 // Basic fields
 if (form.name) formData.append('name', form.name)
 if (form.description) formData.append('description', form.description)
 if (form.price) formData.append('price', String(form.price))
 formData.append('is_published', form.is_published ? '1' : '0')

 // Files
 if (thumbnailFile.value) {
 formData.append('product_thumbnail', thumbnailFile.value)
 }

 imageFiles.value.forEach((file, index) => {
 formData.append(`product_images[${index}]`, file)
 })

 // Deleted files
 deletedFileIds.value.forEach((id, index) => {
 formData.append(`deleted_file_ids[${index}]`, id)
 })

 // Deleted variant groups
 deletedVariantGroupIds.value.forEach((id, index) => {
 formData.append(`deleted_variant_group_ids[${index}]`, id)
 })

 // Deleted SKUs
 deletedSkuIds.value.forEach((id, index) => {
 formData.append(`deleted_sku_ids[${index}]`, id)
 })

 // Deleted variants
 deletedVariantIds.value.forEach((id, index) => {
 formData.append(`deleted_variant_ids[${index}]`, id)
 })

 // Categories and tags
 form.categories.filter(c => c.trim()).forEach((category, index) => {
 formData.append(`categories[${index}]`, category.trim())
 })

 form.tags.filter(t => t.trim()).forEach((tag, index) => {
 formData.append(`tags[${index}]`, tag.trim())
 })

 // Variant groups
 form.variant_groups.forEach((group, groupIndex) => {
 if (group.name) {
 formData.append(`variant_groups[${groupIndex}][name]`, group.name)
 formData.append(`variant_groups[${groupIndex}][index]`, String(group.index))
 
 group.variants.forEach((variant, variantIndex) => {
 if (variant.name) {
 formData.append(`variant_groups[${groupIndex}][variants][${variantIndex}][name]`, variant.name)
 }
 })
 }
 })

 // SKUs
 form.skus.forEach((sku, skuIndex) => {
 if (sku.matrix && sku.price && sku.total_stock) {
 formData.append(`skus[${skuIndex}][matrix]`, sku.matrix)
 formData.append(`skus[${skuIndex}][price]`, String(sku.price))
 formData.append(`skus[${skuIndex}][total_stock]`, String(sku.total_stock))
 }
 })

 router.post(admin.products.update.url(props.product.id), formData, {
 onError: (responseErrors) => {
 errors.value = responseErrors
 isSubmitting.value = false
 },
 onSuccess: () => {
 isSubmitting.value = false
 }
 })
}

// Watch for changes in variant groups to regenerate SKUs
const watchVariantChanges = () => {
 setTimeout(() => generateSkus(), 100)
}

onMounted(() => {
 // Initialize categories and tags if empty
 if (form.categories.length === 0) {
 form.categories.push('')
 }
 if (form.tags.length === 0) {
 form.tags.push('')
 }
})
</script>