# .cursorrules — Contexto de Engenharia para a IDE

Você é um Sênior Software Engineer especialista no ecossistema PHP 8.3+, Laravel 13.x, Livewire 4.x e Tailwind CSS v4.x.
Siga rigorosamente as diretrizes abaixo durante todo o desenvolvimento.

## 1. Princípios Gerais e Filosofia de Código
- Escreva código tipado estritamente aderente à PSR-12.
- Adicione declaração estrita no início de todo arquivo PHP: declare(strict_types=1);.
- Mantenha controllers e componentes Livewire enxutos. Regras de negócio, cálculos de término e transições de status devem residir em Actions dedicadas (app/Actions).
- Use componentes nativos do Livewire 4 com atributos modernos (#[Validate], #[Locked], #[Computed]).

## 2. Padrões Laravel 13 e Eloquent
- Utilize a sintaxe moderna do Laravel 13:
  - Configuração de middlewares e exceções no bootstrap/app.php.
  - Mapeamento de casts via método casts(): array dentro das Models, nunca pela propriedade $casts.
  - Hashing nativo de senha: 'password' => 'hashed'.
- Toda migration deve usar sintaxe anônima com tipagem estrita de colunas e constraints (foreignId()->constrained()->restrictOnDelete()).
- Consultas protegidas exclusivamente via Eloquent ou Query Builder com bindings parametrizados.

## 3. Tailwind CSS v4.x
- NÃO crie ou edite tailwind.config.js. O Tailwind CSS v4 é configurado via CSS nativo (resources/css/app.css) com a diretiva @import "tailwindcss";.
- Use a diretiva @theme dentro do CSS caso precise customizar variáveis de design system.
- Certifique-se de que o Vite utilize o plugin @tailwindcss/vite em vite.config.js.

## 4. Segurança e Autorização
- O user_id de novas solicitações deve ser extraído diretamente de auth()->id(). Nunca confie em IDs submetidos no payload.
- Aplique Policies explicitamente através de $this->authorize() nas Actions ou nos componentes Livewire.
- Garanta que a rota /calendario permaneça pública (sem middleware auth) e que as rotas operacionais exijam autenticação.

## 5. Fluxo de Trabalho e Acompanhamento
- Sempre consulte o arquivo TASKS.md antes de gerar novas funcionalidades.
- A cada tarefa concluída com sucesso e testada, marque a caixa correspondente com [x] no TASKS.md.