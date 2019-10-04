<svg width="600" height="1000">
@foreach($arv as $valor)
    <text font-size="20" font-family="Ar" x={{30}} y={{$valor['posY']+5}}>{{$valor['arv']->getLinhaNo()}}.</text>

    <defs>
        <linearGradient id="grad1" x1="30%" y1="0%" x2="90%" y2="50%">
            <stop offset="0%" style="stop-color:rgb(229,229,229);stop-opacity:1" />
            <stop offset="100%" style="stop-color:rgb(167,167,167);stop-opacity:1" />
        </linearGradient>
    </defs>
    <rect x={{$valor['posX']-75}} y={{$valor['posY']-20}} rx=20 ry=20 width="150" height="40" fill="url(#grad1)" />
    <text font-size="15" font-family="Helvetica,Arial, sans-serif" x={{$valor['posX']-15}} y={{$valor['posY']+5}}>{{$valor['str']}}</text>
    <text font-size="15" font-family="Verdana" x={{$valor['posX']-5}} y={{$valor['posY']+50}}>{{$valor['arv']->getLinhaDerivacao()}}</text>
    @if ($valor['arv']->isUtilizado()==1)
        {{--<text font-size="15" font-family="Verdana" x={{$valor['posX']+60}} y={{$valor['posY']+5}}>Utilizado</text>--}}
    @endif

    @if ($valor['arv']->isFechado()==true)
        <line x1={{$valor['posX']-15}} y1={{$valor['posY']+15}} x2={{$valor['posX']+15}} y2={{$valor['posY']+40}} stroke="#8B0000" stroke-width=5   stroke-linecap="butt"/>
        <line x1={{$valor['posX']+15}} y1={{$valor['posY']+15}} x2={{$valor['posX']-15}} y2={{$valor['posY']+40}} stroke="#8B0000" stroke-width=5   stroke-linecap="butt"/>
        <text font-size="15" font-family="Verdana" x={{$valor['posX']-5}} y={{$valor['posY']+70}}>{{$valor['arv']->getLinhaContradicao()}}</text>
        
    @endif

    
@endforeach

</svg>