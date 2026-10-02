<template>
  <div class="min-h-screen flex bg-slate-50 relative overflow-hidden text-slate-800 lg:pl-72">
    <div class="absolute top-[20%] left-[50%] w-[800px] h-[800px] bg-pink-100 rounded-full mix-blend-multiply filter blur-[120px] opacity-40 pointer-events-none"></div>

    <AdminSidebar :user="user" />

    <main class="flex-1 p-8 md:p-12 relative z-10 overflow-y-auto bg-slate-50">
      <div class="max-w-[1400px] mx-auto">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-12">
          <div>
            <h1 class="text-4xl font-black text-slate-900 tracking-tight">Data Pengguna Sistem</h1>
            <p class="text-pink-600 mt-2 text-sm font-medium">Registrasi, kelola otoritas hak akses jabatan staf, dan proteksi akun internal.</p>
          </div>
          <button @click="openModal()" class="bg-slate-900 text-white px-8 py-4 rounded-2xl font-black uppercase tracking-widest text-[10px] hover:bg-pink-600 transition-all cursor-pointer shadow-lg shadow-slate-950/10 active:scale-[0.98]">
            + Tambah Akun Baru
          </button>
        </div>

        <div class="bg-white rounded-3xl shadow-xl border overflow-hidden border-pink-50">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-slate-50 border-b text-[10px] font-black uppercase tracking-widest text-slate-400">
                <th class="px-8 py-5">Nama Pengguna</th>
                <th class="px-8 py-5">Alamat Email</th>
                <th class="px-8 py-5">Otoritas Otoritas (Role)</th>
                <th class="px-8 py-5 text-center">Aksi Operasional</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="u in users" :key="u.id" class="border-b hover:bg-pink-50/20 transition-colors">
                <td class="px-8 py-5">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-pink-100 text-pink-600 font-black flex items-center justify-center text-xs border border-pink-200">
                      {{ u.name ? u.name.charAt(0).toUpperCase() : 'U' }}
                    </div>
                    <p class="font-black text-slate-900 text-sm">{{ u.name }}</p>
                  </div>
                </td>
                <td class="px-8 py-5 text-sm font-bold text-slate-600">{{ u.email }}</td>
                <td class="px-8 py-5">
                  <span class="px-3 py-1 rounded-lg text-[9px] font-black uppercase border"
                        :class="{
                          'text-rose-600 bg-rose-50 border-rose-200': u.role === 'admin',
                          'text-emerald-600 bg-emerald-50 border-emerald-200': u.role === 'receptionist',
                          'text-slate-600 bg-slate-50 border-slate-200': u.role === 'guest' || u.role === 'tamu'
                        }">
                    {{ u.role }}
                  </span>
                </td>
                <td class="px-8 py-5 text-center">
                  <div class="flex justify-center gap-4">
                    <button @click="openModal(u)" class="text-slate-500 text-xs font-black uppercase cursor-pointer hover:text-pink-500 transition-colors">Edit</button>
                    <button @click="deleteUser(u.id)" :disabled="u.id === user.id" class="text-red-500 text-xs font-black uppercase cursor-pointer hover:text-red-700 transition-colors disabled:opacity-30 disabled:cursor-not-allowed">Hapus</button>
                  </div>
                </td>
              </tr>
              <tr v-if="users.length === 0">
                <td colspan="4" class="px-8 py-10 text-center text-slate-400 font-bold italic text-sm">Belum ada akun terdaftar dalam sistem.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </main>

    <div v-if="isModalOpen" class="fixed inset-0 bg-slate-900/40 backdrop-blur-md flex items-center justify-center z-50 p-4">
      <div class="bg-white p-10 rounded-[2.5rem] shadow-2xl w-full max-w-md border border-pink-50">
        <h2 class="text-3xl font-black mb-1 text-slate-900 uppercase tracking-tight">{{ isEdit ? 'Edit Akun Staf' : 'Tambah Akun Baru' }}</h2>
        <p class="text-xs text-pink-500 font-bold uppercase tracking-wider mb-8">{{ isEdit ? 'Perbarui integritas kredensial internal' : 'Daftarkan hak akses operator baru' }}</p>
        
        <form @submit.prevent="saveUser" class="space-y-6">
          <div>
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Nama Lengkap</label>
            <input v-model="form.name" type="text" placeholder="Masukkan nama pengguna..." class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl font-bold text-sm focus:outline-none focus:border-pink-500 transition-all shadow-inner placeholder-slate-300" required>
          </div>
          <div>
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Alamat Email</label>
            <input v-model="form.email" type="email" placeholder="staf@hotel71.com" class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl font-bold text-sm focus:outline-none focus:border-pink-500 transition-all shadow-inner placeholder-slate-300" required>
          </div>
          <div>
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Hak Akses Jabatan (Role)</label>
            <select v-model="form.role" class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl font-bold text-sm focus:outline-none focus:border-pink-500 transition-all shadow-inner" required>
              <option value="admin">Admin (Full Control)</option>
              <option value="receptionist">Receptionist (Front Office)</option>
              <option value="guest">Guest (Tamu Publik)</option>
            </select>
          </div>
          <div>
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">
              Password Keamanan <span v-if="isEdit" class="text-pink-500 font-medium lowercase">(Kosongkan jika tidak diganti)</span>
            </label>
            <input v-model="form.password" type="password" placeholder="••••••••" class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl font-bold text-sm focus:outline-none focus:border-pink-500 transition-all shadow-inner placeholder-slate-300" :required="!isEdit">
          </div>

          <div class="pt-4 flex gap-4">
            <button type="submit" class="flex-1 bg-slate-900 hover:bg-pink-500 text-white py-4 rounded-2xl font-black uppercase text-[10px] tracking-widest transition-colors shadow-md shadow-slate-900/10 cursor-pointer">Simpan Akun</button>
            <button type="button" @click="isModalOpen = false" class="flex-1 border border-slate-200 text-slate-400 py-4 rounded-2xl font-bold uppercase text-[10px] tracking-widest hover:bg-slate-100 transition-colors cursor-pointer">Batal</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import AdminSidebar from '~/components/admin/AdminSidebar.vue'

definePageMeta({ layout: false })

const user = ref({ name: 'Admin', role: 'admin' })
const users = ref([])
const isModalOpen = ref(false)
const isEdit = ref(false)
const editingId = ref(null)

const form = ref({ name: '', email: '', role: 'receptionist', password: '' })
const originalData = ref(null) // ✅ Menyimpan data asli untuk dibandingkan nanti

const fetchUsers = async (token) => {
  try {
    const res = await $fetch('http://localhost:8000/api/users', { headers: { 'Authorization': `Bearer ${token}` } })
    users.value = res.data || res || []
  } catch (err) {
    console.error('Gagal memuat daftar user:', err)
  }
}

const openModal = (targetUser = null) => {
  if (targetUser) {
    isEdit.value = true
    editingId.value = targetUser.id
    form.value = { 
      name: targetUser.name, 
      email: targetUser.email, 
      role: targetUser.role, 
      password: '' 
    }
    // ✅ Simpan data aslinya persis saat modal terbuka
    originalData.value = { ...form.value }
  } else {
    isEdit.value = false
    editingId.value = null
    form.value = { name: '', email: '', role: 'receptionist', password: '' }
    originalData.value = null
  }
  isModalOpen.value = true
}

// ✅ Computed logic untuk mendeteksi apakah form telah diotak-atik admin
const isFormModified = computed(() => {
  if (!isEdit.value) return true // Kalau tambah baru, berarti pasti ada perubahan
  if (!originalData.value) return true

  return (
    form.value.name !== originalData.value.name ||
    form.value.email !== originalData.value.email ||
    form.value.role !== originalData.value.role ||
    form.value.password.trim() !== '' // Kalau ngetik password baru, dihitung berubah
  )
})

const saveUser = async () => {
  // ✅ LOGIKANYA DI SINI BRO: Cegat kalau ga ada yang diubah
  if (isEdit.value && !isFormModified.value) {
    alert('Informasi: Data ini tidak ada yang diubah.')
    isModalOpen.value = false
    return // Berhenti di sini, gak usah nembak API backend sama sekali
  }

  const token = localStorage.getItem('auth_token')
  try {
    const url = isEdit.value ? `http://localhost:8000/api/users/${editingId.value}` : 'http://localhost:8000/api/users'
    
    // SAFE GUARD FIX: Hapus key password jika kosong pas edit agar tidak merusak enkripsi Laravel hash
    const payload = { ...form.value }
    if (isEdit.value && !payload.password.trim()) {
      delete payload.password
    }

    await $fetch(url, {
      method: isEdit.value ? 'PUT' : 'POST',
      body: payload,
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    })
    
    alert('Data akun berhasil disimpan dengan sukses!')
    isModalOpen.value = false
    await fetchUsers(token)
  } catch (err) {
    alert(err.response?._data?.message || 'Gagal menyimpan data akun baru/edit.')
  }
}

const deleteUser = async (id) => {
  if (id === user.value.id) {
    alert('🚨 PROTEKSI OTOMATIS: Anda dilarang keras menghapus akun administrator Anda sendiri yang sedang aktif digunakan login!')
    return
  }
  
  if (!confirm('Apakah Anda yakin ingin menghapus hak akses akun pengguna sistem ini?')) return
  const token = localStorage.getItem('auth_token')
  try {
    await $fetch(`http://localhost:8000/api/users/${id}`, {
      method: 'DELETE',
      headers: { 'Authorization': `Bearer ${token}` }
    })
    alert('Akun berhasil dihapus dari sistem hotel.')
    await fetchUsers(token)
  } catch (err) {
    alert('Gagal menghapus akun.')
  }
}

onMounted(() => {
  const localToken = localStorage.getItem('auth_token')
  if (!localToken) {
    window.location.href = '/login'
  } else {
    const savedUser = localStorage.getItem('auth_user')
    if (savedUser) user.value = JSON.parse(savedUser)
    fetchUsers(localToken)
  }
})
</script>