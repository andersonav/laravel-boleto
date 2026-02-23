<?php
namespace Alves\LaravelBoleto\Cnab\Retorno\Cnab400\Banco;

use Alves\LaravelBoleto\Cnab\Retorno\Cnab400\AbstractRetorno;
use Alves\LaravelBoleto\Contracts\Boleto\Boleto as BoletoContract;
use Alves\LaravelBoleto\Contracts\Cnab\RetornoCnab400;
use Alves\LaravelBoleto\Util;

class Caixa extends AbstractRetorno implements RetornoCnab400
{
    /**
     * CÃ³digo do banco
     *
     * @var string
     */
    protected $codigoBanco = BoletoContract::COD_BANCO_CEF;

    /**
     * Array com as ocorrencias do banco;
     *
     * @var array
     */
    private $ocorrencias = [
        '01' => 'Entrada Confirmada',
        '02' => 'Baixa Confirmada',
        '03' => 'Abatimento Concedido',
        '04' => 'Abatimento Cancelado',
        '05' => 'Vencimento Alterado',
        '06' => 'Uso da Empresa Alterado',
        '07' => 'Prazo de Protesto Alterado',
        '08' => 'Prazo de DevoluÃ§Ã£o Alterado',
        '09' => 'AlteraÃ§Ã£o Confirmada',
        '10' => 'AlteraÃ§Ã£o com ReemissÃ£o de Bloqueto Confirmada',
        '11' => 'AlteraÃ§Ã£o da OpÃ§Ã£o de Protesto para DevoluÃ§Ã£o',
        '12' => 'AlteraÃ§Ã£o da OpÃ§Ã£o de DevoluÃ§Ã£o para protesto',
        '20' => 'Em Ser',
        '21' => 'LiquidaÃ§Ã£o',
        '22' => 'LiquidaÃ§Ã£o em CartÃ³rio',
        '23' => 'Baixa por DevoluÃ§Ã£o',
        '24' => 'Baixa por Franco Pagamento',
        '25' => 'Baixa por Protesto',
        '26' => 'TÃ­tulo enviado para CartÃ³rio',
        '27' => 'SustaÃ§Ã£o de Protesto',
        '28' => 'Estorno de Protesto',
        '29' => 'Estorno de SustaÃ§Ã£o de Protesto',
        '30' => 'AlteraÃ§Ã£o de TÃ­tulo',
        '31' => 'Tarifa sobre TÃ­tulo Vencido',
        '32' => 'Outras Tarifas de AlteraÃ§Ã£o',
        '33' => 'Estorno de Baixa/LiquidaÃ§Ã£o',
        '34' => 'TransferÃªncia de Carteira/Entrada',
        '35' => 'TransferÃªncia de Carteira/Baixa',
        '99' => 'RejeiÃ§Ã£o do TÃ­tulo â€“ CÃ³d. RejeiÃ§Ã£o informado nas POS 80 a 82'
    ];

    /**
     * Array com as possiveis rejeicoes do banco.
     *
     * @var array
     */
    private $rejeicoes = [
        '01' => 'Movimento sem Cedente Correspondente ',
        '02' => 'Movimento sem TÃ­tulo Correspondente',
        '08' => 'Movimento para TÃ­tulo jÃ¡ com MovimentaÃ§Ã£o no dia ',
        '09' => 'Nosso NÃºmero nÃ£o Pertence ao Cedente',
        '10' => 'InclusÃ£o de TÃ­tulo jÃ¡ Existente',
        '12' => 'Movimento Duplicado',
        '13' => 'Entrada InvÃ¡lida para CobranÃ§a Caucionada (Cedente nÃ£o possui conta CauÃ§Ã£o)',
        '20' => 'CEP do Sacado nÃ£o Encontrado (NÃ£o foi possÃ­vel a DeterminaÃ§Ã£o da AgÃªncia Cobradora para o TÃ­tulo) ',
        '21' => 'AgÃªncia Cobradora nÃ£o Encontrada (AgÃªncia Designada para Cobradora nÃ£o Cadastrada no Sistema) ',
        '22' => 'AgÃªncia Cedente nÃ£o Encontrada (AgÃªncia do Cedente nÃ£o Cadastrada no Sistema)',
        '45' => 'Data de Vencimento com prazo mais de 1 ano',
        '49' => 'Movimento InvÃ¡lido para TÃ­tulo Baixado/Liquidado',
        '50' => 'Movimento InvÃ¡lido para TÃ­tulo enviado ao CartÃ³rio',
        '54' => 'Faixa de CEP da AgÃªncia Cobradora nÃ£o Abrange CEP do Sacado',
        '55' => 'TÃ­tulo jÃ¡ com OpÃ§Ã£o de DevoluÃ§Ã£o',
        '56' => 'Processo de Protesto em Andamento',
        '57' => 'TÃ­tulo jÃ¡ com OpÃ§Ã£o de Protesto',
        '58' => 'Processo de DevoluÃ§Ã£o em Andamento',
        '59' => 'Novo Prazo p/ Protesto/DevoluÃ§Ã£o InvÃ¡lido',
        '76' => 'AlteraÃ§Ã£o de Prazo de Protesto InvÃ¡lida',
        '77' => 'AlteraÃ§Ã£o de Prazo de DevoluÃ§Ã£o InvÃ¡lida',
        '81' => 'CEP do Sacado InvÃ¡lido',
        '82' => 'CGC/CPF do Sacado InvÃ¡lido (DÃ­gito nÃ£o Confere)',
        '83' => 'NÃºmero do Documento (Seu NÃºmero) invÃ¡lido',
        '84' => 'Protesto invÃ¡lido para tÃ­tulo sem NÃºmero do Documento (Seu NÃºmero)',
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
            ->setCodigoCliente($this->rem(31, 36, $header))
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
        $d->setCarteira($this->rem(107, 108, $detalhe))
            ->setNossoNumero($this->rem(57, 73, $detalhe))
            ->setNumeroDocumento($this->rem(117, 126, $detalhe))
            ->setNumeroControle($this->rem(32, 56, $detalhe))
            ->setOcorrencia($this->rem(109, 110, $detalhe))
            ->setOcorrenciaDescricao(array_get($this->ocorrencias, $d->getOcorrencia(), 'Desconhecida'))
            ->setDataOcorrencia($this->rem(111, 116, $detalhe))
            ->setDataVencimento($this->rem(147, 152, $detalhe))
            ->setDataCredito($this->rem(294, 299, $detalhe))
            ->setValor(Util::nFloat($this->rem(153, 165, $detalhe)/100, 2, false))
            ->setValorTarifa(Util::nFloat($this->rem(176, 188, $detalhe)/100, 2, false))
            ->setValorIOF(Util::nFloat($this->rem(215, 227, $detalhe)/100, 2, false))
            ->setValorAbatimento(Util::nFloat($this->rem(228, 240, $detalhe)/100, 2, false))
            ->setValorDesconto(Util::nFloat($this->rem(241, 253, $detalhe)/100, 2, false))
            ->setValorRecebido(Util::nFloat($this->rem(254, 266, $detalhe)/100, 2, false))
            ->setValorMora(Util::nFloat($this->rem(267, 279, $detalhe)/100, 2, false))
            ->setValorMulta(Util::nFloat($this->rem(280, 292, $detalhe)/100, 2, false));

        $this->totais['valor_recebido'] += $d->getValorRecebido();

        if ($d->hasOcorrencia('21', '22')) {
            $this->totais['liquidados']++;
            $d->setOcorrenciaTipo($d::OCORRENCIA_LIQUIDADA);
        } elseif ($d->hasOcorrencia('01')) {
            $this->totais['entradas']++;
            $d->setOcorrenciaTipo($d::OCORRENCIA_ENTRADA);
        } elseif ($d->hasOcorrencia('02', '23', '24', '25')) {
            $this->totais['baixados']++;
            $d->setOcorrenciaTipo($d::OCORRENCIA_BAIXADA);
        } elseif ($d->hasOcorrencia('56')) {
            $this->totais['protestados']++;
            $d->setOcorrenciaTipo($d::OCORRENCIA_PROTESTADA);
        } elseif ($d->hasOcorrencia('05')) {
            $this->totais['alterados']++;
            $d->setOcorrenciaTipo($d::OCORRENCIA_ALTERACAO);
        } elseif ($d->hasOcorrencia('99')) {
            $this->totais['erros']++;
            $d->setError(array_get($this->rejeicoes, $this->rem(80, 82, $detalhe), 'Consulte seu Internet Banking'));
        } else {
            $d->setOcorrenciaTipo($d::OCORRENCIA_OUTROS);
        }

        return true;
    }

    /**
     * @param array $trailer
     *
     * @return bool
     */
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

