<?php //コンテンツ設定に必要な定数や関数

//外部リンクの開き方
define('OP_EXTERNAL_LINK_OPEN_TYPE', 'external_link_open_type');
if ( !function_exists( 'get_external_link_open_type' ) ):
function get_external_link_open_type(){
  return get_theme_option(OP_EXTERNAL_LINK_OPEN_TYPE, 'keep_as_is');
}
endif;

//外部リンクのフォロータイプ
define('OP_EXTERNAL_LINK_FOLLOW_TYPE', 'external_link_follow_type');
if ( !function_exists( 'get_external_link_follow_type' ) ):
function get_external_link_follow_type(){
  return get_theme_option(OP_EXTERNAL_LINK_FOLLOW_TYPE, 'keep_as_is');
}
endif;

//外部リンクアイコン
define('OP_EXTERNAL_LINK_ICON', 'external_link_icon');
if ( !function_exists( 'get_external_link_icon' ) ):
function get_external_link_icon(){
  return get_theme_option(OP_EXTERNAL_LINK_ICON, 'none');
}
endif;