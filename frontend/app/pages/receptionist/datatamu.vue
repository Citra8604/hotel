<template>
  <div class="min-h-screen flex bg-slate-50 text-slate-900 selection:bg-pink-500/30 font-sans overflow-x-hidden lg:pl-72">
    
    <ReceptionistSidebar :user="user" />

    <main class="flex-1 min-w-0 p-8 md:p-12 relative z-10 overflow-y-auto bg-slate-50">
      <div class="max-w-[1400px] mx-auto">
        <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-12 mt-4">
          <div>
            <h1 class="text-4xl font-black text-slate-900 tracking-tight">Data Tamu</h1>
            <p class="text-slate-500 mt-2 text-sm font-medium">Manajemen data profil dan interaksi akun tamu hotel.</p>
          </div>
          <div class="relative w-full md:w-auto flex flex-col md:flex-row gap-4">
            <div class="relative w-full md:w-80">
              <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-lg">🔍</span>
              <input v-model="searchQuery" type="text" placeholder="Cari nama atau email..." class="w-full pl-11 pr-4 py-3.5 bg-white border border-slate-200 rounded-2xl focus:ring-2 focus:ring-pink-500 outline-none text-sm transition-all shadow-sm">
            </div>
          </div>
        </header> 

        <section class="bg-white rounded-3xl shadow-xl border border-pink-50 overflow-hidden">
          <div class="p-8 border-b border-pink-50 bg-slate-50/50 flex justify-between items-center">
            <h3 class="text-[11px] font-black text-slate-600 uppercase tracking-[0.2em]">Daftar Member Terdaftar</h3>
            <span class="px-4 py-1.5 bg-pink-50 text-pink-600 text-[10px] font-black rounded-full uppercase tracking-widest border border-pink-100">
              {{ filteredGuests.length }} Total Tamu
            </span>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full text-left">
              <thead>
                <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-black text-slate-400 uppercase tracking-widest">
                  <th class="px-8 py-5">Identitas Tamu</th>
                  <th class="px-8 py-5">Alamat Email</th>
                  <th class="px-8 py-5">Kontak / No. Telp</th>
                  <th class="px-8 py-5 text-center">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-pink-50">
                <tr v-for="guest in filteredGuests" :key="guest.id" class="hover:bg-pink-50/10 transition-colors">
                  <td class="px-8 py-5">
                    <div class="flex items-center gap-4">
                      <div class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center font-black text-xs shadow-lg shadow-slate-900/10">
                        {{ guest.name ? guest.name.charAt(0).toUpperCase() : 'G' }}
                      </div>
                      <span class="text-sm font-bold text-slate-900">{{ guest.name }}</span>
                    </div>
                  </td>
                  <td class="px-8 py-5">
                    <span class="text-xs font-bold text-slate-600">{{ guest.email }}</span>
                  </td>
                  <td class="px-8 py-5">
                    <div class="flex items-center gap-2">
                      <span class="text-xs font-bold text-slate-700 tracking-tight">{{ guest.no_hp || guest.no_telp || '-' }}</span>
                      <span class="text-[8px] bg-emerald-50 text-emerald-600 px-2 py-0.5 rounded font-black uppercase border border-emerald-100">Verified</span>
                    </div>
                  </td>
                  <td class="px-8 py-5">
                    <div class="flex items-center justify-center gap-2">
                      <button @click="deleteGuest(guest.id)" class="text-[9px] font-black uppercase tracking-widest px-3 py-1.5 bg-rose-50 text-rose-500 rounded-lg hover:bg-rose-500 hover:text-white transition-all border border-rose-100 cursor-pointer">HAPUS</button>
                    </div>
                  </td>
                </tr>
                <tr v-if="filteredGuests.length === 0">
                  <td colspan="4" class="px-8 py-24 text-center">
                    <div class="flex flex-col items-center opacity-30 grayscale">
                      <span class="text-4xl mb-4">👥</span>
                      <p class="text-sm font-black uppercase tracking-widest">Tidak ada data tamu</p>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table> 
          </div>
        </section>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import ReceptionistSidebar from '~/components/receptionist/ReceptionistSidebar.vue'

definePageMeta({ layout: false })

const user = ref({ name: 'Resepsionis', role: 'resepsionis' })
const guests = ref([])
const searchQuery = ref('')

const fetchGuests = async () => {
  const token = localStorage.getItem('auth_token')
  try {
    const data = await $fetch('http://localhost:8000/api/users', { 
      headers: { 'Authorization': `Bearer ${token}` } 
    })
    const listAll = data.data || data || []
    
    // ✅ FIX KEYWORD FILTER: Menyesuaikan penamaan role tabel database asli yaitu 'guest'
    guests.value = listAll.filter(u => String(u.role).toLowerCase() === 'guest' || String(u.role).toLowerCase() === 'tamu')
  } catch (err) { 
    console.error(err) 
  }
}

const deleteGuest = async (id) => {
  if (!confirm('Apakah anda yakin ingin menghapus berkas akun hak akses tamu publik ini?')) return
  const token = localStorage.getItem('auth_token')
  try {
    await $fetch(`http://localhost:8000/api/users/${id}`, { 
      method: 'DELETE', 
      headers: { 'Authorization': `Bearer ${token}` } 
    })
    alert('Akun tamu berhasil dihapus!')
    await fetchGuests()
  } catch (err) { 
    alert('Gagal menghapus data.') 
  }
}

const filteredGuests = computed(() => {
  if (!guests.value) return []
  const query = searchQuery.value.toLowerCase().trim()
  if (!query) return guests.value
  return guests.value.filter(g => 
    (g.name && g.name.toLowerCase().includes(query)) || 
    (g.email && g.email.toLowerCase().includes(query))
  )
})

onMounted(() => {
  // ✅ FIX PENTAL SECURITY LINK: Murni membaca state localStorage token agar tidak ter-kick cookies kosong
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
    fetchGuests()
  }
})
</script>