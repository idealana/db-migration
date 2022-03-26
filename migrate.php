<?php

/**
 * Helper Name:       DB Migration
 * Helper URI:        https://github.com/idealana/db-migration/
 * Description:       PHP Helper to create database table migration.
 * Version:           1.0.0
 * Author:            ideaLana
 * Author URI:        https://idealana.github.com/
 */

foreach (glob('./migrations/*.php') as $fileName) {
	$output = [];

	exec("php {$fileName}", $output);

	if(empty($output)) continue;
	
	echo str_replace('./migrations/', '', $fileName), PHP_EOL;
}
