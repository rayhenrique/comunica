---
version: alpha
name: Comunica
description: Agenda institucional e gestão de pautas com leitura clara e presença discreta.
colors:
  primary: "#17665B"
  ink: "#203344"
  muted: "#516675"
  background: "#F3F6F8"
  surface: "#FFFFFF"
  border: "#CBD6DF"
  success: "#195C3A"
  warning: "#855C11"
  danger: "#9C3333"
typography:
  body:
    fontFamily: "Segoe UI"
  display:
    fontFamily: "Trebuchet MS"
  mono:
    fontFamily: "Consolas"
rounded:
  DEFAULT: "0.75rem"
  control: "0.5rem"
spacing:
  page-max: "76rem"
  section-gap: "2rem"
components:
  button:
    rounded: "{rounded.control}"
    backgroundColor: "{colors.primary}"
    textColor: "{colors.surface}"
  field:
    rounded: "{rounded.control}"
    backgroundColor: "{colors.surface}"
    textColor: "{colors.ink}"
  dialog:
    rounded: "{rounded.DEFAULT}"
    backgroundColor: "{colors.surface}"
    textColor: "{colors.ink}"
  caption:
    textColor: "{colors.muted}"
    backgroundColor: "{colors.surface}"
  feedback-success:
    textColor: "{colors.success}"
    backgroundColor: "{colors.surface}"
  conflict-alert:
    textColor: "{colors.warning}"
    backgroundColor: "{colors.background}"
  field-error:
    textColor: "{colors.danger}"
    backgroundColor: "{colors.surface}"
  separator:
    backgroundColor: "{colors.border}"
    height: "1px"
---

# Comunica — design de produto

## Overview

A referência é um quadro de agenda de uma assessoria institucional: datas como pontos de orientação, pautas legíveis e decisões com rastreabilidade. Público brasileiro, coordenadores, Comunicação e visitantes, conforme PRD. Registro de produto, português do Brasil, sem estética promocional.

A assinatura é o calendário com faixas discretas por tipo de demanda e números de dias destacados. Formulários e decisões permanecem familiares. Não usar gradientes decorativos, imagens sem função, dashboards de métricas inventadas ou excesso de cartões.

Este documento é a fonte de tokens. `node scripts/export-design.mjs` usa `designmd export --format css-tailwind` e adapta o bloco para `@theme static`, gerando resources/css/theme.generated.css. Essa adaptação preserva tokens consumidos em CSS sem utilities. app.css importa esse arquivo, adiciona fallbacks de fontes e aplica aliases. Não editar o arquivo gerado. UX-CONTRACT.md é o contrato comportamental.

## Colors

Primary sinaliza ações e dias selecionados; ink e muted separam hierarquia textual. Background é azul acinzentado, surface branco, border delimita superfícies. Success, warning e danger são semânticos, sempre acompanhados de texto. Somente tema claro nesta fase; forced-colors preserva cores de sistema.

## Typography

Display é usado nos títulos; body nos controles e textos; mono nos horários. Fontes do sistema evitam dependências externas e mudanças de layout. Corpo em 16px, entrelinha 1.5, títulos de 24–40px. Datas dd/mm/aaaa, horários 24h, calendário gregoriano e America/Sao_Paulo como configuração inicial do projeto.

## Layout

Cabeçalho horizontal e conteúdo central de até page-max. Ritmo de 0.5rem/1rem/section-gap. Formulários rolam no documento, sem altura fixa. Abaixo de 768px o calendário vira lista com seletor de dia; tabelas internas mantêm colunas em região horizontal visível. Dialogs limitados à viewport com corpo rolável.

## Elevation & Depth

Bordas e superfícies claras definem hierarquia. Sombra reservada a dialogs. Sem sombras em cada linha. Feedback ocupa região estável e não desloca os botões principais.

## Shapes

Control aplica-se a campos e botões; DEFAULT a painéis e dialogs. Ícones são traços simples ou caracteres acompanhados de texto, sem biblioteca adicional.

## Components

resources/views/components/ui contém Button, Field, Status, Feedback, Pagination e Dialog. Button suporta intenção neutral/primary/danger. Estado ocupado mantém dimensões e desabilita ativação repetida. Native selects e date/datetime-local têm popup deliberadamente pertencente ao navegador/SO; a aplicação controla rótulos, validação e formato de armazenamento, sem prometer geometria ou idioma do popup em todos os sistemas.

Dialog usa elemento dialog com showModal: foco, fundo inerte, Escape e restauração de foco nativos, conteúdo/ações pertencentes ao aplicativo. Textarea não redimensiona; cresce até limite e permanece rolável. Scrollbars recebem baseline global por variáveis sem classe de opt-in.

Movimento restrito a feedback, transições de 120ms, desativado em prefers-reduced-motion. Estados e tipos de demanda nunca dependem apenas da cor. Links são navegação, botões são ações. Sem gráficos nesta fase.

## Do's and Don'ts

- Preserve título, local e parecer completos no detalhe.
- Use os mesmos rótulos/status e primitive de diálogo em todas as telas.
- Não exponha briefing ou dados de moderação no calendário público.
- Não use alert/confirm/prompt nativos nem carregue Alpine duas vezes.
