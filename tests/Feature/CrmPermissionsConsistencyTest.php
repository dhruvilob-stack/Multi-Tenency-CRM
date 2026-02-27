<?php

namespace Tests\Feature;

use App\Support\CrmAccess;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;
use Tests\TestCase;

class CrmPermissionsConsistencyTest extends TestCase
{
    public function test_all_filament_crm_modules_exist_in_permission_definitions_and_role_matrix(): void
    {
        $filamentModules = $this->discoverFilamentCrmModules();
        $definedModules = CrmAccess::definedModules();

        $missingInDefinitions = array_values(array_diff($filamentModules, $definedModules));

        $this->assertSame([], $missingInDefinitions, 'Missing module labels for: '.implode(', ', $missingInDefinitions));

        $reflection = new \ReflectionClass(CrmAccess::class);
        $matrix = $reflection->getConstant('MATRIX');

        $this->assertIsArray($matrix);

        foreach ($matrix as $role => $roleModules) {
            $this->assertIsArray($roleModules);

            $missingInRoleMatrix = array_values(array_diff($filamentModules, array_keys($roleModules)));

            $this->assertSame(
                [],
                $missingInRoleMatrix,
                "Role [{$role}] is missing module matrix entries for: ".implode(', ', $missingInRoleMatrix)
            );
        }
    }

    /**
     * @return array<int, string>
     */
    private function discoverFilamentCrmModules(): array
    {
        $directory = new RecursiveDirectoryIterator(app_path('Filament'));
        $iterator = new RecursiveIteratorIterator($directory);
        $phpFiles = new RegexIterator($iterator, '/^.+\\.php$/i');

        $modules = [];

        foreach ($phpFiles as $file) {
            $content = file_get_contents((string) $file);

            if (! is_string($content)) {
                continue;
            }

            if (preg_match_all("/crmModule\\s*=\\s*'([a-z_]+)'/", $content, $matches)) {
                $modules = array_merge($modules, $matches[1]);
            }
        }

        $modules = array_values(array_unique($modules));
        sort($modules);

        return $modules;
    }
}
