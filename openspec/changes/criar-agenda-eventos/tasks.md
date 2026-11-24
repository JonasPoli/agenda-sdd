# Tasks

## 1. Preparação (dependências e assets)

- [ ] 1.1 Trocar o exemplo de `DATABASE_URL` do `.env` para MySQL 8.0 (sem credenciais reais) e conferir com `php bin/console dbal:run-sql "SELECT 1"` que o banco do `.env.local` responde
- [ ] 1.2 Definir o fuso `America/Sao_Paulo` no `Kernel::boot()` e em `twig.date.timezone`; verificar com `php bin/console debug:config twig date` e com `php -r` ou um `dump(date_default_timezone_get())` temporário
- [ ] 1.3 Instalar `symfonycasts/tailwind-bundle`, rodar `tailwind:init` e deixar `assets/styles/app.css` com `@import "tailwindcss"`; verificar com `php bin/console tailwind:build` sem erros
- [ ] 1.4 Criar o `.symfony.local.yaml` com o worker `tailwind` (`tailwind:build --watch`); verificar que `symfony server:start` sobe o worker
- [ ] 1.5 Criar o entrypoint `admin` (`assets/admin.js` com `stimulus_bootstrap` e `styles/admin.css`) no `importmap.php` e remover o `console.log` de boas-vindas do `app.js`; verificar com `php bin/console debug:asset-map` que `admin.js` não referencia `app.css`
- [ ] 1.6 Verificação do grupo: `php bin/console cache:clear` e `php bin/console importmap:audit` sem erros

## 2. Banco (entidades, migration e regras de persistência)

- [ ] 2.1 Criar a entidade `Usuario` (nome, email único, roles, senha) implementando `UserInterface` e `PasswordAuthenticatedUserInterface`, com validações e `UniqueEntity` no e-mail; verificar com `php bin/console doctrine:schema:validate` (mapeamento)
- [ ] 2.2 Criar a entidade `Categoria` (nome 60 único, cor 7, ativa padrão true) com `NotBlank`, `Length`, `Regex` da cor, `UniqueEntity`, mensagens em pt-BR e setters que fazem `trim` no nome e maiúsculas na cor; verificar com `doctrine:schema:validate`
- [ ] 2.3 Criar a entidade `Evento` (titulo, slug único 140, resumo, descricao, inicio `datetime_immutable`, local, publicado padrão false, `ManyToOne` obrigatório para `Categoria` com `onDelete: RESTRICT`, índice em `publicado, inicio`) com as validações da spec; verificar com `doctrine:schema:validate`
- [ ] 2.4 Criar os repositories com as consultas: `CategoriaRepository` (ordem por nome, ativas mais a atual, contagem de eventos por categoria) e `EventoRepository` (publicados a partir de hoje, próximos N a partir de agora, slug existente, lista do admin em ordem decrescente); verificar com `php bin/console lint:container`
- [ ] 2.5 Criar o entity listener `prePersist` do `Evento` que gera o slug com `AsciiSlugger('pt')` sem mapa de símbolos e acrescenta `-2`, `-3` em colisão; verificar que "Festival de Música & Arte 2026" gera `festival-de-musica-arte-2026`
- [ ] 2.6 Gerar a migration com `make:migration`, conferir no SQL a FK `RESTRICT` e os índices únicos de `usuario.email`, `categoria.nome` e `evento.slug`, e aplicar com `doctrine:migrations:migrate`
- [ ] 2.7 Verificação do grupo: `php bin/console doctrine:schema:validate` com mapeamento e banco em sincronia

## 3. Admin: autenticação e layout

- [ ] 3.1 Configurar o `security.yaml`: provider de entidade por `email`, `form_login` em `/login` com `enable_csrf`, `logout` em `/logout`, `default_target_path` para `admin_painel` e `access_control` `^/admin` com `ROLE_ADMIN`; verificar com `php bin/console debug:firewall main`
- [ ] 3.2 Criar o comando `app:criar-admin` (nome, e-mail, senha com `askHidden`, validação com nova pergunta, e-mail duplicado retornando falha, senha com hash); verificar criando um admin e repetindo o mesmo e-mail para ver a mensagem de erro e código de saída diferente de 0
- [ ] 3.3 Criar o `SecurityController` e `templates/security/login.html.twig` (layout do admin sem menu, Web Awesome, mensagem "E-mail ou senha inválidos.", último e-mail preenchido, redirecionamento de quem já está logado); verificar no navegador login válido, senha errada e e-mail inexistente
- [ ] 3.4 Criar `templates/admin/base.html.twig` com Web Awesome 3 por CDN em versão fixa, `importmap('admin')`, `data-turbo="false"`, menu lateral escuro (Painel, Categorias, Eventos, nome do admin, Sair) com item ativo destacado e mensagens flash em `wa-callout`; verificar com `php bin/console lint:twig templates`
- [ ] 3.5 Criar o form theme `templates/admin/form/tema.html.twig` com os erros junto ao campo e registrá-lo para os formulários do admin; verificar com `lint:twig`
- [ ] 3.6 Criar o filtro Twig `cor_texto` (preto ou branco conforme a luminância); verificar que `#7C3AED` retorna branco e `#FDE68A` retorna preto
- [ ] 3.7 Verificação do grupo no navegador: sem sessão, `/admin/eventos` redireciona para `/login`, o login leva de volta a `/admin/eventos`, Sair encerra a sessão e `/` abre sem login

## 4. Admin: categorias

- [ ] 4.1 Criar o `CategoriaType` (nome, cor com `input type="color"` ou texto, ativa) e o `Admin\CategoriaController` com lista, nova, editar e excluir (POST, CSRF com estado por id, recusa quando há eventos); verificar com `php bin/console debug:router | grep admin_categoria`
- [ ] 4.2 Criar os templates de lista (tabela com amostra da cor, situação em `wa-badge`, quantidade de eventos, estado vazio) e de formulário; verificar com `lint:twig`
- [ ] 4.3 Criar o controller Stimulus `confirmar-exclusao` e o `wa-dialog` único da página, usado pela lista de categorias; verificar que cancelar fecha sem excluir e confirmar envia o POST
- [ ] 4.4 Verificação do grupo no navegador: criar "Música" com `#7c3aed` (gravada como `#7C3AED`), tentar " música " (erro de nome repetido), nome vazio, nome com 61 caracteres e cor `#FFF` (erros), desativar e excluir uma categoria sem eventos com a mensagem de sucesso

## 5. Admin: eventos

- [ ] 5.1 Criar o `EventoType` (título, resumo, descrição, início com `DateTimeType` em widget único, local, categoria com o `query_builder` de ativas mais a atual, publicado; slug fora do formulário e exibido só leitura na edição) e o `Admin\EventoController` com lista, novo, editar e excluir (POST com CSRF); verificar com `debug:router | grep admin_evento`
- [ ] 5.2 Criar os templates de lista (ordem decrescente de início, data em dd/mm/aaaa HH:mm, categoria com cor, situação publicado/rascunho, estado vazio) e de formulário, reutilizando o diálogo de exclusão com o título do evento; verificar com `lint:twig`
- [ ] 5.3 Verificação do grupo no navegador: criar evento como rascunho, salvar com campos obrigatórios vazios e resumo de 256 caracteres (erros), conferir que 15/12/2026 19:30 volta igual na edição, criar "Show de Natal" duas vezes (`show-de-natal` e `show-de-natal-2`), mudar o título sem mudar o slug, ver que uma categoria inativa some do Novo evento mas continua no evento já vinculado, excluir um evento e confirmar que a categoria com eventos não pode mais ser excluída

## 6. Admin: painel

- [ ] 6.1 Criar o `Admin\PainelController` (`/admin`, `admin_painel`) e o template com dois `wa-card` de totais e a lista dos próximos 5 eventos a partir de agora, com selo de rascunho, link para a edição e a mensagem "Nenhum evento agendado." quando vazio; verificar com `lint:twig`
- [ ] 6.2 Verificação do grupo no navegador: com mais de 5 eventos futuros, um passado e um rascunho, o painel mostra os totais corretos, só os 5 próximos em ordem crescente, sem o passado e com o rascunho identificado

## 7. Site público

- [ ] 7.1 Reescrever `templates/base.html.twig` como layout do site com Tailwind: `lang="pt-BR"`, cabeçalho "Agenda" com link para `/`, rodapé e conteúdo responsivo; verificar com `lint:twig`
- [ ] 7.2 Criar o `SiteController` com `site_home` (publicados a partir de hoje, ordem crescente) e `site_evento` (busca por slug e publicado, `createNotFoundException` caso contrário); verificar com `debug:router | grep site_`
- [ ] 7.3 Criar o template da home com cards (título, data dd/mm/aaaa HH:mm, local e resumo omitidos quando vazios, selo da categoria com `cor_texto`, link para o detalhe) e a mensagem "Nenhum evento agendado no momento."; verificar com `lint:twig`
- [ ] 7.4 Criar o template de detalhe com todos os dados, descrição com `nl2br` sobre texto escapado e link de volta; verificar com `lint:twig`
- [ ] 7.5 Verificação do grupo no navegador: a home mostra só publicados de hoje em diante (inclusive um evento de hoje que já começou), em ordem crescente; o detalhe de um publicado abre; um evento publicado com data passada não aparece na home, mas o endereço dele abre o detalhe (200); um rascunho e um slug inexistente retornam 404; em 375 px de largura os cards ficam em uma coluna sem rolagem horizontal; o HTML da home não carrega Web Awesome e o do admin não carrega Tailwind

## 8. Verificação integrada

- [ ] 8.1 Rodar `php bin/console lint:twig templates`, `php bin/console lint:container`, `php bin/console doctrine:schema:validate` e `php bin/console tailwind:build`, todos sem erros
- [ ] 8.2 Percorrer o fluxo completo do zero: `doctrine:migrations:migrate` num banco limpo, `app:criar-admin`, login, criar uma categoria e um evento publicado para amanhã, conferir o painel, abrir a home e o detalhe pelo card, despublicar e ver o detalhe virar 404

## Workflow follow-up

- Arquivar a change com `/opsx:archive` depois da revisão, sincronizando as specs principais.
- Decidir numa change própria entre atualizar para o Symfony 8 ou ajustar o `config.yaml` para 7.4.
