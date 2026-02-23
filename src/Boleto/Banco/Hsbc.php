<?php
namespace Alves\LaravelBoleto\Boleto\Banco;

use Alves\LaravelBoleto\Boleto\AbstractBoleto;
use Alves\LaravelBoleto\Contracts\Boleto\Boleto as BoletoContract;
use Alves\LaravelBoleto\Util;

class Hsbc  extends AbstractBoleto implements BoletoContract
{
    public function __construct(array $params = [])
    {
        parent::__construct($params);
        $this->addCampoObrigatorio('range', 'contaDv');
    }

    /**
     * CÃ³digo do banco
     *
     * @var string
     */
    protected $codigoBanco = self::COD_BANCO_HSBC;
    /**
     * Define as carteiras disponÃ­veis para este banco
     *
     * @var array
     */
    protected $carteiras = ['CSB'];
    /**
     * EspÃ©cie do documento, coÃ³digo para remessa
     *
     * @var string
     */
    protected $especiesCodigo = [
        'DM' => '01',
        'NP' => '02',
        'NS' => '03',
        'REC' => '05',
        'CE' => '09',
        'DS' => '10',
        'PD' => '98',
    ];
    /**
     * CÃ³digo de range de composiÃ§Ã£o do nosso numero.
     *
     * @var string
     */
    protected $range;
    /**
     * EspÃ©cie do documento, geralmente DM (Duplicata Mercantil)
     *
     * @var string
     */
    protected $especieDoc = 'PD';
    /**
     * @return string
     */
    public function getRange()
    {
        return $this->range;
    }
    /**
     * @param string $range
     *
     * @return Hsbc
     */
    public function setRange($range)
    {
        $this->range = $range;

        return $this;
    }
    /**
     * Define o campo EspÃ©cie Doc, HSBC sempre PD
     *
     * @param  string $especieDoc
     * @return AbstractBoleto
     */
    public function setEspecieDoc($especieDoc)
    {
        $this->especieDoc = 'PD';
        return $this;
    }
    /**
     * Retorna o campo AgÃªncia/BeneficiÃ¡rio do boleto
     *
     * @return string
     */
    public function getAgenciaCodigoBeneficiario()
    {
        $agencia = $this->getAgenciaDv() !== null ? $this->getAgencia() . '-' . $this->getAgenciaDv() : $this->getAgencia();

        if ($this->getContaDv() !== null && strlen($this->getContaDv()) == 1) {
            $conta = substr($this->getConta(), 0, -1) . '-' .substr($this->getConta(), -1).$this->getContaDv();
        } elseif ($this->getContaDv() !== null && strlen($this->getContaDv()) == 2) {
            $conta = substr($this->getConta(), 0, -1) . '-' .substr($this->getConta(), -1).$this->getContaDv();
        } else {
            $conta = $this->getConta();
        }

        return $agencia . ' / ' . $conta;
    }
    /**
     * Gera o Nosso NÃºmero.
     *
     * @return string
     */
    protected function gerarNossoNumero()
    {
        $range = Util::numberFormatGeral($this->getRange(), 5);
        $numero_boleto = Util::numberFormatGeral($this->getNumero(), 5);
        $dv = Util::modulo11($range . $numero_boleto, 2, 7);
        return $range . $numero_boleto . $dv;
    }
    /**
     * MÃ©todo que retorna o nosso numero usado no boleto. alguns bancos possuem algumas diferenÃ§as.
     *
     * @return string
     */
    public function getNossoNumeroBoleto()
    {
        return substr_replace($this->getNossoNumero(), '-', -1, 0);
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

        $campoLivre = $this->getNossoNumero();
        $campoLivre .= Util::numberFormatGeral($this->getAgencia(), 4);
        $campoLivre .= Util::numberFormatGeral($this->getConta(), 6);
        $campoLivre .= $this->getContaDv() ? $this->getContaDv() : Util::modulo11(Util::numberFormatGeral($this->getAgencia(), 4) . Util::numberFormatGeral($this->getConta(), 6));
        $campoLivre .= '001';

        return $this->campoLivre = $campoLivre;
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
            'nossoNumero' => substr($campoLivre, 0, 10),
            'nossoNumeroDv' => substr($campoLivre, 10, 1),
            'nossoNumeroFull' => substr($campoLivre, 0, 11),
            'agencia' => substr($campoLivre, 11, 4),
            'contaCorrente' => substr($campoLivre, 15, 6),
            'contaCorrenteDv' => substr($campoLivre, 21, 1),
        ];
    }
}

