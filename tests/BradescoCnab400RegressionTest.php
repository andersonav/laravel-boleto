<?php

declare(strict_types=1);

namespace Alves\LaravelBoleto\Tests;

use Alves\LaravelBoleto\Cnab\Retorno\Cnab400\Banco\Bradesco;
use Alves\LaravelBoleto\Cnab\Retorno\Factory;
use PHPUnit\Framework\TestCase;

class BradescoCnab400RegressionTest extends TestCase
{
    public function test_bradesco_cnab400_parses_money_fields_without_type_errors(): void
    {
        $retorno = Factory::make(__DIR__ . '/../exemplos/arquivos/bradesco.ret');
        $detalhe = $retorno->getDetalhe(1);

        self::assertInstanceOf(Bradesco::class, $retorno);
        self::assertNotNull($detalhe);
        self::assertSame('02', $detalhe->getOcorrencia());
        self::assertSame('100.00', $detalhe->getValor());
        self::assertSame('100.00', $detalhe->getValorRecebido());
        self::assertSame(
            [
                'liquidados' => 0,
                'entradas' => 1,
                'baixados' => 0,
                'protestados' => 0,
                'erros' => 0,
                'alterados' => 0,
            ],
            $retorno->getTotais()
        );
    }
}