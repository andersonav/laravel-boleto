<?php
namespace Alves\LaravelBoleto\Boleto\Banco;

use Alves\LaravelBoleto\Boleto\AbstractBoleto;
use Alves\LaravelBoleto\CalculoDV;
use Alves\LaravelBoleto\Contracts\Boleto\Boleto as BoletoContract;
use Alves\LaravelBoleto\Util;

class Bnb extends AbstractBoleto implements BoletoContract
{
    /**
     * Local de pagamento
     *
     * @var string
     */
    protected $localPagamento = 'PAGÃVEL EM QUALQUER AGÃŠNCIA BANCÃRIA ATÃ‰ O VENCIMENTO';

    /**
     * CÃ³digo do banco
     *
     * @var string
     */
    protected $codigoBanco = self::COD_BANCO_BNB;
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
    protected $carteiras = ['21', '31', '41'];
    /**
     * EspÃ©cie do documento, coÃ³digo para remessa
     *
     * @var string
     */
    protected $especiesCodigo = [
        'DM' => '01',
        'NP' => '02',
        'CH' => '03',
        'CN' => '04',
        'RC' => '05'
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
        $numero_boleto = $this->getNumero();
        return Util::numberFormatGeral($numero_boleto, 7) . CalculoDV::bnbNossoNumero($this->getNumero());
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

        $campoLivre = Util::numberFormatGeral($this->getAgencia(), 4);
        $campoLivre .= Util::numberFormatGeral($this->getConta(), 7);
        $campoLivre .= $this->getContaDv() ?: CalculoDV::bnbContaCorrente($this->getAgencia(), $this->getConta());
        $campoLivre .= $this->getNossoNumero();
        $campoLivre .= Util::numberFormatGeral($this->getCarteira(), 2);
        $campoLivre .= '000';

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
            'codigoCliente' => null,
            'convenio' => null,
            'agenciaDv' => null,
            'agencia' => substr($campoLivre, 0, 4),
            'contaCorrente' => substr($campoLivre, 4, 7),
            'contaCorrenteDv' => substr($campoLivre, 11, 1),
            'nossoNumero' => substr($campoLivre, 12, 7),
            'nossoNumeroDv' => substr($campoLivre, 19, 1),
            'nossoNumeroFull' => substr($campoLivre, 12, 8),
            'carteira' => substr($campoLivre, 20, 2),
        ];
    }
}

