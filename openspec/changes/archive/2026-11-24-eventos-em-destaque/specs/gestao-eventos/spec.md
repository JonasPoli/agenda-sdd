# Spec Delta

## MODIFIED Requirements

### Requirement: Listagem de eventos
A página `/admin/eventos` SHALL listar todos os eventos em uma tabela, em ordem decrescente de data e hora de início, com título, data e hora de início (dd/mm/aaaa HH:mm, horário de Brasília), local, categoria (com a cor), situação (publicado ou rascunho) e a indicação de destaque, além das ações Editar e Excluir e do botão Novo evento.

#### Scenario: Lista ordenada
- **WHEN** existem eventos em 10/12/2026, 05/01/2027 e 20/11/2026
- **THEN** a tabela os exibe na ordem 05/01/2027, 10/12/2026, 20/11/2026

#### Scenario: Nenhum evento
- **WHEN** não há eventos cadastrados
- **THEN** a página exibe "Nenhum evento cadastrado." e o botão Novo evento

#### Scenario: Evento em destaque identificado
- **WHEN** o evento "Show de Natal" está marcado como destaque e "Sarau de Poesia" não está
- **THEN** a linha de "Show de Natal" exibe a indicação "Destaque" e a de "Sarau de Poesia" não exibe

### Requirement: Cadastro e edição de evento
O administrador SHALL poder criar e editar eventos com título, resumo, descrição, data e hora de início, local, categoria, publicado e destaque. Um evento novo MUST vir com publicado e destaque desmarcados. Após salvar com sucesso, o sistema MUST voltar à lista com a mensagem "Evento salvo com sucesso." Um evento em rascunho MUST NOT ficar acessível no site público.

#### Scenario: Criar evento como rascunho
- **WHEN** o administrador preenche título, data e hora e categoria e salva sem marcar publicado
- **THEN** o evento é gravado como rascunho e aparece na lista com a mensagem de sucesso

#### Scenario: Acessar URL pública de evento em rascunho
- **WHEN** um visitante acessa o endereço de um evento em rascunho, como `/evento/festival-de-musica-arte-2026`
- **THEN** o sistema exibe a página "Evento não encontrado" com status 404, sem nenhum dado do evento

#### Scenario: Publicar evento existente
- **WHEN** o administrador edita um rascunho, marca publicado e salva
- **THEN** o evento passa a aparecer como publicado

#### Scenario: Marcar evento como destaque
- **WHEN** o administrador edita um evento publicado, marca destaque e salva
- **THEN** o evento é gravado como destaque, a lista exibe "Evento salvo com sucesso." e a linha dele mostra a indicação "Destaque"

#### Scenario: Mais de 3 destaques são aceitos
- **WHEN** já existem 3 eventos marcados como destaque e o administrador marca um quarto e salva
- **THEN** o evento é gravado como destaque sem nenhum erro no formulário
