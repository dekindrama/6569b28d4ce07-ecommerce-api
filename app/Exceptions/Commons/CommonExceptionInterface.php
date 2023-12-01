<?php

namespace App\Exceptions\Commons;

use Illuminate\Http\Response;

interface CommonExceptionInterface {
    function renderObject(): object;
    function renderResponse(): Response;
}
