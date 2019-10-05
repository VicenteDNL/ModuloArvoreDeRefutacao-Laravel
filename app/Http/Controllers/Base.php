<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Formula\Argumento;
use App\Http\Controllers\Arvore\Gerador;

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
}
public function Index(){

    $diretorio = scandir('C:\xampp\htdocs\ArvoreDeRefutacao\storage\app\public\formulas');
    $num = count($diretorio) - 2;
    $listaFormulas=[];
    for($i=1; $i <= $num ; $i++){
        $xml = simplexml_load_file('C:\xampp\htdocs\ArvoreDeRefutacao\storage\app\public\formulas\formula-'.$i.'.xml');
        $formula = [
            'str'=>$this->arg->stringFormula($xml),
            'xml'=>$i
        ];
        array_push($listaFormulas,$formula);
    }

    return view('arvore',['listaFormulas'=> $listaFormulas, 'formulaGerada'=> 'Nenhuma Fórmula Carregada...']);
    }


   public function SalvarXml(Request $request){

    if ($request->hasFile('arquivo') && $request->file('arquivo')->isValid()){

        $diretorio = scandir('C:\xampp\htdocs\ArvoreDeRefutacao\storage\app\public\formulas');
        $num = count($diretorio) - 1;

        $request->file('arquivo')->storeAs('formulas', 'formula-'.$num.'.xml');
        return $this->Index();
    }

  }

public function CriarArvoreOtimizada(Request $request){
    $id = $request->all()['idFormula'];
    $xml = simplexml_load_file('C:\xampp\htdocs\ArvoreDeRefutacao\storage\app\public\formulas\formula-'.$id.'.xml');
    $listaArgumentos = $this->arg->CriaListaArgumentos($xml);
    $arvore = $this->gerador->inicializarDerivacao($listaArgumentos['premissas'],$listaArgumentos['conclusao']);
    $arv =  $this->gerador->arvoreOtimizada($arvore);
    $impresaoAvr = $this->geraListaArvore($arv,600,300,0);
    $formulaGerada = $this->arg->stringFormula($xml);
    $diretorio = scandir('C:\xampp\htdocs\ArvoreDeRefutacao\storage\app\public\formulas');
    $num = count($diretorio) - 2;
    $listaFormulas=[];
    for($i=1; $i <= $num ; $i++){
        $xml = simplexml_load_file('C:\xampp\htdocs\ArvoreDeRefutacao\storage\app\public\formulas\formula-'.$i.'.xml');
        $formula = [
            'str'=>$this->arg->stringFormula($xml),
            'xml'=>$i
        ];
        array_push($listaFormulas,$formula);
    }


    return view('arvoreotimizada',['arv'=>$impresaoAvr,'listaFormulas'=> $listaFormulas, 'formulaGerada'=> $formulaGerada]);
}

  public function CarragaXmlPorEtapa(Request $request){
    $xml = simplexml_load_file($request->file('arquivo'));
    $listaArgumentos = $this->arg->CriaListaArgumentos($xml);
    $arvore = $this->gerador->inicializarDerivacao($listaArgumentos['premissas'],$listaArgumentos['conclusao']);
    $json = json_encode ($xml, JSON_FORCE_OBJECT);
    $impresaoAvr = $this->geraListaArvore($arvore,600,300,0);
    // var_dump($json);

    var_dump(json_decode ($json));

    // return view('porEtapa.arvorePorEtapa',['arv'=>$impresaoAvr,'json'=>$json]);
  }




  public function PorEtapa(Request $request){
    $diretorio = scandir('C:\xampp\htdocs\ArvoreDeRefutacao\storage\app\public\formulas');
    $num = count($diretorio) - 2;
    $listaFormulas=[];
    for($i=1; $i <= $num ; $i++){
        $xml = simplexml_load_file('C:\xampp\htdocs\ArvoreDeRefutacao\storage\app\public\formulas\formula-'.$i.'.xml');
        $formula = [
            'str'=>$this->arg->stringFormula($xml),
            'xml'=>$i
        ];
        array_push($listaFormulas,$formula);
    }

    return view('porEtapa.baseEtapa',['listaFormulas'=> $listaFormulas, 'formulaGerada'=> 'Nenhuma Fórmula Carregada...']);

  }


  public function Inicializando(Request $request){
        $id = $request->all()['idFormula'];
    $xml = simplexml_load_file('C:\xampp\htdocs\ArvoreDeRefutacao\storage\app\public\formulas\formula-'.$id.'.xml');
    $listaArgumentos = $this->arg->CriaListaArgumentos($xml);
    $arvore = $this->gerador->inicializarDerivacao($listaArgumentos['premissas'],$listaArgumentos['conclusao']);

    $impresaoAvr = $this->geraListaArvore($arvore,600,300,0);
    $formulaGerada = $this->arg->stringFormula($xml);
    $diretorio = scandir('C:\xampp\htdocs\ArvoreDeRefutacao\storage\app\public\formulas');
    $num = count($diretorio) - 2;
    $listaFormulas=[];
    for($i=1; $i <= $num ; $i++){
        $xml = simplexml_load_file('C:\xampp\htdocs\ArvoreDeRefutacao\storage\app\public\formulas\formula-'.$i.'.xml');
        $formula = [
            'str'=>$this->arg->stringFormula($xml),
            'xml'=>$i
        ];
        array_push($listaFormulas,$formula);
    }

    $regras=$this->gerador->arrayPerguntas($arvore);
    return view('porEtapa.arvorePorEtapa',['arv'=>$impresaoAvr,'listaFormulas'=> $listaFormulas, 'formulaGerada'=> $formulaGerada, 'regras'=>$regras]);
  }




   public function geraListaArvore($arvore,$width,$posX,$posY,$array=[])
   {
       $posYFilho = $posY + 80;
       $str = $this->arg->stringArg($arvore->getValorNo());
       $tmh = strlen ( $str )<=4 ? 40 : (strlen ( $str  )>= 18 ? strlen ( $str) *6 : strlen ( $str )*8.5 );
       array_push($array,['arv'=>$arvore,'str'=>$str, 'posX'=>$posX, 'posY'=>$posYFilho, 'tmh'=>$tmh]);
       if ($arvore->getFilhoEsquerdaNo() != null) {
           $divisao = $width / ($arvore->getFilhoEsquerdaNo()->getLinhaNo() + 1);
           $posXFilho = 0;
           for ($i = 0; $i < ($arvore->getFilhoEsquerdaNo()->getLinhaNo() + 1); $i++) {
               if (($divisao + $posXFilho) < $posX) {
                   $posXFilho = $posXFilho + $divisao;
               }
           }
           $array = $this->geraListaArvore($arvore->getFilhoEsquerdaNo(), $width, $posXFilho, $posYFilho,$array);
       }
       if ($arvore->getFilhoCentroNo() != null) {

           $array = $this->geraListaArvore($arvore->getFilhoCentroNo(), $width, $posX, $posYFilho,$array);
       }
       if ($arvore->getFilhoDireitaNo() != null) {
           $divisao = $width / ($arvore->getFilhoDireitaNo()->getLinhaNo() + 1);
           $posXFilho = $width;
           for ($i = 0; $i <($arvore->getFilhoDireitaNo()->getLinhaNo() + 1); $i++) {
               if ( $posXFilho-$divisao > $posX) {
                   $posXFilho = $posXFilho - $divisao;
               }
           }
           $array = $this->geraListaArvore($arvore->getFilhoDireitaNo(), $width, $posXFilho, $posYFilho,$array);
       }
       return $array;
   }
}
