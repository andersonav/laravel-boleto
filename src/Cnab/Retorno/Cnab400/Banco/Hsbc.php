<?php
namespace Alves\LaravelBoleto\Cnab\Retorno\Cnab400\Banco;

use Alves\LaravelBoleto\Cnab\Retorno\Cnab400\AbstractRetorno;
use Alves\LaravelBoleto\Contracts\Boleto\Boleto as BoletoContract;
use Alves\LaravelBoleto\Contracts\Cnab\RetornoCnab400;
use Alves\LaravelBoleto\Util;

class Hsbc extends AbstractRetorno implements RetornoCnab400
{
    /**
     * CÃ³digo do banco
     *
     * @var string
     */
    protected $codigoBanco = BoletoContract::COD_BANCO_HSBC;

    /**
     * Array com as ocorrencias do banco;
     *
     * @var array
     */
    private $ocorrencias = [
        '02' => 'Entrada confirmada',
        '03' => 'Entrada rejeitada ou InstruÃ§Ã£o rejeitada',
        '06' => 'LiquidaÃ§Ã£o normal em dinheiro',
        '07' => 'LiquidaÃ§Ã£o por conta em dinheiro',
        '09' => 'Baixa automÃ¡tica',
        '10' => 'Baixado conforme instruÃ§Ãµes',
        '11' => 'TÃ­tulos em ser (ConciliaÃ§Ã£o Mensal)',
        '12' => 'Abatimento concedido',
        '13' => 'Abatimento cancelado',
        '14' => 'Vencimento prorrogado',
        '15' => 'LiquidaÃ§Ã£o em cartÃ³rio em dinheiro',
        '16' => 'LiquidaÃ§Ã£o - baixado/devolvido em data anterior dinheiro',
        '17' => 'Entregue em cartÃ³rio em .../... protocolo ...........',
        '18' => 'InstruÃ§Ã£o automÃ¡tica de protesto',
        '21' => 'InstruÃ§Ã£o de alteraÃ§Ã£o de mora',
        '22' => 'InstruÃ§Ã£o de protesto processada/re-emitida ',
        '23' => 'Cancelamento de protesto processado',
        '27' => 'NÃºmero do cedente ou controle do participante alterado.',
        '31' => 'LiquidaÃ§Ã£o normal em cheque/compensaÃ§Ã£o/banco correspondente',
        '32' => 'LiquidaÃ§Ã£o em cartÃ³rio em cheque',
        '33' => 'LiquidaÃ§Ã£o por conta em cheque',
        '36' => 'LiquidaÃ§Ã£o - baixado/devolvido em data anterior em cheque',
        '37' => 'Baixa de tÃ­tulo protestado',
        '38' => 'LiquidaÃ§Ã£o de tÃ­tulo nÃ£o registrado - em dinheiro (CobranÃ§a Expressa ou CobranÃ§a Diretiva)',
        '39' => 'LiquidaÃ§Ã£o de tÃ­tulo nÃ£o registrado - em cheque (CobranÃ§a Expressa ou CobranÃ§a Diretiva)',
        '49' => 'Vencimento alterado para .../.../...',
        '51' => 'TÃ­tulo DDA aceito pelo sacado.',
        '52' => 'TÃ­tulo DDA nÃ£o reconhecido pelo sacado.',
        '69' => 'Despesas/custas de cartÃ³rio(complemento posiÃ§Ãµes 176 a 188) ',
        '70' => 'Ressarcimento sobre tÃ­tulos.',
        '71' => 'OcorrÃªncia/InstruÃ§Ã£o nÃ£o permitida para tÃ­tulo em garantia de operaÃ§Ã£o.',
        '72' => 'ConcessÃ£o de Desconto Aceito.',
        '73' => 'Cancelamento CondiÃ§Ã£o de Desconto Fixo Aceito',
        '74' => 'Cancelamento de Desconto DiÃ¡rio Aceito.',
    ];

    /**
     * Array com as possiveis rejeicoes do banco.
     *
     * @var array
     */
    private $rejeicoes = [
        '01' => 'Valor do desconto nÃ£o informado/invÃ¡lido.',
        '02' => 'InexistÃªncia de agÃªncia do HSBC na praÃ§a do sacado. ',
        '03' => 'CEP do sacado incorreto ou invÃ¡lido.',
        '04' => 'Cadastro do cedente nÃ£o aceita banco correspondente. ',
        '05' => 'Tipo de moeda invÃ¡lido.',
        '06' => 'Prazo de protesto indefinido (nÃ£o informado)/invÃ¡lido ou prazo de protesto inferior ao tempo decorrido da data de vencimento em relaÃ§Ã£o ao envio da instruÃ§Ã£o de alteraÃ§Ã£o de prazo.',
        '07' => 'Data do vencimento invÃ¡lida.',
        '08' => 'Nosso nÃºmero(nÃºmero bancÃ¡rio) utilizado nÃ£o possui vinculaÃ§Ã£o com a conta cobranÃ§a.',
        '09' => 'Taxa mensal de mora acima do permitido (170%).',
        '10' => 'Taxa de multa acima do permitido (10% ao mÃªs).',
        '11' => 'Data limite de desconto invÃ¡lida.',
        '12' => 'CEP InvÃ¡lido/InexistÃªncia de Ag HSBC.',
        '13' => 'Valor/Taxa de multa invÃ¡lida.',
        '14' => 'Valor diÃ¡rio da multa nÃ£o informado.',
        '15' => 'Quantidade de dias apÃ³s vencimento para incidÃªncia da multa nÃ£o informada.',
        '16' => 'Outras irregularidades.',
        '17' => 'Data de inÃ­cio da multa invÃ¡lida.',
        '18' => 'Nosso nÃºmero (nÃºmero bancÃ¡rio) jÃ¡ existente para outro tÃ­tulo.',
        '19' => 'Valor do tÃ­tulo invÃ¡lido.',
        '20' => 'AusÃªncia CEP/EndereÃ§o/CNPJ ou Sacador Avalista. ',
        '21' => 'TÃ­tulo sem borderÃ´.',
        '22' => 'NÃºmero da conta do cedente nÃ£o cadastrado.',
        '23' => 'InstruÃ§Ã£o nÃ£o permitida para tÃ­tulo em garantia de operaÃ§Ã£o. ',
        '24' => 'CondiÃ§Ã£o de desconto nÃ£o permitida para titulo em garantia de OperaÃ§Ã£o.',
        '25' => 'Utilizada mais de uma instruÃ§Ã£o de multa.',
        '26' => 'AusÃªncia do endereÃ§o do sacado.',
        '27' => 'CEP invÃ¡lido.do sacado.',
        '28' => 'AusÃªncia do CPF/CNPJ do sacado em tÃ­tulo com instruÃ§Ã£o de protesto.',
        '29' => 'AgÃªncia cedente informada invÃ¡lida.',
        '30' => 'NÃºmero da conta do cedente invÃ¡lido.',
        '31' => 'Contrato garantia nÃ£o cadastrado/invÃ¡lido.',
        '32' => 'Tipo de carteira invÃ¡lido.',
        '33' => 'Conta corrente do cedente nÃ£o compatÃ­vel com o Ã³rgÃ£o do contratante.',
        '34' => 'Faixa de aplicaÃ§Ã£o nÃ£o cadastrada/invÃ¡lida.',
        '35' => 'Nosso nÃºmero (nÃºmero bancÃ¡rio) invÃ¡lido.',
        '36' => 'Data de emissÃ£o do tÃ­tulo invÃ¡lida.',
        '37' => 'Valor do tÃ­tulo acima de R$ 5.000.000,00 (Cinco milhÃµes de reais).',
        '38' => 'Data de desconto menor que data da emissÃ£o.',
        '39' => 'EspÃ©cie invÃ¡lida.',
        '40' => 'AusÃªncia no nome do sacador avalista.',
        '41' => 'Data de inÃ­cio de multa menor que data de emissÃ£o. ',
        '42' => 'Quantidade de moeda variÃ¡vel invÃ¡lida.',
        '43' => 'Controle do participante invÃ¡lido.',
        '44' => 'Nosso nÃºmero (nÃºmero bancÃ¡rio) duplicado no mesmo movimento.',
        '45' => 'TÃ­tulo nÃ£o aceito para compor a carteira de garantias',
        '50' => 'TÃ­tulo liquidado em..../..../... .(Vide data nas posiÃ§Ãµes 111 a 116). ',
        '51' => 'Data de emissÃ£o da ocorrÃªncia invÃ¡lida',
        '52' => 'Nosso nÃºmero (nÃºmero bancÃ¡rio) duplicado.',
        '53' => 'CÃ³digo de ocorrÃªncia comandada invÃ¡lido.',
        '54' => 'Valor do desconto concedido invÃ¡lido. (Vide valor nas posiÃ§Ãµes 228 a 240).',
        '55' => 'Data de prorrogaÃ§Ã£o de vencimento nÃ£o informada.',
        '56' => 'Outras irregularidades.',
        '57' => 'OcorrÃªncia nÃ£o permitida para tÃ­tulo em garantia de operaÃ§Ãµes. ',
        '58' => 'Nosso nÃºmero (nÃºmero bancÃ¡rio) comandado na instruÃ§Ã£o/ocorrÃªncia nÃ£o possui vinculaÃ§Ã£o com a conta cobranÃ§a. ',
        '59' => 'Nosso nÃºmero (nÃºmero bancÃ¡rio) comandado na baixa nÃ£o possui vinculaÃ§Ã£o com a conta cobranÃ§a.',
        '60' => 'Valor do desconto igual ou maior que o valor do tÃ­tulo.',
        '61' => 'Titulo com valor em moeda variÃ¡vel nÃ£o permite condiÃ§Ã£o de desconto.',
        '62' => 'Data do desconto informada nÃ£o coincide com o registro do tÃ­tulo. ',
        '63' => 'Titulo nÃ£o possui condiÃ§Ã£o de desconto diÃ¡rio.',
        '64' => 'TÃ­tulo baixado em...../...../.....(Vide data nas posiÃ§Ãµes 111 a 116) ',
        '65' => 'TÃ­tulo devolvido em...../...../.....(Vide data nas posiÃ§Ãµes 111 a 116) ',
        '66' => 'Valor do tÃ­tulo nÃ£o confere com o registrado.',
        '67' => 'Nosso nÃºmero (nÃºmero bancÃ¡rio) nÃ£o informado.',
        '68' => 'Nosso nÃºmero (nÃºmero bancÃ¡rio) invÃ¡lido.',
        '69' => 'ConcessÃ£o de abatimento nÃ£o Ã© permitida para moeda diferente de Real.',
        '70' => 'Valor do abatimento concedido invÃ¡lido. (Valor do abatimento zerado, maior ou igual ao valor do tÃ­tulo).',
        '71' => 'Cancelamento comandado sobre tÃ­tulo sem abatimento. ',
        '72' => 'ConcessÃ£o de desconto nÃ£o Ã© permitida para moeda diferente de real.',
        '73' => 'Valor do desconto nÃ£o informado.',
        '74' => 'Cancelamento comandado sobre tÃ­tulo sem desconto.',
        '75' => 'Data de vencimento alterado invÃ¡lida. (Vide data nas posiÃ§Ãµes 111 a 116).',
        '76' => 'Data de prorrogaÃ§Ã£o de vencimento invÃ¡lida.',
        '77' => 'Data da instruÃ§Ã£o invÃ¡lida.',
        '78' => 'Protesto comandado em duplicidade no mesmo dia.',
        '79' => 'TÃ­tulo nÃ£o possui instruÃ§Ã£o de protesto ou estÃ¡ com entrada jÃ¡ confirmada em cartÃ³rio.',
        '80' => 'TÃ­tulo nÃ£o possui condiÃ§Ã£o de desconto.',
        '81' => 'TÃ­tulo nÃ£o possui instruÃ§Ã£o de abatimento.',
        '82' => 'Valor de juros invÃ¡lido.',
        '83' => 'Nosso nÃºmero (nÃºmero bancÃ¡rio) inexistente. ',
        '84' => 'Baixa/liquidaÃ§Ã£o por Ã³rgÃ£o nÃ£o autorizado.',
        '85' => 'InstruÃ§Ã£o de protesto recusada/invÃ¡lida.',
        '86' => 'InstruÃ§Ã£o nÃ£o permitida para banco correspondente.',
        '87' => 'Valor da instruÃ§Ã£o invÃ¡lido.',
        '88' => 'InstruÃ§Ã£o invÃ¡lida para tipo de carteira.',
        '89' => 'Valor do desconto informado nÃ£o coincide com o registro do tÃ­tulo.',
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
            ->setAgencia($this->rem(28, 31, $header))
            ->setConta($this->rem(38, 43, $header))
            ->setContaDv($this->rem(44, 44, $header))
            ->setData($this->rem(95, 100, $header));

        return true;
    }

    protected function processarDetalhe(array $detalhe)
    {
        $d = $this->detalheAtual();
        $d->setCarteira($this->rem(108, 108, $detalhe))
            ->setNossoNumero($this->rem(63, 73, $detalhe))
            ->setNumeroDocumento($this->rem(117, 126, $detalhe))
            ->setNumeroControle($this->rem(38, 62, $detalhe))
            ->setOcorrencia($this->rem(109, 110, $detalhe))
            ->setOcorrenciaDescricao(array_get($this->ocorrencias, $d->getOcorrencia(), 'Desconhecida'))
            ->setDataOcorrencia($this->rem(111, 116, $detalhe))
            ->setDataVencimento($this->rem(147, 152, $detalhe))
            ->setValor(Util::nFloat($this->rem(153, 165, $detalhe)/100, 2, false))
            ->setValorTarifa(Util::nFloat($this->rem(176, 188, $detalhe)/100, 2, false))
            ->setValorAbatimento(Util::nFloat($this->rem(228, 240, $detalhe)/100, 2, false))
            ->setValorDesconto(Util::nFloat($this->rem(241, 253, $detalhe)/100, 2, false))
            ->setValorRecebido(Util::nFloat($this->rem(254, 266, $detalhe)/100, 2, false))
            ->setValorMora(Util::nFloat($this->rem(267, 279, $detalhe)/100, 2, false));

        $this->totais['valor_recebido'] += $d->getValorRecebido();

        if ($d->hasOcorrencia('06', '07', '15', '16', '31', '32', '33', '36', '38', '39')) {
            $this->totais['liquidados']++;
            $d->setOcorrenciaTipo($d::OCORRENCIA_LIQUIDADA);
        } elseif ($d->hasOcorrencia('02')) {
            $this->totais['entradas']++;
            $d->setOcorrenciaTipo($d::OCORRENCIA_ENTRADA);
        } elseif ($d->hasOcorrencia('09', '10', '16')) {
            $this->totais['baixados']++;
            $d->setOcorrenciaTipo($d::OCORRENCIA_BAIXADA);
        } elseif ($d->hasOcorrencia('37')) {
            $this->totais['protestados']++;
            $d->setOcorrenciaTipo($d::OCORRENCIA_PROTESTADA);
        } elseif ($d->hasOcorrencia('14', '49')) {
            $this->totais['alterados']++;
            $d->setOcorrenciaTipo($d::OCORRENCIA_ALTERACAO);
        } elseif ($d->hasOcorrencia('03')) {
            $this->totais['erros']++;
            $d->setError(array_get($this->rejeicoes, $this->rem(302, 303, $detalhe), 'Consulte seu Internet Banking'));
        } else {
            $d->setOcorrenciaTipo($d::OCORRENCIA_OUTROS);
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

