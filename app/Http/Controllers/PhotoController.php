<?php namespace App\Http\Controllers;

use App\Models\UserPhoto;

class PhotoController extends Controller {

	public function savePhoto()
	{
        $input = \Request::all();

        $data = explode(',', $input['image_base64']);

        $filename = $input['filename'];
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $filename = uniqid().".".$ext;

        $ifp = fopen(public_path()."/uploads/".$filename, "wb");
        fwrite($ifp, base64_decode($data[1]));
        fclose($ifp);

        // deactivate other photos
        $affected = UserPhoto::where('status', '=', 1)->update(array('status' => 0));

        $userPhoto = new UserPhoto();
        $userPhoto->user_id = \Auth::user()->id;
        $userPhoto->filename = $filename;
        $userPhoto->save();

        return $filename;
	}
}
