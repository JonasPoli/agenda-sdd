# Design

## Context

Motivação e escopo em `proposal.md`; comportamento nas specs desta change (`agenda-publica` e `gestao-eventos`).

Estado atual do código que condiciona a abordagem:

- A home (`SiteController::home`) recebe uma única lista de `EventoRepository::publicadosAPartirDeHoje()`: publicados com início a partir de hoje 00:00 (Brasília), em ordem crescente de início e depois de id. A consulta usa o índice `evento (publicado, inicio)`.
- O card do evento está escrito direto no laço de `templates/site/home.html.twig`.
- O formulário `EventoType` já tem a caixa `publicado`. A lista do admin mostra a situação com `wa-badge`.
- O schema está numa única migration (`Version20261124025133`).

## Goals / Non-Goals

**Goals:**

- O bloco e a lista saem da mesma consulta, de modo que a regra "publicado e de hoje em diante" continua num só lugar.
- O mesmo card serve ao bloco e à lista.

**Non-Goals:**

- Índice ou consulta próprios para destaques: o volume da agenda não justifica.
- Qualquer validação de quantidade de destaques no admin (ver proposal, Fora do escopo).

## Decisions

### 1. Coluna `destaque` no evento

`evento.destaque` booleana, `NOT NULL DEFAULT 0`, mapeada em `Evento::$destaque` (`bool`, padrão `false`), com `isDestaque()`/`setDestaque()`. Ela é criada por uma nova migration gerada com `make:migration`. Os eventos existentes ficam sem destaque.

- Alternativa descartada: tabela separada de destaques com posição. Ela só faria sentido com ordem manual, que está fora do escopo.

### 2. Separação entre bloco e lista no repository

Um novo método `EventoRepository::homeComDestaques(int $maxDestaques = 3)` reaproveita `publicadosAPartirDeHoje()` e percorre o resultado já ordenado. Os primeiros `$maxDestaques` eventos com `destaque = true` vão para `destaques`; todo o resto vai para `demais`, na ordem original. Ele retorna `array{destaques: list<Evento>, demais: list<Evento>}`, e o controller só repassa os dois ao template.

- Por quê: uma consulta só. O 4º destaque em diante cai naturalmente em `demais`, na sua posição por data, como pede a spec. A ordem e o desempate por id são os mesmos da lista.
- Alternativa descartada: duas consultas (destaques com `LIMIT 3` e lista com `NOT IN`). Seriam duas cópias do filtro de publicação e de data, mais o cuidado de passar os ids excluídos.

### 3. Card compartilhado

O card sai do laço da home para `templates/site/_card_evento.html.twig`, incluído pelo bloco e pela lista. O bloco fica numa `<section aria-labelledby>` com o título "Em destaque" e uma moldura de destaque (borda e fundo leves em Tailwind). O card continua igual por dentro.

- Mensagem "Nenhum evento agendado no momento." só quando `destaques` e `demais` estão vazios. Com destaques e lista vazia, a lista e o título "Próximos eventos" não aparecem.

### 4. Admin

- `EventoType` ganha `destaque` (`CheckboxType`, `required: false`) logo depois de `publicado`. A ajuda explica que a home mostra no topo até 3 destaques publicados e os demais ficam na lista. O formulário não faz nenhuma validação de quantidade.
- A lista `/admin/eventos` ganha a coluna "Destaque", com `<wa-badge variant="brand">` e ícone `star` quando marcado, e vazia quando não.

## Risks / Trade-offs

- [O administrador marca mais de 3 destaques e espera ver todos no topo] → O texto de ajuda do campo explica o limite. O 4º em diante continua visível na lista.
- [O destaque de um evento passado ou em rascunho fica marcado no banco] → Não tem efeito no site, porque o filtro da home vem antes da separação, e o admin vê a indicação na lista.

## Migration Plan

1. Gerar a migration (`ALTER TABLE evento ADD destaque TINYINT DEFAULT 0 NOT NULL`), conferir o SQL e aplicar com `doctrine:migrations:migrate`.
2. Rollback: `doctrine:migrations:migrate prev` remove a coluna, e o código anterior volta a funcionar sem ela.
