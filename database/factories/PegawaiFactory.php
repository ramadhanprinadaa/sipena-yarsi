<?php

namespace Database\Factories;

use App\Models\Pegawai;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pegawai>
 */
class PegawaiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Pegawai::class;

    public function definition(): array
    {
        return [
            'unit_kerja_id' => $this->faker->randomElement([1,2,3,4,5,6,7,8,9,10,11]),
            'nip' => $this->faker->unique()->numerify('##################'),
            'ktp' => $this->faker->unique()->numerify('################'),
            'npwp' => $this->faker->unique()->numerify('################'),
            'nama' => $this->faker->name(),
            'gelar_depan' => $this->faker->optional()->randomElement(['Dr.', 'Ir.', 'H.']),
            'gelar_belakang' => $this->faker->optional()->randomElement(['S.Kom', 'S.T', 'M.Kom', 'M.T']),
            'tanggal_lahir' => $this->faker->dateTimeBetween('-25 years', '-20 years')->format('Y-m-d'),
            'tempat_lahir' => $this->faker->city(),
            'tanggal_bergabung' => $this->faker->dateTimeBetween('-10 years', 'now')->format('Y-m-d'),
            'tanggal_pensiun' => $this->faker->dateTimeBetween('+30 years', '+40 years')->format('Y-m-d'),
            'jenis_kelamin' => $this->faker->randomElement(['L', 'P']),
            'alamat_ktp' => $this->faker->address(),
            'alamat_domisili' => $this->faker->address(),
            'no_telpon' => $this->faker->phoneNumber(),
            'email_yarsi' => $this->faker->email(),
            'status' => 'active',
        ];
    }
}