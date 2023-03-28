<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Answers extends Model
{
	public function getCategory() {
			return $this->belongsTo('App\Models\Categories', 'category_id', 'id');
	}

    public function getProject() {
        return $this->belongsTo('App\Models\Projects', 'project_id', 'id');
    }

    public function getQuestion() {
        return $this->belongsTo('App\Models\Questions', 'question_id', 'id');
    }
}
