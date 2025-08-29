# Herepay Mini Store - Technical Assessment

A comprehensive e-commerce platform built for Herepay Tech Team assessment. This project demonstrates advanced Laravel and Vue.js development with full e-commerce functionality.

## 🛠 Tech Stack

### Backend
- **Laravel 12** - Latest Laravel framework
- **PHP 8.2+** - Modern PHP with type declarations
- **MySQL/SQLite** - Database with ULID primary keys
- **Inertia.js** - Server-side rendering for SPA experience
- **Laravel Breeze** - Authentication scaffolding

### Frontend
- **Vue 3** - Latest Vue with Composition API
- **TypeScript** - Full type safety
- **TailwindCSS v4** - Modern CSS framework
- **Reka UI** - Vue component library
- **Vite** - Modern build tool
- **Vue Sonner** - Toast notifications

### Development Tools
- **ESLint + Prettier** - Code quality and formatting
- **Laravel Pail** - Log viewing
- **Laravel Wayfinder** - Type-safe routing

## 📦 Key Packages

### Backend Dependencies
```json
{
  "laravel/framework": "^12.0",
  "inertiajs/inertia-laravel": "^2.0",
  "laravel/wayfinder": "^0.1.9",
  "laravel/pint": "^1.18"
}
```

### Frontend Dependencies
```json
{
  "vue": "^3.5.13",
  "typescript": "^5.2.2",
  "@inertiajs/vue3": "^2.1.0",
  "reka-ui": "^2.2.0",
  "tailwindcss": "^4.1.1",
  "vue-sonner": "^2.0.8",
  "lucide-vue-next": "^0.468.0",
  "@vueuse/core": "^12.8.2",
  "dompurify": "^3.2.6"
}
```

## CORE FEATURE

# ADMIN
- Login
- Dashboard Statistic
- Product Management
-- Group Variant
-- Variant
-- SKU Matrix

# CUSTOMER
- LOGIN
- STORE BROWSING
- CART SYSTEM
- FAKE SIMPLE ORDERING SIMULATOR

# FUTURE IMPROVEMENT
- Comprehensive order flow
- Comprehensive product & order statistic
- Customer Order Management
- Customer analytic
- Admin Order Management
- Product Wishlist/Favourite feature
- Payment Gateway
- Real-time order status
- Caching
- Customer feedback
- Guest Order

## 🗄️ Database Tables

### Core Tables
- **users** - User accounts with role-based access
- **products** - Product catalog with variants
- **sku** - Stock keeping units for variants
- **variant_groups** - Product variant categories
- **variants** - Individual variant options

### Cart & Orders
- **carts** - Shopping cart sessions
- **cart_products** - Cart item relationships
- **orders** - Customer order records
- **order_details** - Order metadata
- **order_products** - Order item details

### Catalog Management
- **categories** - Product categories
- **tags** - Product tags
- **product_categories** - Product-category relationships
- **product_tags** - Product-tag relationships

### File Management
- **files** - Polymorphic file storage
- **file_snapshots** - File version control

### Customer Data
- **shipping_addresses** - Customer shipping info
- **customer_stats** - Customer analytics

### Payments & Analytics
- **invoices** - Invoice generation
- **transactions** - Payment records
- **product_stats** - Product analytics

### Default Users
- **Admin**: admin@herepay.com / password
- **Customer**: customer@example.com / password

## 📖 Documentation

- **ERD**: `docs/Herepay-Store-System-ERD.png`
- **Use Cases**: `docs/Herepay-Store-System-UCD.png`
- **Requirements**: `docs/Herepay Mini Store Challenge.pdf`

## 📧 Assessment Contact

**Herepay Tech Team Assessment**
- **Deadline**: August 27, 2025 (Friday 1 PM)
- **Contacts**: 
  - aliff.rosli96@gmail.com
  - adisazizan@gmail.com
  - syahrul1414@gmail.com

---