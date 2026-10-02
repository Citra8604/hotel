<template>
  <header class="flex justify-between items-center mb-10">
    <div>
      <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight">{{ title }}</h1>
      <p class="text-pink-600 mt-2 text-sm font-medium">{{ subtitle }}</p>
    </div>
    <div class="flex items-center space-x-4 bg-white border border-pink-100 p-2 pr-6 rounded-3xl shadow-xl shadow-pink-500/5">
      <div class="h-10 w-10 rounded-2xl bg-gradient-to-tr from-pink-500 to-rose-500 flex items-center justify-center text-white font-bold text-lg shadow-lg shadow-pink-500/30">
        {{ user?.name ? user.name.charAt(0).toUpperCase() : 'A' }}
      </div>
      <div class="text-right hidden sm:block">
        <p class="text-sm font-bold text-slate-900 leading-tight">{{ user?.name || 'Admin User' }}</p>
        <p class="text-xs text-slate-400 font-semibold uppercase tracking-widest mt-0.5">{{ user?.role || 'Master Control' }}</p>
      </div>
    </div>
  </header>
</template>

<script setup>
defineProps({
  title: String,
  subtitle: String,
  user: Object
})
</script>
