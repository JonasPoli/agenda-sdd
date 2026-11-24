# Spec Delta

## Purpose

Permite ao administrador manter as categorias que classificam os eventos, com nome e cor usados na identificação visual da agenda.

## ADDED Requirements

### Requirement: Listagem de categorias
A página `/admin/categorias` SHALL listar todas as categorias em uma tabela, em ordem alfabética de nome, com nome, amostra da cor, código hexadecimal, situação (ativa ou inativa) e a quantidade de eventos de cada uma, além das ações Editar e Excluir e do botão Nova categoria.

#### Scenario: Lista ordenada
- **WHEN** existem as categorias "Teatro", "Música" e "Cinema"
- **THEN** a tabela as exibe na ordem Cinema, Música, Teatro, cada uma com sua cor, situação e quantidade de eventos

#### Scenario: Nenhuma categoria
- **WHEN** não há categorias cadastradas
- **THEN** a página exibe "Nenhuma categoria cadastrada." e o botão Nova categoria

### Requirement: Cadastro e edição de categoria
O administrador SHALL poder criar e editar categorias com os campos nome, cor e ativa. Uma categoria nova MUST vir com ativa marcada. Após salvar com sucesso, o sistema MUST voltar à lista com uma mensagem de sucesso.

#### Scenario: Criar categoria válida
- **WHEN** o administrador cria a categoria "Música" com a cor "#7C3AED"
- **THEN** a categoria é gravada como ativa, a lista é exibida e aparece a mensagem "Categoria salva com sucesso."

#### Scenario: Desativar categoria
- **WHEN** o administrador edita "Música" e desmarca ativa
- **THEN** a categoria passa a aparecer como inativa na lista

### Requirement: Validação do nome da categoria
O nome da categoria MUST ser obrigatório, ter no máximo 60 caracteres e ser único entre as categorias, sem diferenciar maiúsculas de minúsculas e ignorando espaços nas pontas.

#### Scenario: Nome vazio
- **WHEN** o administrador tenta salvar uma categoria sem nome
- **THEN** o formulário é exibido de novo com o erro "Informe o nome." no campo nome e nada é gravado

#### Scenario: Nome longo demais
- **WHEN** o nome tem 61 caracteres
- **THEN** o formulário mostra "O nome pode ter no máximo 60 caracteres." e nada é gravado

#### Scenario: Nome repetido
- **WHEN** já existe "Música" e o administrador tenta criar " música "
- **THEN** o formulário mostra "Já existe uma categoria com este nome." e nada é gravado

### Requirement: Validação da cor da categoria
A cor MUST ser obrigatória e estar no formato hexadecimal `#RRGGBB` (o caractere `#` seguido de exatamente 6 dígitos hexadecimais). O sistema MUST gravar a cor em letras maiúsculas.

#### Scenario: Cor válida em minúsculas
- **WHEN** o administrador informa a cor "#7c3aed"
- **THEN** a categoria é gravada com a cor "#7C3AED"

#### Scenario: Cor em formato inválido
- **WHEN** o administrador informa "#FFF", "7C3AED" ou "#GGGGGG"
- **THEN** o formulário mostra "Informe a cor no formato #RRGGBB." e nada é gravado

### Requirement: Exclusão de categoria
O administrador SHALL poder excluir uma categoria após confirmar em um diálogo. O sistema MUST recusar a exclusão de uma categoria que tenha ao menos um evento vinculado, mantendo a categoria e exibindo uma mensagem de erro.

#### Scenario: Excluir categoria sem eventos
- **WHEN** o administrador aciona Excluir em uma categoria sem eventos e confirma no diálogo
- **THEN** a categoria é removida e a lista exibe "Categoria excluída com sucesso."

#### Scenario: Cancelar a exclusão
- **WHEN** o administrador aciona Excluir e cancela no diálogo
- **THEN** nada é removido

#### Scenario: Categoria com eventos
- **WHEN** o administrador confirma a exclusão de uma categoria que tem 2 eventos
- **THEN** a categoria continua cadastrada e a lista exibe "Não é possível excluir: a categoria possui eventos."
