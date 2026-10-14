# Decisões de requisitos

## 13/10/2026 — fase 4

1. Antecedência mínima: o usuário confirmou **48 horas corridas**, incluindo fins de semana e feriados. PRD mencionava 48 horas, MVP exigia úteis e TASKS combinava úteis com now()->addHours(48). PRD RN01, MVP e TASKS 4.3 foram alinhados à resposta explícita, preservando o restante dos documentos.
2. Cobertura: o usuário confirmou que **qualquer coverage/both simultânea gera alerta**, mesmo em locais diferentes. O PRD mencionava cobertura externa sem campo para distinguir interna/externa. RF05 e TASKS 4.1 foram alinhados sem alterar o schema. Choque de local também exige sobreposição, independentemente do tipo de demanda.

Critério operacional de intervalos: início inclusivo e término exclusivo. Um evento que começa exatamente quando outro termina não se sobrepõe; inclui-se a interseção parcial ou contenção. Este critério é documentado para os filtros e alertas e coberto por testes. Conflitos são avisos e não impedem aprovação soberana.

## 13/10/2026 — fase 6

O usuário autorizou **Pest 4**, em substituição à versão 3 prevista no título da fase 6. Pest 3 exige PHPUnit 11 e conflita com PHPUnit 12 instalado. Pest 4 utiliza PHPUnit 12 e pest-plugin-laravel 4.1 aceita Laravel 13. TASKS foi alinhado à decisão, sem alterar os quatro critérios de teste ou a stack da aplicação. A suíte PHPUnit existente será preservada e executada pelo Pest juntamente com os novos testes funcionais.

## 13/10/2026 — refinamento visual

Após a Etapa A, o usuário respondeu “pode executar a etapa B”, autorizando o pacote de UI-UX-AUDIT.md: cards mobile na triagem/histórico, compactação e acessibilidade de controles, calendário com título até 40px e seleção mobile, contornos/status, documentação e proteção da identidade. Paleta e fontes atuais preservadas; nenhuma fonte alternativa aplicada. Aprovação limitada a este pacote, com lógica, dados e permissões preservados; sem push automático.

## 13/10/2026 — créditos, apresentação e instalação

O usuário autorizou créditos para KL Tecnologia (kltecnologia.com), link/página Sobre, interface PT-BR, responsividade mobile-first e manual Hostinger/CloudPanel baseado nas imagens fornecidas. A navegação/rodapé e textos padronizados foram atualizados neste escopo, mantendo tokens, fontes, permissões e regras. A baseline visual deve ser revisada com essas alterações; não houve autorização para instalar ou modificar a VPS nesta solicitação.

## 13/10/2026 — Ajuda, novidades e versão

Escopo autorizado: SemVer central, histórico CHANGELOG, notas no banco com auditoria, administração exclusiva da Comunicação, Ajuda/Novidades públicas e modal por usuário/versão no login. A leitura é confirmada ao fechar o modal. Publicação exige versão igual ao código; não incrementa versão e não ocorre automaticamente. Entrega compatível com novas funcionalidades: 1.3.0. Migrations incrementais sem alterar as tabelas de negócio. Push de main e tags autorizado pelo usuário. Os novos componentes seguem os tokens e diálogos existentes.
