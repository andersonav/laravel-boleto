<?php
namespace Alves\LaravelBoleto\Cnab\Retorno\Cnab400\Banco;

use Alves\LaravelBoleto\Cnab\Retorno\Cnab400\AbstractRetorno;
use Alves\LaravelBoleto\Contracts\Boleto\Boleto as BoletoContract;
use Alves\LaravelBoleto\Contracts\Cnab\RetornoCnab400;
use Alves\LaravelBoleto\Util;

class Bradesco extends AbstractRetorno implements RetornoCnab400
{
    /**
     * CÃ³digo do banco
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
        "06" => "LiquidaÃ§Ã£o normal (sem motivo)",
        "09" => "Baixado Automat. via Arquivo",
        "10" => "Baixado conforme instruÃ§Ãµes da AgÃªncia",
        "11" => "Em Ser - Arquivo de TÃ­tulos pendentes (sem motivo)",
        "12" => "Abatimento Concedido (sem motivo)",
        "13" => "Abatimento Cancelado (sem motivo)",
        "14" => "Vencimento Alterado (sem motivo)",
        "15" => "LiquidaÃ§Ã£o em CartÃ³rio (sem motivo)",
        "16" => "TÃ­tulo Pago em Cheque - Vinculado",
        "17" => "LiquidaÃ§Ã£o apÃ³s baixa ou TÃ­tulo nÃ£o registrado (sem motivo)",
        "18" => "Acerto de DepositÃ¡ria (sem motivo)",
        "19" => "ConfirmaÃ§Ã£o Receb. Inst. de Protesto",
        "20" => "ConfirmaÃ§Ã£o Recebimento InstruÃ§Ã£o SustaÃ§Ã£o de Protesto (sem motivo)",
        "21" => "Acerto do Controle do Participante (sem motivo)",
        "22" => "TÃ­tulo Com Pagamento Cancelado",
        "23" => "Entrada do TÃ­tulo em CartÃ³rio (sem motivo)",
        "24" => "Entrada rejeitada por CEP Irregular",
        "27" => "Baixa Rejeitada",
        "28" => "DÃ©bito de tarifas/custas",
        "30" => "AlteraÃ§Ã£o de Outros Dados Rejeitados",
        "32" => "InstruÃ§Ã£o Rejeitada",
        "33" => "ConfirmaÃ§Ã£o Pedido AlteraÃ§Ã£o Outros Dados (sem motivo)",
        "34" => "Retirado de CartÃ³rio e ManutenÃ§Ã£o Carteira (sem motivo)",
        "35" => "Desagendamento do dÃ©bito automÃ¡tico",
        "40" => "Estorno de pagamento (Novo)",
        "55" => "Sustado judicial (Novo)",
        "68" => "Acerto dos dados do rateio de CrÃ©dito",
        "69" => "Cancelamento dos dados do rateio",
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
        '07' => 'AgÃªncia/conta/Digito - |InvÃ¡lido',
        '08' => 'Nosso nÃºmero invÃ¡lido',
        '09' => 'Nosso nÃºmero duplicado',
        '10' => 'Carteira invÃ¡lida',
        '13' => 'IdentificaÃ§Ã£o da emissÃ£o do bloqueto invÃ¡lida',
        '16' => 'Data de vencimento invÃ¡lida',
        '18' => 'Vencimento fora do prazo de operaÃ§Ã£o',
        '20' => 'Valor do TÃ­tulo invÃ¡lido',
        '21' => 'EspÃ©cie do TÃ­tulo invÃ¡lida',
        '22' => 'EspÃ©cie nÃ£o permitida para a carteira',
        '24' => 'Data de emissÃ£o invÃ¡lida',
        '28' => 'CÃ³digo do desconto invÃ¡lido',
        '38' => 'Prazo para protesto/ NegativaÃ§Ã£o invÃ¡lido (ALTERADO)',
        '44' => 'AgÃªncia BeneficiÃ¡rio nÃ£o prevista',
        '45' => 'Nome do pagador nÃ£o informado',
        '46' => 'Tipo/nÃºmero de inscriÃ§Ã£o do pagador invÃ¡lidos',
        '47' => 'EndereÃ§o do pagador nÃ£o informado',
        '48' => 'CEP InvÃ¡lido',
        '50' => 'CEP irregular - Banco Correspondente',
        '63' => 'Entrada para TÃ­tulo jÃ¡ cadastrado',
        '65' => 'Limite excedido',
        '66' => 'NÃºmero autorizaÃ§Ã£o inexistente',
        '68' => 'DÃ©bito nÃ£o agendado - erro nos dados de remessa',
        '69' => 'DÃ©bito nÃ£o agendado - Pagador nÃ£o consta no cadastro de autorizante',
        '70' => 'DÃ©bito nÃ£o agendado - BeneficiÃ¡rio nÃ£o autorizado pelo Pagador',
        '71' => 'DÃ©bito nÃ£o agendado - BeneficiÃ¡rio nÃ£o participa do dÃ©bito AutomÃ¡tico',
        '72' => 'DÃ©bito nÃ£o agendado - CÃ³digo de moeda diferente de R$',
        '73' => 'DÃ©bito nÃ£o agendado - Data de vencimento invÃ¡lida',
        '74' => 'DÃ©bito nÃ£o agendado - Conforme seu pedido, TÃ­tulo nÃ£o registrado',
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
    protected function processarDetalhe(array $detalhe)
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
            ->setOcorrenciaDescricao(array_get($this->ocorrencias, $d->getOcorrencia(), 'Desconhecida'))
            ->setDataOcorrencia($this->rem(111, 116, $detalhe))
            ->setDataVencimento($this->rem(147, 152, $detalhe))
            ->setDataCredito($this->rem(296, 301, $detalhe))
            ->setValor(Util::nFloat($this->rem(153, 165, $detalhe)/100, 2, false))
            ->setValorTarifa(Util::nFloat($this->rem(176, 188, $detalhe)/100, 2, false))
            ->setValorIOF(Util::nFloat($this->rem(215, 227, $detalhe)/100, 2, false))
            ->setValorAbatimento(Util::nFloat($this->rem(228, 240, $detalhe)/100, 2, false))
            ->setValorDesconto(Util::nFloat($this->rem(241, 253, $detalhe)/100, 2, false))
            ->setValorRecebido(Util::nFloat($this->rem(254, 266, $detalhe)/100, 2, false))
            ->setValorMora(Util::nFloat($this->rem(267, 279, $detalhe)/100, 2, false))
            ->setValorMulta(Util::nFloat($this->rem(280, 292, $detalhe)/100, 2, false));

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
                array_get($this->rejeicoes, $msgAdicional[0], ''),
                array_get($this->rejeicoes, $msgAdicional[1], ''),
                array_get($this->rejeicoes, $msgAdicional[2], ''),
                array_get($this->rejeicoes, $msgAdicional[3], ''),
                array_get($this->rejeicoes, $msgAdicional[4], '')
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
    protected function processarTrailer(array $trailer)
    {
        $this->getTrailer()
            ->setQuantidadeTitulos($this->rem(18, 25, $trailer))
            ->setValorTitulos(Util::nFloat($this->rem(26, 39, $trailer)/100, 2, false))
            ->setQuantidadeErros((int) $this->totais['erros'])
            ->setQuantidadeEntradas((int) $this->totais['entradas'])
            ->setQuantidadeLiquidados((int) $this->totais['liquidados'])
            ->setQuantidadeBaixados((int) $this->totais['baixados'])
            ->setQuantidadeAlterados((int) $this->totais['alterados']);

        return true;
    }
}

