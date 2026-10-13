# PRD.md — Sistema de Gestão de Pautas e Calendário da Comunicação

## 1. Visão Geral do Produto
O sistema centraliza e padroniza o fluxo de solicitações de apoio, cobertura e design enviadas pelos coordenadores de setor para a Assessoria de Comunicação. Substitui o controle manual por planilhas, elimina atritos de agendamento por meio de detecção preditiva de choques de horário/local e disponibiliza um calendário público responsivo com as ações institucionais confirmadas.

## 2. Matriz de Perfis e Permissões (RBAC)

| Perfil | Acessos e Ações Permitidas |
| :--- | :--- |
| **Público / Visitante** | - Visualizar calendário mensal consolidado (`/calendario`).<br>- Consultar detalhes das pautas confirmadas do dia selecionado. |
| **Coordenador Solicitante (`coordinator`)** | - Criar nova solicitação de evento/pauta.<br>- Visualizar histórico exclusivamente de suas próprias solicitações.<br>- Acompanhar pareceres e justificativas da moderação.<br>- Cancelar solicitações enquanto o status for `pending`. |
| **Comunicação (`communication_admin`)** | - Visualizar fila global de solicitações de todos os setores.<br>- Visualizar alertas em tempo real de choque de horários e locais.<br>- Aprovar solicitações.<br>- Reagendar e aprovar de forma soberana (definindo nova data/hora e justificativa).<br>- Recusar solicitações com justificativa textual obrigatória.<br>- Acessar a visão de calendário operacional irrestrita. |

## 3. Requisitos Funcionais (RF)

### Módulo 1: Solicitação de Pauta (Coordenador)
* **RF01 - Criação de Solicitação:** O coordenador preenche formulário com:
  - Título do Evento (`string`, max 150)
  - Tipo de Demanda (`coverage` [Cobertura], `art` [Criação de Arte], `both` [Ambos])
  - Data e Horário de Início (`datetime`)
  - Duração Estimada em minutos (`integer`: 30, 60, 90, 120, 180, etc.)
  - Local do Evento (`string`, max 255)
  - Público-Alvo (`string`, max 255)
  - Briefing / Descrição Geral (`text`)
* **RF02 - Painel "Minhas Solicitações":** Tabela paginada e reativa exibindo apenas as solicitações do usuário autenticado, com status visuais (`Pendente`, `Aprovado`, `Reagendado`, `Recusado`), data original, data confirmada e parecer da Comunicação.
* **RF03 - Cancelamento pelo Solicitante:** Permitir cancelar apenas solicitações com status `pending`.

### Módulo 2: Triagem e Moderação (Comunicação)
* **RF04 - Fila de Moderação:** Interface centralizada com filtros por período, setor de origem e tipo de demanda.
* **RF05 - Detecção Visual de Choque:** Na visualização de cada solicitação pendente, o sistema deve computar e exibir um alerta visual (amarelo/vermelho) caso existam eventos confirmados (`approved` ou `rescheduled`):
  - No mesmo local com sobreposição de intervalo de horários;
  - Em horários coincidentes entre duas demandas `coverage` ou `both`, mesmo em locais diferentes (decisão de 13/10/2026; não há distinção interna/externa no schema).
* **RF06 - Moderação Soberana:** Ações executivas:
  - **Aprovar:** Transiciona status para `approved`.
  - **Reagendar & Aprovar:** Permite definir nova data/horário de início, recalcula `end_at`, preserva `original_start_at`, exige `moderation_notes` e define status como `rescheduled`.
  - **Recusar:** Exige justificativa textual (`moderation_notes`, mín. 10 caracteres) e transiciona para `rejected`.

### Módulo 3: Calendário Institucional (Acesso Livre)
* **RF07 - Visualização Mensal Pública:** Rota `/calendario` acessível sem autenticação com navegação mensal (próximo/anterior) e marcadores nos dias com pautas confirmadas (`approved` ou `rescheduled`).
* **RF08 - Detalhe do Dia (Drawer / Modal):** Ao selecionar um dia, listar cronologicamente os eventos confirmados com: Título, Horário de início e término, Local, Tipo de Demanda e Público-alvo.

## 4. Requisitos Não-Funcionais (RNF)
* **RNF01 - Stack Tecnológica:** Laravel 13.x, PHP 8.3+, Livewire 4.x e Tailwind CSS v4.x.
* **RNF02 - Performance de Consulta:** Índices compostos cobrindo intervalo de datas e status para carregar o calendário mensal em menos de 150ms.
* **RNF03 - Responsividade:** Layout Mobile-First; a grade mensal converte-se em lista cronológica com seletor de data em resoluções inferiores a 768px.
* **RNF04 - Isolamento e Segurança:** Policies estritas aplicadas no Eloquent, impedindo vazamento de dados entre coordenadores via URL ou injeção de IDs.
* **RNF05 - Auditoria e Rastreabilidade:** Uso de `SoftDeletes` e preservação dos dados de moderação (`moderated_by`, `moderated_at`, `original_start_at`).

## 5. Regras de Negócio Críticas (RN)
* **RN01 - Antecedência Mínima:** Solicitações devem ser abertas com no mínimo 48 horas corridas de antecedência em relação ao horário de início previsto (decisão de 13/10/2026).
* **RN02 - Cálculo Automático de Término:** O campo `end_at` deve ser calculado no backend: `start_at + duration_minutes`.
* **RN03 - Não-Bloqueio de Choques:** A existência de sobreposição emite apenas advertência visual à moderação; a decisão de aprovar permanece soberana da Comunicação.
* **RN04 - Imutabilidade Pós-Decisão:** Solicitações moderadas (`approved`, `rescheduled`, `rejected`) não podem ser editadas pelo coordenador solicitante.
