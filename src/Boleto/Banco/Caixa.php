<?php
namespace Alves\LaravelBoleto\Boleto\Banco;

use Alves\LaravelBoleto\Boleto\AbstractBoleto;
use Alves\LaravelBoleto\CalculoDV;
use Alves\LaravelBoleto\Contracts\Boleto\Boleto as BoletoContract;
use Alves\LaravelBoleto\Util;

class Caixa  extends AbstractBoleto implements BoletoContract
{
    public function __construct(array $params = [])
    {
        parent::__construct($params);
        $this->setCamposObrigatorios('numero', 'agencia', 'carteira', 'codigoCliente');
    }

    /**
     * CÃ³digo do banco
     *
     * @var string
     */
    protected $codigoBanco = self::COD_BANCO_CEF;
    /**
     * Define as carteiras disponÃ­veis para este banco
     *
     * @var array
     */
    protected $carteiras = ['RG'];
    /**
     * EspÃ©cie do documento, coÃ³digo para remessa
     *
     * @var string
     */
    protected $especiesCodigo = [
        'DM' => '01',
        'NP' => '02',
        'DS' => '03',
        'NS' => '05',
        'LC' => '06',
    ];
    /**
     * Codigo do cliente junto ao banco.
     *
     * @var string
     */
    protected $codigoCliente;
    /**
     * Seta o codigo do cliente.
     *
     * @param mixed $codigoCliente
     *
     * @return $this
     */
    public function setCodigoCliente($codigoCliente)
    {
        $this->codigoCliente = $codigoCliente;

        return $this;
    }
    /**
     * Retorna o codigo do cliente.
     *
     * @return string
     */
    public function getCodigoCliente()
    {
        return $this->codigoCliente;
    }
    /**
     * Retorna o codigo do cliente como se fosse a conta
     * ja que a caixa nÃ£o faz uso da conta para nada.
     *
     * @return string
     */
    public function getConta()
    {
        return $this->getCodigoCliente();
    }

    /**
     * Gera o Nosso NÃºmero.
     *
     * @throws \Exception
     * @return string
     */
    protected function gerarNossoNumero()
    {
        $numero_boleto = Util::numberFormatGeral($this->getNumero(), 15);
        $composicao = '1';
        if ($this->getCarteira() == 'SR') {
            $composicao = '2';
        }

        $carteira = $composicao . '4';
        // As 15 prÃ³ximas posiÃ§Ãµes no nosso nÃºmero sÃ£o a critÃ©rio do beneficiÃ¡rio, utilizando o sequencial
        // Depois, calcula-se o cÃ³digo verificador por mÃ³dulo 11
        $numero = $carteira . Util::numberFormatGeral($numero_boleto, 15);
        return $numero;
    }
    /**
     * MÃ©todo que retorna o nosso numero usado no boleto. alguns bancos possuem algumas diferenÃ§as.
     *
     * @return string
     */
    public function getNossoNumeroBoleto()
    {
        return $this->getNossoNumero() . '-' . CalculoDV::cefNossoNumero($this->getNossoNumero());
    }

    /**
     * Na CEF deve retornar agÃªncia (sem o DV) / cÃ³digo beneficiÃ¡rio (com DV)
     * @return [type] [description]
     */
    public function getAgenciaCodigoBeneficiario(){
        return $this->getAgencia() . ' / ' . 
               $this->getCodigoCliente() . '-' . 
               Util::modulo11($this->getCodigoCliente());
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
        $baixaAutomatica = (int) $baixaAutomatica;
        $this->diasBaixaAutomatica = $baixaAutomatica > 0 ? $baixaAutomatica : 0;
        return $this;
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

        $nossoNumero = Util::numberFormatGeral($this->gerarNossoNumero(), 17);
        $beneficiario = Util::numberFormatGeral($this->getCodigoCliente(), 6);

        $campoLivre = $beneficiario . Util::modulo11($beneficiario);
        $campoLivre .= substr($nossoNumero, 2, 3);
        $campoLivre .= substr($nossoNumero, 0, 1);
        $campoLivre .= substr($nossoNumero, 5, 3);
        $campoLivre .= substr($nossoNumero, 1, 1);
        $campoLivre .= substr($nossoNumero, 8, 9);
        $campoLivre .= Util::modulo11($campoLivre);
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
            'agencia' => null,
            'agenciaDv' => null,
            'contaCorrente' => null,
            'contaCorrenteDv' => null,
            'codigoCliente' => substr($campoLivre, 0, 6),
            'carteira' => substr($campoLivre, 10, 1),
            'nossoNumero' => substr($campoLivre, 7, 3) . substr($campoLivre, 11, 3) . substr($campoLivre, 15, 8),
            'nossoNumeroDv' => substr($campoLivre, 23, 1),
            'nossoNumeroFull' => substr($campoLivre, 7, 3) . substr($campoLivre, 11, 3) . substr($campoLivre, 15, 8),
        ];
    }
}

