<?php
/*
Plugin Name: Better Images Crop
Plugin URI: http://wordpress.org/plugins/better-images-crop
Description: this simple plugin help theme developers to create their thumbnail
with more precition size and area that they want to crop
Version: 1.2
Author: Widi dwi
Author URI: http://messcode.com/wordpress-better-images-crop.html
License: GPL2
*/



if (!is_plugin_page()) {
  add_action ('admin_menu','WD_bic_admin');
}

function WD_bic_admin(){
  add_options_page('Better Image Crop', 'Better Image Crop', 10, __FILE__, 'WD_bic_options_page');
  add_option('wd_bic_options', wd_bic_options_default(), 'Options for Better Images Crop plugin');
}


function better_image_crop( $payload, $orig_w, $orig_h, $dest_w, $dest_h, $crop ){

	// Change this to a conditional that decides whether you
	// want to override the defaults for this image or not.
	if( false )
		return $payload;

        $options = get_option('wd_bic_options');
        $bic_width = $options['bic_w_scale'];
        $bic_height = $options['bic_h_scale'];
        $bic_vertical = $options['bic_v_pos'];
        $bic_horizontal = $options['bic_h_pos'];

        $new_ori_width  = $orig_w * ($bic_width/100);
        $new_ori_height = $orig_h * ($bic_height/100);


        //$global_scale = $dest_w + $dest_h;

         $height_scale = $dest_h/$dest_w;
         $width_scale = $dest_w/$dest_h;
         if($new_ori_width < $new_ori_height){
           //potrait

           $crop_w = $new_ori_width;
           $crop_h = $new_ori_width * $height_scale;
         }else{
           //lancscape

           $crop_h = $new_ori_height;
           $crop_w = $new_ori_height * $width_scale;

         }

        switch($bic_vertical){
          case 1:
            $s_y = 0;
          break;
          case 2:
          $space_height = ($orig_h  - ($new_ori_height - $orig_h)) - $crop_h;
            $s_y = $space_height /2;
          break;
          case 3:
            $s_y = ($orig_h  - ($new_ori_height - $orig_h)) - $crop_h;
          break;
        }

        switch($bic_horizontal){
          case 1:
            $s_x = 0;
          break;
          case 2:
            $space_width = ($orig_w-($orig_w - $new_ori_width)) - $crop_w ;
            $s_x = $space_width/2;
          break;
          case 3:
              $s_x = ($orig_w -($orig_w - $new_ori_width)) - $crop_w;
          break;
        }


        $new_w = $dest_w;
        $new_h = $dest_h;


    if ( $new_w >= $orig_w && $new_h >= $orig_h )
		return false;

	// the return array matches the parameters to imagecopyresampled()
	// int dst_x, int dst_y, int src_x, int src_y, int dst_w, int dst_h, int src_w, int src_h
	return array( 0, 0, (int) $s_x, (int) $s_y, (int) $new_w, (int) $new_h, (int) $crop_w, (int) $crop_h );

}

add_filter( 'image_resize_dimensions', 'better_image_crop', 10, 6 );

function wd_bic_options_default(){
    $options = array();
    $options['bic_w_scale'] = 100;
    $options['bic_h_scale'] = 100;
    $options['bic_v_pos'] = 2;
    $options['bic_h_pos'] = 2;
    return $options;
}

function WD_bic_options_page(){
  if ( isset($_POST['submitted']) ) {
    check_admin_referer('WD_bic_all');
    $options = array();
    $bic_w_val = intval($_POST['bic_w_scale']);
    $bic_h_val = intval($_POST['bic_h_scale']);

    if($bic_w_val >= 100){ $insert_w_val = 100; }
    elseif($bic_w_val <= 50){$insert_w_val = 50;}
    else{$insert_w_val = $bic_w_val;}

    if($bic_h_val >= 100){ $insert_h_val = 100; }
    elseif($bic_h_val <= 50){$insert_h_val = 50;}
    else{$insert_h_val = $bic_h_val;}

    $options['bic_w_scale'] = $insert_w_val;
    $options['bic_h_scale'] = $insert_h_val;
    $options['bic_v_pos'] = intval($_POST['bic_vertical_position']);
    $options['bic_h_pos'] = intval($_POST['bic_horizontal_position']);
    update_option('wd_bic_options',$options);
  }

    $options = get_option('wd_bic_options');
    $bic_w_scale = $options['bic_w_scale'];
    $bic_h_scale = $options['bic_h_scale'];
    $bic_v_pos = $options['bic_v_pos'];
    $bic_h_pos = $options['bic_h_pos'];


  ?>
  <div id="wpbody-content">
  <style type="text/css">
  <!--
  .inline-opt{
    display: inline;
    float: left;
    margin-right: 40px;
  }

  -->
  </style>
    <div class="wrap">
    <form name="evermore" action="" method="post">

    <?php
    if (function_exists('wp_nonce_field')) {
				wp_nonce_field('WD_bic_all');
	}
	?>

	<input type="hidden" name="submitted" value="1" />

    <h2>Better Images Crop Settings</h2>
        <table class="form-table">
            <tbody>
             <tr>

                <i>Scale image value between 50% ~ 100%, if value detect more or less
                then range value then it will change to upper limit / lower limit</i>

             </tr>
            <tr valign="top">
                <th scope="row">
                    <label for="bic_w_scale">Width scale area</label>
                </th>
                <td>
                    <input type="text" value="<?php echo $bic_w_scale;?>" name="bic_w_scale" id="bic_w_scale">
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">
                    <label for="bic_h_scale">Height scale area</label>
                </th>
                <td>
                    <input type="text" value="<?php echo $bic_h_scale;?>" name="bic_h_scale" id="bic_h_scale">
                </td>
            </tr>
            <tr><th><i>Default position is middle and center</i></th></tr>
            <tr>
                <th scope="row">Vertical target position</th>
                <td>
                <fieldset>
                    <legend class="screen-reader-text">
                        <span>Vertical target position</span>
                    </legend>
                    <label title="v-top" class="inline-opt">
                        <input type="radio" name="bic_vertical_position" value="1"
                        <?php if($bic_v_pos == 1) echo 'checked="checked"';?>
                        />
                        <span>Top<br>
                        <?php echo '<img src="' . plugins_url( 'images/images-position-4.png' , __FILE__ ) . '" width="50" height="68"> ';?>
                        </span>
                    </label>
                    <label title="v-middle" class="inline-opt">
                        <input type="radio" name="bic_vertical_position" value="2"
                        <?php if($bic_v_pos == 2) echo 'checked="checked"';?>
                        />
                        <span>Middle<br>
                        <?php echo '<img src="' . plugins_url( 'images/images-position-5.png' , __FILE__ ) . '" width="50" height="68"> ';?>
                        </span>
                    </label>
                    <label title="v-bottom" class="inline-opt">
                        <input type="radio" name="bic_vertical_position" value="3"
                        <?php if($bic_v_pos == 3) echo 'checked="checked"';?>
                        />
                        <span>Bottom<br>
                        <?php echo '<img src="' . plugins_url( 'images/images-position-6.png' , __FILE__ ) . '" width="50" height="68"> ';?>
                        </span>
                    </label>
                </fieldset>
                </td>
            </tr>

            <tr>
                <th scope="row">Horizontal target position</th>
                <td>
                <fieldset>
                    <legend class="screen-reader-text">
                        <span>Horizontal target position</span>
                    </legend>
                    <label title="h-left" class="inline-opt">
                        <input type="radio" name="bic_horizontal_position" value="1"
                        <?php if($bic_h_pos == 1) echo 'checked="checked"';?>
                        />
                        <span>Left<br>
                        <?php echo '<img src="' . plugins_url( 'images/images-position-1.png' , __FILE__ ) . '" width="50" height="68"> ';?>
                        </span>
                    </label>
                    <label title="h-center" class="inline-opt">
                        <input type="radio" name="bic_horizontal_position" value="2"
                        <?php if($bic_h_pos == 2) echo 'checked="checked"';?>
                        />
                        <span>Center<br>
                        <?php echo '<img src="' . plugins_url( 'images/images-position-2.png' , __FILE__ ) . '" width="50" height="68"> ';?>
                        </span>
                    </label>
                    <label title="h-right" class="inline-opt">
                        <input type="radio" name="bic_horizontal_position" value="3"
                        <?php if($bic_h_pos == 3) echo 'checked="checked"';?>
                        />
                        <span>Right<br>
                        <?php echo '<img src="' . plugins_url( 'images/images-position-3.png' , __FILE__ ) . '" width="50" height="68"> ';?>
                        </span>
                    </label>
                </fieldset>
                </td>
            </tr>

            </tbody>
        </table>
        <p class="submit">
        <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
        </p>
    </form>
    </div>
  </div>
  <?php

}
?>