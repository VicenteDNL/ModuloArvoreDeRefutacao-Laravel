<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Formula\Argumento;
use App\Http\Controllers\Arvore\Gerador;
use App\Http\Controllers\Construcao;


class Base extends Controller
{
        /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    function __construct() {
      $this->arg = new Argumento;
      $this->gerador = new Gerador;
      $this->constr = new Construcao;


}
    #Carrega as formular e exibe a pagina inicial
    public function Index(){
        $listaFormulas=$this->constr->stringXmlDiretorio();
        return view('arvore',['listaFormulas'=> $listaFormulas, 'formulaGerada'=> 'Nenhuma Fórmula Carregada...']);

    }

    #Salva na pasta public o Arquivo XML
   public function SalvarXml(Request $request){
    if ($request->hasFile('arquivo') && $request->file('arquivo')->isValid()){
        $diretorio = scandir('C:\xampp\htdocs\ArvoreDeRefutacao\storage\app\public\formulas');
        $num = count($diretorio) - 1;
        $request->file('arquivo')->storeAs('formulas', 'formula-'.$num.'.xml');
        return $this->Index();
    }

  }

    #Busca o XML, e gera a arvore Otimizada
    public function CriarArvoreOtimizada(Request $request){
        # Busca XML no diretorio
        $id = $request->all()['idFormula'];
        $xml = simplexml_load_file('C:\xampp\htdocs\ArvoreDeRefutacao\storage\app\public\formulas\formula-'.$id.'.xml');
        #--------

        #Cria a arvore passando o XML
        $listaArgumentos = $this->arg->CriaListaArgumentos($xml);
        $arvore = $this->gerador->inicializarDerivacao($listaArgumentos['premissas'],$listaArgumentos['conclusao']);
        $arv =  $this->gerador->arvoreOtimizada($arvore);
        #--------

        #Gera lista das possicoes de cada no da tabela
        $impresaoAvr = $this->constr->geraListaArvore($arv,600,300,0);
        #--------

        #Gera uma string da Formula XML
        $formulaGerada = $this->arg->stringFormula($xml);
        #--------

        #Gera lista das arvores para exibir na tabela
        $listaFormulas=$this->constr->stringXmlDiretorio();
        #--------

        return view('arvoreotimizada',['arv'=>$impresaoAvr,'listaFormulas'=> $listaFormulas, 'formulaGerada'=> $formulaGerada]);
    }

    #Carrega pagina inicia de resolucao por etapa
    public function PorEtapa(Request $request){
        #Gera lista das arvores para exibir na tabela
        $listaFormulas=$this->constr->stringXmlDiretorio();
        #--------

        return view('porEtapa.baseEtapa',['listaFormulas'=> $listaFormulas, 'formulaGerada'=> 'Nenhuma Fórmula Carregada...']);
    }


    #Inicializa o processo de criacao por etapa
    public function Inicializando(Request $request){

        $formulario =$request->all();

        # Busca XML no diretorio
        $idFormula = $formulario['idFormula'];
        $xml = simplexml_load_file('C:\xampp\htdocs\ArvoreDeRefutacao\storage\app\public\formulas\formula-'.$idFormula.'.xml');
        #-----

        #Cria a arvore passando o XML
        $listaArgumentos = $this->arg->CriaListaArgumentos($xml);
        $arvore = $this->gerador->inicializarDerivacao($listaArgumentos['premissas'],$listaArgumentos['conclusao']);
        #-----

        $listaDerivacoes=[];


        #Gera lista das possicoes de cada no da tabela
        $impresaoAvr = $this->geraListaArvore($arvore,600,300,0);
        #-----

        #Gera uma string da Formula XML
        $formulaGerada = $this->arg->stringFormula($xml);
        #-----

        #Gera lista das arvores para exibir na tabela
        $listaFormulas=$this->constr->stringXmlDiretorio();
        #-----

        #Gera array com tres alternativas senda 1 valida e 2 invalidas
        $regras=$this->gerador->arrayPerguntas($arvore);
        #-----

        return view('porEtapa.arvorePorEtapa',['arv'=>$impresaoAvr,'listaFormulas'=> $listaFormulas, 'formulaGerada'=> $formulaGerada, 'regras'=>$regras, 'listaDerivacoes'=> json_encode ($listaDerivacoes), 'idFormula'=>$idFormula]);
    }

    public function ValidaResposta(Request $request) {

        $formulario = $request->all();
        $xml = simplexml_load_file('C:\xampp\htdocs\ArvoreDeRefutacao\storage\app\public\formulas\formula-'.$formulario['idFormula'].'.xml');

        $listaArgumentos = $this->arg->CriaListaArgumentos($xml);
        $arvore = $this->gerador->inicializarDerivacao($listaArgumentos['premissas'],$listaArgumentos['conclusao']);

        $valor =$this->gerador->derivar($arvore, $formulario['linha'],$formulario['regra']);

        if($valor['sucesso']=false){
            return ',asdasd';
        }
        else{

            $impresaoAvr = $this->geraListaArvore($arvore,600,300,0);
            $formulaGerada = $this->arg->stringFormula($xml);
            $listaFormulas=$this->constr->stringXmlDiretorio();
            $listaDerivacoes=[];

            $regras=$this->gerador->arrayPerguntas($arvore);

            return view('porEtapa.arvorePorEtapa',['arv'=>$impresaoAvr,'listaFormulas'=> $listaFormulas, 'formulaGerada'=> $formulaGerada, 'regras'=>$regras, 'listaDerivacoes'=> json_encode ($listaDerivacoes), 'idFormula'=>$formulario['idFormula']]);
        }
    }





}
