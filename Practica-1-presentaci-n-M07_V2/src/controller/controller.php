<?php
declare(strict_types=1);
namespace Controller;

require_once(realpath(__DIR__ . '/../model/model.php'));
use function Model\get_csv_path;
use function Model\read_table;

require_once(realpath(__DIR__ . '/../view/viewlib.php'));
use function View\get_template_path;

require_once(realpath(__DIR__ . '/../../vendor/utils/utils.php'));
use function Utils\render_template;



// ############################################################################
// Route handlers
// ############################################################################

// ----------------------------------------------------------------------------
function index(): string {

    $index_body = render_template(get_template_path('/body/index'), []);
    $index_view = render_template(get_template_path('/skeleton/skeleton'),
                                 ['title' => 'WebApp',
                                  'body'  => $index_body]);
    return $index_view;
}

// ----------------------------------------------------------------------------
function blog(): string {

    $blog_body = render_template(get_template_path('/body/blog'), []);
    $blog_view = render_template(get_template_path('/skeleton/skeleton'),
                                 ['title' => 'Blog',
                                  'body'  => $blog_body]);
    return $blog_view;
}

// ----------------------------------------------------------------------------
function gallery(): string {

    $gallery_body = render_template(get_template_path('/body/gallery'), []);
    $gallery_view = render_template(get_template_path('/skeleton/skeleton'),
                                 ['title' => 'Gallery',
                                  'body'  => $gallery_body]);
    return $gallery_view;
}

// ----------------------------------------------------------------------------
function table(): string {

    // 1. Get data
    $football_table = read_table(get_csv_path('liga - Hoja 1'));

    // 2. Fill template with data
    $data_body = render_template(get_template_path('/body/table'),
                                 ['football_table' => $football_table]);

    $data_view = render_template(get_template_path('/skeleton/skeleton'),
                                 ['title' => 'Table',
                                  'body'  => $data_body]);
    return $data_view;
}


// ----------------------------------------------------------------------------
function error_404(string $request_path): string {

    http_response_code(404);

    $error404_body = render_template(get_template_path('/body/error404'),
                                     ['request_path' => $request_path]);

    $error404_view = render_template(get_template_path('/skeleton/skeleton'),
                                 ['title' => 'Not found',
                                  'body'  => $error404_body]);

    return $error404_view;
}