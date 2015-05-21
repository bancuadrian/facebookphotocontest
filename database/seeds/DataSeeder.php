<?php

class DataSeeder extends \Illuminate\Database\Seeder {

    public function run()
    {
        $generator = Faker\Factory::create();

        for($i=0;$i<100;$i++)
        {
            $user = new \App\User();
            $user->fb_id = $generator->randomNumber(8);
            $user->name = $generator->name;
            $user->email = $generator->email;
            $user->verified = 1;
            $user->save();
            $this->addImageToUser($user);
            unset($user);
        }
    }

    public function addImageToUser($user)
    {
        $directory = base_path().'/sample_images';
        $scanned_directory = array_diff(scandir($directory), array('..', '.'));
        $scanned_directory = array_values($scanned_directory);

        $random_file = base_path().'/sample_images/'.$scanned_directory[rand(0,count($scanned_directory) - 1)];
        $ext = pathinfo($random_file, PATHINFO_EXTENSION);

        $filename = uniqid().".".$ext;
        $destination_file = public_path()."/uploads/".$filename;

        copy($random_file,$destination_file);

        // deactivate other photos
        $affected = \App\Models\UserPhoto::where('status', '=', 1)->where('user_id',$user->id)->update(array('status' => 0));

        $userPhoto = new \App\Models\UserPhoto();
        $userPhoto->user_id = $user->id;
        $userPhoto->filename = $filename;
        $userPhoto->save();
    }

}