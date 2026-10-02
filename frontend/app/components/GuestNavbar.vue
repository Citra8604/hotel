<template>
  <div>
    <!-- Sidebar Vertikal Kiri (Fixed + Scrollable jika layar pendek) -->
    <aside class="fixed top-0 left-0 h-screen w-64 md:w-72 bg-white/95 backdrop-blur-md border-r border-slate-100 flex flex-col justify-between p-6 z-50 print:hidden shadow-sm overflow-y-auto">
      <div class="space-y-8">
        <!-- Logo Brand -->
        <NuxtLink to="/dashboard" class="flex items-center space-x-3 px-2 group">
          <div class="w-10 h-10 bg-slate-900 group-hover:bg-pink-500 transition-colors rounded-xl flex items-center justify-center shadow-md shrink-0">
            <span class="text-xl">🏨</span>
          </div>
          <div>
            <span class="font-black tracking-tight text-slate-900 text-sm block">GRAND LUXE</span>
            <span class="text-[8px] font-black tracking-widest text-pink-500 uppercase block -mt-1">Resorts</span>
          </div>
        </NuxtLink>

        <!-- Menu Navigasi Vertikal -->
        <nav class="flex flex-col space-y-2">
          <NuxtLink 
            to="/dashboard" 
            :class="currentPage === 'dashboard' ? 'bg-slate-900 text-white shadow-md shadow-slate-900/10 font-black' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50 font-bold'"
            class="px-4 py-3 rounded-xl text-xs uppercase tracking-wider transition-all flex items-center gap-3"
          >
            <span>📊</span> Dashboard
          </NuxtLink>

          <NuxtLink 
            to="/rooms" 
            :class="currentPage === 'rooms' ? 'bg-slate-900 text-white shadow-md shadow-slate-900/10 font-black' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50 font-bold'"
            class="px-4 py-3 rounded-xl text-xs uppercase tracking-wider transition-all flex items-center gap-3"
          >
            <span>🛏️</span> Pesan Kamar
          </NuxtLink>

          <NuxtLink 
            to="/rooms/orders" 
            :class="currentPage === 'orders' ? 'bg-slate-900 text-white shadow-md shadow-slate-900/10 font-black' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50 font-bold'"
            class="px-4 py-3 rounded-xl text-xs uppercase tracking-wider transition-all flex items-center gap-3"
          >
            <span>📜</span> Riwayat Pesanan
          </NuxtLink>

          <NuxtLink 
            to="/profile" 
            :class="currentPage === 'profile' ? 'bg-slate-900 text-white shadow-md shadow-slate-900/10 font-black' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50 font-bold'"
            class="px-4 py-3 rounded-xl text-xs uppercase tracking-wider transition-all flex items-center gap-3"
          >
            <span>👤</span> Profil Saya
          </NuxtLink>
          
          <button 
            @click="modalLacakOpen = true" 
            class="w-full text-left px-4 py-3 rounded-xl text-xs font-black uppercase tracking-wider text-pink-600 bg-pink-50 border border-pink-100 hover:bg-pink-500 hover:text-white transition-all cursor-pointer flex items-center gap-3 mt-4"
          >
            <span>🔍</span> Lacak Status
          </button>
        </nav>
      </div>

      <!-- Tombol Keluar di Bagian Bawah Sidebar -->
      <div class="pt-6 border-t border-slate-100 shrink-0">
        <button 
          @click="handleLogout" 
          class="w-full px-5 py-3 border border-slate-200 hover:border-rose-200 text-slate-500 hover:text-rose-600 hover:bg-rose-50/50 rounded-xl font-black text-[10px] uppercase tracking-widest transition-all cursor-pointer bg-white text-center flex items-center justify-center gap-2"
        >
          <span>🚪</span> Keluar
        </button>
      </div>
    </aside>

    <!-- Modal Lacak Reservasi -->
    <Transition name="fade">
      <div v-if="modalLacakOpen" class="fixed inset-0 z-[200] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div class="bg-white p-8 md:p-10 rounded-[2.5rem] max-w-md w-full border border-pink-50 shadow-2xl animate-fade-in">
          <h3 class="text-2xl font-black text-slate-900 uppercase mb-1">Lacak Reservasi</h3>
          <p class="text-xs text-pink-500 font-bold uppercase tracking-wider mb-6">Cek berkas invoice publik tanpa login</p>
          
          <div class="space-y-4 mb-6">
            <div>
              <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Nomor Registrasi Pemesanan</label>
              <input v-model="lacakForm.nomor" type="text" placeholder="CONTOH: RSV-XXXXXX" class="w-full p-4 bg-slate-50 border border-slate-200 rounded-xl font-bold text-sm focus:outline-none focus:border-pink-500 uppercase tracking-wider shadow-inner">
            </div>
            <div>
              <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Alamat Email Pemesan</label>
              <input v-model="lacakForm.email" type="email" placeholder="nama@email.com" class="w-full p-4 bg-slate-50 border border-slate-200 rounded-xl font-bold text-sm focus:outline-none focus:border-pink-500 shadow-inner">
            </div>
          </div>

          <div class="flex gap-3">
            <button @click="eksekusiLacak" class="flex-1 py-4 bg-slate-900 hover:bg-pink-500 text-white font-black rounded-2xl text-[10px] uppercase tracking-widest transition-colors cursor-pointer shadow-md">Cari Dokumen</button>
            <button @click="modalLacakOpen = false" class="px-6 py-4 bg-slate-100 text-slate-500 font-black rounded-2xl text-[10px] uppercase tracking-widest cursor-pointer">Batal</button>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRoute } from 'vue-router'

const props = defineProps({
  activePage: {
    type: String,
    default: ''
  }
})

const route = useRoute()

// Deteksi otomatis halaman aktif dari URL jika prop activePage tidak diisi
const currentPage = computed(() => {
  if (props.activePage) return props.activePage
  if (route.path.includes('/rooms/orders')) return 'orders'
  if (route.path.includes('/rooms')) return 'rooms'
  if (route.path.includes('/dashboard')) return 'dashboard'
  if (route.path.includes('/profile')) return 'profile'
  return ''
})

const modalLacakOpen = ref(false)
const lacakForm = ref({ nomor: '', email: '' })

const eksekusiLacak = () => {
  if (!lacakForm.value.nomor.trim() || !lacakForm.value.email.trim()) {
    alert('Mohon lengkapi seluruh field untuk melacak invoice!')
    return
  }
  modalLacakOpen.value = false
  
  window.open(`/print/receipt-check?nomor=${lacakForm.value.nomor.trim().toUpperCase()}&email=${lacakForm.value.email.trim()}`, '_blank')
}

const handleLogout = () => {
  if (confirm('Apakah Anda yakin ingin mengakhiri sesi kunjungan Grand Luxe?')) {
    localStorage.removeItem('auth_token')
    localStorage.removeItem('auth_user')
    window.location.href = '/login'
  }
}
</script>

<style scoped>
@keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
.animate-fade-in { animation: fadeIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>