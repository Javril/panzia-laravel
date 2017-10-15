<?php

namespace App\Api\V1\Requests;

use Config;
use Dingo\Api\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function rules()
    {
        return Config::get('boilerplate.sign_up_by_admin.validation_rules');
    }

    public function authorize()
    {
        return true;
    }
}
