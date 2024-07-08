<?php

class Scrollme_db
{

    public static function getItemsArray($post_id){
        global $wpdb;

        $items = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "sm_items WHERE post_id = " . $post_id, ARRAY_A);

        foreach ($items as $key => $val) {
            foreach ($items[$key] as $item_key => $item_val) {
                $items[$key][$item_key] = preg_match("/^[0-9]+$/", $item_val) == 1 ? intval($item_val) : $item_val;
            }
        }

        return $items;
    }

    public static function updateItems($sm_post_data, $post_id){
        foreach($sm_post_data as $key => $item){
            if($item == null){
                self::deleteItem($key+1, $post_id);
            }else{
                self::insertItem($key+1, $post_id, $item);
            }
        }
    }

    private static function deleteItem($item_id, $post_id){
        global $wpdb;

        $table_name = $wpdb->prefix . "sm_items";

        $sql = "DELETE FROM " . $table_name . " WHERE item_id=" . $item_id . " AND post_id=" . $post_id;

        $wpdb->query($sql);
    }

    public static function deleteItems($post_id){
        global $wpdb;

        $table_name = $wpdb->prefix . "sm_items";

        $sql = "DELETE FROM " . $table_name . " WHERE post_id=" . $post_id;

        $wpdb->query($sql);
    }

    private static function insertItem($item_id, $post_id, $item){
        global $wpdb;

        $item = array_merge($item, array('post_id' => $post_id, 'item_id' => $item_id));

        $table_name = $wpdb->prefix . "sm_items";

        $sql = "SELECT * FROM " . $table_name . " WHERE item_id=" . $item_id . " AND post_id = " . $post_id;

        $row = $wpdb->get_row($sql, ARRAY_A);

        if(empty($row)){
            //insert
            $wpdb->insert($table_name, $item);

        } else {
            //update
            $wpdb->update($table_name, $item, array('post_id' => $post_id, 'item_id' => $item_id));
        }

    }

}