<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Company;

class UpdateTableCompanies extends Migration
{
    protected $idCompanies = [
        '8091e8b7-7142-4929-93a3-84a5a33ac83a',
        'c7aee852-cc5d-4887-ba6d-52794f513cb4',
        '0b26dc3f-943a-42b8-9926-94f1b3f78a60',
        '3074a6c3-c550-43e4-8dc5-35000b18a54e',
        '227bcba4-e0f7-472f-ae21-4d20ae0a872a',
        '641af98d-1bb4-4576-93c3-819c604955c2',
        'db371d81-fd44-4a7b-96fc-3a6e4726ee4b',
        'd4017ca7-f16d-49a3-98a3-56c571d82287',
        '3de5b04a-6540-4d53-be8d-440f9f3bff1b',
        '59c6de76-88b0-4a68-8426-066d446ca82f',
        '4479cdf5-e606-4cf5-b33a-122e6f65e900',
        '5c4fdc2d-84c1-4c81-8626-752cb8937320',
        '80893042-225f-4b05-958f-cfb2517af5a5',
        'f3253f09-fc44-4c5f-8b7f-e3f170d860cc',
        '9a88eb19-f2af-4746-97ce-adec5c37c0c6',
        'b135ace2-5f90-4a7e-bd21-e340c4dd84cb',
        '9ccafedd-7ecb-47d8-9691-2ed1b769b4ed',
        '1ebbf54a-021a-4baa-b18c-c743ea4cf1ba',
        '78cdf726-0d0a-407b-8452-b39c3578ee0b',
        'a6e4c9a8-2d30-4120-a4ee-efa63c69bdab',
        '24088f40-3f85-44a8-9a06-7ebbf961ea83',
        '05771e40-94b9-447e-834f-3346e76b7219',
        'c20d010d-ae2d-49d4-8f9c-0b4226de15f1',
        '5f83c02e-0c47-4e89-9ec4-14d78bebc8f2',
        'a039f752-aa95-49d6-ae0e-7db1124ac8d4',
        '64061083-9e38-4e16-a7bb-02dc306c4749',
        '991bfee0-e01a-4385-bd9b-3d0ff86c2134',
        'db6df071-bfdb-4d28-9097-5716b5505c78',
        '95f0df50-707a-49b7-b56b-a55942684446',
        '4b096e51-88c6-4797-8ea4-7be170b4a3b1',
        'dc852b9c-e218-41f8-88fe-561fa3165041',
        '024c6d9f-9e5a-4f4d-a6ba-79d4bc74eeee',
        '6a52aee8-d374-4230-ad7a-84b9a83f630f',
        '608071c1-a0ad-4e90-abd7-41629a3dd373',
        '2a6d75d6-7d81-4e44-8de8-23c34ed986ac',
        '5be85dfd-b437-42d2-8fc4-3267252629a6',
        '3e6f509c-c461-454b-a3b4-632be231aa4f',
        '1a66dff6-9739-47f0-aac7-6b7395844544',
        'aa16c26e-4015-47a6-abb0-d60179d18143',
        '8cd0f810-9f00-4bf3-9bbf-ad053adac977',
        '58bd7313-ffe0-46c7-ab80-66516f8aea0f',
        '35140af8-a592-470f-8a8f-1475aa393b5e',
        '7d20b83c-590e-412b-9c44-cca2355cd11f',
        '09718702-7f49-4d04-bbe4-5b21de1c15fa',
        '296db508-bab5-4724-b69c-dfa2b883dacc',
        '99568b3e-b92c-4068-96a7-10208fb6710e',
        '2d5057e6-48f5-4cfe-be78-4c9bae0b4ddd',
        '70d346c5-09ec-4291-871a-836c607356d1',
        '3d0c9572-c42a-4004-8164-383a1b5fe8be',
        '044f9d48-6ad6-45c3-9730-f7eb3f68f87b',
        '76934c77-0b95-4712-af7a-7f3d590645f5',
        'aed7ddea-8570-4301-ab24-e692b7e9b167',
        'ddc93809-0c09-47b4-86a5-870c6bc7b88c',
        '8e1e6098-1f51-4e02-bda8-c8b143bbf52b',
        '9d5152c5-53a7-4b3a-a233-99f0cc0af52a',
        '9aa92841-0d6c-4352-b9c2-5ffc1bfcfb3e',
        '6828c5c3-3b23-43b5-9c8a-7f81920cdfc1',
        '29ef2c94-abf8-40a8-9418-ede65c7d40e1',
        'c72d28c3-0de8-49d5-b5e2-c42da4f63b51',
        '515a5edf-1bda-4ce3-964b-312c4ee3038a',
        '433a7d21-085e-48dc-8d6f-d47cf2e332f4',
        'be3b0974-f19d-4966-ae1f-984f4a2ebeae',
        '7ce1b99e-04e6-4bbf-9963-09fc7b165cb5',
        '374b46eb-d219-4e80-8d25-6dc3c783b45e',
        '6b59f24c-da26-4d7b-a28c-e01133594e24',
        'b9daf544-de48-4a1b-9210-5f6a9727f854',
        '7fe3a19c-b6ee-45d5-8870-4f7071fb508e',
        'cb49ca36-2f36-4801-877d-c05cad731d0c',
        '4dd74206-827b-4f55-80a4-0c12176fc526',
        '304cee8a-6f06-4571-a035-ee0dcecc36cf',
        '62a361a6-48ef-4fdf-8d46-9e67a433716e',
        '0a11843d-3a7f-40d3-acd8-ce1df00dc757',
        'c80fb717-8a2e-4953-8d4a-a815ff0eb011',
        '1ec9181a-3bd2-47e5-8769-0d5643e9a4cd',
        'a783ba15-299b-4bec-a319-0ebb739df059',
        'd284e895-2c6b-44e4-a120-ab4e481a38c2',
        'a59fd8c0-9250-48db-a5ae-e89009dd4a00',
        'd8b33440-be27-4641-85f6-02db5d95a257',
        'aa09ae27-f512-415f-9e54-bb6870c271e3',
        '36a06dee-5b76-432b-8478-cdae25483103',
        'c013fd5a-f1da-4bfa-bc72-0f8353102535',
        '31083f85-830e-4d22-9337-af22952cccb8',
        'a64c705c-f3d2-46cb-a41a-e0fd4213e335',
        '3de0bf7e-ad3b-4628-a415-4b45a431fb91',
        'fb01d8cb-310e-4455-9ae3-6d39404fcf3d',
        '499aa02d-bd3d-4774-8c83-f7e8347aa4f4',
        '19c8c62b-c8c1-40fe-9936-68f3273a6c8d',
        '8f3c052b-df48-49e4-a7fb-b26621c0e5a0',
        '57332ba2-e5a2-435d-af25-1c1e2e141be4',
        'c8c44eb9-df29-473a-9437-09d11a99801a',
        'be348481-b98e-4da0-a62f-a4b9dd23c707',
        '84c0527a-f38d-4ab5-b4f7-a59348220897',
        'b6727a67-5641-4c8b-baef-c1fb8255641d',
        '79c4503f-e1fa-44de-a423-8002aad5801e',
        'cb5c0cca-d74e-4840-8d15-c6f37922376f',
        '59fa898b-f09a-4ec0-a40c-da197e3e203e',
        '4352acbe-7760-40cf-b471-52ccdbe04bd1',
        'dc7fa5b4-b7d2-4b94-9189-a32a4a2c5a80',
        'f0171bf3-33f5-484e-9152-f5b095658b94',
        '663b779d-235d-4d2d-919a-e2639cb386db',
        '83bc4fae-ed33-4e94-9a8b-1a0f3df67e11',
        '1dfba5bc-de95-4114-8c01-9e1406fd3db9',
        'bde7566a-5ce7-49b8-94e2-fd4a69004314',
        '0adc86af-2b58-4294-b550-aa1116f66c9a',
        'ba858366-1fe8-4740-8c38-3e5d544fbd00',
        '9a965bcb-c46c-4e7a-b0a4-a9751d5ac7ef',
        '2005e1bb-8db8-4315-8d64-74e18dc49963',
        '73d27e05-8c11-49a5-8f01-e9235b04cc68',
        '983ccb47-3d1d-4d2f-86e0-d0d6734a8300',
        '9a2461d7-fbc8-4671-b4d7-c7ccbe6ec6b0',
        'b1d6d268-3fb7-484d-a451-f313f83e18ee',
        'd415aea0-8660-443d-83d0-cdae7011e436',
        '119e3d3b-3750-431f-980b-bb14ac302e0d',
        '232e8279-1730-40f8-854c-32dc97ded6a6',
        'edb38d1a-6251-4e51-bd7a-02c5c1ddd7f9',
        'e60d686f-24a9-498a-8247-5621e18efa00',
        '47fa122b-1e9f-4a9b-b1d5-d265a8176866',
        '3ed157c3-1b31-4e70-a250-89b0773720a5',
        'c5b965e4-6888-4370-b3d4-30337096fe94',
        'e6fac02e-6944-4aab-98f2-3b2687242de9',
        'bdddd48a-0f53-4ae3-af4d-4ef5251607bd',
        '408b5235-e8e2-4406-b42f-d7a13160654b',
        'ac2454ef-acb0-4c13-855d-31945aca5480',
        'de432a99-e002-48f3-abf2-c0a99f8ce97a',
        '52fbb862-c1e2-4771-b294-29413b6a3cd7',
        'ea2169fc-dd49-4d33-bfd5-7a7c4dff3f24',
        'fc57e3fc-76cc-4b01-948f-387245e30660',
        '71247023-1796-46de-abac-200f6163fe6c',
        '7846f26c-642d-45d6-90e5-b2de969c2162',
        'ab75dfff-f083-49ee-9fe4-e0292a6c2ffb',
        '83e80ae5-affc-32b4-b11d-b4cab371c48b',
        '3e46d4a7-e111-4706-a652-f5b6c0a2842b'
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->boolean('is_test_account')->default(false);
        });

        if(!config('app.debug')){
            Company::whereNotIn('id', $this->idCompanies)
            ->where('created_at', '<', '2025-03-10')
            ->update([
                'is_test_account' => true
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('is_test_account');
        });

        if(!config('app.debug')){
            Company::whereNotIn('id', $this->idCompanies)
            ->where('created_at', '<', '2025-03-10')
            ->update([
                'is_test_account' => false
            ]);
        }
    }
}
