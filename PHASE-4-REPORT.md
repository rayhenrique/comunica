# Fase 4 — relatório de execução em 13/10/2026

## A. Diagnóstico

PRD, MVP-SCOPE, DATABASE-SCHEMA, TASKS, RULES e AGENTS relidos antes da implementação. Fases 1–3 concluídas, schema MySQL InnoDB, Models e Policies disponíveis. Git permanece sem repositório inicializado. Nenhuma dependência instalada nesta fase.

Consultadas referências oficiais de transações e validação: https://laravel.com/framework/docs/13.x/database e https://laravel.com/framework/docs/13.x/validation.

## B. Decisões e implementação

O usuário confirmou explicitamente:
1. Antecedência de **48 horas corridas**, incluindo fins de semana e feriados.
2. Sobreposição entre quaisquer duas demandas coverage/both gera alerta, mesmo em locais diferentes.

Essas decisões foram registradas em DECISIONS.md e alinhadas em PRD RN01/RF05, MVP-SCOPE (prazo), TASKS 4.1/4.3 e AGENTS. Nenhum outro requisito original foi substituído. DATABASE-SCHEMA e RULES não foram modificados.

### 4.1 — Detecção de conflitos

Criada DetectEventConflictAction, protegida com $this->authorize('moderate', EventRequest::class), para impedir acesso de coordenadores à coleção global. Consulta parametrizada Eloquent usa confirmed(), SoftDeletes, exclusão da própria pauta e sobreposição de intervalos. Alerta por mesmo local em qualquer demanda ou duas demandas coverage/both em locais distintos. Arte em outro local não conflita apenas por horário.

Intervalos usam término exclusivo: start existente < término candidato e end existente > início candidato. Um evento adjacente não gera choque; contenção e interseção parcial geram. Coleção de Models ordenada por start_at/id, sem modificar registros. CalculateEventEndAction calcula término a partir de início/duração e não confia em end_at informado pelo cliente. Valida duração inteira entre 1 e 65535 minutos, limite do unsignedSmallInteger do schema, sem impor uma lista arbitrária de durações.

Quatro testes específicos passaram, cinco assertions, antes de marcar 4.1.

### 4.2 — Moderação

Criada ModerateEventAction com autorização explícita, Validator, DB::transaction (até três tentativas em deadlock), lockForUpdate e leitura atual da pauta. Somente pending não excluída pode ser decidida; Models antigos não sobrescrevem decisão atual. Usuário moderador vem de auth()->id(), nunca de payload.

- approve: approved, data original mantida, término recalculado, moderador/data registrados.
- reschedule: nova data validada, normalizada no fuso da aplicação; original_start_at preservada; término recalculado; status rescheduled e justificativa obrigatória.
- reject: status rejected, justificativa com pelo menos dez caracteres e datas preservadas.

Notas são aparadas; notas em branco não satisfazem justificativa. A antecedência de 48 horas é da abertura da solicitação, não foi aplicada à decisão soberana da Comunicação. Nenhuma regra adicional de antecedência no reagendamento criada. Conflitos não impedem aprovação. Nenhuma regra de negócio foi movida para controllers/Models.

Nove testes específicos passaram, 32 assertions, antes de marcar 4.2, incluindo autorização, preservação de datas, decisões inválidas, pauta cancelada/excluída, Model desatualizado e rollback em falha de persistência.

### 4.3 — Validação de submissão

Criado CreateEventRequestRequest autorizado pela Policy create, com campos/tipos/limites documentados e MinimumEventLeadTime implementando ValidationRule. Validação rejeita início anterior a now + 48 horas corridas; o instante exato é aceito. Data sem offset usa America/Sao_Paulo, configuração inicial já estabelecida; offsets explícitos são comparados como instantes absolutos.

Somente campos permitidos entram em validated(); user_id, end_at, status e metadados enviados não integram os dados validados. Quando o fluxo de criação for implementado em 5.2, a Action deverá extrair o solicitante de auth()->id() e usar CalculateEventEndAction para persistir o término. Nenhuma rota de submissão ou persistência de nova solicitação foi antecipada; o teste HTTP do FormRequest registra endpoint apenas no ambiente de teste.

## C. Arquivos

Criados:
- app/Actions/CalculateEventEndAction.php
- app/Actions/DetectEventConflictAction.php
- app/Actions/ModerateEventAction.php
- app/Rules/MinimumEventLeadTime.php
- app/Http/Requests/CreateEventRequestRequest.php
- tests/Feature/DetectEventConflictActionTest.php
- tests/Feature/ModerateEventActionTest.php
- tests/Feature/CreateEventRequestValidationTest.php
- DECISIONS.md e este relatório.

Alterados somente os documentos citados no alinhamento de requisitos e checklist. Nenhuma alteração de .env, schema/migrations, seeders, rotas ou telas nesta fase.

## D. Testes e verificações

- php artisan test --filter=DetectEventConflictActionTest: quatro testes passaram.
- php artisan test --filter=ModerateEventActionTest: nove testes passaram.
- php artisan test: 44 testes passaram, 263 assertions, incluindo todas as fases anteriores. Testes isolados usam SQLite em memória; cobrem limite exato de 48 horas, um segundo abaixo, fim de semana, offsets de fuso, payload protegido, datas/campos inválidos, cálculo atravessando meia-noite e duração inválida.
- MySQL real: fixtures temporárias criadas dentro de transação externa, detecção de cobertura em outro local, aprovação mesmo com conflito, autoria da moderação, reagendamento com término/data original e recusa exercitados pelas Actions. Rollback externo efetuado; comparação integral dos registros antes/depois confirmou as dez pautas originais intactas. Nenhuma migration/seed executada.
- Pint PSR-12 corrigiu espaçamento de argumentos em dois testes; verificação final --test passou.
- php -l nos arquivos de Actions, Rule e FormRequest: sem erros.

O teste no MySQL exercita transações/bloqueio, mas não é um teste simultâneo com dois processos concorrentes. A proteção contra decisão desatualizada foi testada sequencialmente; o código bloqueia a linha e verifica status dentro da transação.

## E. Pendências e controle

4.1, 4.2 e 4.3 marcadas [x] após validação. Fase 5 não iniciada. Próxima tarefa: 5.1, layouts internos e públicos. Login/logout ainda precisa ser alinhado para as telas operacionais, pois não possui tarefa específica no planejamento original. Pest v3/ PHPUnit v12 continua registrado para decisão antes da fase 6; o executor existente foi usado nesta fase.

Sem alteração de credenciais, exposição de segredos ou push remoto. Nenhuma funcionalidade postergada implementada.

## F. Notas da versão

O backend já detecta conflitos de agenda, aplica decisões de Comunicação com auditoria e valida solicitações com 48 horas corridas. As Actions estão preparadas para integração aos componentes Livewire da próxima fase; formulários e botões reais ainda não foram implementados.
