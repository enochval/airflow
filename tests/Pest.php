<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
uses(RefreshDatabase::class)->in('Feature');
uses(\Tests\CreatesApplication::class);

beforeEach()->createApplication();
