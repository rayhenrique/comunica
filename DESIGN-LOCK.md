# Proteção da identidade visual

## Autoridade

DESIGN.md é a fonte canônica de tokens; UX-CONTRACT.md é a fonte de comportamento. DESIGN-SYSTEM.md e UI-UX-GUIDELINES.md complementam esses documentos sem repetir valores. A baseline técnica design-lock.json contém hashes, não outra paleta.

O usuário aprovou em 13/10/2026 a Etapa B do pacote em UI-UX-AUDIT.md, mantendo paleta e fontes atuais. Essa autorização cobre o refinamento descrito; não concede permissão permanente para mudanças de identidade.

## Escopo protegido

Paleta, famílias tipográficas, tokens, marca, proporções fundamentais e comportamento padronizado exigem **autorização humana explícita antes de alteração**. O verificador cobre DESIGN, UX-CONTRACT, estilos, scripts da interface, todos os templates Blade e o gerador. Novos arquivos visuais precisam entrar na baseline revisada; overrides fora de DESIGN também são detectados por hash.

## Fluxo de mudança

1. Apresentar necessidade, arquivos, comportamento e impacto; solicitar autorização explícita quando atingir o escopo protegido.
2. Registrar a autorização em DECISIONS, implementar somente o escopo aprovado e atualizar fontes canônicas/documentos relacionados.
3. Regenerar theme.generated.css pelo gerador local; nunca editá-lo diretamente.
4. Comparar visualmente, executar testes/build e revisar diff antes de recalcular hashes dos arquivos alterados na baseline. Não há comando de atualização automática que transforme drift em autorização.
5. Revisar baseline e decisão junto com o patch. Fazer commit e relatório; push somente autorizado.

## Verificações

`npm run design:check` compara hashes com finais de linha normalizados, inventaria novos arquivos visuais, verifica versão exata do exportador nos manifests e compara a exportação temporária ao CSS gerado. `npm run design:test` tenta alterações de cor, fonte, raio, override CSS, arquivo gerado e novo arquivo visual em diretórios temporários, sem tocar a baseline real. `npm run design:export` preserva a transformação original @theme static.

O exportador está fixado na versão exata do package.json e package-lock.json; npm ci reproduz a instalação. Prosa protegida também pode disparar drift: isso exige revisão, não implica que toda correção editorial seja mudança de identidade. Baseline identifica a entrega e a referência de aprovação.

## Limites

Hash detecta divergência, não prova consentimento. Quem pode editar o repositório também pode editar baseline e verificador; revisão humana de diff/decisão continua obrigatória. O CI executa o check, mas política de proteção de branch no GitHub não foi configurada. Testes visuais/funcionais complementam a verificação estática e não são substituídos por ela.
