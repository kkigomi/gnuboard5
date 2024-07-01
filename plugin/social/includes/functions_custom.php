<?php
if (!defined('_GNUBOARD_')) exit;

function social_member_leave_redirect(){
    global $is_member;

    if( !$is_member ){
        return;
    }

    $provider_name = get_session('ss_social_provider');

    if( social_get_provider_service_name($provider_name) ){

        try
        {
            $adapter = social_login_get_provider_adapter( $provider_name );
            
            // then grab the user profile 
            $user_profile = $adapter->getUserProfile();
        }

        catch( Exception $e )
        {
            $get_error = social_get_error_msg( $e->getCode() );

            if( is_object( $adapter ) ){
                social_logout_with_adapter($adapter);
            }

            alert('SNS 사용자 인증에 실패하였습니다.', G5_URL);
        }

        if( $user_provider = social_get_data('provider', $provider_name, $user_profile) ){
            
            social_login_session_clear(1);
            $token = get_session('ss_leave_token');
            $url = G5_BBS_URL.'/member_confirm.php?url=member_leave.php&leave_token='.$token;

            $social_token = social_nonce_create($provider_name);
            set_session('social_link_token', $social_token);
            
            $params = array('provider'=>$provider_name);

            $url = replaceQueryParams($url, $params);
            goto_url($url);
            
        }

        set_session('ss_social_provider', '');
        alert('잘못된 요청입니다.', G5_URL);
    }
}