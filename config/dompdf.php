<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Settings
    |--------------------------------------------------------------------------
    |
    | Set some default values. It is possible to add all defines that can be set
    | in dompdf_config.inc.php. You can also override the entire config file.
    |
    */
    'show_warnings' => false,   // Throw an exception on warnings from dompdf

    'orientation' => 'portrait',

    'defines' => [

        /**
         * The location of the DOMPDF font directory
         *
         * The location of the DOMPDF font directory
         *
         * If not set and you are using composer, this value will be auto-set
         */
        "DOMPDF_FONT_DIR" => storage_path('fonts/'), // advised by dompdf (https://github.com/dompdf/dompdf/pull/782)

        /**
         * The location of the DOMPDF font cache directory
         *
         * This directory is used to cache the results of the font metrics
         * calculations for each font used. This helps speed up page rendering
         * when using larger fonts or many fonts. This is usually in the same
         * directory as the leading font directory.
         *
         * Note this directory must be writable by the webserver process
         */
        "DOMPDF_FONT_CACHE" => storage_path('fonts/'),

        /**
         * The location of a temporary directory.
         *
         * The directory specified must be writeable by the webserver process.
         * The temporary directory is required to download remote images and
         * when using the PFDLib back end.
         */
        "DOMPDF_TEMP_DIR" => sys_get_temp_dir(),

        /**
         * ==== IMPORTANT ====
         *
         * dompdf's "chroot": Prevents dompdf from accessing system files or
         * other files on the webserver. All local files opened by dompdf
         * must be in a subdirectory of this directory. Similarly, web files
         * can only be accessed if they are in a subdirectory of the
         * DOMPDF_CHROOT directory.
         *
         * Prevent dompdf from accessing files outside the DOMPDF_CHROOT
         * directory.
         *
         * Specify the base path to DOMPDF and specify the location of
         * DOMPDF on your filesystem. DOMPDF will not be able to access
         * files outside of the DOMPDF_CHROOT directory.
         *
         * IMPORTANT: The DOMPDF_CHROOT must contain the font directory,
         * the temporary directory, and the directory containing dompdf.
         *
         * Set the DOMPDF_CHROOT to the same directory as the font directory
         * or higher.
         *
         * We advise to use storage_path() for the DOMPDF_CHROOT.
         */
        "DOMPDF_CHROOT" => base_path(),

        /**
         * Whether to use Unicode fonts or not.
         *
         * When set to true, dompdf will use Unicode fonts instead of the
         * default fonts. This allows the rendering of text in multiple
         * languages. When set to false, dompdf will use the default fonts
         * and may not render text correctly for languages other than
         * English.
         *
         * When set to true, the PDF file will be larger because it will
         * include the Unicode fonts.
         */
        "DOMPDF_UNICODE_ENABLED" => true,

        /**
         * Whether to enable font subsetting or not, this will also set the
         * PDF to be compressed by default.
         *
         * When set to true, dompdf will embed only the characters used in
         * the document, which can reduce the file size. When set to false,
         * dompdf will embed the entire font, which can increase the file
         * size but may be necessary for some fonts.
         */
        "DOMPDF_ENABLE_FONT_SUBSETTING" => false,

        /**
         * The PDF version to generate.
         *
         * The PDF version to generate. Valid values are 1.4, 1.5, 1.6, 1.7.
         * The default is 1.4.
         */
        "DOMPDF_PDF_BACKEND" => "CPDF",

        /**
         * The default paper size.
         *
         * The default paper size. Valid values are A4, A3, A5, letter, legal.
         * The default is A4.
         */
        "DOMPDF_DEFAULT_PAPER_SIZE" => "a4",

        /**
         * The default font family
         *
         * The default font family. Valid values are serif, sans-serif, monospace.
         * The default is serif.
         */
        "DOMPDF_DEFAULT_FONT" => "serif",

        /**
         * Image DPI setting
         *
         * This setting determines the default DPI setting for images and fonts.
         * The DPI may be overridden by @page rules. The default is 96.
         */
        "DOMPDF_DPI" => 96,

        /**
         * Enable inline PHP
         *
         * If this setting is set to true then DOMPDF will automatically evaluate
         * inline PHP contained within <script type="text/php"> ... </script> tags.
         *
         * Enabling inline PHP can be useful for customizing the rendering of
         * documents, but it can also be a security risk if you are processing
         * untrusted documents. Set this option to false if you are processing
         * untrusted documents.
         */
        "DOMPDF_ENABLE_PHP" => false,

        /**
         * Enable inline JavaScript
         *
         * If this setting is set to true then DOMPDF will automatically evaluate
         * inline JavaScript contained within <script type="text/javascript"> ... </script> tags.
         *
         * Enabling inline JavaScript can be useful for customizing the rendering of
         * documents, but it can also be a security risk if you are processing
         * untrusted documents. Set this option to false if you are processing
         * untrusted documents.
         */
        "DOMPDF_ENABLE_JAVASCRIPT" => true,

        /**
         * Enable inline @page CSS
         *
         * If this setting is set to true then DOMPDF will automatically evaluate
         * @page CSS contained within <style> ... </style> tags.
         *
         * Enabling @page CSS can be useful for customizing the rendering of
         * documents, but it can also be a security risk if you are processing
         * untrusted documents. Set this option to false if you are processing
         * untrusted documents.
         */
        "DOMPDF_ENABLE_CSS_FLOAT" => false,

        /**
         * Enable remote file access
         *
         * If this setting is set to true, DOMPDF will access remote sites for
         * images and CSS files as required.
         * This is required for some CSS files that are referenced by URL.
         */
        "DOMPDF_ENABLE_REMOTE" => false,

        /**
         * And finally, the most important one: the log output file
         */
        "DOMPDF_LOG_OUTPUT_FILE" => storage_path('logs/dompdf.html'),

        /**
         * DOMPDF spec PDF version
         */
        "DOMPDF_DEFAULT_MEDIA_TYPE" => "print",

        /**
         * HTML5 parser enabled
         */
        "DOMPDF_ENABLE_HTML5PARSER" => true,

        /**
         * Default font
         */
        "DOMPDF_DEFAULT_FONT" => "DejaVu Sans",

        /**
         * Enable CSS float
         */
        "DOMPDF_ENABLE_CSS_FLOAT" => true,

    ],

];