<?php
namespace Alves\LaravelBoleto\Cnab\Retorno\Cnab400\Banco;

use Alves\LaravelBoleto\Util;
use Alves\LaravelBoleto\Contracts\Cnab\RetornoCnab400;
use Alves\LaravelBoleto\Cnab\Retorno\Cnab400\AbstractRetorno;
use Alves\LaravelBoleto\Contracts\Boleto\Boleto as BoletoContract;

class Safra extends AbstractRetorno implements RetornoCnab400
{
    /**
     * CÃ³digo do banco
     *
     * @var string
     */
    protected $codigoBanco = BoletoContract::COD_BANCO_SAFRA;

    /**
     * Array com as ocorrencias do banco;
     *
     * @var array
     */
    private $ocorrencias = [
        '02' => 'Entrada confirmada',
        '03' => 'Entrada rejeitada',
        '04' => 'AlteraÃ§Ã£o de dados - nova entrada',
        '05' => 'AlteraÃ§Ã£o de dados â€“ baixa',
        '06' => 'LiquidaÃ§Ã£o normal',
        '07' => 'LiquidaÃ§Ã£o parcial â€“ cobranÃ§a inteligente (b2b)',
        '08' => 'LiquidaÃ§Ã£o em cartÃ³rio',
        '09' => 'Baixa simples',
        '10' => 'Baixa por ter sido liquidado',
        '11' => 'Em ser (sÃ³ no retorno mensal)',
        '12' => 'Abatimento concedido',
        '13' => 'Abatimento cancelado',
        '14' => 'Vencimento alterado',
        '15' => 'Baixas rejeitadas',
        '16' => 'InstruÃ§Ãµes rejeitadas',
        '17' => 'AlteraÃ§Ã£o de dados rejeitados',
        '18' => 'CobranÃ§a contratual - instruÃ§Ãµes/alteraÃ§Ãµes rejeitadas/pendentes',
        '19' => 'Confirma recebimento de instruÃ§Ã£o de protesto',
        '20' => 'Confirma recebimento de instruÃ§Ã£o de sustaÃ§Ã£o de protesto /tarifa',
        '21' => 'Confirma recebimento de instruÃ§Ã£o de nÃ£o protestar',
        '23' => 'TÃ­tulo enviado a cartÃ³rio/tarifa',
        '24' => 'InstrucÌ§aÌƒo de protesto rejeitada / sustada / pendente',
        '25' => 'AlegacÌ§oÌƒes do pagador',
        '26' => 'Tarifa de aviso de cobrancÌ§a',
        '27' => 'Tarifa de extrato posicÌ§aÌƒo (B40X)',
        '28' => 'Tarifa de relacÌ§aÌƒo das liquidacÌ§oÌƒes',
        '29' => 'Tarifa de manutencÌ§aÌƒo de tiÌtulos vencidos',
        '30' => 'DÃ©bito mensal de tarifas (para entradas e baixas)',
        '32' => 'Baixa por ter sido protestado',
        '33' => 'Custas de protesto',
        '34' => 'Custas de sustaÃ§Ã£o',
        '35' => 'Custas de cartÃ³rio distribuidor',
        '36' => 'Custas de edital',
        '37' => 'Tarifa de emissÃ£o de boleto/tarifa de envio de duplicata',
        '38' => 'Tarifa de instruÃ§Ã£o',
        '39' => 'Tarifa de ocorrÃªncias',
        '40' => 'Tarifa mensal de emissÃ£o de boleto/tarifa mensal de envio de duplicata',
        '41' => 'DÃ©bito mensal de tarifas â€“ extrato de posiÃ§Ã£o (b4ep/b4ox)',
        '42' => 'DÃ©bito mensal de tarifas â€“ outras instruÃ§Ãµes',
        '43' => 'DÃ©bito mensal de tarifas â€“ manutenÃ§Ã£o de tÃ­tulos vencidos',
        '44' => 'DÃ©bito mensal de tarifas â€“ outras ocorrÃªncias',
        '45' => 'DÃ©bito mensal de tarifas â€“ protesto',
        '46' => 'DÃ©bito mensal de tarifas â€“ sustaÃ§Ã£o de protesto',
        '47' => 'Baixa com transferÃªncia para desconto',
        '48' => 'Custas de sustaÃ§Ã£o judicial',
        '51' => 'Tarifa mensal ref a entradas bancos correspondentes na carteira',
        '52' => 'Tarifa mensal baixas na carteira',
        '53' => 'Tarifa mensal baixas em bancos correspondentes na carteira',
        '54' => 'Tarifa mensal de liquidaÃ§Ãµes na carteira',
        '55' => 'Tarifa mensal de liquidaÃ§Ãµes em bancos correspondentes na carteira',
        '56' => 'Custas de irregularidade',
        '57' => 'InstruÃ§Ã£o cancelada',
        '59' => 'Baixa por crÃ©dito em c/c atravÃ©s do sispag',
        '60' => 'Entrada rejeitada carnÃª',
        '61' => 'Tarifa emissÃ£o aviso de movimentaÃ§Ã£o de tÃ­tulos (2154)',
        '62' => 'DÃ©bito mensal de tarifa - aviso de movimentaÃ§Ã£o de tÃ­tulos (2154)',
        '63' => 'TÃ­tulo sustado judicialmente',
        '64' => 'Entrada confirmada com rateio de crÃ©dito',
        '65' => 'Pagamento com cheque â€“ aguardando compensacÌ§aÌƒo',
        '69' => 'Cheque devolvido',
        '71' => 'Entrada registrada, aguardando avaliaÃ§Ã£o',
        '72' => 'Baixa por crÃ©dito em c/c atravÃ©s do sispag sem tÃ­tulo correspondente',
        '73' => 'ConfirmaÃ§Ã£o de entrada na cobranÃ§a simples â€“ entrada nÃ£o aceita na cobranÃ§a contratual',
        '74' => 'InstrucÌ§aÌƒo de negativacÌ§aÌƒo expressa rejeitada',
        '75' => 'ConfirmacÌ§aÌƒo de recebimento de instrucÌ§aÌƒo de entrada em negativacÌ§aÌƒo expressa',
        '76' => 'Cheque compensado',
        '77' => 'ConfirmacÌ§aÌƒo de recebimento de instrucÌ§aÌƒo de exclusaÌƒo de entrada em negativacÌ§aÌƒo expressa',
        '78' => 'ConfirmacÌ§aÌƒo de recebimento de instrucÌ§aÌƒo de cancelamento de negativacÌ§aÌƒo expressa',
        '79' => 'NegativacÌ§aÌƒo expressa informacional',
        '80' => 'ConfirmacÌ§aÌƒo de entrada em negativacÌ§aÌƒo expressa â€“ tarifa',
        '82' => 'ConfirmacÌ§aÌƒo do cancelamento de negativacÌ§aÌƒo expressa â€“ tarifa',
        '83' => 'ConfirmacÌ§aÌƒo de exclusaÌƒo de entrada em negativacÌ§aÌƒo expressa por liquidacÌ§aÌƒo â€“ tarifa',
        '85' => 'Tarifa por boleto (ateÌ 03 envios) cobrancÌ§a ativa eletroÌ‚nica',
        '86' => 'Tarifa email cobrancÌ§a ativa eletroÌ‚nica',
        '87' => 'Tarifa SMS cobrancÌ§a ativa eletroÌ‚nica',
        '88' => 'Tarifa mensal por boleto (ateÌ 03 envios) cobrancÌ§a ativa eletroÌ‚nica',
        '89' => 'Tarifa mensal email cobrancÌ§a ativa eletroÌ‚nica',
        '90' => 'Tarifa mensal SMS cobrancÌ§a ativa eletroÌ‚nica',
        '91' => 'Tarifa mensal de exclusaÌƒo de entrada de negativacÌ§aÌƒo expressa',
        '92' => 'Tarifa mensal de cancelamento de negativacÌ§aÌƒo expressa',
        '93' => 'Tarifa mensal de exclusaÌƒo de negativacÌ§aÌƒo expressa por liquidacÌ§aÌƒo',
    ];

    /**
     * Array com as possiveis rejeicoes do banco.
     *
     * @var array
     */
    private $rejeicoes = [
        '03' => 'Ag. Cobradora - Cep sem atendimento de protesto no momento',
        '04' => 'Sigla do estado invÃ¡lida',
        '05' => 'Data Vencimento - Prazo da operaÃ§Ã£o menor que prazo mÃ­nimo ou maior que o mÃ¡ximo',
        '07' => 'Valor do tÃ­tulo maior que 10.000.000,00',
        '08' => 'Nome do Pagador - NÃ£o informado ou deslocado',
        '09' => 'AgÃªncia/Conta - AgÃªncia encerrada',
        '10' => 'Logradouro - NÃ£o informado ou deslocado',
        '11' => 'Cep nÃ£o numÃ©rico ou cep invÃ¡lido',
        '12' => 'Sacador/Avalista - Nome nÃ£o informado ou deslocado (bancos correspondentes)',
        '13' => 'Estado/Cep - Cep incompatÃ­vel com a sigla do estado',
        '14' => 'Nosso nÃºmero jÃ¡ registrado no cadastro do banco ou fora da faixa',
        '15' => 'Nosso nÃºmero em duplicidade no mesmo movimento',
        '18' => 'Data de entrada invÃ¡lida para operar com esta carteira',
        '19' => 'OcorrÃªncia invÃ¡lida',
        '21' => 'Ag. Cobradora - Carteira nÃ£o aceita depositÃ¡ria correspondente estado da agÃªncia diferente do estado do pagador ag. cobradora nÃ£o consta no cadastro ou encerrando',
        '22' => 'Carteira - NÃ£o permitida (necessÃ¡rio cadastrar faixa livre)',
        '26' => 'AgÃªncia/Conta nÃ£o liberada para operar com cobranÃ§a',
        '27' => 'Cnpj do beneficiÃ¡rio inapto devoluÃ§Ã£o de tÃ­tulo em garantia',
        '29' => 'CÃ³digo empresa - Categoria da conta invÃ¡lida',
        '30' => 'Entradas bloqueadas, conta suspensa em cobranÃ§a',
        '31' => 'AgÃªncia/Conta - Conta nÃ£o tem permissÃ£o para protestar (contate seu gerente)',
        '35' => 'Iof maior que 5%',
        '36' => 'Quantidade de moeda incompatÃ­vel com valor do tÃ­tulo',
        '37' => 'Cnpj/Cpf do pagador nÃ£o numÃ©rico ou igual a zeros',
        '42' => 'Nosso nÃºmero fora de faixa',
        '52' => 'Ag. cobradora empresa nÃ£o aceita banco correspondente',
        '53' => 'Ag. cobradora empresa nÃ£o aceita banco correspondente - cobranÃ§a mensagem',
        '54' => 'Data de vencimento banco correspondente - tÃ­tulo com vencimento inferior a 15 dias',
        '55' => 'Cep nÃ£o pertence Ã  depositÃ¡ria informada',
        '56' => 'Data Vencimento superior a 180 dias da data de entrada',
        '57' => 'Cep sÃ³ depositÃ¡ria bco do brasil com vencimento inferior a 8 dias',
        '60' => 'Valor do abatimento invÃ¡lido',
        '61' => 'Juros de mora maior que o permitido',
        '62' => 'Valor do desconto maior que valor do tÃ­tulo',
        '63' => 'Valor da importÃ¢ncia por dia de desconto (idd) nÃ£o permitido',
        '64' => 'Data de emissÃ£o do tÃ­tulo invÃ¡lida',
        '65' => 'Taxa financiamento invÃ¡lida (vendor)',
        '66' => 'Data de vencimento invalida/fora de prazo de operaÃ§Ã£o (mÃ­nimo ou mÃ¡ximo)',
        '67' => 'Valor do tÃ­tulo/quantidade de moeda invÃ¡lido',
        '68' => 'Carteira invÃ¡lida ou nÃ£o cadastrada no intercÃ¢mbio da cobranÃ§a',
        '69' => 'Carteira invÃ¡lida para tÃ­tulos com rateio de crÃ©dito',
        '70' => 'AgÃªncia/Conta beneficiÃ¡rio nÃ£o cadastrado para fazer rateio de crÃ©dito',
        '78' => 'AgÃªncia/Conta duplicidade de agÃªncia/conta beneficiÃ¡ria do rateio de crÃ©dito',
        '80' => 'AgÃªncia/Conta quantidade de contas beneficiÃ¡rias do rateio maior do que o permitido (mÃ¡ximo de 30 contas por tÃ­tulo)',
        '81' => 'AgÃªncia/Conta para rateio de crÃ©dito invÃ¡lida / nÃ£o pertence ao itaÃº',
        '82' => 'Desconto/Abatimento nÃ£o permitido para tÃ­tulos com rateio de crÃ©dito',
        '83' => 'Valor do tÃ­tulo menor que a soma dos valores estipulados para rateio',
        '84' => 'AgÃªncia/Conta beneficiÃ¡ria do rateio Ã© a centralizadora de crÃ©dito do beneficiÃ¡rio',
        '85' => 'AgÃªncia/Conta do beneficiÃ¡rio Ã© contratual / rateio de crÃ©dito nÃ£o permitido',
        '86' => 'CÃ³digo do tipo de valor invÃ¡lido / nÃ£o previsto para tÃ­tulos com rateio de crÃ©dito',
        '87' => 'AgÃªncia/Conta registro tipo 4 sem informaÃ§Ã£o de agÃªncias/contas beneficiÃ¡rias do rateio',
        '90' => 'NÃºmero da linha cobranÃ§a mensagem - nÃºmero da linha da mensagem invÃ¡lido ou quantidade de linhas excedidas',
        '97' => 'Sem mensagem (sÃ³ de campos fixos), porÃ©m com registro do tipo 7 ou 8',
        '98' => 'Registro mensagem sem flash cadastrado ou flash informado diferente do cadastrado',
        '99' => 'Conta de cobranÃ§a com flash cadastrado e sem registro de mensagem correspondente',
    ];

    /**
     * Roda antes dos metodos de processar
     */
    protected function init()
    {
        $this->totais = [
            'liquidados'  => 0,
            'entradas'    => 0,
            'baixados'    => 0,
            'protestados' => 0,
            'erros'       => 0,
            'alterados'   => 0,
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
            ->setServico($this->rem(12, 26, $header))
            ->setAgencia($this->rem(27, 30, $header))
            ->setAgenciaDv($this->rem(31, 31, $header))
            ->setConta($this->rem(32, 39, $header))
            ->setContaDv($this->rem(40, 40, $header))
            ->setCodigoCliente($this->rem(27, 40, $header))
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

        $d->setCarteira($this->rem(83, 85, $detalhe))
            ->setNossoNumero($this->rem(63, 71, $detalhe))
            ->setNumeroDocumento($this->rem(117, 126, $detalhe))
            ->setNumeroControle($this->rem(38, 62, $detalhe))
            ->setOcorrencia($this->rem(109, 110, $detalhe))
            ->setOcorrenciaDescricao(array_get($this->ocorrencias, $d->getOcorrencia(), 'Desconhecida'))
            ->setDataOcorrencia($this->rem(111, 116, $detalhe))
            ->setDataVencimento($this->rem(147, 152, $detalhe))
            ->setDataCredito($this->rem(296, 301, $detalhe))
            ->setCodigoLiquidacao($this->rem(393, 394, $detalhe))
            ->setValor(Util::nFloat($this->rem(153, 165, $detalhe) / 100, 2, false))
            ->setValorTarifa(Util::nFloat($this->rem(176, 188, $detalhe) / 100, 2, false))
            ->setValorIOF(Util::nFloat($this->rem(215, 227, $detalhe) / 100, 2, false))
            ->setValorAbatimento(Util::nFloat($this->rem(228, 240, $detalhe) / 100, 2, false))
            ->setValorDesconto(Util::nFloat($this->rem(241, 253, $detalhe) / 100, 2, false))
            ->setValorRecebido(Util::nFloat($this->rem(254, 266, $detalhe) / 100, 2, false))
            ->setValorMora(Util::nFloat($this->rem(267, 279, $detalhe) / 100, 2, false))
            ->setValorMulta(Util::nFloat($this->rem(280, 292, $detalhe) / 100, 2, false));

        $msgAdicional = str_split(sprintf('%08s', $this->rem(378, 385, $detalhe)), 2) + array_fill(0, 4, '');
        if ($d->hasOcorrencia('06', '07', '08', '10', '59')) {
            $this->totais['liquidados']++;
            $d->setOcorrenciaTipo($d::OCORRENCIA_LIQUIDADA);
        } elseif ($d->hasOcorrencia('02', '64', '71', '73')) {
            $this->totais['entradas']++;
            $d->setOcorrenciaTipo($d::OCORRENCIA_ENTRADA);
        } elseif ($d->hasOcorrencia('05', '09', '47', '72')) {
            $this->totais['baixados']++;
            $d->setOcorrenciaTipo($d::OCORRENCIA_BAIXADA);
        } elseif ($d->hasOcorrencia('32')) {
            $this->totais['protestados']++;
            $d->setOcorrenciaTipo($d::OCORRENCIA_PROTESTADA);
        } elseif ($d->hasOcorrencia('14')) {
            $this->totais['alterados']++;
            $d->setOcorrenciaTipo($d::OCORRENCIA_ALTERACAO);
        } elseif ($d->hasOcorrencia('03', '15', '16', '17', '18', '60')) {
            $this->totais['erros']++;
            $error = Util::appendStrings(
                array_get($this->rejeicoes, $msgAdicional[0], ''),
                array_get($this->rejeicoes, $msgAdicional[1], ''),
                array_get($this->rejeicoes, $msgAdicional[2], ''),
                array_get($this->rejeicoes, $msgAdicional[3], '')
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
            ->setQuantidadeTitulos((int) $this->rem(18, 25, $trailer) + (int) $this->rem(58, 65, $trailer) + (int) $this->rem(178, 185, $trailer))
            ->setValorTitulos((float) Util::nFloat($this->rem(221, 234, $trailer) / 100, 2, false))
            ->setQuantidadeErros((int) $this->totais['erros'])
            ->setQuantidadeEntradas((int) $this->totais['entradas'])
            ->setQuantidadeLiquidados((int) $this->totais['liquidados'])
            ->setQuantidadeBaixados((int) $this->totais['baixados'])
            ->setQuantidadeAlterados((int) $this->totais['alterados']);

        return true;
    }
}

