<?php
/*
Plugin Name: CSV Comment Importer
Description: Import comments from a CSV file.
*/

// 添加菜单页面
function csv_comment_importer_menu() {
    add_menu_page('CSV Comment Importer', 'CSV Comment Importer', 'manage_options', 'csv-comment-importer', 'csv_comment_importer_page');
}

add_action('admin_menu', 'csv_comment_importer_menu');

// 插件页面内容
function csv_comment_importer_page() {
    ?>
    <div class="wrap">
        <h2>CSV Comment Importer</h2>
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="csv_file">
            <input type="submit" name="submit" value="Import Comments">
        </form>
    </div>
    <?php
}

// 处理CSV文件上传并导入评论
function process_csv_comment_import() {
    if (isset($_POST['submit'])) {
        if ($_FILES['csv_file']['error'] == 0) {
            $file = $_FILES['csv_file']['tmp_name'];
            $handle = fopen($file, "r");
 global $wpdb;
			
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
             
                
$sql= "SELECT * FROM ".$wpdb->prefix."posts WHERE post_name ='".$data[6]."' and post_type='product'";
$results = $wpdb->get_results( $sql );

              if($results){
                $commentdata = array(
                    'comment_post_ID' => $results[0]->ID, // 产品ID
            
                    'comment_author' => $data[4], // 评论作者
                    'comment_author_email' => $data[5], // 评论作者邮箱
                    'comment_content' => $data[1], // 评论内容
                    'comment_type' => 'review',
                    'comment_parent' => 0,
                    'user_id' => 0,
                    'comment_author_IP' => '',
                    'comment_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'comment_approved' => 1,
                    'comment_date' => date('Y-m-d H:i:s',strtotime($data[3])),
                    'comment_date_gmt' => date('Y-m-d H:i:s',strtotime($data[3])),
                );

                // 插入评论
                $comment_id = wp_insert_comment($commentdata);

                // 添加评分元数据
                add_comment_meta($comment_id, 'rating', $data[2]); // 评分数据列
                add_comment_meta($comment_id, 'reviewx_title', $data[0]); // 评分数据列
              }
            }

            fclose($handle);
            echo 'Comments imported successfully.';
        }
    }
}

add_action('admin_init', 'process_csv_comment_import');
?>
