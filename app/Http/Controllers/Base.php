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
   public function CarragaXml(Request $request){

    $xml = simplexml_load_file($request->file('arquivo'));
    $arg =new Argumento;
    $listaArgumentos = $arg->CriaListaArgumentos($xml);
    $no = new Gerador;
    $arvore = $no->inicializarDerivacao($listaArgumentos['premissas'],$listaArgumentos['conclusao']);
    $arv = $no->arvoreOtimizada($arvore);
    $impresaoAvr = $this->geraListaArvore($arv,600,300,0);
    return view('arvoreotimizada',['arv'=>$impresaoAvr]);
   }


   public function geraListaArvore($arvore,$width,$posX,$posY,$array=[])
   {
       $posYFilho = $posY + 100;
       array_push($array,['arv'=>$arvore,'str'=>$this->stringNo($arvore->getValorNo()), 'posX'=>$posX, 'posY'=>$posYFilho]);
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

    public function stringNo($argumento){
        if (in_array($argumento->getTipoPredicado(), ['CONJUNCAO','BICONDICIONAL','CONDICIONAL', 'DISJUNCAO'])){
            $negacao='';
            $string=null;
            print_r('1');
            if ($argumento->getNegadoPredicado()>0){
                for($i = 0 ; $i < $argumento->getNegadoPredicado(); $i++){
                    $negacao="~ ".$negacao;
                }
            }
            switch ($argumento->getTipoPredicado()) {
                case 'CONJUNCAO':
                    $string = $negacao.' ('.$this->stringNo($argumento->getEsquerdaPredicado()).' ^ '.$this->stringNo($argumento->getDireitaPredicado()).')';
                    break;
                case 'BICONDICIONAL':
//                    print_r($no);
                    $string = $negacao.' ('.$this->stringNo($argumento->getEsquerdaPredicado()).' ↔ '.$this->stringNo($argumento->getDireitaPredicado()).')';
                    break;
                case 'CONDICIONAL':

                    $string = ' ('.$this->stringNo($argumento->getEsquerdaPredicado()).' → '.$this->stringNo($argumento->getDireitaPredicado()).')';
                    break;
                case 'DISJUNCAO':
                    $string =$negacao.' ('.$this->stringNo($argumento->getEsquerdaPredicado()).' v '.$this->stringNo($argumento->getDireitaPredicado()).')';
                    break;
            }
            return $string;
        }
//         if($no->getValorNo()->getTipoPredicado()=='PREDICATIVO'){
        else{
//            print($no->getValorNo()->getValorPredicado());
//            print('--');
            $negacao='';
            if ($argumento->getNegadoPredicado()>0){
                for($i = 0 ; $i < $argumento->getNegadoPredicado(); $i++){
                    $negacao="~ ".$negacao;
                }
            }
            $string= $negacao.' '.$argumento->getValorPredicado();

            return $string;
        }

    }


}