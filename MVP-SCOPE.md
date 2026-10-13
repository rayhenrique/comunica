# MVP-SCOPE.md — Definição e Fechamento de Escopo

## 1. O Que Está no Escopo do MVP (In Scope)

### Fluxo de Solicitação (Coordenador)
- Formulário reativo de abertura de pauta com bloqueio de antecedência mínima de 48 horas corridas (decisão de 13/10/2026).
- Seleção de tipo de demanda: `Cobertura`, `Arte` ou `Ambos`.
- Histórico visual exclusivo das solicitações criadas pelo próprio usuário com status atualizado.
- Cancelamento de solicitações pelo solicitante enquanto o status for `pending`.

### Triagem e Moderação (Assessoria de Comunicação)
- Fila centralizada de pautas com listagem detalhada e filtros.
- Detecção algorítmica e alerta visual em tempo real para choque de horários e conflitos no mesmo local.
- Poder de moderação soberana:
  - Aprovação direta da data original;
  - Reagendamento com nova data/horário e motivo registrado (já aprova e fixa a nova agenda);
  - Recusa com justificativa textual obrigatória (mínimo de 10 caracteres).

### Agenda Pública (Acesso Livre)
- Rota pública `/calendario` dispensando autenticação.
- Visualização mensal navegável com indicadores de pautas confirmadas.
- Painel/gaveta lateral ao clicar no dia listando horário de início/término, título, local e público-alvo.

---

## 2. O Que Foi Postergado / Fora do Escopo (Out of Scope)

* ❌ **Notificações por Canais Externos:** Disparo de e-mails, WhatsApp API ou SMS (todo feedback é consultado pelo painel).
* ❌ **Gestão Interna de Equipe da Comunicação:** Atribuição de tarefas individuais (qual fotógrafo ou designer fará a entrega).
* ❌ **Upload de Arquivos Pesados:** Anexos de arquivos PSD/AI, fotos em alta resolução ou PDFs de briefing (briefing puramente textual).
* ❌ **Negociação Bidirecional:** O coordenador não precisa dar aceite na nova data após reagendamento soberano da Comunicação.
* ❌ **Multi-Tenancy:** Separação para órgãos externos em instâncias distintas.

---

## 3. Critérios de Aceite e Métricas de Sucesso

| Critério | Validação Prática |
| :--- | :--- |
| **Proteção de Prazos** | Tentativa de submissão com antecedência menor que 48 horas corridas é rejeitada com mensagem clara. |
| **Isolamento de Dados** | Coordenador do Setor A não visualiza pautas do Setor B nem tem acesso a IDs de terceiros (retorna `403 Forbidden`). |
| **Detecção de Choque** | Painel da Comunicação exibe badge de alerta amarelo/vermelho ao identificar duas pautas no mesmo intervalo de horário e local. |
| **Transparência Pública** | Qualquer cidadão ou colaborador sem login consegue navegar no `/calendario` e visualizar os eventos do mês e do dia. |
| **Compilação da Stack** | Frontend compila sem dependência de `tailwind.config.js` utilizando o motor do Tailwind v4 no Vite. |
