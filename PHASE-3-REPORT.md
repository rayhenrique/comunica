# Fase 3 — relatório de execução em 13/10/2026

## A. Diagnóstico

Documentos obrigatórios e AGENTS.md relidos. Models e schema das fases 1/2 disponíveis. Inicialmente não existiam Policy, rotas operacionais ou fluxo de login: apenas a página padrão. Git continua sem repositório inicializado. Consultadas referências oficiais https://laravel.com/framework/docs/13.x/authorization e https://laravel.com/framework/docs/13.x/authentication.

## B. Implementação e histórico

### 3.1 — Policy

Criada EventRequestPolicy e registrada explicitamente em AppServiceProvider com Gate::policy. Permissões:
- viewAny: coordenadores e Comunicação; a consulta complementa essa permissão com restrição de autoria.
- view: coordenador dono ou Comunicação; pauta excluída logicamente não é exposta.
- create: apenas coordenador.
- moderate: apenas Comunicação, sem bypass global que conceda create/cancel ao administrador.
- cancel: apenas coordenador dono, pauta pending e não excluída.

Visitantes e perfis desconhecidos são negados. Abilities não implementadas, como update/delete, permanecem negadas pelo Gate. Quatro testes da Policy passaram com 37 assertions antes de marcar 3.1 e avançar.

### 3.2 — Rotas e isolamento da consulta

Rotas web de leitura preparadas, com auth e middleware can:
- GET /solicitacoes: paginação de 15, autorização viewAny e restrição às próprias pautas para coordenadores.
- GET /solicitacoes/{eventRequest}: binding numérico e autorização view; terceiro retorna 403, ID inexistente/excluído retorna 404.
- GET /comunicacao/triagem: acesso exclusivo da Comunicação via moderate; consulta global de dados, ainda sem filtros/tela da fase 5.

EventRequestController permanece enxuto e usa $this->authorize() explicitamente. ListVisibleEventRequestsAction concentra a consulta segura e verifica Gate::forUser()->authorize() também quando chamada diretamente. Identidade vem da sessão autenticada, nunca de user_id enviado na URL. Model SoftDeletes impede retorno de pautas excluídas. Respostas atuais são JSON de dados autorizados, sem relações carregadas ou dados de senha dos usuários.

/calendario está registrado fora de auth/can e retorna 204 sem dados nesta base; a agenda pública mensal será implementada em 5.5. Não é apresentado como calendário funcional. Não criadas rotas de escrita para criação, moderação ou cancelamento: dependem das Actions/formulários das próximas fases. Quando implementadas, deverão reaplicar as Policies na camada executora, além da proteção das rotas.

Como não existe login, bootstrap/app.php configura redirectGuestsTo('/') para acessos HTML, evitando erro por rota login ausente. Pedidos JSON sem sessão recebem 401. Essa configuração é provisória e deve apontar para o login real quando o fluxo for implementado. Nenhuma interface/login/credencial foi criada fora do escopo desta fase.

Criados: app/Policies/EventRequestPolicy.php, app/Actions/ListVisibleEventRequestsAction.php, app/Http/Controllers/EventRequestController.php, tests/Feature/EventRequestPolicyTest.php, tests/Feature/OperationalRoutesTest.php e este relatório.

Alterados: app/Providers/AppServiceProvider.php, bootstrap/app.php, routes/web.php e TASKS.md (somente checkboxes 3.1/3.2). Nenhuma dependência instalada.

## C. Testes

- php artisan test --filter=EventRequestPolicyTest: quatro testes passaram, 37 assertions.
- php artisan test: 26 testes passaram, 196 assertions. Inclui convidados, perfis, dono/terceiro, cancelamento em todos os status, exclusão lógica, consulta restrita mesmo com user_id de terceiro, fila global, IDs inexistentes e Action chamada diretamente.
- Tests RefreshDatabase usam SQLite em memória; não alteraram o banco MySQL local.
- Conferência somente de leitura no MySQL real: Action retorna cinco pautas para coordenador Educação/dez para Comunicação; Policy nega visualização de pauta de terceiro.
- php artisan route:list -v --except-vendor: middleware auth/can presente em todas as rotas operacionais; calendario apenas web.
- php artisan route:cache e route:clear: sucesso; cache removido ao finalizar.
- Pint --preset psr12 e --test: passaram.
- SHA-256 de PRD, MVP-SCOPE, DATABASE-SCHEMA e RULES idênticos aos originais.

Nenhuma migration/seed executada, nenhum segredo exposto/alterado, nenhum commit/push.

## D. Pendências e riscos

O planejamento não enumera uma tarefa específica para autenticação (login/logout); esse fluxo deve ser alinhado antes das telas operacionais da fase 5. Middleware de sessão e autorização estão prontos e testados, mas não existe ainda uma tela para estabelecer sessão pelo navegador.

Mantidas as divergências já registradas: 48 horas úteis/corridas antes de 4.3 e Pest v3/ PHPUnit v12 antes da fase de testes. PHPUnit atual utilizado nas validações desta fase. Nenhuma decisão de regra de antecedência tomada.

## E. Controle

3.1 e 3.2 concluídas e marcadas [x] após validação. Fase 4 não iniciada. Próxima tarefa: 4.1, DetectEventConflictAction.

## F. Notas da versão

A base agora exige sessão nas consultas internas, aplica as permissões dos perfis e bloqueia pautas de terceiros. A consulta global é exclusiva da Comunicação. A rota pública está reservada para o calendário da fase 5, sem divulgar dados internos.
