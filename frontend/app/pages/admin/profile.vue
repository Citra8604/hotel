<template>
  <div class="min-h-screen flex bg-slate-50 text-slate-900 font-sans overflow-x-hidden lg:pl-72">
    <!-- Sidebar Resepsionis -->
    <ReceptionistSidebar :user="user" />

    <main class="flex-1 p-6 md:p-10 relative z-10 overflow-y-auto">
      <header class="mb-10">
        <h1 class="text-4xl font-black text-slate-900 tracking-tight">Profil Saya</h1>
        <p class="text-pink-600 mt-2 text-sm font-medium">Ubah kredensial masuk dan data diri Anda di sini.</p>
      </header>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mt-10">
        <!-- Card Preview Profil -->
        <div class="bg-white border border-slate-100 p-8 rounded-3xl text-center h-fit shadow-sm">
          <div class="w-24 h-24 bg-pink-100 rounded-full flex items-center justify-center mx-auto mb-4 text-pink-600 text-3xl font-bold shadow-inner">
            {{ avatarLetter }}
          </div>
          <h3 class="text-xl font-bold text-slate-900 truncate">{{ profileForm.name || 'Staf Resepsionis' }}</h3>
          <p class="text-sm text-slate-500 mb-4 truncate">{{ profileForm.email || 'resepsionis@hotel.com' }}</p>
          <span class="bg-slate-900 text-white px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-widest inline-block">
            {{ userRole }}
          </span>
        </div>

        <!-- Form Edit Profil -->
        <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm lg:col-span-2">
          <h3 class="text-xl font-bold text-slate-900 mb-6">Ubah Profil Staf</h3>
          <form @submit.prevent="updateProfile" class="space-y-6">
            <div>
              <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Nama Lengkap</label>
              <input 
                v-model="profileForm.name" 
                type="text" 
                class="w-full p-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold focus:outline-none focus:ring-2 focus:ring-slate-900" 
                required 
              />
            </div>
            <div>
              <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Alamat Email</label>
              <input 
                v-model="profileForm.email" 
                type="email" 
                class="w-full p-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold focus:outline-none focus:ring-2 focus:ring-slate-900" 
                required 
              />
            </div>
            <button 
              type="submit" 
              class="w-full py-4 bg-slate-900 text-white font-black rounded-2xl uppercase tracking-widest text-[11px] hover:bg-slate-800 transition-colors shadow-lg shadow-slate-900/10 cursor-pointer"
            >
              Simpan Perubahan
            </button>
          </form>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import ReceptionistSidebar from '~/components/receptionist/ReceptionistSidebar.vue'

definePageMeta({ layout: false })

const userRole = useCookie('user_role', { path: '/' })
const userName = useCookie('user_name', { path: '/' })
const userEmail = useCookie('user_email', { path: '/' })

const user = ref({ 
  name: userName.value || 'Receptionist', 
  role: userRole.value || 'receptionist' 
})

const profileForm = ref({ 
  name: userName.value || 'Receptionist', 
  email: userEmail.value || 'resepsionis@hotel.com' 
})

const avatarLetter = computed(() => {
  return profileForm.value.name && profileForm.value.name.trim() !== ''
    ? profileForm.value.name.trim().charAt(0).toUpperCase()
    : 'R'
})

const updateProfile = async () => {
  userName.value = profileForm.value.name
  userEmail.value = profileForm.value.email
  user.value.name = profileForm.value.name
  alert('Profil berhasil diperbarui!')
}

onMounted(() => {
  if (process.client) {
    const savedUser = localStorage.getItem('auth_user')
    if (savedUser) {
      try {
        const parsed = JSON.parse(savedUser)
        user.value = parsed
        profileForm.value.name = parsed.name || profileForm.value.name
        profileForm.value.email = parsed.email || profileForm.value.email
      } catch (e) {}
    }
  }
})
</script>