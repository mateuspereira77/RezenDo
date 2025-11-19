<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ajuda - RezenDo</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="custom-bg min-h-screen">
<div class="container mx-auto px-3 sm:px-4 py-4 sm:py-8 max-w-6xl">
    <!-- Header -->
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

    <!-- Menu de Navegação -->
    <div class="flex justify-end mb-4">
        @auth
            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-700">Olá, <strong>{{ Auth::user()->name }}</strong></span>
                <a href="{{ route('todos.index') }}" class="text-sm text-[#fb9e0b] hover:text-[#fc6c04] font-medium transition-colors">Dashboard</a>
            </div>
        @else
            <div class="flex items-center gap-4">
                <a href="{{ route('login') }}" class="text-sm text-gray-700 hover:text-[#fb9e0b] font-medium transition-colors">Entrar</a>
                <a href="{{ route('register') }}" class="text-sm text-[#fb9e0b] hover:text-[#fc6c04] font-medium transition-colors">Cadastrar</a>
            </div>
        @endauth
    </div>

<div class="min-h-screen bg-gradient-to-br from-orange-50 to-yellow-50 py-8 px-4 sm:px-6 lg:px-8 -mx-4 sm:-mx-6 rounded-lg">
    <div class="max-w-5xl mx-auto">
        <!-- Cabeçalho -->
        <div class="text-center mb-12">
            <h1 class="text-4xl sm:text-5xl font-bold text-gray-800 mb-4">
                📚 Guia de Uso - RezenDo
            </h1>
            <p class="text-xl text-gray-600">
                Aprenda a usar todas as funcionalidades da sua aplicação de gerenciamento de tarefas
            </p>
        </div>

        <!-- Menu de Navegação Rápida -->
        <div class="main-card-bg rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">📑 Índice</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                <a href="#basico" class="text-[#fb9e0b] hover:text-[#fc6c04] hover:underline transition-colors">1. Guia Básico</a>
                <a href="#criar-tarefa" class="text-[#fb9e0b] hover:text-[#fc6c04] hover:underline transition-colors">2. Criar Tarefas</a>
                <a href="#editar-tarefa" class="text-[#fb9e0b] hover:text-[#fc6c04] hover:underline transition-colors">3. Editar Tarefas</a>
                <a href="#calendario" class="text-[#fb9e0b] hover:text-[#fc6c04] hover:underline transition-colors">4. Calendário</a>
                <a href="#compartilhar" class="text-[#fb9e0b] hover:text-[#fc6c04] hover:underline transition-colors">5. Compartilhar</a>
                <a href="#comentarios" class="text-[#fb9e0b] hover:text-[#fc6c04] hover:underline transition-colors">6. Comentários</a>
                <a href="#historico" class="text-[#fb9e0b] hover:text-[#fc6c04] hover:underline transition-colors">7. Histórico</a>
                <a href="#produtividade" class="text-[#fb9e0b] hover:text-[#fc6c04] hover:underline transition-colors">8. Produtividade</a>
                <a href="#dicas" class="text-[#fb9e0b] hover:text-[#fc6c04] hover:underline transition-colors">9. Dicas & Truques</a>
            </div>
        </div>

        <!-- Seção 1: Guia Básico -->
        <section id="basico" class="main-card-bg rounded-lg shadow-md p-6 sm:p-8 mb-8 scroll-mt-8">
            <div class="flex items-center gap-3 mb-6">
                <span class="text-4xl">🚀</span>
                <h2 class="text-3xl font-bold text-gray-800">1. Guia Básico</h2>
            </div>
            
            <div class="space-y-6">
                <div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-3">O que é o RezenDo?</h3>
                    <p class="text-gray-600 leading-relaxed mb-4">
                        O RezenDo é uma aplicação moderna para gerenciar suas tarefas do dia a dia. Com uma interface inspirada em post-its coloridos, você pode criar, organizar e acompanhar suas tarefas de forma visual e intuitiva.
                    </p>
                    
                    <!-- Imagem do dashboard -->
                    <div class="mb-4 text-center">
                        @php
                            $imagePath = public_path('images/ajuda/dashboard-principal.png');
                            $imageExists = file_exists($imagePath);
                        @endphp
                        @if($imageExists)
                            <img src="{{ asset('images/ajuda/dashboard-principal.png') }}" 
                                 alt="Dashboard Principal" 
                                 class="max-w-full h-auto rounded-lg shadow-lg mx-auto">
                        @else
                            <div class="bg-gray-100 rounded-lg p-8 border-2 border-dashed border-gray-300 text-center">
                                <p class="text-gray-500 mb-2">📸 <strong>Imagem: Dashboard Principal</strong></p>
                                <p class="text-sm text-gray-400">
                                    Adicione uma screenshot do dashboard principal aqui<br>
                                    <code class="text-xs">public/images/ajuda/dashboard-principal.png</code>
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                <div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-3">Navegação Principal</h3>
                    <ul class="list-disc list-inside space-y-2 text-gray-600">
                        <li><strong>Minhas Tarefas:</strong> Visualize e gerencie todas as suas tarefas</li>
                        <li><strong>Calendário:</strong> Veja suas tarefas organizadas por data</li>
                        <li><strong>Meu Histórico:</strong> Acesse tarefas que foram deletadas</li>
                        <li><strong>Minha Produtividade:</strong> Acompanhe suas estatísticas e gráficos</li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- Seção 2: Criar Tarefas -->
        <section id="criar-tarefa" class="main-card-bg rounded-lg shadow-md p-6 sm:p-8 mb-8 scroll-mt-8">
            <div class="flex items-center gap-3 mb-6">
                <span class="text-4xl">➕</span>
                <h2 class="text-3xl font-bold text-gray-800">2. Criar Tarefas</h2>
            </div>
            
            <div class="space-y-6">
                <div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-3">Como criar uma nova tarefa</h3>
                    <ol class="list-decimal list-inside space-y-3 text-gray-600">
                        <li>Acesse a página inicial ou "Minhas Tarefas"</li>
                        <li>Preencha o <strong>Título da Tarefa</strong> (obrigatório, máximo 200 caracteres)</li>
                        <li>Adicione uma <strong>Descrição</strong> (opcional, máximo 500 caracteres)</li>
                        <li>Selecione a <strong>Prioridade</strong>: Simples, Média ou Urgente</li>
                        <li>Defina a <strong>Data de Início</strong> (opcional) no formato DD/MM/AAAA</li>
                        <li>Defina a <strong>Data de Término</strong> (opcional) - deve ser posterior ou igual à data de início</li>
                        <li>Clique em <strong>"Adicionar Tarefa"</strong></li>
                    </ol>
                </div>

                <!-- Imagem do formulário -->
                <div class="mb-4 text-center">
                    @php
                        $imagePath = public_path('images/ajuda/formulario-criacao.png');
                        $imageExists = file_exists($imagePath);
                    @endphp
                    @if($imageExists)
                        <img src="{{ asset('images/ajuda/formulario-criacao.png') }}" 
                             alt="Formulário de Criação" 
                             class="max-w-full h-auto rounded-lg shadow-lg mx-auto">
                    @else
                        <div class="bg-gray-100 rounded-lg p-8 border-2 border-dashed border-gray-300 text-center">
                            <p class="text-gray-500 mb-2">📸 <strong>Imagem: Formulário de Criação</strong></p>
                            <p class="text-sm text-gray-400">
                                Adicione uma screenshot do formulário de criação de tarefa aqui<br>
                                <code class="text-xs">public/images/ajuda/formulario-criacao.png</code>
                            </p>
                        </div>
                    @endif
                </div>

                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                    <p class="text-blue-800">
                        <strong>💡 Dica:</strong> Use a data de término para definir prazos importantes. Tarefas com data aparecerão no calendário automaticamente!
                    </p>
                </div>
            </div>
        </section>

        <!-- Seção 3: Editar Tarefas -->
        <section id="editar-tarefa" class="main-card-bg rounded-lg shadow-md p-6 sm:p-8 mb-8 scroll-mt-8">
            <div class="flex items-center gap-3 mb-6">
                <span class="text-4xl">✏️</span>
                <h2 class="text-3xl font-bold text-gray-800">3. Editar Tarefas</h2>
            </div>
            
            <div class="space-y-6">
                <div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-3">Como editar uma tarefa</h3>
                    <p class="text-gray-600 mb-4">Existem duas formas de editar uma tarefa:</p>
                    
                    <div class="space-y-4">
                        <div>
                            <h4 class="font-semibold text-gray-700 mb-2">📝 Método 1: Modal Rápido</h4>
                            <ol class="list-decimal list-inside space-y-2 text-gray-600 ml-4">
                                <li>Na lista de tarefas, clique no botão <strong>"✏️ Editar"</strong></li>
                                <li>Um modal será aberto com o formulário de edição</li>
                                <li>Faça as alterações desejadas</li>
                                <li>Clique em <strong>"Salvar Alterações"</strong></li>
                            </ol>
                        </div>

                        <div>
                            <h4 class="font-semibold text-gray-700 mb-2">🌐 Método 2: Página Completa</h4>
                            <ol class="list-decimal list-inside space-y-2 text-gray-600 ml-4">
                                <li>Clique no título da tarefa para abrir a visualização completa</li>
                                <li>Na página de detalhes, clique em <strong>"Editar"</strong></li>
                                <li>Faça as alterações na página de edição</li>
                                <li>Clique em <strong>"Salvar"</strong></li>
                            </ol>
                        </div>
                    </div>
                </div>

                <!-- Imagem do modal de edição -->
                <div class="mb-4 text-center">
                    @php
                        $imagePath = public_path('images/ajuda/modal-edicao.png');
                        $imageExists = file_exists($imagePath);
                    @endphp
                    @if($imageExists)
                        <img src="{{ asset('images/ajuda/modal-edicao.png') }}" 
                             alt="Modal de Edição" 
                             class="max-w-full h-auto rounded-lg shadow-lg mx-auto">
                    @else
                        <div class="bg-gray-100 rounded-lg p-8 border-2 border-dashed border-gray-300 text-center">
                            <p class="text-gray-500 mb-2">📸 <strong>Imagem: Modal de Edição</strong></p>
                            <p class="text-sm text-gray-400">
                                Adicione uma screenshot do modal de edição aqui<br>
                                <code class="text-xs">public/images/ajuda/modal-edicao.png</code>
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        <!-- Seção 4: Calendário -->
        <section id="calendario" class="main-card-bg rounded-lg shadow-md p-6 sm:p-8 mb-8 scroll-mt-8">
            <div class="flex items-center gap-3 mb-6">
                <span class="text-4xl">📅</span>
                <h2 class="text-3xl font-bold text-gray-800">4. Calendário</h2>
            </div>
            
            <div class="space-y-6">
                <div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-3">Visualizações do Calendário</h3>
                    <p class="text-gray-600 mb-4">O calendário oferece duas formas de visualização:</p>
                    
                    <div class="space-y-4">
                        <div>
                            <h4 class="font-semibold text-gray-700 mb-2">📆 Visualização Mensal</h4>
                            <ul class="list-disc list-inside space-y-2 text-gray-600 ml-4">
                                <li>Veja todas as tarefas do mês de uma vez</li>
                                <li>Feriados são destacados automaticamente</li>
                                <li>Clique em um dia para ver as tarefas daquele dia</li>
                                <li>Use as setas para navegar entre os meses</li>
                            </ul>
                        </div>

                        <div>
                            <h4 class="font-semibold text-gray-700 mb-2">📊 Visualização Semanal</h4>
                            <ul class="list-disc list-inside space-y-2 text-gray-600 ml-4">
                                <li>Veja uma semana completa com mais detalhes</li>
                                <li>Use a <strong>barra de rolagem inferior</strong> para navegar entre semanas</li>
                                <li>Passe o mouse sobre a barra para ver informações da semana</li>
                                <li>Visualização ideal para planejamento semanal</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Imagem do calendário -->
                <div class="mb-4 text-center">
                    @php
                        $imagePath = public_path('images/ajuda/calendario-semanal.png');
                        $imageExists = file_exists($imagePath);
                    @endphp
                    @if($imageExists)
                        <img src="{{ asset('images/ajuda/calendario-semanal.png') }}" 
                             alt="Calendário Semanal" 
                             class="max-w-full h-auto rounded-lg shadow-lg mx-auto">
                    @else
                        <div class="bg-gray-100 rounded-lg p-8 border-2 border-dashed border-gray-300 text-center">
                            <p class="text-gray-500 mb-2">📸 <strong>Imagem: Calendário Semanal</strong></p>
                            <p class="text-sm text-gray-400">
                                Adicione uma screenshot do calendário semanal com a barra de rolagem aqui<br>
                                <code class="text-xs">public/images/ajuda/calendario-semanal.png</code>
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        <!-- Seção 5: Compartilhar -->
        <section id="compartilhar" class="main-card-bg rounded-lg shadow-md p-6 sm:p-8 mb-8 scroll-mt-8">
            <div class="flex items-center gap-3 mb-6">
                <span class="text-4xl">👥</span>
                <h2 class="text-3xl font-bold text-gray-800">5. Compartilhar Tarefas</h2>
            </div>
            
            <div class="space-y-6">
                <div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-3">Como compartilhar uma tarefa</h3>
                    <ol class="list-decimal list-inside space-y-3 text-gray-600">
                        <li>Abra a tarefa que deseja compartilhar (clique no título)</li>
                        <li>Na página de detalhes, procure pela seção <strong>"Compartilhar Tarefa"</strong></li>
                        <li>Digite o nome ou email do usuário que deseja compartilhar</li>
                        <li>Selecione o usuário da lista de sugestões</li>
                        <li>Escolha a <strong>permissão</strong>:
                            <ul class="list-disc list-inside ml-6 mt-2 space-y-1">
                                <li><strong>Visualizar:</strong> O usuário pode apenas ver a tarefa</li>
                                <li><strong>Editar:</strong> O usuário pode editar e comentar na tarefa</li>
                            </ul>
                        </li>
                        <li>Clique em <strong>"Compartilhar"</strong></li>
                    </ol>
                </div>

                <!-- Imagem de compartilhamento -->
                <div class="mb-4 text-center">
                    @php
                        $imagePath = public_path('images/ajuda/compartilhar-tarefa.png');
                        $imageExists = file_exists($imagePath);
                    @endphp
                    @if($imageExists)
                        <img src="{{ asset('images/ajuda/compartilhar-tarefa.png') }}" 
                             alt="Compartilhar Tarefa" 
                             class="max-w-full h-auto rounded-lg shadow-lg mx-auto">
                    @else
                        <div class="bg-gray-100 rounded-lg p-8 border-2 border-dashed border-gray-300 text-center">
                            <p class="text-gray-500 mb-2">📸 <strong>Imagem: Compartilhamento</strong></p>
                            <p class="text-sm text-gray-400">
                                Adicione uma screenshot da seção de compartilhamento aqui<br>
                                <code class="text-xs">public/images/ajuda/compartilhar-tarefa.png</code>
                            </p>
                        </div>
                    @endif
                </div>

                <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded">
                    <p class="text-yellow-800">
                        <strong>⚠️ Importante:</strong> Apenas o dono da tarefa pode compartilhá-la. Usuários com permissão de edição podem comentar, mas não podem compartilhar com outros.
                    </p>
                </div>
            </div>
        </section>

        <!-- Seção 6: Comentários -->
        <section id="comentarios" class="main-card-bg rounded-lg shadow-md p-6 sm:p-8 mb-8 scroll-mt-8">
            <div class="flex items-center gap-3 mb-6">
                <span class="text-4xl">💬</span>
                <h2 class="text-3xl font-bold text-gray-800">6. Comentários e Menções</h2>
            </div>
            
            <div class="space-y-6">
                <div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-3">Sistema de Comentários</h3>
                    <p class="text-gray-600 mb-4">Você pode adicionar comentários em qualquer tarefa compartilhada ou atribuída a você:</p>
                    
                    <ul class="list-disc list-inside space-y-2 text-gray-600">
                        <li>Digite seu comentário no campo de texto</li>
                        <li>Use <strong>@nome</strong> para mencionar outros usuários</li>
                        <li>Usuários mencionados receberão notificações</li>
                        <li>Você pode responder comentários (criar threads)</li>
                        <li>Edite ou exclua seus próprios comentários</li>
                    </ul>
                </div>

                <!-- Imagem de comentários -->
                <div class="mb-4 text-center">
                    @php
                        $imagePath = public_path('images/ajuda/comentarios-menciones.png');
                        $imageExists = file_exists($imagePath);
                    @endphp
                    @if($imageExists)
                        <img src="{{ asset('images/ajuda/comentarios-menciones.png') }}" 
                             alt="Comentários e Menções" 
                             class="max-w-full h-auto rounded-lg shadow-lg mx-auto">
                    @else
                        <div class="bg-gray-100 rounded-lg p-8 border-2 border-dashed border-gray-300 text-center">
                            <p class="text-gray-500 mb-2">📸 <strong>Imagem: Sistema de Comentários</strong></p>
                            <p class="text-sm text-gray-400">
                                Adicione uma screenshot mostrando comentários e menções aqui<br>
                                <code class="text-xs">public/images/ajuda/comentarios-menciones.png</code>
                            </p>
                        </div>
                    @endif
                </div>

                <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded">
                    <p class="text-green-800">
                        <strong>💡 Dica:</strong> Use menções (@usuario) para chamar a atenção de alguém específico. Isso é especialmente útil em tarefas compartilhadas com várias pessoas!
                    </p>
                </div>
            </div>
        </section>

        <!-- Seção 7: Histórico -->
        <section id="historico" class="main-card-bg rounded-lg shadow-md p-6 sm:p-8 mb-8 scroll-mt-8">
            <div class="flex items-center gap-3 mb-6">
                <span class="text-4xl">📜</span>
                <h2 class="text-3xl font-bold text-gray-800">7. Histórico de Tarefas</h2>
            </div>
            
            <div class="space-y-6">
                <div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-3">Tarefas Deletadas</h3>
                    <p class="text-gray-600 mb-4">Quando você deleta uma tarefa, ela não é perdida permanentemente. Ela vai para o histórico:</p>
                    
                    <ul class="list-disc list-inside space-y-2 text-gray-600">
                        <li>Acesse <strong>"Meu Histórico"</strong> no menu principal</li>
                        <li>Veja todas as tarefas que foram deletadas</li>
                        <li>Clique em uma tarefa para ver detalhes completos e comentários</li>
                        <li>Como dono, você pode <strong>restaurar</strong> ou <strong>deletar permanentemente</strong></li>
                        <li>Tarefas compartilhadas ou atribuídas a você também aparecem no histórico</li>
                    </ul>
                </div>

                <!-- Imagem do histórico -->
                <div class="mb-4 text-center">
                    @php
                        $imagePath = public_path('images/ajuda/historico-tarefas.png');
                        $imageExists = file_exists($imagePath);
                    @endphp
                    @if($imageExists)
                        <img src="{{ asset('images/ajuda/historico-tarefas.png') }}" 
                             alt="Histórico de Tarefas" 
                             class="max-w-full h-auto rounded-lg shadow-lg mx-auto">
                    @else
                        <div class="bg-gray-100 rounded-lg p-8 border-2 border-dashed border-gray-300 text-center">
                            <p class="text-gray-500 mb-2">📸 <strong>Imagem: Página de Histórico</strong></p>
                            <p class="text-sm text-gray-400">
                                Adicione uma screenshot da página de histórico aqui<br>
                                <code class="text-xs">public/images/ajuda/historico-tarefas.png</code>
                            </p>
                        </div>
                    @endif
                </div>

                <div class="bg-orange-50 border-l-4 border-orange-500 p-4 rounded">
                    <p class="text-orange-800">
                        <strong>⚠️ Atenção:</strong> Apenas o dono da tarefa pode restaurá-la ou deletá-la permanentemente. Outros usuários podem apenas visualizar.
                    </p>
                </div>
            </div>
        </section>

        <!-- Seção 8: Produtividade -->
        <section id="produtividade" class="main-card-bg rounded-lg shadow-md p-6 sm:p-8 mb-8 scroll-mt-8">
            <div class="flex items-center gap-3 mb-6">
                <span class="text-4xl">📊</span>
                <h2 class="text-3xl font-bold text-gray-800">8. Relatórios de Produtividade</h2>
            </div>
            
            <div class="space-y-6">
                <div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-3">Acompanhe suas Estatísticas</h3>
                    <p class="text-gray-600 mb-4">A página "Minha Produtividade" oferece insights valiosos:</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-white p-4 rounded-lg shadow">
                            <h4 class="font-semibold text-gray-700 mb-2">📈 Estatísticas Gerais</h4>
                            <ul class="text-sm text-gray-600 space-y-1">
                                <li>• Total de tarefas</li>
                                <li>• Tarefas concluídas</li>
                                <li>• Tarefas pendentes</li>
                                <li>• Taxa de conclusão</li>
                                <li>• Tarefas atrasadas</li>
                            </ul>
                        </div>
                        
                        <div class="bg-white p-4 rounded-lg shadow">
                            <h4 class="font-semibold text-gray-700 mb-2">📊 Gráficos</h4>
                            <ul class="text-sm text-gray-600 space-y-1">
                                <li>• Timeline de criação/conclusão</li>
                                <li>• Distribuição por prioridade</li>
                                <li>• Produtividade por dia da semana</li>
                                <li>• Conclusão por prioridade</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Imagem de produtividade -->
                <div class="mb-4 text-center">
                    @php
                        $imagePath = public_path('images/ajuda/produtividade.png');
                        $imageExists = file_exists($imagePath);
                    @endphp
                    @if($imageExists)
                        <img src="{{ asset('images/ajuda/produtividade.png') }}" 
                             alt="Produtividade" 
                             class="max-w-full h-auto rounded-lg shadow-lg mx-auto">
                    @else
                        <div class="bg-gray-100 rounded-lg p-8 border-2 border-dashed border-gray-300 text-center">
                            <p class="text-gray-500 mb-2">📸 <strong>Imagem: Página de Produtividade</strong></p>
                            <p class="text-sm text-gray-400">
                                Adicione uma screenshot da página de produtividade com gráficos aqui<br>
                                <code class="text-xs">public/images/ajuda/produtividade.png</code>
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        <!-- Seção 9: Dicas e Truques -->
        <section id="dicas" class="main-card-bg rounded-lg shadow-md p-6 sm:p-8 mb-8 scroll-mt-8">
            <div class="flex items-center gap-3 mb-6">
                <span class="text-4xl">💡</span>
                <h2 class="text-3xl font-bold text-gray-800">9. Dicas & Truques</h2>
            </div>
            
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-5 rounded-lg">
                        <h4 class="font-semibold text-blue-800 mb-2">🎯 Organização</h4>
                        <ul class="text-sm text-blue-700 space-y-1">
                            <li>• Use prioridades para destacar tarefas importantes</li>
                            <li>• Defina datas de início e término para planejamento</li>
                            <li>• Atribua responsáveis para tarefas em equipe</li>
                        </ul>
                    </div>
                    
                    <div class="bg-gradient-to-br from-green-50 to-green-100 p-5 rounded-lg">
                        <h4 class="font-semibold text-green-800 mb-2">👥 Colaboração</h4>
                        <ul class="text-sm text-green-700 space-y-1">
                            <li>• Compartilhe tarefas com permissão adequada</li>
                            <li>• Use menções (@) para notificar pessoas</li>
                            <li>• Comente para manter todos informados</li>
                        </ul>
                    </div>
                    
                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-5 rounded-lg">
                        <h4 class="font-semibold text-purple-800 mb-2">📅 Calendário</h4>
                        <ul class="text-sm text-purple-700 space-y-1">
                            <li>• Use a visualização semanal para planejamento</li>
                            <li>• Navegue entre semanas com a barra de rolagem</li>
                            <li>• Veja feriados destacados automaticamente</li>
                        </ul>
                    </div>
                    
                    <div class="bg-gradient-to-br from-orange-50 to-orange-100 p-5 rounded-lg">
                        <h4 class="font-semibold text-orange-800 mb-2">🔔 Notificações</h4>
                        <ul class="text-sm text-orange-700 space-y-1">
                            <li>• Receba notificações de tarefas atribuídas</li>
                            <li>• Seja notificado quando mencionado</li>
                            <li>• Acompanhe mudanças em tarefas compartilhadas</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ -->
        <section class="main-card-bg rounded-lg shadow-md p-6 sm:p-8 mb-8">
            <div class="flex items-center gap-3 mb-6">
                <span class="text-4xl">❓</span>
                <h2 class="text-3xl font-bold text-gray-800">Perguntas Frequentes (FAQ)</h2>
            </div>
            
            <div class="space-y-4">
                <div class="border-l-4 border-[#fb9e0b] pl-4">
                    <h4 class="font-semibold text-gray-700 mb-2">Posso deletar uma tarefa permanentemente?</h4>
                    <p class="text-gray-600">Sim! Apenas o dono da tarefa pode deletá-la permanentemente do histórico. Isso remove a tarefa completamente do sistema.</p>
                </div>
                
                <div class="border-l-4 border-[#fb9e0b] pl-4">
                    <h4 class="font-semibold text-gray-700 mb-2">Como funcionam as permissões de compartilhamento?</h4>
                    <p class="text-gray-600">
                        <strong>Visualizar:</strong> O usuário pode apenas ver a tarefa e seus comentários.<br>
                        <strong>Editar:</strong> O usuário pode editar a tarefa, adicionar comentários e mencionar outros usuários.
                    </p>
                </div>
                
                <div class="border-l-4 border-[#fb9e0b] pl-4">
                    <h4 class="font-semibold text-gray-700 mb-2">Posso editar tarefas que não criei?</h4>
                    <p class="text-gray-600">Apenas se você tiver permissão de "Editar" concedida pelo dono da tarefa, ou se você for o responsável atribuído.</p>
                </div>
                
                <div class="border-l-4 border-[#fb9e0b] pl-4">
                    <h4 class="font-semibold text-gray-700 mb-2">As tarefas deletadas aparecem no calendário?</h4>
                    <p class="text-gray-600">Não. Tarefas deletadas só aparecem no histórico. Elas são removidas do calendário e da lista de tarefas ativas.</p>
                </div>
                
                <div class="border-l-4 border-[#fb9e0b] pl-4">
                    <h4 class="font-semibold text-gray-700 mb-2">Como funciona a data de término?</h4>
                    <p class="text-gray-600">A data de término é opcional e deve ser posterior ou igual à data de início. Use para definir prazos importantes para suas tarefas.</p>
                </div>
            </div>
        </section>

        <!-- Rodapé -->
        <div class="text-center mt-12 mb-8">
            <a href="{{ route('todos.index') }}" 
               class="inline-block custom-btn-primary px-8 py-3 rounded-lg font-semibold transition-colors">
                ← Voltar para o Dashboard
            </a>
        </div>
    </div>
</div>
</div>

<style>
    /* Smooth scroll para navegação */
    html {
        scroll-behavior: smooth;
    }
    
    /* Estilo para imagens quando carregadas */
    img[src*="ajuda"] {
        max-width: 100%;
        height: auto;
        border-radius: 0.5rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
</style>
</body>
</html>

