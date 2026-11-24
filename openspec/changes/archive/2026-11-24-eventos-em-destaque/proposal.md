# Proposal

## Why

Hoje a home trata todos os eventos da mesma forma, em ordem de data, e o administrador não tem como dar visibilidade a um evento importante que ainda está longe na agenda. Marcar eventos como destaque e mostrá-los num bloco no topo da home resolve isso sem mudar o resto da lista.

## What Changes

- Novo campo **Destaque** (sim ou não) no evento, desmarcado por padrão, no formulário de cadastro e edição do admin.
- Indicação de destaque na lista de eventos do admin (`/admin/eventos`).
- Bloco **Em destaque** no topo da home, antes da lista, com até 3 eventos: os destaques publicados de hoje em diante, do mais próximo para o mais distante. Sem destaques visíveis, o bloco não aparece.
- A lista da home deixa de repetir os eventos mostrados no bloco. Um destaque que não couber no bloco (o 4º em diante) continua na lista, para não sumir da home.
- O administrador pode marcar quantos destaques quiser: o limite de 3 vale só para o que a home exibe, sem erro no formulário.

## Fora do escopo

- Bloquear ou avisar no formulário quando já houver 3 ou mais destaques.
- Ordem manual dos destaques, período de validade do destaque e destaque automático.
- Visual diferente para o detalhe do evento em destaque (`/evento/{slug}`).
- Destaques no painel `/admin` e filtro por destaque na lista do admin.
- Imagem de capa ou qualquer upload.

## Capabilities

### New Capabilities

Nenhuma.

### Modified Capabilities

- `agenda-publica`: novo requisito para o bloco de destaques no topo da home; o requisito da lista de próximos eventos passa a excluir os eventos exibidos no bloco.
- `gestao-eventos`: o cadastro e a edição de evento ganham o campo destaque, e a listagem do admin passa a indicar quais eventos estão em destaque.

## Impact

- **Banco:** nova coluna `evento.destaque` (booleana, padrão falso), por migration.
- **Código:** entidade `Evento`, `EventoRepository` (consulta dos destaques da home), `EventoType`, `SiteController::home`, templates `site/home.html.twig`, `admin/evento/index.html.twig` e o formulário do evento.
- **Dependências:** nenhuma nova.
