<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\KeywordRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use App\Models\Keyword;

/**
 * Class KeywordCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class KeywordCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Keyword::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/keyword');
        CRUD::setEntityNameStrings('keyword', 'keywords');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('name');
        CRUD::column('category_id');
        CRUD::column('sub_category_id');
        CRUD::column('niche_category_id');
        CRUD::column('game');
        CRUD::addColumn([
            'name'     => 'name',
            'label'    => 'Number of Words',
            'type'     => 'number',
            'function' => function($entry) {
                return $entry->name;
            }
        ]);
        
        CRUD::column('competition');
        CRUD::column('traffic');
        CRUD::column('branded');

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']); 
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(KeywordRequest::class);

        CRUD::addField([
            'name'     => 'name',
            'label'    => 'Keyword',
            'type'     => 'text',
        ]);
        CRUD::addField([  // Select
           'label'     => "Category",
           'type'      => 'select',
           'name'      => 'category_id', // the db column for the foreign key

           // optional
           // 'entity' should point to the method that defines the relationship in your Model
           // defining entity will make Backpack guess 'model' and 'attribute'
           'entity'    => 'category',

           // optional - manually specify the related model and attribute
           'model'     => "App\Models\Category", // related model
           'attribute' => 'name', // foreign key attribute that is shown to user

           // optional - force the related options to be a custom query, instead of all();
           'options'   => (function ($query) {
                return $query->orderBy('name', 'ASC')->get();
            }), //  you can use this to filter the results show in the select
        ]);
        CRUD::addField([  // Select
           'label'     => "Sub Category",
           'type'      => 'select',
           'name'      => 'sub_category_id', // the db column for the foreign key

           // optional
           // 'entity' should point to the method that defines the relationship in your Model
           // defining entity will make Backpack guess 'model' and 'attribute'
           'entity'    => 'subCategory',

           // optional - manually specify the related model and attribute
           'model'     => "App\Models\SubCategory", // related model
           'attribute' => 'name', // foreign key attribute that is shown to user

           // optional - force the related options to be a custom query, instead of all();
           'options'   => (function ($query) {
                return $query->orderBy('name', 'ASC')->get();
            }), //  you can use this to filter the results show in the select
        ]);
        CRUD::addField([  // Select
           'label'     => "Niche Category",
           'type'      => 'select',
           'name'      => 'niche_category_id', // the db column for the foreign key

           // optional
           // 'entity' should point to the method that defines the relationship in your Model
           // defining entity will make Backpack guess 'model' and 'attribute'
           'entity'    => 'nicheCategory',

           // optional - manually specify the related model and attribute
           'model'     => "App\Models\NicheCategory", // related model
           'attribute' => 'name', // foreign key attribute that is shown to user

           // optional - force the related options to be a custom query, instead of all();
           'options'   => (function ($query) {
                return $query->orderBy('name', 'ASC')->get();
            }), //  you can use this to filter the results show in the select
        ]);
        CRUD::addField([   // radio
            'name'        => 'game', // the name of the db column
            'label'       => 'Game', // the input label
            'type'        => 'radio',
            'options'     => [
                // the key will be stored in the db, the value will be shown as label; 
                "yes" => "Yes",
                "no" => "No"
            ],
            // optional
            'inline'      => true, // show the radios all on the same line?
        ]);
        // CRUD::field('game');
        CRUD::field('competition');
        CRUD::field('traffic');
        CRUD::addField([   // radio
            'name'        => 'branded', // the name of the db column
            'label'       => 'Branded', // the input label
            'type'        => 'radio',
            'options'     => [
                // the key will be stored in the db, the value will be shown as label; 
                "yes" => "Yes",
                "no" => "No"
            ],
            // optional
            'inline'      => true, // show the radios all on the same line?
        ]);
        $Keywords = Keyword::select('id', 'name')->get();
        $array = [];
        foreach ($Keywords as $keyword) {
            $array[$keyword->id] = $keyword->name;
        }
        CRUD::addField([   // SelectMultiple = n-n relationship (with pivot table)
            'label'     => "Related Keywords",
            'type'      => 'select_from_array',
            'name'      => 'related_keyword_id', // the method that defines the relationship in your Model

            // optional
            // 'entity'    => 'keywords', // the method that defines the relationship in your Model
            // 'model'     => "App\Models\Keyword", // foreign key model
            // 'attribute' => 'name', // foreign key attribute that is shown to user
            // 'pivot'     => true, // on create&update, do you need to add/delete pivot table entries?

            // also optional
            'options'   => $array, 
            'allows_multiple' => true, // OPTIONAL; needs you to cast this to array in your model;
            // force the related options to be a custom query, instead of all(); you can use this to filter the results show in the select
        ]);
        // CRUD::field('branded');

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number'])); 
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
