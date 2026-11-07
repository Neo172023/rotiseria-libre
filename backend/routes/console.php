<?php
use Illuminate\Support\Facades\Artisan;
Artisan::command('rotiseria:info', function () {
    $this->info('Rotisería Libre: backend en construcción.');
})->purpose('Muestra el estado de desarrollo del backend');
