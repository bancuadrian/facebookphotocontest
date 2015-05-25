<?php namespace App\Http\Controllers;

use App\Models\UserPhoto;
use App\Models\Vote;
use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

    public function checkFriendsPermissions()
    {
        $request = new \Facebook\FacebookRequest($this->session, 'GET', '/me/permissions');
        $response = $request->execute();
        $permissions = $response->getGraphObject()->asArray();

        $permissions = array_filter($permissions,function($v){
            return ($v->permission == 'user_friends' && $v->status == 'granted');
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
        $affected = UserPhoto::where('status', '=', 1)->where('user_id',\Auth::user()->id)->update(array('status' => 0));

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

    public function removeMyPhoto()
    {
        $input = \Request::all();
        if(!isset($input['photo']) || !isset($input['photo']['id'])) return;

        $userPhoto = UserPhoto::where('user_id',\Auth::user()->id)->where('id',$input['photo']['id'])->where('status',1)->first();

        if($userPhoto)
        {
            $userPhoto->status = 0;
            $userPhoto->save();
        }

        return ['removed'=>'ok'];
    }

    public function getFriendsPhotos()
    {
        $friends = [];

        if(!$this->checkFriendsPermissions())
        {
            return response(['scope_required'=>'user_friends'],412);
        }

        //get all friends from Facebook

        $getOut = false;
        $next = '/me/friends?fields=id,name';
        while(!$getOut)
        {
            $f = $this->getFriends($next);

            if(isset($f['data']) && count($f['data']))
            {
                foreach($f['data'] as $friend)
                {
                    $friends[] = $friend;
                }

                $next = null;

                if(isset($f['paging']->next))
                {
                    $url = $f['paging']->next;
                    $query_str = parse_url($url, PHP_URL_QUERY);
                    parse_str($query_str, $next);
                    unset($next['access_token']);
                    $params = [];

                    foreach($next as $key=>$value)
                    {
                        $params[] = $key.'='.$value;
                    }

                    $next = '/me/friends?'.implode("&",$params);
                }

            }else{
                $getOut = true;
            }
        }


        $friends = array_map(function($e){
            return $e->id;
        },$friends);

        $this->friends = $friends;
        $this->rankings = true;

        return $this->getAllPhotos();;
        // search DB for friend's id
        // return photos
    }

    public function getFriends($link)
    {
        if(!$link)
        {
            return [];
        }

        $request = new \Facebook\FacebookRequest($this->session, 'GET', $link);
        $response = $request->execute();
        $f = $response->getGraphObject()->asArray();

        return $f;
    }

    public function getAllPhotos()
    {
        $this->rankings = isset($this->rankings) ? $this->rankings : \Input::get('rankings');

        $photos = UserPhoto::
            with(['user'=>function($query){
                $query->select(['id','name','avatar']);
            }])
            ->with('votesCount')
            ->where('status',1);

        $photos = $photos->select('id','user_id','filename');
        $photos = $photos->orderByRaw('rand("'.date('Ymdh').'")');
        $photos = $photos->paginate(30);

        if($this->rankings)
        {
            $photos = UserPhoto::
                with(['user'=>function($query){
                    $query->select(['id','name','avatar']);
                }])
                ->join('votes','votes.photo_id','=','userphotos.id')
                ->select(DB::raw('userphotos.id,userphotos.user_id,userphotos.filename,count(*) as aggregate'))
                ->groupBy('votes.photo_id')
                ->orderBy('aggregate','desc')
                ->orderBy('votes.created_at','desc')
                ->where('status',1);

            if(isset($this->friends))
            {
                $friends = $this->friends;
                $photos = $photos->whereHas('user',function($q) use ($friends){
                    $q->whereIn('fb_id',$friends);
                });
            }

            $photos = $photos->paginate(10);

            $photos->each(function($photo){
                $photo->votes_count = new \stdClass();
                $photo->votes_count->photo_id = $photo->id;
                $photo->votes_count->aggregate = $photo->aggregate;
                unset($photo->aggregate);
            });

            return $this->formatPhotoCollection($photos);

        }

        return $this->formatPhotoCollection($photos);
    }

    public function formatPhotoCollection($photoCollection)
    {
        $photoCollection->each(function($photo){
            $photo->path = url("/".$this->save_photo_folder."/".$photo->filename);
        });

        return $photoCollection;
    }

    public function votePhoto()
    {
        $input = \Request::all();
        $vote_registered = 'false';

        $photo = $input['photo'];

        if(User::canVote($input['photo']['id']))
        {
            $vote = new Vote();
            $vote->photo_id = $input['photo']['id'];
            $vote->user_id = Auth::user()->id;
            $vote->ip = $_SERVER['REMOTE_ADDR'];
            $vote->save();
            $vote_registered = 'true';

            $photo = UserPhoto::
                with(['user'=>function($query){
                    $query->select(['id','name','avatar']);
                }])
                ->with('votesCount')
                ->where('status',1)
                ->select('id','user_id','filename')
                ->find($input['photo']['id']);
        }

        return [
            "photo"=>$photo,
            "vote_registered" => $vote_registered
        ];
    }
}
