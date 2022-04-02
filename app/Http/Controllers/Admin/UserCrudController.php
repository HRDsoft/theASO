<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\UserStoreCrudRequest as StoreRequest;
use App\Http\Requests\UserUpdateCrudRequest as UpdateRequest;

use Backpack\PermissionManager\app\Http\Controllers\UserCrudController as BackpackUserController;



class UserCrudController extends BackpackUserController
{
    //
    // public function index()
    // {
    //     dd('1234');
    //     // code...
    // }
    public function setup()
    {
        // dd('123');
        $this->crud->setModel(config('backpack.permissionmanager.models.user'));
        $this->crud->setEntityNameStrings(trans('backpack::permissionmanager.user'), trans('backpack::permissionmanager.users'));
        $this->crud->setRoute(backpack_url('user'));
    }

    public function setupCreateOperation()
    {
        $this->addUserFields();
        $this->crud->setValidation(StoreRequest::class);
    }

    public function setupUpdateOperation()
    {
        $this->addUserFields();
        $this->crud->setValidation(UpdateRequest::class);
    }


    protected function addUserFields()
    {
        $this->crud->addFields([
            [
                'name'  => 'last_name',
                'label' => "Last Name",
                'type'  => 'text',
            ],
            [
                'name'  => 'first_name',
                'label' => "First Name",
                'type'  => 'text',
            ],
            [
                'name'  => 'email',
                'label' => trans('backpack::permissionmanager.email'),
                'type'  => 'email',
            ],
            [
                'name'  => 'password',
                'label' => trans('backpack::permissionmanager.password'),
                'type'  => 'password',
            ],
            [
                'name'  => 'password_confirmation',
                'label' => trans('backpack::permissionmanager.password_confirmation'),
                'type'  => 'password',
            ],
            [
                // two interconnected entities
                'label'             => trans('backpack::permissionmanager.user_role_permission'),
                'field_unique_name' => 'user_role_permission',
                'type'              => 'checklist_dependency',
                'name'              => ['roles', 'permissions'],
                'subfields'         => [
                    'primary' => [
                        'label'            => trans('backpack::permissionmanager.roles'),
                        'name'             => 'roles', // the method that defines the relationship in your Model
                        'entity'           => 'roles', // the method that defines the relationship in your Model
                        'entity_secondary' => 'permissions', // the method that defines the relationship in your Model
                        'attribute'        => 'name', // foreign key attribute that is shown to user
                        'model'            => config('permission.models.role'), // foreign key model
                        'pivot'            => true, // on create&update, do you need to add/delete pivot table entries?]
                        'number_columns'   => 3, //can be 1,2,3,4,6
                    ],
                    'secondary' => [
                        'label'          => mb_ucfirst(trans('backpack::permissionmanager.permission_plural')),
                        'name'           => 'permissions', // the method that defines the relationship in your Model
                        'entity'         => 'permissions', // the method that defines the relationship in your Model
                        'entity_primary' => 'roles', // the method that defines the relationship in your Model
                        'attribute'      => 'name', // foreign key attribute that is shown to user
                        'model'          => config('permission.models.permission'), // foreign key model
                        'pivot'          => true, // on create&update, do you need to add/delete pivot table entries?]
                        'number_columns' => 3, //can be 1,2,3,4,6
                    ],
                ],
            ],
        ]);
    }

    public function setupListOperation()
    {
        $this->crud->addColumns([
            [
                'name'  => 'last_name',
                'label' => 'Last Name',
                'type'  => 'text',
            ],
            [
                'name'  => 'first_name',
                'label' => 'First Name',
                'type'  => 'text',
            ],
            [
                'name'  => 'email',
                'label' => trans('backpack::permissionmanager.email'),
                'type'  => 'email',
            ],
            // [
            //     'name'  => 'email_verified_at',
            //     'label' => "Email Verified at",
            //     'type'  => 'datetime',
            // ],
            [
                'name'  => 'email_verified_at', // The db column name
                'label' => 'Tag Name', // Table column heading
                'type'  => 'datetime',
                // 'format' => 'l j F Y H:i:s', // use something else than the base.default_datetime_format config value
            ],
            [ // n-n relationship (with pivot table)
                'label'     => trans('backpack::permissionmanager.roles'), // Table column heading
                'type'      => 'select_multiple',
                'name'      => 'roles', // the method that defines the relationship in your Model
                'entity'    => 'roles', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model'     => config('permission.models.role'), // foreign key model
            ],
            // [ // n-n relationship (with pivot table)
            //     'label'     => trans('backpack::permissionmanager.extra_permissions'), // Table column heading
            //     'type'      => 'select_multiple',
            //     'name'      => 'permissions', // the method that defines the relationship in your Model
            //     'entity'    => 'permissions', // the method that defines the relationship in your Model
            //     'attribute' => 'name', // foreign key attribute that is shown to user
            //     'model'     => config('permission.models.permission'), // foreign key model
            // ],
        ]);

        if (backpack_pro()) {
            // Role Filter
            $this->crud->addFilter(
                [
                    'name'  => 'role',
                    'type'  => 'dropdown',
                    'label' => trans('backpack::permissionmanager.role'),
                ],
                config('permission.models.role')::all()->pluck('name', 'id')->toArray(),
                function ($value) { // if the filter is active
                    $this->crud->addClause('whereHas', 'roles', function ($query) use ($value) {
                        $query->where('role_id', '=', $value);
                    });
                }
            );

            // Extra Permission Filter
            $this->crud->addFilter(
                [
                    'name'  => 'permissions',
                    'type'  => 'select2',
                    'label' => trans('backpack::permissionmanager.extra_permissions'),
                ],
                config('permission.models.permission')::all()->pluck('name', 'id')->toArray(),
                function ($value) { // if the filter is active
                    $this->crud->addClause('whereHas', 'permissions', function ($query) use ($value) {
                        $query->where('permission_id', '=', $value);
                    });
                }
            );
        }
    }
}
