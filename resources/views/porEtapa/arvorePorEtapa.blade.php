
@extends('baseEtapa')
{{-- @section('arovreObJ',$json) --}}

@section('modal')
    @if($modal['sucesso']==true)
        <div class="modal fade" id="meuModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Opaaaa!</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        {{$modal['messagem']}}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn shadow btn-sm  {{--bg-gradient-blue--}}bg-gradient-green rounded-05rem "  data-dismiss="modal">
                            <span class="text-white text-center ml-2"><i class="fas fa-play text-18"></i></span>
                            <span class="text-white  text-center ">Tentar Novamente</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        {{--        baixar localmente--}}
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#meuModal').modal('show');
        })
    </script>
    @endif
@endsection

@section('alternativas')
<div class="card shadow-sm bg-white rounded-15">
    <div class="card-body d-flex justify-content-center">
    <div class="container-fluid">
        <form method="post" action="{{URL::to('Gerando')}}" enctype="multipart/form-data">
            {{ csrf_field() }}

            <input type="hidden"  name="idFormula" value={{$idFormula}} class="form-control">
            <input type="hidden"  name="derivacoes" value={{$listaDerivacoes}} class="form-control">
            
            <div class="row text-center">
              

                <div class="col-9">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Linha</span>
                            </div>
                            <input type="number" name="linha"  class="form-control" required>
                        </div>
                            
                <div class="row ">
                        <div class="col-4">
                    <div class="custom-control custom-radio">
                        <input type="radio" id="customRadio1" value={{trim($regras[0]['id'])}} name="regra" class="custom-control-input" required>
                        <label class="custom-control-label" for="customRadio1">{{str_replace("_", " da ", $regras[0]['str'])}}</label>
                    </div>
                    </div>
                        <div class="col-4">
                        <div class="custom-control custom-radio">
                            <input type="radio" id="customRadio2" value={{trim($regras[1]['id'])}} name="regra" class="custom-control-input" required>
                            <label class="custom-control-label" for="customRadio2">{{str_replace("_", " da ", $regras[1]['str'])}}</label>
                        </div>
                    </div>
                        <div class="col-4">
                        <div class="custom-control custom-radio">
                            <input type="radio" id="customRadio3" value={{trim($regras[2]['id'])}} name="regra" class="custom-control-input" required>
                            <label class="custom-control-label" for="customRadio3">{{str_replace("_", " da ", $regras[2]['str'])}}</label>
                        </div>
                    </div>
                </div>
            </div>
                <div class="col-3 d-flex align-items-center">
                    <div>
                        <button type="submit" class="btn shadow btn-lg   {{--bg-gradient-blue--}}bg-gradient-green rounded-05rem ">
                                <span class="text-white text-center ml-2"><i class="fas fa-play text-18"></i></span>
                                <span class="text-white  text-center ">Aplicar</span>
                            </button>
                        </div>
                    </div>

            </div>
    
        </form>
    </div>
</div>
</div>


@endsection


@section('col-8')
<svg width="700" height="600">
        @for($i = 1 ; $i<count($arv);$i++)
        @if($arv[$i-1]['posY']>=($arv[$i]['posY']))
            @for($e = $i-1 ; $e>0;$e--)
                @if($arv[$e]['posY']<($arv[$i]['posY']))
                    <line x1={{$arv[$e]['posX']}} y1={{$arv[$e]['posY']+27}} x2={{$arv[$i]['posX']}} y2={{$arv[$i]['posY']-27}} stroke="rgb(175,175,175)" stroke-width=1 stroke-linecap="butt"/>
                @break
                @endif
            @endfor
        @else
                <line x1={{$arv[$i-1]['posX']}} y1={{$arv[$i-1]['posY']+27}} x2={{$arv[$i]['posX']}} y2={{$arv[$i]['posY']-27}} stroke="rgb(175,175,175)" stroke-width=1 stroke-linecap="butt"/>
        @endif
    @endfor
@foreach($arv as $valor)
<circle cx={{$valor['posX']}} cy={{$valor['posY']+27}} r="3" fill="#AFAFA4"/>
<circle cx={{$valor['posX']}} cy={{$valor['posY']-27}} r="3" fill="#AFAFAF"/>
<text font-size="20" font-weight="bold" fill="rgb(175,175,175)" x={{30}} y={{$valor['posY']+5}}>Linha {{$valor['arv']->getLinhaNo()}}</text>
<defs>
        <linearGradient id="grad2" x1="30%" y1="0%" x2="90%" y2="50%">
            <stop offset="0%" style="stop-color:#0000FF;stop-opacity:1" />
            <stop offset="100%" style="stop-color:rgb(0,128,128);stop-opacity:1" />
        </linearGradient>
    </defs>
    <defs>
        <linearGradient id="grad1" x1="30%" y1="0%" x2="90%" y2="50%">
            <stop offset="0%" style="stop-color:rgb(32,178,170);stop-opacity:1" />
            <stop offset="100%" style="stop-color:rgb(0,128,128);stop-opacity:1" />
        </linearGradient>
    </defs>

    @if($proximoNoInsercao!=null)
        @if($proximoNoInsercao->getValorNo()->getValorPredicado()==$valor['arv']->getValorNo()->getValorPredicado() and $proximoNoInsercao->getLinhaNo()==$valor['arv']->getLinhaNo() and $proximoNoInsercao->getValorNo()->getNegadoPredicado()==$valor['arv']->getValorNo()->getNegadoPredicado() )
            <rect x={{$valor['posX']-($valor['tmh']/2)}} y={{$valor['posY']-20}} rx=20 ry=20 width={{$valor['tmh']}} height="40" fill="url(#grad1)" stroke-width="5" stroke="#FFFF00" />
        
        @else
            <rect x={{$valor['posX']-($valor['tmh']/2)}} y={{$valor['posY']-20}} rx=20 ry=20 width={{$valor['tmh']}} height="40" fill="url(#grad1)" stroke-width="2" stroke="#C0C0C0" />
        @endif
    @else
        <rect x={{$valor['posX']-($valor['tmh']/2)}} y={{$valor['posY']-20}} rx=20 ry=20 width={{$valor['tmh']}} height="40" fill="url(#grad1)" stroke-width="2" stroke="#C0C0C0" />
    @endif
    
    <text text-anchor="middle" font-size="15" font-weight="bold" fill="white"  font-family="Helvetica, sans-serif, Arial" x={{$valor['posX']}} y={{$valor['posY']+5}}>{{$valor['str']}}</text>
    <text font-size="15" font-weight="bold" fill="rgb(175,175,175)" x={{$valor['posX']+($valor['tmh']/2)}} y={{$valor['posY']+25}}>{{$valor['arv']->getLinhaDerivacao()}}</text>


    @if ($valor['arv']->isUtilizado()==true)
            <svg x={{$valor['posX']+($valor['tmh']/2)+12}} y={{$valor['posY']-10}} fill=#61CE61>
                <path d="M20.285 2l-11.285 11.567-5.286-5.011-3.714 3.716 9 8.728 15-15.285z"/>
            </svg>
    @endif

    @if ($valor['arv']->isFechado()==true)
        <line x1={{$valor['posX']-15}} y1={{$valor['posY']+15}} x2={{$valor['posX']+15}} y2={{$valor['posY']+40}} stroke="#DC0F4B" stroke-width=4 stroke-dasharray="1"/>
        <line x1={{$valor['posX']+15}} y1={{$valor['posY']+15}} x2={{$valor['posX']-15}} y2={{$valor['posY']+40}} stroke="#DC0F4B" stroke-width=4 stroke-dasharray="1"/>
        <text font-size="17" fill="#DC0F4B" x={{$valor['posX']-5}} y={{$valor['posY']+70}}>{{$valor['arv']->getLinhaContradicao()}}</text>

    @endif


@endforeach

</svg>

@endsection
