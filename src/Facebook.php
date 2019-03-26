<?php

namespace Quick;

use Intervention\Image\ImageManager;

class Facebook
{
	const API_URL = "https://graph.facebook.com/v2.11/";
	const WIDTH = 187;
	const HEIGHT = 187;
	const X_OFFSET = 33;
	const Y_OFFSET = 125;
	const OFFSET_POSITION = 'top-left';
	const OUTCOMES = ['sage', 'bandit'];
    const NL_POST_ID = "372710126516988_373834719737862";
    const FR_POST_ID = "372710126516988_372712486516752";

    protected $imageManager;

    public function __construct()
    {
        $this->imageManager = new ImageManager;
    }

    public function autoreply($comment_id, $post_id)
    {
        try {
            $user = $this->getTaggedUserId($comment_id);
            $user_id = $user['id'];
            $user_name = $user['name'];
            if (!$user_id) {
                return false;
            }

            $file = File::read();
            if (in_array($comment_id, $file)) {
                return false;
            }

            $this->reply($user_id, $user_name, $comment_id, $post_id);

            File::write($comment_id);
        } catch (Exception $e) {
            Logger::log($e->getMessage());
        }
    }

    private function getTaggedUserId($comment_id)
    {
        $contents = file_get_contents(self::API_URL . $comment_id . "?access_token=" . getenv('FACEBOOK_TOKEN') . "&fields=message_tags");
        if ($contents === false) {
            return null;
        }

        $tags = json_decode($contents, true);
        if (!isset($tags['message_tags']) || !count($tags['message_tags']) || empty($tags['message_tags'][0]['id'])) {
            return null;
        }

        return $tags['message_tags'][0];
    }

    private function prepareUserAvatar($outcome, $user_id, $user_name, $post_id)
    {
        $url = self::API_URL . $user_id . '/picture?width=200&height=200';
        $result = 'images/avatars/' . $user_id  . '_' .  $post_id . '.jpg';
        $avatar = $this->imageManager->make($url)->fit(self::WIDTH, self::HEIGHT);
        $template = $this->imageManager
            ->make('images/templates/' . $outcome . '_' . $post_id . '.jpeg')
            ->insert($avatar, self::OFFSET_POSITION, self::X_OFFSET, self::Y_OFFSET)
            ->text($user_name, 120, 100, function($font) {
                $font->file($_SERVER['DOCUMENT_ROOT'].'/Roboto-Light.ttf');
                $font->size(24);
                $font->color('#000000');
            })
            ->save($result);
        return $result;
    }

    private function reply($user_id, $user_name, $comment_id, $post_id)
    {
        $file_path = 'images/avatars/' . $user_id . '_' .  $post_id . '.jpg';
        if (!file_exists($file_path)) {
            $outcome = self::OUTCOMES[mt_rand(0, count(self::OUTCOMES) - 1)];
            $this->prepareUserAvatar($outcome, $user_id, $user_name, self::NL_POST_ID);
            $this->prepareUserAvatar($outcome, $user_id, $user_name, self::FR_POST_ID);
        }

        $outcome_url = "https://proximussint.be/" . $file_path . '?t=1';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::API_URL . $comment_id . "/comments?access_token=" . getenv('FACEBOOK_TOKEN'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "attachment_url=$outcome_url");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        // Logger::log(['quick', $server_output]);
        curl_close($ch);
    }
}
