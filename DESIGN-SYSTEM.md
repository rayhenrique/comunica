# Sistema visual do Comunica

Identidade preservada no refinamento aprovado em 13/10/2026. **DESIGN.md é a fonte canônica dos valores**, usada pelo gerador. Este documento explica uso; não mantém outra tabela numérica de tokens.

## Identidade e tipografia

Comunica é uma agenda institucional com decisões rastreáveis. Hierarquia editorial, textos claros e data como orientação. Preservar a marca, a paleta e as famílias body/display/mono de DESIGN. Body atende leitura e controles; display atende títulos e marca; mono orienta horários. Fallbacks ficam no alias de app.css. Nenhuma fonte remota adicionada.

A escala descrita em DESIGN rege títulos; calendar-title limita o título público à escala aprovada. Evitar títulos promocionais desproporcionais, ícones sem função, gradientes e métricas artificiais.

## Papéis dos tokens

| Grupo canônico | Aplicação |
| --- | --- |
| colors.primary | Ações principais, foco e seleção |
| colors.ink / muted | Texto principal e auxiliar; muted também delimita campos |
| colors.background / surface / border | Fundo, superfícies e divisórias |
| colors.success / warning / danger | Resultado, conflito e erro; sempre com texto |
| typography.body / display / mono | Leitura, hierarquia e dados temporais |
| rounded.DEFAULT / control | Superfícies/diálogos e controles |
| spacing.page-max / section-gap | Largura e ritmo de composição |

Valores, aliases e exportação são consultados em DESIGN e no CSS gerado; não copiar hexadecimais em telas. Uma divisória decorativa não substitui o contorno contrastante de um campo interativo.

## Componentes e variantes

Button neutral/primary/danger preserva tamanho durante carregamento. Field input/select/textarea e compact mantém rótulo, foco, ajuda real e erro associado. Status sempre apresenta texto; aprovado usa superfície de sucesso, reagendado acrescenta contorno sem criar nova cor. Feedback só ocupa espaço com mensagem/estado. Dialog/drawer usa a mesma primitive nativa e rodapé fixo.

Request-card, request-actions e conflict-status são compartilhados entre layouts mobile/desktop: não duplicar autorização ou lógica de domínio. Cards mobile são representações de solicitações, não painéis estatísticos. Calendar event-strip separa horário/título, limita título na grade e oferece detalhe completo.

## Estados e profundidade

Hover e seleção usam os mesmos tokens; foco visível permanece independente do hover. Disabled e busy não simulam sucesso. Sombra apenas no diálogo. Preserve reduced-motion e forced-colors. O inventário e o procedimento de aprovação ficam em DESIGN-LOCK.md; comportamento em UX-CONTRACT.md e sua aplicação em UI-UX-GUIDELINES.md.
