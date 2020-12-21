<?php if( get_post_type() == 'publication' ) {?>

<?php wp_nonce_field('submit_sespub','sespub_nonce'); ?>

<div class="or_input_wrap" style="width: 80%; display: inline-block; ">
	<label>Short Title</label><br />
	<input value="<?php echo $this->model->short_title; ?>" type="text" name="_short_title" style="width: 90%;" />
</div>
<!--
<div class="or_input_wrap" style="width: 80%; display: inline-block; ">
	<label>Non-SES Authors</label><br />
	<input value="<?php //echo esc_attr($this->model->authors); ?>" type="text" name="_authors" style="width: 90%;" />
</div>
-->

<div class="or_input_wrap" style="width: 80%; display: inline-block; ">
<!--
	<label>All Authors</label><br />
-->
<?PHP
 //   $categories = get_categories('taxonomy=sesauthors');
 //   $select = "<select name='cat' id='cat' class='postform'>n";
 //   $select.= "<option value='-1'>Select category</option>n";
    
//    foreach($categories as $category){
//    if($category->count > 0){
//        $select.= "<option value='".$category->slug."'>".$category->name."</option>";
//    }
//  }
//   $select.= "</select>";
//   echo $select;
//   echo '<p />';
/*   
   $url = "https://people.wsu.edu/wp-json/wp/v2/people/?type=wsuwp_people_profile&filter[orderby]=title&filter[order]=ASC&filter[tag]=school-of-economic-sciences&filter[posts_per_page]=40";
   $json = file_get_contents($url);
   $data = json_decode($json, TRUE);
   
   $pepselect = "<select name='pepcat' id='pepcat' class='postform'>n";
   $pepselect.= "<option value='-1'>Select SES Authors</option>n";

	foreach($data as $item){
//    if($item->count > 0){
        $pepselect.= "<option value='".$item[nid]."'>". $item[last_name] . ', '. $item[first_name]. "</option>";
//    }
  }
   $select.= "</select>";
   echo $pepselect;
*/   
?> 
<!--
<input value="<?php echo esc_textarea($this->model->sesauthorsdd); ?>" type="textarea" name="_sesauthorsdd" style="width: 90%;" />
  
<?php   wp_editor($this->model->sesauthorsdd, _sesauthorsdd, $settings = array('teeny' => true, 'quicktags' => false, 'textarea_rows'=>1, 'tinymce' => array('theme_advanced_disable' => 'bold,italic,underline'))); ?>	 

</div>
-->

<div class="or_input_wrap" style="width: 80%; display: inline-block; ">
	<label>Issue and Pages</label><br />
	<input value="<?php echo $this->model->issue_pages; ?>" type="text" name="_issue_pages" style="width: 50%;" />
</div>

<div class="or_input_wrap" style="width: 80%; display: inline-block; ">
	<label>URL  </label><br />
	<input value="<?php echo $this->model->redirect; ?>" type="text" name="_redirect_to" style="width: 50%;" />
</div>


 <?php } ?>