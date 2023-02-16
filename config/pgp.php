<?php

// config for JTD420/PGP
return [
    /*
     * To prevent naming conflicts, this prefix will be added to all Laravel-PGP migrations.
     */
    'table_prefix' => 'pgp_',

    /*
     * Prefix added to rest of Laravel-PGP code, including routes. (Delete default to remove prefix)
     */
    'prefix' => 'PGP',

    /*
     * Choose the layout file to be extended by the views provided in the package.
     * The default layout file is set to 'PGP::layouts.app', but can be changed to match your preferred layout.
     */
    'layout_file' => 'PGP::layouts.app',
    /*
     * Choose the section name to be used in the views provided in the package.
     * The default section name is set to 'content', but it can be changed to match the section defined in your custom layout file.
     * This option is only applicable if you have set a custom 'layout_file' with a different '@section()' name.
     */

    'layout_section' => 'content',

    'uses_custom_auth' => true,

];
