<?php
/**
 * Plugin Name: [Druckhaus Adame] Bestellzusammenfassung
 * Plugin URI: https://druckhaus-adame.de/
 * Description: Fügt Inhalte der Bestellung dem Checkout hinzu.
 * Version: 1.0
 * Author: Jan Wambach
 * Author URI: https://jwdsign.de/
 */

function get_order_content( $entry__id ){
	$entry = FrmEntry::getOne( $entry__id, true);
	$entry_fields = FrmEntryMeta::get_entry_meta_info($entry__id);
	
	$names = array();
	$values = array();
	$visibility  = array();
	$order = array();

	$name = $entry->name;
	$metas = $entry->metas;

	foreach ($entry_fields as $field) {
		$field__id = $field->field_id;
		$field_object = FrmField::getOne( $field__id );
		$field__name = $field_object->name;
		$admin__only = $field_object->field_options["admin_only"];
		$names[] = $field__name;
		$visibility[] = $admin__only;
		//if (empty($admin__only)) {}
	}

	foreach ($metas as $meta) {
		$values[] = $meta;
	}

	$result = array_map(function ($names, $values, $visibility) {
  		return array_combine(
    	['name', 'value', 'visibility'],
    	[$names, $values, $visibility]
  		);
	}, $names, $values, $visibility);

	foreach ($result as $line_item) {

		if (empty($line_item["visibility"])) {
			$item = '<div class="line_item"><strong>'.$line_item["name"].':</strong> <span>'.$line_item["value"].'</span></div>';
			$order[] = $item;
		}
	}	

	return implode('', $order);
}

function order_content( $atts ) {
	$a = shortcode_atts( array (
          'entry' => ''
    ), $atts );
	return get_order_content( $a["entry"] );
}
add_shortcode( 'order', 'order_content' );
