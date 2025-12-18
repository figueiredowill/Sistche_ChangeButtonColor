# Sistche_ChangeButtonColor

Este módulo Magento permite alterar dinamicamente a cor de todos os botões de uma store view específica via comando CLI, aplicando a alteração imediatamente no frontend.

# Decisões técnicas
### Persistência da configuração por escopo

O primeiro passo foi definir onde e como a cor seria armazenada. Para isso, o valor é salvo na tabela `core_config_data`, respeitando o escopo da store view selecionada. Essa escolha segue o padrão nativo do Magento para gerenciamento de configurações e garante herança automática entre website, store e store view, sem a necessidade de soluções customizadas.

### Aplicação imediata da alteração no frontend

Para atender ao requisito do teste, mudança de cor rápida, dinâmica e sem necessidade de intervenção técnica por parte do usuário, optei por injetar CSS inline diretamente no <head> da página via um bloco PHP do módulo.

O bloco consome a configuração armazenada na tabela core_config_data e gera um <style> com os seguintes seletores:
```css
.action.primary,
button.primary,
.action.tocart.primary {
    background-color: #HEX;
    border-color: #HEX;
}
```

- O CSS é carregado antes do render do corpo da página, garantindo que os botões afetados pelo seletor sejam atualizados imediatamente.
- O escopo por store view é respeitado, mantendo isolamento entre lojas.
- Evita alterações em arquivos de tema ou LESS, permitindo alterações rápidas pelo comando CLI.

Os seletores `.action.primary`, `button.primary`, `.action.tocart.primary` cobrem todos os botões principais do Magento, inclusive de "adicionar ao carrinho".
No código CSS o uso de `!important` garante que o estilo inline sobrescreva quaisquer regras de CSS existentes no tema ou módulos de terceiros.

> **Sobre boas práticas:**
Estou ciente de que injetar CSS inline não é recomendado para produção em Magento, pois quebra a separação de estilos e dificulta manutenção e overrides de tema.
A prática correta seria usar LESS ou CSS do tema, mas isso exigiria recompilação de static content para cada alteração de cor, o que não atende ao requisito do teste.

### Limpeza de cache automatizada

O comando CLI implementa a limpeza automática do cache (`bin/magento cache:clean`) **apenas se a execução for bem-sucedida**, garantindo que a nova configuração seja aplicada imediatamente no frontend. Dessa forma, o usuário não precisa executar comandos adicionais.

> **Observação:** Optei pela execução automática de limpeza de cache dado o escopo limitado e o requisito de aplicação imediata da alteração, porém estou ciente de que em ambientes maiores essa prática poderia impactar no desempenho.

### Internacionalização (i18n)

O módulo implementa suporte a traduções para mensagens exibidas no comando CLI, garantindo compatibilidade com diferentes idiomas.  

- As mensagens de sucesso, erro e instruções do comando foram extraídas para arquivos CSV de tradução (`i18n/en_US.csv` e `i18n/pt_BR.csv`). 

# Testes e validações

Os testes foram realizados em ambiente de desenvolvimento (`developer mode`), validando tanto o funcionamento do comando de console quanto o impacto visual da alteração no frontend.

### Validações do comando CLI

Foram verificados os seguintes cenários:

- Execução do comando com um código HEX válido e um ID de store view existente, confirmando a persistência correta da configuração.
- Validação do formato da cor informada, garantindo que apenas valores HEX válidos sejam aceitos.
- Validação da existência da store view informada, evitando a aplicação da configuração em lojas inexistentes.
- Confirmação de mensagens de erro claras e objetivas em casos de parâmetros inválidos.

### Validações de escopo

- Criação de novas store views diretamente pelo painel administrativo do Magento, simulando um cenário real de múltiplas lojas.
- Execução do comando para diferentes store views, assegurando que a cor aplicada afeta apenas a visualização selecionada.
- Verificação da herança de configuração, garantindo que alterações em uma store view não impactem outras lojas ou websites.

### Validações no frontend

- Inspeção visual dos botões no frontend, confirmando a aplicação correta da nova cor.
- Inspeção do HTML gerado, validando que o CSS é injetado dinamicamente no `head` da página apenas quando uma cor está configurada.

### Considerações finais

Essas validações garantem que a solução funciona conforme esperado, respeitando o escopo de configuração do Magento, o funcionamento do sistema de cache e o requisito de aplicação imediata da alteração, sem necessidade de recompilação de assets estáticos.

# Instalação e uso do módulo

## Instalação do módulo

Clone o repositório dentro do diretório `app/code` do Magento:

```bash
git clone https://github.com/figueiredowill/Sistche_ChangeButtonColor.git app/code/Sistche/ChangeButtonColor
```

Ou, se preferir, instale via Composer:

```bash
composer config repositories.sistche-changebuttoncolor vcs https://github.com/figueiredowill/Sistche_ChangeButtonColor.git
composer require sistche/change-button-color:dev-main
```

Execute os comandos de habilitação do módulo:
```bash
bin/magento module:enable Sistche_ChangeButtonColor
bin/magento setup:upgrade
```

## Execução do comando

Para alterar a cor dos botões de uma store view específica, execute o comando:

`bin/magento color:change <hex_color> <store_view_id>`


Exemplo:
```bash
bin/magento color:change 000000 1
```

Esse comando irá configurar a cor preta (#000000) para todos os botões da store view com ID 1.
Após isso, ao acessar a store view configurada, todos os botões estarão com a nova cor aplicada.

# Versão do Magento
Magento Community`2.4.8-p3`

# Author
William Figueiredo - Sistche