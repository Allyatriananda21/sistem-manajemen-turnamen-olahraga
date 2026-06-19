<div class="relative overflow-hidden bg-white/60 dark:bg-slate-900/60 backdrop-blur-md rounded-2xl border border-slate-200/80 dark:border-slate-800/80 p-8 shadow-sm">
    <!-- Glow decorations -->
    <div class="absolute top-0 right-0 -mt-8 -mr-8 w-32 h-32 bg-indigo-500/10 rounded-full blur-2xl"></div>
    <div class="absolute bottom-0 left-0 -mb-8 -ml-8 w-32 h-32 bg-purple-500/10 rounded-full blur-2xl"></div>

    <div class="relative flex flex-col items-center justify-center text-center py-12 space-y-4">
        <!-- Modern Trophy Icon with animated ring -->
        <div class="relative flex items-center justify-center">
            <div class="absolute inset-0 rounded-full bg-indigo-500/20 dark:bg-indigo-500/10 animate-ping"></div>
            <div class="relative p-5 bg-gradient-to-tr from-indigo-500 to-purple-600 rounded-full text-white shadow-lg shadow-indigo-500/30">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5a2 2 0 10-2 2h2zm0 0h4m-4 0H8m12 3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <div class="space-y-2">
            <h3 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Dashboard</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 max-w-sm mx-auto">
                Widget akan ditambahkan di fase berikutnya
            </p>
        </div>

        <!-- Skeleton widgets representation to make it feel premium and "under construction" but clean -->
        <div class="w-full max-w-md pt-8 grid grid-cols-3 gap-3">
            <div class="h-2 bg-slate-200 dark:bg-slate-800 rounded-full"></div>
            <div class="h-2 bg-slate-200 dark:bg-slate-800 rounded-full col-span-2"></div>
            <div class="h-2 bg-slate-200 dark:bg-slate-800 rounded-full col-span-2"></div>
            <div class="h-2 bg-slate-200 dark:bg-slate-800 rounded-full"></div>
            <div class="h-2 bg-slate-200 dark:bg-slate-800 rounded-full col-span-3"></div>
        </div>
    </div>
</div>
