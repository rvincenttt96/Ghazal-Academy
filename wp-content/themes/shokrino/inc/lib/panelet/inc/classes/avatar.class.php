<?php
defined('ABSPATH') || exit;
class Panelet_AVATAR {
    public function __construct() {
        add_filter('get_avatar', [$this,'set_profile_avatar'], 10, 5);
        add_action('wp_ajax_nopriv_ajax_avatar_panelet', [$this, 'ajax_avatar']);
        add_action('wp_ajax_ajax_avatar_panelet', [$this, 'ajax_avatar']);
    }
    public function set_profile_avatar($avatar, $id_or_email, $size, $default, $alt) {
        global $current_user;
        $default = PNLT_ASSETS . '/img/user.jpg';
        $avatar_class = '';
        if ($id_or_email == 0) {
            $gravatar_pic_url = $default;
        } else {
            if ( ! is_object($id_or_email) && ! empty($id_or_email) ) {
                $id = $id_or_email;
            } elseif ( is_object($id_or_email) && ! empty($id_or_email->user_id) ) {
                $id = $id_or_email->user_id;
            } else {
                $id = $current_user->ID;
            }
            $attachment_id = get_user_meta($id, 'image', true);
            $attachment_name = get_user_meta($id, 'panelet_image', true);
            if($attachment_name == 'default') {
                $gravatar_pic_url = $default;
            } elseif(strpos($attachment_name, 'pre-panelet-') === 0) {
                $gravatar_pic_url = PNLT_ASSETS.'/img/'.str_replace('pre-panelet-', '', $attachment_name);
            } else {
                $uploads_dir = wp_upload_dir();
                $uploads_path = $uploads_dir['baseurl'];
                if(!empty($attachment_name)){
                    $profile_url = $uploads_path."/profiles"."/".$attachment_name;
                } else {
                    $profile_url = wp_get_attachment_url( $attachment_id );
                }
                $gravatar_pic_url = $profile_url;
                if(!$gravatar_pic_url){
                    $gravatar_pic_url = $default;
                }
            }
        }
        return "<img id='profile-preview-$size' src='$gravatar_pic_url' class='user-avatar' alt='$alt' style='overflow: hidden;max-width: ".$size."px;' />";
    }
    public static function getUserInfo($field,$user_id='') {
        if(empty($user_id)) {
            global $current_user;
            $user_id = $current_user->ID;
        }
        return get_user_meta( $user_id, $field);
    }
    public static function getUserImg($user_id='',$id='',$width='auto',$height='auto') {
        if(empty($user_id)) {
            global $current_user;
            $user_id = $current_user->ID;
        }
        $class_status = "online";
        $output = '<div class="profile-general">'.get_avatar($user_id, '', '', '', $args = array( 'width' => $width, 'height' => $height )).'</div>';
        return $output;
    }
    public function ajax_avatar() {
        $data = [];
        $message = "";
        $error = "";
        $status = "failure";
        $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : get_current_user_id();
        if ($user_id <= 0) {
            $error = "Invalid user ID.";
        } else {
            $image_type = isset($_POST['image-type']) ? $_POST['image-type'] : '';
            $image_default_url = isset($_POST['image-default-url']) ? $_POST['image-default-url'] : '';
            if ($image_type === "uploaded") {
                if (isset($_FILES['image']) && !empty($_FILES['image']['name'])) {
                    $img_name = $_FILES['image']['name'];
                    $img_type = $_FILES['image']['type'];
                    $tmp_name = $_FILES['image']['tmp_name'];
                    $img_size = $_FILES['image']['size'];
                    $allowed_extensions = ["jpeg", "png", "jpg"];
                    $img_ext = strtolower(pathinfo($img_name, PATHINFO_EXTENSION));
                    if (!in_array($img_ext, $allowed_extensions)) {
                        $error = "Invalid file format. Only JPEG and PNG files are allowed.";
                    } else {
                        $file_content = file_get_contents($tmp_name);
                        if (strpos($file_content, '<?php') !== false) {
                            $error = "Uploaded file contains PHP code, which is not allowed.";
                        } else {
                            $time = current_time('Ymd-His');
                            $current_user_id = $user_id;
                            $new_img_name = $time.'-'.$current_user_id.'.'.$img_ext;
                            $upload_dir = wp_upload_dir();
                            $upload_path = $upload_dir['basedir'];
                            if(!file_exists($upload_path."/profiles")) {
                                mkdir($upload_path."/profiles");
                            }
                            if (move_uploaded_file($tmp_name, $upload_path."/profiles" . '/' . $new_img_name)) {
                                update_user_meta($user_id, 'panelet_image', $new_img_name);
                                $status = "success";
                                $message = "Avatar updated successfully.";
                            } else {
                                $error = "Failed to upload file.";
                            }
                        }
                    }
                } else {
                    $error = "No file uploaded.";
                }
            } elseif ($image_type === "default") {
                update_user_meta($user_id, 'panelet_image', 'pre-panelet-' . $image_default_url);
                $status = "success";
                $message = "Avatar updated successfully.";
            } else {
                $error = "Invalid image type.";
            }
        }
        $response = [
            'success' => $status,
            'message' => $message,
            'error' => $error,
            'data' => $data
        ];
        wp_send_json($response);
        wp_die();
    }
    
}
new Panelet_AVATAR;