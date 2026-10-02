<template>
  <div class="min-h-screen flex bg-white font-sans overflow-hidden">
    <!-- Banner Samping Kiri -->
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden group">
      <img 
        src="/_antigravity/luxury_hotel_lobby_login.png" 
        alt="Luxury Hotel Lobby" 
        class="absolute inset-0 w-full h-full object-cover transition-transform duration-[3s] group-hover:scale-110"
      >
      <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/20 to-transparent"></div>
      
      <div class="absolute bottom-20 left-20 right-20 space-y-6 animate-fade-up">
        <div class="w-20 h-1 bg-pink-500 rounded-full"></div>
        <h2 class="text-6xl font-black text-white tracking-tighter uppercase leading-none">
          Seni Hidup <br/>
          <span class="text-transparent bg-clip-text bg-gradient-to-r from-pink-400 to-rose-400">Paling Mewah</span>
        </h2>
        <p class="text-slate-300 font-bold uppercase tracking-[0.4em] text-xs">Grand Luxe Hotel & Resorts</p>
      </div>

      <div class="absolute top-10 left-10 text-white/30 font-black text-[100px] leading-none pointer-events-none select-none">GL</div>
    </div>

    <!-- Form Login Kanan -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-10 md:p-20 relative">
      <div class="lg:hidden absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-0 right-0 w-96 h-96 bg-pink-50 rounded-full blur-[100px] opacity-60"></div>
      </div>

      <div class="max-w-md w-full space-y-12 relative z-10">
        <div class="space-y-4">
          <div class="lg:hidden flex items-center gap-3 mb-8">
            <div class="w-10 h-10 rounded-xl bg-pink-500 flex items-center justify-center shadow-lg">
              <span class="text-white font-black">GL</span>
            </div>
            <h1 class="text-xl font-black tracking-tighter uppercase text-slate-900">Grand Luxe</h1>
          </div>
          <h2 class="text-4xl font-black tracking-tighter text-slate-900 uppercase">Selamat Datang</h2>
          <p class="text-slate-400 font-bold uppercase text-[10px] tracking-[0.4em]">Masuk ke akun keanggotaan Anda</p>
        </div>

        <form @submit.prevent="handleLogin" class="space-y-8">
          <div class="space-y-3">
            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Email Address</label>
            <input 
              v-model="form.email" 
              type="email" 
              placeholder="nama@grandluxe.com"
              class="w-full px-8 py-6 bg-slate-50 border border-slate-100 rounded-[2rem] outline-none font-bold text-slate-700 focus:ring-4 focus:ring-pink-500/5 focus:border-pink-500 transition-all shadow-inner placeholder-slate-300"
              required
            >
          </div>

          <div class="space-y-3">
            <div class="flex items-center justify-between px-4">
              <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Security Code</label>
              <NuxtLink to="/forgot-password" class="text-[9px] font-black text-pink-500 uppercase tracking-widest hover:text-pink-600">Lupa?</NuxtLink>
            </div>
            <div class="relative group">
              <input 
                v-model="form.password" 
                :type="showPassword ? 'text' : 'password'" 
                placeholder="••••••••"
                class="w-full px-8 py-6 bg-slate-50 border border-slate-100 rounded-[2rem] outline-none font-bold text-slate-700 focus:ring-4 focus:ring-pink-500/5 focus:border-pink-500 transition-all shadow-inner placeholder-slate-300"
                required
              >
              <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-8 flex items-center text-slate-300 hover:text-pink-500 transition-colors">
                <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z" /><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" /></svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd" /><path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z" /></svg>
              </button>
            </div>
          </div>

          <button type="submit" :disabled="loading"
            class="w-full py-6 px-10 bg-slate-900 text-white rounded-[2rem] font-black uppercase tracking-[0.3em] text-[11px] shadow-2xl shadow-slate-900/20 hover:bg-pink-600 hover:shadow-pink-500/30 transition-all active:scale-[0.98] disabled:opacity-50 flex items-center justify-center group cursor-pointer"
          >
            <svg v-if="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            <span class="group-hover:translate-x-1 transition-transform">{{ loading ? 'Authenticating...' : 'Masuk Ke Akun →' }}</span>
          </button>

          <div class="text-center pt-8">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
              Belum Menjadi Member?
              <NuxtLink to="/register" class="text-pink-500 hover:text-pink-600 transition-colors ml-2">Daftar Sekarang</NuxtLink>
            </p>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

definePageMeta({ layout: false })

const form = ref({ email: '', password: '' })
const loading = ref(false)
const showPassword = ref(false)

onMounted(() => {
  localStorage.removeItem('auth_token')
  localStorage.removeItem('auth_user')
  localStorage.removeItem('pending_booking')
})

const handleLogin = async () => {
  loading.value = true
  try {
    const response = await $fetch('http://localhost:8000/api/login', {
      method: 'POST',
      body: form.value,
      headers: { 'Accept': 'application/json' },
    })

    localStorage.setItem('auth_token', response.token || response.access_token)
    localStorage.setItem('auth_user', JSON.stringify(response.user))

    alert('Welcome back, ' + response.user.name + '!')

    const role = String(response.user.role).toLowerCase()
    
    // PENGARAHAN BERDASARKAN PERAN (ROLE)
    if (role === 'admin') {
      navigateTo('/admin/dashboard')
    } else if (role === 'receptionist' || role === 'resepsionis') {
      // 1. DIBUTUHKAN UNTUK RESEPSIONIS: Arahkan ke rute khusus resepsionis
      navigateTo('/receptionist/dashboard') 
    } else {
      // 2. DIBUTUHKAN UNTUK TAMU: Arahkan ke rute khusus tamu
      navigateTo('/tamu/dashboard') 
    }
    
  } catch (err) {
    console.error(err)
    alert(err.data?.message || err.response?._data?.message || 'Login Gagal! Pastikan email dan password benar.')
  } finally {
    loading.value = false
  }
}
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@100;300;400;600;700;900&display=swap');
:root { font-family: 'Outfit', sans-serif; }
@keyframes fade-up { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
.animate-fade-up { animation: fade-up 1s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
</style>