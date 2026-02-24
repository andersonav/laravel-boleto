<?php

namespace Alves\LaravelBoleto\Cnab\Retorno\Cnab240\Banco;

use Alves\LaravelBoleto\Cnab\Retorno\Cnab240\AbstractRetorno;
use Alves\LaravelBoleto\Contracts\Boleto\Boleto as BoletoContract;
use Alves\LaravelBoleto\Contracts\Cnab\RetornoCnab240;
use Alves\LaravelBoleto\Util;

class Santander extends AbstractRetorno implements RetornoCnab240
{
    /**
     * CÃ³digo do banco
     *
     * @var string
     */
    protected $codigoBanco = BoletoContract::COD_BANCO_SANTANDER;

    /**
     * Array com as ocorrencias do banco;
     *
     * @var array
     */
    private $ocorrencias = [
        '02' => 'Entrada confirmada',
        '03' => 'Entrada rejeitada',
        '04' => 'transferÃªncia de carteira/entrada',
        '05' => 'transferÃªncia de carteira/baixa',
        '06' => 'LiquidaÃ§Ã£o',
        '09' => 'Baixa',
        '11' => 'tÃ­tulos em carteira (em ser)',
        '12' => 'confirmaÃ§Ã£o recebimento instruÃ§Ã£o de abatimento',
        '13' => 'confirmaÃ§Ã£o recebimento instruÃ§Ã£o de cancelamento abatimento',
        '14' => 'confirmaÃ§Ã£o recebimento instruÃ§Ã£o alteraÃ§Ã£o de vencimento',
        '17' => 'liquidaÃ§Ã£o apÃ³s baixa ou liquidaÃ§Ã£o tÃ­tulo nÃ£o registrado',
        '19' => 'confirmaÃ§Ã£o recebimento instruÃ§Ã£o de protesto',
        '20' => 'confirmaÃ§Ã£o recebimento instruÃ§Ã£o de sustaÃ§Ã£o/NÃ£o Protestar',
        '23' => 'remessa a cartorio (aponte em cartorio)',
        '24' => 'retirada de cartorio e manutenÃ§Ã£o em carteira',
        '25' => 'protestado e baixado (baixa por ter sido protestado)',
        '26' => 'instruÃ§Ã£o rejeitada',
        '27' => 'confirmaÃ§Ã£o do pedido de alteraÃ§Ã£o de outros dado',
        '28' => 'debito de tarifas/custas',
        '29' => 'ocorrÃªncias do Pagador',
        '30' => 'alteraÃ§Ã£o de dados rejeitada',
        '32' => 'CÃ³digo de IOF invÃ¡lido',
        '51' => 'TÃ­tulo DDA reconhecido pelo Pagador',
        '52' => 'TÃ­tulo DDA nÃ£o reconhecido pelo Pagador',
        '53' => 'TÃ­tulo DDA recusado pela CIP',
        'A4' => 'Pagador DDA',
    ];

    /**
     * Array com as possiveis rejeicoes do banco.
     *
     * @var array
     */
    private $rejeicoes = [
        '01' => 'cÃ³digo do banco invalido',
        '02' => 'cÃ³digo do registro detalhe invÃ¡lido',
        '03' => 'cÃ³digo do segmento invalido',
        '04' => 'cÃ³digo do movimento nÃ£o permitido para carteira',
        '05' => 'cÃ³digo de movimento invalido',
        '06' => 'tipo/numero de inscriÃ§Ã£o do BeneficiÃ¡rio invÃ¡lidos',
        '07' => 'agencia/conta/DV invalido',
        '08' => 'nosso numero invalido',
        '09' => 'nosso numero duplicado',
        '10' => 'carteira invalida',
        '11' => 'forma de cadastramento do titulo invalida',
        '12' => 'tipo de documento invalido',
        '13' => 'identificaÃ§Ã£o da emissÃ£o do Boleto invalida',
        '14' => 'identificaÃ§Ã£o da distribuiÃ§Ã£o do Boleto invalida',
        '15' => 'caracterÃ­sticas da cobranÃ§a incompatÃ­veis',
        '16' => 'data de vencimento invalida',
        '17' => 'data de vencimento anterior a data de emissÃ£o',
        '18' => 'vencimento fora do prazo de operaÃ§Ã£o',
        '19' => 'titulo a cargo de bancos correspondentes com vencimento inferior a xx dias',
        '20' => 'valor do tÃ­tulo invalido',
        '21' => 'espÃ©cie do titulo invalida',
        '22' => 'espÃ©cie nÃ£o permitida para a carteira',
        '23' => 'aceite invalido',
        '24' => 'Data de emissÃ£o invÃ¡lida',
        '25' => 'Data de emissÃ£o posterior a data de entrada',
        '26' => 'CÃ³digo de juros de mora invÃ¡lido',
        '27' => 'Valor/Taxa de juros de mora invÃ¡lido',
        '28' => 'CÃ³digo de desconto invÃ¡lido',
        '29' => 'Valor do desconto maior ou igual ao valor do tÃ­tulo',
        '30' => 'Desconto a conceder nÃ£o confere',
        '31' => 'ConcessÃ£o de desconto - jÃ¡ existe desconto anterior',
        '32' => 'Valor do IOF',
        '33' => 'Valor do abatimento invÃ¡lido',
        '34' => 'Valor do abatimento maior ou igual ao valor do tÃ­tulo',
        '35' => 'Abatimento a conceder nÃ£o confere',
        '36' => 'ConcessÃ£o de abatimento - jÃ¡ existe abatimento anterior',
        '37' => 'CÃ³digo para protesto invÃ¡lido',
        '38' => 'Prazo para protesto invÃ¡lido',
        '39' => 'Pedido de protesto nÃ£o permitido para o tÃ­tulo',
        '40' => 'TÃ­tulo com ordem de protesto emitida',
        '41' => 'Pedido de cancelamento/sustaÃ§Ã£o para tÃ­tulos sem instruÃ§Ã£o de protesto',
        '42' => 'CÃ³digo para baixa/devoluÃ§Ã£o invÃ¡lido',
        '43' => 'Prazo para baixa/devoluÃ§Ã£o invÃ¡lido',
        '44' => 'CÃ³digo de moeda invÃ¡lido',
        '45' => 'Nome do Pagador nÃ£o informado',
        '46' => 'Tipo /NÃºmero de inscriÃ§Ã£o do Pagador invÃ¡lidos',
        '47' => 'EndereÃ§o do Pagador nÃ£o informado',
        '48' => 'CEP invÃ¡lido',
        '49' => 'CEP sem praÃ§a de cobranÃ§a (nÃ£o localizado)',
        '50' => 'CEP referente a um Banco Correspondente',
        '51' => 'CEP incompatÃ­vel com a unidade de federaÃ§Ã£o',
        '52' => 'Unidade de federaÃ§Ã£o invÃ¡lida',
        '53' => 'Tipo/NÃºmero de inscriÃ§Ã£o do sacador/avalista invÃ¡lidos',
        '54' => 'Sacador/Avalista nÃ£o informado',
        '55' => 'Nosso nÃºmero no Banco Correspondente nÃ£o informado',
        '56' => 'CÃ³digo do Banco Correspondente nÃ£o informado',
        '57' => 'CÃ³digo da multa invÃ¡lido',
        '58' => 'Data da multa invÃ¡lida',
        '59' => 'Valor/Percentual da multa invÃ¡lido',
        '60' => 'Movimento para tÃ­tulo nÃ£o cadastrado',
        '61' => 'AlteraÃ§Ã£o de agÃªncia cobradora/dv invÃ¡lida',
        '62' => 'Tipo de impressÃ£o invÃ¡lido',
        '63' => 'Entrada para tÃ­tulo jÃ¡ cadastrado',
        '64' => 'NÃºmero da linha invÃ¡lido',
        '90' => 'Identificador/Quantidade de Parcelas de carnÃª invalido',
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
            ->setTipoInscricao($this->rem(17, 17, $header))
            ->setNumeroInscricao($this->rem(18, 32, $header))
            ->setAgencia($this->rem(33, 36, $header))
            ->setAgenciaDv($this->rem(37, 37, $header))
            ->setConta($this->rem(38, 46, $header))
            ->setContaDv($this->rem(47, 47, $header))
            ->setCodigoCedente($this->rem(53, 61, $header))
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
            ->setNumeroInscricao($this->rem(19, 33, $headerLote))
            ->setAgencia($this->rem(54, 57, $headerLote))
            ->setAgenciaDv($this->rem(58, 58, $headerLote))
            ->setConta($this->rem(59, 67, $headerLote))
            ->setContaDv($this->rem(68, 68, $headerLote))
            ->setCodigoCedente($this->rem(34, 42, $headerLote))
            ->setNomeEmpresa($this->rem(74, 103, $headerLote))
            ->setNumeroRetorno($this->rem(184, 191, $headerLote))
            ->setDataGravacao($this->rem(192, 199, $headerLote));

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
                ->setNossoNumero($this->rem(41, 53, $detalhe))
                ->setCarteira($this->rem(54, 54, $detalhe))
                ->setNumeroDocumento($this->rem(55, 69, $detalhe))
                ->setDataVencimento($this->rem(70, 77, $detalhe))
                ->setValor(Util::nFloat($this->rem(78, 92, $detalhe)/100, 2, false))
                ->setNumeroControle($this->rem(101, 125, $detalhe))
                ->setPagador([
                    'nome' => $this->rem(144, 183, $detalhe),
                    'documento' => $this->rem(129, 143, $detalhe),
                ])
                ->setValorTarifa(Util::nFloat($this->rem(194, 208, $detalhe)/100, 2, false));

            /**
             * ocorrencias
            */
            $msgAdicional = str_split(sprintf('%010s', $this->rem(209, 218, $detalhe)), 2) + array_fill(0, 5, '');
            if ($d->hasOcorrencia('06', '09', '17')) {
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
            } elseif ($d->hasOcorrencia('19')) {
                $this->totais['protestados']++;
                $d->setOcorrenciaTipo($d::OCORRENCIA_PROTESTADA);
            } elseif ($d->hasOcorrencia('27', '30')) {
                $this->totais['alterados']++;
                $d->setOcorrenciaTipo($d::OCORRENCIA_ALTERACAO);
            } elseif ($d->hasOcorrencia('03', '26', '30')) {
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

        if ($this->getSegmentType($detalhe) == 'Y') {
            $d->setCheques([
                '1' => $this->rem(20, 53, $detalhe),
                '2' => $this->rem(44, 87, $detalhe),
                '3' => $this->rem(88, 121, $detalhe),
                '4' => $this->rem(122, 155, $detalhe),
                '5' => $this->rem(156, 189, $detalhe),
                '6' => $this->rem(190, 223, $detalhe),
            ]);
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
            ->setValorTotalTitulosCobrancaDescontada(Util::nFloat($this->rem(99, 115, $trailer)/100, 2, false))
            ->setNumeroAvisoLancamento($this->rem(116, 123, $trailer));

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

