<?php
define( 'SOURCE_PATH', 'example' );

ini_set( 'memory_limit', '512M' );

$extensions          = get_loaded_extensions();
$extension_functions = array();
$all_functions       = array();
$all_classes         = array();

foreach ( $extensions as $ext ) {

    $dom = new ReflectionExtension($ext);

    $functions = array_keys($dom->getFunctions());

    if ( $functions ) {
        $ext_functions = array_combine($functions,  array_fill(0, count($functions), $ext));
        $all_functions = array_merge( $all_functions, $ext_functions );
    }

    $classes = array_keys($dom->getClasses());

    if ( $classes ) {
        $ext_classes = array_combine($classes,  array_fill(0, count($classes), $ext));
        $all_classes = array_merge( $all_classes, $ext_classes );
    }
}

echo "Found " . count( $all_functions ) . " total functions and " . count( $all_classes ) . " total classes in " . count( $extensions ) . " extensions available in PHP.\n";


require_once 'CodeDependency.php';

$cd = new CodeDependency();
$cd->findDependenciesByDirectory( SOURCE_PATH );

echo "Found " . count( $cd->getFoundFunctions() ) . " function calls and " . count( $cd->getFoundClasses() ) . " object instantiations in your script.\n";

$ext = array();
foreach( $cd->getFoundFunctions() as $func ) {
    if ( isset( $all_functions[$func] )) {
        $ext[$all_functions[$func]][] = $func;
    }
}

foreach( $cd->getFoundClasses() as $class ) {
    if ( isset( $all_classes[$class] )) {
        $ext[$all_classes[$class]][] = $class;
    }
}

echo "Scanned code uses following php extensions: ";
echo "\n* ", implode(array_keys($ext),"\n* "), "\n\n";
