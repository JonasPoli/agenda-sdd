# Proposal

## Why

O projeto é hoje apenas o esqueleto do Symfony, sem nenhuma entidade, rota ou tela. Precisamos da primeira versão utilizável da Agenda de Eventos: um site público onde qualquer pessoa vê os próximos eventos e uma área administrativa protegida onde o administrador cadastra categorias e eventos.

## What Changes

- Entidades `Usuario`, `Categoria` e `Evento`, com migrations no MySQL.
- Autenticação por e-mail e senha em `/login`, com `/admin` restrito a `ROLE_ADMIN`, e logout.
- Comando de console `app:criar-admin`, que pede nome, e-mail e senha e cria um administrador.
- Painel em `/admin` com o total de categorias, o total de eventos e os próximos 5 eventos.
- CRUD de categorias (nome, cor hexadecimal, ativa) e de eventos (título, slug gerado do título, resumo, descrição, data e hora de início, local, categoria, publicado), com lista em tabela, diálogo de confirmação na exclusão e mensagens de sucesso.
- Bloqueio da exclusão de uma categoria que tenha eventos.
- Layout administrativo próprio com Web Awesome 3 (via CDN) e menu lateral escuro.
- Site público com Tailwind: home com os eventos publicados a partir de hoje em cards e página de detalhe em `/evento/{slug}`, com 404 para evento inexistente ou não publicado.
- Instalação do `symfonycasts/tailwind-bundle` e configuração do worker `tailwind` no `.symfony.local.yaml`.

## Fora do escopo

- Cadastro, edição ou listagem de usuários pela interface (o administrador é criado só pelo comando).
- Recuperação de senha, "lembrar-me" e perfis além de `ROLE_ADMIN`.
- Inscrição, RSVP, vagas ou ingressos.
- Data e hora de término, eventos recorrentes e eventos de dia inteiro.
- Imagem de capa ou qualquer upload de arquivos.
- Busca, filtro por categoria, paginação e calendário mensal no site público.
- Paginação, ordenação por coluna e busca nas tabelas do admin.
- Lista de eventos passados no site público.
- Exportação `.ics`, feeds, metatags de SEO/Open Graph e envio de e-mails.
- Atualização do Symfony 7.4 (instalado) para o Symfony 8.
- Testes automatizados (a verificação desta versão é por comandos de console e pelo navegador).

## Capabilities

### New Capabilities

- `autenticacao-admin`: login por e-mail e senha, proteção de `/admin` com `ROLE_ADMIN`, logout e criação do administrador pelo comando `app:criar-admin`.
- `painel-admin`: página inicial do admin com os totais e os próximos eventos, e o layout administrativo compartilhado.
- `gestao-categorias`: cadastro de categorias no admin, com validações e regra de exclusão.
- `gestao-eventos`: cadastro de eventos no admin, com geração de slug, validações e escolha de categoria ativa.
- `agenda-publica`: site público com a lista de próximos eventos publicados e a página de detalhe.

### Modified Capabilities

Nenhuma.

## Impact

- **Código novo:** `src/Entity` (Usuario, Categoria, Evento), `src/Repository`, `src/Controller` (site, login e admin), `src/Form`, `src/Command/CriarAdminCommand.php`, `templates/admin/**`, `templates/site/**` e `templates/security/login.html.twig`.
- **Configuração:** `config/packages/security.yaml` (provider de entidade, form login, logout e `access_control`), `config/packages/twig.yaml` (fuso padrão), `.symfony.local.yaml` (worker do Tailwind) e `importmap.php`/`assets` para o CSS do site.
- **Dependências:** `symfonycasts/tailwind-bundle` (Composer). Web Awesome 3 vem por CDN, sem dependência local.
- **Banco:** migrations criando as tabelas `usuario`, `categoria` e `evento`.
