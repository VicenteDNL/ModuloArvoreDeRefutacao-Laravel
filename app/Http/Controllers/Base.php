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
    
    $this->geraListaArvore($arv,600,300,50);




   //  return view('arvoreotimizada',['arv'=>$arv]);
       
   }


   public function geraListaArvore($arvore,$width,$posX,$posY,$array =[]){
      
      if ($arvore->getFilhoCentroNo() ==null and $arvore->getFilhoEsquerdaNo()==null and $arvore->getFilhoDireitaNo()==null){
         return  $array;
      }
      else{
         $no = [$arvore,$posX,$posY];
         array_push($array, $no);
         $posY= $posY+50;
   
         if ($arvore->getFilhoCentroNo()!=null){
            array_push($array,$this->geraListaArvore($arvore->getFilhoCentroNo(),$width,$posX,$posY));
         }
         if ($arvore->getFilhoEsquerdaNo()!=null){
   
            $divisao = $width/($arvore->getLinhaNo()+1);
            $posFilho = 0;
            for ($i=0 ; $i == ($arvore->getLinhaNo()+1); $i++ ){
               if ($divisao+$posFilho < $posX){
                  $posFilho = $posFilho + $divisao;
               }
            }
            array_push($array,$this->geraListaArvore($arvore->getFilhoEsquerdaNo(),$width, $posFilho,$posY));
          }
         if ($arvore->getFilhoDireitaNo()!=null){
         
            $divisao = $width/($arvore->getLinhaNo()+1);
            $posFilho = $width;
            for ($i=0 ; $i == ($arvore->getLinhaNo()+1); $i++ ){
               if ($divisao-$posFilho > $posX){
                  $posFilho = $posFilho - $divisao;
               }
            }
            array_push($array,$this->geraListaArvore($arvore->getFilhoDireitaNo(),$width,$posFilho,$posY));
         }
         return  $array;
      
      }
      
   
  }
}