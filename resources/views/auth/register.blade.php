<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Cadastro - RezenDo</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="custom-bg min-h-screen">
    <div class="container mx-auto px-3 sm:px-4 py-4 sm:py-8 max-w-md">
        <!-- Header com Post-its Decorativos -->
        <div class="header-with-postits mb-8">
            <div class="header-postits-bg">
                <div class="decorative-postit postit-1"></div>
                <div class="decorative-postit postit-2"></div>
                <div class="decorative-postit postit-3"></div>
                <div class="decorative-postit postit-4"></div>
                <div class="decorative-postit postit-5"></div>
                <div class="decorative-postit postit-6"></div>
            </div>
            <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-center text-gray-800 relative z-10">
                <span style="color: #fb9e0b;">Rezen</span><span style="color: #fbe20d;">Do</span>
            </h1>
        </div>

        <!-- Card de Cadastro -->
        <div class="main-card-bg rounded-lg shadow-md p-4 sm:p-6 mb-6">
            <h2 class="text-xl sm:text-2xl font-semibold mb-6 text-gray-700 text-center">Criar Conta</h2>
            
            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <ul class="list-disc list-inside text-sm text-red-600">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nome
                    </label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name"
                        value="{{ old('name') }}"
                        required
                        autofocus
                        placeholder="Seu nome completo"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none custom-focus @error('name') border-red-500 @enderror"
                    >
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email"
                        value="{{ old('email') }}"
                        required
                        placeholder="seu@email.com"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none custom-focus @error('email') border-red-500 @enderror"
                    >
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Senha
                    </label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password"
                        required
                        placeholder="Mínimo 8 caracteres"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none custom-focus @error('password') border-red-500 @enderror"
                    >
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        Confirmar Senha
                    </label>
                    <input 
                        type="password" 
                        id="password_confirmation" 
                        name="password_confirmation"
                        required
                        placeholder="Digite a senha novamente"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none custom-focus"
                    >
                </div>

                <button 
                    type="submit"
                    class="w-full bg-gradient-to-r from-[#fb9e0b] to-[#fc6c04] text-white font-semibold py-2 px-4 rounded-lg hover:opacity-90 transition-opacity shadow-md"
                >
                    Cadastrar
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Já tem uma conta?
                    <a href="{{ route('login') }}" class="text-[#fb9e0b] hover:text-[#fc6c04] font-medium">
                        Faça login
                    </a>
                </p>
            </div>
        </div>

        <div class="text-center">
            <a href="{{ route('todos.index') }}" class="text-sm text-gray-600 hover:text-gray-800">
                ← Voltar para a página inicial
            </a>
        </div>
    </div>
</body>
</html>

