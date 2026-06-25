<?php

declare(strict_types=1);

namespace Alves\LaravelBoleto\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class RemessaExamplesTest extends TestCase
{
    #[DataProvider('remessaScripts')]
    public function test_remessa_example_runs_and_generates_valid_file(string $script): void
    {
        $command = sprintf('php %s', escapeshellarg($script));
        exec($command . ' 2>&1', $output, $exitCode);
        $joinedOutput = trim(implode(PHP_EOL, $output));

        self::assertSame(0, $exitCode, $joinedOutput === '' ? 'Falha ao executar exemplo de remessa.' : $joinedOutput);
        self::assertStringNotContainsString('Deprecated:', $joinedOutput, $joinedOutput);
        self::assertStringNotContainsString('Fatal error', $joinedOutput, $joinedOutput);
        self::assertNotSame('', $joinedOutput, 'O exemplo deveria informar o caminho do arquivo gerado.');

        $lines = preg_split('/\r\n|\r|\n/', $joinedOutput);
        $path = trim((string) end($lines));

        self::assertFileExists($path);

        $content = file_get_contents($path);
        self::assertNotFalse($content);
        self::assertNotSame('', $content);

        $rows = preg_split('/\r\n|\r|\n/', trim($content));
        self::assertGreaterThanOrEqual(3, count($rows));

        $expectedLength = str_contains(basename($script), 'cnab240') ? 240 : null;
        if ($expectedLength === null) {
            $expectedLength = strlen($rows[0]) === 240 ? 240 : 400;
        }

        foreach ($rows as $row) {
            self::assertSame($expectedLength, strlen($row), sprintf('Linha invalida gerada por %s: %s', basename($script), $row));
        }
    }

    public static function remessaScripts(): array
    {
        $scripts = glob(__DIR__ . '/../exemplos/*_remessa.php');
        sort($scripts);

        $data = [];
        foreach ($scripts as $script) {
            $data[basename($script)] = [$script];
        }

        return $data;
    }
}