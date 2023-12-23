<?php 

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class JsonRequest extends FormRequest {

    /**
     * The data to be validated should be processed as JSON.
     * @return mixed
     */
    public function validationData()
    {
        return $this->json()->all();
    }
}
