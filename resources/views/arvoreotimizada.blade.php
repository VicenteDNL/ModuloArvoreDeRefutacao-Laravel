
@php
$posX = 300;
$posY = 50;
@endphp

<svg width="600" height="600">
<text font-size="20" font-family="Verdana" x={{30}} y={{$posY+5}}>{{$arv->getLinhaNo()}}.</text>
  <circle cx={{$posX}} cy={{$posY}} r="30" stroke="#778899" stroke-width="2" fill="#4682B4" />
  <text font-size="15" font-family="Verdana" x={{$posX-15}} y={{$posY+5}}>{{$arv->getValorNo()->getValorPredicado()}}</text>
  @if ($arv->isUtilizado()==true)
  <text font-size="15" font-family="Verdana" x={{$posX+60}} y={{$posY+5}}>Utilizado</text>
  @endif

  @if ($arv->isFechado()==true)
  <line x1={{$posX-15}} y1={{$posY+15}} x2={{$posX+15}} y2={{$posY+40}} stroke="#8B0000" stroke-width=5   stroke-linecap="butt"/>
  <line x1={{$posX+15}} y1={{$posY+15}} x2={{$posX-15}} y2={{$posY+40}} stroke="#8B0000" stroke-width=5   stroke-linecap="butt"/>
  <text font-size="15" font-family="Verdana" x={{$posX-5}} y={{$posY+50}}>{{$arv->getLinhaContraDeri()}} 1</text>
  @endif
 
  
</svg>

{{--@if (1==1)
<p>qq</p>
@else
<p>ww</p>
@endif


@for($i< 0 ; $i < 10 ; $i++)
@endfor -->


 @foreach($array as $valor)
@endforeach

@forelse($array as $valor)
@empty
@endfoeelse

@php
@endphp--}}