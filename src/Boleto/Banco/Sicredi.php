<?php
namespace Alves\LaravelBoleto\Boleto\Banco;

use Alves\LaravelBoleto\Boleto\AbstractBoleto;
use Alves\LaravelBoleto\CalculoDV;
use Alves\LaravelBoleto\Contracts\Boleto\Boleto as BoletoContract;
use Alves\LaravelBoleto\Util;

class Sicredi extends AbstractBoleto implements BoletoContract
{
    public function __construct(array $params = [])
    {
        parent::__construct($params);
        $this->addCampoObrigatorio('byte', 'posto');
    }

    /**
     * Local de pagamento
     *
     * @var string
     */
    protected $localPagamento = 'PagÃ¡vel preferencialmente nas cooperativas de crÃ©dito do sicredi';
    /**
     * CÃ³digo do banco
     *
     * @var string
     */
    protected $codigoBanco = self::COD_BANCO_SICREDI;
    /**
     * Define as carteiras disponÃ­veis para este banco
     *
     * @var array
     */
    protected $carteiras = ['1', '2', '3'];
    /**
     * EspÃ©cie do documento, coÃ³digo para remessa
     *
     * @var string
     */
    protected $especiesCodigo240 = [
        'DMI' => '03', // Duplicata Mercantil por IndicaÃ§Ã£o
        'DM' => '05', // Duplicata Mercantil por IndicaÃ§Ã£o
        'DR' => '06', // Duplicata Rural
        'NP' => '12', // Nota PromissÃ³ria
        'NR' => '13', // Nota PromissÃ³ria Rural
        'NS' => '16', // Nota de Seguros
        'RC' => '17', // Recibo
        'LC' => '07', // Letra de CÃ¢mbio
        'ND' => '19', // Nota de DÃ©bito
        'DSI' => '99', // Duplicata de ServiÃ§o por IndicaÃ§Ã£o
        'OS' => '99', // Outros
    ];
    /**
     * EspÃ©cie do documento, coÃ³digo para remessa
     *
     * @var string
     */
    protected $especiesCodigo400 = [
        'DMI' => 'A', // Duplicata Mercantil por IndicaÃ§Ã£o
        'DM' => 'A', // Duplicata Mercantil por IndicaÃ§Ã£o
        'DR' => 'B', // Duplicata Rural
        'NP' => 'C', // Nota PromissÃ³ria
        'NR' => 'D', // Nota PromissÃ³ria Rural
        'NS' => 'E', // Nota de Seguros
        'RC' => 'G', // Recibo
        'LC' => 'H', // Letra de CÃ¢mbio
        'ND' => 'I', // Nota de DÃ©bito
        'DSI' => 'J', // Duplicata de ServiÃ§o por IndicaÃ§Ã£o
        'OS' => 'K', // Outros
    ];
    /**
     * Se possui registro o boleto (tipo = 1 com registro e 3 sem registro)
     *
     * @var bool
     */
    protected $registro = true;
    /**
     * CÃ³digo do posto do cliente no banco.
     *
     * @var int
     */
    protected $posto;
    /**
     * Byte que compoe o nosso nÃºmero.
     *
     * @var int
     */
    protected $byte = 2;
    /**
     * Define se possui ou nÃ£o registro
     *
     * @param  bool $registro
     * @return $this
     */
    public function setComRegistro($registro)
    {
        $this->registro = $registro;
        return $this;
    }
    /**
     * Retorna se Ã© com registro.
     *
     * @return bool
     */
    public function isComRegistro()
    {
        return $this->registro;
    }
    /**
     * Define o posto do cliente
     *
     * @param  int $posto
     * @return $this
     */
    public function setPosto($posto)
    {
        $this->posto = $posto;
        return $this;
    }
    /**
     * Retorna o posto do cliente
     *
     * @return int
     */
    public function getPosto()
    {
        return $this->posto;
    }

    /**
     * Define o byte
     *
     * @param  int $byte
     *
     * @return $this
     * @throws \Exception
     */
    public function setByte($byte)
    {
        if ($byte > 9) {
            throw new \Exception('O byte deve ser compreendido entre 1 e 9');
        }
        $this->byte = $byte;
        return $this;
    }
    /**
     * Retorna o byte
     *
     * @return int
     */
    public function getByte()
    {
        return $this->byte;
    }
    /**
     * Retorna o campo AgÃªncia/BeneficiÃ¡rio do boleto
     *
     * @return string
     */
    public function getAgenciaCodigoBeneficiario()
    {
        return sprintf('%04s.%02s.%05s', $this->getAgencia(), $this->getPosto(), $this->getConta());
    }
    /**
     * Gera o Nosso NÃºmero.
     *
     * @return string
     */
    protected function gerarNossoNumero()
    {
        $ano = $this->getDataDocumento()->format('y');
        $byte = $this->getByte();
        $numero_boleto = Util::numberFormatGeral($this->getNumero(), 5);
        $nossoNumero = $ano . $byte . $numero_boleto
            . CalculoDV::sicrediNossoNumero($this->getAgencia(), $this->getPosto(), $this->getConta(), $ano, $byte, $numero_boleto);
        return $nossoNumero;
    }
    /**
     * MÃ©todo que retorna o nosso numero usado no boleto. alguns bancos possuem algumas diferenÃ§as.
     *
     * @return string
     */
    public function getNossoNumeroBoleto()
    {
        return Util::maskString($this->getNossoNumero(), '##/######-#');
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

        $campoLivre = $this->isComRegistro() ? '1' : '3';
        $campoLivre .= Util::numberFormatGeral($this->getCarteira(), 1);
        $campoLivre .= $this->getNossoNumero();
        $campoLivre .= Util::numberFormatGeral($this->getAgencia(), 4);
        $campoLivre .= Util::numberFormatGeral($this->getPosto(), 2);
        $campoLivre .= Util::numberFormatGeral($this->getConta(), 5);
        $campoLivre .= '10';
        $campoLivre .= Util::modulo11($campoLivre);

        return $this->campoLivre .= $campoLivre;
    }

    /**
     * MÃ©todo onde qualquer boleto deve extender para gerar o cÃ³digo da posiÃ§Ã£o de 20 a 44
     *
     * @param $campoLivre
     *
     * @return array
     */
    public static function parseCampoLivre($campoLivre) {
        return [
            'convenio' => null,
            'agenciaDv' => null,
            'contaCorrenteDv' => null,
            'codigoCliente' => null,
            'carteira' => substr($campoLivre, 1, 1),
            'nossoNumero' => substr($campoLivre, 2, 8),
            'nossoNumeroDv' => substr($campoLivre, 10, 1),
            'nossoNumeroFull' => substr($campoLivre, 2, 9),
            'agencia' => substr($campoLivre, 11, 4),
            'contaCorrente' => substr($campoLivre, 17, 5),
        ];
    }
}

