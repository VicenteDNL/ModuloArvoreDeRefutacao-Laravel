<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Formula\Argumento;
use App\Http\Controllers\Arvore\Gerador;



class Construcao extends Controller
{
 function __construct() {
        $this->arg = new Argumento;
        $this->gerador = new Gerador;
 }

    public function stringXmlDiretorio(){
        $diretorio = scandir('C:\Users\Jheymerson\Desktop\Danilo\public\storage\formulas');
        $num = count($diretorio) - 2;
        $listaFormulas=[];
        for($i=1; $i <= $num ; $i++){
            $xml = simplexml_load_file('C:\Users\Jheymerson\Desktop\Danilo\public\storage\formulas\formula-'.$i.'.xml');
            $formula = [
                'str'=>$this->arg->stringFormula($xml),
                'xml'=>$i
            ];
            array_push($listaFormulas,$formula);
        }

        return $listaFormulas;

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


