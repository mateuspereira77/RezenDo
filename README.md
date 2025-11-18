# 📝 RezenDo - Sistema de Gerenciamento de Tarefas

<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="200" alt="Laravel Logo">
</p>

<p align="center">
  <strong>Uma aplicação moderna e intuitiva para gerenciar suas tarefas do dia a dia</strong>
</p>

## 📋 Sobre o RezenDo

O **RezenDo** é uma aplicação web desenvolvida para ajudar você a organizar e gerenciar suas tarefas de forma eficiente e visualmente atraente. Com uma interface inspirada em post-its coloridos, o aplicativo oferece uma experiência única e agradável para criar, editar e acompanhar suas tarefas. O sistema inclui um calendário completo com visualização mensal e semanal, destaque de feriados, e sistema de notificações automáticas para nunca perder um prazo importante.

## 🚀 Tecnologias Utilizadas

### Backend
- **Laravel 12** - Framework PHP moderno e robusto
- **PHP 8.4.14** - Linguagem de programação
- **MySQL/PostgreSQL** - Banco de dados relacional

### Frontend
- **Tailwind CSS** - Framework CSS utilitário para design responsivo
- **JavaScript (Vanilla)** - Para interatividade e manipulação do DOM
- **Axios** - Cliente HTTP para requisições AJAX
- **Vite** - Build tool moderna e rápida

### Ferramentas de Desenvolvimento
- **Laravel Pint** - Code formatter para PHP
- **Laravel Sail** - Ambiente de desenvolvimento Docker
- **Pest PHP v4** - Framework de testes moderno
- **PHPUnit v12** - Framework de testes unitários

## 📦 Requisitos e Instalação

### Pré-requisitos

- PHP >= 8.4.14
- Composer
- Node.js >= 18.x e npm
- MySQL/PostgreSQL ou SQLite
- Git

### Passos para Instalação

1. **Clone o repositório**
   ```bash
   git clone <url-do-repositorio>
   cd RezenDo
   ```

2. **Instale as dependências do PHP**
   ```bash
   composer install
   ```

3. **Instale as dependências do Node.js**
   ```bash
   npm install
   ```

4. **Configure o arquivo de ambiente**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure o banco de dados no arquivo `.env`**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=rezendo
   DB_USERNAME=seu_usuario
   DB_PASSWORD=sua_senha
   ```

6. **Execute as migrações**
   ```bash
   php artisan migrate
   ```

7. **Compile os assets**
   ```bash
   npm run build
   # ou para desenvolvimento com hot reload:
   npm run dev
   ```

8. **Inicie o servidor de desenvolvimento**
   ```bash
   php artisan serve
   ```

   A aplicação estará disponível em `http://localhost:8000`

### Usando Laravel Sail (Docker)

Se preferir usar Docker:

```bash
./vendor/bin/sail up -d
./vendor/bin/sail composer install
./vendor/bin/sail npm install
./vendor/bin/sail artisan migrate
./vendor/bin/sail npm run build
```

## ✨ Funcionalidades

### Funcionalidades Atuais

- ✅ **Criação de Tarefas**
  - Título (até 200 caracteres)
  - Descrição opcional (até 500 caracteres)
  - Sistema de prioridades (Simples, Média, Urgente)
  - Data opcional com suporte a formato brasileiro (DD/MM/AAAA ou DD/MM/AA)
  - Contadores de caracteres em tempo real

- ✅ **Gerenciamento de Tarefas**
  - Visualização de todas as tarefas em formato de post-its coloridos
  - Edição completa de tarefas
  - Exclusão de tarefas individuais
  - Marcar tarefas como concluídas/pendentes
  - Alteração de prioridade

- ✅ **Filtros e Organização**
  - Filtrar por: Todas, A Concluir, Concluídas
  - Estatísticas de tarefas (total, pendentes, concluídas)
  - Exclusão em lote de tarefas concluídas

- ✅ **Interface Moderna**
  - Design inspirado em post-its com rotação aleatória
  - Cores diferentes para cada nível de prioridade
  - Layout responsivo para mobile e desktop
  - Animações suaves e transições
  - Notificações toast personalizadas

- ✅ **Validação e Segurança**
  - Validação de datas no formato brasileiro
  - Validação de caracteres máximos
  - Proteção CSRF
  - Sanitização de dados
  - Filtro de palavras inadequadas (validação de profanidade)

- ✅ **Calendário de Tarefas**
  - Visualização mensal e semanal
  - Navegação entre meses e anos
  - Integração completa com datas e prazos
  - Destaque visual de feriados nacionais e do Rio de Janeiro
  - Identificação automática de feriados móveis (Carnaval, Páscoa, Corpus Christi)
  - Concluir e editar tarefas diretamente no calendário
  - Modal com tarefas do dia ao clicar em uma data
  - Lembretes e notificações automáticas do navegador
  - Notificações para tarefas pendentes do dia atual
  - Lembretes de tarefas do dia seguinte (após 18h)

- ✅ **Sistema de Comentários**
  - Comentários em tarefas com suporte a respostas aninhadas
  - Edição e exclusão de comentários próprios
  - Ordenação inteligente: posts mais recentemente comentados aparecem no topo
  - Interface limpa com comentários exibidos verticalmente (sem indentação visual)
  - Indicador visual discreto para identificar respostas
  - Contador de caracteres em tempo real (máximo 1000 caracteres)
  - Permissões: apenas o autor pode editar, autor ou dono da tarefa podem excluir
  - **Sistema de Menções (@usuario)**
    - Menções de usuários em comentários usando @nome
    - Dropdown de sugestões ao digitar @
    - Suporte a nomes compostos
    - Notificações automáticas para usuários mencionados
    - Destaque visual de menções nos comentários

- ✅ **Sistema de Autenticação**
  - Login e registro de usuários
  - Proteção de rotas com middleware de autenticação
  - Sessões seguras e gerenciamento de autenticação
  - Cada usuário possui suas próprias tarefas e comentários

- ✅ **Sistema de Notificações**
  - Notificações em tempo real no navegador
  - Contador de notificações não lidas
  - Dropdown de notificações com histórico
  - Marcar notificações como lidas individualmente ou em lote
  - **Notificações de Atribuição**
    - Notificações automáticas quando uma tarefa é atribuída a você
    - Notificações quando uma tarefa atribuída a você é concluída
  - **Notificações de Comentários**
    - Notificação quando alguém comenta na sua tarefa (mesmo sem menção)
    - Notificação quando você é mencionado em um comentário (@usuario)
  - **Notificações de Compartilhamento**
    - Notificação quando uma tarefa é compartilhada com você
    - Notificação quando o dono edita uma tarefa compartilhada com você
    - Notificação quando outro colaborador edita uma tarefa compartilhada

- ✅ **Colaboração e Compartilhamento**
  - Compartilhamento de tarefas entre usuários
  - Atribuição de responsáveis para tarefas
  - Visualização de tarefas compartilhadas e atribuídas
  - Gerenciamento de permissões de compartilhamento (visualizar ou editar)
  - Notificações automáticas ao compartilhar tarefas
  - Notificações quando tarefas compartilhadas são editadas
  - Sistema completo de rastreamento de alterações em tarefas compartilhadas

- ✅ **Interface e UX Melhoradas**
  - Modal de confirmação de exclusão estilizado (substituindo alertas padrão)
  - Design moderno e intuitivo para todas as ações de confirmação
  - Feedback visual claro para todas as ações do usuário
  - Manutenção de estado ao editar tarefas (prioridade, data, etc.)

## 🔮 Funcionalidades Futuras

### Planejadas para Implementação

- 👥 **Colaboração Avançada**
  - Permissões granulares de compartilhamento (visualizar, editar, excluir)

- 🏷️ **Tags e Categorias**
  - Sistema de tags personalizadas
  - Categorização de tarefas
  - Filtros por tags

- 📊 **Relatórios e Estatísticas**
  - Dashboard com gráficos de produtividade
  - Relatórios de conclusão de tarefas
  - Análise de tempo gasto por tarefa

- 🔔 **Notificações Avançadas**
  - Notificações por email
  - Lembretes de prazos personalizados
  - Configuração de horários de notificação

- 🔍 **Busca Avançada**
  - Busca por texto, data, prioridade
  - Filtros combinados
  - Histórico de tarefas

- 📱 **Aplicativo Mobile**
  - Versão PWA (Progressive Web App)
  - Aplicativo nativo para iOS e Android

- 🌐 **Multi-idioma**
  - Suporte a múltiplos idiomas
  - Localização completa

- 💾 **Exportação e Importação**
  - Exportar tarefas para PDF, CSV, JSON
  - Importar tarefas de outros sistemas
  - Backup automático

- 🎨 **Personalização**
  - Temas personalizados
  - Cores customizáveis
  - Layouts alternativos

## 🧪 Testes

Execute os testes com:

```bash
php artisan test
```

Ou usando Pest:

```bash
./vendor/bin/pest
```

## 📝 Formatação de Código

O projeto utiliza Laravel Pint para formatação automática:

```bash
vendor/bin/pint
```

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

## 👨‍💻 Desenvolvido por Mateus Pereira

Desenvolvido utilizando as melhores práticas do ecossistema Laravel e tecnologias modernas de frontend.

---

<p align="center">Feito com Laravel e muito ☕</p>
