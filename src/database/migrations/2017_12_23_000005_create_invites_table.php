<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invites', function (Blueprint $table) {
            $table->increments('id');
            $table->hashslug();
            $table->name();
            $table->string('token', 64);
            $table->actedStatus('is_accepted');
            $table->actedAt('accepted_at');
            $table->actedBy('invited_by');
            $table->unsignedInteger('inviteable_id');
            $table->string('inviteable_type');
            $table->expired();
            $table->standardTime();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invites');
    }
}
