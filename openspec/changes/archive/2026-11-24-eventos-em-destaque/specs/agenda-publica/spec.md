# Spec Delta

## ADDED Requirements

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

## MODIFIED Requirements

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
