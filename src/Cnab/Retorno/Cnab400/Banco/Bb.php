<?php
namespace Alves\LaravelBoleto\Cnab\Retorno\Cnab400\Banco;

use Alves\LaravelBoleto\Cnab\Retorno\Cnab400\AbstractRetorno;
use Alves\LaravelBoleto\Contracts\Boleto\Boleto as BoletoContract;
use Alves\LaravelBoleto\Contracts\Cnab\RetornoCnab400;
use Alves\LaravelBoleto\Util;

class Bb extends AbstractRetorno implements RetornoCnab400
{
    /**
     * CÃ³digo do banco
     *
     * @var string
     */
    protected $codigoBanco = BoletoContract::COD_BANCO_BB;

    /**
     * Array com as ocorrencias do banco;
     *
     * @var array
     */
    private $ocorrencias = [
        '02' => 'ConfirmaÃ§Ã£o de Entrada de TÃ­tulo',
        '03' => 'Comando recusado (Motivo indicado na posiÃ§Ã£o 087/088)',
        '05' => 'Liquidado sem registro (carteira 17-tipo4)',
        '06' => 'LiquidaÃ§Ã£o Normal',
        '07' => 'LiquidaÃ§Ã£o por Conta',
        '08' => 'LiquidaÃ§Ã£o por Saldo',
        '09' => 'Baixa de Titulo',
        '10' => 'Baixa Solicitada',
        '11' => 'TÃ­tulos em Ser (constara somente do arquivo de existÃªncia de cobranÃ§a, fornecido mediante solicitaÃ§Ã£o do cliente)',
        '12' => 'Abatimento Concedido',
        '13' => 'Abatimento Cancelado',
        '14' => 'AlteraÃ§Ã£o de Vencimento do tÃ­tulo',
        '15' => 'LiquidaÃ§Ã£o em CartÃ³rio',
        '16' => 'ConfirmaÃ§Ã£o de alteraÃ§Ã£o de juros de mora',
        '19' => 'ConfirmaÃ§Ã£o de recebimento de instruÃ§Ãµes para protesto',
        '20' => 'Debito em Conta',
        '21' => 'AlteraÃ§Ã£o do Nome do Sacado',
        '22' => 'AlteraÃ§Ã£o do EndereÃ§o do Sacado',
        '23' => 'IndicaÃ§Ã£o de encaminhamento a cartÃ³rio',
        '24' => 'Sustar Protesto',
        '25' => 'Dispensar Juros de mora',
        '26' => 'AlteraÃ§Ã£o do nÃºmero do tÃ­tulo dado pelo Cedente (Seu nÃºmero) â€“ 10 e 15 posiÃ§Ãµes',
        '28' => 'ManutenÃ§Ã£o de titulo vencido',
        '31' => 'Conceder desconto',
        '32' => 'NÃ£o conceder desconto',
        '33' => 'Retificar desconto',
        '34' => 'Alterar data para desconto',
        '35' => 'Cobrar Multa',
        '36' => 'Dispensar Multa',
        '37' => 'Dispensar Indexador',
        '38' => 'Dispensar prazo limite para recebimento',
        '39' => 'Alterar prazo limite para recebimento',
        '41' => 'AlteraÃ§Ã£o do nÃºmero do controle do participante (25 posiÃ§Ãµes)',
        '42' => 'AlteraÃ§Ã£o do nÃºmero do documento do sacado (CNPJ/CPF)',
        '44' => 'TÃ­tulo pago com cheque devolvido',
        '46' => 'TÃ­tulo pago com cheque, aguardando compensaÃ§Ã£o',
        '72' => 'AlteraÃ§Ã£o de tipo de cobranÃ§a (especÃ­fico para tÃ­tulos das carteiras 11 e 17)',
        '96' => 'Despesas de Protesto',
        '97' => 'Despesas de SustaÃ§Ã£o de Protesto',
        '98' => 'Debito de Custas Antecipadas',
    ];

    /**
     * Array com as possiveis rejeicoes do banco.
     *
     * @var array
     */
    private $rejeicoes = [
        '01' => 'identificaÃ§Ã£o invÃ¡lida',
        '02' => 'variaÃ§Ã£o da carteira invÃ¡lida',
        '03' => 'valor dos juros por um dia invÃ¡lido',
        '04' => 'valor do desconto invÃ¡lido',
        '05' => 'espÃ©cie de tÃ­tulo invÃ¡lida para carteira/variaÃ§Ã£o',
        '06' => 'espÃ©cie de valor invariÃ¡vel invÃ¡lido',
        '07' => 'prefixo da agÃªncia usuÃ¡ria invÃ¡lido',
        '08' => 'valor do tÃ­tulo/apÃ³lice invÃ¡lido',
        '09' => 'data de vencimento invÃ¡lida',
        '10' => 'fora do prazo/sÃ³ admissÃ­vel na carteira',
        '11' => 'inexistÃªncia de margem para desconto',
        '12' => 'o banco nÃ£o tem agÃªncia na praÃ§a do sacado',
        '13' => 'razÃµes cadastrais',
        '14' => 'sacado interligado com o sacador (sÃ³ admissÃ­vel em cobranÃ§a simples- cart. 11 e 17)',
        '15' => 'Titulo sacado contra Ã³rgÃ£o do Poder PÃºblico (sÃ³ admissÃ­vel na carteira 11 e sem ordem de protesto)',
        '16' => 'Titulo preenchido de forma irregular',
        '17' => 'Titulo rasurado',
        '18' => 'EndereÃ§o do sacado nÃ£o localizado ou incompleto',
        '19' => 'CÃ³digo do cedente invÃ¡lido',
        '20' => 'Nome/endereÃ§o do cliente nÃ£o informado (ECT)',
        '21' => 'Carteira invÃ¡lida',
        '22' => 'Quantidade de valor variÃ¡vel invÃ¡lida',
        '23' => 'Faixa nosso-numero excedida',
        '24' => 'Valor do abatimento invÃ¡lido',
        '25' => 'Novo nÃºmero do tÃ­tulo dado pelo cedente invÃ¡lido (Seu nÃºmero)',
        '26' => 'Valor do IOF de seguro invÃ¡lido',
        '27' => 'Nome do sacado/cedente invÃ¡lido',
        '28' => 'Data do novo vencimento invÃ¡lida',
        '29' => 'EndereÃ§o nÃ£o informado',
        '30' => 'Registro de tÃ­tulo jÃ¡ liquidado (carteira 17-tipo 4)',
        '31' => 'Numero do borderÃ´ invÃ¡lido',
        '32' => 'Nome da pessoa autorizada invÃ¡lido',
        '33' => 'Nosso nÃºmero jÃ¡ existente',
        '34' => 'Numero da prestaÃ§Ã£o do contrato invÃ¡lido',
        '35' => 'percentual de desconto invÃ¡lido',
        '36' => 'Dias para fichamento de protesto invÃ¡lido',
        '37' => 'Data de emissÃ£o do tÃ­tulo invÃ¡lida',
        '38' => 'Data do vencimento anterior Ã  data da emissÃ£o do tÃ­tulo',
        '39' => 'Comando de alteraÃ§Ã£o indevido para a carteira',
        '40' => 'Tipo de moeda invÃ¡lido',
        '41' => 'Abatimento nÃ£o permitido',
        '42' => 'CEP/UF invÃ¡lido/nÃ£o compatÃ­veis (ECT)',
        '43' => 'CÃ³digo de unidade variÃ¡vel incompatÃ­vel com a data de emissÃ£o do tÃ­tulo',
        '44' => 'Dados para debito ao sacado invÃ¡lidos',
        '45' => 'Carteira/variaÃ§Ã£o encerrada',
        '46' => 'Convenio encerrado',
        '47' => 'Titulo tem valor diverso do informado',
        '48' => 'Motivo de baixa invalido para a carteira',
        '49' => 'Abatimento a cancelar nÃ£o consta do tÃ­tulo',
        '50' => 'Comando incompatÃ­vel com a carteira',
        '51' => 'CÃ³digo do convenente invalido',
        '52' => 'Abatimento igual ou maior que o valor do titulo',
        '53' => 'Titulo jÃ¡ se encontra na situaÃ§Ã£o pretendida',
        '54' => 'Titulo fora do prazo admitido para a conta 1',
        '55' => 'Novo vencimento fora dos limites da carteira',
        '56' => 'Titulo nÃ£o pertence ao convenente',
        '57' => 'VariaÃ§Ã£o incompatÃ­vel com a carteira',
        '58' => 'ImpossÃ­vel a variaÃ§Ã£o Ãºnica para a carteira indicada',
        '59' => 'Titulo vencido em transferÃªncia para a carteira 51',
        '60' => 'Titulo com prazo superior a 179 dias em variaÃ§Ã£o Ãºnica para carteira 51',
        '61' => 'Titulo jÃ¡ foi fichado para protesto',
        '62' => 'AlteraÃ§Ã£o da situaÃ§Ã£o de debito invÃ¡lida para o cÃ³digo de responsabilidade',
        '63' => 'DV do nosso nÃºmero invÃ¡lido',
        '64' => 'Titulo nÃ£o passÃ­vel de dÃ©bito/baixa â€“ situaÃ§Ã£o anormal',
        '65' => 'Titulo com ordem de nÃ£o protestar â€“ nÃ£o pode ser encaminhado a cartÃ³rio',
        '66' => 'NÃºmero do documento do sacado (CNPJ/CPF) invÃ¡lido',
        '67' => 'Titulo/carne rejeitado',
        '68' => 'CÃ³digo/Data/Percentual de multa invÃ¡lido',
        '69' => 'Valor/Percentual de Juros InvÃ¡lido',
        '70' => 'TÃ­tulo jÃ¡ se encontra isento de juros',
        '71' => 'CÃ³digo de Juros InvÃ¡lido',
        '72' => 'Prefixo da Ag. cobradora invÃ¡lido',
        '73' => 'Numero do controle do participante invÃ¡lido',
        '74' => 'Cliente nÃ£o cadastrado no CIOPE (Desconto/Vendor)',
        '75' => 'Qtde. de dias do prazo limite p/ recebimento de tÃ­tulo vencido invÃ¡lido',
        '76' => 'Titulo excluÃ­do automaticamente por decurso de prazo CIOPE (Desconto/Vendor)',
        '77' => 'Titulo vencido transferido para a conta 1 â€“ Carteira vinculada',
        '80' => 'Nosso numero invÃ¡lido',
        '81' => 'Data para concessÃ£o do desconto invÃ¡lida. Gerada nos seguintes casos: 11 - erro na data do desconto; 12 - data do desconto anterior Ã  data de emissÃ£o',
        '82' => 'CEP do sacado invÃ¡lido',
        '83' => 'Carteira/variaÃ§Ã£o nÃ£o localizada no cedente',
        '84' => 'Titulo nÃ£o localizado na existÃªncia',
        '99' => 'Outros motivos',
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
            ->setServico($this->rem(12, 19, $header))
            ->setAgencia($this->rem(27, 30, $header))
            ->setAgenciaDv($this->rem(31, 31, $header))
            ->setConta($this->rem(32, 39, $header))
            ->setContaDv($this->rem(40, 40, $header))
            ->setConvenio($this->rem(150, 156, $header))
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
        if ($this->rem(1, 1, $detalhe) != '7') {
            return false;
        }

        $d = $this->detalheAtual();

        $d->setCarteira($this->rem(107, 108, $detalhe))
            ->setNossoNumero($this->rem(64, 80, $detalhe))
            ->setNumeroDocumento($this->rem(117, 126, $detalhe))
            ->setNumeroControle($this->rem(39, 63, $detalhe))
            ->setOcorrencia($this->rem(109, 110, $detalhe))
            ->setOcorrenciaDescricao(data_get($this->ocorrencias, $d->getOcorrencia(), 'Desconhecida'))
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

        if ($d->hasOcorrencia('05', '06', '07', '08', '15')) {
            $this->totais['liquidados']++;
            $d->setOcorrenciaTipo($d::OCORRENCIA_LIQUIDADA);
        } elseif ($d->hasOcorrencia('02')) {
            $this->totais['entradas']++;
            $d->setOcorrenciaTipo($d::OCORRENCIA_ENTRADA);
        } elseif ($d->hasOcorrencia('09', '10')) {
            $this->totais['baixados']++;
            $d->setOcorrenciaTipo($d::OCORRENCIA_BAIXADA);
        } elseif ($d->hasOcorrencia('61')) {
            $this->totais['protestados']++;
            $d->setOcorrenciaTipo($d::OCORRENCIA_PROTESTADA);
        } elseif ($d->hasOcorrencia('14')) {
            $this->totais['alterados']++;
            $d->setOcorrenciaTipo($d::OCORRENCIA_ALTERACAO);
        } elseif ($d->hasOcorrencia('03')) {
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
            ->setQuantidadeEntradas((int) $this->totais['entradas'])
            ->setQuantidadeLiquidados((int) $this->totais['liquidados'])
            ->setQuantidadeBaixados((int) $this->totais['baixados'])
            ->setQuantidadeAlterados((int) $this->totais['alterados']);

        return true;
    }
}

