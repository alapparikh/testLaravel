<?php

class UsersController extends \BaseController {

	protected $user;

	public function __construct(User $user){
		$this->user = $user;

	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
		//$users = $this->user->all();

		//return View::make('users.index')->withUsers($users);

		return Redirect::route('admin');
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('users.create');
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all();

		/*if (! $this->user->fill($input)->isValid()){
			return Redirect::back()->withInput()->withErrors($this->user->errors);
		}*/
		/*$validation = Validator::make($input, User::$rules);

		if ($validation->fails()) {
			return Redirect::back()->withInput()->withErrors($validation->messages());
			//return 'failed validation';
		}*/

		if (! User::isValid($input)){
			return Redirect::back()->withInput()->withErrors(User::$errors);
		}

		//$this->user->fill($input)->save();

		User::create([
			'username' => Input::get('username'),
			'email' => Input::get('email'),
			'password' => Hash::make(Input::get('password'))
			]);

		return Redirect::to('login')->with('message', 'Get ready to meet your stomach. Please login');
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($username)
	{
		//
		$user = $this->user->whereUsername($username)->first();

		return View::make('users.show', ['user' => $user]);

		//$id = Auth::id();
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$id = Auth::id();
		$user = $this->user->whereId($id)->first();

		return View::make('users.edit', ['user' => $user]);
		//return $user->id;
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$input = Input::all();
		$user = User::find($id);
		//$inputpassword = Hash::make(Input::get('password'));

		if (! User::isUpdateValid($input)){
			return Redirect::back()->withInput()->withErrors(User::$errors);
		}

		//return $user->password;
		if (Hash::check(Input::get('password'), $user->password)) {
			$user->username = Input::get('username');
			$user->email = Input::get('email');
			$user->password = Hash::make(Input::get('newPassword'));
			$user->save();

			return Redirect::to('admin')->with('message', 'Details saved');
		}
		return Redirect::back()->withInput();
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

	public function recommendations () {
		// Get corresponding User ID for token
		//$user_id = DB::table('mobiletokens')->where('token', Input::get('token'))->pluck('user_id');
		$user_id = 3;

		//$current_status = DB::table('meal_scores')->where('user_id', $user_id->pluck('current_status');
		$user_photo_info = DB::table('photos')->select('link','description','latitude','longitude')->where('user_id',$user_id)->orderBy('created_at','desc')->limit(1)->get();
		$user_photo_latitude = $user_photo_info[0]->latitude;
		$user_photo_longitude = $user_photo_info[0]->longitude;
		//return Response::json(['status' => 'success', 'recoinfo' => $user_photo_longitude]);

		// Get recommendations which are healthier and not eaten by the user before
		$reco_info = DB::table('photos')
		->select('link','description','latitude','longitude')
		->whereNotIn('user_id',array($user_id))
		->whereNotIn('description',$user_photo_info[0]->description)
		/*->whereBetween('meal_score',array(0.1,$current_status - 0.1))*/
		->get();
		
		$reco_info_arr = json_decode($reco_info);
		$reco_info = shuffle($reco_info);
		$recommendations = array();
		
		foreach ($reco_info as $reco){
			$reco_latitude = floatval($reco->latitude);
			$reco_longitude = floatval($reco->longitude);
			array_push($distances, $this->distance($user_photo_latitude,$user_photo_longitude,$reco_latitude,$reco_longitude));
			
			// Check if distance between user's photo and recommendation is less than 100 miles
			if ($this->distance($user_photo_latitude,$user_photo_longitude,$reco_latitude,$reco_longitude) < 100) {
				//$recommendations[] = $reco;
				array_push($recommendations,$reco);
				if (sizeof($recommendations) == 10) {
					break;
				}
			}
		}
		return Response::json(['status' => 'success', 'recoinfo' => $recommendations]);
	}

	/*
	Returns distance in miles between 2 points given their latitude and longitude
	*/
	function distance($lat1, $lon1, $lat2, $lon2) {

		$theta = $lon1 - $lon2;
		$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
		$dist = acos($dist);
		$dist = rad2deg($dist);
		$miles = $dist * 60 * 1.1515;
		
		return $miles;	  
	}

}
