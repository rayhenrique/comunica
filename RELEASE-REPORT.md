# Relatório técnico — entrega 1.3.0

## Diagnóstico e implementação
A entrega anterior estava em commit local 07e8cb5 (v1.2.0), pendente de push. Não havia notas persistidas nem confirmação de leitura. Implementados SemVer central em config/version.php/AppVersion, CHANGELOG histórico, Ajuda/Novidades públicas, editor e publicação exclusivos de communication_admin, histórico com autor/snapshot e modal por usuário/versão após login. Policies verificadas nas rotas, componentes e Actions; IDs sensíveis bloqueados pelo Livewire. Conteúdo público é texto escapado. Publicação exige versão igual ao código em execução; versões futuras não aparecem publicamente. Nenhuma versão é incrementada por commit ou publicação.

Migrations criaram somente releases, release_revisions e user_release_views. Aplicadas no MySQL local, preservando 3 usuários e 10 solicitações. Nenhuma nota foi publicada automaticamente no banco real; a Comunicação publica pelo painel após revisar. Notas e leituras persistem; alterações publicadas não reapresentam o modal para leitores anteriores.

## Verificação
- Pest: 111 testes e 721 assertions aprovados; suíte completa e testes de autorização, SemVer, unicidade, rascunhos, publicação idempotente, auditoria, rollback transacional, edição, escape de HTML e leitura independente por usuário/versão.
- Pint PSR-12 e build Vite executados.
- Nove verificações de identidade e versionamento aprovadas. Auditoria de identidade e exportação, incluindo proteção da fonte de versão e presença da entrada CHANGELOG.
- Playwright CLI em SQLite isolado: login real, modal inicialmente aberto, Escape, persistência após reload. Ajuda, Novidades, lista administrativa, edição e histórico verificados em 360/390/768/1024/1440 px (25 combinações), sem overflow. Screenshot móvel revisada. Sem plugin Browser disponível; utilizado CLI conforme skill de testes.

## Operação e limites
Não houve deploy na VPS. Executar migrations e atualizar caches conforme manual de instalação antes de liberar tráfego. A Comunicação deve criar/publicar a nota da versão implantada. Histórico anterior em CHANGELOG foi consolidado dos registros existentes; não foram inventadas publicações no banco. Backups de notas/leituras necessários antes de rollback de migrations. Publicar notas não publica código.

## Controle da entrega
Fase 7 adicionada sem alterar tarefas concluídas das fases 1–6. AGENTS, decisões, documentação e histórico atualizados. Commit da entrega identificado pela tag Git v1.3.0; o próprio hash não é incluído nos arquivos do commit para evitar referência circular. Push de main e tags v1.2.0/v1.3.0 conforme autorização expressa.
