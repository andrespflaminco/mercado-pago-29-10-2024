<?php

namespace Illuminate\Foundation\Auth;

use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Http\FormRequest;
use App\Traits\ZohoCRMTrait;
use Carbon\Carbon;

class EmailVerificationRequest extends FormRequest
{
    use ZohoCRMTrait;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (! hash_equals((string) $this->route('id'),
                          (string) $this->user()->getKey())) {
            return false;
        }

        if (! hash_equals((string) $this->route('hash'),
                          sha1($this->user()->getEmailForVerification()))) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }

    /**
     * Fulfill the email verification request.
     *
     * @return void
     */
    public function fulfill()
    {
        if (! $this->user()->hasVerifiedEmail()) {
            $this->user()->markEmailAsVerified();

            event(new Verified($this->user()));
            
            $this->user->last_login = Carbon::now();
            $this->user->cantidad_login = $this->user->cantidad_login + 1;
            $this->user->save();
         
            // Llama a tu método personalizado después de la verificación del email
            if($this->user()->lead_soho_id != null){
            $this->updateLeadFromUser($this->user()->id);
            }
            
        }
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        return $validator;
    }
}
