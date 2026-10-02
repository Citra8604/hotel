<template>
  <div class="min-h-screen flex bg-slate-50 relative overflow-hidden text-slate-800 lg:pl-72">
    <AdminSidebar :user="user" />

    <main class="flex-1 p-8 md:p-12 relative z-10 overflow-y-auto bg-slate-50">
      <div class="max-w-[1400px] mx-auto">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-12">
          <div>
            <h1 class="text-4xl font-black text-slate-900 tracking-tight">Data Kamar Fisik</h1>
            <p class="text-slate-500 mt-2 text-sm font-medium">Monitoring nomor ruangan fisik berdasarkan tipe target.</p>
          </div>
          <button @click="openModal()" class="bg-slate-900 text-white px-8 py-4 rounded-2xl font-bold uppercase tracking-widest text-[10px] hover:bg-pink-600 transition-colors cursor-pointer">
            + Tambah Kamar
          </button>
        </div>

        <div class="bg-white rounded-3xl shadow-xl border overflow-hidden">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-slate-50 border-b text-[10px] font-black uppercase tracking-widest text-slate-400">
                <th class="px-8 py-5">Nomor Kamar</th>
                <th class="px-8 py-5">Tipe Kamar</th>
                <th class="px-8 py-5">Status</th>
                <th class="px-8 py-5 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="room in rooms" :key="room.id" class="border-b hover:bg-slate-50">
                <td class="px-8 py-5 text-lg font-black text-slate-900">{{ room.room_number || room.nomor_kamar }}</td>
                
                <td class="px-8 py-5 text-sm font-bold text-amber-700">
                  {{ room.room_type?.name || room.roomType?.name || room.tipe_kamar?.nama_tipe || 'Tipe Kamar Dihapus' }}
                </td>
                
                <td class="px-8 py-5">
                  <span class="px-3 py-1 rounded-lg text-[9px] font-black uppercase border"
                        :class="{
                          'text-emerald-600 bg-emerald-50 border-emerald-200': room.status === 'Tersedia' || room.status === 'available',
                          'text-amber-600 bg-amber-50 border-amber-200': room.status === 'Terisi' || room.status === 'occupied',
                          'text-rose-600 bg-rose-50 border-rose-200': room.status === 'Kotor' || room.status === 'dirty',
                          'text-blue-600 bg-blue-50 border-blue-200': room.status === 'Perbaikan' || room.status === 'maintenance'
                        }">
                    {{ room.status === 'available' ? 'Tersedia' : room.status === 'occupied' ? 'Terisi' : room.status === 'dirty' ? 'Kotor' : room.status === 'maintenance' ? 'Perbaikan' : room.status }}
                  </span>
                </td>
                <td class="px-8 py-5 text-right">
                  <div class="flex justify-end gap-4">
                    <button @click="openModal(room)" class="text-slate-500 text-xs font-black uppercase cursor-pointer hover:text-pink-500 transition-colors">Edit</button>
                    <button @click="deleteRoom(room.id)" class="text-red-500 text-xs font-black uppercase cursor-pointer hover:text-red-700 transition-colors">Hapus</button>
                  </div>
                </td>
              </tr>
              <tr v-if="rooms.length === 0">
                <td colspan="4" class="px-8 py-10 text-center text-slate-400 font-bold italic text-sm">Belum ada unit kamar terdaftar.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </main>

    <div v-if="isModalOpen" class="fixed inset-0 bg-slate-900/40 backdrop-blur-md flex items-center justify-center z-50 p-4">
      <div class="bg-white p-10 rounded-[2.5rem] shadow-2xl w-full max-w-md border">
        <h2 class="text-3xl font-black mb-8 text-slate-900 uppercase">{{ isEdit ? 'Edit Kamar' : 'Registrasi Kamar' }}</h2>
        <form @submit.prevent="saveRoom" class="space-y-6">
          <div>
            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Nomor Kamar</label>
            <input v-model="form.room_number" type="text" placeholder="Contoh: 101, 204" class="w-full p-4 bg-slate-50 border rounded-2xl font-bold focus:outline-none focus:border-pink-500" required>
          </div>
          <div>
            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Tipe Kamar</label>
            <select v-model="form.room_type_id" class="w-full p-4 bg-slate-50 border rounded-2xl font-bold focus:outline-none focus:border-pink-500" required>
              <option value="" disabled>-- Pilih Kategori --</option>
              <option v-for="type in roomTypes" :key="type.id" :value="type.id">{{ type.nama_tipe || type.name }}</option>
            </select>
          </div>
          <div>
            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Status</label>
            <select v-model="form.status" class="w-full p-4 bg-slate-50 border rounded-2xl font-bold focus:outline-none focus:border-pink-500" required>
              <option value="Tersedia">Tersedia</option>
              <option value="Terisi">Terisi</option>
              <option value="Kotor">Kotor</option>
              <option value="Perbaikan">Perbaikan</option>
            </select>
          </div>
          <div class="flex justify-end gap-4 pt-4">
            <button type="button" @click="isModalOpen = false" class="px-6 py-3.5 text-slate-400 font-bold uppercase text-[10px] tracking-widest cursor-pointer hover:bg-slate-50 rounded-2xl transition-colors">Batal</button>
            <button type="submit" class="px-8 py-3.5 bg-slate-900 hover:bg-pink-500 text-white font-black rounded-2xl text-[10px] uppercase tracking-widest transition-colors cursor-pointer">Simpan Kamar</button>
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

const user = ref({ name: 'Admin', role: 'admin' })
const rooms = ref([])
const roomTypes = ref([])
const isModalOpen = ref(false)
const isEdit = ref(false)
const editingId = ref(null)

const form = ref({ room_type_id: '', room_number: '', status: 'Tersedia' })

const fetchRooms = async (token) => {
  try {
    const response = await $fetch('http://localhost:8000/api/rooms', { headers: { 'Authorization': `Bearer ${token}` } })
    rooms.value = response.data || response || []
  } catch (err) {
    console.error('Gagal memuat data kamar:', err)
  }
}

const fetchRoomTypes = async (token) => {
  try {
    const response = await $fetch('http://localhost:8000/api/room-types', { headers: { 'Authorization': `Bearer ${token}` } })
    roomTypes.value = response.data || response || []
  } catch (err) {
    console.error('Gagal memuat tipe kamar:', err)
  }
}

const openModal = (room = null) => {
  if (room) {
    isEdit.value = true
    editingId.value = room.id
    form.value = { 
      room_type_id: room.id_tipe_kamar || room.room_type_id, 
      room_number: room.nomor_kamar || room.room_number, 
      status: room.status === 'available' ? 'Tersedia' : room.status === 'occupied' ? 'Terisi' : room.status === 'dirty' ? 'Kotor' : room.status === 'maintenance' ? 'Perbaikan' : room.status
    }
  } else {
    isEdit.value = false
    editingId.value = null
    form.value = { room_type_id: '', room_number: '', status: 'Tersedia' }
  }
  isModalOpen.value = true
}

const saveRoom = async () => {
  const token = localStorage.getItem('auth_token')
  try {
    const url = isEdit.value ? `http://localhost:8000/api/rooms/${editingId.value}` : 'http://localhost:8000/api/rooms'
    
    await $fetch(url, {
      method: isEdit.value ? 'PUT' : 'POST',
      body: {
        room_number: form.value.room_number,
        room_type_id: form.value.room_type_id,
        status: form.value.status
      },
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    })
    
    alert('Kamar fisik berhasil disimpan!')
    isModalOpen.value = false
    await fetchRooms(token)
  } catch (err) {
    // Menampilkan pesan error khusus dari validasi backend
    alert(err.data?.message || err.response?._data?.message || 'Gagal menyimpan data kamar.')
  }
}

const deleteRoom = async (id) => {
  if (!confirm('Apakah anda yakin ingin menghapus unit kamar fisik ini?')) return
  const token = localStorage.getItem('auth_token')
  try {
    await $fetch(`http://localhost:8000/api/rooms/${id}`, {
      method: 'DELETE',
      headers: { 'Authorization': `Bearer ${token}` }
    })
    alert('Kamar berhasil dihapus!')
    await fetchRooms(token)
  } catch (err) {
    alert('Gagal menghapus kamar.')
  }
}

onMounted(() => {
  const localToken = localStorage.getItem('auth_token')
  if (!localToken) {
    window.location.href = '/login'
  } else {
    const savedUser = localStorage.getItem('auth_user')
    if (savedUser) user.value = JSON.parse(savedUser)
    fetchRooms(localToken)
    fetchRoomTypes(localToken)
  }
})
</script>