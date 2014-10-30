<?php

class PhotosController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		if (Auth::check()) return View::make('photos.create');
		return Redirect::to('login');
	}

	public function webstore()
	{
		$file = Input::file('file');
		$key = $file->getFileName().'_'.Carbon::now();
		$s3 = AWS::get('s3');
		$bucket = 'bssteam17foodjournalingdevelopment';

		if (
			$s3->putObject(array(
		    'Bucket'     => $bucket,
		    'Key'        => $key,
		    'SourceFile' => $file->getRealPath(), //'/the/path/to/the/file/you/are/uploading.ext',
			))) {

			$plainUrl = $s3->getObjectUrl($bucket, $key);
			//return Response::json(['status' => 'Photo successfully uploaded']);
		} else {
			return Response::json(['status' => 'Failed to upload photo. Please try again.']);
		}

		$user_id = Auth::id();

		try{
			Photo::create([
			'key' => $key,
			'user_id' => $user_id, //Implement this after implementing tokens,
			'description' => 'testingagain',
			'link' => $plainUrl
			]);
		}
		catch (Exception $e){
			return Response::json(['status' => 'Failed to upload photo']);
		}
		
		
		return Response::json(['status' => 'Photo successfully uploaded']);
	}
	
	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		/*$file = Input::file('someFile');
		$path = storage_path(); //path('public').'uploads';
		$upload = Input::upload('someFile', $path, $file['name']);*/

		$file = Input::file('file');
		$key = $file->getFileName().'_'.Carbon::now();
		$bucket = 'bssteam17foodjournalingdevelopment';
		$s3 = AWS::get('s3');
		
		if (
			$s3->putObject(array(
		    'Bucket'     => $bucket,
		    'Key'        => $key,
		    'SourceFile' => $file->getRealPath(), //'/the/path/to/the/file/you/are/uploading.ext',
			))) {

			$plainUrl = $s3->getObjectUrl($bucket, $key);
			//return Response::json(['status' => 'Photo successfully uploaded']);
		} else {
			return Response::json(['status' => 'failed']);
		}

		$user_id = DB::table('mobiletokens')->where('token', Input::get('token'))->pluck('user_id');

		try{
			Photo::create([
			'key' => $key,
			'user_id' => $user_id, //Implement this after implementing tokens,
			'description' => Input::get('description'),
			'link' => $plainUrl,
			'latitude' => Input::get('latitude'),
			'longitude' => Input::get('longitude')
			]);
		}
		catch (Exception $e){
			return Response::json(['status' => 'failed']);
		}
		
		return Response::json(['status' => 'success','link' => $plainUrl]);
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$s3 = AWS::get('s3');
		$s3->getObject(array(
			'Bucket' => '',
			'Key' => '',
		));
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
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


}
