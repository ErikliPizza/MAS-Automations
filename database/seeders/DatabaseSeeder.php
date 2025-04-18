<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Appointment;
use App\Models\Service;
use Illuminate\Database\Seeder;
use App\Models\Module;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class DatabaseSeeder extends Seeder
{
    /**
     * php artisan migrate:fresh
     * php artisan db:seed --class=DatabaseSeeder
     * Seed the application's database.
     */
    public function run(): void
    {
        $faker = fake();
        // 1. Seed Modules
        $modules = [
            ['name' => 'Appointment', 'description' => 'This is module a.', 'endpoint' => 'a', 'icon' => '<svg viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="m 6.5 0 c -3.578125 0 -6.5 2.921875 -6.5 6.5 s 2.921875 6.5 6.5 6.5 c 0.167969 0 0.335938 -0.007812 0.5 -0.019531 v -2.007813 c -0.164062 0.019532 -0.332031 0.027344 -0.5 0.027344 c -2.496094 0 -4.5 -2.003906 -4.5 -4.5 s 2.003906 -4.5 4.5 -4.5 s 4.5 2.003906 4.5 4.5 c 0 0.167969 -0.007812 0.335938 -0.027344 0.5 h 2.007813 c 0.011719 -0.164062 0.019531 -0.332031 0.019531 -0.5 c 0 -3.578125 -2.921875 -6.5 -6.5 -6.5 z m 0 3 c -0.277344 0 -0.5 0.222656 -0.5 0.5 v 2.5 h -1.5 c -0.277344 0 -0.5 0.222656 -0.5 0.5 s 0.222656 0.5 0.5 0.5 h 2 c 0.277344 0 0.5 -0.222656 0.5 -0.5 v -3 c 0 -0.277344 -0.222656 -0.5 -0.5 -0.5 z m 4.5 5 v 3 h -3 v 2 h 3 v 3 h 2 v -3 h 3 v -2 h -3 v -3 z m 0 0" fill="#2e3436"></path> </g></svg>'],
            ['name' => 'Clinic', 'description' => 'This is module b.', 'endpoint' => 'b', 'icon' => '<svg fill="#000000" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 97.396 97.396" xml:space="preserve"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g> <g> <path d="M79.247,16.472h-6.5v-9.18c0-2.419-1.969-4.388-4.387-4.388H54.344V1.95c0-1.077-0.873-1.95-1.95-1.95h-18.28 c-1.077,0-1.95,0.873-1.95,1.95v0.954H18.148c-2.419,0-4.387,1.968-4.387,4.388V79.44c0,2.419,1.968,4.387,4.387,4.387h6.501 v9.181c0,2.42,1.968,4.388,4.387,4.388h50.211c2.42,0,4.388-1.968,4.388-4.388V20.86C83.635,18.441,81.667,16.472,79.247,16.472z M24.649,20.86v58.093h-6.013V7.78h13.527v0.583c0,1.077,0.873,1.95,1.95,1.95h18.28c1.077,0,1.95-0.873,1.95-1.95V7.78H67.87 v8.693h-2.641v-0.955c0-1.077-0.873-1.95-1.949-1.95H45c-1.077,0-1.95,0.873-1.95,1.95v0.954H29.036 C26.617,16.472,24.649,18.441,24.649,20.86z M78.76,92.521H29.523V21.347h13.528v0.583c0,1.077,0.873,1.95,1.95,1.95H63.28 c1.078,0,1.951-0.874,1.951-1.95v-0.583h13.526v71.173H78.76z"></path> <rect x="37.242" y="59.707" width="33.798" height="6.825"></rect> <rect x="37.242" y="73.682" width="33.798" height="6.826"></rect> <polygon points="51.136,52.396 57.147,52.396 57.147,44.757 64.786,44.757 64.786,38.745 57.147,38.745 57.147,31.108 51.136,31.108 51.136,38.745 43.499,38.745 43.499,44.757 51.136,44.757 "></polygon> </g> </g> </g></svg>'],
            ['name' => 'Stock Tracking', 'description' => 'This is module c.', 'endpoint' => 'c', 'icon' => '<svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M18.8832 4.69719C19.2737 4.30667 19.9069 4.30667 20.2974 4.69719L23.888 8.28778L27.469 4.7068C27.8595 4.31628 28.4927 4.31628 28.8832 4.7068C29.2737 5.09733 29.2737 5.73049 28.8832 6.12102L25.3022 9.702L28.7827 13.1825C29.1732 13.573 29.1732 14.2062 28.7827 14.5967C28.3922 14.9872 27.759 14.9872 27.3685 14.5967L23.888 11.1162L20.3979 14.6063C20.0074 14.9968 19.3743 14.9968 18.9837 14.6063C18.5932 14.2158 18.5932 13.5826 18.9837 13.1921L22.4738 9.702L18.8832 6.1114C18.4927 5.72088 18.4927 5.08771 18.8832 4.69719Z" fill="#333333"></path> <path fill-rule="evenodd" clip-rule="evenodd" d="M23.86 15.0513C24.0652 14.9829 24.2871 14.9829 24.4923 15.0513L39.2705 19.9765C39.4691 20.0336 39.6499 20.1521 39.783 20.323L43.7861 25.4612C43.9857 25.7173 44.0485 26.0544 43.9545 26.3652C43.8902 26.5779 43.7579 26.7602 43.5821 26.887L28.1827 32.0159L24.965 27.8858C24.7754 27.6424 24.4839 27.5001 24.1753 27.5004C23.8667 27.5007 23.5755 27.6434 23.3863 27.8871L20.186 32.0093L4.74236 26.8577C4.58577 26.7329 4.46805 26.5621 4.40853 26.3652C4.31456 26.0544 4.37733 25.7173 4.57688 25.4612L8.50799 20.4154C8.62826 20.2191 8.81554 20.0652 9.04466 19.9889L23.86 15.0513ZM35.8287 20.9376L24.1802 24.8197L12.5277 20.9362L24.1762 17.0541L35.8287 20.9376Z" fill="#333333"></path> <path d="M28.1442 34.1368L39.991 30.1911L39.9905 36.7628C39.9905 38.054 39.1642 39.2003 37.9392 39.6086L25.1762 43.863V31.4111L27.0393 33.8026C27.2997 34.1368 27.7423 34.2706 28.1442 34.1368Z" fill="#333333"></path> <path d="M23.1762 31.4191V43.863L10.4131 39.6086C9.18811 39.2003 8.36183 38.054 8.36175 36.7628L8.36132 30.1732L20.2251 34.1306C20.6277 34.2649 21.0712 34.1305 21.3314 33.7953L23.1762 31.4191Z" fill="#333333"></path> </g></svg>'],
            ['name' => 'Employee Tracking', 'description' => 'This is module d.', 'endpoint' => 'd', 'icon' => '<svg fill="#000000" viewBox="0 0 36 36" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <title>employee_group_line</title> <g id="e1709d41-49e9-409f-9912-e2615f9236f6" data-name="Layer 3"> <path d="M18.42,16.31a5.7,5.7,0,1,1,5.76-5.7A5.74,5.74,0,0,1,18.42,16.31Zm0-9.4a3.7,3.7,0,1,0,3.76,3.7A3.74,3.74,0,0,0,18.42,6.91Z"></path> <path d="M18.42,16.31a5.7,5.7,0,1,1,5.76-5.7A5.74,5.74,0,0,1,18.42,16.31Zm0-9.4a3.7,3.7,0,1,0,3.76,3.7A3.74,3.74,0,0,0,18.42,6.91Z"></path> <path d="M21.91,17.65a20.6,20.6,0,0,0-13,2A1.77,1.77,0,0,0,8,21.25v3.56a1,1,0,0,0,2,0V21.38a18.92,18.92,0,0,1,12-1.68Z"></path> <path d="M33,22H26.3V20.52a1,1,0,0,0-2,0V22H17a1,1,0,0,0-1,1V33a1,1,0,0,0,1,1H33a1,1,0,0,0,1-1V23A1,1,0,0,0,33,22ZM32,32H18V24h6.3v.41a1,1,0,0,0,2,0V24H32Z"></path> <rect x="21.81" y="27.42" width="5.96" height="1.4"></rect> <path d="M10.84,12.24a18,18,0,0,0-7.95,2A1.67,1.67,0,0,0,2,15.71v3.1a1,1,0,0,0,2,0v-2.9a16,16,0,0,1,7.58-1.67A7.28,7.28,0,0,1,10.84,12.24Z"></path> <path d="M33.11,14.23a17.8,17.8,0,0,0-7.12-2,7.46,7.46,0,0,1-.73,2A15.89,15.89,0,0,1,32,15.91v2.9a1,1,0,1,0,2,0v-3.1A1.67,1.67,0,0,0,33.11,14.23Z"></path> <path d="M10.66,10.61c0-.23,0-.45,0-.67a3.07,3.07,0,0,1,.54-6.11,3.15,3.15,0,0,1,2.2.89,8.16,8.16,0,0,1,1.7-1.08,5.13,5.13,0,0,0-9,3.27,5.1,5.1,0,0,0,4.7,5A7.42,7.42,0,0,1,10.66,10.61Z"></path> <path d="M24.77,1.83a5.17,5.17,0,0,0-3.69,1.55,7.87,7.87,0,0,1,1.9,1,3.14,3.14,0,0,1,4.93,2.52,3.09,3.09,0,0,1-1.79,2.77,7.14,7.14,0,0,1,.06.93,7.88,7.88,0,0,1-.1,1.2,5.1,5.1,0,0,0,3.83-4.9A5.12,5.12,0,0,0,24.77,1.83Z"></path> </g> </g></svg>'],
            ['name' => 'Accounting', 'description' => 'This is module e.', 'endpoint' => 'e', 'icon' => '<svg fill="#000000" viewBox="0 0 50 50" version="1.2" baseProfile="tiny" xmlns="http://www.w3.org/2000/svg" overflow="inherit"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><path d="M41 1h-32c-2.2 0-4 1.8-4 4v40c0 2.2 1.8 4 4 4h32c2.2 0 4-1.8 4-4v-40c0-2.2-1.8-4-4-4zm-24 40c0 1.1-.9 2-2 2h-4c-1.1 0-2-.9-2-2v-1c0-1.1.9-2 2-2h4c1.1 0 2 .9 2 2v1zm0-8c0 1.1-.9 2-2 2h-4c-1.1 0-2-.9-2-2v-1c0-1.1.9-2 2-2h4c1.1 0 2 .9 2 2v1zm0-8c0 1.1-.9 2-2 2h-4c-1.1 0-2-.9-2-2v-1c0-1.1.9-2 2-2h4c1.1 0 2 .9 2 2v1zm12 16c0 1.1-.9 2-2 2h-4c-1.1 0-2-.9-2-2v-1c0-1.1.9-2 2-2h4c1.1 0 2 .9 2 2v1zm0-8c0 1.1-.9 2-2 2h-4c-1.1 0-2-.9-2-2v-1c0-1.1.9-2 2-2h4c1.1 0 2 .9 2 2v1zm0-8c0 1.1-.9 2-2 2h-4c-1.1 0-2-.9-2-2v-1c0-1.1.9-2 2-2h4c1.1 0 2 .9 2 2v1zm12 16c0 1.1-.9 2-2 2h-4c-1.1 0-2-.9-2-2v-1c0-1.1.9-2 2-2h4c1.1 0 2 .9 2 2v1zm0-8c0 1.1-.9 2-2 2h-4c-1.1 0-2-.9-2-2v-1c0-1.1.9-2 2-2h4c1.1 0 2 .9 2 2v1zm0-8c0 1.1-.9 2-2 2h-4c-1.1 0-2-.9-2-2v-1c0-1.1.9-2 2-2h4c1.1 0 2 .9 2 2v1zm0-9c0 1.1-.9 2-2 2h-28c-1.1 0-2-.9-2-2v-8c0-1.1.9-2 2-2h28c1.1 0 2 .9 2 2v8z"></path></g></svg>'],
        ];

        Module::insert($modules);

        // 2. Seed Tenancy, Admin and Additional Users with Modules
        for ($i = 1; $i <= 5; $i++) {
            $tenant = Tenant::create([
                'organization_name' => fake()->company(),  // Using Faker to generate a fake company name
                'expiry_time' => '2024-07-21',
                'status' => 'active'
            ]);
            $tenant->modules()->attach(Module::all());
            $root = User::create([
                'tenant_id' => $tenant->id,
                'name' => $faker->firstName,
                'surname' => $faker->lastName,
                'email' => $faker->unique()->safeEmail,
                'password' => bcrypt('password'), // or use $faker->password

                'role' => 'root',
                'status' => 'active',

                'phone' => $faker->phoneNumber, // Add phone number
                'title' => $faker->jobTitle,
                'birthday' => $faker->dateTimeBetween('-70 years', '-18 years')->format('Y-m-d'),
                'id_number' => $faker->numerify('###############'),
                'bank_account' => $faker->iban(),

                'start_date_of_work' => $faker->dateTimeBetween('-5 years', 'now')->format('Y-m-d'),
                'end_date_of_work' => $faker->dateTimeBetween('now', '+5 years')->format('Y-m-d'),
                'reason_of_leaving' => $faker->optional()->sentence,
                'salary' => $faker->randomFloat(2, 1000, 10000),
            ]);
            // Create an admin user related to the tenant
            $admin = User::create([
                'tenant_id' => $tenant->id,
                'name' => $faker->firstName,
                'surname' => $faker->lastName,
                'email' => $faker->unique()->safeEmail,
                'password' => bcrypt('password'), // or use $faker->password

                'role' => 'admin',
                'status' => 'active',

                'phone' => $faker->phoneNumber, // Add phone number
                'title' => $faker->jobTitle,
                'birthday' => $faker->dateTimeBetween('-70 years', '-18 years')->format('Y-m-d'),
                'id_number' => $faker->numerify('###############'),
                'bank_account' => $faker->iban(),

                'start_date_of_work' => $faker->dateTimeBetween('-5 years', 'now')->format('Y-m-d'),
                'end_date_of_work' => $faker->dateTimeBetween('now', '+5 years')->format('Y-m-d'),
                'reason_of_leaving' => $faker->optional()->sentence,
                'salary' => $faker->randomFloat(2, 1000, 10000),
            ]);
            $admin->modules()->attach(Module::all(), ['tenant_id' => $tenant->id]);
            $root->modules()->attach(Module::all(), ['tenant_id' => $tenant->id]);
            $service1 = Service::create([
                'tenant_id' => $tenant->id,
                'slug' => 'a ' . $i,
                'name' => 'admin',
                'email' => 'admin' . $i . '@mail.com',
                'status' => 'active',
                'phone' => fake()->phoneNumber, // Add phone number
                'opening_time' => '10:00:00',
                'closing_time' => '18:00:00'
            ]);

            $service2 = Service::create([
                'tenant_id' => $tenant->id,
                'slug' => 'b ' . $i,
                'name' => 'root',
                'email' => 'root' . $i . '@mail.com',
                'status' => 'active',
                'phone' => fake()->phoneNumber, // Add phone number
                'opening_time' => '12:00:00',
                'closing_time' => '23:00:00'
            ]);
            Appointment::create([
                'user_id' => $admin->id,
                'tenant_id' => $tenant->id,
                'service_id' => $service1->id,
                'slug' => 'c ' . $i,
                'name' => 'Selin Aydın',
                'email' => 'root' . $i . '@mail.com',
                'status' => 'booked',
                'phone' => fake()->phoneNumber, // Add phone number
                'start_time' => '2024-02-09 13:15:00',
                'end_time' => '2024-02-09 13:45:00',
            ]);
            Appointment::create([
                'user_id' => $admin->id,
                'tenant_id' => $tenant->id,
                'service_id' => $service1->id,
                'slug' => 'd ' . $i,
                'name' => 'Ahmet Kaya',
                'email' => 'root' . $i . '@mail.com',
                'status' => 'booked',
                'phone' => fake()->phoneNumber, // Add phone number
                'start_time' => '2024-02-09 17:15:00',
                'end_time' => '2024-02-09 17:30:00',
            ]);
            // Create 3 additional users and attach modules to them
            for ($j = 1; $j <= 3; $j++) {
                $user = User::create([
                    'tenant_id' => $tenant->id,
                    'name' => $faker->firstName,
                    'surname' => $faker->lastName,
                    'email' => $faker->unique()->safeEmail,
                    'password' => bcrypt('password'), // or use $faker->password

                    'role' => 'additional',
                    'status' => 'active',

                    'phone' => $faker->phoneNumber, // Add phone number
                    'title' => $faker->jobTitle,
                    'birthday' => $faker->dateTimeBetween('-70 years', '-18 years')->format('Y-m-d'),
                    'id_number' => $faker->numerify('###############'),
                    'bank_account' => $faker->iban(),

                    'start_date_of_work' => $faker->dateTimeBetween('-5 years', 'now')->format('Y-m-d'),
                    'end_date_of_work' => $faker->dateTimeBetween('now', '+5 years')->format('Y-m-d'),
                    'reason_of_leaving' => $faker->optional()->sentence,
                    'salary' => $faker->randomFloat(2, 1000, 10000),
                ]);

                // Attach 3 random modules to the user
                $moduleIds = Module::inRandomOrder()->take(2)->pluck('id');
                $user->modules()->attach($moduleIds, ['tenant_id' => $tenant->id]);
            }
        }
    }
}
