<?php

namespace Quick;

class Quick
{
    protected $validator;
    protected $facebook;
    protected $comment_id;

    public function __construct()
    {
        $this->validator = new Validator;
        $this->facebook = new Facebook;
    }

    public function run($input)
    {
        if (!$this->validator->validate($input)) {
            return false;
        }

        $comment_id = $input['entry'][0]['changes'][0]['value']['comment_id'];
        $post_id = $input['entry'][0]['changes'][0]['value']['post_id'];

        $this->facebook->autoreply($comment_id, $post_id);
    }
}
