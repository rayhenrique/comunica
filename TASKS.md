# TASKS.md — Plano de Execução Sequencial (Laravel 13 + Livewire 4 + Tailwind v4)

> **Instrução para a IA:** Execute estritamente uma etapa por vez. Teste e valide cada passo antes de marcar com `[x]` e avançar para o próximo bloco.

---

### Fase 1: Setup do Projeto e Infraestrutura de Dados
- [x] 1.1 Criar base Laravel 13 (`laravel new pautas-comunicacao`) configurando conexão MySQL 8.0+.
- [x] 1.2 Instalar e configurar Livewire 4.x via Composer (`composer require livewire/livewire`).
- [x] 1.3 Instalar Tailwind CSS v4.x via Vite (`npm install -D tailwindcss @tailwindcss/vite`).
- [x] 1.4 Configurar `vite.config.js` com o plugin `@tailwindcss/vite` e configurar `resources/css/app.css` com `@import "tailwindcss";` (sem `tailwind.config.js`).
- [x] 1.5 Criar e rodar migrations das tabelas `users` e `event_requests` com tipos nativos e índices.
- [x] 1.6 Criar seeders (`UserSeeder` e `EventRequestSeeder`) gerando 1 usuário Comunicação, 2 Coordenadores e 10 pautas em status variados.

---

### Fase 2: Models, Casts Modernos e Scopes (Laravel 13)
- [x] 2.1 Model `User`:
  - Definir `$fillable` restrito.
  - Implementar método de casts: `casts(): array` com hashing nativo para `password`.
  - Criar relação `hasMany(EventRequest::class)` e helpers `isCommunicationAdmin(): bool` e `isCoordinator(): bool`.
- [x] 2.2 Model `EventRequest`:
  - Definir `$fillable` e trait `SoftDeletes`.
  - Implementar método de casts: `casts(): array` tipando `start_at`, `end_at`, `original_start_at` e `moderated_at` para `datetime`.
  - Criar scopes `scopeConfirmed($query)` e `scopeInMonth($query, $year,$month)`.
  - Criar accessor/helper para formatação de horário de início/fim.

---

### Fase 3: Camada de Segurança e Autorização (Policies)
- [x] 3.1 Criar `EventRequestPolicy`:
  - `viewAny`: Comunicação visualiza todas; Coordenador visualiza apenas as próprias.
  - `view`: Solicitante dono OU moderador da comunicação.
  - `create`: Restrito a coordenadores autenticados.
  - `moderate`: Apenas usuários com `role === 'communication_admin'`.
  - `cancel`: Permitido apenas ao solicitante dono enquanto `status === 'pending'`.
- [x] 3.2 Proteger rotas autenticadas e registrar autorização explícita.

---

### Fase 4: Actions e Regras de Negócio de Domínio
- [x] 4.1 Criar Action `DetectEventConflictAction`:
  - Localizar eventos com status `approved` ou `rescheduled` com sobreposição de horário e mesmo local, ou com sobreposição entre duas demandas `coverage`/`both`, mesmo em locais diferentes.
  - Retornar coleção com os dados dos conflitos para exibição em tela.
- [x] 4.2 Criar Action `ModerateEventAction`:
  - Execução transacional (`DB::transaction`) para `approve`, `reschedule` (com recálculo de datas e nota obrigatória) e `reject`.
- [x] 4.3 Criar Form Request / Livewire Form Rule para submissão:
  - Validar antecedência mínima de 48 horas corridas (`start_at >= now()->addHours(48)`), conforme decisão de 13/10/2026.

---

### Fase 5: Componentes Livewire 4 & Telas Tailwind v4
- [x] 5.1 Criar Layouts Base (Painel Interno Autenticado e Layout Público da Agenda) com tema minimalista em Tailwind v4.
- [x] 5.2 **Componente `Coordinator\CreateRequest` (Livewire 4)**:
  - Formulário com validação em tempo real e cálculo instantâneo do término previsto via duração.
- [x] 5.3 **Componente `Coordinator\MyRequests` (Livewire 4)**:
  - Tabela com filtros de status e botão de cancelamento seguro para itens pendentes.
- [x] 5.4 **Componente `Communication\Triage` (Livewire 4)**:
  - Lista de pautas pendentes com card analítico.
  - Alerta dinâmico de choque de horário/local consumindo `DetectEventConflictAction`.
  - Modais reativos com Alpine/Livewire para Reagendamento Soberano e Recusa.
- [x] 5.5 **Componente `Public\CalendarView` (Livewire 4)**:
  - Rota pública `/calendario` (sem autenticação).
  - Grade mensal com navegação mês anterior / próximo mês.
  - Drawer / Modal lateral com os eventos confirmados do dia clicado.

---

### Fase 6: Testes Automatizados (Pest v4 — decisão de compatibilidade de 13/10/2026)
- [x] 6.1 Testar rejeição de solicitação criada com menos de 48h de antecedência.
- [x] 6.2 Testar isolamento de visibilidade (Coordenador A não acessa dados do Coordenador B).
- [x] 6.3 Testar ação de moderação (não permitir aprovação/reagendamento por coordenador comum).
- [x] 6.4 Testar detecção de choque de agenda no mesmo local e horário.
