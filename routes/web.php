

<?php


Route::get('/', 'Base@Index');


Route::post('submit', 'Base@SalvarXml');

Route::post('Arvore', 'Base@CriarArvoreOtimizada');

Route::get('/porEtapa','Base@PorEtapa');


Route::post('Inicializado', 'Base@Inicializando');

Route::post('porEtapa/Gerando', 'Base@ValidaResposta');
