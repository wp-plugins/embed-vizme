<?php
/*
Plugin Name: Embed Vizme
Plugin URI: http://help.vizme.com/?p=992
Description: Allows for embedding Vizme Playlists and Collections into a Wordpress blog. The insertion format is: <code>[vizme url (optional aspect type: wide OR full)]</code>
Version: 1.0
Author:  Scott Ernst (Vizme)
Author URI: http://www.vizme.com/

Copyright 2011  Vizme, Inc.  (email : help@vizme.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

include (dirname(__FILE__) . '/plugin.php');

class EmbedVizme extends EmbedVizme_Plugin {

//__________________________________________________________________________________________________ EmbedVizme
/** Constructs a new EmbedVizme object. */
	function EmbedVizme() {
		$this->register_plugin('embedvizme', __FILE__);
		$this->add_filter('the_content', 'executeFilter');
		$this->add_action('init', 'appendToHead');
        $this->add_action('wp_head', 'addHeadScripts');
	}

//__________________________________________________________________________________________________ appendToHead
	function appendToHead() {
        if (is_admin() && !(is_preview() || is_page()))
            return;

        wp_deregister_script('jquery');
        wp_register_script('jquery', "http://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js", false, '1.6.1');
        wp_enqueue_script('jquery');
        wp_register_script('vizmeEmbedResize', plugins_url('vizmeEmbedResize.js' , __FILE__ ), 'jquery', '1.0');
        wp_enqueue_script('vizmeEmbedResize');
    }

//__________________________________________________________________________________________________ addHeadScripts
    function addHeadScripts() {
        echo '<script>jQuery.noConflict();</script>';
    }

//__________________________________________________________________________________________________ replace
	function replace($matches) {
		$data   = explode(' ', strip_tags($matches[1]));

        $params = '';
        try {
            $len = count($data);
            for ($n = 0; $n < $len; $i++) {
                $item = array_shift($data);
                if (strpos($item, 'vizme.com') === false)
                    continue;

                break;
            }

            // Separate address from URL parameters -> $params = ['http://www.vizme.com/], [u=...]
            $params = explode('?', $item);

            // Friendly URL case
            if (count($params) == 1) {
                $params = explode('.com/', $item);
                $params = 'frnd=' . $params[1];
            } else
                $params = $params[1];

            // Format parameters for use.
            $params = str_replace('&amp;', '&', $params);
        }
        catch (Exception $e) {
            return '';
        }

        $aspect = 'vizmewide';
        try {
            if (count($data)) {
                $item   = array_shift($data);
                $aspect = ($item == 'full') ? 'vizmefull' : 'vizmewide';
            }
        }
        catch (Exception $e) { }

		return $this->capture('vizmeEmbedIframe', array('params' => $params, 'aspect' => $aspect));
	}

//__________________________________________________________________________________________________ executeFilter
	function executeFilter($input) {
      // To use within a class, the callback function must be formatted as an array as shown below.
	  return preg_replace_callback("@(?:<p>\s*)?\[vizme*(.*?)\](?:\s*</p>)?@", array(&$this, 'replace'), $input);
	}

}//CLASS

$embedVizme = new EmbedVizme;
?>
