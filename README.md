# 📝 RezenDo - Sistema de Gerenciamento de Tarefas

<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="200" alt="Laravel Logo">
</p>

<p align="center">
  <strong>Uma aplicação moderna e intuitiva para gerenciar suas tarefas do dia a dia</strong>
</p>

## 📋 Sobre o RezenDo

O **RezenDo** é uma aplicação web desenvolvida para ajudar você a organizar e gerenciar suas tarefas de forma eficiente e visualmente atraente. Com uma interface inspirada em post-its coloridos, o aplicativo oferece uma experiência única e agradável para criar, editar e acompanhar suas tarefas.

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

## 🔮 Funcionalidades Futuras

### Planejadas para Implementação

- 📅 **Calendário de Tarefas**
  - Visualização mensal e semanal
  - Integração com datas e prazos
  - Lembretes e notificações

- 👥 **Colaboração**
  - Compartilhamento de tarefas entre usuários
  - Comentários em tarefas
  - Atribuição de responsáveis

- 🏷️ **Tags e Categorias**
  - Sistema de tags personalizadas
  - Categorização de tarefas
  - Filtros por tags

- 📊 **Relatórios e Estatísticas**
  - Dashboard com gráficos de produtividade
  - Relatórios de conclusão de tarefas
  - Análise de tempo gasto por tarefa

- 🔔 **Notificações**
  - Notificações por email
  - Lembretes de prazos
  - Notificações push no navegador

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

## 🤝 Contribuindo

Contribuições são bem-vindas! Sinta-se à vontade para:

1. Fazer um Fork do projeto
2. Criar uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abrir um Pull Request

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

## 👨‍💻 Desenvolvido com ❤️

Desenvolvido utilizando as melhores práticas do ecossistema Laravel e tecnologias modernas de frontend.

---

<p align="center">Feito com Laravel e muito ☕</p>
