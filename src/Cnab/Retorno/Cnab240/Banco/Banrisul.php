<?php

namespace Alves\LaravelBoleto\Cnab\Retorno\Cnab240\Banco;

use Alves\LaravelBoleto\Cnab\Retorno\Cnab240\AbstractRetorno;
use Alves\LaravelBoleto\Contracts\Boleto\Boleto as BoletoContract;
use Alves\LaravelBoleto\Contracts\Cnab\RetornoCnab240;
use Alves\LaravelBoleto\Util;

class Banrisul extends AbstractRetorno implements RetornoCnab240
{
    /**
     * CÃ³digo do banco
     *
     * @var string
     */
    protected $codigoBanco = BoletoContract::COD_BANCO_BANRISUL;

    /**
     * Array com as ocorrencias do banco;
     *
     * @var array
     */
    private $ocorrencias = [
        '02' => 'Entrada Confirmada',
        '03' => 'Entrada Rejeitada',
        '04' => 'Reembolso e Transf. (Desconto-Vendor) ou Transf. de Carteira (Garantia)',
        '05' => 'Reembolso e DevoluÃ§Ã£o Desconto e Vendor',
        '06' => 'LiquidaÃ§Ã£o',
        '09' => 'Baixa',
        '11' => 'TÃ­tulo em carteira (em ser) - Para este cÃ³digo de ocorrÃªncia, o campo data da ocorrÃªncia no banco (posiÃ§Ã£o 138-145 do segmento â€œUâ€), serÃ¡ a data do registro dos tÃ­tulos',
        '12' => 'ConfirmaÃ§Ã£o recebimento instruÃ§Ã£o abatimento',
        '13' => 'ConfirmaÃ§Ã£o recebimento instruÃ§Ã£o de cancelamento abatimento',
        '14' => 'ConfirmaÃ§Ã£o instruÃ§Ã£o alteraÃ§Ã£o de vencimento',
        '15' => 'ConfirmaÃ§Ã£o de Protesto Imediato por FalÃªncia',
        '17' => 'LiquidaÃ§Ã£o apÃ³s baixa ou liquidaÃ§Ã£o tÃ­tulo nÃ£o registrado',
        '19' => 'ConfirmaÃ§Ã£o Recebimento InstruÃ§Ã£o Protesto',
        '20' => 'ConfirmaÃ§Ã£o Recebimento InstruÃ§Ã£o de SustaÃ§Ã£o/Cancelamento de Protesto',
        '23' => 'Remessa a CartÃ³rio (aponte em cartÃ³rio) - A data da Entrega em cartÃ³rio Ã© informada nas posiÃ§Ãµes 138 a 145 do segmento U',
        '24' => 'Reservado',
        '25' => 'Protestado e baixado (baixa por ter sido protestado)',
        '26' => 'InstruÃ§Ã£o Rejeitada',
        '27' => 'ConfirmaÃ§Ã£o do pedido de alteraÃ§Ã£o de outros dados',
        '28' => 'DÃ©bito de tarifas/custo',
        '30' => 'AlteraÃ§Ã£o de Dados rejeitado',
        'AA' => 'DevoluÃ§Ã£o, Liquidado Anteriormente (CCB) - A informaÃ§Ã£o da Data da LiquidaÃ§Ã£o estÃ¡ nas posiÃ§Ãµes 138 a 145 do segmento U',
        'AB' => 'CobranÃ§a a Creditar (em trÃ¢nsito)*',
        'AC' => 'SituaÃ§Ã£o do TÃ­tulo â€“ CartÃ³rio',
    ];


    /**
     * Array com as possiveis descricoes de baixa e liquidacao.
     *
     * @var array
     */
    private $baixa_liquidacao = [
        '01' => 'Por saldo â€“ Reservado',
        '02' => 'Por conta (Parcial)',
        '03' => 'No prÃ³prio Banco',
        '04' => 'CompensaÃ§Ã£o EletrÃ´nica',
        '05' => 'CompensaÃ§Ã£o Convencional',
        '06' => 'Por meio EletrÃ´nico',
        '07' => 'Reservado',
        '08' => 'Em cartÃ³rio',
        '09' => 'Comandado Banco',
        '10' => 'Comandado cliente Arquivo',
        '11' => 'Comandado cliente On-Line',
        '12' => 'Decurso prazo â€“ cliente',
        'AA' => 'Baixa por Pagamento',
    ];


    /**
     * Array com as possiveis rejeicoes do banco.
     *
     * @var array
     */
    private $rejeicoes = [
        '01' => 'CÃ³digo do Banco invÃ¡lido',
        '02' => 'CÃ³digo de registro detalhe invÃ¡lido',
        '03' => 'CÃ³digo do Segmento invÃ¡lido',
        '04' => 'CÃ³digo do movimento nÃ£o permitido para a carteira',
        '05' => 'CÃ³digo do movimento invÃ¡lido',
        '06' => 'Tipo/NÃºmero de inscriÃ§Ã£o do BeneficiÃ¡rio invÃ¡lido',
        '07' => 'AgÃªncia/conta/DV invÃ¡lido',
        '08' => 'Nosso NÃºmero invÃ¡lido',
        '09' => 'Nosso nÃºmero duplicado',
        '10' => 'Carteira invÃ¡lida',
        '11' => 'Forma de cadastramento do tÃ­tulo invÃ¡lido',
        '12' => 'Tipo de documento invÃ¡lido',
        '13' => 'IdentificaÃ§Ã£o da emissÃ£o do bloqueto invÃ¡lido',
        '14' => 'IdentificaÃ§Ã£o da distribuiÃ§Ã£o do bloqueto invÃ¡lido',
        '15' => 'CaracterÃ­sticas da cobranÃ§a incompatÃ­veis - se a carteira e a moeda forem vÃ¡lidas e nÃ£o existir espÃ©cie',
        '16' => 'Data de vencimento invÃ¡lida',
        '17' => 'Data de vencimento anterior a data de emissÃ£o',
        '18' => 'Vencimento fora do prazo de operaÃ§Ã£o',
        '19' => 'TÃ­tulo a cargo de Bancos Correspondentes com vencimento inferior a XX dias',
        '20' => 'Valor do tÃ­tulo invÃ¡lido (nÃ£o numÃ©rico)',
        '21' => 'EspÃ©cie do tÃ­tulo invÃ¡lida (arquivo de registro)',
        '22' => 'EspÃ©cie nÃ£o permetida para a carteira',
        '23' => 'Aceite invÃ¡lido - verifica conteÃºdo vÃ¡lido',
        '24' => 'Data de emissÃ£o invÃ¡lida - verifica se a data Ã© numÃ©rica e se estÃ¡ no formato vÃ¡lido',
        '25' => 'Data de emissÃ£o posterior a data de processamento',
        '26' => 'CÃ³digo de juros de mora invÃ¡lido',
        '27' => 'Valor/taxa de juros de mora invÃ¡lido',
        '28' => 'CÃ³digo do desconto invÃ¡lido',
        '29' => 'Valor do desconto maior ou igual ao valor do tÃ­tulo',
        '30' => 'Desconto a conceder nÃ£o confere',
        '32' => 'Valor do IOF invÃ¡lido',
        '33' => 'Valor do abatimento invÃ¡lido - para registro de tÃ­tulo verifica se o campo Ã© numÃ©rico e para concessÃ£o/cancelamento de abatimento',
        '34' => 'Valor do abatimento maior ou igual ao valor do tÃ­tulo',
        '35' => 'Abatimento a conceder nÃ£o confere',
        '36' => 'ConcessÃ£o de abatimento - jÃ¡ existe abatimento anterior',
        '37' => 'CÃ³digo para protesto invÃ¡lido - rejeita o tÃ­tulo se o campo for diferente de branco, 0, 1 ou 3',
        '38' => 'Prazo para protesto invÃ¡lido - se o cÃ³digo for 1 verifica se o campo Ã© numÃ©rico',
        '39' => 'Pedido de protesto nÃ£o permitido para o tÃ­tulo - nÃ£o permite protesto para as carteiras R, S e N',
        '40' => 'TÃ­tulo com ordem de protesto emitida (para retorno de alteraÃ§Ã£o)',
        '41' => 'Pedido de cancelamento/sustaÃ§Ã£o de protesto invÃ¡lido',
        '42' => 'CÃ³digo para baixa/devoluÃ§Ã£o ou instruÃ§Ã£o invÃ¡lido - verifica se o cÃ³digo Ã© branco, 0, 1 ou 2',
        '43' => 'Prazo para baixa/devoluÃ§Ã£o invÃ¡lido - se o cÃ³digo Ã© 1 verifica se o campo prazo Ã© numÃ©rico',
        '44' => 'CÃ³digo da moeda invÃ¡lido',
        '45' => 'Nome do Pagador invÃ¡lido ou alteraÃ§Ã£o do Pagador nÃ£o permitida',
        '46' => 'Tipo/nÃºmero de inscriÃ§Ã£o do Pagador invÃ¡lido',
        '47' => 'EndereÃ§o nÃ£o informado ou alteraÃ§Ã£o de endereÃ§o nÃ£o permitida',
        '48' => 'CEP invÃ¡lido ou alteraÃ§Ã£o de CEP nÃ£o permitida',
        '49' => 'CEP sem praÃ§a de cobranÃ§a ou alteraÃ§Ã£o de cidade nÃ£o permitida',
        '50' => 'CEP referente a um Banco Correspondente',
        '51' => 'CEP incompatÃ­vel com a unidade da federaÃ§Ã£o',
        '52' => 'Unidade de FederaÃ§Ã£o invÃ¡lida ou alteraÃ§Ã£o de UF nÃ£o permitida',
        '53' => 'Tipo/NÃºmero de inscriÃ§Ã£o do Sacador/Avalista invÃ¡lido',
        '54' => 'Sacador/Avalista nÃ£o informado - para espÃ©cie AD o nome do Sacador Ã© obrigatÃ³rio',
        '57' => 'CÃ³digo da multa invÃ¡lido',
        '58' => 'Data da multa invÃ¡lida',
        '59' => 'Valor/percentual da multa invÃ¡lido',
        '60' => 'Movimento para tÃ­tulo nÃ£o cadastrado - alteraÃ§Ã£o ou devoluÃ§Ã£o',
        '62' => 'Tipo de impressÃ£o invÃ¡lido - Segmento 3S',
        '63' => 'Entrada para tÃ­tulo jÃ¡ cadastrado',
        '79' => 'Data de juros de mora invÃ¡lido - valida data ou prazo na instruÃ§Ã£o de juros',
        '80' => 'Data do desconto invÃ¡lida - valida data ou prazo da instruÃ§Ã£o de desconto',
        '81' => 'CEP invÃ¡lido do Sacador',
        '83' => 'Tipo/NÃºmero de inscriÃ§Ã£o do Sacador invÃ¡lido',
        '84' => 'Sacador nÃ£o informado',
        '86' => 'Seu nÃºmero invÃ¡lido (para retorno de alteraÃ§Ã£o).',
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
                ->setOcorrenciaDescricao(array_get($this->ocorrencias, $this->detalheAtual()->getOcorrencia(), 'Desconhecida'))
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
                $ocorrencia = Util::appendStrings(
                    $d->getOcorrenciaDescricao(),
                    array_get($this->baixa_liquidacao, $msgAdicional[0], ''),
                    array_get($this->baixa_liquidacao, $msgAdicional[1], ''),
                    array_get($this->baixa_liquidacao, $msgAdicional[2], ''),
                    array_get($this->baixa_liquidacao, $msgAdicional[3], ''),
                    array_get($this->baixa_liquidacao, $msgAdicional[4], '')
                );
                $d->setOcorrenciaDescricao($ocorrencia);
                $d->setOcorrenciaTipo($d::OCORRENCIA_LIQUIDADA);
            } elseif ($d->hasOcorrencia('02')) {
                $this->totais['entradas']++;
                if(array_search('a4', array_map('strtolower', $msgAdicional)) !== false) {
                    $d->getPagador()->setDda(true);
                }
                $d->setOcorrenciaTipo($d::OCORRENCIA_ENTRADA);
            } elseif ($d->hasOcorrencia('09')) {
                $this->totais['baixados']++;
                $ocorrencia = Util::appendStrings(
                    $d->getOcorrenciaDescricao(),
                    array_get($this->baixa_liquidacao, $msgAdicional[0], ''),
                    array_get($this->baixa_liquidacao, $msgAdicional[1], ''),
                    array_get($this->baixa_liquidacao, $msgAdicional[2], ''),
                    array_get($this->baixa_liquidacao, $msgAdicional[3], ''),
                    array_get($this->baixa_liquidacao, $msgAdicional[4], '')
                );
                $d->setOcorrenciaDescricao($ocorrencia);
                $d->setOcorrenciaTipo($d::OCORRENCIA_BAIXADA);
            } elseif ($d->hasOcorrencia('25')) {
                $this->totais['protestados']++;
                $d->setOcorrenciaTipo($d::OCORRENCIA_PROTESTADA);
            } elseif ($d->hasOcorrencia('27', '14')) {
                $this->totais['alterados']++;
                $d->setOcorrenciaTipo($d::OCORRENCIA_ALTERACAO);
            } elseif ($d->hasOcorrencia('03', '26', '30')) {
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
        }

        if ($this->getSegmentType($detalhe) == 'U') {
            $d->setValorMulta(Util::nFloat($this->rem(18, 32, $detalhe)/100, 2, false))
                ->setValorDesconto(Util::nFloat($this->rem(33, 47, $detalhe)/100, 2, false))
                ->setValorAbatimento(Util::nFloat($this->rem(48, 62, $detalhe)/100, 2, false))
                ->setValorIOF(Util::nFloat($this->rem(63, 77, $detalhe)/100, 2, false))
                ->setValorRecebido(Util::nFloat($this->rem(78, 92, $detalhe)/100, 2, false))
                ->setValorTarifa($d->getValorRecebido() - Util::nFloat($this->rem(93, 107, $detalhe)/100, 2, false))
                ->setDataOcorrencia($this->rem(138, 145, $detalhe))
                ->setDataCredito($this->rem(146, 153, $detalhe));
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

