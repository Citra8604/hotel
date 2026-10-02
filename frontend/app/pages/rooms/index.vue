<template>
  <!-- Menambahkan md:pl-64 lg:pl-72 agar konten bergeser ke kanan memberi ruang untuk sidebar -->
  <div class="min-h-screen bg-slate-50 text-slate-900 selection:bg-pink-500/30 font-sans overflow-x-hidden md:pl-64 lg:pl-72 transition-all duration-300">
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
      <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] bg-pink-100 rounded-full blur-[120px] opacity-60"></div>
      <div class="absolute bottom-0 right-[10%] w-[30%] h-[30%] bg-rose-50 rounded-full blur-[100px] opacity-40"></div>
    </div>

    <GuestNavbar activePage="rooms" />

    <!-- pt disesuaikan menjadi pt-10 md:pt-14 agar pas di bawah area header -->
    <main class="relative pt-10 md:pt-14 pb-24 px-6 md:px-10 max-w-7xl mx-auto">
      <div class="text-center mb-12">
        <p class="text-[10px] font-black text-pink-500 uppercase tracking-[0.5em] mb-3">The Collection</p>
        <h2 class="text-3xl md:text-5xl font-black text-slate-900 tracking-tighter uppercase mb-4">Temukan Kamar Impian Anda</h2>
        <div class="w-20 h-1.5 bg-gradient-to-r from-pink-500 to-rose-600 mx-auto rounded-full"></div>
      </div>

      <div class="max-w-3xl mx-auto mb-12">
        <div class="bg-white/70 backdrop-blur-xl p-3 md:p-4 rounded-[2.5rem] border border-white shadow-xl shadow-pink-500/5 flex flex-col md:flex-row gap-4">
          <div class="flex-1 relative">
            <span class="absolute left-6 top-1/2 -translate-y-1/2 text-lg">🔍</span>
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Cari tipe kamar..."
              class="w-full pl-14 pr-6 py-3.5 bg-white/60 border border-slate-100 rounded-[1.8rem] focus:ring-2 focus:ring-pink-500 focus:bg-white outline-none text-sm font-bold transition-all"
            />
          </div>
        </div>
      </div>

      <div v-if="loading" class="text-center py-24">
        <div class="w-14 h-14 border-4 border-pink-500 border-t-transparent rounded-full animate-spin mx-auto mb-6"></div>
        <p class="text-slate-400 font-black uppercase tracking-[0.5em] text-[10px]">Memproses Koleksi Kamar...</p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" v-else-if="filteredRoomTypes.length > 0">
        <div
          v-for="room in filteredRoomTypes"
          :key="room.id"
          class="bg-white rounded-[3rem] border border-pink-50 overflow-hidden group hover:shadow-2xl shadow-pink-500/10 transition-all duration-500 flex flex-col"
        >
          <div class="relative h-64 overflow-hidden bg-slate-100 flex items-center justify-center">
            <img 
              v-if="room.foto || room.image" 
              :src="getImageUrl(room.foto || room.image)" 
              class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000 relative z-10" 
              alt="Room Preview"
              @error="(e) => { e.target.style.display = 'none' }"
            />
            
            <div class="absolute inset-0 flex flex-col items-center justify-center text-slate-300 bg-slate-100 z-0">
              <span class="text-4xl">🖼️</span>
              <span class="text-[10px] font-black uppercase tracking-widest mt-2 text-slate-400">Grand Luxe Room</span>
            </div>

            <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-md px-4 py-2 rounded-2xl border border-pink-100 shadow-sm z-20">
              <p class="text-[10px] font-black uppercase tracking-widest" 
                 :class="(room.available_rooms_count ?? room.available_count ?? 0) > 0 ? 'text-pink-600' : 'text-rose-600'">
                SISA {{ room.available_rooms_count ?? room.available_count ?? 0 }} KAMAR
              </p>
            </div>
          </div>

          <div class="p-8 flex-1 flex flex-col">
            <h3 class="text-2xl font-black tracking-tighter text-slate-900 uppercase">{{ room.nama_tipe || room.name }}</h3>
            <p class="text-pink-600 font-black text-sm uppercase tracking-widest mb-4">
              Rp {{ Number(room.harga || room.price || 0).toLocaleString('id-ID') }}
              <span class="text-[10px] text-slate-400 font-bold">/ Malam</span>
            </p>
            <p class="text-slate-500 text-xs mb-6 line-clamp-3 leading-relaxed font-medium italic">"{{ room.deskripsi || room.description }}"</p>
            <div class="space-y-4 mb-8">
              <div class="flex flex-wrap gap-2">
                <span
                  v-for="f in ((room.fasilitas || room.facilities)?.split(',') || [])"
                  :key="f"
                  class="px-3 py-1.5 bg-slate-50 rounded-xl text-[9px] font-black text-slate-500 border border-slate-100 uppercase tracking-tight"
                >
                  {{ f.trim() }}
                </span>
              </div>
            </div>
            <div class="mt-auto">
              <button
                @click="openDetail(room)"
                :disabled="(room.available_rooms_count ?? room.available_count ?? 0) <= 0"
                class="w-full py-4 text-white font-black rounded-2xl transition-all uppercase tracking-[0.2em] text-[10px] shadow-md shadow-slate-900/10 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                :class="(room.available_rooms_count ?? room.available_count ?? 0) > 0 ? 'bg-slate-900 hover:bg-pink-500' : 'bg-slate-400'"
              >
                {{ (room.available_rooms_count ?? room.available_count ?? 0) > 0 ? 'Lihat Detail Kamar' : 'Kamar Penuh' }}
              </button>
            </div>
          </div>
        </div>
      </div>

      <div class="text-center py-20 bg-white border border-dashed rounded-[3rem]" v-else>
        <p class="text-sm text-slate-400 font-bold uppercase tracking-widest">Katalog kamar kosong / tidak ditemukan.</p>
      </div>
    </main>

    <!-- Modal Detail Kamar -->
    <Transition name="fade">
      <div v-if="showModal && selectedRoom" class="fixed inset-0 z-[200] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-md" @click="closeModal"></div>
        <div class="bg-white w-full max-w-4xl rounded-[3rem] shadow-2xl overflow-hidden flex flex-col lg:flex-row relative z-10 max-h-[90vh]">
          
          <div class="w-full lg:w-1/2 h-64 lg:h-auto relative bg-slate-100 flex items-center justify-center">
            <img 
              v-if="selectedRoom.foto || selectedRoom.image" 
              :src="getImageUrl(selectedRoom.foto || selectedRoom.image)" 
              class="w-full h-full object-cover relative z-10" 
              alt="Detailed View"
              @error="(e) => { e.target.style.display = 'none' }"
            />
            <div class="absolute inset-0 flex flex-col items-center justify-center text-slate-300 bg-slate-100 z-0">
              <span class="text-6xl">🏨</span>
              <span class="text-xs font-black uppercase tracking-widest mt-2 text-slate-400">Grand Luxe Suite</span>
            </div>
          </div>

          <div class="w-full lg:w-1/2 p-8 md:p-10 overflow-y-auto flex flex-col">
            <div class="flex justify-between items-start mb-6">
              <div>
                <p class="text-[10px] font-black text-pink-500 uppercase tracking-[0.4em] mb-1">Room Specification</p>
                <h3 class="text-3xl font-black text-slate-900 tracking-tighter uppercase">{{ selectedRoom.nama_tipe || selectedRoom.name }}</h3>
              </div>
              <button @click="closeModal" class="w-10 h-10 bg-slate-50 rounded-full flex items-center justify-center text-slate-400 hover:text-pink-500 text-lg cursor-pointer">✕</button>
            </div>

            <div class="mb-4">
              <p class="text-2xl font-black text-pink-600 tracking-tight">
                Rp {{ Number(selectedRoom.harga || selectedRoom.price || 0).toLocaleString('id-ID') }}
                <span class="text-xs text-slate-400 font-bold uppercase tracking-widest">/ Night</span>
              </p>
            </div>

            <div class="mb-6 flex items-center gap-2">
              <span class="w-2.5 h-2.5 rounded-full" :class="(selectedRoom.available_rooms_count ?? selectedRoom.available_count ?? 0) > 0 ? 'bg-emerald-500' : 'bg-rose-500'"></span>
              <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">
                Ketersediaan Unit: {{ selectedRoom.available_rooms_count ?? selectedRoom.available_count ?? 0 }} Kamar Tersisa
              </p>
            </div>

            <div class="bg-pink-50/50 p-6 rounded-[2rem] border border-pink-100 mb-6">
              <p class="text-slate-600 text-sm leading-relaxed font-medium italic">"{{ selectedRoom.deskripsi || selectedRoom.description }}"</p>
            </div>

            <div v-if="selectedRoom.fasilitas || selectedRoom.facilities" class="mb-8">
              <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Fasilitas</p>
              <div class="flex flex-wrap gap-2">
                <span
                  v-for="f in (selectedRoom.fasilitas || selectedRoom.facilities).split(',')"
                  :key="f"
                  class="px-3 py-1.5 bg-slate-50 rounded-xl text-[9px] font-black text-slate-500 border border-slate-100 uppercase tracking-tight"
                >
                  {{ f.trim() }}
                </span>
              </div>
            </div>

            <div class="mt-auto pt-4 border-t">
              <button
                @click="startBookingFromModal"
                :disabled="(selectedRoom.available_rooms_count ?? selectedRoom.available_count ?? 0) <= 0"
                class="w-full py-5 text-white font-black rounded-2xl uppercase tracking-[0.2em] text-[10px] shadow-xl disabled:opacity-50 disabled:cursor-not-allowed transition-all cursor-pointer"
                :class="(selectedRoom.available_rooms_count ?? selectedRoom.available_count ?? 0) > 0 ? 'bg-gradient-to-r from-pink-500 to-rose-600 hover:opacity-90' : 'bg-slate-400'"
              >
                {{ (selectedRoom.available_rooms_count ?? selectedRoom.available_count ?? 0) > 0 ? 'Pesan Kamar Sekarang' : 'Kamar Penuh' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

definePageMeta({ layout: false })

const roomTypes = ref([])
const loading = ref(true)
const searchQuery = ref('')
const selectedRoom = ref(null)
const showModal = ref(false)

const getImageUrl = (path) => {
  if (!path) return ''
  if (path.startsWith('http')) return path
  const cleanPath = path.replace('storage/', '')
  return `http://localhost:8000/storage/${cleanPath}`
}

const fetchRoomTypes = async () => {
  loading.value = true
  try {
    const response = await $fetch('http://localhost:8000/api/room-types')
    roomTypes.value = response.data || response || []
  } catch (err) {
    console.error('Gagal fetch room types:', err)
  } finally {
    loading.value = false
  }
}

const filteredRoomTypes = computed(() => {
  if (!searchQuery.value) return roomTypes.value
  const query = searchQuery.value.toLowerCase()
  return roomTypes.value.filter(room => {
    const nama = room.nama_tipe || room.name || ''
    return nama.toLowerCase().includes(query)
  })
})

const openDetail = (room) => {
  selectedRoom.value = room
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  selectedRoom.value = null
}

const startBookingFromModal = () => {
  if (!selectedRoom.value) return
  const currentStock = selectedRoom.value.available_rooms_count ?? selectedRoom.value.available_count ?? 0
  if (currentStock <= 0) {
    alert('Maaf, kamar ini sudah penuh.')
    return
  }
  const bookingData = {
    id_tipe_kamar: selectedRoom.value.id,
    nama_tipe: selectedRoom.value.nama_tipe || selectedRoom.value.name,
    harga: selectedRoom.value.harga || selectedRoom.value.price,
    jumlah_kamar: 1,
  }
  localStorage.setItem('pending_booking', JSON.stringify(bookingData))
  navigateTo('/booking')
}

onMounted(() => {
  fetchRoomTypes()
})
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.5s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>