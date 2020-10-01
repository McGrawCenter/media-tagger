<?php 
    /*
    Plugin Name: Media Tagger
    Plugin URI: http://mcgrawect.princeton.edu
    Description: Allow users to 'tag' an image (or video or anything, really) and to have a tag cloud automatically generated. This is useful for crowd-sourced metadata generation and image analysis. To add a tagging form element to a page or post, add the shortcode: [putagger] to your page or post.
    Author: Ben Johnston - benj@princeton.edu
    Version: 1.0
    */



function putagger_scripts() {
  wp_register_script('putagger_js', plugins_url('js/putagger.js', __FILE__), array('jquery'),'1.1', true);
  wp_enqueue_script('putagger_js');
  wp_register_style('putagger_css', plugins_url('css/putagger.css',__FILE__ ));
  wp_enqueue_style('putagger_css');
}

add_action( 'wp_enqueue_scripts', 'putagger_scripts' );  





function putagger_insert_tager( $atts ){
  global $post;

  $min = 8; $max = 55;
  
  if(!$arr = get_post_meta($post->ID, 'PUTags', true)) {
     update_post_meta($post->ID,'PUTags', array());
     $arr = array();
  }

  //if($arr = get_post_meta($post->ID, 'PUTags', true)) {
  

	  $counts = array_count_values($arr);
	  $num_tags = array_sum($counts);
	  $min = 12;
	  $max = 40;
	  $html = "<style></style>";
	    $html .= "<div id='putagger_cloud'>";
	    foreach($counts as $key=>$val) {
	     	$size = floor($min + (($max-$min)/$num_tags) * $val);
		if($size <= 30 ) { $color="#AAA"; }
		if($size > 30 && $size <= 60 ) { $color="#888"; }
		if($size > 60 && $size <= 90 ) { $color="#666"; }
		if($size > 90 && $size <= 120 ) { $color="#000"; }
		if($size > 120 ) { $color="#333"; }
		$html .= "<span class='putagger_term' style='font-size:{$size}px;color:{$color}'>".$key."</span> ";
	    } 
	    $html .= "</div>";
  //} // end if able to unserialize meta value





   if( is_user_logged_in()) {
    $html .= "<div id='tagit_form' style='margin-top:30px;'>";
    $html .= "<form name='tagit' method='POST'>";
    $html .= "<input type='hidden' id='postid' name='postid' value='{$post->ID}'/>";
    $html .= "<input type='text' id='tag' name='tag' style='width:60%;display:inline;' /> <input type='submit' value='Tag' style='display:inline;' />";
    $html .= "</form>";
    $html .= "<script>window.onload = function() {document.getElementById('tag').focus();};</script>";
    $html .= "</div>";
   }

  return $html;


}

add_shortcode( 'putagger', 'putagger_insert_tager' );





if(isset($_POST['tag'])) {

  $post_id = $_POST['postid'];
  $tagstr = $_POST['tag'];
  $tagarr = explode(",",$tagstr);
  // make any new tags lowercase
  $tagarr = array_map('strtolower', $tagarr);

  if($existing_arr = get_post_meta($post_id, 'PUTags', true)) {
    $new_array = array_merge($existing_arr, $tagarr);
  }
  else { $new_array = $tagarr; }

  $tagstr = $new_array;
  if ( ! add_post_meta( $post_id, 'PUTags', $tagstr, true ) ) {
     update_post_meta( $post_id, 'PUTags', $tagstr);
  }


}
