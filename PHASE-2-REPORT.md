# Fase 2 — relatório de execução em 13/10/2026

## A. Diagnóstico

PRD.md, MVP-SCOPE.md, DATABASE-SCHEMA.md, TASKS.md e RULES.md relidos antes da implementação. Fase 1 concluída, banco local com três usuários/dez pautas e sem Model EventRequest. User era o Model padrão, com cast de senha existente. Git disponível, diretório sem repositório inicializado. Nenhuma dependência instalada ou atualizada nesta fase.

Referências oficiais consultadas: https://laravel.com/framework/docs/13.x/eloquent e https://laravel.com/framework/docs/13.x/eloquent-mutators. Implementação usa recursos nativos do Eloquent, declarações estritas e PSR-12.

## B. Implementação e histórico

2.1: User agora usa $fillable explícito para name, email, password e department, mantendo role/id fora da atribuição em massa. Senha usa casts(): array com hashed e permanece oculta na serialização, assim como remember_token. Adicionados eventRequests(): HasMany, isCommunicationAdmin(): bool e isCoordinator(): bool. Criado esqueleto EventRequest necessário ao relacionamento. Três testes UserModelTest passaram (14 assertions) antes de marcar 2.1 e avançar.

2.2: EventRequest completado com SoftDeletes, $fillable restrito aos dados informados na solicitação e casts de start_at, end_at, original_start_at e moderated_at para datetime; duration_minutes para integer. Campos user_id, status, end_at, original_start_at e metadados de moderação/exclusão não são preenchíveis por payload. As futuras Actions deverão atribuir esses valores explicitamente após validação/autorização.

Adicionados relacionamentos user(): BelongsTo e moderator(): BelongsTo, scopeConfirmed() para approved/rescheduled e scopeInMonth() tipado com validação de ano/mês. Critério mensal adotado e testado: interseção com [primeiro dia do mês, primeiro dia do mês seguinte), usando start_at < próximo mês e end_at > início do mês. Inclui eventos que atravessam meses; exclui evento que apenas termina no início do mês ou começa no início do próximo. Sem funções SQL aplicadas às colunas, com bindings Eloquent. O escopo mensal não filtra status implicitamente; compõe com confirmed().

Helpers formattedStartTime() e formattedEndTime() retornam H:i ou null em Model sem data. Nenhum cálculo de término, validação de antecedência ou transição de status implementado no Model; essas regras continuam destinadas às Actions da fase 4. Relacionamentos e scopes não substituem as Policies da fase 3.

Arquivos alterados: app/Models/User.php e TASKS.md (somente checkboxes 2.1/2.2). Criados app/Models/EventRequest.php, tests/Feature/UserModelTest.php, tests/Feature/EventRequestModelTest.php e este relatório.

## C. Testes e validações

- php artisan test --filter=UserModelTest: três testes passaram, 14 assertions.
- php artisan test: 15 testes passaram, 126 assertions, incluindo os testes já existentes. Novos testes cobrem hashing sem rehash duplicado, campos restritos, perfil/relacionamento, casts/datas nulas, helpers, scopes componíveis, limites mensais/anuais, exclusão lógica e restauração.
- Testes com RefreshDatabase usam SQLite em memória configurado no phpunit.xml, sem alterar o MySQL local.
- Conferência de leitura com bootstrap Laravel no MySQL: três usuários, dez pautas, quatro confirmadas, cinco do coordenador Educação; relacionamentos de moderador e scope mensal corretos; horários 09:00 e 10:30 para a fixture reagendada verificada.
- Pint --preset psr12 e --test: passaram.
- php -l nos dois Models: sem erros.
- SHA-256 de PRD, MVP-SCOPE, DATABASE-SCHEMA e RULES permanece igual aos originais.

Nenhuma migration/seed executada, nenhuma credencial exibida ou alterada, nenhum commit/push. Sem alteração de rotas ou interfaces.

## D. Pendências e riscos

As Policies e proteção de rotas serão implementadas na fase 3; os Models desta fase não fornecem autorização por si sós. Divergência de 48 horas úteis/corridas permanece aberta antes da etapa 4.3. Incompatibilidade Pest v3/ PHPUnit v12 permanece registrada para alinhamento da fase de testes; nesta fase foi utilizado o PHPUnit já instalado. A medição de performance do calendário não faz parte desta validação e dependerá da consulta/tela e volume de dados reais.

## E. Controle de tarefas

2.1 e 2.2 concluídas e marcadas [x] após testes. Nenhuma tarefa 3.x ou posterior iniciada. Próxima etapa: 3.1, EventRequestPolicy.

## F. Notas da versão

A aplicação agora lê usuários e pautas com relacionamentos tipados, reconhece perfis e eventos confirmados, filtra eventos por mês e preserva registros excluídos logicamente. A infraestrutura existente continua funcionando; telas e regras executivas seguem nas fases seguintes.
