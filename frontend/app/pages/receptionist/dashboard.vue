<template>
  <div class="min-h-screen flex bg-[#F6F5FA] text-slate-900 font-sans overflow-x-hidden lg:pl-72">
    <ReceptionistSidebar :user="user" />

    <main class="flex-1 p-6 md:p-10 relative z-10 overflow-y-auto">
      <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-12 mt-4">
        <div>
          <h1 class="text-3xl font-bold tracking-tight text-slate-800">Reservation Management</h1>
          <p class="text-slate-400 text-sm">Data Pemesan Hotel 71 / Grand Luxe</p>
        </div>
        <button @click="fetchReservations(); fetchRooms()" class="bg-white border text-xs font-bold px-4 py-2.5 rounded-xl shadow-sm cursor-pointer hover:bg-slate-50">
          REFRESH DATA
        </button>
      </header>

      <section class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm mb-10">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
          <div class="col-span-2">
            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">⚡ AREA SCAN QR TRANSMIT HARDWARE (AUTO CHECK-IN)</label>
            <input v-model="searchQuery" @keyup.enter="handleScanner" type="text" placeholder="Tembak kode QR di invoice pakai scanner USB hardware di sini..." class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-slate-800 font-black text-sm placeholder-slate-300 uppercase tracking-wider">
          </div>
          <div>
            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Filter Hari Inap</label>
            <input v-model="filterDate" type="date" class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl outline-none font-bold text-sm">
          </div>
        </div>
      </section>

      <section class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden mb-10">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm border-collapse">
            <thead>
              <tr class="bg-slate-50/80 border-b border-slate-100 text-[10px] font-black uppercase tracking-widest text-slate-400">
                <th class="p-5">Nomor / Metode</th>
                <th class="p-5">Nama Tamu</th>
                <th class="p-5">Check-In</th>
                <th class="p-5">Check-Out</th>
                <th class="p-5">Status</th>
                <th class="p-5 text-center">Aksi Manual</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="res in filteredReservations" :key="res.id" class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                <td class="p-5">
                  <p class="font-bold text-slate-900 font-mono text-sm tracking-tight">{{ res.reservation_number || res.nomor_pemesanan }}</p>
                  <p class="text-[9px] font-black uppercase tracking-wider text-slate-400 mt-0.5">{{ res.payment_method === 'bank_transfer' ? 'Transfer Bank' : 'Cash' }}</p>
                </td>
                <td class="p-5 font-bold text-slate-800">{{ res.nama_tamu || res.nama_pemesan }}</td>
                <td class="p-5 text-xs text-slate-500 font-semibold">{{ formatDate(res.check_in || res.tgl_check_in) }}</td>
                <td class="p-5 text-xs text-slate-500 font-semibold">{{ formatDate(res.check_out || res.tgl_check_out) }}</td>
                <td class="p-5">
                  <span class="px-2.5 py-1 text-[9px] font-black uppercase rounded-lg border"
                        :class="{
                          'bg-amber-50 text-amber-600 border-amber-200': res.status === 'pending',
                          'bg-blue-50 text-blue-600 border-blue-200': res.status === 'checked_in',
                          'bg-slate-50 text-slate-500 border-slate-200': res.status === 'checked_out',
                          'bg-emerald-50 text-emerald-600 border-emerald-200': res.status === 'verified',
                          'bg-rose-50 text-rose-600 border-rose-200': res.status === 'cancelled'
                        }">
                    {{ 
                      res.status === 'pending' ? 'Baru' :
                      res.status === 'verified' ? 'Verified' :
                      res.status === 'checked_in' ? 'Check-in' :
                      res.status === 'checked_out' ? 'Check-out' : 'Dibatalkan'
                    }}
                  </span>
                </td>
                <td class="p-5 text-center">
                  <button v-if="res.status === 'pending' || res.status === 'verified'" @click="bukaModalCheckIn(res)" class="px-5 py-2.5 bg-[#D4AF37] hover:bg-slate-900 text-white text-[10px] font-black rounded-xl uppercase tracking-widest cursor-pointer transition-colors w-32">
                    Review Manual
                  </button>
                  <button v-else-if="res.status === 'checked_in'" @click="bukaModalCheckout(res)" class="px-5 py-2.5 bg-slate-800 hover:bg-black text-white text-[10px] font-black rounded-xl uppercase tracking-widest cursor-pointer transition-colors w-32">
                    Check-Out
                  </button>
                  <span v-else class="text-xs text-slate-400 font-bold italic">Selesai</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>

      <section class="mt-12 bg-white p-10 rounded-[2.5rem] border border-slate-100 shadow-sm">
        <h3 class="text-2xl font-black tracking-tight text-slate-900 uppercase mb-8">🛏 Status Kamar Fisik</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-4">
          <div v-for="room in rooms" :key="room.id" @click="quickUpdateRoom(room)" class="p-4 rounded-2xl border text-center cursor-pointer relative group transition-all"
               :class="{
                 'bg-emerald-50 border-emerald-100': room.status === 'Tersedia' || room.status === 'available',
                 'bg-rose-50 border-rose-100': room.status === 'Terisi' || room.status === 'occupied',
                 'bg-amber-50 border-amber-100': room.status === 'Kotor' || room.status === 'dirty',
                 'bg-blue-50 border-blue-100': room.status === 'Perbaikan' || room.status === 'maintenance'
               }">
            <p class="text-[9px] font-black uppercase tracking-widest" :class="(room.status === 'Tersedia' || room.status === 'available') ? 'text-emerald-600' : 'text-amber-600'">
              {{ room.status }}
            </p>
            <h4 class="text-2xl font-black text-slate-900 mt-1">{{ room.room_number || room.nomor_kamar }}</h4>
            <p class="text-[8px] font-bold text-slate-400 truncate mt-1">{{ room.room_type?.name || 'Standard' }}</p>
            <div v-if="room.status === 'Kotor' || room.status === 'dirty'" class="absolute inset-0 bg-amber-500/90 text-white flex items-center justify-center text-[10px] font-black uppercase opacity-0 group-hover:opacity-100 transition-all rounded-2xl">🧹 Bersihkan</div>
          </div>
        </div>
      </section>
    </main>

    <div v-if="showCheckinModal" class="fixed inset-0 z-[200] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-fade-in">
      <div class="bg-[#FAF9F5] p-8 md:p-10 rounded-[2rem] max-w-xl w-full shadow-2xl border border-amber-100 max-h-[90vh] overflow-y-auto">
        <h3 class="text-xl font-bold uppercase mb-6 pb-3 border-b text-slate-800">Kasir & Alokasi Kamar</h3>
        
        <div class="grid grid-cols-2 gap-4 bg-white p-4 rounded-xl border border-slate-100 mb-6">
          <div>
            <span class="text-[8px] font-black text-slate-400 block uppercase">Nomor Pesanan</span>
            <span class="font-mono font-bold text-sm text-slate-900">{{ selectedPesanan?.reservation_number || selectedPesanan?.nomor_pemesanan }}</span>
          </div>
          <div>
            <span class="text-[8px] font-black text-slate-400 block uppercase">Nama Tamu</span>
            <span class="font-bold text-sm text-slate-900">{{ selectedPesanan?.nama_tamu || selectedPesanan?.nama_pemesan }}</span>
          </div>
          <div class="col-span-2 pt-2 border-t border-dashed flex justify-between items-center">
            <span class="text-[10px] font-black text-slate-400 uppercase">Total Tagihan</span>
            <span class="text-lg font-black text-slate-800">Rp {{ Number(selectedPesanan?.total_price || selectedPesanan?.total_harga || 0).toLocaleString() }}</span>
          </div>
        </div>

        <div class="space-y-4 mb-6 bg-white p-4 rounded-xl border border-slate-100">
          <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Alokasi Kamar Fisik (Wajib Pilih {{ selectedPesanan?.quantity || selectedPesanan?.jumlah_kamar }} Kamar)</p>
          <div v-for="index in (selectedPesanan?.quantity || selectedPesanan?.jumlah_kamar)" :key="index" class="space-y-1.5">
            <select v-model="selectedKamarIds[index - 1]" class="w-full p-3.5 bg-slate-50 border rounded-xl font-bold text-xs outline-none focus:border-amber-500">
              <option value="" disabled>-- Pilih Nomor Ruangan --</option>
              <option v-for="room in ketersediaanKamarFisik" :key="room.id" :value="room.id" :disabled="selectedKamarIds.includes(room.id) && selectedKamarIds[index - 1] !== room.id">
                Nomor Ruangan: {{ room.room_number || room.nomor_kamar }}
              </option>
            </select>
          </div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-slate-100 mb-8 space-y-4">
          <div class="flex justify-between items-center">
            <span class="text-[10px] font-black text-slate-400 uppercase">Metode Pembayaran</span>
            <span class="px-3 py-1 bg-amber-50 border border-amber-200 text-[10px] font-black text-amber-700 uppercase tracking-wider rounded-lg">
              {{ selectedPesanan?.payment_method === 'bank_transfer' ? 'Transfer Bank' : 'Cash' }}
            </span>
          </div>

          <div v-if="selectedPesanan?.payment_method === 'cash' || selectedPesanan?.metode_pembayaran === 'Cash'" class="space-y-3 pt-3 border-t border-dashed animate-fade-in">
            <div class="flex items-center gap-6 bg-slate-50 p-3 rounded-xl border mb-2">
              <div class="w-24 h-24 bg-white p-1 border rounded-lg shadow-sm shrink-0">
                <img :src="`https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=${selectedPesanan?.reservation_number || selectedPesanan?.nomor_pemesanan}`" class="w-full h-full object-contain" alt="QR Code">
              </div>
              <div>
                <p class="text-[10px] font-black text-amber-600 uppercase tracking-wider bg-amber-50 px-2 py-0.5 rounded border border-amber-100 inline-block">Bayar Di Tempat (CASH)</p>
                <p class="text-[11px] text-slate-400 font-bold mt-1.5">Kalkulasi uang tunai fisik tamu di bawah ini. Tombol check-in manual otomatis terbuka jika nominal uang tunai mencukupi.</p>
              </div>
            </div>

            <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest">Terima Uang Tunai (Rp)</label>
            <input v-model="uangTunai" type="number" placeholder="Contoh: 500000" class="w-full p-3 bg-slate-50 border rounded-xl font-bold text-sm outline-none focus:border-amber-500 shadow-inner">
            
            <div class="flex justify-between items-center pt-2 text-xs font-semibold">
              <span class="text-slate-400 uppercase text-[9px] font-black">Uang Kembalian</span>
              <span class="text-sm font-black text-slate-800">Rp {{ Number(uangKembalian).toLocaleString() }}</span>
            </div>
          </div>

          <div v-else class="pt-3 border-t border-dashed space-y-4">
            <div class="flex items-center gap-6 bg-slate-50 p-3 rounded-xl border">
              <div class="w-24 h-24 bg-white p-1 border rounded-lg shadow-sm shrink-0">
                <img :src="`https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=${selectedPesanan?.reservation_number || selectedPesanan?.nomor_pemesanan}`" class="w-full h-full object-contain" alt="QR Code">
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5 block">Lampiran Bukti Transfer Tamu</p>
                
                <div v-if="selectedPesanan?.payment_proof" class="w-full h-20 bg-white rounded-lg overflow-hidden border p-1 group relative cursor-pointer" @click="bukaImageMentah(selectedPesanan.payment_proof)">
                  <img :src="`http://localhost:8000/storage/${selectedPesanan.payment_proof}`" class="w-full h-full object-cover rounded" alt="Bukti Transfer">
                  <div class="absolute inset-0 bg-black/40 text-white font-black text-[8px] uppercase flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">👁️ Lihat Bukti</div>
                </div>
                <div v-else class="p-3 bg-rose-50 text-rose-600 border border-rose-100 text-[10px] font-black uppercase rounded-xl tracking-wider text-center">
                  ⚠️ TAMU BELUM UPLOAD BUKTI TRANSFER!
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="flex gap-3 justify-end border-t pt-4">
          <button @click="showCheckinModal = false" class="px-6 py-3.5 bg-white border border-slate-200 text-slate-500 font-bold rounded-xl text-[10px] uppercase tracking-widest hover:bg-slate-50 cursor-pointer">Tutup</button>
          <button @click="tolakPesananManual(selectedPesanan?.id || selectedPesanan?.id_pemesanan)" class="px-6 py-3.5 bg-white border border-rose-200 text-rose-500 font-black rounded-xl text-[10px] uppercase tracking-widest hover:bg-rose-50 cursor-pointer">Tolak Pesanan</button>
          <button @click="confirmCheckInManual" :disabled="isButtonCheckinDisabled" class="px-8 py-3.5 bg-slate-800 hover:bg-slate-900 text-white font-black rounded-xl text-[10px] uppercase tracking-widest shadow-md disabled:opacity-30 disabled:cursor-not-allowed cursor-pointer transition-colors">
            Check-In Manual
          </button>
        </div>
      </div>
    </div>

    <div v-if="showCheckoutModal" class="fixed inset-0 z-[300] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md animate-fade-in">
      <div class="bg-[#FAF9F5] p-10 rounded-[2rem] max-w-md w-full border shadow-2xl">
        <h3 class="text-2xl font-black text-center uppercase tracking-tight text-slate-900 mb-2">Review Check-out</h3>
        <p class="text-center font-mono text-xs text-slate-400 mb-6 uppercase tracking-widest">{{ selectedPesanan?.nomor_pemesanan || selectedPesanan?.reservation_number }}</p>
        
        <div class="bg-white p-6 rounded-2xl border border-slate-100 space-y-4 text-sm mb-6 font-medium shadow-inner">
          <div class="flex justify-between">
            <span class="text-slate-400 uppercase text-[9px] font-black">Keterlambatan Checkout</span>
            <span class="font-black text-slate-800">{{ jamTerlambat }} Jam</span>
          </div>
          <div class="flex justify-between items-center pt-3 border-t border-dashed">
            <span class="text-rose-500 uppercase text-[9px] font-black">Sanksi Denda (Rp 50k / Jam)</span>
            <span class="font-black text-lg text-rose-600">Rp {{ Number(totalDenda).toLocaleString() }}</span>
          </div>

          <div v-if="totalDenda > 0" class="space-y-3 pt-3 border-t border-dashed animate-fade-in">
            <label class="block text-[9px] font-black text-pink-500 uppercase tracking-widest">Terima Uang Tunai Denda (Rp)</label>
            <input v-model="uangTunaiDenda" type="number" placeholder="Input uang tunai denda..." class="w-full p-3 bg-slate-50 border border-pink-200 rounded-xl font-bold text-xs outline-none focus:border-pink-500 shadow-inner">
            <div class="flex justify-between items-center pt-1 text-xs font-semibold">
              <span class="text-slate-400 uppercase text-[9px] font-black">Kembalian Uang Denda</span>
              <span class="text-sm font-black text-slate-800">Rp {{ Number(uangKembalianDenda).toLocaleString() }}</span>
            </div>
          </div>
        </div>

        <div class="flex flex-col gap-2">
          <button @click="confirmCheckOut" 
                  :disabled="totalDenda > 0 && (!uangTunaiDenda || uangTunaiDenda < totalDenda)" 
                  class="py-4 bg-slate-900 text-white font-black rounded-xl uppercase tracking-widest text-[10px] cursor-pointer shadow-md disabled:opacity-30 disabled:cursor-not-allowed transition-colors">
            Selesaikan Checkout & Bersihkan
          </button>
          <button @click="showCheckoutModal = false" class="py-4 bg-slate-100 text-slate-400 font-black rounded-xl uppercase tracking-widest text-[10px] text-center cursor-pointer">Batal</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import ReceptionistSidebar from '~/components/receptionist/ReceptionistSidebar.vue'

definePageMeta({ layout: false })

const user = ref({ name: 'Resepsionis', role: 'resepsionis' })
const reservations = ref([])
const rooms = ref([])

const searchQuery = ref('')
const filterDate = ref('')
const currentTime = ref('')

const showCheckinModal = ref(false)
const showCheckoutModal = ref(false)
const selectedPesanan = ref(null)
const selectedKamarIds = ref([])
const uangTunai = ref(null)
const uangTunaiDenda = ref(null)
const totalDenda = ref(0)
const jamTerlambat = ref(0)

const formatDate = (date) => date ? new Date(date).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) : '-'

const fetchReservations = async () => {
  const token = localStorage.getItem('auth_token')
  try {
    const res = await $fetch('http://localhost:8000/api/pemesanan', { headers: { 'Authorization': `Bearer ${token}` } })
    reservations.value = res.data || res || []
  } catch (err) { console.error(err) }
}

const fetchRooms = async () => {
  const token = localStorage.getItem('auth_token')
  try {
    const res = await $fetch('http://localhost:8000/api/rooms', { headers: { 'Authorization': `Bearer ${token}` } })
    rooms.value = res.data || res || []
  } catch (err) { console.error(err) }
}

const filteredReservations = computed(() => {
  let list = reservations.value
  if (filterDate.value) {
    list = list.filter(p => (p.check_in || p.tgl_check_in || '').startsWith(filterDate.value))
  }
  if (searchQuery.value) {
    const q = searchQuery.value.toLowerCase().trim()
    list = list.filter(p => {
      const nama = (p.nama_tamu || p.nama_pemesan || '').toLowerCase()
      const code = (p.reservation_number || p.nomor_pemesanan || '').toLowerCase()
      return nama.includes(q) || code.includes(q)
    })
  }
  return list
})

const ketersediaanKamarFisik = computed(() => {
  if (!selectedPesanan.value) return []
  return rooms.value.filter(k => k.room_type_id === selectedPesanan.value.room_type_id && (k.status === 'Tersedia' || k.status === 'available'))
})

const uangKembalian = computed(() => {
  if (!selectedPesanan.value || !uangTunai.value) return 0
  const cost = Number(selectedPesanan.value.total_price || selectedPesanan.value.total_harga || 0)
  return Math.max(0, uangTunai.value - cost)
})

const uangKembalianDenda = computed(() => {
  if (!uangTunaiDenda.value || uangTunaiDenda.value < totalDenda.value) return 0
  return uangTunaiDenda.value - totalDenda.value
})

const isButtonCheckinDisabled = computed(() => {
  if (!selectedPesanan.value) return true
  const totalWajib = selectedPesanan.value.quantity || selectedPesanan.value.jumlah_kamar || 1
  if (selectedKamarIds.value.filter(id => id).length !== totalWajib) return true
  
  const isCash = selectedPesanan.value.payment_method === 'cash' || selectedPesanan.value.metode_pembayaran === 'Cash'
  if (isCash) {
    const totalHarga = Number(selectedPesanan.value.total_price || selectedPesanan.value.total_harga || 0)
    if (!uangTunai.value || uangTunai.value < totalHarga) return true
  }
  return false
})

const handleScanner = async () => {
  const code = searchQuery.value.trim().toUpperCase()
  if (!code) return
  
  const found = reservations.value.find(p => (p.reservation_number || p.nomor_pemesanan || '').toUpperCase() === code)
  if (found && (found.status === 'pending' || found.status === 'verified')) {
    const totalWajib = found.quantity || found.jumlah_kamar || 1
    const kamarTersedia = rooms.value.filter(k => (k.status === 'Tersedia' || k.status === 'available') && k.room_type_id === found.room_type_id)
    
    if (kamarTersedia.length < totalWajib) {
      alert(`🚨 AUTO CHECK-IN GAGAL: Kamar fisik kosong tipe ini penuh!`)
      searchQuery.value = ''
      return
    }

    const assignedIds = kamarTersedia.slice(0, totalWajib).map(k => k.id)
    const token = localStorage.getItem('auth_token')
    try {
      await $fetch(`http://localhost:8000/api/pemesanan/${found.id || found.id_pemesanan}/checkin`, {
        method: 'POST',
        headers: { 'Authorization': `Bearer ${token}` },
        body: { status: 'checked_in', room_ids: assignedIds }
      })
      alert(`... AUTO CHECK-IN HARDWARE BERHASIL UNTUK NOMOR TIKET ${code}!`);
      searchQuery.value = ''
      await fetchReservations(); await fetchRooms()
    } catch (err) { alert('Gagal memproses QR hardware.') }
  } else {
    alert('QR Kode tidak valid, atau tamu sudah check-in/checkout.')
    searchQuery.value = ''
  }
}

const tolakPesananManual = async (id) => {
  if (!confirm('Apakah anda yakin ingin membatalkan/menolak pesanan kamar tamu ini?')) return
  const token = localStorage.getItem('auth_token')
  try {
    await $fetch(`http://localhost:8000/api/pemesanan/${id}/checkout`, {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${token}` },
      body: { status: 'cancelled' }
    })
    alert('Pesanan berhasil ditolak & dibatalkan!')
    showCheckinModal.value = false
    await fetchReservations(); await fetchRooms()
  } catch (err) { alert('Gagal menolak pesanan.') }
}

const bukaModalCheckIn = (res) => {
  selectedPesanan.value = res
  uangTunai.value = null
  selectedKamarIds.value = Array(res.quantity || res.jumlah_kamar).fill('')
  showCheckinModal.value = true
}

// ✅ FIX AKURAT: Ambil langsung hasil hitungan denda dari API Backend
const bukaModalCheckout = async (res) => {
  selectedPesanan.value = res
  uangTunaiDenda.value = null
  
  const token = localStorage.getItem('auth_token')
  try {
    const info = await $fetch(`http://localhost:8000/api/kamar/${res.id || res.id_pemesanan}/checkout-info`, {
      headers: { 'Authorization': `Bearer ${token}` }
    })
    jamTerlambat.value = info.jam_terlambat || 0
    totalDenda.value = info.fine || 0
    showCheckoutModal.value = true
  } catch (err) {
    // Kalau API bermasalah, kita pakai Fallback Frontend!
    try {
      const tglCheckOut = res.check_out || res.tgl_check_out
      if (tglCheckOut) {
        const standardCheckOut = new Date(`${tglCheckOut}T12:00:00`)
        const now = new Date()
        if (now > standardCheckOut) {
          const diffMs = now - standardCheckOut
          const diffHours = Math.ceil(diffMs / (1000 * 60 * 60))
          jamTerlambat.value = diffHours
          totalDenda.value = diffHours * 50000
        } else {
          jamTerlambat.value = 0
          totalDenda.value = 0
        }
      }
    } catch (e) {
      jamTerlambat.value = 0
      totalDenda.value = 0
    }
    showCheckoutModal.value = true
  }
}

const confirmCheckOut = async () => {
  const token = localStorage.getItem('auth_token')
  try {
    await $fetch('http://localhost:8000/api/pemesanan/' + (selectedPesanan.value.id || selectedPesanan.value.id_pemesanan) + '/checkout', {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${token}` },
      body: { status: 'checked_out', fine: totalDenda.value }
    })
    alert('Check-out lunas! Status unit teralihkan menjadi Kotor.')
    showCheckoutModal.value = false
    await fetchReservations(); await fetchRooms()
  } catch (err) { alert('Gagal checkout.') }
}

const quickUpdateRoom = async (room) => {
  if (room.status !== 'Kotor' && room.status !== 'dirty') return
  if (!confirm(`Ubah status kamar ${room.room_number || room.nomor_kamar} jadi Bersih & Tersedia?`)) return
  const token = localStorage.getItem('auth_token')
  try {
    await $fetch(`http://localhost:8000/api/rooms/${room.id}`, {
      method: 'PUT',
      headers: { 'Authorization': `Bearer ${token}` },
      body: { room_type_id: room.room_type_id, room_number: room.room_number || room.nomor_kamar, status: 'Tersedia' }
    })
    await fetchRooms()
  } catch (err) { alert('Gagal.') }
}

const updateTime = () => { currentTime.value = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' }) }

onMounted(() => {
  const localToken = localStorage.getItem('auth_token')
  const savedUser = localStorage.getItem('auth_user')
  
  if (savedUser) {
    const checkObj = JSON.parse(savedUser)
    const cleanRole = (checkObj.role || '').toLowerCase()
    if (cleanRole !== 'resepsionis' && cleanRole !== 'receptionist' && cleanRole !== 'admin') {
      window.location.href = '/login'
      return
    }
    user.value = checkObj
  }

  if (!localToken) {
    window.location.href = '/login'
  } else {
    fetchReservations(); fetchRooms(); updateTime()
    setInterval(updateTime, 1000)
  }
})
</script>

<style scoped>
@keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
.animate-fade-in { animation: fadeIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
</style>