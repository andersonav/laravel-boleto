<?php

declare(strict_types=1);

namespace Alves\LaravelBoleto\Tests;

use Alves\LaravelBoleto\Cnab\Retorno\Factory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class RetornoFactoryTest extends TestCase
{
    #[DataProvider('sampleReturnFiles')]
    public function test_can_parse_sample_return_files(string $fixture): void
    {
        $retorno = Factory::make($fixture);

        self::assertNotSame('', $retorno->getCodigoBanco());
        self::assertContains($retorno->getTipo(), [240, 400]);
        self::assertGreaterThan(
            0,
            $retorno->getDetalhes()->count(),
            sprintf('Fixture %s deveria gerar ao menos um detalhe.', basename($fixture))
        );
    }

    public static function sampleReturnFiles(): array
    {
        $fixtures = glob(__DIR__ . '/../exemplos/arquivos/*.ret');
        sort($fixtures);

        $data = [];
        foreach ($fixtures as $fixture) {
            $data[basename($fixture)] = [$fixture];
        }

        return $data;
    }
}