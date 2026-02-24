<?php

namespace Alves\LaravelBoleto\Cnab\Retorno\Cnab240\Banco;

use Alves\LaravelBoleto\Cnab\Retorno\Cnab240\AbstractRetorno;
use Alves\LaravelBoleto\Contracts\Boleto\Boleto as BoletoContract;
use Alves\LaravelBoleto\Contracts\Cnab\RetornoCnab240;
use Alves\LaravelBoleto\Util;

class Sicredi extends AbstractRetorno implements RetornoCnab240
{
    /**
     * CÃ³digo do banco
     *
     * @var string
     */
    protected $codigoBanco = BoletoContract::COD_BANCO_SICREDI;

    /**
     * Array com as ocorrencias do banco;
     *
     * @var array
     */
    private $ocorrencias = [
        '02' => 'Entrada confirmada',
        '03' => 'Entrada rejeitada',
        '06' => 'LiquidaÃ§Ã£o',
        '07' => 'ConfirmaÃ§Ã£o do recebimento da instruÃ§Ã£o de desconto',
        '08' => 'ConfirmaÃ§Ã£o do recebimento do cancelamento do desconto',
        '09' => 'Baixa',
        '12' => 'ConfirmaÃ§Ã£o do recebimento instruÃ§Ã£o de abatimento',
        '13' => 'ConfirmaÃ§Ã£o do recebimento instruÃ§Ã£o de cancelamento abatimento',
        '14' => 'ConfirmaÃ§Ã£o do recebimento instruÃ§Ã£o alteraÃ§Ã£o de vencimento',
        '17' => 'LiquidaÃ§Ã£o apÃ³s baixa ou liquidaÃ§Ã£o tÃ­tulo nÃ£o registrado',
        '19' => 'ConfirmaÃ§Ã£o do recebimento instruÃ§Ã£o de protesto',
        '20' => 'ConfirmaÃ§Ã£o do recebimento instruÃ§Ã£o de sustaÃ§Ã£o/cancelamento de protesto',
        '23' => 'Remessa a cartÃ³rio (aponte em cartÃ³rio)',
        '24' => 'Retirada de cartÃ³rio e manutenÃ§Ã£o em carteira',
        '25' => 'Protestado e baixado (baixa por ter sido protestado)',
        '26' => 'InstruÃ§Ã£o rejeitada',
        '27' => 'ConfirmaÃ§Ã£o do pedido de alteraÃ§Ã£o de outros dados',
        '28' => 'DÃ©bito de tarifas custas',
        '30' => 'AlteraÃ§Ã£o de dados rejeitada',
        '36' => 'Baixa rejeitada',
        '51' => 'TÃ­tulo DDA reconhecido pelo pagador',
        '52' => 'TÃ­tulo DDA nÃ£o reconhecido pelo pagador',
    ];

    /**
     * Array com as possiveis rejeicoes do banco.
     *
     * @var array
     */
    private $rejeicoes = [
        '22' => 'EspÃ©cie do tÃ­tulo nÃ£o permitida para a carteira',
        '23' => 'Aceite invÃ¡lido',
        '24' => 'Data da emissÃ£o invÃ¡lida',
        '25' => 'Data da emissÃ£o posterior a data de entrada',
        '26' => 'CÃ³digo de juros de mora invÃ¡lido',
        '27' => 'Valor/taxa de juros de mora invÃ¡lido',
        '28' => 'CÃ³digo do desconto invÃ¡lido',
        '29' => 'Valor do desconto maior ou igual ao valor do tÃ­tulo',
        '30' => 'Desconto a conceder nÃ£o confere',
        '31' => 'ConcessÃ£o de desconto - jÃ¡ existe desconto anterior',
        '33' => 'Valor do abatimento invÃ¡lido',
        '34' => 'Valor do abatimento maior ou igual ao valor do tÃ­tulo',
        '35' => 'Valor a conceder nÃ£o confere',
        '36' => 'ConcessÃ£o de abatimento - jÃ¡ existe abatimento anterior',
        '37' => 'CÃ³digo para protesto invÃ¡lido',
        '38' => 'Prazo para protesto invÃ¡lido',
        '39' => 'Pedido de protesto nÃ£o permitido para o tÃ­tulo',
        '40' => 'TÃ­tulo com ordem de protesto emitida',
        '41' => 'Pedido de cancelamento/sustaÃ§Ã£o para tÃ­tulos sem instruÃ§Ã£o de protesto',
        '44' => 'CÃ³digo da moeda invÃ¡lido',
        '45' => 'Nome do pagador nÃ£o informado',
        '46' => 'Tipo/nÃºmero de inscriÃ§Ã£o do pagador invÃ¡lidos',
        '47' => 'EndereÃ§o do pagador nÃ£o informado',
        '48' => 'CEP invÃ¡lido',
        '53' => 'Tipo/nÃºmero de inscriÃ§Ã£o do pagador/avalista invÃ¡lido',
        '54' => 'Pagador/avalista nÃ£o informado',
        '55' => 'Nosso nÃºmero no banco correspondente nÃ£o informado',
        '56' => 'CÃ³digo do banco correspondente nÃ£o informado',
        '57' => 'CÃ³digo da multa invÃ¡lido',
        '58' => 'Data da multa invÃ¡lida',
        '59' => 'Valor/percentual da multa invÃ¡lido',
        '60' => 'Movimento para tÃ­tulo nÃ£o cadastrado',
        '61' => 'AlteraÃ§Ã£o da cooperativa crÃ©dito/agÃªncia cobradora/DV invÃ¡lida',
        '62' => 'Tipo de impressÃ£o invÃ¡lido',
        '63' => 'Entrada para tÃ­tulo jÃ¡ cadastrado',
        '64' => 'NÃºmero da linha invÃ¡lido',
        '79' => 'Data juros de mora invÃ¡lida',
        '80' => 'Data do desconto invÃ¡lida',
        '84' => 'NÃºmero autorizaÃ§Ã£o inexistente',
        '85' => 'TÃ­tulo com pagamento vinculado',
        '86' => 'Seu nÃºmero invÃ¡lido',
        'A4' => 'Pagador DDA',
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
            'erros' => 0,
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
            ->setCodBanco($this->rem(1, 3, $header))
            ->setLoteServico($this->rem(4, 7, $header))
            ->setTipoRegistro($this->rem(8, 8, $header))
            ->setTipoInscricao($this->rem(18, 18, $header))
            ->setNumeroInscricao($this->rem(19, 32, $header))
            ->setCodigoCedente($this->rem(33, 52, $header))
            ->setAgencia($this->rem(53, 57, $header))
            ->setAgenciaDv($this->rem(58, 58, $header))
            ->setConta($this->rem(59, 70, $header))
            ->setContaDv($this->rem(71, 71, $header))
            ->setNomeEmpresa($this->rem(73, 102, $header))
            ->setNomeBanco($this->rem(103, 132, $header))
            ->setCodigoRemessaRetorno($this->rem(143, 143, $header))
            ->setData($this->rem(144, 151, $header))
            ->setNumeroSequencialArquivo($this->rem(158, 163, $header))
            ->setVersaoLayoutArquivo($this->rem(164, 166, $header));

        return true;
    }

    /**
     * @param array $headerLote
     *
     * @return bool
     * @throws \Exception
     */
    protected function processarHeaderLote(array $headerLote)
    {
        $this->getHeaderLote()
            ->setCodBanco($this->rem(1, 3, $headerLote))
            ->setNumeroLoteRetorno($this->rem(4, 7, $headerLote))
            ->setTipoRegistro($this->rem(8, 8, $headerLote))
            ->setTipoOperacao($this->rem(9, 9, $headerLote))
            ->setTipoServico($this->rem(10, 11, $headerLote))
            ->setVersaoLayoutLote($this->rem(14, 16, $headerLote))
            ->setTipoInscricao($this->rem(18, 18, $headerLote))
            ->setNumeroInscricao($this->rem(19, 33, $headerLote))
            ->setCodigoCedente($this->rem(34, 53, $headerLote))
            ->setAgencia($this->rem(54, 58, $headerLote))
            ->setAgenciaDv($this->rem(59, 59, $headerLote))
            ->setConta($this->rem(60, 71, $headerLote))
            ->setContaDv($this->rem(72, 72, $headerLote))
            ->setNomeEmpresa($this->rem(74, 103, $headerLote))
            ->setNumeroRetorno($this->rem(184, 191, $headerLote))
            ->setDataGravacao($this->rem(192, 199, $headerLote))
            ->setDataCredito($this->rem(200, 207, $headerLote));

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

        if ($this->getSegmentType($detalhe) == 'T') {
            $d->setOcorrencia($this->rem(16, 17, $detalhe))
                ->setOcorrenciaDescricao(data_get($this->ocorrencias, $this->detalheAtual()->getOcorrencia(), 'Desconhecida'))
                ->setNossoNumero($this->rem(38, 57, $detalhe))
                ->setCarteira($this->rem(58, 58, $detalhe))
                ->setNumeroDocumento($this->rem(59, 73, $detalhe))
                ->setDataVencimento($this->rem(74, 81, $detalhe))
                ->setValor(Util::nFloat($this->rem(82, 96, $detalhe)/100, 2, false))
                ->setNumeroControle($this->rem(106, 130, $detalhe))
                ->setPagador([
                    'nome' => $this->rem(149, 188, $detalhe),
                    'documento' => $this->rem(134, 148, $detalhe),
                ])
                ->setValorTarifa(Util::nFloat($this->rem(199, 213, $detalhe)/100, 2, false));

            /**
             * ocorrencias
            */
            $msgAdicional = str_split(sprintf('%08s', $this->rem(214, 223, $detalhe)), 2) + array_fill(0, 5, '');
            if ($d->hasOcorrencia('06', '17')) {
                $this->totais['liquidados']++;
                $d->setOcorrenciaTipo($d::OCORRENCIA_LIQUIDADA);
            } elseif ($d->hasOcorrencia('02')) {
                $this->totais['entradas']++;
                if(array_search('a4', array_map('strtolower', $msgAdicional)) !== false) {
                    $d->getPagador()->setDda(true);
                }
                $d->setOcorrenciaTipo($d::OCORRENCIA_ENTRADA);
            } elseif ($d->hasOcorrencia('09')) {
                $this->totais['baixados']++;
                $d->setOcorrenciaTipo($d::OCORRENCIA_BAIXADA);
            } elseif ($d->hasOcorrencia('25')) {
                $this->totais['protestados']++;
                $d->setOcorrenciaTipo($d::OCORRENCIA_PROTESTADA);
            } elseif ($d->hasOcorrencia('27', '14')) {
                $this->totais['alterados']++;
                $d->setOcorrenciaTipo($d::OCORRENCIA_ALTERACAO);
            } elseif ($d->hasOcorrencia('03', '26', '30', '36')) {
                $this->totais['erros']++;
                $error = Util::appendStrings(
                    data_get($this->rejeicoes, $msgAdicional[0], ''),
                    data_get($this->rejeicoes, $msgAdicional[1], ''),
                    data_get($this->rejeicoes, $msgAdicional[2], ''),
                    data_get($this->rejeicoes, $msgAdicional[3], ''),
                    data_get($this->rejeicoes, $msgAdicional[4], '')
                );
                $d->setError($error);
            } else {
                $d->setOcorrenciaTipo($d::OCORRENCIA_OUTROS);
            }
        }

        if ($this->getSegmentType($detalhe) == 'U') {
            $d->setValorMulta(Util::nFloat($this->rem(18, 32, $detalhe)/100, 2, false))
                ->setValorDesconto(Util::nFloat($this->rem(33, 47, $detalhe)/100, 2, false))
                ->setValorAbatimento(Util::nFloat($this->rem(48, 62, $detalhe)/100, 2, false))
                ->setValorIOF(Util::nFloat($this->rem(63, 77, $detalhe)/100, 2, false))
                ->setValorRecebido(Util::nFloat($this->rem(78, 92, $detalhe)/100, 2, false))
                ->setDataOcorrencia($this->rem(138, 145, $detalhe))
                ->setDataCredito($this->rem(146, 153, $detalhe));

            if(Util::nFloat($this->rem(93, 107, $detalhe)/100, 2, false) > 0 && ($d->getValorRecebido() - Util::nFloat($this->rem(93, 107, $detalhe)/100, 2, false) > 0)){
                $d->setValorTarifa($d->getValorRecebido() - Util::nFloat($this->rem(93, 107, $detalhe)/100, 2, false));
            }
        }

        return true;
    }

    /**
     * @param array $trailer
     *
     * @return bool
     * @throws \Exception
     */
    protected function processarTrailerLote(array $trailer)
    {
        $this->getTrailerLote()
            ->setLoteServico($this->rem(4, 7, $trailer))
            ->setTipoRegistro($this->rem(8, 8, $trailer))
            ->setQtdRegistroLote((int) $this->rem(18, 23, $trailer))
            ->setQtdTitulosCobrancaSimples((int) $this->rem(24, 29, $trailer))
            ->setValorTotalTitulosCobrancaSimples(Util::nFloat($this->rem(30, 46, $trailer)/100, 2, false))
            ->setQtdTitulosCobrancaVinculada((int) $this->rem(47, 52, $trailer))
            ->setValorTotalTitulosCobrancaVinculada(Util::nFloat($this->rem(53, 69, $trailer)/100, 2, false))
            ->setQtdTitulosCobrancaCaucionada((int) $this->rem(70, 75, $trailer))
            ->setValorTotalTitulosCobrancaCaucionada(Util::nFloat($this->rem(76, 92, $trailer)/100, 2, false))
            ->setQtdTitulosCobrancaDescontada((int) $this->rem(93, 98, $trailer))
            ->setValorTotalTitulosCobrancaDescontada(Util::nFloat($this->rem(99, 115, $trailer)/100, 2, false));

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
            ->setNumeroLote($this->rem(4, 7, $trailer))
            ->setTipoRegistro($this->rem(8, 8, $trailer))
            ->setQtdLotesArquivo((int) $this->rem(18, 23, $trailer))
            ->setQtdRegistroArquivo((int) $this->rem(24, 29, $trailer));

        return true;
    }
}

