<?php

namespace Alves\LaravelBoleto\Cnab\Retorno\Cnab240\Banco;

use Alves\LaravelBoleto\Cnab\Retorno\Cnab240\AbstractRetorno;
use Alves\LaravelBoleto\Contracts\Boleto\Boleto as BoletoContract;
use Alves\LaravelBoleto\Contracts\Cnab\RetornoCnab240;
use Alves\LaravelBoleto\Util;

class Itau extends AbstractRetorno implements RetornoCnab240
{
    /**
     * CÃ³digo do banco
     *
     * @var string
     */
    protected $codigoBanco = BoletoContract::COD_BANCO_ITAU;

    /**
     * Array com as ocorrencias do banco;
     *
     * @var array
     */
    private $ocorrencias = [
        '02' => 'Entrada confirmada (com possibilidade de mensagem â€“ nota 23 â€“ tabela 8)',
        '03' => 'Entrada rejeitada (nota 23 - tabela 1)',
        '04' => 'AlteraÃ§Ã£o de dados â€“ nova entrada ou alteraÃ§Ã£o/exclusÃ£oados acatada',
        '05' => 'AlteraÃ§Ã£o de dados â€“ baixa',
        '06' => 'LiquidaÃ§Ã£o normal',
        '08' => 'LiquidaÃ§Ã£o em cartÃ³rio',
        '09' => 'Baixa simples',
        '10' => 'Baixa por ter sido liquidado',
        '11' => 'Em ser (sÃ³ no retorno mensal)',
        '12' => 'Abatimento concedido',
        '13' => 'Abatimento cancelado',
        '14' => 'Vencimento alterado',
        '15' => 'Baixas rejeitadas (nota 23 - tabela 4)',
        '16' => 'InstruÃ§Ãµes rejeitadas (nota 23 â€“ tabela 3)',
        '17' => 'AlteraÃ§Ã£o/exclusÃ£o de dados rejeitada (nota 23 - tabela 2)',
        '18' => 'CobranÃ§a contratual â€“ instruÃ§Ãµes/alteraÃ§Ãµes rejeitadas/pendentes (nota 23 - tabela 5)',
        '19' => 'ConfirmaÃ§Ã£o recebimento de instruÃ§Ã£o de protesto',
        '20' => 'ConfirmaÃ§Ã£o recebimento de instruÃ§Ã£o de sustaÃ§Ã£o de protesto /tarifa',
        '21' => 'ConfirmaÃ§Ã£o recebimento de instruÃ§Ã£o de nÃ£o protestar',
        '23' => 'Protesto enviado a cartÃ³rio/tarifa',
        '24' => 'InstruÃ§Ã£o de protesto sustada (nota 23 - tabela 7)',
        '25' => 'AlegaÃ§Ãµes do pagador (nota 23 - tabela 6)',
        '26' => 'Tarifa de aviso de cobranÃ§a',
        '27' => 'Tarifa de extrato posiÃ§Ã£o (b40x)',
        '28' => 'Tarifa de relaÃ§Ã£o das liquidaÃ§Ãµes',
        '29' => 'Tarifa de manutenÃ§Ã£o de tÃ­tulos vencidos',
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
        '51' => 'Tarifa mensal referente a entradas bancos correspondentes na carteira',
        '52' => 'Tarifa mensal baixas na carteira',
        '53' => 'Tarifa mensal baixas em bancos correspondentes na carteira',
        '54' => 'Tarifa mensal de liquidaÃ§Ãµes na carteira',
        '55' => 'Tarifa mensal de liquidaÃ§Ãµes em bancos correspondentes na carteira',
        '56' => 'Custas de irregularidade',
        '57' => 'InstruÃ§Ã£o cancelada (nota 23 â€“ tabela 8)',
        '60' => 'Entrada rejeitada carnÃª (nota 20 â€“ tabela 1)',
        '61' => 'Tarifa emissÃ£o aviso de movimentaÃ§Ã£o de tÃ­tulos (2154)',
        '62' => 'DÃ©bito mensal de tarifa â€“ aviso de movimentaÃ§Ã£o de tÃ­tulos (2154)',
        '63' => 'TÃ­tulo sustado judicialmente',
        '74' => 'InstruÃ§Ã£o de negativaÃ§Ã£o expressa rejeitada (nota 25 â€“ tabela 3)',
        '75' => 'Confirma o recebimento de instruÃ§Ã£o de entrada em negativaÃ§Ã£o expressa',
        '77' => 'Confirma o recebimento de instruÃ§Ã£o de exclusÃ£o de entrada em negativaÃ§Ã£o expressa',
        '78' => 'Confirma o recebimento de instruÃ§Ã£o de cancelamento da negativaÃ§Ã£o expressa',
        '79' => 'NegativaÃ§Ã£o expressa informacional (nota 25 â€“ tabela 12)',
        '80' => 'ConfirmaÃ§Ã£o de entrada em negativaÃ§Ã£o expressa â€“ tarifa',
        '82' => 'ConfirmaÃ§Ã£o o cancelamento de negativaÃ§Ã£o expressa - tarifa',
        '83' => 'ConfirmaÃ§Ã£o da exclusÃ£o/cancelamento da negativaÃ§Ã£o expressa por liquidaÃ§Ã£o - tarifa',
        '85' => 'Tarifa por boleto (atÃ© 03 envios) cobranÃ§a ativa eletrÃ´nica',
        '86' => 'Tarifa email cobranÃ§a ativa eletrÃ´nica',
        '87' => 'Tarifa sms cobranÃ§a ativa eletrÃ´nica',
        '88' => 'Tarifa mensal por boleto (atÃ© 03 envios) cobranÃ§a ativa eletrÃ´nica',
        '89' => 'Tarifa mensal email cobranÃ§a ativa eletrÃ´nica',
        '90' => 'Tarifa mensal sms cobranÃ§a ativa eletrÃ´nica',
        '91' => 'Tarifa mensal de exclusÃ£o de entrada em negativaÃ§Ã£o expressa',
        '92' => 'Tarifa mensal de cancelamento de negativaÃ§Ã£o expressa',
        '93' => 'Tarifa mensal de exclusÃ£o/cancelamento de negativaÃ§Ã£o expressa por liquidaÃ§Ã£o',
        '94' => 'Confirma recebimento de instruÃ§Ã£o de nÃ£o negativar',
    ];

    /**
     * Array com as possiveis rejeicoes do banco.
     *
     * @var array
     */
    private $rejeicoes = [
        '03' => [
            '03' => 'Ag. cobradora nÃ£o foi possÃ­vel atribuir a agÃªncia pelo cep ou cep invÃ¡lido',
            '04' => 'Estado sigla do estado invÃ¡lida',
            '05' => 'Data vencimento prazo da operaÃ§Ã£o menor que prazo mÃ­nimo ou maior que o mÃ¡ximo',
            '08' => 'Nome do pagador nÃ£o informado ou deslocado',
            '09' => 'AgÃªncia/conta agÃªncia encerrada',
            '10' => 'Logradouro nÃ£o informado ou deslocado',
            '11' => 'Cep cep nÃ£o numÃ©rico',
            '12' => 'Sacador avalista nome nÃ£o informado ou deslocado (bancos correspondentes)',
            '13' => 'Estado/cep cep incompatÃ­vel com a sigla do estado',
            '14' => 'Nosso nÃºmero nosso nÃºmero jÃ¡ registrado no cadastro do banco ou fora da faixa',
            '15' => 'Nosso nÃºmero nosso nÃºmero em duplicidade no mesmo movimento',
            '18' => 'Data de entrada data de entrada invÃ¡lida para operar com esta carteira',
            '19' => 'OcorrÃªncia ocorrÃªncia invÃ¡lida',
            '21' => 'Ag. cobradora carteira nÃ£o aceita depositÃ¡ria correspondente, estado da agÃªncia diferente do estado do pagador, ag. cobradora nÃ£o consta no cadastro ou encerrando',
            '22' => 'Carteira carteira nÃ£o permitida (necessÃ¡rio cadastrar faixa livre)',
            '27' => 'Cnpj inapto cnpj do beneficiÃ¡rio inapto devoluÃ§Ã£o de tÃ­tulo em garantia',
            '29' => 'CÃ³digo empresa categoria da conta invÃ¡lida',
            '31' => 'AgÃªncia/conta conta nÃ£o tem permissÃ£o para protestar (contate seu gerente)',
            '35' => 'Valor do iof iof maior que 5%',
            '36' => 'Qtdade de moeda quantidade de moeda incompatÃ­vel com valor do tÃ­tulo',
            '37' => 'Cnpj/cpf do pagador nÃ£o numÃ©rico ou igual a zeros',
            '42' => 'Nosso nÃºmero nosso nÃºmero fora de faixa',
            '52' => 'Ag. cobradora empresa nÃ£o aceita banco correspondente',
            '53' => 'Ag. cobradora empresa nÃ£o aceita banco correspondente - cobranÃ§a mensagem',
            '54' => 'Data de vencto banco correspondente â€“ tÃ­tulo com vencimento inferior a 15 dias',
            '55' => 'Dep./bco. corresp. cep nÃ£o pertence a depositÃ¡ria informada',
            '56' => 'Dt. vcto./bco. coresp. vencto. superior a 180 dias da data de entrada',
            '57' => 'Data de vencimento cep sÃ³ depositÃ¡ria bco. do brasil com vencto. inferior a 8 dias',
            '60' => 'Abatimento valor do abatimento invÃ¡lido',
            '61' => 'Juros de mora juros de mora maior que o permitido',
            '62' => 'Desconto valor do desconto maior que o valor do tÃ­tulo',
            '63' => 'Desconto de antecipaÃ§Ã£o valor da importÃ¢ncia por dia de desconto (idd) nÃ£o permitido',
            '64' => 'EmissÃ£o do tÃ­tulo data de emissÃ£o do tÃ­tulo invÃ¡lida (vendor)',
            '65' => 'Taxa financto. taxa invÃ¡lida (vendor)',
            '66' => 'Data de vencto.. invalida/fora de prazo de operaÃ§Ã£o (mÃ­nimo ou mÃ¡ximo)',
            '67' => 'Valor/qtidade. valor do tÃ­tulo/quantidade de moeda invÃ¡lido',
            '68' => 'Carteira carteira invÃ¡lida ou nÃ£o cadastrada no intercÃ¢mbio da cobranÃ§a',
            '98' => 'Flash invÃ¡lido registro mensagem sem flash cadastrado ou flash informado diferente do cadastrado',
            '91' => 'Dac dac agÃªncia / conta corrente invÃ¡lido',
            '92' => 'Dac dac agÃªncia/conta/carteira/nosso nÃºmero invÃ¡lido',
            '93' => 'Estado sigla estado invÃ¡lida',
            '94' => 'Estado sigla estado incompatÃ­vel com cep do pagador',
            '95' => 'Cep cep do pagador nÃ£o numÃ©rico ou invÃ¡lido',
            '96' => 'EndereÃ§o endereÃ§o / nome / cidade pagador invÃ¡lido',
        ],
        '15' => [
            '04' => 'Nosso nÃºmero em duplicidade num mesmo movimento',
            '05' => 'SolicitaÃ§Ã£o de baixa para tÃ­tulo jÃ¡ baixado ou liquidado',
            '06' => 'SolicitaÃ§Ã£o de baixa para tÃ­tulo nÃ£o registrado no sistema',
            '07' => 'CobranÃ§a prazo curto - solicitaÃ§Ã£o de baixa p/ tÃ­tulo nÃ£o registrado no sistema',
            '08' => 'SolicitaÃ§Ã£o de baixa para tÃ­tulo em floating',
        ],
        '16' => [
            '01' => 'InstruÃ§Ã£o/ocorrÃªncia nÃ£o existente',
            '03' => 'Conta nÃ£o tem permissÃ£o para protestar (contate seu gerente)',
            '06' => 'Nosso nÃºmero igual a zeros',
            '09' => 'Cnpj/cpf do sacador/avalista invÃ¡lido',
            '14' => 'Registro em duplicidade',
            '15' => 'Cnpj/cpf informado sem nome do sacador/avalista',
            '19' => 'Valor do abatimento maior que 90% do valor do tÃ­tulo',
            '20' => 'Existe sustacao de protesto pendente para o titulo',
            '21' => 'TÃ­tulo nÃ£o registrado no sistema',
            '22' => 'TÃ­tulo baixado ou liquidado',
            '23' => 'InstruÃ§Ã£o nÃ£o aceita',
            '24' => 'InstruÃ§Ã£o incompatÃ­vel - existe instruÃ§Ã£o de protesto para o tÃ­tulo',
            '25' => 'InstruÃ§Ã£o incompatÃ­vel - nÃ£o existe instruÃ§Ã£o de protesto para o tÃ­tulo',
            '26' => 'InstruÃ§Ã£o nÃ£o aceita por jÃ¡ ter sido emitida a ordem de protesto ao cartÃ³rio',
            '27' => 'InstruÃ§Ã£o nÃ£o aceita por nÃ£o ter sido emitida a ordem de protesto ao cartÃ³rio',
            '28' => 'JÃ¡ existe uma mesma instruÃ§Ã£o cadastrada anteriormente para o tÃ­tulo',
            '29' => 'Valor lÃ­quido + valor do abatimento diferente do valor do tÃ­tulo registrado',
            '30' => 'Existe uma instruÃ§Ã£o de nÃ£o protestar ativa para o tÃ­tulo',
            '31' => 'Existe uma ocorrÃªncia do pagador que bloqueia a instruÃ§Ã£o',
            '32' => 'DepositÃ¡ria do tÃ­tulo = 9999 ou carteira nÃ£o aceita protesto',
            '33' => 'AlteraÃ§Ã£o de vencimento igual Ã  registrada no sistema ou que torna o tÃ­tulo vencido',
            '34' => 'InstruÃ§Ã£o de emissÃ£o de aviso de cobranÃ§a para tÃ­tulo vencido antes do vencimento',
            '35' => 'SolicitaÃ§Ã£o de cancelamento de instruÃ§Ã£o inexistente',
            '36' => 'TÃ­tulo sofrendo alteraÃ§Ã£o de controle (agÃªncia/conta/carteira/nosso nÃºmero)',
            '37' => 'InstruÃ§Ã£o nÃ£o permitida para a carteira',
            '40' => 'InstruÃ§Ã£o incompatÃ­vel â€“ nÃ£o existe instruÃ§Ã£o de negativaÃ§Ã£o expressa para o tÃ­tulo',
            '41' => 'InstruÃ§Ã£o nÃ£o permitida â€“ tÃ­tulo jÃ¡ enviado para negativaÃ§Ã£o expressa',
            '42' => 'InstruÃ§Ã£o nÃ£o permitida â€“ tÃ­tulo com negativaÃ§Ã£o expressa concluÃ­da',
            '43' => 'Prazo invÃ¡lido para negativaÃ§Ã£o â€“ mÃ­nimo: 02 dias corridos apÃ³s o vencimento',
            '45' => 'InstruÃ§Ã£o incompatÃ­vel para o mesmo tÃ­tulo nesta data',
            '47' => 'InstruÃ§Ã£o nÃ£o permitida â€“ espÃ©cie invÃ¡lida',
            '48' => 'Dados do pagador invÃ¡lidos (cpf / cnpj / nome)',
            '49' => 'Dados do endereÃ§o do pagador invÃ¡lidos',
            '50' => 'Data de emissÃ£o do tÃ­tulo invÃ¡lida',
            '51' => 'InstruÃ§Ã£o nÃ£o permitida â€“ tÃ­tulo com negativaÃ§Ã£o expressa agendada',
        ],
        '17' => [
            '02' => 'AgÃªncia cobradora invÃ¡lida ou com o mesmo conteÃºdo',
            '04' => 'Sigla do estado invÃ¡lida',
            '05' => 'Data de vencimento invÃ¡lida ou com o mesmo conteÃºdo',
            '06' => 'Valor do tÃ­tulo com outra alteraÃ§Ã£o simultÃ¢nea',
            '08' => 'Nome do pagador com o mesmo conteÃºdo',
            '11' => 'Cep invÃ¡lido',
            '12' => 'NÃºmero inscriÃ§Ã£o invÃ¡lido do sacador avalista',
            '13' => 'Seu nÃºmero com o mesmo conteÃºdo',
            '21' => 'AgÃªncia cobradora nÃ£o consta no cadastro de depositÃ¡ria ou em encerramento',
            '42' => 'AlteraÃ§Ã£o invÃ¡lida para tÃ­tulo vencido',
            '43' => 'AlteraÃ§Ã£o bloqueada â€“ vencimento jÃ¡ alterado',
            '53' => 'InstruÃ§Ã£o com o mesmo conteÃºdo',
            '54' => 'Data vencimento para bancos correspondentes inferior ao aceito pelo banco',
            '55' => 'AlteraÃ§Ãµes iguais para o mesmo controle (agÃªncia/conta/carteira/nosso nÃºmero)',
            '60' => 'Valor de iof â€“ alteraÃ§Ã£o nÃ£o permitida para carteiras de n.s. â€“ moeda variÃ¡vel',
            '61' => 'TÃ­tulo jÃ¡ baixado ou liquidado ou nÃ£o existe tÃ­tulo correspondente no sistema',
            '66' => 'AlteraÃ§Ã£o nÃ£o permitida para carteiras de notas de seguros â€“ moeda variÃ¡vel',
            '67' => 'Nome invÃ¡lido do sacador avalista',
            '72' => 'EndereÃ§o invÃ¡lido â€“ sacador avalista',
            '73' => 'Bairro invÃ¡lido â€“ sacador avalista',
            '74' => 'Cidade invÃ¡lida â€“ sacador avalista',
            '75' => 'Sigla estado invÃ¡lido â€“ sacador avalista',
            '76' => 'Cep invÃ¡lido â€“ sacador avalista',
            '81' => 'AlteraÃ§Ã£o bloqueada - tÃ­tulo com negativaÃ§Ã£o expressa ou protesto',
        ],
        '18' => [
            '16' => 'Abatimento/alteraÃ§Ã£o do valor do tÃ­tulo ou solicitaÃ§Ã£o de baixa bloqueados',
            '40' => 'NÃ£o aprovada devido ao impacto na elegibilidade de garantias',
            '41' => 'Automaticamente rejeitada',
            '42' => 'Confirma recebimento de instruÃ§Ã£o â€“ pendente de anÃ¡lise',
        ]
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
            ->setAgencia($this->rem(54, 57, $header))
            ->setConta($this->rem(66, 70, $header))
            ->setContaDv($this->rem(72, 72, $header))
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
            ->setAgencia($this->rem(55, 58, $headerLote))
            ->setConta($this->rem(67, 71, $headerLote))
            ->setContaDv($this->rem(73, 73, $headerLote))
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
                ->setNossoNumero($this->rem(41, 48, $detalhe))
                ->setCarteira($this->rem(38, 40, $detalhe))
                ->setNumeroDocumento($this->rem(59, 68, $detalhe))
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
            $msgAdicional = str_split(sprintf('%08s', $this->rem(214, 221, $detalhe)), 2) + array_fill(0, 5, '');
            if ($d->hasOcorrencia('06', '08', '10')) {
                $this->totais['liquidados']++;
                $d->setOcorrenciaTipo($d::OCORRENCIA_LIQUIDADA);
            } elseif ($d->hasOcorrencia('02')) {
                $this->totais['entradas']++;
                if(array_search('a4', array_map('strtolower', $msgAdicional)) !== false) {
                    $d->getPagador()->setDda(true);
                }
                $d->setOcorrenciaTipo($d::OCORRENCIA_ENTRADA);
            } elseif ($d->hasOcorrencia('09')) {
                $this->totais['baixados']++;
                $d->setOcorrenciaTipo($d::OCORRENCIA_BAIXADA);
            } elseif ($d->hasOcorrencia('32')) {
                $this->totais['protestados']++;
                $d->setOcorrenciaTipo($d::OCORRENCIA_PROTESTADA);
            } elseif ($d->hasOcorrencia('04')) {
                $this->totais['alterados']++;
                $d->setOcorrenciaTipo($d::OCORRENCIA_ALTERACAO);
            } elseif ($d->hasOcorrencia('03', '15', '16', '17', '18')) {
                $this->totais['erros']++;
                $error = Util::appendStrings(
                    data_get($this->rejeicoes, $msgAdicional[0], ''),
                    data_get($this->rejeicoes, $msgAdicional[1], ''),
                    data_get($this->rejeicoes, $msgAdicional[2], ''),
                    data_get($this->rejeicoes, $msgAdicional[3], '')
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
            ->setQtdRegistroLote((int) $this->rem(18, 23, $trailer))
            ->setQtdTitulosCobrancaSimples((int) $this->rem(24, 29, $trailer))
            ->setValorTotalTitulosCobrancaSimples(Util::nFloat($this->rem(30, 46, $trailer)/100, 2, false))
            ->setQtdTitulosCobrancaVinculada((int) $this->rem(47, 52, $trailer))
            ->setValorTotalTitulosCobrancaVinculada(Util::nFloat($this->rem(53, 69, $trailer)/100, 2, false));

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

