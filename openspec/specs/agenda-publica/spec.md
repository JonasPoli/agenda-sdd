# agenda-publica Specification

## Purpose

Mostra ao público, sem login, os próximos eventos publicados da agenda e os detalhes de cada um em um endereço próprio.

## Requirements

### Requirement: Lista de próximos eventos na home
A página `/` SHALL listar os eventos publicados cuja data de início é hoje ou posterior, considerando o dia a partir de 00:00 no horário de Brasília, em ordem crescente de data e hora de início. Eventos não publicados MUST NOT aparecer.

#### Scenario: Somente publicados e a partir de hoje
- **WHEN** hoje é 23/11/2026 e existem um evento publicado em 20/11/2026, um publicado em 23/11/2026 às 08:00, um publicado em 10/12/2026 e um rascunho em 01/12/2026
- **THEN** a home lista, nesta ordem, o evento de 23/11/2026 e o de 10/12/2026

#### Scenario: Evento de hoje que já começou
- **WHEN** são 21:00 de 23/11/2026 e há um evento publicado que começou às 19:00 do mesmo dia
- **THEN** o evento ainda aparece na home

#### Scenario: Nenhum evento futuro
- **WHEN** não há eventos publicados a partir de hoje
- **THEN** a home exibe "Nenhum evento agendado no momento."

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
