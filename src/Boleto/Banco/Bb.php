<?php
namespace Alves\LaravelBoleto\Boleto\Banco;

use Alves\LaravelBoleto\Boleto\AbstractBoleto;
use Alves\LaravelBoleto\CalculoDV;
use Alves\LaravelBoleto\Contracts\Boleto\Boleto as BoletoContract;
use Alves\LaravelBoleto\Util;

class Bb extends AbstractBoleto implements BoletoContract
{
    public function __construct(array $params = [])
    {
        parent::__construct($params);
        $this->setCamposObrigatorios('numero', 'convenio', 'carteira');
    }
    /**
     * CÃ³digo do banco
     *
     * @var string
     */
    protected $codigoBanco = self::COD_BANCO_BB;
    /**
     * Define as carteiras disponÃ­veis para este banco
     *
     * @var array
     */
    protected $carteiras = ['11', '12', '15', '17', '18', '31', '51'];
    /**
     * EspÃ©cie do documento, coÃ³digo para remessa
     *
     * @var string
     */
    protected $especiesCodigo240 = [
        'CH' => '01', // Cheque
        'DM' => '02', // Duplicata Mercantil	        'DM' => '02', // Duplicata Mercantil
        'DS' => '04', // Duplicata de ServiÃ§o	        'DS' => '04', // Duplicata de ServiÃ§o
        'DR' => '06', // Duplicata Rural	        'DR' => '06', // Duplicata Rural
        'LC' => '07', // Letra de Cambio	        'LC' => '07', // Letra de Cambio
        'NP' => '12', // Nota Provisoria	        'NP' => '12', // Nota Provisoria
        'NS' => '16', // Nota de Seguro	        'NS' => '16', // Nota de Seguro
        'REC' => '17', // Recibo	        'REC' => '17', // Recibo
        'ND' => '19', // Nota de DÃ©bito	        'ND' => '19', // Nota de DÃ©bito
        'AS' => '20', // Apolice de Seguro	        'AS' => '20', // Apolice de Seguro
        'W' => '26', // Warrant	        'W' => '26', // Warrant
        'DAE' => '27', // Divida Ativa de Estado	        'DAE' => '27', // Divida Ativa de Estado
        'DAM' => '28', // Divida Ativa de Municipio	        'DAM' => '28', // Divida Ativa de Municipio
        'DAU' => '29' // Divida Ativa UniÃ£o	        'DAU' => '29' // Divida Ativa UniÃ£o
    ];
    /**
     * EspÃ©cie do documento, coÃ³digo para remessa
     *
     * @var string
     */
    protected $especiesCodigo400 = [
        'DM'  => '01', // Duplicata Mercantil
        'NP'  => '02', // Nota Promissoria
        'NS'  => '03', // Nota de Seguro
        'REC' => '05', // Recibo
        'LC'  => '08', // Letra de Cambio
        'W'   => '09', // Warrant
        'CH'  => '10', // Cheque
        'DS'  => '12', // Duplicata de ServiÃ§o
        'ND'  => '13', // Nota de DÃ©bito
        'AS'  => '15', // Apolice de Seguro
        'DAE' => '25', // Divida Ativa de Estado
        'DAM' => '26', // Divida Ativa de Municipio
        'DAU' => '27'  // Divida Ativa UniÃ£o
    ];
    /**
     * Define o nÃºmero do convÃªnio (4, 6 ou 7 caracteres)
     *
     * @var string
     */
    protected $convenio;
    /**
     * Defgine o numero da variaÃ§Ã£o da carteira.
     *
     * @var string
     */
    protected $variacao_carteira;

    /**
     * Retorna o campo AgÃªncia/BeneficiÃ¡rio do boleto
     *
     * @return string
     */
    public function getAgenciaCodigoBeneficiario()
    {
        $agencia = $this->getAgencia() . '-' . CalculoDV::bbAgencia($this->getAgencia());
        $codigoCliente = $this->getConvenio();

        return $agencia . ' / ' . $codigoCliente;
    }

    /**
     * Define o nÃºmero do convÃªnio. Sempre use string pois a quantidade de caracteres Ã© validada.
     *
     * @param  string $convenio
     * @return Bb
     */
    public function setConvenio($convenio)
    {
        $this->convenio = $convenio;
        return $this;
    }
    /**
     * Retorna o nÃºmero do convÃªnio
     *
     * @return string
     */
    public function getConvenio()
    {
        return $this->convenio;
    }
    /**
     * Define o nÃºmero da variaÃ§Ã£o da carteira, para saber quando utilizar o nosso numero de 17 posiÃ§Ãµes.
     *
     * @param  string $variacao_carteira
     * @return Bb
     */
    public function setVariacaoCarteira($variacao_carteira)
    {
        $this->variacao_carteira = $variacao_carteira;
        return $this;
    }
    /**
     * Retorna o nÃºmero da variacao de carteira
     *
     * @return string
     */
    public function getVariacaoCarteira()
    {
        return $this->variacao_carteira;
    }
    /**
     * Gera o Nosso NÃºmero.
     *
     * @throws \Exception
     * @return string
     */
    protected function gerarNossoNumero()
    {
        $convenio = $this->getConvenio();
        $numero_boleto = $this->getNumero();
        switch (strlen($convenio)) {
        case 4:
            $numero = Util::numberFormatGeral($convenio, 4) . Util::numberFormatGeral($numero_boleto, 7);
            break;
        case 6:
            if (in_array($this->getCarteira(), ['16', '18']) && $this->getVariacaoCarteira() == 17) {
                $numero = Util::numberFormatGeral($numero_boleto, 17);
            } else {
                $numero = Util::numberFormatGeral($convenio, 6) . Util::numberFormatGeral($numero_boleto, 5);
            }
            break;
        case 7:
            $numero = Util::numberFormatGeral($convenio, 7) . Util::numberFormatGeral($numero_boleto, 10);
            break;
        default:
            throw new \Exception('O cÃ³digo do convÃªnio precisa ter 4, 6 ou 7 dÃ­gitos!');
        }
        return $numero;
    }
    /**
     * MÃ©todo que retorna o nosso numero usado no boleto. alguns bancos possuem algumas diferenÃ§as.
     *
     * @return string
     */
    public function getNossoNumeroBoleto()
    {
        $nn = $this->getNossoNumero() . CalculoDV::bbNossoNumero($this->getNossoNumero());
        return strlen($nn) < 17 ? substr_replace($nn, '-', -1, 0) : $nn;
    }
    /**
     * MÃ©todo para gerar o cÃ³digo da posiÃ§Ã£o de 20 a 44
     *
     * @return string
     * @throws \Exception
     */
    protected function getCampoLivre()
    {
        if ($this->campoLivre) {
            return $this->campoLivre;
        }
        $length = strlen($this->getConvenio());
        $nossoNumero = $this->gerarNossoNumero();
        if (strlen($this->getNumero()) > 10) {
            if ($length == 6 && in_array($this->getCarteira(), ['16', '18']) && Util::numberFormatGeral($this->getVariacaoCarteira(), 3) == '017') {
                return $this->campoLivre = Util::numberFormatGeral($this->getConvenio(), 6) . $nossoNumero . '21';
            } else {
                throw new \Exception('SÃ³ Ã© possÃ­vel criar um boleto com mais de 10 dÃ­gitos no nosso nÃºmero quando a carteira Ã© 21 e o convÃªnio possuir 6 dÃ­gitos.');
            }
        }
        switch ($length) {
        case 4:
        case 6:
            return $this->campoLivre = $nossoNumero . Util::numberFormatGeral($this->getAgencia(), 4) . Util::numberFormatGeral($this->getConta(), 8) . Util::numberFormatGeral($this->getCarteira(), 2);
        case 7:
            return $this->campoLivre = '000000' . $nossoNumero . Util::numberFormatGeral($this->getCarteira(), 2);
        }
        throw new \Exception('O cÃ³digo do convÃªnio precisa ter 4, 6 ou 7 dÃ­gitos!');
    }

    /**
     * MÃ©todo onde qualquer boleto deve extender para gerar o cÃ³digo da posiÃ§Ã£o de 20 a 44
     *
     * @param $campoLivre
     *
     * @return array
     */
    public static function parseCampoLivre($campoLivre) {
        $convenio = substr($campoLivre, 0, 6);
        $nossoNumero = substr($campoLivre, 6, 5);
        if ($convenio == '000000') {
            $convenio = substr($campoLivre, 6, 7);
            $nossoNumero = substr($campoLivre, 13, 10);
        }
        if ($convenio == '0000000' && in_array(substr($campoLivre, -2), ['16', '18']) ) {
            $convenio = substr($campoLivre, 0, 4);
            $nossoNumero = substr($campoLivre, 4, 7);
        }
        if ($convenio == '0000000' && !in_array(substr($campoLivre, -2), ['16', '18']) ) {
            $convenio = null;
            $nossoNumero = substr($campoLivre, 0, 17);
        }

        return [
            'codigoCliente' => null,
            'agencia' => null,
            'agenciaDv' => null,
            'contaCorrente' => null,
            'contaCorrenteDv' => null,
            'carteira' => substr($campoLivre, -2),
            'convenio' => $convenio,
            'nossoNumero' => $nossoNumero,
            'nossoNumeroDv' => null,
            'nossoNumeroFull' => $nossoNumero,
        ];
    }
}

