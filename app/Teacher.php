<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Teacher
 *
 * @property int $id
 * @property string|null $google_id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string|null $remember_token
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Teacher whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Teacher whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Teacher whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Teacher whereGoogleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Teacher whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Teacher whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Teacher whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Teacher whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Teacher extends Model
{
    //
}
