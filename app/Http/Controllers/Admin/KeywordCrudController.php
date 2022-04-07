<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Database\QueryException;
use App\Http\Requests\KeywordRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use App\Models\Keyword;
use App\Models\RelatedKeyword;
use Illuminate\Http\Request;

/**
 * Class KeywordCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class KeywordCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation { store as traitStore; }
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation { update as traitUpdate; }
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
        CRUD::addColumn([
            'name'     => 'name',
            'label'    => 'Keyword',
            'type'     => 'text'
        ]);
        CRUD::addColumn([
            'name'     => 'category_id',
            'label'    => 'Category',
            'type'     => 'closure',
            'function' => function($entry) {
                return $entry->category->name;
            }
        ]);
        CRUD::addColumn([
            'name'     => 'sub_category_id',
            'label'    => 'Sub Category',
            'type'     => 'closure',
            'function' => function($entry) {
                return $entry->subCategory->name;
            }
        ]);
        CRUD::addColumn([
            'name'     => 'niche_category_id',
            'label'    => 'Niche Category',
            'type'     => 'closure',
            'function' => function($entry) {
                return ($entry->nicheCategory)?$entry->nicheCategory->name:'';
            }
        ]);
        CRUD::column('game');
        CRUD::addColumn([
            'name'     => 'number_of_words',
            'label'    => 'Number of Words',
            'type'     => 'closure',
            'function' => function($entry) {
                return str_word_count($entry->name);
            }
        ]);
        CRUD::addColumn([
            'name'     => 'characters',
            'label'    => 'Characters',
            'type'     => 'closure',
            'function' => function($entry) {
                return strlen($entry->name);
            }
        ]);
        CRUD::column('competition');
        CRUD::column('traffic');
        CRUD::column('branded');

        CRUD::addColumn([
            'name'     => 'related_keywords',
            'label'    => 'Related Keywords',
            'type'     => 'closure',
            'function' => function($entry) {
                $keywords = [];
                foreach ($entry->relatedKeywords as $index => $relatedKeyword) {
                    if ($relatedKeyword->related_keyword) {
                        $keywords[] = $relatedKeyword->related_keyword->name;
                    }
                    // code...
                }
                return implode(", ", $keywords);
            }
        ]);
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
            'default' => "no"
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
            'default' => "no"

        ]);
        
        CRUD::addField([   // SelectMultiple = n-n relationship (with pivot table)
            'label'     => "Related Keywords (Comma ', ' Separated)",
            'type'      => 'text',
            'name'      => 'related_keyword_id', // the method that defines the relationship in your Model
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
        $keyword = Keyword::find(explode('/', url()->current())[5]);
        $keywords = [];
        foreach ($keyword->relatedKeywords as $index => $relatedKeyword) {
            $keywords[] = $relatedKeyword->related_keyword->name;
        }

        CRUD::addField([   // SelectMultiple = n-n relationship (with pivot table)
            'label'     => "Related Keywords (Comma ', ' Separated)",
            'type'      => 'text',
            'name'      => 'related_keyword_id', // the method that defines the relationship in your Model
            'value'     => implode(", ", $keywords)
        ]);
    }

    public function store(Request $request)
    {   

        $response = $this->traitStore();
        $keyword_id = $this->data["entry"]->id;

        $related_keywords = explode(", ", $request->related_keyword_id);
        foreach ($related_keywords as $index => $related_keyword) {
            try {
                $Keyword = new Keyword();
                $Keyword->category_id = $request->category_id;
                $Keyword->sub_category_id = $request->sub_category_id;
                $Keyword->niche_category_id = $request->niche_category_id;
                $Keyword->name = $related_keyword;
                $Keyword->game = $request->game;
                $Keyword->competition = $request->competition;
                $Keyword->traffic = $request->traffic;
                $Keyword->branded = $request->branded;
                $Keyword->save();
                // try {
                //     $RelatedKeyword = new RelatedKeyword();
                //     $RelatedKeyword->keyword_id = $keyword_id;
                //     $RelatedKeyword->related_keyword_id = $Keyword->id;
                //     $RelatedKeyword->save();
                    
                // } catch (Exception $e) {
                    
                // }
                // try {
                //     $RelatedKeyword = new RelatedKeyword();
                //     $RelatedKeyword->keyword_id = $Keyword->id;
                //     $RelatedKeyword->related_keyword_id = $keyword_id;
                //     $RelatedKeyword->save();
                // } catch (Exception $e) {
                
                // }
               
            } catch (\Throwable   $e) {
                dd($e);
            }
           

            
        }
        // do something after save
        return $response;
    }

    public function update(Request $request, $id)
    {   

        $response = $this->traitUpdate();

        $related_keywords = explode(", ", $request->related_keyword_id);

        foreach ($related_keywords as $index => $related_keyword) {
            $Keyword = Keyword::find($id);
            $Keyword->category_id = $request->category_id;
            $Keyword->sub_category_id = $request->sub_category_id;
            $Keyword->niche_category_id = $request->niche_category_id;
            $Keyword->name = $related_keyword;
            $Keyword->game = $request->game;
            $Keyword->competition = $request->competition;
            $Keyword->traffic = $request->traffic;
            $Keyword->branded = $request->branded;
            $Keyword->save();

            
            $RelatedKeyword = new RelatedKeyword();
            $RelatedKeyword->keyword_id = $keyword_id;
            $RelatedKeyword->related_keyword_id = $Keyword->id;
            $RelatedKeyword->save();

            $RelatedKeyword = new RelatedKeyword();
            $RelatedKeyword->keyword_id = $Keyword->id;
            $RelatedKeyword->related_keyword_id = $keyword_id;
            $RelatedKeyword->save();
        }
        // do something after save
        return $response;
    }

}
