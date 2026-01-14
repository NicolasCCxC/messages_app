<?php
use Illuminate\Database\Migrations\Migration;
use App\Models\MembershipHasModules;
use App\Models\MembershipSubModule;

return new class extends Migration {
    public function up(): void
    {
        $name = 'Documentos electrónicos - Paquete 15 documentos';

        MembershipHasModules::where('membership_modules_id', 3)
            ->where(function ($q) {
                $q->whereNull('name')->orWhere('name', '');
            })
            ->whereHas('membershipSubmodules', function ($q) {
                $q->where('sub_module_id', 1);
            })
            ->update(['name' => $name]);

        MembershipSubModule::where('sub_module_id', 1)
            ->where(function ($q) {
                $q->whereNull('name')->orWhere('name', '');
            })
            ->update(['name' => $name]);
    }

    public function down(): void
    {
        $name = 'Documentos electrónicos - Paquete 15 documentos';

        MembershipHasModules::where('membership_modules_id', 3)
            ->where('name', $name)
            ->whereHas('subModules', function ($q) {
                $q->where('sub_module_id', 1);
            })
            ->update(['name' => null]);

        MembershipSubModule::where('sub_module_id', 1)
            ->where('name', $name)
            ->update(['name' => null]);
    }
};