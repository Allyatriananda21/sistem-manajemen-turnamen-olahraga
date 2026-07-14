<x-layouts::auth.simple :title="__('Log in')">
    <div class="flex flex-col gap-8">

        {{-- Brand Header --}}
        <div class="flex flex-col items-center gap-3 text-center">
            <div class="flex h-16 w-16 items-center justify-center rounded-2xl shadow-lg"
                 style="background: linear-gradient(135deg, #1e2b1d 0%, #2D3E2C 60%, #3d5c3b 100%); border: 1px solid rgba(228,253,151,0.25);">
                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                     style="color: #E4FD97;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white"
                    style="font-family: 'Space Grotesk', sans-serif;">
                    TrophyHub
                </h1>
                <p class="text-xs font-semibold uppercase tracking-widest" style="color: #4a7c30;">
                    Admin Panel
                </p>
            </div>
        </div>

        {{-- Card --}}
        <div class="rounded-2xl border shadow-xl dark:shadow-black/30 overflow-hidden"
             style="border-color: rgba(228,253,151,0.2); background: rgba(30,43,29,0.97);">

            {{-- Card top accent --}}
            <div class="h-1 w-full" style="background: linear-gradient(90deg, #4a7c30, #E4FD97, #4a7c30);"></div>

            <div class="flex flex-col gap-5 p-8">

                <div class="text-center">
                    <h2 class="text-lg font-semibold text-white" style="font-family: 'Space Grotesk', sans-serif;">
                        Masuk ke Akun Anda
                    </h2>
                    <p class="mt-1 text-sm" style="color: #64748b;">
                        Masukkan email dan password untuk melanjutkan
                    </p>
                </div>

                {{-- Session Status --}}
                <x-auth-session-status class="text-center" :status="session('status')" />

                <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-4">
                    @csrf

                    {{-- Email --}}
                    <div class="flex flex-col gap-1.5">
                        <label for="email" class="text-xs font-semibold uppercase tracking-wider" style="color: rgba(228,253,151,0.7);">
                            {{ __('Email') }}
                        </label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-3 flex items-center">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #4a7c30;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                </svg>
                            </div>
                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                autocomplete="email"
                                placeholder="admin@turnamen.test"
                                class="w-full rounded-xl pl-10 pr-4 py-2.5 text-sm text-white placeholder-slate-600 transition-all duration-200 focus:outline-none focus:ring-2"
                                style="background: rgba(255,255,255,0.05); border: 1px solid rgba(228,253,151,0.15); focus-ring-color: #E4FD97;"
                                onfocus="this.style.borderColor='rgba(228,253,151,0.5)'; this.style.boxShadow='0 0 0 2px rgba(228,253,151,0.15)'"
                                onblur="this.style.borderColor='rgba(228,253,151,0.15)'; this.style.boxShadow='none'"
                            />
                        </div>
                        @error('email')
                            <p class="flex items-center gap-1 text-xs text-red-400">
                                <svg class="h-3 w-3 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="flex flex-col gap-1.5">
                        <div class="flex items-center justify-between">
                            <label for="password" class="text-xs font-semibold uppercase tracking-wider" style="color: rgba(228,253,151,0.7);">
                                {{ __('Password') }}
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}"
                                   class="text-xs transition-colors duration-200 hover:underline underline-offset-4"
                                   style="color: #4a7c30;"
                                   onmouseover="this.style.color='#E4FD97'"
                                   onmouseout="this.style.color='#4a7c30'">
                                    {{ __('Lupa password?') }}
                                </a>
                            @endif
                        </div>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-3 flex items-center">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #4a7c30;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input
                                id="password"
                                type="password"
                                name="password"
                                required
                                autocomplete="current-password"
                                placeholder="••••••••"
                                class="w-full rounded-xl pl-10 pr-4 py-2.5 text-sm text-white placeholder-slate-600 transition-all duration-200 focus:outline-none"
                                style="background: rgba(255,255,255,0.05); border: 1px solid rgba(228,253,151,0.15);"
                                onfocus="this.style.borderColor='rgba(228,253,151,0.5)'; this.style.boxShadow='0 0 0 2px rgba(228,253,151,0.15)'"
                                onblur="this.style.borderColor='rgba(228,253,151,0.15)'; this.style.boxShadow='none'"
                            />
                        </div>
                        @error('password')
                            <p class="flex items-center gap-1 text-xs text-red-400">
                                <svg class="h-3 w-3 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Remember Me --}}
                    <div class="flex items-center gap-2.5">
                        <input
                            id="remember"
                            type="checkbox"
                            name="remember"
                            {{ old('remember') ? 'checked' : '' }}
                            class="h-4 w-4 rounded"
                            style="accent-color: #E4FD97;"
                        />
                        <label for="remember" class="text-sm" style="color: #64748b;">
                            {{ __('Ingat saya') }}
                        </label>
                    </div>

                    {{-- Submit --}}
                    <button
                        type="submit"
                        data-test="login-button"
                        class="group relative mt-1 w-full overflow-hidden rounded-xl px-4 py-3 text-sm font-bold tracking-wide transition-all duration-200 focus:outline-none"
                        style="background: #E4FD97; color: #1e2b1d; font-family: 'Space Grotesk', sans-serif;"
                        onmouseover="this.style.background='#d8f57a'; this.style.boxShadow='0 4px 20px rgba(228,253,151,0.35)'"
                        onmouseout="this.style.background='#E4FD97'; this.style.boxShadow='none'"
                    >
                        <span class="flex items-center justify-center gap-2">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                            {{ __('Masuk') }}
                        </span>
                    </button>
                </form>

            </div>
        </div>

        {{-- Footer --}}
        <p class="text-center text-xs" style="color: rgba(100,116,139,0.6);">
            &copy; {{ date('Y') }} TrophyHub — Sistem Manajemen Turnamen
        </p>

    </div>
</x-layouts::auth.simple>
