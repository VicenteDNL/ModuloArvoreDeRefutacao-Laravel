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

   public function CarragaXml(Request $request){

    $xml = simplexml_load_file($request->file('arquivo'));

    $listaArgumentos = $this->arg->CriaListaArgumentos($xml);
    $arvore = $this->gerador->inicializarDerivacao($listaArgumentos['premissas'],$listaArgumentos['conclusao']);
    $arv =  $this->gerador->arvoreOtimizada($arvore);
    
    $impresaoAvr = $this->geraListaArvore($arv,600,300,0);
    print_r($arv);
    return view('arvoreotimizada',['arv'=>$impresaoAvr]);
   }


   public function geraListaArvore($arvore,$width,$posX,$posY,$array=[])
   {
       $posYFilho = $posY + 100;
       array_push($array,['arv'=>$arvore,'str'=>$this->arg->stringArg($arvore->getValorNo()), 'posX'=>$posX, 'posY'=>$posYFilho]);
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