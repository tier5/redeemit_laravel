<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;
//use App\Presenters\DatePresenter;

class Feedback extends Model  {

	//use DatePresenter;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */

	protected $guarded = array('id');
	protected $fillable = array('email', 'feedback', 'rating', 'source');

	protected $table = 'reedemer_feedbacks';

	
   
 	
}