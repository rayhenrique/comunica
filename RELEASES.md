# Versionamento e notas de versão

`config/version.php` é a única fonte da versão atual do código. `AppVersion` valida MAJOR.MINOR.PATCH, sem prefixo, pré-release ou zeros à esquerda. Interface e publicação usam essa fonte. CHANGELOG e versoes.md são históricos, não configurações.

Toda entrega avalia o impacto SemVer. Alterar a versão exige decisão explícita de entrega; commits e publicação de notas não a incrementam. Uma alteração compatível com funcionalidades novas incrementa MINOR; correções incrementam PATCH; incompatibilidades incrementam MAJOR.

Após implantar o código e executar `php artisan migrate --force`, entrar como communication_admin, abrir **Administrar novidades**, criar uma nota com a versão exibida, título, resumo e conteúdo, revisar e confirmar **Publicar**. Rascunhos de versões futuras podem ser preparados, mas a publicação exige igualdade com a versão do código em execução. Datas são atribuídas pelo servidor na publicação. Conteúdo é texto simples, com quebras de linha; HTML é escapado. Cada criação, edição e primeira publicação gera uma revisão com autor, data e snapshot. Editar uma nota publicada preserva a versão e a data original; não dispara novo modal para quem já leu.

Ajuda e Novidades são públicas. Rascunhos, revisões e autores internos não são públicos. Notas de versões superiores ao código em execução ficam ocultas, inclusive após rollback.

O login bem-sucedido habilita a verificação de novidades. O modal aparece para a nota publicada da versão atual quando o usuário ainda não confirmou sua leitura. Fechar por botão ou Escape registra a leitura; navegar sem fechar mantém a pendência. A leitura é persistida por usuário e versão e não depende do navegador. Sem nota publicada, não há modal. Uma nova versão publicada cria nova pendência no próximo login. Notas não devem conter dados pessoais, credenciais ou informações internas, pois serão públicas.

Tabelas incrementais: releases (versão única e conteúdo), release_revisions (auditoria), user_release_views (chave composta usuário/versão). As tabelas de solicitações e usuários não são recriadas. Realizar backup antes de atualização em produção; rollback de migrations remove as notas e leituras, portanto preservar backup desses dados.
