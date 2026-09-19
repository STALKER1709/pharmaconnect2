<?php

use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Tâches planifiées (php artisan schedule:run)
|--------------------------------------------------------------------------
| Sur Windows (Laragon/XAMPP), planifier :
|   schtasks /create /sc minute /tn "PharmaConnect" /tr "C:\laragon\bin\php\php-8.2\php.exe C:\chemin\pharmaconnect\artisan schedule:run"
*/

Schedule::command('model:prune')->daily();
