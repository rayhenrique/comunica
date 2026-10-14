# Comunica

Sistema institucional de solicitações de apoio à Comunicação e calendário público, desenvolvido pela [KL Tecnologia](https://kltecnologia.com). **Versão 1.2.0**: apresentação pública, créditos, interface PT-BR e guia de instalação na VPS.

## Funcionalidades

- Coordenador: abertura com antecedência de 48 horas corridas, histórico próprio e cancelamento de pendentes.
- Comunicação: triagem com filtros, alertas de conflito, aprovação, reagendamento e recusa justificada.
- Visitante: calendário mensal e detalhes públicos dos eventos confirmados.
- Autenticação, Policies explícitas, término calculado no servidor e auditoria de moderação.

O MVP não inclui notificações externas, anexos, distribuição de tarefas da equipe ou aceite de reagendamento.

## Documentação

| Documento | Conteúdo |
| --- | --- |
| [Manual do usuário](MANUAL-USUARIO.md) | Acesso e operação dos três perfis |
| [Instalação Hostinger/CloudPanel](MANUAL-INSTALACAO-VPS.md) | Implantação na VPS, com as imagens fornecidas, HTTPS, banco, usuários e atualização |
| [Histórico de versões](versoes.md) | Entregas e procedimento de versionamento |
| [PRD](PRD.md) e [Escopo](MVP-SCOPE.md) | Requisitos e limites do MVP |
| [Banco de dados](DATABASE-SCHEMA.md) | Tabelas, tipos e índices |
| [Tarefas](TASKS.md) | Checklist das fases 1–6 |
| [Regras](RULES.md) e [AGENTS](AGENTS.md) | Convenções obrigatórias de desenvolvimento |
| [Decisões](DECISIONS.md) | Prazo, conflitos e compatibilidade Pest |
| [Design](DESIGN.md) e [Contrato de UX](UX-CONTRACT.md) | Tokens visuais e componentes compartilhados |
| [Sistema visual](DESIGN-SYSTEM.md), [Diretrizes](UI-UX-GUIDELINES.md) e [Proteção](DESIGN-LOCK.md) | Aplicação da identidade e revisão obrigatória de mudanças |
| [Relatório da fase 5](PHASE-5-REPORT.md) | Implementação das telas e QA de navegador |
| [Relatório da fase 6](PHASE-6-REPORT.md) | Testes de aceite e dependências |

## Requisitos do ambiente

As dependências exatas são fixadas em composer.lock e package-lock.json. O ambiente validado usa PHP 8.5, Laravel 13, Livewire 4, Tailwind CSS 4, Vite 7, MySQL 8.4, Composer 2 e Node 24 com npm 11. Para outra instalação, execute `composer check-platform-reqs` e respeite os requisitos dos lockfiles; a referência PHP 8.3+ do planejamento não substitui as exigências efetivas das dependências.

No Windows, WampServer fornece PHP/MySQL. Confira se o PHP correto está no PATH e se as extensões exigidas estão ativas. Os testes usam PDO SQLite; a aplicação usa PDO MySQL. Não há tailwind.config.js: o tema Tailwind 4 é configurado em CSS.

## Instalação local no Windows / PowerShell

Execute na raiz do projeto:

```powershell
composer install
npm ci
```

Somente em uma instalação nova que ainda não possui .env:

```powershell
Copy-Item .env.example .env
php artisan key:generate
```

Edite o .env local com APP_URL, conexão MySQL, nome do banco, usuário e senha fornecidos pela administração. O banco deve existir. Nunca versione esse arquivo. No ambiente já configurado, preserve .env e APP_KEY; trocar a chave pode invalidar dados criptografados e sessões.

A configuração inicial usa português brasileiro e America/Sao_Paulo. O fuso é uma escolha de ambiente; as telas atuais indicam horário de Brasília.

Para aplicar somente migrations pendentes:

```powershell
php artisan migrate:status
php artisan migrate
```

Antes de atualizar um banco com dados, faça backup e confira as migrations. Não use migrate:fresh/reset no banco existente. Para preparar dados demonstrativos em uma instalação de desenvolvimento nova, execute opcionalmente:

```powershell
php artisan db:seed
```

Os seeders criam um usuário Comunicação, dois coordenadores e dez pautas demonstrativas. Não execute seeders como parte automática de uma implantação institucional.

## Acesso local

As contas demonstrativas são comunicacao@example.test, educacao@example.test e saude@example.test. Não há senha padrão publicada. No ambiente local, defina uma senha de pelo menos 12 caracteres para uma conta existente:

```powershell
php artisan comunica:password comunicacao@example.test
```

O comando solicita senha e confirmação sem exibição, e só funciona em local/testing. Repita com o e-mail de um coordenador para testar esse perfil. Não há cadastro público ou recuperação de senha por e-mail.

Em dois terminais separados:

```powershell
php artisan serve --host=127.0.0.1 --port=8000
```

```powershell
npm run dev
```

Acesse http://127.0.0.1:8000/calendario ou http://127.0.0.1:8000/login. Para usar assets compilados, execute `npm run build` e encerre o Vite de desenvolvimento normalmente, garantindo que public/hot não aponte para um servidor encerrado.

Se usar o Apache do WampServer, configure o DocumentRoot/VirtualHost para a pasta **public** do projeto, com reescrita de URLs habilitada. Não exponha a raiz que contém .env. Mantenha storage e bootstrap/cache graváveis pelo processo PHP.

## Rotas de operação

| Rota | Acesso |
| --- | --- |
| /calendario | Público |
| /sobre | Apresentação pública do sistema e desenvolvimento |
| /login | Entrada de usuários |
| /solicitacoes/nova | Coordenador |
| /solicitacoes | Histórico próprio; Comunicação segue para a triagem |
| /comunicacao/triagem | Comunicação |
| POST /logout | Encerramento de sessão autenticada |

## Testes e verificações

```powershell
php artisan test
php vendor/bin/pest tests/Feature/Phase6
php vendor/bin/pint --test --preset=psr12
composer validate --strict
composer audit
npm run build
npm run design:check
npm run design:test
```

Pest 4 foi autorizado para compatibilidade com PHPUnit 12 e Laravel 13. Os testes PHPUnit anteriores foram preservados. A validação da versão 1.2.0 passou com **98 testes e 666 assertions**, além de sete verificações de proteção de design. phpunit.xml configura SQLite em memória, sessão e cache em array. Não direcione testes com RefreshDatabase ao banco MySQL que contém dados.

A interface foi validada em Chrome no Windows, incluindo viewports mobile simulados. Não há certificação de acessibilidade, teste de carga ou validação em todos os navegadores. As evidências e limitações estão nos relatórios.

## Estrutura

- app/Actions: criação, cancelamento, moderação, cálculos e consultas de domínio.
- app/Policies: autorização por perfil/proprietário.
- app/Livewire: telas e formulário reativo.
- resources/views/components/ui: componentes compartilhados.
- resources/css e resources/js: tema e comportamento da interface.
- database/migrations e database/seeders: schema e demonstração.
- tests/Feature/Phase6: critérios de aceite em Pest.

## Controle de versão

O Git utiliza a branch main e o remoto [rayhenrique/comunica](https://github.com/rayhenrique/comunica). A versão local de referência é v1.2.0; consulte versoes.md e `git tag --list` para as tags efetivamente criadas. v1.1.0 já está publicada; esta entrega 1.2.0 permanece local até autorização de envio.

```powershell
git status --short
git log --oneline --decorate
git diff
```

Revise alterações e exclusões antes de adicionar arquivos. Versione lockfiles para instalações reproduzíveis. .env, dependências instaladas, logs, sessões, dumps e arquivos de banco local estão excluídos. Git não substitui backup do banco.

## Atualização de uma instalação

Faça backup do banco e do ambiente, obtenha a versão desejada, instale as dependências pelos lockfiles, revise/aplique migrations pendentes, compile os assets e valide os fluxos. Preserve a APP_KEY existente. Ajuste APP_ENV e APP_DEBUG ao ambiente de destino; a configuração local documentada não equivale a uma implantação de produção validada.

## Créditos

Desenvolvimento: [KL Tecnologia](https://kltecnologia.com). Construído sobre Laravel e suas dependências. Consulte as licenças dos respectivos pacotes; este README não define uma licença institucional própria para o Comunica.
