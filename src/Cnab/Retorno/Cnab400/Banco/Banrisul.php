<?php
namespace Alves\LaravelBoleto\Cnab\Retorno\Cnab400\Banco;

use Alves\LaravelBoleto\Cnab\Retorno\Cnab400\AbstractRetorno;
use Alves\LaravelBoleto\Contracts\Boleto\Boleto as BoletoContract;
use Alves\LaravelBoleto\Contracts\Cnab\RetornoCnab400;
use Alves\LaravelBoleto\Util;

class Banrisul extends AbstractRetorno implements RetornoCnab400
{

    /**
     * Array com as ocorrencias do banco;
     *
     * @var array
     */
    private $ocorrencias = [
        '02' => 'ConfirmaÃ§Ã£o de entrada',
        '03' => 'Entrada rejeitada',
        '04' => 'Baixa de tÃ­tulo liquidado por edital',
        '06' => 'LiquidaÃ§Ã£o normal',
        '07' => 'LiquidaÃ§Ã£o parcial',
        '08' => 'Baixa por pagamento, liquidaÃ§Ã£o pelo saldo',
        '09' => 'DevoluÃ§Ã£o automÃ¡tica',
        '10' => 'Baixado conforme instruÃ§Ãµes',
        '11' => 'Arquivo levantamento',
        '12' => 'ConcessÃ£o de abatimento',
        '13' => 'Cancelamento de abatimento',
        '14' => 'Vencimento alterado',
        '15' => 'Pagamento em cartÃ³rio',
        '16' => 'AlteraÃ§Ã£o de dados',
        '18' => 'AlteraÃ§Ã£o de instruÃ§Ãµes',
        '19' => 'ConfirmaÃ§Ã£o de instruÃ§Ã£o protesto',
        '20' => 'ConfirmaÃ§Ã£o de instruÃ§Ã£o para sustar protesto',
        '21' => 'Aguardando autorizaÃ§Ã£o para protesto por edital',
        '22' => 'Protesto sustado por alteraÃ§Ã£o de vencimento e prazo de cartÃ³rio',
        '23' => 'ConfirmaÃ§Ã£o da entrada em cartÃ³rio',
        '25' => 'DevoluÃ§Ã£o, liquidado anteriormente',
        '26' => 'Devolvido pelo cartÃ³rio â€“ erro de informaÃ§Ã£o.',
        '30' => 'cobranÃ§a a creditar (liquidaÃ§Ã£o em trÃ¢nsito)',
        '31' => 'TÃ­tulo em trÃ¢nsito pago em cartÃ³rio',
        '32' => 'Reembolso e transferÃªncia Desconto e Vendor ou carteira em garantia',
        '33' => 'Reembolso e devoluÃ§Ã£o Desconto e Vendor',
        '34' => 'Reembolso nÃ£o efetuado por falta de saldo',
        '40' => 'Baixa de tÃ­tulos protestados',
        '41' => 'Despesa de aponte.',
        '42' => 'AlteraÃ§Ã£o de tÃ­tulo',
        '43' => 'RelaÃ§Ã£o de tÃ­tulos',
        '44' => 'ManutenÃ§Ã£o mensal',
        '45' => 'SustaÃ§Ã£o de cartÃ³rio e envio de tÃ­tulo a cartÃ³rio',
        '46' => 'Fornecimento de formulÃ¡rio prÃ©-impresso',
        '47' => 'ConfirmaÃ§Ã£o de entrada â€“ Pagador DDA',
        '68' => 'Acerto dos dados do rateio de crÃ©dito',
        '69' => 'Cancelamento dos dados do rateio',
    ];


    /**
     * Array com as possiveis descricoes de baixa e liquidacao.
     *
     * @var array
     */
    private $baixa_liquidacao = [
        '37' => 'Cancelamento de rateio por motivo de baixa comandada',
        '38' => 'Rateio efetuado, BeneficiÃ¡rio aguardando crÃ©dito',
        '39' => 'Rateio efetuado, BeneficiÃ¡rio jÃ¡ creditado',
        '40' => 'Rateio nÃ£o efetuado, conta dÃ©bito BeneficiÃ¡rio principal bloqueada',
        '41' => 'Rateio nÃ£o efetuado, conta BeneficiÃ¡rio encerrada',
        '42' => 'Rateio nÃ£o efetuado, cÃ³digo cÃ¡lculo 2 (valor registro) e valor pago menor',
        '43' => 'OcorrÃªncia nÃ£o possui rateio',
    ];

    /**
     * Array com as possiveis rejeicoes do banco.
     *
     * @var array
     */
    private $rejeicoes = [
        '01' => 'CÃ³digo do Banco invÃ¡lido',
        '02' => 'AgÃªncia/Conta/NÃºmero de controle â€“ InvÃ¡lido CobranÃ§a Partilhada',
        '04' => 'CÃ³digo do movimento nÃ£o permitido para a carteira',
        '05' => 'CÃ³digo do movimento invÃ¡lido',
        '07' => 'TÃ­tulo rejeitado na cobranÃ§a CEP irregular',
        '08' => 'Nosso NÃºmero invÃ¡lido',
        '09' => 'Nosso NÃºmero duplicado',
        '10' => 'Carteira invÃ¡lida',
        '15' => 'CaracterÃ­sticas da cobranÃ§a incompatÃ­veis â€“ se a carteira e a moeda forem vÃ¡lidas e nÃ£o existir espÃ©cie',
        '16' => 'Data de vencimento invÃ¡lida',
        '17' => 'Data de vencimento anterior Ã  data de emissÃ£o',
        '18' => 'Vencimento fora do prazo de operaÃ§Ã£o',
        '20' => 'Valor do tÃ­tulo invÃ¡lido (nÃ£o numÃ©rico)',
        '21' => 'EspÃ©cie do tÃ­tulo invÃ¡lida (arquivo de registro)',
        '23' => 'Aceite invÃ¡lido â€“ verifica conteÃºdo vÃ¡lido',
        '24' => 'Data de emissÃ£o invÃ¡lida â€“ verifica se a data Ã© numÃ©rica e se estÃ¡ no formato vÃ¡lido',
        '25' => 'Data de emissÃ£o posterior Ã  data de processamento',
        '26' => 'CÃ³digo de juros de mora invÃ¡lido',
        '27' => 'Valor/taxa de juros de mora invÃ¡lido',
        '28' => 'CÃ³digo do desconto invÃ¡lido',
        '29' => 'Valor do desconto maior ou igual ao valor do tÃ­tulo',
        '30' => 'Desconto a conceder nÃ£o confere:',
        '32' => 'Valor de IOF invÃ¡lido:',
        '33' => 'Valor do abatimento invÃ¡lido â€“ para registro de tÃ­tulo verifica se o campo Ã© numÃ©rico e para concessÃ£o/cancelamento de abatimento indica o erro',
        '34' => 'Valor do abatimento maior ou igual ao valor do tÃ­tulo',
        '37' => 'CÃ³digo para protesto invÃ¡lido â€“ rejeita o tÃ­tulo se o campo for diferente de branco, 0, 1 ou 3',
        '38' => 'Prazo para protesto invÃ¡lido â€“ se o cÃ³digo for 1 verifica se o campo Ã© numÃ©rico',
        '39' => 'Pedido de protesto nÃ£o permitido para o tÃ­tulo â€“ nÃ£o permite protesto para as carteiras R, S, N e X',
        '40' => 'TÃ­tulo com ordem de protesto emitida (para retorno de alteraÃ§Ã£o)',
        '41' => 'Pedido de cancelamento/sustaÃ§Ã£o de protesto invÃ¡lido',
        '42' => 'CÃ³digo para baixa/devoluÃ§Ã£o ou instruÃ§Ã£o invÃ¡lido â€“ verifica se o cÃ³digo Ã© branco, 0, 1 ou 2',
        '43' => 'Prazo para baixa/devoluÃ§Ã£o invÃ¡lido â€“ se o cÃ³digo Ã© 1 verifica se o campo prazo Ã© numÃ©rico',
        '44' => 'CÃ³digo da moeda invÃ¡lido',
        '45' => 'Nome do Pagador invÃ¡lido ou alteraÃ§Ã£o do Pagador nÃ£o permitida',
        '46' => 'Tipo/nÃºmero de inscriÃ§Ã£o do Pagador invÃ¡lido',
        '47' => 'EndereÃ§o nÃ£o informado ou alteraÃ§Ã£o de endereÃ§o nÃ£o permitida',
        '48' => 'CEP invÃ¡lido ou alteraÃ§Ã£o de CEP nÃ£o permitida',
        '49' => 'CEP sem praÃ§a de cobranÃ§a ou alteraÃ§Ã£o de cidade nÃ£o permitida',
        '50' => 'CEP referente a um Banco Correspondente',
        '52' => 'Unidade de FederaÃ§Ã£o invÃ¡lida ou alteraÃ§Ã£o de UF nÃ£o permitida',
        '53' => 'Tipo/NÃºmero de inscriÃ§Ã£o do Sacador/Avalista invÃ¡lido',
        '54' => 'Sacador/Avalista nÃ£o informado â€“ para espÃ©cie AD o nome do Sacador Ã© obrigatÃ³rio',
        '57' => 'CÃ³digo da multa invÃ¡lido',
        '58' => 'Data da multa invÃ¡lida',
        '59' => 'Valor/percentual da multa invÃ¡lido',
        '60' => 'Movimento para tÃ­tulo nÃ£o cadastrado â€“ alteraÃ§Ã£o ou devoluÃ§Ã£o',
        '62' => 'Tipo de impressÃ£o invÃ¡lido â€“ Segmento 3S',
        '63' => 'Entrada para tÃ­tulo jÃ¡ cadastrado',
        '79' => 'Data de juros de mora invÃ¡lido â€“ valida data ou prazo na instruÃ§Ã£o de juros',
        '80' => 'Data do desconto invÃ¡lida â€“ valida data ou prazo da instruÃ§Ã£o de desconto',
        '81' => 'CEP invÃ¡lido do Sacador',
        '83' => 'Tipo/NÃºmero de inscriÃ§Ã£o do Sacador invÃ¡lido',
        '84' => 'Sacador nÃ£o informado',
        '86' => 'Seu nÃºmero invÃ¡lido (para retorno de alteraÃ§Ã£o)',
    ];

    /**
     * CÃ³digo do banco
     *
     * @var string
     */
    protected $codigoBanco = BoletoContract::COD_BANCO_BANRISUL;

    /**
     * Roda antes dos metodos de processar
     */
    protected function init()
    {
        $this->totais = [
            'liquidados' => 0,
            'erros' => 0,
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
            ->setConta($this->rem(31, 39, $header))
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
            ->setNossoNumero($this->rem(63, 72, $detalhe))
            ->setNumeroDocumento($this->rem(117, 126, $detalhe))
            ->setNumeroControle($this->rem(38, 62, $detalhe))
            ->setOcorrencia($this->rem(109, 110, $detalhe))
            ->setOcorrenciaDescricao(data_get($this->ocorrencias, $d->getOcorrencia(), 'Desconhecida'))
            ->setDataOcorrencia($this->rem(111, 116, $detalhe))
            ->setDataVencimento($this->rem(147, 152, $detalhe))
            ->setDataCredito($this->rem(296, 301, $detalhe))
            ->setValor(Util::nFloat($this->rem(153, 165, $detalhe)/100, 2, false))
            ->setValorTarifa(Util::nFloat($this->rem(176, 188, $detalhe)/100, 2, false))
            ->setValorDesconto(Util::nFloat($this->rem(241, 253, $detalhe)/100, 2, false))
            ->setValorRecebido(Util::nFloat($this->rem(254, 266, $detalhe)/100, 2, false))
            ->setValorMora(Util::nFloat($this->rem(267, 279, $detalhe)/100, 2, false))
            ->setValorMulta(Util::nFloat($this->rem(280, 292, $detalhe)/100, 2, false));

        /**
         * ocorrencias
         */
        $msgAdicional = str_split(sprintf('%010s', $this->rem(383, 392, $detalhe)), 2) + array_fill(0, 5, '');
        if ($d->hasOcorrencia('06', '25', '08')) {
            $this->totais['liquidados']++;
            $ocorrencia = Util::appendStrings(
                $d->getOcorrenciaDescricao(),
                data_get($this->baixa_liquidacao, $msgAdicional[0], ''),
                data_get($this->baixa_liquidacao, $msgAdicional[1], ''),
                data_get($this->baixa_liquidacao, $msgAdicional[2], ''),
                data_get($this->baixa_liquidacao, $msgAdicional[3], ''),
                data_get($this->baixa_liquidacao, $msgAdicional[4], '')
            );
            $d->setOcorrenciaDescricao($ocorrencia);
            $d->setOcorrenciaTipo($d::OCORRENCIA_LIQUIDADA);
        } elseif ($d->hasOcorrencia('02', '47')) {
            $this->totais['entradas']++;
            $d->setOcorrenciaTipo($d::OCORRENCIA_ENTRADA);
        } elseif ($d->hasOcorrencia('04', '08', '10')) {
            $this->totais['baixados']++;
            $ocorrencia = Util::appendStrings(
                $d->getOcorrenciaDescricao(),
                data_get($this->baixa_liquidacao, $msgAdicional[0], ''),
                data_get($this->baixa_liquidacao, $msgAdicional[1], ''),
                data_get($this->baixa_liquidacao, $msgAdicional[2], ''),
                data_get($this->baixa_liquidacao, $msgAdicional[3], ''),
                data_get($this->baixa_liquidacao, $msgAdicional[4], '')
            );
            $d->setOcorrenciaDescricao($ocorrencia);
            $d->setOcorrenciaTipo($d::OCORRENCIA_BAIXADA);
        } elseif ($d->hasOcorrencia('40')) {
            $this->totais['protestados']++;
            $d->setOcorrenciaTipo($d::OCORRENCIA_PROTESTADA);
        } elseif ($d->hasOcorrencia('14', '16', '18', '42')) {
            $this->totais['alterados']++;
            $d->setOcorrenciaTipo($d::OCORRENCIA_ALTERACAO);
        } elseif ($d->hasOcorrencia('03', '24')) {
            $this->totais['erros']++;
            $d->setError(data_get($this->rejeicoes, $this->rem(383, 392, $detalhe), 'Consulte seu Internet Banking'));
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
            ->setValorTitulos(Util::nFloat($this->rem(26, 39, $trailer)/100, 2, false))
            ->setQuantidadeTitulos((int) $this->rem(18, 25, $trailer))
            ->setQuantidadeErros((int) $this->totais['erros'])
            ->setQuantidadeEntradas((int)  $this->rem(49, 55, $trailer))
            ->setQuantidadeLiquidados((int)  $this->rem(71, 77, $trailer))
            ->setQuantidadeBaixados((int) $this->totais['baixados'])
            ->setQuantidadeAlterados((int) $this->totais['alterados']);

        return true;
    }
}

