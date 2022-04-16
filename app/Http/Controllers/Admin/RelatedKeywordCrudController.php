<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\RelatedKeywordRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use DB;

/**
 * Class RelatedKeywordCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class RelatedKeywordCrudController extends CrudController
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
        CRUD::setModel(\App\Models\RelatedKeyword::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/related-keyword');
        CRUD::setEntityNameStrings('related keyword', 'related keywords');
        $this->crud->setCreateView('related_keywords.create');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::addColumn([
            'name'     => 'keyword_id',
            'label'    => 'Keyword',
            'type'     => 'closure',
            'function' => function($entry) {
                return $entry->keyword->name;
            }
        ]);
        CRUD::addColumn([
            'name'     => 'related_keyword_id',
            'label'    => 'Related Keywords',
            'type'     => 'custom_html',
            'value' => function($entry) {
                $keywords = [];
                foreach ($entry->keyword->relatedKeywords as $key => $related_keyword) {
                    $keywords[] = '<a href="'.backpack_url('related-keyword/'.$related_keyword->id.'/show').'">'.$related_keyword->related_keyword->name.'</a>';
                }
                return  implode(", ", $keywords);
            }
        ]);


         // ->select(DB::raw('group_concat(name) as names'))
        // CRUD::column('related_keyword_id');
        CRUD::groupBy('keyword_id');

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
        CRUD::setValidation(RelatedKeywordRequest::class);
        CRUD::field('keyword_id');
        CRUD::field('related_keyword_id');

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
