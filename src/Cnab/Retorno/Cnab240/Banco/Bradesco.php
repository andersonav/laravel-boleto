<?php

namespace Alves\LaravelBoleto\Cnab\Retorno\Cnab240\Banco;

use Alves\LaravelBoleto\Cnab\Retorno\Cnab240\AbstractRetorno;
use Alves\LaravelBoleto\Contracts\Boleto\Boleto as BoletoContract;
use Alves\LaravelBoleto\Contracts\Cnab\RetornoCnab240;
use Alves\LaravelBoleto\Util;

class Bradesco extends AbstractRetorno implements RetornoCnab240
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
        '02' => 'Entrada Confirmada',
        '03' => 'Entrada Rejeitada',
        '04' => 'TransferÃªncia de Carteira/Entrada',
        '05' => 'TransferÃªncia de Carteira/Baixa',
        '06' => 'LiquidaÃ§Ã£o',
        '07' => 'ConfirmaÃ§Ã£o do Recebimento da InstruÃ§Ã£o de Desconto',
        '08' => 'ConfirmaÃ§Ã£o do Recebimento do Cancelamento do Desconto',
        '09' => 'Baixa',
        '11' => 'TÃ­tulos em Carteira (Em Ser)',
        '12' => 'ConfirmaÃ§Ã£o Recebimento InstruÃ§Ã£o de Abatimento',
        '13' => 'ConfirmaÃ§Ã£o Recebimento InstruÃ§Ã£o de Cancelamento Abatimento',
        '14' => 'ConfirmaÃ§Ã£o Recebimento InstruÃ§Ã£o AlteraÃ§Ã£o de Vencimento',
        '15' => 'Franco de Pagamento',
        '17' => 'LiquidaÃ§Ã£o ApÃ³s Baixa ou LiquidaÃ§Ã£o TÃ­tulo NÃ£o Registrado b/c',
        '19' => 'ConfirmaÃ§Ã£o Recebimento InstruÃ§Ã£o de Protesto',
        '20' => 'ConfirmaÃ§Ã£o Recebimento InstruÃ§Ã£o de SustaÃ§Ã£o de Protesto',
        '23' => 'Remessa a CartÃ³rio (Aponte em CartÃ³rio)',
        '24' => 'Retirada de CartÃ³rio e ManutenÃ§Ã£o em Carteira',
        '25' => 'Protestado e Baixado (Baixa por Ter Sido Protestado)',
        '26' => 'InstruÃ§Ã£o Rejeitada (utilizar serviÃ§o NegativaÃ§Ã£o)',
        '27' => 'ConfirmaÃ§Ã£o do Pedido de AlteraÃ§Ã£o de Outros Dados',
        '28' => 'DÃ©bito de Tarifas/Custas',
        '29' => 'OcorrÃªncias do Pagador (NÃ£o Tratar DDA)',
        '30' => 'AlteraÃ§Ã£o de Dados Rejeitada',
        '33' => 'ConfirmaÃ§Ã£o da AlteraÃ§Ã£o dos Dados do Rateio de CrÃ©dito',
        '34' => 'ConfirmaÃ§Ã£o do Cancelamento dos Dados do Rateio de CrÃ©dito',
        '35' => 'ConfirmaÃ§Ã£o do Desagendamento do DÃ©bito AutomÃ¡tico',
        '36' => 'ConfirmaÃ§Ã£o de envio de e-mail/SMS (NÃ£o Tratar)',
        '37' => 'Envio de e-mail/SMS rejeitado (NÃ£o tratar)',
        '38' => 'ConfirmaÃ§Ã£o de alteraÃ§Ã£o do Prazo Limite de Recebimento',
        '39' => 'ConfirmaÃ§Ã£o de Dispensa de Prazo Limite de Recebimento',
        '40' => 'ConfirmaÃ§Ã£o da alteraÃ§Ã£o do nÃºmero do tÃ­tulo dado pelo beneficiario',
        '41' => 'ConfirmaÃ§Ã£o da alteraÃ§Ã£o do nÃºmero controle do Participante',
        '42' => 'ConfirmaÃ§Ã£o da alteraÃ§Ã£o dos dados do Pagador',
        '43' => 'ConfirmaÃ§Ã£o da alteraÃ§Ã£o dos dados do Sacador/Avalista',
        '44' => 'TÃ­tulo pago com cheque devolvido',
        '45' => 'TÃ­tulo pago com cheque compensado',
        '46' => 'InstruÃ§Ã£o para cancelar protesto confirmada',
        '47' => 'InstruÃ§Ã£o para protesto para fins falimentares confirmada',
        '48' => 'ConfirmaÃ§Ã£o de instruÃ§Ã£o de transferÃªncia de carteira/modalidade de cobranÃ§a',
        '49' => 'AlteraÃ§Ã£o de contrato de cobranÃ§a',
        '50' => 'TÃ­tulo pago com cheque pendente de liquidaÃ§Ã£o',
        '51' => 'TÃ­tulo DDA reconhecido pelo pagador',
        '52' => 'TÃ­tulo DDA nÃ£o reconhecido pelo pagador',
        '53' => 'TÃ­tulo DDA recusado pela CIP',
        '54' => 'ConfirmaÃ§Ã£o da InstruÃ§Ã£o de Baixa de TÃ­tulo Negativado sem Protesto',
        '73' => 'ConfirmaÃ§Ã£o recebimento pedido de negativaÃ§Ã£o',
    ];

    /**
     * Array com as possiveis descricoes de baixa e liquidacao.
     *
     * @var array
     */
    private $baixa_liquidacao = [
        '01' => 'Por Saldo',
        '02' => 'Por Conta',
        '03' => 'LiquidaÃ§Ã£o no GuichÃª de Caixa em Dinheiro',
        '04' => 'CompensaÃ§Ã£o EletrÃ´nica',
        '05' => 'CompensaÃ§Ã£o Convencional',
        '06' => 'Por Meio EletrÃ´nico',
        '07' => 'ApÃ³s Feriado Local',
        '08' => 'Em CartÃ³rio',
        '30' => 'LiquidaÃ§Ã£o no GuichÃª de Caixa em Cheque',
        '31' => 'LiquidaÃ§Ã£o em banco correspondente',
        '32' => 'LiquidaÃ§Ã£o Terminal de Auto-Atendimento',
        '33' => 'LiquidaÃ§Ã£o na Internet (Home banking)',
        '34' => 'Liquidado Office Banking',
        '35' => 'Liquidado Correspondente em Dinheiro',
        '36' => 'Liquidado Correspondente em Cheque',
        '37' => 'Liquidado por meio de Central de Atendimento (Telefone) Baixa: PELO BANCO)',
        '09' => 'Comandada Banco',
        '10' => 'Comandada Cliente Arquivo',
        '11' => 'Comandada Cliente On-line',
        '12' => 'Decurso Prazo - Cliente',
        '13' => 'Decurso Prazo - Banco',
        '14' => 'Protestado',
        '15' => 'TÃ­tulo ExcluÃ­do',
    ];

    /**
     * Array com as possiveis rejeicoes do banco.
     *
     * @var array
     */
    private $rejeicoes = [
        '01' => 'CÃ³digo do Banco InvÃ¡lido',
        '02' => 'CÃ³digo do Registro Detalhe InvÃ¡lido',
        '03' => 'CÃ³digo do Segmento InvÃ¡lido',
        '04' => 'CÃ³digo de Movimento NÃ£o Permitido para Carteira',
        '05' => 'CÃ³digo de Movimento InvÃ¡lido',
        '06' => 'Tipo/NÃºmero de InscriÃ§Ã£o do Beneficiario InvÃ¡lidos',
        '07' => 'AgÃªncia/Conta/DV InvÃ¡lido',
        '08' => 'Nosso NÃºmero InvÃ¡lido',
        '09' => 'Nosso NÃºmero Duplicado',
        '10' => 'Carteira InvÃ¡lida',
        '11' => 'Forma de Cadastramento do TÃ­tulo InvÃ¡lido',
        '12' => 'Tipo de Documento InvÃ¡lido',
        '13' => 'IdentificaÃ§Ã£o da EmissÃ£o do Bloqueto InvÃ¡lida',
        '14' => 'IdentificaÃ§Ã£o da DistribuiÃ§Ã£o do Bloqueto InvÃ¡lida',
        '15' => 'CaracterÃ­sticas da CobranÃ§a IncompatÃ­veis',
        '16' => 'Data de Vencimento InvÃ¡lida',
        '17' => 'Data de Vencimento Anterior a Data de EmissÃ£o',
        '18' => 'Vencimento fora do prazo da operaÃ§Ã£o da OperaÃ§Ã£o (indicador registro de tÃ­tulos vencidos hÃ¡ mais de 59 dias)',
        '19' => 'TÃ­tulo a cargo de Bancos Correspondentes com vencimento inferior a XX dias',
        '20' => 'Valor do TÃ­tulo InvÃ¡lido',
        '21' => 'EspÃ©cie do TÃ­tulo InvÃ¡lida',
        '22' => 'EspÃ©cie do TÃ­tulo NÃ£o permitida para a carteira',
        '23' => 'Aceita InvÃ¡lido (Utilizar serviÃ§o NegativaÃ§Ã£o)',
        '24' => 'Data da EmissÃ£o InvÃ¡lida',
        '25' => 'Data da EmissÃ£o porteior da data de entrada',
        '26' => 'CÃ³digo de Juros de Mora InvÃ¡lido',
        '27' => 'Valor/taxa de Juros de Mora InvÃ¡lido',
        '28' => 'CÃ³digo do Desconto InvÃ¡lido',
        '29' => 'Valor do Desconto Maior ou Igual ao Valor do tÃ­tulo',
        '30' => 'Desconto a conceder nÃ£o confere',
        '31' => 'ConcessÃ£o de Desconto - JÃ¡ Existe Desconto Anterior',
        '32' => 'Valor do IOF InvÃ¡lido',
        '33' => 'Valor do Abatimento InvÃ¡lido',
        '34' => 'Valor do Abatimento Maior ou Igual ao Valor do TÃ­tulo',
        '35' => 'Valor a Conceder NÃ£o Confere',
        '36' => 'ConcessÃ£o de Abatimento - JÃ¡ Existe Abatimento Anterior',
        '37' => 'CÃ³digo para Protesto InvÃ¡lido',
        '38' => 'Prazo para Protesto/NegativaÃ§Ã£o InvÃ¡lido (alterado)',
        '39' => 'Pedido de Protesto/ NegativaÃ§Ã£o NÃ£o Permitido para o TÃ­tulo (alterado)',
        '40' => 'TÃ­tulo com Ordem/pedido de Protesto/NegativaÃ§Ã£o Emitida (o) (alterado)',
        '41' => 'Pedido de SustaÃ§Ã£o/Excl p/ TÃ­tulo s/ Instr de Protesto/NegativaÃ§Ã£o (alterado)',
        '42' => 'CÃ³digo para Baixa/DevoluÃ§Ã£o InvÃ¡lido',
        '43' => 'Prazo para Baixa/DevoluÃ§Ã£o InvÃ¡lido',
        '44' => 'CÃ³digo da Moeda InvÃ¡lido',
        '45' => 'Nome do Pagador NÃ£o Informado',
        '46' => 'Tipo/NÃºmero de InscriÃ§Ã£o do Pagador InvÃ¡lidos',
        '47' => 'EndereÃ§o do Pagador NÃ£o Informado',
        '48' => 'CEP InvÃ¡lido',
        '49' => 'CEP Sem PraÃ§a de CobranÃ§a (NÃ£o Localizado)',
        '50' => 'CEP Referente a um Banco Correspondente',
        '51' => 'CEP incompatÃ­vel com a Unidade da FederaÃ§Ã£o',
        '52' => 'Unidade da FederaÃ§Ã£o InvÃ¡lida',
        '53' => 'Tipo/NÃºmero de InscriÃ§Ã£o do Pagadorr/Avalista InvÃ¡lidos',
        '54' => 'Pagadorr/Avalista NÃ£o Informado',
        '55' => 'Nosso nÃºmero no Banco Correspondente NÃ£o Informado',
        '56' => 'CÃ³digo do Banco Correspondente NÃ£o Informado',
        '57' => 'CÃ³digo da Multa InvÃ¡lido',
        '58' => 'Data da Multa InvÃ¡lida',
        '59' => 'Valor/Percentual da Multa InvÃ¡lido',
        '60' => 'Movimento para TÃ­tulo NÃ£o Cadastrado',
        '61' => 'AlteraÃ§Ã£o da AgÃªncia Cobradora/DV InvÃ¡lida',
        '62' => 'Tipo de ImpressÃ£o InvÃ¡lido',
        '63' => 'Entrada para TÃ­tulo jÃ¡ Cadastrado',
        '64' => 'NÃºmero da Linha InvÃ¡lido',
        '65' => 'CÃ³digo do Banco para DÃ©bito InvÃ¡lido',
        '66' => 'AgÃªncia/Conta/DV para DÃ©bito InvÃ¡lido',
        '67' => 'Dados para DÃ©bito incompatÃ­vel com a IdentificaÃ§Ã£o da EmissÃ£o do Bloqueto',
        '68' => 'DÃ©bito AutomÃ¡tico Agendado',
        '69' => 'DÃ©bito NÃ£o Agendado - Erro nos Dados da Remessa',
        '70' => 'DÃ©bito NÃ£o Agendado - Pagador NÃ£o Consta do Cadastro de Autorizante',
        '71' => 'DÃ©bito NÃ£o Agendado - Beneficiario NÃ£o Autorizado pelo Pagador',
        '72' => 'DÃ©bito NÃ£o Agendado - Beneficiario NÃ£o Participa da Modalidade DÃ©bito AutomÃ¡tico',
        '73' => 'DÃ©bito NÃ£o Agendado - CÃ³digo de Moeda Diferente de Real (R$)',
        '74' => 'DÃ©bito NÃ£o Agendado - Data Vencimento InvÃ¡lida',
        '75' => 'DÃ©bito NÃ£o Agendado, Conforme seu Pedido, TÃ­tulo NÃ£o Registrado',
        '76' => 'DÃ©bito NÃ£o Agendado, Tipo/Num. InscriÃ§Ã£o do Debitado, InvÃ¡lido',
        '77' => 'TransferÃªncia para Desconto NÃ£o Permitida para a Carteira do TÃ­tulo',
        '78' => 'Data Inferior ou Igual ao Vencimento para DÃ©bito AutomÃ¡tico',
        '79' => 'Data Juros de Mora InvÃ¡lido',
        '80' => 'Data do Desconto InvÃ¡lida',
        '81' => 'Tentativas de DÃ©bito Esgotadas - Baixado',
        '82' => 'Tentativas de DÃ©bito Esgotadas - Pendente',
        '83' => 'Limite Excedido',
        '84' => 'NÃºmero AutorizaÃ§Ã£o Inexistente',
        '85' => 'TÃ­tulo com Pagamento Vinculado',
        '86' => 'Seu NÃºmero InvÃ¡lido',
        '87' => 'e-mail/SMS enviado',
        '88' => 'e-mail Lido',
        '89' => 'e-mail/SMS devolvido - endereÃ§o de e-mail ou nÃºmero do celular incorreto',
        '90' => 'e-mail devolvido - caixa postal cheia',
        '91' => 'e-mail/nÃºmero do celular do pagador nÃ£o informado',
        '92' => 'Pagador optante por Bloqueto EletrÃ´nico - e-mail nÃ£o enviado',
        '93' => 'CÃ³digo para emissÃ£o de bloqueto nÃ£o permite envio de e-mail',
        '94' => 'CÃ³digo da Carteira invÃ¡lido para envio e-mail.',
        '95' => 'ntrato nÃ£o permite o envio de e-mail',
        '96' => 'Ãºmero de contrato invÃ¡lido',
        '97' => 'RejeiÃ§Ã£o da alteraÃ§Ã£o do prazo limite de recebimento',
        '98' => 'RejeiÃ§Ã£o de dispensa de prazo limite de recebimento',
        '99' => 'RejeiÃ§Ã£o da alteraÃ§Ã£o do nÃºmero do tÃ­tulo dado pelo beneficiario',
        'A1' => 'RejeiÃ§Ã£o da alteraÃ§Ã£o do nÃºmero controle do participante',
        'A2' => 'RejeiÃ§Ã£o da alteraÃ§Ã£o dos dados do pagador',
        'A3' => 'RejeiÃ§Ã£o da alteraÃ§Ã£o dos dados do pagadorr/avalista',
        'A4' => 'Pagador DDA',
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
            ->setAgencia($this->rem(53, 57, $header))
            ->setAgenciaDv($this->rem(58, 58, $header))
            ->setConta($this->rem(59, 70, $header))
            ->setContaDv($this->rem(71, 71, $header))
//            ->setContaDv($this->rem(72, 72, $header))
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
            ->setAgencia($this->rem(54, 58, $headerLote))
            ->setAgenciaDv($this->rem(59, 59, $headerLote))
            ->setConta($this->rem(60, 71, $headerLote))
            ->setContaDv($this->rem(72, 72, $headerLote))
//            ->setContaDv($this->rem(73, 73, $headerLote))
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
                ->setOcorrenciaDescricao(data_get($this->ocorrencias, $this->detalheAtual()->getOcorrencia(), 'Desconhecida'))
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
                    data_get($this->baixa_liquidacao, $msgAdicional[0], ''),
                    data_get($this->baixa_liquidacao, $msgAdicional[1], ''),
                    data_get($this->baixa_liquidacao, $msgAdicional[2], ''),
                    data_get($this->baixa_liquidacao, $msgAdicional[3], ''),
                    data_get($this->baixa_liquidacao, $msgAdicional[4], '')
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
                    data_get($this->rejeicoes, $msgAdicional[0], ''),
                    data_get($this->rejeicoes, $msgAdicional[1], ''),
                    data_get($this->rejeicoes, $msgAdicional[2], ''),
                    data_get($this->rejeicoes, $msgAdicional[3], ''),
                    data_get($this->rejeicoes, $msgAdicional[4], '')
                );
                $d->setOcorrenciaDescricao($ocorrencia);
                $d->setOcorrenciaTipo($d::OCORRENCIA_BAIXADA);
            } elseif ($d->hasOcorrencia('25')) {
                $this->totais['protestados']++;
                $d->setOcorrenciaTipo($d::OCORRENCIA_PROTESTADA);
            } elseif ($d->hasOcorrencia('33', '38', '40', '41', '42', '43', '49')) {
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

