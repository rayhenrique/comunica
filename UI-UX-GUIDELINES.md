# Diretrizes de interface e experiência

Este documento aplica **UX-CONTRACT.md**, fonte canônica de comportamento. **DESIGN.md** determina os tokens; DESIGN-SYSTEM explica seus papéis. Havendo divergência, não escolher uma regra silenciosamente: registrar e solicitar decisão.

## Navegação e adaptação

Preservar rotas, perfis e destinos do contrato. Navegação indica tela atual e permite quebra de nomes longos. Abaixo do breakpoint canônico das listas, triagem/histórico usam cards; acima dele, tabelas. Cards mantêm status, demanda, datas e todas as ações autorizadas. Triagem inclui setor, intervalo, local e conflito. Uma coleção paginada alimenta ambas as representações; somente a visível participa da interação.

Filtros permanecem acessíveis e ativos em URL, com retorno à primeira página. Compactar ajuda e espaçamento, nunca esconder filtros ativos por conveniência visual. Paginação quebra linha quando necessário. Estado vazio explica como recuperar resultados.

## Calendário

Preservar grade mensal desktop e lista cronológica mobile. Estado selecionado tem sinal visual e aria-pressed em ambos. Horário separado do título; títulos extensos não expandem colunas. O contador de outras pautas continua levando ao detalhe completo do dia. Preservar campos públicos e privacidade definidos no PRD; não expor briefing em atributos/tooltip.

## Formulários e feedback

Rótulo persistente, campo e ajuda real formam um grupo. Emitir aria-describedby somente para elementos existentes; erro aparece associado e com anúncio. Ajuda sem conteúdo não reserva altura. Validação preserva valores; não recriar Forms nem introduzir regras novas. A previsão de término acompanha duração e continua subordinada ao cálculo do servidor.

Botões possuem alvo confortável, foco e estado ocupado com dimensões estáveis. Mostrar senha preserva mascaramento inicial, autocomplete e controle acessível. Feedback vazio não ocupa altura; carregamento/erro pode ocupar espaço, mas não desloca o botão da operação. Sucesso somente após resposta do servidor.

## Diálogos, teclado e acessibilidade

Preservar showModal, fundo inerte, Escape e restauração de foco. Corpo flexível rola; cabeçalho/ações permanecem visíveis. Valores completos podem quebrar linha; títulos truncados na grade continuam completos no detalhe. Nenhuma ação crítica depende de hover. Testar Tab/Shift+Tab no conteúdo e ciclos completos; a passagem para a interface do navegador não autoriza interação no fundo inerte.

Meta de acessibilidade do contrato permanece; revisão visual não equivale a certificação. Verificar contraste dos estados, nomes acessíveis, associação de erros, foco não oculto e conteúdo com zoom. Manter recursos nativos de select/data aceitos no contrato.

## Verificação

Testar os três perfis, as cinco larguras aprovadas, filtros/paginação, formulário inválido, decisão/cancelamento, vazio, títulos e nomes extensos, muitos eventos, foco/Escape, rolagem e ausência de overflow do documento. Executar testes, Pint, build e auditoria da identidade. Capturas e dados de QA ficam fora do código e do banco real. Relatar separadamente dispositivos físicos, navegadores e estados não exercitados.
