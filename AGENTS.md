# Comunica — instruções permanentes

Aplicação institucional de gestão de solicitações de apoio à Comunicação e calendário público.

- Leia integralmente PRD.md, MVP-SCOPE.md, DATABASE-SCHEMA.md, TASKS.md e RULES.md antes de implementar. São as referências obrigatórias; todas as regras de RULES.md permanecem aplicáveis, sem redução.
- Consulte TASKS.md antes de qualquer implementação. Execute apenas a tarefa autorizada, em ordem, sem antecipar etapas ou funcionalidades fora do MVP.
- Preserve os documentos originais. Atualize somente o checklist autorizado após conclusão efetiva e validação; não resolva divergências silenciosamente.
- Stack: PHP 8.3+ compatível com Laravel 13.x, Livewire 4.x, Tailwind CSS 4.x, MySQL 8.0+, Vite e Pest conforme planejamento. Não substituir frameworks.
- Use PSR-12, declare(strict_types=1), código legível, tipado, seguro e organizado, com responsabilidades claras e controllers/componentes enxutos.
- Mantenha regras de negócio em Actions específicas em app/Actions. Exija Policies e autorização explícita. Use Eloquent/Query Builder com bindings seguros; nunca interpole entradas em SQL.
- Priorize interfaces responsivas, minimalistas e recursos nativos. Não crie tailwind.config.js; configuração Tailwind 4 será CSS-first.
- Execute testes e verificações apropriadas antes de concluir cada tarefa. Não declare resultados não executados; registre impedimentos de infraestrutura.
- Não exponha nem versione .env, credenciais ou segredos. Verifique o estado Git quando disponível; não faça push automaticamente.
- Ao terminar cada etapa, apresente diagnóstico, implementação, testes, pendências, controle de tarefas e notas da versão. Mantenha histórico resumido.
- Para trabalho de UI, consulte integralmente DESIGN.md, UX-CONTRACT.md, DESIGN-SYSTEM.md, UI-UX-GUIDELINES.md e DESIGN-LOCK.md. DESIGN permanece fonte canônica de tokens e UX-CONTRACT de comportamento; documentos complementares não criam valores concorrentes.
- Alterações de paleta, tipografia, tokens, marca, identidade, proporções fundamentais ou comportamento padronizado exigem autorização humana explícita e registro em DECISIONS.md. Preserve o gerador; nunca edite theme.generated.css diretamente. Execute design:check/design:test e valide a interface; alterações da baseline também exigem revisão contra o escopo aprovado. Não faça push sem autorização.
- Antecedência mínima definida pelo usuário em 13/10/2026: 48 horas corridas, incluindo fins de semana e feriados. A divergência original foi resolvida; preserve esta decisão registrada em DECISIONS.md.
- Conflitos de cobertura: qualquer sobreposição entre duas demandas coverage/both gera alerta, mesmo em locais diferentes, conforme decisão do usuário em 13/10/2026. Mesmo local exige sobreposição, independentemente do tipo. Alertas não bloqueiam moderação.
- America/Sao_Paulo é configuração inicial de ambiente, não regra do PRD.
- A conexão MySQL local foi validada e a fase 1 foi concluída. Credenciais ficam exclusivamente no .env; não as invente ou exponha. Execute futuras migrations somente no escopo autorizado, preservando dados existentes; não use migrate:fresh/reset em banco com dados sem autorização explícita.
