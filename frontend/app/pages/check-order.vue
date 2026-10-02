<template>
  <!-- Menambahkan md:pl-64 lg:pl-72 agar sejajar rapi dengan tata letak sidebar -->
  <div class="min-h-screen bg-slate-50 text-slate-900 selection:bg-pink-500/30 font-sans overflow-x-hidden md:pl-64 lg:pl-72 transition-all duration-300">
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
      <div class="absolute top-[10%] left-[10%] w-[40%] h-[40%] bg-pink-100 rounded-full blur-[120px] opacity-60"></div>
      <div class="absolute bottom-0 right-[10%] w-[30%] h-[30%] bg-rose-50 rounded-full blur-[100px] opacity-40"></div>
    </div>

    <GuestNavbar activePage="orders" />

    <main class="relative z-10 max-w-2xl mx-auto pt-10 md:pt-14 pb-12 px-6">
      <div class="mb-8 flex justify-between items-center">
        <NuxtLink to="/tamu/dashboard" class="inline-flex items-center gap-2 text-[10px] font-black text-slate-400 hover:text-pink-600 uppercase tracking-[0.2em] transition-colors">
          <span>← Kembali ke Dashboard</span>
        </NuxtLink>
        <h1 class="text-2xl font-black tracking-tighter uppercase text-slate-900">Cek Pesanan</h1>
      </div>

      <section class="bg-white p-8 md:p-12 rounded-[3.5rem] border border-pink-50 shadow-2xl shadow-pink-500/5 mb-12 relative overflow-hidden">
        <h2 class="text-2xl font-black mb-10 text-center uppercase tracking-tight text-slate-900 relative z-10">Temukan Pesanan Anda</h2>
        <form @submit.prevent="findOrder" class="space-y-8 relative z-10">
          <div class="space-y-3">
            <label class="text-[10px] font-black text-pink-500 uppercase tracking-widest ml-2">Nomor Reservasi</label>
            <input v-model="search.nomor_pemesanan" type="text" placeholder="Contoh: RSV-XXXXXX" class="w-full px-6 py-5 bg-slate-50 border border-slate-100 rounded-2xl font-bold text-slate-900 outline-none focus:ring-2 focus:ring-pink-500 transition-all" required>
          </div>
          <div class="space-y-3">
            <label class="text-[10px] font-black text-pink-500 uppercase tracking-widest ml-2">Alamat Email Pemesan</label>
            <input v-model="search.email_pemesan" type="email" placeholder="email@anda.com" class="w-full px-6 py-5 bg-slate-50 border border-slate-100 rounded-2xl font-bold text-slate-900 outline-none focus:ring-2 focus:ring-pink-500 transition-all" required>
          </div>
          <button type="submit" :disabled="loading" class="w-full py-5 bg-slate-900 hover:bg-pink-600 text-white font-black rounded-2xl text-[10px] uppercase tracking-widest shadow-md transition-all cursor-pointer">
            {{ loading ? 'Mencari...' : 'Cek Detail Pesanan' }}
          </button>
        </form>
      </section>

      <div v-if="order" class="bg-white p-8 md:p-12 rounded-[4rem] border border-pink-100 shadow-xl relative overflow-hidden">
        <div class="flex flex-col md:flex-row justify-between items-start gap-6 mb-12 relative z-10">
          <div>
            <p class="text-[10px] font-black text-pink-500 uppercase tracking-widest mb-2">Status Pesanan</p>
            <h3 class="text-4xl font-black uppercase text-slate-900">{{ order.status_pemesanan }}</h3>
          </div>
          <div class="md:text-right">
            <p class="text-[10px] font-black text-slate-400 tracking-widest mb-2">Total Biaya</p>
            <p class="text-3xl font-black text-pink-600">Rp {{ Number(order.total_harga || 0).toLocaleString('id-ID') }}</p>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 border-b pb-6">
          <div>
            <p class="text-[10px] uppercase font-bold text-slate-400">Nama Pemesan</p>
            <p class="font-black text-slate-800">{{ order.nama_pemesan }}</p>
          </div>
          <div>
            <p class="text-[10px] uppercase font-bold text-slate-400">Kamar Diorder</p>
            <p class="font-black text-slate-800">{{ order.jumlah_kamar }} Unit ({{ order.jumlah_malam }} Malam)</p>
          </div>
        </div>

        <button @click="cetakBukti(order.id_pemesanan, order.nomor_pemesanan, order.email_pemesan)" class="w-full py-4 bg-pink-50 text-pink-600 hover:bg-pink-500 hover:text-white transition-colors font-black rounded-2xl border border-pink-100 text-[10px] uppercase tracking-widest cursor-pointer">
          🖨️ Cetak Bukti Pemesanan (Invoice)
        </button>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref } from 'vue'

definePageMeta({ layout: false })

const config = useRuntimeConfig()
const search = ref({ nomor_pemesanan: '', email_pemesan: '' })
const order = ref(null)
const loading = ref(false)

const findOrder = async () => {
  loading.value = true
  order.value = null
  try {
    const data = await $fetch('/pemesanan/cek-publik', {
      baseURL: config.public.apiBase || 'http://localhost:8000/api',
      method: 'POST',
      body: search.value
    })
    order.value = data.data || data
  } catch (err) {
    alert(err.response?._data?.message || 'Pesanan tidak ditemukan. Cek kembali nomor dan email Anda.')
  } finally {
    loading.value = false
  }
}

const cetakBukti = (id, nomor, email) => {
  window.open(`/cetak-invoice/${id}?nomor=${nomor}&email=${email}`, '_blank')
}
</script>