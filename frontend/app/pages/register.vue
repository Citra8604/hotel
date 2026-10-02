<template>
  <div class="min-h-screen flex bg-white font-sans overflow-hidden">
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden group">
      <img 
        src="/_antigravity/luxury_hotel_lobby_login.png" 
        alt="Luxury Hotel Lobby" 
        class="absolute inset-0 w-full h-full object-cover transition-transform duration-[3s] group-hover:scale-110 grayscale-[0.2]"
      >
      <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/40 to-transparent"></div>
      
      <div class="absolute bottom-20 left-20 right-20 space-y-6 animate-fade-up">
        <div class="w-20 h-1 bg-pink-500 rounded-full"></div>
        <h2 class="text-6xl font-black text-white tracking-tighter uppercase leading-none">
          Mulai <br/>
          <span class="text-transparent bg-clip-text bg-gradient-to-r from-pink-400 to-rose-400">Petualangan Anda</span>
        </h2>
        <p class="text-slate-300 font-bold uppercase tracking-[0.4em] text-xs">Reservasi Eksklusif Menanti Anda</p>
      </div>

      <div class="absolute top-10 left-10 text-white/30 font-black text-[100px] leading-none pointer-events-none select-none">GL</div>
    </div>

    <div class="w-full lg:w-1/2 flex items-center justify-center p-10 md:p-20 relative overflow-y-auto">
      <div class="max-w-md w-full space-y-12 py-12 relative z-10">
        <div class="space-y-4">
          <div class="lg:hidden flex items-center gap-3 mb-8">
            <div class="w-10 h-10 rounded-xl bg-pink-500 flex items-center justify-center shadow-lg">
              <span class="text-white font-black">GL</span>
            </div>
            <h1 class="text-xl font-black tracking-tighter uppercase text-slate-900">Grand Luxe</h1>
          </div>
          <h2 class="text-4xl font-black tracking-tighter text-slate-900 uppercase">Daftar Keanggotaan</h2>
          <p class="text-slate-400 font-bold uppercase text-[10px] tracking-[0.4em]">Nikmati layanan prioritas di seluruh resor kami</p>
        </div>

        <form @submit.prevent="handleRegister" class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-3 md:col-span-2">
              <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Account Type</label>
              <select v-model="form.role" class="w-full px-8 py-5 bg-slate-50 border border-slate-100 rounded-[2rem] outline-none font-bold text-slate-700 focus:ring-4 focus:ring-pink-500/5 focus:border-pink-500 transition-all appearance-none cursor-pointer" required>
                <option value="guest">Tamu (Guest)</option>
                <option value="receptionist">Resepsionis (Staff)</option>
                <option value="admin">Administrator (Sultan)</option>
              </select>
            </div>

            <div class="space-y-3">
              <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Full Name</label>
              <input v-model="form.name" type="text" placeholder="Andi Wijaya" class="w-full px-8 py-5 bg-slate-50 border border-slate-100 rounded-[2rem] outline-none font-bold text-slate-700 focus:ring-4 focus:ring-pink-500/5 focus:border-pink-500 transition-all shadow-inner" required>
            </div>

            <div class="space-y-3">
              <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">WhatsApp</label>
              <input v-model="form.phone" type="tel" placeholder="0812..." class="w-full px-8 py-5 bg-slate-50 border border-slate-100 rounded-[2rem] outline-none font-bold text-slate-700 focus:ring-4 focus:ring-pink-500/5 focus:border-pink-500 transition-all shadow-inner" required>
            </div>

            <div class="space-y-3 md:col-span-2">
              <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Email Address</label>
              <input v-model="form.email" type="email" placeholder="andi@email.com" class="w-full px-8 py-5 bg-slate-50 border border-slate-100 rounded-[2rem] outline-none font-bold text-slate-700 focus:ring-4 focus:ring-pink-500/5 focus:border-pink-500 transition-all shadow-inner" required>
            </div>

            <div class="space-y-3">
              <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Security Code</label>
              <input v-model="form.password" type="password" placeholder="••••••••" class="w-full px-8 py-5 bg-slate-50 border border-slate-100 rounded-[2rem] outline-none font-bold text-slate-700 focus:ring-4 focus:ring-pink-500/5 focus:border-pink-500 transition-all shadow-inner" required>
            </div>

            <div class="space-y-3">
              <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Verify Code</label>
              <input v-model="form.password_confirmation" type="password" placeholder="••••••••" class="w-full px-8 py-5 bg-slate-50 border border-slate-100 rounded-[2rem] outline-none font-bold text-slate-700 focus:ring-4 focus:ring-pink-500/5 focus:border-pink-500 transition-all shadow-inner" required>
            </div>
          </div>

          <button type="submit" :disabled="loading"
            class="w-full py-6 px-10 bg-slate-900 text-white rounded-[2rem] font-black uppercase tracking-[0.3em] text-[11px] shadow-2xl shadow-slate-900/20 hover:bg-pink-600 hover:shadow-pink-500/30 transition-all active:scale-[0.98] disabled:opacity-50 flex items-center justify-center group mt-4"
          >
            <svg v-if="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            <span class="group-hover:translate-x-1 transition-transform">{{ loading ? 'Registering...' : 'Daftar Sekarang →' }}</span>
          </button>

          <div class="text-center pt-8">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
              Sudah Memiliki Akun?
              <NuxtLink to="/login" class="text-pink-500 hover:text-pink-600 transition-colors ml-2">Masuk Saja</NuxtLink>
            </p>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const form = ref({
  name: '',
  email: '',
  phone: '',
  password: '',
  password_confirmation: '',
  role: 'guest',
})

const loading = ref(false)

const handleRegister = async () => {
  if (form.value.password !== form.value.password_confirmation) {
    alert('Konfirmasi kata sandi tidak cocok!')
    return
  }

  loading.value = true
  try {
    await $fetch('http://localhost:8000/api/register', {
      method: 'POST',
      body: form.value,
      headers: { 'Accept': 'application/json' }
    })
    
    alert(`Pendaftaran Berhasil! Selamat Datang ${form.value.name}. Silakan login.`)
    navigateTo('/login')
  } catch (err) {
    // 🔥 RADAR ERROR AKTIF: Kita bongkar isi kepala Laravel!
    console.error("ERROR DARI LARAVEL:", err.response?._data)
    
    const errors = err.response?._data?.errors
    if (errors) {
      // Kalo ada error validasi (misal password kurang panjang, email salah format)
      // Ini bakal nampilin pesan aslinya ke layar lu!
      const pesanError = Object.values(errors).map(e => e[0]).join('\n')
      alert("⚠️ GAGAL DAFTAR KARENA:\n\n" + pesanError)
    } else {
      alert(err.response?._data?.message || 'Pendaftaran Gagal! Cek console F12.')
    }
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

::-webkit-scrollbar { width: 6px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: #fbcfe8; border-radius: 10px; }
::-webkit-scrollbar-thumb:hover { background: #f472b6; }
</style>