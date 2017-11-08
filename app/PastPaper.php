<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PastPaper extends Model
{
	protected $table = 'past_papers';

	protected $fillable = [ 'name', 'resource_type', 'file', 'semester' ];
}
