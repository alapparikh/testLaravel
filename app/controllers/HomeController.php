<?php

class HomeController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/

	public function handleHome()
	{
		if (Auth::check()) {
			$id = Auth::id();

			$links = DB::table('photos')->select('link')->where('user_id','=',$id)->orderBy('created_at','desc')->get();
			$keys = array_keys($links);
			return View::make('admin.admin')->with('links',$links)->with('keys',$keys);
		}
		return View::make('admin.landing');
	}

	public function dataExport() {
		if (Auth::attempt(['email'=>Input::get('email'),'password'=>Input::get('password')] ))
		{
			$id = Auth::id();
			//$results = DB::select('select * from users where id = ?', array($id));
			$results = User::find($id);
			//$photos = DB::table('photos')->where('user_id', $id);
			Auth::logout();
			return Response::json($results);
		}
		return Response::json(['status'=>'failed']);
	}

	/*public function testtoken() {
		$token = Hash::make('email'.time());
		return $token;
	}*/

	public function dataDownload() {
		

		if (Auth::check())
		{
			$id = Auth::id();
			
			$result = User::find($id);
			//$photokeys = DB::table('photos')->where('user_id', $id)->pluck('key');
			$photokeys = DB::table('photos')->select('key')->where('user_id','=',$id)->get();

			//INCOMPLETE FUNCTION
			//Get all image files, save locally, download as zip, delete locally
			foreach ($photokeys as $photokey) {
				$s3 = AWS::get('s3');
				$s3->getObject(array(
	    		'Bucket'     => 'bssteam17foodjournalingdevelopment',
	    		'Key'        => $photokey->key,
	    		'SaveAs' => public_path() . '/files/'.$photokey->key, //'/the/path/to/the/file/you/are/uploading.ext',
			));
			}
			$directory = public_path() . '/files';
			
			$zip = new ZipArchive;
			$zipFileName = 'user_photos.zip';
			if ($zip->open(public_path() . '/' . $zipFileName, ZipArchive::CREATE) === TRUE) {

		    // Copy all the files from the folder and place them in the archive.
		    foreach (glob($directory . '/*') as $fileName) {
	                $file = basename($fileName);
	                $zip->addFile($fileName, $file);
	            }
            $zip->close();

            $headers = array(
                'Content-Type' => 'application/octet-stream',
            );

		    // Download .zip file.
            $success = File::deleteDirectory($directory, true);
		    return Response::download(public_path() . '/' . $zipFileName, $zipFileName, $headers);

	        } else {
	            return 'failed';
	        }		
		}
		else {
			return Redirect::to('login');
		}
	}

}
