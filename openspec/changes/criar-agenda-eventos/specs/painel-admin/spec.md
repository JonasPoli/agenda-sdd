# Spec Delta

## Purpose

Dá ao administrador uma visão geral da agenda ao entrar na área administrativa e um layout comum de navegação entre os cadastros.

## ADDED Requirements

### Requirement: Totais no painel
A página `/admin` SHALL exibir o total de categorias e o total de eventos cadastrados, contando todos os registros, inclusive categorias inativas e eventos não publicados ou passados.

#### Scenario: Totais com dados cadastrados
- **WHEN** existem 3 categorias (uma inativa) e 7 eventos (dois não publicados) e o administrador abre `/admin`
- **THEN** o painel mostra 3 categorias e 7 eventos

#### Scenario: Banco vazio
- **WHEN** não há categorias nem eventos e o administrador abre `/admin`
- **THEN** o painel mostra 0 categorias e 0 eventos

### Requirement: Próximos eventos no painel
A página `/admin` SHALL listar até 5 eventos com data e hora de início a partir do momento atual, em ordem crescente de início, publicados ou não, mostrando título, data e hora, categoria e situação (publicado ou rascunho). Cada item MUST levar à edição do evento.

#### Scenario: Mais de 5 eventos futuros
- **WHEN** existem 8 eventos futuros e o administrador abre `/admin`
- **THEN** são listados apenas os 5 com início mais próximo, do mais próximo para o mais distante

#### Scenario: Eventos passados não aparecem
- **WHEN** um evento começou ontem
- **THEN** ele não aparece entre os próximos eventos do painel

#### Scenario: Rascunhos aparecem identificados
- **WHEN** um evento futuro não está publicado
- **THEN** ele aparece na lista com a indicação de rascunho

#### Scenario: Nenhum evento futuro
- **WHEN** não há eventos futuros
- **THEN** o painel exibe a mensagem "Nenhum evento agendado." no lugar da lista

### Requirement: Layout administrativo
Todas as páginas da área administrativa SHALL usar o mesmo layout, com menu lateral escuro contendo os links Painel, Categorias e Eventos, o nome do administrador logado e a opção Sair. O item da seção atual MUST aparecer destacado.

#### Scenario: Navegação pelo menu
- **WHEN** o administrador está em `/admin/categorias`
- **THEN** o menu lateral mostra Painel, Categorias e Eventos com Categorias destacado, além do nome dele e da opção Sair

### Requirement: Mensagens de sucesso
Após criar, editar ou excluir um registro no admin, o sistema SHALL exibir uma mensagem de sucesso uma única vez na página seguinte. Mensagens de erro de regra de negócio MUST ser exibidas do mesmo modo, com aparência de erro.

#### Scenario: Mensagem após salvar
- **WHEN** o administrador salva uma categoria nova
- **THEN** a lista de categorias exibe uma mensagem de sucesso, que não aparece de novo ao recarregar a página
