<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { 
  ShoppingCart, 
  DollarSign, 
  Package, 
  Clock, 
  TrendingUp, 
  Calendar,
  CheckCircle,
  Eye,
  ArrowRight
} from 'lucide-vue-next';

interface DashboardStats {
  totalOrders: number;
  completedOrders: number;
  totalRevenue: number;
  totalProducts: number;
  pendingOrders: number;
  thisMonthOrders: number;
  thisMonthCompleted: number;
  thisMonthRevenue: number;
}

interface RecentOrder {
  id: string;
  order_number: string;
  total_price: number;
  status: string;
  created_at: string;
  customer_name: string;
  invoice_status: string;
}

interface TopProduct {
  id: string;
  name: string;
  total_revenue: number;
  total_sold_in_units: number;
  last_sold_at: string | null;
}

const props = defineProps<{
  stats: DashboardStats;
  recentOrders: RecentOrder[];
  topProducts: TopProduct[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: dashboard().url,
  },
];

const formatPrice = (price: number) => {
  return price.toFixed(2);
};

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  });
};

const getStatusColor = (status: string) => {
  switch (status) {
    case 'processing':
    case 'shipped':
    case 'delivered':
      return 'bg-green-100 text-green-800';
    case 'pending':
      return 'bg-yellow-100 text-yellow-800';
    case 'cancelled':
      return 'bg-red-100 text-red-800';
    default:
      return 'bg-gray-100 text-gray-800';
  }
};

const getStatusLabel = (status: string) => {
  return status.charAt(0).toUpperCase() + status.slice(1);
};
</script>

<template>
  <Head title="Dashboard" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4">
      <!-- Welcome Header -->
      <div class="mb-2">
        <h1 class="text-3xl font-bold text-gray-900">Welcome back!</h1>
        <p class="text-gray-600">Here's what's happening with your store today.</p>
      </div>

      <!-- Statistics Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Total Orders -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">Total Orders</p>
              <p class="text-3xl font-bold text-gray-900">{{ stats.totalOrders }}</p>
            </div>
            <div class="rounded-full bg-blue-100 p-3">
              <ShoppingCart class="h-6 w-6 text-blue-600" />
            </div>
          </div>
        </div>

        <!-- Completed Orders -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">Completed Orders</p>
              <p class="text-3xl font-bold text-gray-900">{{ stats.completedOrders }}</p>
            </div>
            <div class="rounded-full bg-green-100 p-3">
              <CheckCircle class="h-6 w-6 text-green-600" />
            </div>
          </div>
        </div>

        <!-- Total Revenue -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">Total Revenue</p>
              <p class="text-3xl font-bold text-gray-900">RM{{ formatPrice(stats.totalRevenue) }}</p>
            </div>
            <div class="rounded-full bg-emerald-100 p-3">
              <DollarSign class="h-6 w-6 text-emerald-600" />
            </div>
          </div>
        </div>

        <!-- Total Products -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">Published Products</p>
              <p class="text-3xl font-bold text-gray-900">{{ stats.totalProducts }}</p>
            </div>
            <div class="rounded-full bg-purple-100 p-3">
              <Package class="h-6 w-6 text-purple-600" />
            </div>
          </div>
        </div>

        <!-- Pending Orders -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">Pending Orders</p>
              <p class="text-3xl font-bold text-gray-900">{{ stats.pendingOrders }}</p>
            </div>
            <div class="rounded-full bg-yellow-100 p-3">
              <Clock class="h-6 w-6 text-yellow-600" />
            </div>
          </div>
        </div>

        <!-- This Month Completed -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">This Month Completed</p>
              <p class="text-3xl font-bold text-gray-900">{{ stats.thisMonthCompleted }}</p>
            </div>
            <div class="rounded-full bg-indigo-100 p-3">
              <Calendar class="h-6 w-6 text-indigo-600" />
            </div>
          </div>
        </div>

        <!-- This Month Revenue -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">This Month Revenue</p>
              <p class="text-3xl font-bold text-gray-900">RM{{ formatPrice(stats.thisMonthRevenue) }}</p>
            </div>
            <div class="rounded-full bg-emerald-100 p-3">
              <TrendingUp class="h-6 w-6 text-emerald-600" />
            </div>
          </div>
        </div>
      </div>

      <!-- Main Content Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Orders -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                   <div class="mb-6">
           <h3 class="text-lg font-semibold text-gray-900">Recent Orders</h3>
         </div>
          
          <div v-if="recentOrders.length > 0" class="space-y-4">
            <div 
              v-for="order in recentOrders" 
              :key="order.id"
              class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
            >
              <div class="flex-1">
                <div class="flex items-center gap-3">
                  <p class="font-medium text-gray-900">{{ order.order_number }}</p>
                  <span 
                    :class="getStatusColor(order.status)"
                    class="px-2 py-1 text-xs font-medium rounded-full"
                  >
                    {{ getStatusLabel(order.status) }}
                  </span>
                </div>
                <p class="text-sm text-gray-600">{{ order.customer_name }}</p>
                <p class="text-xs text-gray-500">{{ formatDate(order.created_at) }}</p>
              </div>
              <div class="text-right">
                <p class="font-semibold text-gray-900">RM{{ formatPrice(order.total_price) }}</p>
                <p class="text-xs text-gray-500">{{ order.invoice_status }}</p>
              </div>
            </div>
          </div>
          
          <div v-else class="text-center py-8 text-gray-500">
            <ShoppingCart class="h-12 w-12 mx-auto mb-2 text-gray-300" />
            <p class="text-sm">No orders yet</p>
            <p class="text-xs">Orders will appear here once customers start shopping</p>
          </div>
        </div>

        <!-- Top Performing Products -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
          <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Top Performing Products</h3>
          </div>
          
          <div v-if="topProducts.length > 0" class="space-y-4">
            <div 
              v-for="product in topProducts" 
              :key="product.id"
              class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
            >
              <div class="flex-1">
                <p class="font-medium text-gray-900">{{ product.name }}</p>
                <p class="text-sm text-gray-600">{{ product.total_sold_in_units }} units sold</p>
                <p v-if="product.last_sold_at" class="text-xs text-gray-500">
                  Last sold: {{ formatDate(product.last_sold_at) }}
                </p>
              </div>
              <div class="text-right">
                <p class="font-semibold text-green-600">RM{{ formatPrice(product.total_revenue) }}</p>
                <p class="text-xs text-gray-500">Revenue</p>
              </div>
            </div>
          </div>
          
          <div v-else class="text-center py-8 text-gray-500">
            <Package class="h-12 w-12 mx-auto mb-2 text-gray-300" />
            <p class="text-sm">No sales data yet</p>
            <p class="text-xs">Product performance will appear here after sales</p>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
