<template>
  <!-- Menambahkan md:pl-64 lg:pl-72 agar bergeser presisi di kanan sidebar -->
  <div class="min-h-screen bg-slate-50 text-slate-900 selection:bg-pink-500/30 font-sans overflow-x-hidden md:pl-64 lg:pl-72 transition-all duration-300">
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
      <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] bg-pink-100 rounded-full blur-[120px] opacity-60"></div>
      <div class="absolute bottom-0 right-[10%] w-[30%] h-[30%] bg-rose-50 rounded-full blur-[100px] opacity-40"></div>
    </div>

    <GuestNavbar activePage="profile" :userName="user?.name" @logout="handleLogout" />

    <main class="relative z-10 max-w-7xl mx-auto pt-10 md:pt-14 pb-24 px-6 md:px-12">
      <header class="mb-12">
        <h1 class="text-4xl font-black text-slate-900 tracking-tight uppercase">Akun & Riwayat Anda</h1>
        <p class="text-slate-500 mt-2 text-sm font-medium">Menej data personal serta upload berkas bukti transaksi penginapan Anda.</p>
      </header>

      <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
        <div class="lg:col-span-4 bg-white p-8 rounded-[3rem] border border-pink-50 shadow-xl text-center">
          <div class="w-20 h-20 bg-gradient-to-tr from-pink-500 to-rose-600 rounded-[2rem] flex items-center justify-center text-white text-3xl font-black mx-auto mb-6 shadow-lg shadow-pink-500/20">
            {{ user?.name ? user.name.charAt(0).toUpperCase() : 'U' }}
          </div>
          <h3 class="text-2xl font-black text-slate-900 tracking-tight uppercase">{{ user?.name }}</h3>
          <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1 mb-8">{{ user?.email }}</p>

          <form @submit.prevent="handleUpdateProfile" class="space-y-4 text-left border-t pt-6">
            <div>
              <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-2">Nama Pengguna</label>
              <input v-model="profileForm.name" type="text" class="w-full px-5 py-3.5 bg-slate-50 border rounded-2xl text-xs font-bold outline-none focus:ring-2 focus:ring-pink-500 transition-all" required>
            </div>
            <div>
              <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-2">Alamat Email</label>
              <input v-model="profileForm.email" type="email" class="w-full px-5 py-3.5 bg-slate-50 border rounded-2xl text-xs font-bold outline-none focus:ring-2 focus:ring-pink-500 transition-all" required>
            </div>
            <button type="submit" :disabled="updating" class="w-full py-4 bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-pink-600 transition-all shadow-md cursor-pointer">
              {{ updating ? 'Memperbarui...' : 'Simpan Profil ✨' }}
            </button>
          </form>
        </div>

        <div class="lg:col-span-8 space-y-8">
          <div class="flex justify-between items-center">
            <h3 class="text-xl font-black uppercase tracking-tight text-slate-900">Log Pemesanan Saya</h3>
            <span class="px-4 py-1.5 bg-pink-50 text-pink-600 text-[10px] font-black rounded-full border border-pink-100 uppercase tracking-widest">
              {{ reservations.length }} Riwayat
            </span>
          </div>

          <div class="space-y-6">
            <div v-for="res in reservations" :key="res.id_pemesanan" class="bg-white p-8 rounded-[3rem] border border-pink-50 shadow-xl relative overflow-hidden flex flex-col md:flex-row justify-between gap-6 items-start md:items-center">
              <div>
                <span class="px-3 py-1 bg-slate-100 border text-slate-500 text-[9px] font-black uppercase rounded-lg tracking-wider mb-3 inline-block">
                  {{ res.nomor_pemesanan }}
                </span>
                <h4 class="text-xl font-black text-slate-900 uppercase tracking-tight">{{ res.room_type?.name || 'Tipe Kamar' }}</h4>
                <p class="text-xs font-bold text-slate-400 mt-1">Inap: {{ res.tgl_check_in }} s/d {{ res.tgl_check_out }}</p>
                <p class="text-sm font-black text-pink-600 mt-3">Total Tagihan: Rp {{ Number(res.total_harga || 0).toLocaleString() }}</p>
              </div>

              <div class="w-full md:w-auto flex flex-col gap-3 items-end">
                <span class="text-[10px] font-black uppercase tracking-widest px-4 py-2 rounded-xl border"
                  :class="res.status_pemesanan === 'Baru' || res.status_pemesanan === 'pending' ? 'bg-amber-50 text-amber-600 border-amber-100' : 'bg-emerald-50 text-emerald-600 border-emerald-100'">
                  {{ res.status_pemesanan }}
                </span>

                <div v-if="(res.status_pemesanan === 'Baru' || res.status_pemesanan === 'pending') && res.metode_pembayaran === 'Transfer Bank'" class="w-full md:w-56 mt-2">
                  <label class="block text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1 text-right">Upload Bukti Struk Transfer</label>
                  <input type="file" @change="(e) => handleUploadProof(e, res.id_pemesanan)" accept="image/*" class="w-full text-[10px] bg-slate-50 p-2 border rounded-xl font-bold">
                </div>
              </div>
            </div>

            <div v-if="reservations.length === 0" class="text-center py-20 bg-white border border-dashed rounded-[3rem]">
              <p class="text-sm text-slate-400 font-bold uppercase tracking-widest">Belum ada riwayat booking dibuat</p>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

definePageMeta({ layout: false })

const user = ref({ name: 'Guest', email: '' })
const reservations = ref([])
const updating = ref(false)
const profileForm = ref({ name: '', email: '' })

const fetchProfileAndBookings = async (token) => {
  try {
    const resUser = await $fetch('http://localhost:8000/api/user', {
      headers: { 'Authorization': `Bearer ${token}` }
    })
    user.value = resUser.data || resUser
    profileForm.value.name = user.value.name
    profileForm.value.email = user.value.email

    const resBookings = await $fetch('http://localhost:8000/api/pemesanan/saya', {
      headers: { 'Authorization': `Bearer ${token}` }
    })
    reservations.value = resBookings.data || resBookings || []
  } catch (err) {
    console.error(err)
    localStorage.removeItem('auth_token')
    window.location.href = '/login'
  }
}

const handleUpdateProfile = async () => {
  const token = localStorage.getItem('auth_token')
  updating.value = true
  try {
    await $fetch('http://localhost:8000/api/profile/update', {
      method: 'PUT',
      headers: { 'Authorization': `Bearer ${token}` },
      body: { name: profileForm.value.name, email: profileForm.value.email }
    })
    alert('Profil sukses diperbarui!')
    user.value.name = profileForm.value.name
    user.value.email = profileForm.value.email
    localStorage.setItem('auth_user', JSON.stringify(user.value))
  } catch (err) {
    alert('Gagal memperbarui profil.')
  } finally {
    updating.value = false
  }
}

const handleUploadProof = async (e, id) => {
  const file = e.target.files[0]
  if (!file) return
  
  const token = localStorage.getItem('auth_token')
  const formData = new FormData()
  formData.append('bukti_pembayaran', file)

  try {
    await $fetch(`http://localhost:8000/api/pemesanan/${id}/upload-bukti`, {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${token}` },
      body: formData
    })
    alert('Bukti pembayaran berhasil diunggah!')
    fetchProfileAndBookings(token)
  } catch (err) {
    alert('Gagal mengunggah berkas bukti.')
  }
}

const handleLogout = () => {
  localStorage.removeItem('auth_token')
  localStorage.removeItem('auth_user')
  window.location.href = '/login'
}

onMounted(() => {
  const localToken = localStorage.getItem('auth_token')
  if (!localToken) {
    window.location.href = '/login'
  } else {
    fetchProfileAndBookings(localToken)
  }
})
</script>

<style scoped>
div { transition: all 0.3s ease; }
</style>