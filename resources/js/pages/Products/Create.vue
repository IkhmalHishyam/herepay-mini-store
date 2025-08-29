<template>
  <AppSidebarLayout title="Create Product" :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-semibold">Create New Product</h1>
          <p class="text-muted-foreground">Fill in the product details to create a new product</p>
        </div>
        <Button variant="outline" @click="router.visit(admin.products.index())">
          <ArrowLeft class="h-4 w-4 mr-2" />
          Back to Products
        </Button>
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
                <label for="is_published" class="text-sm font-medium">Publish product immediately</label>
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
          
          <!-- Thumbnail -->
          <div class="mb-6">
            <label class="block text-sm font-medium mb-2">Thumbnail Image</label>
            <div class="flex items-start space-x-4">
              <div 
                class="w-24 h-24 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center cursor-pointer hover:border-gray-400 transition-colors"
                @click="thumbnailInputRef?.click()"
              >
                <div v-if="thumbnailPreview" class="w-full h-full rounded-lg overflow-hidden">
                  <img :src="thumbnailPreview" alt="Thumbnail preview" class="w-full h-full object-cover" />
                </div>
                <div v-else class="text-center">
                  <ImageIcon class="h-6 w-6 mx-auto mb-1" />
                  <p class="text-xs text-gray-500">Upload</p>
                </div>
              </div>
              <div class="flex-1">
                <input
                  ref="thumbnailInputRef"
                  type="file"
                  accept="image/*"
                  class="hidden"
                  @change="handleThumbnailUpload"
                />
                <p class="text-sm text-muted-foreground mb-2">
                  Upload a thumbnail image for your product. Maximum file size: 5MB.
                </p>
                <div class="flex space-x-2">
                  <Button 
                    type="button" 
                    variant="outline" 
                    size="sm"
                    @click="thumbnailInputRef?.click()"
                  >
                    Choose File
                  </Button>
                  <Button 
                    v-if="form.product_thumbnail" 
                    type="button" 
                    variant="outline" 
                    size="sm"
                    @click="removeThumbnail"
                  >
                    Remove
                  </Button>
                </div>
              </div>
            </div>
            <p v-if="errors.product_thumbnail" class="text-sm text-red-600 mt-1">{{ errors.product_thumbnail }}</p>
          </div>

          <!-- Additional Images -->
          <div>
            <label class="block text-sm font-medium mb-2">Additional Images</label>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-4">
              <!-- Existing Images -->
              <div 
                v-for="(image, index) in imagesPreviews" 
                :key="index"
                class="relative aspect-square border-2 border-gray-200 rounded-lg overflow-hidden group"
              >
                <img :src="image" alt="Product image" class="w-full h-full object-cover" />
                <button
                  type="button"
                  @click="removeImage(index)"
                  class="absolute top-1 right-1 bg-red-500 rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity"
                >
                  <X class="h-3 w-3" />
                </button>
              </div>
              
              <!-- Upload New -->
              <div 
                class="aspect-square border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center cursor-pointer hover:border-gray-400 transition-colors"
                @click="imagesInputRef?.click()"
              >
                <div class="text-center">
                  <Plus class="h-6 w-6 mx-auto mb-1" />
                  <p class="text-xs text-gray-500">Add Image</p>
                </div>
              </div>
            </div>
            
            <input
              ref="imagesInputRef"
              type="file"
              accept="image/*"
              multiple
              class="hidden"
              @change="handleImagesUpload"
            />
            <p class="text-sm text-muted-foreground">
              Upload additional product images. Maximum 5MB per image.
            </p>
            <p v-if="errors.product_images" class="text-sm text-red-600 mt-1">{{ errors.product_images }}</p>
          </div>
        </div>

        <!-- Categories & Tags -->
        <div class="rounded-xl border border-sidebar-border/70 bg-card p-6 border-sidebar-border">
          <h3 class="text-lg font-semibold mb-4">Categories & Tags</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                    v-if="form.categories.length > 1"
                  >
                    <X class="h-4 w-4" />
                  </Button>
                </div>
                <Button 
                  type="button" 
                  variant="outline" 
                  size="sm"
                  @click="addCategory"
                  class="w-full"
                >
                  <Plus class="h-4 w-4 mr-2" />
                  Add Category
                </Button>
              </div>
              <p v-if="errors.categories" class="text-sm text-red-600 mt-1">{{ errors.categories }}</p>
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
                    v-if="form.tags.length > 1"
                  >
                    <X class="h-4 w-4" />
                  </Button>
                </div>
                <Button 
                  type="button" 
                  variant="outline" 
                  size="sm"
                  @click="addTag"
                  class="w-full"
                >
                  <Plus class="h-4 w-4 mr-2" />
                  Add Tag
                </Button>
              </div>
              <p v-if="errors.tags" class="text-sm text-red-600 mt-1">{{ errors.tags }}</p>
            </div>
          </div>
        </div>

        <!-- Variant Groups -->
        <div class="rounded-xl border border-sidebar-border/70 bg-card p-6 border-sidebar-border">
          <div class="flex items-center justify-between mb-4">
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
            <Grid3X3 class="h-12 w-12 mx-auto mb-2" />
            <p>No variants defined. Add a variant group to start.</p>
          </div>

          <div v-else class="space-y-6">
            <div 
              v-for="(group, groupIndex) in form.variant_groups" 
              :key="groupIndex"
              class="border rounded-lg p-4"
            >
              <div class="flex items-center justify-between mb-4">
                <div class="flex-1 mr-4 space-y-2">
                  <div class="flex items-center gap-2">
                    <div class="flex-1">
                      <Input
                        v-model="group.name"
                        placeholder="Variant group name (e.g., Size, Color)"
                        class="font-medium"
                        @input="generateSkus"
                      />
                      <p v-if="errors[`variant_groups.${groupIndex}.name`]" class="text-sm text-red-600 mt-1">
                        {{ errors[`variant_groups.${groupIndex}.name`] }}
                      </p>
                    </div>
                    <div class="w-20">
                      <Input
                        v-model="group.index"
                        type="number"
                        min="0"
                        placeholder="0"
                        class="text-sm"
                        @input="generateSkus"
                      />
                    </div>
                    <Button 
                      type="button" 
                      variant="outline" 
                      size="sm"
                      @click="removeVariantGroup(groupIndex)"
                      class="text-red-600 hover:text-red-700"
                    >
                      <Trash2 class="h-4 w-4" />
                    </Button>
                  </div>
                </div>
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
                      @input="generateSkus"
                    />
                    <Button
                      type="button"
                      variant="outline"
                      size="sm"
                      @click="removeVariant(groupIndex, variantIndex)"
                      class="text-red-600 hover:text-red-700"
                      v-if="group.variants.length > 1"
                    >
                      <X class="h-4 w-4" />
                    </Button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- SKU Matrix Management -->
        <div v-if="form.skus.length > 0" class="rounded-xl border border-sidebar-border/70 bg-card p-6 border-sidebar-border">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold">SKU Pricing & Inventory</h3>
            <div class="text-sm text-muted-foreground">
              {{ form.skus.length }} variant combination{{ form.skus.length !== 1 ? 's' : '' }}
            </div>
          </div>

          <div class="space-y-3">
            <div 
              v-for="(sku, skuIndex) in form.skus" 
              :key="sku.matrix"
              class="grid grid-cols-1 lg:grid-cols-3 gap-4 p-4 border rounded-lg bg-gray-50"
            >
              <!-- Matrix Display -->
              <div>
                <label class="block text-xs font-medium mb-1">Variant Combination</label>
                <div class="px-3 py-2 bg-white border rounded text-sm font-mono">
                  {{ sku.matrix }}
                </div>
              </div>

              <!-- Price -->
              <div>
                <label class="block text-xs font-medium mb-1">Price (RM) *</label>
                <Input
                  v-model="sku.price"
                  type="number"
                  step="0.01"
                  min="0"
                  placeholder="0.00"
                  class="text-sm"
                />
                <p v-if="errors[`skus.${skuIndex}.price`]" class="text-xs text-red-600 mt-1">
                  {{ errors[`skus.${skuIndex}.price`] }}
                </p>
              </div>

              <!-- Stock -->
              <div>
                <label class="block text-xs font-medium mb-1">Stock *</label>
                <Input
                  v-model="sku.total_stock"
                  type="number"
                  min="0"
                  placeholder="0"
                  class="text-sm"
                />
                <p v-if="errors[`skus.${skuIndex}.total_stock`]" class="text-xs text-red-600 mt-1">
                  {{ errors[`skus.${skuIndex}.total_stock`] }}
                </p>
              </div>
            </div>
          </div>
          <div class="mt-4 p-3 bg-blue-50 rounded-lg">
            <p class="text-sm text-blue-800">
              <span class="font-medium">💡 Tip:</span> 
              SKU matrices are automatically generated based on your variant combinations. 
              The format is: <code class="bg-blue-100 px-1 rounded">product-name:variant1:variant2:...</code>
            </p>
          </div>
        </div>

        <!-- Submit Actions -->
        <div class="flex items-center justify-end space-x-4 pt-4">
          <Button 
            type="button" 
            variant="outline"
            @click="router.visit(admin.products.index())"
          >
            Cancel
          </Button>
          <Button 
            type="submit" 
            :disabled="processing"
          >
            <span v-if="processing">Creating...</span>
            <span v-else>Create Product</span>
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
import { 
  ArrowLeft, 
  ImageIcon, 
  Plus, 
  X, 
  Trash2,
  Grid3X3,
  RefreshCw
} from 'lucide-vue-next'
import { ref, reactive } from 'vue'
import { router } from '@inertiajs/vue3'
import type { BreadcrumbItemType } from '@/types'
import admin from '@/routes/admin'

interface Variant {
  name: string
}

interface VariantGroup {
  name: string
  index: number
  variants: Variant[]
}

interface Sku {
  matrix: string
  price: number | string
  total_stock: number | string
}

interface ProductForm {
  name: string
  description: string
  price: number | string
  is_published: boolean
  product_thumbnail: File | null
  product_images: File[]
  categories: string[]
  tags: string[]
  variant_groups: VariantGroup[]
  skus: Sku[]
}

const breadcrumbs: BreadcrumbItemType[] = [
  {
    title: 'Products',
    href: admin.products.index.url()
  },
  {
    title: 'Create Product',
    href: admin.products.create.url()
  }
]

const thumbnailInputRef = ref<HTMLInputElement>()
const imagesInputRef = ref<HTMLInputElement>()
const thumbnailPreview = ref<string>()
const imagesPreviews = ref<string[]>([])
const processing = ref(false)
const errors = ref<Record<string, string>>({})

const form = reactive<ProductForm>({
  name: '',
  description: '',
  price: '',
  is_published: false,
  product_thumbnail: null,
  product_images: [],
  categories: [''],
  tags: [''],
  variant_groups: [],
  skus: []
})

const handleThumbnailUpload = (event: Event) => {
  const target = event.target as HTMLInputElement
  const file = target.files?.[0]
  if (file) {
    form.product_thumbnail = file
    const reader = new FileReader()
    reader.onload = (e) => {
      thumbnailPreview.value = e.target?.result as string
    }
    reader.readAsDataURL(file)
  }
}

const removeThumbnail = () => {
  form.product_thumbnail = null
  thumbnailPreview.value = undefined
  if (thumbnailInputRef.value) {
    thumbnailInputRef.value.value = ''
  }
}

const handleImagesUpload = (event: Event) => {
  const target = event.target as HTMLInputElement
  const files = Array.from(target.files || [])
  
  files.forEach(file => {
    form.product_images.push(file)
    const reader = new FileReader()
    reader.onload = (e) => {
      imagesPreviews.value.push(e.target?.result as string)
    }
    reader.readAsDataURL(file)
  })
}

const removeImage = (index: number) => {
  form.product_images.splice(index, 1)
  imagesPreviews.value.splice(index, 1)
}

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

const addVariantGroup = () => {
  form.variant_groups.push({
    name: '',
    index: form.variant_groups.length,
    variants: []
  })
  generateSkus()
}

const removeVariantGroup = (index: number) => {
  form.variant_groups.splice(index, 1)
  // Re-index remaining groups
  form.variant_groups.forEach((group, idx) => {
    group.index = idx
  })
  generateSkus()
}

const addVariant = (groupIndex: number) => {
  form.variant_groups[groupIndex].variants.push({
    name: ''
  })
  generateSkus()
}

const removeVariant = (groupIndex: number, variantIndex: number) => {
  form.variant_groups[groupIndex].variants.splice(variantIndex, 1)
  generateSkus()
}

const generateSkus = () => {
  // Only generate SKUs if we have variants
  if (form.variant_groups.length === 0) {
    form.skus = []
    return
  }

  // Filter out groups with no variants or empty names
  const validGroups = form.variant_groups.filter(group => 
    group.name.trim() && group.variants.some(v => v.name.trim())
  )

  if (validGroups.length === 0) {
    form.skus = []
    return
  }

  // Generate all possible combinations
  const combinations: string[][] = []
  
  const generateCombinations = (groupIndex: number, currentCombination: string[]) => {
    if (groupIndex >= validGroups.length) {
      combinations.push([...currentCombination])
      return
    }
    
    const group = validGroups[groupIndex]
    const validVariants = group.variants.filter(v => v.name.trim())
    
    if (validVariants.length === 0) {
      // Skip this group if no valid variants
      generateCombinations(groupIndex + 1, currentCombination)
    } else {
      for (const variant of validVariants) {
        generateCombinations(groupIndex + 1, [...currentCombination, variant.name.trim()])
      }
    }
  }

  generateCombinations(0, [])

  // Create SKU matrix strings and preserve existing prices/stock
  const existingSkus = new Map(form.skus.map(sku => [sku.matrix, sku]))
  
  form.skus = combinations.map(combination => {
    // Sort groups by index to ensure consistent matrix format
    validGroups.sort((a, b) => a.index - b.index)
    const sortedCombination = combination

    const matrix = `${form.name || 'product'}:${sortedCombination.join(':')}`
    
    // Preserve existing values or set defaults
    const existing = existingSkus.get(matrix)
    return {
      matrix,
      price: existing?.price || form.price || '',
      total_stock: existing?.total_stock || ''
    }
  })
}

const handleSubmit = () => {
  processing.value = true
  errors.value = {}

  // Create FormData for file uploads
  const formData = new FormData()
  
  // Basic fields
  formData.append('name', form.name)
  formData.append('description', form.description)
  formData.append('price', String(form.price))
  formData.append('is_published', form.is_published ? '1' : '0')
  
  // Files
  if (form.product_thumbnail) {
    formData.append('product_thumbnail', form.product_thumbnail)
  }
  
  form.product_images.forEach((file, index) => {
    formData.append(`product_images[${index}]`, file)
  })
  
  // Categories (filter empty ones)
  const validCategories = form.categories.filter(cat => cat.trim())
  validCategories.forEach((category, index) => {
    formData.append(`categories[${index}]`, category)
  })
  
  // Tags (filter empty ones) 
  const validTags = form.tags.filter(tag => tag.trim())
  validTags.forEach((tag, index) => {
    formData.append(`tags[${index}]`, tag)
  })
  
  // Variant groups
  form.variant_groups.forEach((group, groupIndex) => {
    if (group.name.trim()) {
      formData.append(`variant_groups[${groupIndex}][name]`, group.name)
      formData.append(`variant_groups[${groupIndex}][index]`, String(group.index))
      group.variants.forEach((variant, variantIndex) => {
        formData.append(`variant_groups[${groupIndex}][variants][${variantIndex}][name]`, variant.name)
      })
    }
  })

  // SKUs
  form.skus.forEach((sku, skuIndex) => {
    formData.append(`skus[${skuIndex}][matrix]`, sku.matrix)
    formData.append(`skus[${skuIndex}][price]`, String(sku.price))
    formData.append(`skus[${skuIndex}][total_stock]`, String(sku.total_stock))
  })

  router.post(admin.products.store.url(), formData, {
    forceFormData: true,
    onSuccess: () => {
      // Success handled by redirect in controller
    },
    onError: (responseErrors) => {
      errors.value = responseErrors
      processing.value = false
    },
    onFinish: () => {
      processing.value = false
    }
  })
}
</script>