<?php namespace App\Http\Controllers;

use App\Models\UserPhoto;

class PhotoController extends Controller {

    private $session;

    function __construct()
    {
        $this->session = new \Facebook\FacebookSession(\Auth::user()->token);
    }

    public function checkPhotosPermissions()
    {
        $request = new \Facebook\FacebookRequest($this->session, 'GET', '/me/permissions');
        $response = $request->execute();
        $permissions = $response->getGraphObject()->asArray();

        $permissions = array_filter($permissions,function($v){
            return ($v->permission == 'user_photos' && $v->status == 'granted');
        });

        return count($permissions);
    }

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
        if(!$this->checkPhotosPermissions())
        {
            return response(['scope_required'=>'user_photos'],412);
        }

        $request = new \Facebook\FacebookRequest($this->session, 'GET', '/me/albums?fields=id,name,cover_photo,picture,count&limit=5000');
        $response = $request->execute();
        $albums = $response->getGraphObject()->asArray();

        return $albums;
    }

    public function getPhotosForAlbum()
    {

        if(!$this->checkPhotosPermissions())
        {
            return response(['scope_required'=>'user_photos'],412);
        }

        $input = \Request::all();
        $params = [];
        $params[] = 'fields=images';
        $params[] = 'limit=100';

        $request = new \Facebook\FacebookRequest($this->session, 'GET', '/'.$input['album_id'].'/photos?fields=images&limit=100');
        $response = $request->execute();
        $photos = $response->getGraphObject()->asArray();

        return $photos;
    }

    public function getImageBase64()
    {
        $input = \Request::all();

        $image = $input['image']['source'];

        $data = file_get_contents($image);

        $type = pathinfo($image, PATHINFO_EXTENSION);
        $type_extra_data = parse_url($type,PHP_URL_QUERY);
        $type = str_replace("?".$type_extra_data,"",$type);

        $dataUri = 'data:image/' . $type . ';base64,' . base64_encode($data);

        $response = [
            "base64" => $dataUri,
        ];

        return $response;
    }
}
