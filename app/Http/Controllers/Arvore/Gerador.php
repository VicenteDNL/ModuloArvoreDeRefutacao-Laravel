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
         
        if ($arvore->getLinhaNo()==$linha){
            array_push($nos,$arvore);
            }

        if($arvore->getFilhoCentroNo()!=null){ 
            $nos= $this->getNoslinha($arvore->getFilhoCentroNo(),$linha,$nos);
        }
        elseif($arvore->getFilhoEsquerdaNo()!=null and $arvore->getFilhoDireitaNo()!=null){
            $nos= $this->getNoslinha($arvore->getFilhoEsquerdaNo(),$linha, $nos);   
            $nos= $this->getNoslinha($arvore->getFilhoDireitaNo(),$linha, $nos);
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
        $contradEsq1 = $this->encontraContradicao($arvore,$noInsercao->getFilhoEsquerdaNo());
  

        $noInsercao->getFilhoEsquerdaNo()->setFilhoCentroNo(new No($array_filhos['esquerda'][1],null,null,null,$noInsercao->getLinhaNo()+2,null,$linhaDerivado,false,false));
        $contradEsq2 = $this->encontraContradicao($arvore,$noInsercao->getFilhoEsquerdaNo()->getFilhoCentroNo());
        if($contradEsq2!=false){
            $noInsercao->getFilhoEsquerdaNo()->getFilhoCentroNo()->FecharRamo($contradEsq2->getLinhaNo());
        }
        elseif($contradEsq1!=false and $contradEsq2==false){
            $noInsercao->setFilhoEsquerdaNo(new No($array_filhos['esquerda'][1],null,null,null,$noInsercao->getLinhaNo()+1,null,$linhaDerivado,false,false));
            $noInsercao->getFilhoEsquerdaNo()->setFilhoCentroNo(new No($array_filhos['esquerda'][0],null,null,null,$noInsercao->getLinhaNo()+2,null,$linhaDerivado,false,false));
            $noInsercao->getFilhoEsquerdaNo()->getFilhoCentroNo()->FecharRamo($contradEsq1->getLinhaNo());
        }



        $noInsercao->setFilhoDireitaNo(new No($array_filhos['direita'][0],null,null,null,$noInsercao->getLinhaNo()+1,null,$linhaDerivado,false,false));
        $contradDir1 = $this->encontraContradicao($arvore,$noInsercao->getFilhoDireitaNo());
 

        $noInsercao->getFilhoDireitaNo()->setFilhoCentroNo(new No($array_filhos['direita'][1],null,null,null,$noInsercao->getLinhaNo()+2,null,$linhaDerivado,false,false));
        $contradDir2 = $this->encontraContradicao($arvore,$noInsercao->getFilhoDireitaNo()->getFilhoCentroNo());
        if($contradDir2!=false){
            $noInsercao->getFilhoDireitaNo()->getFilhoCentroNo()->FecharRamo($contradDir2->getLinhaNo());
        }
        elseif($contradDir1!=false and $contradDir2==false){
            $noInsercao->setFilhoDireitaNo(new No($array_filhos['direita'][1],null,null,null,$noInsercao->getLinhaNo()+1,null,$linhaDerivado,false,false));
            $noInsercao->getFilhoDireitaNo()->setFilhoCentroNo(new No($array_filhos['direita'][0],null,null,null,$noInsercao->getLinhaNo()+2,null,$linhaDerivado,false,false));
            $noInsercao->getFilhoDireitaNo()->getFilhoCentroNo()->FecharRamo($contradDir1->getLinhaNo());
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
         $noInsercao->setFilhoCentroNo(new No($array_filhos['centro'][0],null,null,null,$noInsercao->getLinhaNo()+1,null,$linhaDerivado,false,false));

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

     public function existePossibilidade($array, $id){
         foreach($array as $valor){
             if( $valor['id']==$id){
                 return true;
             }
         }
         return false;
     }

     public function possibilidades($arvore,$array=[]){
        

        if ($arvore->getValorNo()->getNegadoPredicado()>=2 and $arvore->isUtilizado()==false){
            if ($this->existePossibilidade($array,1)==false){
                array_push($array, ['id'=>1,'str'=>'DUPLANAGACAO']);
            } 
        }
        elseif($arvore->getValorNo()->getTipoPredicado()=='CONJUNCAO' and $arvore->getValorNo()->getNegadoPredicado()==0 and $arvore->isUtilizado()==false){
            if ($this->existePossibilidade($array,2)==false){
                array_push($array, ['id'=>2,'str'=>'CONJUNCAO']);
            }
        }
        elseif ($arvore->getValorNo()->getTipoPredicado()== 'DISJUNCAO' and $arvore->getValorNo()->getNegadoPredicado()==1 and $arvore->isUtilizado()==false){
            if ($this->existePossibilidade($array,3)==false){
                array_push($array, ['id'=>3,'str'=>'DISJUNCAONEGADA']);
            }
        }
        elseif ($arvore->getValorNo()->getTipoPredicado()== 'CONDICIONAL' and $arvore->getValorNo()->getNegadoPredicado()==1 and $arvore->isUtilizado()==false) {
            if ($this->existePossibilidade($array,4)==false){
                array_push($array, ['id'=>4,'str'=>'CONDICIONALNEGADA']);
            }
        }
        elseif($arvore->getValorNo()->getTipoPredicado()=='DISJUNCAO' and $arvore->getValorNo()->getNegadoPredicado()==0 and $arvore->isUtilizado()==false){
            if ($this->existePossibilidade($array,5)==false){
                array_push($array, ['id'=>5,'str'=>'DISJUNCAO']);
            }
        }
        elseif ($arvore->getValorNo()->getTipoPredicado()== 'CONDICIONAL' and $arvore->getValorNo()->getNegadoPredicado()==0 and $arvore->isUtilizado()==false){
            if ($this->existePossibilidade($array,6)==false){
                array_push($array, ['id'=>6,'str'=>'CONDICIONAL']);
            }
        }
        elseif ($arvore->getValorNo()->getTipoPredicado()== 'BICONDICIONAL' and $arvore->getValorNo()->getNegadoPredicado()==0 and $arvore->isUtilizado()==false){
            if ($this->existePossibilidade($array,7)==false){
                array_push($array, ['id'=>7,'str'=>'BICONDICIONAL']);
            }
        }
        elseif ($arvore->getValorNo()->getTipoPredicado()== 'CONJUNCAO' and $arvore->getValorNo()->getNegadoPredicado()==1 and $arvore->isUtilizado()==false){
            if ($this->existePossibilidade($array,8)==false){
                array_push($array, ['id'=>8,'str'=>'CONJUNCAONEGADA']);
            }
        }
        elseif ($arvore->getValorNo()->getTipoPredicado()== 'BICONDICIONAL' and $arvore->getValorNo()->getNegadoPredicado()==1 and $arvore->isUtilizado()==false){
            if ($this->existePossibilidade($array,9)==false){
                array_push($array, ['id'=>9,'str'=>'BICONDICIONALNEGADA']);
            }
        }

   
        if ($arvore->getFilhoCentroNo()!=null ){
            
            $array=$this->possibilidades($arvore->getFilhoCentroNo(),$array);

        }
        elseif($arvore->getFilhoEsquerdaNo()!=null and $arvore->getFilhoDireitaNo()!=null){

            $array=$this->possibilidades($arvore->getFilhoEsquerdaNo(),$array);
            
        
            $array=$this->possibilidades($arvore->getFilhoDireitaNo(),$array);


        }
        return  $array;

    }

     public function arrayPerguntas($arvore){
         $listapossibilidades = $this->possibilidades($arvore);
         $listaPosValida=[];

         $possibilidades =[  'DUPLANAGACAO'=>['id'=>1,'str'=>'Dupla Negação'],
             'CONJUNCAO'=>['id'=>2,'str'=>'Conjunção'],
             'DISJUNCAONEGADA'=>['id'=>3,'str'=>'Negação da Disjunção'],
             'CONDICIONALNEGADA'=>['id'=>4,'str'=>'Negacão da Condicional'],
             'DISJUNCAO'=>['id'=>5,'str'=>'Disjunção'],
             'CONDICIONAL'=>['id'=>6,'str'=>'Condicional'],
             'BICONDICIONAL'=>['id'=>7,'str'=>'Bicondicional'],
             'CONJUNCAONEGADA'=>['id'=>8,'str'=>'Negação da Conjunção'],
             'BICONDICIONALNEGADA'=>['id'=>9,'str'=>'Negação da Bicondicional']
         ];

         foreach ( $listapossibilidades as $pos){
                array_push($listaPosValida,$possibilidades[$pos['str']]);
             
         };

        if (count( $listaPosValida)<3){
            $comp=false;
            while($comp==false){
                $nova = array_rand($possibilidades,1);
                $existe=false;
                foreach ($listaPosValida as $pos){
                    if ($possibilidades[$nova]['id']==$pos['id']){
                        $existe=true;
                    }
                }
                if($existe==false){
                    array_push($listaPosValida,$possibilidades[$nova]);
                    if(count($listaPosValida)==3){
                        $comp=true;}
                }
            }
        }

        shuffle($listaPosValida);
    
        return $listaPosValida;
     }

     public function derivar($arvore, $linha,$regra){
        $noInsercao = $this->proximoNoParaInsercao($arvore);

        
        $no =[];
        $listaNos=$this->getNoslinha($arvore, (int)$linha);
        foreach($listaNos as $noValido){
            $noDescendente =$this->isDecendente($noValido,$noInsercao);
            if($noDescendente!=false){
                if($noValido->isUtilizado()==false){
                    $no=$noValido;
                }
               
            }
        }
        
        if($no==null){
            return ['sucesso'=>false, 'messagem'=>'Linha não Existe'];
        }
        else{
    
            if(($no->getValorNo()->getTipoPredicado()=='PREMISSA' OR $no->getValorNo()->getTipoPredicado()=='CONCLUSAO' OR $no->getValorNo()->getTipoPredicado()=='PREDICATIVO') and $no->getValorNo()->getNegadoPredicado()<2){
               var_dump($no->getValorNo()->getTipoPredicado()=='PREMISSA');
                return ['sucesso'=>false, 'messagem'=>'Linha Invalida'];
            }
            elseif($no->getValorNo()->getNegadoPredicado()>=2  and $regra==1){
                $array_filhos =$this->regras->DuplaNeg($no->getValorNo());
                $no->utilizado(true);
                $this->criarNo($noInsercao,$arvore,$array_filhos,$no->getLinhaNo());
                return ['sucesso'=>true, 'messagem'=>'Negação_Dupla','arv'=>$arvore];
            }
            elseif($no->getValorNo()->getTipoPredicado()=='CONJUNCAO' and $no->getValorNo()->getNegadoPredicado()==0 and $regra==2){
                $array_filhos = $this->regras->conjuncao($no->getValorNo());
                $no->utilizado(true);
                $this->criarNoSemBifucacao($noInsercao,$arvore,$array_filhos,$no->getLinhaNo());
                return ['sucesso'=>true, 'messagem'=>'Conjunção','arv'=>$arvore];
            }
            elseif ($no->getValorNo()->getTipoPredicado()== 'DISJUNCAO' and $no->getValorNo()->getNegadoPredicado()==1  and $regra==3){
                $array_filhos = $this->regras->disjuncaoNeg($no->getValorNo());
                $no->utilizado(true);
                $this->criarNoSemBifucacao($noInsercao,$arvore,$array_filhos,$no->getLinhaNo());
                return ['sucesso'=>true, 'messagem'=>'Negação_Disjunção','arv'=>$arvore];
            }
            elseif ($no->getValorNo()->getTipoPredicado()== 'CONDICIONAL' and $no->getValorNo()->getNegadoPredicado()==1  and $regra==4) {
                $array_filhos = $this->regras->condicionalNeg($no->getValorNo());
                $no->utilizado(true);
                $this->criarNoSemBifucacao($noInsercao,$arvore,$array_filhos,$no->getLinhaNo());
                return ['sucesso'=>true, 'messagem'=>'Negacão_Condicional','arv'=>$arvore];
            }
            elseif($no->getValorNo()->getTipoPredicado()=='DISJUNCAO' and $no->getValorNo()->getNegadoPredicado()==0  and $regra==5){
                $array_filhos = $this->regras->disjuncao($no->getValorNo());
                 $no->utilizado(true);
                 $this->criarNoBifurcado($noInsercao,$arvore,$array_filhos,$no->getLinhaNo());
                 return ['sucesso'=>true, 'messagem'=>'Disjunção','arv'=>$arvore];
            }
            elseif ($no->getValorNo()->getTipoPredicado()== 'CONDICIONAL' and $no->getValorNo()->getNegadoPredicado()==0  and $regra==6){
                $array_filhos = $this->regras->condicional($no->getValorNo());
                $no->utilizado(true);
                $this->criarNoBifurcado($noInsercao,$arvore,$array_filhos,$no->getLinhaNo());
                return ['sucesso'=>true, 'messagem'=>'Condicional','arv'=>$arvore];
            }
            elseif ($no->getValorNo()->getTipoPredicado()== 'BICONDICIONAL' and $no->getValorNo()->getNegadoPredicado()==0  and $regra==7){
                $array_filhos = $this->regras->bicondicional($no->getValorNo());
                 $no->utilizado(true);
                 $this->criarNoBifurcadoDuplo($noInsercao,$arvore,$array_filhos,$no->getLinhaNo());
                 return ['sucesso'=>true, 'messagem'=>'Bicondicional','arv'=>$arvore];
            }
            elseif ($no->getValorNo()->getTipoPredicado()== 'CONJUNCAO' and $no->getValorNo()->getNegadoPredicado()==1  and $regra==8){
                $array_filhos = $this->regras->conjuncaoNeg($no->getValorNo());
                $no->utilizado(true);
                $this->criarNoBifurcado($noInsercao,$arvore,$array_filhos,$no->getLinhaNo());
                return ['sucesso'=>true, 'messagem'=>'Negação_Conjunção','arv'=>$arvore];
            }
            elseif ($no->getValorNo()->getTipoPredicado()== 'BICONDICIONAL' and $no->getValorNo()->getNegadoPredicado()==1  and $regra==9){
                $array_filhos = $this->regras->bicondicionalNeg($no->getValorNo());
                 $no->utilizado(true);
                 $this->criarNoBifurcadoDuplo($noInsercao,$arvore,$array_filhos,$no->getLinhaNo());
                 return ['sucesso'=>true, 'messagem'=>'Negação_Bicondicional','arv'=>$arvore];

                }
            return ['sucesso'=>false, 'messagem'=>'Regra Invalida'];

        }

     }

     public function gerarArvorePassoPasso($arvore,$listaDerivacoes){
        foreach ($listaDerivacoes as $derivacao){
            $noInsercao = $this->proximoNoParaInsercao($arvore);

            $listaNos=$this->getNoslinha($arvore, $derivacao['linha']);

            $no=[];
            foreach($listaNos as $noValido){
                $noDescendente =$this->isDecendente($noValido,$noInsercao);
                if($noDescendente!=false){
                    $no=$noValido;
                }
            }


            if($derivacao['regra']==9){
                $array_filhos = $this->regras->bicondicionalNeg($no->getValorNo());
                $no->utilizado(true);
                $this->criarNoBifurcadoDuplo($noInsercao,$arvore,$array_filhos,$no->getLinhaNo());
            }
            elseif($derivacao['regra']==8){
                $array_filhos = $this->regras->conjuncaoNeg($no->getValorNo());
                $no->utilizado(true);
                $this->criarNoBifurcado($noInsercao,$arvore,$array_filhos,$no->getLinhaNo());
            }
            elseif($derivacao['regra']==7){
                $array_filhos = $this->regras->bicondicional($no->getValorNo());
                $no->utilizado(true);
                $this->criarNoBifurcadoDuplo($noInsercao,$arvore,$array_filhos,$no->getLinhaNo());
            }
            elseif($derivacao['regra']==6){
                $array_filhos = $this->regras->condicional($no->getValorNo());
                $no->utilizado(true);
                $this->criarNoBifurcado($noInsercao,$arvore,$array_filhos,$no->getLinhaNo());
            }
            elseif($derivacao['regra']==5){
                $array_filhos = $this->regras->disjuncao($no->getValorNo());
                $no->utilizado(true);
                $this->criarNoBifurcado($noInsercao,$arvore,$array_filhos,$no->getLinhaNo());
            }
            elseif($derivacao['regra']==4){
                $array_filhos = $this->regras->condicionalNeg($no->getValorNo());
                $no->utilizado(true);
                $this->criarNoSemBifucacao($noInsercao,$arvore,$array_filhos,$no->getLinhaNo());
            }
            elseif($derivacao['regra']==3){
                $array_filhos = $this->regras->disjuncaoNeg($no->getValorNo());
                $no->utilizado(true);
                $this->criarNoSemBifucacao($noInsercao,$arvore,$array_filhos,$no->getLinhaNo());
            }
            elseif($derivacao['regra']==2){
                $array_filhos = $this->regras->conjuncao($no->getValorNo());
                $no->utilizado(true);
                $this->criarNoSemBifucacao($noInsercao,$arvore,$array_filhos,$no->getLinhaNo());
            }
            else{
                $array_filhos =$this->regras->DuplaNeg($no->getValorNo());
                $no->utilizado(true);
                $this->criarNo($noInsercao,$arvore,$array_filhos,$no->getLinhaNo());

            }


        }
        return $arvore;


    }

}