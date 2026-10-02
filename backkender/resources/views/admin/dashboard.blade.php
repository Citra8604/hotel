<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Hotel 71</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 flex h-screen overflow-hidden">

    <div class="w-64 bg-slate-900 text-white flex flex-col">
        <div class="p-6 text-2xl font-bold border-b border-slate-800 flex items-center gap-2">
            <div class="bg-blue-600 rounded-full h-8 w-8 flex items-center justify-center text-sm">71</div>
            Admin Panel
        </div>
        
        <nav class="flex-1 mt-4 px-4 space-y-2">
            <p class="text-xs font-semibold text-slate-500 uppercase px-2">Master Data</p>
            <a href="/admin/dashboard" class="flex items-center p-3 bg-blue-600 rounded-lg">
                <i class="fas fa-home mr-3 text-sm"></i> Dashboard
            </a>
            <a href="{{ route('admin.users') }}" class="flex items-center p-3 hover:bg-slate-800 rounded-lg transition text-slate-300">
                <i class="fas fa-users-cog mr-3 text-sm"></i> User Management
            </a>
            <a href="{{ route('admin.rooms') }}" class="flex items-center p-3 hover:bg-slate-800 rounded-lg transition text-slate-300">
                <i class="fas fa-bed mr-3 text-sm"></i> Room Management
            </a>
            <a href="#" class="flex items-center p-3 hover:bg-slate-800 rounded-lg transition text-slate-300">
                <i class="fas fa-tags mr-3 text-sm"></i> Room Type Management
            </a>
            
            <p class="text-xs font-semibold text-slate-500 uppercase px-2 mt-6">Reports</p>
            <a href="#" class="flex items-center p-3 hover:bg-slate-800 rounded-lg transition text-slate-300">
                <i class="fas fa-file-invoice-dollar mr-3 text-sm"></i> Pendapatan
            </a>
        </nav>

        <div class="p-4 border-t border-slate-800 bg-slate-950">
            <div class="flex items-center gap-3 mb-4 px-2">
                <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center font-bold text-xs">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div class="overflow-hidden">
                    <p class="text-sm font-medium truncate">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-slate-500 capitalize">{{ Auth::user()->role }}</p>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full bg-red-500/10 hover:bg-red-500 text-red-500 hover:text-white py-2 rounded-md text-sm transition font-medium">
                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                </button>
            </form>
        </div>
    </div>

    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white shadow-sm p-4 flex justify-between items-center px-8 z-10">
            <h1 class="text-lg font-bold text-slate-800">Administrator Dashboard</h1>
            <div class="text-sm text-slate-500">{{ date('l, d F Y') }}</div>
        </header>

        <main class="flex-1 overflow-y-auto p-8 bg-slate-50">
            <div class="bg-gradient-to-r from-blue-600 to-blue-800 p-8 rounded-2xl shadow-lg text-white mb-8">
                <h2 class="text-2xl font-bold">Selamat Datang Kembali, {{ Auth::user()->name }}!</h2>
                <p class="text-blue-100 mt-1">Kelola data kamar, tipe kamar, dan user melalui menu di sebelah kiri.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-slate-500 font-medium uppercase">Total User</p>
                            <p class="text-3xl font-bold text-slate-800 mt-1">{{ $count['users'] ?? '0' }}</p>
                        </div>
                        <div class="h-12 w-12 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600 text-xl">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-slate-500 font-medium uppercase">Kamar Tersedia</p>
                            <p class="text-3xl font-bold text-slate-800 mt-1">--</p>
                        </div>
                        <div class="h-12 w-12 bg-green-50 rounded-lg flex items-center justify-center text-green-600 text-xl">
                            <i class="fas fa-door-open"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-slate-500 font-medium uppercase">Total Pendapatan</p>
                            <p class="text-3xl font-bold text-slate-800 mt-1">Rp 0</p>
                        </div>
                        <div class="h-12 w-12 bg-yellow-50 rounded-lg flex items-center justify-center text-yellow-600 text-xl">
                            <i class="fas fa-wallet"></i>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

</body>
</html>