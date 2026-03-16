<x-guest-layout>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@100;400;700;900&display=swap');

        .font-outfit {
            font-family: 'Outfit', sans-serif !important;
        }
    </style>

    <div class="min-h-screen w-full flex items-center justify-center bg-[#980046] px-4 font-outfit">
        <div class="text-center select-none w-full">
            <div class="flex items-center justify-center gap-2 mb-3">
                <img src="/storage/images/system/logo-movete-high.png" alt="logo" class="h-7 w-auto" style="filter: brightness(0) invert(1);">
                <span class="font-extrabold text-5xl text-white tracking-wide">movete</span>
            </div>

            <h2 class="text-white font-semibold text-xl">¡Bienvenido nuevamente!</h2>
            <p class="text-white/85 text-[13px] leading-snug mt-1">Ingresa tu usuario y tu contraseña para acceder a tu cuenta</p>

            <div class="mt-6">
                <div class="bg-white rounded-3xl shadow-xl ring-1 ring-black/5 mx-auto p-6 w-[600px] md:w-[400px]">
                    <div class="mb-4">
                        <div class="bg-gray-100 rounded-full p-1 w-full flex shadow-inner">
                            <a href="{{ route('login') }}" class="flex-1 text-center rounded-full py-2.5 text-sm transition font-medium bg-white shadow text-[#980046] block">
                                Inicio Sesión
                            </a>
                            <a href="#" class="flex-1 text-center rounded-full py-2.5 text-sm transition font-medium text-[#980046]/70 hover:text-[#980046] block">
                                Registro
                            </a>
                        </div>
                    </div>

                    <!-- Session Status -->
                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <!-- Validation Errors -->
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />

                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="space-y-4">
                            <div class="space-y-3.5">
                                <label class="block">
                                    <div class="relative flex items-center">
                                        <span class="absolute left-3 text-[#980046]">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                                <circle cx="12" cy="7" r="4"></circle>
                                            </svg>
                                        </span>
                                        <input id="email" type="email" name="email" placeholder="Usuario"
                                            class="w-full bg-[#F4F4F4] rounded-full pl-10 pr-10 h-11 text-sm placeholder:text-gray-400 outline-none focus:ring-2 focus:ring-[#E5393A]/30 border-none"
                                            value="{{ old('email') }}" required autofocus>
                                    </div>
                                </label>

                                <label class="block">
                                    <div class="relative flex items-center">
                                        <span class="absolute left-3 text-[#980046]">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                            </svg>
                                        </span>
                                        <input id="password" type="password" name="password" placeholder="Contraseña"
                                            class="w-full bg-[#F4F4F4] rounded-full pl-10 pr-10 h-11 text-sm placeholder:text-gray-400 outline-none focus:ring-2 focus:ring-[#E5393A]/30 border-none"
                                            required autocomplete="current-password">

                                        <button type="button" class="absolute right-3 text-[#980046] focus:outline-none" aria-label="Mostrar contraseña" id="togglePassword">
                                            <svg id="eyeIconOpen" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <svg id="eyeIconClosed" class="hidden" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
                                                <path d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88L5.99 5.99m7.59 7.59l3.89 3.89m-1.28-11.28A10.003 10.003 0 0121.543 12c-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                <path d="M3 3l18 18" />
                                            </svg>
                                        </button>
                                    </div>
                                </label>
                            </div>

                            @if (Route::has('password.request'))
                            <div class="text-right">
                                <a href="{{ route('password.request') }}" class="text-[11px] text-[#E5393A] hover:underline">¿Olvidaste tu contraseña?</a>
                            </div>
                            @endif

                            <button type="submit" class="w-full rounded-full py-3 text-white text-sm font-semibold shadow mt-2" style="background-color: #E5393A;">
                                Iniciar Sesión
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const eyeIconOpen = document.getElementById('eyeIconOpen');
            const eyeIconClosed = document.getElementById('eyeIconClosed');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIconOpen.style.display = 'none';
                eyeIconClosed.style.display = 'block';
            } else {
                passwordInput.type = 'password';
                eyeIconOpen.style.display = 'block';
                eyeIconClosed.style.display = 'none';
            }
        });
    </script>
</x-guest-layout>
