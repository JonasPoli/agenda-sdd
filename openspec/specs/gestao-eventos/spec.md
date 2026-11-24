# gestao-eventos Specification

## Purpose

Permite ao administrador cadastrar, publicar e manter os eventos da agenda, cada um com um endereço público estável.

## Requirements

### Requirement: Listagem de eventos
A página `/admin/eventos` SHALL listar todos os eventos em uma tabela, em ordem decrescente de data e hora de início, com título, data e hora de início (dd/mm/aaaa HH:mm, horário de Brasília), local, categoria (com a cor) e situação (publicado ou rascunho), além das ações Editar e Excluir e do botão Novo evento.

#### Scenario: Lista ordenada
- **WHEN** existem eventos em 10/12/2026, 05/01/2027 e 20/11/2026
- **THEN** a tabela os exibe na ordem 05/01/2027, 10/12/2026, 20/11/2026

#### Scenario: Nenhum evento
- **WHEN** não há eventos cadastrados
- **THEN** a página exibe "Nenhum evento cadastrado." e o botão Novo evento

### Requirement: Cadastro e edição de evento
O administrador SHALL poder criar e editar eventos com título, resumo, descrição, data e hora de início, local, categoria e publicado. Um evento novo MUST vir com publicado desmarcado. Após salvar com sucesso, o sistema MUST voltar à lista com a mensagem "Evento salvo com sucesso." Um evento em rascunho MUST NOT ficar acessível no site público.

#### Scenario: Criar evento como rascunho
- **WHEN** o administrador preenche título, data e hora e categoria e salva sem marcar publicado
- **THEN** o evento é gravado como rascunho e aparece na lista com a mensagem de sucesso

#### Scenario: Acessar URL pública de evento em rascunho
- **WHEN** um visitante acessa o endereço de um evento em rascunho, como `/evento/festival-de-musica-arte-2026`
- **THEN** o sistema exibe a página "Evento não encontrado" com status 404, sem nenhum dado do evento

#### Scenario: Publicar evento existente
- **WHEN** o administrador edita um rascunho, marca publicado e salva
- **THEN** o evento passa a aparecer como publicado

### Requirement: Validação dos campos do evento
O título MUST ser obrigatório e ter até 120 caracteres; a data e hora de início e a categoria MUST ser obrigatórias; o resumo MUST ter até 255 caracteres e o local até 120, ambos opcionais; a descrição MUST ser opcional e sem limite prático de tamanho. Erros MUST aparecer junto ao campo, sem gravar nada.

#### Scenario: Campos obrigatórios vazios
- **WHEN** o administrador tenta salvar um evento sem título, sem data e hora e sem categoria
- **THEN** o formulário é exibido de novo com um erro em cada um desses campos e nada é gravado

#### Scenario: Resumo longo demais
- **WHEN** o resumo tem 256 caracteres
- **THEN** o formulário mostra "O resumo pode ter no máximo 255 caracteres." e nada é gravado

### Requirement: Data e hora no horário de Brasília
A data e hora de início MUST ser informada e exibida no fuso America/Sao_Paulo, de modo que o horário digitado no formulário seja o mesmo mostrado no admin e no site público.

#### Scenario: Horário preservado
- **WHEN** o administrador grava um evento para 15/12/2026 às 19:30
- **THEN** o admin e o site exibem "15/12/2026 19:30" e o formulário de edição traz o mesmo horário

### Requirement: Escolha de categoria ativa
O campo categoria do formulário SHALL oferecer apenas categorias ativas, em ordem alfabética. Ao editar um evento cuja categoria foi desativada, essa categoria MUST continuar disponível no campo para que o evento possa ser salvo sem trocá-la.

#### Scenario: Categoria inativa fora da lista
- **WHEN** existem as categorias "Música" (ativa) e "Dança" (inativa) e o administrador abre Novo evento
- **THEN** o campo categoria oferece só "Música"

#### Scenario: Editar evento de categoria desativada
- **WHEN** um evento pertence a "Dança", que foi desativada depois, e o administrador abre a edição desse evento
- **THEN** o campo categoria mostra "Dança" selecionada e o evento pode ser salvo

#### Scenario: Categoria inativa enviada à força
- **WHEN** o formulário de um evento novo é enviado com o identificador de uma categoria inativa
- **THEN** o formulário rejeita o valor e nada é gravado

### Requirement: Slug gerado do título
Ao criar um evento, o sistema SHALL gerar o slug a partir do título: minúsculas, sem acentos, com palavras separadas por hífen e sem outros caracteres. O slug MUST ser único; havendo colisão, o sistema MUST acrescentar `-2`, `-3` e assim por diante. O slug MUST permanecer o mesmo quando o título for editado e não é editável no formulário.

#### Scenario: Slug a partir de título acentuado
- **WHEN** o administrador cria o evento "Festival de Música & Arte 2026"
- **THEN** o slug gravado é "festival-de-musica-arte-2026"

#### Scenario: Título repetido
- **WHEN** já existe um evento com slug "show-de-natal" e outro evento é criado com o título "Show de Natal"
- **THEN** o novo evento recebe o slug "show-de-natal-2"

#### Scenario: Edição do título não muda o endereço
- **WHEN** o administrador altera o título de "Show de Natal" para "Grande Show de Natal"
- **THEN** o slug continua "show-de-natal"

### Requirement: Exclusão de evento
O administrador SHALL poder excluir um evento após confirmar em um diálogo que mostra o título do evento. Após a exclusão, a lista MUST exibir a mensagem "Evento excluído com sucesso."

#### Scenario: Excluir com confirmação
- **WHEN** o administrador aciona Excluir em um evento e confirma no diálogo
- **THEN** o evento é removido e a lista exibe a mensagem de sucesso

#### Scenario: Cancelar a exclusão
- **WHEN** o administrador aciona Excluir e cancela no diálogo
- **THEN** o evento continua cadastrado

### Requirement: Proteção das ações que alteram dados
As ações de criar, editar e excluir no admin MUST aceitar alterações apenas por requisição POST com token CSRF válido. Uma exclusão sem token válido MUST ser recusada sem remover nada.

#### Scenario: Exclusão sem token
- **WHEN** chega uma requisição de exclusão de evento sem token CSRF válido
- **THEN** o evento não é removido

#### Scenario: Exclusão de categoria sem token
- **WHEN** chega uma requisição de exclusão de categoria sem token CSRF válido
- **THEN** a categoria não é removida
