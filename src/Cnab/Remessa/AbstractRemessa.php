<?php
namespace Alves\LaravelBoleto\Cnab\Remessa;

use Carbon\Carbon;
use Alves\LaravelBoleto\Util;
use Alves\LaravelBoleto\Contracts\Pessoa as PessoaContract;
use Alves\LaravelBoleto\Contracts\Boleto\Boleto as BoletoContract;

abstract class AbstractRemessa
{

    const HEADER = 'header';
    const HEADER_LOTE = 'header_lote';
    const DETALHE = 'detalhe';
    const TRAILER_LOTE = 'trailer_lote';
    const TRAILER = 'trailer';

    protected $tamanho_linha = false;

    /**
     * Campos que sÃ£o necessÃ¡rios para a remessa
     *
     * @var array
     */
    private $camposObrigatorios = [
        'carteira',
        'agencia',
        'conta',
        'beneficiario',
    ];

    /**
     * @var array
     */
    protected $boletos = [];

    /**
     * CÃ³digo do banco
     *
     * @var string
     */
    protected $codigoBanco;

    /**
     * Contagem dos registros Detalhes
     *
     * @var int
     */
    protected $iRegistros = 0;

    /**
     * Array contendo o cnab.
     *
     * @var array
     */
    protected $aRegistros = [
        self::HEADER  => [],
        self::DETALHE => [],
        self::TRAILER => [],
    ];

    /**
     * Variavel com ponteiro para linha que esta sendo editada.
     *
     * @var
     */
    protected $atual;

    /**
     * Caracter de fim de linha
     *
     * @var string
     */
    protected $fimLinha = "\n";

    /**
     * Caracter de fim de arquivo
     *
     * @var null
     */
    protected $fimArquivo = null;

    /**
     * ID do arquivo remessa, sequencial.
     *
     * @var
     */
    protected $idremessa;
    /**
     * A data que serÃ¡ informada no header da remessa
     *
     * @var Carbon;
     */
    protected $dataRemessa = null;
    /**
     * AgÃªncia
     *
     * @var int
     */
    protected $agencia;
    /**
     * Conta
     *
     * @var int
     */
    protected $conta;
    /**
     * DÃ­gito da conta
     *
     * @var int
     */
    protected $contaDv;
    /**
     * Carteira de cobranÃ§a.
     *
     * @var
     */
    protected $carteira;
    /**
     * Define as carteiras disponÃ­veis para cada banco
     *
     * @var array
     */
    protected $carteiras = [];

    /**
     * Entidade beneficiario (quem esta gerando a remessa)
     *
     * @var PessoaContract
     */
    protected $beneficiario;

    /**
     * Construtor
     *
     * @param array $params ParÃ¢metros iniciais para construÃ§Ã£o do objeto
     */
    public function __construct($params = [])
    {
        Util::fillClass($this, $params);
    }

    /**
     * Informa a data da remessa a ser gerada
     *
     * @param $data
     */
    public function setDataRemessa($data){
        $this->dataRemessa = $data;
    }

    /**
     * Retorna a data da remessa a ser gerada
     *
     * @param $format
     *
     * @return string;
     */
    public function getDataRemessa($format){
        if(is_null($this->dataRemessa)){
            return Carbon::now()->format($format);
        }
        return $this->dataRemessa->format($format);
    }
    /**
     * Seta os campos obrigatÃ³rios
     *
     * @return $this
     */
    protected function setCamposObrigatorios()
    {
        $args                     = func_get_args();
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
            ! is_array($arg) || call_user_func_array([$this, __FUNCTION__], $arg);
            ! is_string($arg) || array_push($this->camposObrigatorios, $arg);
        }

        return $this;
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
     * @return mixed
     */
    public function getIdremessa()
    {
        return $this->idremessa;
    }

    /**
     * @param mixed $idremessa
     *
     * @return AbstractRemessa
     */
    public function setIdremessa($idremessa)
    {
        $this->idremessa = $idremessa;

        return $this;
    }

    /**
     * @return PessoaContract
     */
    public function getBeneficiario()
    {
        return $this->beneficiario;
    }

    /**
     * @param $beneficiario
     *
     * @return AbstractRemessa
     * @throws \Exception
     */
    public function setBeneficiario($beneficiario)
    {
        Util::addPessoa($this->beneficiario, $beneficiario);

        return $this;
    }

    /**
     * Define a agÃªncia
     *
     * @param  int $agencia
     *
     * @return AbstractRemessa
     */
    public function setAgencia($agencia)
    {
        $this->agencia = (string) $agencia;

        return $this;
    }

    /**
     * Retorna a agÃªncia
     *
     * @return int
     */
    public function getAgencia()
    {
        return $this->agencia;
    }

    /**
     * Define o nÃºmero da conta
     *
     * @param  int $conta
     *
     * @return AbstractRemessa
     */
    public function setConta($conta)
    {
        $this->conta = (string) $conta;

        return $this;
    }

    /**
     * Retorna o nÃºmero da conta
     *
     * @return int
     */
    public function getConta()
    {
        return $this->conta;
    }

    /**
     * Define o dÃ­gito verificador da conta
     *
     * @param  int $contaDv
     *
     * @return AbstractRemessa
     */
    public function setContaDv($contaDv)
    {
        $this->contaDv = substr($contaDv, - 1);

        return $this;
    }

    /**
     * Retorna o dÃ­gito verificador da conta
     *
     * @return int
     */
    public function getContaDv()
    {
        return $this->contaDv;
    }

    /**
     * Define o cÃ³digo da carteira (Com ou sem registro)
     *
     * @param  string $carteira
     *
     * @return AbstractRemessa
     * @throws \Exception
     */
    public function setCarteira($carteira)
    {
        if (! in_array($carteira, $this->getCarteiras())) {
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
     * Retorna o cÃ³digo da carteira (Com ou sem registro)
     *
     * @return string
     */
    public function getCarteiraNumero()
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
     * MÃ©todo que valida se o banco tem todos os campos obrigadotorios preenchidos
     *
     * @param $messages
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
     * FunÃ§Ã£o para gerar o cabeÃ§alho do arquivo.
     *
     * @return mixed
     */
    abstract protected function header();

    /**
     * FunÃ§Ã£o para adicionar detalhe ao arquivo.
     *
     * @param BoletoContract $detalhe
     *
     * @return mixed
     */
    abstract public function addBoleto(BoletoContract $detalhe);

    /**
     * FunÃ§Ã£o que gera o trailer (footer) do arquivo.
     *
     * @return mixed
     */
    abstract protected function trailer();

    /**
     * FunÃ§Ã£o para adicionar multiplos boletos.
     *
     * @param array $boletos
     *
     * @return $this
     */
    public function addBoletos(array $boletos)
    {
        foreach ($boletos as $boleto) {
            $this->addBoleto($boleto);
        }

        return $this;
    }

    /**
     * FunÃ§Ã£o para add valor a linha nas posiÃ§Ãµes informadas.
     *
     * @param integer $i
     * @param integer $f
     * @param         $value
     *
     * @return array
     * @throws \Exception
     */
    protected function add($i, $f, $value)
    {
        return Util::adiciona($this->atual, $i, $f, $value);
    }

    /**
     * Retorna o header do arquivo.
     *
     * @return mixed
     */
    protected function getHeader()
    {
        return $this->aRegistros[self::HEADER];
    }

    /**
     * Retorna os detalhes do arquivo
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getDetalhes()
    {
        return collect($this->aRegistros[self::DETALHE]);
    }

    /**
     * Retorna o trailer do arquivo.
     *
     * @return mixed
     */
    protected function getTrailer()
    {
        return $this->aRegistros[self::TRAILER];
    }

    /**
     * Valida se a linha esta correta.
     *
     * @param array $a
     *
     * @return string
     * @throws \Exception
     */
    protected function valida(array $a)
    {
        if ($this->tamanho_linha === false) {
            throw new \Exception('Classe remessa deve informar o tamanho da linha');
        }

        $a = array_filter($a, 'mb_strlen');
        if (count($a) != $this->tamanho_linha) {
            throw new \Exception(sprintf('$a nÃ£o possui %s posiÃ§Ãµes, possui: %s', $this->tamanho_linha, count($a)));
        }

        return implode('', $a);
    }

    /**
     * Gera o arquivo, retorna a string.
     *
     * @return string
     * @throws \Exception
     */
    abstract public function gerar();

    /**
     * Salva o arquivo no path informado
     *
     * @param $path
     *
     * @return mixed
     * @throws \Exception
     */
    public function save($path)
    {
        $folder = dirname($path);
        if (! is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        if (! is_writable(dirname($path))) {
            throw new \Exception('Path ' . $folder . ' nÃ£o possui permissao de escrita');
        }

        $string = $this->gerar();
        file_put_contents($path, $string);

        return $path;
    }

    /**
     * Realiza o download da string retornada do metodo gerar
     *
     * @param null $filename
     *
     * @throws \Exception
     */
    public function download($filename = null)
    {
        if ($filename === null) {
            $filename = 'remessa.txt';
        }
        header('Content-type: text/plain');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo $this->gerar();
    }
}

