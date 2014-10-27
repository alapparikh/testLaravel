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
		return View::make('photos.create');
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

		$s3 = AWS::get('s3');
		$s3->putObject(array(
		    'Bucket'     => 'bssteam17foodjournalingdevelopment',
		    'Key'        => $file->getFileName(),
		    'SourceFile' => $file->getRealPath(), //'/the/path/to/the/file/you/are/uploading.ext',
		));
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
