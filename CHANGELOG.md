# Histórico de versões

A versão atual do código tem uma única fonte: `config/version.php`. Este histórico registra entregas; publicar notas no banco não altera o código. SemVer: MAJOR para incompatibilidades, MINOR para funcionalidades compatíveis, PATCH para correções compatíveis. Commits não incrementam a versão automaticamente.

## 1.3.0 — 2026-10-13

### Novidades
- Central de Ajuda e Novidades pública, com administração exclusiva da Comunicação.
- Notas por versão, rascunhos, publicação e histórico de alterações com autor e data.
- Modal de novidades após login, com leitura registrada por usuário e versão.

### Melhorias
- Versão do código centralizada e exibida no rodapé; publicação permitida somente para a versão implantada.
- Conteúdo de notas tratado como texto, sem execução de HTML.

### Correções
- Diálogos reconhecem estado aberto inicial, mantendo fechamento por teclado.

### Alterações relevantes
- Três tabelas incrementais: releases, release_revisions e user_release_views. Executar migrations antes de atender tráfego atualizado.
- A Comunicação deve criar e publicar a nota da entrega; nenhuma nota é publicada automaticamente.

## 1.2.0 — 2026-10-13

### Novidades
- Página Sobre e créditos de desenvolvimento para KL Tecnologia.
- Manual de instalação na VPS Hostinger com CloudPanel.

### Melhorias
- Traduções PT-BR, navegação e rodapé responsivos.

### Correções
- Mensagens de erro e contagens traduzidas.

### Alterações relevantes
- Documentação do diretório público e configuração de produção.

## 1.1.0 — 2026-10-13

### Novidades
- Identidade visual institucional consolidada e contratos de interface.

### Melhorias
- Fluxos e telas adaptados ao uso móvel e ao teclado.

### Correções
- Ajustes de interação e apresentação validados na etapa B.

### Alterações relevantes
- Proteção de identidade visual com auditoria automatizada.

## 1.0.0 — 2026-10-13

### Novidades
- Calendário público, solicitações de coordenadores e triagem pela Comunicação.

### Melhorias
- Manual do usuário e documentação de instalação local.

### Correções
- Validação de antecedência e conflitos conforme decisões do projeto.

### Alterações relevantes
- MVP inicial, autorização por perfil e testes de domínio.
