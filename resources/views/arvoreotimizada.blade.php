<svg width="600" height="600">
@foreach($arv as $valor)
    <text font-size="20" font-family="Verdana" x={{30}} y={{$valor['posY']+5}}>{{$valor['arv']->getLinhaNo()}}.</text>
    <circle cx= {{$valor['posX']}} cy= {{$valor['posY']}} r="30" stroke="#778899" stroke-width="2" fill="#4682B4" />
    <text font-size="15" font-family="Verdana" x={{$valor['posX']-15}} y={{$valor['posY']+5}}>{{$valor['arv']->getValorNo()->getValorPredicado()}}</text>
    @if ($valor['arv']->isUtilizado()==true)
        <text font-size="15" font-family="Verdana" x={{$valor['posX']+60}} y={{$valor['posY']+5}}>Utilizado</text>
    @endif

    @if ($valor['arv']->isFechado()==true)
        <line x1={{$valor['posX']-15}} y1={{$valor['posY']+15}} x2={{$valor['posX']+15}} y2={{$valor['posY']+40}} stroke="#8B0000" stroke-width=5   stroke-linecap="butt"/>
        <line x1={{$valor['posX']-15}} y1={{$valor['posY']+15}} x2={{$valor['posX']-15}} y2={{$valor['posY']+40}} stroke="#8B0000" stroke-width=5   stroke-linecap="butt"/>
        <text font-size="15" font-family="Verdana" x={{$valor['posX']-5}} y={{$valor['posY']+50}}>{{$valor['arv']->getLinhaContraDeri()}}</text>
    @endif


@endforeach
</svg>