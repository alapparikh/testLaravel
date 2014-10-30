<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Photo extends Eloquent  {

	protected $fillable = ['user_id', 'key','description','link','latitude','longitude'];

	/**
		 * The database table used by the model.
		 *
		 * @var string
		 */
		protected $table = 'photos';
}