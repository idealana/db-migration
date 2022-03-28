<?php

date_default_timezone_set('Asia/Jakarta');

function templateCreate() {
	$template  = "<?php";
	$template .= "\n\n";
	$template .= '$dirClass = __DIR__ . "/../class";';
	$template .= "\n";
	$template .= 'require_once $dirClass . "/Schema.php";';
	$template .= "\n";
	$template .= 'require_once $dirClass . "/Blueprint.php";';
	$template .= "\n\n";
	$template .= 'Schema::create(\'table_name\', function(Blueprint $table){';
	$template .= "\n";
	$template .= "\t" . '$table->id(\'column_name\');';
	$template .= "\n";
	$template .= "\t" . '$table->createdAt();';
	$template .= "\n";
	$template .= "\t" . '$table->updatedAt();';
	$template .= "\n";
	$template .= '});';
	$template .= "\n";

	return $template;
}

function templateAddColumn() {
	$template  = "<?php";
	$template .= "\n\n";
	$template .= '$dirClass = __DIR__ . "/../class";';
	$template .= "\n";
	$template .= 'require_once $dirClass . "/Schema.php";';
	$template .= "\n";
	$template .= 'require_once $dirClass . "/Blueprint.php";';
	$template .= "\n\n";
	$template .= 'Schema::table(\'table_name\', function(Blueprint $table){';
	$template .= "\n";
	$template .= "\t" . '// $table->string(\'column_name\');';
	$template .= "\n";
	$template .= '});';
	$template .= "\n";

	return $template;
}

if(! empty($argv[1])) {
	// Make Command
	if(strpos($argv[1], 'make:') !== false) {
		$command  = str_replace('make:', '', $argv[1]);
		$fileName = $argv[2] ?? '';

		// Make Migration
		if('migration' === $command && ! empty($fileName)) {
			$template = ! empty($argv[3]) && $argv[3] === '--addcolumn'
				? templateAddColumn()
				: templateCreate();

			$now      = date('Y_m_d_H_i_s');
			$fileName = "{$now}_{$fileName}.php";

			$file = fopen("migrations/{$fileName}", "w");
			fwrite($file, $template);
			fclose($file);

			echo "Create {$fileName}";
		}
	}
}
