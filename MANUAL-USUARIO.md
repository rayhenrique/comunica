# Manual do usuário — Comunica

Versão de referência: **1.1.0**, de 13/10/2026.

O Comunica reúne pedidos de apoio à Comunicação e uma agenda de eventos confirmados. Acesse pelo endereço fornecido pela instituição. No ambiente local de desenvolvimento, o endereço padrão é `http://127.0.0.1:8000`.

## Perfis e acesso

| Perfil | O que pode fazer |
| --- | --- |
| Visitante | Consultar o calendário e detalhes públicos, sem login |
| Coordenador | Criar solicitações, acompanhar as próprias e cancelar pedidos pendentes |
| Comunicação | Consultar todos os setores, analisar conflitos e decidir pedidos pendentes |

Para entrar, acesse `/login`, informe seu e-mail e senha e selecione **Entrar**. A conta deve ser disponibilizada pela administração; o MVP não possui cadastro público ou recuperação de senha por e-mail. Para encerrar o acesso, use **Sair**. Em computador compartilhado, encerre a sessão ao terminar.

## Consultar o calendário

1. Abra `/calendario`; não é necessário entrar.
2. Navegue pelos meses usando os controles anterior/próximo.
3. Selecione um dia para abrir o painel de eventos.
4. Consulte título, início, término, local, tipo de demanda e público-alvo.
5. Feche o painel usando **Fechar** ou a tecla Escape.

No celular, a agenda aparece como lista cronológica com seletor de data. Somente pautas aprovadas ou reagendadas aparecem publicamente. Briefings e justificativas internas ficam nos painéis protegidos. Um dia vazio pode apenas não ter eventos confirmados.

## Coordenador: criar uma solicitação

1. Entre e abra **Nova solicitação** (`/solicitacoes/nova`).
2. Preencha os dados abaixo.
3. Confira o término previsto e selecione **Criar solicitação**.
4. Aguarde a confirmação e acompanhe o pedido em **Minhas solicitações**.

| Campo | Orientação |
| --- | --- |
| Título do evento | Nome claro, até 150 caracteres |
| Tipo de demanda | Cobertura, Criação de arte ou Cobertura e arte |
| Data e hora de início | No ambiente inicial, horário de Brasília; no mínimo 48 horas corridas a partir do envio |
| Duração estimada | Minutos inteiros, de 1 a 65.535; por exemplo, 60 para uma hora |
| Local | Identificação do local, até 255 caracteres |
| Público-alvo | Público previsto, até 255 caracteres |
| Briefing / descrição | Texto opcional com objetivo, contexto e apoio necessário; sem anexos |

O término é calculado automaticamente. As 48 horas incluem fins de semana e feriados: um pedido enviado na sexta às 9h pode indicar domingo às 9h, mas não domingo às 8h59. Confira o prazo novamente se demorar para enviar.

Se houver erro, corrija os campos indicados e envie novamente. Os valores permanecem no formulário durante essa correção. Sair da página ou reabrir uma sessão não recupera um rascunho; copie textos longos antes de sair. O sistema pede confirmação ao abandonar alterações pendentes.

## Coordenador: acompanhar e cancelar

Abra **Minhas solicitações** (`/solicitacoes`). Use o filtro de status e a paginação para localizar um pedido. Selecione **Detalhes** para consultar os dados e o parecer da Comunicação.

No celular, cada solicitação aparece em um card com datas, demanda, status e ações. No desktop, os mesmos dados aparecem em tabela. Filtros e paginação funcionam nas duas apresentações.

| Status | Significado |
| --- | --- |
| Pendente | Aguarda decisão da Comunicação; o solicitante pode cancelar |
| Aprovado | Confirmado na data solicitada e visível no calendário |
| Reagendado | Confirmado na nova data definida pela Comunicação; data original e motivo preservados |
| Recusado | Não confirmado; consulte a justificativa |
| Cancelado | Cancelado pelo solicitante; permanece no histórico |

Para cancelar, use **Cancelar** no pedido pendente e confirme em **Cancelar solicitação**. Use **Manter solicitação** para desistir. Se a Comunicação decidir antes da confirmação, o cancelamento será negado e o estado atual será preservado. Pedidos cancelados não são reabertos: para pedir apoio novamente, crie uma nova solicitação respeitando o prazo.

Solicitações decididas não podem ser editadas pelo coordenador. O reagendamento já confirma a nova agenda e dispensa aceite do solicitante. Consulte regularmente seu painel: não há envio de notificações externas no MVP.

## Comunicação: analisar e decidir

No celular, a triagem apresenta cards com setor, horário, local, demanda, status, conflitos e botão de análise. No desktop, mantém a tabela. As permissões e decisões são as mesmas nas duas apresentações.

1. Entre com perfil da Comunicação e abra **Triagem de pautas** (`/comunicacao/triagem`).
2. Filtre por status, setor, demanda ou período. **Limpar filtros** retorna à fila padrão de pendentes.
3. Selecione **Analisar** e confira dados, briefing e alertas.
4. Escolha uma decisão e confirme no botão correspondente.

| Decisão | Procedimento |
| --- | --- |
| Aprovar | Escolha **Aprovar** e confirme **Aprovar solicitação** |
| Reagendar e aprovar | Informe nova data/hora e justificativa; confirme **Reagendar e aprovar** |
| Recusar | Informe justificativa com pelo menos 10 caracteres; confirme **Recusar solicitação** |

O reagendamento preserva a data original e recalcula o término. A aprovação e o reagendamento tornam o evento visível no calendário. A recusa mantém a justificativa no histórico do solicitante.

O alerta de conflito aparece quando eventos confirmados se sobrepõem no mesmo local, ou quando duas demandas de cobertura/ambos são simultâneas, mesmo em locais diferentes. Um evento que começa exatamente quando outro termina não gera sobreposição. Ao alterar a data de reagendamento, confira o alerta atualizado. O aviso não bloqueia a decisão da Comunicação.

Somente pedidos pendentes podem receber decisão. Caso outra pessoa já tenha decidido, atualize a fila e confira o estado vigente. Para sair da análise sem decidir, use **Voltar à triagem**.

## Erros e recuperação

| Situação | Como proceder |
| --- | --- |
| Credenciais inválidas | Confira e-mail/senha; procure a administração se não tiver acesso |
| Muitas tentativas de login | Aguarde o tempo indicado antes de tentar novamente |
| Campo inválido | Leia a mensagem, corrija o valor e envie novamente |
| Sem resultados | Ajuste ou limpe os filtros; confira mês e data no calendário |
| Sem conexão | Restabeleça a rede e confira se a operação foi concluída antes de repetir |
| Sessão expirada | Preserve textos ainda não enviados, entre novamente e confira o histórico |
| Falha ao salvar | Confira o painel para verificar se houve gravação; repita se necessário |
| Acesso negado | Verifique o perfil e se o pedido pertence a você; contate a administração |

Use Tab para navegar entre campos e botões, Enter/Espaço para acionar controles conforme o navegador e Escape para fechar diálogos. Seletores de data e listas podem ter aparência diferente conforme o sistema operacional.

Para solicitar suporte, informe a tela, a ação, o horário e a mensagem apresentada. Não envie sua senha. A administração deve fornecer o canal institucional de suporte.
