<?php

namespace App\Imports;

use App\Models\Division;
use App\Models\Education;
use App\Models\JobTitle;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;

class UsersImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    public function __construct(public bool $save = true) {}

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        try {
            $division_id = Division::firstOrCreate(['name' => $row['division']])->id;
            $job_title_id = JobTitle::firstOrCreate(['name' => $row['job_title']])->id;
            $education_id = Education::firstOrCreate(['name' => $row['education']])->id;

            $user = new User([
                'nip' => $row['nip'],
                'name' => $row['name'],
                'email' => $row['email'],
                'phone' => $row['phone'],
                'gender' => $row['gender'],
                'birth_date' => $row['birth_date'],
                'birth_place' => $row['birth_place'],
                'address' => $row['address'],
                'city' => $row['city'],
                'education_id' => $education_id,
                'division_id' => $division_id,
                'job_title_id' => $job_title_id,
                'password' => Hash::make($row['password']),
                'raw_password' => $row['password'],
                'created_at' => $row['created_at'] ?? now(),
                'updated_at' => $row['updated_at'] ?? now(),
            ]);

            if ($this->save) {
                $user->save();
            }

            return $user;
        } catch (\Exception $e) {
            Log::error("Error importing user: " . $e->getMessage());
            return null;
        }
    }

    public function rules(): array
    {
        return [
            'nip' => ['required', 'string', 'max:255', Rule::unique('users', 'nip')],
            'name' => ['required', 'string'],
            'email' => ['required', 'string', 'email', Rule::unique('users', 'email')],
            'phone' => ['required', 'string'],
            'gender' => ['required', 'string'],
            'birth_date' => ['required', 'date'],
            'birth_place' => ['required', 'string'],
            'address' => ['required', 'string'],
            'city' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            Log::warning('Validation failure on row ' . $failure->row() . ': ' . implode(', ', $failure->errors()));
        }
    }
}
