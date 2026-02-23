<?php

namespace Alves\LaravelBoleto\Boleto\Banco;

use Alves\LaravelBoleto\Boleto\AbstractBoleto;
use Alves\LaravelBoleto\CalculoDV;
use Alves\LaravelBoleto\Contracts\Boleto\Boleto as BoletoContract;
use Alves\LaravelBoleto\Util;

class Santander extends AbstractBoleto implements BoletoContract
{
    public function __construct(array $params = [])
    {
        parent::__construct($params);
        $this->setCamposObrigatorios('numero', 'codigoCliente', 'carteira');
    }

    /**
     * CÃ³digo do banco
     *
     * @var string
     */
    protected $codigoBanco = self::COD_BANCO_SANTANDER;
    /**
     * Define as carteiras disponÃ­veis para este banco
     *
     * @var array
     */
    protected $carteiras = ['101', '201'];
    /**
     * EspÃ©cie do documento, cÃ³digo para remessa 240
     *
     * @var string
     */
    protected $especiesCodigo240 = [
        'DM'  => '02',
        'DS'  => '04',
        'LC'  => '07',
        'NP'  => '12',
        'NR'  => '13',
        'RC'  => '17',
        'AP'  => '20',
        'BCC' => '31',
        'BDP' => '32',
        'CH'  => '97',
        'ND'  => '98'
    ];
    /**
     * EspÃ©cie do documento, cÃ³digo para remessa 400
     *
     * @var string
     */
    protected $especiesCodigo400 = [
        'DM'  => '01',
        'NP'  => '02',
        'AP'  => '03',
        'RC'  => '05',
        'DP'  => '06',
        'LC'  => '07',
        'BDP' => '08',
        'BCC' => '19',
    ];
    /**
     * Mostrar o endereÃ§o do beneficiÃ¡rio abaixo da razÃ£o e CNPJ na ficha de compensaÃ§Ã£o
     *
     * @var boolean
     */
    protected $mostrarEnderecoFichaCompensacao = true;
    /**
     * Define os nomes das carteiras para exibiÃ§Ã£o no boleto
     *
     * @var array
     */
    protected $carteirasNomes = [
        '101' => 'CobranÃ§a Simples ECR',
        '102' => 'CobranÃ§a Simples CSR',
        '201' => 'Penhor'
    ];
    /**
     * Define o valor do IOS - Seguradoras (Se 7% informar 7. Limitado a 9%) - Demais clientes usar 0 (zero)
     *
     * @var int
     */
    protected $ios = 0;
    /**
     * Variaveis adicionais.
     *
     * @var array
     */
    public $variaveis_adicionais = [
        'esconde_uso_banco' => true,
    ];

    /**
     * CÃ³digo do cliente.
     *
     * @var int
     */
    protected $codigoCliente;

    /**
     * Retorna o campo AgÃªncia/BeneficiÃ¡rio do boleto
     *
     * @return string
     */
    public function getAgenciaCodigoBeneficiario()
    {
        $agencia = $this->getAgenciaDv() !== null ? $this->getAgencia() . '-' . $this->getAgenciaDv() : $this->getAgencia();
        $codigoCliente = $this->getCodigoCliente();

        return $agencia . ' / ' . $codigoCliente;
    }

    /**
     * Retorna o cÃ³digo da carteira
     * @return string
     */
    public function getCarteiraNumero()
    {
        switch ($this->carteira) {
            case '101':
                $carteira = '5';
                break;
            case '201':
                $carteira = '1';
                break;
            default:
                $carteira = $this->carteira;
                break;
        }

        return $carteira;
    }

    /**
     * Retorna o cÃ³digo do cliente.
     *
     * @return int
     */
    public function getCodigoCliente()
    {
        return $this->codigoCliente;
    }

    /**
     * Define o cÃ³digo do cliente.
     *
     * @param int $codigoCliente
     *
     * @return AbstractBoleto
     */
    public function setCodigoCliente($codigoCliente)
    {
        $this->codigoCliente = $codigoCliente;

        return $this;
    }

    /**
     * Define o cÃ³digo da carteira (Com ou sem registro)
     *
     * @param string $carteira
     * @return AbstractBoleto
     * @throws \Exception
     */
    public function setCarteira($carteira)
    {
        switch ($carteira) {
            case '1':
            case '5':
                $carteira = '101';
                break;
            case '4':
                $carteira = '102';
                break;
        }
        return parent::setCarteira($carteira);
    }

    /**
     * Define o valor do IOS
     *
     * @param int $ios
     */
    public function setIos($ios)
    {
        $this->ios = $ios;
    }

    /**
     * Retorna o atual valor do IOS
     *
     * @return int
     */
    public function getIos()
    {
        return $this->ios;
    }

    /**
     * Seta dias para baixa automÃ¡tica
     *
     * @param int $baixaAutomatica
     *
     * @return $this
     * @throws \Exception
     */
    public function setDiasBaixaAutomatica($baixaAutomatica)
    {
        if ($this->getDiasProtesto() > 0) {
            throw new \Exception('VocÃª deve usar dias de protesto ou dias de baixa, nunca os 2');
        }
        if (!in_array($baixaAutomatica, [15, 30])) {
            throw new \Exception('O Banco Santander so aceita 15 ou 30 dias apÃ³s o vencimento para baixa automÃ¡tica');
        }
        $baixaAutomatica = (int)$baixaAutomatica;
        $this->diasBaixaAutomatica = $baixaAutomatica > 0 ? $baixaAutomatica : 0;
        return $this;
    }

    /**
     * Gera o Nosso NÃºmero.
     *
     * @return string
     */
    protected function gerarNossoNumero()
    {
        $numero_boleto = $this->getNumero();
        return Util::numberFormatGeral($numero_boleto, 12)
            . CalculoDV::santanderNossoNumero($numero_boleto);
    }

    /**
     * MÃ©todo para gerar o cÃ³digo da posiÃ§Ã£o de 20 a 44
     *
     * @return string
     */
    protected function getCampoLivre()
    {
        if ($this->campoLivre) {
            return $this->campoLivre;
        }
        return $this->campoLivre = '9' . Util::numberFormatGeral($this->getCodigoCliente(), 7)
            . Util::numberFormatGeral($this->getNossoNumero(), 13)
            . Util::numberFormatGeral($this->getIos(), 1)
            . Util::numberFormatGeral($this->getCarteira(), 3);
    }

    /**
     * MÃ©todo onde qualquer boleto deve extender para gerar o cÃ³digo da posiÃ§Ã£o de 20 a 44
     *
     * @param $campoLivre
     *
     * @return array
     */
    public static function parseCampoLivre($campoLivre)
    {
        return [
            'convenio'        => null,
            'agencia'         => null,
            'agenciaDv'       => null,
            'contaCorrente'   => null,
            'contaCorrenteDv' => null,
            'codigoCliente'   => substr($campoLivre, 1, 7),
            'nossoNumero'     => substr($campoLivre, 8, 12),
            'nossoNumeroDv'   => substr($campoLivre, 20, 1),
            'nossoNumeroFull' => substr($campoLivre, 8, 13),
            'carteira'        => substr($campoLivre, 22, 3),
        ];
    }
}

