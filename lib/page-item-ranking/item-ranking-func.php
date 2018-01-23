<?php //ランキング関係の関数

//関数テキストテーブルのバージョン
define('ITEM_RANKINGS_TABLE_VERSION', DEBUG_MODE ? rand(0, 99) : '0.0');
define('ITEM_RANKINGS_TABLE_NAME',  $wpdb->prefix . THEME_NAME . '_item_rankings');

//関数テキスト移動用URL
define('IR_LIST_URL',   add_query_arg(array('action' => false,   'id' => false)));
define('IR_NEW_URL',    add_query_arg(array('action' => 'new',   'id' => false)));

//テーブルのバージョン取得
define('OP_ITEM_RANKINGS_TABLE_VERSION', 'item_rankings_table_version');
if ( !function_exists( 'get_item_rankings_table_version' ) ):
function get_item_rankings_table_version(){
  return get_theme_option(OP_ITEM_RANKINGS_TABLE_VERSION);
}
endif;

//テーブルが存在するか
if ( !function_exists( 'is_item_rankings_table_exist' ) ):
function is_item_rankings_table_exist(){
  return is_db_table_exist(ITEM_RANKINGS_TABLE_NAME);
}
endif;

//レコードを追加
if ( !function_exists( 'insert_item_ranking_record' ) ):
function insert_item_ranking_record($posts){
  $table = ITEM_RANKINGS_TABLE_NAME;
  $now = current_time('mysql');
  //_v($posts);
  $data = array(
    'date' => $now,
    'modified' => $now,
    'title' => $posts['title'],
    'item_ranking' => serialize($posts['item_ranking']),
    'count' => $posts['count'],
  );
  //_v($data);
  $format = array(
    '%s',
    '%s',
    '%s',
    '%s',
    '%d',
  );
  return insert_db_table_record($table, $data, $format);
}
endif;

//レコードの編集
if ( !function_exists( 'update_item_ranking_record' ) ):
function update_item_ranking_record($id, $posts){
  $table = ITEM_RANKINGS_TABLE_NAME;
  $now = current_time('mysql');

  $data = array(
    'modified' => $now,
    'title' => $posts['title'],
    'item_ranking' => serialize($posts['item_ranking']),
    'count' => $posts['count'],
  );
  $where = array('id' => $id);
  $format = array(
    '%s',
    '%s',
    '%s',
    '%d',
  );
  $where_format = array('%d');
  return update_db_table_record($table, $data, $where, $format, $where_format);
}
endif;

//初期データの入力
if ( !function_exists( 'add_default_item_ranking_records' ) ):
function add_default_item_ranking_records(){
  // $posts = array();
  // $posts['title'] = __( '[SAMPLE 01] 男性（左）', THEME_NAME );
  // $posts['name']  = __( '太郎', THEME_NAME );
  // $posts['icon']  = SB_DEFAULT_MAN_ICON;
  // $posts['style'] = SBS_STANDARD;
  // $posts['position'] = SBP_LEFT;
  // $posts['iconstyle'] = SBIS_CIRCLE_BORDER;
  // $posts['visible'] = 1;
  // insert_item_ranking_record($posts);
}
endif;

//テーブルの作成
if ( !function_exists( 'create_item_rankings_table' ) ):
function create_item_rankings_table() {
  $add_default_records = false;
  //テーブルが存在しない場合初期データを挿入（テーブル作成時のみ挿入）
  if (!is_item_rankings_table_exist()) {
    $add_default_records = true;
  }
  // SQL文でテーブルを作る
  $sql = "CREATE TABLE ".ITEM_RANKINGS_TABLE_NAME." (
    id bigint(20) NOT NULL AUTO_INCREMENT,
    date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
    modified datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
    title varchar(126),
    item_ranking text NOT NULL,
    count bigint(20) DEFAULT 1,
    PRIMARY KEY (id)
  )";
  $res = create_db_table($sql);

  //初期データの挿入
  if ($res && $add_default_records) {
    //データ挿入処理
    add_default_item_ranking_records();
  }

  set_theme_mod( OP_ITEM_RANKINGS_TABLE_VERSION, ITEM_RANKINGS_TABLE_VERSION );
  return $res;
}
endif;


//テーブルのアップデート
if ( !function_exists( 'update_item_rankings_table' ) ):
function update_item_rankings_table() {
  // オプションに登録されたデータベースのバージョンを取得
  $installed_ver = get_item_rankings_table_version();
  $now_ver = ITEM_RANKINGS_TABLE_VERSION;
  if ( is_update_db_table($installed_ver, $now_ver) ) {
    create_item_rankings_table();
  }
}
endif;


//テーブルのアンインストール
if ( !function_exists( 'uninstall_item_rankings_table' ) ):
function uninstall_item_rankings_table() {
  uninstall_db_table(ITEM_RANKINGS_TABLE_NAME);
  remove_theme_mod(OP_ITEM_RANKINGS_TABLE_VERSION);
}
endif;


//テーブルレコードの取得
if ( !function_exists( 'get_item_rankings' ) ):
function get_item_rankings( $keyword = null, $order_by = null ) {

  $table_name = ITEM_RANKINGS_TABLE_NAME;
  return get_db_table_records($table_name, 'title', $keyword, $order_by);
}
endif;


//テーブルレコードの取得
if ( !function_exists( 'get_item_ranking' ) ):
function get_item_ranking( $id ) {
  $table_name = ITEM_RANKINGS_TABLE_NAME;
  $record = get_db_table_record( $table_name, $id );
  $record->title = !empty($record->title) ? stripslashes_deep($record->title) : '';
  $record->item_ranking = !empty($record->item_ranking) ? stripslashes_deep(unserialize($record->item_ranking)) : array();
  $record->count = !empty($record->count) ? $record->count : 1;

  //var_dump($record);

  return $record;
}
endif;

//関数テキストレコードの削除
if ( !function_exists( 'delete_item_ranking' ) ):
function delete_item_ranking( $id ) {
  $table_name = ITEM_RANKINGS_TABLE_NAME;
  return delete_db_table_record( $table_name, $id );
}
endif;

//HTMLを生成
if ( !function_exists( 'generate_item_ranking_tag' ) ):
function generate_item_ranking_tag($id, $is_demo = false){
  $record = get_item_ranking($id);
  $items = isset($record->item_ranking) ? $record->item_ranking : array();
  $count = isset($record->count) ? intval($record->count) : 1;
  //$demo_class = $is_demo ? ' demo' : '';
  ?>
  <?php //アイテムが存在している場合
  if (!empty($items)): ?>
  <div class="ranking-items">
  <?php
  for ($i = 1; $i <= $count; $i++):
    if ($first_only && $i > 1) {
      break;
    }
    $name = isset($items[$i]['name']) ? $items[$i]['name'] : '';
    $rating = isset($items[$i]['rating']) ? $items[$i]['rating'] : 'none';
    $image_tag = isset($items[$i]['image_tag']) ? $items[$i]['image_tag'] : '';
    $description = isset($items[$i]['description']) ? $items[$i]['description'] : '';
    $detail_url = isset($items[$i]['detail_url']) ? $items[$i]['detail_url'] : '';
    $link_url = isset($items[$i]['link_url']) ? $items[$i]['link_url'] : '';
    $link_tag = isset($items[$i]['link_tag']) ? $items[$i]['link_tag'] : '';
    //改行を取り除く
    $name      = preg_replace('/\n/', '', $name);
    $image_tag = preg_replace('/\n/', '', $image_tag);
    $link_tag  = preg_replace('/\n/', '', $link_tag);
    //ショートコード実行用フィルター
    $name        = apply_filters( 'ranking_item_name',        $name                 );
    $image_tag   = apply_filters( 'ranking_item_image_tag',   $image_tag            );
    $description = apply_filters( 'ranking_item_description', wpautop($description) );
    $link_tag    = apply_filters( 'ranking_item_link_tag',    $link_tag             );
   ?>

    <div class="ranking-item">

      <div class="ranking-item-name">
        <div class="ranking-item-name-crown">
          <?php generate_ranking_crown_tag($i); ?>
        </div>
        <div class="ranking-item-name-text">
          <?php echo $name; ?>
        </div>
      </div>

      <?php //評価が設定されている場合
      if ($rating != 'none'): ?>
      <div class="ranking-item-rating">
        <?php
        $rates = explode('.', $rating);
        //var_dump($rates);
        $has_herf = count($rates) == 2;
        if ($has_herf) {
          $before = intval($rates[0]);
          $middle = 1;
          $after = 5 - 1 - $before;
        } else {
          $before = intval($rating);
          $middle = 0;
          $after = 5 - $before;
        }
        for ($i=1; $i <= $before; $i++) {
          echo '<span class="fa fa-star"></span>';
        }
        for ($i=1; $i <= $middle; $i++) {
          echo '<span class="fa fa-star-half-o"></span>';
        }
        for ($i=1; $i <= $after; $i++) {
          echo '<span class="fa fa-star-o"></span>';
        }
         ?>
      </div>
      <?php endif ?>

      <div class="ranking-item-img-desc">

        <?php //画像タグ情報があるとき
        if ($image_tag): ?>
        <div class="ranking-item-image-tag">
          <?php echo $image_tag; ?>
        </div>
        <?php endif ?>

        <div class="ranking-item-description">
          <?php echo $description; ?>
        </div>

      </div>

      <?php //ボタン情報があるとき
      if ($detail_url || $link_url || $link_tag): ?>
      <div class="ranking-item-link-buttons">

        <?php //詳細ページURLがあるとき
        if ($detail_url): ?>
        <div class="ranking-item-detail">
          <a href="<?php echo $detail_url; ?>"><?php _e( '詳細ページ', THEME_NAME ) ?></a>
        </div>
        <?php endif ?>


        <?php //リンク情報があるとき
        if ($link_url || $link_tag): ?>
        <div class="ranking-item-link">
          <?php if ($link_url): ?>
            <a href="<?php echo $link_url; ?>" target="_blank"><?php _e( '公式ページ', THEME_NAME ) ?></a>
          <?php else: ?>
            <?php echo $link_tag; ?>
          <?php endif ?>
        </div>
        <?php endif ?>

      </div>
      <?php endif ?>

    </div>

  <?php endfor ?>

  </div>
  <?php endif ?>

<?php
}
endif;

//ランキングアイテム入力項目が全て空か
if ( !function_exists( 'is_ranking_item_all_empty' ) ):
function is_ranking_item_all_empty($item){
  return empty($item['name']) && empty($item['image_tag']) && empty($item['description']) && empty($item['detail_url']) && empty($item['link_tag']);
}
endif;

//ランキングアイテム入力項目が有効か
if ( !function_exists( 'is_ranking_item_available' ) ):
function is_ranking_item_available($item){
  return !empty($item['name']) && !empty($item['description']);
}
endif;

if ( !function_exists( 'generate_ranking_crown_tag' ) ):
function generate_ranking_crown_tag($ranking_number){
  switch ($ranking_number) {
    case 1:
      echo '<div class="g-crown"><div class="g-crown-circle"></div></div>';
      break;
    case 2:
      echo '<div class="s-crown"><div class="s-crown-circle"></div></div>';
      break;
    case 3:
      echo '<div class="c-crown"><div class="c-crown-circle"></div></div>';
      break;
    default:

      break;
  }
}
endif;