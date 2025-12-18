# Sistche_ChangeButtonColor

Este módulo Magento permite alterar dinamicamente a cor de todos os botões de uma store view específica via comando CLI, aplicando a alteração imediatamente no frontend.

# Decisões técnicas
### Persistência da configuração por escopo

O primeiro passo foi definir onde e como a cor seria armazenada. Para isso, o valor é salvo na tabela `core_config_data`, respeitando o escopo da store view selecionada. Essa escolha segue o padrão nativo do Magento para gerenciamento de configurações e garante herança automática entre website, store e store view, sem a necessidade de soluções customizadas.

### Aplicação imediata da alteração no frontend

Como o requisito do teste exige que a mudança seja aplicada de forma dinâmica, rápida, com o mínimo de esforço por parte do usuário, foi decidido não utilizar LESS ou o pipeline de static content. Em vez disso, a solução insere o CSS dinamicamente no `head` da página, permitindo que a alteração seja refletida imediatamente após a execução do comando, garantindo isolamento por store view.

### Injeção do CSS via layout (`head.additional`)

Para aplicar a cor dos botões de forma dinâmica, o módulo adiciona um bloco ao container `head.additional` no layout:

```xml
<referenceContainer name="head.additional">
    <block class="Sistche\ChangeButtonColor\Block\ButtonColor" 
           name="sistche.changebuttoncolor" 
           template="Sistche_ChangeButtonColor::button_color.phtml"/>
</referenceContainer>
```

O CSS é carregado antes do corpo da página, garantindo que todos os botões da store view sejam afetados imediatamente.
Essa abordagem evita alterações diretas em arquivos de tema, mantendo compatibilidade com múltiplas store views.

### Seletores CSS e efeito hover
O bloco PHP ButtonColor gera o seguinte CSS inline:

```css
.action.primary,
button.primary,
.action.tocart.primary {
    background-color: #HEX !important;
    border-color: #HEX !important;
}
```

Os seletores cobrem todos os botões principais do Magento.
O uso de !important garante que o estilo inline sobrescreva quaisquer regras de CSS existentes no tema ou módulos de terceiros.

### Leitura e geração dinâmica do CSS

No frontend, o Block PHP `Sistche\ChangeButtonColor\Block\ButtonColor` é responsável por consumir a configuração armazenada e gerar o CSS inline apenas quando uma cor está definida.

### Limpeza de cache automatizada

O comando CLI implementa a limpeza automática do cache (`bin/magento cache:clean`) **apenas se a execução for bem-sucedida**, garantindo que a nova configuração seja aplicada imediatamente no frontend. Dessa forma, o usuário não precisa executar comandos adicionais.

> **Observação:** Tenho ciência que em ambientes maiores, a execução automática de limpeza de cache pode impactar o desempenho; nesse caso, é aceitável dado o escopo limitado e o requisito de aplicação imediata da alteração.

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
composer require sistche/changebuttoncolor:dev-main
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