<?php

namespace App\Core\Interfaces\Enum;
abstract class HttpCode
{
    const SUCCESS = 200;
    const BAD_REQUEST = 400;
    const UNAUTHORIZED = 401;
    const NOT_FOUND = 404;
    const UNPROCESSABLE_ENTITY = 422;
}
