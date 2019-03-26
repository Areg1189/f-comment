<?php

namespace Quick;

class Validator
{
    // const PAGE_ID = "396653700450194";
    const PAGE_ID = "372710126516988";
    // const NL_POST_ID = "396653700450194_1508459315936288";
    const NL_POST_ID = "372710126516988_373834719737862";
    // const FR_POST_ID = "396653700450194_1508460239269529";
    const FR_POST_ID = "372710126516988_372712486516752";

	public function validate($input)
	{
		if (!isset($input['entry']) || !count($input['entry'])) {
            return false;
        }

        $entry = $input['entry'][0];
        if (!isset($entry['changes']) || !count($entry['changes']) || empty($entry['changes'][0]['value'])) {
            return false;
        }

        $value = $entry['changes'][0]['value'];
        if (!isset($value['verb']) || $value['verb'] != 'add' || !isset($value['item']) || $value['item'] != 'comment') {
            return false;
        }

        if (!isset($value['post_id']) || !isset($value['from']) || !isset($value['from']['id'])) {
            return false;
        }

        if ($value['post_id'] != self::NL_POST_ID && $value['post_id'] != self::FR_POST_ID) {
            return false;
        }

        if ($value['from']['id'] == self::PAGE_ID) {
            return false;
        }

        if (empty($value['comment_id'])) {
            return false;
        }

        return true;
	}
}