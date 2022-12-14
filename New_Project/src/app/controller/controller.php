<?php
declare(strict_types=1);
namespace Controller;

require_once(__DIR__ . '/../config.php');
use function Config\get_lib_dir;
use function Config\get_model_dir;
use function Config\get_view_dir;
use function Controller\login as ControllerLogin;

require_once(get_lib_dir() . '/request/request.php');
use Request\Request;

require_once(get_lib_dir() . '/context/context.php');
use Context\Context;

require_once(get_lib_dir() . '/response/response.php');
use Response\Response;

require_once(get_model_dir() . '/model.php');
use function Model\get_csv_path;
use function Model\read_table;
use function Model\add_blog_new;
use function Model\add_new_user;
use function Model\get_images;
use function Model\add_new_team;
use function Model\delete_new;
use function Model\get_web_service;

require_once(get_view_dir() . '/view.php');
use function View\get_template_path;
use function View\render_template;
use function View\prettify_blog;
use function View\show_modal;



// ############################################################################
// Route handlers
// ############################################################################
// All controller functions receive $request, whether they use it or not.

// ----------------------------------------------------------------------------
function index(Request $request, Context $context): array {

    $index_body = render_template(get_template_path('/body/index'), []);
    $index_view = render_template(get_template_path('/skeleton/skeleton'),
                                 ['title' => 'WebApp - Diari Deportiu',
                                  'body'  => $index_body]);

    $response   = new Response($index_view);
    return [$response, $context];
}

// ----------------------------------------------------------------------------
function blog(Request $request, Context $context): array {

    // 1. If request is POST, add data
    if ($request->method == 'POST') {
        $delete  = $request->parameters['esborrar'];
        $message = $request->parameters['message'];

        if ($delete == "si") {
            delete_new(get_csv_path('blog'));

            if ($message != "") {
                add_blog_new( get_csv_path('blog'), $message );
            }
            
        }
        else{
            add_blog_new( get_csv_path('blog'), $message );
        }

        
    }

    // 2. Get data
    $blog = read_table(get_csv_path('blog'));

    // 3. Prettify data
    $pretty_blog = prettify_blog($blog);

    // 4. Fill template with data
    $blog_body = render_template(get_template_path('/body/blog'),
                                 ['blog_table' => $pretty_blog]);
    $blog_view = render_template(get_template_path('/skeleton/skeleton'),
                                 ['title' => 'Blog',
                                  'body'  => $blog_body]);

    // 5. Return response
    $response = new Response($blog_view);
    return [$response, $context];
}

// ----------------------------------------------------------------------------
function gallery(Request $request, Context $context): array {

    // 1. Get data
    $web_links = get_images("gallery");

    $gallery_body = render_template(get_template_path('/body/gallery'), 
                                    ['images_array' => $web_links]);
    $gallery_view = render_template(get_template_path('/skeleton/skeleton'),
                                 ['title' => 'Galeria',
                                  'body'  => $gallery_body]);

    $response = new Response($gallery_view);
    return [$response, $context];
}

// ----------------------------------------------------------------------------
function data(Request $request, Context $context): array {

    if ($request->method == 'POST'){
        $position = $request->parameters['position'];
        $team     = $request->parameters['team'];
        $points   = $request->parameters['points'];
        $delete   = $request->parameters['delete'];

        if ($delete != "si") {
            $delete = "no";
        }

        add_new_team( get_csv_path('liga'), $position, $team, $points, $delete );
    }

    // 1. Get data
    $manga_table = read_table(get_csv_path('liga'));

    // 2. Fill template with data
    $data_body = render_template(get_template_path('/body/data'),
                                ['manga_table' => $manga_table]);

    $data_view = render_template(get_template_path('/skeleton/skeleton'),
                                ['title' => 'Data',
                                'body'  => $data_body]);

    $response  = new Response($data_view);
    

    
    
    return [$response, $context];
}

// ----------------------------------------------------------------------------
function login(Request $request, Context $context): array {

   
    if($request->method == 'GET') {
       
        $login_body = render_template(get_template_path('/body/login'),[]);
        $login_view = render_template(get_template_path('/skeleton/skeleton'),
                                      ['title' => 'Login',
                                       'body'  => $login_body]);

        $response = new Response($login_view);
        return [$response, $context];
                                   
    } elseif ($request->method == 'POST'){

        $username = $request->parameters['username'];
        $password = $request->parameters['password'];
        $rol      = "";

        //validate user
        $login = read_table(get_csv_path('users'));

        $body_users = $login->body;

        // iterate .csv to find or not user 
        foreach ($body_users as $user) {

            if($user["Username"] == $username and $user["Password"] == $password){
                $rol = $user["Rol"];
                $response = new Response("hola el usuario $username tiene el rol de $rol");

                if      ($rol == "visitant"){}
                else if ($rol == "client")  {}
                else if ($rol == "admin")   {}
                else if ($rol == "root")    {}
            }
        }


        //$response = new Response($username . PHP_EOL . $password . PHP_EOL);
        return [$response, $context];
    }

}

// ----------------------------------------------------------------------------
function register(Request $request, Context $context): array {

    if($request->method == 'GET') 
    {
       
        $login_body = render_template(get_template_path('/body/register'),[]);
        $login_view = render_template(get_template_path('/skeleton/skeleton'),
                                      ['title' => 'Registre',
                                       'body'  => $login_body]);

        $response = new Response($login_view);
        return [$response, $context];
    }

    elseif ($request->method == 'POST') 
    {
        $username = $request->parameters['r_username'];
        $password = $request->parameters['r_password'];
        $role     = $request->parameters['role'];

        //add user to csv
        add_new_user(get_csv_path('users'), $username, $password, $role);

        //redirect to login



        $response = new Response($username . PHP_EOL . $password . PHP_EOL . $role);
        return [$response, $context];
    }
}

// ----------------------------------------------------------------------------
function web_service(Request $request, Context $context): array {

    // 1. Get data
    $webs_array = get_web_service();

    $web_service_body = render_template(get_template_path('/body/web-service'),
                                 ['webs_array' => $webs_array]);
                                 
    $web_service_view = render_template(get_template_path('/skeleton/skeleton'),
                                 ['title' => 'Web Service',
                                  'body'  => $web_service_body]);

    $response = new Response($web_service_view);
    return [$response, $context];
}

// ----------------------------------------------------------------------------
function error_404(Request $request, Context $context): array {

    $error404_body = render_template(get_template_path('/body/error404'),
                                     ['request_path' => $request->path]);

    $error404_view = render_template(get_template_path('/skeleton/skeleton'),
                                 ['title' => 'Not found',
                                  'body'  => $error404_body]);

    $response = new Response($error404_view, 404);
    return [$response, $context];
}

// ----------------------------------------------------------------------------
