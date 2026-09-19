<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /** Ajoute la valeur « annulee » à livraisons.statut (MySQL uniquement : SQLite ne contraint pas l'enum). */
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE livraisons MODIFY statut ENUM('disponible','assignee','acceptee','en_route','arrivee','livree','echec','annulee') NOT NULL DEFAULT 'disponible'");
        }
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE livraisons MODIFY statut ENUM('disponible','assignee','acceptee','en_route','arrivee','livree','echec') NOT NULL DEFAULT 'disponible'");
        }
    }
};
