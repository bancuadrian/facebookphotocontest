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

    public function getAlbumsFromFacebook()
    {
        \Facebook\FacebookSession::setDefaultApplication('440229369435697','6cd4598aace14b4b1aa74d68ddbe6aa6');
        $session = new \Facebook\FacebookSession(\Auth::user()->token);
        //$session = new \Facebook\FacebookSession(\Auth::user()->token);

        $request = new \Facebook\FacebookRequest($session, 'GET', '/me/permissions');
        $response = $request->execute();
        $permissions = $response->getGraphObject()->asArray();

        $permissions = array_filter($permissions,function($v){
            return ($v->permission == 'user_photos' && $v->status == 'granted');
        });

        if(!count($permissions))
        {
            return response(['scope_required'=>'user_photos'],412);
        }

        $request = new \Facebook\FacebookRequest($session, 'GET', '/me/albums?fields=id,name,cover_photo,picture,count&limit=5000');
        $response = $request->execute();
        $albums = $response->getGraphObject()->asArray();

        return $albums;
    }
}
