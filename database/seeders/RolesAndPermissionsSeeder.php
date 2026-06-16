<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = $this->createRole(
            'admin',
            'Admin',
            'All privilege untuk mengelola, melihat, menambah, mengedit, dan menghapus seluruh data.',
            ['preview', 'insert', 'edit', 'delete', 'manage_users', 'all_privilege']
        );

        $guruRole = $this->createRole(
            'guru',
            'Guru',
            'Akses untuk preview, insert, dan edit data akademik tanpa hak hapus.',
            ['preview', 'insert', 'edit']
        );

        $siswaRole = $this->createRole(
            'siswa',
            'Siswa',
            'Akses preview untuk melihat data akademik.',
            ['preview']
        );

        Role::where('name', 'super_admin')->delete();

        $this->createUser('Admin Sekolah', 'admin@smk.sch.id', $adminRole);
        $this->createUser('Rahmat Kusuma, M.T.', 'rahmat.guru@smk.sch.id', $guruRole);
        $this->createUser('Aditya Pratama', 'aditya.siswa@smk.sch.id', $siswaRole);
    }

    private function createRole(string $name, string $label, string $description, array $privileges): Role
    {
        return Role::updateOrCreate(
            ['name' => $name],
            [
                'label' => $label,
                'description' => $description,
                'privileges' => $privileges,
            ]
        );
    }

    private function createUser(string $name, string $email, Role $role): User
    {
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make('password'),
            ]
        );

        $user->roles()->syncWithoutDetaching([$role->id]);

        return $user;
    }
}
