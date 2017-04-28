<?php

namespace Ffte\Search\Models;


use October\Rain\Database\Model;

class SearchIndex extends Model
{
    public $table = "ffte_search_indices";
    public $guarded = [];
    public $timestamps = false;
}