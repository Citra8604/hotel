<template>
  <div class="min-h-screen flex bg-slate-50 relative overflow-hidden text-slate-800 lg:pl-72">
    <div class="absolute top-[20%] left-[50%] w-[800px] h-[800px] bg-pink-100 rounded-full mix-blend-multiply filter blur-[120px] opacity-40 pointer-events-none"></div>

    <AdminSidebar :user="user" />

    <main class="flex-1 p-6 md:p-10 relative z-10 overflow-y-auto">
      <header class="mb-10">
        <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight">Dashboard Admin</h1>
        <p class="text-pink-600 mt-2 text-sm font-medium">Sistem Pengelolaan Hotel 71</p>
      </header>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
        <div class="bg-white p-8 rounded-3xl shadow-xl shadow-pink-500/5 border border-pink-50 hover:shadow-pink-500/10 transition-all duration-300 group">
          <div class="flex items-center justify-between mb-4">
            <p class="text-sm text-slate-500 uppercase font-bold tracking-widest">Total Staf / Staf</p>
            <div class="w-12 h-12 bg-pink-50 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">👥</div>
          </div>
          <p class="text-5xl font-black text-slate-900">{{ stats.totalUser }}</p>
          <div class="mt-6 h-1.5 w-16 bg-gradient-to-r from-pink-400 to-rose-600 rounded-full"></div>
        </div>
        
        <div class="bg-white p-8 rounded-3xl shadow-xl shadow-pink-500/5 border border-pink-50 hover:shadow-pink-500/10 transition-all duration-300 group">
          <div class="flex items-center justify-between mb-4">
            <p class="text-sm text-slate-500 uppercase font-bold tracking-widest">Kamar Fisik Terdaftar</p>
            <div class="w-12 h-12 bg-rose-50 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">🛏️</div>
          </div>
          <p class="text-5xl font-black text-slate-900">{{ stats.totalKamar }}</p>
          <div class="mt-6 h-1.5 w-16 bg-gradient-to-r from-rose-400 to-fuchsia-600 rounded-full"></div>
        </div>
        
        <div class="bg-white p-8 rounded-3xl shadow-xl shadow-pink-500/5 border border-pink-50 hover:shadow-pink-500/10 transition-all duration-300 group">
          <div class="flex items-center justify-between mb-4">
            <p class="text-sm text-slate-500 uppercase font-bold tracking-widest">Estimasi Pendapatan</p>
            <div class="w-12 h-12 bg-fuchsia-50 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">💰</div>
          </div>
          <p class="text-3xl font-black text-slate-900">{{ formatPrice(stats.pendapatanBulanIni) }}</p>
          <div class="mt-6 h-1.5 w-16 bg-gradient-to-r from-fuchsia-400 to-pink-600 rounded-full"></div>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <NuxtLink to="/admin/room-types" class="bg-white border border-slate-100 p-6 rounded-2xl shadow-sm hover:shadow-md transition-all block">
          <h4 class="font-bold text-lg text-slate-800 mb-1">📋 Master Tipe Kamar</h4>
          <p class="text-xs text-slate-500">Kelola kategori beserta foto dan harga.</p>
        </NuxtLink>
        <NuxtLink to="/admin/rooms" class="bg-white border border-slate-100 p-6 rounded-2xl shadow-sm hover:shadow-md transition-all block">
          <h4 class="font-bold text-lg text-slate-800 mb-1">🔧 Kamar Fisik</h4>
          <p class="text-xs text-slate-500">Menej nomor kamar harian hotel.</p>
        </NuxtLink>
        <NuxtLink to="/admin/users" class="bg-white border border-slate-100 p-6 rounded-2xl shadow-sm hover:shadow-md transition-all block">
          <h4 class="font-bold text-lg text-slate-800 mb-1">👥 Kelola Akun</h4>
          <p class="text-xs text-slate-500">Registrasi hak akses staf resepsionis.</p>
        </NuxtLink>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import AdminSidebar from '~/components/admin/AdminSidebar.vue'

definePageMeta({ layout: false })

const user = ref({ name: 'Admin', role: 'admin' })

const stats = ref({
  totalTipeKamar: 3,
  totalKamar: 12,
  totalUser: 5,
  pendapatanBulanIni: 4850000
})

const formatPrice = (price) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(price || 0)

const fetchDashboardStats = async (token) => {
  try {
    const resUser = await $fetch('http://localhost:8000/api/users', { headers: { 'Authorization': `Bearer ${token}` } })
    const userData = resUser.data || resUser || []
    if (userData.length > 0) stats.value.totalUser = userData.length

    const resRooms = await $fetch('http://localhost:8000/api/rooms', { headers: { 'Authorization': `Bearer ${token}` } })
    const roomsData = resRooms.data || resRooms || []
    if (roomsData.length > 0) stats.value.totalKamar = roomsData.length

    const resBook = await $fetch('http://localhost:8000/api/pemesanan', { headers: { 'Authorization': `Bearer ${token}` } })
    const listBook = resBook.data || resBook || []
    const totalRev = listBook
      .filter(b => b.status === 'verified' || b.status === 'checked_in' || b.status === 'checked_out')
      .reduce((sum, b) => sum + Number(b.total_price || b.total_harga || 0), 0)
    
    if (totalRev > 0) stats.value.pendapatanBulanIni = totalRev

  } catch (err) {
    console.warn('Gagal memuat statistik realtime admin backend.')
  }
}

onMounted(() => {
  const localToken = localStorage.getItem('auth_token')
  const savedUser = localStorage.getItem('auth_user')
  if (savedUser) user.value = JSON.parse(savedUser)

  if (localToken) {
    fetchDashboardStats(localToken)
  } else {
    window.location.href = '/login'
  }
})
</script>