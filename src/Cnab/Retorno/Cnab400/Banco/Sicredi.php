<?php
namespace Alves\LaravelBoleto\Cnab\Retorno\Cnab400\Banco;

use Alves\LaravelBoleto\Cnab\Retorno\Cnab400\AbstractRetorno;
use Alves\LaravelBoleto\Contracts\Boleto\Boleto as BoletoContract;
use Alves\LaravelBoleto\Contracts\Cnab\RetornoCnab400;
use Alves\LaravelBoleto\Util;

class Sicredi extends AbstractRetorno implements RetornoCnab400
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
        '06' => 'LiquidaÃ§Ã£o normal',
        '09' => 'Baixado automaticamente via arquivo',
        '10' => 'Baixado conforme instruÃ§Ãµes da cooperativa de crÃ©dito',
        '12' => 'Abatimento concedido',
        '13' => 'Abatimento cancelado',
        '14' => 'Vencimento alterado',
        '15' => 'LiquidaÃ§Ã£o em cartÃ³rio',
        '17' => 'LiquidaÃ§Ã£o apÃ³s baixa',
        '19' => 'ConfirmaÃ§Ã£o de recebimento de instruÃ§Ã£o de protesto',
        '20' => 'ConfirmaÃ§Ã£o de recebimento de instruÃ§Ã£o de sustaÃ§Ã£o de protesto',
        '23' => 'Entrada de tÃ­tulo em cartÃ³rio',
        '24' => 'Entrada rejeitada por CEP irregular',
        '27' => 'Baixa rejeitada',
        '28' => 'Tarifa',
        '29' => 'RejeiÃ§Ã£o do pagador',
        '30' => 'AlteraÃ§Ã£o rejeitada',
        '32' => 'InstruÃ§Ã£o rejeitada',
        '33' => 'ConfirmaÃ§Ã£o de pedido de alteraÃ§Ã£o de outros dados',
        '34' => 'Retirado de cartÃ³rio e manutenÃ§Ã£o em carteira',
        '35' => 'Aceite do pagador',
    ];

    /**
     * Array com as possiveis rejeicoes do banco.
     *
     * @var array
     */
    private $rejeicoes = [
        '0A' => 'Aceito',
        '0D' => 'Desprezado',
        '01' => 'CÃ³digo do banco invÃ¡lido',
        '02' => 'CÃ³digo do registro detalhe invÃ¡lido',
        '03' => 'CÃ³digo da ocorrÃªncia invÃ¡lido',
        '04' => 'CÃ³digo de ocorrÃªncia nÃ£o permitida para a carteira',
        '05' => 'CÃ³digo de ocorrÃªncia nÃ£o numÃ©rico',
        '07' => 'Cooperativa/agÃªncia/conta/dÃ­gito invÃ¡lidos',
        '08' => 'Nosso nÃºmero invÃ¡lido',
        '09' => 'Nosso nÃºmero duplicado',
        '10' => 'Carteira invÃ¡lida',
        '15' => 'Cooperativa/carteira/agÃªncia/conta/nosso nÃºmero invÃ¡lidos',
        '16' => 'Data de vencimento invÃ¡lida',
        '17' => 'Data de vencimento anterior Ã  data de emissÃ£o',
        '18' => 'Vencimento fora do prazo de operaÃ§Ã£o',
        '20' => 'Valor do tÃ­tulo invÃ¡lido',
        '21' => 'EspÃ©cie do tÃ­tulo invÃ¡lida',
        '22' => 'EspÃ©cie nÃ£o permitida para a carteira',
        '24' => 'Data de emissÃ£o invÃ¡lida',
        '29' => 'Valor do desconto maior/igual ao valor do tÃ­tulo',
        '31' => 'ConcessÃ£o de desconto - existe desconto anterior',
        '33' => 'Valor do abatimento invÃ¡lido',
        '34' => 'Valor do abatimento maior/igual ao valor do tÃ­tulo',
        '36' => 'ConcessÃ£o de abatimento - existe abatimento anterior',
        '38' => 'Prazo para protesto invÃ¡lido',
        '39' => 'Pedido para protesto nÃ£o permitido para o tÃ­tulo',
        '40' => 'TÃ­tulo com ordem de protesto emitida',
        '41' => 'Pedido cancelamento/sustaÃ§Ã£o sem instruÃ§Ã£o de protesto',
        '44' => 'Cooperativa de crÃ©dito/agÃªncia beneficiÃ¡ria nÃ£o prevista',
        '45' => 'Nome do pagador invÃ¡lido',
        '46' => 'Tipo/nÃºmero de inscriÃ§Ã£o do pagador invÃ¡lidos',
        '47' => 'EndereÃ§o do pagador nÃ£o informado',
        '48' => 'CEP irregular',
        '49' => 'NÃºmero de InscriÃ§Ã£o do pagador/avalista invÃ¡lido',
        '50' => 'Pagador/avalista nÃ£o informado',
        '60' => 'Movimento para tÃ­tulo nÃ£o cadastrado',
        '63' => 'Entrada para tÃ­tulo jÃ¡ cadastrado',
        'A1' => 'PraÃ§a do pagador nÃ£o cadastrada.',
        'A2' => 'Tipo de cobranÃ§a do tÃ­tulo divergente com a praÃ§a do pagador.',
        'A3' => 'Cooperativa/agÃªncia depositÃ¡ria divergente: atualiza o cadastro de praÃ§as da Coop./agÃªncia beneficiÃ¡ria',
        'A4' => 'BeneficiÃ¡rio nÃ£o cadastrado ou possui CGC/CIC invÃ¡lido',
        'A5' => 'Pagador nÃ£o cadastrado',
        'A6' => 'Data da instruÃ§Ã£o/ocorrÃªncia invÃ¡lida',
        'A7' => 'OcorrÃªncia nÃ£o pode ser comandada',
        'B4' => 'Tipo de moeda invÃ¡lido',
        'B5' => 'Tipo de desconto/juros invÃ¡lido',
        'B6' => 'Mensagem padrÃ£o nÃ£o cadastrada',
        'B7' => 'Seu nÃºmero invÃ¡lido',
        'B8' => 'Percentual de multa invÃ¡lido',
        'B9' => 'Valor ou percentual de juros invÃ¡lido',
        'C1' => 'Data limite para concessÃ£o de desconto invÃ¡lida',
        'C2' => 'Aceite do tÃ­tulo invÃ¡lido',
        'C3' => 'Campo alterado na instruÃ§Ã£o â€œ31 â€“ alteraÃ§Ã£o de outros dadosâ€ invÃ¡lido',
        'C4' => 'TÃ­tulo ainda nÃ£o foi confirmado pela centralizadora',
        'C5' => 'TÃ­tulo rejeitado pela centralizadora',
        'C6' => 'TÃ­tulo jÃ¡ liquidado',
        'C7' => 'TÃ­tulo jÃ¡ baixado',
        'C8' => 'Existe mesma instruÃ§Ã£o pendente de confirmaÃ§Ã£o para este tÃ­tulo',
        'C9' => 'InstruÃ§Ã£o prÃ©via de concessÃ£o de abatimento nÃ£o existe ou nÃ£o confirmada',
        'D1' => 'TÃ­tulo dentro do prazo de vencimento (em dia)',
        'D2' => 'EspÃ©cie de documento nÃ£o permite protesto de tÃ­tulo',
        'D3' => 'TÃ­tulo possui instruÃ§Ã£o de baixa pendente de confirmaÃ§Ã£o',
        'D4' => 'Quantidade de mensagens padrÃ£o excede o limite permitido',
        'D5' => 'Quantidade invÃ¡lida no pedido de boletos prÃ©-impressos da cobranÃ§a sem registro',
        'D6' => 'Tipo de impressÃ£o invÃ¡lida para cobranÃ§a sem registro',
        'D7' => 'Cidade ou Estado do pagador nÃ£o informado',
        'D8' => 'SeqÃ¼Ãªncia para composiÃ§Ã£o do nosso nÃºmero do ano atual esgotada',
        'D9' => 'Registro mensagem para tÃ­tulo nÃ£o cadastrado',
        'E2' => 'Registro complementar ao cadastro do tÃ­tulo da cobranÃ§a com e sem registro nÃ£o cadastrado',
        'E3' => 'Tipo de postagem invÃ¡lido, diferente de S, N e branco',
        'E4' => 'Pedido de boletos prÃ©-impressos',
        'E5' => 'ConfirmaÃ§Ã£o/rejeiÃ§Ã£o para pedidos de boletos nÃ£o cadastrado',
        'E6' => 'Pagador/avalista nÃ£o cadastrado',
        'E7' => 'InformaÃ§Ã£o para atualizaÃ§Ã£o do valor do tÃ­tulo para protesto invÃ¡lido',
        'E8' => 'Tipo de impressÃ£o invÃ¡lido, diferente de A, B e branco',
        'E9' => 'CÃ³digo do pagador do tÃ­tulo divergente com o cÃ³digo da cooperativa de crÃ©dito',
        'F1' => 'Liquidado no sistema do cliente',
        'F2' => 'Baixado no sistema do cliente',
        'F3' => 'InstruÃ§Ã£o invÃ¡lida, este tÃ­tulo estÃ¡ caucionado/descontado',
        'F4' => 'InstruÃ§Ã£o fixa com caracteres invÃ¡lidos',
        'F6' => 'Nosso nÃºmero / nÃºmero da parcela fora de seqÃ¼Ãªncia â€“ total de parcelas invÃ¡lido',
        'F7' => 'Falta de comprovante de prestaÃ§Ã£o de serviÃ§o',
        'F8' => 'Nome do beneficiÃ¡rio incompleto / incorreto.',
        'F9' => 'CNPJ / CPF incompatÃ­vel com o nome do pagador / Sacador Avalista',
        'G1' => 'CNPJ / CPF do pagador IncompatÃ­vel com a espÃ©cie',
        'G2' => 'TÃ­tulo aceito: sem a assinatura do pagador',
        'G3' => 'TÃ­tulo aceito: rasurado ou rasgado',
        'G4' => 'TÃ­tulo aceito: falta tÃ­tulo (cooperativa/ag. beneficiÃ¡ria deverÃ¡ enviÃ¡-lo)',
        'G5' => 'PraÃ§a de pagamento incompatÃ­vel com o endereÃ§o',
        'G6' => 'TÃ­tulo aceito: sem endosso ou beneficiÃ¡rio irregular',
        'G7' => 'TÃ­tulo aceito: valor por extenso diferente do valor numÃ©rico',
        'G8' => 'Saldo maior que o valor do tÃ­tulo',
        'G9' => 'Tipo de endosso invÃ¡lido',
        'H1' => 'Nome do pagador incompleto / Incorreto',
        'H2' => 'SustaÃ§Ã£o judicial',
        'H3' => 'Pagador nÃ£o encontrado',
        'H4' => 'AlteraÃ§Ã£o de carteira',
        'H7' => 'EspÃ©cie de documento necessita beneficiÃ¡rio ou avalista PJ',
        'H9' => 'Dados do tÃ­tulo nÃ£o conferem com disquete',
        'I1' => 'Pagador e Sacador Avalista sÃ£o a mesma pessoa',
        'I2' => 'Aguardar um dia Ãºtil apÃ³s o vencimento para protestar',
        'I3' => 'Data do vencimento rasurada',
        'I4' => 'Vencimento â€“ extenso nÃ£o confere com nÃºmero',
        'I5' => 'Falta data de vencimento no tÃ­tulo',
        'I6' => 'DM/DMI sem comprovante autenticado ou declaraÃ§Ã£o',
        'I7' => 'Comprovante ilegÃ­vel para conferÃªncia e microfilmagem',
        'I8' => 'Nome solicitado nÃ£o confere com emitente ou pagador',
        'I9' => 'Confirmar se sÃ£o 2 emitentes. Se sim, indicar os dados dos 2',
        'J1' => 'EndereÃ§o do pagador igual ao do pagador ou do portador',
        'J2' => 'EndereÃ§o do apresentante incompleto ou nÃ£o informado',
        'J3' => 'Rua/nÃºmero inexistente no endereÃ§o',
        'J4' => 'Falta endosso do favorecido para o apresentante',
        'J5' => 'Data da emissÃ£o rasurada',
        'J6' => 'Falta assinatura do pagador no tÃ­tulo',
        'J7' => 'Nome do apresentante nÃ£o informado/incompleto/incorreto',
        'J8' => 'Erro de preenchimento do titulo',
        'J9' => 'Titulo com direito de regresso vencido',
        'K1' => 'Titulo apresentado em duplicidade',
        'K2' => 'Titulo jÃ¡ protestado',
        'K3' => 'Letra de cambio vencida â€“ falta aceite do pagador',
        'K4' => 'Falta declaraÃ§Ã£o de saldo assinada no tÃ­tulo',
        'K5' => 'Contrato de cambio â€“ Falta conta grÃ¡fica',
        'K6' => 'AusÃªncia do documento fÃ­sico',
        'K7' => 'Pagador falecido',
        'K8' => 'Pagador apresentou quitaÃ§Ã£o do tÃ­tulo',
        'K9' => 'TÃ­tulo de outra jurisdiÃ§Ã£o territorial',
        'L1' => 'TÃ­tulo com emissÃ£o anterior a concordata do pagador',
        'L2' => 'Pagador consta na lista de falÃªncia',
        'L3' => 'Apresentante nÃ£o aceita publicaÃ§Ã£o de edital',
        'L4' => 'Dados do Pagador em Branco ou invÃ¡lido',
        'L5' => 'CÃ³digo do Pagador na agÃªncia beneficiÃ¡ria estÃ¡ duplicado',
        'M2' => 'NÃ£o reconhecimento da dÃ­vida pelo pagador',
    ];

    /**
     * Roda antes dos metodos de processar
     */
    protected function init()
    {
        $this->totais = [
            'valor_recebido' => 0,
            'liquidados' => 0,
            'entradas' => 0,
            'baixados' => 0,
            'protestados' => 0,
            'erros' => 0,
            'alterados' => 0,
        ];
    }

    protected function processarHeader(array $header)
    {
        $this->getHeader()
            ->setOperacaoCodigo($this->rem(2, 2, $header))
            ->setOperacao($this->rem(3, 9, $header))
            ->setServicoCodigo($this->rem(10, 11, $header))
            ->setServico($this->rem(12, 26, $header))
            ->setConta($this->rem(27, 31, $header))
            ->setCodigoCliente($this->rem(32, 45, $header))
            ->setData($this->rem(95, 102, $header), 'Ymd');

        return true;
    }

    protected function processarDetalhe(array $detalhe)
    {
        $d = $this->detalheAtual();
		
        $d->setNossoNumero($this->rem(48, 62, $detalhe))
            ->setNumeroControle($this->rem(117, 126, $detalhe))
            ->setNumeroDocumento($this->rem(117, 126, $detalhe))
            ->setOcorrencia($this->rem(109, 110, $detalhe))
            ->setOcorrenciaDescricao(array_get($this->ocorrencias, $d->getOcorrencia(), 'Desconhecida'))
            ->setDataOcorrencia($this->rem(111, 116, $detalhe))
            ->setDataVencimento($this->rem(147, 152, $detalhe))
            ->setValor(Util::nFloat($this->rem(153, 165, $detalhe), 2, false) / 100)
            ->setValorTarifa(Util::nFloat($this->rem(176, 188, $detalhe), 2, false) / 100)
            ->setValorAbatimento(Util::nFloat($this->rem(228, 240, $detalhe), 2, false) / 100)
            ->setValorDesconto(Util::nFloat($this->rem(241, 253, $detalhe), 2, false) / 100)
            ->setValorRecebido(Util::nFloat($this->rem(254, 266, $detalhe), 2, false) / 100)
            ->setValorMora(Util::nFloat($this->rem(267, 279, $detalhe), 2, false) / 100)
            ->setValorMulta(Util::nFloat($this->rem(280, 292, $detalhe), 2, false) / 100)
            ->setDataCredito($this->rem(329, 336, $detalhe), 'Ymd');

        if ($d->hasOcorrencia('06', '15', '16')) {
			$this->totais['valor_recebido'] += $d->getValorRecebido();
            $this->totais['liquidados']++;
            $d->setOcorrenciaTipo($d::OCORRENCIA_LIQUIDADA);
        } elseif ($d->hasOcorrencia('02')) {
            $this->totais['entradas']++;
            $d->setOcorrenciaTipo($d::OCORRENCIA_ENTRADA);
        } elseif ($d->hasOcorrencia('09', '10')) {
            $this->totais['baixados']++;
            $d->setOcorrenciaTipo($d::OCORRENCIA_BAIXADA);
        } elseif ($d->hasOcorrencia('23')) {
            $this->totais['protestados']++;
            $d->setOcorrenciaTipo($d::OCORRENCIA_PROTESTADA);
        } elseif ($d->hasOcorrencia('33')) {
            $this->totais['alterados']++;
            $d->setOcorrenciaTipo($d::OCORRENCIA_ALTERACAO);
        } elseif ($d->hasOcorrencia('03', '27', '30')) {
            $this->totais['erros']++;
	    if($d->hasOcorrencia('03')) {
               if(isset($this->rejeicoes[$this->rem(319, 320, $detalhe)])){
                  $d->setRejeicao($this->rejeicoes[$this->rem(319, 320, $detalhe)]);
               }
            }
        } else {
            $d->setOcorrenciaTipo($d::OCORRENCIA_OUTROS);
        }

        $stringErrors = sprintf('%010s', $this->rem(319, 328, $detalhe));
        $errorsRetorno = str_split($stringErrors, 2) + array_fill(0, 5, '') + array_fill(0, 5, '');
        if (trim($stringErrors, '0') != '') {
            $error = [];
            $error[] = array_get($this->rejeicoes, $errorsRetorno[0], '');
            $error[] = array_get($this->rejeicoes, $errorsRetorno[1], '');
            $error[] = array_get($this->rejeicoes, $errorsRetorno[2], '');
            $error[] = array_get($this->rejeicoes, $errorsRetorno[3], '');
            $error[] = array_get($this->rejeicoes, $errorsRetorno[4], '');

            $error = array_filter($error);

            if (count($error) > 0){
                $d->setError(implode(PHP_EOL, $error));
            }
        }

        return true;
    }

    protected function processarTrailer(array $trailer)
    {
        $this->getTrailer()
            ->setQuantidadeTitulos((int) $this->count())
            ->setValorTitulos((float) Util::nFloat($this->totais['valor_recebido'], 2, false))
            ->setQuantidadeErros((int) $this->totais['erros'])
            ->setQuantidadeEntradas((int) $this->totais['entradas'])
            ->setQuantidadeLiquidados((int) $this->totais['liquidados'])
            ->setQuantidadeBaixados((int) $this->totais['baixados'])
            ->setQuantidadeAlterados((int) $this->totais['alterados']);

        return true;
    }
}

