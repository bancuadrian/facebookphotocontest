<?php

require __DIR__.'/../bootstrap/autoload.php';



$session = new \Facebook\FacebookSession('CAAGQHHX5SFkBAK0NhBmvPd62HSZC5dYjiopuJ5ZAqbYCCbs7y1ZBNNTsnZCK94aPXHvEYPzEEVNjGYgkX4ZBdndJPJ5mZAPFBfq7jQPcDkKTXT4zkrxWqEDUkwhYhshynonJqOEkX94OVBitQqln1jX9uW9vgNTzKfhPHGeZBXImPLloJ1skjTg0poGffRvp69rDj2EJejEXAsswFH9muSlf6WhgpOSv60ZD');
$me = (new \Facebook\FacebookRequest(
    $session, 'GET', '/me'
))->execute()->getGraphObject(GraphUser::className());