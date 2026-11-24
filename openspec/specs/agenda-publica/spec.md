# agenda-publica Specification

## Purpose

Mostra ao público, sem login, os próximos eventos publicados da agenda e os detalhes de cada um em um endereço próprio.

## Requirements

### Requirement: Lista de próximos eventos na home
A página `/` SHALL listar os eventos publicados cuja data de início é hoje ou posterior, considerando o dia a partir de 00:00 no horário de Brasília, em ordem crescente de data e hora de início. Eventos não publicados MUST NOT aparecer. Os eventos exibidos no bloco de destaques MUST NOT se repetir na lista.

#### Scenario: Somente publicados e a partir de hoje
- **WHEN** hoje é 23/11/2026 e existem um evento publicado em 20/11/2026, um publicado em 23/11/2026 às 08:00, um publicado em 10/12/2026 e um rascunho em 01/12/2026
- **THEN** a home lista, nesta ordem, o evento de 23/11/2026 e o de 10/12/2026

#### Scenario: Evento de hoje que já começou
- **WHEN** são 21:00 de 23/11/2026 e há um evento publicado que começou às 19:00 do mesmo dia
- **THEN** o evento ainda aparece na home

#### Scenario: Nenhum evento futuro
- **WHEN** não há eventos publicados a partir de hoje
- **THEN** a home exibe "Nenhum evento agendado no momento."

#### Scenario: Destaque não se repete na lista
- **WHEN** há eventos publicados em 28/11, 05/12 e 10/12/2026 e só o de 05/12/2026 é destaque
- **THEN** o de 05/12/2026 aparece apenas no bloco, e a lista mostra os de 28/11 e 10/12/2026

#### Scenario: Todos os próximos eventos estão no bloco
- **WHEN** os únicos eventos publicados a partir de hoje são 2 destaques
- **THEN** a home exibe o bloco com os dois e não exibe a lista nem a mensagem "Nenhum evento agendado no momento."

### Requirement: Card do evento
Cada evento da home SHALL aparecer em um card com título, data e hora de início (dd/mm/aaaa HH:mm), local, resumo e um selo com o nome da categoria na cor da categoria. O card MUST levar à página de detalhe do evento. Local e resumo vazios MUST ser omitidos sem deixar rótulos soltos.

#### Scenario: Card completo
- **WHEN** o evento "Show de Natal" da categoria "Música" (#7C3AED) está na home
- **THEN** o card mostra título, data e hora, local, resumo e um selo "Música" com fundo #7C3AED e texto legível, e leva a `/evento/show-de-natal`

#### Scenario: Evento sem local
- **WHEN** um evento listado não tem local
- **THEN** o card é exibido sem a linha de local

### Requirement: Página de detalhe do evento
A página `/evento/{slug}` SHALL exibir todos os dados públicos do evento: título, data e hora de início, local, categoria com sua cor, resumo e descrição, preservando as quebras de linha da descrição, e um link de volta para a home. Evento publicado com data passada MUST continuar acessível pelo endereço.

#### Scenario: Detalhe de evento publicado
- **WHEN** um visitante acessa `/evento/show-de-natal` de um evento publicado
- **THEN** a página exibe todos os dados do evento e um link para voltar à agenda

#### Scenario: Evento publicado com data passada não aparece na home, mas a página de detalhe continua acessível pelo link
- **WHEN** hoje é 23/11/2026 e o evento publicado "Feira de Outono" (`/evento/feira-de-outono`) aconteceu em 20/11/2026
- **THEN** a home não lista o evento
- **AND** quem acessa `/evento/feira-de-outono` vê a página de detalhe normalmente, com status 200, e não 404

### Requirement: Eventos indisponíveis retornam 404
A página de detalhe MUST responder com status 404 quando o slug não existir ou quando o evento não estiver publicado, sem revelar dados do evento.

#### Scenario: Slug inexistente
- **WHEN** um visitante acessa `/evento/nao-existe`
- **THEN** o sistema responde 404

#### Scenario: Evento em rascunho
- **WHEN** um visitante acessa o endereço de um evento não publicado
- **THEN** o sistema responde 404 e nenhum dado do evento é exibido

### Requirement: Layout do site público
Todas as páginas do site público SHALL ter um cabeçalho com o nome "Agenda", que leva à home, e um rodapé, com visual claro e adaptado a telas de celular.

#### Scenario: Cabeçalho e rodapé
- **WHEN** um visitante abre a home ou um detalhe de evento
- **THEN** a página exibe o cabeçalho "Agenda" com link para `/` e o rodapé

#### Scenario: Tela de celular
- **WHEN** a home é aberta em uma tela de 375 px de largura
- **THEN** os cards ficam em uma coluna, sem rolagem horizontal

### Requirement: Bloco de destaques na home
A página `/` SHALL exibir, no topo e antes da lista de próximos eventos, um bloco "Em destaque" com até 3 eventos marcados como destaque, escolhidos entre os que aparecem na home (publicados, de hoje em diante), do início mais próximo para o mais distante. Cada destaque MUST usar o mesmo card da lista. Sem nenhum destaque a exibir, o bloco MUST NOT aparecer.

#### Scenario: Até 3 destaques no topo
- **WHEN** hoje é 23/11/2026 e há destaques publicados em 10/12/2026 e 28/11/2026
- **THEN** o bloco "Em destaque" aparece antes da lista com os dois eventos, na ordem 28/11/2026 e 10/12/2026

#### Scenario: Mais de 3 destaques
- **WHEN** há 4 destaques publicados a partir de hoje, em 28/11, 05/12, 10/12 e 15/12/2026
- **THEN** o bloco mostra só os de 28/11, 05/12 e 10/12/2026, e o de 15/12/2026 aparece na lista abaixo, na sua posição por data

#### Scenario: Destaque em rascunho ou passado
- **WHEN** hoje é 23/11/2026 e há um destaque em rascunho em 01/12/2026 e um destaque publicado em 20/11/2026
- **THEN** nenhum dos dois aparece no bloco nem na lista

#### Scenario: Nenhum destaque a exibir
- **WHEN** nenhum evento publicado de hoje em diante está marcado como destaque
- **THEN** a home não exibe o bloco "Em destaque" e mostra só a lista

#### Scenario: Card do destaque
- **WHEN** o evento em destaque "Show de Natal", da categoria "Música", aparece no bloco
- **THEN** o card mostra título, data e hora, local, resumo e o selo "Música" na cor da categoria, e leva a `/evento/show-de-natal`
