

<?php


Route::get('/', 'Base@Index');


Route::post('submit', 'Base@SalvarXml');

Route::post('Arvore', 'Base@CriarArvoreOtimizada');

Route::get('/porEtapa','Base@PorEtapa');


Route::post('porEtapa/inicializa', 'Base@CarragaXmlPorEtapa');


Route::post('porEtapa/Inicializado', 'Base@Inicializando');
