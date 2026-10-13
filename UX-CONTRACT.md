# UX Contract — Comunica

## Product context

Coordenadores solicitam apoio; Comunicação decide; visitantes consultam agenda. UI pt-BR, datas locais no fuso configurado America/Sao_Paulo. Meta WCAG 2.2 AA. Sem cadastro público ou recuperação por e-mail no MVP. Login/logout para usuários existentes é suporte ao painel desta fase; senhas existentes preservadas.

## Business-context sources

| Domínio | Fonte | Revisão |
|---|---|---|
| Perfis e permissões | PRD.md §2, EventRequestPolicy | 13/10/2026 |
| Decisões e cancelamento | PRD.md RF03/RF06, RN04 | 13/10/2026 |
| Antecedência/conflitos | DECISIONS.md, PRD.md RN01/RF05 | 13/10/2026 |
| Dados públicos | PRD.md RF07/RF08, MVP-SCOPE.md | 13/10/2026 |
| Exclusão lógica | DATABASE-SCHEMA.md | 13/10/2026 |

## Visual contract

DESIGN.md gera theme.generated.css; app.css aplica aliases e componentes. Tema claro; sem imagens externas/fontes remotas. Não duplicar valores em telas.

## Canonical UI Map

| Capability | Canonical owner | Source of truth | Allowed variants | Verification |
|---|---|---|---|---|
| Select/Listbox | ui.field (select nativo) | DESIGN.md | native, geometria do SO aceita | teclado e popup |
| Date | ui.field (date/datetime-local) | UX-CONTRACT.md | native, popup/locale do SO aceitos | digitação e popup |
| Form | ui.field + FormRequest + Livewire | PRD/Actions | criação, login, decisão | testes e navegador |
| Scrollbar | resources/css/app.css | DESIGN.md | gutter estável | computed style |
| Toast | ui.feedback | UX-CONTRACT.md | status persistente, sucesso/erro | live region |
| CRUD | Actions + componentes Livewire | PRD/Policies | criar, cancelar, moderar | testes e fluxo completo |

## Component behavior

Button: hover/foco/pressionado, disabled não interativo, busy com spinner reservado. Field: label/for, aria-invalid e error/help associados; validação após blur/submissão e foco no primeiro erro. Textarea: resize none, auto-grow limitado. Status: texto sem ação falsa. Dialog: showModal, Escape, fundo inerte e restauração do gatilho.

## Dataset navigation

Listas internas paginadas no servidor, 15 registros. Status, período, setor e demanda em URL; filtro muda e retorna página 1. Paginação localizada. Nenhuma seleção em massa. Detalhes mantêm valores completos. Calendário é limitado ao mês e expõe somente campos públicos de pautas confirmadas.

## Flow ledger

| Operation | Trigger | Pending | Success destination | Feedback | Failure recovery | Source ref |
|---|---|---|---|---|---|---|
| Create | Criar solicitação | botão ocupado | Minhas solicitações, sem filtro | Solicitação criada | valores preservados, erros inline | PRD RF01 |
| Cancel | Cancelar solicitação | confirmação + botão ocupado | mesma lista | Solicitação cancelada | dialog aberto e erro | PRD RF03 |
| Moderate | Aprovar / Reagendar e aprovar / Recusar | botão ocupado | mesma triagem | decisão confirmada | dialog aberto, dados preservados | PRD RF06 |
| Day detail | Ver eventos do dia | indicador estável | drawer no calendário | eventos cronológicos | vazio explícito | PRD RF08 |
| Login | Entrar | botão ocupado | destino autorizado/intencionado | sessão criada | e-mail preservado, senha limpa | perfis PRD §2 |

## Navigation and responsive behavior

Títulos específicos por rota. 403/404 próprios com acesso ao calendário. Cabeçalho permite calendário e painel autorizado. Tabelas internas rolam horizontalmente, sem cortar controles; abaixo de 768px calendário vira lista cronológica. Não usar wire:navigate em formulários com dados pendentes: navegação real usa beforeunload; retorno intencional usa confirmação do aplicativo.

## Overlays and feedback

ui.dialog e variante drawer compartilham showModal; ações perigosas nomeiam pauta e consequência. Confirmar cancelamento; aprovação tem contexto na própria decisão. Feedback usa role=status ou alert, não desaparece automaticamente. Top layer de dialog e z-dialog token; aviso de conexão no contexto. Sem toast com segredos.

## Async and resilience

Mutação pessimista; sucesso somente após confirmação do servidor. wire:loading desabilita submissões; criação inclui chave de operação de sessão para impedir duplicação após resposta perdida. Actions verificam Policy, bloqueiam pauta atual e não sobrescrevem decisões. Offline não permite gravar e conserva dados em memória; nenhum briefing/senha em localStorage. Sessão expirada pede novo login e não simula sucesso. Reautenticação pode exigir reentrada de dados; sem armazenamento persistente de rascunhos no MVP. Filtros Livewire cancelam/substituem leituras de estado; sem busca textual remota nesta fase.

## Validation

CreateEventRequestRequest fornece regras/atributos aos Forms e Action. Erros inline e resumo, novalidate e foco no primeiro inválido. Formatos aceitos pelo servidor; limites do schema aplicados. Nenhum user_id/parecer do payload atribuído. Senha mascarada, reveal acessível e sem persistência. Falhas de banco recebem mensagem genérica e mantêm campos, sem divulgar SQL.

## Permission and clipboard

Ocultar controles irrelevantes ao perfil; status decidido mostra parecer e ausência de cancelamento. Autorizar novamente cada chamada Livewire; 403 ao acesso direto sem permissão. Nenhum recurso de clipboard/secreto nesta fase.

## Verification

PHPUnit + Pint + Vite build + audit_project.py strict + DESIGN lint. Navegador desktop e 390px: criação, cancelamento, decisões, calendário público, filtros/vazio, validação, teclado/dialog/Escape, offline e reduced motion. Comparação de formulário de criação/login e drawers de pauta/dia. Evidências no relatório da fase 5 e capturas fora do repositório.
