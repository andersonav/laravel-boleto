<?php
namespace Alves\LaravelBoleto\Boleto\Banco;

use Alves\LaravelBoleto\Boleto\AbstractBoleto;
use Alves\LaravelBoleto\CalculoDV;
use Alves\LaravelBoleto\Contracts\Boleto\Boleto as BoletoContract;
use Alves\LaravelBoleto\Util;

class Safra  extends AbstractBoleto implements BoletoContract
{

    /**
     * Local de pagamento
     *
     * @var string
     */
    protected $localPagamento = 'PagÃ¡vel em qualquer Banco';

    /**
     * CÃ³digo do banco
     *
     * @var string
     */
    protected $codigoBanco = self::COD_BANCO_SAFRA;
    
    /**
     * VariÃ¡veis adicionais.
     *
     * @var array
     */
    public $variaveis_adicionais = [
        'carteira_nome' => '',
    ];
    
    /**
     * Define as carteiras disponÃ­veis para este banco
     *
     * @var array
     */
    protected $carteiras = ['1', '2'];
    
    /**
     * EspÃ©cie do documento, coÃ³digo para remessa
     *
     * @var string
     */
    protected $especiesCodigo = [
        'DM'        => '01',
        'NP'        => '02',
        'NS'        => '03',
        'REC'       => '05',
        'DS'        => '09',
        'OUTROS'    => '99',
        '01'        => '01',
        '02'        => '02',
        '03'        => '03',
        '05'        => '05',
        '09'        => '09',
        '1'         => '01',
        '2'         => '02',
        '3'         => '03',
        '5'         => '05',
        '9'         => '09'
    ];
    
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
        $baixaAutomatica = (int) $baixaAutomatica;
        $this->diasBaixaAutomatica = $baixaAutomatica > 0 ? $baixaAutomatica : 0;
        return $this;
    }

    /**
     * Gera o Nosso NÃºmero.
     *
     * @return string
     * @throws \Exception
     */
    protected function gerarNossoNumero()
    {
        return Util::numberFormatGeral($this->getNumero(), 9);
    }
    /**
     * MÃ©todo que retorna o nosso numero usado no boleto. alguns bancos possuem algumas diferenÃ§as.
     *
     * @return string
     */
    public function getNossoNumeroBoleto()
    {
        return $this->getNossoNumero();
    }
    
    /**
     * 
     * @return type
     */
    public function getAgencia() 
    {
        return Util::numberFormatGeral(parent::getAgencia(), 5);
    }
    
    /**
     * 
     * @return type
     */
    public function getConta() 
    {
        return Util::numberFormatGeral(parent::getConta(), 9);
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
        return $this->campoLivre = "7" 
            . Util::numberFormatGeral($this->getAgencia(), 5)
            . Util::numberFormatGeral($this->getConta(), 9)
            . Util::numberFormatGeral($this->getNossoNumero(), 9) 
            . "2";
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
            'codigoCliente' => null,
            'agencia' => substr($campoLivre, 0, 5),
            'contaCorrente' => substr($campoLivre, 5, 9),
            'nossoNumero' => substr($campoLivre, 9, 9)
        ];
    }

    public function getAceite(): string {
        if (parent::getAceite() == 'S' || parent::getAceite() == 'SIM') {
            return 'SIM';
        } else {
            return 'NÃƒO';
        }
    }
    
}

