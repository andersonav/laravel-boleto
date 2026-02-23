<?php
namespace Alves\LaravelBoleto\Boleto\Banco;

use Alves\LaravelBoleto\Boleto\AbstractBoleto;
use Alves\LaravelBoleto\CalculoDV;
use Alves\LaravelBoleto\Contracts\Boleto\Boleto as BoletoContract;
use Alves\LaravelBoleto\Util;

class Bancoob extends AbstractBoleto implements BoletoContract
{
    public function __construct(array $params = [])
    {
        parent::__construct($params);
        $this->addCampoObrigatorio('convenio');
    }

    /**
     * CÃ³digo do banco
     * @var string
     */
    protected $codigoBanco = self::COD_BANCO_BANCOOB;
    /**
     * Define as carteiras disponÃ­veis para este banco
     * @var array
     */
    protected $carteiras = ['1','3'];
    /**
     * EspÃ©cie do documento, cÃ³digo para remessa do CNAB240
     * @var string
     */
    protected $especiesCodigo = [
        //Equivalentes ao CNAB240
        'CH'  => '01', //Cheque
        'DM'  => '02', //Duplicata Mercantil
        'DMI' => '03', //Duplicata Mercantil p/ IndicaÃ§Ã£o
        'DS'  => '04', //Duplicata de ServiÃ§o
        'DSI' => '05', //Duplicata de ServiÃ§o p/ IndicaÃ§Ã£o
        'DR'  => '06', //Duplicata Rural
        'LC'  => '07', //Letra de CÃ¢mbio
        'NCC' => '08', //Nota de CrÃ©dito Comercial
        'NCE' => '09', //Nota de CrÃ©dito a ExportaÃ§Ã£o
        'NCI' => '10', //Nota de CrÃ©dito Industrial
        'NCR' => '11', //Nota de CrÃ©dito Rural
        'NP'  => '12', //Nota PromissÃ³ria
        'NPR' => '13', //Nota PromissÃ³ria Rural
        'TM'  => '14', //Triplicata Mercantil
        'TS'  => '15', //Triplicata de ServiÃ§o
        'NS'  => '16', //Nota de Seguro
        'RC'  => '17', //Recibo
        'FAT' => '18', //Fatura
        'ND'  => '19', //Nota de DÃ©bito
        'AP'  => '20', //ApÃ³lice de Seguro
        'ME'  => '21', //Mensalidade Escolar
        'PC'  => '22', //Parcela de ConsÃ³rcio
        'NF'  => '23', //Nota Fiscal
        'DD'  => '24', //Documento de DÃ­vida
        'CPR' => '25',  //CÃ©dula de Produto Rural,
        'O'   => '99',  //Outros,
        //Equivalente no CNAB400 que nÃ£o existe no CNAB240
        'W'   => '100',  //Warrant CNAB400
    ];
    /**
     * Define o nÃºmero do convÃªnio (4, 6 ou 7 caracteres)
     *
     * @var string
     */
    protected $convenio;
    /**
     * Define o nÃºmero do convÃªnio. Sempre use string pois a quantidade de caracteres Ã© validada.
     *
     * @param  string $convenio
     * @return Bancoob
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
     * Gera o Nosso NÃºmero.
     *
     * @throws \Exception
     * @return string
     */
    protected function gerarNossoNumero()
    {
        return Util::numberFormatGeral($this->getNumero(), 7)
            . CalculoDV::bancoobNossoNumero($this->getAgencia(), $this->getConvenio(), $this->getNumero());
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
     * @throws \Exception
     */
    protected function getCampoLivre()
    {
        if ($this->campoLivre) {
            return $this->campoLivre;
        }

        $nossoNumero = $this->getNossoNumero();

        $campoLivre = Util::numberFormatGeral($this->getCarteira(), 1);
        $campoLivre .= Util::numberFormatGeral($this->getAgencia(), 4);
        $campoLivre .= Util::numberFormatGeral($this->getCarteira(), 2);
        $campoLivre .= Util::numberFormatGeral($this->getConvenio(), 7);
        $campoLivre .= Util::numberFormatGeral($nossoNumero, 8);
        $campoLivre .= Util::numberFormatGeral(1, 3); //Numero da parcela - NÃ£o implementado

        return $this->campoLivre = $campoLivre;
    }

    /**
     * MÃ©todo onde qualquer boleto deve extender para gerar o cÃ³digo da posiÃ§Ã£o de 20 a 44
     *
     * @param $campoLivre
     *
     * @return array
     */
    static public function parseCampoLivre($campoLivre) {
        return [
            'codigoCliente' => null,
            'agenciaDv' => null,
            'contaCorrente' => null,
            'contaCorrenteDv' => null,
            'carteira' => substr($campoLivre, 0, 1),
            'agencia' => substr($campoLivre, 1, 4),
            'modalidade' => substr($campoLivre, 5, 2),
            'convenio' => substr($campoLivre, 7, 7),
            'nossoNumero' => substr($campoLivre, 14, 7),
            'nossoNumeroDv' => substr($campoLivre, 21, 1),
            'nossoNumeroFull' => substr($campoLivre, 14, 8),
            'parcela' => substr($campoLivre, 22, 3),
        ];
    }


    /**
     * AgÃªncia/CÃ³digo do BeneficiÃ¡rio: Informar o prefixo da agÃªncia e o cÃ³digo de associado/cliente.
     * Estes dados constam na planilha "Capa" deste arquivo. O cÃ³digo de cliente nÃ£o deve ser
     * confundido com o nÃºmero da conta corrente, pois sÃ£o cÃ³digos diferentes.
     * @return string
     */
    public function getAgenciaCodigoBeneficiario(){
        return $this->getAgencia() . ' / ' . $this->getConvenio();
    }
    
}

