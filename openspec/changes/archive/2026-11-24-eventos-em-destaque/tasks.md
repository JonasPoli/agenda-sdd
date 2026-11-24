# Tasks

## 1. Banco

- [x] 1.1 Acrescentar `destaque` (`bool`, padrão `false`, coluna `NOT NULL DEFAULT 0`) à entidade `Evento`, com `isDestaque()` e `setDestaque()`; verificar com `php bin/console doctrine:schema:validate --skip-sync` (mapeamento correto)
- [x] 1.2 Gerar a migration com `make:migration`, conferir que o SQL só acrescenta a coluna `destaque` (sem a tabela do Messenger nem outras mudanças) e aplicar com `doctrine:migrations:migrate`; verificar que os eventos existentes ficaram com `destaque = 0` via `dbal:run-sql`
- [x] 1.3 Verificação do grupo: `php bin/console doctrine:schema:validate` com mapeamento e banco em sincronia

## 2. Admin

- [x] 2.1 Acrescentar ao `EventoType` o campo `destaque` (`CheckboxType`, opcional, depois de `publicado`), com ajuda dizendo que a home mostra no topo até 3 destaques publicados e que os demais ficam na lista, e incluí-lo no template do formulário; verificar com `php bin/console lint:twig templates`
- [x] 2.2 Acrescentar a coluna "Destaque" à lista `/admin/eventos`, com `wa-badge` e ícone `star` nos eventos marcados e vazia nos demais; verificar com `lint:twig`
- [x] 2.3 Verificação do grupo no navegador: Novo evento vem com destaque desmarcado; marcar destaque num evento publicado e salvar mostra "Evento salvo com sucesso." e a indicação "Destaque" só na linha dele; com 3 destaques já marcados, marcar um quarto salva sem erro

## 3. Site público

- [x] 3.1 Criar `EventoRepository::homeComDestaques(int $maxDestaques = 3)` sobre `publicadosAPartirDeHoje()`, separando os primeiros destaques em `destaques` e o resto em `demais`, na ordem original; fazer o `SiteController::home` repassar os dois ao template; verificar com `php bin/console lint:container`
- [x] 3.2 Extrair o card da home para `templates/site/_card_evento.html.twig` e usá-lo na lista, sem mudar o HTML do card; verificar com `lint:twig` e conferindo no navegador que os cards da lista continuam iguais
- [x] 3.3 Montar na home o bloco "Em destaque" antes da lista (só quando houver destaques), a lista com `demais`, o título "Próximos eventos" só quando a lista tiver eventos e a mensagem "Nenhum evento agendado no momento." só quando bloco e lista estiverem vazios; verificar com `lint:twig` e `php bin/console tailwind:build`
- [x] 3.4 Verificação do grupo no navegador:
  - Com 4 destaques publicados a partir de hoje, o bloco mostra os 3 mais próximos em ordem crescente e o 4º aparece na lista na sua posição.
  - Um destaque não se repete na lista.
  - Um destaque em rascunho e um destaque passado não aparecem.
  - Sem destaques, o bloco some.
  - Com só destaques, aparecem o bloco e nenhuma lista ou mensagem.
  - Sem eventos, aparece a mensagem.
  - O card do destaque leva ao detalhe.
  - Em 375 px de largura, bloco e lista ficam em uma coluna sem rolagem horizontal.

## 4. Verificação integrada

- [x] 4.1 Rodar `php bin/console lint:twig templates`, `php bin/console lint:container`, `php bin/console doctrine:schema:validate` e `php bin/console tailwind:build`, todos sem erros

## Workflow follow-up

- Arquivar a change com `/opsx:archive` depois da revisão, sincronizando as specs principais.
