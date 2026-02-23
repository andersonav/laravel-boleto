<?php
namespace Alves\LaravelBoleto\Boleto\Banco;

use Alves\LaravelBoleto\Boleto\AbstractBoleto;
use Alves\LaravelBoleto\CalculoDV;
use Alves\LaravelBoleto\Contracts\Boleto\Boleto as BoletoContract;
use Alves\LaravelBoleto\Contracts\Boleto\Boleto;
use Alves\LaravelBoleto\Util;

class Bradesco  extends AbstractBoleto implements BoletoContract
{
    /**
     * CÃ³digo do banco
     *
     * @var string
     */
    protected $codigoBanco = Boleto::COD_BANCO_BRADESCO;
    /**
     * Define as carteiras disponÃ­veis para este banco
     * '09' => Com registro | '06' => Sem Registro | '21' => Com Registro - PagÃ¡vel somente no Bradesco | '22' => Sem Registro - PagÃ¡vel somente no Bradesco | '25' => Sem Registro - EmissÃ£o na Internet | '26' => Com Registro - EmissÃ£o na Internet
     *
     * @var array
     */
    protected $carteiras = ['04', '09', '21', '26'];
    /**
     * Trata-se de cÃ³digo utilizado para identificar mensagens especificas ao cedente, sendo
     * que o mesmo consta no cadastro do Banco, quando nÃ£o houver cÃ³digo cadastrado preencher
     * com zeros "000".
     *
     * @var int
     */
    protected $cip = '000';
    /**
     * Variaveis adicionais.
     *
     * @var array
     */
    public $variaveis_adicionais = [
        'cip' => '000',
        'mostra_cip' => true,
    ];
    /**
     * EspÃ©cie do documento, coÃ³digo para remessa
     *
     * @var string
     */
    protected $especiesCodigo = [
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
        'CPR' => '25', //CÃ©dula de Produto Rural,
        'WAR' => '26', //Warrant
        'DAE' => '27', //DÃ­vida Ativa do Estado
        'DAM' => '28', //DÃ­vida Ativa do MunicÃ­pio
        'DAU' => '29', //DÃ­vida Ativa da UniÃ£o
        'EC'  => '30', //Encargos condominiais
        'CC'  => '31', //CC CartÃ£o de CrÃ©dito,
        'BDP' => '32', //BDP - Boleto de Proposta
        'O'   => '99', //Outros,
    ];
    /**
     * Mostrar o endereÃ§o do beneficiÃ¡rio abaixo da razÃ£o e CNPJ na ficha de compensaÃ§Ã£o
     *
     * @var boolean
     */
    protected $mostrarEnderecoFichaCompensacao = true;
    /**
     * Gera o Nosso NÃºmero.
     *
     * @return string
     */
    protected function gerarNossoNumero()
    {
        return Util::numberFormatGeral($this->getNumero(), 11)
            . CalculoDV::bradescoNossoNumero($this->getCarteira(), $this->getNumero());
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
     * MÃ©todo que retorna o nosso numero usado no boleto. alguns bancos possuem algumas diferenÃ§as.
     *
     * @return string
     */
    public function getNossoNumeroBoleto()
    {
        return Util::numberFormatGeral($this->getCarteira(), 2) . ' / ' .  substr_replace($this->getNossoNumero(), '-', -1, 0);
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

        $campoLivre = Util::numberFormatGeral($this->getAgencia(), 4);
        $campoLivre .= Util::numberFormatGeral($this->getCarteira(), 2);
        $campoLivre .= Util::numberFormatGeral($this->getNumero(), 11);
        $campoLivre .= Util::numberFormatGeral($this->getConta(), 7);
        $campoLivre .= '0';

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
            'contaCorrenteDv' => null,
            'agencia' => substr($campoLivre, 0, 4),
            'carteira' => substr($campoLivre, 4, 2),
            'nossoNumero' => substr($campoLivre, 6, 11),
            'nossoNumeroDv' => null,
            'nossoNumeroFull' => substr($campoLivre, 6, 11),
            'contaCorrente' => substr($campoLivre, 17, 7),
        ];
    }

    /**
     * Define o campo CIP do boleto
     *
     * @param  int $cip
     * @return Bradesco
     */
    public function setCip($cip)
    {
        $this->cip = $cip;
        $this->variaveis_adicionais['cip'] = $this->getCip();
        return $this;
    }

    /**
     * Retorna o campo CIP do boleto
     *
     * @return string
     */
    public function getCip()
    {
        return Util::numberFormatGeral($this->cip, 3);
    }
}

