<?php

namespace Alves\LaravelBoleto\Boleto\Banco;

use Alves\LaravelBoleto\Boleto\AbstractBoleto;
use Alves\LaravelBoleto\CalculoDV;
use Alves\LaravelBoleto\Contracts\Boleto\Boleto as BoletoContract;
use Alves\LaravelBoleto\Util;

class Banrisul extends AbstractBoleto implements BoletoContract
{
    /**
     * CÃ³digo do banco
     *
     * @var string
     */
    protected $codigoBanco = self::COD_BANCO_BANRISUL;

    /**
     * Define as carteiras disponÃ­veis para este banco
     * 1 -> CobranÃ§a Simples
     * 2 -> CobranÃ§a Vinculada
     * 3 -> CobranÃ§a Caucionada
     * 4 -> CobranÃ§a em IGPM
     * 5 -> CobranÃ§a Caucionada CGB Especial
     * 6 -> CobranÃ§a Simples Seguradora
     * 7 -> CobranÃ§a em UFIR
     * 8 -> CobranÃ§a em IDTR
     * B -> CobranÃ§a Caucionada CGB Especial
     * C -> CobranÃ§a Vinculada
     * D -> CobranÃ§a CSB
     * E -> CobranÃ§a Caucionada CÃ¢mbio
     * F -> CobranÃ§a Vendor
     * G -> BBH
     * H -> CobranÃ§a Caucionada DÃ³lar
     * I -> CobranÃ§a Caucionada Compror
     * J -> CobranÃ§a Caucionada NPR
     * K -> CobranÃ§a Simples INCC-M
     * M -> CobranÃ§a Partilhada
     * N -> Capital de Giro CGB ICM
     * P -> Capital de Giro CGB ICM
     * R -> Desconto de Duplicata
     * S -> Vendor EletrÃ´nico
     * T -> Leasing
     * U -> CSB e CCB sem registro
     * X -> Vendor BDL
     *
     * @var array
     */
    protected $carteiras = ['1', '2', '3', '4', '5', '6', '7', '8', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'M', 'N', 'P', 'R', 'S', 'T', 'U', 'X'];

    /**
     * EspÃ©cie do documento, cÃ³digo para remessa do CNAB240
     * @var string
     */
    protected $especiesCodigo = [
        'DM'  => '02', //Duplicata Mercantil â€“ Banco emite bloqueto franqueado. Se a posiÃ§Ã£o 61 for igual a 2 o Banco transformarÃ¡ â€œespÃ©cie do tÃ­tuloâ€ para AA
        'DS'  => '04', //Duplicata de ServiÃ§o
        'LC'  => '07', //Letra de CÃ¢mbio
        'NP'  => '12', //Nota PromissÃ³ria
        'CCB' => 'AA', //O Banco nÃ£o emite o bloqueto
        'CD'  => 'AB', //CobranÃ§a Direta
        'CE'  => 'AC', //CobranÃ§a Escritural
        'TT'  => 'AD', //TÃ­tulo de terceiros
    ];

    /**
     * Codigo do cliente junto ao banco.
     *
     * @var string
     */
    protected $codigoCliente;

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
     * Gerar nosso nÃºmero
     *
     * @return string
     */
    protected function gerarNossoNumero()
    {
        $numero_boleto = $this->getNumero();
        $nossoNumero = Util::numberFormatGeral($numero_boleto, 8)
            . CalculoDV::banrisulNossoNumero(Util::numberFormatGeral($numero_boleto, 8));
        return $nossoNumero;
    }
    /**
     * MÃ©todo que retorna o nosso numero usado no boleto. alguns bancos possuem algumas diferenÃ§as.
     *
     * @return string
     */
    public function getNossoNumeroBoleto()
    {
        return substr_replace($this->getNossoNumero(), '-', -2, 0);
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

        $campoLivre = '2';
        $campoLivre .= '1';
        $campoLivre .= Util::numberFormatGeral($this->getCodigoCliente(), 11); //4 digitos da agencia + 7 primeiros digitos pois os ultimos 2 sÃ£o digitos verificadores
        $campoLivre .= Util::numberFormatGeral($this->getNumero(), 8);
        $campoLivre .= '40';
        $campoLivre .= CalculoDV::banrisulDuploDigito(Util::onlyNumbers($campoLivre));

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
            'carteira' => substr($campoLivre, 0, 1),
            'agencia' => substr($campoLivre, 2, 4),
            'contaCorrente' => substr($campoLivre, 6, 7),
            'nossoNumero' => substr($campoLivre, 13, 8),
            'nossoNumeroDv' => null,
            'nossoNumeroFull' => substr($campoLivre, 13, 8),
        ];
    }

    /**
     * Retorna o codigo do cliente.
     *
     * @return mixed
     */
    public function getCodigoCliente()
    {
        return $this->codigoCliente;
    }

    /**
     * Seta o codigo do cliente.
     *
     * @param mixed $codigoCliente
     *
     * @return Banrisul
     */
    public function setCodigoCliente($codigoCliente)
    {
        $this->codigoCliente = $codigoCliente;

        return $this;
    }

    /**
     * Retorna o campo AgÃªncia/BeneficiÃ¡rio do boleto
     *
     * @return string
     */
    public function getAgenciaCodigoBeneficiario()
    {
        $codigoCliente = $this->getCodigoCliente();

        return $codigoCliente;
    }
}

