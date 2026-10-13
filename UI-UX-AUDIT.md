# Comunica — Etapa A: auditoria e proposta

Data: 13/10/2026. Base: v1.0.0, commit b765cc0. **Proposta aguardando aprovação; nenhuma alteração visual implementada.**

## Diagnóstico e método

PRD, MVP-SCOPE, DATABASE-SCHEMA, RULES, TASKS, AGENTS, DESIGN, UX-CONTRACT e DECISIONS lidos. Revisados Blade, CSS, JavaScript, componentes Livewire, Actions e testes. As fases 1–6 estão concluídas; esta proposta é um refinamento posterior autorizado pelo novo pedido, sem mudança de regras, banco ou permissões.

Skills utilizadas: frontend-design, frontend-design-premium, frontend-testing-debugging e playwright. Browser plugin indisponível; utilizado Playwright CLI já instalado, sem dependências novas. Auditoria visual ao vivo de calendário e login em http://127.0.0.1:8000. Telas protegidas avaliadas pelo código real; seus fluxos autenticados não foram repetidos no navegador nesta etapa. Evidências anteriores da fase 5 são históricas e não substituem nova validação após implementação.

## Achados e proposta por tela

| Tela/componente | Evidência atual | Ajuste proposto | Impacto esperado |
| --- | --- | --- | --- |
| Triagem | Tabela min-w-200 dentro de overflow-x-auto; nenhum modo de cards. As ações ficam em coluna distante no celular | Manter tabela a partir de 768 px; lista semântica de cards abaixo disso, com título/setor, intervalo/local, demanda, status, conflito e ação. Consumir a mesma coleção/paginação e componentes de status/botão | Ações e dados disponíveis sem rolagem lateral no celular |
| Filtros da triagem | Cinco campos, grid gap-4, p-5, ajuda vazia por campo e linha separada para limpar | Variante compacta de ui.field; reduzir apenas espaçamento interno e alinhar limpar aos filtros. Em mobile, manter campos acessíveis, sem esconder automaticamente filtros ativos | Mais pautas visíveis, sem remover controles ou reduzir área de toque |
| Histórico do coordenador | Tabela min-w-190, também dependente de rolagem horizontal | Aplicar o mesmo padrão de lista mobile, com data original, data confirmada, demanda, status, detalhes e cancelamento autorizado | Consistência entre as duas listas internas; depende de aprovação expressa desta extensão |
| Calendário desktop | Faixa truncada em uma linha combina horário e título; apenas duas pautas por dia e acesso a restantes por contador. Colunas medem cerca de 101 px na largura 768 | Separar horário de título; título com até duas linhas e min-width:0; manter limite de duas pautas e contador com acesso ao detalhe. Preservar sete colunas e a lista mobile | Melhor leitura sem permitir que títulos longos alarguem a grade |
| Calendário mobile | Lista cronológica correta; 32 px de feedback vazio e ajuda vazia do seletor aumentam separação antes das pautas. Seleção visível está implementada na grade, mas não nos itens mobile | Eliminar a reserva sem conteúdo e aplicar estado selecionado equivalente aos itens mobile com semântica consistente | Menos espaço sem função e orientação após fechar o detalhe |
| Formulário de criação | Ajuda renderizada mesmo sem hint; painel de término usa a mesma área visual de um campo completo | Renderizar ajuda apenas quando existir; manter IDs ARIA corretos. Compactar previsão junto à duração, sem alterar cálculo nem campos | Ritmo mais uniforme e relação clara entre duração e término |
| Login | Em 390 px, cada ajuda vazia mede 20 px; mostrar senha mede 16 px de altura. Mensagem inicial e formulário demandam bastante rolagem | Compactar espaçamentos compartilhados e dar área de toque de 44 px ao controle de senha. Preservar o texto e o layout existente | Acesso mais eficiente e controle de senha mais confortável |
| Diálogos | Corpo rolável e rodapé fixo já existem; altura do corpo depende de subtrações fixas, além do layout flex | Preservar showModal, Escape, foco e rodapé; revisar limites em 360 px, zoom e teclado virtual antes de ajustar. Não substituir a primitive | Evitar regressão de ações visíveis em conteúdo longo |
| Status | approved/rescheduled compartilham exatamente o tratamento de sucesso; diferenciação existe no texto | Preservar texto e tokens; diferenciar reagendado por tratamento de contorno/peso, sem nova cor | Leitura rápida da diferença entre confirmação original e nova data |
| Navegação | Nome do usuário sem regra específica de quebra no cabeçalho interno; texto longo pode competir com Sair | Testar nomes/setores longos e permitir quebra controlada sem esconder saída | Cabeçalho estável nas menores larguras |

Arquivos envolvidos: resources/views/livewire/communication/triage.blade.php, coordinator/my-requests.blade.php, coordinator/create-request.blade.php, public/calendar-view.blade.php e auth/login.blade.php; resources/views/components/ui/{field,feedback,status,dialog,pagination}.blade.php; resources/views/components/layouts/{app,public}.blade.php; resources/css/app.css. JavaScript só deve mudar se a verificação de foco exigir correção, preservando a arquitetura atual. Components Livewire, Actions e Policies permanecem funcionais e sem novas regras.

## Hierarquia e acessibilidade

- O título do calendário usa text-4xl/md:text-5xl (36/48 px), enquanto a descrição tipográfica em DESIGN informa títulos de 24–40 px. Proposta: aprovar teto de 40 px para esse título e alinhar fonte canônica e implementação; não alterar silenciosamente uma das fontes.
- As bordas dos inputs usam border contra surface: contraste calculado de **1,48:1** (1,36:1 contra background). A orientação proposta é usar muted já existente para delimitar controles, mantendo border nas divisórias decorativas. Depende de aprovação do tratamento visual; não exige mudar a paleta. Uma auditoria completa WCAG precisa avaliar o papel da delimitação e demais estados.
- Contraste calculado contra branco: primary 6,80:1, ink 12,98:1, muted 5,99:1 e warning 5,94:1. Isso valida essas combinações, não todos os estados/misturas da aplicação.
- Estados vazios de ajuda e feedback não devem ser confundidos com reserva necessária do spinner. Na implementação, preservar o tamanho do botão e medir deslocamento durante carregamento/erro; uma redução de espaço não autoriza instabilidade de interação.
- Abrir o dia, consultar detalhes, Escape e restauração do gatilho funcionaram nesta inspeção. Após um Tab, a medição não encontrou foco dentro do diálogo; pode corresponder à passagem pela interface do navegador com apenas um controle focável. Requer ciclo completo Tab/Shift+Tab e fundo inerte na Etapa B antes de concluir sobre contenção de foco. Não foi declarado como defeito confirmado.
- Não foi comprovado overflow causado por título longo: é risco de conteúdo a validar com fixtures extensas, sem alterar registros reais.

## Proposta tipográfica — decisão separada

**Opção recomendada para o primeiro pacote: preservar Segoe UI, Trebuchet MS e Consolas**, corrigindo escala, espaçamento e hierarquia. Mantém a identidade atual e não adiciona download de fontes. Limite: a presença dessas famílias varia por plataforma, então o fallback pode alterar métricas e personalidade.

Alternativa a aprovar: **Source Sans 3 para corpo e títulos**, preservando a marca atual e o tratamento numérico inicialmente. A Adobe a descreve como uma família desenhada para interfaces e disponibiliza WOFF2 sob OFL. Meu julgamento de design: suas formas abertas e variedade de pesos combinam com uma interface institucional discreta e permitem separar hierarquia por peso, sem um display chamativo. Fonte: [repositório oficial](https://github.com/adobe-fonts/source-sans).

Se escolhida, hospedar os arquivos localmente, fixar versão, incluir licença, limitar pesos e testar caracteres pt-BR, fallback, carregamento e layout. Isso acrescenta payload; o tamanho e desempenho precisam ser medidos antes de aprovação final da entrega. Não foi baixada nem aplicada nesta auditoria. Não há promessa de melhora mensurável de legibilidade sem comparação visual.

Uma alternativa com personalidade mais técnica é IBM Plex Sans, distribuída oficialmente pela IBM sob OFL: [fonte oficial](https://github.com/IBM/plex). Julgamento: menos alinhada à discrição pretendida que Source Sans 3; não recomendo adicionar múltiplas famílias apenas para diferenciação. Nenhuma troca tipográfica é necessária para corrigir os problemas operacionais.

Nenhuma alteração de paleta, marca ou gradientes é proposta. Os ajustes de proporções, contornos e comportamento mobile acima também entram na aprovação; não serão tratados como autorização automática para mudanças futuras.

## Tokens e reprodução do gerador

DESIGN.md continua canônico; scripts/export-design.mjs exporta css-tailwind e adapta somente @theme para @theme static. theme.generated.css permanece gerado, sem edição manual.

Achado: o script invoca `npx --yes -p @google/design.md`, **sem versão fixa** e fora de package-lock.json. O cache disponível nesta máquina contém **0.4.0**. Duas exportações diretas dessa versão produziram o mesmo conteúdo; com a adaptação existente, o resultado coincide com theme.generated.css (comparação normalizando finais de linha e whitespace terminal). Isso comprova reprodução local nessa versão, sem garantir reprodução futura do comando flutuante.

Proposta: preservar o gerador e sua transformação, fixar @google/design.md como dependência de desenvolvimento em versão exata/lockfile e chamá-lo localmente. Não fixar versão sem confirmar que reproduz os tokens aprovados.

O uso de rounded-xl em .panel coincide hoje com rounded.DEFAULT, mas depende do valor Tailwind em vez de referenciar diretamente o token protegido. Há também valores de proporção, espaçamento e overlay fora do YAML. Portanto um hash de DESIGN sozinho não protege toda a identidade.

## Documentação e proteção propostas para a Etapa B

| Documento | Responsabilidade; sem fonte concorrente |
| --- | --- |
| DESIGN-SYSTEM.md | Referenciar os nomes canônicos de DESIGN; explicar papéis, uso e variantes sem repetir valores de paleta/tipografia |
| UI-UX-GUIDELINES.md | Referenciar UX-CONTRACT; explicar aplicação por tela, reflow, formulários e estados |
| DESIGN-LOCK.md | Definir escopo protegido, responsáveis, evidência de aprovação e atualização revisada da baseline |
| AGENTS.md | Exigir consulta a todos os documentos visuais e autorização explícita para paleta, tipografia, tokens, proporções fundamentais e comportamento padronizado |

Proposta de verificação automatizada: baseline do conjunto canônico de tokens; exportação temporária e comparação com CSS gerado; inspeção de overrides/valores visuais fora da fonte; testes negativos de alteração de cor/fonte/raio; comando de CI que falha em drift. Alteração aprovada deve ter decisão registrada e baseline atualizada no mesmo conjunto revisado. Não haverá comando que declare uma mudança automaticamente autorizada.

Limite explícito: qualquer pessoa que possa editar o repositório também pode mudar baseline e verificador. Aprovação humana, revisão de diff e proteção de branch complementam o teste; um hash não prova consentimento. Não configurar políticas do GitHub sem pedido específico.

Os três documentos definitivos e o verificador **não foram criados nesta Etapa A**, para não registrar propostas como identidade aprovada. DESIGN e UX-CONTRACT atuais precisam ser atualizados juntos se os cards mobile forem aprovados, pois hoje documentam tabelas internas com rolagem horizontal.

## Resultados executados

| Verificação | Resultado real |
| --- | --- |
| php artisan test --compact | 94 testes passaram; 614 assertions; 7,42 s |
| Pint --test --preset=psr12 | Passou |
| npm run build | Passou; 58 módulos |
| DESIGN lint | Zero erros/avisos |
| audit_project.py --mode strict | Zero findings; cobertura Blade limitada, não substitui revisão manual |
| Reprodução de tokens com exportador 0.4.0 | Duas saídas iguais e compatíveis com CSS atual |
| Calendário em 360, 390, 768, 1024 e 1440 × 900 | Conteúdo presente; nenhum overflow horizontal do documento |
| Login em 390 × 844 | Conteúdo presente, sem overflow horizontal; reservas vazias e altura do reveal medidos |
| Calendário: abrir dia → detalhes → Escape | Abriu e fechou; foco voltou ao botão do dia |
| Console de calendário/login | Zero erros/avisos; nenhuma sobreposição de erro de framework observada |

Capturas fora do repositório: C:/Users/rayhe/.codex/visualizations/2026/10/13/01a136cd-de81-7cb0-8d3e-d7cfb2f113db/comunica-ui-audit. Calendário atual tem cinco pautas confirmadas no mês; nenhuma escrita na base real foi realizada.

Ainda não executados nesta etapa: matriz visual completa das telas autenticadas, casos com títulos/nomes muito longos e muitos eventos, paginação com múltiplas páginas no navegador, zoom 200%, teclado virtual físico, contraste de todas as variantes, regressão visual automatizada e avaliação em outros navegadores. Os testes funcionais existentes de filtros, formulários, autorização e privacidade passaram, mas não provam esses aspectos visuais.

## Pacote submetido à aprovação

1. Cards mobile na triagem e no histórico, mantendo tabelas desktop e comportamento de filtros/paginação.
2. Compactação de filtros, ajuda e feedback vazios; revisão de previsão de término e controle de senha.
3. Calendário com horário/título separados, duas linhas limitadas, seleção mobile e título com teto de 40 px.
4. Distinção dos status, contraste de contornos com tokens existentes e robustez de diálogos/cabeçalho.
5. Documentação definitiva, AGENTS, gerador com versão fixa, proteção de tokens e validação completa nas cinco larguras.

Recomendação tipográfica: manter fontes atuais neste pacote. A alternativa Source Sans 3 requer escolha explícita adicional.

## Plano de entrega após aprovação

Commits separados de componentes/layout, documentação/proteção e testes/notas de versão. Executar suíte, Pint, build, drift e testes negativos; validar os três perfis em 360/390/768/1024/1440 px, foco/Escape/scroll/ações, filtros/paginação e erros. Não alterar PRD, regras de negócio, schema ou permissões. Sem push automático.

## Controle e notas desta etapa

Etapa A concluída: inspeção, achados e proposta registrados. Etapa B pendente de aprovação conforme o pedido explícito. Nenhuma tarefa das fases concluídas foi remarcada e nenhuma mudança visual foi aplicada. Este relatório é a única alteração de fonte desta entrega e não muda a versão funcional v1.0.0.
