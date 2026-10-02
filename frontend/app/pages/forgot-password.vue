<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-pink-50 via-white to-rose-50 relative overflow-hidden">
    <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-pink-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
    <div class="absolute top-[20%] right-[-10%] w-96 h-96 bg-rose-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000"></div>
    <div class="absolute bottom-[-20%] left-[20%] w-96 h-96 bg-fuchsia-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-4000"></div>

    <div class="relative z-10 w-full max-w-md p-8 sm:p-10 backdrop-blur-xl bg-white/70 border border-white shadow-2xl rounded-3xl text-slate-800">
      <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gradient-to-tr from-pink-500 to-rose-500 mb-4 shadow-lg shadow-pink-500/30">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
          </svg>
        </div>
        <h1 class="text-3xl font-extrabold tracking-tight mb-2 text-slate-900">{{ stepTitle }}</h1>
        <p class="text-pink-600 text-sm font-medium">{{ stepDescription }}</p>
      </div>

      <div class="flex items-center justify-between mb-8 px-2">
        <div v-for="i in 4" :key="i" class="flex items-center flex-1 last:flex-none">
          <div :class="['w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm transition-all duration-300', step >= i ? 'bg-pink-500 text-white shadow-lg shadow-pink-500/30' : 'bg-pink-100 text-pink-300']">
            {{ i }}
          </div>
          <div v-if="i < 4" :class="['h-1 flex-1 mx-2 transition-all duration-300', step > i ? 'bg-pink-500' : 'bg-pink-100']"></div>
        </div>
      </div>

      <div v-if="errorMessage" class="mb-6 p-3 bg-red-50 border border-red-100 rounded-xl text-red-600 text-sm flex items-center gap-2 animate-shake">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
        </svg>
        {{ errorMessage }}
      </div>

      <div v-if="step === 1" class="space-y-6">
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Email Address</label>
          <input 
            v-model="form.email" 
            type="email" 
            placeholder="nama@email.com"
            class="w-full px-4 py-3 bg-white border border-pink-100 rounded-xl focus:ring-2 focus:ring-pink-400 focus:border-transparent outline-none transition-all text-slate-900 placeholder-slate-400 shadow-sm"
            required
            @keyup.enter="handleCheckEmail"
          >
        </div>
        <button @click="handleCheckEmail" :disabled="loading" class="w-full bg-gradient-to-r from-pink-500 to-rose-600 text-white py-3 px-4 rounded-xl font-bold shadow-lg shadow-pink-500/30 hover:shadow-pink-500/50 hover:scale-[1.02] active:scale-[0.98] transition-all disabled:opacity-70 disabled:cursor-not-allowed flex items-center justify-center">
          <span v-if="!loading">Lanjut ke WhatsApp</span>
          <svg v-else class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
        </button>
      </div>

      <div v-else-if="step === 2" class="space-y-6">
        <div class="p-4 bg-pink-50 rounded-xl border border-pink-100 text-center">
          <p class="text-xs text-pink-400 uppercase tracking-wider mb-1 font-bold">Nomor Terhubung</p>
          <p class="text-xl font-bold text-slate-900">{{ maskedPhone }}</p>
        </div>
        <button @click="handleSendOtp" :disabled="loading" class="w-full bg-gradient-to-r from-pink-500 to-rose-600 text-white py-3 px-4 rounded-xl font-bold shadow-lg shadow-pink-500/30 hover:shadow-pink-500/50 hover:scale-[1.02] active:scale-[0.98] transition-all disabled:opacity-70 disabled:cursor-not-allowed flex items-center justify-center">
          <span v-if="!loading">Kirim Kode OTP</span>
          <svg v-else class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
        </button>
        <button @click="step = 1" class="w-full text-sm text-pink-600 hover:text-pink-700 transition-colors font-medium cursor-pointer">Batalkan / Ganti Email</button>
      </div>

      <div v-else-if="step === 3" class="space-y-6">
        <div class="flex justify-between gap-2">
          <input 
            v-for="(digit, index) in 6" 
            :key="index"
            ref="otpInputs"
            v-model="otpDigits[index]"
            type="text" 
            maxlength="1"
            class="w-12 h-14 text-center text-2xl font-bold bg-white border border-pink-100 rounded-xl focus:ring-2 focus:ring-pink-400 outline-none text-slate-900 shadow-sm transition-all"
            @input="handleOtpInput($event, index)"
            @keydown.delete="handleOtpDelete($event, index)"
          >
        </div>
        <button @click="handleVerifyOtp" :disabled="loading" class="w-full bg-gradient-to-r from-pink-500 to-rose-600 text-white py-3 px-4 rounded-xl font-bold shadow-lg shadow-pink-500/30 hover:shadow-pink-500/50 hover:scale-[1.02] active:scale-[0.98] transition-all disabled:opacity-70 disabled:cursor-not-allowed flex items-center justify-center">
          <span v-if="!loading">Verifikasi Kode</span>
          <svg v-else class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
        </button>
        <div class="text-center">
          <button @click="handleSendOtp" class="text-sm text-pink-600 hover:text-pink-700 transition-colors font-medium cursor-pointer">Kirim Ulang Kode WA</button>
        </div>
      </div>

      <div v-else-if="step === 4" class="space-y-6">
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Password Baru</label>
            <input v-model="form.password" type="password" placeholder="••••••••" class="w-full px-4 py-3 bg-white border border-pink-100 rounded-xl focus:ring-2 focus:ring-pink-400 focus:border-transparent outline-none transition-all text-slate-900 placeholder-slate-400 shadow-sm" required>
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Konfirmasi Password</label>
            <input v-model="form.password_confirmation" type="password" placeholder="••••••••" class="w-full px-4 py-3 bg-white border border-pink-100 rounded-xl focus:ring-2 focus:ring-pink-400 focus:border-transparent outline-none transition-all text-slate-900 placeholder-slate-400 shadow-sm" required @keyup.enter="handleResetPassword">
          </div>
        </div>
        <button @click="handleResetPassword" :disabled="loading" class="w-full bg-gradient-to-r from-pink-500 to-rose-600 text-white py-3 px-4 rounded-xl font-bold shadow-lg shadow-pink-500/30 hover:shadow-pink-500/50 hover:scale-[1.02] active:scale-[0.98] transition-all disabled:opacity-70 disabled:cursor-not-allowed flex items-center justify-center">
          <span v-if="!loading">Update Password</span>
          <svg v-else class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
        </button>
      </div>

      <div class="text-center mt-8">
        <NuxtLink to="/login" class="text-sm font-bold text-pink-600 hover:text-pink-700 transition-colors">Kembali ke Login</NuxtLink>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const step = ref(1)
const loading = ref(false)
const errorMessage = ref('')
const maskedPhone = ref('')
const otpDigits = ref(['', '', '', '', '', ''])
const otpInputs = ref([])
const verifyToken = ref('')

const form = ref({
  email: '',
  password: '',
  password_confirmation: ''
})

const stepTitle = computed(() => {
  if (step.value === 1) return 'Verifikasi Email'
  if (step.value === 2) return 'Konfirmasi WhatsApp'
  if (step.value === 3) return 'Verifikasi OTP'
  return 'Reset Password'
})

const stepDescription = computed(() => {
  if (step.value === 1) return 'Langkah awal untuk memulihkan akun Anda.'
  if (step.value === 2) return 'Kami akan mengirimkan kode keamanan.'
  if (step.value === 3) return 'Masukkan 6 digit kode yang Anda terima di WA.'
  return 'Buatlah password baru yang aman.'
})

// ✅ FIX: Seluruh alur step mengandalkan EMAIL murni (bukan phone) agar spesifik menarget 1 akun
const handleCheckEmail = async () => {
  if (!form.value.email) return
  loading.value = true
  errorMessage.value = ''
  try {
    const res = await $fetch('http://localhost:8000/api/forgot-password/check-email', {
      method: 'POST',
      body: { email: form.value.email },
      headers: { 'Accept': 'application/json' }
    })
    maskedPhone.value = res.phone
    step.value = 2
  } catch (err) {
    errorMessage.value = err.data?.message || err.response?._data?.message || 'Email tidak terdaftar dalam sistem.'
  } finally {
    loading.value = false
  }
}

const handleSendOtp = async () => {
  loading.value = true
  errorMessage.value = ''
  try {
    await $fetch('http://localhost:8000/api/forgot-password', {
      method: 'POST',
      body: { email: form.value.email },
      headers: { 'Accept': 'application/json' }
    })
    step.value = 3
  } catch (err) {
    errorMessage.value = err.data?.message || err.response?._data?.message || 'Gagal mengirim OTP ke WhatsApp.'
  } finally {
    loading.value = false
  }
}

const handleOtpInput = (e, index) => {
  if (e.target.value && index < 5) {
    setTimeout(() => {
      if(otpInputs.value[index+1]) otpInputs.value[index+1].focus()
    }, 10)
  }
}

const handleOtpDelete = (e, index) => {
  if (!otpDigits.value[index] && index > 0) {
    setTimeout(() => {
      if(otpInputs.value[index-1]) otpInputs.value[index-1].focus()
    }, 10)
  }
}

const handleVerifyOtp = async () => {
  const otp = otpDigits.value.join('')
  if (otp.length !== 6) {
    errorMessage.value = 'Mohon masukkan 6 digit OTP Fonnte.'
    return
  }
  loading.value = true
  errorMessage.value = ''
  try {
    const res = await $fetch('http://localhost:8000/api/verify-otp', {
      method: 'POST',
      body: { email: form.value.email, otp },
      headers: { 'Accept': 'application/json' }
    })
    verifyToken.value = res.token
    step.value = 4
  } catch (err) {
    errorMessage.value = err.data?.message || err.response?._data?.message || 'Kode OTP tidak valid atau kadaluarsa.'
  } finally {
    loading.value = false
  }
}

const handleResetPassword = async () => {
  if (form.value.password !== form.value.password_confirmation) {
    errorMessage.value = 'Password dan konfirmasinya tidak cocok!'
    return
  }
  loading.value = true
  errorMessage.value = ''
  try {
    await $fetch('http://localhost:8000/api/reset-password', {
      method: 'POST',
      body: { 
        email: form.value.email,
        token: verifyToken.value,
        password: form.value.password,
        password_confirmation: form.value.password_confirmation
      },
      headers: { 'Accept': 'application/json' }
    })
    alert('Sukses! Password akun Anda berhasil direset. Silakan login kembali.')
    navigateTo('/login')
  } catch (err) {
    errorMessage.value = err.data?.message || err.response?._data?.message || 'Gagal mengubah password ke server.'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
@keyframes blob {
  0% { transform: translate(0px, 0px) scale(1); }
  33% { transform: translate(30px, -50px) scale(1.1); }
  66% { transform: translate(-20px, 20px) scale(0.9); }
  100% { transform: translate(0px, 0px) scale(1); }
}
@keyframes shake {
  0%, 100% { transform: translateX(0); }
  25% { transform: translateX(-5px); }
  75% { transform: translateX(5px); }
}
.animate-blob { animation: blob 7s infinite; }
.animate-shake { animation: shake 0.2s ease-in-out 0s 2; }
.animation-delay-2000 { animation-delay: 2s; }
.animation-delay-4000 { animation-delay: 4s; }
</style>