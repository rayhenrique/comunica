# Fase 6 — relatório de execução em 13/10/2026

## Diagnóstico

PRD, MVP-SCOPE, DATABASE-SCHEMA, TASKS, RULES e AGENTS relidos integralmente antes da implementação. A aplicação chegou à fase 6 com 65 testes PHPUnit, 392 assertions e as telas da fase 5 concluídas. A execução inicial confirmou esses resultados.

O planejamento exigia Pest v3, que depende de PHPUnit 11, enquanto composer.json exigia PHPUnit 12. A documentação oficial confirma as bases: [Pest 3 / PHPUnit 11](https://v3.pestphp.com/docs/upgrade-guide) e [Pest 4 / PHPUnit 12](https://pestphp.com/docs/upgrade-guide). Os metadados do Composer confirmaram a incompatibilidade da versão 3 e o suporte de pest-plugin-laravel 4.1 ao Laravel 13.

O usuário autorizou explicitamente **Pest 4**. A decisão foi registrada em DECISIONS.md e no título da fase 6 de TASKS.md; os critérios das tarefas permaneceram iguais. Nenhuma divergência foi resolvida por suposição.

## Implementação e validação por tarefa

### 6.1 — Antecedência

LeadTimeTest.php exercita a Action real de criação e o formulário Livewire. Relógio congelado numa sexta-feira; o instante exato de 48 horas no domingo é aceito. Datas passadas, um minuto abaixo, um segundo abaixo e offset UTC equivalente são rejeitados com mensagem explícita e zero gravações. O formulário mantém os valores após erro; a criação válida calcula o término no servidor.

Resultado específico: **6 testes, 22 assertions**, aprovado antes de marcar 6.1.

### 6.2 — Isolamento

VisibilityIsolationTest.php verifica dois coordenadores nos dois sentidos, em pending, approved, rescheduled, rejected e cancelled. Listagens HTML e JSON ignoram tentativa de troca de user_id, incluem somente a pauta própria e não exibem título alheio. URLs de detalhe e chamadas Livewire com IDs de outro solicitante retornam 403. Pedidos de cancelamento alheios são negados e o status permanece intacto.

Resultado específico: **5 testes parametrizados, 150 assertions**, aprovado antes de marcar 6.2.

### 6.3 — Moderação

ModerationAuthorizationTest.php testa approve, reschedule e reject para proprietário e outro coordenador. Actions negam autorização e uma comparação integral dos atributos lidos do banco confirma ausência de alterações, incluindo datas, notas e auditoria. A rota e o componente de triagem também negam o perfil. Inclui visitante sem autenticação e caso positivo de Comunicação reagendando com original_start_at, cálculo de término e identificação do moderador.

Na primeira execução, a comparação da fixture recém-criada com a linha lida do banco identificou diferenças de representação (colunas nullable ausentes e ordem das chaves). O teste foi corrigido para ler a linha persistida antes e depois; nenhuma mudança na aplicação foi necessária.

Resultado específico final: **8 testes, 32 assertions**, aprovado antes de marcar 6.3.

### 6.4 — Conflitos

ScheduleConflictTest.php verifica horários idênticos, sobreposição parcial antes/depois e contenção nos dois sentidos, no mesmo local. Horários adjacentes, eventos excluídos, não confirmados e demandas art em outro local não geram conflito. As quatro combinações coverage/both entre locais diferentes geram alerta mesmo com evento rescheduled. A aprovação continua permitida apesar do alerta, conforme decisão soberana.

Resultado específico: **10 testes, 18 assertions**, aprovado antes de marcar 6.4.

## Arquivos e dependências

- Criados tests/Pest.php, tests/Support/Phase6Fixtures.php e os quatro arquivos em tests/Feature/Phase6.
- Atualizados composer.json e composer.lock: Pest **4.7.8**, plugin Laravel **4.1.0** e dependências de desenvolvimento. Composer instalou 18 pacotes e ajustou PHPUnit **12.5.39 → 12.5.33**, dentro da mesma versão principal, conforme restrições do Pest selecionado.
- Pest.php vincula TestCase e RefreshDatabase somente à nova pasta; todos os testes de classe existentes foram preservados e continuam executados pelo Pest.
- TASKS.md atualizado apenas no título autorizado e nas marcações de 6.1–6.4; DECISIONS.md registra a definição de versão. PRD, MVP-SCOPE, DATABASE-SCHEMA, RULES e AGENTS preservados.
- Nenhuma mudança em código de produção, interface, schema, migrations, seeders, .env ou credenciais.

## Verificação final

| Comando | Resultado |
| --- | --- |
| php artisan test --compact | **94 testes passaram, 614 assertions**; 8,00 segundos nesta execução |
| php vendor/bin/pint --test --preset=psr12 | Passou |
| composer validate --strict | composer.json válido |
| composer audit --no-interaction | Nenhum advisory de vulnerabilidade encontrado nas dependências |
| php vendor/bin/pest --version | Pest 4.7.8 |
| Consulta de contagem no MySQL local | 3 usuários e 10 solicitações, incluindo excluídas |
| git status --short | Diretório sem repositório Git; nenhum push |

Pint corrigiu somente finais de linha e linha em branco no novo teste de moderação. A suíte completa passou após essa correção. A instalação inicial com restrições contendo caret não foi interpretada corretamente pelo wrapper Windows do Composer; ele reverteu os manifests e a instalação foi refeita com restrições tilde válidas, mantendo Pest na versão principal 4.

## Uso e limites

Executar `php artisan test` ou `composer test` para a suíte completa. Para esta fase: `php vendor/bin/pest tests/Feature/Phase6`. phpunit.xml configura SQLite em memória, cache e sessão em array; não apontar essa suíte com RefreshDatabase para o banco MySQL que contém dados. Nenhuma migration ou fixture foi aplicada ao banco real durante esta fase.

Esta validação cobre os quatro critérios funcionais autorizados. Não é teste de carga, concorrência real entre processos, cobertura percentual ou nova auditoria de segurança do código. Não houve alteração visual que exigisse repetir o QA de navegador ou build da fase 5. O resultado de composer audit refere-se apenas aos advisories de dependências consultados.

## Controle e notas da versão

**6.1, 6.2, 6.3 e 6.4 concluídas**, validadas individualmente e marcadas no checklist. A divergência Pest v3/ PHPUnit 12 foi resolvida pelo usuário. Nenhuma pendência bloqueia as tarefas da fase 6; os limites gerais do MVP e da validação da fase 5 permanecem registrados em seu relatório.

A suíte passou de 65 para 94 testes, acrescentando 29 casos e 222 assertions em Pest. As fases 1–6 previstas em TASKS.md estão concluídas; nenhuma funcionalidade fora do MVP ou nova etapa foi antecipada.
