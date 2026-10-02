<template>
  <div class="min-h-screen bg-slate-50 text-slate-900 selection:bg-pink-500/30 font-sans overflow-x-hidden">
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
      <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] bg-pink-100 rounded-full blur-[120px] opacity-60"></div>
      <div class="absolute bottom-0 right-[10%] w-[30%] h-[30%] bg-rose-50 rounded-full blur-[100px] opacity-40"></div>
    </div>

    <nav class="relative z-[100] px-6 md:px-12 py-10 flex justify-between items-center max-w-7xl mx-auto">
      <NuxtLink to="/rooms" class="flex items-center gap-3 text-[10px] font-black text-slate-400 hover:text-pink-600 transition-all uppercase tracking-[0.3em] group">
        <span>← Kembali</span>
      </NuxtLink>
      <h1 class="text-2xl md:text-3xl font-black tracking-tighter uppercase text-slate-900 text-center">Konfirmasi Reservasi</h1>
      <div class="hidden lg:block w-48"></div>
    </nav>

    <main class="relative z-10 max-w-7xl mx-auto py-8 pb-24 px-6 md:px-12">
      <div class="mb-16 max-w-4xl mx-auto">
        <div class="flex justify-between items-center relative">
          <div class="absolute top-1/2 left-0 w-full h-0.5 bg-slate-200 -translate-y-1/2 z-0"></div>
          <div class="absolute top-1/2 left-0 h-0.5 bg-pink-500 -translate-y-1/2 z-0 transition-all duration-500" :style="{ width: ((currentStep - 1) / 2 * 100) + '%' }"></div>
          <div
            v-for="step in [
              { n: 1, title: 'Jadwal', icon: '📅' },
              { n: 2, title: 'Biodata', icon: '👤' },
              { n: 3, title: 'Bayar', icon: '💳' }
            ]"
            :key="step.n"
            class="relative z-10 flex flex-col items-center gap-4"
          >
            <div
              class="w-12 h-12 rounded-2xl flex items-center justify-center font-black text-sm border-4 transition-all duration-300"
              :class="currentStep >= step.n ? 'bg-white border-pink-500 text-pink-500' : 'bg-slate-50 border-slate-200 text-slate-300'"
            >
              <span v-if="currentStep > step.n">✓</span>
              <span v-else>{{ step.icon }}</span>
            </div>
          </div>
        </div>
      </div>

      <div v-if="bookingData" class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
        <div class="lg:col-span-7 space-y-10">

          <section v-if="currentStep === 1" class="bg-white p-10 rounded-[4rem] border border-pink-50 shadow-xl">
            <h2 class="text-xl font-black uppercase tracking-tight text-slate-900 mb-8">Pilih Jadwal Menginap</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
              <div class="flex flex-col gap-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Check-In</label>
                <input v-model="bookingData.check_in" type="date" :min="today" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-pink-400 outline-none font-bold" />
              </div>
              <div class="flex flex-col gap-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Check-Out</label>
                <input v-model="bookingData.check_out" type="date" :min="bookingData.check_in || today" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-pink-400 outline-none font-bold" />
              </div>
            </div>
            <p v-if="totalNights > 0" class="mt-4 text-sm text-pink-500 font-bold">{{ totalNights }} malam dipilih</p>
            <button @click="goToStep2" class="w-full py-4 mt-6 bg-slate-900 hover:bg-pink-500 text-white font-black uppercase tracking-widest text-xs rounded-xl transition-colors cursor-pointer">
              Lanjut Ke Biodata →
            </button>
          </section>

          <section v-if="currentStep === 2" class="bg-white p-10 rounded-[4rem] border border-pink-50 shadow-xl">
            <h2 class="text-xl font-black uppercase tracking-tight text-slate-900 mb-8">Data Pemesan</h2>
            <div class="space-y-4">
              <div class="flex flex-col gap-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Nama Sesuai KTP</label>
                <input v-model="biodataForm.nama_pemesan" type="text" placeholder="Nama lengkap..." class="w-full p-4 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-pink-400 outline-none font-bold" />
              </div>
              <div class="flex flex-col gap-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Email</label>
                <input v-model="biodataForm.email_pemesan" type="email" placeholder="email@contoh.com" class="w-full p-4 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-pink-400 outline-none font-bold" />
              </div>
              <div class="flex flex-col gap-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">No. HP</label>
                <input v-model="biodataForm.no_hp" type="tel" placeholder="08xxxxxxxxxx" class="w-full p-4 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-pink-400 outline-none font-bold" />
              </div>
            </div>
            <div class="flex gap-4 mt-6">
              <button @click="currentStep = 1" class="px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition-colors uppercase text-xs">← Kembali</button>
              <button @click="goToStep3" class="flex-1 py-4 bg-slate-900 hover:bg-pink-500 text-white font-black rounded-xl uppercase tracking-widest text-xs transition-colors cursor-pointer">
                Lanjut Ke Pembayaran →
              </button>
            </div>
          </section>

          <section v-if="currentStep === 3" class="bg-white p-10 rounded-[4rem] border border-pink-50 shadow-xl">
            <h2 class="text-xl font-black uppercase tracking-tight text-slate-900 mb-2">Metode Pembayaran</h2>
            
            <p v-if="isWajibTransfer" class="text-xs text-rose-500 font-black uppercase tracking-wider mb-6">
              ⚠️ Harga Kamar >= Rp 500,000. Metode Cash Otomatis Dinonaktifkan!
            </p>
            <p v-else class="text-xs text-slate-400 font-medium mb-6">Silakan tentukan metode penyelesaian tagihan Anda.</p>
            
            <div class="space-y-3">
              <label class="flex items-center gap-4 p-5 rounded-2xl border-2 cursor-pointer transition-all"
                     :class="selectedPayment === 'Transfer Bank' ? 'border-pink-500 bg-pink-50' : 'border-slate-100 bg-slate-50 hover:border-pink-200'">
                <input type="radio" v-model="selectedPayment" value="Transfer Bank" class="accent-pink-500" />
                <span class="font-bold text-slate-700">Transfer Bank / QRIS</span>
              </label>

              <label v-if="!isWajibTransfer" class="flex items-center gap-4 p-5 rounded-2xl border-2 cursor-pointer transition-all"
                     :class="selectedPayment === 'Cash' ? 'border-pink-500 bg-pink-50' : 'border-slate-100 bg-slate-50 hover:border-pink-200'">
                <input type="radio" v-model="selectedPayment" value="Cash" class="accent-pink-500" />
                <span class="font-bold text-slate-700">Bayar di Tempat (Cash)</span>
              </label>
            </div>

            <div v-if="selectedPayment === 'Transfer Bank'" class="mt-8 p-6 bg-slate-50 rounded-3xl border border-dashed border-pink-200 flex flex-col items-center text-center animate-fade-in">
              <p class="text-[10px] font-black text-pink-500 uppercase tracking-widest mb-3">Pindai Kode QRIS Resmi Hotel 71</p>
              <div class="w-44 h-44 p-2 bg-white border rounded-2xl shadow-sm mb-3">
                <img :src="`https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=HOTEL71-TOTAL-${totalPrice}`" class="w-full h-full object-contain" alt="QRIS Dummy" />
              </div>
              <p class="text-[11px] font-black text-slate-800 uppercase tracking-tight">Grand Luxe Digital Merchant</p>
              <p class="text-[9px] text-slate-400 font-medium mt-1">Silakan lakukan transfer sesuai total summary, lalu klik tombol di bawah untuk masuk ke antrean berkas.</p>
            </div>

            <div v-if="errorMessage" class="mt-4 p-4 bg-rose-50 border border-rose-200 rounded-2xl">
              <p class="text-rose-600 text-sm font-bold whitespace-pre-line">{{ errorMessage }}</p>
            </div>

            <div class="flex gap-4 mt-6">
              <button @click="currentStep = 2" class="px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition-colors uppercase text-xs">← Kembali</button>
              <button @click="confirmBooking" :disabled="submitting" class="flex-1 py-4 bg-pink-500 hover:bg-pink-600 text-white font-black rounded-xl transition-colors disabled:opacity-50 disabled:cursor-not-allowed uppercase text-xs tracking-widest cursor-pointer">
                {{ submitting ? 'Memproses...' : '✓ Selesaikan Booking' }}
              </button>
            </div>
          </section>
        </div>

        <div class="lg:col-span-5 sticky top-8 bg-white p-10 rounded-[4rem] border border-pink-50 shadow-2xl">
          <p class="text-[10px] font-black text-pink-500 uppercase tracking-widest mb-6">Summary Tagihan</p>
          <p class="font-black text-2xl text-slate-800 tracking-tight">{{ bookingData.nama_tipe || bookingData.name }}</p>
          <div class="mt-6 space-y-3 text-sm text-slate-500 font-medium">
            <div class="flex justify-between">
              <span>Jumlah Kamar</span>
              <span class="font-bold text-slate-700">{{ bookingData.jumlah_kamar }} Unit</span>
            </div>
            <div class="flex justify-between">
              <span>Durasi</span>
              <span class="font-bold text-slate-700">{{ totalNights }} Malam</span>
            </div>
            <div class="flex justify-between">
              <span>Harga/Malam</span>
              <span class="font-bold text-slate-700">{{ formatPrice(bookingData.harga || bookingData.price) }}</span>
            </div>
          </div>
          <div class="border-t border-dashed border-pink-100 mt-6 pt-6 flex justify-between items-center">
            <span class="text-sm font-black text-slate-500 uppercase tracking-widest">Total</span>
            <span class="text-2xl font-black text-pink-600">{{ formatPrice(totalPrice) }}</span>
          </div>
          <div v-if="bookingData.check_in && bookingData.check_out" class="mt-4 pt-4 border-t border-dashed border-pink-100 text-xs text-slate-400 space-y-1">
            <div class="flex justify-between">
              <span>Check-In</span>
              <span class="font-bold">{{ bookingData.check_in }}</span>
            </div>
            <div class="flex justify-between">
              <span>Check-Out</span>
              <span class="font-bold">{{ bookingData.check_out }}</span>
            </div>
          </div>
        </div>
      </div>

      <div v-else class="text-center py-32">
        <p class="text-slate-400 font-bold uppercase tracking-widest text-sm mb-6">Data booking tidak ditemukan.</p>
        <NuxtLink to="/rooms" class="px-8 py-4 bg-slate-900 text-white font-bold rounded-2xl">← Pilih Kamar Dulu</NuxtLink>
      </div>
    </main>

    <Transition name="fade">
      <div v-if="successReservation" class="fixed inset-0 z-[300] flex items-center justify-center p-6 bg-slate-900/80 backdrop-blur-md">
        <div class="bg-white p-12 rounded-[5rem] max-w-xl w-full text-center border shadow-2xl">
          <div class="w-20 h-20 bg-pink-500 rounded-[2.5rem] flex items-center justify-center text-4xl mx-auto mb-8 text-white">✓</div>
          <h3 class="text-3xl font-black uppercase tracking-tight text-slate-900 mb-2">Booking Berhasil!</h3>
          <p class="text-slate-400 text-sm mb-8">Reservasi Anda telah dikonfirmasi oleh sistem.</p>
          <div class="bg-slate-50 p-6 rounded-3xl border text-center mb-8">
            <p class="text-[9px] font-black text-pink-500 uppercase tracking-widest mb-1">Nomor Registrasi Reservasi</p>
            <p class="text-3xl font-black text-slate-900 font-mono tracking-wider uppercase">{{ successReservation.nomor_pemesanan || successReservation.reservation_number }}</p>
          </div>
          <div class="grid grid-cols-1 gap-4">
            <NuxtLink to="/orders" class="py-5 bg-slate-900 text-white font-black rounded-3xl text-[11px] uppercase tracking-widest text-center block hover:bg-pink-600 transition-colors shadow-lg">
              Buka Riwayat & Upload Bukti →
            </NuxtLink>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

definePageMeta({ layout: false })

const currentStep = ref(1)
const bookingData = ref(null)
const selectedPayment = ref('Transfer Bank')
const submitting = ref(false)
const successReservation = ref(null)
const errorMessage = ref('')

const biodataForm = ref({ nama_pemesan: '', email_pemesan: '', no_hp: '' })
const today = new Date().toISOString().split('T')[0]

const formatPrice = (price) =>
  new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(price || 0)

const isWajibTransfer = computed(() => {
  if (!bookingData.value) return false
  const hargaSatuan = bookingData.value.harga || bookingData.value.price || 0
  return hargaSatuan >= 500000
})

const totalNights = computed(() => {
  if (!bookingData.value?.check_in || !bookingData.value?.check_out) return 0
  const start = new Date(bookingData.value.check_in)
  const end = new Date(bookingData.value.check_out)
  return end > start ? Math.max(1, Math.round((end - start) / (1000 * 60 * 60 * 24))) : 0
})

const totalPrice = computed(() => {
  if (!bookingData.value) return 0
  const hargaKamar = bookingData.value.harga || bookingData.value.price || 0
  return hargaKamar * (bookingData.value.jumlah_kamar || 1) * totalNights.value
})

const handleFileUpload = (e) => { buktiFile.value = e.target.files[0] || null }

const goToStep2 = () => {
  if (!bookingData.value.check_in || !bookingData.value.check_out) {
    alert('Mohon isi tanggal check-in dan check-out terlebih dahulu.')
    return
  }
  if (new Date(bookingData.value.check_out) <= new Date(bookingData.value.check_in)) {
    alert('Tanggal check-out harus setelah tanggal check-in.')
    return
  }
  currentStep.value = 2
}

const goToStep3 = () => {
  if (!biodataForm.value.nama_pemesan.trim()) {
    alert('Mohon isi nama pemesan sesuai identitas asli.')
    return
  }
  if (!biodataForm.value.email_pemesan.trim()) {
    alert('Mohon isi email aktif untuk pengiriman nota.')
    return
  }
  if (isWajibTransfer.value) {
    selectedPayment.value = 'Transfer Bank'
  }
  currentStep.value = 3
}

const confirmBooking = async () => {
  submitting.value = true
  errorMessage.value = ''

  try {
    const token = localStorage.getItem('auth_token')
    const targetTypeId = bookingData.value.id_tipe_kamar || bookingData.value.id

    const formData = new FormData()
    formData.append('id_tipe_kamar', targetTypeId)
    formData.append('tgl_check_in', bookingData.value.check_in)
    formData.append('tgl_check_out', bookingData.value.check_out)
    formData.append('jumlah_kamar', bookingData.value.jumlah_kamar || 1)
    formData.append('nama_pemesan', biodataForm.value.nama_pemesan)
    formData.append('email_pemesan', biodataForm.value.email_pemesan)
    formData.append('no_hp', biodataForm.value.no_hp || '08123456789')
    formData.append('metode_pembayaran', selectedPayment.value)
    
    const targetStatus = selectedPayment.value === 'Transfer Bank' ? 'verified' : 'pending'
    formData.append('status', targetStatus)

    const response = await $fetch('http://localhost:8000/api/pemesanan', {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' },
      body: formData,
    })

    successReservation.value = response.data || response
    localStorage.removeItem('pending_booking')

  } catch (err) {
    const data = err.data || err.response?._data
    if (data?.message) {
      errorMessage.value = data.message
    } else {
      errorMessage.value = 'Gagal memproses reservasi. Silakan coba lagi.'
    }
    console.error('Error detail:', err)
  } finally {
    submitting.value = false
  }
}

onMounted(() => {
  const raw = localStorage.getItem('pending_booking')
  if (raw) bookingData.value = JSON.parse(raw)

  const savedUser = localStorage.getItem('auth_user')
  if (savedUser) {
    const userData = JSON.parse(savedUser)
    biodataForm.value.nama_pemesan = userData.name || ''
    biodataForm.value.email_pemesan = userData.email || ''
    biodataForm.value.no_hp = userData.no_hp || ''
  }
})
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.4s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
.animate-fade-in { animation: fadeIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
</style>