<?php

function allow_json_upload($mimes)
{
    $mimes['json'] = 'application/json';
    return $mimes;
}
add_filter('upload_mimes', 'allow_json_upload');



add_action('rest_api_init', function () {
    register_rest_route('elementor/v1', '/import-template', array(
        'methods' => 'POST',
        'callback' => 'import_elementor_template',
        'permission_callback' => '__return_true',
    ));
});


function import_elementor_template(WP_REST_Request $request)
{

    $files = $request->get_file_params();

    if (empty($files['file']) || $files['file']['error'] !== UPLOAD_ERR_OK) {
        return new WP_REST_Response(['message' => 'File upload failed.'], 400);
    }

    $uploaded_file = $files['file'];
    
    $file_type = wp_check_filetype_and_ext($uploaded_file['tmp_name'], $uploaded_file['name']);

    if ($file_type['ext'] !== 'json' || $file_type['type'] !== 'application/json') {
        return new WP_REST_Response(['message' => 'Invalid file type. Please upload a JSON file.'], 400);
    }


    // Pindahkan file ke lokasi sementara
    $file_path = wp_upload_dir()['path'] . '/' . $uploaded_file['name'];
    move_uploaded_file($uploaded_file['tmp_name'], $file_path);

    // Baca file JSON
    $json_content = file_get_contents($file_path);
    $template_data = json_decode($json_content, true);

    if (!is_array($template_data) || empty($template_data['content'])) {
        return new WP_REST_Response(['message' => 'Invalid template data. Please check your JSON file.'], 400);
    }

    $obj = new \Elementor\TemplateLibrary\Source_Local();
    $reflection = new \ReflectionClass($obj);
    $method = $reflection->getMethod('process_export_import_content');
    $method->setAccessible(true);

    $result = $method->invokeArgs($obj, [$template_data['content'], 'on_import']);

    $db = new \Elementor\DB();
    $content = $db->get_plain_text_from_data($template_data['content']);

    $template_args = [
        'post_title'   => $template_data['title'] ?? 'Imported Template',
        'post_content' => $content,
        'post_status'  => 'publish',
        'post_type'    => 'elementor_library',
        'meta_input'   => [
            '_elementor_template_type' => $template_data['type'] ?? 'section',
            '_elementor_data'          => wp_json_encode($template_data['content']),
            '_elementor_edit_mode'     => 'builder'
        ],
    ];

    // Buat post baru untuk template Elementor
    $template_id = wp_insert_post($template_args);

    if (!$template_id) {
        return new WP_REST_Response(['message' => 'Failed to create template.'], 500);
    }

    return new WP_REST_Response(['message' => 'Template imported successfully.', 'template_id' => $template_id], 200);
}
