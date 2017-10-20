<?php
/*
Plugin Name: ICGK Conditional Branch
Plugin URI:
Description: 階層構造を持つポストタイプの条件分岐関数集：has_child($post_id), has_parent($post_id), is_tree($parent_id), is_child($parent_id), is_parent($child_id)。"icgk-conditional-branch"全部をテストするには：test_conditional_branch($parent_id, $child_id)。
Version: 1.0.0
Author: ICHIGENKI
Author URI:
License: GPL2
*/

// $post_id = この ID を持つページが親ページを持つか
function has_parent($post_id) {
	if( $post_id ) {
		$post = get_post($post_id);
		setup_postdata( $post);
	} else {
		global $post;
	}

  if ( is_post_type_hierarchical($post->post_type) && $post->post_parent ) { // 親を持つ階層構造のあるポストタイプかテスト
    return $post->post_parent;
  } else {
    return false;
  }
}

// $post_id = この ID を持つページが子ページを持つか
function has_child($post_id) {
	if( $post_id ) {
		$post = get_post($post_id);
		setup_postdata( $post);
	} else {
		global $post;
	}

	if ( is_post_type_hierarchical($post->post_type) ) { // 階層構造を持つポストタイプかテスト
		$ancestor = array_pop( get_post_ancestors( $post->ID ) );
		$parent = get_children(array(
			'post_type'   => $post->post_type, 
			'post_parent' => $post->ID,
			'post_status' => 'publish' 
		));
		if ( empty($parent) ) {
			return false;
		} else {
			return true;
		}

	} else {  // 階層構造を持たない場合
		return false;
	}
}

// 現在のページ（roop中）が、$parent_id = この ID を持つページの子孫ページであるか
function is_tree($parent_id) {
  global $post;

	if ( is_page( $parent_id ) || is_single($parent_id) ) {
    return true; // そのページを表示中
	} else {
	  $anc = get_post_ancestors( $post->ID );
	  foreach ( $anc as $ancestor ) {
	    if( is_post_type_hierarchical($post->post_type) && $ancestor == $parent_id ) {
	      return true; // 子ページを表示中
	    }
	  }
		return false; // そのページもでなく、子ページでもない
	}
}

// 現在のページ（roop中）が、$parent_id = この ID を持つページの直接の子ページであるか
function is_child($parent_id) {
  global $post;

	if( is_post_type_hierarchical($post->post_type) && $post->post_parent == $parent_id ) {
    return true;
	} else {
		return false;
	}
}

// 現在のページ（roop中）が、$child_id = この ID を持つページの直接の親ページであるか
function is_parent($child_id) {
  global $post;

	$child = get_post($child_id);
	if( is_post_type_hierarchical($post->post_type) && $child->post_parent == $post->ID ) {
    return true;
	} else {
		return false;
	}
}

// "icgk-conditional-branch"をテストする
function test_conditional_branch($parent_id, $child_id) {
echo '<p>';
if( has_child() ) {
	echo '子ページを持っています。<br />'."\n";
} else {
	echo '子ページを持っていません。<br />'."\n";
}
if( has_parent() ) {
	echo '親ページを持っています。<br />'."\n";
} else {
	echo '親ページを持っていません。<br />'."\n";
}
if( is_tree($parent_id) ) {
	echo '「'.get_the_title($parent_id).'」または「'.get_the_title($parent_id).'」の子孫ページです。<br />'."\n";
} else {
	echo '「'.get_the_title($parent_id).'」とは関係のないページです。<br />'."\n";
}
if( is_child($parent_id) ) {
	echo '「'.get_the_title($parent_id).'」の直接の子ページです。<br />'."\n";
} else {
	echo '「'.get_the_title($parent_id).'」の直接の子ページではありません。<br />'."\n";
}
if( is_parent($child_id) ) {
	echo '「'.get_the_title($child_id).'」の直接の親ページです。<br />'."\n";
} else {
	echo '「'.get_the_title($child_id).'」の直接の親ページではありません。<br />'."\n";
}
echo '</p>'."\n";
}

