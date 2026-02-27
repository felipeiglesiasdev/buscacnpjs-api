<x-guest-layout>
    

    <!-- CSS Customizado com as Cores da Marca e Animações -->
    <style>
        :root {
            --cor-verde: #499F2D;
            --cor-azul: #004EA5;
            --cor-escura: #222222;
        }

        /* Fundo da tela: Usa a cor escura e adiciona um glow suave das cores da marca */
        body, .bg-gray-100 {
            background-color: var(--cor-escura) !important;
            background-image: 
                radial-gradient(circle at top right, rgba(0, 78, 165, 0.15), transparent 40%),
                radial-gradient(circle at bottom left, rgba(73, 159, 45, 0.15), transparent 40%) !important;
        }

        /* Customização do Card Branco do Breeze */
        .bg-white {
            background-color: #ffffff !important;
            border-radius: 16px !important; /* Bordas mais arredondadas */
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4) !important;
            padding: 2.5rem 2rem !important;
            border-top: 5px solid var(--cor-azul) !important; /* Detalhe azul no topo */
        }

        /* Inputs com espaço para os ícones */
        .input-custom {
            padding-left: 2.8rem !important;
            height: 3.2rem;
            border-color: #e5e7eb !important;
            color: var(--cor-escura) !important;
            transition: all 0.3s ease !important;
            border-radius: 8px !important;
        }
        
        .input-custom:focus {
            border-color: var(--cor-azul) !important;
            box-shadow: 0 0 0 4px rgba(0, 78, 165, 0.15) !important;
        }

        /* Ícones dentro dos inputs */
        .icon-input {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.2rem;
            color: #9ca3af;
            transition: color 0.3s ease;
        }
        .group:focus-within .icon-input {
            color: var(--cor-azul);
        }

        /* Botão Primário: Azul -> Verde no Hover */
        .btn-login {
            background-color: var(--cor-azul) !important;
            color: #ffffff !important;
            height: 3.2rem;
            border-radius: 8px !important;
            transition: all 0.4s ease !important;
            cursor: pointer !important;
            border: none !important;
            font-weight: 600 !important;
            letter-spacing: 0.5px;
        }
        .btn-login:hover {
            background-color: var(--cor-verde) !important;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(73, 159, 45, 0.3) !important;
        }

        /* Links Customizados */
        .link-custom {
            color: var(--cor-azul);
            transition: color 0.3s ease;
        }
        .link-custom:hover {
            color: var(--cor-verde);
        }

        /* Checkbox */
        .checkbox-custom {
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .checkbox-custom:checked {
            background-color: var(--cor-azul) !important;
            border-color: var(--cor-azul) !important;
        }
        .checkbox-custom:focus {
            box-shadow: 0 0 0 2px rgba(0, 78, 165, 0.2) !important;
        }

        /* Animação suave de entrada */
        .animate-fade-in-up {
            animation: fadeInUp 0.7s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
            transform: translateY(20px);
        }
        @keyframes fadeInUp {
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

    <div class="animate-fade-in-up">

        <!-- Slot da Logo: É renderizado acima do card branco pelo guest.blade.php -->
        <a href="/" class="flex justify-center transition-transform duration-300 hover:scale-105">
            <img src="{{ asset('logo/buscaCnpjs.png') }}" alt="BuscaCNPJs Logo" class="h-12 w-auto object-contain drop-shadow-xl mb-4">
        </a>
        
        
        <!-- Cabeçalho do Formulário -->
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold" style="color: var(--cor-escura);">Acesso à Plataforma</h2>
            <p class="text-sm mt-1 text-gray-500">Insira suas credenciais para continuar</p>
        </div>

        <!-- Session Status (Mensagens de Erro/Sucesso) -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- E-mail -->
            <div class="group relative mb-5">
                <x-input-label for="email" value="E-mail" class="font-medium mb-1 block" style="color: var(--cor-escura);" />
                
                <div class="relative">
                    <div class="icon-input">
                        <i class="bi bi-envelope"></i>
                    </div>
                    <x-text-input id="email" 
                                  class="block w-full input-custom" 
                                  type="email" 
                                  name="email" 
                                  :value="old('email')" 
                                  required 
                                  autofocus 
                                  autocomplete="username" 
                                  placeholder="seuemail@seuemail.com" />
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-sm" />
            </div>


            <!-- Senha -->
            <div class="group relative mb-6">
                <div class="flex items-center justify-between mb-1">
                    <x-input-label for="password" value="Senha" class="font-medium" style="color: var(--cor-escura);" />
                    
                    @if (Route::has('password.request'))
                        <a class="text-sm font-semibold link-custom text-decoration-none" href="{{ route('password.request') }}">
                            Esqueceu a senha?
                        </a>
                    @endif
                </div>
                
                <div class="relative">
                    <div class="icon-input">
                        <i class="bi bi-shield-lock"></i>
                    </div>
                    <x-text-input id="password" 
                                  class="block w-full input-custom"
                                  type="password"
                                  name="password"
                                  required 
                                  autocomplete="current-password"
                                  placeholder="••••••••" />
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500 text-sm" />
            </div>

            <!-- Lembrar de Mim -->
            <div class="block mb-6">
                <label for="remember_me" class="inline-flex items-center cursor-pointer">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 checkbox-custom h-4 w-4" name="remember">
                    <span class="ms-2 text-sm font-medium" style="color: var(--cor-escura);">Lembrar de mim</span>
                </label>
            </div>

            <!-- Botão Entrar -->
            <div class="mt-4">
                <button type="submit" class="w-full flex items-center justify-center btn-login shadow-sm">
                    Entrar <i class="bi bi-box-arrow-in-right ms-2 text-lg"></i>
                </button>
            </div>
            
        </form>
    </div>
</x-guest-layout>