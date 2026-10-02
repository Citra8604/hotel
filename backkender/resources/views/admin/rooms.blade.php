<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Rooms - Hotel 71</title>
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
            <a href="{{ route('admin.dashboard') }}" class="flex items-center p-3 hover:bg-slate-800 rounded-lg transition text-slate-300">
                <i class="fas fa-home mr-3 text-sm"></i> Dashboard
            </a>
            <a href="{{ route('admin.users') }}" class="flex items-center p-3 hover:bg-slate-800 rounded-lg transition text-slate-300">
                <i class="fas fa-users-cog mr-3 text-sm"></i> User Management
            </a>
            <a href="{{ route('admin.rooms') }}" class="flex items-center p-3 bg-slate-800 rounded-lg transition text-white">
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
            <div>
                <h1 class="text-lg font-bold text-slate-800">Room Management</h1>
                <p class="text-sm text-slate-500">Daftar semua kamar berikut tipe dan status masing-masing.</p>
            </div>
            <div class="text-sm text-slate-500">{{ date('l, d F Y') }}</div>
        </header>

        <main class="flex-1 overflow-y-auto p-8 bg-slate-50">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-slate-100 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4 text-xs font-semibold uppercase text-slate-500">No. Kamar</th>
                            <th class="px-6 py-4 text-xs font-semibold uppercase text-slate-500">Tipe Kamar</th>
                            <th class="px-6 py-4 text-xs font-semibold uppercase text-slate-500">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rooms as $room)
                            <tr class="border-b border-slate-100 hover:bg-slate-50 transition">
                                <td class="px-6 py-4 font-medium text-slate-700">{{ $room->room_number }}</td>
                                <td class="px-6 py-4 text-slate-500">{{ $room->roomType->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-slate-500 capitalize">{{ $room->status }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-16 text-center text-slate-400 italic">Belum ada data kamar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </main>
    </div>

</body>
</html>