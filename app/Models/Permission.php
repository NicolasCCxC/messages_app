<?php

namespace App\Models;

use App\Traits\UuidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory, UuidsTrait;

    const MODULE_COMPANY_PROFILE = 'Perfil de la empresa';
    CONST SUBMODULE_SERVICES_INFORMATION = 'Información de la empresa';
    CONST SUBMODULE_PRODUCT_SERVICES= 'Información de productos y/o servicios';

    const MODULE_WEB_PAGE = 'Sitio web y tienda virtual';
    const SUBMODULE_WEB_DESING = 'Diseño página web';
    const SUBMODULE_MANAGE_NOTIFICATION_DESING = 'Administración: notificaciones Diseño';

    const MODULE_ELECTRONIC_BILLING = 'Facturación electrónica';
    const SUBMODULE_MANAGE_NOTIFICATION_BILLING = 'Administración: notificaciones Facturación';
    const SUBMODULE_DOCUMENT_SUPPORT = 'Documentos soporte';

    const MODULE_INVENTORY_CONTROL = 'Administración de bodegas';
    const SUBMODULE_INVENTORY_CONTROL = 'Bodegas';
    const SUBMODULE_INVENTORY_MANAGE = 'Manejo de inventario';
    const SUBMODULE_MANAGE_NOTIFICATION_INVENTORY = 'Administración: notificaciones Bodegas';

    const MODULE_PURCHASING_PROCESS = 'Reportes analíticos';
    const SUBMODULE_PRODUCT_MANAGEMENT = 'Gestión de productos/servicios';
    const SUBMODULE_CUSTOMER_MANAGMENT = 'Gestión de clientes';
    const SUBMODULE_SUPPLIER_MANAGEMENT = 'Gestión de proveedores';

    const MODULE_ACCOUNTING = 'Contabilidad (Próximamente)';
    const MODULE_ELECTRONIC_PAYROLL = 'Nómina electrónica (Próximamente)';
    const MODULE_CRM = 'CRM (Próximamente)';

    const MODULE_DIGITIZATION_PHYSICAL_STORE = 'Digitalización tienda física';
    const SUBMODULE_SYSTEM_MANAGEMENT = 'Sistema de administración';

    const MODULE_PLANNING_ORGANIZATION = 'Planeación y organización';

    const MODULE_DOCUMENT_SUPPORT = 'Documento soporte';
    const SUBMODULE_MANAGE_NOTIFICATION_DOCUMENT_SUPPORT = 'Administración: notificaciones Documento Soporte';
    
    const ALL_MODULES = [
        self::MODULE_COMPANY_PROFILE,
        self::MODULE_WEB_PAGE,
        self::MODULE_ELECTRONIC_BILLING,
        self::MODULE_INVENTORY_CONTROL,
        self::MODULE_PURCHASING_PROCESS,
        self::MODULE_ACCOUNTING,
        self::MODULE_ELECTRONIC_PAYROLL,
        self::MODULE_CRM,
        self::MODULE_DOCUMENT_SUPPORT
    ];

    const subModulesToModule = [
        self::SUBMODULE_SERVICES_INFORMATION => self::MODULE_COMPANY_PROFILE,
        self::SUBMODULE_PRODUCT_SERVICES => self::MODULE_COMPANY_PROFILE,
        self::SUBMODULE_WEB_DESING => self::MODULE_WEB_PAGE,
        self::SUBMODULE_MANAGE_NOTIFICATION_DESING => self::MODULE_WEB_PAGE,
        self::MODULE_ELECTRONIC_BILLING => self::MODULE_ELECTRONIC_BILLING,
        self::SUBMODULE_MANAGE_NOTIFICATION_BILLING => self::MODULE_ELECTRONIC_BILLING,
        self::MODULE_DOCUMENT_SUPPORT => self::MODULE_DOCUMENT_SUPPORT,
        self::SUBMODULE_MANAGE_NOTIFICATION_DOCUMENT_SUPPORT => self::MODULE_DOCUMENT_SUPPORT,
        self::SUBMODULE_INVENTORY_CONTROL => self::MODULE_INVENTORY_CONTROL,
        self::SUBMODULE_MANAGE_NOTIFICATION_INVENTORY => self::MODULE_INVENTORY_CONTROL,
        self::SUBMODULE_PRODUCT_MANAGEMENT => self::MODULE_PURCHASING_PROCESS,
        self::SUBMODULE_CUSTOMER_MANAGMENT => self::MODULE_PURCHASING_PROCESS,
        self::SUBMODULE_SUPPLIER_MANAGEMENT => self::MODULE_PURCHASING_PROCESS,
        self::SUBMODULE_INVENTORY_MANAGE => self::MODULE_INVENTORY_CONTROL,
        self::SUBMODULE_SYSTEM_MANAGEMENT => self::MODULE_DIGITIZATION_PHYSICAL_STORE,
        self::MODULE_PLANNING_ORGANIZATION => self::MODULE_PLANNING_ORGANIZATION,

    ];

    const SKELETON_PERMISSIONS = [
        [
            'name' => self::MODULE_COMPANY_PROFILE,
            'front_name' => self::MODULE_COMPANY_PROFILE,
            'id' => 1,
            'merge' => false,
            'children' => [
                [
                    'front_name' => self::SUBMODULE_SERVICES_INFORMATION,
                    'father' => self::MODULE_COMPANY_PROFILE,
                    'name' => self::SUBMODULE_SERVICES_INFORMATION,
                    'children' => [],
                ],
                [
                    'front_name' => self::SUBMODULE_PRODUCT_SERVICES,
                    'father' => self::MODULE_COMPANY_PROFILE,
                    'name' => self::SUBMODULE_PRODUCT_SERVICES,
                    'children' => [],
                ]
            ]
        ],
        [
            'name' => self::MODULE_WEB_PAGE,
            'front_name' => self::MODULE_WEB_PAGE,
            'id' => 4,
            'merge' => false,
            'children' => [
                [
                    'front_name' => self::SUBMODULE_WEB_DESING,
                    'father' => self::MODULE_WEB_PAGE,
                    'name' => self::SUBMODULE_WEB_DESING,
                    'children' => [],
                ],
                [
                    'front_name' => 'Notificaciones',
                    'father' => self::MODULE_WEB_PAGE,
                    'name' => self::SUBMODULE_MANAGE_NOTIFICATION_DESING,
                    'children' => [],
                ]
            ]
        ],
        [
            'name' => self::MODULE_ELECTRONIC_BILLING,
            'id' => 5,
            'merge' => true,
            'children' => [
                [
                    'front_name' => 'Notificaciones',
                    'father' => self::MODULE_ELECTRONIC_BILLING,
                    'name' => self::SUBMODULE_MANAGE_NOTIFICATION_BILLING,
                    'children' => [],
                ]
            ]
        ],
        [
            'name' => self::MODULE_DOCUMENT_SUPPORT,
            'id' => 11,
            'merge' => true,
            'children' => [
                [
                    'front_name' => 'Notificaciones',
                    'father' => self::MODULE_DOCUMENT_SUPPORT,
                    'name' => self::SUBMODULE_MANAGE_NOTIFICATION_DOCUMENT_SUPPORT,
                    'children' => [],
                ]
            ] 
        ],
        [
            'name' => self::MODULE_INVENTORY_CONTROL,
            'id' => 6,
            'merge' => false,
            'children' => [
                [
                    'front_name' => self::SUBMODULE_INVENTORY_CONTROL,
                    'father' => self::MODULE_INVENTORY_CONTROL,
                    'name' => self::SUBMODULE_INVENTORY_CONTROL,
                    'children' => [],
                ],
                [
                    'front_name' => self::SUBMODULE_INVENTORY_MANAGE,
                    'father' => self::MODULE_INVENTORY_CONTROL,
                    'name' => self::SUBMODULE_INVENTORY_MANAGE,
                    'children' => [],
                ],
                [
                    'front_name' => 'Notificaciones',
                    'father' => self::MODULE_INVENTORY_CONTROL,
                    'name' => self::SUBMODULE_MANAGE_NOTIFICATION_INVENTORY,
                    'children' => [],
                ]
            ]
        ],
        [
            'front_name' => self::MODULE_PURCHASING_PROCESS,
            'name' => self::MODULE_PURCHASING_PROCESS,
            'id' => 2,
            'merge' => false,
            'children' => [
                [
                    'father' => self::MODULE_PURCHASING_PROCESS,
                    'front_name' => self::SUBMODULE_PRODUCT_MANAGEMENT,
                    'name' => self::SUBMODULE_PRODUCT_MANAGEMENT,
                    'children' => [],
                ],
                [
                    'father' => self::MODULE_PURCHASING_PROCESS,
                    'front_name' => self::SUBMODULE_CUSTOMER_MANAGMENT,
                    'name' => self::SUBMODULE_CUSTOMER_MANAGMENT,
                    'children' => [],
                ],
                [
                    'father' => self::MODULE_PURCHASING_PROCESS,
                    'front_name' => self::SUBMODULE_SUPPLIER_MANAGEMENT,
                    'name' => self::SUBMODULE_SUPPLIER_MANAGEMENT,
                    'children' => [],
                ]
            ]
        ],
        [
            'front_name' => self::MODULE_ACCOUNTING,
            'name' => self::MODULE_ACCOUNTING,
            'id' => 7,
            'merge' => false,
            'children' => []
        ],
        [
            'front_name' => self::MODULE_ELECTRONIC_PAYROLL,
            'name' => self::MODULE_ELECTRONIC_PAYROLL,
            'id' => 8,
            'merge' => false,
            'children' => []
        ],
        [
            'front_name' => self::MODULE_CRM,
            'name' => self::MODULE_CRM,
            'id' => 9,
            'merge' => false,
            'children' => []
        ],
        [
            'front_name' => self::MODULE_DIGITIZATION_PHYSICAL_STORE,
            'name' => self::MODULE_DIGITIZATION_PHYSICAL_STORE,
            'id' => 3,
            'merge' => false,
            'children' => [
                [
                    'father' => self::MODULE_DIGITIZATION_PHYSICAL_STORE,
                    'front_name' => self::SUBMODULE_SYSTEM_MANAGEMENT,
                    'name' => self::SUBMODULE_SYSTEM_MANAGEMENT,
                    'children' => [],
                ],
            ]
        ],
        [
            'front_name' => self::MODULE_PLANNING_ORGANIZATION,
            'name' => self::MODULE_PLANNING_ORGANIZATION,
            'id' => 10,
            'merge' => false,
            'children' => []
        ],
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'index',
    ];


    public function roles ()
    {
        return $this->belongsToMany(Role::class,'roles_permissions','permissions_id','roles_id');
    }
}
