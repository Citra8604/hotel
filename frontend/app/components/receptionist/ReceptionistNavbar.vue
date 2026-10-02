<template>
  <nav class="bg-white/80 backdrop-blur-xl border-b border-pink-100 sticky top-0 z-50 px-6 py-4 shadow-sm shadow-pink-500/5">
    <div class="max-w-7xl mx-auto flex items-center justify-between">
      <!-- Brand -->
      <div class="flex items-center space-x-3">
        <div class="flex items-center justify-center w-10 h-10 rounded-2xl bg-gradient-to-tr from-pink-500 to-rose-500 shadow-lg shadow-pink-500/20">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
          </svg>
        </div>
        <h2 class="text-xl font-black bg-clip-text text-transparent bg-gradient-to-r from-pink-600 to-rose-600 tracking-tight hidden sm:block">Grandeur Front</h2>
      </div>

      <!-- Links -->
      <div class="flex items-center space-x-1 lg:space-x-4">
        <NuxtLink
          v-for="link in navLinks"
          :key="link.to"
          :to="link.to"
          class="flex items-center space-x-2 py-2.5 px-4 rounded-xl transition-all duration-300 text-xs font-black uppercase tracking-widest"
          :class="route.path === link.to ? 'bg-pink-50 text-pink-600 border border-pink-100 shadow-sm' : 'text-slate-500 hover:text-pink-600 hover:bg-pink-50/50'"
        >
          <span class="text-lg">{{ link.icon }}</span>
          <span class="hidden md:inline">{{ link.name }}</span>
        </NuxtLink>
      </div>

      <!-- User & Logout -->
      <div class="flex items-center space-x-4">
        <div class="hidden lg:flex items-center space-x-3 pr-4 border-r border-slate-100">
          <div class="text-right">
            <p class="text-[10px] font-black text-slate-900 leading-tight">Receptionist</p>
            <p class="text-[8px] text-pink-500 font-black uppercase tracking-widest mt-0.5">Front Desk</p>
          </div>
          <div class="h-9 w-9 rounded-xl bg-gradient-to-tr from-pink-50 to-rose-50 flex items-center justify-center text-pink-500 font-black text-sm border border-pink-100">
            R
          </div>
        </div>
        <button @click="handleLogout" class="p-2.5 bg-rose-50 text-rose-500 hover:bg-rose-100 rounded-xl transition-all border border-rose-100 shadow-sm shadow-rose-500/5 group" title="Logout">
          <span class="text-xl group-hover:scale-110 transition-transform block">🚪</span>
        </button>
      </div>
    </div>
  </nav>
</template>

<script setup>
const route = useRoute()

const navLinks = [
  { name: 'Front Desk', to: '/receptionist/dashboard', icon: '🏠' },
  { name: 'Tamu', to: '/receptionist/datatamu', icon: '👥' },
  { name: 'Laporan', to: '/receptionist/reports', icon: '📊' },
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
