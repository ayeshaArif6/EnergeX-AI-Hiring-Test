<?php
use Illuminate\Database\Capsule\Manager as Capsule;

Capsule::schema()->dropIfExists('users');
Capsule::schema()->create('users', function ($t) {
  $t->increments('id');
  $t->string('name');
  $t->string('email')->unique();
  $t->string('password');
  $t->string('role')->default('user');
});
