<?php

namespace App\Http\Controllers\Arvore;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Arvore\No;
use App\Http\Controllers\Formula\Regras;

class Gerador extends Controller
{
    private $arvore;
    private $ultimalinha=1;

     function __construct() {
            $this->regras = new Regras();
     }

    public function getUltimaLinha(){
        return $this->ultimalinha;
    }

     private function addLinha(){
         $this->ultimalinha=$this->ultimalinha+1;
     }

    /*Recebe o numero da linha  e a arvore e retorna todos os nos que estão no mesma linha*/
    public function getNoslinha($arvore,$linha, $nos=[]){
          //corrigir retorno mais de um elemento
        $nose=[];
        if ($arvore->getLinhaNo()==$linha){
            array_push($nos,$arvore);

            }
        else{
            if($arvore->getFilhoEsquerdaNo()!=null){
                $nose= $this->getNoslinha($arvore->getFilhoEsquerdaNo(),$linha, $nos);
            }
            if($arvore->getFilhoCentroNo()!=null){
                $nose= $this->getNoslinha($arvore->getFilhoCentroNo(),$linha, $nos);
            }
            if($arvore->getFilhoDireitaNo()!=null){
                $nose= $this->getNoslinha($arvore->getFilhoDireitaNo(),$linha, $nos);
            }
            return $nose;
        }
        return $nos;


    }

     /* Esta função gera e retorna as primeiras linhas da arvores de refutacao, a partir das premissas e conclusão */
    public function inicializarDerivacao ($premissas,$conclusao){
        $ultimoNo=null;
        if ($premissas !=null){
            foreach ($premissas as $premissa){
                if ($this->arvore==null){
                    $this->arvore = new No($premissa->getValorObjPremissa(),null,null,null,1,null,null,false,false);
                    $ultimoNo=$this->arvore;
                }
                else{
                    $ultimoNo->setFilhoCentroNo(new No($premissa->getValorObjPremissa(),null,null,null,$this->getUltimaLinha(),null,null,false,false));
                    $ultimoNo=$ultimoNo->getFilhoCentroNo();
                }
                $this->addLinha();
            }
        }
        if ($conclusao !=null){
            $conclusao[0]->getValorObjConclusao()->addNegacaoPredicado();
            if ($this->arvore==null){
                $this->arvore= (new No($conclusao[0]->getValorObjConclusao(),null,null,null,1,null,null,false,false));
                $ultimoNo=$this->arvore;
            }else{
                $ultimoNo->setFilhoCentroNo(new No($conclusao[0]->getValorObjConclusao(),null,null,null,$this->getUltimaLinha(),null,null,false,false));
                $ultimoNo=$ultimoNo->getFilhoCentroNo();
            }
            $this->addLinha();
        }
        return $this->arvore;
    }

     /* está função encontra o NO que possui dupla negação e o retorna, se nao encontrar retorna false
        buscando do nó raiz até os nos folhas*/
    public function encontraDuplaNegacao($arvore,$noSemBifur){
        $negacao=false;
        if ($arvore->getValorNo()->getNegadoPredicado()>=2 and $arvore->isUtilizado()==false){
            if ($this->isDecendente($arvore,$noSemBifur)){
                return $arvore;}
        }
        else{
            if ($arvore->getFilhoEsquerdaNo()!=null and $negacao==false ){;
                $negacao = $this->encontraDuplaNegacao($arvore->getFilhoEsquerdaNo(),$noSemBifur);
            }
            if ($arvore->getFilhoCentroNo()!=null and $negacao==false ){
                $negacao = $this->encontraDuplaNegacao($arvore->getFilhoCentroNo(),$noSemBifur);
            }
            if ($arvore->getFilhoDireitaNo()!=null and $negacao==false ){
                $negacao = $this->encontraDuplaNegacao($arvore->getFilhoDireitaNo(),$noSemBifur);
            }
            return $negacao;
        }

    }

     /* está função encontrar o NO que possui bifurcação e ainda nao utilizado e o retorna No, se nao encontrar retorna false, buscando do nó raiz até os nos folhas*/
     public function encontraNoBifuca($arvore,$noSemBifur){
         $NoBifucacao = false;
         if (in_array($arvore->getValorNo()->getTipoPredicado(), ['DISJUNCAO','CONDICIONAL','BICONDICIONAL']) and $arvore->getValorNo()->getNegadoPredicado()==0 and $arvore->isUtilizado()==false){
            if ($this->isDecendente($arvore,$noSemBifur)){
                return $arvore;}
         }
         else if (in_array($arvore->getValorNo()->getTipoPredicado(), ['CONJUNCAO','BICONDICIONAL']) and $arvore->getValorNo()->getNegadoPredicado()==1 and $arvore->isUtilizado()==false){
            if ($this->isDecendente($arvore,$noSemBifur)){
                return $arvore;}
         }
         else{
            if ($arvore->getFilhoEsquerdaNo()!=null and $NoBifucacao==false){
                $NoBifucacao = $this->encontraNoBifuca($arvore->getFilhoEsquerdaNo(),$noSemBifur);
             }
            if ($arvore->getFilhoCentroNo()!=null and $NoBifucacao==false){
                $NoBifucacao = $this->encontraNoBifuca($arvore->getFilhoCentroNo(),$noSemBifur);
             }
             if ($arvore->getFilhoDireitaNo()!=null and $NoBifucacao==false){
                 $NoBifucacao = $this->encontraNoBifuca($arvore->getFilhoDireitaNo(),$noSemBifur);
             }
             return  $NoBifucacao;
         }
     }

     /* está função encontrar o NO que possui nao possui bifurcação e ainda nao utilizado e o retorna No, se nao encontrar retorna false, buscando do nó raiz até os nos folhas*/
     public function encontraNoSemBifucacao($arvore,$noSemBifur){
         $NoSemBifucacao = false;
         if ($arvore->getValorNo()->getTipoPredicado()== 'CONJUNCAO' and $arvore->getValorNo()->getNegadoPredicado()==0 and $arvore->isUtilizado()==false){
            if ($this->isDecendente($arvore,$noSemBifur)){
                return $arvore;}

         }
         else if (in_array($arvore->getValorNo()->getTipoPredicado(), ['DISJUNCAO','CONDICIONAL']) and $arvore->getValorNo()->getNegadoPredicado()==1 and $arvore->isUtilizado()==false){
            if ($this->isDecendente($arvore,$noSemBifur)){
                return $arvore;}
         }
         else{
             if ($arvore->getFilhoEsquerdaNo()!=null and $NoSemBifucacao==false){
                 $NoSemBifucacao = $this->encontraNoSemBifucacao($arvore->getFilhoEsquerdaNo(),$noSemBifur);
             }
             if ($arvore->getFilhoCentroNo()!=null and $NoSemBifucacao==false){
                 $NoSemBifucacao = $this->encontraNoSemBifucacao($arvore->getFilhoCentroNo(),$noSemBifur);
             }
             if ($arvore->getFilhoDireitaNo()!=null and $NoSemBifucacao==false){
                 $NoSemBifucacao = $this->encontraNoSemBifucacao($arvore->getFilhoDireitaNo(),$noSemBifur);
             }
             return $NoSemBifucacao;
         }
     }

     /*esta funçao recebe com parametro a arvore atual, e um boleano (indica se entre os seus descentende foi encontrado um No que ainda nao foi derivado), percorrendo da centro -esquerda-direita- para ate encontras
     um No folha apto para ser o proximo a ser inserido, caso nao encontre returna NULL*/
     public function  proximoNoParaInsercao($arvore, $descendenteSemDerivacao = false){
         $proximoNo =null;
         if ( $arvore->isUtilizado()==false and ( (in_array($arvore->getValorNo()->getTipoPredicado(), ['DISJUNCAO','CONDICIONAL','BICONDICIONAL','CONJUNCAO'])) or ( $arvore->getValorNo()->getTipoPredicado()=='PREDICATIVO' and $arvore->getValorNo()->getNegadoPredicado()>=2) )){
             $descendenteSemDerivacao = true;
         }
         if ($arvore->getFilhoDireitaNo() ==null and  $arvore->getFilhoEsquerdaNo() ==null and  $arvore->getFilhoCentroNo() ==null  and $arvore->isFechado()==false and  $descendenteSemDerivacao==true){
             return $arvore;
         }
         else {
             if ($arvore->getFilhoCentroNo()!=null and $proximoNo ==null){
                 $proximoNo = $this->proximoNoParaInsercao($arvore->getFilhoCentroNo(),$descendenteSemDerivacao);
             }
             if ($arvore->getFilhoEsquerdaNo()!=null and $proximoNo ==null){
                 $proximoNo = $this->proximoNoParaInsercao($arvore->getFilhoEsquerdaNo(),$descendenteSemDerivacao);
             }
             if ($arvore->getFilhoDireitaNo()!=null and $proximoNo ==null){
                 $proximoNo = $this->proximoNoParaInsercao($arvore->getFilhoDireitaNo(),$descendenteSemDerivacao);
             }
             return $proximoNo;
         }
     }

     /*esta funcao recebe uma arvore (a partir de um NO qualquer), e o No de interese, afim de verificar se o no raiz da arvore é parente do no de interesse, e retorna true se a condicao for vedadeira e false se nao for
     verdadeira*/
     public function isDecendente($arvore,$no){
         $noDescendente=false;

        if ($arvore->getValorNo()->getValorPredicado() == $no->getValorNo()->getValorPredicado() and $arvore->getValorNo()->getNegadoPredicado() == $no->getValorNo()->getNegadoPredicado()){

            return true;
        }
        else {
            if ($arvore->getFilhoCentroNo()!=null and $noDescendente==false){

                $noDescendente = $this->isDecendente($arvore->getFilhoCentroNo(),$no);

            }
            if ($arvore->getFilhoEsquerdaNo()!=null and $noDescendente==false ){

                $noDescendente = $this->isDecendente($arvore->getFilhoEsquerdaNo(),$no);
            }
            if ($arvore->getFilhoDireitaNo()!=null and $noDescendente==false){
                $noDescendente = $this->isDecendente($arvore->getFilhoDireitaNo(),$no);

            }
            return $noDescendente;

        }
     }

     /* Esta função recebe uma arvore e um NO, e verifica se existe uma contradicao para o NO na Arvore, se verdadeiro retorna o NÓ contraditorio, se nao retorna False*/
     public function encontraContradicao($arvore,$no){
        $contradicao = false;
         if ($arvore->getValorNo()->getValorPredicado() == $no->getValorNo()->getValorPredicado()){

             $negacaoNo = $no->getValorNo()->getNegadoPredicado();

             if ($negacaoNo == 1 and $arvore->getValorNo()->getNegadoPredicado()==0){
                 if ($this->isDecendente($arvore,$no)){

                     return $arvore;}
             }
             elseif ($negacaoNo == 0 and $arvore->getValorNo()->getNegadoPredicado()==1) {
                 if ($this->isDecendente($arvore, $no)) {
                     return $arvore;}
             }
            else{
                if ($arvore->getFilhoCentroNo()!=null and $contradicao == false){
                    $contradicao = $this->encontraContradicao($arvore->getFilhoCentroNo(),$no);
                }
                if ($arvore->getFilhoEsquerdaNo()!=null and $contradicao == false){
                    $contradicao = $this->encontraContradicao($arvore->getFilhoEsquerdaNo(),$no);
                }
                if ($arvore->getFilhoDireitaNo()!=null and $contradicao == false){
                    $contradicao =  $this->encontraContradicao($arvore->getFilhoDireitaNo(),$no);
                }
                return $contradicao;

            }
        }
        else {
            if ($arvore->getFilhoCentroNo()!=null and $contradicao == false){
                $contradicao = $this->encontraContradicao($arvore->getFilhoCentroNo(),$no);
            }
            if ($arvore->getFilhoEsquerdaNo()!=null and $contradicao == false){
                $contradicao = $this->encontraContradicao($arvore->getFilhoEsquerdaNo(),$no);
            }
            if ($arvore->getFilhoDireitaNo()!=null and $contradicao == false){
                $contradicao =  $this->encontraContradicao($arvore->getFilhoDireitaNo(),$no);
            }
            return $contradicao;
        }

     }

     /*esta função recebe a referencia do No que vai sofrer inserção, a arvore atual, e o array dos nos resultantes da aplicacao da regra de derivacao e faz a verificação da contradicao do novo no*/
     public function criarNoBifurcado($noInsercao,$arvore,$array_filhos,$linhaDerivado){

        $noInsercao->setFilhoEsquerdaNo(new No($array_filhos['esquerda'][0],null,null,null,$noInsercao->getLinhaNo()+1,null,$linhaDerivado,false,false));

        $contradicao = $this->encontraContradicao($arvore,$noInsercao->getFilhoEsquerdaNo());
        if($contradicao!=false){
            $noInsercao->getFilhoEsquerdaNo()->FecharRamo($contradicao->getLinhaNo());
        }

        $noInsercao->setFilhoDireitaNo(new No($array_filhos['direita'][0],null,null,null,$noInsercao->getLinhaNo()+1,null,$linhaDerivado,false,false));
        $contradicao = $this->encontraContradicao($arvore,$noInsercao->getFilhoDireitaNo());
        if($contradicao!=false){
            $noInsercao->getFilhoDireitaNo()->FecharRamo($contradicao->getLinhaNo());
        }
     }

     /*esta função recebe a referencia do No que vai sofrer inserção, a arvore atual, e o array dos nos resultantes da aplicacao da regra de derivacao e faz a verificação da contradicao do novo no*/
     public function criarNoBifurcadoDuplo($noInsercao,$arvore,$array_filhos,$linhaDerivado){

        $noInsercao->setFilhoEsquerdaNo(new No($array_filhos['esquerda'][0],null,null,null,$noInsercao->getLinhaNo()+1,null,$linhaDerivado,false,false));
        $contradicao = $this->encontraContradicao($arvore,$noInsercao->getFilhoEsquerdaNo());
        if($contradicao!=false){
            $noInsercao->getFilhoEsquerdaNo()->FecharRamo($contradicao->getLinhaNo());
        }

        $noInsercao->getFilhoEsquerdaNo()->setFilhoCentroNo(new No($array_filhos['esquerda'][1],null,null,null,$noInsercao->getLinhaNo()+2,null,$linhaDerivado,false,false));
        $contradicao = $this->encontraContradicao($arvore,$noInsercao->getFilhoEsquerdaNo()->getFilhoCentroNo());
        if($contradicao!=false){
            $noInsercao->getFilhoEsquerdaNo()->getFilhoCentroNo()->FecharRamo($contradicao->getLinhaNo());
        }


        $noInsercao->setFilhoDireitaNo(new No($array_filhos['direita'][0],null,null,null,$noInsercao->getLinhaNo()+1,null,$linhaDerivado,false,false));
        $contradicao = $this->encontraContradicao($arvore,$noInsercao->getFilhoDireitaNo());
        if($contradicao!=false){
            $noInsercao->getFilhoDireitaNo()->FecharRamo($contradicao->getLinhaNo());
        }

        $noInsercao->getFilhoDireitaNo()->setFilhoCentroNo(new No($array_filhos['direita'][1],null,null,null,$noInsercao->getLinhaNo()+2,null,$linhaDerivado,false,false));
        $contradicao = $this->encontraContradicao($arvore,$noInsercao->getFilhoDireitaNo()->getFilhoCentroNo());
        if($contradicao!=false){
            $noInsercao->getFilhoDireitaNo()->getFilhoCentroNo()->FecharRamo($contradicao->getLinhaNo());
        }
     }

     /*esta função recebe a referencia do No que vai sofrer inserção, a arvore atual, e o array dos nos resultantes da aplicacao da regra de derivacao e faz a verificação da contradicao do novo no*/
     public function criarNoSemBifucacao($noInsercao,$arvore,$array_filhos,$linhaDerivado){

        $primeiroNo=new No($array_filhos['centro'][0],null,null,null,null,null,$linhaDerivado,false,false);
        $segundoNo=new No($array_filhos['centro'][1],null,null,null,null,null,$linhaDerivado,false,false);


         $noInsercao->setFilhoCentroNo($primeiroNo);
         $noInsercao->getFilhoCentroNo()->setFilhoCentroNo($segundoNo);


         $contradicaoPrim = $this->encontraContradicao($arvore,$noInsercao->getFilhoCentroNo());

         $contradicaoSeg = $this->encontraContradicao($arvore,$noInsercao->getFilhoCentroNo()->getFilhoCentroNo());


         if ($contradicaoPrim!=false and $contradicaoSeg==false){
            $noInsercao->getFilhoCentroNo()->removeFilhoCentroNo();
             $noInsercao->removeFilhoCentroNo();

            $noInsercao->setFilhoCentroNo($segundoNo);
            $noInsercao->getFilhoCentroNo()->setFilhoCentroNo($primeiroNo);

            $primeiroNo->setLinhaNo($noInsercao->getLinhaNo()+2);
            $segundoNo->setLinhaNo($noInsercao->getLinhaNo()+1);
            $noInsercao->getFilhoCentroNo()->getFilhoCentroNo()->FecharRamo($contradicaoPrim->getLinhaNo());
         }
         elseif(($contradicaoPrim!=false and $contradicaoSeg!=false) or ($contradicaoPrim==false and $contradicaoSeg!=false) ){
            $primeiroNo->setLinhaNo($noInsercao->getLinhaNo()+1);
            $segundoNo->setLinhaNo($noInsercao->getLinhaNo()+2);
            $noInsercao->getFilhoCentroNo()->getFilhoCentroNo()->FecharRamo($contradicaoSeg->getLinhaNo());
         }
         else{
            $primeiroNo->setLinhaNo($noInsercao->getLinhaNo()+1);
            $segundoNo->setLinhaNo($noInsercao->getLinhaNo()+2);
         }

     }

     /*esta função recebe a referencia do No que vai sofrer inserção, a arvore atual, e o array dos nos resultantes da aplicacao da regra de derivacao e faz a verificação da contradicao do novo no*/
     public function criarNo($noInsercao,$arvore,$array_filhos,$linhaDerivado){
         $noInsercao->setFilhoCentroNo(new No($array_filhos['centro'][0],null,null,null,$this->getUltimaLinha(),null,$linhaDerivado,false,false));

         $contradicao = $this->encontraContradicao($arvore,$noInsercao->getFilhoCentroNo());
         if($contradicao!=false){
             $noInsercao->getFilhoCentroNo()->FecharRamo($contradicao->getLinhaNo());
         }
     }


     public function arvoreOtimizada($arvore){
        $noInsercao=$this->proximoNoParaInsercao($arvore);

        if ($noInsercao==null){
            return $arvore;

        }
        else{

            $no =$this->encontraDuplaNegacao($arvore,$noInsercao);
            $noBifur =$this->encontraNoBifuca($arvore,$noInsercao);
            $noSemBifur =$this->encontraNoSemBifucacao($arvore,$noInsercao);

             if ($no){
                 $array_filhos =$this->regras->DuplaNeg($no->getValorNo());
                 $no->utilizado(true);
                 $this->criarNo($noInsercao,$arvore,$array_filhos,$no->getLinhaNo());
                 return $this->arvoreOtimizada($arvore);
             }
             elseif($noSemBifur){
                 if($noSemBifur->getValorNo()->getTipoPredicado()=='CONJUNCAO' and $noSemBifur->getValorNo()->getNegadoPredicado()==0){
                     $array_filhos = $this->regras->conjuncao($noSemBifur->getValorNo());
                     $noSemBifur->utilizado(true);
                     $this->criarNoSemBifucacao($noInsercao,$arvore,$array_filhos,$noSemBifur->getLinhaNo());
                 }
                 else if ($noSemBifur->getValorNo()->getTipoPredicado()== 'DISJUNCAO' and $noSemBifur->getValorNo()->getNegadoPredicado()==1){
                     $array_filhos = $this->regras->disjuncaoNeg($noSemBifur->getValorNo());
                     $noSemBifur->utilizado(true);
                     $this->criarNoSemBifucacao($noInsercao,$arvore,$array_filhos,$noSemBifur->getLinhaNo());
                 }
                 elseif ($noSemBifur->getValorNo()->getTipoPredicado()== 'CONDICIONAL' and $noSemBifur->getValorNo()->getNegadoPredicado()==1) {
                     $array_filhos = $this->regras->condicionalNeg($noSemBifur->getValorNo());
                     $noSemBifur->utilizado(true);
                     $this->criarNoSemBifucacao($noInsercao,$arvore,$array_filhos,$noSemBifur->getLinhaNo());
                 }
                return $this->arvoreOtimizada($arvore);
             }
             elseif($noBifur){
                 if($noBifur->getValorNo()->getTipoPredicado()=='DISJUNCAO' and $noBifur->getValorNo()->getNegadoPredicado()==0){
                     $array_filhos = $this->regras->disjuncao($noBifur->getValorNo());
                     $noBifur->utilizado(true);
                     $this->criarNoBifurcado($noInsercao,$arvore,$array_filhos,$noBifur->getLinhaNo());
                 }
                 else if ($noBifur->getValorNo()->getTipoPredicado()== 'CONDICIONAL' and $noBifur->getValorNo()->getNegadoPredicado()==0){
                     $array_filhos = $this->regras->condicional($noBifur->getValorNo());
                     $noBifur->utilizado(true);
                     $this->criarNoBifurcado($noInsercao,$arvore,$array_filhos,$noBifur->getLinhaNo());
                 }
                 else if ($noBifur->getValorNo()->getTipoPredicado()== 'BICONDICIONAL' and $noBifur->getValorNo()->getNegadoPredicado()==0){
                     $array_filhos = $this->regras->bicondicional($noBifur->getValorNo());
                     $noBifur->utilizado(true);
                     $this->criarNoBifurcadoDuplo($noInsercao,$arvore,$array_filhos,$noBifur->getLinhaNo());
                 }
                 else if ($noBifur->getValorNo()->getTipoPredicado()== 'CONJUNCAO' and $noBifur->getValorNo()->getNegadoPredicado()==1){

                     $array_filhos = $this->regras->conjuncaoNeg($noBifur->getValorNo());
                     $noBifur->utilizado(true);
                     $this->criarNoBifurcado($noInsercao,$arvore,$array_filhos,$noBifur->getLinhaNo());
                 }
                 else if ($noBifur->getValorNo()->getTipoPredicado()== 'BICONDICIONAL' and $noBifur->getValorNo()->getNegadoPredicado()==1){
                     $array_filhos = $this->regras->bicondicionalNeg($noBifur->getValorNo());
                     $noBifur->utilizado(true);
                     $this->criarNoBifurcadoDuplo($noInsercao,$arvore,$array_filhos,$noBifur->getLinhaNo());
                 }
                 return $this->arvoreOtimizada($arvore);
             }
             return $arvore;
        }
     }


     public function possibilidades($arvore,$array=[]){

        if ($arvore->getValorNo()->getNegadoPredicado()>2){
            array_push($array, 'Negação Dupla');
        }
        elseif($arvore->getValorNo()->getTipoPredicado()=='CONJUNCAO' and $arvore->getValorNo()->getNegadoPredicado()==0){
            array_push($array, 'Conjunção');
        }
        elseif ($arvore->getValorNo()->getTipoPredicado()== 'DISJUNCAO' and $arvore->getValorNo()->getNegadoPredicado()==1){
            array_push($array, ' Negação_Disjunção');
        }
        elseif ($arvore->getValorNo()->getTipoPredicado()== 'CONDICIONAL' and $arvore->getValorNo()->getNegadoPredicado()==1) {
            array_push($array, 'Negacão_Condicional');
        }
        elseif($arvore->getValorNo()->getTipoPredicado()=='DISJUNCAO' and $arvore->getValorNo()->getNegadoPredicado()==0){
            array_push($array, 'Disjunção');
            }
        elseif ($arvore->getValorNo()->getTipoPredicado()== 'CONDICIONAL' and $arvore->getValorNo()->getNegadoPredicado()==0){
            array_push($array, 'Condicional');
            }
        elseif ($arvore->getValorNo()->getTipoPredicado()== 'BICONDICIONAL' and $arvore->getValorNo()->getNegadoPredicado()==0){
            array_push($array, 'Bicondicional');
        }
        elseif ($arvore->getValorNo()->getTipoPredicado()== 'CONJUNCAO' and $arvore->getValorNo()->getNegadoPredicado()==1){
            array_push($array, 'Negação_Conjunção');
            }
        elseif ($arvore->getValorNo()->getTipoPredicado()== 'BICONDICIONAL' and $arvore->getValorNo()->getNegadoPredicado()==1){
            array_push($array, 'Negação_Bicondicional');

            }



        if ($arvore->getFilhoEsquerdaNo()==null and $arvore->getFilhoCentroNo()==null and$arvore->getFilhoDireitaNo()==null){
            return $array;
        }
        else{

            if ($arvore->getFilhoCentroNo()!=null ){
                 return $this->possibilidades($arvore->getFilhoCentroNo(),$array);
            }
            if ($arvore->getFilhoEsquerdaNo()!=null ){
                return $this->possibilidades($arvore->getFilhoEsquerdaNo(),$array);
            }
            if ($arvore->getFilhoDireitaNo()!=null){
                return $this->possibilidades($arvore->getFilhoDireitaNo(),$array);
            }
        }



    }

     public function arrayPerguntas($arvore){
         $listaPerguntas = $this->possibilidades($arvore);

        if (count( $listaPerguntas)<3){
            $possibilidades = ['Negação_Bicondicional','Negação_Conjunção','Bicondicional','Condicional','Disjunção','Negacão_Condicional','Negação_Disjunção','Conjunção','Negação_Dupla'];

            $comp=false;
            while($comp==false){
                $nova = array_rand($possibilidades,1);
                if (in_array($possibilidades[$nova],$listaPerguntas)==false){
                        array_push($listaPerguntas,$possibilidades[$nova]);
                        if(count($listaPerguntas)==3){
                            $comp=true;
                        }
                }
            }
        }

        shuffle($listaPerguntas);
        return $listaPerguntas;
     }

     public function derivar($arvore, $linha,$regra){
        $noInsercao = $this->proximoNoParaInsercao($arvore);

        $no =[];
        $listaNos=$this->getNoslinha($arvore, (int)$linha);
        foreach($listaNos as $noValido){


            $noDescendente =$this->isDecendente($noValido,$noInsercao);
            if($noDescendente!=false){
                $no=$noValido;
            }
        }

        if($no==null){
            return ['sucesso'=>false, 'messagem'=>'Linha não Existe'];
        }
        else{
            print_r($no->getValorNo()->getTipoPredicado());
            print_r($no->getValorNo()->getNegadoPredicado());
            if($no->getValorNo()->getTipoPredicado()=='PREMISSA' OR $no->getValorNo()->getTipoPredicado()=='CONCLUSAO' OR $no->getValorNo()->getTipoPredicado()=='PREDICATIVO'){
                return ['sucesso'=>false, 'messagem'=>'Linha Invalida'];
            }
            elseif($no->getValorNo()->getNegadoPredicado()>2  and $regra=='Negação Dupla'){
                $array_filhos =$this->regras->DuplaNeg($no->getValorNo());
                $no->utilizado(true);
                $this->criarNo($noInsercao,$arvore,$array_filhos,$no->getLinhaNo());
                return ['sucesso'=>true, 'messagem'=>'Negação_Dupla','arv'=>$arvore];
            }
            elseif($no->getValorNo()->getTipoPredicado()=='CONJUNCAO' and $no->getValorNo()->getNegadoPredicado()==0 and $regra=='Conjunção'){
                $array_filhos = $this->regras->conjuncao($no->getValorNo());
                $no->utilizado(true);
                $this->criarNoSemBifucacao($noInsercao,$arvore,$array_filhos,$no->getLinhaNo());
                return ['sucesso'=>true, 'messagem'=>'Conjunção','arv'=>$arvore];
            }
            elseif ($no->getValorNo()->getTipoPredicado()== 'DISJUNCAO' and $no->getValorNo()->getNegadoPredicado()==1  and $regra=='Negação_Disjunção'){
                $array_filhos = $this->regras->disjuncaoNeg($no->getValorNo());
                $no->utilizado(true);
                $this->criarNoSemBifucacao($noInsercao,$arvore,$array_filhos,$no->getLinhaNo());
                return ['sucesso'=>true, 'messagem'=>'Negação_Disjunção','arv'=>$arvore];
            }
            elseif ($no->getValorNo()->getTipoPredicado()== 'CONDICIONAL' and $no->getValorNo()->getNegadoPredicado()==1  and $regra=='Negacão_Condicional') {
                $array_filhos = $this->regras->condicionalNeg($no->getValorNo());
                $no->utilizado(true);
                $this->criarNoSemBifucacao($noInsercao,$arvore,$array_filhos,$no->getLinhaNo());
                return ['sucesso'=>true, 'messagem'=>'Negacão_Condicional','arv'=>$arvore];
            }
            elseif($no->getValorNo()->getTipoPredicado()=='DISJUNCAO' and $no->getValorNo()->getNegadoPredicado()==0  and $regra=='Disjunção'){
                $array_filhos = $this->regras->disjuncao($no->getValorNo());
                 $no->utilizado(true);
                 $this->criarNoBifurcado($noInsercao,$arvore,$array_filhos,$no->getLinhaNo());
                 return ['sucesso'=>true, 'messagem'=>'Disjunção','arv'=>$arvore];
            }
            elseif ($no->getValorNo()->getTipoPredicado()== 'CONDICIONAL' and $no->getValorNo()->getNegadoPredicado()==0  and $regra=='Condicional'){
                $array_filhos = $this->regras->condicional($no->getValorNo());
                $no->utilizado(true);
                $this->criarNoBifurcado($noInsercao,$arvore,$array_filhos,$no->getLinhaNo());
                return ['sucesso'=>true, 'messagem'=>'Condicional','arv'=>$arvore];
            }
            elseif ($no->getValorNo()->getTipoPredicado()== 'BICONDICIONAL' and $no->getValorNo()->getNegadoPredicado()==0  and $regra=='Bicondicional'){
                $array_filhos = $this->regras->bicondicional($no->getValorNo());
                 $no->utilizado(true);
                 $this->criarNoBifurcadoDuplo($noInsercao,$arvore,$array_filhos,$no->getLinhaNo());
                 return ['sucesso'=>true, 'messagem'=>'Bicondicional','arv'=>$arvore];
            }
            elseif ($no->getValorNo()->getTipoPredicado()== 'CONJUNCAO' and $no->getValorNo()->getNegadoPredicado()==1  and $regra=='Negação_Conjunção'){
                $array_filhos = $this->regras->conjuncaoNeg($no->getValorNo());
                $no->utilizado(true);
                $this->criarNoBifurcado($noInsercao,$arvore,$array_filhos,$no->getLinhaNo());
                return ['sucesso'=>true, 'messagem'=>'Negação_Conjunção','arv'=>$arvore];
            }
            elseif ($no->getValorNo()->getTipoPredicado()== 'BICONDICIONAL' and $no->getValorNo()->getNegadoPredicado()==1  and $regra=='Negação_Bicondicional'){
                $array_filhos = $this->regras->bicondicionalNeg($no->getValorNo());
                 $no->utilizado(true);
                 $this->criarNoBifurcadoDuplo($noInsercao,$arvore,$array_filhos,$no->getLinhaNo());
                 return ['sucesso'=>true, 'messagem'=>'Negação_Bicondicional','arv'=>$arvore];

                }
            return ['sucesso'=>false, 'messagem'=>'Regra Invalida'];

        }



        // $nosLinha =$this->encontraDuplaNegacao($arvore,$noInsercao);
        //     $noBifur =$this->encontraNoBifuca($arvore,$noInsercao);
        //     $noSemBifur =$this->encontraNoSemBifucacao($arvore,$noInsercao);

     }

}
