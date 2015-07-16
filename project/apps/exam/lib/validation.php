<?php

/**
 * 解开auth
 *
 * @param $user_id
 * @param $uk
 * @return array|bool
 */
function ukdecode($user_id, $uk)
{
    //解开uk
    $decode = explode("\t", uc_authcode($uk, 'DECODE', LOGIN_KEY));
    if (empty($decode) || count($decode) != 2)
        return array();
    list(, $member_id) = $decode;
    if ($member_id != $user_id)
        return array();
    return true;
}

