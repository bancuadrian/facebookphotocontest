<?php namespace App\Http\Controllers;

use App\Models\UserPhoto;

class PhotoController extends Controller {

    private $session;

    public $allow_image_types = [
        IMAGETYPE_JPEG,
        IMAGETYPE_PNG
    ];

    public $save_photo_path = '';
    public $save_photo_folder = 'uploads';

    function __construct()
    {
        $this->session = new \Facebook\FacebookSession(\Auth::user()->token);
        $this->save_photo_path = public_path()."/".$this->save_photo_folder;
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
        $ext_extra_data = parse_url($ext,PHP_URL_QUERY);
        $ext = str_replace("?".$ext_extra_data,"",$ext);

        $filename = uniqid().".".$ext;

        $ifp = fopen($this->save_photo_path."/".$filename, "wb");
        fwrite($ifp, base64_decode($data[1]));
        fclose($ifp);

        $type = exif_imagetype($this->save_photo_path."/".$filename);

        if(!in_array($type,$this->allow_image_types))
        {
            return response(['error'=>'not_allowed'],412);
        }

        // deactivate other photos
        $affected = UserPhoto::where('status', '=', 1)->update(array('status' => 0));

        $userPhoto = new UserPhoto();
        $userPhoto->user_id = \Auth::user()->id;
        $userPhoto->filename = $filename;
        $userPhoto->save();

        $userPhoto->path = url("/".$this->save_photo_folder."/".$userPhoto->filename);

        return $userPhoto;
	}

    public function getAlbumsFromFacebook()
    {
        if(!$this->checkPhotosPermissions())
        {
            return response(['scope_required'=>'user_photos'],412);
        }

        $input = \Request::all();

        $page = null;
        if(isset($input['direction']))
        {
            if($input['direction'] == 'next' && isset($input['albums']['paging']) && isset($input['albums']['paging']['next']))
            {
                $page = 'after='.$input['albums']['paging']['cursors']['after'];
            }

            if($input['direction'] == 'previous' && isset($input['albums']['paging']) && isset($input['albums']['paging']['previous']))
            {
                $page = 'before='.$input['albums']['paging']['cursors']['before'];
            }
        }

        $facebook_query_string = '/me/albums?fields=id,name,cover_photo,picture,count&limit=10';
        if($page)
        {
            $facebook_query_string .= '&'.$page;
        }

        $request = new \Facebook\FacebookRequest($this->session, 'GET', $facebook_query_string);
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
        $params[] = 'limit=30';

        $page = null;
        if(isset($input['direction']))
        {
            if($input['direction'] == 'next' && isset($input['paging']) && isset($input['paging']['next']))
            {
                $page = 'after='.$input['paging']['cursors']['after'];
            }

            if($input['direction'] == 'previous' && isset($input['paging']) && isset($input['paging']['previous']))
            {
                $page = 'before='.$input['paging']['cursors']['before'];
            }
        }

        if($page)
        {
            $params[] = $page;
        }

        $request = new \Facebook\FacebookRequest($this->session, 'GET', '/'.$input['album_id'].'/photos?'.implode("&",$params));
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

    public function getMyPhoto()
    {
        $userPhoto = UserPhoto::where('user_id',\Auth::user()->id)->where('status',1)->first();

        if($userPhoto)
        {
            $userPhoto->path = url("/".$this->save_photo_folder."/".$userPhoto->filename);
        }

        return $userPhoto;
    }
}
