<?php

spl_autoload_register( function ( $class_name ) {
    // Check if the class is in the namespace of our plugin.
    if ( strpos( $class_name, 'MVC' ) !== 0 ) {
        return;
    }

    // Define the file path based on the class name.
    $file_name = strtolower(
        preg_replace(
            [ '/^MVC\\\/', '/([a-z])([A-Z])/', '/_/', '/\\\/' ],
            [ '', '$1-$2', '-', DIRECTORY_SEPARATOR ],
            $class_name
        )
    );

    $file_path = MVC_PLUGIN_DIR . 'includes/' . $file_name . '.php';

    if ( file_exists( $file_path ) ) {
        require $file_path;
    }
} );
