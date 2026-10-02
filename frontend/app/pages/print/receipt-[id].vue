<template>
  <div class="min-h-screen bg-slate-100 py-12 px-4 print:bg-white print:p-0 font-sans">
    <div v-if="reservation" id="print-area" class="max-w-4xl mx-auto bg-white shadow-2xl rounded-[2rem] overflow-hidden print:shadow-none print:rounded-none relative">
      
      <div class="absolute inset-0 flex items-center justify-center opacity-[0.03] pointer-events-none select-none overflow-hidden">
        <h1 class="text-[20rem] font-black rotate-[-35deg] tracking-tighter">INVOICE</h1>
      </div>

      <div class="h-4 bg-gradient-to-r from-slate-900 via-pink-500 to-rose-600"></div>
      
      <div class="p-12 md:p-16 relative z-10">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-8 mb-16">
          <div class="flex items-center gap-6">
            <div class="w-20 h-20 bg-slate-900 rounded-3xl flex items-center justify-center shadow-xl">
              <span class="text-3xl text-white font-black">H71</span>
            </div>
            <div>
              <h1 class="text-4xl font-black tracking-tighter text-slate-900 leading-none">HOTEL 71</h1>
              <p class="text-xs font-black text-pink-500 uppercase tracking-[0.3em] mt-1">Official Guest Billing</p>
            </div>
          </div>
          <div class="text-right">
            <div class="inline-block px-6 py-2 bg-slate-900 text-white rounded-full text-[10px] font-black uppercase tracking-widest mb-4">
              Official Invoice
            </div>
            <h2 class="text-2xl font-mono font-bold text-slate-800">{{ reservation.reservation_number || reservation.nomor_pemesanan }}</h2>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Diterbitkan: {{ new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) }}</p>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mb-16 p-8 bg-slate-50 rounded-[2.5rem] border border-slate-100">
          <div class="space-y-4">
            <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] flex items-center gap-2">
              <span class="w-1.5 h-1.5 rounded-full bg-pink-500"></span> Tagihan Untuk
            </h3>
            <div>
              <p class="text-xl font-black text-slate-900">{{ reservation.nama_tamu || reservation.nama_pemesan }}</p>
              <p class="text-sm font-medium text-slate-500 mt-1">{{ reservation.email || reservation.email_pemesan }}</p>
            </div>
          </div>
          <div class="space-y-4 md:border-l md:border-slate-200 md:pl-12">
            <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] flex items-center gap-2">
              <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Detail Penginapan
            </h3>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <p class="text-[9px] font-black text-slate-400 uppercase">Check-in</p>
                <p class="text-sm font-bold text-slate-800">{{ formatDate(reservation.check_in || reservation.tgl_check_in) }}</p>
              </div>
              <div>
                <p class="text-[9px] font-black text-slate-400 uppercase">Check-out</p>
                <p class="text-sm font-bold text-slate-800">{{ formatDate(reservation.check_out || reservation.tgl_check_out) }}</p>
              </div>
              <div class="col-span-2">
                <p class="text-[9px] font-black text-slate-400 uppercase">Kategori & Kuantitas</p>
                <p class="text-sm font-bold text-slate-800">{{ reservation.room_type?.name || reservation.nama_tipe || 'Kamar Pilihan' }} ({{ reservation.quantity || reservation.jumlah_kamar }} Unit)</p>
              </div>
            </div>
          </div>
        </div>

        <div class="mb-16">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="border-b-2 border-slate-900">
                <th class="py-6 px-4 text-[10px] font-black uppercase tracking-widest text-slate-900">Layanan / Item</th>
                <th class="py-6 px-4 text-[10px] font-black uppercase tracking-widest text-slate-900 text-center">Qty</th>
                <th class="py-6 px-4 text-[10px] font-black uppercase tracking-widest text-slate-900 text-right">Subtotal</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr class="group">
                <td class="py-8 px-4">
                  <p class="font-black text-slate-900">Sewa Kamar Hotel Resmi</p>
                  <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tight mt-1">Sistem Pemesanan Terverifikasi</p>
                </td>
                <td class="py-8 px-4 text-center font-bold text-slate-700">{{ reservation.quantity || reservation.jumlah_kamar }} Kamar</td>
                <td class="py-8 px-4 text-right font-black text-slate-900 text-lg">Rp {{ Number(reservation.total_price || reservation.total_harga || 0).toLocaleString() }}</td>
              </tr>
              <tr v-if="Number(reservation.late_fine || reservation.denda || 0) > 0" class="bg-rose-50/50">
                <td class="py-8 px-4">
                  <p class="font-black text-rose-600">Denda Keterlambatan</p>
                  <p class="text-[10px] font-bold text-rose-400 uppercase tracking-tight mt-1">Biaya Overtime Checkout (Rp 50k / Jam)</p>
                </td>
                <td class="py-8 px-4 text-center font-bold text-rose-400">-</td>
                <td class="py-8 px-4 text-right font-black text-rose-600 text-lg">Rp {{ Number(reservation.late_fine || reservation.denda).toLocaleString() }}</td>
              </tr>
            </tbody>
            <tfoot>
              <tr>
                <td colspan="2" class="py-12 px-4">
                  <div class="p-6 bg-slate-900 rounded-3xl text-white inline-block min-w-[240px]">
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-50 mb-4">Metode Pembayaran</p>
                    <p class="text-sm font-bold uppercase">💳 {{ reservation.payment_method === 'bank_transfer' || reservation.metode_pembayaran === 'Transfer Bank' ? 'Transfer Bank' : 'Cash' }}</p>
                    <div class="mt-4 pt-4 border-t border-white/10 flex justify-between items-center">
                      <span class="text-[10px] font-bold opacity-50 uppercase">Status:</span>
                      <span class="text-[10px] font-black uppercase px-3 py-1 rounded-full text-white"
                            :class="{
                              'bg-amber-500': reservation.status === 'pending',
                              'bg-emerald-500': reservation.status === 'verified',
                              'bg-blue-500': reservation.status === 'checked_in',
                              'bg-slate-500': reservation.status === 'checked_out',
                              'bg-rose-500': reservation.status === 'cancelled'
                            }">
                        {{ 
                          reservation.status === 'pending' ? 'Belum Lunas' :
                          reservation.status === 'verified' ? 'Lunas (Verified)' :
                          reservation.status === 'checked_in' ? 'Check-In' :
                          reservation.status === 'checked_out' ? 'Check-Out' : 'Dibatalkan'
                        }}
                      </span>
                    </div>
                  </div>
                </td>
                <td class="py-12 px-4 text-right align-bottom">
                  <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] mb-2">Total Biaya</p>
                  <p class="text-5xl font-black text-slate-900 tracking-tighter">Rp {{ (Number(reservation.total_price || reservation.total_harga || 0) + Number(reservation.late_fine || reservation.denda || 0)).toLocaleString() }}</p>
                </td>
              </tr>
            </tfoot>
          </table>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-16 pt-12 border-t border-slate-100">
          <div class="space-y-4">
            <h4 class="text-[10px] font-black text-slate-900 uppercase tracking-widest">Catatan Penting:</h4>
            <ul class="text-[9px] text-slate-400 space-y-2 leading-relaxed font-medium">
              <li class="flex gap-2"><span>•</span> Simpan invoice cetak ini sebagai bukti check-in/check-out resmi.</li>
              <li class="flex gap-2"><span>•</span> Barcode QR dapat digunakan untuk scan otomatis di front desk hotel.</li>
            </ul>
          </div>
          <div class="flex flex-row items-center justify-between gap-8 border-l-2 border-slate-50 pl-8">
            <div class="flex flex-col items-center">
              <div class="w-32 h-32 p-2 border-2 border-slate-100 rounded-2xl bg-white shadow-sm mb-4">
                <img :src="`https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=${reservation.reservation_number || reservation.nomor_pemesanan}`" alt="Hardware QR Code" class="w-full h-full object-contain">
              </div>
              <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Scan via Hardware Link</p>
            </div>
            <div class="flex-1 flex flex-col items-center text-center">
              <div class="w-full max-w-[200px] border-b-2 border-slate-900 pb-20 relative">
                <p class="text-[10px] font-black text-slate-300 absolute inset-0 flex items-center justify-center italic opacity-30">Sign Departement</p>
              </div>
              <p class="mt-4 text-[10px] font-black text-slate-900 uppercase tracking-widest">Front Office Hotel 71</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="fixed bottom-10 right-10 print:hidden flex gap-4">
      <button @click="bukaCetak" class="px-10 py-5 bg-slate-900 text-white font-black rounded-2xl shadow-2xl hover:scale-105 transition-all uppercase tracking-widest text-xs flex items-center gap-3 cursor-pointer">
        <span>🖨️</span> Cetak Lembar Invoice
      </button>
      
      <button @click="navigateTo('/rooms/orders')" class="px-10 py-5 bg-white text-slate-900 border font-black rounded-2xl shadow-xl hover:scale-105 transition-all uppercase tracking-widest text-xs cursor-pointer">
        Kembali Ke Riwayat
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

definePageMeta({ layout: false })

const route = useRoute()
const reservation = ref(null)

const formatDate = (dateStr) => {
  if (!dateStr) return '-'
  return new Date(dateStr).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}

const bukaCetak = () => { window.print() }

onMounted(async () => {
  const token = localStorage.getItem('auth_token')
  try {
    const response = await $fetch('http://localhost:8000/api/pemesanan', {
      headers: { 'Authorization': `Bearer ${token}` }
    })
    const list = response.data || response || []
    const targetId = route.params.id
    
    const found = list.find(b => b.id == targetId || b.id_pemesanan == targetId)
    if (found) {
      reservation.value = found
    } else {
      alert('Data berkas reservasi tidak ditemukan di dalam riwayat sistem!')
    }
  } catch (err) {
    console.error(err)
    alert('Gagal mengambil data.')
  }
})
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@200;400;600;800&family=JetBrains+Mono:wght@700&display=swap');
.font-sans { font-family: 'Plus Jakarta Sans', sans-serif; }
.font-mono { font-family: 'JetBrains Mono', monospace; }
@media print {
  body { background: white !important; }
  .print\:hidden { display: none !important; }
}
@page { size: A4; margin: 10mm; }
</style>