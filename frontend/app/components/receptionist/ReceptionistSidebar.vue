<template>
  <aside class="fixed inset-y-0 left-0 w-72 bg-white border-r border-slate-100 flex flex-col z-30 overflow-hidden shadow-[10px_0_40_rgba(0,0,0,0.02)]">
    <!-- Sophisticated Background Pattern -->
    <div class="absolute inset-0 opacity-[0.03] pointer-events-none" style="background-image: url('https://www.transparenttextures.com/patterns/cubes.png');"></div>
    <div class="absolute -top-24 -left-24 w-64 h-64 bg-pink-50 rounded-full blur-[80px] opacity-40 pointer-events-none"></div>

    <!-- Brand Header -->
    <div class="p-8 relative z-10">
      <div class="flex items-center space-x-5">
        <div class="w-14 h-14 bg-slate-900 rounded-[1.5rem] flex items-center justify-center shadow-2xl shadow-slate-900/20 group-hover:rotate-6 transition-transform">
          <span class="text-2xl">🛎️</span>
        </div>
        <div>
          <h2 class="text-2xl font-black text-slate-900 tracking-tighter leading-none">GRAND LUXE</h2>
          <p class="text-[9px] font-black text-pink-500 tracking-[0.4em] uppercase mt-2">Front Desk</p>
        </div>
      </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-6 py-4 space-y-1 relative z-10 overflow-y-auto custom-scrollbar">
      <div class="pb-6 px-4">
        <p class="text-[10px] font-black text-slate-300 uppercase tracking-[0.25em]">Operasional</p>
      </div>

      <NuxtLink
        v-for="item in menuItems"
        :key="item.to"
        :to="item.to"
        class="group flex items-center space-x-4 px-6 py-4.5 rounded-2xl transition-all duration-300 relative"
        :class="route.path === item.to 
          ? 'bg-slate-900 text-white shadow-xl shadow-slate-900/10' 
          : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50'"
      >
        <span class="text-xl transition-transform duration-300 group-hover:scale-110">{{ item.icon }}</span>
        <span class="font-bold text-[11px] uppercase tracking-widest">{{ item.name }}</span>
        
        <div v-if="route.path === item.to" class="absolute right-4 w-1.5 h-1.5 bg-pink-500 rounded-full shadow-[0_0_10px_#ec4899]"></div>
      </NuxtLink>

      <div class="pt-12 pb-6 px-4">
        <p class="text-[10px] font-black text-slate-300 uppercase tracking-[0.25em]">Account Settings</p>
      </div>

      <NuxtLink
        to="/receptionist/profile"
        class="group flex items-center space-x-4 px-6 py-4.5 rounded-2xl transition-all duration-300 relative"
        :class="route.path === '/receptionist/profile' 
          ? 'bg-slate-900 text-white shadow-xl shadow-slate-900/10' 
          : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50'"
      >
        <span class="text-xl">👤</span>
        <span class="font-bold text-[11px] uppercase tracking-widest">Profil Saya</span>
      </NuxtLink>
    </nav>

    <!-- User Section -->
    <div class="p-6 border-t border-slate-50 relative z-10 bg-white/80 backdrop-blur-sm">
      <div class="flex items-center space-x-4 mb-8 p-4 bg-slate-50 rounded-3xl border border-slate-100">
        <div class="w-12 h-12 rounded-2xl bg-white flex items-center justify-center text-slate-900 font-black shadow-sm border border-slate-100">
          {{ user?.name ? user.name.charAt(0).toUpperCase() : 'R' }}
        </div>
        <div class="overflow-hidden">
          <p class="text-xs font-black text-slate-900 truncate">{{ user?.name || 'Receptionist' }}</p>
          <div class="flex items-center space-x-1.5 mt-1">
            <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></div>
            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Active Now</p>
          </div>
        </div>
      </div>
      
      <button @click="handleLogout" class="w-full py-4.5 bg-white border-2 border-slate-100 text-slate-400 hover:text-rose-600 hover:border-rose-100 rounded-2xl transition-all duration-300 font-black text-[10px] uppercase tracking-[0.2em] group">
        <span>Logout 🚪</span>
      </button>
    </div>
  </aside>
</template>

<script setup>
const route = useRoute()
const props = defineProps({
  user: Object
})

const menuItems = [
  { name: 'Data Pemesanan', to: '/receptionist/dashboard', icon: '🏠' },
  { name: 'Data Tamu', to: '/receptionist/datatamu', icon: '👥' },
  { name: 'Laporan Harian', to: '/receptionist/reports', icon: '📊' },
]

const handleLogout = async () => {
  if (!confirm('Apakah Anda yakin ingin keluar?')) return
  try {
    await $fetch('/api/logout', {
      method: 'POST',
      credentials: 'include'
    })
  } catch (err) {}
  navigateTo('/login')
}
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #fdf2f8;
  border-radius: 10px;
}
.custom-scrollbar:hover::-webkit-scrollbar-thumb {
  background: #fce7f3;
}
</style>
