<?php
use Illuminate\Database\Capsule\Manager as Capsule;

Capsule::schema()->dropIfExists('posts');
Capsule::schema()->create('posts', function ($t) {
  $t->increments('id');
  $t->string('title');
  $t->text('content');
  $t->unsignedInteger('user_id');
  $t->timestamp('created_at')->useCurrent();
});
