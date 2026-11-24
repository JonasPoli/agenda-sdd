# Design

## Context

Motivação e escopo estão em `proposal.md`; o comportamento esperado, nas specs desta change.

Estado atual do código, que condiciona as decisões abaixo:

- Symfony **7.4** instalado (`composer.json`), PHP 8.4, `symfony/string` disponível, `maker-bundle` em dev.
- O `.env.local` aponta para **MySQL 8.0**; o `.env` e o `compose.yaml` ainda trazem o PostgreSQL da receita padrão.
- CSRF **stateless** ativo (`config/packages/csrf.yaml`) para os ids `submit` (formulários), `authenticate` e `logout`. Esse modo depende do controller Stimulus `csrf_protection_controller.js`, carregado pelo entrypoint `app` do AssetMapper.
- Turbo Drive ativo via `@symfony/ux-turbo` (`assets/controllers.json`).
- `assets/app.js` importa `styles/app.css`; ou seja, quem carrega o entrypoint `app` carrega também o CSS do site.
- Não existem entidades, controllers, templates de admin nem o Tailwind bundle.

## Goals / Non-Goals

**Goals:**

- Uma única fonte de horário (Brasília) do formulário ao banco e à tela.
- Admin e site com assets totalmente separados: o admin nunca recebe o CSS do Tailwind.
- Regras de integridade (slug único, categoria com eventos, nome único) garantidas também no banco, não só no formulário.
- Controllers finos: consultas nos repositories e regras de slug fora do controller.

**Non-Goals:**

- Componentização sofisticada (Twig Components, Live Components).
- Infraestrutura de Docker: o `compose.yaml` com PostgreSQL não é usado nem alterado aqui.
- Internacionalização além do pt-BR.

## Decisions

### 1. Modelo de dados

```
+-----------------+        +---------------------+        +------------------+
| usuario         |        | categoria           | 1    N | evento           |
+-----------------+        +---------------------+--------+------------------+
| id              |        | id                  |        | id               |
| nome  (120)     |        | nome  (60, unique)  |        | titulo (120)     |
| email (180, uq) |        | cor   (7)  #RRGGBB  |        | slug   (140, uq) |
| roles (json)    |        | ativa (bool, true)  |        | resumo (255,null)|
| senha (hash)    |        +---------------------+        | descricao (text) |
+-----------------+                                       | inicio (datetime)|
                                                          | local (120,null) |
                                                          | publicado (false)|
                                                          | categoria_id  FK |
                                                          +------------------+
```

- `evento.categoria_id` é `NOT NULL` com `ON DELETE RESTRICT`: mesmo que a checagem da aplicação falhe, o banco recusa excluir categoria com eventos.
- Índice em `evento (publicado, inicio)` para a consulta da home.
- `inicio` como `datetime_immutable`. `slug` com 140 caracteres para caber o título de 120 mais o sufixo numérico.
- Alternativa descartada: `ManyToOne` com `onDelete: CASCADE`, que apagaria eventos em silêncio.

### 2. Fuso horário: tudo em America/Sao_Paulo

O fuso padrão do PHP é definido como `America/Sao_Paulo` no boot da aplicação (`Kernel::boot()`), e o Twig usa o mesmo fuso (`twig.date.timezone`). O banco guarda o horário local sem fuso.

- Por quê: o Brasil não tem horário de verão desde 2019, então o horário local é inequívoco, e o formulário, o banco, o Twig e a consulta "a partir de hoje" (`new DateTimeImmutable('today')`) falam a mesma língua sem conversões.
- Alternativa descartada: gravar em UTC e converter na borda (`model_timezone`/`view_timezone` no formulário e filtro no Twig). Mais correto para sistemas multi-fuso, mas acrescenta pontos de erro sem benefício aqui.

### 3. Slug gerado num listener do Doctrine

Um entity listener `prePersist` do `Evento` gera o slug com o `AsciiSlugger` (locale `pt`, sem mapa de símbolos, para que `&` seja removido em vez de virar `and`), converte para minúsculas e consulta o repository para acrescentar `-2`, `-3`… enquanto houver colisão. Não existe `preUpdate`: o slug não muda depois de criado.

- Por quê: qualquer caminho de criação (controller, futura importação, fixtures) passa pela mesma regra, e o controller continua fino.
- O índice único em `slug` é a proteção final contra corrida entre duas gravações simultâneas.
- Alternativa descartada: gerar no controller ou no setter do título (o setter mudaria o slug na edição e não tem acesso ao repository).

### 4. Categoria ativa no formulário de evento

O campo é um `EntityType` com `query_builder` que traz `ativa = true OR id = :atual`, ordenado por nome; `:atual` é a categoria do evento em edição (nulo num evento novo).

- Como o `EntityType` só aceita valores da própria lista, um id de categoria inativa enviado à força é rejeitado pelo componente de formulário, sem validação extra.

### 5. Validações

- Nas entidades, com atributos do Validator: `NotBlank`, `Length`, `Regex('/^#[0-9A-Fa-f]{6}$/')` para a cor e `UniqueEntity` para o nome da categoria, o e-mail do usuário e o slug.
- Os setters normalizam os valores: `trim` no nome e `strtoupper` na cor.
- O nome único sem diferenciar maiúsculas vem da collation `utf8mb4_unicode_ci` do MySQL, usada tanto pelo índice único quanto pela consulta do `UniqueEntity`.
- Mensagens em português definidas direto nos atributos (sem arquivo de tradução nesta versão).

### 6. Segurança

- Provider de entidade (`Usuario`, propriedade `email`), `form_login` em `/login` com `enable_csrf: true`, `logout` em `/logout` e `default_target_path: admin_painel`.
- `access_control`: `^/admin` exige `ROLE_ADMIN`.
- Exclusões: formulário POST com token CSRF **com estado** por registro (`csrf_token('excluir_evento_' ~ id)`) validado com `isCsrfTokenValid`. Esses ids não estão na lista stateless, então usam a sessão e não dependem do JavaScript.
- Comando `app:criar-admin` com `SymfonyStyle::ask`/`askHidden` e validadores que repetem a pergunta; o e-mail duplicado encerra com `Command::FAILURE`. A senha passa pelo `UserPasswordHasherInterface`.

### 7. Assets: dois entrypoints no AssetMapper

```
importmap: app   -> assets/app.js   -> stimulus_bootstrap + styles/app.css (Tailwind)
           admin -> assets/admin.js -> stimulus_bootstrap + styles/admin.css (ajustes do layout)

templates/base.html.twig          (site)  -> importmap('app')
templates/admin/base.html.twig    (admin) -> importmap('admin') + Web Awesome 3 (CDN)
```

- Por quê: os dois layouts precisam do Stimulus (o CSRF stateless dos formulários depende do `csrf_protection_controller`), mas só o site pode carregar o Tailwind. Um entrypoint por área resolve sem gambiarra.
- Alternativa descartada: um `<link>` direto para o CSS do Tailwind no site, mantendo um único entrypoint sem CSS. Funciona, mas foge do fluxo padrão do tailwind-bundle (`app.css` importado no `app.js`).
- Tailwind via `symfonycasts/tailwind-bundle` (Tailwind 4, `@import "tailwindcss"` em `app.css`) e worker `tailwind` (`tailwind:build --watch`) no `.symfony.local.yaml`.

### 8. Layout do admin com Web Awesome 3

- CSS e loader do Web Awesome 3 por CDN com versão fixa (`@3.x.y`, sem `latest`), para que uma atualização do CDN não quebre a aula.
- Grade própria em `admin.css`: menu lateral escuro fixo à esquerda e conteúdo à direita. Os componentes `wa-*` ficam para botões, cards (totais do painel), `wa-badge` (situação e categoria), `wa-icon`, `wa-callout` (mensagens flash) e `wa-dialog`.
- Alternativa considerada: o componente de layout `wa-page`. É viável, mas a grade própria é mais simples de explicar e de controlar.
- Formulários: elementos nativos, estilizados pelos estilos nativos do Web Awesome, com um form theme mínimo (`templates/admin/form/tema.html.twig`) para os erros aparecerem junto ao campo.
- Turbo Drive desligado no admin (`data-turbo="false"` no `<body>`): evita conflito entre a navegação do Turbo e os web components e diálogos. No site ele continua ativo; o formulário inválido já responde 422 pelo `AbstractController::render()`.

### 9. Diálogo de confirmação de exclusão

Um único `wa-dialog` por página de lista e um controller Stimulus `confirmar-exclusao` (entrypoint `admin`). O botão Excluir de cada linha leva a URL de ação, o token e o título; o controller preenche o formulário do diálogo e o abre. Confirmar envia o POST; cancelar só fecha.

- Alternativa descartada: um `wa-dialog` por linha, que duplica marcação em toda a tabela.

### 10. Rotas

| Rota | Nome | Observação |
|---|---|---|
| `GET /` | `site_home` | lista pública |
| `GET /evento/{slug}` | `site_evento` | 404 se não publicado |
| `/login`, `/logout` | `app_login`, `app_logout` | segurança |
| `GET /admin` | `admin_painel` | |
| `/admin/categorias[/nova, /{id}/editar, /{id}/excluir]` | `admin_categoria_*` | excluir só via POST |
| `/admin/eventos[/novo, /{id}/editar, /{id}/excluir]` | `admin_evento_*` | excluir só via POST |

### 11. Cor legível no selo da categoria

Um filtro Twig `cor_texto` calcula a luminância da cor da categoria e devolve `#000000` ou `#FFFFFF`, garantindo texto legível no selo do site e do admin com qualquer cor cadastrada.

## Risks / Trade-offs

- [`utf8mb4_unicode_ci` também ignora acentos: "Musica" e "Música" são considerados o mesmo nome] → Aceito: evita categorias quase duplicadas. Se incomodar, trocar a collation da coluna numa change futura.
- [Fuso fixo no boot: se um dia houver eventos em outros fusos, os dados gravados não terão fuso] → Fora do escopo; a migração para UTC exigiria converter os dados existentes.
- [Um CDN do Web Awesome fora do ar deixa o admin sem estilo] → Versão fixa no CDN; baixar para o AssetMapper fica como evolução.
- [CSRF stateless depende do JavaScript: com o JS bloqueado, os formulários de cadastro falham] → Aceito para um painel interno; as exclusões usam token com estado e funcionam sem JS.
- [Divergência entre o `config.yaml` do OpenSpec (Symfony 8) e o código (7.4)] → Esta change usa o 7.4 instalado; as APIs usadas (Security, Form, Validator, AssetMapper, String) existem nas duas versões.
- [O `.env` e o `compose.yaml` com PostgreSQL podem confundir quem clonar o projeto] → Ajustar o exemplo de `DATABASE_URL` do `.env` para MySQL; o `compose.yaml` fica como está.

## Migration Plan

1. Instalar o tailwind-bundle e configurar os assets.
2. Criar as entidades e gerar **uma** migration inicial (`make:migration`); conferir o SQL (FK `RESTRICT`, índices únicos) antes de rodar `doctrine:migrations:migrate`.
3. Rodar `app:criar-admin` para criar o primeiro acesso.

Rollback: `doctrine:migrations:migrate prev` remove as tabelas; não há dados anteriores a preservar.

## Open Questions

- Atualizar o projeto para o Symfony 8 (como diz o `config.yaml`) ou corrigir o `config.yaml` para 7.4? Não altera esta change; fica para uma change própria.
