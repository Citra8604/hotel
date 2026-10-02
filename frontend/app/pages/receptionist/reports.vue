<template>
  <div class="min-h-screen flex bg-slate-50 text-slate-900 font-sans overflow-x-hidden lg:pl-72">
    <ReceptionistSidebar :user="user" />

    <main class="flex-1 p-6 md:p-10 relative z-10 overflow-y-auto">

      <!-- ── Header ── -->
      <header class="mb-6 print:hidden mt-8 flex items-start justify-between gap-4 flex-wrap">
        <div>
          <h1 class="text-3xl font-black tracking-tight text-slate-900">Laporan Operasional</h1>
          <p class="text-slate-500 text-sm mt-1">Pantau performa reservasi kedatangan dan keberangkatan harian.</p>
        </div>
        <button @click="printReport"
          class="flex items-center gap-2 px-5 py-2.5 bg-slate-900 text-white text-xs font-black uppercase tracking-widest rounded-xl hover:bg-pink-600 transition-colors shadow-md cursor-pointer">
          🖨️ Cetak Laporan
        </button>
      </header>

      <!-- ── Print Header ── -->
      <div class="hidden print:block text-center mb-8 border-b-2 border-slate-900 pb-5">
        <h1 class="text-3xl font-black uppercase tracking-tighter text-slate-900">GRAND LUXE</h1>
        <p class="text-xs uppercase font-black text-pink-500 tracking-[0.3em] mt-1">Daily Operational Front Office Report</p>
        <p class="text-xs font-bold text-slate-400 mt-3">Tanggal Audit: {{ formatDateLong(tglLaporan) }}</p>
      </div>

      <!-- ── Stat Cards ── -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6 print:hidden">
        <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-4">
          <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Total Reservasi</p>
          <p class="text-3xl font-black text-slate-900">{{ semuaData.length }}</p>
          <p class="text-[10px] text-slate-400 mt-0.5">Hari ini</p>
        </div>
        <div class="bg-emerald-50 rounded-xl border border-emerald-100 shadow-sm p-4">
          <p class="text-[10px] font-black uppercase tracking-widest text-emerald-500 mb-1">Check-in</p>
          <p class="text-3xl font-black text-emerald-600">{{ laporanCheckIn.length }}</p>
          <p class="text-[10px] text-emerald-400 mt-0.5">Kedatangan</p>
        </div>
        <div class="bg-rose-50 rounded-xl border border-rose-100 shadow-sm p-4">
          <p class="text-[10px] font-black uppercase tracking-widest text-rose-400 mb-1">Check-out</p>
          <p class="text-3xl font-black text-rose-600">{{ laporanCheckOut.length }}</p>
          <p class="text-[10px] text-rose-400 mt-0.5">Keberangkatan</p>
        </div>
        <div class="bg-blue-50 rounded-xl border border-blue-100 shadow-sm p-4">
          <p class="text-[10px] font-black uppercase tracking-widest text-blue-400 mb-1">Menginap</p>
          <p class="text-3xl font-black text-blue-600">{{ laporanMenginap.length }}</p>
          <p class="text-[10px] text-blue-400 mt-0.5">Masih di hotel</p>
        </div>
      </div>

      <!-- ── Filter Bar ── -->
      <section class="bg-white border border-slate-100 rounded-xl shadow-sm p-4 mb-4 print:hidden space-y-3">
        <div class="flex flex-wrap gap-3">
          <input v-model="tglLaporan" type="date"
            class="flex-1 min-w-[150px] px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm font-bold outline-none focus:border-pink-500 transition-all" />
          <select v-model="filterStatus"
            class="flex-1 min-w-[150px] px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm font-bold outline-none focus:border-pink-500 transition-all">
            <option value="">Semua Status</option>
            <option value="check-in">Check-in</option>
            <option value="check-out">Check-out</option>
            <option value="menginap">Menginap</option>
            <option value="pending">Pending</option>
          </select>
          <select v-model="filterTipe"
            class="flex-1 min-w-[150px] px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm font-bold outline-none focus:border-pink-500 transition-all">
            <option value="">Semua Tipe Kamar</option>
            <option v-for="t in tipesAvailable" :key="t" :value="t">{{ t }}</option>
          </select>
        </div>
        <div class="flex gap-3">
          <input v-model="filterSearch" type="text" placeholder="Cari nama / kode..."
            class="flex-1 px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm font-bold outline-none focus:border-pink-500 transition-all" />
          <button @click="resetFilter"
            class="px-4 py-2 text-xs font-black uppercase tracking-wider bg-slate-100 text-slate-500 rounded-lg hover:bg-slate-200 transition-colors">
            ✕ Reset
          </button>
        </div>
      </section>

      <!-- ── Tabs ── -->
      <div class="flex gap-2 mb-4 flex-wrap print:hidden">
        <button v-for="tab in tabs" :key="tab.key" @click="activeTab = tab.key"
          :class="['px-4 py-2 text-xs font-black uppercase tracking-wider rounded-xl transition-colors',
            activeTab === tab.key ? 'bg-slate-900 text-white' : 'bg-white border border-slate-200 text-slate-500 hover:bg-slate-50']">
          {{ tab.label }}
          <span :class="['ml-1.5 px-1.5 py-0.5 rounded-md text-[10px]',
            activeTab === tab.key ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-400']">
            {{ tab.count }}
          </span>
        </button>
      </div>

      <!-- ── Table ── -->
      <div class="bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-sm border-collapse">
            <thead>
              <tr class="bg-slate-50 border-b border-slate-100">
                <th v-for="col in columns" :key="col.key"
                  @click="col.sortable && sortBy(col.key)"
                  :class="['px-4 py-3 text-left text-[10px] font-black uppercase tracking-widest text-slate-400 whitespace-nowrap',
                    col.sortable ? 'cursor-pointer hover:text-slate-700 select-none' : '']">
                  {{ col.label }}
                  <span v-if="col.sortable" class="ml-1 opacity-50 text-[9px]">
                    <template v-if="sortCol === col.key">{{ sortDir === 'asc' ? '▲' : '▼' }}</template>
                    <template v-else>⇅</template>
                  </span>
                </th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="loading">
                <td :colspan="columns.length" class="px-4 py-12 text-center text-slate-400 text-xs font-bold italic">
                  Memuat data...
                </td>
              </tr>
              <tr v-else-if="filteredData.length === 0">
                <td :colspan="columns.length" class="px-4 py-12 text-center text-slate-400 text-xs font-bold italic">
                  Tidak ada data untuk filter yang dipilih.
                </td>
              </tr>
              <tr v-else v-for="row in filteredData" :key="row.id"
                class="border-b border-slate-50 hover:bg-slate-50/60 transition-colors">
                <td class="px-4 py-3 font-black text-slate-900 whitespace-nowrap">
                  {{ row.nama_pemesan || row.nama_tamu || '-' }}
                </td>
                <td class="px-4 py-3 font-mono text-[11px] text-slate-400 uppercase tracking-wider whitespace-nowrap">
                  {{ row.nomor_pemesanan || row.reservation_number || '-' }}
                </td>
                <td class="px-4 py-3 text-slate-700 font-semibold whitespace-nowrap">
                  {{ row.nama_tipe || 'Kamar Pilihan' }}
                </td>
                <td class="px-4 py-3 text-center font-bold text-slate-700">
                  {{ row.quantity || row.jumlah_kamar || 1 }}
                </td>
                <td class="px-4 py-3 text-slate-600 whitespace-nowrap">
                  {{ formatDate(row.tgl_check_in || row.check_in) }}
                </td>
                <td class="px-4 py-3 text-slate-600 whitespace-nowrap">
                  {{ formatDate(row.tgl_check_out || row.check_out) }}
                </td>
                <td class="px-4 py-3 whitespace-nowrap">
                  <span :class="['px-2.5 py-1 text-[9px] font-black uppercase rounded-lg border', statusBadge(row).cls]">
                    {{ statusBadge(row).label }}
                  </span>
                </td>
                <td class="px-4 py-3 whitespace-nowrap print:hidden">
                  <button class="px-3 py-1.5 text-[10px] font-black uppercase tracking-wider border border-slate-200 rounded-lg hover:bg-slate-100 transition-colors text-slate-600">
                    Detail
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="px-5 py-3 border-t border-slate-50 flex items-center justify-between flex-wrap gap-2 print:hidden">
          <p class="text-[11px] text-slate-400 font-bold">
            Menampilkan <span class="text-slate-700">{{ filteredData.length }}</span> dari
            <span class="text-slate-700">{{ semuaData.length }}</span> data
          </p>
          <p class="text-[11px] text-slate-400 font-bold">
            Audit: {{ formatDateLong(tglLaporan) }}
          </p>
        </div>
      </div>

    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import ReceptionistSidebar from '~/components/receptionist/ReceptionistSidebar.vue'

definePageMeta({ layout: false })

const user         = ref({ name: 'Resepsionis', role: 'resepsionis' })
const pesananList  = ref([])
const loading      = ref(true)
const tglLaporan   = ref(new Date().toISOString().split('T')[0])
const filterStatus = ref('')
const filterTipe   = ref('')
const filterSearch = ref('')
const activeTab    = ref('semua')
const sortCol      = ref(null)
const sortDir      = ref('asc')

const formatDate = (date) =>
  date ? new Date(date).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) : '-'

const formatDateLong = (dateStr) =>
  dateStr ? new Date(dateStr).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '-'

const statusBadge = (p) => {
  const s = (p.status_pemesanan || p.status || '').toLowerCase()
  if (s.includes('check_in') || s.includes('check-in') || s === 'confirmed')
    return { label: 'Check-in', cls: 'bg-emerald-50 text-emerald-600 border-emerald-200' }
  if (s.includes('check_out') || s.includes('check-out') || s === 'checked_out')
    return { label: 'Check-out', cls: 'bg-rose-50 text-rose-500 border-rose-200' }
  if (s.includes('menginap') || s.includes('in_house'))
    return { label: 'Menginap', cls: 'bg-blue-50 text-blue-600 border-blue-200' }
  return { label: s || 'Pending', cls: 'bg-amber-50 text-amber-600 border-amber-200' }
}

const fetchPesanan = async (localToken) => {
  loading.value = true
  try {
    const response = await $fetch('http://localhost:8000/api/pemesanan', {
      headers: { 'Authorization': `Bearer ${localToken}` }
    })
    pesananList.value = response.data || response || []
  } catch (err) {
    console.error('Gagal mengambil laporan harian:', err)
  } finally {
    loading.value = false
  }
}

const laporanCheckIn = computed(() => {
  if (!pesananList.value) return []
  return pesananList.value.filter(p => {
    const checkInDate   = p.tgl_check_in || p.check_in
    const currentStatus = p.status_pemesanan || p.status
    return checkInDate && checkInDate.startsWith(tglLaporan.value) &&
           currentStatus !== 'checked_out' && currentStatus !== 'Check-out'
  })
})

const laporanCheckOut = computed(() => {
  if (!pesananList.value) return []
  return pesananList.value.filter(p => {
    const checkOutDate = p.tgl_check_out || p.check_out
    return checkOutDate && checkOutDate.startsWith(tglLaporan.value)
  })
})

const laporanMenginap = computed(() =>
  pesananList.value.filter(p => {
    const ci = p.tgl_check_in  || p.check_in
    const co = p.tgl_check_out || p.check_out
    const s  = (p.status_pemesanan || p.status || '').toLowerCase()
    return ci && co && ci < tglLaporan.value && co > tglLaporan.value &&
           !s.includes('check_out') && !s.includes('check-out')
  })
)

const semuaData = computed(() => {
  const seen = new Set()
  return [...laporanCheckIn.value, ...laporanCheckOut.value, ...laporanMenginap.value]
    .filter(p => { if (seen.has(p.id)) return false; seen.add(p.id); return true })
})

const tipesAvailable = computed(() =>
  [...new Set(semuaData.value.map(p => p.nama_tipe).filter(Boolean))]
)

const filteredData = computed(() => {
  let rows =
    activeTab.value === 'check-in'  ? laporanCheckIn.value  :
    activeTab.value === 'check-out' ? laporanCheckOut.value :
    activeTab.value === 'menginap'  ? laporanMenginap.value :
    semuaData.value

  if (filterStatus.value) {
    rows = rows.filter(p => {
      const s = (p.status_pemesanan || p.status || '').toLowerCase()
      if (filterStatus.value === 'check-in')  return s.includes('check_in')  || s.includes('check-in')  || s === 'confirmed'
      if (filterStatus.value === 'check-out') return s.includes('check_out') || s.includes('check-out') || s === 'checked_out'
      if (filterStatus.value === 'menginap')  return s.includes('menginap')  || s.includes('in_house')
      if (filterStatus.value === 'pending')   return !s.includes('check') && !s.includes('menginap') && !s.includes('in_house') && !s.includes('confirmed')
      return true
    })
  }

  if (filterTipe.value)   rows = rows.filter(p => p.nama_tipe === filterTipe.value)

  if (filterSearch.value) {
    const q = filterSearch.value.toLowerCase()
    rows = rows.filter(p => {
      const nama = (p.nama_pemesan || p.nama_tamu || '').toLowerCase()
      const kode = (p.nomor_pemesanan || p.reservation_number || '').toLowerCase()
      return nama.includes(q) || kode.includes(q)
    })
  }

  if (sortCol.value) {
    const getter = {
      nama:     p => p.nama_pemesan    || p.nama_tamu          || '',
      kode:     p => p.nomor_pemesanan || p.reservation_number || '',
      tipe:     p => p.nama_tipe       || '',
      checkin:  p => p.tgl_check_in    || p.check_in           || '',
      checkout: p => p.tgl_check_out   || p.check_out          || '',
    }[sortCol.value] || (() => '')
    rows = [...rows].sort((a, b) => {
      const av = getter(a).toLowerCase(), bv = getter(b).toLowerCase()
      return sortDir.value === 'asc' ? av.localeCompare(bv) : bv.localeCompare(av)
    })
  }
  return rows
})

const tabs = computed(() => [
  { key: 'semua',     label: 'Semua',     count: semuaData.value.length       },
  { key: 'check-in',  label: 'Check-in',  count: laporanCheckIn.value.length  },
  { key: 'check-out', label: 'Check-out', count: laporanCheckOut.value.length },
  { key: 'menginap',  label: 'Menginap',  count: laporanMenginap.value.length },
])

const columns = [
  { key: 'nama',     label: 'Nama Tamu',  sortable: true  },
  { key: 'kode',     label: 'Kode RSV',   sortable: true  },
  { key: 'tipe',     label: 'Tipe Kamar', sortable: true  },
  { key: 'unit',     label: 'Unit',       sortable: false },
  { key: 'checkin',  label: 'Check-in',   sortable: true  },
  { key: 'checkout', label: 'Check-out',  sortable: true  },
  { key: 'status',   label: 'Status',     sortable: false },
  { key: 'aksi',     label: 'Aksi',       sortable: false },
]

const sortBy = (col) => {
  if (sortCol.value === col) sortDir.value = sortDir.value === 'asc' ? 'desc' : 'asc'
  else { sortCol.value = col; sortDir.value = 'asc' }
}

const resetFilter = () => {
  filterStatus.value = ''; filterTipe.value = ''; filterSearch.value = ''
  activeTab.value = 'semua'; sortCol.value = null; sortDir.value = 'asc'
}

const printReport = () => window.print()

onMounted(() => {
  const localToken = localStorage.getItem('auth_token')
  if (!localToken) {
    window.location.href = '/login'
  } else {
    const savedUser = localStorage.getItem('auth_user')
    if (savedUser) user.value = JSON.parse(savedUser)
    fetchPesanan(localToken)
  }
})
</script>

<!-- GLOBAL style (bukan scoped) supaya bisa reach sidebar & body saat print -->
<style>
@media print {
  /* Sembunyikan sidebar, navbar, semua elemen di luar main */
  aside,
  nav,
  header.sidebar,
  [class*="sidebar"],
  [class*="Sidebar"],
  .lg\:pl-72 > *:not(main) {
    display: none !important;
  }

  /* Reset body & html */
  html, body {
    margin: 0 !important;
    padding: 0 !important;
    background: white !important;
  }

  /* Wrapper utama: buang flex & padding sidebar */
  .min-h-screen.flex {
    display: block !important;
    padding-left: 0 !important;
  }

  /* Main full width */
  main {
    display: block !important;
    width: 100% !important;
    max-width: 100% !important;
    padding: 1.5rem !important;
    margin: 0 !important;
    background: white !important;
    overflow: visible !important;
  }

  /* Tabel cetak rapi */
  table {
    font-size: 11px !important;
    width: 100% !important;
    border-collapse: collapse !important;
  }

  th, td {
    padding: 6px 10px !important;
    border-bottom: 1px solid #e2e8f0 !important;
  }

  thead tr {
    background: #f8fafc !important;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }

  tr { page-break-inside: avoid; }

  .bg-white {
    border: none !important;
    box-shadow: none !important;
  }
}
</style>