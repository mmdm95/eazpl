<?php

use Eazpl\App\Database\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

final class CreateLabelTemplatesTable implements Migration
{
    public function up(): void
    {
        Capsule::schema()->create('label_templates', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->json('state');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Capsule::schema()->dropIfExists('label_templates');
    }
}

return new CreateLabelTemplatesTable();
