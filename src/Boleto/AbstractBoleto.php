<?php

namespace Alves\LaravelBoleto\Boleto;

use Carbon\Carbon;
use Alves\LaravelBoleto\Boleto\Render\Html;
use Alves\LaravelBoleto\Boleto\Render\Pdf;
use Alves\LaravelBoleto\Boleto\Render\PdfCaixa;
use Alves\LaravelBoleto\Contracts\Boleto\Boleto;
use Alves\LaravelBoleto\Contracts\Pessoa as PessoaContract;
use Alves\LaravelBoleto\Contracts\Boleto\Boleto as BoletoContract;
use Alves\LaravelBoleto\Util;

/**
 * Class AbstractBoleto
 *
 * @package Alves\LaravelBoleto\Boleto
 */
abstract class AbstractBoleto implements BoletoContract
{
    /**
     * Campos que sÃ£o necessÃ¡rios para o boleto
     *
     * @var array
     */
    private $camposObrigatorios = [
        'numero',
        'agencia',
        'conta',
        'carteira',
    ];

    protected $protectedFields = [
        'nossoNumero',
    ];

    /**
     * CÃ³digo do banco
     *
     * @var string
     */
    protected $codigoBanco;
    /**
     * Moeda
     *
     * @var int
     */
    protected $moeda = 9;
    /**
     * Valor total do boleto
     *
     * @var float
     */
    protected $valor;
    /**
     * Desconto total do boleto
     *
     * @var float
     */
    protected $desconto;
    /**
     * Valor para multa
     *
     * @var float
     */
    protected $multa = 0;
    /**
     * Valor para mora juros
     *
     * @var float
     */
    protected $juros = 0;
    /**
     * Dias apos vencimento do juros
     *
     * @var int
     */
    protected $jurosApos = 0;
    /**
     * Dias para protesto
     *
     * @var integer
     */
    protected $diasProtesto = 0;
    /**
     * Dias para baixa automÃ¡tica
     *
     * @var integer
     */
    protected $diasBaixaAutomatica;
    /**
     * Data do documento
     *
     * @var \Carbon\Carbon
     */
    protected $dataDocumento;
    /**
     * Data de emissÃ£o
     *
     * @var \Carbon\Carbon
     */
    protected $dataProcessamento;
    /**
     * Data de vencimento
     *
     * @var \Carbon\Carbon
     */
    protected $dataVencimento;
    /**
     * Data de limite de desconto
     *
     * @var \Carbon\Carbon
     */
    protected $dataDesconto;
    /**
     * Campo de aceite
     *
     * @var string
     */
    protected $aceite = 'N';
    /**
     * EspÃ©cie do documento, geralmente DM (Duplicata Mercantil)
     *
     * @var string
     */
    protected $especieDoc = 'DM';
    /**
     * EspÃ©cie do documento, coÃ³digo para remessa
     *
     * @var array
     */
    protected $especiesCodigo = [];
    /**
     * NÃºmero do documento
     *
     * @var int
     */
    protected $numeroDocumento;
    /**
     * Define o nÃºmero definido pelo cliente para compor o Nosso NÃºmero
     *
     * @var int
     */
    protected $numero;
    /**
     * Define o nÃºmero definido pelo cliente para controle da remessa
     *
     * @var string
     */
    protected $numeroControle;
    /**
     * Campo de uso do banco no boleto
     *
     * @var string
     */
    protected $usoBanco;
    /**
     * AgÃªncia
     *
     * @var string
     */
    protected $agencia;
    /**
     * DÃ­gito da agÃªncia
     *
     * @var string
     */
    protected $agenciaDv;
    /**
     * Conta
     *
     * @var string
     */
    protected $conta;
    /**
     * DÃ­gito da conta
     *
     * @var string
     */
    protected $contaDv;
    /**
     * Modalidade de cobranÃ§a do cliente, geralmente CobranÃ§a Simples ou Registrada
     *
     * @var string
     */
    protected $carteira;
    /**
     * Define as carteiras disponÃ­veis para cada banco
     *
     * @var array
     */
    protected $carteiras = [];
    /**
     * Define as carteiras disponÃ­veis para cada banco
     *
     * @var array
     */
    protected $carteirasNomes = [];
    /**
     * Entidade beneficiario (quem emite o boleto)
     *
     * @var PessoaContract
     */
    protected $beneficiario;
    /**
     * Entidade pagadora (de quem se cobra o boleto)
     *
     * @var PessoaContract
     */
    protected $pagador;
    /**
     * Entidade sacador avalista
     *
     * @var PessoaContract
     */
    protected $sacadorAvalista;
    /**
     * Array com as linhas do demonstrativo (descriÃ§Ã£o do pagamento)
     *
     * @var array
     */
    protected $descricaoDemonstrativo;
    /**
     * Linha de local de pagamento
     *
     * @var string
     */
    protected $localPagamento = 'PagÃ¡vel em qualquer agÃªncia bancÃ¡ria atÃ© o vencimento.';
    /**
     * Array com as linhas de instruÃ§Ãµes
     *
     * @var array
     */
    protected $instrucoes = ['Pagar atÃ© a data do vencimento.'];
    /**
     * Array com as linhas de instruÃ§Ãµes de impressÃ£o
     *
     * @var array
     */
    protected $instrucoes_impressao = [];
    /**
     * LocalizaÃ§Ã£o do logotipo do banco, referente ao diretÃ³rio de imagens
     *
     * @var string
     */
    protected $logo;
    /**
     * VariÃ¡veis adicionais.
     *
     * @var array
     */
    public $variaveis_adicionais = [];
    /**
     * Cache do campo livre para evitar processamento desnecessÃ¡rio.
     *
     * @var string
     */
    protected $campoLivre;
    /**
     * Cache do nosso numero para evitar processamento desnecessÃ¡rio.
     *
     * @var string
     */
    protected $campoNossoNumero;

    /**
     * Cache da linha digitabel para evitar processamento desnecessÃ¡rio.
     *
     * @var string
     */
    protected $campoLinhaDigitavel;

    /**
     * Cache do codigo de barras para evitar processamento desnecessÃ¡rio.
     *
     * @var string
     */
    protected $campoCodigoBarras;

    /**
     * Status do boleto, se vai criar alterar ou baixa no banco.
     *
     * @var int
     */
    protected $status = BoletoContract::STATUS_REGISTRO;

    /**
     * @var int
     */
    private $status_custom = null;

    /**
     * Mostrar o endereÃ§o do beneficiÃ¡rio abaixo da razÃ£o e CNPJ na ficha de compensaÃ§Ã£o
     *
     * @var boolean
     */
    protected $mostrarEnderecoFichaCompensacao = false;

    /**
     * Construtor
     *
     * @param array $params ParÃ¢metros iniciais para construÃ§Ã£o do objeto
     */
    public function __construct($params = [])
    {
        Util::fillClass($this, $params);
        // Marca a data de emissÃ£o para hoje, caso nÃ£o especificada
        if (!$this->getDataDocumento()) {
            $this->setDataDocumento(new Carbon());
        }
        // Marca a data de processamento para hoje, caso nÃ£o especificada
        if (!$this->getDataProcessamento()) {
            $this->setDataProcessamento(new Carbon());
        }
        // Marca a data de vencimento para daqui a 5 dias, caso nÃ£o especificada
        if (!$this->getDataVencimento()) {
            $this->setDataVencimento(new Carbon(date('Y-m-d', strtotime('+5 days'))));
        }
        // Marca a data de desconto
        if (!$this->getDataDesconto()) {
            $this->setDataDesconto($this->getDataVencimento());
        }
    }

    /**
     * @return array
     */
    public function getProtectedFields()
    {
        return $this->protectedFields;
    }

    /**
     * Seta os campos obrigatÃ³rios
     *
     * @return $this
     */
    protected function setCamposObrigatorios()
    {
        $args = func_get_args();
        $this->camposObrigatorios = [];
        foreach ($args as $arg) {
            $this->addCampoObrigatorio($arg);
        }
        return $this;
    }

    /**
     * Adiciona os campos obrigatÃ³rios
     *
     * @return $this
     */
    protected function addCampoObrigatorio()
    {
        $args = func_get_args();
        foreach ($args as $arg) {
            !is_array($arg) || call_user_func_array([$this, __FUNCTION__], $arg);
            !is_string($arg) || array_push($this->camposObrigatorios, $arg);
        }
        return $this;
    }

    /**
     * Define a agÃªncia
     *
     * @param  string $agencia
     *
     * @return AbstractBoleto
     */
    public function setAgencia($agencia)
    {
        $this->agencia = (string)$agencia;

        return $this;
    }

    /**
     * Retorna a agÃªncia
     *
     * @return string
     */
    public function getAgencia()
    {
        return $this->agencia;
    }

    /**
     * Define o dÃ­gito da agÃªncia
     *
     * @param  string $agenciaDv
     *
     * @return AbstractBoleto
     */
    public function setAgenciaDv($agenciaDv)
    {
        $this->agenciaDv = $agenciaDv;

        return $this;
    }

    /**
     * Retorna o dÃ­gito da agÃªncia
     *
     * @return string
     */
    public function getAgenciaDv()
    {
        return $this->agenciaDv;
    }

    /**
     * Define o cÃ³digo da carteira (Com ou sem registro)
     *
     * @param  string $carteira
     *
     * @return AbstractBoleto
     * @throws \Exception
     */
    public function setCarteira($carteira)
    {
        if (!in_array($carteira, $this->getCarteiras())) {
            throw new \Exception("Carteira nÃ£o disponÃ­vel!");
        }
        $this->carteira = $carteira;

        return $this;
    }

    /**
     * Retorna o cÃ³digo da carteira (Com ou sem registro)
     *
     * @return string
     */
    public function getCarteira()
    {
        return $this->carteira;
    }

    /**
     * Retorna as carteiras disponÃ­veis para este banco
     *
     * @return array
     */
    public function getCarteiras()
    {
        return $this->carteiras;
    }

    /**
     * Define a entidade beneficiario
     *
     * @param $beneficiario
     *
     * @return AbstractBoleto
     * @throws \Exception
     */
    public function setBeneficiario($beneficiario)
    {
        Util::addPessoa($this->beneficiario, $beneficiario);
        return $this;
    }

    /**
     * Retorna a entidade beneficiario
     *
     * @return PessoaContract
     */
    public function getBeneficiario()
    {
        return $this->beneficiario;
    }

    /**
     * Retorna o cÃ³digo do banco
     *
     * @return string
     */
    public function getCodigoBanco()
    {
        return $this->codigoBanco;
    }

    /**
     * Define o nÃºmero da conta
     *
     * @param  string $conta
     *
     * @return AbstractBoleto
     */
    public function setConta($conta)
    {
        $this->conta = (string)$conta;

        return $this;
    }

    /**
     * Retorna o nÃºmero da conta
     *
     * @return string
     */
    public function getConta()
    {
        return $this->conta;
    }

    /**
     * Define o dÃ­gito verificador da conta
     *
     * @param  string $contaDv
     *
     * @return AbstractBoleto
     */
    public function setContaDv($contaDv)
    {
        $this->contaDv = $contaDv;

        return $this;
    }

    /**
     * Retorna o dÃ­gito verificador da conta
     *
     * @return string
     */
    public function getContaDv()
    {
        return $this->contaDv;
    }

    /**
     * Define a data de vencimento
     *
     * @param  \Carbon\Carbon $dataVencimento
     *
     * @return AbstractBoleto
     */
    public function setDataVencimento(Carbon $dataVencimento)
    {
        $this->dataVencimento = $dataVencimento;

        return $this;
    }

    /**
     * Retorna a data de vencimento
     *
     * @return \Carbon\Carbon
     */
    public function getDataVencimento()
    {
        return $this->dataVencimento;
    }

    /**
     * Define a data de limite de desconto
     *
     * @param  \Carbon\Carbon $dataDesconto
     *
     * @return AbstractBoleto
     */
    public function setDataDesconto(Carbon $dataDesconto)
    {
        $this->dataDesconto = $dataDesconto;

        return $this;
    }

    /**
     * Retorna a data de limite de desconto
     *
     * @return \Carbon\Carbon
     */
    public function getDataDesconto()
    {
        return $this->dataDesconto;
    }

    /**
     * Define a data do documento
     *
     * @param  \Carbon\Carbon $dataDocumento
     *
     * @return AbstractBoleto
     */
    public function setDataDocumento(Carbon $dataDocumento)
    {
        $this->dataDocumento = $dataDocumento;

        return $this;
    }

    /**
     * Retorna a data do documento
     *
     * @return \Carbon\Carbon
     */
    public function getDataDocumento()
    {
        return $this->dataDocumento;
    }

    /**
     * Define o campo aceite
     *
     * @param  string $aceite
     *
     * @return AbstractBoleto
     */
    public function setAceite($aceite)
    {
        $this->aceite = $aceite;

        return $this;
    }

    /**
     * Retorna o campo aceite
     *
     * @return string
     */
    public function getAceite()
    {
        return is_numeric($this->aceite) ? ($this->aceite ? 'A' : 'N') : $this->aceite;
    }

    /**
     * Define o campo EspÃ©cie Doc, geralmente DM (Duplicata Mercantil)
     *
     * @param  string $especieDoc
     *
     * @return AbstractBoleto
     */
    public function setEspecieDoc($especieDoc)
    {
        $this->especieDoc = $especieDoc;

        return $this;
    }

    /**
     * Retorna o campo EspÃ©cie Doc, geralmente DM (Duplicata Mercantil)
     *
     * @return string
     */
    public function getEspecieDoc()
    {
        return $this->especieDoc;
    }

    /**
     * Retorna o codigo da EspÃ©cie Doc
     *
     * @param int $default
     * @param int $tipo
     *
     * @return string
     */
    public function getEspecieDocCodigo($default = 99, $tipo = 240)
    {
        if (property_exists($this, 'especiesCodigo240') && $tipo == 240) {
            $especie = $this->especiesCodigo240;
        } elseif(property_exists($this, 'especiesCodigo400') && $tipo == 400) {
            $especie = $this->especiesCodigo400;
        } else {
            $especie = $this->especiesCodigo;
        }
        return key_exists(strtoupper($this->especieDoc), $especie)
            ? $especie[strtoupper($this->getEspecieDoc())]
            : $default;
    }

    /**
     * Define o campo NÃºmero do documento
     *
     * @param  int $numeroDocumento
     *
     * @return AbstractBoleto
     */
    public function setNumeroDocumento($numeroDocumento)
    {
        $this->numeroDocumento = $numeroDocumento;

        return $this;
    }

    /**
     * Retorna o campo NÃºmero do documento
     *
     * @return string
     */
    public function getNumeroDocumento()
    {
        return $this->numeroDocumento;
    }

    /**
     * Define o nÃºmero  definido pelo cliente para compor o nosso nÃºmero
     *
     * @param  int $numero
     *
     * @return AbstractBoleto
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Retorna o nÃºmero definido pelo cliente para compor o nosso nÃºmero
     *
     * @return int
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Define o nÃºmero  definido pelo cliente para controle da remessa
     *
     * @param  string $numeroControle
     *
     * @return AbstractBoleto
     */
    public function setNumeroControle($numeroControle)
    {
        $this->numeroControle = $numeroControle;

        return $this;
    }

    /**
     * Retorna o nÃºmero definido pelo cliente para controle da remessa
     *
     * @return int
     */
    public function getNumeroControle()
    {
        return $this->numeroControle;
    }

    /**
     * Define o campo Uso do banco
     *
     * @param  string $usoBanco
     *
     * @return AbstractBoleto
     */
    public function setUsoBanco($usoBanco)
    {
        $this->usoBanco = $usoBanco;

        return $this;
    }

    /**
     * Retorna o campo Uso do banco
     *
     * @return string
     */
    public function getUsoBanco()
    {
        return $this->usoBanco;
    }

    /**
     * Define a data de geraÃ§Ã£o do boleto
     *
     * @param  \Carbon\Carbon $dataProcessamento
     *
     * @return AbstractBoleto
     */
    public function setDataProcessamento(Carbon $dataProcessamento)
    {
        $this->dataProcessamento = $dataProcessamento;

        return $this;
    }

    /**
     * Retorna a data de geraÃ§Ã£o do boleto
     *
     * @return \Carbon\Carbon
     */
    public function getDataProcessamento()
    {
        return $this->dataProcessamento;
    }

    /**
     * Adiciona uma instruÃ§Ã£o (mÃ¡ximo 5)
     *
     * @param string $instrucao
     *
     * @return AbstractBoleto
     * @throws \Exception
     */
    public function addInstrucao($instrucao)
    {
        if (count($this->getInstrucoes()) > 8) {
            throw new \Exception('Atingido o mÃ¡ximo de 5 instruÃ§Ãµes.');
        }
        array_push($this->instrucoes, $instrucao);

        return $this;
    }

    /**
     * Define um array com instruÃ§Ãµes (mÃ¡ximo 8) para pagamento
     *
     * @param array $instrucoes
     *
     * @return AbstractBoleto
     * @throws \Exception
     */
    public function setInstrucoes(array $instrucoes)
    {
        if (count($instrucoes) > 8) {
            throw new \Exception('MÃ¡ximo de 8 instruÃ§Ãµes.');
        }
        $this->instrucoes = $instrucoes;

        return $this;
    }

    /**
     * Retorna um array com instruÃ§Ãµes (mÃ¡ximo 8) para pagamento
     *
     * @return array
     */
    public function getInstrucoes()
    {
        return array_slice((array)$this->instrucoes + [null, null, null, null, null, null, null, null], 0, 8);
    }

    /**
     * Define um array com instruÃ§Ãµes (mÃ¡ximo 5) para impressao
     *
     * @param array $instrucoes_impressao
     *
     * @return AbstractBoleto
     * @throws \Exception
     */
    public function setInstrucoesImpressao(array $instrucoes_impressao)
    {
        if (count($instrucoes_impressao) > 5) {
            throw new \Exception('MÃ¡ximo de 5 instruÃ§Ãµes.');
        }
        $this->instrucoes_impressao = $instrucoes_impressao;

        return $this;
    }

    /**
     * Retorna um array com instruÃ§Ãµes (mÃ¡ximo 5) para impressÃ£o
     *
     * @return array
     */
    public function getInstrucoesImpressao()
    {
        if (!empty($this->instrucoes_impressao))
            return array_slice((array)$this->instrucoes_impressao + [null, null, null, null, null], 0, 5);
        else
            return [];
    }

    /**
     * Adiciona um demonstrativo (mÃ¡ximo 5)
     *
     * @param string $descricaoDemonstrativo
     *
     * @return AbstractBoleto
     * @throws \Exception
     */
    public function addDescricaoDemonstrativo($descricaoDemonstrativo)
    {
        if (count($this->getDescricaoDemonstrativo()) > 5) {
            throw new \Exception('Atingido o mÃ¡ximo de 5 demonstrativos.');
        }
        array_push($this->descricaoDemonstrativo, $descricaoDemonstrativo);

        return $this;
    }

    /**
     * Define um array com a descriÃ§Ã£o do demonstrativo (mÃ¡ximo 5)
     *
     * @param array $descricaoDemonstrativo
     *
     * @return AbstractBoleto
     * @throws \Exception
     */
    public function setDescricaoDemonstrativo(array $descricaoDemonstrativo)
    {
        if (count($descricaoDemonstrativo) > 5) {
            throw new \Exception('MÃ¡ximo de 5 demonstrativos.');
        }
        $this->descricaoDemonstrativo = $descricaoDemonstrativo;

        return $this;
    }

    /**
     * Retorna um array com a descriÃ§Ã£o do demonstrativo (mÃ¡ximo 5)
     *
     * @return array
     */
    public function getDescricaoDemonstrativo()
    {
        return array_slice((array)$this->descricaoDemonstrativo + [null, null, null, null, null], 0, 5);
    }

    /**
     * Define o local de pagamento do boleto
     *
     * @param  string $localPagamento
     *
     * @return AbstractBoleto
     */
    public function setLocalPagamento($localPagamento)
    {
        $this->localPagamento = $localPagamento;

        return $this;
    }

    /**
     * Retorna o local de pagamento do boleto
     *
     * @return string
     */
    public function getLocalPagamento()
    {
        return $this->localPagamento;
    }

    /**
     * Define a moeda utilizada pelo boleto
     *
     * @param  int $moeda
     *
     * @return AbstractBoleto
     */
    public function setMoeda($moeda)
    {
        $this->moeda = $moeda;

        return $this;
    }

    /**
     * Retorna a moeda utilizada pelo boleto
     *
     * @return int
     */
    public function getMoeda()
    {
        return $this->moeda;
    }

    /**
     * Define o objeto do pagador
     *
     * @param $pagador
     *
     * @return AbstractBoleto
     * @throws \Exception
     */
    public function setPagador($pagador)
    {
        Util::addPessoa($this->pagador, $pagador);
        return $this;
    }

    /**
     * Retorna o objeto do pagador
     *
     * @return PessoaContract
     */
    public function getPagador()
    {
        return $this->pagador;
    }

    /**
     * Define o objeto sacador avalista do boleto
     *
     * @param $sacadorAvalista
     *
     * @return AbstractBoleto
     * @throws \Exception
     */
    public function setSacadorAvalista($sacadorAvalista)
    {
        Util::addPessoa($this->sacadorAvalista, $sacadorAvalista);
        return $this;
    }

    /**
     * Retorna o objeto sacador avalista do boleto
     *
     * @return PessoaContract
     */
    public function getSacadorAvalista()
    {
        return $this->sacadorAvalista;
    }

    /**
     * Define o valor total do boleto (incluindo taxas)
     *
     * @param  string $valor
     *
     * @return AbstractBoleto
     */
    public function setValor($valor)
    {
        $this->valor = Util::nFloat($valor, 2, false);

        return $this;
    }

    /**
     * Retorna o valor total do boleto (incluindo taxas)
     *
     * @return string
     */
    public function getValor()
    {
        return Util::nFloat($this->valor, 2, false);
    }

    /**
     * Define o desconto total do boleto (incluindo taxas)
     *
     * @param  string $desconto
     *
     * @return AbstractBoleto
     */
    public function setDesconto($desconto)
    {
        $this->desconto = Util::nFloat($desconto, 2, false);

        return $this;
    }

    /**
     * Retorna o desconto total do boleto (incluindo taxas)
     *
     * @return string
     */
    public function getDesconto()
    {
        return Util::nFloat($this->desconto, 2, false);
    }

    /**
     * Seta a % de multa
     *
     * @param  float $multa
     *
     * @return AbstractBoleto
     */
    public function setMulta($multa)
    {
        $this->multa = (float)($multa > 0.00 ? $multa : 0.00);

        return $this;
    }

    /**
     * Retorna % de multa
     *
     * @return float
     */
    public function getMulta()
    {
        return $this->multa;
    }

    /**
     * Seta a % de juros
     *
     * @param  float $juros
     *
     * @return AbstractBoleto
     */
    public function setJuros($juros)
    {
        $this->juros = (float)($juros > 0.00 ? $juros : 0.00);

        return $this;
    }

    /**
     * Retorna % juros
     *
     * @return float
     */
    public function getJuros()
    {
        return $this->juros;
    }

    /**
     * Retorna valor mora diÃ¡ria
     *
     * @return float
     */
    public function getMoraDia()
    {
        if ($this->getJuros() <= 0) {
           return 0;
        }
        return Util::percent($this->getValor(), $this->getJuros())/30;
    }

    /**
     * Seta a quantidade de dias apos o vencimento que cobra o juros
     *
     * @param  int $jurosApos
     *
     * @return AbstractBoleto
     */
    public function setJurosApos($jurosApos)
    {
        $jurosApos = (int)$jurosApos;
        $this->jurosApos = $jurosApos > 0 ? $jurosApos : 0;

        return $this;
    }

    /**
     * Retorna a quantidade de dias apos o vencimento que cobrar a juros
     *
     * @return int
     */
    public function getJurosApos()
    {
        return $this->jurosApos ? $this->jurosApos : false;
    }

    /**
     * Seta dias para protesto
     *
     * @param int $diasProtesto
     *
     * @return AbstractBoleto
     * @throws \Exception
     */
    public function setDiasProtesto($diasProtesto)
    {
        if ($this->getDiasBaixaAutomatica() > 0) {
            throw new \Exception('VocÃª deve usar dias de protesto ou dias de baixa, nunca os 2');
        }
        $diasProtesto = (int)$diasProtesto;
        $this->diasProtesto = $diasProtesto > 0 ? $diasProtesto : 0;

        return $this;
    }

    /**
     * Retorna os diasProtesto
     *
     * @param int $default
     *
     * @return int
     */
    public function getDiasProtesto($default = 0)
    {
        return $this->diasProtesto > 0 ? $this->diasProtesto : $default;
    }

    /**
     * Seta dias para baixa automÃ¡tica
     *
     * @param int $baixaAutomatica
     *
     * @return AbstractBoleto
     * @throws \Exception
     */
    public function setDiasBaixaAutomatica($baixaAutomatica)
    {
        $exception = sprintf('O banco %s nÃ£o suporta baixa automÃ¡tica, pode usar tambÃ©m: setDiasProtesto(%s)', basename(get_class($this)), $baixaAutomatica);
        throw new \Exception($exception);
    }

    /**
     * Retorna os diasProtesto
     *
     * @param int $default
     *
     * @return int
     */
    public function getDiasBaixaAutomatica($default = 0)
    {
        return $this->diasBaixaAutomatica > 0 ? $this->diasBaixaAutomatica : $default;
    }

    /**
     * Define a localizaÃ§Ã£o do logotipo
     *
     * @param  string $logo
     *
     * @return AbstractBoleto
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * Retorna a localizaÃ§Ã£o do logotipo
     *
     * @return string
     */
    public function getLogo()
    {
        return $this->logo ? $this->logo : "http://dummyimage.com/300x70/f5/0.png&text=Sem+Logo";
    }

    /**
     * Retorna o logotipo em Base64, pronto para ser inserido na pÃ¡gina
     *
     * @return string
     */
    public function getLogoBase64()
    {
        return 'data:image/' . pathinfo($this->getLogo(), PATHINFO_EXTENSION) .
            ';base64,' . base64_encode(file_get_contents($this->getLogo()));
    }

    /**
     * Retorna a localizaÃ§Ã£o do logotipo do banco relativo Ã  pasta de imagens
     *
     * @return string
     */
    public function getLogoBanco()
    {
        return realpath(__DIR__ . '/../../logos/' . $this->getCodigoBanco() . '.png');
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Marca o boleto para ser alterado no banco
     *
     * @return AbstractBoleto
     */
    public function alterarBoleto()
    {
        $this->status = BoletoContract::STATUS_ALTERACAO;

        return $this;
    }

    /**
     * Marca o boleto para alterar data vecimento no banco
     *
     * @return AbstractBoleto
     */
    public function alterarDataDeVencimento()
    {
        $this->status = BoletoContract::STATUS_ALTERACAO_DATA;

        return $this;
    }

    /**
     * Comandar instruÃ§Ã£o custom
     *
     * @return AbstractBoleto
     */
    public function comandarInstrucao($instrucao)
    {
        $this->status = BoletoContract::STATUS_CUSTOM;
        $this->status_custom = $instrucao;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getComando()
    {
        return $this->status == Boleto::STATUS_CUSTOM ? $this->status_custom : null;
    }

    /**
     * Marca o boleto para ser baixado no banco
     *
     * @return AbstractBoleto
     */
    public function baixarBoleto()
    {
        $this->status = BoletoContract::STATUS_BAIXA;

        return $this;
    }

    /**
     * Retorna o logotipo do banco em Base64, pronto para ser inserido na pÃ¡gina
     *
     * @return string
     */
    public function getLogoBancoBase64()
    {
        return 'data:image/' . pathinfo($this->getLogoBanco(),
                PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($this->getLogoBanco()));
    }

    /**
     * Mostra exception ao erroneamente tentar setar o nosso nÃºmero
     *
     * @throws \Exception
     */
    final public function setNossoNumero()
    {
        throw new \Exception('NÃ£o Ã© possÃ­vel definir o nosso nÃºmero diretamente. Utilize o mÃ©todo setNumero.');
    }

    /**
     * Retorna o Nosso NÃºmero calculado.
     *
     * @return string
     */
    public function getNossoNumero()
    {
        if (empty($this->campoNossoNumero)) {
            return $this->campoNossoNumero = $this->gerarNossoNumero();
        }
        return $this->campoNossoNumero;
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
     * MÃ©todo onde o Boleto deverÃ¡ gerar o Nosso NÃºmero.
     *
     * @return string
     */
    abstract protected function gerarNossoNumero();

    /**
     * MÃ©todo onde qualquer boleto deve extender para gerar o cÃ³digo da posiÃ§Ã£o de 20 a 44
     *
     * @return string
     */
    abstract protected function getCampoLivre();

    /**
     * MÃ©todo que valida se o banco tem todos os campos obrigadotorios preenchidos
     *
     * @return boolean
     */
    public function isValid(&$messages)
    {
        foreach ($this->camposObrigatorios as $campo) {
            $test = call_user_func([$this, 'get' . ucwords($campo)]);
            if ($test === '' || is_null($test)) {
                $messages .= "Campo $campo estÃ¡ em branco";
                return false;
            }
        }
        return true;
    }

    /**
     * Retorna o campo AgÃªncia/BeneficiÃ¡rio do boleto
     *
     * @return string
     */
    public function getAgenciaCodigoBeneficiario()
    {
        $agencia = $this->getAgenciaDv() !== null ? $this->getAgencia() . '-' . $this->getAgenciaDv() : $this->getAgencia();
        $conta = $this->getContaDv() !== null ? $this->getConta() . '-' . $this->getContaDv() : $this->getConta();

        return $agencia . ' / ' . $conta;
    }

    /**
     * Retorna o nome da carteira para impressÃ£o no boleto
     *
     * Caso o nome da carteira a ser impresso no boleto seja diferente do nÃºmero
     * EntÃ£o crie uma variÃ¡vel na classe do banco correspondente $carteirasNomes
     * sendo uma array cujos Ã­ndices sejam os nÃºmeros das carteiras e os valores
     * seus respectivos nomes
     *
     * @return string
     */
    public function getCarteiraNome()
    {
        return isset($this->carteirasNomes[$this->getCarteira()]) ? $this->carteirasNomes[$this->getCarteira()] : $this->getCarteira();
    }

    /**
     * Retorna o codigo de barras
     *
     * @return string
     * @throws \Exception
     */
    public function getCodigoBarras()
    {
        if (!empty($this->campoCodigoBarras)) {
            return $this->campoCodigoBarras;
        }

        if (!$this->isValid($messages)) {
            throw new \Exception('Campos requeridos pelo banco, aparentam estar ausentes ' . $messages);
        }

        $codigo = Util::numberFormatGeral($this->getCodigoBanco(), 3)
            . $this->getMoeda()
            . Util::fatorVencimento($this->getDataVencimento())
            . Util::numberFormatGeral($this->getValor(), 10)
            . $this->getCampoLivre();

        $resto = Util::modulo11($codigo, 2, 9, 0);
        $dv = (in_array($resto, [0, 10, 11])) ? 1 : $resto;

        return $this->campoCodigoBarras = substr($codigo, 0, 4) . $dv . substr($codigo, 4);
    }

    /**
     * Retorna o cÃ³digo do banco com o dÃ­gito verificador
     *
     * @return string
     */
    public function getCodigoBancoComDv()
    {
        $codigoBanco = $this->getCodigoBanco();

        $semX = [BoletoContract::COD_BANCO_CEF];
        $x10 = in_array($codigoBanco, $semX) ? 0 : 'X';

        return $codigoBanco . '-' . Util::modulo11($codigoBanco, 2, 9, 0, $x10);
    }

    /**
     * Retorna a linha digitÃ¡vel do boleto
     *
     * @return string
     * @throws \Exception
     */
    public function getLinhaDigitavel()
    {
        if (!empty($this->campoLinhaDigitavel)) {
            return $this->campoLinhaDigitavel;
        }

        $codigo = $this->getCodigoBarras();

        $s1 = substr($codigo, 0, 4) . substr($codigo, 19, 5);
        $s1 = $s1 . Util::modulo10($s1);
        $s1 = substr_replace($s1, '.', 5, 0);

        $s2 = substr($codigo, 24, 10);
        $s2 = $s2 . Util::modulo10($s2);
        $s2 = substr_replace($s2, '.', 5, 0);

        $s3 = substr($codigo, 34, 10);
        $s3 = $s3 . Util::modulo10($s3);
        $s3 = substr_replace($s3, '.', 5, 0);

        $s4 = substr($codigo, 4, 1);

        $s5 = substr($codigo, 5, 14);

        return $this->campoLinhaDigitavel = sprintf('%s %s %s %s %s', $s1, $s2, $s3, $s4, $s5);
    }

    /**
     * Retorna se a segunda linha contendo o endereÃ§o do beneficiÃ¡rio deve ser exibida na ficha de compensaÃ§Ã£o
     *
     * @return bool
     */
    public function getMostrarEnderecoFichaCompensacao()
    {
        return $this->mostrarEnderecoFichaCompensacao;
    }

    /**
     * Seta se a segunda linha contendo o endereÃ§o do beneficiÃ¡rio deve ser exibida na ficha de compensaÃ§Ã£o
     *
     * @param bool $mostrarEnderecoFichaCompensacao
     */
    public function setMostrarEnderecoFichaCompensacao($mostrarEnderecoFichaCompensacao)
    {
        $this->mostrarEnderecoFichaCompensacao = $mostrarEnderecoFichaCompensacao;
    }

    /**
     * Render PDF
     *
     * @param bool $print
     * @param bool $instrucoes
     *
     * @return string
     * @throws \Exception
     */
     public function renderPDF($print = false, $instrucoes = true)
    {
        if($this->codigoBanco == 104){
           $pdf = new PdfCaixa();
        }else{
           $pdf = new Pdf();
        }
        $pdf->addBoleto($this);
        !$print || $pdf->showPrint();
        $instrucoes || $pdf->hideInstrucoes();

        return $pdf->gerarBoleto('S', null);
    }

    /**
     * Render HTML
     *
     * @param bool $print
     * @param bool $instrucoes
     *
     * @return string
     * @throws \Throwable
     */
    public function renderHTML($print = false, $instrucoes = true)
    {
        $html = new Html();
        $html->addBoleto($this);
        !$print || $html->showPrint();
        $instrucoes || $html->hideInstrucoes();

        return $html->gerarBoleto();
    }

    /**
     * @return $this
     */
    public function copy()
    {
        return clone $this;
    }

    /**
     * On clone clean variables
     */
    function __clone()
    {
        $this->campoLivre = null;
        $this->campoNossoNumero = null;
        $this->campoLinhaDigitavel = null;
        $this->campoCodigoBarras = null;
    }

    /**
     * Return Boleto Array.
     *
     * @return array
     * @throws \Exception
     */
    public function toArray()
    {
        return array_merge(
            [
                'linha_digitavel' => $this->getLinhaDigitavel(),
                'codigo_barras' => $this->getCodigoBarras(),
                'beneficiario' => [
                    'nome' => $this->getBeneficiario()->getNome(),
                    'endereco' => $this->getBeneficiario()->getEndereco(),
                    'bairro' => $this->getBeneficiario()->getBairro(),
                    'cep' => $this->getBeneficiario()->getCep(),
                    'uf' => $this->getBeneficiario()->getUf(),
                    'cidade' => $this->getBeneficiario()->getCidade(),
                    'documento' => $this->getBeneficiario()->getDocumento(),
                    'nome_documento' => $this->getBeneficiario()->getNomeDocumento(),
                    'endereco2' => $this->getBeneficiario()->getCepCidadeUf(),
                    'endereco_completo' => $this->getBeneficiario()->getEnderecoCompleto(),
                ],
                'logo_base64' => $this->getLogoBase64(),
                'logo' => $this->getLogo(),
                'logo_banco_base64' => $this->getLogoBancoBase64(),
                'logo_banco' => $this->getLogoBanco(),
                'codigo_banco' => $this->getCodigoBanco(),
                'codigo_banco_com_dv' => $this->getCodigoBancoComDv(),
                'especie' => 'R$',
                'data_vencimento' => $this->getDataVencimento(),
                'data_processamento' => $this->getDataProcessamento(),
                'data_documento' => $this->getDataDocumento(),
                'data_desconto' => $this->getDataDesconto(),
                'valor' => Util::nReal($this->getValor(), 2, false),
                'desconto' => Util::nReal($this->getDesconto(), 2, false),
                'multa' => Util::nReal($this->getMulta(), 2, false),
                'juros' => Util::nReal($this->getJuros(), 2, false),
                'juros_apos' => $this->getJurosApos(),
                'dias_protesto' => $this->getDiasProtesto(),
                'sacador_avalista' =>
                    $this->getSacadorAvalista()
                        ? [
                        'nome' => $this->getSacadorAvalista()->getNome(),
                        'endereco' => $this->getSacadorAvalista()->getEndereco(),
                        'bairro' => $this->getSacadorAvalista()->getBairro(),
                        'cep' => $this->getSacadorAvalista()->getCep(),
                        'uf' => $this->getSacadorAvalista()->getUf(),
                        'cidade' => $this->getSacadorAvalista()->getCidade(),
                        'documento' => $this->getSacadorAvalista()->getDocumento(),
                        'nome_documento' => $this->getSacadorAvalista()->getNomeDocumento(),
                        'endereco2' => $this->getSacadorAvalista()->getCepCidadeUf(),
						'endereco_completo' => $this->getSacadorAvalista()->getEnderecoCompleto(),
                    ]
                        : [],
                'pagador' => [
                    'nome' => $this->getPagador()->getNome(),
                    'endereco' => $this->getPagador()->getEndereco(),
                    'bairro' => $this->getPagador()->getBairro(),
                    'cep' => $this->getPagador()->getCep(),
                    'uf' => $this->getPagador()->getUf(),
                    'cidade' => $this->getPagador()->getCidade(),
                    'documento' => $this->getPagador()->getDocumento(),
                    'nome_documento' => $this->getPagador()->getNomeDocumento(),
                    'endereco2' => $this->getPagador()->getCepCidadeUf(),
					'endereco_completo' => $this->getPagador()->getEnderecoCompleto(),
                ],
                'demonstrativo' => $this->getDescricaoDemonstrativo(),
                'instrucoes' => $this->getInstrucoes(),
                'instrucoes_impressao' => $this->getInstrucoesImpressao(),
                'local_pagamento' => $this->getLocalPagamento(),
                'numero' => $this->getNumero(),
                'numero_documento' => $this->getNumeroDocumento(),
                'numero_controle' => $this->getNumeroControle(),
                'agencia_codigo_beneficiario' => $this->getAgenciaCodigoBeneficiario(),
                'nosso_numero' => $this->getNossoNumero(),
                'nosso_numero_boleto' => $this->getNossoNumeroBoleto(),
                'especie_doc' => $this->getEspecieDoc(),
                'especie_doc_cod' => $this->getEspecieDocCodigo(),
                'aceite' => $this->getAceite(),
                'carteira' => $this->getCarteira(),
                'carteira_nome' => $this->getCarteiraNome(),
                'uso_banco' => $this->getUsoBanco(),
                'status' => $this->getStatus(),
                'mostrar_endereco_ficha_compensacao' => $this->getMostrarEnderecoFichaCompensacao()
            ], $this->variaveis_adicionais
        );
    }
}

