<svg width="600" height="600">
@foreach($arv as $valor)
    <text font-size="20" font-family="Ar" x={{30}} y={{$valor['posY']+5}}>{{$valor['arv']->getLinhaNo()}}.</text>

    <defs>
        <linearGradient id="grad1" x1="0%" y1="0%" x2="100%" y2="50%">
            <stop offset="0%" style="stop-color:rgb(229,229,229);stop-opacity:1" />
            <stop offset="100%" style="stop-color:rgb(167,167,167);stop-opacity:1" />
        </linearGradient>
    </defs>
    <circle cx= {{$valor['posX']}} cy= {{$valor['posY']}} r="30" stroke="#d7d7d7" stroke-width="2" fill="url(#grad1)" />
    <text font-size="15" font-family="Helvetica,Arial, sans-serif" x={{$valor['posX']-15}} y={{$valor['posY']+5}}>{{$valor['str']}}</text>

    @if ($valor['arv']->isUtilizado()==true)
        <text font-size="15" font-family="Verdana" x={{$valor['posX']+60}} y={{$valor['posY']+5}}>Utilizado</text>
    @endif

    @if ($valor['arv']->isFechado()==true)
        <line x1={{$valor['posX']-15}} y1={{$valor['posY']+15}} x2={{$valor['posX']+15}} y2={{$valor['posY']+40}} stroke="#8B0000" stroke-width=5   stroke-linecap="butt"/>
        <line x1={{$valor['posX']+15}} y1={{$valor['posY']+15}} x2={{$valor['posX']-15}} y2={{$valor['posY']+40}} stroke="#8B0000" stroke-width=5   stroke-linecap="butt"/>
        <text font-size="15" font-family="Verdana" x={{$valor['posX']-5}} y={{$valor['posY']+50}}>{{$valor['arv']->getLinhaContraDeri()}}</text>
    @endif
@endforeach
</svg>