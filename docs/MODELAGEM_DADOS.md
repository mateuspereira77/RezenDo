# 📊 Modelagem de Dados - RezenDo

## Visão Geral

O RezenDo é um sistema de gerenciamento de tarefas com suporte a multi-usuário, compartilhamento, comentários, menções e notificações. A modelagem atual prioriza simplicidade, performance e colaboração.

---

## 📋 Entidades

### 1. **users** (Usuários)

Tabela padrão do Laravel para autenticação de usuários.

#### Estrutura:

| Campo | Tipo | Descrição | Constraints |
|-------|------|-----------|-------------|
| `id` | bigint (PK) | Identificador único | Auto-incremento |
| `name` | string(255) | Nome do usuário | Obrigatório |
| `email` | string(255) | Email do usuário | Obrigatório, único |
| `email_verified_at` | timestamp | Data de verificação do email | Nullable |
| `password` | string(255) | Senha criptografada | Obrigatório |
| `remember_token` | string(100) | Token de "lembrar-me" | Nullable |
| `created_at` | timestamp | Data de criação | Automático |
| `updated_at` | timestamp | Data de atualização | Automático |

#### Índices:
- `email` (UNIQUE)

#### Relacionamentos:
- `hasMany` Todo (tarefas criadas pelo usuário)
- `hasMany` Comment (comentários feitos pelo usuário)
- `belongsToMany` Todo (tarefas compartilhadas via `todo_user`)
- `belongsToMany` Comment (comentários mencionados via `comment_mentions`)
- `hasMany` Notification (notificações recebidas)

---

### 2. **todos** (Tarefas)

Tabela principal do sistema, armazena todas as tarefas criadas.

#### Estrutura:

| Campo | Tipo | Descrição | Constraints |
|-------|------|-----------|-------------|
| `id` | bigint (PK) | Identificador único | Auto-incremento |
| `user_id` | bigint (FK) | ID do usuário dono | Nullable, CASCADE DELETE |
| `assigned_to` | bigint (FK) | ID do usuário responsável | Nullable, SET NULL DELETE |
| `text` | string(200) | Título da tarefa | Obrigatório, máx 200 caracteres |
| `description` | text | Descrição detalhada | Nullable, máx 500 caracteres |
| `completed` | boolean | Status de conclusão | Default: false |
| `priority` | enum | Nível de prioridade | Values: 'simple', 'medium', 'urgent', Default: 'simple' |
| `day` | string(255) | Dia da semana (legado) | Nullable |
| `date` | date | Data específica da tarefa | Nullable |
| `created_at` | timestamp | Data de criação | Automático |
| `updated_at` | timestamp | Data de atualização | Automático |

#### Índices:
- `priority` (para ordenação)
- `completed` (para filtros)
- `date` (para ordenação por data)
- `user_id` (para relacionamento com usuários)
- `assigned_to` (para filtros por responsável)
- `created_at` (para ordenação)

#### Validações:
- `text`: obrigatório, máximo 200 caracteres
- `description`: opcional, máximo 500 caracteres
- `priority`: deve ser 'simple', 'medium' ou 'urgent'
- `date`: formato válido (YYYY-MM-DD) ou DD/MM/YYYY ou DD/MM/YY

#### Relacionamentos:
- `belongsTo` User (dono da tarefa)
- `belongsTo` User (responsável atribuído, via `assigned_to`)
- `belongsToMany` User (usuários com acesso compartilhado via `todo_user`)
- `hasMany` Comment (comentários da tarefa)

---

### 3. **todo_user** (Compartilhamento de Tarefas)

Tabela pivot que gerencia o compartilhamento de tarefas entre usuários.

#### Estrutura:

| Campo | Tipo | Descrição | Constraints |
|-------|------|-----------|-------------|
| `id` | bigint (PK) | Identificador único | Auto-incremento |
| `todo_id` | bigint (FK) | ID da tarefa compartilhada | Obrigatório, CASCADE DELETE |
| `user_id` | bigint (FK) | ID do usuário com acesso | Obrigatório, CASCADE DELETE |
| `permission` | enum | Nível de permissão | Values: 'read', 'write', Default: 'read' |
| `created_at` | timestamp | Data de criação | Automático |
| `updated_at` | timestamp | Data de atualização | Automático |

#### Índices:
- `todo_id` (para consultas por tarefa)
- `user_id` (para consultas por usuário)
- `[todo_id, user_id]` (UNIQUE - evita duplicatas)

#### Regras de Negócio:
- **read**: Usuário pode apenas visualizar a tarefa
- **write**: Usuário pode visualizar e editar a tarefa
- Um usuário não pode ter a mesma tarefa compartilhada duas vezes

---

### 4. **comments** (Comentários)

Tabela que armazena comentários feitos nas tarefas, com suporte a respostas aninhadas.

#### Estrutura:

| Campo | Tipo | Descrição | Constraints |
|-------|------|-----------|-------------|
| `id` | bigint (PK) | Identificador único | Auto-incremento |
| `parent_id` | bigint (FK) | ID do comentário pai | Nullable, CASCADE DELETE |
| `todo_id` | bigint (FK) | ID da tarefa | Obrigatório, CASCADE DELETE |
| `user_id` | bigint (FK) | ID do autor do comentário | Obrigatório, CASCADE DELETE |
| `content` | text | Conteúdo do comentário | Obrigatório |
| `created_at` | timestamp | Data de criação | Automático |
| `updated_at` | timestamp | Data de atualização | Automático |

#### Índices:
- `parent_id` (para consultas de respostas)
- `todo_id` (para consultas por tarefa)
- `user_id` (para consultas por usuário)
- `created_at` (para ordenação)

#### Relacionamentos:
- `belongsTo` Todo (tarefa do comentário)
- `belongsTo` User (autor do comentário)
- `belongsTo` Comment (comentário pai, para respostas)
- `hasMany` Comment (respostas do comentário)
- `belongsToMany` User (usuários mencionados via `comment_mentions`)

#### Regras de Negócio:
- Comentários são ordenados por `last_activity_at` (calculado recursivamente)
- Respostas são exibidas em layout empilhado (sem indentação visual)
- Comentários podem mencionar usuários usando `@nome`

---

### 5. **comment_mentions** (Menções em Comentários)

Tabela pivot que relaciona comentários com usuários mencionados.

#### Estrutura:

| Campo | Tipo | Descrição | Constraints |
|-------|------|-----------|-------------|
| `id` | bigint (PK) | Identificador único | Auto-incremento |
| `comment_id` | bigint (FK) | ID do comentário | Obrigatório, CASCADE DELETE |
| `user_id` | bigint (FK) | ID do usuário mencionado | Obrigatório, CASCADE DELETE |
| `created_at` | timestamp | Data de criação | Automático |
| `updated_at` | timestamp | Data de atualização | Automático |

#### Índices:
- `comment_id` (para consultas por comentário)
- `user_id` (para consultas por usuário)

#### Regras de Negócio:
- Usuários mencionados recebem notificações
- Menções são detectadas via regex no conteúdo do comentário
- Suporta nomes compostos (ex: "Mateus Pereira")

---

### 6. **notifications** (Notificações)

Tabela padrão do Laravel para armazenar notificações do sistema.

#### Estrutura:

| Campo | Tipo | Descrição | Constraints |
|-------|------|-----------|-------------|
| `id` | uuid (PK) | Identificador único | UUID |
| `type` | string | Tipo da notificação | Obrigatório |
| `notifiable_type` | string | Tipo do modelo notificável | Obrigatório |
| `notifiable_id` | bigint | ID do modelo notificável | Obrigatório |
| `data` | text | Dados da notificação (JSON) | Obrigatório |
| `read_at` | timestamp | Data de leitura | Nullable |
| `created_at` | timestamp | Data de criação | Automático |
| `updated_at` | timestamp | Data de atualização | Automático |

#### Tipos de Notificações:
- `CommentMentionedNotification`: Usuário foi mencionado em um comentário
- `TodoCommentedNotification`: Tarefa do usuário recebeu um comentário
- `TodoSharedNotification`: Tarefa foi compartilhada com o usuário
- `SharedTodoEditedNotification`: Tarefa compartilhada foi editada por outro usuário
- `TodoOwnerEditedNotification`: Tarefa compartilhada foi editada pelo dono
- `TodoAssignedNotification`: Tarefa foi atribuída ao usuário
- `TodoCompletedNotification`: Tarefa atribuída foi concluída

---

## 🔄 Diagrama de Relacionamentos

```
┌─────────────┐
│    users    │
├─────────────┤
│ id (PK)     │
│ name        │
│ email       │
│ password    │
│ ...         │
└─────────────┘
      │
      │ (hasMany)
      │
      ▼
┌─────────────┐         ┌──────────────┐
│    todos    │────────▶│  todo_user   │
├─────────────┤         ├──────────────┤
│ id (PK)     │         │ todo_id (FK) │
│ user_id (FK)│◀────────│ user_id (FK) │
│ assigned_to │         │ permission   │
│ text        │         └──────────────┘
│ description │
│ completed   │
│ priority    │
│ date        │
│ created_at  │
│ updated_at  │
└─────────────┘
      │
      │ (hasMany)
      │
      ▼
┌─────────────┐         ┌──────────────────┐
│  comments   │────────▶│ comment_mentions  │
├─────────────┤         ├──────────────────┤
│ id (PK)     │         │ comment_id (FK)   │
│ parent_id   │         │ user_id (FK)      │
│ todo_id (FK)│         └──────────────────┘
│ user_id (FK)│
│ content     │
│ created_at  │
│ updated_at  │
└─────────────┘
      │
      │ (self-referencing)
      │
      └─────────┐
                │
                ▼
         (replies)

┌─────────────┐
│notifications│
├─────────────┤
│ id (PK)     │
│ type        │
│ notifiable  │
│ data        │
│ read_at     │
└─────────────┘
```

---

## 🎯 Regras de Negócio

### Prioridades
- **simple** (Simples): Tarefas de baixa prioridade
- **medium** (Média): Tarefas de importância moderada
- **urgent** (Urgente): Tarefas que precisam de atenção imediata

### Ordenação Padrão
1. Prioridade (urgent → medium → simple)
2. Data de criação (mais recentes primeiro)

### Filtros Disponíveis
- **Todas**: Mostra todas as tarefas
- **A Concluir**: Mostra apenas tarefas com `completed = false`
- **Concluídas**: Mostra apenas tarefas com `completed = true`

### Status de Conclusão
- `completed = false`: Tarefa pendente
- `completed = true`: Tarefa concluída

### Compartilhamento
- Apenas o dono da tarefa pode compartilhar
- Permissões: `read` (visualizar) ou `write` (editar)
- Usuários com acesso compartilhado recebem notificações quando:
  - A tarefa é compartilhada com eles
  - O dono edita a tarefa
  - Outro usuário com acesso edita a tarefa

### Comentários
- Ordenados por `last_activity_at` (comentário mais recente no topo)
- Suportam respostas aninhadas (sem limite de profundidade)
- Layout empilhado (sem indentação visual)
- Usuários podem mencionar outros usando `@nome`
- O dono da tarefa é notificado quando alguém comenta (mesmo sem menção)

### Menções
- Detectadas via regex no conteúdo do comentário
- Formato: `@nome` ou `@Nome Completo`
- Usuários mencionados recebem notificações
- Suporta nomes compostos

### Notificações
- Armazenadas no banco de dados (canal `database`)
- Tipos implementados:
  - Menção em comentário
  - Comentário na tarefa
  - Compartilhamento de tarefa
  - Edição de tarefa compartilhada
  - Atribuição de tarefa
  - Conclusão de tarefa atribuída

---

## 🔍 Consultas Importantes

### Buscar tarefas ordenadas por prioridade
```php
Todo::orderByRaw("
    CASE 
        WHEN priority = 'urgent' THEN 1
        WHEN priority = 'medium' THEN 2
        WHEN priority = 'simple' THEN 3
        ELSE 4
    END
")->orderBy('created_at', 'desc')->get();
```

### Buscar tarefas pendentes
```php
Todo::where('completed', false)->get();
```

### Buscar tarefas concluídas
```php
Todo::where('completed', true)->get();
```

### Buscar tarefas por data
```php
Todo::whereDate('date', $date)->get();
```

### Buscar tarefas compartilhadas com usuário
```php
Todo::whereHas('sharedWith', function ($q) use ($userId) {
    $q->where('users.id', $userId);
})->get();
```

### Buscar comentários de uma tarefa ordenados por atividade
```php
Comment::where('todo_id', $todoId)
    ->whereNull('parent_id')
    ->with(['replies', 'user', 'mentions'])
    ->get()
    ->sortByDesc(function ($comment) {
        return $comment->last_activity_at;
    });
```

### Buscar usuários mencionados em um comentário
```php
Comment::find($commentId)->mentions;
```

---

## 📈 Melhorias Futuras

### 1. Categorias/Tags
- Criar tabela `categories` ou `tags`
- Implementar relacionamento `belongsToMany`

### 2. Arquivos Anexos
- Criar tabela `todo_attachments`
- Relacionamento `hasMany` com Todo

### 3. Soft Deletes
- Adicionar `deleted_at` na tabela `todos`
- Implementar SoftDeletes trait no modelo

### 4. Histórico/Auditoria
- Criar tabela `todo_history`
- Registrar todas as alterações nas tarefas

### 5. Reações em Comentários
- Sistema de reações (like/dislike) já existe na estrutura, mas foi removido da UI
- Pode ser reativado no futuro se necessário

---

## 📝 Notas Técnicas

### Performance
- Índices criados nos campos mais consultados (priority, completed, date, user_id, assigned_to)
- Ordenação otimizada usando CASE no SQL
- Eager loading para evitar N+1 queries em relacionamentos

### Segurança
- Validação de dados no controller
- Form Requests para validação
- Sanitização de inputs
- Proteção contra SQL injection (Eloquent ORM)
- Autorização via Policies

### Escalabilidade
- Estrutura preparada para relacionamentos complexos
- Índices adequados para grandes volumes de dados
- Notificações assíncronas (preparado para queues)

---

**Última atualização**: 2025-01-XX  
**Versão da modelagem**: 2.0
