<template>
  <div class="min-h-screen flex bg-slate-50 relative overflow-hidden text-slate-800 lg:pl-72">
    <div class="absolute top-[20%] left-[50%] w-[800px] h-[800px] bg-pink-100 rounded-full mix-blend-multiply filter blur-[120px] opacity-40 pointer-events-none"></div>

    <AdminSidebar :user="user" />

    <main class="flex-1 p-6 md:p-10 relative z-10 overflow-y-auto">
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-12">
        <div>
          <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight">Tipe Kamar</h1>
          <p class="text-pink-600 mt-2 text-sm font-medium">Kelola kategori, foto, dan target kuota kamar secara eksklusif.</p>
        </div>
        <button @click="openModal()" class="bg-gradient-to-r from-pink-500 to-rose-600 text-white px-8 py-4 rounded-2xl font-black shadow-lg shadow-pink-500/20 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center gap-2 uppercase tracking-widest text-[10px] cursor-pointer">
          <span class="text-xl leading-none">+</span> Tambah Tipe Baru
        </button>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <div v-for="type in roomTypes" :key="type.id" class="bg-white rounded-[2.5rem] shadow-xl shadow-pink-500/5 border border-pink-50 overflow-hidden group hover:shadow-pink-500/10 transition-all duration-500">
          
          <div class="relative h-56 bg-slate-100 overflow-hidden flex items-center justify-center">
            <img 
              v-if="type.foto || type.image" 
              :src="getImageUrl(type.foto || type.image)" 
              class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110 relative z-10" 
              alt="Room Photo"
              @error="(e) => { e.target.style.display = 'none' }"
            >
            
            <div class="absolute inset-0 flex flex-col items-center justify-center text-slate-300 bg-slate-100 z-0">
              <span class="text-4xl">🖼️</span>
              <span class="text-xs font-bold uppercase tracking-wider mt-2 text-slate-400">Preview Kamar</span>
            </div>

            <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-md px-4 py-2 rounded-2xl border border-pink-100 shadow-sm z-20">
              <p class="text-[10px] font-black text-pink-600 uppercase tracking-widest">Rp {{ Number(type.harga || type.price || 0).toLocaleString() }}</p>
            </div>
          </div>
          
          <div class="p-8">
            <h3 class="text-2xl font-black text-slate-900 mb-1 uppercase tracking-tight">{{ type.nama_tipe || type.name }}</h3>
            <div class="flex items-center space-x-2 mb-4">
              <span class="px-2 py-0.5 bg-emerald-50 text-emerald-600 rounded text-[8px] font-black uppercase tracking-widest border border-emerald-100">Active</span>
              <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">/ Limit: {{ type.jumlah_kamar || type.stock }} Unit</span>
            </div>
            <p class="text-sm text-slate-500 line-clamp-2 mb-6 font-medium leading-relaxed">{{ type.deskripsi || type.description }}</p>
            
            <div class="flex items-center justify-between pt-6 border-t border-pink-50">
              <div class="flex space-x-2">
                <button @click="openModal(type)" class="p-3 bg-pink-50 text-pink-600 rounded-xl hover:bg-pink-100 transition-all border border-pink-100 cursor-pointer text-xs font-black uppercase tracking-widest">
                  Edit
                </button>
                <button @click="deleteType(type.id)" class="p-3 bg-rose-50 text-rose-600 rounded-xl hover:bg-rose-100 transition-all border border-rose-100 cursor-pointer text-xs font-black uppercase tracking-widest">
                  Hapus
                </button>
              </div>
              <div class="text-[10px] font-black text-pink-300 uppercase tracking-widest">ID: #00{{ type.id }}</div>
            </div>
          </div>
        </div>
      </div>
    </main>

    <div v-if="isModalOpen" class="fixed inset-0 bg-slate-900/40 backdrop-blur-md flex items-center justify-center z-50 p-4">
      <div class="bg-white p-10 rounded-[2.5rem] shadow-2xl w-full max-w-lg border border-pink-50 overflow-y-auto max-h-[90vh]">
        <h2 class="text-3xl font-black mb-8 text-slate-900 uppercase tracking-tight">{{ isEdit ? 'Edit Tipe Kamar' : 'Tambah Tipe Kamar' }}</h2>
        <form @submit.prevent="saveType" class="space-y-6">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-[10px] font-black text-pink-500 uppercase tracking-widest mb-2">Nama Tipe Kamar</label>
              <input v-model="form.nama_tipe" type="text" class="w-full p-4 bg-slate-50 border rounded-2xl text-sm font-bold focus:outline-none focus:border-pink-500" required>
            </div>
            <div>
              <label class="block text-[10px] font-black text-pink-500 uppercase tracking-widest mb-2">Harga per Malam</label>
              <input v-model="form.harga" type="number" class="w-full p-4 bg-slate-50 border rounded-2xl text-sm font-bold focus:outline-none focus:border-pink-500" required>
            </div>
            <div class="col-span-2">
              <label class="block text-[10px] font-black text-pink-500 uppercase tracking-widest mb-2">Target Stok Kamar</label>
              <input v-model="form.jumlah_kamar" type="number" class="w-full p-4 bg-slate-50 border rounded-2xl text-sm font-bold focus:outline-none focus:border-pink-500" required>
            </div>
          </div>
          <div>
            <label class="block text-[10px] font-black text-pink-500 uppercase tracking-widest mb-2">Upload Foto</label>
            <input type="file" @change="handleFileUpload" accept="image/*" class="w-full p-3 bg-slate-50 border border-dashed rounded-2xl text-sm font-bold">
          </div>
          <div>
            <label class="block text-[10px] font-black text-pink-500 uppercase tracking-widest mb-2">Deskripsi</label>
            <textarea v-model="form.deskripsi" rows="3" class="w-full p-4 bg-slate-50 border rounded-2xl text-sm font-bold focus:outline-none focus:border-pink-500" required></textarea>
          </div>
          <div>
            <label class="block text-[10px] font-black text-pink-500 uppercase tracking-widest mb-2">Fasilitas (Pisahkan dgn koma)</label>
            <input v-model="form.fasilitas" type="text" placeholder="WiFi, AC, TV" class="w-full p-4 bg-slate-50 border rounded-2xl text-sm font-bold focus:outline-none focus:border-pink-500" required>
          </div>
          <div class="flex justify-end gap-4 pt-4">
            <button type="button" @click="isModalOpen = false" class="px-6 py-3.5 text-slate-400 font-bold uppercase tracking-widest text-[10px]">Batal</button>
            <button type="submit" class="px-8 py-3.5 bg-gradient-to-r from-pink-500 to-rose-600 text-white font-black rounded-2xl text-[10px] uppercase tracking-widest">Simpan Data</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import AdminSidebar from '~/components/admin/AdminSidebar.vue'

definePageMeta({ layout: false })

const roomTypes = ref([])
const isModalOpen = ref(false)
const isEdit = ref(false)
const editingId = ref(null)

const user = ref({ name: 'Admin', role: 'admin' })
const form = ref({ nama_tipe: '', harga: '', jumlah_kamar: '', deskripsi: '', fasilitas: '' })
const fileFoto = ref(null)

// ✅ FIX URL PARSING: Pembersihan string url ganda agar link asset lurus ke simlink storage laravel
const getImageUrl = (path) => {
  if (!path) return ''
  if (path.startsWith('http')) return path
  
  // Bersihkan jika string database mengandung kata 'storage/' bawaan seeder lama
  const cleanPath = path.replace('storage/', '')
  return `http://localhost:8000/storage/${cleanPath}`
}

const handleFileUpload = (e) => {
  const file = e.target.files[0]
  if (file) fileFoto.value = file
}

const fetchRoomTypes = async (token) => {
  try {
    const response = await $fetch('http://localhost:8000/api/room-types', {
      headers: { 'Authorization': `Bearer ${token}` }
    })
    roomTypes.value = response.data || response || []
  } catch (err) {
    console.error('Error fetching room types:', err)
  }
}

const openModal = (type = null) => {
  if (type) {
    isEdit.value = true
    editingId.value = type.id
    form.value = { 
      nama_tipe: type.nama_tipe || type.name, 
      harga: type.harga || type.price, 
      jumlah_kamar: type.jumlah_kamar || type.stock, 
      deskripsi: type.deskripsi || type.description, 
      fasilitas: type.fasilitas || type.facilities 
    }
  } else {
    isEdit.value = false
    editingId.value = null
    form.value = { nama_tipe: '', harga: '', jumlah_kamar: '', deskripsi: '', fasilitas: '' }
  }
  fileFoto.value = null
  isModalOpen.value = true
}

const saveType = async () => {
  const token = localStorage.getItem('auth_token')
  try {
    const formData = new FormData()
    formData.append('nama_tipe', form.value.nama_tipe)
    formData.append('harga', form.value.harga)
    formData.append('jumlah_kamar', form.value.jumlah_kamar)
    formData.append('deskripsi', form.value.deskripsi)
    formData.append('fasilitas', form.value.fasilitas)
    if (fileFoto.value) formData.append('foto', fileFoto.value)

    const url = isEdit.value ? `http://localhost:8000/api/room-types/${editingId.value}` : 'http://localhost:8000/api/room-types'
    
    await $fetch(url, {
      method: 'POST', 
      body: formData,
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    })

    alert('Data Tipe Kamar berhasil disimpan!')
    isModalOpen.value = false
    await fetchRoomTypes(token)
  } catch (err) {
    alert(err.data?.message || 'Gagal menyimpan data kategori tipe kamar.')
  }
}

const deleteType = async (id) => {
  if (!confirm('Hapus kategori tipe kamar ini secara permanen?')) return
  const token = localStorage.getItem('auth_token')
  try {
    await $fetch(`http://localhost:8000/api/room-types/${id}`, {
      method: 'DELETE',
      headers: { 'Authorization': `Bearer ${token}` }
    })
    alert('Tipe kamar berhasil dihapus!')
    await fetchRoomTypes(token)
  } catch (err) {
    alert('Gagal menghapus data.')
  }
}

onMounted(() => {
  const localToken = localStorage.getItem('auth_token')
  if (!localToken) {
    window.location.href = '/login'
  } else {
    const savedUser = localStorage.getItem('auth_user')
    if (savedUser) user.value = JSON.parse(savedUser)
    fetchRoomTypes(localToken)
  }
})
</script> 