@extends('arvore')

@section('upload')
<svg width="700" height="660">
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
            <linearGradient id="grad1" x1="30%" y1="0%" x2="90%" y2="50%">
                <stop offset="0%" style="stop-color:rgb(32,178,170);stop-opacity:1" />
                <stop offset="100%" style="stop-color:rgb(0,128,128);stop-opacity:1" />
            </linearGradient>
        </defs>
{{--    <defs>--}}
{{--        <linearGradient id="grad1" x1="0" y1="80%" x2="80%" y2="50%">--}}
{{--            <stop offset="0%" style="stop-color:#3ef39f;stop-opacity:0.9" />--}}
{{--            <stop offset="100%" style="stop-color:#23c4f7;stop-opacity:0.9" />--}}
{{--        </linearGradient>--}}
{{--    </defs>--}}
  <rect x={{$valor['posX']-($valor['tmh']/2)}} y={{$valor['posY']-20}} rx=20 ry=20 width={{$valor['tmh']}} height="40" fill="url(#grad1)" stroke=#C0C0C0 stroke-width=2 {{--  stroke-dasharray="4 1"--}}/>
    <text text-anchor="middle" font-size="15" font-weight="bold" fill="white"  font-family="Helvetica, sans-serif, Arial" x={{$valor['posX']}} y={{$valor['posY']+5}}>{{$valor['str']}}</text>
    <text font-size="15" font-weight="bold" fill="rgb(175,175,175)" x={{$valor['posX']+($valor['tmh']/2)}} y={{$valor['posY']+25}}>{{$valor['arv']->getLinhaDerivacao()}}</text>


    @if ($valor['arv']->isUtilizado()==1)
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
