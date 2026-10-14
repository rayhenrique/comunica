# Histórico de versões — Comunica

## Convenção

O projeto usa versões `MAJOR.MINOR.PATCH`: MAJOR para mudanças incompatíveis, MINOR para funcionalidades compatíveis e PATCH para correções e documentação. As versões são identificadas por tags Git `vX.Y.Z` após validação. Este arquivo registra o conteúdo de cada entrega; o Git registra os arquivos e commits.

Não reescrever tags publicadas. Atualizar este histórico e o manual quando o comportamento mudar. Commitar somente código, documentação e lockfiles; ambientes, senhas, sessões, dumps e dependências instaladas ficam excluídos. O banco de dados não é um backup Git.

## [1.1.0] — 13/10/2026

Refinamento UI/UX aprovado após auditoria em duas etapas. A conferência final encontrou o commit f528a9f e a tag v1.1.0 já publicados por outra ação. Esta sessão não executou push; a nota documental dessa observação permanece local até autorização.

- Cards mobile no histórico e triagem, mantendo tabelas desktop, filtros, paginação e ações autorizadas.
- Filtros compactos, ajuda condicional, controle de senha com alvo confortável e campos com contorno contrastante usando tokens existentes.
- Calendário com horário separado, títulos limitados na grade e seleção mobile persistente; título principal dentro da escala aprovada.
- Status reagendado distinguido por contorno; diálogos com corpo flexível e ações visíveis.
- DESIGN-SYSTEM, UI-UX-GUIDELINES e DESIGN-LOCK complementam DESIGN e UX-CONTRACT; consulta e aprovação obrigatórias em AGENTS.
- Exportador fixado em versão exata e lockfile, baseline de identidade, auditoria de drift, testes negativos e workflow CI.
- Paleta, fontes, marca, regras, banco e permissões preservados. Nenhuma fonte alternativa aplicada.

Validação: 95 testes/639 assertions, sete testes de proteção de identidade, Pint e build aprovados. Matrizes Chrome em 360/390/768/1024/1440 px com dados isolados. Consulte [relatório da Etapa B](UI-UX-REFINEMENT-REPORT.md) para cobertura, evidências e limites.

## [1.0.0] — 13/10/2026

Primeira versão identificada do MVP, consolidando as fases 1–6. Os marcos abaixo foram desenvolvidos antes da inicialização do Git; não representam commits ou tags históricos.

| Marco | Entrega | Evidência |
| --- | --- | --- |
| Fase 1 | Laravel, Livewire, Tailwind, MySQL, migrations e dados demonstrativos | [Relatório](PHASE-1-REPORT.md) |
| Fase 2 | Models, casts, relações e consultas de calendário | [Relatório](PHASE-2-REPORT.md) |
| Fase 3 | Policies e isolamento por perfil | [Relatório](PHASE-3-REPORT.md) |
| Fase 4 | Conflitos, moderação transacional e antecedência | [Relatório](PHASE-4-REPORT.md) |
| Fase 5 | Login, solicitação, histórico, triagem e calendário público responsivo | [Relatório](PHASE-5-REPORT.md) |
| Fase 6 | Pest 4 e testes dos critérios de aceite | [Relatório](PHASE-6-REPORT.md) |
| Documentação | README específico, manual do usuário e controle de versão local | Esta entrega |

### Regras consolidadas

- Abertura com antecedência mínima de 48 horas corridas.
- Término calculado no servidor a partir da duração.
- Coordenadores acessam somente as próprias solicitações; cancelam apenas pendentes.
- Comunicação aprova, reagenda e aprova, ou recusa com justificativa.
- Choques de local ou cobertura simultânea geram aviso e permitem decisão soberana.
- Calendário público apresenta somente pautas aprovadas/reagendadas e dados públicos.

### Validação e limites

A fase 6 registrou **94 testes aprovados e 614 assertions**, PSR-12 e validação Composer aprovados. A fase 5 registra build e QA Chrome desktop/mobile. Consulte os relatórios para comandos, ambiente e limites efetivamente verificados.

Nesta entrega documental, os links locais dos três documentos foram validados, os novos arquivos passaram em `git diff --cached --check` e os 147 arquivos do índice foram conferidos para excluir ambientes, bancos, sessões e dependências instaladas. Não houve alteração de comportamento que exigisse nova execução da suíte. O controle Git foi inicializado na branch main; o autor local usa a identificação fornecida pelo usuário. Nenhum remoto foi configurado. O checklist das fases permanece preservado.

Não inclui notificações externas, gestão individual de equipe, anexos ou negociação de reagendamento. Não houve teste de carga nem concorrência real entre processos; confirmação de performance em volume permanece fora da evidência disponível. A deduplicação de submissão usa cache temporário, e o formulário não persiste rascunhos entre sessões.

## Procedimento para próximas versões

1. Implementar somente o escopo autorizado, consultar TASKS e registrar decisões.
2. Executar testes e verificações apropriadas; atualizar README/manual e este histórico.
3. Conferir `git status` e `git diff`; adicionar somente arquivos revisados.
4. Criar commit com autor configurado e descrição objetiva.
5. Criar tag da versão validada: `git tag -a vX.Y.Z -m "Comunica X.Y.Z"`.

Nenhum remoto ou push é configurado automaticamente. Para consultar o histórico: `git log --oneline --decorate` e `git tag --list`.
