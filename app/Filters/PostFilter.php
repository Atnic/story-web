<?php

namespace App\Filters;

use Atnic\LaravelGenerator\Filters\BaseFilter;

/**
 * PostFilter Filter
 */
class PostFilter extends BaseFilter
{
    /**
     * Searchable Field,
     * support relation also, ex: [ 'name', 'posts' => [ 'title' ] ]
     * @var array
     */
    protected $searchables = [
        'post',
        'user' => ['name']
    ];

    /**
     * Sortables Field
     * support relation but belongsTo morhpTo hasOne morphOne only, ex: [ 'id', 'name', 'role.name' ]
     * @var array
     */
    protected $sortables = [
        'id',
        'post',
        'created_at',
        'updated_at'
    ];

    /**
     * Default Sort, null if no default, ex: 'name,asc'
     * @var string|null
     */
    protected $default_sort = 'created_at,desc';

    /**
     * Default per page, null if use model per page default, ex: 20
     * @var int|null
     */
    protected $default_per_page = null;
}
