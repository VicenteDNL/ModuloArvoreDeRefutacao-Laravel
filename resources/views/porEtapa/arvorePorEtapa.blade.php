@extends('baseEtapa')
{{-- @section('arovreObJ',$json) --}}
@section('alternativas')
<div class="card" style="margin-top: 20px;">
        <div class="card-body">
            <div class="container-fluid">
                <form method="post" action="{{URL::to('/porEtapa/gerando')}}" enctype="multipart/form-data">
                    {{ csrf_field() }}

                    <div class="row text-center">
                        <div class="col">
                            <div class="custom-control custom-radio">
                                <input type="radio" id="customRadio1" name="customRadio" class="custom-control-input">
                                <label class="custom-control-label" for="customRadio1">{{$regras[0]}}</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="custom-control custom-radio">
                                <input type="radio" id="customRadio2" name="customRadio" class="custom-control-input">
                                <label class="custom-control-label" for="customRadio2">{{$regras[1]}}</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="custom-control custom-radio">
                                <input type="radio" id="customRadio3" name="customRadio" class="custom-control-input">
                                <label class="custom-control-label" for="customRadio3">{{$regras[2]}}</label>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="form-group">
                        <button type="submit" class="btn btn-info btn-lg btn-block btn-sm">Aplicar Regra</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection


@section('col-8')
<svg width="600" height="600">
        @for($i = 1 ; $i<count($arv);$i++)
        @if($arv[$i-1]['posY']>=($arv[$i]['posY']))
            @for($e = $i-1 ; $e>0;$e--)
                @if($arv[$e]['posY']<($arv[$i]['posY']))
                    <line x1={{$arv[$e]['posX']}} y1={{$arv[$e]['posY']+27}} x2={{$arv[$i]['posX']}} y2={{$arv[$i]['posY']-27}} stroke="rgb(105,105,105)" stroke-width=2   stroke-linecap="butt"/>
                    @break
                @endif
            @endfor
        @else
            <line x1={{$arv[$i-1]['posX']}} y1={{$arv[$i-1]['posY']+27}} x2={{$arv[$i]['posX']}} y2={{$arv[$i]['posY']-27}} stroke="rgb(105,105,105)" stroke-width=2   stroke-linecap="butt"/>
        @endif
    @endfor
@foreach($arv as $valor)

        <circle cx={{$valor['posX']}} cy={{$valor['posY']+27}} r="4" fill="#4682B4"/>
        <circle cx={{$valor['posX']}} cy={{$valor['posY']-27}} r="4" fill="#4682B4"/>
    <text font-size="20" font-weight="bold" fill="rgb(105,105,105)" font-family="Helvetica, sans-serif, Arial" x={{30}} y={{$valor['posY']+5}}>{{$valor['arv']->getLinhaNo()}}.</text>

    <defs>
        <linearGradient id="grad1" x1="30%" y1="0%" x2="90%" y2="50%">
            <stop offset="0%" style="stop-color:rgb(32,178,170);stop-opacity:1" />
            <stop offset="100%" style="stop-color:rgb(0,128,128);stop-opacity:1" />
        </linearGradient>
    </defs>
    <rect x={{$valor['posX']-($valor['tmh']/2)}} y={{$valor['posY']-20}} rx=20 ry=20 width={{$valor['tmh']}} height="40" fill="url(#grad1)" stroke-width="3" stroke="rgb(105,105,105)" />
    <text text-anchor="middle" font-size="15" font-weight="bold" fill="white"  font-family="Helvetica, sans-serif, Arial" x={{$valor['posX']}} y={{$valor['posY']+5}}>{{$valor['str']}}</text>
    <text font-size="15" font-weight="bold" font-family="Helvetica, sans-serif, Arial" fill="rgb(105,105,105)" x={{$valor['posX']+($valor['tmh']/2)}} y={{$valor['posY']+25}}>{{$valor['arv']->getLinhaDerivacao()}}</text>


    @if ($valor['arv']->isUtilizado()==1)
        {{--<text font-size="15" font-family="Verdana" x={{$valor['posX']+60}} y={{$valor['posY']+5}}>Utilizado</text>--}}
    @endif

    @if ($valor['arv']->isFechado()==true)
        <line x1={{$valor['posX']-15}} y1={{$valor['posY']+15}} x2={{$valor['posX']+15}} y2={{$valor['posY']+40}} stroke="#8B0000" stroke-width=5   stroke-linecap="butt"/>
        <line x1={{$valor['posX']+15}} y1={{$valor['posY']+15}} x2={{$valor['posX']-15}} y2={{$valor['posY']+40}} stroke="#8B0000" stroke-width=5   stroke-linecap="butt"/>
        <text font-size="15" fill="rgb(105,105,105)" font-family="Helvetica, sans-serif, Arial" x={{$valor['posX']-5}} y={{$valor['posY']+70}}>{{$valor['arv']->getLinhaContradicao()}}</text>

    @endif


@endforeach

</svg>

@endsection
