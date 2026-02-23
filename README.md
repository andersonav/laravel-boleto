# Laravel Boleto

Biblioteca para gerar boletos, remessas CNAB e leitura de arquivos de retorno no ecossistema Laravel.

[![Packagist](https://img.shields.io/packagist/v/alves/laravel-boleto.svg?style=flat-square)](https://packagist.org/packages/alves/laravel-boleto)
[![Downloads](https://img.shields.io/packagist/dt/alves/laravel-boleto.svg?style=flat-square)](https://packagist.org/packages/alves/laravel-boleto)
[![License](https://img.shields.io/packagist/l/alves/laravel-boleto.svg?style=flat-square)](LICENSE)

## Requisitos

- PHP 8.2 ou superior
- Laravel 12
- Extensões PHP: `intl` e `mbstring`

## Instalação

```bash
composer require alves/laravel-boleto
```

## Uso rápido

```php
<?php

use Alves\LaravelBoleto\Boleto\Banco\Bradesco;
use Alves\LaravelBoleto\Pessoa;

$beneficiario = new Pessoa([
    'nome'      => 'ACME LTDA',
    'documento' => '12.345.678/0001-90',
    'endereco'  => 'Rua 1',
    'bairro'    => 'Centro',
    'cep'       => '99999-999',
    'uf'        => 'SP',
    'cidade'    => 'São Paulo',
]);

$pagador = new Pessoa([
    'nome'      => 'Cliente Teste',
    'documento' => '123.456.789-09',
    'endereco'  => 'Rua 2',
    'bairro'    => 'Centro',
    'cep'       => '99999-999',
    'uf'        => 'SP',
    'cidade'    => 'São Paulo',
]);

$boleto = new Bradesco([
    'logo'            => realpath(__DIR__ . '/logos/237.png'),
    'dataVencimento'  => new DateTime(),
    'valor'           => 100.00,
    'multa'           => false,
    'juros'           => false,
    'numero'          => 1,
    'numeroDocumento' => 1,
    'pagador'         => $pagador,
    'beneficiario'    => $beneficiario,
    'agencia'         => 1111,
    'conta'           => 99999,
    'carteira'        => 9,
]);
```

## Exemplos

Os exemplos completos estão na pasta `exemplos/`:

- Geração de boletos por banco
- Geração de remessas CNAB 240/400
- Leitura de arquivos de retorno

## Bancos suportados

- Banco do Brasil
- Bancoob
- Banrisul
- Bradesco
- Caixa
- HSBC
- Itaú
- Santander
- Sicredi
- BNB
- Safra

## Estrutura do projeto

- `src/`: núcleo da biblioteca
- `exemplos/`: scripts de referência
- `logos/`: logos utilizados nos boletos
- `docker/`: ambiente de apoio para desenvolvimento local

## Autor e manutenção

Este fork/estrutura é mantido por **Alves Gusmão**.

Se quiser contribuir:

1. Abra uma issue descrevendo a melhoria ou problema.
2. Envie um PR com alteração objetiva e testável.
3. Inclua evidências de funcionamento (exemplo gerado, retorno processado ou teste).

## Licença

Este projeto está licenciado sob a [MIT](LICENSE).
