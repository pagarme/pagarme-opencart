## Módulo integração Pagar.me para Opencart

## Compatibilidade

 OpenCart 1.5.x à 2.3.x

## Funcionalidades

 Integrar sua loja virtual OpenCart com o gateway de pagamentos [Pagar.me](http://pagar.me)

 Transações de cartão de crédito

 Transações de boleto bancário

 Atualização de status automática
 
 Configuração de parcelamento de cartão


## Checkout transparente

Para utilizar essa integração, instale o módulo da pasta [API](API/)

![Versão API](https://i.imgur.com/0lyloNC.png)

## Checkout Pagar.me

Para utilizar essa integração, instale o módulo da pasta [Checkout Pagar.me](Checkout%20Pagar.Me)

![Checkout Pagar.me](https://i.imgur.com/V8NzjZy.png)


## Instalação

Você deve escolher se utilizará o Checkout Transparente, ou o Checkout Pagar.me, e então instale-o de acordo com a versão de seu Opencart.

### Versão 1.5.x
1. Copie todo o conteúdo da pasta "upload" para a raíz da sua loja
2. Acesse o painel administrativo da sua loja
3. Acesse Extensões > Formas de pagamento e instale os módulos (Cartão de crédito e/ou boleto)
4. Preencha as informações necessária (as chaves API e criptografia são encontradas no dashboard do Pagar.Me)
5. Pronto! O módulo está funcionando

### Versão 2.x

Para versão 2.0 à 2.2 do Opencart utilize os arquivos da pasta 2.x.

1. Acesse Extensões > Instalador
2. Faça o upload do arquivo OCMOD
3. Acesse Extensões > Modificações
4. Clique no botão atualizar
5. Acesse extensões pagamentos
6. Instale e configure
7. Pronto! O módulo está funcionando

## Campos Customizáveis

Para as versões 2.x do Opencart é necessário criar alguns campos para que o módulo funcione perfeitamente, o campo obrigatório é de CPF/CNPJ e os opcionais são de número da casa e complemento.
Você pode escolher em ter apenas o campo de CPF ou apenas o campo de CNPJ.

Acesse seu painel administrador, selecione a opção `Customer > Custom Fields`, e clique para adicionar um novo campo customizável.

![Custom Fields](https://i.imgur.com/Enz7Vdf.png)

Os campos que podem ser criados para que a integração entenda são `CPF`, `CNPJ`, `Número` e `Complemento`.

Crie o campo de CPF caso venda para pessoas físicas e o de CNPJ caso venda para pessoas jurídicas.

### CPF

| *Campo do OpenCart* | *Valor Recomendado*                         | *Obrigatoriedade* |
|---------------------|---------------------------------------------|-------------------|
| Custom Field Name   | CPF                                         | Obrigatório       |
| Location            | Account                                     | Obrigatório       |
| Type                | Text                                        | Obrigatório       |
| Customer Group      | Habilite os grupos que possuirão esse campo | Obrigatório       |
| Required            | Habilitado                                  | Obrigatório       |
| Status              | Habilitado                                  | Obrigatório       |
| Sort Order          | 3                                           | Opcional          |


### CNPJ

| *Campo do OpenCart* | *Valor Recomendado*                         | *Obrigatoriedade* |
|---------------------|---------------------------------------------|-------------------|
| Custom Field Name   | CNPJ                                        | Obrigatório       |
| Location            | Account                                     | Obrigatório       |
| Type                | Text                                        | Obrigatório       |
| Customer Group      | Habilite os grupos que possuirão esse campo | Obrigatório       |
| Required            | Habilitado                                  | Obrigatório       |
| Status              | Habilitado                                  | Obrigatório       |
| Sort Order          | 3                                           | Opcional          |

### Número 

| *Campo do OpenCart* | *Valor Recomendado*                         | *Obrigatoriedade* |
|---------------------|---------------------------------------------|-------------------|
| Custom Field Name   | Número                                      | Obrigatório       |
| Location            | Address                                     | Obrigatório       |
| Type                | Text                                        | Obrigatório       |
| Customer Group      | Habilite os grupos que possuirão esse campo | Obrigatório       |
| Required            | Opcional                                    | Opcional          |
| Status              | Opcional                                    | Opcional          |
| Sort Order          | 2                                           | Opcional          |

### Complemento

| *Campo do OpenCart* | *Valor Recomendado*                         | *Obrigatoriedade* |
|---------------------|---------------------------------------------|-------------------|
| Custom Field Name   | Complemento                                 | Obrigatório       |
| Location            | Address                                     | Obrigatório       |
| Type                | Text                                        | Obrigatório       |
| Customer Group      | Habilite os grupos que possuirão esse campo | Obrigatório       |
| Required            | Opcional                                    | Opcional          |
| Status              | Opcional                                    | Opcional          |
| Sort Order          | 3                                           | Opcional          |


## Ajuda

Crie uma issue neste repositório ou entre em contato com suporte@pagar.me caso precise de ajuda em algo. =)
