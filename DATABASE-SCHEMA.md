# DATABASE-SCHEMA.md — Modelagem Relacional MySQL 8.0+ / 8.4 LTS

## 1. Diagrama Textual de Entidades

```text
[ users ] (1)
   │
   ├──< (N) [ event_requests ] (como solicitante: user_id)
   │
   └──< (N) [ event_requests ] (como moderador: moderated_by)
```

---

## 2. Dicionário de Tabelas e Tipagens

### Tabela: `users`
Armazena credenciais, perfil e setor de lotação.

| Coluna | Tipo Laravel / MySQL | Modificadores | Descrição |
| :--- | :--- | :--- | :--- |
| `id` | `bigIncrements` | PK | Identificador único |
| `name` | `string(255)` | NOT NULL | Nome do servidor/colaborador |
| `email` | `string(255)` | NOT NULL, UNIQUE | E-mail corporativo |
| `password` | `string(255)` | NOT NULL | Hash da senha |
| `role` | `enum` | NOT NULL, DEFAULT `'coordinator'` | Valores: `'coordinator'`, `'communication_admin'` |
| `department` | `string(100)` | NOT NULL | Setor / Secretaria solicitante |
| `remember_token` | `rememberToken()` | NULLABLE | Token de sessão |
| `created_at` | `timestamp` | NULLABLE | Data de criação |
| `updated_at` | `timestamp` | NULLABLE | Data de atualização |

---

### Tabela: `event_requests`
Armazena as solicitações de eventos, parâmetros de agenda e dados de moderação.

| Coluna | Tipo Laravel / MySQL | Modificadores | Descrição |
| :--- | :--- | :--- | :--- |
| `id` | `bigIncrements` | PK | Identificador único |
| `user_id` | `foreignId` | NOT NULL, INDEX | FK referenciando `users.id` via `restrictOnDelete` |
| `title` | `string(150)` | NOT NULL | Título da pauta/evento |
| `demand_type` | `enum` | NOT NULL | Valores: `'coverage'`, `'art'`, `'both'` |
| `location` | `string(255)` | NOT NULL, INDEX | Local de realização do evento |
| `target_audience` | `string(255)` | NOT NULL | Público-alvo previsto |
| `description` | `text` | NULLABLE | Briefing ou contextualização |
| `start_at` | `dateTime` | NOT NULL, INDEX | Data e hora de início (atualizada se reagendado) |
| `end_at` | `dateTime` | NOT NULL, INDEX | Data e hora estimada de término |
| `duration_minutes` | `unsignedSmallInteger` | NOT NULL | Duração em minutos |
| `original_start_at` | `dateTime` | NULLABLE | Data/hora original caso ocorra reagendamento |
| `status` | `enum` | NOT NULL, DEFAULT `'pending'`, INDEX | Valores: `'pending'`, `'approved'`, `'rescheduled'`, `'rejected'`, `'cancelled'` |
| `moderated_by` | `foreignId` | NULLABLE | FK referenciando `users.id` via `nullOnDelete` |
| `moderation_notes` | `text` | NULLABLE | Justificativa de recusa ou motivo do reagendamento |
| `moderated_at` | `dateTime` | NULLABLE | Timestamp em que a moderação ocorreu |
| `created_at` | `timestamp` | NULLABLE | Data de envio da solicitação |
| `updated_at` | `timestamp` | NULLABLE | Data da última alteração |
| `deleted_at` | `softDeletes` | NULLABLE | Exclusão lógica |

---

## 3. Índices Estruturais de Performance

```sql
-- Otimização para consulta no calendário público mensal
CREATE INDEX idx_calendar_lookup ON event_requests (status, start_at, end_at);

-- Otimização para detecção de conflitos de horário no mesmo local
CREATE INDEX idx_conflict_lookup ON event_requests (location, start_at, end_at, status);

-- Otimização para listagem do painel individual do coordenador
CREATE INDEX idx_user_requests ON event_requests (user_id, status, created_at);
```

---

## 4. Migration Laravel 13 Reference

```php
<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();$table->string('name');
            $table->string('email')->unique();$table->string('password');
            $table->enum('role', ['coordinator', 'communication_admin'])->default('coordinator');$table->string('department', 100);
            $table->rememberToken();$table->timestamps();
        });

        Schema::create('event_requests', function (Blueprint $table) {$table->id();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();$table->string('title', 150);
            $table->enum('demand_type', ['coverage', 'art', 'both']);$table->string('location');
            $table->string('target_audience');$table->text('description')->nullable();
            $table->dateTime('start_at');$table->dateTime('end_at');
            $table->unsignedSmallInteger('duration_minutes');$table->dateTime('original_start_at')->nullable();
            $table->enum('status', ['pending', 'approved', 'rescheduled', 'rejected', 'cancelled'])->default('pending');$table->foreignId('moderated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('moderation_notes')->nullable();$table->dateTime('moderated_at')->nullable();
            $table->timestamps();$table->softDeletes();

            $table->index(['status', 'start_at', 'end_at'], 'idx_calendar_lookup');
            $table->index(['location', 'start_at', 'end_at'], 'idx_conflict_lookup');$table->index(['user_id', 'status', 'created_at'], 'idx_user_requests');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_requests');
        Schema::dropIfExists('users');
    }
};
```