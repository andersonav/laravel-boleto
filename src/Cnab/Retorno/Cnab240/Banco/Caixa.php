<?php

namespace Alves\LaravelBoleto\Cnab\Retorno\Cnab240\Banco;

use Alves\LaravelBoleto\Cnab\Retorno\Cnab240\AbstractRetorno;
use Alves\LaravelBoleto\Contracts\Boleto\Boleto as BoletoContract;
use Alves\LaravelBoleto\Contracts\Cnab\RetornoCnab240;
use Alves\LaravelBoleto\Util;

class Caixa extends AbstractRetorno implements RetornoCnab240
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
        '01' => 'SolicitaÃ§Ã£o de ImpressÃ£o de TÃ­tulos Confirmada',
        '02' => 'Entrada Confirmada',
        '03' => 'Entrada Rejeitada',
        '04' => 'TransferÃªncia de Carteira/Entrada',
        '05' => 'TransferÃªncia de Carteira/Baixa',
        '06' => 'LiquidaÃ§Ã£o',
        '07' => 'ConfirmaÃ§Ã£o do Recebimento da InstruÃ§Ã£o de Desconto',
        '08' => 'ConfirmaÃ§Ã£o do Recebimento do Cancelamento do Desconto',
        '09' => 'Baixa',
        '12' => 'ConfirmaÃ§Ã£o Recebimento InstruÃ§Ã£o de Abatimento',
        '13' => 'ConfirmaÃ§Ã£o Recebimento InstruÃ§Ã£o de Cancelamento Abatimento',
        '14' => 'ConfirmaÃ§Ã£o Recebimento InstruÃ§Ã£o AlteraÃ§Ã£o de Vencimento',
        '19' => 'ConfirmaÃ§Ã£o Recebimento InstruÃ§Ã£o de Protesto',
        '20' => 'ConfirmaÃ§Ã£o Recebimento InstruÃ§Ã£o de SustaÃ§Ã£o/Cancelamento de Protesto',
        '23' => 'Remessa a CartÃ³rio',
        '24' => 'Retirada de CartÃ³rio',
        '25' => 'Protestado e Baixado (Baixa por Ter Sido Protestado)',
        '26' => 'InstruÃ§Ã£o Rejeitada',
        '27' => 'ConfirmaÃ§Ã£o do Pedido de AlteraÃ§Ã£o de Outros Dados',
        '28' => 'DÃ©bito de Tarifas/Custas',
        '30' => 'AlteraÃ§Ã£o de Dados Rejeitada',
        '35' => 'ConfirmaÃ§Ã£o de InclusÃ£o Banco de Pagador',
        '36' => 'ConfirmaÃ§Ã£o de AlteraÃ§Ã£o Banco de Pagador',
        '37' => 'ConfirmaÃ§Ã£o de ExclusÃ£o Banco de Pagador',
        '38' => 'EmissÃ£o de Boletos de Banco de Pagador',
        '39' => 'ManutenÃ§Ã£o de Pagador Rejeitada',
        '40' => 'Entrada de TÃ­tulo via Banco de Pagador Rejeitada',
        '41' => 'ManutenÃ§Ã£o de Banco de Pagador Rejeitada',
        '44' => 'Estorno de Baixa / LiquidaÃ§Ã£o',
        '45' => 'AlteraÃ§Ã£o de Dados',
        '46' => 'LiquidaÃ§Ã£o On-line',
        '47' => 'Estorno de LiquidaÃ§Ã£o On-line',
        '51' => 'TÃ­tulo DDA reconhecido pelo pagador',
        '52' => 'TÃ­tulo DDA nÃ£o reconhecido pelo pagador',
        '53' => 'TÃ­tulo DDA recusado pela CIP',
        '61' => 'ConfirmaÃ§Ã£o de alteraÃ§Ã£o do valor nominal do tÃ­tulo',
        '62' => 'ConfirmaÃ§Ã£o de alteraÃ§Ã£o do valor/percentual mÃ­nimo/mÃ¡ximo',
    ];

    /**
     * Array com as possiveis descricoes de baixa e liquidacao.
     *
     * @var array
     */
    private $baixa_liquidacao = [
        '02' => 'Casa LotÃ©rica',
        '03' => 'AgÃªncias CAIXA',
        '04' => 'CompensaÃ§Ã£o EletrÃ´nica',
        '05' => 'CompensaÃ§Ã£o Convencional',
        '06' => 'Internet Banking',
        '07' => 'Correspondente BancÃ¡rio',
        '08' => 'Em CartÃ³rio',
        '09' => 'Comandada Banco',
        '10' => 'Comandada Cliente via Arquivo',
        '11' => 'Comandada Cliente On-line',
        '12' => 'Decurso Prazo â€“ Cliente',
        '13' => 'Decurso Prazo â€“ Banco',
        '14' => 'Protestado',
    ];

    /**
     * Array com as possiveis rejeicoes do banco.
     *
     * @var array
     */
    private $rejeicoes = [
        'AA' => 'CÃ³d Desconto Preenchido, Obrig Data e Valor/Perc',
        'AB' => 'Cod Desconto ObrigatÃ³rio p/ CÃ³d Mov = 7',
        'AC' => 'Forma de Cadastramento InvÃ¡lida',
        'AD' => 'Data de Desconto deve estar em Ordem Crescente',
        'AE' => 'Data de Desconto Ã© Posterior a Data de Vencimento',
        'AF' => 'TÃ­tulo nÃ£o estÃ¡ com situaÃ§Ã£o â€œEm Abertoâ€',
        'AG' => 'TÃ­tulo jÃ¡ estÃ¡ Vencido / Vencendo',
        'AH' => 'NÃ£o existe desconto a ser cancelado',
        'AI' => 'Data solicitada p/ Prot/Dev Ã© anterior Ã  data atual',
        'AJ' => 'CÃ³digo do Pagador InvÃ¡lido',
        'AK' => 'NÃºmero da Parcela Invalida ou Fora de Sequencia',
        'AL' => 'Estorno de Envio NÃ£o Permitido',
        'AM' => 'Nosso Numero Fora de Sequencia',
        'A4' => 'Pagador DDA',
        'B2' => 'Valor Nominal do TÃ­tulo Conflitante',
        'CA' => 'AutorizaÃ§Ã£o de pagamento parcial invÃ¡lida',
        'CB' => 'IdentificaÃ§Ã£o do tipo de pagamento invÃ¡lida',
        'CC' => 'Quantidade de pagamentos possÃ­veis invÃ¡lida',
        'CD' => 'Tipo de valor mÃ¡ximo invÃ¡lido',
        'CE' => 'Valor/percentual mÃ¡ximo invÃ¡lido',
        'CF' => 'Tipo de valor mÃ­nimo invÃ¡lido',
        'CG' => 'Valor/percentual mÃ­nimo invÃ¡lido',
        'CH' => 'Segmento Y53 nÃ£o informado',
        'CI' => 'AlteraÃ§Ã£o do valor/percentual mÃ­nimo/mÃ¡ximo invÃ¡lida para o tipo de pagamento do tÃ­tulo',
        'CJ' => 'Valor/percentual mÃ­nimo/mÃ¡ximo igual ao cadastrado',
        'CK' => 'TÃ­tulo autorizado para pagamentos parciais nÃ£o pode ser desautorizado',
        'CL' => 'Quantidade de pagamentos possÃ­veis menor que a quantidade de pagamentos realizados',
        'VA' => 'Arq.Ret.Inexis. P/ Redisp. Nesta Dt/Nro',
        'VB' => 'Registro Duplicado',
        'VC' => 'BeneficiÃ¡rio deve ser padrÃ£o CNAB240',
        'VD' => 'Ident. Banco Pagador InvÃ¡lida',
        'VE' => 'Num Docto Cobr InvÃ¡lido',
        'VF' => 'Vlr/Perc a ser concedido invÃ¡lido',
        'VG' => 'Data de InscriÃ§Ã£o InvÃ¡lida',
        'VH' => 'Data Movto InvÃ¡lida',
        'VI' => 'Data Inicial InvÃ¡lida',
        'VJ' => 'Data Final InvÃ¡lida',
        'VK' => 'Banco de Pagador jÃ¡ cadastrado',
        'VL' => 'BeneficiÃ¡rio nÃ£o cadastrado',
        'VM' => 'NÃºmero de Lote Duplicado',
        'VN' => 'Forma de EmissÃ£o de Boleto InvÃ¡lida',
        'VO' => 'Forma Entrega Boleto InvÃ¡lida p/ EmissÃ£o via Banco',
        'VP' => 'Forma Entrega Boleto Invalida p/ EmissÃ£o via BeneficiÃ¡rio',
        'VQ' => 'OpÃ§Ã£o para Endosso InvÃ¡lida',
        'VR' => 'Tipo de Juros ao MÃªs InvÃ¡lido',
        'VS' => 'Percentual de Juros ao MÃªs InvÃ¡lido',
        'VT' => 'Percentual / Valor de Desconto InvÃ¡lido',
        'VU' => 'Prazo de Desconto InvÃ¡lido',
        'VV' => 'Preencher Somente Percentual ou Valor',
        'VW' => 'Prazo de Multa Invalido',
        'VX' => 'Perc. Desconto tem que estar em ordem decrescente',
        'VY' => 'Valor Desconto tem que estar em ordem decrescente',
        'VZ' => 'Dias/Data desconto tem que estar em ordem decrescente',
        'WA' => 'Vlr Contr p/ aquisiÃ§Ã£o de Bens InvÃ¡lid',
        'WB' => 'Vlr Contr p/ Fundo de Reserva InvÃ¡lid',
        'WC' => 'Vlr Rend. AplicaÃ§Ãµes Financ InvÃ¡lido',
        'WD' => 'Valor Multa/Juros MonetÃ¡rios InvÃ¡lido',
        'WE' => 'Valor PrÃªmios de Seguro InvÃ¡lido',
        'WF' => 'Valor Custas Judiciais InvÃ¡lido',
        'WG' => 'Valor Reembolso de Despesas InvÃ¡lido',
        'WH' => 'Valor Outros InvÃ¡lido',
        'WI' => 'Valor de AquisiÃ§Ã£o de Bens InvÃ¡lido',
        'WJ' => 'Valor Devolvido ao Consorciado InvÃ¡lido',
        'WK' => 'Vlr Desp. Registro de Contrato InvÃ¡lido',
        'WL' => 'Valor de Rendimentos Pagos InvÃ¡lido',
        'WM' => 'Data de DescriÃ§Ã£o InvÃ¡lida',
        'WN' => 'Valor do Seguro InvÃ¡lido',
        'WO' => 'Data de Vencimento InvÃ¡lida',
        'WP' => 'Data de Nascimento InvÃ¡lida',
        'WQ' => 'CPF/CNPJ do Aluno InvÃ¡lido',
        'WR' => 'Data de AvaliaÃ§Ã£o InvÃ¡lida',
        'WS' => 'CPF/CNPJ do LocatÃ¡rio InvÃ¡lido',
        'WT' => 'Literal da Remessa InvÃ¡lida',
        'WU' => 'Tipo de Registro InvÃ¡lido',
        'WV' => 'Modelo InvÃ¡lido',
        'WW' => 'CÃ³digo do Banco de Pagadores InvÃ¡lido',
        'WX' => 'Banco de Pagadores nÃ£o Cadastrado',
        'WY' => 'Qtde dias para Protesto tem que estar entre 2 e 90',
        'WZ' => 'NÃ£o existem Pagadores para este Banco',
        'XA' => 'PreÃ§o UnitÃ¡rio do Produto InvÃ¡lido',
        'XB' => 'PreÃ§o Total do Produto InvÃ¡lido',
        'XC' => 'Valor Atual do Bem InvÃ¡lido',
        'XD' => 'Quantidade de Bens Entregues InvÃ¡lido',
        'XE' => 'Quantidade de Bens DistribuÃ­dos InvÃ¡lido',
        'XF' => 'Quantidade de Bens nÃ£o DistribuÃ­dos InvÃ¡lido',
        'XG' => 'NÃºmero da PrÃ³xima Assembleia InvÃ¡lido',
        'XH' => 'HorÃ¡rio da PrÃ³xima Assembleia InvÃ¡lido',
        'XI' => 'Data da PrÃ³xima Assembleia InvÃ¡lida',
        'XJ' => 'NÃºmero de Ativos InvÃ¡lido',
        'XK' => 'NÃºmero de Desistentes ExcluÃ­dos InvÃ¡lido',
        'XL' => 'NÃºmero de Quitados InvÃ¡lido',
        'XM' => 'NÃºmero de Contemplados InvÃ¡lido',
        'XN' => 'NÃºmero de nÃ£o Contemplados InvÃ¡lido',
        'XO' => 'Data da Ãšltima Assembleia InvÃ¡lida',
        'XP' => 'Quantidade de PrestaÃ§Ãµes InvÃ¡lida',
        'XQ' => 'Data de Vencimento da Parcela InvÃ¡lida',
        'XR' => 'Valor da AmortizaÃ§Ã£o InvÃ¡lida',
        'XS' => 'CÃ³digo do Personalizado InvÃ¡lido',
        'XT' => 'Valor da ContribuiÃ§Ã£o InvÃ¡lida',
        'XU' => 'Percentual da ContribuiÃ§Ã£o InvÃ¡lido',
        'XV' => 'Valor do Fundo de Reserva InvÃ¡lido',
        'XW' => 'NÃºmero Parcela InvÃ¡lido ou Fora de SequÃªncia',
        'XX' => 'Percentual Fundo de Reserva InvÃ¡lido',
        'XY' => 'Prz Desc/Multa Preenchido, Obrigat.Perc. ou Valor',
        'XZ' => 'Valor Taxa de AdministraÃ§Ã£o InvÃ¡lida',
        'YA' => 'Data de Juros InvÃ¡lida ou NÃ£o Informada',
        'YB' => 'Data Desconto InvÃ¡lida ou NÃ£o Informada',
        'YC' => 'E-mail InvÃ¡lido',
        'YD' => 'CÃ³digo de OcorrÃªncia InvÃ¡lido',
        'YE' => 'Pagador jÃ¡ Cadastrado (Banco de Pagadores)',
        'YF' => 'Pagador nÃ£o Cadastrado (Banco de Pagadores)',
        'YG' => 'Remessa Sem Registro Tipo 9',
        'YH' => 'IdentificaÃ§Ã£o da SolicitaÃ§Ã£o InvÃ¡lida',
        'YI' => 'Quantidade Boletos Solicitada InvÃ¡lida',
        'YJ' => 'Trailer do Arquivo nÃ£o Encontrado',
        'YK' => 'Tipo InscriÃ§Ã£o do ResponsÃ¡vel InvÃ¡lido',
        'YL' => 'NÃºmero InscriÃ§Ã£o do ResponsÃ¡vel InvÃ¡lido',
        'YM' => 'Ajuste de Vencimento InvÃ¡lido',
        'YN' => 'Ajuste de EmissÃ£o InvÃ¡lido',
        'YO' => 'CÃ³digo de Modelo InvÃ¡lido',
        'YP' => 'VÃ­a de Entrega InvÃ¡lido',
        'YQ' => 'EspÃ©cie Banco de Pagador InvÃ¡lido',
        'YR' => 'Aceite Banco de Pagador InvÃ¡lido',
        'YS' => 'Pagador jÃ¡ Cadastrado',
        'YT' => 'Pagador nÃ£o Cadastrado',
        'YU' => 'NÃºmero do Telefone InvÃ¡lido',
        'YV' => 'CNPJ do CondomÃ­nio InvÃ¡lido',
        'YW' => 'Indicador de Registro de TÃ­tulo InvÃ¡lido',
        'YX' => 'Valor da Nota InvÃ¡lido',
        'YY' => 'Qtde de dias para DevoluÃ§Ã£o tem que estar entre 1 e 999',
        'YZ' => 'Quantidade de Produtos InvÃ¡lida',
        'ZA' => 'Perc. Taxa de AdministraÃ§Ã£o InvÃ¡lido',
        'ZB' => 'Valor do Seguro InvÃ¡lido',
        'ZC' => 'Percentual do Seguro InvÃ¡lido',
        'ZD' => 'Valor da DiferenÃ§a da Parcela InvÃ¡lido',
        'ZE' => 'Perc. Da DiferenÃ§a da Parcela InvÃ¡lido',
        'ZF' => 'Valor Reajuste do Saldo de Caixa InvÃ¡lido',
        'ZG' => 'Perc. Reajuste do Saldo de Caixa InvÃ¡lido',
        'ZH' => 'Valor Total a Pagar InvÃ¡lido',
        'ZI' => 'Percentual ao Total a Pagar InvÃ¡lido',
        'ZJ' => 'Valor de Outros AcrÃ©scimos InvÃ¡lido',
        'ZK' => 'Perc. De Outros AcrÃ©scimos InvÃ¡lido',
        'ZL' => 'Valor de Outras DeduÃ§Ãµes InvÃ¡lido',
        'ZM' => 'Perc. De Outras DeduÃ§Ãµes InvÃ¡lido',
        'ZN' => 'Valor da ContribuiÃ§Ã£o InvÃ¡lida',
        'ZO' => 'Percentual da ContribuiÃ§Ã£o InvÃ¡lida',
        'ZP' => 'Valor de Juros/Multa InvÃ¡lido',
        'ZQ' => 'Percentual de Juros/Multa InvÃ¡lido',
        'ZR' => 'Valor Cobrado InvÃ¡lido',
        'ZS' => 'Percentual Cobrado InvÃ¡lido',
        'ZT' => 'Valor Disponibilizado em Caixa InvÃ¡lido',
        'ZU' => 'Valor DepÃ³sito BancÃ¡rio InvÃ¡lido',
        'ZV' => 'Valor AplicaÃ§Ãµes Financeiras InvÃ¡lido',
        'ZW' => 'Data/Valor Preenchidos, ObrigatÃ³rio CÃ³digo Desconto',
        'ZX' => 'Valor Cheques em CobranÃ§a InvÃ¡lido',
        'ZY' => 'Desconto c/ valor Fixo, ObrigatÃ³rio Valor do TÃ­tulo',
        'ZZ' => 'CÃ³digo Movimento InvÃ¡lido p/ Segmento Y8',
        '01' => 'CÃ³digo do Banco InvÃ¡lido',
        '02' => 'CÃ³digo do Registro InvÃ¡lido',
        '03' => 'CÃ³digo do Segmento InvÃ¡lido',
        '04' => 'CÃ³digo do Movimento nÃ£o Permitido p/ Carteira',
        '05' => 'CÃ³digo do Movimento InvÃ¡lido',
        '06' => 'Tipo NÃºmero InscriÃ§Ã£o BeneficiÃ¡rio InvÃ¡lido',
        '07' => 'Agencia/Conta/DV InvÃ¡lidos',
        '08' => 'Nosso NÃºmero InvÃ¡lido',
        '09' => 'Nosso NÃºmero Duplicado',
        '10' => 'Carteira InvÃ¡lida',
        '11' => 'Data de GeraÃ§Ã£o InvÃ¡lida',
        '12' => 'Tipo de Documento InvÃ¡lido',
        '13' => 'Identif. Da EmissÃ£o do Boleto InvÃ¡lida',
        '14' => 'Identif. Da DistribuiÃ§Ã£o do Boleto InvÃ¡lida',
        '15' => 'CaracterÃ­sticas CobranÃ§a IncompatÃ­veis',
        '16' => 'Data de Vencimento InvÃ¡lida',
        '17' => 'Data de Vencimento Anterior Ã  Data de EmissÃ£o',
        '18' => 'Vencimento fora do prazo de operaÃ§Ã£o',
        '19' => 'TÃ­tulo a Cargo de Bco Correspondentes c/ Vencto Inferior a XX Dias',
        '20' => 'Valor do TÃ­tulo InvÃ¡lido',
        '21' => 'EspÃ©cie do TÃ­tulo InvÃ¡lida',
        '22' => 'EspÃ©cie do TÃ­tulo NÃ£o Permitida para a Carteira',
        '23' => 'Aceite InvÃ¡lido',
        '24' => 'Data da EmissÃ£o InvÃ¡lida',
        '25' => 'Data da EmissÃ£o Posterior a Data de Entrada',
        '26' => 'CÃ³digo de Juros de Mora InvÃ¡lido',
        '27' => 'Valor/Taxa de Juros de Mora InvÃ¡lido',
        '28' => 'CÃ³digo do Desconto InvÃ¡lido',
        '29' => 'Valor do Desconto Maior ou Igual ao Valor do TÃ­tulo',
        '30' => 'Desconto a Conceder NÃ£o Confere',
        '31' => 'ConcessÃ£o de Desconto - JÃ¡ Existe Desconto Anterior',
        '32' => 'Valor do IOF InvÃ¡lido',
        '33' => 'Valor do Abatimento InvÃ¡lido',
        '34' => 'Valor do Abatimento Maior ou Igual ao Valor do TÃ­tulo',
        '35' => 'Valor Abatimento a Conceder NÃ£o Confere',
        '36' => 'ConcessÃ£o de Abatimento - JÃ¡ Existe Abatimento Anterior',
        '37' => 'CÃ³digo para Protesto InvÃ¡lido',
        '38' => 'Prazo para Protesto InvÃ¡lido',
        '39' => 'Pedido de Protesto NÃ£o Permitido para o TÃ­tulo',
        '40' => 'TÃ­tulo com Ordem de Protesto Emitida',
        '41' => 'Pedido Cancelamento/SustaÃ§Ã£o p/ TÃ­tulos sem InstruÃ§Ã£o Protesto',
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
        '53' => 'Tipo/NÃºmero de InscriÃ§Ã£o do Sacador/Avalista InvÃ¡lidos',
        '54' => 'Sacador/Avalista NÃ£o Informado',
        '55' => 'Nosso nÃºmero no Banco Correspondente NÃ£o Informado',
        '56' => 'CÃ³digo do Banco Correspondente NÃ£o Informado',
        '57' => 'CÃ³digo da Multa InvÃ¡lido',
        '58' => 'Data da Multa InvÃ¡lida',
        '59' => 'Valor/Percentual da Multa InvÃ¡lido',
        '60' => 'Movimento para TÃ­tulo NÃ£o Cadastrado',
        '61' => 'AlteraÃ§Ã£o da AgÃªncia Cobradora/DV InvÃ¡lida',
        '62' => 'Tipo de ImpressÃ£o InvÃ¡lido',
        '63' => 'Entrada para TÃ­tulo jÃ¡ Cadastrado',
        '64' => 'Entrada InvÃ¡lida para CobranÃ§a Caucionada',
        '65' => 'CEP do Pagador nÃ£o encontrado',
        '66' => 'Agencia Cobradora nÃ£o encontrada',
        '67' => 'Agencia BeneficiÃ¡rio nÃ£o encontrada',
        '68' => 'MovimentaÃ§Ã£o invÃ¡lida para tÃ­tulo',
        '69' => 'AlteraÃ§Ã£o de dados invÃ¡lida',
        '70' => 'Apelido do cliente nÃ£o cadastrado',
        '71' => 'Erro na composiÃ§Ã£o do arquivo',
        '72' => 'Lote de serviÃ§o invÃ¡lido',
        '73' => 'CÃ³digo do BeneficiÃ¡rio invÃ¡lido',
        '74' => 'BeneficiÃ¡rio nÃ£o pertencente a CobranÃ§a EletrÃ´nica',
        '75' => 'Nome da Empresa invÃ¡lido',
        '76' => 'Nome do Banco invÃ¡lido',
        '77' => 'CÃ³digo da Remessa invÃ¡lido',
        '78' => 'Data/Hora GeraÃ§Ã£o do arquivo invÃ¡lida',
        '79' => 'NÃºmero Sequencial do arquivo invÃ¡lido',
        '80' => 'VersÃ£o do Lay out do arquivo invÃ¡lido',
        '81' => 'Literal REMESSA-TESTE - VÃ¡lido sÃ³ p/ fase testes',
        '82' => 'Literal REMESSA-TESTE - ObrigatÃ³rio p/ fase testes',
        '83' => 'Tp NÃºmero InscriÃ§Ã£o Empresa invÃ¡lido',
        '84' => 'Tipo de OperaÃ§Ã£o invÃ¡lido',
        '85' => 'Tipo de serviÃ§o invÃ¡lido',
        '86' => 'Forma de lanÃ§amento invÃ¡lido',
        '87' => 'NÃºmero da remessa invÃ¡lido',
        '88' => 'NÃºmero da remessa menor/igual remessa anterior',
        '89' => 'Lote de serviÃ§o divergente',
        '90' => 'NÃºmero sequencial do registro invÃ¡lido',
        '91' => 'Erro seq de segmento do registro detalhe',
        '92' => 'Cod movto divergente entre grupo de segm',
        '93' => 'Qtde registros no lote invÃ¡lido',
        '94' => 'Qtde registros no lote divergente',
        '95' => 'Qtde lotes no arquivo invÃ¡lido',
        '96' => 'Qtde lotes no arquivo divergente',
        '97' => 'Qtde registros no arquivo invÃ¡lido',
        '98' => 'Qtde registros no arquivo divergente',
        '99' => 'CÃ³digo de DDD invÃ¡lido',
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
            ->setCodigoCedente($this->rem(59, 64, $header))
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
            ->setCodigoCedente($this->rem(60, 65, $headerLote))
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
                ->setNossoNumero($this->rem(39, 56, $detalhe))
                ->setCarteira($this->rem(58, 58, $detalhe))
                ->setNumeroDocumento($this->rem(59, 69, $detalhe))
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
            if ($d->hasOcorrencia('06', '46')) {
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
                    data_get($this->baixa_liquidacao, $msgAdicional[0], ''),
                    data_get($this->baixa_liquidacao, $msgAdicional[1], ''),
                    data_get($this->baixa_liquidacao, $msgAdicional[2], ''),
                    data_get($this->baixa_liquidacao, $msgAdicional[3], ''),
                    data_get($this->baixa_liquidacao, $msgAdicional[4], '')
                );
                $d->setOcorrenciaDescricao($ocorrencia);
                $d->setOcorrenciaTipo($d::OCORRENCIA_BAIXADA);
            } elseif ($d->hasOcorrencia('25')) {
                $this->totais['protestados']++;
                $d->setOcorrenciaTipo($d::OCORRENCIA_PROTESTADA);
            } elseif ($d->hasOcorrencia('36', '45', '61', '62')) {
                $this->totais['alterados']++;
                $d->setOcorrenciaTipo($d::OCORRENCIA_ALTERACAO);
            } elseif ($d->hasOcorrencia('03', '26', '30', '39', '40', '41')) {
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
                ->setDataOcorrencia($this->rem(138, 145, $detalhe))
                ->setDataCredito($this->rem(146, 153, $detalhe));


            if(Util::nFloat($this->rem(93, 107, $detalhe)/100, 2, false) > 0 && ($d->getValorRecebido() - Util::nFloat($this->rem(93, 107, $detalhe)/100, 2, false) > 0)){
                $d->setValorTarifa($d->getValorRecebido() - Util::nFloat($this->rem(93, 107, $detalhe)/100, 2, false));
            }
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
            ->setQtdRegistroLote((int) $this->rem(18, 23, $trailer));

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

