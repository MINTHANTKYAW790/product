<template>
  <div class="dashboard">
    <header class="header">
      <h1>Order & Inventory System</h1>
      <div class="user-info">
        <span>Welcome, {{ user?.name }}</span>
        <button @click="handleLogout" class="logout-btn">Logout</button>
      </div>
    </header>
    
    <div class="content">
      <div class="products-section">
        <h2>Available Products</h2>
        
        <div v-if="loading" class="loading">Loading products...</div>
        <div v-else-if="error" class="error">{{ error }}</div>
        
        <div v-else class="products-grid">
          <div 
            v-for="product in products" 
            :key="product.id" 
            class="product-card"
            :class="{ 'selected': isSelected(product.id) }"
            @click="toggleSelection(product)"
          >
            <h3>{{ product.name }}</h3>
            <p class="price">${{ product.price }}</p>
            <p class="stock">Stock: {{ product.stock }}</p>
            <div class="quantity-control" @click.stop>
              <button @click="decreaseQuantity(product.id)" :disabled="!getQuantity(product.id)">-</button>
              <span>{{ getQuantity(product.id) || 0 }}</span>
              <button @click="increaseQuantity(product.id)" :disabled="getQuantity(product.id) >= product.stock">+</button>
            </div>
          </div>
        </div>
      </div>
      
      <div class="orders-section">
        <h2>Your Cart</h2>
        
        <div v-if="cart.length === 0" class="empty-cart">
          <p>No items in cart</p>
          <p>Click on products to add them</p>
        </div>
        
        <div v-else>
          <div class="cart-items">
            <div v-for="item in cart" :key="item.product.id" class="cart-item">
              <div class="item-info">
                <h4>{{ item.product.name }}</h4>
                <p>${{ item.product.price }} x {{ item.quantity }}</p>
              </div>
              <div class="item-total">
                ${{ (item.product.price * item.quantity).toFixed(2) }}
              </div>
            </div>
          </div>
          
          <div class="cart-total">
            <h3>Total: ${{ cartTotal.toFixed(2) }}</h3>
          </div>
          
          <button 
            @click="placeOrder" 
            :disabled="ordering || cart.length === 0"
            class="order-btn"
          >
            {{ ordering ? 'Placing Order...' : 'Place Order' }}
          </button>
        </div>
        
        <div v-if="orderMessage" :class="['order-message', orderMessage.type]">
          {{ orderMessage.text }}
        </div>
      </div>
    </div>
    
    <div class="orders-history">
      <h2>Order History</h2>
      
      <div v-if="ordersLoading" class="loading">Loading orders...</div>
      <div v-else-if="orders.length === 0" class="empty-cart">
        <p>No orders yet</p>
      </div>
      
      <div v-else class="orders-list">
        <div v-for="order in orders" :key="order.id" class="order-card">
          <div class="order-header">
            <span class="order-id">Order #{{ order.id }}</span>
            <span class="order-date">{{ formatDate(order.created_at) }}</span>
          </div>
          <div class="order-items">
            <div v-for="item in order.order_items" :key="item.id" class="order-item">
              <span>{{ item.product?.name || 'Product' }}</span>
              <span>x{{ item.quantity }}</span>
              <span>${{ item.price }}</span>
            </div>
          </div>
          <div class="order-total">
            Total: ${{ order.total_price }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()

const user = ref(null)
const products = ref([])
const orders = ref([])
const cart = ref([])
const loading = ref(false)
const ordering = ref(false)
const ordersLoading = ref(false)
const error = ref('')
const orderMessage = ref(null)

const API_URL = 'http://localhost:8000/api'

const getAuthHeader = () => ({
  headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
})

onMounted(async () => {
  const storedUser = localStorage.getItem('user')
  if (storedUser) {
    user.value = JSON.parse(storedUser)
  }
  await fetchProducts()
  await fetchOrders()
})

const fetchProducts = async () => {
  loading.value = true
  try {
    const response = await axios.get(`${API_URL}/products`)
    products.value = response.data.products
  } catch (err) {
    error.value = 'Failed to load products'
  } finally {
    loading.value = false
  }
}

const fetchOrders = async () => {
  ordersLoading.value = true
  try {
    const response = await axios.get(`${API_URL}/orders`, getAuthHeader())
    orders.value = response.data.orders
  } catch (err) {
    console.error('Failed to load orders')
  } finally {
    ordersLoading.value = false
  }
}

const isSelected = (productId) => {
  return cart.value.some(item => item.product.id === productId)
}

const getQuantity = (productId) => {
  const item = cart.value.find(item => item.product.id === productId)
  return item ? item.quantity : 0
}

const toggleSelection = (product) => {
  const existingIndex = cart.value.findIndex(item => item.product.id === product.id)
  
  if (existingIndex === -1) {
    cart.value.push({ product, quantity: 1 })
  }
}

const increaseQuantity = (productId) => {
  const item = cart.value.find(item => item.product.id === productId)
  if (item && item.quantity < item.product.stock) {
    item.quantity++
  }
}

const decreaseQuantity = (productId) => {
  const index = cart.value.findIndex(item => item.product.id === productId)
  if (index !== -1) {
    if (cart.value[index].quantity > 1) {
      cart.value[index].quantity--
    } else {
      cart.value.splice(index, 1)
    }
  }
}

const cartTotal = computed(() => {
  return cart.value.reduce((total, item) => {
    return total + (item.product.price * item.quantity)
  }, 0)
})

const placeOrder = async () => {
  ordering.value = true
  orderMessage.value = null
  
  try {
    const items = cart.value.map(item => ({
      product_id: item.product.id,
      quantity: item.quantity
    }))
    
    await axios.post(`${API_URL}/orders`, { items }, getAuthHeader())
    
    orderMessage.value = {
      type: 'success',
      text: 'Order placed successfully!'
    }
    
    cart.value = []
    await fetchProducts()
    await fetchOrders()
  } catch (err) {
    orderMessage.value = {
      type: 'error',
      text: err.response?.data?.message || 'Failed to place order'
    }
  } finally {
    ordering.value = false
  }
}

const handleLogout = async () => {
  try {
    await axios.post(`${API_URL}/logout`, {}, getAuthHeader())
  } catch (err) {
    console.error('Logout error')
  }
  
  localStorage.removeItem('token')
  localStorage.removeItem('user')
  router.push('/')
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}
</script>

<style scoped>
.dashboard {
  min-height: 100vh;
  background-color: #f5f5f5;
}

.header {
  background-color: #4CAF50;
  color: white;
  padding: 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.header h1 {
  font-size: 24px;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 20px;
}

.logout-btn {
  padding: 8px 16px;
  background-color: white;
  color: #4CAF50;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-weight: 500;
}

.logout-btn:hover {
  background-color: #f0f0f0;
}

.content {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 20px;
  padding: 20px;
  max-width: 1400px;
  margin: 0 auto;
}

.products-section, .orders-section {
  background: white;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

h2 {
  font-size: 20px;
  margin-bottom: 20px;
  color: #333;
}

.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 15px;
}

.product-card {
  border: 2px solid #ddd;
  border-radius: 8px;
  padding: 15px;
  cursor: pointer;
  transition: all 0.3s;
}

.product-card:hover {
  border-color: #4CAF50;
}

.product-card.selected {
  border-color: #4CAF50;
  background-color: #f9f9f9;
}

.product-card h3 {
  font-size: 16px;
  margin-bottom: 10px;
  color: #333;
}

.price {
  font-size: 18px;
  font-weight: bold;
  color: #4CAF50;
  margin-bottom: 5px;
}

.stock {
  font-size: 14px;
  color: #666;
  margin-bottom: 10px;
}

.quantity-control {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
}

.quantity-control button {
  width: 30px;
  height: 30px;
  border: 1px solid #ddd;
  background: white;
  border-radius: 4px;
  cursor: pointer;
}

.quantity-control button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.empty-cart {
  text-align: center;
  padding: 40px;
  color: #666;
}

.cart-items {
  margin-bottom: 20px;
}

.cart-item {
  display: flex;
  justify-content: space-between;
  padding: 10px 0;
  border-bottom: 1px solid #eee;
}

.item-info h4 {
  font-size: 14px;
  margin-bottom: 5px;
}

.item-info p {
  font-size: 12px;
  color: #666;
}

.item-total {
  font-weight: bold;
  color: #4CAF50;
}

.cart-total {
  text-align: right;
  font-size: 20px;
  font-weight: bold;
  margin-bottom: 20px;
  padding: 10px;
  background-color: #f9f9f9;
  border-radius: 4px;
}

.order-btn {
  width: 100%;
  padding: 15px;
  background-color: #4CAF50;
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 16px;
  cursor: pointer;
}

.order-btn:hover:not(:disabled) {
  background-color: #45a049;
}

.order-btn:disabled {
  background-color: #ccc;
  cursor: not-allowed;
}

.order-message {
  margin-top: 15px;
  padding: 10px;
  border-radius: 4px;
  text-align: center;
}

.order-message.success {
  background-color: #e8f5e9;
  color: #4CAF50;
}

.order-message.error {
  background-color: #ffebee;
  color: #f44336;
}

.orders-history {
  padding: 20px;
  max-width: 1400px;
  margin: 0 auto;
}

.orders-history h2 {
  background: white;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  margin-bottom: 20px;
}

.orders-list {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 15px;
}

.order-card {
  background: white;
  padding: 15px;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.order-header {
  display: flex;
  justify-content: space-between;
  margin-bottom: 10px;
  padding-bottom: 10px;
  border-bottom: 1px solid #eee;
}

.order-id {
  font-weight: bold;
}

.order-date {
  font-size: 12px;
  color: #666;
}

.order-items {
  margin-bottom: 10px;
}

.order-item {
  display: flex;
  justify-content: space-between;
  font-size: 14px;
  padding: 5px 0;
}

.order-total {
  font-weight: bold;
  color: #4CAF50;
  text-align: right;
}

.loading, .error {
  text-align: center;
  padding: 20px;
}

.error {
  color: #f44336;
}
</style>