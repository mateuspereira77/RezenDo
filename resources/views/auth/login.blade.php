<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - RezenDo</title>
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

        <!-- Card de Login -->
        <div class="main-card-bg rounded-lg shadow-md p-4 sm:p-6 mb-6">
            <h2 class="text-xl sm:text-2xl font-semibold mb-6 text-gray-700 text-center">Entrar</h2>
            
            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <ul class="list-disc list-inside text-sm text-red-600">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

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
                        autofocus
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
                        placeholder="Sua senha"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none custom-focus @error('password') border-red-500 @enderror"
                    >
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center">
                    <input 
                        type="checkbox" 
                        id="remember" 
                        name="remember"
                        class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
                    >
                    <label for="remember" class="ml-2 block text-sm text-gray-700">
                        Lembrar-me
                    </label>
                </div>

                <button 
                    type="submit"
                    class="w-full bg-gradient-to-r from-[#fb9e0b] to-[#fc6c04] text-white font-semibold py-2 px-4 rounded-lg hover:opacity-90 transition-opacity shadow-md"
                >
                    Entrar
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Não tem uma conta?
                    <a href="{{ route('register') }}" class="text-[#fb9e0b] hover:text-[#fc6c04] font-medium">
                        Cadastre-se
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

