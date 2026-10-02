<template>
  <!-- Menambahkan md:pl-64 lg:pl-72 agar seluruh area konten bergeser ke kanan sidebar -->
  <div class="min-h-screen bg-slate-50 text-slate-900 selection:bg-pink-500/30 font-sans overflow-x-hidden md:pl-64 lg:pl-72 transition-all duration-300">
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
      <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] bg-pink-100 rounded-full blur-[120px] opacity-60"></div>
      <div class="absolute bottom-0 right-[10%] w-[30%] h-[30%] bg-rose-50 rounded-full blur-[100px] opacity-40"></div>
      <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[60%] h-[60%] bg-pink-50/30 rounded-full blur-[150px] opacity-30"></div>
    </div>

    <!-- Sidebar Tamu -->
    <GuestNavbar activePage="dashboard" :userName="user?.name" @logout="handleLogout" />

    <!-- Area Konten utama dengan penyesuaian padding atas & samping -->
    <main class="relative pt-10 md:pt-14 pb-24 px-6 md:px-12 max-w-7xl mx-auto space-y-16">
      
      <section v-if="user" class="relative group">
        <div class="relative overflow-hidden p-8 md:p-14 bg-white/60 backdrop-blur-xl rounded-[3.5rem] border border-white shadow-2xl shadow-pink-500/10">
          <div class="absolute -top-24 -right-24 w-96 h-96 bg-pink-100/40 rounded-full blur-[100px] group-hover:scale-110 transition-transform duration-1000"></div>
          <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-rose-50/40 rounded-full blur-[100px]"></div>
          
          <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-12">
            <div class="space-y-6">
              <div class="inline-flex items-center gap-2 px-4 py-2 bg-pink-50 text-pink-600 rounded-full border border-pink-100">
                <span class="w-2 h-2 bg-pink-500 rounded-full animate-pulse"></span>
                <span class="text-[10px] font-black uppercase tracking-widest">Guest Dashboard</span>
              </div>
              <h2 class="text-4xl md:text-6xl font-black text-slate-900 tracking-tighter leading-tight uppercase">
                Selamat Datang kembali,<br/>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-pink-500 to-rose-600">{{ user.name }}</span>
              </h2>
              <div class="flex flex-wrap items-center gap-6">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center text-lg">📧</div>
                  <div>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Email Terdaftar</p>
                    <p class="text-xs font-bold text-slate-700">{{ user.email }}</p>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4 lg:w-80">
              <div class="bg-white/80 p-6 rounded-3xl border border-white shadow-sm hover:translate-y-[-4px] transition-transform">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Stay</p>
                <h3 class="text-3xl font-black text-slate-900">{{ userReservations.length }}</h3>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section>
        <div class="flex justify-between items-end mb-10">
          <div>
            <div class="flex items-center gap-3 mb-2">
              <span class="w-6 h-1 bg-pink-500 rounded-full"></span>
              <p class="text-[10px] font-black text-pink-500 uppercase tracking-[0.4em]">Status Perjalanan</p>
            </div>
            <h2 class="text-3xl font-black text-slate-900 tracking-tight uppercase">Reservasi Saya</h2>
          </div>
          <div class="flex items-center gap-4">
             <button @click="fetchReservations" class="p-3 bg-white border border-slate-100 rounded-2xl hover:bg-pink-50 hover:text-pink-500 transition-all shadow-sm text-slate-400 group cursor-pointer">
              🔄 Refresh
            </button>
            <NuxtLink to="/profile" class="px-5 py-3 bg-white border border-slate-100 rounded-2xl text-[10px] font-black text-slate-400 hover:text-pink-500 uppercase tracking-widest transition-all shadow-sm">Lihat Profil & History →</NuxtLink>
          </div>
        </div>

        <div v-if="userReservations.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <div v-for="res in userReservations" :key="res.id" class="bg-white group p-8 rounded-[3rem] border border-pink-50 shadow-xl relative overflow-hidden transition-all">
            <div class="relative z-10 flex flex-col h-full">
              <div class="flex justify-between items-start mb-6">
                <div class="w-14 h-14 rounded-2xl bg-pink-50 flex items-center justify-center text-2xl shadow-sm">🏢</div>
                <span class="text-[9px] font-black uppercase px-4 py-2 bg-amber-50 text-amber-600 rounded-xl border tracking-widest">{{ res.status }}</span>
              </div>
              <div class="mb-6">
                <p class="text-[9px] font-black text-pink-500 uppercase tracking-widest mb-1">{{ res.reservation_number }}</p>
                <h4 class="text-xl font-black text-slate-900 uppercase tracking-tight">{{ res.room_type?.name }}</h4>
              </div>
              <div class="grid grid-cols-2 gap-4 mt-auto">
                <div class="bg-slate-50/50 p-4 rounded-2xl border"><p class="text-[8px] font-black text-slate-400 uppercase">Check In</p><p class="text-xs font-bold text-slate-700">{{ res.check_in }}</p></div>
                <div class="bg-slate-50/50 p-4 rounded-2xl border"><p class="text-[8px] font-black text-slate-400 uppercase">Check Out</p><p class="text-xs font-bold text-slate-700">{{ res.check_out }}</p></div>
              </div>
            </div>
          </div>
        </div>
        
        <div v-else class="text-center py-20 bg-white/40 border-2 border-dashed border-pink-100 rounded-[3.5rem]">
           <p class="text-sm text-slate-500 font-medium">Belum ada riwayat reservasi kamar.</p>
        </div>
      </section>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const user = ref(null)
const userReservations = ref([])

const fetchUser = async () => {
  try {
    const token = localStorage.getItem('auth_token')
    user.value = await $fetch('http://localhost:8000/api/user', { 
      headers: token ? { 'Authorization': `Bearer ${token}` } : {}
    })
  } catch (err) {
    window.location.href = '/login'
  }
}

const fetchReservations = async () => {
  try {
    const token = localStorage.getItem('auth_token')
    const response = await $fetch('http://localhost:8000/api/reservations/my', { 
      headers: token ? { 'Authorization': `Bearer ${token}` } : {}
    })
    userReservations.value = response.data || response || []
  } catch (err) {
    console.error(err)
  }
}

const handleLogout = () => {
  localStorage.removeItem('auth_token')
  window.location.href = '/login'
}

onMounted(async () => {
  await fetchUser()
  await fetchReservations()
})
</script>

<style scoped>
.page-enter-active, .page-leave-active {
  transition: all 0.5s ease;
}
.page-enter-from, .page-leave-to {
  opacity: 0;
  transform: translateY(20px);
}
</style>