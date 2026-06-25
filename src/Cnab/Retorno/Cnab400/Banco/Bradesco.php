<?php
namespace Alves\LaravelBoleto\Cnab\Retorno\Cnab400\Banco;

use Alves\LaravelBoleto\Cnab\Retorno\Cnab400\AbstractRetorno;
use Alves\LaravelBoleto\Contracts\Boleto\Boleto as BoletoContract;
use Alves\LaravelBoleto\Contracts\Cnab\RetornoCnab400;
use Alves\LaravelBoleto\Util;

class Bradesco extends AbstractRetorno implements RetornoCnab400
{
    /**
     * CÃƒÂ³digo do banco
     *
     * @var string
     */
    protected $codigoBanco = BoletoContract::COD_BANCO_BRADESCO;

    /**
     * Array com as ocorrencias do banco;
     *
     * @var array
     */
    private $ocorrencias = [
        "02" => "Entrada Confirmada",
        "03" => "Entrada Rejeitada",
        "06" => "LiquidaÃƒÂ§ÃƒÂ£o normal (sem motivo)",
        "09" => "Baixado Automat. via Arquivo",
        "10" => "Baixado conforme instruÃƒÂ§ÃƒÂµes da AgÃƒÂªncia",
        "11" => "Em Ser - Arquivo de TÃƒÂ­tulos pendentes (sem motivo)",
        "12" => "Abatimento Concedido (sem motivo)",
        "13" => "Abatimento Cancelado (sem motivo)",
        "14" => "Vencimento Alterado (sem motivo)",
        "15" => "LiquidaÃƒÂ§ÃƒÂ£o em CartÃƒÂ³rio (sem motivo)",
        "16" => "TÃƒÂ­tulo Pago em Cheque - Vinculado",
        "17" => "LiquidaÃƒÂ§ÃƒÂ£o apÃƒÂ³s baixa ou TÃƒÂ­tulo nÃƒÂ£o registrado (sem motivo)",
        "18" => "Acerto de DepositÃƒÂ¡ria (sem motivo)",
        "19" => "ConfirmaÃƒÂ§ÃƒÂ£o Receb. Inst. de Protesto",
        "20" => "ConfirmaÃƒÂ§ÃƒÂ£o Recebimento InstruÃƒÂ§ÃƒÂ£o SustaÃƒÂ§ÃƒÂ£o de Protesto (sem motivo)",
        "21" => "Acerto do Controle do Participante (sem motivo)",
        "22" => "TÃƒÂ­tulo Com Pagamento Cancelado",
        "23" => "Entrada do TÃƒÂ­tulo em CartÃƒÂ³rio (sem motivo)",
        "24" => "Entrada rejeitada por CEP Irregular",
        "27" => "Baixa Rejeitada",
        "28" => "DÃƒÂ©bito de tarifas/custas",
        "30" => "AlteraÃƒÂ§ÃƒÂ£o de Outros Dados Rejeitados",
        "32" => "InstruÃƒÂ§ÃƒÂ£o Rejeitada",
        "33" => "ConfirmaÃƒÂ§ÃƒÂ£o Pedido AlteraÃƒÂ§ÃƒÂ£o Outros Dados (sem motivo)",
        "34" => "Retirado de CartÃƒÂ³rio e ManutenÃƒÂ§ÃƒÂ£o Carteira (sem motivo)",
        "35" => "Desagendamento do dÃƒÂ©bito automÃƒÂ¡tico",
        "40" => "Estorno de pagamento (Novo)",
        "55" => "Sustado judicial (Novo)",
        "68" => "Acerto dos dados do rateio de CrÃƒÂ©dito",
        "69" => "Cancelamento dos dados do rateio",
    ];

    /**
     * Array com as possiveis rejeicoes do banco.
     *
     * @var array
     */
    private $rejeicoes = [
        '02' => 'CÃƒÂ³digo do registro detalhe invÃƒÂ¡lido',
        '03' => 'CÃƒÂ³digo da ocorrÃƒÂªncia invÃƒÂ¡lida',
        '04' => 'CÃƒÂ³digo de ocorrÃƒÂªncia nÃƒÂ£o permitida para a carteira',
        '05' => 'CÃƒÂ³digo de ocorrÃƒÂªncia nÃƒÂ£o numÃƒÂ©rico',
        '07' => 'AgÃƒÂªncia/conta/Digito - |InvÃƒÂ¡lido',
        '08' => 'Nosso nÃƒÂºmero invÃƒÂ¡lido',
        '09' => 'Nosso nÃƒÂºmero duplicado',
        '10' => 'Carteira invÃƒÂ¡lida',
        '13' => 'IdentificaÃƒÂ§ÃƒÂ£o da emissÃƒÂ£o do bloqueto invÃƒÂ¡lida',
        '16' => 'Data de vencimento invÃƒÂ¡lida',
        '18' => 'Vencimento fora do prazo de operaÃƒÂ§ÃƒÂ£o',
        '20' => 'Valor do TÃƒÂ­tulo invÃƒÂ¡lido',
        '21' => 'EspÃƒÂ©cie do TÃƒÂ­tulo invÃƒÂ¡lida',
        '22' => 'EspÃƒÂ©cie nÃƒÂ£o permitida para a carteira',
        '24' => 'Data de emissÃƒÂ£o invÃƒÂ¡lida',
        '28' => 'CÃƒÂ³digo do desconto invÃƒÂ¡lido',
        '38' => 'Prazo para protesto/ NegativaÃƒÂ§ÃƒÂ£o invÃƒÂ¡lido (ALTERADO)',
        '44' => 'AgÃƒÂªncia BeneficiÃƒÂ¡rio nÃƒÂ£o prevista',
        '45' => 'Nome do pagador nÃƒÂ£o informado',
        '46' => 'Tipo/nÃƒÂºmero de inscriÃƒÂ§ÃƒÂ£o do pagador invÃƒÂ¡lidos',
        '47' => 'EndereÃƒÂ§o do pagador nÃƒÂ£o informado',
        '48' => 'CEP InvÃƒÂ¡lido',
        '50' => 'CEP irregular - Banco Correspondente',
        '63' => 'Entrada para TÃƒÂ­tulo jÃƒÂ¡ cadastrado',
        '65' => 'Limite excedido',
        '66' => 'NÃƒÂºmero autorizaÃƒÂ§ÃƒÂ£o inexistente',
        '68' => 'DÃƒÂ©bito nÃƒÂ£o agendado - erro nos dados de remessa',
        '69' => 'DÃƒÂ©bito nÃƒÂ£o agendado - Pagador nÃƒÂ£o consta no cadastro de autorizante',
        '70' => 'DÃƒÂ©bito nÃƒÂ£o agendado - BeneficiÃƒÂ¡rio nÃƒÂ£o autorizado pelo Pagador',
        '71' => 'DÃƒÂ©bito nÃƒÂ£o agendado - BeneficiÃƒÂ¡rio nÃƒÂ£o participa do dÃƒÂ©bito AutomÃƒÂ¡tico',
        '72' => 'DÃƒÂ©bito nÃƒÂ£o agendado - CÃƒÂ³digo de moeda diferente de R$',
        '73' => 'DÃƒÂ©bito nÃƒÂ£o agendado - Data de vencimento invÃƒÂ¡lida',
        '74' => 'DÃƒÂ©bito nÃƒÂ£o agendado - Conforme seu pedido, TÃƒÂ­tulo nÃƒÂ£o registrado',
        '75' => 'DÃƒÂ©bito nÃƒÂ£o agendado - Tipo de nÃƒÂºmero de inscriÃƒÂ§ÃƒÂ£o do debitado invÃƒÂ¡lido',
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
    protected function processarHeader( $header)
    {
        $this->getHeader()
            ->setOperacaoCodigo($this->rem(2, 2, $header))
            ->setOperacao($this->rem(3, 9, $header))
            ->setServicoCodigo($this->rem(10, 11, $header))
            ->setServico($this->rem(12, 26, $header))
            ->setCodigoCliente($this->rem(27, 46, $header))
            ->setData($this->rem(95, 100, $header));

        return true;
    }

    /**
     * @param array $detalhe
     *
     * @return bool
     * @throws \Exception
     */
    protected function processarDetalhe( $detalhe)
    {
        if ($this->count() == 1) {
            $this->getHeader()
                ->setAgencia($this->rem(25, 29, $detalhe))
                ->setConta($this->rem(30, 36, $detalhe))
                ->setContaDv($this->rem(37, 37, $detalhe));
        }

        $d = $this->detalheAtual();
        $d->setCarteira($this->rem(108, 108, $detalhe))
            ->setNossoNumero($this->rem(71, 82, $detalhe))
            ->setNumeroDocumento($this->rem(117, 126, $detalhe))
            ->setNumeroControle($this->rem(38, 62, $detalhe))
            ->setOcorrencia($this->rem(109, 110, $detalhe))
            ->setOcorrenciaDescricao(data_get($this->ocorrencias, $d->getOcorrencia(), 'Desconhecida'))
            ->setDataOcorrencia($this->rem(111, 116, $detalhe))
            ->setDataVencimento($this->rem(147, 152, $detalhe))
            ->setDataCredito($this->rem(296, 301, $detalhe))
            ->setValor($this->formatCnabMoney($this->rem(153, 165, $detalhe)))
            ->setValorTarifa($this->formatCnabMoney($this->rem(176, 188, $detalhe)))
            ->setValorIOF($this->formatCnabMoney($this->rem(215, 227, $detalhe)))
            ->setValorAbatimento($this->formatCnabMoney($this->rem(228, 240, $detalhe)))
            ->setValorDesconto($this->formatCnabMoney($this->rem(241, 253, $detalhe)))
            ->setValorRecebido($this->formatCnabMoney($this->rem(254, 266, $detalhe)))
            ->setValorMora($this->formatCnabMoney($this->rem(267, 279, $detalhe)))
            ->setValorMulta($this->formatCnabMoney($this->rem(280, 292, $detalhe)));

        $msgAdicional = str_split(sprintf('%08s', $this->rem(319, 328, $detalhe)), 2) + array_fill(0, 5, '');
        if ($d->hasOcorrencia('06', '15', '17')) {
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
        } elseif ($d->hasOcorrencia('14')) {
            $this->totais['alterados']++;
            $d->setOcorrenciaTipo($d::OCORRENCIA_ALTERACAO);
        } elseif ($d->hasOcorrencia('03', '24', '27', '30', '32')) {
            $this->totais['erros']++;
            $error = Util::appendStrings(
                data_get($this->rejeicoes, $msgAdicional[0], ''),
                data_get($this->rejeicoes, $msgAdicional[1], ''),
                data_get($this->rejeicoes, $msgAdicional[2], ''),
                data_get($this->rejeicoes, $msgAdicional[3], ''),
                data_get($this->rejeicoes, $msgAdicional[4], '')
            );
            if($d->hasOcorrencia('03')) {
               if(isset($this->rejeicoes[$this->rem(319, 320, $detalhe)])){
                  $d->setRejeicao($this->rejeicoes[$this->rem(319, 320, $detalhe)]);
               }
            }
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
    protected function processarTrailer( $trailer)
    {
        $this->getTrailer()
            ->setQuantidadeTitulos($this->rem(18, 25, $trailer))
            ->setValorTitulos($this->formatCnabMoney($this->rem(26, 39, $trailer)))
            ->setQuantidadeErros((int) $this->totais['erros'])
            ->setQuantidadeEntradas((int) $this->totais['entradas'])
            ->setQuantidadeLiquidados((int) $this->totais['liquidados'])
            ->setQuantidadeBaixados((int) $this->totais['baixados'])
            ->setQuantidadeAlterados((int) $this->totais['alterados']);

        return true;
    }
}

