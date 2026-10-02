<template>
  <div class="min-h-screen flex bg-slate-50 text-slate-900 font-sans overflow-x-hidden lg:pl-72">
    <ReceptionistSidebar :user="user" />

    <main class="flex-1 p-6 md:p-10 relative z-10 overflow-y-auto">
      <header class="mb-10 print:hidden mt-8">
        <h1 class="text-4xl font-black tracking-tight mb-2 text-slate-900">Laporan Operasional</h1>
        <p class="text-slate-500 font-medium">Pantau performa reservasi kedatangan dan keberangkatan harian.</p>
      </header>

      <section class="bg-white p-6 rounded-2xl border mb-10 print:hidden border-pink-50 shadow-xl shadow-pink-500/5">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
          <div class="col-span-2">
            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Pilih Hari Audit</label>
            <input v-model="tglLaporan" type="date" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl outline-none font-bold focus:border-pink-500 transition-all">
          </div>
          <button @click="printReport" class="py-4 bg-slate-900 text-white font-black rounded-xl uppercase text-[10px] tracking-widest hover:bg-pink-600 transition-colors shadow-md cursor-pointer">
            🖨️ Cetak Laporan
          </button>
        </div>
      </section>

      <div class="hidden print:block text-center mb-12 border-b-4 border-slate-900 pb-6">
        <h1 class="text-4xl font-black uppercase tracking-tighter text-slate-900">HOTEL 71 JAKARTA</h1>
        <p class="text-xs uppercase font-black text-pink-500 tracking-[0.3em] mt-1">Daily Operational Front Office Report</p>
        <p class="text-xs font-bold text-slate-400 mt-4">Tanggal Audit: {{ formatDateLong(tglLaporan) }}</p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="bg-white border border-pink-50 p-8 rounded-3xl shadow-xl shadow-pink-500/5">
          <h3 class="text-lg font-black border-b border-pink-100 pb-3 mb-4 text-emerald-600 uppercase tracking-tight flex items-center gap-2">
            <span>➡️</span> Kedatangan (Check-In Hari Ini)
          </h3>
          <ul class="space-y-4">
            <li v-for="p in laporanCheckIn" :key="p.id" class="flex justify-between items-center border-b border-slate-50 pb-3 text-sm">
              <div>
                <p class="font-black text-slate-900">{{ p.nama_pemesan || p.nama_tamu }}</p>
                <p class="text-[10px] font-mono font-bold text-slate-400 uppercase tracking-wider">{{ p.nomor_pemesanan || p.reservation_number }}</p>
                <p class="text-[10px] font-bold text-pink-500 mt-0.5">{{ p.nama_tipe || 'Kamar Pilihan' }} ({{ p.quantity || p.jumlah_kamar }} Unit)</p>
              </div>
              <span class="px-2.5 py-1 text-[9px] font-black uppercase bg-emerald-50 text-emerald-600 border border-emerald-200 rounded-lg">
                {{ p.status_pemesanan || p.status }}
              </span>
            </li>
            <li v-if="laporanCheckIn.length === 0" class="text-xs text-slate-400 font-bold italic py-4 text-center">Tidak ada aktivitas kedatangan harian.</li>
          </ul>
        </div>

        <div class="bg-white border border-pink-50 p-8 rounded-3xl shadow-xl shadow-pink-500/5">
          <h3 class="text-lg font-black border-b border-pink-100 pb-3 mb-4 text-slate-700 uppercase tracking-tight flex items-center gap-2">
            <span>⬅️</span> Keberangkatan (Check-Out Hari Ini)
          </h3>
          <ul class="space-y-4">
            <li v-for="p in laporanCheckOut" :key="p.id" class="flex justify-between items-center border-b border-slate-50 pb-3 text-sm">
              <div>
                <p class="font-black text-slate-900">{{ p.nama_pemesan || p.nama_tamu }}</p>
                <p class="text-[10px] font-mono font-bold text-slate-400 uppercase tracking-wider">{{ p.nomor_pemesanan || p.reservation_number }}</p>
                <p class="text-[10px] font-bold text-slate-500 mt-0.5">{{ p.nama_tipe || 'Kamar Pilihan' }} ({{ p.quantity || p.jumlah_kamar }} Unit)</p>
              </div>
              <span class="px-2.5 py-1 text-[9px] font-black uppercase bg-slate-50 text-slate-500 border border-slate-200 rounded-lg">
                {{ p.status_pemesanan || p.status }}
              </span>
            </li>
            <li v-if="laporanCheckOut.length === 0" class="text-xs text-slate-400 font-bold italic py-4 text-center">Tidak ada aktivitas keberangkatan harian.</li>
          </ul>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import ReceptionistSidebar from '~/components/receptionist/ReceptionistSidebar.vue'

definePageMeta({ layout: false })

const user = ref({ name: 'Resepsionis', role: 'resepsionis' })
const pesananList = ref([])
const tglLaporan = ref(new Date().toISOString().split('T')[0])

const formatDate = (date) => date ? new Date(date).toLocaleDateString('id-ID', { day: '2-digit', month: 'short' }) : '-'
const formatDateLong = (dateStr) => dateStr ? new Date(dateStr).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '-'

const fetchPesanan = async (localToken) => {
  try {
    const response = await $fetch('http://localhost:8000/api/pemesanan', { 
      headers: { 'Authorization': `Bearer ${localToken}` } 
    })
    pesananList.value = response.data || response || []
  } catch (err) { 
    console.error('Gagal mengambil laporan harian:', err) 
  }
}

// ✅ FIX MUTLAK LOGIC FILTER: Membaca fallback properti bahasa inggris database aseli lu agar data tidak blank harian
const laporanCheckIn = computed(() => {
  if (!pesananList.value) return []
  return pesananList.value.filter(p => {
    const checkInDate = p.tgl_check_in || p.check_in
    const currentStatus = p.status_pemesanan || p.status
    return checkInDate && checkInDate.startsWith(tglLaporan.value) && currentStatus !== 'checked_out' && currentStatus !== 'Check-out'
  })
})

const laporanCheckOut = computed(() => {
  if (!pesananList.value) return []
  return pesananList.value.filter(p => {
    const checkOutDate = p.tgl_check_out || p.check_out
    return checkOutDate && checkOutDate.startsWith(tglLaporan.value)
  })
})

const printReport = () => window.print()

onMounted(() => {
  const localToken = localStorage.getItem('auth_token')
  if (!localToken) {
    window.location.href = '/login'
  } else {
    const savedUser = localStorage.getItem('auth_user')
    if (savedUser) user.value = JSON.parse(savedUser)
    fetchPesanan(localToken)
  }
})
</script>

<style scoped>
@media print {
  main { padding: 0 !important; width: 100% !important; background: white !important; }
  .bg-white { border: none !important; box-shadow: none !important; padding: 0 !important; }
}
</style>