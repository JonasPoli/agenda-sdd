# Spec Delta

## Purpose

Garante que só administradores acessem a área administrativa, com login por e-mail e senha, e permite criar administradores pela linha de comando.

## ADDED Requirements

### Requirement: Login por e-mail e senha
O sistema SHALL oferecer em `/login` um formulário com e-mail e senha. Credenciais válidas de um administrador MUST iniciar a sessão e levar ao painel `/admin`. Credenciais inválidas MUST manter a pessoa na tela de login com uma mensagem de erro em português, sem revelar se o e-mail existe.

#### Scenario: Login com credenciais válidas
- **WHEN** um administrador informa e-mail e senha corretos em `/login`
- **THEN** a sessão é iniciada e ele é redirecionado para `/admin`

#### Scenario: Login com senha incorreta
- **WHEN** alguém informa um e-mail cadastrado com a senha errada
- **THEN** a tela de login é exibida de novo com a mensagem "E-mail ou senha inválidos." e o e-mail digitado preenchido

#### Scenario: Login com e-mail inexistente
- **WHEN** alguém informa um e-mail que não está cadastrado
- **THEN** a tela de login exibe a mesma mensagem "E-mail ou senha inválidos."

#### Scenario: Administrador já autenticado abre o login
- **WHEN** um administrador com sessão ativa acessa `/login`
- **THEN** ele é redirecionado para `/admin`

### Requirement: Área administrativa restrita a ROLE_ADMIN
Toda URL que começa com `/admin` MUST exigir um usuário autenticado com o papel `ROLE_ADMIN`. As páginas do site público e a página `/login` MUST continuar acessíveis sem autenticação.

#### Scenario: Visitante sem sessão acessa o admin
- **WHEN** um visitante não autenticado acessa `/admin/eventos`
- **THEN** ele é redirecionado para `/login`

#### Scenario: Retorno à página pedida após o login
- **WHEN** um visitante é redirecionado de `/admin/eventos` para `/login` e faz login com sucesso
- **THEN** ele é levado a `/admin/eventos`

#### Scenario: Site público sem login
- **WHEN** um visitante não autenticado acessa `/`
- **THEN** a página é exibida normalmente

### Requirement: Logout
O sistema SHALL permitir que o administrador encerre a sessão a partir da área administrativa. Depois do logout, o acesso a `/admin` MUST exigir novo login.

#### Scenario: Encerrar a sessão
- **WHEN** o administrador aciona "Sair" no menu do admin
- **THEN** a sessão é encerrada, ele é levado para `/login` e um novo acesso a `/admin` volta a pedir login

### Requirement: Criação de administrador por comando
O sistema SHALL oferecer o comando de console `app:criar-admin`, que pede nome, e-mail e senha e cria um usuário com o papel `ROLE_ADMIN` e a senha armazenada apenas como hash. A senha MUST ser digitada sem eco no terminal.

#### Scenario: Criar o primeiro administrador
- **WHEN** o comando `app:criar-admin` é executado e recebe nome "Maria", e-mail "maria@exemplo.com" e uma senha
- **THEN** o usuário é gravado com `ROLE_ADMIN`, o comando exibe uma mensagem de sucesso e termina com código 0, e esse e-mail e senha passam a funcionar em `/login`

#### Scenario: E-mail já cadastrado
- **WHEN** o comando recebe um e-mail que já pertence a outro usuário
- **THEN** nenhum usuário é criado, o comando exibe "Já existe um usuário com este e-mail." e termina com código diferente de 0

#### Scenario: Dados inválidos
- **WHEN** o comando recebe nome vazio, e-mail em formato inválido ou senha com menos de 8 caracteres
- **THEN** ele informa o problema e pede o valor de novo, sem gravar nada até receber dados válidos
