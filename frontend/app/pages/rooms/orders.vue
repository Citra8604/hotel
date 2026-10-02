<template>
  <!-- Menambahkan md:pl-64 lg:pl-72 agar seluruh judul & tabel pesanan tidak tertutup sidebar -->
  <div class="min-h-screen bg-slate-50 text-slate-900 selection:bg-pink-500/30 font-sans overflow-x-hidden md:pl-64 lg:pl-72 transition-all duration-300">
    <div class="fixed inset-0 overflow-hidden pointer-events-none -z-10">
      <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] bg-pink-100 rounded-full blur-[120px] opacity-60"></div>
      <div class="absolute bottom-0 right-[10%] w-[30%] h-[30%] bg-rose-50 rounded-full blur-[100px] opacity-40"></div>
    </div>

    <GuestNavbar activePage="orders" />

    <main class="max-w-7xl mx-auto px-6 lg:px-8 py-10 md:py-14 relative z-10">
      <header class="mb-10">
        <p class="text-[10px] font-black text-pink-500 uppercase tracking-[0.4em] mb-2">Transaction Track</p>
        <h1 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight uppercase">Daftar Transaksi Anda</h1>
        <p class="text-slate-500 text-sm mt-1 font-medium">Selesaikan proses administrasi transfer atau simpan struk nota resmi check-in Anda.</p>
      </header>

      <section class="bg-white p-6 md:p-8 rounded-[2.5rem] border border-pink-50 shadow-xl shadow-pink-500/5">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse min-w-[700px]">
            <thead>
              <tr class="border-b border-pink-50 text-[10px] font-black uppercase tracking-widest text-slate-400 bg-slate-50/50">
                <th class="px-5 py-4">Informasi Kamar</th>
                <th class="px-5 py-4">Masa Inap</th>
                <th class="px-5 py-4">Total Biaya</th>
                <th class="px-5 py-4">Metode Pembayaran</th>
                <th class="px-5 py-4">Status Transaksi</th>
                <th class="px-5 py-4 text-center">Aksi Dokumen</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-pink-50">
              <tr v-for="res in myReservations" :key="res.id" class="hover:bg-pink-50/10 transition-colors">
                <td class="px-5 py-4">
                  <p class="font-black text-slate-900 text-sm uppercase">{{ res.nama_tipe || res.room_type?.name || 'Kategori Kamar' }}</p>
                  <p class="text-[10px] font-mono font-bold text-slate-400 mt-0.5 uppercase tracking-wider">{{ res.nomor_pemesanan || res.reservation_number }}</p>
                  <p class="text-[10px] font-bold text-pink-500 mt-0.5">{{ res.quantity || res.jumlah_kamar }} Unit</p>
                </td>
                <td class="px-5 py-4 text-xs font-bold text-slate-600">
                  <p>Check-In: {{ formatDate(res.tgl_check_in || res.check_in) }}</p>
                  <p class="text-slate-400 mt-0.5">Check-Out: {{ formatDate(res.tgl_check_out || res.check_out) }}</p>
                </td>
                <td class="px-5 py-4 text-sm font-black text-slate-900">
                  {{ formatPrice(res.total_harga || res.total_price) }}
                  <p v-if="res.late_fine > 0 || res.denda > 0" class="text-[10px] font-bold text-rose-500 mt-0.5">+ Penalti: {{ formatPrice(res.late_fine || res.denda) }}</p>
                </td>
                <td class="px-5 py-4 text-xs font-black uppercase tracking-wider text-slate-500">
                  {{ res.metode_pembayaran || (res.payment_method === 'bank_transfer' ? 'Transfer Bank' : 'Cash') }}
                </td>
                <td class="px-5 py-4">
                  <span :class="getStatusClass(res.status_pemesanan || res.status)" class="px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest border">
                    {{ res.status_pemesanan || res.status }}
                  </span>
                </td>
                <td class="px-5 py-4 text-center">
                  <div class="flex justify-center items-center gap-2">
                    
                    <button 
                      v-if="(res.payment_method === 'bank_transfer' || res.metode_pembayaran === 'Transfer Bank') && !['checked_in', 'checked_out', 'cancelled'].includes(res.status)" 
                      @click="triggerUploadProof(res.id || res.id_pemesanan)" 
                      class="px-3.5 py-2 bg-pink-500 hover:bg-pink-600 text-white text-[10px] font-black rounded-xl uppercase tracking-widest transition-colors cursor-pointer shadow-md"
                    >
                      📁 Upload Bukti
                    </button>

                    <button @click="printReceipt(res.id || res.id_pemesanan)" class="px-3.5 py-2 bg-slate-900 hover:bg-pink-500 text-white text-[10px] font-black rounded-xl uppercase tracking-widest transition-colors cursor-pointer shadow-md">
                      🖨️ Cetak Nota
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="myReservations.length === 0">
                <td colspan="6" class="px-6 py-12 text-center text-slate-400 italic font-bold text-sm">Anda belum memiliki riwayat data reservasi kamar hotel.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>

      <input type="file" ref="fileInputRef" accept="image/*" class="hidden" @change="onFileSelected" />
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

definePageMeta({ layout: false })

const user = ref({ name: 'Tamu', role: 'guest' })
const myReservations = ref([])
const fileInputRef = ref(null)
const activeReservationId = ref(null)

const formatPrice = (price) =>
  new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(price || 0)

const formatDate = (dateStr) => dateStr ? new Date(dateStr).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) : '-'

const fetchMyHistory = async () => {
  const token = localStorage.getItem('auth_token')
  try {
    const response = await $fetch('http://localhost:8000/api/pemesanan/saya', {
      headers: { 'Authorization': `Bearer ${token}` }
    })
    myReservations.value = response.data || response || []
  } catch (err) {
    console.error('Gagal memuat riwayat transaksi:', err)
  }
}

const triggerUploadProof = (id) => {
  activeReservationId.value = id
  fileInputRef.value.click()
}

const onFileSelected = async (e) => {
  const file = e.target.files[0]
  if (!file) return

  const token = localStorage.getItem('auth_token')
  const formData = new FormData()
  formData.append('bukti_pembayaran', file)

  try {
    await $fetch(`http://localhost:8000/api/pemesanan/${activeReservationId.value}/upload-bukti`, {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' },
      body: formData
    })
    alert('Sukses! Bukti pembayaran berhasil diunggah ke kasir internal.')
    await fetchMyHistory()
  } catch (err) {
    alert('Gagal mengunggah berkas bukti transfer.')
  } finally {
    e.target.value = ''
  }
}

const printReceipt = (id) => {
  window.open(`/print/receipt-${id}`, '_blank')
}

const getStatusClass = (status) => {
  const s = String(status).toLowerCase()
  if (s === 'checked_in' || s === 'check-in') return 'bg-blue-50 text-blue-600 border-blue-200'
  if (s === 'checked_out' || s === 'check-out') return 'bg-slate-50 text-slate-500 border-slate-200'
  if (s === 'cancelled' || s === 'dibatalkan') return 'bg-rose-50 text-rose-600 border-rose-200'
  if (s === 'verified') return 'bg-emerald-50 text-emerald-600 border-emerald-200'
  return 'bg-amber-50 text-amber-600 border-amber-200'
}

onMounted(() => {
  const localToken = localStorage.getItem('auth_token')
  const savedUser = localStorage.getItem('auth_user')
  
  if (savedUser) user.value = JSON.parse(savedUser)

  if (!localToken) {
    window.location.href = '/login'
  } else {
    fetchMyHistory()
  }
})
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.4s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>