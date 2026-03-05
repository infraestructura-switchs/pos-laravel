<x-guest-layout>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@100;400;700;900&display=swap');
        
        body {
            background-color: #9d0154 !important;
            font-family: 'Outfit', sans-serif !important;
        }

        .login-card {
            background: white;
            border-radius: 40px;
            padding: 40px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 450px;
        }

        .branding-title {
            font-size: 3rem;
            font-weight: 900;
            color: white;
            letter-spacing: -2px;
            margin: 0;
            line-height: 1;
        }

        .branding-subtitle {
            color: white;
            font-size: 1.5rem;
            font-weight: 700;
            margin-top: 15px;
            text-align: center;
        }

        .branding-desc {
            color: rgba(255,255,255,0.8);
            font-size: 0.875rem;
            text-align: center;
            margin-top: 5px;
        }

        .toggle-container {
            background: #f3f4f6;
            border-radius: 20px;
            display: flex;
            padding: 5px;
            margin-bottom: 30px;
        }

        .toggle-btn {
            flex: 1;
            text-align: center;
            padding: 10px;
            font-weight: 600;
            border-radius: 15px;
            text-decoration: none;
            transition: all 0.3s;
        }

        .toggle-btn.active {
            background: white;
            color: #9d0154;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .toggle-btn.inactive {
            color: #9ca3af;
        }

        .input-group {
            position: relative;
            margin-bottom: 20px;
        }

        .input-icon {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            width: 20px;
            height: 20px;
        }

        .custom-input {
            width: 100%;
            padding: 18px 20px 18px 55px;
            background: #f9fafb;
            border: none;
            border-radius: 20px;
            font-size: 1rem;
            color: #374151;
            transition: all 0.3s;
            box-sizing: border-box;
        }

        .custom-input:focus {
            background: white;
            outline: none;
            box-shadow: 0 0 0 2px #9d0154;
        }

        .forgot-link {
            display: block;
            text-align: right;
            color: #f06155;
            font-size: 0.875rem;
            font-weight: 600;
            text-decoration: none;
            margin-bottom: 30px;
            transition: color 0.3s;
        }

        .forgot-link:hover {
            color: #9d0154;
        }

        .login-submit {
            width: 100%;
            background: #e64136;
            color: white;
            border: none;
            border-radius: 20px;
            padding: 18px;
            font-size: 1.125rem;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 10px 15px -3px rgba(230, 65, 54, 0.4);
            transition: all 0.3s;
        }

        .login-submit:hover {
            background: #d43a30;
            transform: translateY(-1px);
        }

        .login-submit:active {
            transform: translateY(0);
        }

        .eye-btn {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            padding: 5px;
        }

        .main-wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
            box-sizing: border-box;
        }
    </style>

    <div class="main-wrapper">
        <!-- Branding Header -->
        <div style="margin-bottom: 40px; display: flex; flex-direction: column; align-items: center;">
            <div style="display: flex; align-items: center;">
                <img src="/storage/images/system/logo-movete-high.png" alt="movete" style="height: 60px; width: auto; filter: brightness(0) invert(1);">
            </div>
            <h2 class="branding-subtitle">¡Bienvenido nuevamente!</h2>
            <p class="branding-desc">Ingresa tu usuario y tu contraseña para acceder a tu cuenta</p>
        </div>

        <div class="login-card">
            <!-- Toggle Login/Register -->
            <div class="toggle-container">
                <a href="{{ route('login') }}" class="toggle-btn active">
                    Inicio Sesión
                </a>
                <a href="{{ route('tenant.register.form') }}" class="toggle-btn inactive">
                    Registro
                </a>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <!-- Validation Errors -->
            <x-auth-validation-errors class="mb-4" :errors="$errors" />

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Address -->
                <div class="input-group">
                    <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <input id="email" 
                        class="custom-input" 
                        type="email" 
                        name="email" 
                        placeholder="Usuario"
                        value="{{ old('email') }}" 
                        required autofocus />
                </div>

                <!-- Password -->
                <div class="input-group">
                    <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    <input id="password" 
                        class="custom-input"
                        type="password"
                        name="password"
                        placeholder="Contraseña"
                        required autocomplete="current-password" />
                <button type="button" class="eye-btn" id="togglePassword">
                        <svg id="eyeIconOpen" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg id="eyeIconClosed" class="hidden" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88L5.99 5.99m7.59 7.59l3.89 3.89m-1.28-11.28A10.003 10.003 0 0121.543 12c-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />
                        </svg>
                    </button>
                </div>

                @if (Route::has('password.request'))
                    <a class="forgot-link" href="{{ route('password.request') }}">
                        ¿Olvidaste tu contraseña?
                    </a>
                @endif

                <button type="submit" class="login-submit">
                    Iniciar Sesión
                </button>
            </form>
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
