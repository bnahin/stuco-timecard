<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\StudentInfo
 *
 * @property int $id
 * @property int|null $user_id
 * @property int $student_id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property int $grade
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property-read \App\User|null $user
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\StudentInfo onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StudentInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StudentInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StudentInfo whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StudentInfo whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StudentInfo whereGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StudentInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StudentInfo whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StudentInfo whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StudentInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StudentInfo whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\StudentInfo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\StudentInfo withoutTrashed()
 * @mixin \Eloquent
 * @property-read mixed $full_name
 */
class StudentInfo extends Model
{
    use SoftDeletes;

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $table = 'student_info';

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFullNameAttribute() {
        return "{$this->first_name} {$this->last_name}";
    }
}
