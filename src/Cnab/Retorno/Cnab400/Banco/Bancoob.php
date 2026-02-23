<?php
namespace Alves\LaravelBoleto\Cnab\Retorno\Cnab400\Banco;

use Alves\LaravelBoleto\Cnab\Retorno\Cnab400\AbstractRetorno;
use Alves\LaravelBoleto\Contracts\Boleto\Boleto as BoletoContract;
use Alves\LaravelBoleto\Contracts\Cnab\RetornoCnab400;
use Alves\LaravelBoleto\Util;

class Bancoob extends AbstractRetorno implements RetornoCnab400
{
    /**
     * CÃ³digo do banco
     *
     * @var string
     */
    protected $codigoBanco = BoletoContract::COD_BANCO_BANCOOB;

    /**
     * Array com as ocorrencias do banco;
     *
     * @var array
     */

    private $ocorrencias = [
        '02' => 'ConfirmaÃ§Ã£o Entrada TÃ­tulo',
        '05' => 'LiquidaÃ§Ã£o Sem Registro: Identifica a liquidaÃ§Ã£o de tÃ­tulo da modalidade ""SEM REGISTRO""',
        '06' => 'LiquidaÃ§Ã£o Normal: Identificar a liquidaÃ§Ã£o de tÃ­tulo de modalidade ""REGISTRADA"", com exceÃ§Ã£o dos tÃ­tulos que forem liquidados em cartÃ³rio (CÃ³d. de movimento 15=LiquidaÃ§Ã£o em CartÃ³rio)',
        '09' => 'Baixa de Titulo: Identificar as baixas de tÃ­tulos, com exceÃ§Ã£o da baixa realizada com o cÃ³d. de movimento 10 (Baixa - Pedido BeneficiÃ¡rio)',
        '10' => 'Baixa Solicitada (Baixa - Pedido BeneficiÃ¡rio): Identificar as baixas de tÃ­tulos comandadas a pedido do BeneficiÃ¡rio',
        '11' => 'TÃ­tulos em Ser: Identifica os tÃ­tulos em carteira, que estiverem com a situaÃ§Ã£o ""em abarto"" (vencidos e a vencer).',
        '14' => 'AlteraÃ§Ã£o de Vencimento',
        '15' => 'LiquidaÃ§Ã£o em CartÃ³rio: Identifica as liquidaÃ§Ãµes dos tÃ­tulos ocorridas em cartÃ³rios de protesto',
        '23' => 'Encaminhado a Protesto: Identifica o recebimento da instruÃ§Ã£o de protesto',
        '27' => 'ConfirmaÃ§Ã£o AlteraÃ§Ã£o Dados.',
        '48' => 'ConfirmaÃ§Ã£o de instruÃ§Ã£o de transferÃªncia de carteira/modalidade de cobranÃ§a"'
    ];
    /**
     * Array com as possiveis rejeicoes do banco.
     *
     * @var array
     */
    private $rejeicoes = [
        '02' => 'CÃ³digo do registro detalhe invÃ¡lido',
        '03' => 'CÃ³digo da ocorrÃªncia invÃ¡lida',
        '04' => 'CÃ³digo de ocorrÃªncia nÃ£o permitida para a carteira',
        '05' => 'CÃ³digo de ocorrÃªncia nÃ£o numÃ©rico',
        '07' => 'AgÃªncia/conta/Digito â€“ |InvÃ¡lido',
        '08' => 'Nosso nÃºmero invÃ¡lido',
        '09' => 'Nosso nÃºmero duplicado',
        '10' => 'Carteira invÃ¡lida',
        '16' => 'Data de vencimento invÃ¡lida',
        '18' => 'Vencimento fora do prazo de operaÃ§Ã£o',
        '20' => 'Valor do TÃ­tulo invÃ¡lido',
        '21' => 'EspÃ©cie do TÃ­tulo invÃ¡lida',
        '22' => 'EspÃ©cie nÃ£o permitida para a carteira',
        '24' => 'Data de emissÃ£o invÃ¡lida',
        '38' => 'Prazo para protesto invÃ¡lido',
        '44' => 'AgÃªncia Cedente nÃ£o prevista',
        '50' => 'CEP irregular â€“ Banco Correspondente',
        '63' => 'Entrada para TÃ­tulo jÃ¡ cadastrado',
        '68' => 'DÃ©bito nÃ£o agendado â€“ erro nos dados de remessa',
        '69' => 'DÃ©bito nÃ£o agendado â€“ Pagador nÃ£o consta no cadastro de autorizante',
        '70' => 'DÃ©bito nÃ£o agendado â€“ Cedente nÃ£o autorizado pelo Pagador',
        '71' => 'DÃ©bito nÃ£o agendado â€“ Cedente nÃ£o participa da modalidade de dÃ©bito automÃ¡tico',
        '72' => 'DÃ©bito nÃ£o agendado â€“ CÃ³digo de moeda diferente de R$',
        '73' => 'DÃ©bito nÃ£o agendado â€“ Data de vencimento invÃ¡lida',
        '74' => 'DÃ©bito nÃ£o agendado â€“ Conforme seu pedido, TÃ­tulo nÃ£o registrado',
        '75' => 'DÃ©bito nÃ£o agendado â€“ Tipo de nÃºmero de inscriÃ§Ã£o do debitado invÃ¡lido',
];

    /**
     * Roda antes dos metodos de processar
     */
    protected function init()
    {
        $this->totais = [
            'liquidados' => 0,
            'entradas' => 0,
            'baixados' => 0,
            'protestados' => 0,
            'alterados' => 0,
        ];
    }

    /**
     * @param array $header
     *
     * @return bool
     * @throws \Exception
     */
    protected function processarHeader(array $header)
    {
        $this->getHeader()
            ->setOperacaoCodigo($this->rem(2, 2, $header))
            ->setOperacao($this->rem(3, 9, $header))
            ->setServicoCodigo($this->rem(10, 11, $header))
            ->setServico($this->rem(12, 19, $header))
            ->setAgencia($this->rem(27, 30, $header))
            ->setAgenciaDv($this->rem(31, 31, $header))
            ->setConta($this->rem(32, 39, $header))
            ->setContaDv($this->rem(40, 40, $header))
            ->setConvenio($this->rem(41, 46, $header))
            ->setData($this->rem(95, 100, $header));

        return true;
    }

    /**
     * @param array $detalhe
     *
     * @return bool
     * @throws \Exception
     */
    protected function processarDetalhe(array $detalhe)
    {
        $d = $this->detalheAtual();

        $d->setCarteira($this->rem(108, 108, $detalhe))
            ->setNossoNumero($this->rem(63, 73, $detalhe))
            ->setNumeroDocumento($this->rem(117, 126, $detalhe))
            ->setNumeroControle($this->rem(38, 62, $detalhe))
            ->setOcorrencia($this->rem(109, 110, $detalhe))
            ->setOcorrenciaDescricao(array_get($this->ocorrencias, $d->getOcorrencia(), 'Desconhecida'))
            ->setDataOcorrencia($this->rem(111, 116, $detalhe))
            ->setDataVencimento($this->rem(147, 152, $detalhe))
            ->setDataCredito($this->rem(176, 181, $detalhe))
            ->setValor(Util::nFloat($this->rem(153, 165, $detalhe)/100, 2, false))
            ->setValorTarifa(Util::nFloat($this->rem(182, 188, $detalhe)/100, 2, false))
            ->setValorIOF(Util::nFloat($this->rem(215, 227, $detalhe)/100, 2, false))
            ->setValorAbatimento(Util::nFloat($this->rem(228, 240, $detalhe)/100, 2, false))
            ->setValorDesconto(Util::nFloat($this->rem(241, 253, $detalhe)/100, 2, false))
            ->setValorRecebido(Util::nFloat($this->rem(254, 266, $detalhe)/100, 2, false))
            ->setValorMora(Util::nFloat($this->rem(267, 279, $detalhe)/100, 2, false))
            ->setValorMulta(Util::nFloat($this->rem(280, 292, $detalhe)/100, 2, false));

        $msgAdicional = str_split(sprintf('%08s', $this->rem(319, 328, $detalhe)), 2) + array_fill(0, 5, '');
        if ($d->hasOcorrencia('05', '06')) {
            $this->totais['liquidados']++;
            $d->setOcorrenciaTipo($d::OCORRENCIA_LIQUIDADA);
        } elseif ($d->hasOcorrencia('02')) {
            $this->totais['entradas']++;
            $d->setOcorrenciaTipo($d::OCORRENCIA_ENTRADA);
        } elseif ($d->hasOcorrencia('09')) {
            $this->totais['baixados']++;
            $d->setOcorrenciaTipo($d::OCORRENCIA_BAIXADA);
        } elseif ($d->hasOcorrencia('23')) {
            $this->totais['protestados']++;
            $d->setOcorrenciaTipo($d::OCORRENCIA_PROTESTADA);
        } elseif ($d->hasOcorrencia('14')) {
            $this->totais['alterados']++;
            $d->setOcorrenciaTipo($d::OCORRENCIA_ALTERACAO);
        }  elseif ($d->hasOcorrencia('03', '24', '27', '30', '32')) {
            $this->totais['erros']++;
            $error = Util::appendStrings(
                array_get($this->rejeicoes, $msgAdicional[0], ''),
                array_get($this->rejeicoes, $msgAdicional[1], ''),
                array_get($this->rejeicoes, $msgAdicional[2], ''),
                array_get($this->rejeicoes, $msgAdicional[3], ''),
                array_get($this->rejeicoes, $msgAdicional[4], '')
            );
            $d->setError($error);
        } else {
            $d->setOcorrenciaTipo($d::OCORRENCIA_OUTROS);
        }

        return true;
    }

    /**
     * @param array $trailer
     *
     * @return bool
     * @throws \Exception
     */
    protected function processarTrailer(array $trailer)
    {
        $this->getTrailer()
            ->setQuantidadeTitulos((int) $this->rem(164, 171, $trailer))
            ->setQuantidadeEntradas((int) $this->totais['entradas'])
            ->setQuantidadeLiquidados((int) $this->totais['liquidados'])
            ->setQuantidadeBaixados((int) $this->totais['baixados'])
            ->setQuantidadeAlterados((int) $this->totais['alterados']);

        return true;
    }
}

