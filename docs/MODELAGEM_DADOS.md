# 📊 Modelagem de Dados - RezenDo

## Visão Geral

O RezenDo é um sistema de gerenciamento de tarefas simples e eficiente. A modelagem atual prioriza simplicidade e performance.

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
- `hasMany` Todo (futuro - quando implementar multi-usuário)

---

### 2. **todos** (Tarefas)

Tabela principal do sistema, armazena todas as tarefas criadas.

#### Estrutura:

| Campo | Tipo | Descrição | Constraints |
|-------|------|-----------|-------------|
| `id` | bigint (PK) | Identificador único | Auto-incremento |
| `text` | string(255) | Título da tarefa | Obrigatório, máx 200 caracteres |
| `description` | text | Descrição detalhada | Nullable, máx 500 caracteres |
| `completed` | boolean | Status de conclusão | Default: false |
| `priority` | enum | Nível de prioridade | Values: 'simple', 'medium', 'urgent', Default: 'simple' |
| `day` | string(255) | Dia da semana (legado) | Nullable |
| `date` | date | Data específica da tarefa | Nullable |
| `user_id` | bigint (FK) | ID do usuário dono | Nullable (futuro) |
| `created_at` | timestamp | Data de criação | Automático |
| `updated_at` | timestamp | Data de atualização | Automático |

#### Índices:
- `priority` (para ordenação)
- `completed` (para filtros)
- `date` (para ordenação por data)
- `user_id` (futuro - para relacionamento com usuários)
- `created_at` (para ordenação)

#### Validações:
- `text`: obrigatório, máximo 200 caracteres
- `description`: opcional, máximo 500 caracteres
- `priority`: deve ser 'simple', 'medium' ou 'urgent'
- `date`: formato válido (YYYY-MM-DD) ou DD/MM/YYYY ou DD/MM/YY

#### Relacionamentos:
- `belongsTo` User (futuro - quando implementar multi-usuário)

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
      │ (futuro: hasMany)
      │
      ▼
┌─────────────┐
│    todos    │
├─────────────┤
│ id (PK)     │
│ text        │
│ description │
│ completed   │
│ priority    │
│ date        │
│ user_id (FK)│──┐ (futuro)
│ created_at  │  │
│ updated_at  │  │
└─────────────┘  │
                 │
                 └── (belongsTo)
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

---

## 📈 Melhorias Futuras

### 1. Multi-usuário
- Adicionar `user_id` na tabela `todos`
- Implementar relacionamento `belongsTo` User
- Adicionar autenticação para usuários

### 2. Categorias/Tags
- Criar tabela `categories` ou `tags`
- Implementar relacionamento `belongsToMany`

### 3. Arquivos Anexos
- Criar tabela `todo_attachments`
- Relacionamento `hasMany` com Todo

### 4. Comentários
- Criar tabela `todo_comments`
- Relacionamento `hasMany` com Todo

### 5. Soft Deletes
- Adicionar `deleted_at` na tabela `todos`
- Implementar SoftDeletes trait no modelo

### 6. Histórico/Auditoria
- Criar tabela `todo_history`
- Registrar todas as alterações nas tarefas

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

---

## 📝 Notas Técnicas

### Performance
- Índices criados nos campos mais consultados (priority, completed, date)
- Ordenação otimizada usando CASE no SQL

### Segurança
- Validação de dados no controller
- Sanitização de inputs
- Proteção contra SQL injection (Eloquent ORM)

### Escalabilidade
- Estrutura preparada para relacionamentos futuros
- Índices adequados para grandes volumes de dados

---

**Última atualização**: 2025-01-XX  
**Versão da modelagem**: 1.0

